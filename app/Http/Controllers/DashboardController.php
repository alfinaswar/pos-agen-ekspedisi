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

        // Ambil kode tenant dari request, jika ada
        $kodeTenant = $request->query('kode_tenant');

        // 1. Total Pendapatan & Transaksi Bulan Ini (Menggunakan PendapatanBersih)
        $transaksiQuery = Transaksi::whereMonth('Tanggal', $currentMonth->month)
            ->whereYear('Tanggal', $currentMonth->year);

        if ($kodeTenant) {
            $transaksiQuery->where('KodeTenant', $kodeTenant);
        }
        $totalPendapatan = (clone $transaksiQuery)->sum('PendapatanBersih');
        $totalTransaksi = (clone $transaksiQuery)->count();

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
            $expDataQuery = Transaksi::select('Ekspedisi', DB::raw('SUM(PendapatanBersih) as total'))
                ->whereMonth('Tanggal', $bln)
                ->whereYear('Tanggal', $selectedYear);

            if ($kodeTenant) {
                $expDataQuery->where('KodeTenant', $kodeTenant);
            }

            $expData = $expDataQuery
                ->groupBy('Ekspedisi')
                ->orderBy('total', 'desc')
                ->get();

            $ekspedisiPerBulanData[$bln] = [
                'labels' => $expData->pluck('Ekspedisi')->map(fn($exp) => $expeditionNames[$exp] ?? 'Ekspedisi ' . $exp)->toArray(),
                'values' => $expData->pluck('total')->toArray()
            ];
        }

        // 6b. Pendapatan per User per bulan (SEMUA User, tanpa limit)
        $userPerBulanData = [];
        for ($bln = 1; $bln <= 12; $bln++) {
            $userDataQuery = Transaksi::with('userCreate')->select('UserCreate', DB::raw('SUM(PendapatanBersih) as total'))
                ->whereMonth('Tanggal', $bln)
                ->whereYear('Tanggal', $selectedYear)
                ->whereNotNull('UserCreate');
            if ($kodeTenant) {
                $userDataQuery->where('KodeTenant', $kodeTenant);
            }
            $userData = $userDataQuery
                ->groupBy('UserCreate')
                ->orderBy('total', 'desc')
                ->get();

            $userPerBulanData[$bln] = [
                'labels' => $userData->pluck('userCreate.name')->map(fn($n) => $n ?: 'Tidak Diketahui')->toArray(),
                'values' => $userData->pluck('total')->toArray()
            ];
        }

        // 6c. Pendapatan per Divisi per bulan (SEMUA Divisi, tanpa limit)
        $divisiPerBulanData = [];
        for ($bln = 1; $bln <= 12; $bln++) {
            $divisiDataQuery = Transaksi::with('getDivisi')->select('Divisi', DB::raw('SUM(PendapatanBersih) as total'))
                ->whereMonth('Tanggal', $bln)
                ->whereYear('Tanggal', $selectedYear)
                ->whereNotNull('Divisi');
            if ($kodeTenant) {
                $divisiDataQuery->where('KodeTenant', $kodeTenant);
            }
            $divisiData = $divisiDataQuery
                ->groupBy('Divisi')
                ->orderBy('total', 'desc')
                ->get();

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
            $trendPendapatanQuery = Transaksi::whereDate('Tanggal', $date);
            if ($kodeTenant) {
                $trendPendapatanQuery->where('KodeTenant', $kodeTenant);
            }
            $trendLabels[] = $date->isoFormat('ddd');
            $trendData[] = $trendPendapatanQuery->sum('PendapatanBersih');
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
        $transaksiTerbaruQuery = Transaksi::with('ekspedisi')->orderBy('Tanggal', 'desc')->limit(5);
        if ($kodeTenant) {
            $transaksiTerbaruQuery->where('KodeTenant', $kodeTenant);
        }
        $transaksiTerbaru = $transaksiTerbaruQuery->get();

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
            'selectedBulan', // Sekarang variabel ini sudah terdefinisi
            'kodeTenant' // kirim ke view jika perlu
        ));
    }
    public function IndexTenant(Request $request)
    {
        $Now = Carbon::now();

        // Ambil kode tenant dari request, jika ada
        $kodeTenant = $request->query('kode_tenant');

        // 1. Total Tenant
        $tenantQuery = Tenant::query();
        if ($kodeTenant) {
            $tenantQuery->where('KodeTenant', $kodeTenant);
        }
        $TotalTenant = $tenantQuery->count();

        $TenantAktif = Tenant::when($kodeTenant, function($query) use ($kodeTenant) {
                return $query->where('KodeTenant', $kodeTenant);
            })
            ->where('StatusSubscription', 'Aktif')->count();
        $TenantExpired = Tenant::when($kodeTenant, function($query) use ($kodeTenant) {
                return $query->where('KodeTenant', $kodeTenant);
            })
            ->where('StatusSubscription', 'Expired')->count();
        $TenantNonaktif = Tenant::when($kodeTenant, function($query) use ($kodeTenant) {
                return $query->where('KodeTenant', $kodeTenant);
            })
            ->where('StatusSubscription', 'Nonaktif')->count();

        // 2. Pendapatan
        $pendapatanQuery = TagihanPembayaran::where('StatusPembayaran', 'Lunas');
        if ($kodeTenant) {
            $pendapatanQuery->whereHas('Tenant', function($q) use ($kodeTenant) {
                $q->where('KodeTenant', $kodeTenant);
            });
        }
        $TotalPendapatan = $pendapatanQuery->sum('JumlahTagihan');

        $PendapatanBulanIniQuery = TagihanPembayaran::where('StatusPembayaran', 'Lunas')
            ->whereYear('TanggalPembayaran', $Now->year)
            ->whereMonth('TanggalPembayaran', $Now->month);
        if ($kodeTenant) {
            $PendapatanBulanIniQuery->whereHas('Tenant', function($q) use ($kodeTenant) {
                $q->where('KodeTenant', $kodeTenant);
            });
        }
        $PendapatanBulanIni = $PendapatanBulanIniQuery->sum('JumlahTagihan');

        // 3. Tagihan
        $TagihanBelumBayarQuery = TagihanPembayaran::where('StatusPembayaran', 'Belum Bayar');
        $TagihanTerlambatQuery = TagihanPembayaran::where('StatusPembayaran', 'Terlambat');
        $TagihanNominalBelumBayarQuery = TagihanPembayaran::where('StatusPembayaran', 'Belum Bayar');

        if ($kodeTenant) {
            $TagihanBelumBayarQuery->whereHas('Tenant', function($q) use ($kodeTenant) {
                $q->where('KodeTenant', $kodeTenant);
            });
            $TagihanTerlambatQuery->whereHas('Tenant', function($q) use ($kodeTenant) {
                $q->where('KodeTenant', $kodeTenant);
            });
            $TagihanNominalBelumBayarQuery->whereHas('Tenant', function($q) use ($kodeTenant) {
                $q->where('KodeTenant', $kodeTenant);
            });
        }

        $TotalTagihanBelumBayar = $TagihanBelumBayarQuery->count();
        $NominalBelumBayar = $TagihanNominalBelumBayarQuery->sum('JumlahTagihan');
        $TotalTagihanTerlambat = $TagihanTerlambatQuery->count();

        // 4. Pendaftaran Pending
        $PendaftaranPendingQuery = PendaftaranTenant::where('Status', 'N/A');
        $PendaftaranHariIniQuery = PendaftaranTenant::whereDate('created_at', $Now->toDateString());

        if ($kodeTenant) {
            $PendaftaranPendingQuery->whereHas('Tenant', function($q) use ($kodeTenant) {
                $q->where('KodeTenant', $kodeTenant);
            });
            $PendaftaranHariIniQuery->whereHas('Tenant', function($q) use ($kodeTenant) {
                $q->where('KodeTenant', $kodeTenant);
            });
        }

        $PendaftaranPending = $PendaftaranPendingQuery->count();
        $PendaftaranHariIni = $PendaftaranHariIniQuery->count();

        // 5. Subscription Akan Habis (7 hari)
        $SubscriptionAkanHabisQuery = Tenant::where('StatusSubscription', 'Aktif')
            ->whereBetween('TanggalAkhirSubscription', [$Now, $Now->copy()->addDays(7)]);
        if ($kodeTenant) {
            $SubscriptionAkanHabisQuery->where('KodeTenant', $kodeTenant);
        }
        $SubscriptionAkanHabis = $SubscriptionAkanHabisQuery->count();

        // 6. Pertumbuhan Tenant (6 bulan terakhir)
        $PertumbuhanTenant = [];
        for ($Index = 5; $Index >= 0; $Index--) {
            $Month = $Now->copy()->subMonths($Index);
            $PertumbuhanTenantQuery = Tenant::whereYear('created_at', $Month->year)
                ->whereMonth('created_at', $Month->month);
            if ($kodeTenant) {
                $PertumbuhanTenantQuery->where('KodeTenant', $kodeTenant);
            }
            $Count = $PertumbuhanTenantQuery->count();
            $PertumbuhanTenant[] = [
                'Month' => $Month->format('M Y'),
                'Count' => $Count
            ];
        }

        // 7. Top 5 Tenant by Revenue
        $TopTenantQuery = TagihanPembayaran::select('TenantId', DB::raw('SUM(JumlahTagihan) as TotalRevenue'))
            ->where('StatusPembayaran', 'Lunas')
            ->groupBy('TenantId')
            ->orderBy('TotalRevenue', 'desc')
            ->limit(5)
            ->with('Tenant');
        if ($kodeTenant) {
            $TopTenantQuery->whereHas('Tenant', function($q) use ($kodeTenant) {
                $q->where('KodeTenant', $kodeTenant);
            });
        }
        $TopTenant = $TopTenantQuery->get();

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
            'kodeTenant' => $kodeTenant
        ];

        return view('dashboard-manajemen-tenant', compact('Data'));
    }
}
