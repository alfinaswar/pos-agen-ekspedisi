@extends('layouts.app')

@section('content')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">

    <div class="container-fluid">
        <div class="page-title-head d-flex align-items-center flex-wrap gap-2 mb-4">
            <div class="flex-grow-1">
                <h4 class="page-main-title m-0 fw-semibold">
                    <i class="ti ti-package me-2 text-primary"></i>Master Paket Harga
                </h4>
            </div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb m-0 py-0">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-decoration-none text-reset">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Data Paket Harga</li>
                </ol>
            </nav>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0 fw-semibold"><i class="ti ti-list me-2"></i>Data Paket Harga</h4>
                        <a href="{{ route('master-paket-harga.create') }}" class="btn btn-primary btn-sm d-flex align-items-center gap-1">
                            <i class="ti ti-plus"></i> Tambah Paket
                        </a>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered dt-responsive nowrap align-middle mb-0" id="PaketTable" style="width: 100%;">
                                <thead class="table-light">
                                    <tr>
                                        <th class="text-center" style="width: 50px;">#</th>
                                        <th>Kode Paket</th>
                                        <th>Nama Paket</th>
                                        <th class="text-center">Durasi</th>
                                        <th class="text-end">Harga</th>
                                        <th class="text-center">Status</th>
                                        <th class="text-center" style="width: 120px;">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $(document).ready(function() {
            // 1. Inisialisasi Toast Notification
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true
            });

            @if (session('success'))
                Toast.fire({ icon: 'success', title: '{{ session('success') }}' });
            @endif

            // 2. DataTables Initialization
            $('#PaketTable').DataTable({
                responsive: true,
                serverSide: true,
                processing: true,
                destroy: true,
                autoWidth: false,
                ajax: {
                    url: "{{ route('master-paket-harga.index') }}",
                    type: 'GET'
                },
                columnDefs: [
                    { className: 'text-center', targets: [0, 3, 5, 6] },
                    { className: 'text-end', targets: [4] },
                    { orderable: false, targets: [0, 6] }
                ],
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', searchable: false },
                    {
                        data: 'KodePaket',
                        name: 'KodePaket',
                        render: function(Data) {
                            return `<span class="badge bg-light text-dark border fw-normal">${Data}</span>`;
                        }
                    },
                    {
                        data: 'NamaPaket',
                        name: 'NamaPaket',
                        render: function(Data) {
                            return `<span class="fw-semibold text-dark">${Data}</span>`;
                        }
                    },
                    {
                        data: 'DurasiBulan',
                        name: 'DurasiBulan',
                        render: function(Data) {
                            return `<span class="text-muted">${Data} Bulan</span>`;
                        }
                    },
                    { data: 'Harga', name: 'Harga' },
                    { data: 'Status', name: 'Status' },
                    { data: 'action', name: 'action', searchable: false }
                ],
                language: {
                    url: "//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json"
                }
            });

            // 3. Delete Handler dengan SweetAlert2
            $('body').on('click', '.btn-hapus', function() {
                const Id = $(this).data('id');
                const Nama = $(this).data('nama');

                Swal.fire({
                    title: 'Hapus Paket Harga?',
                    html: `Anda akan menghapus paket:<br><strong class="text-primary">${Nama}</strong><br>Tindakan ini tidak dapat dibatalkan!`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc3545',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal',
                    reverseButtons: true
                }).then((Result) => {
                    if (Result.isConfirmed) {
                        $.ajax({
                            url: "{{ route('master-paket-harga.destroy', ':id') }}".replace(':id', Id),
                            type: 'POST',
                            data: {
                                _method: 'DELETE',
                                _token: '{{ csrf_token() }}'
                            },
                            beforeSend: function() {
                                Swal.fire({
                                    title: 'Menghapus...',
                                    text: 'Mohon tunggu sebentar',
                                    allowOutsideClick: false,
                                    didOpen: () => Swal.showLoading()
                                });
                            },
                            success: function(Response) {
                                if (Response.success) {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Berhasil!',
                                        text: Response.message,
                                        timer: 2000,
                                        showConfirmButton: false
                                    });
                                    $('#PaketTable').DataTable().ajax.reload(null, false);
                                } else {
                                    Swal.fire('Gagal!', Response.message || 'Terjadi kesalahan', 'error');
                                }
                            },
                            error: function(Xhr) {
                                const ErrorMessage = Xhr.responseJSON?.message || 'Terjadi kesalahan saat menghapus data.';
                                Swal.fire('Gagal!', ErrorMessage, 'error');
                            }
                        });
                    }
                });
            });
        });
    </script>
@endpush
