@extends('layouts.app')

@section('content')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />

    <div class="container-fluid">
        <div class="page-title-head d-flex align-items-center flex-wrap gap-2 mb-4">
            <div class="flex-grow-1">
                <h4 class="page-main-title m-0 fw-semibold">
                    <i class="ti ti-receipt-off me-2 text-primary"></i>Reimbursement
                </h4>
            </div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb m-0 py-0">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-decoration-none text-reset">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Data Reimbursement</li>
                </ol>
            </nav>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0 fw-semibold"><i class="ti ti-list me-2"></i>Data Pengajuan</h4>
                        <a href="{{ route('reimbursement.create') }}" class="btn btn-primary btn-sm d-flex align-items-center gap-1">
                            <i class="ti ti-plus"></i> Ajukan Reimbursement
                        </a>
                    </div>
                    <div class="card-body">
                        {{-- LETAKKAN FILTER DISINI --}}
                        <form id="filterForm" >
                            <div class="row g-2 align-items-end">
                                <div class="col-md-4">
                                    <label for="filterTanggal" class="form-label mb-1 fw-normal">Filter Tanggal</label>
                                    <input type="text" id="filterTanggal" class="form-control form-control-sm" autocomplete="off" placeholder="Pilih rentang tanggal">
                                </div>
                                <div class="col-md-3">
                                    <label for="filterStatus" class="form-label mb-1 fw-normal">Filter Status</label>
                                    <select id="filterStatus" class="form-select form-select-sm">
                                        <option value="">Semua Status</option>
                                        <option value="Menunggu">Menunggu</option>
                                        <option value="Ditolak">Ditolak</option>
                                        <option value="Dibayar">Dibayar</option>
                                    </select>
                                </div>
                                <div class="col-md-5 d-flex align-items-end gap-2">
                                    <button type="button" id="btnTampilkan" class="btn btn-primary btn-sm"><i class="ti ti-filter"></i> Tampilkan</button>
                                    <button type="button" id="btnReset" class="btn btn-secondary btn-sm"><i class="ti ti-refresh"></i> Reset</button>
                                    <button type="button" id="btnExport" class="btn btn-success btn-sm"><i class="ti ti-download"></i> Export</button>
                                </div>
                            </div>
                        </form>

                        <div class="table-responsive">
                            <table class="table table-striped table-bordered dt-responsive nowrap align-middle mb-0" id="reimbursementTable" style="width: 100%;">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width: 50px;" class="text-center">#</th>
                                        <th>Tanggal</th>
                                        <th>Nama</th>
                                        <th>Item</th>
                                        <th class="text-end">Nominal</th>
                                        <th class="text-center">Status</th>
                                        <th class="text-center">Bukti</th>
                                        <th style="width: 100px;" class="text-center">Aksi</th>
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
    <script src="https://cdn.jsdelivr.net/npm/moment@2.29.1/moment.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <!-- DataTables Buttons for Export -->
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>

    <script>
        $(document).ready(function() {
            const Toast = Swal.mixin({ toast: true, position: 'top-end', showConfirmButton: false, timer: 3000, timerProgressBar: true });

            @if (session('success'))
                Toast.fire({ icon: 'success', title: '{{ session('success') }}' });
            @endif

            // Init date range picker
            $('#filterTanggal').daterangepicker({
                autoUpdateInput: false,
                locale: {
                    cancelLabel: 'Reset',
                    applyLabel: 'Terapkan',
                    format: 'YYYY-MM-DD'
                }
            });

            $('#filterTanggal').on('apply.daterangepicker', function(ev, picker) {
                $(this).val(picker.startDate.format('YYYY-MM-DD') + ' s/d ' + picker.endDate.format('YYYY-MM-DD'));
            });

            $('#filterTanggal').on('cancel.daterangepicker', function(ev, picker) {
                $(this).val('');
            });

            // Tombol Reset: reset semua filter
            $('#btnReset').on('click', function() {
                $('#filterTanggal').val('');
                $('#filterStatus').val('');
                // juga reset date picker ke keadaan default
                $('#filterTanggal').data('daterangepicker').setStartDate(moment());
                $('#filterTanggal').data('daterangepicker').setEndDate(moment());
                $('#filterTanggal').data('daterangepicker').hide();
                $('#reimbursementTable').DataTable().ajax.reload();
            });

            // Tombol Tampilkan: reload datatable berdasarkan filter
            $('#btnTampilkan').on('click', function() {
                $('#reimbursementTable').DataTable().ajax.reload();
            });

            // Tombol Export
            $('#btnExport').on('click', function() {
                $('#reimbursementTable').DataTable().button('.buttons-excel').trigger();
            });

            // Trigger reload juga ketika enter di dalam input date atau select status
            $('#filterTanggal, #filterStatus').on('keyup', function(e) {
                if (e.keyCode === 13) {
                    $('#reimbursementTable').DataTable().ajax.reload();
                }
            });

            $('body').on('click', '.btn-delete', function() {
                const id = $(this).data('id');
                const nama = $(this).data('nama');

                Swal.fire({
                    title: 'Hapus Pengajuan?',
                    html: `Hapus data reimbursement atas nama:<br><strong class="text-primary">${nama}</strong>?`,
                    icon: 'warning', showCancelButton: true, confirmButtonColor: '#dc3545', cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Ya, Hapus!', cancelButtonText: 'Batal', reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ route('reimbursement.destroy', ':id') }}".replace(':id', id),
                            type: 'DELETE',
                            data: { _token: '{{ csrf_token() }}' },
                            success: function(response) {
                                if (response.status === 200 || response.success) {
                                    Swal.fire({ icon: 'success', title: 'Berhasil!', text: response.message, timer: 2000, showConfirmButton: false });
                                    $('#reimbursementTable').DataTable().ajax.reload(null, false);
                                } else {
                                    Swal.fire('Gagal!', response.message || 'Terjadi kesalahan', 'error');
                                }
                            },
                            error: function(xhr) {
                                Swal.fire('Gagal!', xhr.responseJSON?.message || 'Terjadi kesalahan.', 'error');
                            }
                        });
                    }
                });
            });

            let table = $('#reimbursementTable').DataTable({
                responsive: true,
                serverSide: true,
                processing: true,
                destroy: true,
                autoWidth: false,
                dom: 'Bfrtip',
                buttons: [
                    {
                        extend: "excelHtml5",
                        className: "d-none buttons-excel", // Hide by default, triggered by Export button
                        exportOptions: {
                            columns: ':not(:last-child)' // Exclude aksi column
                        }
                    }
                ],
                ajax: {
                    url: "{{ route('reimbursement.index') }}",
                    type: 'GET',
                    data: function (d) {
                        // Ambil filter tanggal
                        let tanggal = $('#filterTanggal').val();
                        let tanggal_awal = '';
                        let tanggal_akhir = '';
                        if (tanggal && tanggal.includes(' s/d ')) {
                            let arr = tanggal.split(' s/d ');
                            tanggal_awal = arr[0];
                            tanggal_akhir = arr[1];
                        }
                        d.tanggal_awal = tanggal_awal;
                        d.tanggal_akhir = tanggal_akhir;
                        // Ambil status
                        d.status = $('#filterStatus').val();
                    }
                },
                // ✅ Default sorting: Tanggal Terbaru ke Terlama (desc)
                order: [[1, 'desc']],
                language: {
                    processing: '<div class="spinner-border spinner-border-sm text-primary me-2" role="status"></div> Memuat data...',
                    paginate: { next: '<i class="ti ti-chevron-right"></i>', previous: '<i class="ti ti-chevron-left"></i>' },
                    url: "//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json"
                },
                columnDefs: [
                    { className: 'text-center', targets: [0, 5, 6, 7] },
                    { className: 'text-end', targets: [4] },
                    { orderable: false, targets: [0, 7] }
                ],
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', searchable: false },
                    {
                        data: 'Tanggal', name: 'Tanggal',
                        render: (data) => data ? new Date(data).toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' }) : '-'
                    },
                    { data: 'Nama', name: 'Nama', render: (data) => `<span class="fw-semibold text-dark">${data}</span>` },
                    { data: 'Item', name: 'Item' },
                    {
                        data: 'Nominal', name: 'Nominal',
                        render: (data) => new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(data || 0)
                    },
                    {
                        data: 'Status', name: 'Status',
                        render: (data) => {
                            let badge = 'bg-secondary';
                            if (data === 'Menunggu') badge = 'bg-warning text-dark';
                            if (data === 'Ditolak') badge = 'bg-danger';
                            if (data === 'Dibayar') badge = 'bg-success';
                            return `<span class="badge ${badge}">${data}</span>`;
                        }
                    },
                    {
                        data: 'BuktiUpload', name: 'BuktiUpload', orderable: false, searchable: false,
                        render: (data) => data ? `<a href="/storage/${data}" target="_blank" class="btn btn-sm btn-outline-primary"><i class="ti ti-eye"></i></a>` : '<span class="text-muted">-</span>'
                    },
                    { data: 'action', name: 'action', searchable: false }
                ]
            });
        });
    </script>
@endpush
