<?php

namespace App\Http\Controllers;

use App\Models\MasterPaketHarga;
use App\Models\TagihanPembayaran;
use App\Models\Tenant;
use App\Services\DokuService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;

class TagihanPembayaranController extends Controller
{
    public function Index(Request $Request)
    {
        if ($Request->ajax()) {
            $User = auth()->user();
            $User = auth()->user();
            $Query = TagihanPembayaran::with('Tenant','getTenant')->latest('id');
            // Tampilkan semua jika Superadmin, filter berdasarkan KodeTenant jika bukan
            if (auth()->user()->role !== 'Superadmin') {
                $Query = $Query->where('KodeTenant', auth()->user()->KodeTenant);
            }

            // Tambahkan filter berdasarkan kode tenant jika tersedia pada user login


            // ✅ TAMBAHAN: Logika Filter Tahun dan Bulan
            $FilterTahun = $Request->FilterTahun ?? null;
            $FilterBulan = $Request->FilterBulan ?? null;

            if ($FilterTahun && $FilterBulan) {
                // Gabungkan menjadi format YYYY-MM (contoh: 2026-08)
                $PeriodeBulan = $FilterTahun . '-' . str_pad($FilterBulan, 2, '0', STR_PAD_LEFT);
                $Query->where('PeriodeBulan', $PeriodeBulan);
            } elseif ($FilterTahun) {
                // Jika hanya tahun yang dipilih, cari semua bulan di tahun tersebut
                $Query->where('PeriodeBulan', 'like', $FilterTahun . '-%');
            }

            // Keamanan: Jika bukan Superadmin, paksa filter berdasarkan KodeTenant
            if ($User->role !== 'Superadmin' && isset($User->KodeTenant)) {
                $Query->where('KodeTenant', $User->KodeTenant);
            }

            // Filter Tenant (HANYA berlaku jika Superadmin)
            if ($User->role === 'Superadmin' && $Request->filled('KodeTenant')) {
                $Query->where('KodeTenant', $Request->KodeTenant);
            }

            return DataTables::of($Query)
                ->addIndexColumn()
                // Tambahkan kolom KodeTenant pada datatable
                ->addColumn('KodeTenant', function ($Row) {
                    return $Row->Tenant ? $Row->Tenant->Kode : '-';
                })
                ->editColumn('NomorTagihan', function ($Row) {
                    return '<span class="fw-semibold text-primary">' . $Row->NomorTagihan . '</span>';
                })
                ->editColumn('NamaTenant', function ($Row) {
                    return $Row->Tenant ? $Row->Tenant->Nama : '-';
                })
                ->addColumn('TanggalBayar', function ($Row) {
                    if ($Row->TanggalPembayaran) {
                        return \Carbon\Carbon::parse($Row->TanggalPembayaran)->format('d-m-Y');
                    }
                    return '<span class="text-muted">-</span>';
                })
                ->editColumn('JumlahTagihan', function ($Row) {
                    return 'Rp ' . number_format($Row->JumlahTagihan, 0, ',', '.');
                })
                ->editColumn('StatusPembayaran', function ($Row) {
                    $Badge = match ($Row->StatusPembayaran) {
                        'Lunas' => 'bg-success',
                        'Terlambat' => 'bg-danger',
                        default => 'bg-warning text-dark'
                    };
                    return '<span class="badge ' . $Badge . '">' . $Row->StatusPembayaran . '</span>';
                })
                ->editColumn('BuktiPembayaran', function ($Row) {
                    if ($Row->BuktiPembayaran) {
                        return '<a href="' . asset('storage/' . $Row->BuktiPembayaran) . '" target="_blank" class="btn btn-sm btn-outline-primary"><i class="ti ti-eye"></i></a>';
                    }
                    return '<span class="text-muted">-</span>';
                })
                ->addColumn('action', function ($Row) {
                    $Btn = '<div class="d-flex gap-1 justify-content-center">';
                    $Btn .= '<a href="' . route('tagihan-pembayaran.show', $Row->id) . '" class="btn btn-info btn-sm text-white" title="Lihat Detail"><i class="ti ti-eye"></i></a> ';

                    if ($Row->StatusPembayaran !== 'Lunas') {
                        $Btn .= '<a href="' . route('tagihan-pembayaran.konfirmasi', $Row->id) . '" class="btn btn-success btn-sm text-white" title="Konfirmasi Bayar"><i class="ti ti-check"></i></a> ';
                    }
                    $Btn .= '<button type="button" class="btn btn-danger btn-sm btn-hapus" data-id="' . $Row->id . '" data-nomor="' . htmlspecialchars($Row->NomorTagihan) . '" title="Hapus"><i class="ti ti-trash"></i></button>';
                    $Btn .= '</div>';
                    return $Btn;
                })
                // Tambahkan 'KodeTenant' ke daftar kolom yang boleh mengandung HTML mentah jika ingin ditampilkan dalam bentuk HTML
                ->rawColumns(['KodeTenant', 'NomorTagihan', 'NamaTenant', 'StatusPembayaran', 'BuktiPembayaran', 'action'])
                ->make(true);
        }

        $User = auth()->user();
        $Tenants = $User->role === 'Superadmin'
            ? Tenant::select('id', 'Kode', 'Nama')->orderBy('Nama', 'asc')->get()
            : Tenant::select('id', 'Kode', 'Nama')->where('Kode', $User->KodeTenant ?? 0)->get();
        $Now = Carbon::now();
        $SevenDaysFromNow = $Now->copy()->addDays(7);

        $TenantAkanHabis = Tenant::where('StatusSubscription', 'Aktif')->where('Kode', auth()->user()->KodeTenant)
            ->whereBetween('TanggalAkhirSubscription', [$Now, $SevenDaysFromNow])
            ->orderBy('TanggalAkhirSubscription', 'asc')
            ->get();
        return view('tagihan-pembayaran.index', compact('Tenants', 'TenantAkanHabis'));
    }

