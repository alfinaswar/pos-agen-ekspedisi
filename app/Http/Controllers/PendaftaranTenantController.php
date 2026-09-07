<?php

namespace App\Http\Controllers;

use App\Models\MasterPaketHarga;
use App\Models\PendaftaranTenant;
use App\Services\DokuService;
use App\Services\TenantProvisioningService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Yajra\DataTables\DataTables;

class PendaftaranTenantController extends Controller
{
    public function __construct(
        protected TenantProvisioningService $provisioning
    ) {
    }

    /**
     * Display a listing
     */
    public function Index(Request $Request)
    {
        if ($Request->ajax()) {
            $Query = PendaftaranTenant::latest('created_at');

            if ($Request->filled('TanggalAwal')) {
                $Query->whereDate('created_at', '>=', $Request->TanggalAwal);
            }
            if ($Request->filled('TanggalAkhir')) {
                $Query->whereDate('created_at', '<=', $Request->TanggalAkhir);
            }

            return DataTables::of($Query)
                ->addIndexColumn()
                ->editColumn('Status', function ($Row) {
                    $Badge = match ($Row->Status) {
                        'Y' => 'bg-success',
                        'N' => 'bg-danger',
                        default => 'bg-secondary'
                    };
                    $Label = match ($Row->Status) {
                        'Y' => 'Disetujui',
                        'N' => 'Ditolak',
                        default => 'Belum Diverifikasi'
                    };
                    return '<span class="badge ' . $Badge . '">' . $Label . '</span>';
                })
                ->editColumn('PaymentStatus', function ($Row) {
                    $Badge = match ($Row->PaymentStatus) {
                        'PAID' => 'bg-success',
                        'FAILED' => 'bg-danger',
                        'EXPIRED' => 'bg-warning',
                        default => 'bg-info'
                    };
                    return '<span class="badge ' . $Badge . '">' . ($Row->PaymentStatus ?? 'PENDING') . '</span>';
                })
                ->editColumn('BuktiPembayaran', function ($Row) {
                    return '<span class="text-muted">Via DOKU</span>';
                })
                ->addColumn('action', function ($Row) {
                    $Btn = '<div class="d-flex gap-1 justify-content-center">';
                    $Btn .= '<a href="' . route('pendaftaran-tenant.show', $Row->id) . '" class="btn btn-info btn-sm text-white" title="Detail"><i class="ti ti-eye"></i></a> ';
                    $Btn .= '<button type="button" class="btn btn-danger btn-sm btn-hapus" data-id="' . $Row->id . '" data-nama="' . htmlspecialchars($Row->Nama) . '" title="Hapus"><i class="ti ti-trash"></i></button>';
                    $Btn .= '</div>';
                    return $Btn;
                })
                ->rawColumns(['Status', 'PaymentStatus', 'BuktiPembayaran', 'action'])
                ->make(true);
        }

        return view('manejemen-tenant.pendaftaran.index');
    }

    public function create()
    {
        //
    }

