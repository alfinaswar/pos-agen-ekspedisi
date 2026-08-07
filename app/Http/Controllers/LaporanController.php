<?php

namespace App\Http\Controllers;

use App\Exports\LaporanPendapatanExport;
use App\Exports\LaporanPerDivisiExport;
use App\Exports\LaporanPerUserExport;
use App\Models\Divisi;
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
            // Ambil langsung string dari kolom UserCreate
            $data = Transaksi::whereBetween('Tanggal', [$startDate, $endDate])
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
            // Ambil langsung string dari kolom Divisi
            $data = Transaksi::whereBetween('Tanggal', [$startDate, $endDate])
                ->select(
                    'Divisi',
                    DB::raw('COUNT(*) as jumlah_transaksi'),
                    DB::raw('SUM(PendapatanBersih) as total_pendapatan')
                )
                ->groupBy('Divisi')
                ->orderBy('total_pendapatan', 'desc')
                ->get();

            // Join ke tabel divisi untuk mendapatkan nama divisi (misal field 'nama_divisi')
            $divisiNames = Divisi::pluck('Nama', 'id')->toArray();
            $chartLabels = $data->pluck('Divisi')->map(function($divisiId) use ($divisiNames) {
                return $divisiNames[$divisiId] ?? 'Tanpa Divisi';
            })->toArray();


            // ✅ TAMBAHAN: Ambil breakdown ekspedisi untuk setiap divisi (untuk grafik drill-down)
            $expeditionNames = Ekspedisi::pluck('NamaEkspedisi', 'id')->toArray();

            foreach ($data as $row) {
                // Query khusus untuk mendapatkan jumlah transaksi per ekspedisi di divisi ini
                $ekspedisiData = Transaksi::whereBetween('Tanggal', [$startDate, $endDate])
                    ->where('Divisi', $row->Divisi)
                    ->select('Ekspedisi', DB::raw('COUNT(*) as jumlah'))
                    ->groupBy('Ekspedisi')
                    ->get();

                $breakdown = [];
                foreach ($ekspedisiData as $exp) {
                    $expName = $expeditionNames[$exp->Ekspedisi] ?? 'Ekspedisi ' . $exp->Ekspedisi;
                    $breakdown[] = [
                        'name' => $expName,
                        'jumlah' => $exp->jumlah
                    ];
                }
                // Simpan ke dalam object row agar bisa diakses di view sebagai JSON
                $row->ekspedisi_breakdown = $breakdown;
            }
        }else {
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

        // 4. Siapkan Data Grafik
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

        // 1. Tentukan range & nama file
        if ($type === 'harian') {
            $startDate = $date->copy()->startOfDay();
            $endDate = $date->copy()->endOfDay();
            $filename = "Laporan_Pendapatan_Harian_{$date->format('Y-m-d')}.xlsx";
        } else {
            $startDate = $date->copy()->startOfMonth();
            $endDate = $date->copy()->endOfMonth();
            $typeLabel = ucfirst(str_replace('_', ' ', $type)); // e.g., "Per User", "Per Divisi", "Bulanan"
            $filename = "Laporan_Pendapatan_{$typeLabel}_{$date->format('Y_m')}.xlsx";
        }

        // 2. Panggil Export Class yang SPESIFIK berdasarkan tipe
        if ($type === 'per_user') {
            $data = Transaksi::whereBetween('Tanggal', [$startDate, $endDate])
                ->select('UserCreate', DB::raw('COUNT(*) as jumlah_transaksi'), DB::raw('SUM(PendapatanBersih) as total_pendapatan'))
                ->groupBy('UserCreate')->orderBy('total_pendapatan', 'desc')->get();

            return Excel::download(
                new LaporanPerUserExport($data, $data->sum('jumlah_transaksi'), $data->sum('total_pendapatan'), $tanggal),
                $filename
            );

        } elseif ($type === 'per_divisi') {
            $data = Transaksi::whereBetween('Tanggal', [$startDate, $endDate])
                ->select('Divisi', DB::raw('COUNT(*) as jumlah_transaksi'), DB::raw('SUM(PendapatanBersih) as total_pendapatan'))
                ->groupBy('Divisi')->orderBy('total_pendapatan', 'desc')->get();

            return Excel::download(
                new LaporanPerDivisiExport($data, $data->sum('jumlah_transaksi'), $data->sum('total_pendapatan'), $tanggal),
                $filename
            );

        } else {
            // Harian & Bulanan (Ekspedisi)
            $data = Transaksi::whereBetween('Tanggal', [$startDate, $endDate])
                ->select('Ekspedisi', DB::raw('COUNT(*) as jumlah_transaksi'), DB::raw('SUM(PendapatanBersih) as total_pendapatan'))
                ->groupBy('Ekspedisi')->orderBy('total_pendapatan', 'desc')->get();

            $expeditionNames = Ekspedisi::pluck('NamaEkspedisi', 'id')->toArray();

            return Excel::download(
                new LaporanPendapatanExport($data, $data->sum('jumlah_transaksi'), $data->sum('total_pendapatan'), $type, $tanggal, $expeditionNames),
                $filename
            );
        }
    }
}