    public function Create()
    {
        $user = auth()->user();
        $KodeTenant = $user->KodeTenant ?? null;
        if ($user->role === 'Superadmin') {
            $Tenants = Tenant::where('StatusSubscription', 'Aktif')->get(['id', 'Kode', 'Nama']);
        } else {
            $Tenants = Tenant::where('StatusSubscription', 'Aktif')
                ->where('Kode', $KodeTenant)
                ->get(['id', 'Kode', 'Nama']);
        }
        $Paket = MasterPaketHarga::get();
        return view('tagihan-pembayaran.create', compact('Tenants', 'KodeTenant', 'Paket'));
    }

    public function Show(TagihanPembayaran $TagihanPembayaran)
    {
        // Muat relasi Tenant agar data nama tenant dan kode tenant tersedia di view
        $TagihanPembayaran->load('Tenant');
        return view('tagihan-pembayaran.show', compact('TagihanPembayaran'));
    }

    public function Store(Request $Request, DokuService $doku)
    {
        // 1. Validasi Input (sesuaikan dengan nama field di form view)
        // dd($Request->all());
        $Request->validate([
            'TenantId' => 'required|exists:tenants,Kode',
            'Paket' => 'nullable|exists:master_paket_hargas,id',
            'PeriodeBulan' => 'required|date_format:Y-m',
            'harga' => 'required|string',  // ← FIELD DI VIEW = 'harga'
            'TanggalJatuhTempo' => 'required|date',
            'BerlakuHingga' => 'required|date|after_or_equal:TanggalJatuhTempo',
            'Catatan' => 'nullable|string|max:1000',
        ], [
            'required' => ':attribute wajib diisi.',
            'exists' => ':attribute tidak valid.',
            'date' => ':attribute harus berupa tanggal.',
            'date_format' => 'Format :attribute harus YYYY-MM.',
            'after_or_equal' => ':attribute harus sama atau setelah Tanggal Jatuh Tempo.',
        ]);
        // dd($Request->all());
        // 2. Bersihkan format angka (Rp 149.000 → 149000)
        $CleanAmount = (int) preg_replace('/[^0-9]/', '', $Request->harga);
        if ($CleanAmount <= 0) {
            return back()->withErrors(['harga' => 'Jumlah tagihan harus lebih dari 0.'])->withInput();
        }
        // 3. Ambil data tenant
        $tenant = Tenant::where('Kode', $Request->TenantId)->first();
        // dd($tenant);

        // 4. Generate nomor invoice DOKU
        $invoiceNumber = 'INV-' . date('Ymd') . '-' . strtoupper(Str::random(6));
        // dd($Request->all());
        // 5. Siapkan data tagihan
        $Data = [
            'TenantId' => $tenant->Kode,
            'KodeTenant' => $tenant->Kode,
            'NomorTagihan' => $invoiceNumber,
            'Paket' => $Request->Paket,
            'PeriodeBulan' => $Request->PeriodeBulan,
            'TanggalJatuhTempo' => $Request->TanggalJatuhTempo,
            'JumlahTagihan' => $CleanAmount,
            'StatusPembayaran' => 'Belum Bayar',
            'PaymentStatus' => 'PENDING',
            'TanggalPembayaran' => null,
            'BerlakuHingga' => $Request->BerlakuHingga,
            'BuktiPembayaran' => null,
            'Catatan' => $Request->Catatan,
            'Status' => 'N/A',
            'CatatanVerifikasi' => null,
            'DokuInvoiceNumber' => $invoiceNumber,
            'UserCreate' => Auth::user()->name ?? 'System',
        ];
        // dd($Data);
        // 6. Simpan tagihan dulu
        $tagihan = TagihanPembayaran::create($Data);

        // 7. Normalize phone tenant
        $rawPhone = $tenant->TeleponPIC ?? $tenant->Telepon ?? '';
        $normalizedPhone = $doku->normalizePhone($rawPhone);


        // 8. 🔥 FIX: Generate URL tanpa forceScheme (sudah di AppServiceProvider)
        $callbackUrl = route('tagihan-pembayaran.payment-finish', $tagihan->id);
        $notificationUrl = route('webhooks.doku');

        // 9. Generate DOKU Checkout
        $result = $doku->createCheckout([
            'amount' => $CleanAmount,
            'invoice_number' => $invoiceNumber,
            'callback_url' => $callbackUrl,
            'notification_url' => $notificationUrl,
            'payment_due_date' => 60 * 24,  // 24 jam untuk tagihan
            'customer_id' => 'TENANT-' . $tenant->Kode,
            'customer_name' => $tenant->NamaPIC ?? $tenant->Nama,
            'customer_email' => $tenant->EmailPIC ?? $tenant->Email,
            'customer_phone' => $normalizedPhone,
        ]);
        // dd($result);
        if (!$result['success']) {
            Log::error('DOKU Checkout failed for tagihan', [
                'tagihan_id' => $tagihan->id,
                'status' => $result['status'],
                'body' => $result['body'],
            ]);

            $tagihan->update(['PaymentStatus' => 'FAILED']);

            return redirect()
            ->route('tagihan-pembayaran.index')
            ->with('error', 'Tagihan berhasil dibuat, tapi link pembayaran gagal dibuat: '
            . ($result['body']['error']['message'] ?? $result['body']['message'][0] ?? 'Unknown error'));
        }

        $responseBody = $result['body'];

        // 10. Update tagihan dengan data dari DOKU
        $updateData = [
            'DokuPaymentUrl' => $responseBody['response']['payment']['url'] ?? null,
            'DokuTokenId' => $responseBody['response']['payment']['token_id'] ?? null,
            'DokuSessionId' => $responseBody['response']['order']['session_id'] ?? null,
        ];

        // Parse expired_date dengan aman
        if (!empty($responseBody['response']['payment']['expired_date'])) {
            try {
                $updateData['PaymentExpiredAt'] = \Carbon\Carbon::createFromFormat(
                    'YmdHis',
                    $responseBody['response']['payment']['expired_date']
                );
            } catch (\Exception $e) {
                $updateData['PaymentExpiredAt'] = now()->addHours(24);
            }
        } else {
            $updateData['PaymentExpiredAt'] = now()->addHours(24);
        }

        $tagihan->update($updateData);

        return redirect()
        ->route('tagihan-pembayaran.payment-finish', $tagihan->id)
            ->with('success', 'Tagihan berhasil dibuat. Link pembayaran siap dibagikan ke tenant.');
        }

