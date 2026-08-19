@extends('layouts.app-manajemen-tenant')

@section('content')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">

    <div class="container-fluid">
        <div class="page-title-head d-flex align-items-center flex-wrap gap-2 mb-4">
            <div class="flex-grow-1">
                <h4 class="page-main-title m-0 fw-semibold">
                    <i class="ti ti-building-plus me-2 text-primary"></i>Pendaftaran Tenant
                </h4>
            </div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb m-0 py-0">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-decoration-none text-reset">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Data Pendaftaran</li>
                </ol>
            </nav>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0 fw-semibold"><i class="ti ti-list me-2"></i>Data Pendaftaran Tenant</h4>
                    </div>
                    <div class="card-body">

                        <!-- ✅ TAMBAHAN: Filter Row -->
                        <div class="row g-2 mb-3 align-items-end">
                            <div class="col-md-3">
                                <label class="form-label small fw-semibold text-muted">Tanggal Awal</label>
                                <input type="date" class="form-control form-control-sm" id="FilterTanggalAwal">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small fw-semibold text-muted">Tanggal Akhir</label>
                                <input type="date" class="form-control form-control-sm" id="FilterTanggalAkhir">
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex gap-2">
                                    <button type="button" id="BtnFilter" class="btn btn-primary btn-sm px-3">
                                        <i class="ti ti-filter me-1"></i> Tampilkan
                                    </button>
                                    <button type="button" id="BtnReset" class="btn btn-secondary btn-sm px-3" title="Reset Filter">
                                        <i class="ti ti-refresh me-1"></i> Reset
                                    </button>
                                </div>
                            </div>
                        </div>
                        <!-- ✅ AKHIR Filter Row -->

                        <div class="table-responsive">
                            <table class="table table-striped table-bordered dt-responsive nowrap align-middle mb-0" id="PendaftaranTable" style="width: 100%;">
                                <thead class="table-light">
                                    <tr>
                                        <th class="text-center">#</th>
                                        <th>Kode</th>
                                        <th>Nama Tenant</th>
                                        <th>Email Tenant</th>
                                        <th>Nama PIC</th>
                                        <th>Email PIC</th>
                                        <th class="text-center">Bukti Bayar</th>
                                        <th class="text-center">Status</th>
                                        <th class="text-center">Aksi</th>
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
    <!-- ✅ TAMBAHAN: SweetAlert2 untuk Konfirmasi Hapus -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $(document).ready(function() {
            // 1. DataTables Init
            $('#PendaftaranTable').DataTable({
                responsive: true,
                serverSide: true,
                processing: true,
                destroy: true,
                autoWidth: false,
                ajax: {
                    url: "{{ route('pendaftaran-tenant.index') }}",
                    type: 'GET',
                    // ✅ Kirim parameter filter ke server
                    data: function (Data) {
                        Data.TanggalAwal = $('#FilterTanggalAwal').val();
                        Data.TanggalAkhir = $('#FilterTanggalAkhir').val();
                    }
                },
                columnDefs: [
                    { className: 'text-center', targets: [0, 6, 7, 8] },
                    { orderable: false, targets: [0, 8] }
                ],
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', searchable: false },
                    { data: 'Kode', name: 'Kode' },
                    {
                        data: 'Nama', name: 'Nama',
                        render: function(Data) { return `<span class="fw-semibold text-dark">${Data}</span>`; }
                    },
                    { data: 'Email', name: 'Email' },
                    { data: 'NamaPIC', name: 'NamaPIC' },
                    { data: 'EmailPIC', name: 'EmailPIC' },
                    { data: 'BuktiPembayaran', name: 'BuktiPembayaran' },
                    { data: 'Status', name: 'Status' },
                    { data: 'action', name: 'action', searchable: false }
                ],
                language: { url: "//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json" }
            });

            // 2. Logic Filter & Reset
            $('#BtnFilter').on('click', function() {
                $('#PendaftaranTable').DataTable().ajax.reload();
            });

            $('#BtnReset').on('click', function() {
                $('#FilterTanggalAwal').val('');
                $('#FilterTanggalAkhir').val('');
                $('#PendaftaranTable').DataTable().ajax.reload();
            });

            // 3. ✅ Logic Hapus Data (Delete Handler)
            $('body').on('click', '.btn-hapus', function() {
                const Id = $(this).data('id');
                const Nama = $(this).data('nama');

                Swal.fire({
                    title: 'Hapus Pendaftaran?',
                    html: `Anda akan menghapus data pendaftaran untuk:<br><strong class="text-primary">${Nama}</strong><br>Tindakan ini tidak dapat dibatalkan!`,
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
                            url: "{{ route('pendaftaran-tenant.destroy', ':id') }}".replace(':id', Id),
                            type: 'POST', // Laravel method spoofing untuk DELETE
                            data: {
                                _method: 'DELETE',
                                _token: '{{ csrf_token() }}'
                            },
                            beforeSend: function() {
                                Swal.fire({ title: 'Menghapus...', text: 'Mohon tunggu sebentar', allowOutsideClick: false, didOpen: () => Swal.showLoading() });
                            },
                            success: function(Response) {
                                if (Response.success) {
                                    Swal.fire({ icon: 'success', title: 'Berhasil!', text: Response.message, timer: 2000, showConfirmButton: false });
                                    $('#PendaftaranTable').DataTable().ajax.reload(null, false);
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
