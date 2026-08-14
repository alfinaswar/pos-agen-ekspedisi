<?php

namespace App\Http\Controllers;

use App\Models\PekerjaanKurir;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;

class PekerjaanKurirController extends Controller
{
    public function index(Request $Request)
    {
        if ($Request->ajax()) {
            // Ambil user yang sedang login
            $user = Auth::user();

            // Jika bukan admin, filter berdasarkan IdUser
            if ($user && $user->role !== 'Admin') {
                $Query = PekerjaanKurir::with('getKurir')->where('IdUser', $user->id)->latest();
            } else {
                $Query = PekerjaanKurir::with('getKurir')->latest();
            }

            return DataTables::of($Query)
                ->addIndexColumn()
                ->addColumn('Tanggal', function ($Row) {
                    if ($Row->Tanggal) {
                        $tanggal = \Carbon\Carbon::parse($Row->Tanggal)->format('d M Y');
                        $jam = $Row->Jam
                            ? ' <span class="text-muted" style="font-size:100%;font-family:inherit;">'
                                . '<span style="font-size:100%;">'
                                . \Carbon\Carbon::createFromFormat('H:i:s', $Row->Jam)->format('H:i')
                                . '</span></span>'
                            : '';
                        return '<span style="font-family:inherit;font-size:98%;">' . $tanggal . '</span>' . $jam;
                    }
                    return '<span style="font-family:inherit;font-size:98%;">-</span>';
                })
                ->editColumn('BuktiFoto', function ($Row) {
                    if ($Row->BuktiFoto) {
                        return '<a href="' . asset('storage/' . $Row->BuktiFoto) . '" target="_blank" class="btn btn-sm btn-outline-primary"><i class="ti ti-eye"></i> Lihat</a>';
                    }
                    return '<span class="text-muted">-</span>';
                })
                ->editColumn('Durasi', function ($Row) {
                    if ($Row->Durasi) {
                        return htmlspecialchars($Row->Durasi) . ' Menit';
                    }
                    return '<span class="text-muted">-</span>';
                })

                ->addColumn('JumlahPaket', function ($Row) {
                    return is_null($Row->JumlahPaket)
                        ? '<span class="text-muted">-</span>'
                        : '<strong>' . $Row->JumlahPaket . ' Paket</strong>';
                })
                ->addColumn('action', function ($Row) {
                    $btnEdit = '<a href="' . route('pekerjaan-kurir.edit', $Row->id) . '" class="btn btn-warning btn-sm text-white" title="Edit"><i class="ti ti-edit"></i></a>';
                    $btnDelete = '<button type="button" class="btn btn-danger btn-sm btn-delete" data-id="' . $Row->Id . '" data-tanggal="' . $Row->Tanggal . '" title="Hapus"><i class="ti ti-trash"></i></button>';
                    return '<div class="d-flex gap-1 justify-content-center">' . $btnEdit . ' ' . $btnDelete . '</div>';
                })
                ->addColumn('NamaKurir', function ($Row) {
                    if (isset($Row->getKurir) && $Row->getKurir && isset($Row->getKurir->name)) {
                        return e($Row->getKurir->name);
                    }
                    // fallback if no relation loaded
                    if (isset($Row->NamaKurir)) {
                        return e($Row->NamaKurir);
                    }
                    return '<span class="text-muted">-</span>';
                })

                ->rawColumns(['Pekerjaan', 'BuktiFoto', 'action','JumlahPaket','Tanggal','NamaKurir'])
                ->make(true);
        }

        return view('pekerjaan-kurir.index');
    }

    public function create()
    {
        return view('pekerjaan-kurir.create');
    }

