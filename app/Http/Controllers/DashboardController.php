<?php

namespace App\Http\Controllers;

use App\Models\Transaksi;
use App\Models\Reimbursement;
use App\Models\Absensi;
use App\Models\User;
use App\Models\Ekspedisi;
use App\Models\PendaftaranTenant;
use App\Models\TagihanPembayaran;
use App\Models\Tenant;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $selectedMonth = $request->query('bulan') ? intval($request->query('bulan')) : Carbon::now()->month;
        $selectedYear = $request->query('tahun') ? intval($request->query('tahun')) : Carbon::now()->year;
        $currentMonth = Carbon::create($selectedYear, $selectedMonth, 1);

        // 1. Total Pendapatan & Transaksi Bulan Ini (Menggunakan PendapatanBersih)
        $totalPendapatan = Transaksi::whereMonth('Tanggal', $currentMonth->month)
            ->whereYear('Tanggal', $currentMonth->year)
            ->sum('PendapatanBersih');

        $totalTransaksi = Transaksi::whereMonth('Tanggal', $currentMonth->month)
            ->whereYear('Tanggal', $currentMonth->year)
            ->count();

        $avgTransaksiPerHari = $totalTransaksi > 0 ? round($totalTransaksi / $currentMonth->daysInMonth, 1) : 0;
        $reimbursementPending = Reimbursement::where('Status', 'Menunggu')->count();

        // 5. Kehadiran Hari Ini
        $today = Carbon::today();
        $kehadiranHariIni = Absensi::whereDate('Tanggal', $today)->where('Status', 'H')->count();
        $totalKaryawan = User::where('role', '!=', 'Admin')->count();
        $persentaseHadir = $totalKaryawan > 0 ? round(($kehadiranHariIni / $totalKaryawan) * 100, 1) : 0;

        $expeditionNames = Ekspedisi::pluck('NamaEkspedisi', 'id')->toArray();

        // 6. Pendapatan per Ekspedisi per bulan (TAMPILKAN SEMUA)
        $ekspedisiPerBulanData = [];
        for ($bln = 1; $bln <= 12; $bln++) {
            $expData = Transaksi::select('Ekspedisi', DB::raw('SUM(PendapatanBersih) as total'))
                ->whereMonth('Tanggal', $bln)
                ->whereYear('Tanggal', $selectedYear)
                ->groupBy('Ekspedisi')
                ->orderBy('total', 'desc')
                ->get(); // <-- limit(5) DIHAPUS, tampilkan semua ekspedisi

            $ekspedisiPerBulanData[$bln] = [
                'labels' => $expData->pluck('Ekspedisi')->map(fn($exp) => $expeditionNames[$exp] ?? 'Ekspedisi ' . $exp)->toArray(),
                'values' => $expData->pluck('total')->toArray()
            ];
        }

        // 6b. Pendapatan per User per bulan (SEMUA User, tanpa limit)
        $userPerBulanData = [];
        for ($bln = 1; $bln <= 12; $bln++) {
            $userData = Transaksi::with('userCreate')->select('UserCreate', DB::raw('SUM(PendapatanBersih) as total'))
                ->whereMonth('Tanggal', $bln)
                ->whereYear('Tanggal', $selectedYear)
                ->whereNotNull('UserCreate') // Hindari group by null
                ->groupBy('UserCreate')
                ->orderBy('total', 'desc')
                ->get(); // <-- limit(5) DIHAPUS

            $userPerBulanData[$bln] = [
                'labels' => $userData->pluck('userCreate.name')->map(fn($n) => $n ?: 'Tidak Diketahui')->toArray(),
                'values' => $userData->pluck('total')->toArray()
            ];
        }

        // 6c. Pendapatan per Divisi per bulan (SEMUA Divisi, tanpa limit)
        $divisiPerBulanData = [];
        for ($bln = 1; $bln <= 12; $bln++) {
            $divisiData = Transaksi::with('getDivisi')->select('Divisi', DB::raw('SUM(PendapatanBersih) as total'))
                ->whereMonth('Tanggal', $bln)
                ->whereYear('Tanggal', $selectedYear)
                ->whereNotNull('Divisi') // Hindari group by null
                ->groupBy('Divisi')
                ->orderBy('total', 'desc')
                ->get(); // <-- limit(5) DIHAPUS

            $divisiPerBulanData[$bln] = [
                'labels' => $divisiData->pluck('getDivisi.Nama')->map(fn($n) => $n ?: 'Tanpa Divisi')->toArray(),
                'values' => $divisiData->pluck('total')->toArray()
            ];
        }

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
            $trendData[] = Transaksi::whereDate('Tanggal', $date)->sum('PendapatanBersih');
        }

        // 9. Statistik Kehadiran Minggu Ini
        $attendanceHadir = [];
        $attendanceIzin = [];
        for ($i = 0; $i < 5; $i++) {
            $date = Carbon::today()->startOfWeek()->addDays($i);
            $attendanceHadir[] = Absensi::whereDate('Tanggal', $date)->where('Status', 'H')->count();
            $attendanceIzin[] = Absensi::whereDate('Tanggal', $date)->whereIn('Status', ['I', 'S'])->count();
        }

        // 10 & 11. Data Terbaru
        $transaksiTerbaru = Transaksi::with('ekspedisi')->orderBy('Tanggal', 'desc')->limit(5)->get();
        $reimbursementTerbaru = Reimbursement::orderBy('created_at', 'desc')->limit(5)->get();

        // Dropdown Options
        $availableMonths = [];
        for ($m = 1; $m <= 12; $m++) {
            $availableMonths[$m] = Carbon::create()->month($m)->isoFormat('MMMM');
        }

        $minYear = Transaksi::min(DB::raw('YEAR(Tanggal)')) ?? Carbon::now()->year;
        $availableYears = range($minYear, Carbon::now()->year);

        // ✅ PERBAIKAN: Definisikan variabel ini sebelum compact
        $selectedBulan = $selectedMonth;

        return view('dashboard', compact(
            'totalPendapatan',
            'totalTransaksi',
            'avgTransaksiPerHari',
            'reimbursementPending',
            'kehadiranHariIni',
            'totalKaryawan',
            'persentaseHadir',
            'ekspedisiPerBulanData',
            'userPerBulanData',
            'divisiPerBulanData',
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
            'selectedBulan' // Sekarang variabel ini sudah terdefinisi
        ));
    }
    public function IndexTenant()
    {
        $Now = Carbon::now();

        // 1. Total Tenant
        $TotalTenant = Tenant::count();
        $TenantAktif = Tenant::where('StatusSubscription', 'Aktif')->count();
        $TenantExpired = Tenant::where('StatusSubscription', 'Expired')->count();
        $TenantNonaktif = Tenant::where('StatusSubscription', 'Nonaktif')->count();

        // 2. Pendapatan
        $TotalPendapatan = TagihanPembayaran::where('StatusPembayaran', 'Lunas')->sum('JumlahTagihan');
        $PendapatanBulanIni = TagihanPembayaran::where('StatusPembayaran', 'Lunas')
            ->whereYear('TanggalPembayaran', $Now->year)
            ->whereMonth('TanggalPembayaran', $Now->month)
            ->sum('JumlahTagihan');

        // 3. Tagihan
        $TotalTagihanBelumBayar = TagihanPembayaran::where('StatusPembayaran', 'Belum Bayar')->count();
        $NominalBelumBayar = TagihanPembayaran::where('StatusPembayaran', 'Belum Bayar')->sum('JumlahTagihan');
        $TotalTagihanTerlambat = TagihanPembayaran::where('StatusPembayaran', 'Terlambat')->count();

        // 4. Pendaftaran Pending
        $PendaftaranPending = PendaftaranTenant::where('Status', 'N/A')->count();
        $PendaftaranHariIni = PendaftaranTenant::whereDate('created_at', $Now->toDateString())->count();

        // 5. Subscription Akan Habis (7 hari)
        $SubscriptionAkanHabis = Tenant::where('StatusSubscription', 'Aktif')
            ->whereBetween('TanggalAkhirSubscription', [$Now, $Now->copy()->addDays(7)])
            ->count();

        // 6. Pertumbuhan Tenant (6 bulan terakhir)
        $PertumbuhanTenant = [];
        for ($Index = 5; $Index >= 0; $Index--) {
            $Month = $Now->copy()->subMonths($Index);
            $Count = Tenant::whereYear('created_at', $Month->year)
                ->whereMonth('created_at', $Month->month)
                ->count();
            $PertumbuhanTenant[] = [
                'Month' => $Month->format('M Y'),
                'Count' => $Count
            ];
        }

        // 7. Top 5 Tenant by Revenue
        $TopTenant = TagihanPembayaran::select('TenantId', DB::raw('SUM(JumlahTagihan) as TotalRevenue'))
            ->where('StatusPembayaran', 'Lunas')
            ->groupBy('TenantId')
            ->orderBy('TotalRevenue', 'desc')
            ->limit(5)
            ->with('Tenant')
            ->get();

        // ✅ GROUP SEMUA VARIABEL KE DALAM ARRAY $Data AGAR COCOK DENGAN VIEW
        $Data = [
            'TotalTenant' => $TotalTenant,
            'TenantAktif' => $TenantAktif,
            'TenantExpired' => $TenantExpired,
            'TenantNonaktif' => $TenantNonaktif,
            'TotalPendapatan' => $TotalPendapatan,
            'PendapatanBulanIni' => $PendapatanBulanIni,
            'TotalTagihanBelumBayar' => $TotalTagihanBelumBayar,
            'NominalBelumBayar' => $NominalBelumBayar,
            'TotalTagihanTerlambat' => $TotalTagihanTerlambat,
            'PendaftaranPending' => $PendaftaranPending,
            'PendaftaranHariIni' => $PendaftaranHariIni,
            'SubscriptionAkanHabis' => $SubscriptionAkanHabis,
            'PertumbuhanTenant' => $PertumbuhanTenant,
            'TopTenant' => $TopTenant,
        ];

        return view('dashboard-manajemen-tenant', compact('Data'));
    }
}
