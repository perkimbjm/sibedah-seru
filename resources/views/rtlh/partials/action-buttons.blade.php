@can('rtlh_show')
    <a href="{{ route('app.rtlh.show', $row->id) }}" class="btn btn-xs btn-primary">Lihat</a>
@endcan
@can('rtlh_edit')
    <a href="{{ route('app.rtlh.edit', $row->id) }}" class="btn btn-xs btn-info">Edit</a>
@endcan

@can('rtlh_delete')
<a href="{{ route('app.rtlh.destroy', $row->id) }}" class="btn btn-xs btn-danger">Hapus</a>
@endcan