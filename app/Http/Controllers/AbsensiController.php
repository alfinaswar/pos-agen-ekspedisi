<?php

namespace App\Http\Controllers;

use App\Exports\AbsensiExport;
use App\Models\Absensi;
use App\Models\Divisi;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;

class AbsensiController extends Controller
{
    public function index(Request $request)
    {
        // Dapatkan kode tenant dari user login (pastikan field ini ada di tabel user)
        $kodeTenant = Auth::user()->KodeTenant ?? null;

        if ($request->ajax()) {
            // Tambahkan Status dan Catatan ke select
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
                'StatusVerif',
                'KodeTenant'
            ])->latest('created_at');

            // Filter by KodeTenant
            if ($kodeTenant) {
                $query->where('KodeTenant', $kodeTenant);
            }

            $isPrivileged = auth()->check() && in_array(auth()->user()->role, ['Admin', 'Leader']);

            if (!$isPrivileged) {
                $query->where('Nama', auth()->user()->id);
                if ($request->filled('status_verif'))
                    $query->where('StatusVerif', $request->status_verif);
                if ($request->filled('bulan'))
                    $query->whereMonth('Tanggal', $request->bulan);
                if ($request->filled('status'))
                    $query->where('Status', $request->status);
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
                        $btn .= '<a href="' . route('absensi.show', $row->id) . '" class="btn btn-info btn-sm text-white" title="Lihat Detail" target="_blank">';
                        $btn .= '<i class="ti ti-eye"></i></a> ';
                    }

                    if ($canEdit) {
                        $btn .= '<a href="' . route('absensi.edit', $row->id) . '" class="btn btn-warning btn-sm text-white" title="Edit" target="_blank">';
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
                            (auth()->check() && in_array(auth()->user()->role, ['Admin', 'Leader'])) ||
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
                ->addColumn('Tanggal', function ($row) {
                    $tanggal = $row->Tanggal ?? null;
                    if ($tanggal) {
                        return \Carbon\Carbon::parse($tanggal)->translatedFormat('l, d/m/Y');
                    }
                    return '-';
                })
                ->rawColumns(['action', 'StatusInfo', 'Divisi', 'StatusVerif', 'Tanggal'])
                ->make(true);
        }

