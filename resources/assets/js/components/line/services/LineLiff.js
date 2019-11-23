export class LineLiff {
  constructor(liff) {
    this.liff = liff;
    this.liffToken = document.getElementById('liff-token').value;
    this._profile = null;
  }

  get profile() {
    return this._profile;
  }

  set profile(value) {
    this._profile = value;
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