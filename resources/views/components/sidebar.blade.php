<div class="sidebar-content">
    <!-- Tambahkan tombol close untuk mobile -->
    <button type="button" id="closeSidebarMobile" class="btn btn-sm btn-light d-md-none">
        <i class="fas fa-times"></i>
    </button>

    <div class="card">
        <div class="text-white card-header bg-primary">
            <h5 class="mb-0 card-title">Bedah Rumah</h5>
        </div>
        <div class="card-body">
            <form id="houseData" action="{{ route("app.bedah.store") }}" enctype="multipart/form-data" method="POST">
                @csrf

                <div class="form-group">
                    <label class="required control-label col-sm-4" for="nik">NIK</label>

                    <input class="form-control form-control-sm {{ $errors->has('nik') ? 'is-invalid' : '' }}" type="number" name="nik" id="nik" value="{{ old('nik', '') }}" required pattern="[0-9]{16}" placeholder="Isi Nomor Identitas yang Valid (16 digit)">
                    <small id="nikAlert" class="text-success" style="display:none;">NIK ada di RTLH</small>

                    @if($errors->has('nik'))
                        <span class="text-danger">{{ $errors->first('nik') }}</span>
                    @endif
                </div>

                <button type="button" class="mb-2 btn btn-sm btn-info" id="fillDataButton" style="display:none;">Isi Data</button>

                <div class="form-group">
                    <label class="required control-label col-sm-4" for="name">Nama</label>
                    <input class="form-control form-control-sm {{ $errors->has('name') ? 'is-invalid' : '' }}" type="text" name="name" id="name" value="{{ old('name', '') }}" required placeholder="Masukkan nama Penerima Bantuan">
                    @if($errors->has('name'))
                        <span class="text-danger">{{ $errors->first('name') }}</span>
                    @endif
                </div>

                <div class="form-group">
                    <label class="required control-label col-sm-4" for="address">Alamat</label>
                    <input class="form-control form-control-sm {{ $errors->has('address') ? 'is-invalid' : '' }}" type="text" name="address" id="address" value="{{ old('address', '') }}" required placeholder="Masukkan Alamat (dengan nama desa)">
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
                    <label class="required control-label col-sm-4" for="year">Tahun</label>
                    <input class="form-control form-control-sm {{ $errors->has('year') ? 'is-invalid' : '' }}" type="number" name="year" id="year" value="{{ old('year', date('Y')) }}" required min="2020" max="2029" oninput="this.value = this.value.slice(0, 4);">
                    @if($errors->has('year'))
                        <span class="text-danger">{{ $errors->first('year') }}</span>
                    @endif
                </div>

                <div class="form-group">
                    <label class="required control-label col-sm-4" for="type">Jenis Bantuan</label>
                    <input class="form-control form-control-sm {{ $errors->has('type') ? 'is-invalid' : '' }}" type="text" name="type" id="type" value="{{ old('type', '') }}" required placeholder="Jenis Bantuan / Kegiatan">
                    @if($errors->has('type'))
                        <span class="text-danger">{{ $errors->first('type') }}</span>
                    @endif
                </div>

                <div class="form-group">
                    <label class="control-label col-sm-4" for="source">Sumber Dana</label>
                    <input class="form-control form-control-sm {{ $errors->has('source') ? 'is-invalid' : '' }}" type="text" name="source" id="source" value="{{ old('source', '') }}" required placeholder="Contoh : APBD / APBN / APBD Prov / CSR/ Lainnya">
                    @if($errors->has('source'))
                        <span class="text-danger">{{ $errors->first('source') }}</span>
                    @endif
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
                    <label class="control-label col-sm-4" for="note">Catatan</label>
                    <textarea class="form-control form-control-sm {{ $errors->has('note') ? 'is-invalid' : '' }}" name="note" id="note" placeholder="Catatan atau keterangan tambahan, jika ada">{{ old('note', '') }}</textarea>
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
