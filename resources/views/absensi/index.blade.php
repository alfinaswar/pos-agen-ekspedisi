@extends('layouts.app')

@section('content')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">

    <div class="container-fluid">
        <div class="page-title-head d-flex align-items-center flex-wrap gap-2 mb-4">
            <div class="flex-grow-1">
                <h4 class="page-main-title m-0 fw-semibold">
                    <i class="ti ti-clock-hour-9 me-2 text-primary"></i>Master Absensi
                </h4>
            </div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb m-0 py-0">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}"
                            class="text-decoration-none text-reset">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Data Absensi</li>
                </ol>
            </nav>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0 fw-semibold"><i class="ti ti-list me-2"></i>Data Absensi</h4>
                        <a href="{{ route('absensi.create') }}"
                            class="btn btn-primary btn-sm d-flex align-items-center gap-1">
                            <i class="ti ti-plus"></i> Tambah Absensi
                        </a>
                    </div>
                    <div class="card-body">

                        <!-- === FILTER ROW === -->
                        <div class="row mb-3 g-2">
                            <!-- Filter Bulan -->
                            <div class="col-md-3">
                                <label class="form-label small fw-semibold text-muted">Bulan</label>
                                <select class="form-select form-select-sm" id="filter_bulan">
                                    <option value="">Semua Bulan</option>
                                    <option value="1">Januari</option>
                                    <option value="2">Februari</option>
                                    <option value="3">Maret</option>
                                    <option value="4">April</option>
                                    <option value="5">Mei</option>
                                    <option value="6">Juni</option>
                                    <option value="7">Juli</option>
                                    <option value="8">Agustus</option>
                                    <option value="9">September</option>
                                    <option value="10">Oktober</option>
                                    <option value="11">November</option>
                                    <option value="12">Desember</option>
                                </select>
                            </div>

                            <!-- Filter Status -->
                            <div class="col-md-3">
                                <label class="form-label small fw-semibold text-muted">Status</label>
                                <select class="form-select form-select-sm" id="filter_status">
                                    <option value="">Semua Status</option>
                                    <option value="H">Hadir (H)</option>
                                    <option value="I">Izin (I)</option>
                                    <option value="S">Sakit (S)</option>
                                    <option value="TK">Tanpa Keterangan (TK)</option>
                                </select>
                            </div>

                            <!-- Filter User (HANYA UNTUK ADMIN) -->
                            @if (auth()->check() && auth()->user()->role === 'Admin')
                                {{-- Sesuaikan 'role' dengan kolom di tabel users Anda --}}
                                <div class="col-md-3">
                                    <label class="form-label small fw-semibold text-muted">Nama Karyawan</label>
                                    <select class="form-select form-select-sm" id="filter_user">
                                        <option value="">Semua Karyawan</option>
                                        @foreach ($users as $user)
                                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            @endif

                            <!-- Tombol Aksi Filter -->
                            <div
                                class="col-md-{{ auth()->check() && auth()->user()->role === 'Admin' ? '3' : '6' }} d-flex align-items-end gap-2">
                                <button class="btn btn-sm btn-secondary" id="filter_reset" title="Reset Filter">
                                    <i class="ti ti-refresh"></i> Reset
                                </button>
                                <button class="btn btn-sm btn-primary" id="filter_submit" title="Terapkan Filter">
                                    <i class="ti ti-filter"></i> Tampilkan
                                </button>
                                <!-- Export Excel Button -->
                                <button class="btn btn-sm btn-success" id="btn_export_excel" title="Export ke Excel">
                                    <i class="ti ti-file-spreadsheet"></i> Export Excel
                                </button>
                            </div>
                        </div>
                        <!-- === END FILTER ROW === -->

                        <div class="table-responsive">
                            <table class="table table-striped table-bordered dt-responsive nowrap align-middle mb-0"
                                id="absensiTable" style="width: 100%;">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width: 50px;" class="text-center">#</th>
                                        <th>Nama</th>
                                        <th>Divisi</th>
                                        <th>Tanggal</th>
                                        <th class="text-center">Status</th>
                                        <th>Jam Hadir</th>
                                        <th>Jam Pulang</th>
                                        <th class="text-center">Lembur</th>
                                        <th>Durasi Lembur</th>
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

    <script>
        $(document).ready(function() {
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true
            });

            @if (session('success'))
                Toast.fire({
                    icon: 'success',
                    title: '{{ session('success') }}'
                });
            @endif
            $('#btn_export_excel').on('click', function() {
                const bulan = $('#filter_bulan').val();
                const status = $('#filter_status').val();
                const user_name = $('#filter_user').val();

                let url = "{{ route('absensi.export') }}";
                let params = [];

                if (bulan) params.push(`bulan=${bulan}`);
                if (status) params.push(`status=${status}`);
                if (user_name) params.push(`user_name=${user_name}`);

                if (params.length > 0) {
                    url += '?' + params.join('&');
                }

                window.location.href = url;
            });
            // 1. Delete Handler
            $('body').on('click', '.btn-delete', function() {
                const id = $(this).data('id');
                const nama = $(this).data('nama');

                Swal.fire({
                    title: 'Hapus Data?',
                    html: `Anda akan menghapus data absensi:<br><strong class="text-primary">${nama}</strong><br>Tindakan ini tidak dapat dibatalkan!`,
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
                            url: "{{ route('absensi.destroy', ':id') }}".replace(':id', id),
                            type: 'DELETE',
                            data: {
                                _token: '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                if (response.status === 200 || response.success) {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Berhasil!',
                                        text: response.message,
                                        timer: 2000,
                                        showConfirmButton: false
                                    });
                                    $('#absensiTable').DataTable().ajax.reload(null,
                                        false);
                                } else {
                                    Swal.fire('Gagal!', response.message ||
                                        'Terjadi kesalahan', 'error');
                                }
                            },
                            error: function(xhr) {
                                Swal.fire('Gagal!', xhr.responseJSON?.message ||
                                    'Terjadi kesalahan saat menghapus data.',
                                    'error');
                            }
                        });
                    }
                });
            });

            // 2. DataTables Init dengan Filter
            const absensiTable = $('#absensiTable').DataTable({
                responsive: true,
                serverSide: true,
                processing: true,
                destroy: true,
                autoWidth: false,
                ajax: {
                    url: "{{ route('absensi.index') }}",
                    type: 'GET',
                    data: function(d) {
                        // Kirim nilai filter ke server
                        d.bulan = $('#filter_bulan').val();
                        d.status = $('#filter_status').val();
                        d.user_name = $('#filter_user').val();
                    }
                },
                language: {
                    processing: '<div class="spinner-border spinner-border-sm text-primary me-2" role="status"></div> Memuat data...',
                    paginate: {
                        next: '<i class="ti ti-chevron-right"></i>',
                        previous: '<i class="ti ti-chevron-left"></i>'
                    },
                    url: "//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json"
                },
                columnDefs: [{
                        className: 'text-center',
                        targets: [0, 4, 7, 9]
                    },
                    {
                        orderable: false,
                        targets: [0, 9]
                    }
                ],
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        searchable: false
                    },
                    {
                        data: 'Nama',
                        name: 'Nama',
                        render: (data) => `<span class="fw-semibold text-dark">${data}</span>`
                    },
                    {
                        data: 'Divisi',
                        name: 'Divisi'
                    },
                    {
                        data: 'Tanggal',
                        name: 'Tanggal',
                        render: (data) => data ? new Date(data).toLocaleDateString('id-ID', {
                            day: '2-digit',
                            month: 'short',
                            year: 'numeric'
                        }) : '-'
                    },
                    {
                        data: 'Status',
                        name: 'Status',
                        render: (data) => {
                            const badges = {
                                'H': 'bg-success',
                                'I': 'bg-primary',
                                'S': 'bg-warning text-dark',
                                'TK': 'bg-danger'
                            };
                            const labels = {
                                'H': 'Hadir',
                                'I': 'Izin',
                                'S': 'Sakit',
                                'TK': 'Tanpa Ket.'
                            };
                            return `<span class="badge ${badges[data] || 'bg-secondary'}">${labels[data] || data}</span>`;
                        }
                    },
                    {
                        data: 'JamHadir',
                        name: 'JamHadir',
                        render: (data) => data ?
                            `<span class="badge bg-success bg-opacity-10 text-success"><i class="ti ti-login me-1"></i>${data}</span>` :
                            '<span class="text-muted">-</span>'
                    },
                    {
                        data: 'JamPulang',
                        name: 'JamPulang',
                        render: (data) => data ?
                            `<span class="badge bg-danger bg-opacity-10 text-danger"><i class="ti ti-logout me-1"></i>${data}</span>` :
                            '<span class="text-muted">-</span>'
                    },
                    {
                        data: 'Lembur',
                        name: 'Lembur',
                        render: (data) => data === 'Y' ?
                            '<span class="badge bg-warning text-dark">Ya</span>' :
                            '<span class="badge bg-secondary">Tidak</span>'
                    },
                    {
                        data: null,
                        name: 'DurasiLembur',
                        orderable: false,
                        searchable: false,
                        render: (data) => {
                            if (data.Lembur === 'Y' && (data.MulaiLembur || data.SelesaiLembur)) {
                                return `<small class="text-muted">${data.MulaiLembur || '-'} s/d ${data.SelesaiLembur || '-'}</small>`;
                            }
                            return '<span class="text-muted">-</span>';
                        }
                    },
                    {
                        data: 'action',
                        name: 'action',
                        searchable: false
                    }
                ]
            });

            // 3. Event Listener untuk Filter
            $('#filter_submit').on('click', function() {
                absensiTable.ajax.reload();
            });

            $('#filter_reset').on('click', function() {
                $('#filter_bulan').val('');
                $('#filter_status').val('');
                $('#filter_user').val('');
                absensiTable.ajax.reload();
            });
        });
    </script>
@endpush
