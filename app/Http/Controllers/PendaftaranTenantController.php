<?php

namespace App\Http\Controllers;

use App\Models\PendaftaranTenant;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class PendaftaranTenantController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function Index(Request $Request)
    {
        if ($Request->ajax()) {
            $Query = PendaftaranTenant::latest('created_at');

            return DataTables::of($Query)
                ->addIndexColumn()
                ->editColumn('Status', function ($Row) {
                    $Badge = match ($Row->Status) {
                        'Y' => 'bg-success',
                        'N' => 'bg-danger',
                        default => 'bg-secondary'
                    };
                    $Label = match ($Row->Status) {
                        'Y' => 'Disetujui',
                        'N' => 'Ditolak',
                        default => 'Belum Diverifikasi'
                    };
                    return '<span class="badge ' . $Badge . '">' . $Label . '</span>';
                })
                ->editColumn('BuktiPembayaran', function ($Row) {
                    if ($Row->BuktiPembayaran) {
                        return '<a href="' . asset('storage/' . $Row->BuktiPembayaran) . '" target="_blank" class="btn btn-sm btn-outline-primary"><i class="ti ti-eye"></i> Lihat</a>';
                    }
                    return '<span class="text-muted">-</span>';
                })
                ->addColumn('action', function ($Row) {
                    $Btn = '<div class="d-flex gap-1 justify-content-center">';
                    $Btn .= '<a href="#" class="btn btn-info btn-sm text-white" title="Detail"><i class="ti ti-eye"></i></a>';
                    $Btn .= '</div>';
                    return $Btn;
                })
                ->rawColumns(['Status', 'BuktiPembayaran', 'action'])
                ->make(true);
        }

        return view('manejemen-tenant.pendaftaran.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // dd($request->all());
        $validated = $request->validate([
            'Nama'           => 'required|string|max:255',
            'Email'          => 'required|email|max:255',
            'Telepon'        => 'required|string|min:9|max:20',
            'Alamat'         => 'required|string|max:500',
            'NamaPIC'        => 'required|string|max:255',
            'EmailPIC'       => 'required|email|max:255',
            'AlamatPIC'      => 'nullable|string|max:500',
            'BuktiPembayaran'=> 'required|file|mimes:jpg,jpeg,png,pdf,webp|max:5120', // 5MB
        ], [
            'required' => ':attribute wajib diisi.',
            'email'    => ':attribute harus berupa email yang valid.',
            'min'      => ':attribute minimal :min karakter.',
            'max'      => ':attribute maksimal :max karakter.',
            'file'     => ':attribute harus berupa file.',
            'mimes'    => ':attribute harus JPG/PNG/PDF/WEBP.',
        ]);

        // Handle file upload
        $buktiPembayaranPath = null;
        if ($request->hasFile('BuktiPembayaran')) {
            $file = $request->file('BuktiPembayaran');
            $fileName = time() . '_' . preg_replace('/[^A-Za-z0-9\-_\.]/', '', $file->getClientOriginalName());
            $buktiPembayaranPath = $file->storeAs('bukti_pembayaran', $fileName, 'public');
        }
        // Simpan data pendaftaran
        $pendaftaran = PendaftaranTenant::create([
            'Nama'             => $validated['Nama'],
            'Email'            => $validated['Email'],
            'Alamat'           => $validated['Alamat'],
            'NamaPIC'          => $validated['NamaPIC'],
            'EmailPIC'         => $validated['EmailPIC'],
            'AlamatPIC'        => $validated['AlamatPIC'] ?? null,
            'BuktiPembayaran'  => $buktiPembayaranPath,
            'Status'           => 'N',
        ]);

        return redirect()
            ->back()
            ->with('success', [
                'message' => 'Pendaftaran berhasil dikirim! Proses pendaftaran akan diproses dalam 1 x 24 jam.',
                'kode' => $pendaftaran->Kode,
            ]);

    }

    /**
     * Display the specified resource.
     */
    public function show(PendaftaranTenant $pendaftaranTenant)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PendaftaranTenant $pendaftaranTenant)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PendaftaranTenant $pendaftaranTenant)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PendaftaranTenant $pendaftaranTenant)
    {
        //
    }
}
