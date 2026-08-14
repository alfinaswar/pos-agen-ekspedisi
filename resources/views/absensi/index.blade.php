@extends('layouts.app')

@section('content')
@php
    $canBulkApprove = auth()->check() && in_array(auth()->user()->role, ['Admin', 'Leader']);
@endphp
@push('styles')
<style>
    /* Styling Profesional untuk Popup Catatan Verifikasi Absensi */
    .absensi-note-popup { text-align: left; }
    .absensi-note-popup .note-header {
        display: flex; align-items: center; gap: 15px; padding-bottom: 15px; border-bottom: 1px solid #e9ecef; margin-bottom: 20px;
    }
    .absensi-note-popup .icon-box {
        width: 48px; height: 48px; background: linear-gradient(135deg, #fff3cd 0%, #ffe69c 100%); color: #856404;
        border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; flex-shrink: 0;
    }
    .absensi-note-popup .header-text h6 { font-size: 1.1rem; letter-spacing: -0.01em; }
    .absensi-note-popup .note-body {
        background: #f8f9fa; border-left: 4px solid #ffc107; padding: 16px 20px; border-radius: 0 8px 8px 0; margin-bottom: 20px;
    }
    .absensi-note-popup .note-body p { font-size: 0.95rem; line-height: 1.6; color: #343a40; margin: 0; font-style: italic; }
    .absensi-note-popup .note-footer {
        font-size: 0.8rem; color: #6c757d; display: flex; align-items: center; background: #e9ecef; padding: 10px 15px; border-radius: 8px;
    }
    .swal2-absensi-note {
        border-radius: 16px !important; padding: 0 !important; overflow: hidden; box-shadow: 0 10px 40px rgba(0,0,0,0.15) !important; border: none !important;
    }
    .swal2-absensi-note .swal2-title { display: none !important; }
    .swal2-absensi-note .swal2-html-container { margin: 0 !important; padding: 25px 30px 30px 30px !important; }
    .swal2-absensi-note .swal2-confirm {
        background-color: #212529 !important; color: #fff !important; border-radius: 8px !important; padding: 10px 28px !important;
        font-weight: 600 !important; font-size: 0.9rem !important; box-shadow: 0 4px 6px rgba(0,0,0,0.1) !important; transition: all 0.2s ease !important;
    }
    .swal2-absensi-note .swal2-confirm:hover { background-color: #000 !important; transform: translateY(-1px); }
</style>
@endpush
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
                    <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-decoration-none text-reset">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Data Absensi</li>
                </ol>
            </nav>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0 fw-semibold"><i class="ti ti-list me-2"></i>Data Absensi</h4>
                        <div class="d-flex gap-2">
                            @if($canBulkApprove)
                            <button type="button" class="btn btn-warning btn-sm d-flex align-items-center gap-1" id="btn_bulk_approve" disabled>
                                <i class="ti ti-checklist"></i> Verifikasi Terpilih (<span id="selected_count">0</span>)
                            </button>
                            @endif
                            <a href="{{ route('absensi.create') }}" class="btn btn-primary btn-sm d-flex align-items-center gap-1">
                                <i class="ti ti-plus"></i> Tambah Absensi
                            </a>
                        </div>
                    </div>
                    <div class="card-body">

                        <!-- === FILTER ROW === -->
                        @php
                            $isPrivileged = auth()->check() && in_array(auth()->user()->role, ['Admin', 'Leader']);
                        @endphp

                        <div class="row mb-3 g-2">
                            <!-- Filter Bulan -->
                            <div class="col-md-2">
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
                            <div class="col-md-2">
                                <label class="form-label small fw-semibold text-muted">Status</label>
                                <select class="form-select form-select-sm" id="filter_status">
                                    <option value="">Semua Status</option>
                                    <option value="H">Hadir (H)</option>
                                    <option value="I">Izin (I)</option>
                                    <option value="S">Sakit (S)</option>
                                    <option value="TK">Tanpa Keterangan (TK)</option>
                                </select>
                            </div>

                            <!-- Filter Status Verif -->
                            <div class="col-md-2">
                                <label class="form-label small fw-semibold text-muted">Status Verif</label>
                                <select class="form-select form-select-sm" id="filter_status_verif" name="filter_status_verif">
                                    <option value="">Semua Status</option>
                                    <option value="Y">Y (Disetujui)</option>
                                    <option value="N">N (Ditolak)</option>
                                    <option value="N/A">N/A (Belum Diverifikasi)</option>
                                </select>
                            </div>

                            <!-- Filter User (HANYA UNTUK ADMIN & LEADER) -->
                            @if($isPrivileged)
                            <div class="col-md-2">
                                <label class="form-label small fw-semibold text-muted">Nama Karyawan</label>
                                <select class="form-select form-select-sm" id="filter_user">
                                    <option value="">Semua Karyawan</option>
                                    @foreach ($users as $user)
                                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Filter Divisi (HANYA UNTUK ADMIN & LEADER) -->
                            <div class="col-md-2">
                                <label class="form-label small fw-semibold text-muted">Divisi</label>
                                <select class="form-select form-select-sm" id="filter_divisi">
                                    <option value="">Semua Divisi</option>
                                    @foreach ($divisis as $div)
                                        <option value="{{ $div->id }}">{{ $div->Nama }}</option>
                                    @endforeach
                                </select>
                            </div>
                            @endif

                            <!-- Tombol Aksi Filter -->

                        </div>
<div class="col-md-4 d-flex align-items-end gap-1 justify-content-end ms-auto mb-3">
    <div class="ms-auto d-flex gap-1">
        <button class="btn btn-sm btn-secondary" id="filter_reset" title="Reset Filter">
            <i class="ti ti-refresh"></i> Reset
        </button>
        <button class="btn btn-sm btn-primary" id="filter_submit" title="Terapkan Filter">
            <i class="ti ti-filter"></i> Tampilkan
        </button>
        <button class="btn btn-sm btn-success" id="btn_export_excel" title="Export ke Excel">
            <i class="ti ti-file-spreadsheet"></i> Export Excel
        </button>
    </div>
</div>
                        <!-- === END FILTER ROW === -->


                        <div class="table-responsive">
                            <table class="table table-striped table-bordered dt-responsive nowrap align-middle mb-0" id="absensiTable" style="width: 100%;">
                               <thead class="table-light">
                                    <tr>
                                        @if($canBulkApprove)
                                        <th style="width: 40px;" class="text-center">
                                            <input type="checkbox" class="form-check-input" id="select_all" title="Pilih Semua">
                                        </th>
                                        @endif
                                        <th style="width: 50px;" class="text-center">#</th>
                                        <th>Nama</th>
                                        <th>Divisi</th>
                                        <th>Tanggal</th>
                                        <th class="text-center">Status</th>
                                        <th>Jam Hadir</th>
                                        <th>Jam Pulang</th>
                                        <th class="text-center">Lembur</th>
                                        <th>Durasi Lembur</th>
                                        <th>Status Verif</th>
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
@include('absensi.modal-bulk-approve')
@push('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

       <script>
        let absensiTable;
        let selectedIds = new Set();

        $(document).ready(function() {
            const Toast = Swal.mixin({ toast: true, position: 'top-end', showConfirmButton: false, timer: 3000, timerProgressBar: true });

            @if (session('success'))
                Toast.fire({ icon: 'success', title: '{{ session('success') }}' });
            @endif

            // Export Excel Handler (Tetap sama)
            $('#btn_export_excel').on('click', function() {
                const bulan = $('#filter_bulan').val();
                const status = $('#filter_status').val();
                const status_verif = $('#filter_status_verif').val();
                const user_name = $('#filter_user').val();
                const divisi = $('#filter_divisi').val();

                let url = "{{ route('absensi.export') }}";
                let params = [];
                if (bulan) params.push(`bulan=${bulan}`);
                if (status) params.push(`status=${status}`);
                if (status_verif) params.push(`status_verif=${status_verif}`);
                if (user_name) params.push(`user_name=${user_name}`);
                if (divisi) params.push(`divisi=${divisi}`);
                if (params.length > 0) url += '?' + params.join('&');
                window.location.href = url;
            });

            // DataTables Init
            absensiTable = $('#absensiTable').DataTable({
                responsive: true, serverSide: true, processing: true, destroy: true, autoWidth: false,
                ajax: {
                    url: "{{ route('absensi.index') }}", type: 'GET',
                    data: function(d) {
                        d.bulan = $('#filter_bulan').val();
                        d.status = $('#filter_status').val();
                        d.status_verif = $('#filter_status_verif').val();
                        d.user_name = $('#filter_user').val();
                        d.divisi = $('#filter_divisi').val();
                    }
                },
                drawCallback: function() {
                    @if($canBulkApprove)
                        $('#select_all').prop('checked', false);
                        updateSelectedCount();
                    @endif
                },
                language: {
                    processing: '<div class="spinner-border spinner-border-sm text-primary me-2" role="status"></div> Memuat data...',
                    paginate: { next: '<i class="ti ti-chevron-right"></i>', previous: '<i class="ti ti-chevron-left"></i>' },
                    url: "//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json"
                },
                columnDefs: [
                    @if($canBulkApprove)
                        { className: 'text-center', targets: [0, 1, 4, 7, 9, 11] }, // Checkbox(0), #(1), Status(4), Lembur(7), Durasi(9), Aksi(11)
                        { orderable: false, targets: [0, 11] } // Non-sortable: Checkbox, Aksi
                    @else
                        { className: 'text-center', targets: [0, 4, 7, 9, 10] }, // #(0), Status(4), Lembur(7), Durasi(9), Aksi(10)
                        { orderable: false, targets: [0, 10] } // Non-sortable: #, Aksi
                    @endif
                ],
                columns: [
                    @if($canBulkApprove)
                    {
                        data: 'id', name: 'id', orderable: false, searchable: false,
                        render: function(data, type, row) {
                            const isChecked = selectedIds.has(data) ? 'checked' : '';
                            return `<input type="checkbox" class="form-check-input row-checkbox" value="${data}" ${isChecked}>`;
                        }
                    },
                    @endif
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', searchable: false },
                    { data: 'Nama', name: 'Nama', render: (data) => `<span class="fw-semibold text-dark">${data}</span>` },
                    { data: 'Divisi', name: 'Divisi' },
                    { data: 'Tanggal', name: 'Tanggal' },

                    {
                        data: 'Status', name: 'Status',
                        render: (data) => {
                            const badges = { 'H': 'bg-success', 'I': 'bg-primary', 'S': 'bg-warning text-dark', 'TK': 'bg-danger' };
                            const labels = { 'H': 'Hadir', 'I': 'Izin', 'S': 'Sakit', 'TK': 'Tanpa Ket.' };
                            return `<span class="badge ${badges[data] || 'bg-secondary'}">${labels[data] || data}</span>`;
                        }
                    },
                    { data: 'JamHadir', name: 'JamHadir', render: (data) => data ? `<span class="badge bg-success bg-opacity-10 text-success"><i class="ti ti-login me-1"></i>${data}</span>` : '<span class="text-muted">-</span>' },
                    { data: 'JamPulang', name: 'JamPulang', render: (data) => data ? `<span class="badge bg-danger bg-opacity-10 text-danger"><i class="ti ti-logout me-1"></i>${data}</span>` : '<span class="text-muted">-</span>' },
                    { data: 'Lembur', name: 'Lembur', render: (data) => data === 'Y' ? '<span class="badge bg-warning text-dark">Ya</span>' : '<span class="badge bg-secondary">Tidak</span>' },
                    {
                        data: null, name: 'DurasiLembur', orderable: false, searchable: false,
                        render: (data) => {
                            if (data.Lembur === 'Y' && (data.MulaiLembur || data.SelesaiLembur)) {
                                return `<small class="text-muted">${data.MulaiLembur || '-'} s/d ${data.SelesaiLembur || '-'}</small>`;
                            }
                            return '<span class="text-muted">-</span>';
                        }
                    },
                    { data: 'StatusVerif', name: 'StatusVerif', className: 'text-center' },
                    { data: 'action', name: 'action', searchable: false }
                ]
            });

            // ✅ Logic Checkbox & Bulk Action (Hanya untuk Admin/Leader)
            @if($canBulkApprove)
            $('#absensiTable tbody').on('change', '.row-checkbox', function() {
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
                $('#selected_count').text(count);
                $('#modal_selected_count').text(count);
                $('#btn_bulk_approve').prop('disabled', count === 0);
            }

            $('#btn_bulk_approve').on('click', function() {
                if (selectedIds.size > 0) {
                    const modal = new bootstrap.Modal(document.getElementById('bulkApproveModal'));
                    modal.show();
                }
            });

            $('#btn_submit_bulk').on('click', function() {
                const statusValue = $('#bulk_status_verif').val();
                const catatan = $('#bulk_catatan').val();
                const ids = Array.from(selectedIds);

                if (!statusValue) { Swal.fire('Error', 'Status verifikasi wajib dipilih.', 'error'); return; }

                let statusText = statusValue === 'Y' ? 'Disetujui (Y)' : (statusValue === 'N' ? 'Ditolak (N)' : 'Belum Diverifikasi (N/A)');

                Swal.fire({
                    title: 'Konfirmasi Verifikasi Massal',
                    html: `Anda akan mengubah <strong>${ids.length}</strong> data absensi menjadi status:<br>
                           <strong class="text-primary fs-5">${statusText}</strong><br>
                           <span class="text-muted small">Lanjutkan proses ini?</span>`,
                    icon: 'warning', showCancelButton: true, confirmButtonColor: '#f59e0b', cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Ya, Verifikasi!', cancelButtonText: 'Batal', reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        Swal.fire({ title: 'Memproses...', text: 'Mohon tunggu sebentar', allowOutsideClick: false, didOpen: () => Swal.showLoading() });

                        $.ajax({
                            url: "{{ route('absensi.bulkApprove') }}", type: 'POST',
                            data: { _token: '{{ csrf_token() }}', ids: ids, StatusVerif: statusValue, Catatan: catatan },
                            success: function(response) {
                                Swal.fire({ icon: 'success', title: 'Berhasil!', text: response.message, timer: 2000, showConfirmButton: false });
                                selectedIds.clear();
                                $('#bulkApproveForm')[0].reset();
                                bootstrap.Modal.getInstance(document.getElementById('bulkApproveModal')).hide();
                                absensiTable.ajax.reload(null, false);
                            },
                            error: function(xhr) {
                                Swal.fire('Gagal!', xhr.responseJSON?.message || 'Terjadi kesalahan saat memverifikasi.', 'error');
                            }
                        });
                    }
                });
            });
            @endif

            // Event Listener untuk Filter
            $('#filter_submit').on('click', function() { absensiTable.ajax.reload(); });
            $('#filter_reset').on('click', function() {
                $('#filter_bulan').val(''); $('#filter_status').val(''); $('#filter_status_verif').val('');
                $('#filter_user').val(''); $('#filter_divisi').val('');
                @if($canBulkApprove) selectedIds.clear(); @endif
                absensiTable.ajax.reload();
            });

            // Delete Handler (Tetap sama seperti sebelumnya)
            $('body').on('click', '.btn-delete', function() {
                const id = $(this).data('id');
                const nama = $(this).data('nama');
                Swal.fire({
                    title: 'Hapus Data?', html: `Anda akan menghapus data absensi:<br><strong class="text-primary">${nama}</strong><br>Tindakan ini tidak dapat dibatalkan!`,
                    icon: 'warning', showCancelButton: true, confirmButtonColor: '#dc3545', cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Ya, Hapus!', cancelButtonText: 'Batal', reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ route('absensi.destroy', ':id') }}".replace(':id', id), type: 'DELETE', data: { _token: '{{ csrf_token() }}' },
                            success: function(response) {
                                if (response.status === 200 || response.success) {
                                    Swal.fire({ icon: 'success', title: 'Berhasil!', text: response.message, timer: 2000, showConfirmButton: false });
                                    absensiTable.ajax.reload(null, false);
                                } else { Swal.fire('Gagal!', response.message || 'Terjadi kesalahan', 'error'); }
                            },
                            error: function(xhr) { Swal.fire('Gagal!', xhr.responseJSON?.message || 'Terjadi kesalahan saat menghapus data.', 'error'); }
                        });
                    }
                });
            });

            // ✅ EVENT LISTENER: Popup Lihat Catatan Verifikasi Absensi (Dari request sebelumnya)
            $('body').on('click', '.btn-view-catatan-absensi', function() {
                const catatan = $(this).data('catatan');
                Swal.fire({
                    html: `<div class="absensi-note-popup">
                        <div class="note-header"><div class="icon-box"><i class="ti ti-shield-check"></i></div>
                        <div class="header-text"><h6 class="mb-0 fw-bold text-dark">Catatan Persetujuan Leader</h6><small class="text-muted">Pesan resmi terkait absensi ini</small></div></div>
                        <div class="note-body"><p class="mb-0 text-dark">"${catatan}"</p></div>
                        <div class="note-footer"><i class="ti ti-info-circle me-2"></i><span>Harap perhatikan catatan ini sebelum mengajukan revisi.</span></div>
                    </div>`,
                    showConfirmButton: true, confirmButtonText: 'Mengerti, Tutup', showCancelButton: false,
                    customClass: { popup: 'swal2-absensi-note', confirmButton: 'swal2-confirm-custom' },
                    backdrop: true, allowOutsideClick: true
                });
            });
        });
    </script>
@endpush
