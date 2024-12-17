@extends('layouts.main')

@php
    $menuName = 'Hak Akses';
    $currentRoute = ucwords(str_replace('.', ' / ', Route::currentRouteName()));
@endphp

@section('content')
@can('permission_create')
    <div style="margin-bottom: 10px;" class="row">
        <div class="col-lg-12">
            <a class="btn btn-success" href="{{ route('app.permissions.create') }}">
                <i class="fas fa-plus"></i> Tambah Hak Akses
            </a>
        </div>
    </div>
@endcan
<div class="card">
    <div class="card-header">
        Izin Akses
    </div>

    <div class="card-body">
        <div class="table-responsive">
            <table class=" table table-bordered table-striped table-hover datatable datatable-Permission">
                <thead>
                    <tr>
                        <th width="10">

                        </th>
                        <th>
                            ID
                        </th>
                        <th>
                            Jenis Hak Akses
                        </th>
                        <th>
                            &nbsp;
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($permissions as $key => $permission)
                        <tr data-entry-id="{{ $permission->id }}">
                            <td>

                            </td>
                            <td>
                                {{ $permission->id ?? '' }}
                            </td>
                            <td>
                                {{ $permission->name ?? '' }}
                            </td>
                            <td>
                                @can('permission_edit')
                                    <a class="btn btn-xs btn-info" href="{{ route('app.permissions.edit', $permission->id) }}">
                                        Edit
                                    </a>
                                @endcan


                                @can('permission_delete')
                                <form action="{{ route('app.permissions.destroy', $permission->id) }}" method="POST"
                                        onsubmit="return confirm('Apakah Kamu Yakin ?')" style="display: inline-block;">
                                        {{ method_field('DELETE') }}
                                        {{ csrf_field() }}
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
@can('permission_delete')
  let deleteButtonTrans = 'Hapus'
  let deleteButton = {
    text: deleteButtonTrans,
    url: "{{ route('app.permissions.massDestroy') }}",
    url: "",
    className: 'btn-danger',
    action: function (e, dt, node, config) {
      var ids = $.map(dt.rows({ selected: true }).nodes(), function (entry) {
          return $(entry).data('entry-id')
      });

      if (ids.length === 0) {
        alert('Tidak ada yang dipilih') }}')

        return
      }

      if (confirm('Apakah Kamu Yakin ?')) {
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
    order: [[ 1, 'desc' ]],
    pageLength: 100,
  });
  let table = $('.datatable-Permission:not(.ajaxTable)').DataTable({ buttons: dtButtons })
  $('a[data-toggle="tab"]').on('shown.bs.tab click', function(e){
      $($.fn.dataTable.tables(true)).DataTable()
          .columns.adjust();
  });
  
})

</script>
@endsection
