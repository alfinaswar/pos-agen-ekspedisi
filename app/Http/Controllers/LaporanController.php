<?php

namespace App\Http\Controllers;

use App\Exports\LaporanPendapatanExport;
use App\Models\Transaksi;
use App\Models\Ekspedisi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;

class LaporanController extends Controller
{
    public function index(Request $request)
    {
        $type = $request->get('type', 'harian');

        // Default: Harian pakai tanggal hari ini, lainnya pakai bulan ini
        $defaultDate = ($type === 'harian') ? date('Y-m-d') : date('Y-m');
        $tanggal = $request->get('tanggal', $defaultDate);

        $date = Carbon::parse($tanggal);

        // 1. Tentukan Range Tanggal
        if ($type === 'harian') {
            $startDate = $date->copy()->startOfDay();
            $endDate = $date->copy()->endOfDay();
        } else {
            // Bulanan, Per User, Per Divisi menggunakan range 1 bulan penuh
            $startDate = $date->copy()->startOfMonth();
            $endDate = $date->copy()->endOfMonth();
        }

        // 2. Query Data Berdasarkan Tipe
        if ($type === 'per_user') {
            $data = Transaksi::with('userCreate')->whereBetween('Tanggal', [$startDate, $endDate])
                ->select(
                    'UserCreate',
                    DB::raw('COUNT(*) as jumlah_transaksi'),
                    DB::raw('SUM(PendapatanBersih) as total_pendapatan')
                )
                ->groupBy('UserCreate')
                ->orderBy('total_pendapatan', 'desc')
                ->get();

            $chartLabels = $data->pluck('userCreate.name')->map(fn($n) => $n ?: 'Tidak Diketahui')->toArray();

        } elseif ($type === 'per_divisi') {
            // ✅ PERBAIKAN: Ambil langsung dari field Divisi di tabel transaksis
            $data = Transaksi::whereBetween('Tanggal', [$startDate, $endDate])
                ->select(
                    'Divisi', // Field Divisi di tabel transaksis
                    DB::raw('COUNT(*) as jumlah_transaksi'),
                    DB::raw('SUM(PendapatanBersih) as total_pendapatan')
                )
                ->groupBy('Divisi')
                ->orderBy('total_pendapatan', 'desc')
                ->get();

            $chartLabels = $data->pluck('Divisi')->map(fn($n) => $n ?: 'Tanpa Divisi')->toArray();

        } else {
            // Harian & Bulanan (Default: Group by Ekspedisi)
            $data = Transaksi::whereBetween('Tanggal', [$startDate, $endDate])
                ->select(
                    'Ekspedisi',
                    DB::raw('COUNT(*) as jumlah_transaksi'),
                    DB::raw('SUM(PendapatanBersih) as total_pendapatan')
                )
                ->groupBy('Ekspedisi')
                ->orderBy('total_pendapatan', 'desc')
                ->get();

            $expeditionNames = Ekspedisi::pluck('NamaEkspedisi', 'id')->toArray();
            $chartLabels = $data->pluck('Ekspedisi')->map(function ($id) use ($expeditionNames) {
                return $expeditionNames[$id] ?? 'Ekspedisi ' . $id;
            })->toArray();
        }

        // 3. Hitung Total & Persentase
        $totalTransaksi = $data->sum('jumlah_transaksi');
        $totalPendapatan = $data->sum('total_pendapatan');

        $dataWithPercentage = $data->map(function ($item) use ($totalPendapatan) {
            $item->persentase = $totalPendapatan > 0
                ? round(($item->total_pendapatan / $totalPendapatan) * 100, 1)
                : 0;
            return $item;
        });

        // 4. Siapkan Data Grafik (Bar Chart Sederhana untuk SEMUA tipe)
        $chartData = $data->pluck('total_pendapatan')->toArray();
        $expeditionNames = isset($expeditionNames) ? $expeditionNames : [];

        return view('laporan.index', compact(
            'type',
            'tanggal',
            'data',
            'dataWithPercentage',
            'totalTransaksi',
            'totalPendapatan',
            'chartLabels',
            'chartData',
            'expeditionNames'
        ));
    }

    public function exportExcel(Request $request)
    {
        $type = $request->get('type', 'harian');
        $tanggal = $request->get('tanggal', date('Y-m-d'));
        $date = Carbon::parse($tanggal);

        // Tentukan range & nama file
        if ($type === 'harian') {
            $startDate = $date->copy()->startOfDay();
            $endDate = $date->copy()->endOfDay();
            $filename = "Laporan_Harian_{$date->format('Y-m-d')}.xlsx";
        } else {
            $startDate = $date->copy()->startOfMonth();
            $endDate = $date->copy()->endOfMonth();
            $filename = "Laporan_" . ucfirst($type) . "_{$date->format('Y_m')}.xlsx";
        }

        // Query Export (Sama dengan logic index)
        if ($type === 'per_user') {
            $data = Transaksi::whereBetween('Tanggal', [$startDate, $endDate])
                ->select('UserCreate', DB::raw('COUNT(*) as jumlah_transaksi'), DB::raw('SUM(PendapatanBersih) as total_pendapatan'))
                ->groupBy('UserCreate')->orderBy('total_pendapatan', 'desc')->get();
            $expeditionNames = [];
        } elseif ($type === 'per_divisi') {
            // ✅ PERBAIKAN: Ambil langsung dari field Divisi di tabel transaksis
            $data = Transaksi::whereBetween('Tanggal', [$startDate, $endDate])
                ->select(
                    'Divisi', // Field Divisi di tabel transaksis
                    DB::raw('COUNT(*) as jumlah_transaksi'),
                    DB::raw('SUM(PendapatanBersih) as total_pendapatan')
                )
                ->groupBy('Divisi')
                ->orderBy('total_pendapatan', 'desc')
                ->get();
            $expeditionNames = [];
        } else {
            $data = Transaksi::whereBetween('Tanggal', [$startDate, $endDate])
                ->select('Ekspedisi', DB::raw('COUNT(*) as jumlah_transaksi'), DB::raw('SUM(PendapatanBersih) as total_pendapatan'))
                ->groupBy('Ekspedisi')->orderBy('total_pendapatan', 'desc')->get();
            $expeditionNames = Ekspedisi::pluck('NamaEkspedisi', 'id')->toArray();
        }

        $totalTransaksi = $data->sum('jumlah_transaksi');
        $totalPendapatan = $data->sum('total_pendapatan');

        return Excel::download(
            new LaporanPendapatanExport($data, $totalTransaksi, $totalPendapatan, $type, $tanggal, $expeditionNames),
            $filename
        );
    }
}
