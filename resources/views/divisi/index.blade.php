@extends('layouts.app')

@section('content')
    <!-- DataTables & Responsive CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">

    <div class="container-fluid">
        <!-- Page Title Header -->
        <div class="page-title-head d-flex align-items-center flex-wrap gap-2 mb-4">
            <div class="flex-grow-1">
                <h4 class="page-main-title m-0 fw-semibold">
                    <i class="ti ti-building me-2 text-primary"></i>Master Divisi
                </h4>
            </div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb m-0 py-0">
                    <li class="breadcrumb-item">
                        <a href="{{ route('home') }}" class="text-decoration-none text-reset">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">Data Divisi</li>
                </ol>
            </nav>
        </div>

        <!-- Content Card -->
        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm border-0">
                    <!-- Card Header -->
                    <div class="card-header bg-white py-3 d-flex align-items-center flex-wrap gap-2">
                        <h4 class="card-title mb-0 fw-semibold">
                            <i class="ti ti-list me-2"></i>Data Divisi
                        </h4>
                        <div class="ms-auto">
                            <a href="{{ route('divisi.create') }}" class="btn btn-primary btn-sm d-flex align-items-center gap-1">
                                <i class="ti ti-plus"></i> Tambah Divisi
                            </a>
                        </div>
                    </div>


                    <!-- Card Body -->
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered dt-responsive nowrap align-middle mb-0"
                                id="divisiTable" style="width: 100%;">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width: 50px;" class="text-center">#</th>
                                        <th>Nama Divisi</th>
                                        <th>Keterangan</th>
                                        <th style="width: 100px;" class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {{-- Data dimuat via DataTables --}}
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- DataTables JS & Bootstrap 5 Integration -->
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
    <!-- Responsive Extension -->
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.min.js"></script>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $(document).ready(function() {
            // 1. Toast notification config
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer);
                    toast.addEventListener('mouseleave', Swal.resumeTimer);
                }
            });

            // 2. Show success toast from session
            @if (session('success'))
                Toast.fire({
                    icon: 'success',
                    title: '{{ session('success') }}'
                });
            @endif

            // 3. Delete Handler
            $('body').on('click', '.btn-delete', function() {
                const id = $(this).data('id');
                const nama = $(this).data('nama');

                Swal.fire({
                    title: 'Hapus Divisi?',
                    html: `Anda akan menghapus divisi:<br><strong class="text-primary">${nama}</strong><br>Tindakan ini tidak dapat dibatalkan!`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc3545',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ route('divisi.destroy', ':id') }}".replace(':id', id),
                            type: 'DELETE',
                            data: {
                                _token: '{{ csrf_token() }}'
                            },
                            beforeSend: function() {
                                Swal.fire({
                                    title: 'Menghapus...',
                                    text: 'Mohon tunggu sebentar',
                                    allowOutsideClick: false,
                                    allowEscapeKey: false,
                                    didOpen: () => Swal.showLoading()
                                });
                            },
                            success: function(response) {
                                if (response.status === 200 || response.success) {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Berhasil!',
                                        text: response.message || 'Data berhasil dihapus',
                                        timer: 2000,
                                        showConfirmButton: false
                                    });
                                    $('#divisiTable').DataTable().ajax.reload(null, false);
                                } else {
                                    Swal.fire('Gagal!', response.message || 'Terjadi kesalahan', 'error');
                                }
                            },
                            error: function(xhr) {
                                const message = xhr.responseJSON?.message || 'Terjadi kesalahan saat menghapus data.';
                                Swal.fire('Gagal!', message, 'error');
                            }
                        });
                    }
                });
            });

            // 4. DataTables Init
            $('#divisiTable').DataTable({
                responsive: true,
                serverSide: true,
                processing: true,
                destroy: true,
                autoWidth: false,
                ajax: {
                    url: "{{ route('divisi.index') }}",
                    type: 'GET'
                },
                language: {
                    processing: '<div class="spinner-border spinner-border-sm text-primary me-2" role="status"></div> Memuat data...',
                    paginate: {
                        next: '<i class="ti ti-chevron-right"></i>',
                        previous: '<i class="ti ti-chevron-left"></i>'
                    },
                    url: "//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json"
                },
                columnDefs: [
                    {
                        className: 'text-center',
                        targets: [0, 3] // Pusatkan kolom # (0) dan Aksi (3)
                    },
                    {
                        orderable: false,
                        targets: [0, 3] // Nonaktifkan sorting pada kolom # dan Aksi
                    }
                ],
                columns: [
                    {
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        searchable: false
                    },
                    {
                        data: 'Nama',
                        name: 'Nama',
                        render: function(data) {
                            return data ? `<span class="fw-bold text-primary"><i class="ti ti-building me-1 text-muted"></i>${data}</span>` : '<span class="text-muted">-</span>';
                        }
                    },
                    {
                        data: 'Keterangan',
                        name: 'Keterangan',
                        render: function(data) {
                            if (!data || data.trim() === '') {
                                return '<span class="text-muted fst-italic small">Tidak ada keterangan</span>';
                            }
                            // Batasi panjang keterangan di tabel agar tidak merusak layout
                            const maxLength = 60;
                            const shortData = data.length > maxLength ? data.substring(0, maxLength) + '...' : data;
                            return `<span class="text-muted small" title="${data}">${shortData}</span>`;
                        }
                    },
                    {
                        data: 'action',
                        name: 'action',
                        searchable: false
                    }
                ]
            });
        });
    </script>
@endpush
