<template>
	<transition name="fade">
		<div class="mt-4" v-if="chartData">
			<ve-line :data="chartData"
			         :extend="extend"
			         :settings="chartSettings"></ve-line>
		</div>
	</transition>
</template>

<script>
  export default {
    name: 'WeightGraph',
    props: {
      lineLiffApi: {
        required: true,
        default: (() => {})
      }
    },
    data() {
      return {
        chartSettings: {
          labelMap: {
            'weight': '體重',
            'fat': '體脂肪'
          },
        },
        extend: {
          series: {
            label: {
              normal: {
                show: true
              }
            }
          }
        },
        chartData: {
          columns: ['date', 'weight', 'fat', 'bmi'],
          rows: []
        },
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
      this.lineLiffApi.getWeeklyWeightsRecords()
        .then(res => {
            const records = res.data.data;
            this.$set(this['chartData'], 'rows', records);
            this.stopLoading();
          }
        );
    }
  };
</script>

<style scoped>
</style>