    public function store(Request $Request)
    {
        // Custom validation, following migration structure (nullable for most fields except required)
        $Request->validate([
            'Tanggal' => 'nullable|date',
            'Jam' => 'nullable',
            'Pekerjaan' => 'nullable|string|max:255',
            'DariLokasi' => 'nullable|string|max:255',
            'Tujuan' => 'nullable|string|max:255',
            'JumlahPaket' => 'nullable|integer|min:0',
            'Durasi' => 'nullable|string|max:255',
            'Keterangan' => 'nullable|string',
            'BuktiFoto' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        // Prepare data for insertion, capitalize keys according to migration definition
        $Data = [
            'Tanggal'      => $Request->input('Tanggal'),
            'Jam'          => $Request->input('Jam'),
            'Pekerjaan'    => $Request->input('Pekerjaan'),
            'DariLokasi'   => $Request->input('DariLokasi'),
            'Tujuan'       => $Request->input('Tujuan'),
            'JumlahPaket'  => $Request->input('JumlahPaket', 0),
            'Durasi'       => $Request->input('Durasi'),
            'Keterangan'   => $Request->input('Keterangan'),
            'IdUser'       => Auth::id(),
            'UserCreate'   => Auth::user()->name ?? 'System',
            // 'Tenant'       => Auth::user()->tenant ?? null,
        ];

        if ($Request->hasFile('BuktiFoto')) {
            $File = $Request->file('BuktiFoto');
            $FileName = time() . '_' . preg_replace('/[^A-Za-z0-9\-_\.]/', '', $File->getClientOriginalName());
            $Data['BuktiFoto'] = $File->storeAs('pekerjaan_kurir', $FileName, 'public');
        }

        PekerjaanKurir::create($Data);

        return redirect()->route('pekerjaan-kurir.index')->with('success', 'Laporan aktivitas kurir berhasil disimpan.');
    }

    public function edit(PekerjaanKurir $PekerjaanKurir)
    {
        return view('pekerjaan-kurir.edit', compact('PekerjaanKurir'));
    }

    public function update(Request $Request, PekerjaanKurir $PekerjaanKurir)
    {
        $Request->validate([
            'Tanggal' => 'required|date',
            'Jam' => 'required',
            'Pekerjaan' => 'required|in:Ambil Paket,Antar Paket,Lain-lain',
            'DariLokasi' => 'required|string|max:255',
            'Tujuan' => 'required|string|max:255',
            'JumlahPaket' => 'required|integer|min:0',
            'Durasi' => 'required|string|max:100',
            'Keterangan' => 'nullable|string',
            'BuktiFoto' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $Data = $Request->except(['BuktiFoto']);
        $Data['UserUpdate'] = Auth::user()->name ?? 'System';

        if ($Request->hasFile('BuktiFoto')) {
            if ($PekerjaanKurir->BuktiFoto && Storage::disk('public')->exists($PekerjaanKurir->BuktiFoto)) {
                Storage::disk('public')->delete($PekerjaanKurir->BuktiFoto);
            }
            $File = $Request->file('BuktiFoto');
            $FileName = time() . '_' . preg_replace('/[^A-Za-z0-9\-_\.]/', '', $File->getClientOriginalName());
            $Data['BuktiFoto'] = $File->storeAs('pekerjaan_kurir', $FileName, 'public');
        }

        $PekerjaanKurir->update($Data);

        return redirect()->route('pekerjaan-kurir.index')->with('success', 'Laporan aktivitas kurir berhasil diperbarui.');
    }

    public function destroy($id)
    {
        dd($id);
        try {
            // Cari data dulu
            $PekerjaanKurir = PekerjaanKurir::findOrFail($id);

            if ($PekerjaanKurir->BuktiFoto && Storage::disk('public')->exists($PekerjaanKurir->BuktiFoto)) {
                Storage::disk('public')->delete($PekerjaanKurir->BuktiFoto);
            }

            $PekerjaanKurir->UserDelete = Auth::user()->name ?? 'System';
            $PekerjaanKurir->save();
            $PekerjaanKurir->delete();

            return response()->json(['success' => true, 'message' => 'Data berhasil dihapus.']);
        } catch (\Exception $Exception) {
            return response()->json(['success' => false, 'message' => 'Gagal menghapus data.'], 500);
        }
    }
}
