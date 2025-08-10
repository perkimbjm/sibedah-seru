<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/4.6.1/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.5/js/select2.full.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.22.2/moment.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script src="{{ asset('js/app/main.js') }}"></script>
    <script>
      $(document).ready(function(){
      $(".preloader").fadeOut(1500);
      })
    </script>

  <script>
    $('.confirm_save').click(function(event) {
          var form =  $(this).closest("form");
          var name = $(this).data("name");
          event.preventDefault();
        Swal.fire({
        title: 'Apakah Yakin Ingin Menyimpan ?',
        showDenyButton: true,
        showCancelButton: true,
        cancelButtonText: 'Batal',
        confirmButtonText: 'Simpan',
        denyButtonText: 'Jangan Simpan',
      }).then((result) => {
        /* Read more about isConfirmed, isDenied below */
        if (result.isConfirmed) {
          form.submit();
          Swal.fire('Berhasil tersimpan !', '', 'success')
        } else if (result.isDenied) {
          window.history.back();
          Swal.fire('Gagal menyimpan', '', 'info')

        }
      });
    });

  </script>


  <script src="{{ asset('js/app/adminlte320.js') }}"></script>


