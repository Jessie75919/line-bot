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
				              v-if="lineLiffApi && page ==='index'">

				</weight-input>
				<weight-setting :liffService="liffService"
				                :setting="setting"
				                @stopLoading="setLoading(false)"
				                v-else-if="lineLiffApi && page === 'setting'">
				</weight-setting>
				<weight-records :lineLiffApi="lineLiffApi"
				                @startLoading="setLoading(true)"
				                @stopLoading="setLoading(false)"
				                v-else-if="lineLiffApi && page === 'records'">
				</weight-records>
				<weight-graph :lineLiffApi="lineLiffApi"
				              @startLoading="setLoading(true)"
				              @stopLoading="setLoading(false)"
				              v-else-if="lineLiffApi && page === 'review'">
				</weight-graph>
			</transition>
		</div>
	</div>
</template>

<script>
  import { LineLiffWeightApi } from 'resources/assets/js/components/line/api/LineLiffWeightApi';
  import HeaderBar from 'resources/assets/js/components/line/common/HeaderBar';
  import WeightGraph from 'resources/assets/js/components/line/pages/WeightGraph';
  import WeightInput from 'resources/assets/js/components/line/pages/WeightInput';
  import WeightRecords from 'resources/assets/js/components/line/pages/WeightRecords';
  import WeightSetting from 'resources/assets/js/components/line/pages/WeightSetting';
  import { LineLiff } from 'resources/assets/js/components/line/services/LineLiff';

  export default {
    name: 'line-liff-index',
    components: {
      WeightGraph,
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