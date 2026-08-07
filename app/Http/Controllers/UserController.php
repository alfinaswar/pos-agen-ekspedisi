<?php

namespace App\Http\Controllers;

use App\Models\Divisi;
use App\Models\User;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = User::select(['id', 'name', 'email', 'email_verified_at', 'role', 'divisi', 'no_hp', 'created_at']);

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $btn = '<div class="d-flex gap-1 justify-content-center">';
                    $btn .= '<a href="' . route('users.edit', $row->id) . '" class="btn btn-warning btn-sm text-white" title="Edit">';
                    $btn .= '<i class="ti ti-edit"></i></a> ';
                    $btn .= '<button type="button" class="btn btn-danger btn-sm btn-delete" data-id="' . $row->id . '" data-nama="' . htmlspecialchars($row->name) . '" title="Hapus">';
                    $btn .= '<i class="ti ti-trash"></i></button>';
                    $btn .= '</div>';
                    return $btn;
                })
                ->editColumn('email_verified_at', function ($row) {
                    return $row->email_verified_at ? '<span class="badge bg-success">Verified</span>' : '<span class="badge bg-secondary">Unverified</span>';
                })
                ->editColumn('role', function ($row) {
                    $badge = $row->role === 'Admin' ? 'bg-primary' : 'bg-info text-dark';
                    return '<span class="badge ' . $badge . '">' . $row->role . '</span>';
                })
                ->editColumn('divisi', function ($row) {
                    return $row->divisi ?? '<span class="text-muted">-</span>';
                })
                ->editColumn('no_hp', function ($row) {
                    return $row->no_hp ?? '<span class="text-muted">-</span>';
                })
                ->editColumn('divisi', function ($row) {
                    if (!$row->divisi) {
                        return '<span class="text-muted">-</span>';
                    }
                    $divisi = Divisi::find($row->divisi);
                    return $divisi ? $divisi->Nama : '<span class="text-muted">Tidak Diketahui</span>';
                })

                ->rawColumns(['email_verified_at', 'role', 'divisi', 'no_hp', 'action'])
                ->make(true);
        }
        // $user = Divisi::get();
        return view('users.index');
    }

    public function create()
    {
        $divisi = Divisi::get();
        return view('users.create',compact('divisi'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:Admin,Leader,Kasir,Finance',
            'divisi' => 'nullable|exists:divisis,id',
            'no_hp' => 'nullable|string|max:20',
            'foto_profil' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'foto_ktp' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $data = $request->except(['password', 'foto_profil', 'foto_ktp']);
        $data['password'] = Hash::make($request->password);

        // ✅ Handle Upload Foto Profil dengan storeAs
        if ($request->hasFile('foto_profil')) {
            $file = $request->file('foto_profil');
            $fileName = time() . '_' . preg_replace('/[^A-Za-z0-9\-_\.]/', '', $file->getClientOriginalName());
            $data['foto_profil'] = $file->storeAs('users/foto_profil', $fileName, 'public');
        }

        // ✅ Handle Upload Foto KTP dengan storeAs
        if ($request->hasFile('foto_ktp')) {
            $file = $request->file('foto_ktp');
            $fileName = time() . '_' . preg_replace('/[^A-Za-z0-9\-_\.]/', '', $file->getClientOriginalName());
            $data['foto_ktp'] = $file->storeAs('users/foto_ktp', $fileName, 'public');
        }

        User::create($data);

        return redirect()->route('users.index')->with('success', 'User berhasil ditambahkan.');
    }

    public function edit(User $user)
    {
        $divisi = Divisi::get();
        return view('users.edit', compact('user','divisi'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed',
            'role' => 'required|in:Admin,Leader,Kasir,Finance',
            'divisi' => 'nullable|exists:divisis,id',
            'no_hp' => 'nullable|string|max:20',
            'foto_profil' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'foto_ktp' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $data = $request->except(['password', 'foto_profil', 'foto_ktp']);

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        // ✅ Handle Update Foto Profil dengan storeAs (Hapus file lama jika ada)
        if ($request->hasFile('foto_profil')) {
            if ($user->foto_profil && Storage::disk('public')->exists($user->foto_profil)) {
                Storage::disk('public')->delete($user->foto_profil);
            }
            $file = $request->file('foto_profil');
            $fileName = time() . '_' . preg_replace('/[^A-Za-z0-9\-_\.]/', '', $file->getClientOriginalName());
            $data['foto_profil'] = $file->storeAs('users/foto_profil', $fileName, 'public');
        }

        // ✅ Handle Update Foto KTP dengan storeAs (Hapus file lama jika ada)
        if ($request->hasFile('foto_ktp')) {
            if ($user->foto_ktp && Storage::disk('public')->exists($user->foto_ktp)) {
                Storage::disk('public')->delete($user->foto_ktp);
            }
            $file = $request->file('foto_ktp');
            $fileName = time() . '_' . preg_replace('/[^A-Za-z0-9\-_\.]/', '', $file->getClientOriginalName());
            $data['foto_ktp'] = $file->storeAs('users/foto_ktp', $fileName, 'public');
        }

        $user->update($data);

        return redirect()->route('users.index')->with('success', 'Data user berhasil diperbarui.');
    }

    public function destroy(User $user)
    {
        try {
            // Prevent deleting yourself
            if ($user->id === auth()->id()) {
                return response()->json([
                    'success' => false,
                    'status' => 403,
                    'message' => 'Anda tidak dapat menghapus akun Anda sendiri.'
                ], 403);
            }

            $user->delete();

            return response()->json([
                'success' => true,
                'status' => 200,
                'message' => 'User berhasil dihapus.'
            ]);
        } catch (\Exception $e) {
            Log::error('Gagal menghapus user: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'status' => 500,
                'message' => 'Gagal menghapus user.'
            ], 500);
        }
    }
}
