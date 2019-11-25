<template>
	<div id="liff">
		<loading :active.sync="isLoading"
		         :height="200"
		         :is-full-page="true"
		         :width="200"
		         color="#1c516a"
		></loading>
		<header-bar :page="page"
		            @setPage="setPageHandler"
		></header-bar>
		<div class="liff-content">
			<transition mode="out-in" name="fade">
				<weight-input :liffService="liffService"
				              :setting="setting"
				              @startLoading="setLoading(true)"
				              @stopLoading="setLoading(false)"
				              v-if="page ==='index'">

				</weight-input>
				<weight-setting :liffService="liffService"
				                :setting="setting"
				                @stopLoading="setLoading(false)"
				                v-else-if="page === 'setting'">
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
        isLoading: true,
        liffService: new LineLiff(liff),
        appUrl: document.getElementById('app_url').value,
        page: document.getElementById('page').value,
        lineLiffApi: null,
        setting: {
          height: null,
          goal_weight: null,
          goal_fat: null,
          enable_notification: false,
          notify_at: null,
          notify_days: []
        },
      };
    },
    methods: {
      setPageHandler(page) {
        this.page = page;
      },
      setLoading(mode) {
        this.isLoading = mode;
      }
    },
    computed: {},
    async created() {
      await this.liffService.init();
      const profile = this.liffService.profile;
      this.lineLiffApi = new LineLiffWeightApi(this.appUrl, profile.userId);
      this.lineLiffApi.getSetting()
        .then(res => {
            const setting = res.data.data;
            if (setting) {
              this.$set(this, 'setting', setting);
              this.isLoading = false;
            } else {
              this.page = 'setting';
            }
          }
        );
    }
  };
</script>

<style>
	#liff {
		min-height: 800px;
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