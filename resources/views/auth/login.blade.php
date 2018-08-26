<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta content="width=device-width, initial-scale=1" name="viewport">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>網站管理系統</title>
    <link rel="SHORTCUT ICON" href="/images/share/favicon.ico"/>
    <link rel="icon" href="/images/share/favicon.ico" type="image/ico"/>
    <link rel="stylesheet" href="{{ mix('/css/all.css') }}">
    <script>window.Laravel = {csrfToken : "{{ csrf_token() }}"};  </script>
    <script src="{{ mix('/js/app.js') }}"></script>
    <script src="{{ mix('/js/all.js') }}"></script>
</head>

<body class="loginBody">
<div class="login">
    <div class="logo"><img src="images/share/logo.svg"></div>
    <h1>CHU.C 啾囍 網站管理系統 登入</h1>
    <div class="loginBox">
        <form method="post" action="{{ route('login') }}">
            @csrf

            <div class="loginList">
                <i class="fas fa-user"></i>
                <input type="text" placeholder="帳號" name="email" id="account" required autofocus>
            </div>
            <div class="loginList">
                <i class="fas fa-lock"></i>
                <input type="password" placeholder="密碼" name="password" id="password" required>
            </div>
            <div class="loginBtn">
                <input type="submit" name="submit" id="submit" value="登入">
                <a href="forget.htm">忘記密碼</a>
            </div>
        </form>
    </div>
</div>
<footer>©CHU.C 啾囍 All Rights Reserved.</footer>
