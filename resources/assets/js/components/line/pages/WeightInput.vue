<template>
	<div class="container mt-3">
		<h3>今日：{{today}}</h3>
		<form>
			<div class="form-group-lg">
				<label for="weight">體重（kg）</label>
				<input class="form-control" id="weight" type="number" v-model="bodyStatus.weight">
			</div>
			<div class="form-group-lg mt-3">
				<label for="fat">體脂（%）</label>
				<input class="form-control" id="fat" type="number" v-model="bodyStatus.fat">
			</div>
			<div class="form-group-lg mt-3">
				<label for="fat">BMI (身高：{{setting.height}}cm 計算) </label>
				<input class="form-control" id="bmi" readonly type="number" v-model="bmi">
			</div>
			<div class="form-group-lg text-center">
				<button :disabled="isInputReady"
				        @click.prevent="submit"
				        class="btn btn-primary btn-lg mt-4 w-100"
				        type="button">
					記錄
				</button>
			</div>
		</form>
	</div>
</template>

<script>
  export default {
    name: 'weight-input',
    props: {
      setting: {
        default: (() => {})
      },
      liffService: {
        required: true,
        default: (() => {})
      }
    },
    data() {
      return {
        today: document.getElementById('today').value,
        bodyStatus: {
          weight: null,
          fat: null,
          bmi: null,
        }
      };
    },
    methods: {
      submit() {
        this.liffService.sendTextMessage(`weight，${JSON.stringify(this.bodyStatus)}`);
        this.liffService.close();
      }
    },
    computed: {
      isInputReady() {
        return !this.bodyStatus.weight || !this.bodyStatus.fat;
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
    }
  };
</script>

<style scoped>

</style>