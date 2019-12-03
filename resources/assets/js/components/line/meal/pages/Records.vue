<template>
	<table class="table table-striped">
		<thead class="thead-dark">
		<tr>
			<th scope="col">日期</th>
			<th scope="col">體重（kg）</th>
			<th scope="col">體脂（%）</th>
			<th scope="col">BMI</th>
		</tr>
		</thead>
		<tbody>
		<tr v-for="record in weightRecords">
			<th scope="row">{{record.date}}</th>
			<th>{{record.weight}}</th>
			<td>{{record.fat}}</td>
			<td>{{record.bmi}}</td>
		</tr>
		</tbody>
	</table>
</template>

<script>
  export default {
    name: 'WeightRecords',
    props: {
      lineLiffApi: {
        required: true,
        default: (() => {})
      }
    },
    data() {
      return {
        weightRecords: []
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
      this.lineLiffApi.getWeightsRecords()
        .then(res => {
            const records = res.data.data;
            this.$set(this, 'weightRecords', records);
            this.stopLoading();
          }
        );
    }
  };
</script>

<style scoped>

</style>