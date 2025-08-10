@extends('layouts.main')

@php
    $menuName = 'Usulan Masyarakat';
    $currentRoute = ucwords(str_replace('.', ' / ', Route::currentRouteName()));
@endphp

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h3 class="card-title">Daftar Usulan</h3>
        @php
            $user = Auth::user();
            $isAdminOrTfl = $user->role_id == 1 || $user->role_id == 2 || $user->role_id == 10 ||
                           $user->hasRole(['Super Admin', 'Admin', 'tfl']);
        @endphp
        @if(Gate::allows('usulan_create'))
        <a href="{{ route('usulan.proposals.create') }}" class="btn btn-primary">
            <i class="mr-2 fas fa-plus"></i>Tambah Usulan
        </a>
        @endif
    </div>

    <div class="card-body">
        @if($isAdminOrTfl)
        <!-- Stats Cards -->
        <div class="mb-4 row">
            <div class="col-md-3">
                <div class="info-box bg-info">
                    <span class="info-box-icon"><i class="fas fa-clock"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Menunggu Verifikasi</span>
                        <span class="info-box-number" id="pending-count">-</span>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="info-box bg-success">
                    <span class="info-box-icon"><i class="fas fa-check"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Diterima</span>
                        <span class="info-box-number" id="accepted-count">-</span>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="info-box bg-danger">
                    <span class="info-box-icon"><i class="fas fa-times"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Ditolak</span>
                        <span class="info-box-number" id="rejected-count">-</span>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="info-box bg-warning">
                    <span class="info-box-icon"><i class="fas fa-exclamation-triangle"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">> 5 Hari</span>
                        <span class="info-box-number" id="old-count">-</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter -->
        <div class="mb-3 row">
            <div class="col-md-3">
                <select id="status-filter" class="form-control">
                    <option value="">Semua Status</option>
                    <option value="pending">Menunggu Verifikasi</option>
                    <option value="accepted">Diterima</option>
                    <option value="rejected">Ditolak</option>
                </select>
            </div>
            <div class="col-md-3">
                <select id="jenis-filter" class="form-control">
                    <option value="">Semua Jenis</option>
                    <option value="RTLH">RTLH</option>
                    <option value="Rumah Korban Bencana">Rumah Korban Bencana</option>
                </select>
            </div>
            <div class="col-md-3">
                <input type="text" id="search-input" class="form-control" placeholder="Cari nama atau NIK...">
            </div>
            <div class="col-md-3">
                <button id="reset-filter" class="btn btn-secondary">
                    <i class="mr-2 fas fa-refresh"></i>Reset Filter
                </button>
            </div>
        </div>
        @endif

        <!-- Table -->
        <div class="table-responsive">
            <table id="usulan-table" class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama</th>
                        <th>NIK</th>
                        <th>Jenis Usulan</th>
                        <th>Lokasi</th>
                        <th>Status</th>
                        <th>Tanggal</th>
                        <th>Foto</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Data akan diisi oleh DataTables -->
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Detail Usulan -->
<div class="modal fade" id="detailModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detail Usulan</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body" id="detail-content">
                <!-- Content akan diisi via AJAX -->
            </div>
        </div>
    </div>
</div>
@endsection

@push('after-style')
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap4.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.2.2/css/buttons.bootstrap4.min.css">
@endpush

@push('after-script')
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap4.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.bootstrap4.min.js"></script>

