<?php

namespace App\Http\Controllers;

use App\Exports\AbsensiExport;
use App\Models\Absensi;
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
            $query = Absensi::with('getUser')->select([
                'id', 'Nama', 'Divisi', 'NoHp', 'Tanggal',
                'JamHadir', 'JamPulang', 'Status', 'Lembur', 'MulaiLembur', 'SelesaiLembur'
            ]);

            // Jika bukan Admin, filter data agar hanya menampilkan absensi user ini saja
            if (!auth()->user() || auth()->user()->role !== 'Admin') {
                $query->where('Nama', auth()->user()->id);
            }
            // Kalau Admin, tampilkan semua dan izinkan filter
            else {
                // 1. Filter Bulan
                if ($request->filled('bulan')) {
                    $query->whereMonth('Tanggal', $request->bulan);
                }

                // 2. Filter Status
                if ($request->filled('status')) {
                    $query->where('Status', $request->status);
                }

                // 3. Filter User (berdasarkan Nama)
                if ($request->filled('user_name')) {
                    $query->where('Nama', $request->user_name);
                }
            }

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $btn = '<div class="d-flex gap-1 justify-content-center">';
                    // Admin: Bisa edit & hapus
                    if (auth()->user() && (auth()->user()->role === 'Admin' || auth()->user()->role === 'Leader')) {
                        $btn .= '<a href="' . route('absensi.edit', $row->id) . '" class="btn btn-warning btn-sm text-white" title="Edit">';
                        $btn .= '<i class="ti ti-edit"></i></a> ';
                        $btn .= '<button type="button" class="btn btn-danger btn-sm btn-delete" data-id="' . $row->id . '" data-nama="' . htmlspecialchars($row->getUser->name) . '" title="Hapus">';
                        $btn .= '<i class="ti ti-trash"></i></button>';
                    }
                    // User lain selain admin, hanya boleh hapus
                    elseif (auth()->user()) {
                        $btn .= '<button type="button" class="btn btn-danger btn-sm btn-delete" data-id="' . $row->id . '" data-nama="' . htmlspecialchars($row->getUser->name) . '" title="Hapus">';
                        $btn .= '<i class="ti ti-trash"></i></button>';
                    }
                    $btn .= '</div>';
                    return $btn;
                })
                ->editColumn('Nama', function($row) {
                    return htmlspecialchars($row->getUser->name);
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        // Admin bisa memilih user untuk filter di view, non-admin hanya dapat dirinya
        if (auth()->user() && auth()->user()->role === 'Admin') {
            $users = User::get();
        } else {
            $users = User::where('name', auth()->user()->name)->get();
        }
        return view('absensi.index',compact('users'));
    }

    public function create()
    {
        $user = User::get();
        return view('absensi.create',compact('user'));
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
        return view('absensi.edit', compact('absensi','user'));
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
