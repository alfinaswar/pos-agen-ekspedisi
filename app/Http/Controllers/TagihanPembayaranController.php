<?php

namespace App\Http\Controllers;

use App\Models\TagihanPembayaran;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;

class TagihanPembayaranController extends Controller
{
    public function Index(Request $Request)
    {
        if ($Request->ajax()) {
            $User = auth()->user();
            $Query = TagihanPembayaran::with('Tenant')->latest('id');

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

            // Keamanan: Jika bukan Superadmin, paksa filter berdasarkan TenantId
            if ($User->role !== 'Superadmin' && isset($User->TenantId)) {
                $Query->where('TenantId', $User->TenantId);
            }

            // Filter Tenant (HANYA berlaku jika Superadmin)
            if ($User->role === 'Superadmin' && $Request->filled('TenantId')) {
                $Query->where('TenantId', $Request->TenantId);
            }

            return DataTables::of($Query)
                ->addIndexColumn()
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
                ->rawColumns(['NomorTagihan', 'NamaTenant', 'StatusPembayaran', 'BuktiPembayaran', 'action'])
                ->make(true);
        }

        $User = auth()->user();
        $Tenants = $User->role === 'Superadmin'
            ? Tenant::select('id', 'Nama')->orderBy('Nama', 'asc')->get()
            : Tenant::select('id', 'Nama')->where('id', $User->TenantId ?? 0)->get();

        return view('tagihan-pembayaran.index', compact('Tenants'));
    }

    public function Create()
    {
        $Tenants = Tenant::where('StatusSubscription', 'Aktif')->get();
        return view('tagihan-pembayaran.create', compact('Tenants'));
    }
    public function Show(TagihanPembayaran $TagihanPembayaran)
    {
        // Muat relasi Tenant agar data nama tenant tersedia di view
        $TagihanPembayaran->load('Tenant');
        return view('tagihan-pembayaran.show', compact('TagihanPembayaran'));
    }
    public function Store(Request $Request)
    {
        // 1. Validasi Input
        $Request->validate([
            'TenantId' => 'required|exists:tenants,id',
            'PeriodeBulan' => 'required|date_format:Y-m',
            'JumlahTagihan' => 'required|string',
            'TanggalPembayaran' => 'required|date',
            'BerlakuHingga' => 'required|date|after_or_equal:TanggalPembayaran',
            'BuktiPembayaran' => 'required|file|mimes:jpeg,png,jpg,pdf|max:2048',
            'Catatan' => 'nullable|string|max:1000',
        ]);

        // 2. Bersihkan format angka (hapus titik ribuan)
        $CleanAmount = str_replace('.', '', $Request->JumlahTagihan);

        // 3. Siapkan data untuk disimpan
        $Data = $Request->except(['JumlahTagihan', 'BuktiPembayaran', 'BerlakuHingga']);
        $Data['JumlahTagihan'] = $CleanAmount;

        // Mapping field 'BerlakuHingga' dari view ke 'TanggalJatuhTempo' di database
        $Data['TanggalJatuhTempo'] = $Request->BerlakuHingga;

        $Data['StatusPembayaran'] = 'Lunas'; // Default status saat dibuat
        $Data['UserCreate'] = Auth::user()->name ?? 'System';

        // 4. Handle Upload File
        if ($Request->hasFile('BuktiPembayaran')) {
            $File = $Request->file('BuktiPembayaran');
            $FileName = time() . '_' . preg_replace('/[^A-Za-z0-9\-_\.]/', '', $File->getClientOriginalName());
            $Data['BuktiPembayaran'] = $File->storeAs('tagihan', $FileName, 'public');
        }

        // 5. Simpan ke Database
        TagihanPembayaran::create($Data);

        return redirect()->route('tagihan-pembayaran.index')->with('success', 'Tagihan pembayaran berhasil dibuat.');
    }

    public function Edit(TagihanPembayaran $TagihanPembayaran)
    {
        $Tenants = Tenant::where('StatusSubscription', 'Aktif')->orderBy('Nama', 'asc')->get();

        // Load relasi tenant agar nama tenant tersedia di view
        $TagihanPembayaran->load('Tenant');

        return view('tagihan-pembayaran.edit', compact('TagihanPembayaran', 'Tenants'));
    }

    public function Update(Request $Request, TagihanPembayaran $TagihanPembayaran)
    {
        // 1. Validasi Input (Bukti Pembayaran jadi nullable agar tidak wajib diganti)
        $Request->validate([
            'TenantId' => 'required|exists:tenants,id',
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
        $Data['TanggalJatuhTempo'] = $Request->BerlakuHingga; // Mapping ke DB
        $Data['UserUpdate'] = Auth::user()->name ?? 'System';

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
                if ($Request->Status === 'Y' && $Tagihan->TenantId) {
                    $tenant = Tenant::where('Kode', $Tagihan->TenantId)->first();
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
        if ($StatusVerifikasi === 'Y' && $TagihanPembayaran->TenantId) {
            $tenant = Tenant::where('Kode', $TagihanPembayaran->TenantId)->first();
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
}
