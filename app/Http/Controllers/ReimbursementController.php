<?php

namespace App\Http\Controllers;

use App\Models\Reimbursement;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ReimbursementController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Reimbursement::select(['id', 'Tanggal', 'Nama', 'Item', 'Nominal', 'Status', 'BuktiUpload']);

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $btn = '<div class="d-flex gap-1 justify-content-center">';
                    // Owner bisa klik edit untuk mengubah status
                    $btn .= '<a href="' . route('reimbursement.edit', $row->id) . '" class="btn btn-warning btn-sm text-white" title="Update Status / Edit">';
                    $btn .= '<i class="ti ti-edit"></i></a> ';
                    $btn .= '<button type="button" class="btn btn-danger btn-sm btn-delete" data-id="' . $row->id . '" data-nama="' . htmlspecialchars($row->Nama) . '" title="Hapus">';
                    $btn .= '<i class="ti ti-trash"></i></button>';
                    $btn .= '</div>';
                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('reimbursement.index');
    }

    public function create()
    {
        return view('reimbursement.create');
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
        $data['UserCreate'] = auth()->user()->name ?? 'System';

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
        return view('reimbursement.edit', compact('reimbursement'));
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
