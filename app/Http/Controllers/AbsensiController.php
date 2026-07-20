<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use App\Models\User;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Log;

class AbsensiController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            // Tampilkan data terbaru (terbaru di atas) dengan order by Tanggal DESC, id DESC
            $data = Absensi::with('getUser')->select(['id', 'Nama', 'Divisi', 'NoHp', 'Tanggal', 'JamHadir', 'JamPulang', 'Status', 'Lembur', 'MulaiLembur', 'SelesaiLembur'])
                ->orderBy('Tanggal', 'desc')
                ->orderBy('id', 'desc');

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $btn = '<div class="d-flex gap-1 justify-content-center">';
                    $btn .= '<a href="' . route('absensi.edit', $row->id) . '" class="btn btn-warning btn-sm text-white" title="Edit">';
                    $btn .= '<i class="ti ti-edit"></i></a> ';
                    $btn .= '<button type="button" class="btn btn-danger btn-sm btn-delete" data-id="' . $row->id . '" data-nama="' . htmlspecialchars($row->Nama) . '" title="Hapus">';
                    $btn .= '<i class="ti ti-trash"></i></button>';
                    $btn .= '</div>';
                    return $btn;
                })
                ->editColumn('Nama', function ($row) {
                    if ($row->getUser) {
                        return $row->getUser->name;
                    }
                    return $row->Nama;
                })

                ->rawColumns(['action'])
                ->make(true);
        }

        return view('absensi.index');
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
            'JamHadir' => 'nullable|date_format:H:i',
            'JamPulang' => 'nullable|date_format:H:i',
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
