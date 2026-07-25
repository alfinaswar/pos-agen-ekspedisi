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

        // Default tanggal: Bulan ini untuk bulanan/per_user/per_divisi, Hari ini untuk lainnya
        $defaultDate = in_array($type, ['bulanan', 'per_user', 'per_divisi']) ? date('Y-m') : date('Y-m-d');
        $tanggal = $request->get('tanggal', $defaultDate);

        $date = Carbon::parse($tanggal);

        // 1. Tentukan Range Tanggal
        if (in_array($type, ['bulanan', 'per_user', 'per_divisi'])) {
            $startDate = $date->copy()->startOfMonth();
            $endDate = $date->copy()->endOfMonth();
        } else {
            $startDate = $date->copy()->startOfDay();
            $endDate = $date->copy()->endOfDay();
        }

        // 2. Query Data Berdasarkan Tipe (Menggunakan PendapatanBersih)
        if ($type === 'per_user') {
            $data = Transaksi::whereBetween('Tanggal', [$startDate, $endDate])
                ->select(
                    'UserCreate',
                    DB::raw('COUNT(*) as jumlah_transaksi'),
                    DB::raw('SUM(PendapatanBersih) as total_pendapatan') // <-- DIUBAH
                )
                ->groupBy('UserCreate')
                ->orderBy('total_pendapatan', 'desc')
                ->get();

            $chartLabels = $data->pluck('UserCreate')->map(fn($n) => $n ?: 'Tidak Diketahui')->toArray();

        } elseif ($type === 'per_divisi') {
            // Kemungkinan "Tanpa Divisi" sering muncul karena ada transaksi dengan users.divisi NULL/tidak sesuai dengan divisis.id.
            // Perbaikan: groupBy harus ikut groupBy NULL, dan label dibedakan dengan jelas berdasarkan hasil join.

            $data = Transaksi::select(
                DB::raw('COALESCE(divisis.Nama, "Tanpa Divisi") as NamaDivisi'),
                DB::raw('COUNT(transaksis.id) as jumlah_transaksi'),
                DB::raw('SUM(transaksis.PendapatanBersih) as total_pendapatan')
            )
                ->leftJoin('users', 'transaksis.UserCreate', '=', 'users.name')
                ->leftJoin('divisis', 'users.divisi', '=', 'divisis.id')
                ->whereBetween('transaksis.Tanggal', [$startDate, $endDate])
                // COALESCE agar NULL digroup sebagai "Tanpa Divisi"
                ->groupBy(DB::raw('COALESCE(divisis.Nama, "Tanpa Divisi")'))
                ->orderBy('total_pendapatan', 'desc')
                ->get();

            // chartLabels otomatis sudah jadi text, tidak perlu pengecekan lagi
            $chartLabels = $data->pluck('NamaDivisi')->toArray();
        } else {
            // Logika Ekspedisi / Harian / Bulanan
            $data = Transaksi::whereBetween('Tanggal', [$startDate, $endDate])
            ->select(
                    'Ekspedisi',
                    DB::raw('COUNT(*) as jumlah_transaksi'),
                    DB::raw('SUM(PendapatanBersih) as total_pendapatan') // <-- DIUBAH
                    )
                    ->groupBy('Ekspedisi')
                    ->orderBy('total_pendapatan', 'desc')
                    ->get();

                    // dd($data);
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

        // 4. Siapkan Data Grafik (Line Chart 7 Hari Terakhir KHUSUS User & Divisi)
        $chartType = in_array($type, ['per_user', 'per_divisi']) ? 'line' : 'bar';
        $chartDatasets = [];
        $finalChartLabels = [];

        if ($chartType === 'line') {
            $last7Days = [];
            for ($i = 6; $i >= 0; $i--) {
                $d = Carbon::today()->subDays($i);
                $finalChartLabels[] = $d->isoFormat('ddd, D MMM');
                $last7Days[] = $d->format('Y-m-d');
            }

            $groupColumn = ($type === 'per_user') ? 'UserCreate' : 'divisis.Nama';

            // Ambil data harian untuk 7 hari terakhir (Menggunakan PendapatanBersih)
            $dailyData = Transaksi::select(
                DB::raw('DATE(transaksis.Tanggal) as tgl'),
                $groupColumn . ' as group_name',
                DB::raw('SUM(transaksis.PendapatanBersih) as total') // <-- DIUBAH
            )
                ->when($type === 'per_divisi', function ($q) {
                    $q->leftJoin('users', 'transaksis.UserCreate', '=', 'users.name')
                        ->leftJoin('divisis', 'users.divisi', '=', 'divisis.id');
                })
                ->whereIn(DB::raw('DATE(transaksis.Tanggal)'), $last7Days)
                ->groupBy('tgl', 'group_name')
                ->get();

            $colors = ['#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', '#ec4899', '#06b6d4', '#f97316'];

            foreach ($chartLabels as $index => $label) {
                $dataPoints = [];
                foreach ($last7Days as $day) {
                    $record = $dailyData->firstWhere(function ($item) use ($day, $label) {
                        return $item->tgl === $day && $item->group_name === $label;
                    });
                    $dataPoints[] = $record ? (float) $record->total : 0;
                }

                $color = $colors[$index % count($colors)];
                $chartDatasets[] = [
                    'label' => $label,
                    'data' => $dataPoints,
                    'borderColor' => $color,
                    'backgroundColor' => $color . '33',
                    'fill' => true,
                    'tension' => 0.4,
                    'pointRadius' => 4,
                    'borderWidth' => 2
                ];
            }
        } else {
            $chartData = $dataWithPercentage->pluck('total_pendapatan')->toArray();
            $chartDatasets = [
                [
                    'label' => 'Pendapatan Bersih', // <-- DIUBAH
                    'data' => $chartData,
                    'backgroundColor' => '#3b82f6',
                    'borderRadius' => 6,
                    'borderSkipped' => false,
                ]
            ];
            $finalChartLabels = $chartLabels;
        }

        $expeditionNames = isset($expeditionNames) ? $expeditionNames : [];

        return view('laporan.index', compact(
            'type',
            'tanggal',
            'data',
            'dataWithPercentage',
            'totalTransaksi',
            'totalPendapatan',
            'finalChartLabels',
            'chartDatasets',
            'chartType',
            'expeditionNames'
        ));
    }

    public function exportExcel(Request $request)
    {
        $type = $request->get('type', 'harian');
        $tanggal = $request->get('tanggal', date('Y-m-d'));
        $date = Carbon::parse($tanggal);

        if (in_array($type, ['bulanan', 'per_user', 'per_divisi'])) {
            $startDate = $date->copy()->startOfMonth();
            $endDate = $date->copy()->endOfMonth();
            $filename = "Laporan_Pendapatan_Bersih_" . ucfirst($type) . "_{$date->format('Y_m')}.xlsx";
        } else {
            $startDate = $date->copy()->startOfDay();
            $endDate = $date->copy()->endOfDay();
            $filename = "Laporan_Pendapatan_Bersih_Harian_{$date->format('Y-m-d')}.xlsx";
        }

        // Query export (Menggunakan PendapatanBersih)
        if ($type === 'per_user') {
            $data = Transaksi::whereBetween('Tanggal', [$startDate, $endDate])
                ->select('UserCreate', DB::raw('COUNT(*) as jumlah_transaksi'), DB::raw('SUM(PendapatanBersih) as total_pendapatan'))
                ->groupBy('UserCreate')->orderBy('total_pendapatan', 'desc')->get();
            $expeditionNames = [];
        } elseif ($type === 'per_divisi') {
            $data = Transaksi::select('divisis.Nama as Ekspedisi', DB::raw('COUNT(transaksis.id) as jumlah_transaksi'), DB::raw('SUM(transaksis.PendapatanBersih) as total_pendapatan'))
                ->leftJoin('users', 'transaksis.UserCreate', '=', 'users.name')
                ->leftJoin('divisis', 'users.divisi', '=', 'divisis.id')
                ->whereBetween('transaksis.Tanggal', [$startDate, $endDate])
                ->groupBy('divisis.Nama')->orderBy('total_pendapatan', 'desc')->get();
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
