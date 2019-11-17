@extends('line.base')
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
            goal_fat: null
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
            return this.setting.goal_weight && this.setting.goal_fat && this.setting.height;
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
