<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    @section('meta')
        <meta charset="UTF-8">
        <meta content="width=device-width, initial-scale=1" name="viewport">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <title>網站管理系統</title>
        <link rel="SHORTCUT ICON" href="/images/share/favicon.ico"/>
        <link rel="icon" href="/images/share/favicon.ico" type="image/ico"/>
@show

<!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script>window.Laravel = {csrfToken : "{{ csrf_token() }}"};  </script>
    <script src="{{ mix('/js/app.js') }}"></script>
    <script src="{{ mix('/js/all.js') }}"></script>
    <link rel="stylesheet" href="{{ mix('/css/all.css') }}">
</head>
<body>

@include('consoles.components.header')

<main>

    @include('consoles.components.sidebar')

    @yield('product')

</main>


<!--狀態按鈕-->
<script>
    var elems = Array.prototype.slice.call(document.querySelectorAll('.js-switch'));

    elems.forEach(function(html){
        var switchery = new Switchery(html);

    });

</script>
</body>
</html>