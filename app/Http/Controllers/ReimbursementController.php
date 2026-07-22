<?php

namespace App\Http\Controllers;

use App\Exports\ReimbursementExport;
use App\Models\Reimbursement;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class ReimbursementController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            // Mulai query dasar
            $query = Reimbursement::with('getUser')->latest()->select(['id', 'Tanggal', 'Nama', 'Item', 'Nominal', 'Status', 'BuktiUpload']);

            // Kalau bukan admin, tampilkan hanya data reimbursement yang dibuat oleh user login saat ini
            if (!auth()->user() || auth()->user()->role !== 'Admin') {
                // Asumsikan 'Nama' adalah user_id yang create reimbursement
                $query->where('Nama', auth()->user()->id);
            }

            // FILTER: Berdasarkan tanggal_awal dan tanggal_akhir
            if ($request->filled('tanggal_awal')) {
                $query->whereDate('Tanggal', '>=', $request->input('tanggal_awal'));
            }
            if ($request->filled('tanggal_akhir')) {
                $query->whereDate('Tanggal', '<=', $request->input('tanggal_akhir'));
            }

            // FILTER: Status reimbursement (optional)
            if ($request->filled('status')) {
                $query->where('Status', $request->input('status'));
            }

            // FILTER: Berdasarkan Nama pengaju (optional, gunakan id user)
            if ($request->filled('nama')) {
                $query->where('Nama', $request->input('nama'));
            }

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $btn = '<div class="d-flex gap-1 justify-content-center">';
                    // Admin bisa edit dan hapus, atau user non-admin jika Nama = user login
                    if (auth()->user()) {
                        $isAdmin = auth()->user()->role === 'Admin';
                        $isOwner = $row->Nama == auth()->user()->id;
                        if ($isAdmin || $isOwner) {
                            $btn .= '<a href="' . route('reimbursement.edit', $row->id) . '" class="btn btn-warning btn-sm text-white" title="Update Status / Edit">';
                            $btn .= '<i class="ti ti-edit"></i></a> ';
                            $btn .= '<button type="button" class="btn btn-danger btn-sm btn-delete" data-id="' . $row->id . '" data-nama="' . htmlspecialchars($row->Nama) . '" title="Hapus">';
                            $btn .= '<i class="ti ti-trash"></i></button>';
                        }
                    }
                    $btn .= '</div>';
                    return $btn;
                })

                ->editColumn('Nama', function ($row) {
                    return optional($row->getUser)->name ?: $row->Nama;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        // Dapatkan list user untuk filter (opsional dikirim ke blade)
        $users = User::select(['id', 'name'])->get();

        return view('reimbursement.index', compact('users'));
    }

    public function create()
    {
        $user = User::get();
        return view('reimbursement.create',compact('user'));
    }
    public function export(Request $request)
    {
        $query = Reimbursement::query();
        $filters = [];

        // 1. Filter Tanggal
        if ($request->filled('tanggal_awal') && $request->filled('tanggal_akhir')) {
            $query->whereBetween('Tanggal', [$request->tanggal_awal, $request->tanggal_akhir]);
            $filters[] = "Periode: " . Carbon::parse($request->tanggal_awal)->isoFormat('D MMM YYYY') . " s/d " . Carbon::parse($request->tanggal_akhir)->isoFormat('D MMM YYYY');
        }

        // 2. Filter Status
        if ($request->filled('status')) {
            $query->where('Status', $request->status);
            $filters[] = "Status: " . $request->status;
        }

        $filterInfo = !empty($filters) ? implode(' | ', $filters) : "Semua Data";

        // Ambil data sesuai filter, urutkan dari terbaru
        $data = $query->orderBy('Tanggal', 'desc')->get();

        $filename = 'Laporan_Reimbursement_' . Carbon::now()->format('Ymd_His') . '.xlsx';

        return Excel::download(
            new ReimbursementExport($data, $filterInfo),
            $filename
        );
    }
    public function store(Request $request)
    {
        $request->validate([
            'Tanggal' => 'required|date',
            'Nama' => 'required|string|max:100',
            'Item' => 'required|string',
            'Nominal' => 'required|numeric|min:0',
            'BuktiUpload' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        $data = $request->except(['BuktiUpload']);
        $data['Status'] = 'Menunggu'; // Default status saat user input
        $data['UserCreate'] = auth()->user()->id ?? null;

        // Handle Upload File
        if ($request->hasFile('BuktiUpload')) {
            $file = $request->file('BuktiUpload');
            $fileName = time() . '_' . preg_replace('/[^A-Za-z0-9\-_\.]/', '', $file->getClientOriginalName());
            $data['BuktiUpload'] = $file->storeAs('reimbursement', $fileName, 'public');
        }

        Reimbursement::create($data);

        return redirect()->route('reimbursement.index')->with('success', 'Pengajuan reimbursement berhasil dikirim.');
    }

    public function edit(Reimbursement $reimbursement)
    {
        $user = User::get();
        return view('reimbursement.edit', compact('reimbursement','user'));
    }

    public function update(Request $request, Reimbursement $reimbursement)
    {
        // dd($request->all());
        $request->validate([
            'Tanggal' => 'required|date',
            'Nama' => 'required|string|max:100',
            'Item' => 'required|string',
            'Nominal' => 'required|numeric|min:0',
            'Status' => 'required|in:Menunggu,Ditolak,Dibayar',
            'BuktiUpload' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        $data = $request->except(['BuktiUpload']);
        // Catat siapa Owner yang mengubah status
        $data['OwnerUpdate'] = auth()->user()->name ?? 'Owner';

        // Handle Upload File Baru
        if ($request->hasFile('BuktiUpload')) {
            if ($reimbursement->BuktiUpload && Storage::disk('public')->exists($reimbursement->BuktiUpload)) {
                Storage::disk('public')->delete($reimbursement->BuktiUpload);
            }
            $file = $request->file('BuktiUpload');
            $fileName = time() . '_' . preg_replace('/[^A-Za-z0-9\-_\.]/', '', $file->getClientOriginalName());
            $data['BuktiUpload'] = $file->storeAs('reimbursement', $fileName, 'public');
        } else {
            unset($data['BuktiUpload']);
        }

        $reimbursement->update($data);

        return redirect()->route('reimbursement.index')->with('success', 'Status reimbursement berhasil diperbarui.');
    }

    public function destroy(Reimbursement $reimbursement)
    {
        try {
            if ($reimbursement->BuktiUpload && Storage::disk('public')->exists($reimbursement->BuktiUpload)) {
                Storage::disk('public')->delete($reimbursement->BuktiUpload);
            }
            $reimbursement->update(['UserDelete' => auth()->user()->name ?? 'System']);
            $reimbursement->delete();

            return response()->json(['success' => true, 'status' => 200, 'message' => 'Data berhasil dihapus.']);
        } catch (\Exception $e) {
            Log::error('Gagal menghapus reimbursement: ' . $e->getMessage());
            return response()->json(['success' => false, 'status' => 500, 'message' => 'Gagal menghapus data.'], 500);
        }
    }
}
