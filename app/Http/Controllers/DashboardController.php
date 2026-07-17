<?php

namespace App\Http\Controllers;

use App\Models\Transaksi;
use App\Models\Reimbursement;
use App\Models\Absensi;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $currentMonth = Carbon::now();

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

        // 6. Pendapatan per Ekspedisi
        $ekspedisiData = Transaksi::select('Ekspedisi', DB::raw('SUM(Pendapatan) as total'))
            ->groupBy('Ekspedisi')
            ->orderBy('total', 'desc')
            ->limit(5)
            ->get();

        $ekspedisiLabels = $ekspedisiData->pluck('Ekspedisi')->map(function ($exp) {
            $names = ['1' => 'J&T Express', '2' => 'JNE', '3' => 'SiCepat', '4' => 'AnterAja', '5' => 'Ninja Express'];
            return $names[$exp] ?? 'Ekspedisi ' . $exp;
        })->toArray();

        $ekspedisiValues = $ekspedisiData->pluck('total')->toArray();

        // 7. Status Reimbursement
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

        // 9. Statistik Kehadiran Minggu Ini
        $attendanceHadir = [];
        $attendanceIzin = [];
        for ($i = 4; $i >= 0; $i--) {
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
            'reimbursementStatus',
            'trendLabels',
            'trendData',
            'attendanceHadir',
            'attendanceIzin',
            'transaksiTerbaru',
            'reimbursementTerbaru'
        ));
    }
}
