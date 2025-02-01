<div class="sidebar-content">
    <!-- Tambahkan tombol close untuk mobile -->
    <button type="button" id="closeSidebarMobile" class="btn btn-sm btn-light d-md-none">
        <i class="fas fa-times"></i>
    </button>

    <div class="card">
        <div class="text-white card-header bg-primary">
            <h5 class="mb-0 card-title">Rumah Tidak Layak Huni</h5>
        </div>
        <div class="card-body">
            <form id="rtlhData" action="{{ route("app.rumah.store") }}" enctype="multipart/form-data" method="POST">
                @csrf

                <div class="form-group">
                    <label class="required" for="nik">NIK</label>
                    <input class="form-control {{ $errors->has('nik') ? 'is-invalid' : '' }}" type="number" name="nik" id="nik" value="{{ old('nik', '') }}" required pattern="[0-9]{16}" placeholder="Isi Nomor Identitas yang Valid (16 digit)">
                    @if($errors->has('nik'))
                        <span class="text-danger">{{ $errors->first('nik') }}</span>
                    @endif
                </div>

                <div class="form-group">
                    <label class="required" for="kk">KK</label>
                    <input class="form-control {{ $errors->has('kk') ? 'is-invalid' : '' }}" type="number" name="kk" id="kk" value="{{ old('kk', '') }}" required pattern="[0-9]" placeholder="Isi Nomor Kartu Keluarga yang Valid">
                    @if($errors->has('kk'))
                        <span class="text-danger">{{ $errors->first('kk') }}</span>
                    @endif
                </div>

                <div class="form-group">
                    <label class="required" for="name">Nama</label>
                    <input class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}" type="text" name="name" id="name" value="{{ old('name', '') }}" required placeholder="Masukkan Nama">
                    @if($errors->has('name'))
                        <span class="text-danger">{{ $errors->first('name') }}</span>
                    @endif
                </div>

                <div class="form-group">
                    <label class="required" for="address">Alamat</label>
                    <input class="form-control {{ $errors->has('address') ? 'is-invalid' : '' }}" type="text" name="address" id="address" value="{{ old('address', '') }}" required placeholder="Masukkan Alamat (dengan nama desa)">
                    @if($errors->has('address'))
                        <span class="text-danger">{{ $errors->first('address') }}</span>
                    @endif
                </div>

                <div class="form-group">
                    <label class="required control-label col-sm-4" for="village_id">Kel / Desa</label>
                    <select class="form-control form-control-sm select2 {{ $errors->has('village_id') ? 'is-invalid' : '' }}" name="village_id" id="village_id" required>
                        <option value="">Pilih Desa</option>
                    </select>
                    @if($errors->has('village_id'))
                        <span class="text-danger">{{ $errors->first('village_id') }}</span>
                    @endif
                </div>

                <div class="form-group">
                    <label class="required control-label col-sm-4" for="district_id">Kecamatan</label>
                    <input type="text" class="form-control form-control-sm" id="district_name" readonly>
                    <input type="hidden" name="district_id" id="district_id" required>
                </div>

                <div class="form-group">
                    <button type="button" class="mt-2 btn btn-info btn-sm w-100" id="togglePicker">
                        <i class="fas fa-map-marker-alt"></i> Pilih Lokasi di Peta
                    </button>
                </div>

                <div class="form-group">
                    <div class="row">
                        <div class="col-sm-6">
                            <label class="control-label" for="lat">Latitude</label>
                            <input class="form-control form-control-sm {{ $errors->has('lat') ? 'is-invalid' : '' }}" type="text" name="lat" id="lat" value="{{ old('lat', '') }}" required placeholder="Contoh : -2.xxx">
                            @if($errors->has('lat'))
                                <span class="text-danger">{{ $errors->first('lat') }}</span>
                            @endif
                        </div>
                        <div class="col-sm-6">
                            <label class="control-label" for="lng">Longitude</label>
                            <input class="form-control form-control-sm {{ $errors->has('lng') ? 'is-invalid' : '' }}" type="text" name="lng" id="lng" value="{{ old('lng', '') }}" required placeholder="Contoh : 115.xxx">
                            @if($errors->has('lng'))
                                <span class="text-danger">{{ $errors->first('lng') }}</span>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label class="required" for="people">Jumlah Penghuni</label>
                    <input class="form-control {{ $errors->has('people') ? 'is-invalid' : '' }}" type="number" name="people" id="people" value="{{ old('people', '') }}" required>
                    @if($errors->has('people'))
                        <span class="text-danger">{{ $errors->first('people') }}</span>
                    @endif
                </div>

                <div class="form-group">
                    <label class="required" for="area">Luas Bangunan Rumah (p x l x jumlah lantai)</label>
                    <input class="form-control {{ $errors->has('area') ? 'is-invalid' : '' }}" type="number" name="area" id="area" value="{{ old('area', '') }}" required>
                    @if($errors->has('area'))
                        <span class="text-danger">{{ $errors->first('area') }}</span>
                    @endif
                </div>

                <x-condition-select name="pondasi" label="Pondasi" :options="$kelayakanOptions" :errors="$errors" />
                <x-condition-select name="kolom_blk" label="Kolom / Balok" :options="$kelayakanOptions" :errors="$errors" />
                <x-condition-select name="rngk_atap" label="Rangka Atap" :options="$kelayakanOptions" :errors="$errors" />
                <x-condition-select name="atap" label="Atap" :options="$kelayakanOptions" :errors="$errors" />
                <x-condition-select name="dinding" label="Dinding" :options="$kelayakanOptions" :errors="$errors" />
                <x-condition-select name="lantai" label="Lantai" :options="$kelayakanOptions" :errors="$errors" />
                <x-condition-select name="air" label="Sumber Air Minum" :options="$airOptions" :errors="$errors" />
                <x-condition-select name="jarak_tinja" label="Jarak Sumber Air Minum ke TPA Tinja" :options="$jarakTinjaOptions" :errors="$errors" />
                <x-condition-select name="wc" label="Kepemilikan WC" :options="$wcOptions" :errors="$errors" />
                <x-condition-select name="jenis_wc" label="Jenis Kloset / WC" :options="$jenisWcOptions" :errors="$errors" />
                <x-condition-select name="tpa_tinja" label="TPA Tinja" :options="$tpaTinjaOptions" :errors="$errors" />
                <x-condition-select name="status_safety" label="Penilaian Kelayakan Bangunan" :options="$kelayakanOptions2" :errors="$errors" :uppercase="true" />
                <x-condition-select name="status" label="Penilaian Kelayakan Bangunan dan Sanitasi" :options="$kelayakanOptions2" :errors="$errors" :uppercase="true" />

                <div class="form-group">
                    <label for="note">Catatan</label>
                    <textarea class="form-control {{ $errors->has('note') ? 'is-invalid' : '' }}" name="note" id="note" placeholder="Catatan atau keterangan tambahan, jika ada">{{ old('note', '') }}</textarea>
                    @if($errors->has('note'))
                        <span class="text-danger">{{ $errors->first('note') }}</span>
                    @endif
                </div>


                <div class="btn-group w-100">
                    <button type="button" class="m-2 btn btn-secondary btn-sm" id="cancelBtn">
                        <i class="fas fa-times"></i> Cancel
                    </button>
                    <button type="submit" class="m-2 btn btn-primary btn-sm">
                        <i class="fas fa-save"></i> Save
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
