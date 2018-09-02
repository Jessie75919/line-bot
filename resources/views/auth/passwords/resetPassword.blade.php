<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script>window.Laravel = {csrfToken : "{{ csrf_token() }}"};  </script>
    <title>Reset Your Password</title>
    <script src="{{ mix('/js/app.js') }}"></script>
</head>
<body>
<form action="/reset_pwd" method="post">
    @csrf
    <div>
        <label for="reset">Input Your New Password</label>
        <input type="text" name="password">
    </div>
    <div>
        <label for="reset">Confirm Your New Password</label>
        <input type="text" name="confirm_password">
        <input type="hidden" value="{{ $userId }}" name="userId">
    </div>
    <button id="submit" type="submit">Reset Password</button>
</form>

</body>

<script>


    @if ($errors->any())
        swal({
            title   : "{{ $errors->first('fail') }}",
            icon    : "error",
            buttons : {
                cancel : '好喔',
            }
        });
    @endif




    var validateInput = function(){
        var pw        = $('input[name=password]').val();
        var pwConfirm = $('input[name=confirm_password]').val();
        if(pw === '' || pwConfirm === '') {
            swal({
                title  : "輸入的密碼不能為空白喔！",
                icon   : "warning",
                button : "OK",
            });
            return false;
        }

        if(pw !== pwConfirm) {
            swal({
                title  : "輸入的密碼不相符，麻煩再確認一下喔！",
                icon   : "warning",
                button : "OK",
            });
            return false;
        }
        return true
    };

    $('form').on('submit', function(e){
        return validateInput();
    });

</script>
</html>
