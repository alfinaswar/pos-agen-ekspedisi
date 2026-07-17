<?php

namespace App\Http\Controllers;

use App\Exports\LaporanPendapatanExport;
use App\Models\Transaksi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;

class LaporanController extends Controller
{
    public function index(Request $request)
    {
        $type = $request->get('type', 'harian'); // harian, bulanan, per_ekspedisi
        $tanggal = $request->get('tanggal', date('Y-m-d'));

        // Parse tanggal
        $date = Carbon::parse($tanggal);

        // Query berdasarkan type
        if ($type === 'bulanan') {
            $startDate = $date->copy()->startOfMonth();
            $endDate = $date->copy()->endOfMonth();
        } else {
            $startDate = $date->copy()->startOfDay();
            $endDate = $date->copy()->endOfDay();
        }

        // Query data per ekspedisi
        $data = Transaksi::whereBetween('Tanggal', [$startDate, $endDate])
            ->select(
                'Ekspedisi',
                DB::raw('COUNT(*) as jumlah_transaksi'),
                DB::raw('SUM(Pendapatan) as total_pendapatan')
            )
            ->groupBy('Ekspedisi')
            ->orderBy('total_pendapatan', 'desc')
            ->get();

        // Hitung total
        $totalTransaksi = $data->sum('jumlah_transaksi');
        $totalPendapatan = $data->sum('total_pendapatan');

        // Tambah persentase
        $dataWithPercentage = $data->map(function ($item) use ($totalPendapatan) {
            $item->persentase = $totalPendapatan > 0
                ? round(($item->total_pendapatan / $totalPendapatan) * 100, 1)
                : 0;
            return $item;
        });

        // Data untuk chart
        $chartLabels = $dataWithPercentage->pluck('Ekspedisi')->toArray();
        $chartData = $dataWithPercentage->pluck('total_pendapatan')->toArray();

        // Mapping nama ekspedisi (jika perlu)
        $expeditionNames = [
            '1' => 'J&T Express',
            '2' => 'JNE',
            '3' => 'SiCepat',
            '4' => 'AnterAja',
            '5' => 'Ninja Express',
        ];

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

        if ($type === 'bulanan') {
            $startDate = $date->copy()->startOfMonth();
            $endDate = $date->copy()->endOfMonth();
            $filename = "Laporan_Pendapatan_Bulanan_{$date->format('Y_m')}.xlsx";
        } else {
            $startDate = $date->copy()->startOfDay();
            $endDate = $date->copy()->endOfDay();
            $filename = "Laporan_Pendapatan_Harian_{$date->format('Y-m-d')}.xlsx";
        }

        $data = Transaksi::whereBetween('Tanggal', [$startDate, $endDate])
            ->select(
                'Ekspedisi',
                DB::raw('COUNT(*) as jumlah_transaksi'),
                DB::raw('SUM(Pendapatan) as total_pendapatan')
            )
            ->groupBy('Ekspedisi')
            ->orderBy('total_pendapatan', 'desc')
            ->get();

        $totalTransaksi = $data->sum('jumlah_transaksi');
        $totalPendapatan = $data->sum('total_pendapatan');

        $expeditionNames = [
            '1' => 'J&T Express',
            '2' => 'JNE',
            '3' => 'SiCepat',
            '4' => 'AnterAja',
            '5' => 'Ninja Express',
        ];

        // Panggil Export Class yang baru (tanpa view)
        return Excel::download(
            new LaporanPendapatanExport(
                $data,
                $totalTransaksi,
                $totalPendapatan,
                $type,
                $tanggal,
                $expeditionNames
            ),
            $filename
        );
    }
}
