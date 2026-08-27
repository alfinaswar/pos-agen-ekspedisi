<?php

namespace App\Http\Controllers;

use App\Models\MasterPaketHarga;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

class MasterPaketHargaController extends Controller
{
    public function Index(Request $Request)
    {
        if ($Request->ajax()) {
            $Query = MasterPaketHarga::latest('CreatedAt');

            return DataTables::of($Query)
                ->addIndexColumn()
                ->editColumn('Harga', function ($Row) {
                    return 'Rp ' . number_format($Row->Harga, 0, ',', '.');
                })
                ->editColumn('Status', function ($Row) {
                    $Badge = $Row->Status === 'Aktif' ? 'bg-success' : 'bg-secondary';
                    return '<span class="badge ' . $Badge . '">' . $Row->Status . '</span>';
                })
                ->addColumn('action', function ($Row) {
                    $Btn = '<div class="d-flex gap-1 justify-content-center">';
                    $Btn .= '<a href="' . route('master-paket-harga.edit', $Row->id) . '" class="btn btn-warning btn-sm text-white" title="Edit"><i class="ti ti-edit"></i></a> ';
                    $Btn .= '<button type="button" class="btn btn-danger btn-sm btn-hapus" data-id="' . $Row->id . '" data-nama="' . htmlspecialchars($Row->NamaPaket) . '" title="Hapus"><i class="ti ti-trash"></i></button>';
                    $Btn .= '</div>';
                    return $Btn;
                })
                ->rawColumns(['Harga', 'Status', 'action'])
                ->make(true);
        }

        return view('master-paket-harga.index');
    }

    public function Create()
    {
        return view('master-paket-harga.create');
    }

    public function Store(Request $Request)
    {
        $Request->validate([
            'NamaPaket' => 'required|string|max:255',
            'KodePaket' => 'required|string|max:50|unique:master_paket_hargas,KodePaket',
            'Deskripsi' => 'nullable|string',
            'Harga' => 'required|string', // String karena diformat dengan titik, akan dibersihkan di bawah
            'DurasiBulan' => 'required|integer|min:1',
            'Fitur' => 'nullable|string', // Akan diubah jadi array JSON
            'Status' => 'required|in:Aktif,Nonaktif',
        ]);

        // Bersihkan format angka (hapus titik ribuan)
        $CleanHarga = str_replace('.', '', $Request->Harga);

        // Ubah textarea fitur (dipisah enter) menjadi JSON Array
        $FiturArray = [];
        if (!empty($Request->Fitur)) {
            $FiturArray = array_filter(array_map('trim', explode("\n", $Request->Fitur)));
        }

        $Data = $Request->except(['Harga', 'Fitur']);
        $Data['Harga'] = $CleanHarga;
        $Data['Fitur'] = $FiturArray;
        $Data['UserCreate'] = Auth::user()->name ?? 'System';

        MasterPaketHarga::create($Data);

        return redirect()->route('master-paket-harga.index')->with('success', 'Paket harga berhasil ditambahkan.');
    }

    public function Edit(MasterPaketHarga $MasterPaketHarga)
    {
        return view('master-paket-harga.edit', compact('MasterPaketHarga'));
    }

    public function Update(Request $Request, MasterPaketHarga $MasterPaketHarga)
    {
        $Request->validate([
            'NamaPaket' => 'required|string|max:255',
            'KodePaket' => 'required|string|max:50|unique:master_paket_hargas,KodePaket,' . $MasterPaketHarga->id,
            'Deskripsi' => 'nullable|string',
            'Harga' => 'required|string',
            'DurasiBulan' => 'required|integer|min:1',
            'Fitur' => 'nullable|string',
            'Status' => 'required|in:Aktif,Nonaktif',
        ]);

        $CleanHarga = str_replace('.', '', $Request->Harga);

        $FiturArray = [];
        if (!empty($Request->Fitur)) {
            $FiturArray = array_filter(array_map('trim', explode("\n", $Request->Fitur)));
        }

        $Data = $Request->except(['Harga', 'Fitur']);
        $Data['Harga'] = $CleanHarga;
        $Data['Fitur'] = $FiturArray;
        $Data['UserUpdate'] = Auth::user()->name ?? 'System';

        $MasterPaketHarga->update($Data);

        return redirect()->route('master-paket-harga.index')->with('success', 'Paket harga berhasil diperbarui.');
    }

    public function Destroy(MasterPaketHarga $MasterPaketHarga)
    {
        try {
            $MasterPaketHarga->UserDelete = Auth::user()->name ?? 'System';
            $MasterPaketHarga->save();
            $MasterPaketHarga->delete();

            return response()->json([
                'success' => true,
                'message' => 'Paket harga berhasil dihapus.'
            ]);
        } catch (\Exception $Exception) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus data: ' . $Exception->getMessage()
            ], 500);
        }
    }
}
