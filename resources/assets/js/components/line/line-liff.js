const LineLiff = require('./index');
const Vue = require('vue');

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

new Vue({
  render: h => h(LineLiff),
}).$mount('#line-liff');