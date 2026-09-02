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
                        <div class="ms-auto">
                            <a href="{{ route('reimbursement.create') }}" class="btn btn-primary btn-sm d-flex align-items-center gap-1">
                                <i class="ti ti-plus"></i> Ajukan Reimbursement
                            </a>
                        </div>
                    </div>

                    <div class="card-body">
                        {{-- FORM FILTER --}}
                        <form id="FilterForm">
                            <div class="row g-2 align-items-end mb-3">
                                {{-- Filter Tanggal --}}
                                <div class="col-md-3">
                                    <label for="FilterTanggal" class="form-label mb-1 fw-normal">Filter Tanggal</label>
                                    <input type="text" id="FilterTanggal" class="form-control form-control-sm" autocomplete="off" placeholder="Pilih rentang tanggal">
                                </div>

                                {{-- ✅ Filter Nama (BARU) --}}
                                @if(in_array(auth()->user()->role, ['Admin', 'Superadmin']))
                                <div class="col-md-3">
                                    <label for="FilterNama" class="form-label mb-1 fw-normal">Filter Nama</label>
                                    <select id="FilterNama" class="form-select form-select-sm">
                                        <option value="">Semua Nama</option>
                                        @foreach($users as $User)
                                            <option value="{{ $User->id }}">{{ $User->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                @endif


                                {{-- Filter Status --}}
                                <div class="col-md-2">
                                    <label for="FilterStatus" class="form-label mb-1 fw-normal">Filter Status</label>
                                    <select id="FilterStatus" class="form-select form-select-sm">
                                        <option value="">Semua Status</option>
                                        <option value="Menunggu">Menunggu</option>
                                        <option value="Ditolak">Ditolak</option>
                                        <option value="Dibayar">Dibayar</option>
                                    </select>
                                </div>

                                {{-- Action Buttons --}}
                                <div class="col-md-4 d-flex align-items-end gap-2">
                                    <button type="button" id="BtnTampilkan" class="btn btn-primary btn-sm"><i class="ti ti-filter"></i> Tampilkan</button>
                                    <button type="button" id="BtnReset" class="btn btn-secondary btn-sm"><i class="ti ti-refresh"></i> Reset</button>
                                    <button type="button" id="BtnExport" class="btn btn-success btn-sm"><i class="ti ti-download"></i> Export</button>
                                </div>
                            </div>
                        </form>

                        <div class="table-responsive">
                            <table class="table table-striped table-bordered dt-responsive nowrap align-middle mb-0" id="ReimbursementTable" style="width: 100%;">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width: 50px;" class="text-center">#</th>
                                        <th>Tanggal</th>
                                        <th>Nama</th>
                                        <th>Item</th>
                                        <th class="text-end">Nominal</th>
                                        <th class="text-center">Status</th>
                                        <th class="text-center">Bukti Pengajuan</th>
                                        <th class="text-center">Bukti Transfer</th>
                                        <th style="width: 100px;" class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <div id="TableInfoWrapper"></div>
                            <div id="TablePaginationWrapper"></div>
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

            // 1. Init Date Range Picker
            $('#FilterTanggal').daterangepicker({
                autoUpdateInput: false,
                locale: {
                    cancelLabel: 'Reset',
                    applyLabel: 'Terapkan',
                    format: 'YYYY-MM-DD'
                }
            });

            $('#FilterTanggal').on('apply.daterangepicker', function(Event, Picker) {
                $(this).val(Picker.startDate.format('YYYY-MM-DD') + ' s/d ' + Picker.endDate.format('YYYY-MM-DD'));
            });

            $('#FilterTanggal').on('cancel.daterangepicker', function(Event, Picker) {
                $(this).val('');
            });

            // 2. DataTables Initialization
            let Table = $('#ReimbursementTable').DataTable({
                responsive: true,
                serverSide: true,
                processing: true,
                destroy: true,
                autoWidth: false,
                dom: "<'row'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6'f>>" +
                     "rt" +
                     "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
                buttons: [
                    {
                        extend: "excelHtml5",
                        className: "d-none buttons-excel",
                        exportOptions: {
                            columns: ':not(:last-child)'
                        }
                    }
                ],
                ajax: {
                    url: "{{ route('reimbursement.index') }}",
                    type: 'GET',
                    data: function (Data) {
                        // ✅ Ambil filter tanggal
                        let Tanggal = $('#FilterTanggal').val();
                        let TanggalAwal = '';
                        let TanggalAkhir = '';
                        if (Tanggal && Tanggal.includes(' s/d ')) {
                            let Arr = Tanggal.split(' s/d ');
                            TanggalAwal = Arr[0];
                            TanggalAkhir = Arr[1];
                        }
                        Data.tanggal_awal = TanggalAwal;
                        Data.tanggal_akhir = TanggalAkhir;

                        // ✅ Ambil filter Nama (BARU)
                        Data.nama = $('#FilterNama').val();

                        // ✅ Ambil filter Status
                        Data.status = $('#FilterStatus').val();
                    }
                },
                order: [[1, 'desc']],
                language: {
                    processing: '<div class="spinner-border spinner-border-sm text-primary me-2" role="status"></div> Memuat data...',
                    paginate: { next: '<i class="ti ti-chevron-right"></i>', previous: '<i class="ti ti-chevron-left"></i>' },
                    url: "//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json"
                },
                columnDefs: [
                    { className: 'text-center', targets: [0, 5, 6, 7, 8] },
                    { className: 'text-end', targets: [4] },
                    { orderable: false, targets: [0, 8] }
                ],
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', searchable: false },
                    {
                        data: 'Tanggal', name: 'Tanggal',
                        render: (Data) => Data ? new Date(Data).toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' }) : '-'
                    },
                    { data: 'Nama', name: 'Nama', render: (Data) => `<span class="fw-semibold text-dark">${Data}</span>` },
                    { data: 'Item', name: 'Item' },
                    {
                        data: 'Nominal', name: 'Nominal',
                        render: (Data) => new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(Data || 0)
                    },
                    {
                        data: 'Status', name: 'Status',
                        render: (Data) => {
                            let Badge = 'bg-secondary';
                            if (Data === 'Menunggu') Badge = 'bg-warning text-dark';
                            if (Data === 'Ditolak') Badge = 'bg-danger';
                            if (Data === 'Dibayar') Badge = 'bg-success';
                            return `<span class="badge ${Badge}">${Data}</span>`;
                        }
                    },
                    {
                        data: 'BuktiUpload', name: 'BuktiUpload', orderable: false, searchable: false,
                        render: (Data) => Data ? `<a href="/storage/${Data}" target="_blank" class="btn btn-sm btn-outline-primary" title="Lihat Bukti Pengajuan"><i class="ti ti-eye"></i></a>` : '<span class="text-muted">-</span>'
                    },
                    {
                        data: 'BuktiTransfer', name: 'BuktiTransfer', orderable: false, searchable: false,
                        render: (Data) => Data ? `<a href="/storage/${Data}" target="_blank" class="btn btn-sm btn-outline-success" title="Lihat Bukti Transfer"><i class="ti ti-eye"></i></a>` : '<span class="text-muted">-</span>'
                    },
                    { data: 'action', name: 'action', searchable: false }
                ],
                lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
                pageLength: 10,
                drawCallback: function(Settings) {
                    var Info = $('#ReimbursementTable_wrapper .dataTables_info');
                    $('#TableInfoWrapper').empty().append(Info);
                    var Pagination = $('#ReimbursementTable_wrapper .dataTables_paginate');
                    $('#TablePaginationWrapper').empty().append(Pagination);
                }
            });

            // 3. Tombol Tampilkan: reload datatable berdasarkan filter
            $('#BtnTampilkan').on('click', function() {
                Table.ajax.reload();
            });

            // 4. Tombol Reset: reset semua filter
            $('#BtnReset').on('click', function() {
                $('#FilterTanggal').val('');
                $('#FilterNama').val('');
                $('#FilterStatus').val('');
                $('#FilterTanggal').data('daterangepicker').setStartDate(moment());
                $('#FilterTanggal').data('daterangepicker').setEndDate(moment());
                $('#FilterTanggal').data('daterangepicker').hide();
                Table.ajax.reload();
            });

            // 5. Tombol Export
            $('#BtnExport').on('click', function() {
                Table.button('.buttons-excel').trigger();
            });

            // 6. Trigger reload ketika enter di input
            $('#FilterTanggal, #FilterNama, #FilterStatus').on('keyup', function(Event) {
                if (Event.keyCode === 13) {
                    Table.ajax.reload();
                }
            });

            // 7. Logic Hapus Data
            $('body').on('click', '.btn-delete', function() {
                const Id = $(this).data('id');
                const Nama = $(this).data('nama');

                Swal.fire({
                    title: 'Hapus Pengajuan?',
                    html: `Hapus data reimbursement atas nama:<br><strong class="text-primary">${Nama}</strong>?`,
                    icon: 'warning', showCancelButton: true, confirmButtonColor: '#dc3545', cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Ya, Hapus!', cancelButtonText: 'Batal', reverseButtons: true
                }).then((Result) => {
                    if (Result.isConfirmed) {
                        $.ajax({
                            url: "{{ route('reimbursement.destroy', ':id') }}".replace(':id', Id),
                            type: 'DELETE',
                            data: { _token: '{{ csrf_token() }}' },
                            success: function(Response) {
                                if (Response.status === 200 || Response.success) {
                                    Swal.fire({ icon: 'success', title: 'Berhasil!', text: Response.message, timer: 2000, showConfirmButton: false });
                                    Table.ajax.reload(null, false);
                                } else {
                                    Swal.fire('Gagal!', Response.message || 'Terjadi kesalahan', 'error');
                                }
                            },
                            error: function(Xhr) {
                                Swal.fire('Gagal!', Xhr.responseJSON?.message || 'Terjadi kesalahan.', 'error');
                            }
                        });
                    }
                });
            });

            // 8. Pindahkan info & pagination setelah draw pertama
            setTimeout(function() {
                var Info = $('#ReimbursementTable_wrapper .dataTables_info');
                $('#TableInfoWrapper').empty().append(Info);
                var Pagination = $('#ReimbursementTable_wrapper .dataTables_paginate');
                $('#TablePaginationWrapper').empty().append(Pagination);
            }, 300);
        });
    </script>
@endpush
