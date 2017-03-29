/**
 * JavaScript dependencies
 */

window._ = require('lodash');
// window.$ = window.jQuery = require('jquery');
// require('bootstrap-sass');
// window.Vue = require('vue');
window.axios = require('axios');

/**
 * Ajax config
 */

window.axios.defaults.headers.common = {
    'X-CSRF-TOKEN': window.Laravel.csrfToken,
    'X-Requested-With': 'XMLHttpRequest'
};

/**
 * Vue application instance
 */

import Vue from 'vue'
import VueRouter from 'vue-router'
import ElementUI from 'element-ui'
import lang from 'element-ui/lib/locale/lang/es'
import locale from 'element-ui/lib/locale'
import 'element-ui/lib/theme-default/index.css'
// import './assets/theme/theme-darkblue/index.css'
import './assets/icon.css'

// configure language
locale.use(lang)

// app components
import App from './App.vue'
import routes from './routes'

// components
Vue.use(ElementUI)
Vue.use(VueRouter)

// disable message
Vue.config.productionTip = false

const router = new VueRouter({
  routes
})

const app = new Vue({
	router,
  render: h => h(App),
  el: '#app',
});
