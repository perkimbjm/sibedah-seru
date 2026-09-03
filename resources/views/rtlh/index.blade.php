@extends('layouts.main')

@php
    $menuName = 'Data RTLH Kab. Balangan';
    $currentRoute = ucwords(str_replace('.', ' / ', Route::currentRouteName()));
@endphp

@section('content')
@can('rtlh_create')
    <div style="margin-bottom: 10px;" class="row">
        <div class="col-lg-12">
            <a class="btn btn-success" href="{{ route('app.rtlh.create') }}">
                <i class="fas fa-plus"></i> Tambah Data
            </a>
            <a class="btn btn-info" href="{{ route('app.rutilahu.peta') }}">
                <i class="far fa-map"></i> Peta
            </a>
            <a class="btn btn-danger" href="{{ route('app.rtlh.import') }}">
                <i class="fas fa-file-excel"></i> Import Excel
            </a>
        </div>
    </div>
@endcan
<div class="card">
    <div class="card-header">
        Data RTLH
    </div>

    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover ajaxTable datatable datatable-User">
                <thead>
                    <tr>
                        <th rowspan="2" width="10" class="text-center align-middle"></th>
                        <th rowspan="2" class="text-center align-middle">ID</th>
                        <th rowspan="2" class="text-center align-middle">Nama</th>
                        <th rowspan="2" class="text-center align-middle">NIK</th>
                        <th rowspan="2" class="text-center align-middle">Alamat</th>
                        <th rowspan="2" class="text-center align-middle">Latitude</th>
                        <th rowspan="2" class="text-center align-middle">Longitude</th>
                        <th rowspan="2" class="text-center align-middle">Kecamatan</th>
                        <th rowspan="2" class="text-center align-middle">Jumlah Penghuni</th>
                        <th rowspan="2" class="text-center align-middle">Luas Bangunan Rumah</th>
                        <th colspan="3" class="text-center align-middle">Struktural</th>
                        <th colspan="3" class="text-center align-middle">Non Struktural</th>
                        <th colspan="2" class="text-center align-middle">Air Bersih</th>
                        <th colspan="3" class="text-center align-middle">Sanitasi</th>
                        <th rowspan="2" class="text-center align-middle">Penilaian Keselamatan Bangunan</th>
                        <th rowspan="2" class="text-center align-middle">Penilaian Keselamatan Bangunan dan Sanitasi</th>
                        <th rowspan="2" class="text-center align-middle">Status Perbaikan</th>
                        <th rowspan="2" class="text-center align-middle no-export">Galeri Foto</th>
                        <th rowspan="2" class="text-center align-middle">&nbsp;</th>
                    </tr>
                    <tr>
                        <th class="text-center align-middle">Pondasi</th>
                        <th class="text-center align-middle">Kolom / Balok</th>
                        <th class="text-center align-middle">Rangka Atap</th>
                        <th class="text-center align-middle">Atap</th>
                        <th class="text-center align-middle">Dinding</th>
                        <th class="text-center align-middle">Lantai</th>
                        <th class="text-center align-middle">Sumber Air Bersih</th>
                        <th class="text-center align-middle">Jarak ke Pembuang Tinja</th>
                        <th class="text-center align-middle">Fasilitas BAB</th>
                        <th class="text-center align-middle">Jenis</th>
                        <th class="text-center align-middle">TPA Tinja</th>
                    </tr>
                </thead>

            </table>
        </div>
    </div>
</div>



