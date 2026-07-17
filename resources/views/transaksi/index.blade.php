@extends('layouts.app')

@section('content')
    <!-- DataTables & Responsive CSS (Bawaan Bootstrap 5) -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">

    <div class="container-fluid">
        <!-- Page Title Header -->
        <div class="page-title-head d-flex align-items-center flex-wrap gap-2 mb-4">
            <div class="flex-grow-1">
                <h4 class="page-main-title m-0 fw-semibold">
                    <i class="ti ti-receipt me-2 text-primary"></i>Master Transaksi
                </h4>
            </div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb m-0 py-0">
                    <li class="breadcrumb-item">
                        <a href="{{ route('home') }}" class="text-decoration-none text-reset">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">Data Transaksi</li>
                </ol>
            </nav>
        </div>

        <!-- Content Card -->
        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm border-0">
                    <!-- Card Header -->
                    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0 fw-semibold">
                            <i class="ti ti-list me-2"></i>Data Transaksi
                        </h4>
                        <a href="{{ route('transaksi.create') }}" class="btn btn-primary btn-sm d-flex align-items-center gap-1">
                            <i class="ti ti-plus"></i> Tambah Transaksi
                        </a>
                    </div>

                    <!-- Card Body -->
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered dt-responsive nowrap align-middle mb-0"
                                id="transaksiTable" style="width: 100%;">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width: 50px;" class="text-center">#</th>
                                        <th>Kode Transaksi</th>
                                        <th>Tanggal</th>
                                        <th>Ekspedisi</th>
                                        <th>No. Resi</th>
                                        <th>Metode</th>
                                        <th class="text-end">Pendapatan</th>
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
                const kode = $(this).data('kode');

                Swal.fire({
                    title: 'Hapus Transaksi?',
                    html: `Anda akan menghapus transaksi dengan Kode:<br><strong class="text-primary">${kode}</strong><br>Tindakan ini tidak dapat dibatalkan!`,
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
                            url: "{{ route('transaksi.destroy', ':id') }}".replace(':id', id),
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
                                    $('#transaksiTable').DataTable().ajax.reload(null, false);
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
            $('#transaksiTable').DataTable({
                responsive: true,
                serverSide: true,
                processing: true,
                destroy: true,
                autoWidth: false,
                ajax: {
                    url: "{{ route('transaksi.index') }}",
                    type: 'GET'
                },

                columnDefs: [
                    {
                        className: 'text-center',
                        targets: [0, 7] // Pusatkan kolom # (0) dan Aksi (7)
                    },
                    {
                        className: 'text-end',
                        targets: [6] // Rata kanan untuk kolom Pendapatan (6)
                    },
                    {
                        orderable: false,
                        targets: [0, 7] // Nonaktifkan sorting pada kolom # dan Aksi
                    }
                ],
                columns: [
                    {
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        searchable: false
                    },
                    {
                        data: 'KodeTransaksi',
                        name: 'KodeTransaksi',
                        render: function(data) {
                            return data ? `<span class="fw-bold text-primary">${data}</span>` : '<span class="text-muted">-</span>';
                        }
                    },
                    {
                        data: 'Tanggal',
                        name: 'Tanggal',
                        render: function(data) {
                            if (!data) return '<span class="text-muted">-</span>';
                            // Format: 16 Jul 2026 14:30
                            const date = new Date(data);
                            return date.toLocaleDateString('id-ID', {
                                day: '2-digit', month: 'short', year: 'numeric',
                                hour: '2-digit', minute: '2-digit'
                            });
                        }
                    },
                    {
                        data: 'Ekspedisi',
                        name: 'Ekspedisi',
                        render: function(data) {
                            return data ? `<span class="fw-semibold">${data}</span>` : '<span class="text-muted">-</span>';
                        }
                    },
                    {
                        data: 'NoResi',
                        name: 'NoResi',
                        render: function(data) {
                            return data ? `<span class="font-monospace small">${data}</span>` : '<span class="text-muted">-</span>';
                        }
                    },
                    {
                        data: 'Metode',
                        name: 'Metode',
                        render: function(data) {
                            if (!data) return '<span class="badge bg-secondary">-</span>';

                            const badgeClass = data === 'Tunai' ? 'bg-success' : 'bg-info text-dark';
                            const icon = data === 'Tunai' ? 'ti ti-cash' : 'ti ti-credit-card';

                            return `<span class="badge ${badgeClass}"><i class="${icon} me-1"></i>${data}</span>`;
                        }
                    },
                    {
                        data: 'Pendapatan',
                        name: 'Pendapatan',
                        render: function(data) {
                            // Format otomatis ke Rupiah (contoh: Rp 150.000)
                            return new Intl.NumberFormat('id-ID', {
                                style: 'currency',
                                currency: 'IDR',
                                minimumFractionDigits: 0
                            }).format(data || 0);
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
