<template>
	<div id="liff">
		<header-bar :page="page"
		            @setPage="setPageHandler"
		></header-bar>
		<div class="liff-content">
			<transition mode="out-in" name="fade">
				<weight-input
						:liffService="liffService"
						:setting="setting"
						style="position:absolute;"
						v-if="page ==='index'">

				</weight-input>
				<weight-setting
						:liffService="liffService"
						:setting="setting"
						style="position:absolute;"
						v-else
				>
				</weight-setting>
				<weight-records :lineLiffApi="lineLiffApi"
				                @startLoading="setLoading(true)"
				                @stopLoading="setLoading(false)"
				                v-else-if="page === 'records'">
				</weight-records>
			</transition>
		</div>
	</div>
</template>

<script>
  import { LineLiffWeightApi } from './api/LineLiffWeightApi';
  import HeaderBar from './common/HeaderBar';
  import WeightInput from './pages/WeightInput';
  import WeightRecords from './pages/WeightRecords';
  import WeightSetting from './pages/WeightSetting';
  import { LineLiff } from './services/LineLiff';

  export default {
    name: 'line-liff-index',
    components: {
      HeaderBar,
      WeightInput,
      WeightSetting,
      WeightRecords
    },
    data() {
      return {
        liffService: new LineLiff(liff),
        appUrl: document.getElementById('app_url').value,
        page: document.getElementById('page').value,
        setting: {
          height: null,
          goal_weight: null,
          goal_fat: null,
          enable_notification: false,
          notify_day: null,
          notify_at: null,
        },
      };
    },
    methods: {
      setPageHandler(page) {
        this.page = page;
      },
    },
    computed: {},
    async created() {
      await this.liffService.init();
      const profile = this.liffService.profile;
      axios.get(`${this.appUrl}/line/liff/weight/my-setting/${profile.userId}`)
        .then(res => {
            if (res.status === 200) {
              if (res.data.setting) {
                this.$set(this, 'setting', res.data.setting);
              } else {
                this.page = 'setting';
              }
            }
          }
        );
    }
  };
</script>

<style scoped>
	#liff {
		height: 800px;
	}

	.fade-enter-active, .fade-leave-active {
		transition: opacity .3s;
	}

	.fade-enter, .fade-leave-to {
		opacity: 0;
	}

	.liff-content {
		position: relative;
	}
</style>