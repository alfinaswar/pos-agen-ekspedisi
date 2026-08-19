@extends('layouts.app')

@section('content')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">

    <div class="container-fluid">
        <div class="page-title-head d-flex align-items-center flex-wrap gap-2 mb-4">
            <div class="flex-grow-1">
                <h4 class="page-main-title m-0 fw-semibold">
                    <i class="ti ti-receipt me-2 text-primary"></i>Tagihan Pembayaran
                </h4>
            </div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb m-0 py-0">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-decoration-none text-reset">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Data Tagihan</li>
                </ol>
            </nav>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0 fw-semibold"><i class="ti ti-list me-2"></i>Data Tagihan</h4>
                        <div class="ms-auto d-flex gap-2">
                            @if(auth()->user()->role === 'Superadmin')
                            <button type="button" class="btn btn-success btn-sm d-flex align-items-center gap-1" id="BtnBulkApprove" disabled>
                                <i class="ti ti-checklist"></i> Setujui Terpilih (<span id="SelectedCount">0</span>)
                            </button>
                            @endif
                            <a href="{{ route('tagihan-pembayaran.create') }}" class="btn btn-primary btn-sm d-flex align-items-center gap-1">
                                <i class="ti ti-plus"></i> Buat Tagihan
                            </a>
                        </div>
                    </div>


                    <div class="card-body">
                        <!-- Filter Row (Status Dihapus) -->
                        <div class="row g-2 mb-3 align-items-end">
                            @if(auth()->user()->role === 'Superadmin')
                            <div class="col-md-3">
                                <label class="form-label small fw-semibold text-muted">Pilih Tenant</label>
                                <select class="form-select form-select-sm" id="FilterTenant">
                                    <option value="">Semua Tenant</option>
                                    @foreach($Tenants as $Tenant)
                                        <option value="{{ $Tenant->id }}">{{ $Tenant->Nama }}</option>
                                    @endforeach
                                </select>
                            </div>
                            @endif

                            <div class="col-md-{{ auth()->user()->role === 'Superadmin' ? '9' : '12' }}">
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
                        <!-- Akhir Filter Row -->

                        <div class="table-responsive">
                            <table class="table table-striped table-bordered dt-responsive nowrap align-middle mb-0" id="TagihanTable" style="width: 100%;">
                                <thead class="table-light">
                                    <tr>
                                        @if(auth()->user()->role === 'Superadmin')
                                        <th style="width: 40px;" class="text-center">
                                            <input type="checkbox" class="form-check-input" id="SelectAll" title="Pilih Semua">
                                        </th>
                                        @endif
                                        <th class="text-center">#</th>
                                        <th>No. Tagihan</th>
                                        @if(auth()->user()->role === 'Superadmin')
                                        <th>Nama Tenant</th>
                                        @else
                                        <th>Nama Tenant</th>
                                        @endif
                                        <th>Periode</th>
                                        <th class="text-center">Jatuh Tempo</th>
                                        <th class="text-center">Tanggal Bayar</th>
                                        <th class="text-end">Jumlah</th>
                                        <th class="text-center">Status</th>
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

    <!-- Modal Bulk Approve (Hanya untuk Superadmin) -->
    @if(auth()->user()->role === 'Superadmin')
    <div class="modal fade" id="BulkApproveModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-success bg-opacity-10 border-bottom-0">
                    <h5 class="modal-title fw-bold text-success-emphasis">
                        <i class="ti ti-checklist me-2"></i>Persetujuan Massal Tagihan
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <p class="text-muted small mb-0">
                        Anda akan mengubah status <strong id="ModalSelectedCount" class="text-dark">0</strong> tagihan terpilih menjadi <strong class="text-success">Lunas</strong>.<br>
                        <small class="text-danger">* Tindakan ini akan otomatis mengisi "Tanggal Bayar" dengan hari ini.</small>
                    </p>
                </div>
                <div class="modal-footer border-top-0 pt-0">
                    <button type="button" class="btn btn-light text-muted" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-success text-white fw-semibold px-4" id="BtnSubmitBulkApprove">
                        <i class="ti ti-check me-1"></i> Ya, Setujui Semua
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
            let SelectedIds = new Set();

            // 1. DataTables Init
            $('#TagihanTable').DataTable({
                responsive: true,
                serverSide: true,
                processing: true,
                destroy: true,
                autoWidth: false,
                ajax: {
                    url: "{{ route('tagihan-pembayaran.index') }}",
                    type: 'GET',
                    data: function (Data) {
                        @if(auth()->user()->role === 'Superadmin')
                        Data.TenantId = $('#FilterTenant').val();
                        @endif
                    }
                },
                drawCallback: function() {
                    @if(auth()->user()->role === 'Superadmin')
                        $('#SelectAll').prop('checked', false);
                        UpdateSelectedCount();
                    @endif
                },
                columnDefs: [
                    @if(auth()->user()->role === 'Superadmin')
                        // Superadmin: 0:Check, 1:#, 2:No, 3:Tenant, 4:Periode, 5:JatuhTempo, 6:TglBayar, 7:Jumlah, 8:Status, 9:Bukti, 10:Aksi
                        { className: 'text-center', targets: [0, 1, 5, 6, 8, 9, 10] },
                        { className: 'text-end', targets: [7] },
                        { orderable: false, targets: [0, 10] }
                    @else
                        // Admin: 0:#, 1:No, 2:Tenant, 3:Periode, 4:JatuhTempo, 5:TglBayar, 6:Jumlah, 7:Status, 8:Bukti, 9:Aksi
                        { className: 'text-center', targets: [0, 4, 5, 7, 8, 9] },
                        { className: 'text-end', targets: [6] },
                        { orderable: false, targets: [0, 9] }
                    @endif
                ],
                columns: [
                    @if(auth()->user()->role === 'Superadmin')
                    {
                        data: 'id', name: 'id', orderable: false, searchable: false,
                        render: function(Data, Type, Row) {
                            const IsChecked = SelectedIds.has(Data) ? 'checked' : '';
                            return `<input type="checkbox" class="form-check-input row-checkbox" value="${Data}" ${IsChecked}>`;
                        }
                    },
                    @endif
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', searchable: false },
                    { data: 'NomorTagihan', name: 'NomorTagihan' },
                    { data: 'NamaTenant', name: 'NamaTenant' },
                    { data: 'PeriodeBulan', name: 'PeriodeBulan' },
                    {
                        data: 'TanggalJatuhTempo', name: 'TanggalJatuhTempo',
                        render: function(Data) {
                            return Data ? new Date(Data).toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' }) : '-';
                        }
                    },
                    {
                        data: 'TanggalPembayaran', name: 'TanggalPembayaran',
                        render: function(Data) {
                            return Data ? new Date(Data).toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' }) : '-';
                        }
                    },
                    { data: 'JumlahTagihan', name: 'JumlahTagihan' },
                    { data: 'StatusPembayaran', name: 'StatusPembayaran' },
                    { data: 'BuktiPembayaran', name: 'BuktiPembayaran' },
                    { data: 'action', name: 'action', searchable: false }
                ],
                language: { url: "//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json" }
            });

            // 2. Logic Filter & Reset
            $('#BtnFilter').on('click', function() {
                $('#TagihanTable').DataTable().ajax.reload();
            });

            $('#BtnReset').on('click', function() {
                @if(auth()->user()->role === 'Superadmin')
                $('#FilterTenant').val('');
                @endif
                $('#TagihanTable').DataTable().ajax.reload();
            });

            // 3. Logic Hapus Data (Delete Handler)
            $('body').on('click', '.btn-hapus', function() {
                const Id = $(this).data('id');
                const Nomor = $(this).data('nomor');

                Swal.fire({
                    title: 'Hapus Tagihan?',
                    html: `Anda akan menghapus tagihan:<br><strong class="text-primary">${Nomor}</strong><br>Tindakan ini tidak dapat dibatalkan!`,
                    icon: 'warning', showCancelButton: true, confirmButtonColor: '#dc3545', cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Ya, Hapus!', cancelButtonText: 'Batal', reverseButtons: true
                }).then((Result) => {
                    if (Result.isConfirmed) {
                        $.ajax({
                            url: "{{ route('tagihan-pembayaran.destroy', ':id') }}".replace(':id', Id),
                            type: 'POST',
                            data: { _method: 'DELETE', _token: '{{ csrf_token() }}' },
                            beforeSend: function() { Swal.fire({ title: 'Menghapus...', text: 'Mohon tunggu sebentar', allowOutsideClick: false, didOpen: () => Swal.showLoading() }); },
                            success: function(Response) {
                                if (Response.success) {
                                    Swal.fire({ icon: 'success', title: 'Berhasil!', text: Response.message, timer: 2000, showConfirmButton: false });
                                    $('#TagihanTable').DataTable().ajax.reload(null, false);
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

            // 4. Logic Checkbox & Bulk Approve (Hanya untuk Superadmin)
            @if(auth()->user()->role === 'Superadmin')
            $('#TagihanTable tbody').on('change', '.row-checkbox', function() {
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
                $('#BtnBulkApprove').prop('disabled', Count === 0);
            }

            $('#BtnBulkApprove').on('click', function() {
                if (SelectedIds.size > 0) {
                    const Modal = new bootstrap.Modal(document.getElementById('BulkApproveModal'));
                    Modal.show();
                }
            });

            $('#BtnSubmitBulkApprove').on('click', function() {
                const Ids = Array.from(SelectedIds);

                Swal.fire({
                    title: 'Konfirmasi Persetujuan Massal',
                    html: `Anda akan menyetujui <strong>${Ids.length}</strong> tagihan menjadi status <strong class="text-success">Lunas</strong>.<br><span class="text-muted small">Lanjutkan proses ini?</span>`,
                    icon: 'warning', showCancelButton: true, confirmButtonColor: '#198754', cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Ya, Setujui!', cancelButtonText: 'Batal', reverseButtons: true
                }).then((Result) => {
                    if (Result.isConfirmed) {
                        Swal.fire({ title: 'Memproses...', text: 'Mohon tunggu sebentar', allowOutsideClick: false, didOpen: () => Swal.showLoading() });
                        $.ajax({
                            url: "{{ route('tagihan-pembayaran.bulkApprove') }}",
                            type: 'POST',
                            data: { _token: '{{ csrf_token() }}', Ids: Ids },
                            success: function(Response) {
                                Swal.fire({ icon: 'success', title: 'Berhasil!', text: Response.message, timer: 2000, showConfirmButton: false });
                                SelectedIds.clear();
                                bootstrap.Modal.getInstance(document.getElementById('BulkApproveModal')).hide();
                                $('#TagihanTable').DataTable().ajax.reload(null, false);
                            },
                            error: function(Xhr) {
                                Swal.fire('Gagal!', Xhr.responseJSON?.message || 'Terjadi kesalahan saat memproses.', 'error');
                            }
                        });
                    }
                });
            });
            @endif
        });
    </script>
@endpush
