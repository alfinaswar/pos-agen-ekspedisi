@extends('layouts.app')

@section('content')
@push('styles')
<style>
    /* Styling Profesional untuk Popup Catatan Finance */
    .finance-note-popup {
        text-align: left;
    }
    .finance-note-popup .note-header {
        display: flex;
        align-items: center;
        gap: 15px;
        padding-bottom: 15px;
        border-bottom: 1px solid #e9ecef;
        margin-bottom: 20px;
    }
    .finance-note-popup .icon-box {
        width: 48px;
        height: 48px;
        background: linear-gradient(135deg, #fff3cd 0%, #ffe69c 100%);
        color: #856404;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        flex-shrink: 0;
    }
    .finance-note-popup .header-text h6 {
        font-size: 1.1rem;
        letter-spacing: -0.01em;
    }
    .finance-note-popup .note-body {
        background: #f8f9fa;
        border-left: 4px solid #ffc107; /* Accent warna kuning finance */
        padding: 16px 20px;
        border-radius: 0 8px 8px 0;
        margin-bottom: 20px;
    }
    .finance-note-popup .note-body p {
        font-size: 0.95rem;
        line-height: 1.6;
        color: #343a40;
        margin: 0;
        font-style: italic;
    }
    .finance-note-popup .note-footer {
        font-size: 0.8rem;
        color: #6c757d;
        display: flex;
        align-items: center;
        background: #e9ecef;
        padding: 10px 15px;
        border-radius: 8px;
    }

    /* Override Default SweetAlert agar lebih clean */
    .swal2-finance-note {
        border-radius: 16px !important;
        padding: 0 !important;
        overflow: hidden;
        box-shadow: 0 10px 40px rgba(0,0,0,0.15) !important;
        border: none !important;
    }
    .swal2-finance-note .swal2-title {
        display: none !important; /* Kita pakai custom header */
    }
    .swal2-finance-note .swal2-html-container {
        margin: 0 !important;
        padding: 25px 30px 30px 30px !important;
    }
    .swal2-finance-note .swal2-confirm {
        background-color: #212529 !important; /* Dark professional button */
        color: #fff !important;
        border-radius: 8px !important;
        padding: 10px 28px !important;
        font-weight: 600 !important;
        font-size: 0.9rem !important;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1) !important;
        transition: all 0.2s ease !important;
    }
    .swal2-finance-note .swal2-confirm:hover {
        background-color: #000 !important;
        transform: translateY(-1px);
    }
