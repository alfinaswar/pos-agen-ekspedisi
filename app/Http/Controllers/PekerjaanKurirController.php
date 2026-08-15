<?php

namespace App\Http\Controllers;

use App\Exports\PekerjaanKurirExport;
use App\Models\PekerjaanKurir;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;

class PekerjaanKurirController extends Controller
{
    public function Index(Request $Request)
    {
        if ($Request->ajax()) {
            $Query = PekerjaanKurir::latest();

            // ✅ TAMBAHAN: Logika Filter
            if ($Request->filled('TanggalAwal')) {
                $Query->whereDate('Tanggal', '>=', $Request->TanggalAwal);
            }
            if ($Request->filled('TanggalAkhir')) {
                $Query->whereDate('Tanggal', '<=', $Request->TanggalAkhir);
            }
            if ($Request->filled('UserId')) {
                $Query->where('UserId', $Request->UserId); // Asumsi ada kolom UserId, atau ganti 'UserCreate' jika menggunakan string nama
            }

            return DataTables::of($Query)
                ->addIndexColumn()
                ->editColumn('Tanggal', function ($Row) {
                    return $Row->Tanggal ? \Carbon\Carbon::parse($Row->Tanggal)->format('d M Y') . '<br><small class="text-muted">' . $Row->Jam . '</small>' : '-';
                })
                ->editColumn('Pekerjaan', function ($Row) {
                    $Badge = match ($Row->Pekerjaan) {
                        'Ambil Paket' => 'bg-info text-dark',
                        'Antar Paket' => 'bg-success',
                        'Lain-lain' => 'bg-secondary'
                    };
                    return '<span class="badge ' . $Badge . '">' . $Row->Pekerjaan . '</span>';
                })
                ->editColumn('Status', function ($Row) {
                    $Badge = match ($Row->Status) {
                        'Y' => 'bg-success',
                        'N' => 'bg-danger',
                        default => 'bg-secondary'
                    };
                    $Label = match ($Row->Status) {
                        'Y' => 'Disetujui',
                        'N' => 'Ditolak',
                        default => 'Belum Verif'
                    };

                    $Html = '<span class="badge ' . $Badge . '">' . $Label . '</span>';

                    if (!empty($Row->Catatan)) {
                        $CatatanEscaped = htmlspecialchars($Row->Catatan, ENT_QUOTES, 'UTF-8');
                        $Html .= ' <button type="button" class="btn btn-sm btn-outline-secondary p-0 px-1 ms-1 btn-view-catatan-kurir"
                                    data-catatan="' . $CatatanEscaped . '" title="Lihat Catatan Verifikasi" style="vertical-align: middle;">
                                    <i class="ti ti-message" style="font-size: 0.9rem;"></i>
                                  </button>';
                    }
                    return $Html;
                })
                ->editColumn('BuktiFoto', function ($Row) {
                    if ($Row->BuktiFoto) {
                        return '<a href="' . asset('storage/' . $Row->BuktiFoto) . '" target="_blank" class="btn btn-sm btn-outline-primary"><i class="ti ti-eye"></i></a>';
                    }
                    return '<span class="text-muted">-</span>';
                })
                ->addColumn('action', function ($Row) {
                    $Btn = '<div class="d-flex gap-1 justify-content-center">';
                    // Tidak ada tombol Edit, hanya tombol Hapus jika ingin tetap bisa menghapus
                    $Btn .= '<button type="button" class="btn btn-danger btn-sm btn-delete" data-id="' . $Row->Id . '" data-tanggal="' . $Row->Tanggal . '" title="Hapus"><i class="ti ti-trash"></i></button>';
                    $Btn .= '</div>';
                    return $Btn;
                })
                ->editColumn('NamaKurir', function ($Row) {
                    return $Row->getKurir->name ?? '-';
                })

                ->rawColumns(['Tanggal', 'Pekerjaan', 'Status', 'BuktiFoto', 'action'])
                ->make(true);
        }

        // ✅ TAMBAHAN: Kirim data user ke view untuk dropdown filter
        $Users = User::select('id', 'name')->orderBy('name', 'asc')->get();
        return view('pekerjaan-kurir.index', compact('Users'));
    }

