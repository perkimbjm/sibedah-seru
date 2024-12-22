@can($viewGate)
    <a class="btn btn-xs btn-primary mt-2" href="{{ route('app.' . $crudRoutePart . '.show', $row->id) }}" data-toggle="tooltip" title="Lihat">
        <i class="fas fa-eye"></i> Lihat
    </a>
@endcan
@can($editGate)
    <a class="btn btn-xs btn-success mt-2" href="{{ route('app.' . $crudRoutePart . '.edit', $row->id) }}">
        <i class="fas fa-edit"></i> Edit
    </a>
@endcan
@can($deleteGate)
    <form action="{{ route('app.' . $crudRoutePart . '.destroy', $row->id) }}" method="POST" style="display: inline-block;">
        <input type="hidden" name="_method" value="DELETE">
        <input type="hidden" name="_token" value="{{ csrf_token() }}">
        <button type="submit" class="btn btn-xs btn-danger show_confirm mt-2">
            <i class="fas fa-trash-alt"></i> Hapus</button>
    </form>
@endcan

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script type="text/javascript">
    $('.show_confirm').click(function(event) {
          var form =  $(this).closest("form");
          var name = $(this).data("name");
          event.preventDefault();
          Swal.fire({
              title: 'Apakah Kamu Yakin ?',
              icon: "warning",
              showCancelButton: true,
              confirmButtonColor: '#3085d6',
              cancelButtonColor: '#d33',
              confirmButtonText: 'Apakah Kamu Yakin ?',
              cancelButtonText: 'Batal'
          }).then((willDelete) => {
            if (willDelete.isConfirmed) {
              form.submit();

              Swal.fire(
                'Hapus',
                'Penghapusan Data Berhasil',
                'success'
                )
            }
          });
      });
</script>