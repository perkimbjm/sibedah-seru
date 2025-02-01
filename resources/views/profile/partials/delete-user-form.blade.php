<div class="container mt-2">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Hapus Akun Anda</div>

                <div class="card-body">
                    <div class="mb-0 form-group row">
                        <div class="col-md-6 offset-md-4">
                            <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#deleteAccountModal">
                                Hapus Akun
                            </button>
                        </div>
                    </div>

                    <!-- Modal -->
                    <div class="modal fade" id="deleteAccountModal" tabindex="-1" role="dialog" aria-labelledby="deleteAccountModalLabel" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="deleteAccountModalLabel">Hapus Akun Anda</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <p>Ketika akun Anda dihapus, semua sumber daya dan data akan dihapus secara permanen. Sebelum menghapus akun Anda, silakan unduh data atau informasi apa pun yang Anda ingin simpan.</p>
                                    <form method="POST" action="{{ route('profile.destroy') }}" id="deleteAccountForm">
                                        @csrf
                                        @method('DELETE')

                                        <div class="form-group">
                                            <label for="password">Password</label>
                                            <input id="password-account" type="password" name="password" required class="form-control" placeholder="Masukkan password Anda">
                                            @error('password')
                                                <span class="text-sm text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </form>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                                    <button type="button" class="btn btn-danger" onclick="document.getElementById('deleteAccountForm').submit();">Hapus Akun</button>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
