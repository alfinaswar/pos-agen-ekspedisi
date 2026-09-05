<?php

namespace App\Services;

use App\Models\PendaftaranTenant;
use App\Models\Tenant;
use App\Models\User;
use App\Models\TagihanPembayaran;
use App\Mail\TenantApprovedMail;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class TenantProvisioningService
{
    /**
     * Provision tenant lengkap: update status, buat tenant, user admin, tagihan, kirim email.
     *
     * @param  PendaftaranTenant  $pendaftaran             Data pendaftaran yang akan diproses
     * @param  string             $verifiedBy              Nama verifier ('System', nama admin, dll)
     * @param  string|null        $catatan                 Catatan verifikasi opsional
     * @param  bool               $flashPasswordToSession  Jika true, password di-flash ke session (untuk ditampilkan sekali)
     * @return array  ['success' => bool, 'tenant' => Tenant|null, 'message' => string|null, 'error' => string|null]
     */
    public function provision(
        PendaftaranTenant $pendaftaran,
        string $verifiedBy = 'System',
        ?string $catatan = null,
        bool $flashPasswordToSession = false
    ): array {
        // Reload relasi paket agar data harga & durasi tersedia
        $pendaftaran->load('getPaket');
        $paket = $pendaftaran->getPaket;

        if (!$paket) {
            return [
                'success' => false,
                'tenant' => null,
                'message' => null,
                'error' => "Paket tidak ditemukan untuk pendaftaran #{$pendaftaran->id}.",
            ];
        }

        DB::beginTransaction();

        try {
            // ─────────────────────────────────────────────────────────────
            // 1. UPDATE STATUS PENDAFTARAN → DISetujui (Y)
            // ─────────────────────────────────────────────────────────────
            $pendaftaran->update([
                'Status' => 'Y',
                'CatatanVerifikasi' => $catatan ?? 'Pembayaran berhasil via DOKU Payment Gateway.',
                'VerifOleh' => $verifiedBy,
                'VerifPada' => now(),
            ]);

            // ─────────────────────────────────────────────────────────────
            // 2A. BUAT DATA MASTER TENANT
            // ─────────────────────────────────────────────────────────────
            $TanggalJoin = now();
            $durasiBulan = (int) ($paket->DurasiBulan ?? 1);
            $PasswordPlain = Carbon::parse($pendaftaran->created_at)->format('Ymd'); // misal: 20260905
            $TanggalBerakhir = $TanggalJoin->copy()->addMonths($durasiBulan);

            $NewTenant = Tenant::create([
                'Nama' => $pendaftaran->Nama ?? null,
                'Email' => $pendaftaran->Email ?? null,
                'Alamat' => $pendaftaran->Alamat ?? null,
                'Telepon' => $pendaftaran->Telepon ?? null,
                'NamaPIC' => $pendaftaran->NamaPIC ?? null,
                'EmailPIC' => $pendaftaran->EmailPIC ?? null,
                'AlamatPIC' => $pendaftaran->AlamatPIC ?? null,
                'TeleponPIC' => $pendaftaran->TeleponPIC ?? null,
                'TanggalJoin' => $TanggalJoin,
                'StatusSubscription' => 'Aktif',
                'TanggalMulaiSubscription' => $TanggalJoin,
                'TanggalAkhirSubscription' => $TanggalBerakhir,
                'UserCreate' => $verifiedBy,
            ]);

            // ─────────────────────────────────────────────────────────────
            // 2B. BUAT USER ADMIN UNTUK TENANT TERSEBUT
            // ─────────────────────────────────────────────────────────────
            $UserName = $pendaftaran->NamaPIC ?: $pendaftaran->Nama;
            $UserEmail = $pendaftaran->EmailPIC ?: $pendaftaran->Email;

            User::create([
                'tenant_id' => $NewTenant->Kode,
                'name' => $UserName,
                'email' => $UserEmail,
                'password' => Hash::make($PasswordPlain),
                'role' => 'Admin',
                'user_create' => $verifiedBy,
            ]);

            // ─────────────────────────────────────────────────────────────
            // 2C. BUAT TAGIHAN PEMBAYARAN PERTAMA (STATUS LUNAS)
            // ─────────────────────────────────────────────────────────────
            $PeriodeBulan = now()->format('Y-m');
            $TanggalJatuhTempo = now(); // Sudah langsung lunas karena sudah bayar

            // Referensi bukti pembayaran (simpan invoice DOKU sebagai referensi)
            $buktiBayarRef = $pendaftaran->DokuInvoiceNumber
                ?? ('DOKU-' . $pendaftaran->id . '-' . now()->format('YmdHis'));

            TagihanPembayaran::create([
                'TenantId' => $NewTenant->Kode,
                'PeriodeBulan' => $PeriodeBulan,
                'TanggalJatuhTempo' => $TanggalJatuhTempo,
                'JumlahTagihan' => $paket->Harga ?? 0, // Dinamis dari harga paket
                'StatusPembayaran' => 'Lunas',
                'TanggalPembayaran' => $pendaftaran->PaidAt ?? now(),
                'BerlakuHingga' => $TanggalBerakhir,
                'BuktiPembayaran' => $buktiBayarRef,
                'Catatan' => 'Pembayaran via DOKU Payment Gateway — Token: '
                    . ($pendaftaran->DokuTokenId ?? '-'),
                'Status' => 'N/A',
                'CatatanVerifikasi' => null,
                'VerifPada' => null,
                'VerifOleh' => null,
                'UserCreate' => $verifiedBy,
            ]);

            // ─────────────────────────────────────────────────────────────
            // COMMIT TRANSACTION
            // ─────────────────────────────────────────────────────────────
            DB::commit();

            // ─────────────────────────────────────────────────────────────
            // 3. FLASH KREDENSIAL KE SESSION (untuk ditampilkan sekali)
            // ─────────────────────────────────────────────────────────────
            if ($flashPasswordToSession) {
                session()->flash('provisioned_credentials', [
                    'email' => $UserEmail,
                    'password' => $PasswordPlain,
                    'name' => $UserName,
                    'tenant' => $NewTenant->Nama,
                    'kode' => $NewTenant->Kode,
                ]);
            }

            // ─────────────────────────────────────────────────────────────
            // 4. KIRIM EMAIL APPROVAL
            // ─────────────────────────────────────────────────────────────
            if (!empty($UserEmail)) {
                try {
                    $LoginUrl = url('/login');

                    Mail::to($UserEmail)->send(new TenantApprovedMail(
                        $UserName,
                        $UserEmail,
                        $PasswordPlain,
                        $LoginUrl
                    ));

                    Log::info('✅ Email approval berhasil dikirim.', [
                        'to' => $UserEmail,
                        'tenant' => $NewTenant->Kode,
                    ]);
                } catch (Exception $mailException) {
                    // Log error email tapi JANGAN gagalkan provisioning
                    Log::error('❌ Gagal kirim email approval (provisioning tetap lanjut).', [
                        'to' => $UserEmail,
                        'error' => $mailException->getMessage(),
                        'tenant' => $NewTenant->Kode,
                    ]);
                }
            }

            return [
                'success' => true,
                'tenant' => $NewTenant,
                'message' => "Tenant '{$NewTenant->Nama}' berhasil diaktivasi.",
                'error' => null,
            ];
        } catch (Exception $e) {
            DB::rollBack();

            Log::error('❌ Tenant provisioning GAGAL.', [
                'pendaftaran_id' => $pendaftaran->id,
                'invoice' => $pendaftaran->DokuInvoiceNumber ?? null,
                'error' => $e->getMessage(),
                'file' => $e->getFile() . ':' . $e->getLine(),
            ]);

            return [
                'success' => false,
                'tenant' => null,
                'message' => null,
                'error' => 'Gagal provisioning: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Helper: cek apakah pendaftaran sudah di-provision (sudah ada tenant).
     */
    public function isProvisioned(PendaftaranTenant $pendaftaran): bool
    {
        return $pendaftaran->Status === 'Y'
            && Tenant::where('Email', $pendaftaran->Email)->exists();
    }
}
