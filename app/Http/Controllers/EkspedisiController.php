<?php

namespace App\Http\Controllers;

use App\Models\Ekspedisi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EkspedisiController extends Controller
{
    public function index()
    {
        return view('ekspedisi.index');
    }

    public function data()
    {
        $ekspedisi = Ekspedisi::with('Creator')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'data' => $ekspedisi->map(function ($item) {
                return [
                    'id' => $item->id,
                    'NamaEkspedisi' => $item->NamaEkspedisi,
                    'Deskripsi' => $item->Deskripsi,
                    'UserCreate' => $item->Creator->Nama ?? '-',
                    'created_at' => $item->created_at->format('d/m/Y H:i'),
                    'actions' => '
                        <button class="btn btn-sm btn-primary me-1" onclick="editEkspedisi(' . $item->id . ')">
                            <i class="bi bi-pencil"></i>
                        </button>
                        <button class="btn btn-sm btn-danger" onclick="deleteEkspedisi(' . $item->id . ')">
                            <i class="bi bi-trash"></i>
                        </button>
                    ',
                ];
            })
        ]);
    }

    public function create()
    {
        return view('ekspedisi.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'NamaEkspedisi' => 'required|string|max:100',
            'Deskripsi' => 'nullable|string',
        ]);

        $validated['UserCreate'] = Auth::id();

        Ekspedisi::create($validated);

        return redirect()->route('ekspedisi.index')
            ->with('success', 'Ekspedisi berhasil ditambahkan');
    }

    public function show($id)
    {
        $ekspedisi = Ekspedisi::with('Creator')->findOrFail($id);
        return view('ekspedisi.show', compact('ekspedisi'));
    }

    public function edit($id)
    {
        $ekspedisi = Ekspedisi::findOrFail($id);
        return view('ekspedisi.edit', compact('ekspedisi'));
    }

    public function update(Request $request, $id)
    {
        $ekspedisi = Ekspedisi::findOrFail($id);

        $validated = $request->validate([
            'NamaEkspedisi' => 'required|string|max:100',
            'Deskripsi' => 'nullable|string',
        ]);

        $ekspedisi->update($validated);

        return redirect()->route('ekspedisi.index')
            ->with('success', 'Ekspedisi berhasil diupdate');
    }

    public function destroy($id)
    {
        $ekspedisi = Ekspedisi::findOrFail($id);
        $ekspedisi->delete();

        return response()->json([
            'success' => true,
            'message' => 'Ekspedisi berhasil dihapus'
        ]);
    }
}
