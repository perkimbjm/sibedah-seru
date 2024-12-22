@can('rtlh_show')
    <a href="{{ route('app.rtlh.show', $row->id) }}" class="btn btn-xs btn-primary"><i class="fas fa-eye"></i> Lihat</a>
@endcan
@can('rtlh_edit')
    <a href="{{ route('app.rtlh.edit', $row->id) }}" class="btn btn-xs btn-info"><i class="fas fa-edit"></i> Edit</a>
@endcan

@can('rtlh_delete')
<form action="{{ route('app.rtlh.destroy', $row->id) }}" method="POST" onsubmit="return confirm('Apakah Anda Yakin ?');" style="display: inline-block;">
    <input type="hidden" name="_method" value="DELETE">
    <input type="hidden" name="_token" value="{{ csrf_token() }}">
    <button type="submit" class="btn btn-xs btn-danger"><i class="fas fa-trash-alt"></i> Hapus</button>
</form>
@endcan