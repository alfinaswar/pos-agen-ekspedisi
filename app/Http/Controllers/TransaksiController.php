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
            // 1. Buat base query (TAMBAHKAN 'Catatan' di sini)
            $query = Transaksi::with(['ekspedisi', 'userCreate'])
                ->select([
                    'id',
                    'KodeTransaksi',
                    'Tanggal',
                    'Ekspedisi',
                    'NoResi',
                    'Metode',
                    'NamaPengirim',
                    'Diskon',
                    'PendapatanBersih',
                    'Pendapatan',
                    'UserCreate',
                    'UserFinance',
                    'DicekPada',
                    'Status',
                    'BuktiBayar',
                    'Catatan',
                    'TanggalJatuhTempo' // <-- TAMBAHKAN 'Catatan'
                ])
                ->orderBy('id', 'desc');

            // Kalau bukan admin/leader/finance, hanya tampilkan data milik user itu sendiri
            if (!auth()->user() || !in_array(auth()->user()->role, ['Admin', 'Leader', 'Finance'])) {
                $query->where('UserCreate', auth()->id());
            }

            // 2. Terapkan filter
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

            // ✅ FILTER BARU: Status Verifikasi (Tersedia untuk semua role)
            if ($request->filled('status_verifikasi')) {
                $query->where('Status', $request->input('status_verifikasi'));
            }

            // Filter user (hanya Admin/Leader)
            if ($request->filled('user') && auth()->user() && in_array(auth()->user()->role, ['Admin', 'Leader'])) {
                $query->where('UserCreate', $request->input('user'));
            }

            // 3. Hitung total
            $totalPendapatan = (clone $query)->sum('Pendapatan') ?? 0;
            $totalDiskon = (clone $query)->sum('Diskon') ?? 0;
            $totalPendapatanBersih = (clone $query)->sum('PendapatanBersih') ?? 0;

            // 4. Return DataTables
            return DataTables::of($query)
                ->addIndexColumn()
                ->editColumn('Ekspedisi', function ($row) {
                    return $row->ekspedisi && $row->ekspedisi->NamaEkspedisi
                        ? $row->ekspedisi->NamaEkspedisi
                        : '<span class="text-muted">-</span>';
                })
                ->addColumn('action', function ($row) {
                    // ... (LOGIKA ACTION BUTTON TETAP SAMA SEPERTI SEBELUMNYA) ...
                    $user = auth()->user();
                    $role = $user ? $user->role : null;
                    $status = $row->Status ?? 'N/A';
                    $canShow = false;
                    $canEdit = false;
                    $canDelete = false;

                    if ($role === 'Admin') {
                        $canShow = true;
                        $canEdit = true;
                        $canDelete = true;
                    } elseif ($role === 'Finance') {
                        $canShow = true;
                    } elseif (in_array($role, ['Kasir', 'Leader'])) {
                        $canShow = false;
                        if ($status === 'N' || $status === 'N/A') {
                            $canEdit = true;
                            $canDelete = true;
                        }
                    }

                    $btn = '<div class="d-flex gap-1 justify-content-center">';
                    if ($canShow)
                        $btn .= '<a href="' . route('transaksi.show', $row->id) . '" class="btn btn-info btn-sm text-white" title="Lihat Detail"><i class="ti ti-eye"></i></a> ';
                    if ($canEdit)
                        $btn .= '<a href="' . route('transaksi.edit', $row->id) . '" class="btn btn-warning btn-sm text-white" title="Edit"><i class="ti ti-edit"></i></a> ';
                    if ($canDelete) {
                        $identifier = $row->KodeTransaksi ?? 'Transaksi ini';
                        $btn .= '<button type="button" class="btn btn-danger btn-sm btn-delete" data-id="' . $row->id . '" data-kode="' . htmlspecialchars($identifier) . '" title="Hapus"><i class="ti ti-trash"></i></button>';
                    }
                    $btn .= '</div>';
                    return $btn;
                })
                ->editColumn('UserCreate', function ($row) {
                    return $row->userCreate && $row->userCreate->name
                        ? \Illuminate\Support\Str::limit($row->userCreate->name, 15)
                        : '<span class="text-muted">-</span>';
                })

                // ✅ MODIFIKASI KOLOM StatusInfo: Tambahkan ikon pesan jika ada Catatan
                ->addColumn('StatusInfo', function ($row) {
                    $badgeClass = 'bg-light text-dark';
                    $label = 'Belum Verif';

                    if ($row->Status === 'Y') {
                        $badgeClass = 'bg-success';
                        $label = 'Valid';
                    } elseif ($row->Status === 'N') {
                        $badgeClass = 'bg-danger';
                        $label = 'Tidak Valid';
                    }

                    $html = '<span class="badge ' . $badgeClass . '">' . $label . '</span>';

                    // Jika ada catatan, tambahkan ikon kecil yang bisa diklik
                    // hanya muncul kalau user create nya sesuai dengan user login id
                    if (!empty($row->Catatan) && isset($row->UserCreate) && auth()->check() && $row->UserCreate == auth()->id()) {
                        $catatanEscaped = htmlspecialchars($row->Catatan, ENT_QUOTES, 'UTF-8');
                        $html .= ' <button type="button" class="btn btn-sm btn-outline-secondary p-0 px-1 ms-1 btn-view-catatan"
                                    data-catatan="' . $catatanEscaped . '" title="Lihat Catatan Finance" style="vertical-align: middle;">
                                    <i class="ti ti-message" style="font-size: 0.9rem;"></i>
                                  </button>';
                    }


                    $dicekPada = $row->DicekPada
                        ? '<br><small class="text-muted"><i class="ti ti-clock"></i> ' . \Carbon\Carbon::parse($row->DicekPada)->format('d-m-Y H:i') . '</small>'
                        : '';

                    return $html . $dicekPada;
                })
                ->addColumn('Bayar', function ($row) {
                    $kodeBayar = $row->NamaPengirim ? htmlspecialchars($row->NamaPengirim) : '<span class="text-muted">-</span>';
                    if ($row->BuktiBayar) {
                        $url = asset('storage/' . $row->BuktiBayar);
                        $buktiLink = '<a href="' . $url . '" target="_blank" class="btn btn-sm btn-outline-info ms-1" title="Lihat / Unduh Bukti Bayar"><i class="ti ti-download"></i></a>';
                    } else {
                        $buktiLink = '';
                    }
                    return $kodeBayar . ' ' . $buktiLink;
                })
                // ✅ TAMBAHKAN KOLOM "Tagihan" untuk menampilkan status tagihan jika metode pembayaran adalah "Tagihan"
                ->addColumn('Tagihan', function ($row) {
                    if (isset($row->Metode) && $row->Metode === 'Tagihan') {
                        $jatuhTempo = $row->TanggalJatuhTempo
                            ? \Carbon\Carbon::parse($row->TanggalJatuhTempo)->format('d-m-Y')
                            : '<span class="text-muted">-</span>';

                        return '<div>
                            <div><i class="ti ti-calendar-event"></i> Jatuh Tempo: ' . $jatuhTempo . '</div>
                        </div>';
                    } else {
                        return '<span class="text-muted">-</span>';
                    }
                })


                ->with([
                    'total_pendapatan' => number_format($totalPendapatan, 0, ',', '.'),
                    'total_diskon' => number_format($totalDiskon, 0, ',', '.'),
                    'total_pendapatan_bersih' => number_format($totalPendapatanBersih, 0, ',', '.'),
                ])
                ->rawColumns(['action', 'Ekspedisi', 'Bayar', 'StatusInfo'])
                ->make(true);
        }

        $ekspedisi = Ekspedisi::get();
        if (auth()->user() && !in_array(auth()->user()->role, ['Admin', 'Leader'])) {
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
            'Ekspedisi'     => 'required|string|max:255',
            'NoResi'        => 'required|string|max:255',
            'Metode'        => 'required|in:Tunai,Non-Tunai,COD,Tagihan,Qris,Transfer,',
            'Pendapatan'    => 'required|numeric|min:0',
            // 'KodeBayar'     => 'required_if:Metode,Non-Tunai|nullable|string|max:255',
            'NamaPengirim' => 'required|string|max:255',
            'BuktiBayar'    => 'required_if:Metode,Non-Tunai|file|mimes:jpg,jpeg,png,pdf|max:2348', // Wajib kalau Non-Tunai, maks 2MB
            'Keterangan'    => 'nullable|string',
            'TanggalJatuhTempo' => 'required_if:Metode,Tagihan|nullable|date',

        ]);

