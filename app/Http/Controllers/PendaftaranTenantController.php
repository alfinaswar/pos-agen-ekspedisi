<?php

namespace App\Http\Controllers;

use App\Mail\TenantApprovedMail;
use App\Models\MasterPaketHarga;
use App\Models\PendaftaranTenant;
use App\Models\TagihanPembayaran;
use App\Models\Tenant;
use App\Models\User;
use App\Services\DokuService;
use App\Services\TenantProvisioningService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Str;

class PendaftaranTenantController extends Controller
{
    public function __construct(
        protected TenantProvisioningService $provisioning
    ) {
    }
    /**
     * Display a listing of the resource.
     */
    public function Index(Request $Request)
    {
        if ($Request->ajax()) {
            $Query = PendaftaranTenant::latest('created_at');

            // ✅ TAMBAHAN: Logika Filter Tanggal (berdasarkan created_at)
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
                ->editColumn('BuktiPembayaran', function ($Row) {
                    if ($Row->BuktiPembayaran) {
                        return '<a href="' . asset('storage/' . $Row->BuktiPembayaran) . '" target="_blank" class="btn btn-sm btn-outline-primary"><i class="ti ti-eye"></i></a>';
                    }
                    return '<span class="text-muted">-</span>';
                })
                ->addColumn('action', function ($Row) {
                    $Btn = '<div class="d-flex gap-1 justify-content-center">';
                    $Btn .= '<a href="' . route('pendaftaran-tenant.show', $Row->id) . '" class="btn btn-info btn-sm text-white" title="Verifikasi"><i class="ti ti-eye"></i></a> ';
                    // ✅ TAMBAHAN: Tombol Hapus
                    $Btn .= '<button type="button" class="btn btn-danger btn-sm btn-hapus" data-id="' . $Row->id . '" data-nama="' . htmlspecialchars($Row->Nama) . '" title="Hapus"><i class="ti ti-trash"></i></button>';
                    $Btn .= '</div>';
                    return $Btn;
                })
                ->rawColumns(['Status', 'BuktiPembayaran', 'action'])
                ->make(true);
        }

        return view('manejemen-tenant.pendaftaran.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
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

        // 🔥 NORMALISASI NOMOR TELEPON DI SINI
        $rawPhone = $validated['TeleponPIC'] ?? $validated['Telepon'];
        $normalizedPhone = $doku->normalizePhone($rawPhone);

        // Debug: Log nomor sebelum dan sesudah normalisasi
        \Log::info('Phone normalization', [
            'raw' => $rawPhone,
            'normalized' => $normalizedPhone,
        ]);

        $result = $doku->createCheckout([
            'amount' => $paket->Harga,
            'invoice_number' => $invoiceNumber,
            'callback_url' => route('pendaftaran.payment.finish', $pendaftaran->id),
            'notification_url' => route('webhooks.doku'),
            'payment_due_date' => 60,
            'customer_id' => 'CUST-' . $pendaftaran->id,
            'customer_name' => $validated['NamaPIC'],
            'customer_email' => $validated['EmailPIC'],
            'customer_phone' => $normalizedPhone, // ← Pakai yang sudah dinormalisasi
        ]);

        if (!$result['success']) {
            \Log::error('DOKU Checkout failed', [
                'status' => $result['status'],
                'body' => $result['body'],
                'raw' => $result['raw'],
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
     */

    public function paymentFinish(Request $request, $id)
    {
        $pendaftaran = PendaftaranTenant::findOrFail($id);
        // Ambil credential dari session (jika ada)
        $credentials = session()->pull('provisioned_credentials');
        return view('landing-page.payment-finish', compact('pendaftaran', 'credentials'));
    }

    /**
     * Display the specified resource.
     */
    public function Show(PendaftaranTenant $PendaftaranTenant)
    {
        return view('manejemen-tenant.pendaftaran.show', compact('PendaftaranTenant'));
    }

    // ✅ METHOD BARU: Proses Verifikasi
    public function Verifikasi(Request $Request, PendaftaranTenant $PendaftaranTenant)
    {
        $Request->validate([
            'Status' => 'required|in:Y,N',
            'CatatanVerifikasi' => 'nullable|string|max:1000',
        ]);

        if ($Request->Status === 'Y') {
            // Cek apakah pembayaran sudah lunas dulu
            if ($PendaftaranTenant->PaymentStatus !== 'PAID') {
                return redirect()->back()->with(
                    'error',
                    'Pembayaran belum lunas. Tunggu konfirmasi dari payment gateway.'
                );
            }

            $result = $this->provisioning->provision(
                $PendaftaranTenant,
                Auth::user()->name ?? 'Admin',
                $Request->CatatanVerifikasi,
                false  // Admin tidak perlu flash password
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

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PendaftaranTenant $pendaftaranTenant)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PendaftaranTenant $pendaftaranTenant)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function Destroy(PendaftaranTenant $PendaftaranTenant)
    {
        try {
            if ($PendaftaranTenant->BuktiPembayaran && Storage::disk('public')->exists($PendaftaranTenant->BuktiPembayaran)) {
                Storage::disk('public')->delete($PendaftaranTenant->BuktiPembayaran);
            }

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
}
