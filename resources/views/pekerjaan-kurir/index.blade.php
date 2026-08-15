@extends('layouts.app')

@section('content')
    @php
        $CanBulkVerify = auth()->check() && in_array(auth()->user()->role, ['Admin', 'Leader']);
    @endphp

    <!-- CSS untuk Popup Catatan Profesional -->
    <style>
        .kurir-note-popup { text-align: left; }
        .kurir-note-popup .note-header { display: flex; align-items: center; gap: 15px; padding-bottom: 15px; border-bottom: 1px solid #e9ecef; margin-bottom: 20px; }
        .kurir-note-popup .icon-box { width: 48px; height: 48px; background: linear-gradient(135deg, #fff3cd 0%, #ffe69c 100%); color: #856404; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; flex-shrink: 0; }
        .kurir-note-popup .header-text h6 { font-size: 1.1rem; letter-spacing: -0.01em; }
        .kurir-note-popup .note-body { background: #f8f9fa; border-left: 4px solid #ffc107; padding: 16px 20px; border-radius: 0 8px 8px 0; margin-bottom: 20px; }
        .kurir-note-popup .note-body p { font-size: 0.95rem; line-height: 1.6; color: #343a40; margin: 0; font-style: italic; }
        .kurir-note-popup .note-footer { font-size: 0.8rem; color: #6c757d; display: flex; align-items: center; background: #e9ecef; padding: 10px 15px; border-radius: 8px; }
        .swal2-kurir-note { border-radius: 16px !important; padding: 0 !important; overflow: hidden; box-shadow: 0 10px 40px rgba(0,0,0,0.15) !important; border: none !important; }
        .swal2-kurir-note .swal2-title { display: none !important; }
        .swal2-kurir-note .swal2-html-container { margin: 0 !important; padding: 25px 30px 30px 30px !important; }
        .swal2-kurir-note .swal2-confirm { background-color: #212529 !important; color: #fff !important; border-radius: 8px !important; padding: 10px 28px !important; font-weight: 600 !important; font-size: 0.9rem !important; box-shadow: 0 4px 6px rgba(0,0,0,0.1) !important; transition: all 0.2s ease !important; }
        .swal2-kurir-note .swal2-confirm:hover { background-color: #000 !important; transform: translateY(-1px); }
    </style>

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
                        <div class="d-flex gap-2">
                            @if($CanBulkVerify)
                            <button type="button" class="btn btn-warning btn-sm d-flex align-items-center gap-1" id="BtnBulkVerify" disabled>
                                <i class="ti ti-checklist"></i> Verifikasi Terpilih (<span id="SelectedCount">0</span>)
                            </button>
                            @endif
                            <a href="{{ route('pekerjaan-kurir.create') }}" class="btn btn-primary btn-sm d-flex align-items-center gap-1">
                                <i class="ti ti-plus"></i> Tambah Laporan
                            </a>
                        </div>
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
                            <div class="col-md-3">
                                <label class="form-label small fw-semibold text-muted">User / Kurir</label>
                                <select class="form-select form-select-sm" id="FilterUserId">
                                    <option value="">Semua User</option>
                                    @foreach($Users as $User)
                                        <option value="{{ $User->id }}">{{ $User->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <div class="d-flex gap-2">
                                    <button type="button" id="BtnFilter" class="btn btn-primary btn-sm px-3">
                                        <i class="ti ti-filter me-1"></i> Tampilkan
                                    </button>
                                    <button type="button" id="BtnResetFilter" class="btn btn-secondary btn-sm px-3" title="Reset Filter">
                                        <i class="ti ti-refresh me-1"></i> Reset
                                    </button>
                                    <button type="button" id="BtnExport" class="btn btn-success btn-sm px-3" title="Export Excel">
                                        <i class="ti ti-download me-1"></i> Export
                                    </button>
                                </div>
                            </div>
                        </div>
                        <!-- ✅ AKHIR Filter Row -->

                        <div class="table-responsive">
                            <table class="table table-striped table-bordered dt-responsive nowrap align-middle mb-0" id="KurirTable" style="width: 100%;">
                                <thead class="table-light">
                                    <tr>
                                        @if($CanBulkVerify)
                                        <th style="width: 40px;" class="text-center">
                                            <input type="checkbox" class="form-check-input" id="SelectAll" title="Pilih Semua">
                                        </th>
                                        @endif
                                        <th class="text-center">#</th>
                                        <th>Tanggal & Jam</th>
                                        <th>Nama Kurir</th>
                                        <th>Pekerjaan</th>
                                        <th>Rute (Dari - Tujuan)</th>
                                        <th class="text-center">Jml. Paket</th>
                                        <th>Durasi</th>
                                        <th class="text-center">Bukti</th>
                                        <th class="text-center">Status Verif</th>
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

    <!-- Modal Bulk Verify -->
    @if($CanBulkVerify)
    <div class="modal fade" id="BulkVerifyModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-warning bg-opacity-10 border-bottom-0">
                    <h5 class="modal-title fw-bold text-warning-emphasis">
                        <i class="ti ti-shield-check me-2"></i>Verifikasi Massal Laporan
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <p class="text-muted small mb-3">
                        Anda akan mengubah status untuk <strong id="ModalSelectedCount" class="text-dark">0</strong> laporan terpilih.
                    </p>
                    <form id="BulkVerifyForm">
                        <div class="mb-3">
                            <label for="BulkStatus" class="form-label fw-semibold">Status Verifikasi <span class="text-danger">*</span></label>
                            <select class="form-select" id="BulkStatus" name="Status" required>
                                <option value="Y">Y (Disetujui / Valid)</option>
                                <option value="N">N (Ditolak / Tidak Valid)</option>
                                <option value="N/A">N/A (Belum Diverifikasi)</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="BulkCatatan" class="form-label fw-semibold">Catatan Verifikasi</label>
                            <textarea class="form-control" id="BulkCatatan" name="Catatan" rows="3" placeholder="Opsional: Isi alasan jika ditolak atau catatan tambahan..."></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer border-top-0 pt-0">
                    <button type="button" class="btn btn-light text-muted" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-warning text-dark fw-semibold px-4" id="BtnSubmitBulk">
                        <i class="ti ti-check me-1"></i> Simpan Perubahan
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif
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

            let SelectedIds = new Set();

            // 1. Delete Handler (Tetap sama)
            $('body').on('click', '.btn-delete', function() {
                const Id = $(this).data('id');
                const Tanggal = $(this).data('tanggal');
                Swal.fire({
                    title: 'Hapus Laporan?',
                    html: `Hapus data aktivitas tanggal <strong class="text-primary">${Tanggal}</strong>?<br><small class="text-muted">Tindakan ini tidak dapat dibatalkan!</small>`,
                    icon: 'warning', showCancelButton: true, confirmButtonColor: '#dc3545', cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Ya, Hapus!', cancelButtonText: 'Batal', reverseButtons: true
                }).then((Result) => {
                    if (Result.isConfirmed) {
                        $.ajax({
                            url: "{{ route('pekerjaan-kurir.destroy', ':id') }}".replace(':id', Id),
                            type: 'POST',
                            data: { _method: 'DELETE', _token: '{{ csrf_token() }}' },
                            beforeSend: function() { Swal.fire({ title: 'Menghapus...', text: 'Mohon tunggu sebentar', allowOutsideClick: false, didOpen: () => Swal.showLoading() }); },
                            success: function(Response) {
                                if (Response.success) {
                                    Swal.fire({ icon: 'success', title: 'Berhasil!', text: Response.message, timer: 2000, showConfirmButton: false });
                                    $('#KurirTable').DataTable().ajax.reload(null, false);
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

            // 2. DataTables Init
            $('#KurirTable').DataTable({
                responsive: true, serverSide: true, processing: true, destroy: true, autoWidth: false,
                ajax: {
                    url: "{{ route('pekerjaan-kurir.index') }}",
                    type: 'GET',
                    // ✅ TAMBAHAN: Kirim parameter filter ke server
                    data: function (Data) {
                        Data.TanggalAwal = $('#FilterTanggalAwal').val();
                        Data.TanggalAkhir = $('#FilterTanggalAkhir').val();
                        Data.UserId = $('#FilterUserId').val();
                    }
                },
                drawCallback: function() {
                    @if($CanBulkVerify)
                        $('#SelectAll').prop('checked', false);
                        UpdateSelectedCount();
                    @endif
                },
                columnDefs: [
                    @if($CanBulkVerify)
                        { className: 'text-center', targets: [0, 1, 6, 8, 9, 10] },
                        { orderable: false, targets: [0, 10] }
                    @else
                        { className: 'text-center', targets: [0, 5, 7, 8, 9] },
                        { orderable: false, targets: [0, 9] }
                    @endif
                ],
                columns: [
                    @if($CanBulkVerify)
                    {
                        data: 'id', name: 'id', orderable: false, searchable: false,
                        render: function(Data, Type, Row) {
                            const IsChecked = SelectedIds.has(Data) ? 'checked' : '';
                            return `<input type="checkbox" class="form-check-input row-checkbox" value="${Data}" ${IsChecked}>`;
                        }
                    },
                    @endif
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', searchable: false },
                    { data: 'Tanggal', name: 'Tanggal' },
                    { data: 'NamaKurir', name: 'NamaKurir' },
                    { data: 'Pekerjaan', name: 'Pekerjaan' },
                    { data: 'DariLokasi', name: 'DariLokasi', render: function(Data, Type, Row) {
                        return '<small class="d-block text-muted">Dari: ' + Data + '</small><small class="fw-semibold">Ke: ' + Row.Tujuan + '</small>';
                    }},
                    { data: 'JumlahPaket', name: 'JumlahPaket' },
                    { data: 'Durasi', name: 'Durasi' },
                    { data: 'BuktiFoto', name: 'BuktiFoto' },
                    { data: 'Status', name: 'Status' },
                    { data: 'action', name: 'action', searchable: false }
                ],
                language: { url: "//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json" }
            });

            // ✅ 3. EVENT LISTENER: Popup Lihat Catatan Verifikasi Kurir (Tetap sama)
            $('body').on('click', '.btn-view-catatan-kurir', function() {
                const Catatan = $(this).data('catatan');
                Swal.fire({
                    html: `
                        <div class="kurir-note-popup">
                            <div class="note-header">
                                <div class="icon-box"><i class="ti ti-shield-check"></i></div>
                                <div class="header-text">
                                    <h6 class="mb-0 fw-bold text-dark">Catatan Verifikasi</h6>
                                    <small class="text-muted">Pesan resmi terkait laporan ini</small>
                                </div>
                            </div>
                            <div class="note-body"><p class="mb-0 text-dark">"${Catatan}"</p></div>
                            <div class="note-footer">
                                <i class="ti ti-info-circle me-2"></i>
                                <span>Harap perhatikan catatan ini sebelum mengajukan revisi.</span>
                            </div>
                        </div>
                    `,
                    showConfirmButton: true, confirmButtonText: 'Mengerti, Tutup', showCancelButton: false,
                    customClass: { popup: 'swal2-kurir-note', confirmButton: 'swal2-confirm-custom' },
                    backdrop: true, allowOutsideClick: true
                });
            });

            // ✅ 4. Logic Filter & Export
            $('#BtnFilter').on('click', function() {
                $('#KurirTable').DataTable().ajax.reload();
            });

            $('#BtnResetFilter').on('click', function() {
                $('#FilterTanggalAwal').val('');
                $('#FilterTanggalAkhir').val('');
                $('#FilterUserId').val('');
                $('#KurirTable').DataTable().ajax.reload();
            });

            $('#BtnExport').on('click', function() {
                const TanggalAwal = $('#FilterTanggalAwal').val();
                const TanggalAkhir = $('#FilterTanggalAkhir').val();
                const UserId = $('#FilterUserId').val();

                let Url = "{{ route('pekerjaan-kurir.export') }}";
                let Params = [];

                if (TanggalAwal) Params.push('TanggalAwal=' + TanggalAwal);
                if (TanggalAkhir) Params.push('TanggalAkhir=' + TanggalAkhir);
                if (UserId) Params.push('UserId=' + UserId);

                if (Params.length > 0) {
                    Url += '?' + Params.join('&');
                }

                window.location.href = Url;
            });

            // 5. Logic Checkbox & Bulk Action (Tetap sama)
            @if($CanBulkVerify)
            $('#KurirTable tbody').on('change', '.row-checkbox', function() {
                const Id = $(this).val();
                if ($(this).is(':checked')) SelectedIds.add(Id);
                else { SelectedIds.delete(Id); $('#SelectAll').prop('checked', false); }
                UpdateSelectedCount();
            });

            $('#SelectAll').on('change', function() {
                const IsChecked = $(this).is(':checked');
                $('.row-checkbox').each(function() {
                    const Id = $(this).val();
                    $(this).prop('checked', IsChecked);
                    if (IsChecked) SelectedIds.add(Id); else SelectedIds.delete(Id);
                });
                UpdateSelectedCount();
            });

            function UpdateSelectedCount() {
                const Count = SelectedIds.size;
                $('#SelectedCount').text(Count);
                $('#ModalSelectedCount').text(Count);
                $('#BtnBulkVerify').prop('disabled', Count === 0);
            }

            $('#BtnBulkVerify').on('click', function() {
                if (SelectedIds.size > 0) {
                    const Modal = new bootstrap.Modal(document.getElementById('BulkVerifyModal'));
                    Modal.show();
                }
            });

            $('#BtnSubmitBulk').on('click', function() {
                const StatusValue = $('#BulkStatus').val();
                const Catatan = $('#BulkCatatan').val();
                const Ids = Array.from(SelectedIds);

                if (!StatusValue) { Swal.fire('Error', 'Status verifikasi wajib dipilih.', 'error'); return; }

                let StatusText = StatusValue === 'Y' ? 'Disetujui (Y)' : (StatusValue === 'N' ? 'Ditolak (N)' : 'Belum Diverifikasi (N/A)');

                Swal.fire({
                    title: 'Konfirmasi Verifikasi Massal',
                    html: `Anda akan mengubah <strong>${Ids.length}</strong> laporan menjadi status:<br><strong class="text-primary fs-5">${StatusText}</strong><br><span class="text-muted small">Lanjutkan proses ini?</span>`,
                    icon: 'warning', showCancelButton: true, confirmButtonColor: '#f59e0b', cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Ya, Verifikasi!', cancelButtonText: 'Batal', reverseButtons: true
                }).then((Result) => {
                    if (Result.isConfirmed) {
                        Swal.fire({ title: 'Memproses...', text: 'Mohon tunggu sebentar', allowOutsideClick: false, didOpen: () => Swal.showLoading() });
                        $.ajax({
                            url: "{{ route('pekerjaan-kurir.bulkVerify') }}",
                            type: 'POST',
                            data: { _token: '{{ csrf_token() }}', Ids: Ids, Status: StatusValue, Catatan: Catatan },
                            success: function(Response) {
                                Swal.fire({ icon: 'success', title: 'Berhasil!', text: Response.message, timer: 2000, showConfirmButton: false });
                                SelectedIds.clear();
                                $('#BulkVerifyForm')[0].reset();
                                bootstrap.Modal.getInstance(document.getElementById('BulkVerifyModal')).hide();
                                $('#KurirTable').DataTable().ajax.reload(null, false);
                            },
                            error: function(Xhr) {
                                Swal.fire('Gagal!', Xhr.responseJSON?.message || 'Terjadi kesalahan saat memverifikasi.', 'error');
                            }
                        });
                    }
                });
            });
            @endif
        });
    </script>
@endpush
