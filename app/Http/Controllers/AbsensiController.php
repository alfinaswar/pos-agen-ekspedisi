<?php

namespace App\Http\Controllers;

use App\Exports\AbsensiExport;
use App\Models\Absensi;
use App\Models\Divisi;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class AbsensiController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            // Tambahkan Status dan Catatan ke select
            // dd($request->status_verif);
            $query = Absensi::with('getUser', 'getDivisi')->select([
                'id',
                'Nama',
                'Divisi',
                'NoHp',
                'Tanggal',
                'JamHadir',
                'JamPulang',
                'Status',
                'Lembur',
                'MulaiLembur',
                'SelesaiLembur',
                'Catatan',
                'StatusVerif'
            ])->latest('created_at');

            $isPrivileged = auth()->check() && in_array(auth()->user()->role, ['Admin', 'Leader']);

            if (!$isPrivileged) {
                $query->where('Nama', auth()->user()->id);
                if ($request->filled('status_verif'))
                    $query->where('StatusVerif', $request->status_verif);
            } else {
                if ($request->filled('bulan'))
                    $query->whereMonth('Tanggal', $request->bulan);
                if ($request->filled('status'))
                    $query->where('Status', $request->status);
                if ($request->filled('user_name'))
                    $query->where('Nama', $request->user_name);
                if ($request->filled('divisi'))
                    $query->where('Divisi', $request->divisi);
                if ($request->filled('status_verif'))
                    $query->where('StatusVerif', $request->status_verif);
            }


            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $user = auth()->user();
                    $role = $user ? $user->role : null;
                    $status = $row->StatusVerif ?? 'N/A';

                    $canShow = false;
                    $canEdit = false;
                    $canDelete = false;

                    // 1. Admin & Leader: Bisa Show, Edit, Hapus
                    if (in_array($role, ['Admin', 'Leader'])) {
                        $canShow = true;
                        $canEdit = true;
                        $canDelete = true;
                    }
                    // 2. Kasir: TIDAK Boleh Show. Edit/Hapus hanya jika N/A atau N
                    elseif ($role === 'Kasir') {
                        $canShow = false;
                        if ($status === 'N/A' || $status === 'N') {
                            $canEdit = true;
                            $canDelete = true;
                        }
                    }

                    $btn = '<div class="d-flex gap-1 justify-content-center">';

                    if ($canShow) {
                        $btn .= '<a href="' . route('absensi.show', $row->id) . '" class="btn btn-info btn-sm text-white" title="Lihat Detail">';
                        $btn .= '<i class="ti ti-eye"></i></a> ';
                    }
                    if ($canEdit) {
                        $btn .= '<a href="' . route('absensi.edit', $row->id) . '" class="btn btn-warning btn-sm text-white" title="Edit">';
                        $btn .= '<i class="ti ti-edit"></i></a> ';
                    }
                    if ($canDelete) {
                        $namaUser = htmlspecialchars($row->getUser->name ?? $row->Nama);
                        $btn .= '<button type="button" class="btn btn-danger btn-sm btn-delete" data-id="' . $row->id . '" data-nama="' . $namaUser . '" title="Hapus">';
                        $btn .= '<i class="ti ti-trash"></i></button>';
                    }
                    $btn .= '</div>';
                    return $btn;
                })
                // Tambahkan kolom Status Info agar terlihat di tabel
                ->addColumn('StatusInfo', function ($row) {
                    $statusText = '';
                    switch ($row->Status) {
                        case 'Y':
                            $statusText = '<span class="badge bg-success">Disetujui</span>';
                            break;
                        case 'N':
                            $statusText = '<span class="badge bg-danger">Ditolak</span>';
                            break;
                        default:
                            $statusText = '<span class="badge bg-light text-dark">Belum Verif</span>';
                            break;
                    }
                    return $statusText;
                })
                ->editColumn('Nama', function ($row) {
                    return htmlspecialchars($row->getUser->name ?? $row->Nama);
                })
                ->editColumn('Divisi', function ($row) {
                    return htmlspecialchars($row->getDivisi->Nama ?? ($row->Divisi ?? '-'));
                })
                ->editColumn('StatusVerif', function ($row) {
                    $statusText = '';
                    switch ($row->StatusVerif) {
                        case 'Y':
                            $statusText = '<span class="badge bg-success">Disetujui</span>';
                            break;
                        case 'N':
                            $statusText = '<span class="badge bg-danger">Ditolak</span>';
                            break;
                        case 'N/A':
                            $statusText = '<span class="badge bg-secondary">N/A</span>';
                            break;
                        default:
                            $statusText = '<span class="badge bg-light text-dark">Belum Diverifikasi</span>';
                            break;
                    }
                    if (
                        !empty($row->Catatan) &&
                        (
                            (auth()->check() && in_array(auth()->user()->role, ['Admin', 'Leader']))
                            ||
                            (auth()->check() && $row->getUser && $row->getUser->id == auth()->id())
                        )
                    ) {
                        $catatanEscaped = htmlspecialchars($row->Catatan, ENT_QUOTES, 'UTF-8');
                        $statusText .= ' <button type="button" class="btn btn-sm btn-outline-secondary p-0 px-1 ms-1 btn-view-catatan-absensi"
                                    data-catatan="' . $catatanEscaped . '" title="Lihat Catatan Verifikasi" style="vertical-align: middle;">
                                    <i class="ti ti-message" style="font-size: 0.9rem;"></i>
                                  </button>';
                    }


                    return $statusText;
                })
                ->rawColumns(['action', 'StatusInfo', 'Divisi', 'StatusVerif'])
                ->make(true);

        }

        $users = User::get();
        $divisis = Divisi::orderBy('Nama', 'asc')->get();
        return view('absensi.index', compact('users', 'divisis'));
    }
    public function bulkApprove(Request $request)
    {
        // Hanya Admin dan Leader yang boleh
        if (!in_array(auth()->user()->role, ['Admin', 'Leader'])) {
            abort(403, 'Aksi tidak diizinkan.');
        }

        $request->validate([
            'ids' => 'required|array|min:1',
            'ids.*' => 'exists:absensis,id',
            'StatusVerif' => 'required|in:Y,N,N/A',
            'Catatan' => 'nullable|string|max:1000',
        ]);

        $updatedCount = 0;
        $userName = auth()->user()->name ?? 'System';
        $now = now();

        foreach ($request->ids as $id) {
            $absensi = Absensi::find($id);
            if ($absensi) {
                $absensi->update([
                    'StatusVerif' => $request->StatusVerif,
                    'Catatan' => $request->Catatan,
                    'UserLeader' => $userName,
                    'DisetujuiPada' => $now,
                ]);
                $updatedCount++;
            }
        }

        return response()->json([
            'success' => true,
            'message' => "Berhasil memverifikasi {$updatedCount} data absensi."
        ]);
    }
    public function create()
    {
        $user = User::get();
        $divisi = Divisi::get();
        return view('absensi.create',compact('user','divisi'));
    }
    public function show(Absensi $absensi)
    {
        $absensi->load('getUser', 'getDivisi');
        return view('absensi.show', compact('absensi'));
    }
    public function approve(Request $request, Absensi $absensi)
    {
        // Hanya Admin dan Leader yang boleh melakukan ini
        if (!in_array(auth()->user()->role, ['Admin', 'Leader'])) {
            abort(403, 'Aksi tidak diizinkan.');
        }

        $request->validate([
            'Status' => 'required|in:Y,N,N/A',
            'Catatan' => 'nullable|string|max:1000',
        ]);

        $absensi->update([
            'StatusVerif' => $request->Status,
            'Catatan' => $request->Catatan,
            'UserLeader' => auth()->user()->name,
            'DisetujuiPada' => now(),
        ]);

        return redirect()->route('absensi.show', $absensi->id)
            ->with('success', 'Status persetujuan absensi berhasil diperbarui.');
    }
    public function store(Request $request)
    {
        $request->validate([
            'Nama' => 'required|string|max:100',
            'Divisi' => 'required|string|max:100',
            'NoHp' => 'required|string|max:20',
            'Tanggal' => 'required|date',
            'JamHadir' => 'nullable|date_format:H:i',
            'JamPulang' => 'nullable|date_format:H:i',
            'Status' => 'required|in:H,I,S,TK',
            'Lembur' => 'required|in:Y,N',
            'MulaiLembur' => 'required_if:Lembur,Y|nullable|string|max:50',
            'SelesaiLembur' => 'required_if:Lembur,Y|nullable|string|max:50',
        ]);

        // Cek apakah user sudah mengisi absensi dengan nama & tanggal yang sama
        $existing = Absensi::where('Nama', $request->Nama)
            ->where('Tanggal', $request->Tanggal)
            ->first();

        if ($existing) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['Tanggal' => 'Anda hanya dapat mengisi absensi satu kali dalam hari yang sama.']);
        }

        $data = $request->all();
        $data['UserCreate'] = auth()->user()->name ?? 'System';

        Absensi::create($data);

        return redirect()->route('absensi.index')->with('success', 'Data absensi berhasil ditambahkan.');
    }
    public function export(Request $request)
    {
        $query = Absensi::select([
            'id',
            'Nama',
            'Divisi',
            'NoHp',
            'Tanggal',
            'JamHadir',
            'JamPulang',
            'Status',
            'Lembur',
            'MulaiLembur',
            'SelesaiLembur'
        ])->orderBy('Tanggal', 'desc');

        $filterParts = [];

        // Filter Bulan
        if ($request->filled('bulan')) {
            $query->whereMonth('Tanggal', $request->bulan);
            $filterParts[] = 'Bulan: ' . Carbon::create()->month($request->bulan)->isoFormat('MMMM');
        }

        // Filter Status
        if ($request->filled('status')) {
            $query->where('Status', $request->status);
            $statusLabels = [
                'H' => 'Hadir',
                'I' => 'Izin',
                'S' => 'Sakit',
                'TK' => 'Tanpa Keterangan'
            ];
            $filterParts[] = 'Status: ' . ($statusLabels[$request->status] ?? $request->status);
        }

        // Filter User
        if ($request->filled('user_name')) {
            $query->where('Nama', $request->user_name);
            $filterParts[] = 'Karyawan: ' . $request->user_name;
        }

        $data = $query->get();
        $filterInfo = !empty($filterParts) ? implode(' | ', $filterParts) : 'Semua Data';

        $filename = 'Laporan_Absensi_' . Carbon::now()->format('Y-m-d_His') . '.xlsx';

        return Excel::download(
            new AbsensiExport($data, $filterInfo),
            $filename
        );
    }
    public function edit(Absensi $absensi)
    {
        $user = User::get();
        $divisi = Divisi::get();
        return view('absensi.edit', compact('absensi','user','divisi'));
    }

    public function update(Request $request, Absensi $absensi)
    {
        $request->validate([
            'Nama' => 'required|string|max:100',
            'Divisi' => 'required|string|max:100',
            'NoHp' => 'required|string|max:20',
            'Tanggal' => 'required|date',
            'JamHadir' => 'nullable',
            'JamPulang' => 'nullable',

            'Status' => 'required|in:H,I,S,TK',
            'Lembur' => 'required|in:Y,N',
            'MulaiLembur' => 'required_if:Lembur,Y|nullable|string|max:50',
            'SelesaiLembur' => 'required_if:Lembur,Y|nullable|string|max:50',
        ]);

        $data = $request->all();
        $data['UserUpdate'] = auth()->user()->name ?? 'System';

        $absensi->update($data);

        return redirect()->route('absensi.index')->with('success', 'Data absensi berhasil diperbarui.');
    }

    public function destroy(Absensi $absensi)
    {
        try {
            $absensi->update(['UserDelete' => auth()->user()->name ?? 'System']);
            $absensi->delete();

            return response()->json([
                'success' => true,
                'status' => 200,
                'message' => 'Data absensi berhasil dihapus.'
            ]);
        } catch (\Exception $e) {
            Log::error('Gagal menghapus absensi: ' . $e->getMessage());
            return response()->json(['success' => false, 'status' => 500, 'message' => 'Gagal menghapus data.'], 500);
        }
    }
}
