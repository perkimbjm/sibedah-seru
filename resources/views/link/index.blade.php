@extends('layouts.main')
@php
    $menuName = 'Daftar Link';
    $currentRoute = ucwords(str_replace('.', ' / ', Route::currentRouteName()));
@endphp

@section('content')
@can('link_create')
    <div style="margin-bottom: 10px;" class="row">
        <div class="col-lg-12">
            <a class="btn btn-success" href="{{ route('app.links.create') }}">
                <i class="fas fa-plus"></i> Tambah Link
            </a>
        </div>
    </div>
@endcan
<div class="card">
    <div class="card-header">
        Link / Tautan di Halaman Depan
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
                            Nama Tautan
                        </th>
                        <th>
                            Link URL
                        </th>
                        <th>
                            &nbsp;
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($links as $key => $link)
                        <tr data-entry-id="{{ $link->id }}">
                            <td>

                            </td>
                            <td>
                                {{ $link->id ?? '' }}
                            </td>
                            <td>
                                {{ $link->title ?? '' }}
                            </td>
                            <td>
                                {{ $link->url ?? '' }}
                            </td>                           
                            <td>
                                @can('link_edit')
                                    <a class="btn btn-xs btn-info" href="{{ route('app.links.edit', $link->id) }}">
                                        Edit
                                    </a>
                                @endcan

                                @can('link_delete')
                                    <form action="{{ route('app.links.destroy', $link->id) }}" method="POST" onsubmit="return confirm('Apakah Anda Yakin ?')" style="display: inline-block;">
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
@can('link_delete')
  let deleteButtonTrans = 'Multi Delete'
  let deleteButton = {
    text: deleteButtonTrans,
    url: "{{ route('app.links.massDestroy') }}",
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
    order: [[ 2, 'desc' ]], // Urutkan berdasarkan ID
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
