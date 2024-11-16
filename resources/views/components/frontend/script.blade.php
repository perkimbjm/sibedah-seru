<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/js/bootstrap.bundle.min.js" integrity="sha384-b5kHyXgcpbZJO/tY9Ul7kGkf1S0CWuKcCD38l8YkeH8z8QjE0GmW1gYU5S9FOnJ0" crossorigin="anonymous"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
        <script>
                $('#terms').click(function(){
                //If the checkbox is checked.
                if($(this).is(':checked')){
                    //Enable the close button.
                    $('#closeBtn').removeClass('disabled');
                    $('#closeBtn').attr("data-toggle", "modal");
                } else{
                    //If it is not checked, disable the button.
                    $('#closeBtn').addClass('disabled');
                    $('#closeBtn').removeAttr('data-toggle');
                }
              });
        </script>