        // Filter user/divisi berdasarkan tenant (opsional, jika memang field KodeTenant ada di tabel)
        $users = User::where('KodeTenant', $kodeTenant)->get();
        $divisis = Divisi::where('KodeTenant', $kodeTenant)->orderBy('Nama', 'asc')->get();
        return view('absensi.index', compact('users', 'divisis'));
    }

    public function bulkApprove(Request $request)
    {
        // Hanya Admin dan Leader yang boleh
        if (!in_array(auth()->user()->role, ['Admin', 'Leader'])) {
            abort(403, 'Aksi tidak diizinkan.');
        }

        // Ambil kode tenant login
        $kodeTenant = Auth::user()->KodeTenant ?? null;

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
            $absensi = Absensi::where('id', $id)
                            ->where('KodeTenant', $kodeTenant)
                            ->first();
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
        $kodeTenant = Auth::user()->KodeTenant ?? null;
        $user = User::where('KodeTenant', $kodeTenant)->get();
        $divisi = Divisi::where('KodeTenant', $kodeTenant)->get();
        return view('absensi.create', compact('user', 'divisi'));
    }

    public function show(Absensi $absensi)
    {
        $absensi->load('getUser', 'getDivisi');
        return view('absensi.show', compact('absensi'));
    }

    public function approve(Request $request, Absensi $absensi)
    {
        if (!in_array(auth()->user()->role, ['Admin', 'Leader'])) {
            abort(403, 'Aksi tidak diizinkan.');
        }

        // Pastikan approve hanya untuk tenant yang sesuai
        $kodeTenant = Auth::user()->KodeTenant ?? null;
        if ($absensi->KodeTenant !== $kodeTenant) {
            abort(403, 'Aksi tidak diizinkan pada tenant ini.');
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

        return redirect()
            ->route('absensi.show', $absensi->id)
            ->with('success', 'Status persetujuan absensi berhasil diperbarui.');
    }

    public function Store(Request $Request)
    {
        $kodeTenant = Auth::user()->KodeTenant ?? null;
        // 1. Validasi Input (Disesuaikan dengan form baru)
        $Request->validate([
            'Divisi' => 'required|string|max:100',
            'NoHp' => 'required|string|max:20',
            'Tanggal' => 'required|date',
            'JamHadir' => 'nullable|date_format:H:i',
            'JamPulang' => 'nullable|date_format:H:i',
            'Status' => 'required|in:H,I,S,TK',
            'Lembur' => 'required|in:Y,N',
            'MulaiLembur' => 'required_if:Lembur,Y|nullable|date_format:H:i',
            'SelesaiLembur' => 'required_if:Lembur,Y|nullable|date_format:H:i',
            'AlasanLembur' => 'required_if:Lembur,Y|nullable|string|max:500',
            'FotoAbsenMasuk' => 'required|image|mimes:jpeg,png,jpg,webp|max:5120',
            'FotoAbsenKeluar' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
        ]);

        // 2. Cek Duplikasi: User + Tanggal yang sama, hanya untuk tenant aktif
        $Existing = Absensi::where('Nama', $Request->UserId)
            ->where('Tanggal', $Request->Tanggal)
            ->where('KodeTenant', $kodeTenant)
            ->first();

        if ($Existing) {
            return redirect()
                ->back()
                ->withInput()
                ->withErrors(['Tanggal' => 'Anda sudah mengisi absensi untuk tanggal ini.']);
        }

        // 3. Siapkan Data Dasar (Hanya ambil field yang aman, buang file object)
        $Data = $Request->only([
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
            'AlasanLembur'
        ]);

        $Data['UserCreate'] = Auth::user()->name ?? 'System';
        $Data['KodeTenant'] = $kodeTenant;

        // 4. Simpan file FotoAbsenMasuk tanpa kompresi
        if ($Request->hasFile('FotoAbsenMasuk')) {
            $FileMasuk = $Request->file('FotoAbsenMasuk');
            $FileNameMasuk = time() . '_masuk_' . uniqid() . '.' . $FileMasuk->getClientOriginalExtension();
            $FileMasuk->storeAs('public/absensi', $FileNameMasuk);
            $Data['FotoAbsenMasuk'] = 'absensi/' . $FileNameMasuk;
        }

        // 5. Simpan file FotoAbsenKeluar tanpa kompresi
        if ($Request->hasFile('FotoAbsenKeluar')) {
            $FileKeluar = $Request->file('FotoAbsenKeluar');
            $FileNameKeluar = time() . '_keluar_' . uniqid() . '.' . $FileKeluar->getClientOriginalExtension();
            $FileKeluar->storeAs('public/absensi', $FileNameKeluar);
            $Data['FotoAbsenKeluar'] = 'absensi/' . $FileNameKeluar;
        }

        Absensi::create($Data);

        return redirect()->route('absensi.index')->with('success', 'Data absensi berhasil ditambahkan.');
    }

    public function export(Request $request)
    {
        $kodeTenant = Auth::user()->KodeTenant ?? null;

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
            'SelesaiLembur',
            'KodeTenant'
        ])->orderBy('Tanggal', 'desc');

        // Filter by kode tenant
        if ($kodeTenant) {
            $query->where('KodeTenant', $kodeTenant);
        }

        $filterParts = [];

        if ($request->filled('bulan')) {
            $query->whereMonth('Tanggal', $request->bulan);
            $filterParts[] = 'Bulan: ' . Carbon::create()->month($request->bulan)->isoFormat('MMMM');
        }

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
        $kodeTenant = Auth::user()->KodeTenant ?? null;
        $user = User::where('KodeTenant', $kodeTenant)->get();
        $divisi = Divisi::where('KodeTenant', $kodeTenant)->get();
        return view('absensi.edit', compact('absensi', 'user', 'divisi'));
    }

    public function Update(Request $Request, Absensi $Absensi)
    {
        $kodeTenant = Auth::user()->KodeTenant ?? null;

        // Pastikan hanya edit absensi tenant sendiri
        if ($Absensi->KodeTenant !== $kodeTenant) {
            abort(403, 'Aksi tidak diizinkan pada data tenant lain.');
        }

        $Request->validate([
            'Nama' => 'required',
            'Divisi' => 'required|string|max:100',
            'NoHp' => 'required|string|max:20',
            'Tanggal' => 'required|date',
            'JamHadir' => 'nullable|date_format:H:i',
            'JamPulang' => 'nullable|date_format:H:i',
            'Status' => 'required|in:H,I,S,TK',
            'Lembur' => 'required|in:Y,N',
            'MulaiLembur' => 'required_if:Lembur,Y|nullable|date_format:H:i',
            'SelesaiLembur' => 'required_if:Lembur,Y|nullable|date_format:H:i',
            'AlasanLembur' => 'required_if:Lembur,Y|nullable|string|max:500',
            'FotoAbsenMasuk' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'FotoAbsenKeluar' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
        ]);
        $Data = $Request->only([
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
            'AlasanLembur'
        ]);

        $Data['UserUpdate'] = Auth::user()->name ?? 'System';
        $Data['KodeTenant'] = $kodeTenant; // Update kode tenant jika diperlukan

        if ($Request->hasFile('FotoAbsenMasuk')) {
            if ($Absensi->FotoAbsenMasuk && Storage::disk('public')->exists($Absensi->FotoAbsenMasuk)) {
                Storage::disk('public')->delete($Absensi->FotoAbsenMasuk);
            }
            $FileMasuk = $Request->file('FotoAbsenMasuk');
            $FileNameMasuk = time() . '_masuk_' . uniqid() . '.' . $FileMasuk->getClientOriginalExtension();
            $FileMasuk->storeAs('absensi', $FileNameMasuk, 'public');
            $Data['FotoAbsenMasuk'] = 'absensi/' . $FileNameMasuk;
        }

        if ($Request->hasFile('FotoAbsenKeluar')) {
            if ($Absensi->FotoAbsenKeluar && Storage::disk('public')->exists($Absensi->FotoAbsenKeluar)) {
                Storage::disk('public')->delete($Absensi->FotoAbsenKeluar);
            }
            $FileKeluar = $Request->file('FotoAbsenKeluar');
            $FileNameKeluar = time() . '_keluar_' . uniqid() . '.' . $FileKeluar->getClientOriginalExtension();
            $FileKeluar->storeAs('absensi', $FileNameKeluar, 'public');
            $Data['FotoAbsenKeluar'] = 'absensi/' . $FileNameKeluar;
        }

        $Absensi->update($Data);

        return redirect()->route('absensi.index')->with('success', 'Data absensi berhasil diperbarui.');
    }

    public function destroy(Absensi $absensi)
    {
        $kodeTenant = Auth::user()->KodeTenant ?? null;
        // Hanya boleh menghapus absensi dari tenant sendiri
        if ($absensi->KodeTenant !== $kodeTenant) {
            return response()->json(['success' => false, 'status' => 403, 'message' => 'Tidak diizinkan menghapus data tenant lain.'], 403);
        }

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
