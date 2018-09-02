<!DOCTYPE html>
<html lang="zh-Hant">

<head>
    <meta charset="UTF-8">
    <meta content="width=device-width, initial-scale=1" name="viewport">
    <title>網站管理系統</title>
    <link rel="SHORTCUT ICON" href="/images/share/favicon.ico"/>
    <link rel="icon" href="/images/share/favicon.ico" type="image/ico"/>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script>window.Laravel = {csrfToken : "{{ csrf_token() }}"};  </script>
    <link rel="stylesheet" href="{{ mix('/css/all.css') }}">
    <script src="{{ mix('/js/app.js') }}"></script>
</head>

<body class="loginBody">
<div class="login">
    <div class="logo"><img src="/images/share/logo.svg"></div>
    <h1>CHU.C 啾囍 網站管理系統 忘記密碼</h1>
    <div class="loginBox">
        <form action="/send_reset_pwd_mail" method="post">
            @csrf
            <div class="loginList">
                <i class="fas fa-envelope"></i>
                <input type="email" placeholder="E-Mail" name="email" id="email">
            </div>
            <div class="loginBtn">
                <input type="submit" name="submit" id="submit" value="送出">
                <a href="{{ url('login') }}">返回登入頁</a>
            </div>
        </form>
    </div>
</div>
<footer>©CHU.C 啾囍 All Rights Reserved.</footer>

<!--[if lt IE 8]>
<script type="text/javascript" src="js/html5.js"></script><![endif]-->
<script>
    @if ($errors->has('fail') || $errors->has('email'))
        swal({
            title   : "{{ $errors->first('fail')}}{{ $errors->first('email') }}",
            icon    : "error",
            buttons : {
                cancel  : '好喔',
            }
        });
    @endif

    @if (session('success'))
        swal({
            title   : "系統信發送提醒",
            text    : "{{ session('success') }}",
            icon    : "success",
            buttons : {
                cancel  : '好喔',
            }
        });
    @endif



</script>




</body>

</html>
