<?php

namespace App\Http\Controllers;

use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;

class TenantController extends Controller
{
    public function Index(Request $Request)
    {
        if ($Request->ajax()) {
            $Query = Tenant::latest('created_at');

            return DataTables::of($Query)
                ->addIndexColumn()
                ->editColumn('TanggalJoin', function ($Row) {
                    return $Row->TanggalJoin ? \Carbon\Carbon::parse($Row->TanggalJoin)->format('d M Y') : '-';
                })

                ->editColumn('StatusSubscription', function ($Row) {
                    $Badge = match ($Row->StatusSubscription) {
                        'Aktif' => 'bg-success',
                        'Expired' => 'bg-danger',
                        default => 'bg-secondary'
                    };
                    return '<span class="badge ' . $Badge . '">' . $Row->StatusSubscription . '</span>';
                })
                ->addColumn('TanggalMulaiSubscription', function ($Row) {
                    return $Row->TanggalMulaiSubscription ? \Carbon\Carbon::parse($Row->TanggalMulaiSubscription)->format('d M Y') : '-';
                })
                ->addColumn('TanggalAkhirSubscription', function ($Row) {
                    return $Row->TanggalAkhirSubscription ? \Carbon\Carbon::parse($Row->TanggalAkhirSubscription)->format('d M Y') : '-';
                })

                ->addColumn('action', function ($Row) {
                    $Btn = '<div class="d-flex gap-1 justify-content-center">';
                    $Btn .= '<a href="' . route('tenant.edit', $Row->id) . '" class="btn btn-warning btn-sm text-white" title="Edit"><i class="ti ti-edit"></i></a> ';
                    $Btn .= '<button type="button" class="btn btn-danger btn-sm btn-delete" data-id="' . $Row->id . '" data-nama="' . htmlspecialchars($Row->Nama) . '" title="Hapus"><i class="ti ti-trash"></i></button>';
                    $Btn .= '</div>';
                    return $Btn;
                })
                ->rawColumns(['StatusSubscription', 'action'])
                ->make(true);
        }

        return view('manejemen-tenant.tenant.index');
    }

    public function Create()
    {
        return view('manejemen-tenant.tenant.create');
    }

    public function Store(Request $Request)
    {
        $Request->validate([
            'Nama' => 'required|string|max:255',
            'Kode' => 'required|string|max:50|unique:tenants,Kode',
            'Email' => 'nullable|email|max:100',
            'Telepon' => 'nullable|string|max:50',
            'Alamat' => 'nullable|string',
            'TanggalJoin' => 'required|date',
            'KodeReferal' => 'nullable|string|max:50|unique:tenants,KodeReferal',
            'StatusSubscription' => 'required|in:Aktif,Nonaktif,Expired',
            'TanggalMulaiSubscription' => 'nullable|date',
            'TanggalAkhirSubscription' => 'nullable|date|after_or_equal:TanggalMulaiSubscription',
        ]);

        $Data = $Request->all();
        $Data['UserCreate'] = Auth::user()->name ?? 'System';

        // Auto generate Kode Referal jika kosong
        if (empty($Data['KodeReferal'])) {
            $Data['KodeReferal'] = strtoupper(Str::random(8));
        }

        Tenant::create($Data);

        return redirect()->route('tenant.index')->with('success', 'Tenant berhasil ditambahkan.');
    }

    public function Edit(Tenant $Tenant)
    {
        return view('manejemen-tenant.tenant.edit', compact('Tenant'));
    }

    public function Update(Request $Request, Tenant $Tenant)
    {
        $Request->validate([
            'Nama' => 'required|string|max:255',
            'Kode' => 'required|string|max:50|unique:tenants,Kode,' . $Tenant->id,
            'Email' => 'nullable|email|max:100',
            'Telepon' => 'nullable|string|max:50',
            'Alamat' => 'nullable|string',
            'TanggalJoin' => 'required|date',
            'KodeReferal' => 'nullable|string|max:50|unique:tenants,KodeReferal,' . $Tenant->id,
            'StatusSubscription' => 'required|in:Aktif,Nonaktif,Expired',
            'TanggalMulaiSubscription' => 'nullable|date',
            'TanggalAkhirSubscription' => 'nullable|date|after_or_equal:TanggalMulaiSubscription',
        ]);

        $Data = $Request->all();
        $Data['UserUpdate'] = Auth::user()->name ?? 'System';

        $Tenant->update($Data);

        return redirect()->route('tenant.index')->with('success', 'Tenant berhasil diperbarui.');
    }

    public function Destroy(Tenant $Tenant)
    {
        try {
            $Tenant->UserDelete = Auth::user()->name ?? 'System';
            $Tenant->save();
            $Tenant->delete();

            return response()->json([
                'success' => true,
                'message' => 'Tenant berhasil dihapus.'
            ]);
        } catch (\Exception $Exception) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus tenant.'
            ], 500);
        }
    }
}
