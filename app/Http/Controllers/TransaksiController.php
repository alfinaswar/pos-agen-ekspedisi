<?php

namespace App\Http\Controllers;

use App\Models\Transaksi;
use App\Models\Ekspedisi; // Pastikan model ini di-import jika ada relasi
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Log;

class TransaksiController extends Controller
{  
    public function index(Request $request)
    {
        if ($request->ajax()) {
            // Gunakan eager loading untuk mengambil nama ekspedisi agar lebih efisien
            $data = Transaksi::with('ekspedisi')->select('transaksis.*');

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('NamaPelanggan', function ($row) {
                    return $row->NamaPelanggan ?? $row->Pelanggan ?? '-'; // Sesuaikan dengan nama kolom di DB Anda
                })
                ->addColumn('NamaEkspedisi', function ($row) {
                    return $row->ekspedisi ? $row->ekspedisi->NamaEkspedisi : '-';
                })
                ->addColumn('action', function ($row) {
                    $btn = '<div class="d-flex gap-1 justify-content-center">';
                    $btn .= '<a href="' . route('transaksi.edit', $row->id) . '" class="btn btn-warning btn-sm text-white" title="Edit">';
                    $btn .= '<i class="ti ti-edit"></i></a> ';
                    $btn .= '<button type="button" class="btn btn-danger btn-sm btn-delete" data-id="' . $row->id . '" data-invoice="' . htmlspecialchars($row->NoInvoice) . '" title="Hapus">';
                    $btn .= '<i class="ti ti-trash"></i></button>';
                    $btn .= '</div>';
                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('transaksi.index');
    }

    public function create()
    {
        $ekspedisis = Ekspedisi::all(); // Untuk dropdown di form create
        return view('transaksi.create', compact('ekspedisis'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'NoInvoice' => 'required|string|unique:transaksis,NoInvoice',
            'Tanggal' => 'required|date',
            'NamaPelanggan' => 'required|string|max:255',
            'Ekspedisi_ID' => 'required|exists:ekspedisis,id',
            'TotalBiaya' => 'required|numeric|min:0',
            'Status' => 'required|in:Proses,Selesai,Batal',
        ]);

        Transaksi::create($request->all());

        return redirect()->route('transaksi.index')->with('success', 'Transaksi berhasil ditambahkan.');
    }

    public function edit(Transaksi $transaksi)
    {
        $ekspedisis = Ekspedisi::all();
        return view('transaksi.edit', compact('transaksi', 'ekspedisis'));
    }

    public function update(Request $request, Transaksi $transaksi)
    {
        $request->validate([
            'NoInvoice' => 'required|string|unique:transaksis,NoInvoice,' . $transaksi->id,
            'Tanggal' => 'required|date',
            'NamaPelanggan' => 'required|string|max:255',
            'Ekspedisi_ID' => 'required|exists:ekspedisis,id',
            'TotalBiaya' => 'required|numeric|min:0',
            'Status' => 'required|in:Proses,Selesai,Batal',
        ]);

        $transaksi->update($request->all());

        return redirect()->route('transaksi.index')->with('success', 'Transaksi berhasil diperbarui.');
    }

    public function destroy(Transaksi $transaksi)
    {
        try {
            $transaksi->delete();

            return response()->json([
                'success' => true,
                'status' => 200,
                'message' => 'Data transaksi berhasil dihapus.'
            ]);
        } catch (\Exception $e) {
            Log::error('Gagal menghapus transaksi: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'status' => 500,
                'message' => 'Gagal menghapus data. Pastikan data tidak memiliki ketergantungan.'
            ], 500);
        }
    }
}
