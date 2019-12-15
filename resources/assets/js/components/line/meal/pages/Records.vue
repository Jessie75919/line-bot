<template>
	<div class="container mt-3" v-if="mealRecords">
		<div class="card" v-for="record in mealRecords">
			<div class="card-header text-white bg-dark d-flex justify-content-between">
				{{record.save_date}} / {{record.meal_type.name}}
			</div>
			<img :src="record.image_url" alt="Card image cap" class="card-img-top">
		</div>
	</div>
</template>

<script>
  export default {
    name: 'MealRecords',
    props: {
      lineLiffApi: {
        required: true,
        default: (() => {})
      }
    },
    data() {
      return {
        mealRecords: null
      };
    },
    methods: {
      stopLoading() {
        this.$emit('stopLoading');
      },
      startLoading() {
        this.$emit('startLoading');
      }
    },
    created() {
      this.startLoading();
      this.lineLiffApi.getMealRecords()
        .then(res => {
            const records = res.data.data;
            this.$set(this, 'mealRecords', records);
            this.stopLoading();
          }
        );
    }
  };
</script>

<style scoped>
	.container {
		padding-bottom: 50px !important;
	}
</style>