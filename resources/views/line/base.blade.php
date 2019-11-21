<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <style>
        body {
            height: 800px;
        }
    </style>
    @yield('css')
</head>
<body>
<input type="hidden" id="liff-token" value="{{$liffToken}}">
<input type="hidden" id="today" value="{{$today}}">


<div id="app">
    @yield('content')
</div>
<script src="https://cdn.jsdelivr.net/npm/vue"></script>
{{--<script src="https://cdn.jsdelivr.net/npm/vue@2.6.0"></script>--}}
<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
<script src="https://unpkg.com/axios/dist/axios.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/vconsole@3.2.0/dist/vconsole.min.js"></script>
<script src="https://static.line-scdn.net/liff/edge/2.1/sdk.js"></script>
<script>
  var vConsole = new VConsole();
  Vue.config.devtools = true;
  Vue.config.silent = false;

  class LiffService {
    get profile() {
      return this._profile;
    }

    set profile(value) {
      this._profile = value;
    }

    constructor(liff) {
      this.liff = liff;
      this.liffToken = document.getElementById('liff-token').value;
      this._profile = null;
    }

    init() {
      const self = this;
      return this.liff
        .init({ liffId: this.liffToken })
        .then(() => {
          if (!self.isLoggedIn()) {
            self.login();
          }
        })
        .then(() => this.getProfile())
        .catch(err => window.alert('Error getting profile: ' + err));
    }

    login() {
      return this.liff.login();
    }

    logout() {
      return this.liff.logout();
    }

    isInClient() {
      return this.liff.isInClient;
    }

    isLoggedIn() {
      return this.liff.isLoggedIn();
    }

    close() {
      this.liff.closeWindow();
    }

    getProfile() {
      return this.liff.getProfile()
        .then(profile => this._profile = profile)
        .catch(error => window.alert('Error getting profile: ' + error));
    }

    sendTextMessage(text, callback = null) {
      if (!this.isInClient()) {
        window.alert('You cannot use liff.sendMessages() in an external browser.');
        return;
      }
      return this.liff.sendMessages([{ 'type': 'text', text }])
        .then(function () {
          if (callback) {
            callback();
          }
          return true;
        }).catch(function (error) {
          window.alert('Error sending message: ' + error);
        });
    }
  }

</script>
@yield('js')
</body>
</html>