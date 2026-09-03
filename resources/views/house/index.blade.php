@extends('layouts.main')

@php
    $menuName = 'Penerima Bantuan Bedah Rumah';
    $currentRoute = ucwords(str_replace('.', ' / ', Route::currentRouteName()));
@endphp

@section('content')
@can('house_create')
    <div style="margin-bottom: 10px;" class="row">
        <div class="col-lg-12">
            <a class="btn btn-success" href="{{ route('app.houses.create') }}">
                <i class="fas fa-plus"></i> Tambah Data
            </a>
            <a class="btn btn-info" href="{{ route('app.bedah.peta') }}">
                <i class="far fa-map"></i> Peta
            </a>
            <a class="btn btn-danger" href="{{ route('app.houses.import') }}">
                <i class="fas fa-file-excel"></i> Import Excel
            </a>
        </div>
    </div>
@endcan
<div class="card">
    <div class="card-header">
        Penerima Bantuan
    </div>

    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover datatable datatable-House">
                <thead>
                    <tr>
                        <th width="10">

                        </th>
                        <th>
                            ID
                        </th>
                        <th>
                            Nama
                        </th>
                        <th>
                            NIK
                        </th>
                        <th>
                            Alamat
                        </th>
                        <th>
                            Latitude
                        </th>
                        <th>
                            Longitude
                        </th>
                        <th>
                            Kecamatan
                        </th>
                        <th>
                            Tahun
                        </th>
                        <th>
                            Jenis Bantuan
                        </th>
                        <th>
                            Sumber Dana
                        </th>
                        <th class="no-export">
                            Galeri
                        </th>

                        <th>
                            &nbsp;
                        </th>
                    </tr>

                </thead>
                <tbody>
                    @foreach($houses as $key => $house)
                        <tr data-entry-id="{{ $house->id }}">
                            <td>

                            </td>
                            <td>
                                {{ $house->id ?? '' }}
                            </td>
                            <td>
                                {{ $house->name ?? '' }}
                            </td>
                            <td>
                                {{ $house->nik ?? '' }}
                            </td>
                            <td>
                                {{ $house->address?? '' }}
                            </td>
                            <td>
                                {{ $house->lat ?? '' }}
                            </td>
                            <td>
                                {{ $house->lng ?? '' }}
                            </td>
                            <td>
                                {{ $house->district ?? '' }}
                            </td>

                            <td>
                                {{ $house->year?? '' }}
                            </td>

                            <td>
                                {{ $house->type ?? '' }}
                            </td>

                            <td>
                                {{ $house->source ?? '' }}
                            </td>
                            <td>
                                <a class="font-bold btn btn-sm btn-warning text-dark" href="{{ route('app.gallery.index', $house) }}">Lihat Foto</a>
                            </td>

                            <td>
                                @can('house_show')
                                    <a class="btn btn-xs btn-primary" href="{{ route('app.houses.show', $house->id) }}">
                                        Lihat
                                    </a>
                                @endcan

                                @can('house_edit')
                                    <a class="btn btn-xs btn-info" href="{{ route('app.houses.edit', $house->id) }}">
                                        Edit
                                    </a>
                                @endcan

                                @can('house_delete')
                                    <form action="{{ route('app.houses.destroy', $house->id) }}" method="POST" onsubmit="return confirm('Apakah Anda Yakin ?');" style="display: inline-block;">
                                        <input type="hidden" name="_method" value="DELETE">
                                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                        <input type="submit" class="btn btn-xs btn-danger" value="Hapus">
                                    </form>
                                @endcan

                            </td>

                        </tr>
                    @endforeach
                </tbody>
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
@can('house_delete')
  let deleteButtonTrans = 'Hapus'
  let deleteButton = {
    text: deleteButtonTrans,
    url: "{{ route('app.houses.massDestroy') }}",
    className: 'btn-danger',
    action: function (e, dt, node, config) {
      var ids = $.map(dt.rows({ selected: true }).nodes(), function (entry) {
          return $(entry).data('entry-id')
      });

      if (ids.length === 0) {
        alert('Tidak Ada Data yang DIpilih')

        return
      }

      if (confirm('Apakah Anda Yakin ?')) {
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

  // Indeks kolom Latitude (5) & Longitude (6) sesuai urutan kolom tabel.
  // Nilai koordinat dipaksa menjadi teks saat ekspor agar Excel tidak mengubahnya
  // menjadi angka desimal (presisi terpotong / notasi ilmiah).
  const coordColumnIndexes = [5, 6];
  // Kolom NIK (indeks 3) selalu diperlakukan sebagai teks berapa pun jumlah digitnya.
  const nikColumnIndexes = [3];
  const exportFormatBodyHouse = function (data, row, column, node) {
    let cleanData = data;
    if (typeof cleanData === 'string') {
      // Buang seluruh elemen tautan (beserta teksnya) — tautan adalah aksi, bukan data.
      cleanData = cleanData.replace(/<a\b[^>]*>[\s\S]*?<\/a>/gi, ' ');
      // Bersihkan tag HTML dan decode entity.
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
      btn.exportOptions.format.body = exportFormatBodyHouse;
    }
  });

  $.extend(true, $.fn.dataTable.defaults, {
    orderCellsTop: true,
    order: [[ 1, 'asc' ]], // Urutkan berdasarkan ID
    pageLength: 10,
  });
  let table = $('.datatable-House:not(.ajaxTable)').DataTable({ buttons: dtButtons })
  $('a[data-toggle="tab"]').on('shown.bs.tab click', function(e){
      $($.fn.dataTable.tables(true)).DataTable()
          .columns.adjust();
  });

})

</script>
@endsection