// dd($request->all());
        $data = $request->except(['BuktiBayar']);
        $data['Divisi'] = auth()->user()->divisi ?? '-';

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
        $transaksi->load('ekspedisi');
        return view('transaksi.show', compact('transaksi'));
    }
    public function approve(Request $request, Absensi $absensi)
    {
        // Hanya Admin dan Leader yang boleh melakukan ini
        if (!in_array(auth()->user()->role, ['Admin', 'Leader'])) {
            abort(403, 'Aksi tidak diizinkan.');
        }

        $request->validate([
            'Status' => 'required|in:Y,N,N/A',
            'Catatan' => 'nullable|string|max:1000',
        ]);

        $absensi->update([
            'StatusVerif' => $request->Status,
            'Catatan' => $request->Catatan,
            'UserLeader' => auth()->user()->name,
            'DisetujuiPada' => now(),
        ]);

        return redirect()->route('absensi.show', $absensi->id)
            ->with('success', 'Status persetujuan absensi berhasil diperbarui.');
    }
    public function update(Request $request, Transaksi $transaksi)
    {
        // 1. Validasi (abaikan unique untuk ID saat ini)
        $request->validate([
            'KodeTransaksi' => 'nullable|string|unique:transaksis,KodeTransaksi,' . $transaksi->id,
            'Tanggal' => 'required|date',
            'Ekspedisi' => 'required|string|max:255',
            'NoResi' => 'required|string|max:255',
            'Metode' => 'required|in:Tunai,Non-Tunai,COD,Tagihan,Qris,Transfer',
            'Pendapatan' => 'required|numeric|min:0',
            // 'KodeBayar' => 'required_if:Metode,Non-Tunai|nullable|string|max:255',
            'NamaPengirim' => 'required|string|max:255',
            'BuktiBayar' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'Keterangan' => 'nullable|string',
            'TanggalJatuhTempo' => 'required_if:Metode,Tagihan|nullable|date',
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
    public function Export(Request $Request)
    {
        // 1. Buat Query Builder (JANGAN di ->get() dulu agar ringan)
        $Query = Transaksi::with('ekspedisi', 'userCreate')
            ->select([
                'id',
                'KodeTransaksi',
                'Tanggal',
                'Ekspedisi',
                'NoResi',
                'Metode',
                'KodeBayar',
                'UserCreate',
                'Pendapatan',
                'Diskon',
                'PendapatanBersih',
                'Keterangan',
            ])
            ->orderBy('created_at', 'desc');

        $FilterInfo = "Semua Data";
        $Params = [];

        // 2. Terapkan Filter
        if ($Request->filled('tanggal_awal')) {
            $Query->whereDate('Tanggal', '>=', $Request->tanggal_awal);
            $Params['tanggal_awal'] = $Request->tanggal_awal;
            $FilterInfo = "Periode: " . Carbon::parse($Request->tanggal_awal)->isoFormat('D MMMM YYYY');
        }
        if ($Request->filled('tanggal_akhir')) {
            $Query->whereDate('Tanggal', '<=', $Request->tanggal_akhir);
            $Params['tanggal_akhir'] = $Request->tanggal_akhir;
            $FilterInfo .= " s/d " . Carbon::parse($Request->tanggal_akhir)->isoFormat('D MMMM YYYY');
        }
        if ($Request->filled('metode')) {
            $Query->where('Metode', $Request->metode);
            $Params['metode'] = $Request->metode;
            $FilterInfo .= " | Metode: " . $Request->metode;
        }

        // 3. Hitung Total secara efisien TANPA memuat semua data ke memori (menggunakan clone query)
        $TotalPendapatan = (clone $Query)->sum('Pendapatan');
        $TotalDiskon = (clone $Query)->sum('Diskon');
        $TotalPendapatanBersih = (clone $Query)->sum('PendapatanBersih');

        // 4. Generate filename
        $Filename = "Laporan_Transaksi_" . Carbon::now()->format('Y-m-d_His') . ".xlsx";

        // 5. Kirim Query Builder (bukan Collection) ke Export Class
        return Excel::download(
            new TransaksiExport($Query, $TotalPendapatan, $TotalDiskon, $TotalPendapatanBersih, $FilterInfo),
            $Filename
        );
    }
    public function updateStatus(Request $request, Transaksi $transaksi)
    {
        $request->validate([
            'Status' => 'required|string|max:50'
        ]);
        $transaksi->Status = $request->input('Status');
        $transaksi->Catatan = $request->input('Catatan');
        $transaksi->UserFinance = auth()->id();
        $transaksi->DicekPada = now();
        $transaksi->save();
        return redirect()->back()->with('success', 'Status transaksi berhasil diperbarui.');
    }
    public function bulkUpdateStatus(Request $request)
    {
        $request->validate([
            'ids' => 'required|array|min:1',
            'ids.*' => 'exists:transaksis,id',
            'Status' => 'required|in:Y,N,N/A',
            'Catatan' => 'nullable|string|max:1000',
        ]);

        $updatedCount = 0;
        $userName = auth()->user()->id ?? '1';
        $now = now();
        foreach ($request->ids as $id) {
            $transaksi = Transaksi::find($id);
            if ($transaksi) {
                $transaksi->update([
                    'Status' => $request->Status,
                    'Catatan' => $request->Catatan,
                    'UserFinance' => $userName,
                    'DicekPada' => $now,
                ]);
                $updatedCount++;

            }
        }

        return response()->json([
            'success' => true,
            'message' => "Berhasil memverifikasi {$updatedCount} transaksi."
        ]);
    }
    public function destroy(Transaksi $transaksi)
    {
        try {
            if ($transaksi->BuktiBayar && Storage::disk('public')->exists($transaksi->BuktiBayar)) {
                Storage::disk('public')->delete($transaksi->BuktiBayar);
            }

            $transaksi->update(['UserDelete' => auth()->id()]);
            $transaksi->delete();

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
