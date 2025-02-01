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
                        <th>
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
