<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css"
          integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <a class="navbar-brand" href="#">Navbar</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
            aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav mr-auto">
            <li class="nav-item active">
                <a class="nav-link" href="#">Home <span class="sr-only">(current)</span></a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#">Link</a>
            </li>
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true"
                   aria-expanded="false">
                    Dropdown
                </a>
                <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                    <a class="dropdown-item" href="#">Action</a>
                    <a class="dropdown-item" href="#">Another action</a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item" href="#">Something else here</a>
                </div>
            </li>
            <li class="nav-item">
                <a class="nav-link disabled" href="#" tabindex="-1" aria-disabled="true">Disabled</a>
            </li>
        </ul>
        <form class="form-inline my-2 my-lg-0">
            <input class="form-control mr-sm-2" type="search" placeholder="Search" aria-label="Search">
            <button class="btn btn-outline-success my-2 my-sm-0" type="submit">Search</button>
        </form>
    </div>
</nav>

<div class="btn-group" role="group" aria-label="Basic example">
    <button type="button" id="getProfileButton" class="btn btn-secondary">Click Me</button>
    <button type="button" id="getAccessToken" class="btn btn-secondary">Get Token!</button>
    <button type="button" id="sendMessageButton" class="btn btn-secondary">Send Message</button>
</div>

<p id="userIdProfileField"></p>
<p id="displayNameField"></p>
<p id="statusMessageField"></p>
<p id="accessTokenField"></p>

<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo"
        crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"
        integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"
        integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/vconsole@3.2.0/dist/vconsole.min.js"></script>
<script src="https://static.line-scdn.net/liff/edge/2.1/sdk.js"></script>
<script>
  var vConsole = new VConsole();

  function initializeLiff(myLiffId) {
    liff
      .init({ liffId: myLiffId })
      .then(() => {
        initializeApp();// start to use LIFF's api
      })
      .catch((err) => {
        window.alert('Error getting profile: ' + err);
      });
  }

  initializeLiff('1570164480-WZ6l3QKK');

  function initializeApp() {
    document.getElementById('getProfileButton')
      .addEventListener('click', function () {
        liff.getProfile().then(function (profile) {
          document.getElementById('userIdProfileField').textContent = profile.userId;
          document.getElementById('displayNameField').textContent = profile.displayName;
          document.getElementById('statusMessageField').textContent = profile.statusMessage;

        }).catch(function (error) {
          window.alert('Error getting profile: ' + error);
        });
      });

    document.getElementById('getAccessToken').addEventListener('click', function () {
      if (!liff.isLoggedIn() && !liff.isInClient()) {
        alert('To get an access token, you need to be logged in. Please tap the "login" button below and try again.');
      } else {
        const accessToken = liff.getAccessToken();
        document.getElementById('accessTokenField').textContent = accessToken;
        toggleAccessToken();
      }
    });

    // sendMessages call
    document.getElementById('sendMessageButton').addEventListener('click', function () {
      if (!liff.isInClient()) {
        sendAlertIfNotInClient();
      } else {
        liff.sendMessages([
          {
            'type': 'text',
            'text': 'You\'ve successfully sent a message! Hooray!'
          }
        ]).then(function () {
          window.alert('Message sent');
        }).catch(function (error) {
          window.alert('Error sending message: ' + error);
        });
      }
    });
  }

</script>
</body>
</html>