    /**
     * Store pendaftaran + buat DOKU checkout
     */
    public function store(Request $request, DokuService $doku)
    {
        $validated = $request->validate([
            'Nama' => 'required|string|max:255',
            'Paket' => 'required|string|max:255',
            'Email' => 'required|email|max:255',
            'Telepon' => 'required|string|min:9|max:20',
            'Alamat' => 'required|string|max:500',
            'NamaPIC' => 'required|string|max:255',
            'EmailPIC' => 'required|email|max:255',
            'TeleponPIC' => 'nullable|string|max:20',
            'AlamatPIC' => 'nullable|string|max:500',
        ], [
            'required' => ':attribute wajib diisi.',
            'email' => ':attribute harus berupa email yang valid.',
            'min' => ':attribute minimal :min karakter.',
            'max' => ':attribute maksimal :max karakter.',
        ]);

        $paket = MasterPaketHarga::findOrFail($validated['Paket']);
        $invoiceNumber = 'MRK-' . date('Ymd') . '-' . strtoupper(Str::random(6));

        $pendaftaran = PendaftaranTenant::create([
            'Nama' => $validated['Nama'],
            'Paket' => $validated['Paket'],
            'Email' => $validated['Email'],
            'Telepon' => $validated['Telepon'],
            'Alamat' => $validated['Alamat'],
            'NamaPIC' => $validated['NamaPIC'],
            'EmailPIC' => $validated['EmailPIC'],
            'TeleponPIC' => $validated['TeleponPIC'] ?? null,
            'AlamatPIC' => $validated['AlamatPIC'] ?? null,
            'Status' => 'N/A',
            'PaymentStatus' => 'PENDING',
            'DokuInvoiceNumber' => $invoiceNumber,
        ]);

        $rawPhone = $validated['TeleponPIC'] ?? $validated['Telepon'];
        $normalizedPhone = $doku->normalizePhone($rawPhone);

        \Log::info('Phone normalization', [
            'raw' => $rawPhone,
            'normalized' => $normalizedPhone,
        ]);

        $result = $doku->createCheckout([
            'amount' => $paket->Harga,
            'invoice_number' => $invoiceNumber,
            'callback_url' => route('pendaftaran.payment.finish', $pendaftaran->id),
            'notification_url' => route('webhooks.doku'), // Tetap ada tapi tidak wajib
            'payment_due_date' => 60,
            'customer_id' => 'CUST-' . $pendaftaran->id,
            'customer_name' => $validated['NamaPIC'],
            'customer_email' => $validated['EmailPIC'],
            'customer_phone' => $normalizedPhone,
        ]);

        if (!$result['success']) {
            \Log::error('DOKU Checkout failed', [
                'status' => $result['status'],
                'body' => $result['body'],
            ]);

            $pendaftaran->update(['PaymentStatus' => 'FAILED']);

            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal membuat pembayaran: ' . ($result['body']['message'][0] ?? 'Unknown error'));
        }

        $responseBody = $result['body'];

        $pendaftaran->update([
            'DokuPaymentUrl' => $responseBody['response']['payment']['url'] ?? null,
            'DokuTokenId' => $responseBody['response']['payment']['token_id'] ?? null,
            'DokuSessionId' => $responseBody['response']['order']['session_id'] ?? null,
        ]);

        $paymentUrl = $responseBody['response']['payment']['url'] ?? null;

        if ($paymentUrl) {
            return redirect()->away($paymentUrl);
        }

        return redirect()->back()->with('error', 'Payment URL tidak ditemukan.');
    }

    /**
     * Halaman setelah user selesai bayar (callback dari DOKU)
     * Tampilkan halaman pending dengan polling otomatis
     */
    public function paymentFinish(Request $request, $id)
    {
        $pendaftaran = PendaftaranTenant::findOrFail($id);

        // Ambil credential dari session (jika ada dari auto-approve)
        $credentials = session()->pull('provisioned_credentials');

        return view('landing-page.payment-finish', compact('pendaftaran', 'credentials'));
    }

    public function Show(PendaftaranTenant $PendaftaranTenant)
    {
        return view('manejemen-tenant.pendaftaran.show', compact('PendaftaranTenant'));
    }

    /**
     * Verifikasi manual oleh admin
     */
    public function Verifikasi(Request $Request, PendaftaranTenant $PendaftaranTenant)
    {
        $Request->validate([
            'Status' => 'required|in:Y,N',
            'CatatanVerifikasi' => 'nullable|string|max:1000',
        ]);

        if ($Request->Status === 'Y') {
            if ($PendaftaranTenant->PaymentStatus !== 'PAID') {
                return redirect()->back()->with(
                    'error',
                    'Pembayaran belum lunas. Tunggu konfirmasi pembayaran.'
                );
            }

            $result = $this->provisioning->provision(
                $PendaftaranTenant,
                Auth::user()->name ?? 'Admin',
                $Request->CatatanVerifikasi,
                false
            );

            if (!$result['success']) {
                return redirect()->back()->with('error', $result['error']);
            }

            return redirect()
                ->route('pendaftaran-tenant.index')
                ->with('success', $result['message']);
        }

        // Penolakan
        $PendaftaranTenant->update([
            'Status' => 'N',
            'CatatanVerifikasi' => $Request->CatatanVerifikasi,
            'VerifOleh' => Auth::user()->name ?? 'Admin',
            'VerifPada' => now(),
        ]);

        return redirect()
            ->route('pendaftaran-tenant.index')
            ->with('success', 'Pendaftaran tenant ditolak.');
    }

    public function edit(PendaftaranTenant $pendaftaranTenant)
    {
        //
    }

    public function update(Request $request, PendaftaranTenant $pendaftaranTenant)
    {
        //
    }

