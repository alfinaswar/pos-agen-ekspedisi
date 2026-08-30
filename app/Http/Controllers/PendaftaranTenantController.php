<?php

namespace App\Http\Controllers;

use App\Mail\TenantApprovedMail;
use App\Models\PendaftaranTenant;
use App\Models\TagihanPembayaran;
use App\Models\Tenant;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\DataTables;

class PendaftaranTenantController extends Controller
{
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
    public function store(Request $request)
    {
        // dd($request->all());
        $validated = $request->validate([
            'Nama' => 'required|string|max:255',
            'Paket' => 'required|string|max:255',
            'Email' => 'required|email|max:255',
            'Telepon' => 'required|string|min:9|max:20',
            'Alamat' => 'required|string|max:500',
            'NamaPIC' => 'required|string|max:255',
            'EmailPIC' => 'required|email|max:255',
            'AlamatPIC' => 'nullable|string|max:500',
            'BuktiPembayaran' => 'required|file|mimes:jpg,jpeg,png,pdf,webp|max:5120',  // 5MB
        ], [
            'required' => ':attribute wajib diisi.',
            'email' => ':attribute harus berupa email yang valid.',
            'min' => ':attribute minimal :min karakter.',
            'max' => ':attribute maksimal :max karakter.',
            'file' => ':attribute harus berupa file.',
            'mimes' => ':attribute harus JPG/PNG/PDF/WEBP.',
        ]);

        // Handle file upload
        $buktiPembayaranPath = null;
        if ($request->hasFile('BuktiPembayaran')) {
            $file = $request->file('BuktiPembayaran');
            $fileName = time() . '_' . preg_replace('/[^A-Za-z0-9\-_\.]/', '', $file->getClientOriginalName());
            $buktiPembayaranPath = $file->storeAs('bukti_pembayaran', $fileName, 'public');
        }
        // Simpan data pendaftaran
        $pendaftaran = PendaftaranTenant::create([
            'Nama' => $validated['Nama'],
            'Paket' => $validated['Paket'],
            'Email' => $validated['Email'],
            'Alamat' => $validated['Alamat'],
            'NamaPIC' => $validated['NamaPIC'],
            'EmailPIC' => $validated['EmailPIC'],
            'AlamatPIC' => $validated['AlamatPIC'] ?? null,
            'BuktiPembayaran' => $buktiPembayaranPath,
            'Status' => 'N/A',
        ]);

        return redirect()
            ->back()
            ->with('success', [
                'message' => 'Pendaftaran berhasil dikirim! Proses pendaftaran akan diproses dalam 1 x 24 jam.',
                'kode' => $pendaftaran->Kode,
            ]);
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
        // Mengambil data paket terkait dengan pendaftaran tenant
        DB::beginTransaction();
        try {
            // 1. Update Status Pendaftaran
            $PendaftaranTenant->update([
                'Status' => $Request->Status,
                'CatatanVerifikasi' => $Request->CatatanVerifikasi,
                'VerifOleh' => Auth::user()->name ?? 'System',
                'VerifPada' => now(),
            ]);

            // 2. Jika Disetujui (Y), Buat Master Tenant, User Admin, dan Tagihan Pertama
            if ($Request->Status === 'Y') {
                $TanggalJoin = now();
                $PasswordPlain = Carbon::parse($PendaftaranTenant->created_at)->format('Ymd');

                // A. Buat Data Master Tenant
                $NewTenant = Tenant::create([
                    'Nama' => $PendaftaranTenant->Nama ?? null,
                    'Email' => $PendaftaranTenant->Email ?? null,
                    'Alamat' => $PendaftaranTenant->Alamat ?? null,
                    'Telepon' => $PendaftaranTenant->Telepon ?? null,
                    'NamaPIC' => $PendaftaranTenant->NamaPIC ?? null,
                    'EmailPIC' => $PendaftaranTenant->EmailPIC ?? null,
                    'AlamatPIC' => $PendaftaranTenant->AlamatPIC ?? null,
                    'TeleponPIC' => $PendaftaranTenant->TeleponPIC ?? null,
                    'TanggalJoin' => $TanggalJoin,
                    'StatusSubscription' => 'Aktif',
                    'TanggalMulaiSubscription' => $TanggalJoin,
                    'TanggalAkhirSubscription' => $TanggalJoin->copy()->addMonth($PendaftaranTenant->getPaket->DurasiBulan),
                    'UserCreate' => Auth::user()->name ?? 'System',
                ]);

                // B. Buat User Admin untuk Tenant tersebut
                $UserName = $PendaftaranTenant->NamaPIC ?: $PendaftaranTenant->Nama;
                $UserEmail = $PendaftaranTenant->EmailPIC ?: $PendaftaranTenant->Email;

                User::create([
                    'tenant_id' => $NewTenant->Kode,
                    'name' => $UserName,
                    'email' => $UserEmail,
                    'password' => Hash::make($PasswordPlain),
                    'role' => 'Admin',
                    'user_create' => Auth::user()->name ?? 'System',
                ]);

                $PeriodeBulan = now()->format('Y-m');
                $TanggalJatuhTempo = now()->addDays(7);
                $buktiPembayaranBaru = null;
                if ($PendaftaranTenant->BuktiPembayaran && Storage::disk('public')->exists($PendaftaranTenant->BuktiPembayaran)) {
                    $namaFile = basename($PendaftaranTenant->BuktiPembayaran);
                    $targetPath = 'bukti-bayar/' . $namaFile;
                    Storage::disk('public')->copy($PendaftaranTenant->BuktiPembayaran, $targetPath);
                    $buktiPembayaranBaru = $targetPath;
                }
                TagihanPembayaran::create([
                    'TenantId' => $NewTenant->Kode,
                    'PeriodeBulan' => $PeriodeBulan,
                    'TanggalJatuhTempo' => $TanggalJatuhTempo,
                    'JumlahTagihan' => 149000,
                    'StatusPembayaran' => 'Lunas',
                    'TanggalPembayaran' => $TanggalJoin,
                    'BerlakuHingga' => $TanggalJoin->copy()->addMonth($PendaftaranTenant->getPaket->DurasiBulan),
                    'BuktiPembayaran' => $buktiPembayaranBaru,
                    'Catatan' => null,
                    'Status' => 'N/A',
                    'CatatanVerifikasi' => null,
                    'VerifPada' => null,
                    'VerifOleh' => null,
                    'UserCreate' => Auth::user()->name ?? 'System',
                ]);
            }

            DB::commit();

            // 3. Kirim Email Jika Disetujui dan Email PIC Ada
            if ($Request->Status === 'Y' && !empty($UserEmail)) {
                $LoginUrl = url('/login');

                Mail::to($UserEmail)->send(new TenantApprovedMail(
                    $UserName,
                    $UserEmail,
                    $PasswordPlain,
                    $LoginUrl
                ));
            }

            $Pesan = $Request->Status === 'Y'
                ? 'Pendaftaran berhasil disetujui. Tenant, Akun Admin, dan Tagihan Pertama telah dibuat.'
                : 'Pendaftaran tenant ditolak.';

            return redirect()->route('pendaftaran-tenant.index')->with('success', $Pesan);
        } catch (\Exception $Exception) {
            dd($Exception);
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal memproses verifikasi: ' . $Exception->getMessage());
        }
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