@endsection
@section('scripts')
@parent
@include('app.index-script')
<script>
$(function () {
  let dtButtons = $.extend(true, [], $.fn.dataTable.defaults.buttons)
@can('rtlh_delete')
  let deleteButtonTrans = 'Multi Delete';
  let deleteButton = {
    text: deleteButtonTrans,
    url: "{{ route('app.rtlh.massDestroy') }}",
    className: 'btn-danger',
    action: function (e, dt, node, config) {
      var ids = $.map(dt.rows({ selected: true }).data(), function (entry) {
          return entry.id
      });

      if (ids.length === 0) {
        alert('belum ada data yang dipilih')

        return
      }

      if (confirm('Apakah kamu Yakin ingin menghapus ?')) {
        $.ajax({
          headers: {'x-csrf-token': _token},
          method: 'POST',
          url: config.url,
          data: { ids: ids, _method: 'DELETE' }})
          .done(function () { location.reload() })
      }
    }
  }
  dtButtons.push(deleteButton)
@endcan

  // Indeks kolom Latitude (5) & Longitude (6) sesuai urutan kolom tabel di bawah.
  // Nilai koordinat dipaksa menjadi teks saat ekspor agar Excel tidak mengubahnya
  // menjadi angka desimal (presisi terpotong / notasi ilmiah).
  const coordColumnIndexes = [5, 6];
  // Kolom NIK (indeks 3) selalu diperlakukan sebagai teks berapa pun jumlah digitnya.
  const nikColumnIndexes = [3];
  const exportFormatBodyRtlh = function (data, row, column, node) {
    let cleanData = data;
    if (typeof cleanData === 'string') {
      // Buang seluruh elemen tautan (beserta teksnya), mis. "Lihat Data" pada kolom
      // Status Perbaikan — tautan adalah aksi, bukan data yang perlu diekspor.
      cleanData = cleanData.replace(/<a\b[^>]*>[\s\S]*?<\/a>/gi, ' ');
      // Bersihkan tag HTML dan decode entity, samakan dengan format ekspor default.
      cleanData = cleanData.replace(/<[^>]*>/g, ' ').trim();
      let txt = document.createElement('textarea');
      txt.innerHTML = cleanData;
      cleanData = txt.value.replace(/\s+/g, ' ').trim();
    }
    if (cleanData === null || cleanData === undefined || cleanData === '') {
      return cleanData;
    }
    // Zero Width Non-Joiner memaksa Excel/Sheets memperlakukan nilai sebagai teks.
    if (coordColumnIndexes.indexOf(column) !== -1) {
      return '\u200C' + cleanData;
    }
    // Paksa kolom NIK, serta setiap rangkaian angka panjang (>= 15 digit), menjadi teks.
    // Ini mencegah Excel me-right-align (tipe number) dan memangkas presisi angka besar
    // (mis. NIK 17 digit yang berubah menjadi ...0000), termasuk NIK yang bukan 16 digit.
    if (typeof cleanData === 'string' && /^\d+$/.test(cleanData) &&
        (nikColumnIndexes.indexOf(column) !== -1 || cleanData.length >= 15)) {
      return '\u200C' + cleanData;
    }
    return cleanData;
  };
  const exportButtonKinds = ['copy', 'csv', 'excel', 'pdf', 'print'];
  dtButtons.forEach(function (btn) {
    if (!btn || exportButtonKinds.indexOf(btn.extend) === -1) {
      return;
    }
    btn.exportOptions = btn.exportOptions || {};
    // Kecualikan kolom Galeri (ditandai class .no-export) dari hasil ekspor.
    if (typeof btn.exportOptions.columns === 'string') {
      if (btn.exportOptions.columns.indexOf('.no-export') === -1) {
        btn.exportOptions.columns += ':not(.no-export)';
      }
    } else {
      btn.exportOptions.columns = ':visible:not(.no-export)';
    }
    if (btn.extend === 'csv' || btn.extend === 'excel' || btn.extend === 'copy') {
      btn.exportOptions.format = btn.exportOptions.format || {};
      btn.exportOptions.format.body = exportFormatBodyRtlh;
    }
  });

    let dtOverrideGlobals = {
        buttons: dtButtons,
        processing: true,
        serverSide: true,
        retrieve: true,
        aaSorting: [],
        ajax: "{{ route('app.rtlh.index') }}",
        columns: [
            {data: 'placeholder', name: 'placeholder', orderable: false, searchable: false},
            {data: 'id', name: 'id'},
            {data: 'name', name: 'name'},
            {data: 'nik', name: 'nik'},
            {data: 'address', name: 'address'},
            {data: 'lat', name: 'lat'},
            {data: 'lng', name: 'lng'},
            {data: 'district_name', name: 'district_name'},
            {data: 'people', name: 'people'},
            {data: 'area', name: 'area'},
            {data: 'pondasi', name: 'pondasi'},
            {data: 'kolom_blk', name: 'kolom_blk'},
            {data: 'rngk_atap', name: 'rngk_atap'},
            {data: 'atap', name: 'atap'},
            {data: 'dinding', name: 'dinding'},
            {data: 'lantai', name: 'lantai'},
            {data: 'air', name: 'air'},
            {data: 'jarak_tinja', name: 'jarak_tinja'},
            {data: 'wc', name: 'wc'},
            {data: 'jenis_wc', name: 'jenis_wc'},
            {data: 'tpa_tinja', name: 'tpa_tinja'},
            {data: 'status_safety', name: 'status_safety'},
            {data: 'status', name: 'status'},
            {data: 'status_perbaikan', name: 'status_perbaikan'},
            {data: 'gallery', name: 'gallery',orderable: false, searchable: false },
            {data: 'action', name: 'action', orderable: false, searchable: false}
        ],
        order: [[1, 'asc']],
        pageLength: 10,
        dom: '<"dataTables_length"l>Bfrtip',
        lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "Semua"]]
    };
        let table = $('.datatable-User').DataTable(dtOverrideGlobals);
  $('a[data-toggle="tab"]').on('shown.bs.tab click', function(e){
      $($.fn.dataTable.tables(true)).DataTable()
          .columns.adjust();
  });

let visibleColumnsIndexes = null;
$('.datatable thead').on('input', '.search', function () {
      let strict = $(this).attr('strict') || false
      let value = strict && this.value ? "^" + this.value + "$" : this.value

      let index = $(this).parent().index()
      if (visibleColumnsIndexes !== null) {
        index = visibleColumnsIndexes[index]
      }

      table
        .column(index)
        .search(value, strict)
        .draw()
  });
table.on('column-visibility.dt', function(e, settings, column, state) {
      visibleColumnsIndexes = []
      table.columns(":visible").every(function(colIdx) {
          visibleColumnsIndexes.push(colIdx);
      });
  })
});
</script>
@endsection
