@extends('layouts.main')

@php
    $menuName = 'Unduh Dokumen';
    $currentRoute = ucwords(str_replace('.', ' / ', Route::currentRouteName()));
@endphp

@section('content')
@can('download_create')
    <div style="margin-bottom: 10px;" class="row">
        <div class="col-lg-12">
            <a class="btn btn-success" href="{{ route('app.downloads.create') }}">
                <i class="fas fa-plus"></i> Tambah File
            </a>
        </div>
    </div>
@endcan
<div class="card">
    <div class="card-header">
        Daftar File dapat Didownload
    </div>

    <div class="card-body">
        <div class="table-responsive">
            <table class=" table table-bordered table-striped table-hover datatable datatable-User">
                <thead>
                    <tr>
                        <th width="10">

                        </th>
                        <th>
                            ID
                        </th>
                        <th>
                            Jenis Peraturan
                        </th>
                        <th>
                            Nomor / Tahun
                        </th>
                        <th>
                            Deskripsi
                        </th>
                        <th>
                            Link Download
                        </th>
                        <th>
                            &nbsp;
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($downloads as $key => $download)
                        <tr data-entry-id="{{ $download->id }}">
                            <td>

                            </td>
                            <td>
                                {{ $download->id ?? '' }}
                            </td>
                            <td>
                                {{ $download->title ?? '' }}
                            </td>
                            <td>
                                {{ $download->year ?? '' }}
                            </td>
                            <td>
                                {{ strlen($download->description ?? '') > 150 ? ucfirst(substr($download->description, 0, 50)) . '...' : ucfirst($download->description) }}
                            </td>
                            <td>
                                {{ $download->file_url ?? '' }}
                            
                            <td>
                                @can('download_show')
                                    <a class="btn btn-xs btn-primary" href="{{ route('app.downloads.show', $download->id) }}">
                                        Lihat
                                    </a>
                                @endcan

                                @can('download_edit')
                                    <a class="btn btn-xs btn-info" href="{{ route('app.downloads.edit', $download->id) }}">
                                        Edit
                                    </a>
                                @endcan

                                @can('download_delete')
                                    <form action="{{ route('app.downloads.destroy', $download->id) }}" method="POST" onsubmit="return confirm('Apakah Anda Yakin ?')" style="display: inline-block;">
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
<script>
    $(function () {
  let dtButtons = $.extend(true, [], $.fn.dataTable.defaults.buttons)
@can('download_delete')
  let deleteButtonTrans = 'Multi Delete'
  let deleteButton = {
    text: deleteButtonTrans,
    className: 'btn-danger',
    action: function (e, dt, node, config) {
      var ids = $.map(dt.rows({ selected: true }).nodes(), function (entry) {
          return $(entry).data('entry-id')
      });

      if (ids.length === 0) {
        alert('Tidak ada Data Yang Dipilih')

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
  let table = $('.datatable-User:not(.ajaxTable)').DataTable({ buttons: dtButtons })
  $('a[data-toggle="tab"]').on('shown.bs.tab click', function(e){
      $($.fn.dataTable.tables(true)).DataTable()
          .columns.adjust();
  });
  
})

</script>
@endsection
