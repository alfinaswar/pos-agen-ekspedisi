@extends('layouts.app')

@section('title', 'Data Ekspedisi')
@section('page-title', 'Data Ekspedisi')

@section('content')
<div class="card">
    <div class="card-header bg-white py-3">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h5 class="mb-0">Daftar Ekspedisi</h5>
            </div>
            <div class="col-md-6 text-md-end">
                <a href="{{ route('ekspedisi.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-lg me-2"></i>Tambah Ekspedisi
                </a>
            </div>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table id="tableEkspedisi" class="table table-striped table-hover" style="width:100%">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Ekspedisi</th>
                        <th>Deskripsi</th>
                        <th>Dibuat Oleh</th>
                        <th>Tanggal Dibuat</th>
                        <th width="120">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Delete -->
<div class="modal fade" id="modalDelete" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Konfirmasi Hapus</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin menghapus ekspedisi ini?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-danger" id="btnConfirmDelete">Hapus</button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">
@endpush

@push('scripts')
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.min.js"></script>

<script>
    let table;
    let deleteId = null;

    $(document).ready(function() {
        table = $('#tableEkspedisi').DataTable({
            processing: true,
            serverSide: false,
            ajax: '{{ route("ekspedisi.data") }}',
            columns: [
                { data: null, defaultContent: '', className: 'text-center', width: '50px' },
                { data: 'NamaEkspedisi', name: 'NamaEkspedisi' },
                { data: 'Deskripsi', name: 'Deskripsi' },
                { data: 'UserCreate', name: 'UserCreate' },
                { data: 'created_at', name: 'created_at' },
                { data: 'actions', name: 'actions', orderable: false, searchable: false, className: 'text-center' }
            ],
            order: [[4, 'desc']],
            responsive: true,
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/id.json'
            },
            drawCallback: function(settings) {
                var api = this.api();
                api.nodes().each(function(row, rowIndex) {
                    var cell = api.cell(row, 0).node();
                    $(cell).html(rowIndex + 1);
                });
            }
        });
    });

    function editEkspedisi(id) {
        window.location.href = '/ekspedisi/' + id + '/edit';
    }

    function deleteEkspedisi(id) {
        deleteId = id;
        $('#modalDelete').modal('show');
    }

    $('#btnConfirmDelete').click(function() {
        if (deleteId) {
            $.ajax({
                url: '/ekspedisi/' + deleteId,
                type: 'DELETE',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    $('#modalDelete').modal('hide');
                    table.ajax.reload();
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: 'Ekspedisi berhasil dihapus',
                        timer: 2000
                    });
                },
                error: function(xhr) {
                    $('#modalDelete').modal('hide');
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Terjadi kesalahan saat menghapus data'
                    });
                }
            });
        }
    });

    @if(session('success'))
    Swal.fire({
        icon: 'success',
        title: 'Berhasil',
        text: '{{ session('success') }}',
        timer: 2000
    });
    @endif
</script>
@endpush