    public function create()
    {
        return view('pekerjaan-kurir.create');
    }
    public function BulkVerify(Request $Request)
    {
        $Request->validate([
            'Ids' => 'required|array|min:1',
            'Ids.*' => 'exists:pekerjaan_kurirs,id',
            'Status' => 'required|in:Y,N,N/A',
            'Catatan' => 'nullable|string|max:1000',
        ]);

        $UpdatedCount = 0;
        $UserName = Auth::user()->name ?? 'System';
        $Now = now();

        foreach ($Request->Ids as $Id) {
            $PekerjaanKurir = PekerjaanKurir::find($Id);
            // dd($PekerjaanKurir);
            if ($PekerjaanKurir) {
                $PekerjaanKurir->update([
                    'Status' => $Request->Status,
                    'Catatan' => $Request->Catatan,
                    'UserVerif' => $UserName,
                    'DicekPada' => $Now,
                ]);
                $UpdatedCount++;
            }
        }

        return response()->json([
            'success' => true,
            'message' => "Berhasil memverifikasi {$UpdatedCount} laporan pekerjaan kurir."
        ]);
    }
    public function store(Request $Request)
    {
        // Custom validation, following migration structure (nullable for most fields except required)
        $Request->validate([
            'Tanggal' => 'nullable|date',
            'Jam' => 'nullable',
            'Pekerjaan' => 'nullable|string|max:255',
            'DariLokasi' => 'nullable|string|max:255',
            'Tujuan' => 'nullable|string|max:255',
            'JumlahPaket' => 'nullable|integer|min:0',
            'Durasi' => 'nullable|string|max:255',
            'Keterangan' => 'nullable|string',
            'BuktiFoto' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        // Prepare data for insertion, capitalize keys according to migration definition
        $Data = [
            'Tanggal'      => $Request->input('Tanggal'),
            'Jam'          => $Request->input('Jam'),
            'Pekerjaan'    => $Request->input('Pekerjaan'),
            'DariLokasi'   => $Request->input('DariLokasi'),
            'Tujuan'       => $Request->input('Tujuan'),
            'JumlahPaket'  => $Request->input('JumlahPaket', 0),
            'Durasi'       => $Request->input('Durasi'),
            'Keterangan'   => $Request->input('Keterangan'),
            'IdUser'       => Auth::id(),
            'UserCreate'   => Auth::user()->name ?? 'System',
            // 'Tenant'       => Auth::user()->tenant ?? null,
        ];

        if ($Request->hasFile('BuktiFoto')) {
            $File = $Request->file('BuktiFoto');
            $FileName = time() . '_' . preg_replace('/[^A-Za-z0-9\-_\.]/', '', $File->getClientOriginalName());
            $Data['BuktiFoto'] = $File->storeAs('pekerjaan_kurir', $FileName, 'public');
        }

        PekerjaanKurir::create($Data);

        return redirect()->route('pekerjaan-kurir.index')->with('success', 'Laporan aktivitas kurir berhasil disimpan.');
    }

    public function edit(PekerjaanKurir $PekerjaanKurir)
    {
        return view('pekerjaan-kurir.edit', compact('PekerjaanKurir'));
    }

    public function update(Request $Request, PekerjaanKurir $PekerjaanKurir)
    {
        $Request->validate([
            'Tanggal' => 'required|date',
            'Jam' => 'required',
            'Pekerjaan' => 'required|in:Ambil Paket,Antar Paket,Lain-lain',
            'DariLokasi' => 'required|string|max:255',
            'Tujuan' => 'required|string|max:255',
            'JumlahPaket' => 'required|integer|min:0',
            'Durasi' => 'required|string|max:100',
            'Keterangan' => 'nullable|string',
            'BuktiFoto' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $Data = $Request->except(['BuktiFoto']);
        $Data['UserUpdate'] = Auth::user()->name ?? 'System';

        if ($Request->hasFile('BuktiFoto')) {
            if ($PekerjaanKurir->BuktiFoto && Storage::disk('public')->exists($PekerjaanKurir->BuktiFoto)) {
                Storage::disk('public')->delete($PekerjaanKurir->BuktiFoto);
            }
            $File = $Request->file('BuktiFoto');
            $FileName = time() . '_' . preg_replace('/[^A-Za-z0-9\-_\.]/', '', $File->getClientOriginalName());
            $Data['BuktiFoto'] = $File->storeAs('pekerjaan_kurir', $FileName, 'public');
        }

        $PekerjaanKurir->update($Data);

        return redirect()->route('pekerjaan-kurir.index')->with('success', 'Laporan aktivitas kurir berhasil diperbarui.');
    }

    public function destroy(PekerjaanKurir $PekerjaanKurir)
    {
        try {
            // 1. Hapus file bukti foto dari storage jika ada
            if ($PekerjaanKurir->BuktiFoto && Storage::disk('public')->exists($PekerjaanKurir->BuktiFoto)) {
                Storage::disk('public')->delete($PekerjaanKurir->BuktiFoto);
            }

            // 2. Catat user yang melakukan penghapusan (Audit Trail)
            $PekerjaanKurir->UserDelete = Auth::user()->name ?? 'System';
            $PekerjaanKurir->save();

            // 3. Lakukan soft delete
            $PekerjaanKurir->delete();

            // 4. Kembalikan response JSON yang diharapkan oleh AJAX
            return response()->json([
                'success' => true,
                'message' => 'Data laporan aktivitas berhasil dihapus.'
            ]);

        } catch (\Exception $Exception) {
            // Jika terjadi error, kembalikan JSON error agar bisa ditangkap oleh blok 'error' di AJAX
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus data: ' . $Exception->getMessage()
            ], 500);
        }
    }
    public function Export(Request $Request)
    {
        $Query = PekerjaanKurir::latest();

        // Terapkan filter yang sama dengan DataTables
        if ($Request->filled('TanggalAwal')) {
            $Query->whereDate('Tanggal', '>=', $Request->TanggalAwal);
        }
        if ($Request->filled('TanggalAkhir')) {
            $Query->whereDate('Tanggal', '<=', $Request->TanggalAkhir);
        }
        if ($Request->filled('UserId')) {
            $Query->where('UserId', $Request->UserId);
        }

        $Data = $Query->get();

        // Format String Info Filter untuk ditampilkan di Excel
        $FilterInfo = 'Periode: ' . ($Request->TanggalAwal ? $Request->TanggalAwal : 'Semua') .
            ' s/d ' . ($Request->TanggalAkhir ? $Request->TanggalAkhir : 'Semua');

        if ($Request->filled('UserId')) {
            $UserName = User::find($Request->UserId)?->name ?? 'Unknown';
            $FilterInfo .= ' | Kurir: ' . $UserName;
        }

        // Nama file dengan timestamp
        $FileName = 'Laporan_Pekerjaan_Kurir_' . date('Y-m-d_His') . '.xlsx';

        // Download menggunakan Maatwebsite\Excel
        return Excel::download(new PekerjaanKurirExport($Data, $FilterInfo), $FileName);
    }
}
