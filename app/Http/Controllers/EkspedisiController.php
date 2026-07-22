<?php

namespace App\Http\Controllers;

use App\Models\Ekspedisi;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Log;

class EkspedisiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Ekspedisi::select(['id', 'NamaEkspedisi', 'Deskripsi'])
                ->orderBy('id', 'desc'); // YANG TERBARU DULU
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $btn = '<div class="d-flex gap-1 justify-content-center">';
                    $btn .= '<a href="' . route('ekspedisi.edit', $row->id) . '" class="btn btn-warning btn-sm text-white" title="Edit">';
                    $btn .= '<i class="ti ti-edit"></i></a> ';
                    $btn .= '<button type="button" class="btn btn-danger btn-sm btn-delete" data-id="' . $row->id . '" data-nama="' . htmlspecialchars($row->NamaEkspedisi) . '" title="Hapus">';
                    $btn .= '<i class="ti ti-trash"></i></button>';
                    $btn .= '</div>';
                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }



        // Return view untuk request biasa
        return view('ekspedisi.index'); // Sesuaikan path view Anda (misal: 'admin.ekspedisi.index')
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('ekspedisi.create'); // Sesuaikan path view Anda
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validasi input
        $request->validate([
            'NamaEkspedisi' => 'required|string|max:255|unique:ekspedisi,NamaEkspedisi',
            'Deskripsi' => 'nullable|string|max:1000',
        ], [
            'NamaEkspedisi.required' => 'Nama ekspedisi wajib diisi.',
            'NamaEkspedisi.unique' => 'Nama ekspedisi sudah terdaftar.',
        ]);

        // Simpan data
        Ekspedisi::create([
            'NamaEkspedisi' => $request->NamaEkspedisi,
            'Deskripsi' => $request->Deskripsi,
        ]);

        // Redirect dengan pesan sukses (akan ditangkap oleh SweetAlert2 Toast di view)
        return redirect()->route('ekspedisi.index')->with('success', 'Data ekspedisi berhasil ditambahkan.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $ekspedisi = Ekspedisi::findOrFail($id); // cari dulu datanya
        return view('ekspedisi.edit', compact('ekspedisi'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {

        $ekspedisi = Ekspedisi::findOrFail($id);
        $request->validate([
            'NamaEkspedisi' => 'required|string|max:255|unique:ekspedisi,NamaEkspedisi,' . $ekspedisi->id,
            'Deskripsi' => 'nullable|string|max:1000',
        ]);

        $ekspedisi->update([
            'NamaEkspedisi' => $request->NamaEkspedisi,
            'Deskripsi' => $request->Deskripsi,
        ]);

        return redirect()->route('ekspedisi.index')->with('success', 'Data ekspedisi berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Ekspedisi $ekspedisi)
    {
        try {
            $ekspedisi->delete();

            // Response JSON ini dirancang agar cocok dengan logika JS:
            // if (response.status === 200 || response.success)
            return response()->json([
                'success' => true,
                'status' => 200,
                'message' => 'Data ekspedisi berhasil dihapus.'
            ]);
        } catch (\Exception $e) {
            Log::error('Gagal menghapus ekspedisi: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'status' => 500,
                'message' => 'Gagal menghapus data. Pastikan data tidak sedang digunakan di transaksi lain.'
            ], 500);
        }
    }
}
