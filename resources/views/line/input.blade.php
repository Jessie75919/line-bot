@extends('line.base')
@section('content')
    @verbatim
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
    @endverbatim
@endsection

@section('js')
    <script>
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
            this.sendTextMessage(`weight，${JSON.stringify(this.bodyStatus)}`);
          }
        },
        created() {
          this.liffService.init();
        }
      });
    </script>
@endsection
