@extends('layouts.app')

@section('content')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">

    <div class="container-fluid">
        <div class="page-title-head d-flex align-items-center flex-wrap gap-2 mb-4">
            <div class="flex-grow-1">
                <h4 class="page-main-title m-0 fw-semibold">
                    <i class="ti ti-motorbike me-2 text-primary"></i>Laporan Pekerjaan Kurir
                </h4>
            </div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb m-0 py-0">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-decoration-none text-reset">Dashboard</a></li>
                    <li class="breadcrumb-item active">Pekerjaan Kurir</li>
                </ol>
            </nav>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0 fw-semibold"><i class="ti ti-list me-2"></i>Data Aktivitas Harian</h4>
                        <a href="{{ route('pekerjaan-kurir.create') }}" class="btn btn-primary btn-sm d-flex align-items-center gap-1">
                            <i class="ti ti-plus"></i> Tambah Laporan
                        </a>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered dt-responsive nowrap align-middle mb-0" id="kurirTable" style="width: 100%;">
                                <thead class="table-light">
                                    <tr>
                                        <th class="text-center">#</th>
                                        <th>Tanggal & Jam</th>
                                        <th>Pekerjaan</th>
                                        <th>Rute (Dari - Tujuan)</th>
                                        <th class="text-center">Jml. Paket</th>
                                        <th>Durasi</th>
                                        <th class="text-center">Bukti</th>
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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $(document).ready(function() {
            const Toast = Swal.mixin({ toast: true, position: 'top-end', showConfirmButton: false, timer: 3000, timerProgressBar: true });
            @if (session('success')) Toast.fire({ icon: 'success', title: '{{ session('success') }}' }); @endif

            $('body').on('click', '.btn-delete', function() {
                const Id = $(this).data('id');
                const Tanggal = $(this).data('tanggal');
                Swal.fire({
                    title: 'Hapus Laporan?',
                    html: `Hapus data aktivitas tanggal <strong class="text-primary">${Tanggal}</strong>?`,
                    icon: 'warning', showCancelButton: true, confirmButtonColor: '#dc3545', cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Ya, Hapus!', cancelButtonText: 'Batal', reverseButtons: true
                }).then((Result) => {
                    if (Result.isConfirmed) {
                        $.ajax({
                            url: "{{ route('pekerjaan-kurir.destroy', ':id') }}".replace(':id', Id),
                            type: 'DELETE', data: { _token: '{{ csrf_token() }}' },
                            success: function(Response) {
                                if (Response.success) {
                                    Swal.fire({ icon: 'success', title: 'Berhasil!', text: Response.message, timer: 2000, showConfirmButton: false });
                                    $('#kurirTable').DataTable().ajax.reload(null, false);
                                } else {
                                    Swal.fire('Gagal!', Response.message, 'error');
                                }
                            }
                        });
                    }
                });
            });

            $('#kurirTable').DataTable({
                responsive: true, serverSide: true, processing: true, destroy: true, autoWidth: false,
                ajax: { url: "{{ route('pekerjaan-kurir.index') }}", type: 'GET' },
                columnDefs: [
                    { className: 'text-center', targets: [0, 4, 6, 7] },
                    { orderable: false, targets: [0, 7] }
                ],
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', searchable: false },
                    { data: 'Tanggal', name: 'Tanggal' },

                    { data: 'Pekerjaan', name: 'Pekerjaan' },
                    { data: 'DariLokasi', name: 'DariLokasi', render: function(Data, Type, Row) { return '<small class="d-block text-muted">Dari: ' + Data + '</small><small class="fw-semibold">Ke: ' + Row.Tujuan + '</small>'; } },
                    { data: 'JumlahPaket', name: 'JumlahPaket' },
                    { data: 'Durasi', name: 'Durasi' },
                    { data: 'BuktiFoto', name: 'BuktiFoto' },
                    { data: 'action', name: 'action', searchable: false }
                ],
                language: { url: "//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json" }
            });
        });
    </script>
@endpush
