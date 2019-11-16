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
@verbatim
    <div id="app">
        <nav class="navbar navbar-expand-sm navbar-dark bg-dark">
            <a class="navbar-brand" href="#">Give Me Lighter!</a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent"
                    aria-controls="navbarSupportedContent"
                    aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav mr-auto">
                    <li class="nav-item active">
                        <a class="nav-link" href="#">記錄<span class="sr-only">(current)</span></a>
                    </li>
                </ul>
            </div>
        </nav>
        <div class="container mt-3">
            <h3>今日：{{today}}</h3>
            <form>
                <div class="form-group-lg">
                    <label for="weight">體重</label>
                    <input type="number" class="form-control" id="weight" v-model="bodyStatus.weight">
                </div>
                <div class="form-group-lg mt-3">
                    <label for="fat">體脂（%）</label>
                    <input type="number" class="form-control" id="fat" v-model="bodyStatus.fat">
                </div>
                <div class="form-group-lg text-center">
                    <button type="button"
                            :disabled="!bodyStatus.weight || !bodyStatus.fat"
                            @click.prevent="submit"
                            class="btn btn-primary btn-lg mt-4 w-100">
                        記錄
                    </button>
                </div>
            </form>
        </div>
    </div>
@endverbatim

<script src="https://cdn.jsdelivr.net/npm/vue"></script>
<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/vconsole@3.2.0/dist/vconsole.min.js"></script>
<script src="https://static.line-scdn.net/liff/edge/2.1/sdk.js"></script>
<script>
  var vConsole = new VConsole();

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
      this.liff
        .init({ liffId: this.liffToken })
        .then(() => {
          if (!this.isLoggedIn()) {
            self.login();
          }
          console.log('liff init successfully');
        })
        .catch(err => window.alert('Error getting profile: ' + err));
    }

    login() {
      return this.liff.login();
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
      this.liff.getProfile()
        .then(profile => this.profile = profile)
        .catch(error => window.alert('Error getting profile: ' + error));
    }

    sendTextMessage(text, callback = null) {
      if (!this.isInClient()) {
        window.alert('You cannot use liff.sendMessages() in an external browser.');
        return;
      }
      this.liff.sendMessages([{ 'type': 'text', text, }])
        .then(() => {
          if (callback) {
            callback();
          }
        })
        .catch(error => window.alert('Error sending message: ' + error));
    }
  }

  new Vue({
    el: '#app',
    data: {
      liffService: new LiffService(liff),
      today: document.getElementById('today').value,
      bodyStatus: {
        weight: null,
        fat: null
      }
    },
    methods: {
      close() {
        this.liffService.close();
      },
      login() {
        this.liffService.login();
      },
      profile() {
        this.liffService.getProfile();
      },
      sendTextMessage(text) {
        this.liffService.sendTextMessage(text);
        this.liffService.close();
      },
      submit() {
        this.sendTextMessage(`weight，{weight：${this.bodyStatus.weight}, fat：${this.bodyStatus.fat}}`);
      }
    },
    created() {
      this.liffService.init();
    }
  });

</script>
</body>
</html>