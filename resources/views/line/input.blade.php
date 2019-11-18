@extends('line.base')
@section('content')
    <input type="hidden" id="app_url" value="{{env('APP_URL')}}">
    @verbatim
        <div class="container mt-3">
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
                            :disabled="!bodyStatus.weight || !bodyStatus.fat"
                            @click.prevent="submit"
                            class="btn btn-primary btn-lg mt-4 w-100">
                        記錄
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
          setting: {
            height: null,
            goal_weight: null,
            goal_fat: null
          },
          bodyStatus: {
            weight: null,
            fat: null,
            bmi: null,
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
            this.sendTextMessage(`weight，${JSON.stringify(this.bodyStatus)}`);
          }
        },
        computed: {
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
          await this.liffService.init();
          const profile = this.liffService.profile;
          axios.get(`${this.appUrl}/line/liff/weight/my-setting/${profile.userId}`)
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
