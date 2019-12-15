<template>
	<div class="container mt-3">
		<div class="card">
			<div class="card-header text-white bg-dark d-flex justify-content-between">
				飲食提醒(每日)
				<button @click="addReminder" class="btn btn-primary">新增</button>
			</div>
			<div class="card-body" v-if="mealTypes">
				<div class="form-group-lg mt-4" v-for="(time, index) in notify_times">
					<div class="time-picker-container">
						<div style="margin-right: 5px">
							<button @click="removeTime(index)"
							        class="btn btn-outline-danger btn-sm"
							        style="font-weight: bold;">x
							</button>
						</div>
						<select @change="changeMealType(time)"
						        class="form-control time-picker"
						        v-model="time.meal_type_id">
							<option :value="type.id" v-for="type in mealTypes">{{type.name}}</option>
						</select>
						<select class="form-control time-picker"
						        v-model="time.hour">
							<option :value="hour" v-for="hour in hours">{{hour}} 點</option>
						</select>
						<select class="form-control time-picker" v-model="time.minute">
							<option value="00">00 分</option>
							<option value="15">15 分</option>
							<option value="30">30 分</option>
							<option value="45">45 分</option>
						</select>
					</div>
				</div>
			</div>
		</div>

		<div class="form-group-lg text-center mb-3">
			<button @click.prevent="submit"
			        class="btn btn-primary btn-lg mt-4 w-100"
			        type="button">
				設定
			</button>
		</div>
	</div>
</template>

<script>

  export default {
    name: 'MealSetting',
    props: {
      setting: {
        default: (() => {})
      },
      liffService: {
        required: true,
        default: (() => {})
      },
      lineLiffApi: {
        required: true,
        default: (() => {})
      }
    },
    filters: {
      toName(typeId) {
        if (!this.mealTypes) {
          return;
        }
        return this.mealTypes.find(type => type.id === typeId).name;
      }
    },
    data() {
      return {
        mealTypes: null,
        notify_times: [],
      };
    },
    methods: {
      addReminder() {
        if (this.notify_times.length === 5) {
          alert('最多設定五個提醒時間喔！');
          return;
        }

        const currentMealTypeIds = this.notify_times.map(time => time.meal_type_id);
        const [nextMealType,] = this.mealTypes.filter(meal => !currentMealTypeIds.includes(meal.id));

        this.notify_times.push({
          meal_type_id: nextMealType.id,
          hour: nextMealType.time.hour,
          minute: nextMealType.time.minute
        });
      },
      changeMealType(time) {
        const mealTypeId = time.meal_type_id;
        const mealType = this.mealTypes.find(type => type.id === mealTypeId);
        this.$set(time, 'hour', mealType.time.hour);
        this.$set(time, 'minute', mealType.time.minute);
      },
      removeTime(timeIdx) {
        this.notify_times.splice(timeIdx, 1);
      },
      submit() {
        if (this.notify_times.length > 0) {
          this.notify_times.forEach(time => {
            time['time'] = `${time['hour']}:${time['minute']}`;
            delete time['hour'];
            delete time['minute'];
          });
        } else {
          if (!confirm('請問確定要關閉所有提醒嗎？')) {
            return;
          }
        }

        this.liffService.sendTextMessage(`meal，setting，${JSON.stringify(this.notify_times)}`);
        this.liffService.close();
      },
      stopLoading() {
        this.$emit('stopLoading');
      },
    },
    computed: {
      hours() {
        const hours = [...Array(24).keys()];
        return hours.map(h => h.pad());
      },
    },
    watch: {
      lineLiffApi: {
        immediate: true,
        handler(newValue, oldValue) {
          if (!oldValue && newValue) {
            newValue.getMealTypes()
              .then(res => {
                  const mealTypes = res.data.data;
                  mealTypes.forEach(type => {
                    const [hour, minute] = type['time'].split(':');
                    type['time'] = { hour, minute, };
                  });
                  this.$set(this, 'mealTypes', mealTypes);
                  this.stopLoading();
                }
              );
          }
        }
      },
      setting: {
        immediate: true,
        handler(newValue, oldValue) {
          if (Array.isArray(newValue) && newValue.length > 0) {
            this.notify_times = newValue;
            this.stopLoading();
          }
        }
      },
    },
    created() {
      Number.prototype.pad = function (size) {
        var s = String(this);
        while (s.length < (size || 2)) {s = '0' + s;}
        return s;
      };
    },
  };
</script>

<style lang="scss" scoped>
	.time-picker {
		width: 50%;
	}

	.time-picker-container {
		display: flex;
		align-items: center;
	}

	.active {
		color: #fff !important;
	}

	select {
		padding: 0 !important;
	}
</style>