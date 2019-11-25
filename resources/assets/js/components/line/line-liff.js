const LineLiff = require('./index');
const Vue = require('vue');
import VeLine from 'v-charts/lib/line.common';
import Loading from 'vue-loading-overlay';
// Import stylesheet
import 'vue-loading-overlay/dist/vue-loading.css';
import { download, get, patch, post, remove } from '../../custom/api';

window.api = { get, download, post, patch, remove };
window.jQuery = require('jquery');
window.axios = require('axios');
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

const isProduction = isProd => {
  Vue.config.devtools = !isProd;
  Vue.config.debug = !isProd;
  Vue.config.silent = isProd;
  Vue.config.productionTip = !isProd;
};

/* temp hard code Production mode */
isProduction(false);

Vue.component('Loading', Loading);
Vue.component(VeLine.name, VeLine);

new Vue({
  render: h => h(LineLiff),
}).$mount('#line-liff');