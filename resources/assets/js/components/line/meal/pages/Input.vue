<template>
	<div class="container mt-3">
		<div class="card">
			<div class="card-body">
				<form>
					<div class="form-group-lg">
						<label>請輸入記錄內容</label>
						<input class="form-control"
						       type="text"
						       v-model="meal">
					</div>
					<div class="form-group-lg text-center">
						<button :disabled="! meal"
						        @click.prevent="submit"
						        class="btn btn-primary btn-lg mt-4 w-100"
						        type="button">
							記錄
						</button>
					</div>
				</form>
			</div>
		</div>
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
        meal: null
      };
    },
    methods: {
      submit() {
        this.meal.replace('/，/g', ',');
        this.liffService.sendTextMessage(`meal，text-save，${JSON.stringify(this.meal)}`);
        this.liffService.close();
      },
    },
  };
</script>

<style scoped>

</style>