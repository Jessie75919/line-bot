<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="{{ mix('/css/vendor.css') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="api-url" content="{{ env('API_URL') }}">
    <title>Document</title>
</head>
<body>

<div id="foody"></div>
<div id="foody"></div>
<script src="{{ mix('/js/app.js') }}"></script>
<script src="{{ mix('/js/foody.js') }}"></script>

</body>
</html>