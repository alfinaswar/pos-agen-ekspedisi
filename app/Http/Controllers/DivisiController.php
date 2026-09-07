<?php

namespace App\Http\Controllers;

use App\Models\Divisi;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Log;

class DivisiController extends Controller
{
    public function index(Request $request)
    {
        // Dapatkan kode tenant dari user login (pastikan field ini ada di tabel user)
        $kodeTenant = auth()->user()->KodeTenant ?? null;

        if ($request->ajax()) {
            $data = Divisi::select(['id', 'Nama', 'Keterangan', 'KodeTenant']);

            // Filter hanya data milik tenant terkait (jika ada kode tenant di user)
            if ($kodeTenant) {
                $data->where('KodeTenant', $kodeTenant);
            }

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $btn = '<div class="d-flex gap-1 justify-content-center">';
                    $btn .= '<a href="' . route('divisi.edit', $row->id) . '" class="btn btn-warning btn-sm text-white" title="Edit"><i class="ti ti-edit"></i></a> ';
                    $btn .= '<button type="button" class="btn btn-danger btn-sm btn-delete" data-id="' . $row->id . '" data-nama="' . htmlspecialchars($row->Nama) . '" title="Hapus"><i class="ti ti-trash"></i></button>';
                    $btn .= '</div>';
                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('divisi.index');
    }

    public function create()
    {
        return view('divisi.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'Nama' => 'required|string|max:100',
            'Keterangan' => 'nullable|string',
        ]);
        $data = $request->all();
        $data['UserCreate'] = auth()->user()->name ?? 'System';

        // Tambah kode tenant pada data (jika user login punya kode tenant)
        $data['KodeTenant'] = auth()->user()->KodeTenant ?? null;

        Divisi::create($data);
        return redirect()->route('divisi.index')->with('success', 'Divisi berhasil ditambahkan.');
    }

    public function edit(Divisi $divisi)
    {
        return view('divisi.edit', compact('divisi'));
    }

    public function update(Request $request, Divisi $divisi)
    {
        $request->validate([
            'Nama' => 'required|string|max:100',
            'Keterangan' => 'nullable|string',
        ]);
        $data = $request->all();
        $data['UserUpdate'] = auth()->user()->name ?? 'System';

        // Pastikan KodeTenant tidak diubah (tetap yang lama)
        unset($data['KodeTenant']);

        $divisi->update($data);
        return redirect()->route('divisi.index')->with('success', 'Divisi berhasil diperbarui.');
    }

    public function destroy(Divisi $divisi)
    {
        try {
            $divisi->update(['UserDelete' => auth()->user()->name ?? 'System']);
            $divisi->delete();
            return response()->json(['success' => true, 'status' => 200, 'message' => 'Data berhasil dihapus.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'status' => 500, 'message' => 'Gagal menghapus data.'], 500);
        }
    }
}
