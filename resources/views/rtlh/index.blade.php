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
        </div>
    </div>
@endcan
<div class="card">
    <div class="card-header">
        Data RTLH
    </div>

    <div class="card-body">
        <div class="table-responsive">
            <table class=" table table-bordered table-striped table-hover datatable datatable-User">
                <thead>
                    <tr>
                        <th rowspan="2" width="10" class="text-center align-middle"></th>
                        <th rowspan="2" class="text-center align-middle">ID</th>
                        <th rowspan="2" class="text-center align-middle">Nama</th>
                        <th rowspan="2" class="text-center align-middle">NIK</th>
                        <th rowspan="2" class="text-center align-middle">Alamat</th>
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
                <tbody>
                    @foreach($rtlhs as $key => $rtlh)
                        <tr data-entry-id="{{ $rtlh->id }}">
                            <td>

                            </td>
                            <td>
                                {{ $rtlh->id ?? '' }}
                            </td>
                            <td>
                                {{ $rtlh->name ?? '' }}
                            </td>
                            <td>
                                {{ $rtlh->nik ?? '' }}
                            </td>
                            <td>
                                {{ $rtlh->address?? '' }}
                            </td>
                            <td>
                                {{ $rtlh->districts->name ?? '' }}
                            </td>
                            <td>
                                {{ $rtlh->people ?? '' }}
                            </td>
                            <td>
                                {{ $rtlh->area ?? '' }}
                            </td>

                            <td>
                                {{ $rtlh->pondasi ?? '' }}
                            </td>
                           
                            <td>
                                {{ $rtlh->kolom_blk ?? '' }}
                            </td>

                            <td>
                                {{ $rtlh->rngk_atap ?? '' }}
                            </td>
                            <td>
                                {{ $rtlh->atap ?? '' }}
                            </td>

                            <td>
                                {{ $rtlh->dinding ?? '' }}
                            </td>
                           
                            <td>
                                {{ $rtlh->lantai ?? '' }}
                            </td>

                            <td>
                                {{ $rtlh->air ?? '' }}
                            </td>

                            <td>
                                {{ $rtlh->jarak_tinja ?? '' }}
                            </td>
                           
                            <td>
                                {{ $rtlh->wc ?? '' }}
                            </td>

                            <td>
                                {{ $rtlh->jenis_wc ?? '' }}
                            </td>
                           
                            <td>
                                {{ $rtlh->tpa_tinja ?? '' }}
                            </td>

                            <td>
                                {{ $rtlh->status_safety ?? '' }}
                            </td>

                            <td>
                                {{ $rtlh->status ?? '' }}
                            </td>

                            <td>
                                @if($rtlh->is_renov)
                                    <span class="badge bg-success">Sudah Diperbaiki</span>
                                    @php
                                        $house = \App\Models\House::where('rtlh_id', $rtlh->id)->first();
                                    @endphp
                                    @if($house)
                                        <a class="btn btn-xs btn-primary" href="{{ route('app.houses.show', $house->id) }}">Lihat Data</a>
                                    @endif
                                @else
                                    <span class="badge bg-danger">Belum Diperbaiki</span>
                                @endif
                            </td>


                            <td>
                                @can('rtlh_show')
                                    <a class="btn btn-xs btn-primary" href="{{ route('app.rtlh.show', $rtlh->id) }}">
                                        Lihat
                                    </a>
                                @endcan

                                @can('rtlh_edit')
                                    <a class="btn btn-xs btn-info" href="{{ route('app.rtlh.edit', $rtlh->id) }}">
                                        Edit
                                    </a>
                                @endcan

                                @can('rtlh_delete')
                                    <form action="{{ route('app.rtlh.destroy', $rtlh->id) }}" method="POST" onsubmit="return confirm('Apakah Anda Yakin ?');" style="display: inline-block;">
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
@can('rtlh_delete')
  let deleteButtonTrans = 'Hapus Data'
  let deleteButton = {
    text: deleteButtonTrans,
    className: 'btn-danger',
    action: function (e, dt, node, config) {
      var ids = $.map(dt.rows({ selected: true }).nodes(), function (entry) {
          return $(entry).data('entry-id')
      });

      if (ids.length === 0) {
        alert('Tidak Ada Data yang Dipilih ')

        return
      }

      if (confirm('Apakah kamu Yakin ?')) {
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