    public function Destroy(PendaftaranTenant $PendaftaranTenant)
    {
        try {
            $PendaftaranTenant->delete();

            return response()->json([
                'success' => true,
                'message' => 'Data pendaftaran berhasil dihapus.'
            ]);
        } catch (\Exception $Exception) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus data: ' . $Exception->getMessage()
            ], 500);
        }
    }

    /**
     * 🔥 POLLING ENDPOINT — Dipanggil frontend setiap 3-5 detik
     * Cek status langsung ke DOKU API, langsung provision kalau SUCCESS
     */
    public function checkPaymentStatus(Request $request, $id)
    {
        $pendaftaran = PendaftaranTenant::with('getPaket')->find($id);

        if (!$pendaftaran) {
            return response()->json([
                'success' => false,
                'error' => 'Pendaftaran tidak ditemukan',
            ], 404);
        }

        // Jika sudah PAID dan sudah di-provision, langsung return
        if ($pendaftaran->PaymentStatus === 'PAID' && $pendaftaran->Status === 'Y') {
            return response()->json([
                'success' => true,
                'data' => [
                    'payment_status' => $pendaftaran->PaymentStatus,
                    'status' => $pendaftaran->Status,
                    'is_paid' => true,
                    'is_provisioned' => true,
                    'invoice_number' => $pendaftaran->DokuInvoiceNumber,
                    'paid_at' => $pendaftaran->PaidAt?->format('d M Y H:i'),
                    'payment_channel' => $pendaftaran->PaymentChannel,
                ],
            ]);
        }

        // Jika masih PENDING, cek langsung ke DOKU API
        if ($pendaftaran->PaymentStatus === 'PENDING' && $pendaftaran->DokuInvoiceNumber) {
            $doku = app(DokuService::class);
            $result = $doku->checkPaymentStatus($pendaftaran->DokuInvoiceNumber);
            // dd($result);
            if ($result['success'] && isset($result['body']['transaction']['status'])) {
                $dokuStatus = strtoupper($result['body']['transaction']['status']);

                $paymentStatus = match ($dokuStatus) {
                    'SUCCESS' => 'PAID',
                    'FAILED' => 'FAILED',
                    'EXPIRED' => 'EXPIRED',
                    default => 'PENDING',
                };

                $updateData = ['PaymentStatus' => $paymentStatus];

                if ($paymentStatus === 'PAID') {
                    $updateData['PaidAt'] = now();
                    $updateData['PaymentChannel'] = $result['body']['channel']['id'] ?? null;
                }

                $pendaftaran->update($updateData);

                // 🔥 LANGSUNG PROVISION KALAU BERHASIL BAYAR
                if ($dokuStatus === 'SUCCESS' && $pendaftaran->Status !== 'Y') {
                    \Log::info('🚀 Auto-provisioning triggered via polling', [
                        'invoice' => $pendaftaran->DokuInvoiceNumber,
                    ]);

                    $provisionResult = $this->provisioning->provision(
                        $pendaftaran,
                        'System (DOKU Auto-Approve)',
                        'Pembayaran berhasil via DOKU. Status otomatis disetujui.',
                        true // flash password ke session
                    );

                    if ($provisionResult['success']) {
                        \Log::info('✅ Auto-provisioning berhasil', [
                            'tenant' => $provisionResult['tenant']->Kode ?? null,
                        ]);
                        // Reload data terbaru
                        $pendaftaran->refresh();
                    } else {
                        \Log::error('❌ Auto-provisioning gagal', [
                            'error' => $provisionResult['error'],
                        ]);
                    }
                }
            } else {
                // Log kalau API call gagal
                \Log::warning('DOKU status check failed', [
                    'invoice' => $pendaftaran->DokuInvoiceNumber,
                    'status' => $result['status'] ?? null,
                    'body' => $result['body'] ?? null,
                ]);
            }
        }

        return response()->json([
            'success' => true,
            'data' => [
                'payment_status' => $pendaftaran->PaymentStatus,
                'status' => $pendaftaran->Status,
                'is_paid' => $pendaftaran->PaymentStatus === 'PAID',
                'is_provisioned' => $pendaftaran->Status === 'Y',
                'invoice_number' => $pendaftaran->DokuInvoiceNumber,
                'paid_at' => $pendaftaran->PaidAt?->format('d M Y H:i'),
                'payment_channel' => $pendaftaran->PaymentChannel,
            ],
        ]);
    }
}