<script>
$(document).ready(function() {
    const tableElement = $('#usulan-table');
    if (tableElement.length === 0) {
        console.error('Table element not found');
        return;
    }

    var table = tableElement.DataTable({
                  processing: true,
          searching: false, // Menghilangkan search bar default
          serverSide: true,
        ajax: {
            url: '{{ route("usulan.proposals.index") }}',
            data: function(d) {
                d.status = $('#status-filter').val() || '';
                d.jenis = $('#jenis-filter').val() || '';
                d.search = $('#search-input').val() || '';
            },
            error: function(xhr, error, code) {
                console.error('AJAX Error:', {error, code, response: xhr.responseText});
            }
        },
        columns: [
            {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
            {data: 'nama', name: 'nama', orderable: true, searchable: true},
            {data: 'nik', name: 'nik', orderable: true},
            {data: 'jenis_usulan_badge', name: 'jenis_usulan', type: 'html', orderable: true},
            {data: 'village_name', name: 'village.name', orderable: true, searchable: true },
            {data: 'status_badge', name: 'status', type: 'html', orderable: true},
            {data: 'created_at', name: 'created_at', orderable: true, searchable: true},
            {data: 'foto_preview', name: 'foto_rumah', orderable: false, searchable: false},
            {data: 'action', name: 'action', orderable: false, searchable: false}
        ],
        // Multi-column default ordering: Status pending first, then by created_at desc
        order: [
            [5, 'asc'],  // status_badge column (pending first)
            [6, 'desc']  // created_at column (newest first)
        ],
        orderMulti: true, // Allow multiple column sorting
        pageLength: 25,
        language: {
            url: '//cdn.datatables.net/plug-ins/1.11.5/i18n/id.json'
        }
    });

    // Event handlers dengan null checks
    $('#status-filter, #jenis-filter').on('change', function() {
        if (table) {
            table.draw();
            // Reset to default ordering when filters change
            if ($('#status-filter').val() === '' && $('#jenis-filter').val() === '') {
                table.order([[5, 'asc'], [6, 'desc']]).draw();
            }
        }
    });

    $('#search-input').on('keyup', function() {
        if (table) table.draw();
    });

    $('#reset-filter').on('click', function() {
        $('#status-filter, #jenis-filter, #search-input').val('');
        if (table) {
            // Reset to default multi-column ordering
            table.order([[5, 'asc'], [6, 'desc']]).draw();
        }
    });

    // Add visual indicator for default sorting
    table.on('order.dt', function() {
        const order = table.order();
        const isDefaultOrder = order.length === 2 &&
                              order[0][0] === 5 && order[0][1] === 'asc' &&
                              order[1][0] === 6 && order[1][1] === 'desc';

        if (isDefaultOrder) {
            $('.dt-ordering-indicator').remove();
            $('#usulan-table thead th:eq(5)').append('<span class="dt-ordering-indicator text-primary"> ↑</span>');
            $('#usulan-table thead th:eq(6)').append('<span class="dt-ordering-indicator text-primary"> ↓</span>');
        } else {
            $('.dt-ordering-indicator').remove();
        }
    });

    @if($isAdminOrTfl)
    // Filter events with existence checks
    const statusFilter = $('#status-filter');
    const jenisFilter = $('#jenis-filter');
    const searchInput = $('#search-input');
    const resetButton = $('#reset-filter');

    if (statusFilter.length && jenisFilter.length) {
        statusFilter.add(jenisFilter).change(function() {
            if (table) table.draw();
        });
    }

    if (searchInput.length) {
        searchInput.on('keyup', function() {
            if (table) table.draw();
        });
    }

    if (resetButton.length) {
        resetButton.click(function() {
            if (statusFilter.length) statusFilter.val('');
            if (jenisFilter.length) jenisFilter.val('');
            if (searchInput.length) searchInput.val('');
            if (table) table.draw();
        });
    }

    // Load stats with error handling
    function loadStats() {
        $.get('{{ route("usulan.verifikasi-management.stats") }}')
            .done(function(data) {
                const pendingCount = $('#pending-count');
                const acceptedCount = $('#accepted-count');
                const rejectedCount = $('#rejected-count');
                const oldCount = $('#old-count');

                if (pendingCount.length) pendingCount.text(data.pending_usulan || 0);
                if (acceptedCount.length) acceptedCount.text(data.accepted_usulan || 0);
                if (rejectedCount.length) rejectedCount.text(data.rejected_usulan || 0);
                if (oldCount.length) oldCount.text(data.older_than_5_days || 0);
            })
            .fail(function(xhr, status, error) {
                console.warn('Gagal memuat statistik:', error);
            });
    }

    // Load stats initially
    loadStats();

    // Auto refresh stats every 30 seconds
    setInterval(loadStats, 30000);
    @endif
});
</script>
@endpush
