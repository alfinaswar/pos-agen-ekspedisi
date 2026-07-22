<?php

namespace App\Http\Controllers;

use App\Models\Transaksi;
use App\Models\Reimbursement;
use App\Models\Absensi;
use App\Models\User;
use App\Models\Ekspedisi;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // Ambil bulan dan tahun dari query string, jika tidak ada pakai bulan dan tahun saat ini
        $selectedMonth = $request->query('bulan') ? intval($request->query('bulan')) : Carbon::now()->month;
        $selectedYear = $request->query('tahun') ? intval($request->query('tahun')) : Carbon::now()->year;
        $currentMonth = Carbon::create($selectedYear, $selectedMonth, 1);

        // 1. Total Pendapatan Bulan Ini
        $totalPendapatan = Transaksi::whereMonth('Tanggal', $currentMonth->month)
            ->whereYear('Tanggal', $currentMonth->year)
            ->sum('Pendapatan');

        // 2. Total Transaksi Bulan Ini
        $totalTransaksi = Transaksi::whereMonth('Tanggal', $currentMonth->month)
            ->whereYear('Tanggal', $currentMonth->year)
            ->count();

        // 3. Rata-rata transaksi per hari
        $avgTransaksiPerHari = $totalTransaksi > 0
            ? round($totalTransaksi / $currentMonth->daysInMonth, 1)
            : 0;

        // 4. Reimbursement Pending
        $reimbursementPending = Reimbursement::where('Status', 'Menunggu')->count();

        // 5. Kehadiran Hari Ini
        $today = Carbon::today();
        $kehadiranHariIni = Absensi::whereDate('Tanggal', $today)
            ->where('Status', 'H')
            ->count();

        $totalKaryawan = User::where('role', '!=', 'Admin')->count(); // Asumsi admin bukan karyawan
        $persentaseHadir = $totalKaryawan > 0
            ? round(($kehadiranHariIni / $totalKaryawan) * 100, 1)
            : 0;

        // 6. Pendapatan per Ekspedisi per bulan (untuk ChartJS dropdown & chart dinamis)
        // Ambil data seluruh bulan dalam tahun terpilih
        $ekspedisiPerBulanData = [];
        $expeditionNames = Ekspedisi::pluck('NamaEkspedisi', 'id')->toArray();

        for ($bln = 1; $bln <= 12; $bln++) {
            $expData = Transaksi::select('Ekspedisi', DB::raw('SUM(Pendapatan) as total'))
                ->whereMonth('Tanggal', $bln)
                ->whereYear('Tanggal', $selectedYear)
                ->groupBy('Ekspedisi')
                ->orderBy('total', 'desc')
                ->limit(5)
                ->get();

            $labels = $expData->pluck('Ekspedisi')->map(function ($exp) use ($expeditionNames) {
                return $expeditionNames[$exp] ?? 'Ekspedisi ' . $exp;
            })->toArray();

            $values = $expData->pluck('total')->toArray();

            $ekspedisiPerBulanData[$bln] = [
                'labels' => $labels,
                'values' => $values
            ];
        }

        // Ambil ekspedisi label dan value untuk bulan terpilih saja (untuk hardcoded fallback di statistik summary)
        $ekspedisiLabels = $ekspedisiPerBulanData[$selectedMonth]['labels'] ?? [];
        $ekspedisiValues = $ekspedisiPerBulanData[$selectedMonth]['values'] ?? [];

        // 7. Status Reimbursement (Menunggu, Dibayar, Ditolak)
        $reimbursementStatus = [
            Reimbursement::where('Status', 'Menunggu')->count(),
            Reimbursement::where('Status', 'Dibayar')->count(),
            Reimbursement::where('Status', 'Ditolak')->count(),
        ];

        // 8. Tren Pendapatan 7 Hari Terakhir
        $trendLabels = [];
        $trendData = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $trendLabels[] = $date->isoFormat('ddd');
            $income = Transaksi::whereDate('Tanggal', $date)->sum('Pendapatan');
            $trendData[] = $income;
        }

        // 9. Statistik Kehadiran Minggu Ini (Senin - Jumat)
        $attendanceHadir = [];
        $attendanceIzin = [];
        // Loop Senin (0) sampai Jumat (4)
        for ($i = 0; $i < 5; $i++) {
            $date = Carbon::today()->startOfWeek()->addDays($i);
            $hadir = Absensi::whereDate('Tanggal', $date)->where('Status', 'H')->count();
            $izin = Absensi::whereDate('Tanggal', $date)->whereIn('Status', ['I', 'S'])->count();
            $attendanceHadir[] = $hadir;
            $attendanceIzin[] = $izin;
        }

        // 10. Transaksi Terbaru
        $transaksiTerbaru = Transaksi::with('ekspedisi')
            ->orderBy('Tanggal', 'desc')
            ->limit(5)
            ->get();

        // 11. Reimbursement Terbaru
        $reimbursementTerbaru = Reimbursement::orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Available bulan & tahun untuk dropdown
        $availableMonths = [];
        for ($m = 1; $m <= 12; $m++) {
            $availableMonths[$m] = Carbon::create()->month($m)->isoFormat('MMMM');
        }

        // Tahun minimum dari transaksi, dari transaksi paling awal
        $minYear = Transaksi::min(DB::raw('YEAR(Tanggal)')) ?? Carbon::now()->year;
        $maxYear = Carbon::now()->year;
        $availableYears = range($minYear, $maxYear);

        // Diperlukan di js (lihat dashboard.blade.php view)
        $selectedBulan = $selectedMonth;

        return view('dashboard', compact(
            'totalPendapatan',
            'totalTransaksi',
            'avgTransaksiPerHari',
            'reimbursementPending',
            'kehadiranHariIni',
            'totalKaryawan',
            'persentaseHadir',
            'ekspedisiLabels',
            'ekspedisiValues',
            'ekspedisiPerBulanData',
            'reimbursementStatus',
            'trendLabels',
            'trendData',
            'attendanceHadir',
            'attendanceIzin',
            'transaksiTerbaru',
            'reimbursementTerbaru',
            'selectedMonth',
            'selectedYear',
            'availableMonths',
            'availableYears',
            'selectedBulan'
        ));
    }
}
