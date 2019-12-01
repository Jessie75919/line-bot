<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
</head>
<body>
<input type="hidden" id="liff-token" value="{{$liffToken}}">
<input type="hidden" id="today" value="{{$today}}">
<input type="hidden" id="app_url" value="{{env('APP_URL')}}">
<input type="hidden" id="page" value="{{$page}}">


<div id="line-liff-weight"></div>

<script src="https://cdn.jsdelivr.net/npm/vconsole@3.2.0/dist/vconsole.min.js"></script>
<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo"
        crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js"
        integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js"
        integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
<script src="https://static.line-scdn.net/liff/edge/2.1/sdk.js"></script>
<script src="{{ mix('/js/line/weight/line-liff.min.js') }}"></script>
<script>
  //  var vConsole = new VConsole();
</script>
</body>
</html>