    public function Edit(TagihanPembayaran $TagihanPembayaran)
    {
        // Load data tenants beserta KodeTenant
        $Tenants = Tenant::where('StatusSubscription', 'Aktif')->orderBy('Nama', 'asc')->get(['id', 'Kode', 'Nama']);

        // Load relasi tenant agar nama tenant dan kode tenant tersedia di view
        $TagihanPembayaran->load('Tenant');

        return view('tagihan-pembayaran.edit', compact('TagihanPembayaran', 'Tenants'));
    }

    public function Update(Request $Request, TagihanPembayaran $TagihanPembayaran)
    {
        // 1. Validasi Input (Bukti Pembayaran jadi nullable agar tidak wajib diganti)
        $Request->validate([
            'KodeTenant' => 'required|exists:tenants,id',
            'PeriodeBulan' => 'required|date_format:Y-m',
            'JumlahTagihan' => 'required|string',
            'TanggalPembayaran' => 'required|date',
            'BerlakuHingga' => 'required|date|after_or_equal:TanggalPembayaran',
            'BuktiPembayaran' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:2048',
            'Catatan' => 'nullable|string|max:1000',
        ]);

        // 2. Bersihkan format angka
        $CleanAmount = str_replace('.', '', $Request->JumlahTagihan);

        // 3. Siapkan data untuk diupdate
        $Data = $Request->except(['JumlahTagihan', 'BuktiPembayaran', 'BerlakuHingga']);
        $Data['JumlahTagihan'] = $CleanAmount;
        $Data['TanggalJatuhTempo'] = $Request->BerlakuHingga;  // Mapping ke DB
        $Data['UserUpdate'] = Auth::user()->name ?? 'System';

        // Update KodeTenant sesuai KodeTenant yang terbaru
        $tenant = Tenant::find($Request->KodeTenant);
        $Data['KodeTenant'] = $tenant ? $tenant->Kode : null;

        // 4. Handle Upload File Baru (Jika ada)
        if ($Request->hasFile('BuktiPembayaran')) {
            // Hapus file lama dari storage jika ada
            if ($TagihanPembayaran->BuktiPembayaran && Storage::disk('public')->exists($TagihanPembayaran->BuktiPembayaran)) {
                Storage::disk('public')->delete($TagihanPembayaran->BuktiPembayaran);
            }

            // Upload file baru
            $File = $Request->file('BuktiPembayaran');
            $FileName = time() . '_' . preg_replace('/[^A-Za-z0-9\-_\.]/', '', $File->getClientOriginalName());
            $Data['BuktiPembayaran'] = $File->storeAs('tagihan', $FileName, 'public');
        }

        // 5. Update ke Database
        $TagihanPembayaran->update($Data);

        return redirect()->route('tagihan-pembayaran.index')->with('success', 'Tagihan pembayaran berhasil diperbarui.');
    }

