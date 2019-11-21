@extends('line.base')

@section('css')
    <style>
        .fade-enter-active, .fade-leave-active {
            transition: opacity .3s;
        }

        .fade-enter, .fade-leave-to {
            opacity: 0;
        }
    </style>
@endsection

@section('content')
    <input type="hidden" id="app_url" value="{{env('APP_URL')}}">
    @verbatim
        <div class="container mt-3">
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
                                <option value="0">星期一</option>
                                <option value="1">星期二</option>
                                <option value="2">星期三</option>
                                <option value="3">星期四</option>
                                <option value="4">星期五</option>
                                <option value="5">星期六</option>
                                <option value="6">星期日</option>
                            </select>
                        </div>
                        <div class="form-group-lg mt-3">
                            <label for="notify_at">提醒時間</label>
                            <input type="time" class="form-control"
                                   v-model="setting.notify_at"
                                   min="09:00"
                                   max="23:00"
                                   step="600"
                            >
                        </div>
                    </div>
                </transition>
                <div class="form-group-lg text-center">
                    <button type="button"
                            :disabled="! isReady"
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
          appUrl: document.getElementById('app_url').value,
          setting: {
            height: null,
            goal_weight: null,
            goal_fat: null,
            enable_notification: false,
            notify_day: null,
            notify_at: null,
          }
        },
        methods: {
          close() {
            this.liffService.close();
          },
          sendTextMessage(text) {
            this.liffService.sendTextMessage(text);
            this.liffService.close();
          },
          submit() {
            this.sendTextMessage(`weight-goal，${JSON.stringify(this.setting)}`);
          },
        },
        computed: {
          isReady() {
            return this.setting.goal_weight &&
              this.setting.goal_fat &&
              this.setting.height;
          }
        },
        async created() {
          await this.liffService.init();
          const profile = this.liffService.profile;

//          const url = `${this.appUrl}/line/liff/weight/my-setting/${profile.userId}`;
          axios.get(`my-setting/${profile.userId}`)
            .then(res => {
                if (res.status === 200) {
                  if (res.data.setting) {
                    this.$set(this, 'setting', res.data.setting);
                  }
                }
              }
            );
        }
      });
    </script>
@endsection
