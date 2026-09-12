<?php

namespace App\Services;

use App\Mail\TenantApprovedMail;
use App\Models\PendaftaranTenant;
use App\Models\TagihanPembayaran;
use App\Models\Tenant;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Exception;

class TenantProvisioningService
{
    /**
     * Provision tenant: buat Tenant, User Admin, Tagihan, kirim email.
     *
     * @param  PendaftaranTenant  $pendaftaran
     * @param  string             $verifiedBy        (nama verifier, misal 'System' atau 'Admin')
     * @param  string|null        $catatan
     * @param  bool               $flashPassword     flash password ke session (untuk ditampilkan sekali)
     * @return array ['success' => bool, 'tenant' => Tenant|null, 'message' => string|null, 'error' => string|null]
     */
    public function provision(
        PendaftaranTenant $pendaftaran,
        string $verifiedBy = 'System',
        ?string $catatan = null,
        bool $flashPassword = false
    ): array {
        // Reload relasi paket
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
            // ═══════════════════════════════════════════════════
            // 1. UPDATE STATUS PENDAFTARAN → DISetujui (Y)
            // ═══════════════════════════════════════════════════
            $pendaftaran->update([
                'Status' => 'Y',
                'CatatanVerifikasi' => $catatan ?? 'Pembayaran berhasil via DOKU.',
                'VerifOleh' => $verifiedBy,
                'VerifPada' => now(),
            ]);

            // ═══════════════════════════════════════════════════
            // 2A. BUAT MASTER TENANT
            // ═══════════════════════════════════════════════════
            $TanggalJoin = now();
            $durasiBulan = (int) ($paket->DurasiBulan ?? 1);
            $PasswordPlain = Carbon::parse($pendaftaran->created_at)->format('Ymd') . Str::random(4);

            $TanggalBerakhir = $TanggalJoin->copy()->addMonths($durasiBulan);

            $NewTenant = Tenant::create([
                'Nama' => $pendaftaran->Nama,
                'Email' => $pendaftaran->Email,
                'Alamat' => $pendaftaran->Alamat,
                'Telepon' => $pendaftaran->Telepon,
                'NamaPIC' => $pendaftaran->NamaPIC,
                'EmailPIC' => $pendaftaran->EmailPIC,
                'AlamatPIC' => $pendaftaran->AlamatPIC,
                'TeleponPIC' => $pendaftaran->TeleponPIC,
                'TanggalJoin' => $TanggalJoin,
                'StatusSubscription' => 'Aktif',
                'TanggalMulaiSubscription' => $TanggalJoin,
                'TanggalAkhirSubscription' => $TanggalBerakhir,
                'UserCreate' => $verifiedBy,
            ]);

            // ═══════════════════════════════════════════════════
            // 2B. BUAT USER ADMIN
            // ═══════════════════════════════════════════════════
            $UserName = $pendaftaran->NamaPIC ?: $pendaftaran->Nama;
            $UserEmail = $pendaftaran->EmailPIC ?: $pendaftaran->EmailPIC;

            User::create([
                'tenant_id' => $NewTenant->Kode,
                'KodeTenant' => $NewTenant->Kode,
                'name' => $UserName,
                'email' => $UserEmail,
                'password' => Hash::make($PasswordPlain),
                'role' => 'Admin',
                'user_create' => $verifiedBy,
            ]);

            // ═══════════════════════════════════════════════════
            // 2C. BUAT TAGIHAN PEMBAYARAN PERTAMA (LUNAS)
            // ═══════════════════════════════════════════════════
            $PeriodeBulan = now()->format('Y-m');
            $buktiBayarRef = $pendaftaran->DokuInvoiceNumber ?? 'DOKU-' . $pendaftaran->id;

            TagihanPembayaran::create([
                'TenantId' => $NewTenant->Kode,
                'PeriodeBulan' => $PeriodeBulan,
                'TanggalJatuhTempo' => now(),
                'JumlahTagihan' => $paket->Harga ?? 0,
                'StatusPembayaran' => 'Lunas',
                'TanggalPembayaran' => $pendaftaran->PaidAt ?? now(),
                'BerlakuHingga' => $TanggalBerakhir,
                'BuktiPembayaran' => $buktiBayarRef,
                'Catatan' => 'Pembayaran via DOKU - Token: ' . ($pendaftaran->DokuTokenId ?? '-'),
                'Status' => 'Y',
                'KodeTenant' => $NewTenant->Kode,
                'CatatanVerifikasi' => 'Verifikasi Pembayaran via DOKU - Token: ' . ($pendaftaran->DokuTokenId ?? '-'),
                'VerifPada' => now(),
                'VerifOleh' => 'DOKU',
                'UserCreate' => $verifiedBy,
            ]);

            // ═══════════════════════════════════════════════════
            // COMMIT TRANSACTION
            // ═══════════════════════════════════════════════════
            DB::commit();

            // ═══════════════════════════════════════════════════
            // 3. FLASH PASSWORD KE SESSION (untuk tampil 1x)
            // ═══════════════════════════════════════════════════
            if ($flashPassword) {
                session()->flash('provisioned_credentials', [
                    'email' => $UserEmail,
                    'password' => $PasswordPlain,
                    'name' => $UserName,
                    'tenant' => $NewTenant->Nama,
                    'kode' => $NewTenant->Kode,
                ]);
            }

            // ═══════════════════════════════════════════════════
            // 4. KIRIM EMAIL APPROVAL
            // ═══════════════════════════════════════════════════
            if (!empty($UserEmail)) {
                try {
                    Mail::to($UserEmail)->send(new TenantApprovedMail(
                        $UserName,
                        $UserEmail,
                        $PasswordPlain,
                        url('/login'),
                        $paket->NamaPaket ?? null,  // PaketNama
                        $TanggalJoin->translatedFormat('d F Y'),  // TanggalMulai
                        $TanggalBerakhir->translatedFormat('d F Y')  // TanggalBerakhir
                    ));
                    Log::info('✅ Email approval terkirim ke ' . $UserEmail);
                } catch (Exception $mailEx) {
                    Log::error('❌ Gagal kirim email: ' . $mailEx->getMessage());
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
            Log::error('❌ Provisioning GAGAL', [
                'pendaftaran_id' => $pendaftaran->id,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'tenant' => null,
                'message' => null,
                'error' => 'Provisioning gagal: ' . $e->getMessage(),
            ];
        }
    }
}