    public function Destroy(TagihanPembayaran $TagihanPembayaran)
    {
        try {
            if ($TagihanPembayaran->BuktiPembayaran && Storage::disk('public')->exists($TagihanPembayaran->BuktiPembayaran)) {
                Storage::disk('public')->delete($TagihanPembayaran->BuktiPembayaran);
            }

            $TagihanPembayaran->UserDelete = Auth::user()->name ?? 'System';
            $TagihanPembayaran->save();
            $TagihanPembayaran->delete();

            return response()->json(['success' => true, 'message' => 'Tagihan berhasil dihapus.']);
        } catch (\Exception $Exception) {
            return response()->json(['success' => false, 'message' => 'Gagal menghapus tagihan.'], 500);
        }
    }

    // ✅ METHOD TAMBAHAN: Form Konfirmasi Pembayaran
    public function KonfirmasiForm(TagihanPembayaran $TagihanPembayaran)
    {
        return view('tagihan-pembayaran.konfirmasi', compact('TagihanPembayaran'));
    }

    public function BulkApprove(Request $Request)
    {
        // Validation
        $Request->validate([
            'Ids' => 'required|array|min:1',
            'Ids.*' => 'exists:tagihan_pembayarans,id',
            'Status' => 'required|in:Y,N,N/A',
            'CatatanVerifikasi' => 'nullable|string|max:1000',
            'VerifPada' => 'nullable|date',
            'VerifOleh' => 'nullable|string|max:200',
        ]);

        $UpdatedCount = 0;
        $FailedIds = [];
        $UserName = $Request->VerifOleh ?? (Auth::user()->name ?? 'System');
        $VerifDate = $Request->VerifPada ?? now();

        foreach ($Request->Ids as $Id) {
            try {
                $Tagihan = TagihanPembayaran::findOrFail($Id);

                $UpdateData = [
                    'Status' => $Request->Status,
                    'CatatanVerifikasi' => $Request->CatatanVerifikasi,
                    'VerifPada' => $VerifDate,
                    'VerifOleh' => $UserName,
                ];
                if ($Request->filled('CatatanVerifikasi')) {
                    $UpdateData['Catatan'] = $Request->CatatanVerifikasi;
                }

                // Update tenant subscription if Status is 'Y'
                if ($Request->Status === 'Y' && $Tagihan->KodeTenant) {
                    $tenant = Tenant::where('Kode', $Tagihan->KodeTenant)->first();
                    if ($tenant) {
                        $tenant->StatusSubscription = 'Aktif';
                        $tenant->TanggalMulaiSubscription = now();
                        $tenant->TanggalAkhirSubscription = now()->copy()->addMonth();
                        if (!$tenant->save()) {
                            throw new \Exception("Gagal menyimpan perubahan tenant (ID: $Id)");
                        }
                    }
                }

                if (!$Tagihan->update($UpdateData)) {
                    throw new \Exception("Gagal update tagihan (ID: $Id)");
                }

                $UpdatedCount++;
            } catch (\Throwable $e) {
                $FailedIds[] = [
                    'id' => $Id,
                    'error' => $e->getMessage()
                ];
                // Optionally: Log::error("BulkApprove error: ".$e->getMessage());
            }
        }

        if (count($FailedIds) > 0) {
            return response()->json([
                'success' => false,
                'message' => "Sebagian tagihan gagal diverifikasi. Berhasil: {$UpdatedCount}. Gagal: " . count($FailedIds),
                'failed' => $FailedIds
            ], 500);
        }

        return response()->json([
            'success' => true,
            'message' => "Berhasil memverifikasi {$UpdatedCount} tagihan pembayaran dengan status {$Request->Status}."
        ]);
    }

