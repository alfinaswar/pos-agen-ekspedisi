<?php

namespace App\Http\Controllers;

use App\Models\Ekspedisi;
use App\Models\Transaksi;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class TransaksiController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Transaksi::with('ekspedisi')
                ->select([
                    'id',
                    'KodeTransaksi',
                    'Tanggal',
                    'Ekspedisi',
                    'NoResi',
                    'Metode',
                    'Pendapatan'
                ])
                ->orderBy('created_at', 'desc'); // Urutkan dari created at terbaru
            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('Ekspedisi', function($row) {
                    return $row->ekspedisi && $row->ekspedisi->NamaEkspedisi
                        ? $row->ekspedisi->NamaEkspedisi
                        : '<span class="text-muted">-</span>';
                })
                ->addColumn('action', function ($row) {
                    $btn = '<div class="d-flex gap-1 justify-content-center">';
                    $btn .= '<a href="' . route('transaksi.edit', $row->id) . '" class="btn btn-warning btn-sm text-white" title="Edit">';
                    $btn .= '<i class="ti ti-edit"></i></a> ';
                    // Perhatikan: data-kode digunakan untuk pesan konfirmasi SweetAlert
                    $btn .= '<button type="button" class="btn btn-danger btn-sm btn-delete" data-id="' . $row->id . '" data-kode="' . htmlspecialchars($row->KodeTransaksi ?? 'Tanpa Kode') . '" title="Hapus">';
                    $btn .= '<i class="ti ti-trash"></i></button>';
                    $btn .= '</div>';
                    return $btn;
                })
                ->rawColumns(['action', 'Ekspedisi'])
                ->make(true);

        }


        return view('transaksi.index');
    }

    public function create()
    {
        $ekspedisis = Ekspedisi::get(); // Uncomment jika model Ekspedisi sudah ada
        return view('transaksi.create',compact('ekspedisis'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'KodeTransaksi' => 'nullable|string|unique:transaksis,KodeTransaksi',
            'Tanggal'       => 'required|date',
            'Ekspedisi'     => 'nullable|string|max:255',
            'NoResi'        => 'nullable|string|max:255',
            'Metode'        => 'required|in:Tunai,Non-Tunai',
            'Pendapatan'    => 'required|numeric|min:0',
            'KodeBayar'     => 'required_if:Metode,Non-Tunai|nullable|string|max:255',
            'BuktiBayar'    => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048', // Maks 2MB
            'Keterangan'    => 'nullable|string',
        ]);


        $data = $request->except(['BuktiBayar']);


        if (empty($data['KodeTransaksi'])) {
            unset($data['KodeTransaksi']);
        }

        $data['UserCreate'] = auth()->user()->name ?? 'System';

        if ($request->hasFile('BuktiBayar')) {
            $file = $request->file('BuktiBayar');
            $fileName = time() . '_' . preg_replace('/[^A-Za-z0-9\-_\.]/', '', $file->getClientOriginalName());
            $filePath = $file->storeAs('bukti-bayar', $fileName, 'public');
            $data['BuktiBayar'] = $filePath;
        }

        Transaksi::create($data);

        return redirect()->route('transaksi.index')->with('success', 'Transaksi berhasil ditambahkan.');
    }

    public function edit(Transaksi $transaksi)
    {
        $ekspedisis = Ekspedisi::get();
        return view('transaksi.edit', compact('transaksi','ekspedisis'));
    }

    public function update(Request $request, Transaksi $transaksi)
    {
        // 1. Validasi (abaikan unique untuk ID saat ini)
        $request->validate([
            'KodeTransaksi' => 'nullable|string|unique:transaksis,KodeTransaksi,' . $transaksi->id,
            'Tanggal' => 'required|date',
            'Ekspedisi' => 'nullable|string|max:255',
            'NoResi' => 'nullable|string|max:255',
            'Metode' => 'required|in:Tunai,Non-Tunai',
            'Pendapatan' => 'required|numeric|min:0',
            'KodeBayar' => 'required_if:Metode,Non-Tunai|nullable|string|max:255',
            'BuktiBayar' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'Keterangan' => 'nullable|string',
        ]);

        $data = $request->except(['BuktiBayar']);
        $data['UserUpdate'] = auth()->user()->name ?? 'System';

        // 2. Handle Upload File Baru (dan Hapus File Lama)
        if ($request->hasFile('BuktiBayar')) {
            // Hapus file lama dari storage jika ada
            if ($transaksi->BuktiBayar && Storage::disk('public')->exists($transaksi->BuktiBayar)) {
                Storage::disk('public')->delete($transaksi->BuktiBayar);
            }

            $file = $request->file('BuktiBayar');
            $fileName = time() . '_' . preg_replace('/[^A-Za-z0-9\-_\.]/', '', $file->getClientOriginalName());
            $filePath = $file->storeAs('bukti-bayar', $fileName, 'public');
            $data['BuktiBayar'] = $filePath;
        } else {
            // Jika tidak ada file baru, pastikan field tidak ter-overwrite jadi null
            unset($data['BuktiBayar']);
        }

        // 3. Update data
        $transaksi->update($data);

        return redirect()->route('transaksi.index')->with('success', 'Transaksi berhasil diperbarui.');
    }

    public function destroy(Transaksi $transaksi)
    {
        try {
            if ($transaksi->BuktiBayar && Storage::disk('public')->exists($transaksi->BuktiBayar)) {
                Storage::disk('public')->delete($transaksi->BuktiBayar);
            }

            $transaksi->update(['UserDelete' => auth()->user()->name ?? 'System']);
            $transaksi->delete(); // Soft delete

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
                'message' => 'Gagal menghapus data.'
            ], 500);
        }
    }
}
