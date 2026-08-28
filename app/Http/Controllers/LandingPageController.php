<?php

namespace App\Http\Controllers;

use App\Models\MasterPaketHarga;
use Illuminate\Http\Request;

class LandingPageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $Paket = MasterPaketHarga::get();
        // dd($Paket);
        return view('landing-page.index',compact('Paket'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function daftar()
    {
        $Paket = MasterPaketHarga::get();
        // dd($Paket);
        return view('landing-page.pendaftaran', compact('Paket'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