</style>
@endpush
    @php
        $canBulkVerify = auth()->check() && in_array(auth()->user()->role, ['Admin', 'Finance']);
    @endphp
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">
    <!-- Date Range Picker CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />

    <div class="container-fluid">
        <div class="page-title-head d-flex align-items-center flex-wrap gap-2 mb-4">
            <div class="flex-grow-1">
                <h4 class="page-main-title m-0 fw-semibold">
                    <i class="ti ti-receipt me-2 text-primary"></i>Master Transaksi
                </h4>
            </div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb m-0 py-0">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}"
                            class="text-decoration-none text-reset">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Data Transaksi</li>
                </ol>
            </nav>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm border-0">
                    <div
                        class="card-header bg-white py-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <h4 class="card-title mb-0 fw-semibold"><i class="ti ti-list me-2"></i>Data Transaksi</h4>
                        <div class="d-flex gap-2">
                            @if (auth()->check() && in_array(auth()->user()->role, ['Admin', 'Finance']))
                                <button type="button" class="btn btn-warning btn-sm d-flex align-items-center gap-1"
                                    id="btn_bulk_verify" disabled>
                                    <i class="ti ti-checklist"></i> Verifikasi Terpilih (<span id="selected_count">0</span>)
                                </button>
                            @endif
                            <a href="{{ route('transaksi.create') }}"
                                class="btn btn-primary btn-sm d-flex align-items-center gap-1">
                                <i class="ti ti-plus me-1"></i> Tambah Transaksi
                            </a>
                        </div>
                    </div>

                    <div class="card-body">
                        <!-- Modern Filter Row -->
                        <div class="row mb-3 g-2">
                            <!-- Date Range Picker -->
                            <div class="col-md-2">
                                <label class="form-label small fw-semibold text-muted">Periode Tanggal</label>
                                <input type="text" id="filter_tanggal_range" class="form-control form-control-sm" placeholder="Pilih rentang tanggal" autocomplete="off">
                            </div>

                            <!-- ✅ FILTER BARU: Status Verifikasi -->
                            <div class="col-md-2">
                                <label class="form-label small fw-semibold text-muted">Status Verifikasi</label>
                                <select id="filter_status_verifikasi" class="form-select form-select-sm">
                                    <option value="">Semua Status</option>
                                    <option value="Y">Sudah Verif (Valid)</option>
                                    <option value="N">Tidak Valid</option>
                                    <option value="N/A">Belum Verif</option>
                                </select>
                            </div>

                            <!-- Metode -->
                            <div class="col-md-2">
                                <label class="form-label small fw-semibold text-muted">Metode</label>
                                <select id="filter_metode" class="form-select form-select-sm">
                                    <option value="">Semua Metode</option>
                                    <option value="Tunai">Tunai</option>
                                    <option value="COD">COD</option>
                                    <option value="Tagihan">Tagihan</option>
                                    <option value="Qris">Qris</option>
                                    <option value="Transfer">Transfer</option>
                                </select>

                            </div>

                            <!-- Ekspedisi -->
                            <div class="col-md-2">
                                <label class="form-label small fw-semibold text-muted">Ekspedisi</label>
                                <select id="filter_ekspedisi" class="form-select form-select-sm">
                                    <option value="">Semua Ekspedisi</option>
                                    @foreach ($ekspedisi as $eks)
                                        <option value="{{ $eks->id }}">{{ $eks->NamaEkspedisi }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- User (Hanya untuk Admin/Leader) -->
                            @if (auth()->check() && (auth()->user()->role === 'Admin' || auth()->user()->role === 'Leader'))
                                <div class="col-md-2">
                                    <label class="form-label small fw-semibold text-muted">User Input</label>
                                    <select id="filter_user" class="form-select form-select-sm">
                                        <option value="">Semua User</option>
                                        @foreach ($users as $user)
                                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            @endif

                            <!-- Action Buttons -->
                            @php $btnCol = (auth()->check() && (auth()->user()->role === 'Admin' || auth()->user()->role === 'Leader')) ? '2' : '4'; @endphp

                            <div class="col-md-{{ $btnCol }} d-flex align-items-end flex-wrap gap-2">
                                <button class="btn btn-sm btn-secondary" id="filter_reset" title="Reset Filter">
                                    <i class="ti ti-refresh"></i> Reset
                                </button>
                                <button class="btn btn-sm btn-primary" id="filter_submit" title="Terapkan Filter">
                                    <i class="ti ti-filter"></i> Tampilkan
                                </button>
                                <a href="javascript:void(0)" id="btn-export-excel" class="btn btn-success btn-sm d-flex align-items-center gap-1" title="Export Excel">
                                    <i class="ti ti-file-spreadsheet me-1"></i> Export
                                </a>
                            </div>
                        </div>

                        <!-- DataTable -->
                        <div class="table-responsive" style="overflow-x: auto;">
                            <table class="table table-striped table-bordered dt-responsive nowrap align-middle mb-0"
                                id="transaksiTable" style="width: 1400px; min-width: 100%;">
                                <thead class="table-light">
                                    <tr>
                                        @if ($canBulkVerify)
                                            <th style="width: 40px;" class="text-center">
                                                <input type="checkbox" class="form-check-input" id="select_all"
                                                    title="Pilih Semua">
                                            </th>
                                        @endif

                                        <th style="width: 50px;" class="text-center">#</th>
                                        <th>User Input</th>
                                        <th>Tanggal</th>
                                        <th>Ekspedisi</th>
                                        <th>No. Resi</th>
                                        <th>Metode</th>
                                        <th>Pengirim</th>
                                        <th class="text-end">Pendapatan</th>
                                        <th class="text-end">Diskon</th>
                                        <th class="text-end">Pendapatan Bersih</th>
                                        <th>Status Info</th>
                                        <th style="width: 100px;" class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                        <!-- Total Pendapatan, Diskon, dan Pendapatan Bersih Accumulator -->
                        <div class="mt-3 d-flex justify-content-end gap-3 flex-wrap">
                            <div class="card bg-primary bg-opacity-10 border-0 shadow-sm px-4 py-2">
                                <div class="d-flex align-items-center gap-3">
                                    <span class="fw-semibold text-primary">Total Pendapatan (Terfilter):</span>
                                    <span class="fs-4 fw-bold text-primary" id="total_pendapatan_display">Rp 0</span>
                                </div>
                            </div>
                            <div class="card bg-warning bg-opacity-10 border-0 shadow-sm px-4 py-2">
                                <div class="d-flex align-items-center gap-3">
                                    <span class="fw-semibold text-warning">Total Diskon (Terfilter):</span>
                                    <span class="fs-4 fw-bold text-warning" id="total_diskon_display">Rp 0</span>
                                </div>
                            </div>
                            <div class="card bg-success bg-opacity-10 border-0 shadow-sm px-4 py-2">
                                <div class="d-flex align-items-center gap-3">
                                    <span class="fw-semibold text-success">Total Pendapatan Bersih (Terfilter):</span>
                                    <span class="fs-4 fw-bold text-success" id="total_pendapatan_bersih_display">Rp
                                        0</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('transaksi.modal.bulk-verify')
@endsection

@push('scripts')
    <!-- jQuery & DataTables -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.min.js"></script>

    <!-- Date Range Picker & Moment.js -->
    <script src="https://cdn.jsdelivr.net/npm/moment@2.29.1/moment.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
     <script>
        let transaksiTable;
        let selectedIds = new Set();
        const STORAGE_KEY = 'transaksi_index_filters';

        $(document).ready(function() {
            const Toast = Swal.mixin({ toast: true, position: 'top-end', showConfirmButton: false, timer: 3000, timerProgressBar: true });

            @if (session('success'))
                Toast.fire({ icon: 'success', title: '{{ session('success') }}' });
            @endif

            // 1. Load Filter dari localStorage
            let savedFilters = JSON.parse(localStorage.getItem(STORAGE_KEY)) || {};
            let savedRange = savedFilters.tanggal_range || '';
            $('#filter_tanggal_range').val(savedRange);

            $('#filter_tanggal_range').daterangepicker({
                autoUpdateInput: false,
                startDate: savedFilters.tanggal_awal ? moment(savedFilters.tanggal_awal) : moment(),
                endDate: savedFilters.tanggal_akhir ? moment(savedFilters.tanggal_akhir) : moment(),
                locale: { cancelLabel: 'Reset', applyLabel: 'Terapkan', format: 'YYYY-MM-DD' }
            });
            $('#filter_tanggal_range').on('apply.daterangepicker', function(ev, picker) {
                $(this).val(picker.startDate.format('YYYY-MM-DD') + ' s/d ' + picker.endDate.format('YYYY-MM-DD'));
            });
            $('#filter_tanggal_range').on('cancel.daterangepicker', function(ev, picker) { $(this).val(''); });

            // Set nilai filter lainnya (TAMBAHKAN status_verifikasi)
            $('#filter_metode').val(savedFilters.metode || '');
            $('#filter_ekspedisi').val(savedFilters.ekspedisi || '');
            $('#filter_user').val(savedFilters.user || '');
            $('#filter_status_verifikasi').val(savedFilters.status_verifikasi || ''); // <-- BARU

            // 2. DataTables Init
            transaksiTable = $('#transaksiTable').DataTable({
                responsive: true, serverSide: true, processing: true, destroy: true, autoWidth: false,
                stateSave: true,
                stateDuration: 86400,
                stateSaveCallback: function(settings, data) { localStorage.setItem('DataTables_' + STORAGE_KEY, JSON.stringify(data)); },
                stateLoadCallback: function(settings) { return JSON.parse(localStorage.getItem('DataTables_' + STORAGE_KEY)); },
                ajax: {
                    url: "{{ route('transaksi.index') }}", type: 'GET',
                    data: function(d) {
                        let tanggalVal = $('#filter_tanggal_range').val();
                        d.tanggal_awal = ''; d.tanggal_akhir = '';
                        if (tanggalVal && tanggalVal.includes(' s/d ')) {
                            let arr = tanggalVal.split(' s/d ');
                            d.tanggal_awal = arr[0]; d.tanggal_akhir = arr[1];
                        }
                        d.metode = $('#filter_metode').val();
                        d.ekspedisi = $('#filter_ekspedisi').val();
                        d.user = $('#filter_user').val();
                        d.status_verifikasi = $('#filter_status_verifikasi').val(); // <-- BARU
                    }
                },
                drawCallback: function(settings) {
                    var json = this.api().ajax.json();
                    if (json) {
                        $('#total_pendapatan_display').text('Rp ' + (json.total_pendapatan || 0));
                        $('#total_diskon_display').text('Rp ' + (json.total_diskon || 0));
                        $('#total_pendapatan_bersih_display').text('Rp ' + (json.total_pendapatan_bersih || 0));
                    }
                    @if ($canBulkVerify)
                        $('#select_all').prop('checked', false);
                        updateSelectedCount();
                    @endif
                },
                columnDefs: [
                    @if ($canBulkVerify)
                        { className: 'text-center', targets: [0, 1, 12] }, { className: 'text-end', targets: [8, 9, 10] }, { orderable: false, targets: [0, 12] }
                    @else
                        { className: 'text-center', targets: [0, 11] }, { className: 'text-end', targets: [7, 8, 9] }, { orderable: false, targets: [0, 11] }
                    @endif
                ],
                columns: [
                    @if ($canBulkVerify)
                        { data: 'id', name: 'id', orderable: false, searchable: false, render: function(data, type, row) { const isChecked = selectedIds.has(data) ? 'checked' : ''; return `<input type="checkbox" class="form-check-input row-checkbox" value="${data}" ${isChecked}>`; } },
                    @endif
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', searchable: false },
                    { data: 'UserCreate', name: 'UserCreate', render: (data) => data ? `<span class="fw-bold text-primary">${data}</span>` : '<span class="text-muted">-</span>' },
                    { data: 'Tanggal', name: 'Tanggal', render: (data) => data ? new Date(data).toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' }) : '-' },
                    { data: 'Ekspedisi', name: 'Ekspedisi', render: (data) => data ? `<span class="fw-semibold">${data}</span>` : '<span class="text-muted">-</span>' },
                    { data: 'NoResi', name: 'NoResi', render: (data) => data ? `<span class="font-monospace small">${data}</span>` : '<span class="text-muted">-</span>' },
                    { data: 'Metode', name: 'Metode', render: (data) => {
                        if (!data) return '<span class="badge bg-secondary">-</span>';
                        let badgeClass = '';
                        let icon = '';
                        let label = data;

                        // Tentukan warna badge dan ikon berdasarkan metode
                        switch (data) {
                            case 'Tunai':
                                badgeClass = 'bg-success';
                                icon = 'ti ti-cash';
                                break;
                            case 'Non-Tunai':
                                badgeClass = 'bg-info text-dark';
                                icon = 'ti ti-credit-card';
                                break;
                            case 'COD':
                                badgeClass = 'bg-warning text-dark';
                                icon = 'ti ti-truck';
                                break;
                            case 'Tagihan':
                                badgeClass = 'bg-primary';
                                icon = 'ti ti-file-invoice';
                                break;
                            case 'Qris':
                                badgeClass = 'bg-danger';
                                icon = 'ti ti-qrcode';
                                break;
                            case 'Transfer':
                                badgeClass = 'bg-purple';
                                icon = 'ti ti-exchange';
                                break;
                            default:
                                badgeClass = 'bg-secondary';
                                icon = '';
                        }
                        return `<span class="badge ${badgeClass}">${icon ? `<i class="${icon} me-1"></i>` : ''}${label}</span>`;
                    }},

                    { data: 'Bayar', name: 'Bayar' },
                    { data: 'Pendapatan', name: 'Pendapatan', render: (data) => new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(data || 0) },
                    { data: 'Diskon', name: 'Diskon', render: (data) => new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(data || 0) },
                    { data: 'PendapatanBersih', name: 'PendapatanBersih', render: (data) => new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(data || 0) },
                    { data: 'StatusInfo', name: 'StatusInfo' },
                    { data: 'action', name: 'action', searchable: false },
                ]
            });

            // 3. Fungsi Simpan Filter (TAMBAHKAN status_verifikasi)
            function saveFilters() {
                let tanggalVal = $('#filter_tanggal_range').val();
                let filters = {
                    tanggal_range: tanggalVal,
                    tanggal_awal: '', tanggal_akhir: '',
                    metode: $('#filter_metode').val(),
                    ekspedisi: $('#filter_ekspedisi').val(),
                    user: $('#filter_user').val(),
                    status_verifikasi: $('#filter_status_verifikasi').val() // <-- BARU
                };
                if (tanggalVal && tanggalVal.includes(' s/d ')) {
                    let arr = tanggalVal.split(' s/d ');
                    filters.tanggal_awal = arr[0]; filters.tanggal_akhir = arr[1];
                }
                localStorage.setItem(STORAGE_KEY, JSON.stringify(filters));
            }

            // ✅ 4. EVENT LISTENER BARU: Popup Lihat Catatan Finance
             $('body').on('click', '.btn-view-catatan', function() {
                const catatan = $(this).data('catatan');

                Swal.fire({
                    html: `
                        <div class="finance-note-popup">
                            <div class="note-header">
                                <div class="icon-box">
                                    <i class="ti ti-shield-check"></i>
                                </div>
                                <div class="header-text">
                                    <h6 class="mb-0 fw-bold text-dark">Catatan Verifikasi Finance</h6>
                                    <small class="text-muted">Pesan resmi terkait transaksi ini</small>
                                </div>
                            </div>
                            <div class="note-body">
                                <p class="mb-0 text-dark">"${catatan}"</p>
                            </div>
                            <div class="note-footer">
                                <i class="ti ti-info-circle me-2"></i>
                                <span>Harap perhatikan catatan ini sebelum memproses lebih lanjut.</span>
                            </div>
                        </div>
                    `,
                    showConfirmButton: true,
                    confirmButtonText: 'Mengerti, Tutup',
                    showCancelButton: false,
                    customClass: {
                        popup: 'swal2-finance-note',
                        confirmButton: 'swal2-confirm-custom'
                    },
                    backdrop: true,
                    allowOutsideClick: true
                });
            });

            // 5. Logic Checkbox & Bulk Action (Tetap sama seperti sebelumnya)
            @if ($canBulkVerify)
                $('#transaksiTable tbody').on('change', '.row-checkbox', function() {
                    const id = $(this).val();
                    if ($(this).is(':checked')) selectedIds.add(id);
                    else { selectedIds.delete(id); $('#select_all').prop('checked', false); }
                    updateSelectedCount();
                });
                $('#select_all').on('change', function() {
                    const isChecked = $(this).is(':checked');
                    $('.row-checkbox').each(function() {
                        const id = $(this).val();
                        $(this).prop('checked', isChecked);
                        if (isChecked) selectedIds.add(id); else selectedIds.delete(id);
                    });
                    updateSelectedCount();
                });
                function updateSelectedCount() {
                    const count = selectedIds.size;
                    $('#selected_count').text(count); $('#modal_selected_count').text(count);
                    $('#btn_bulk_verify').prop('disabled', count === 0);
                }
                $('#btn_bulk_verify').on('click', function() {
                    if (selectedIds.size > 0) new bootstrap.Modal(document.getElementById('bulkVerifyModal')).show();
                });
                $('#btn_submit_bulk').on('click', function() {
                    const statusValue = $('#bulk_status').val();
                    const catatan = $('#bulk_catatan').val();
                    const ids = Array.from(selectedIds);
                    if (!statusValue) { Swal.fire('Error', 'Status verifikasi wajib dipilih.', 'error'); return; }
                    let statusText = statusValue === 'Y' ? 'Disetujui / Valid' : (statusValue === 'N' ? 'Ditolak / Tidak Valid' : 'Belum Diverifikasi');
                    Swal.fire({
                        title: 'Konfirmasi Verifikasi Massal',
                        html: `Anda akan mengubah <strong>${ids.length}</strong> transaksi menjadi status:<br><strong class="text-primary fs-5">${statusText}</strong><br><span class="text-muted small">Lanjutkan proses ini?</span>`,
                        icon: 'warning', showCancelButton: true, confirmButtonColor: '#f59e0b', cancelButtonColor: '#6c757d',
                        confirmButtonText: 'Ya, Verifikasi!', cancelButtonText: 'Batal', reverseButtons: true
                    }).then((result) => {
                        if (result.isConfirmed) {
                            Swal.fire({ title: 'Memproses...', text: 'Mohon tunggu sebentar', allowOutsideClick: false, didOpen: () => Swal.showLoading() });
                            $.ajax({
                                url: "{{ route('transaksi.bulkUpdateStatus') }}", type: 'POST',
                                data: { _token: '{{ csrf_token() }}', ids: ids, Status: statusValue, Catatan: catatan },
                                success: function(response) {
                                    Swal.fire({ icon: 'success', title: 'Berhasil!', text: response.message, timer: 2000, showConfirmButton: false });
                                    selectedIds.clear(); $('#bulkVerifyForm')[0].reset();
                                    bootstrap.Modal.getInstance(document.getElementById('bulkVerifyModal')).hide();
                                    transaksiTable.ajax.reload(null, false);
                                },
                                error: function(xhr) { Swal.fire('Gagal!', xhr.responseJSON?.message || 'Terjadi kesalahan.', 'error'); }
                            });
                        }
                    });
                });
            @endif

            // 6. Filter Actions
            $('#filter_submit').on('click', function() { saveFilters(); transaksiTable.ajax.reload(); });
            $('#filter_reset').on('click', function() {
                $('#filter_tanggal_range').val('');
                $('#filter_tanggal_range').data('daterangepicker').setStartDate(moment());
                $('#filter_tanggal_range').data('daterangepicker').setEndDate(moment());
                $('#filter_tanggal_range').data('daterangepicker').hide();
                $('#filter_metode').val(''); $('#filter_ekspedisi').val('');
                $('#filter_user').val(''); $('#filter_status_verifikasi').val(''); // <-- BARU
                localStorage.removeItem(STORAGE_KEY);
                @if ($canBulkVerify) selectedIds.clear(); @endif
                transaksiTable.ajax.reload();
            });

            // 7. Delete Handler & Export Handler (Tetap sama seperti sebelumnya)
            $('body').on('click', '.btn-delete', function() {
                // ... (kode delete tetap sama) ...
                const id = $(this).data('id'); const kode = $(this).data('kode');
                Swal.fire({ title: 'Hapus Transaksi?', html: `Anda akan menghapus transaksi dengan Kode:<br><strong class="text-primary">${kode}</strong><br>Tindakan ini tidak dapat dibatalkan!`, icon: 'warning', showCancelButton: true, confirmButtonColor: '#dc3545', cancelButtonColor: '#6c757d', confirmButtonText: 'Ya, Hapus!', cancelButtonText: 'Batal', reverseButtons: true }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({ url: "{{ route('transaksi.destroy', ':id') }}".replace(':id', id), type: 'DELETE', data: { _token: '{{ csrf_token() }}' }, beforeSend: function() { Swal.fire({ title: 'Menghapus...', allowOutsideClick: false, didOpen: () => Swal.showLoading() }); }, success: function(response) { if (response.status === 200 || response.success) { Swal.fire({ icon: 'success', title: 'Berhasil!', text: response.message, timer: 2000, showConfirmButton: false }); transaksiTable.ajax.reload(null, false); } else { Swal.fire('Gagal!', response.message || 'Terjadi kesalahan', 'error'); } }, error: function(xhr) { Swal.fire('Gagal!', xhr.responseJSON?.message || 'Terjadi kesalahan saat menghapus data.', 'error'); } });
                    }
                });
            });

            $('#btn-export-excel').on('click', function() {
                // ... (kode export tetap sama, pastikan tambahkan status_verifikasi jika ingin di-export juga, opsional) ...
                let tanggalVal = $('#filter_tanggal_range').val(); let tanggal_awal = '', tanggal_akhir = '';
                if (tanggalVal && tanggalVal.includes(' s/d ')) { let arr = tanggalVal.split(' s/d '); tanggal_awal = arr[0]; tanggal_akhir = arr[1]; }
                let metode = $('#filter_metode').val(); let ekspedisi = $('#filter_ekspedisi').val(); let user = $('#filter_user').val();
                let url = "{{ route('transaksi.export') }}"; let params = [];
                if (tanggal_awal) params.push(`tanggal_awal=${tanggal_awal}`); if (tanggal_akhir) params.push(`tanggal_akhir=${tanggal_akhir}`);
                if (metode) params.push(`metode=${metode}`); if (ekspedisi) params.push(`ekspedisi=${ekspedisi}`); if (user) params.push(`user=${user}`);
                if (params.length > 0) url += '?' + params.join('&');
                window.location.href = url;
            });
        });
    </script>
@endpush