    public function KonfirmasiProses(Request $Request, TagihanPembayaran $TagihanPembayaran)
    {
        $Request->validate([
            'TanggalPembayaran' => 'required|date',
            'StatusVerifikasi' => 'required|in:Y,N,N/a,N/A',
            'Catatan' => 'nullable|string|max:1000',
        ]);

        $UserName = Auth::user()->name ?? 'System';
        $VerifDate = $Request->TanggalPembayaran ?? now();
        $StatusVerifikasi = $Request->StatusVerifikasi;

        $UpdateData = [
            'StatusPembayaran' => $StatusVerifikasi === 'Y' ? 'Lunas' : ($StatusVerifikasi === 'N' ? 'Ditolak' : 'N/A'),
            'TanggalPembayaran' => $VerifDate,
            'Catatan' => $Request->Catatan,
            'UserUpdate' => $UserName,
        ];

        if ($Request->hasFile('BuktiPembayaran')) {
            if ($TagihanPembayaran->BuktiPembayaran && Storage::disk('public')->exists($TagihanPembayaran->BuktiPembayaran)) {
                Storage::disk('public')->delete($TagihanPembayaran->BuktiPembayaran);
            }
            $File = $Request->file('BuktiPembayaran');
            $FileName = time() . '_' . preg_replace('/[^A-Za-z0-9\-_\.]/', '', $File->getClientOriginalName());
            $UpdateData['BuktiPembayaran'] = $File->storeAs('tagihan', $FileName, 'public');
        }

        // Jika status Y, update data subscription di model Tenant terkait
        if ($StatusVerifikasi === 'Y' && $TagihanPembayaran->KodeTenant) {
            $tenant = Tenant::where('Kode', $TagihanPembayaran->KodeTenant)->first();
            if ($tenant) {
                $tenant->StatusSubscription = 'Aktif';
                $tenant->TanggalMulaiSubscription = now();
                $tenant->TanggalAkhirSubscription = now()->copy()->addMonth();
                $tenant->save();
            }
        }

        $TagihanPembayaran->update($UpdateData);

        return redirect()->route('tagihan-pembayaran.index')->with(
            'success',
            'Pembayaran berhasil diverifikasi. Status telah diubah menjadi ' . $UpdateData['StatusPembayaran'] . '.'
        );
    }

