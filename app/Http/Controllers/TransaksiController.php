<?php

namespace App\Http\Controllers;

use App\Exports\TransaksiExport;
use App\Models\Ekspedisi;
use App\Models\Transaksi;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class TransaksiController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            // 1. Buat base query
            $query = Transaksi::with(['ekspedisi', 'userCreate'])
                ->select([
                    'id',
                    'KodeTransaksi',
                    'Tanggal',
                    'Ekspedisi',
                    'NoResi',
                    'Metode',
                    'KodeBayar',
                    'Pendapatan',
                    'UserCreate'
                ])
                ->orderBy('Tanggal', 'desc');

            // Kalau bukan admin, hanya tampilkan data milik user itu sendiri
            if (!auth()->user() || auth()->user()->role !== 'Admin') {
                $query->where('UserCreate', auth()->id());
            }

            // 2. Terapkan filter (khusus admin, filter user juga ditampilkan; kalau bukan admin user di-force ke dirinya)
            if ($request->filled('tanggal_awal')) {
                $query->whereDate('Tanggal', '>=', $request->input('tanggal_awal'));
            }
            if ($request->filled('tanggal_akhir')) {
                $query->whereDate('Tanggal', '<=', $request->input('tanggal_akhir'));
            }
            if ($request->filled('metode')) {
                $query->where('Metode', $request->input('metode'));
            }
            if ($request->filled('ekspedisi')) {
                $query->where('Ekspedisi', $request->input('ekspedisi'));
            }
            // Filter user hanya boleh dilakukan admin, selebihnya ignored (filter already forced above)
            if ($request->filled('user') && auth()->user() && auth()->user()->role === 'Admin') {
                $query->where('UserCreate', $request->input('user'));
            }

            // 3. Hitung total pendapatan (gunakan clone agar tidak mengganggu query utama DataTables)
            $totalPendapatan = (clone $query)->sum('Pendapatan') ?? 0;

            // 4. Return DataTables
            return DataTables::of($query)
                ->addIndexColumn()
                ->editColumn('Ekspedisi', function ($row) {
                    return $row->ekspedisi && $row->ekspedisi->NamaEkspedisi
                        ? $row->ekspedisi->NamaEkspedisi
                        : '<span class="text-muted">-</span>';
                })
                ->addColumn('action', function ($row) {
                    $btn = '<div class="d-flex gap-1 justify-content-center">';
                    $btn .= '<a href="' . route('transaksi.edit', $row->id) . '" class="btn btn-warning btn-sm text-white" title="Edit">';
                    $btn .= '<i class="ti ti-edit"></i></a> ';
                    $btn .= '<button type="button" class="btn btn-danger btn-sm btn-delete" data-id="' . $row->id . '" data-kode="' . htmlspecialchars($row->KodeTransaksi ?? 'Tanpa Kode') . '" title="Hapus">';
                    $btn .= '<i class="ti ti-trash"></i></button>';
                    $btn .= '</div>';
                    return $btn;
                })
                ->editColumn('UserCreate', function($row) {
                    return $row->userCreate && $row->userCreate->name
                        ? $row->userCreate->name
                        : '<span class="text-muted">-</span>';
                })

                // Kirim total ke frontend
                ->with('total_pendapatan', number_format($totalPendapatan, 0, ',', '.'))
                ->rawColumns(['action', 'Ekspedisi'])
                ->make(true);
        }

        $ekspedisi = Ekspedisi::get();
        // Kalau bukan admin, hanya kirim data user itu saja ke view (untuk filter jika pakai blade select dsb)
        if (auth()->user() && auth()->user()->role !== 'Admin') {
            $users = User::where('id', auth()->id())->get();
        } else {
            $users = User::all();
        }

        return view('transaksi.index', compact('ekspedisi', 'users'));
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
            'Metode'        => 'required|in:Tunai,Non-Tunai,COD',
            'Pendapatan'    => 'required|numeric|min:0',
            'KodeBayar'     => 'required_if:Metode,Non-Tunai|nullable|string|max:255',
            'BuktiBayar'    => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048', // Maks 2MB
            'Keterangan'    => 'nullable|string',
        ]);


        $data = $request->except(['BuktiBayar']);


        if (empty($data['KodeTransaksi'])) {
            unset($data['KodeTransaksi']);
        }

        $data['UserCreate'] = auth()->id();

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
    public function show(Transaksi $transaksi)
    {
        // kosong
    }

    public function update(Request $request, Transaksi $transaksi)
    {
        // 1. Validasi (abaikan unique untuk ID saat ini)
        $request->validate([
            'KodeTransaksi' => 'nullable|string|unique:transaksis,KodeTransaksi,' . $transaksi->id,
            'Tanggal' => 'required|date',
            'Ekspedisi' => 'nullable|string|max:255',
            'NoResi' => 'nullable|string|max:255',
            'Metode' => 'required|in:Tunai,Non-Tunai,COD',
            'Pendapatan' => 'required|numeric|min:0',
            'KodeBayar' => 'required_if:Metode,Non-Tunai|nullable|string|max:255',
            'BuktiBayar' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'Keterangan' => 'nullable|string',
        ]);


        $data = $request->except(['BuktiBayar']);
        $data['UserUpdate'] = auth()->id();

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
    public function export(Request $request)
    {
        // 1. Ambil data dengan filter yang sama seperti DataTable
        $query = Transaksi::with('ekspedisi','userCreate')
            ->select([
                'id',
                'KodeTransaksi',
                'Tanggal',
                'Ekspedisi',
                'NoResi',
                'Metode',
                'KodeBayar',
                'UserCreate',
                'Pendapatan'
            ])
            ->orderBy('created_at', 'desc');

        $filterInfo = "Semua Data";
        $params = [];

        // Terapkan filter
        if ($request->filled('tanggal_awal')) {
            $query->whereDate('Tanggal', '>=', $request->input('tanggal_awal'));
            $params['tanggal_awal'] = $request->input('tanggal_awal');
            $filterInfo = "Periode: " . Carbon::parse($request->tanggal_awal)->isoFormat('D MMMM YYYY');
        }
        if ($request->filled('tanggal_akhir')) {
            $query->whereDate('Tanggal', '<=', $request->input('tanggal_akhir'));
            $params['tanggal_akhir'] = $request->input('tanggal_akhir');
            $filterInfo .= " s/d " . Carbon::parse($request->tanggal_akhir)->isoFormat('D MMMM YYYY');
        }
        if ($request->filled('metode')) {
            $query->where('Metode', $request->input('metode'));
            $params['metode'] = $request->input('metode');
            $filterInfo .= " | Metode: " . $request->metode;
        }

        $data = $query->get();
        $totalPendapatan = $data->sum('Pendapatan');

        // 2. Generate filename dengan timestamp
        $filename = "Laporan_Transaksi_" . Carbon::now()->format('Y-m-d_His') . ".xlsx";

        // 3. Kirim ke Export Class
        return Excel::download(
            new TransaksiExport($data, $totalPendapatan, $filterInfo, $params),
            $filename
        );
    }
    public function destroy(Transaksi $transaksi)
    {
        try {
            if ($transaksi->BuktiBayar && Storage::disk('public')->exists($transaksi->BuktiBayar)) {
                Storage::disk('public')->delete($transaksi->BuktiBayar);
            }

            $transaksi->update(['UserDelete' => auth()->id()]);
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
