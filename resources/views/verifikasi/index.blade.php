@extends('layouts.main')

@php
    $menuName = 'Verifikasi Usulan';
    $currentRoute = ucwords(str_replace('.', ' / ', Route::currentRouteName()));
@endphp

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Daftar Verifikasi Usulan</h3>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped" id="verifikasi-table">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama Pengusul</th>
                                    <th>Nama (yang diusulkan)</th>
                                    <th>NIK</th>
                                    <th>Jenis Usulan</th>
                                    <th>Status</th>
                                    <th>Tanggal Usulan</th>
                                    <th>Verifikator</th>
                                    <th>Hasil Verifikasi</th>
                                    <th>Tanggal Verifikasi</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('after-style')
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap4.min.css">
@endpush

@push('after-script')
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap4.min.js"></script>

<script>
$(document).ready(function() {
    $('#verifikasi-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '{{ route("usulan.verifikasi-management.verifikasi.index") }}',
        },
        columns: [
            {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
            {data: 'nama_pengusul', name: 'nama_pengusul'},
            {data: 'nama_usulan', name: 'nama_usulan'},
            {data: 'nik', name: 'nik'},
            {data: 'jenis_usulan', name: 'jenis_usulan'},
            {data: 'status', name: 'status', orderable: false},
            {data: 'tanggal_usulan', name: 'tanggal_usulan'},
            {data: 'verifikator', name: 'verifikator'},
            {data: 'hasil_verifikasi', name: 'hasil_verifikasi', orderable: false},
            {data: 'tanggal_verifikasi', name: 'tanggal_verifikasi'},
            {data: 'action', name: 'action', orderable: false, searchable: false}
        ],
        order: [[6, 'desc']],
        pageLength: 25,
        language: {
            url: '//cdn.datatables.net/plug-ins/1.11.5/i18n/id.json'
        }
    });
});
</script>
@endpush
