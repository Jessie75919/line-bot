const Foody = require('./index');
const Vue = require('vue');

const isProduction = isProd => {
  Vue.config.devtools = !isProd;
  Vue.config.debug = !isProd;
  Vue.config.silent = isProd;
  Vue.config.productionTip = !isProd;
};

/* temp hard code Production mode */
isProduction(true);

new Vue({
  render: h => h(Foody),
}).$mount('#foody');