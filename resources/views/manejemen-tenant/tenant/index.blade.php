@extends('layouts.app-manajemen-tenant')

@section('content')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">

    <div class="container-fluid">
        <div class="page-title-head d-flex align-items-center flex-wrap gap-2 mb-4">
            <div class="flex-grow-1">
                <h4 class="page-main-title m-0 fw-semibold">
                    <i class="ti ti-building me-2 text-primary"></i>Master Tenant
                </h4>
            </div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb m-0 py-0">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-decoration-none text-reset">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Data Tenant</li>
                </ol>
            </nav>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0 fw-semibold"><i class="ti ti-list me-2"></i>Data Tenant</h4>
                        <div class="ms-auto">
                            {{-- <a href="{{ route('tenant.create') }}" class="btn btn-primary btn-sm d-flex align-items-center gap-1">
                                <i class="ti ti-plus"></i> Tambah Tenant
                            </a> --}}
                        </div>
                    </div>

                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered dt-responsive nowrap align-middle mb-0"
                                id="TenantTable" style="width: 100%;">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width: 50px;" class="text-center">#</th>
                                        <th>Nama Tenant</th>
                                        <th>Kode</th>
                                        <th>Tanggal Join</th>
                                        <th class="text-center">Status Subscription</th>
                                        <th>Kode Referal</th>
                                        <th>Tanggal Mulai Subscription</th>
                                        <th>Tanggal Akhir Subscription</th>
                                        <th style="width: 120px;" class="text-center">Aksi</th>
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
            const Toast = Swal.mixin({ toast: true, position: 'top-end', showConfirmButton: false, timer: 3000, timerProgressBar: true });

            @if (session('success'))
                Toast.fire({ icon: 'success', title: '{{ session('success') }}' });
            @endif

            // Delete Handler
            $('body').on('click', '.btn-delete', function() {
                const Id = $(this).data('id');
                const Nama = $(this).data('nama');

                Swal.fire({
                    title: 'Hapus Tenant?',
                    html: `Anda akan menghapus data tenant:<br><strong class="text-primary">${Nama}</strong><br>Tindakan ini tidak dapat dibatalkan!`,
                    icon: 'warning', showCancelButton: true, confirmButtonColor: '#dc3545', cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Ya, Hapus!', cancelButtonText: 'Batal', reverseButtons: true
                }).then((Result) => {
                    if (Result.isConfirmed) {
                        $.ajax({
                            url: "{{ route('tenant.destroy', ':id') }}".replace(':id', Id),
                            type: 'DELETE',
                            data: { _token: '{{ csrf_token() }}' },
                            beforeSend: function() {
                                Swal.fire({ title: 'Menghapus...', text: 'Mohon tunggu sebentar', allowOutsideClick: false, didOpen: () => Swal.showLoading() });
                            },
                            success: function(Response) {
                                if (Response.status === 200 || Response.success) {
                                    Swal.fire({ icon: 'success', title: 'Berhasil!', text: Response.message, timer: 2000, showConfirmButton: false });
                                    $('#TenantTable').DataTable().ajax.reload(null, false);
                                } else {
                                    Swal.fire('Gagal!', Response.message || 'Terjadi kesalahan', 'error');
                                }
                            },
                            error: function(Xhr) {
                                const Message = Xhr.responseJSON?.message || 'Terjadi kesalahan saat menghapus data.';
                                Swal.fire('Gagal!', Message, 'error');
                            }
                        });
                    }
                });
            });

            // DataTables Init
            $('#TenantTable').DataTable({
                responsive: true, serverSide: true, processing: true, destroy: true, autoWidth: false,
                ajax: { url: "{{ route('tenant.index') }}", type: 'GET' },
                columnDefs: [
                    { className: 'text-center', targets: [0, 4, 8] },
                    { orderable: false, targets: [0, 8] }
                ],
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', searchable: false },
                    { data: 'Nama', name: 'Nama', render: function(Data) { return `<span class="fw-semibold text-dark">${Data}</span>`; } },
                    { data: 'Kode', name: 'Kode' },
                    { data: 'TanggalJoin', name: 'TanggalJoin' },
                    { data: 'StatusSubscription', name: 'StatusSubscription' },
                    { data: 'KodeReferal', name: 'KodeReferal', render: function(Data) { return Data ? `<span class="badge bg-light text-dark border">${Data}</span>` : '<span class="text-muted">-</span>'; } },
                    { data: 'TanggalMulaiSubscription', name: 'TanggalMulaiSubscription' },
                    { data: 'TanggalAkhirSubscription', name: 'TanggalAkhirSubscription' },
                    { data: 'action', name: 'action', searchable: false }
                ],
                language: { url: "//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json" }
            });
        });
    </script>
@endpush
