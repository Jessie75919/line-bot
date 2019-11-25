<template>
	<div class="container mt-3">
		<div class="card">
			<div class="card-header text-white bg-dark">
				目標設定
			</div>
			<div class="card-body">
				<form>
					<div class="form-group-lg mt-3">
						<label for="weight">目標體重（kg）</label>
						<input class="form-control" id="weight" type="number" v-model="setting.goal_weight">
					</div>
					<div class="form-group-lg mt-3">
						<label for="fat">目標體脂（%）</label>
						<input class="form-control" id="fat" type="number" v-model="setting.goal_fat">
					</div>
				</form>
			</div>
		</div>
		<div class="card mt-3">
			<div class="card-header text-white bg-dark">
				個人資料設定
			</div>
			<div class="card-body">
				<form>
					<div class="form-group-lg">
						<label for="height">身高（cm）</label>
						<input class="form-control" id="height" type="number" v-model="setting.height">
					</div>
				</form>
			</div>
		</div>
		<div class="card mt-3">
			<div class="card-header text-white bg-dark">
				提醒設定
			</div>
			<div class="card-body">
				<form class="mb-3">
					<div class="form-group-lg mt-3">
						<div class="form-check">
							<input class="form-check-input" id="enable_notification"
							       type="checkbox"
							       v-model="setting.enable_notification">
							<label class="form-check-label" for="enable_notification">
								開啓記錄提醒
							</label>
						</div>
					</div>
					<transition name="fade">
						<div v-if="setting.enable_notification">
							<!-- frequency tab =============================== -->
							<ul class="nav nav-pills nav-fill mt-4">
								<li class="nav-item">
									<a :class="{'active' : once_a_week}"
									   @click="setNotifiedDays('once')"
									   class="nav-link">
										每週一次
									</a>
								</li>
								<li class="nav-item">
									<a :class="{'active' : !once_a_week}"
									   @click="setNotifiedDays('multi')"
									   class="nav-link">
										每週多次
									</a>
								</li>
							</ul>
							<transition mode="out-in" name="fade">
								<div class="form-group-lg mt-4" v-if="once_a_week">
									<label for="notify_day">每週提醒日</label>
									<select @change="showChange" class="form-control"
									        id="notify_day"
									        v-model="notify_at_day">
										<option :value="1">星期一</option>
										<option :value="2">星期二</option>
										<option :value="3">星期三</option>
										<option :value="4">星期四</option>
										<option :value="5">星期五</option>
										<option :value="6">星期六</option>
										<option :value="0">星期日</option>
									</select>
								</div>
								<div class="form-group-lg mt-4" v-else>
									<label>每週提醒日</label>
									<div class="form-check m-3" v-for="day in days">
										<input :id="day.id" :value="day.val"
										       class="form-check-input"
										       type="checkbox"
										       v-model="setting.notify_days">
										<label :for="day.id" class="form-check-label">
											{{day.name}}
										</label>
									</div>
								</div>
							</transition>
							<div class="form-group-lg mt-4">
								<label>提醒時間</label>
								<div class="time-picker-container">
									<select class="form-control time-picker"
									        id="hour"
									        v-model="time.hour">
										<option :value="hour" v-for="hour in hours">{{hour}} 點</option>
									</select>
									<select class="form-control time-picker" id="minute" v-model="time.minute">
										<option value="00">00 分</option>
										<option value="30">30 分</option>
									</select>
								</div>
							</div>
						</div>
					</transition>

				</form>
			</div>
		</div>
		<div class="form-group-lg text-center mb-3">
			<button :disabled="! isSettingReady"
			        @click.prevent="submit"
			        class="btn btn-primary btn-lg mt-4 w-100"
			        type="button">
				設定
			</button>
		</div>
	</div>
</template>

<script>
  export default {
    name: 'WeightSetting',
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
        notify_days: [],
        once_a_week: true,
        notify_at_day: 0,
        time: {
          hour: '09',
          minute: '00'
        },
      };
    },
    methods: {
      showChange() {
        console.log(this.setting);
      },
      submit() {
        this.$set(this.setting, 'notify_at', `${this.time.hour}:${this.time.minute}`);
        if (this.once_a_week) {
          this.$set(this.setting, 'notify_days', [this.notify_at_day]);
        }
        this.liffService.sendTextMessage(`weight-goal，${JSON.stringify(this.setting)}`);
        this.liffService.close();
      },
      stopLoading() {
        this.$emit('stopLoading');
      },
      setNotifiedDays(mode) {
        if (mode === 'once') {
          this.once_a_week = true;
          this.$set(this.setting, 'notify_days', [0]);
        } else {
          this.once_a_week = false;
          this.notify_days.push(0);
        }
      }
    },
    computed: {
      isSettingReady() {
        return this.setting.goal_weight &&
          this.setting.goal_fat &&
          this.setting.height;
      },
      days() {
        return [
          { val: 0, name: '星期日', id: 'sun' },
          { val: 1, name: '星期一', id: 'mon' },
          { val: 2, name: '星期二', id: 'tue' },
          { val: 3, name: '星期三', id: 'wed' },
          { val: 4, name: '星期四', id: 'thu' },
          { val: 5, name: '星期五', id: 'fri' },
          { val: 6, name: '星期六', id: 'sat' },
        ];
      },
      hours() {
        const hours = [...Array(24).keys()];
        return hours.map(h => h.pad());
      },
    },
    created() {
      Number.prototype.pad = function (size) {
        var s = String(this);
        while (s.length < (size || 2)) {s = '0' + s;}
        return s;
      };
    },
    mounted() {
      const notifyAt = this.setting.notify_at;
      if (notifyAt) {
        const timeArr = notifyAt.split(':');
        this.$set(this['time'], 'hour', timeArr[0]);
        this.$set(this['time'], 'minute', timeArr[1]);
      }
      const notifyDays = this.setting.notify_days;
      if (notifyDays && notifyDays.length === 1) {
        this.once_a_week = true;
        this.notify_at_day = notifyDays[0];
      } else {
        this.once_a_week = false;
      }
      this.stopLoading();
    }
  };
</script>

<style lang="scss" scoped>


	.time-picker {
		width: 50%;
	}

	.time-picker-container {
		display: flex;
	}

	.active {
		color: #fff !important;
	}
</style>