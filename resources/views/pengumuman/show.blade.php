@extends('layouts.app')

@section('title', 'Detail Pengumuman')

@section('content')
<div class="container-fluid">
    <!-- Breadcrumb -->
    <div class="py-3">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb m-0 bg-transparent px-0">
                <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-decoration-none text-reset">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('pengumuman.index') }}" class="text-decoration-none text-reset">Pengumuman</a></li>
                <li class="breadcrumb-item active" aria-current="page">Detail</li>
            </ol>
        </nav>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-9 col-xl-8">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white border-bottom-0 py-4">
                    <h4 class="mb-0 fw-bold text-primary">
                        <i class="ti ti-megaphone"></i> Detail Pengumuman
                    </h4>
                </div>
                <div class="card-body pt-1">
                    <div class="mb-4 d-flex gap-3 align-items-center flex-wrap">
                        @if($Pengumuman->Gambar)
                            <div>
                                <img src="{{ asset('storage/'.$Pengumuman->Gambar) }}" alt="Gambar Pengumuman" style="max-width:140px;max-height:110px;border-radius:6px;object-fit:cover;">
                            </div>
                        @endif
                        <div>
                            <div class="badge bg-secondary mb-2">{{ $Pengumuman->Kategori ?? '-' }}</div>
                            <h3 class="fw-bold mb-1">{{ $Pengumuman->Judul }}</h3>
                            <div class="mb-1 text-muted fst-italic" style="font-size:0.97em;">
                                Diposting pada: {{ $Pengumuman->Tanggal }}
                                @if($Pengumuman->UserCreate)
                                    oleh <span class="fw-semibold">{{ $Pengumuman->UserCreate }}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div>
                        <div class="mb-2 fw-bold">Isi Pengumuman:</div>
                        <div class="mb-4" style="font-size:1.07em;">
                            {!! $Pengumuman->Isi !!}
                        </div>
                    </div>
                    <a href="{{ route('pengumuman.index') }}" class="btn btn-secondary">
                        <i class="ti ti-arrow-left"></i> Kembali
                    </a>
                    <a href="{{ route('pengumuman.edit', $Pengumuman->id) }}" class="btn btn-warning text-white">
                        <i class="ti ti-edit"></i> Edit
                    </a>
                </div>
                <div class="card-footer bg-white py-3 small text-end text-muted">
                    <span>Terakhir diperbarui:
                        {{ $Pengumuman->updated_at ? $Pengumuman->updated_at->format('d M Y, H:i') : '-' }}
                        @if($Pengumuman->UserUpdate)
                            oleh <span class="fw-semibold">{{ $Pengumuman->UserUpdate }}</span>
                        @endif
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
