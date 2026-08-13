<?php

namespace App\Http\Controllers;

use App\Models\Pengumuman;
use App\Models\Divisi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;

class PengumumanController extends Controller
{
    public function Index(Request $Request)
    {
        if ($Request->ajax()) {
            $Query = Pengumuman::latest('created_at');

            if ($Request->filled('Kategori')) {
                $Query->where('Kategori', $Request->Kategori);
            }

            return DataTables::of($Query)
                ->addIndexColumn()
                ->editColumn('Kategori', function ($Row) {
                    $Badge = match ($Row->Kategori) {
                        'Darurat' => 'bg-danger',
                        'Penting' => 'bg-warning text-dark',
                        default => 'bg-info text-dark'
                    };
                    return '<span class="badge ' . $Badge . '">' . $Row->Kategori . '</span>';
                })
                ->editColumn('Gambar', function ($Row) {
                    if ($Row->Gambar) {
                        return '<img src="' . asset('storage/' . $Row->Gambar) . '" alt="Gambar" style="max-width: 80px; max-height: 60px; border-radius: 6px; object-fit: cover;">';
                    }
                    return '<span class="text-muted">-</span>';
                })
                ->addColumn('Isi', function ($Row) {
                    // Ambil plain text tanpa tag HTML
                    $IsiText = strip_tags($Row->Isi);
                    // Batasi 100 karakter, tambahkan '...' jika lebih
                    if (mb_strlen($IsiText) > 100) {
                        $IsiText = mb_substr($IsiText, 0, 100) . '...';
                    }
                    // Tampilkan tetap dalam kolom agar tidak rusak layout, escape XSS
                    return '<span class="col" title="' . e($IsiText) . '">' . e($IsiText) . '</span>';
                })


                ->editColumn('UserCreate', function ($Row) {
                    return $Row->UserCreate ?: 'System';
                })
                ->addColumn('action', function ($Row) {
                    $Btn = '<div class="d-flex gap-1 justify-content-center">';
                    $Btn .= '<a href="' . route('pengumuman.show', $Row->id) . '" class="btn btn-info btn-sm text-white" title="Lihat"><i class="ti ti-eye"></i></a> ';
                    $Btn .= '<a href="' . route('pengumuman.edit', $Row->id) . '" class="btn btn-warning btn-sm text-white" title="Edit"><i class="ti ti-edit"></i></a> ';
                    $Btn .= '<button type="button" class="btn btn-danger btn-sm btn-delete" data-id="' . $Row->id . '" data-judul="' . htmlspecialchars($Row->Judul) . '" title="Hapus"><i class="ti ti-trash"></i></button>';
                    $Btn .= '</div>';
                    return $Btn;
                })
                ->rawColumns(['Kategori', 'Gambar', 'Isi', 'action'])
                ->make(true);
        }

        return view('pengumuman.index');
    }

    public function Create()
    {
        $Divisis = Divisi::get();
        return view('pengumuman.create', compact('Divisis'));
    }

    public function Store(Request $Request)
    {
        $Request->validate([
            'Judul' => 'required|string|max:255',
            'Gambar' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'Isi' => 'required|string',
            'Tanggal' => 'required|string',
        ]);

        $Data = $Request->except(['Gambar']);
        $Data['UserCreate'] = Auth::user()->name ?? 'System';

        // Handle Upload Gambar
        if ($Request->hasFile('Gambar')) {
            $File = $Request->file('Gambar');
            $FileName = time() . '_' . preg_replace('/[^A-Za-z0-9\-_\.]/', '', $File->getClientOriginalName());
            $Data['Gambar'] = $File->storeAs('pengumuman', $FileName, 'public');
        }


        Pengumuman::create($Data);

        return redirect()->route('pengumuman.index')->with('success', 'Pengumuman berhasil diterbitkan.');
    }

    public function Show(Pengumuman $Pengumuman)
    {
        return view('pengumuman.show', compact('Pengumuman'));
    }

    public function Edit(Pengumuman $Pengumuman)
    {
        $Divisis = Divisi::get();
        return view('pengumuman.edit', compact('Pengumuman', 'Divisis'));
    }

    public function Update(Request $Request, Pengumuman $Pengumuman)
    {
        // Validasi hanya field yang sama dengan Store
        $Request->validate([
            'Judul'   => 'required|string|max:255',
            'Gambar'  => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'Isi'     => 'required|string',
            'Tanggal' => 'required|string',
        ]);

        $Data = $Request->except(['Gambar']);
        $Data['UserUpdate'] = Auth::user()->name ?? 'System';

        // Handle Update Gambar
        if ($Request->hasFile('Gambar')) {
            // Hapus gambar lama jika ada
            if ($Pengumuman->Gambar && Storage::disk('public')->exists($Pengumuman->Gambar)) {
                Storage::disk('public')->delete($Pengumuman->Gambar);
            }
            $File = $Request->file('Gambar');
            $FileName = time() . '_' . preg_replace('/[^A-Za-z0-9\-_\.]/', '', $File->getClientOriginalName());
            $Data['Gambar'] = $File->storeAs('pengumuman', $FileName, 'public');
        }

        $Pengumuman->update($Data);

        return redirect()->route('pengumuman.index')->with('success', 'Pengumuman berhasil diperbarui.');
    }

    public function Destroy(Pengumuman $Pengumuman)
    {
        try {
            // Hapus gambar dari storage jika ada
            if ($Pengumuman->Gambar && Storage::disk('public')->exists($Pengumuman->Gambar)) {
                Storage::disk('public')->delete($Pengumuman->Gambar);
            }

            // Soft delete dengan mencatat UserDelete
            $Pengumuman->UserDelete = Auth::user()->name ?? 'System';
            $Pengumuman->save();
            $Pengumuman->delete();

            return response()->json([
                'success' => true,
                'message' => 'Pengumuman berhasil dihapus.'
            ]);
        } catch (\Exception $Exception) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus pengumuman.'
            ], 500);
        }
    }
}
