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

    <script>
        $(document).ready(function() {
            $('#PendaftaranTable').DataTable({
                responsive: true,
                serverSide: true,
                processing: true,
                destroy: true,
                autoWidth: false,
                ajax: { url: "{{ route('pendaftaran-tenant.index') }}", type: 'GET' },
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
        });
    </script>
@endpush
