@extends('line.base')

@section('css')
    <style>
        .fade-enter-active, .fade-leave-active {
            transition: opacity .3s;
        }

        .fade-enter, .fade-leave-to {
            opacity: 0;
        }

        .time-picker {
            width: 50%;
        }

        .time-picker-container {
            display: flex;
        }
    </style>
@endsection
@section('content')
    <input type="hidden" id="app_url" value="{{env('APP_URL')}}">
    <input type="hidden" id="page" value="{{$page}}">
    @verbatim
        <nav class="navbar navbar-expand-sm navbar-dark bg-dark">
            <a class="navbar-brand" href="#">Give Me Lighter!</a>
            <button class="navbar-toggler" type="button"
                    data-toggle="collapse"
                    data-target="#navbarSupportedContent"
                    aria-controls="navbarSupportedContent"
                    aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav mr-auto">
                    <li class="nav-item" :class="{active: page === 'index'}">
                        <a class="nav-link" @click="page='index'">記錄<span class="sr-only">(current)</span></a>
                    </li>
                    <li class="nav-item" :class="{active: page === 'setting'}">
                        <a class="nav-link" @click="page='setting'">設定<span class="sr-only"></span></a>
                    </li>
                </ul>
            </div>
        </nav>
        <div class="container mt-3" v-if="page === 'index'">
            <h3>今日：{{today}}</h3>
            <form>
                <div class="form-group-lg">
                    <label for="weight">體重（kg）</label>
                    <input type="number" class="form-control" id="weight" v-model="bodyStatus.weight">
                </div>
                <div class="form-group-lg mt-3">
                    <label for="fat">體脂（%）</label>
                    <input type="number" class="form-control" id="fat" v-model="bodyStatus.fat">
                </div>
                <div class="form-group-lg mt-3">
                    <label for="fat">BMI (身高：{{setting.height}}cm 計算) </label>
                    <input type="number" readonly class="form-control" id="bmi" v-model="bmi">
                </div>
                <div class="form-group-lg text-center">
                    <button type="button"
                            :disabled="isInputReady"
                            @click.prevent="submit"
                            class="btn btn-primary btn-lg mt-4 w-100">
                        記錄
                    </button>
                </div>
            </form>
        </div>
        <div class="container mt-3" v-if="page === 'setting'">
            <h3>設定</h3>
            <form>
                <div class="form-group-lg">
                    <label for="height">身高（cm）</label>
                    <input type="number" class="form-control" id="height" v-model="setting.height">
                </div>
                <div class="form-group-lg mt-3">
                    <label for="weight">目標體重（kg）</label>
                    <input type="number" class="form-control" id="weight" v-model="setting.goal_weight">
                </div>
                <div class="form-group-lg mt-3">
                    <label for="fat">目標體脂（%）</label>
                    <input type="number" class="form-control" id="fat" v-model="setting.goal_fat">
                </div>
                <div class="form-group-lg mt-3">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox"
                               v-model="setting.enable_notification"
                               id="enable_notification">
                        <label class="form-check-label" for="enable_notification">
                            開啓記錄提醒
                        </label>
                    </div>
                </div>
                <transition name="fade">
                    <div v-if="setting.enable_notification">
                        <div class="form-group-lg mt-3">
                            <label for="notify_day">每週提醒日</label>
                            <select class="form-control" id="notify_day" v-model="setting.notify_day">
                                <option value="1">星期一</option>
                                <option value="2">星期二</option>
                                <option value="3">星期三</option>
                                <option value="4">星期四</option>
                                <option value="5">星期五</option>
                                <option value="6">星期六</option>
                                <option value="0">星期日</option>
                            </select>
                        </div>
                        <div class="form-group-lg mt-3">
                            <label for="notify_at">提醒時間</label>
                            <div class="time-picker-container">
                                <select class="form-control time-picker"
                                        id="notify_day"
                                        v-model="time.hour">
                                    <option :value="hour" v-for="hour in hours">{{hour}} 點</option>
                                </select>
                                <select class="form-control time-picker" id="notify_day" v-model="time.minute">
                                    <option value="00">00 分</option>
                                    <option value="30">30 分</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </transition>
                <div class="form-group-lg text-center">
                    <button type="button"
                            :disabled="! isSettingReady"
                            @click.prevent="submit"
                            class="btn btn-primary btn-lg mt-4 w-100">
                        設定
                    </button>
                </div>
            </form>
        </div>
    @endverbatim
@endsection

@section('js')
    <script>
      new Vue({
        el: '#app',
        data: {
          liffService: new LiffService(liff),
          today: document.getElementById('today').value,
          appUrl: document.getElementById('app_url').value,
          page: document.getElementById('page').value,
          time: {
            hour: '09',
            minute: '00'
          },
          setting: {
            height: null,
            goal_weight: null,
            goal_fat: null,
            enable_notification: false,
            notify_day: null,
            notify_at: null,
          },
          bodyStatus: {
            weight: null,
            fat: null,
            bmi: null,
          }
        },
        methods: {
          sendTextMessage(text) {
            this.liffService.sendTextMessage(text);
            this.liffService.close();
          },
          submit() {
            if (this.page === 'setting') {
              this.$set(this.setting, 'notify_at', `${this.time.hour}:${this.time.minute}`);
              this.sendTextMessage(`weight-goal，${JSON.stringify(this.setting)}`);
              return;
            }

            this.sendTextMessage(`weight，${JSON.stringify(this.bodyStatus)}`);
          }
        },
        computed: {
          isSettingReady() {
            return this.setting.goal_weight &&
              this.setting.goal_fat &&
              this.setting.height;
          },
          isInputReady() {
            return !this.bodyStatus.weight || !this.bodyStatus.fat;
          },
          hours() {
            const hours = [...Array(24).keys()];
            return hours.map(h => h.pad());
          },
          bmi() {
            if (!this.bodyStatus.weight || !this.setting.height) {
              return null;
            }
            this.bodyStatus.bmi = this.bodyStatus.weight / Math.pow((this.setting.height / 100), 2);
            if (isNaN(this.bodyStatus.bmi)) {
              return null;
            }
            this.bodyStatus.bmi = this.bodyStatus.bmi.toFixed(2);
            return this.bodyStatus.bmi;
          }
        },
        async created() {
          Number.prototype.pad = function (size) {
            var s = String(this);
            while (s.length < (size || 2)) {s = '0' + s;}
            return s;
          };

          await this.liffService.init();
          const profile = this.liffService.profile;
          axios.get(`${this.appUrl}/line/liff/weight/my-setting/${profile.userId}`)
            .then(res => {
                if (res.status === 200) {
                  if (res.data.setting) {
                    this.$set(this, 'setting', res.data.setting);
                    const notifyAt = res.data.setting.notify_at;
                    if (notifyAt) {
                      const timeArr = notifyAt.split(':');
                      this.$set(this['time'], 'hour', timeArr[0]);
                      this.$set(this['time'], 'minute', timeArr[1]);
                    }
                  }
                }
              }
            );
        }
      });
    </script>
@endsection