    public function paymentFinish(Request $request, $id)
    {
        $tagihan = TagihanPembayaran::with(['Tenant', 'Paket'])->findOrFail($id);

        // Cek status DOKU sekali (user-facing request)
        if ($tagihan->PaymentStatus === 'PENDING' && $tagihan->DokuInvoiceNumber) {
            $doku = app(DokuService::class);
            $result = $doku->checkPaymentStatus($tagihan->DokuInvoiceNumber);

            if ($result['success'] && isset($result['body']['transaction']['status'])) {
                $dokuStatus = strtoupper($result['body']['transaction']['status']);
                $channel = $result['body']['channel']['id'] ?? null;

                $this->handleDokuStatusUpdate($tagihan, $dokuStatus, $channel);
                $tagihan->refresh();
            }
        }

        return view('tagihan-pembayaran.payment-finish', compact('tagihan'));
    }

    public function checkPaymentStatus(Request $request, $id)
    {
        // ✅ Eager load Tenant & Paket (penting untuk update subscription)
        $tagihan = TagihanPembayaran::with(['Tenant', 'Paket'])->find($id);

        if (!$tagihan) {
            return response()->json(['success' => false, 'error' => 'Not found'], 404);
        }

        // Cek status DOKU kalau masih PENDING
        if ($tagihan->PaymentStatus === 'PENDING' && $tagihan->DokuInvoiceNumber) {
            $doku = app(DokuService::class);
            $result = $doku->checkPaymentStatus($tagihan->DokuInvoiceNumber);

            if ($result['success'] && isset($result['body']['transaction']['status'])) {
                $dokuStatus = strtoupper($result['body']['transaction']['status']);
                $channel = $result['body']['channel']['id'] ?? null;

                // 🔥 Pakai helper yang SAMA — idempotent, no double-update
                $this->handleDokuStatusUpdate($tagihan, $dokuStatus, $channel);
                $tagihan->refresh();
            }
        }

        return response()->json([
            'success' => true,
            'data' => [
                'payment_status' => $tagihan->PaymentStatus,
                'status_pembayaran' => $tagihan->StatusPembayaran,
                'paid_at' => $tagihan->PaidAt?->format('d M Y H:i'),
                'payment_channel' => $tagihan->PaymentChannel,
            ],
        ]);
    }
    protected function handleDokuStatusUpdate(TagihanPembayaran $tagihan, string $dokuStatus, ?string $channel = null): void
    {
        $now = now();

        if ($dokuStatus === 'SUCCESS') {
            // ✅ Guard: Skip kalau sudah PAID (idempotency)
            if ($tagihan->PaymentStatus === 'PAID') {
                \Log::info('⏭️ Skip: tagihan sudah PAID sebelumnya', [
                    'tagihan_id' => $tagihan->id,
                ]);
                return;
            }

            // 1. Update Tagihan
            $tagihan->update([
                'PaymentStatus' => 'PAID',
                'StatusPembayaran' => 'Lunas',
                'TanggalPembayaran' => $now,
                'PaidAt' => $now,
                'PaymentChannel' => $channel,
                'BuktiPembayaran' => 'DOKU-' . $tagihan->DokuInvoiceNumber,
                'UserUpdate' => Auth::user()->name ?? 'System (DOKU)',
            ]);

            // 2. Update Tenant Subscription (hanya kalau ada relasi)
            $this->updateTenantSubscription($tagihan, $now);

        } elseif ($dokuStatus === 'FAILED') {
            if ($tagihan->PaymentStatus !== 'FAILED') {
                $tagihan->update(['PaymentStatus' => 'FAILED']);
            }
        } elseif ($dokuStatus === 'EXPIRED') {
            if ($tagihan->PaymentStatus !== 'EXPIRED') {
                $tagihan->update(['PaymentStatus' => 'EXPIRED']);
            }
        }
    }

