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
				<meal-setting :liffService="liffService"
				              :lineLiffApi="lineLiffApi"
				              :setting="setting"
				              @stopLoading="setLoading(false)"
				              v-if="lineLiffApi && page === 'index'">
				</meal-setting>
				<meal-records :lineLiffApi="lineLiffApi"
				              @startLoading="setLoading(true)"
				              @stopLoading="setLoading(false)"
				              v-else-if="lineLiffApi && page === 'records'">
				</meal-records>
			</transition>
		</div>
	</div>
</template>

<script>
  import { LineLiff } from '../services/LineLiff';
  import { LineLiffMealApi } from './api/LineLiffMealApi';
  import HeaderBar from './common/HeaderBar';
  import MealRecords from './pages/Records';
  import MealSetting from './pages/Setting';

  export default {
    name: 'line-liff-index',
    components: {
      HeaderBar,
      MealSetting,
      MealRecords
    },
    data() {
      return {
        isLoading: true,
        liffService: new LineLiff(liff),
        appUrl: document.getElementById('app_url').value,
        page: document.getElementById('page').value,
        lineLiffApi: null,
        setting: null,
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
      this.lineLiffApi = new LineLiffMealApi(this.appUrl, profile.userId);
      this.isLoading = false;
      this.lineLiffApi.getSetting()
        .then(res => {
            this.$set(this, 'setting', res.data.data);
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