    /**
     * Update subscription tenant dengan logika perpanjangan
     */
    protected function updateTenantSubscription(TagihanPembayaran $tagihan, Carbon $paidAt): void
    {
        $tenant = $tagihan->Tenant;

        if (!$tenant) {
            Log::warning('⚠️ Tenant tidak ditemukan', [
                'tagihan_id' => $tagihan->id,
                'kode_tenant' => $tagihan->KodeTenant,
            ]);
            return;
        }

        // Ambil durasi dari relasi Paket
        $durasiBulan = 1;
        if ($tagihan->Paket && !empty($tagihan->Paket->DurasiBulan)) {
            $durasiBulan = (int) $tagihan->Paket->DurasiBulan;
        }

        // ✅ SAFE GUARD: Convert ke Carbon kalau masih string
        $existingAkhir = $tenant->TanggalAkhirSubscription;
        if ($existingAkhir && is_string($existingAkhir)) {
            $existingAkhir = \Carbon\Carbon::parse($existingAkhir);
        }

        // Logika: perpanjang dari akhir sebelumnya, atau mulai baru
        if (
            $tenant->StatusSubscription === 'Aktif'
            && $existingAkhir
            && $existingAkhir->gte($paidAt)
        ) {
            $mulai = $existingAkhir->copy();
        } else {
            $mulai = $paidAt->copy();
        }

        $akhir = $mulai->copy()->addMonths($durasiBulan);

        $tenant->update([
            'StatusSubscription' => 'Aktif',
            'TanggalMulaiSubscription' => $mulai,
            'TanggalAkhirSubscription' => $akhir,
            'UserUpdate' => Auth::user()->name ?? 'System (DOKU)',
        ]);

        Log::info('✅ Tenant subscription updated', [
            'tenant_kode' => $tenant->Kode,
            'durasi_bulan' => $durasiBulan,
            'mulai' => $mulai->toDateTimeString(),
            'akhir' => $akhir->toDateTimeString(),
        ]);
    }
}
