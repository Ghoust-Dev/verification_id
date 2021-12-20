import App from "./App.vue"
import store from './store'
import router from './router'
import axios from 'axios'
import VueAxios from 'vue-axios'
import { BootstrapVue, IconsPlugin, BootstrapVueIcons } from 'bootstrap-vue'

import 'bootstrap/dist/css/bootstrap.css'
import 'bootstrap-vue/dist/bootstrap-vue.css'
import 'bootstrap-vue/dist/bootstrap-vue-icons.min.css'


require('./bootstrap');

window.Vue = require('vue').default;

Vue.use(VueAxios, axios);

const authInstance = axios.create({
    baseURL: window.location.origin
  })

authInstance.defaults.headers.common['Access-Control-Allow-Origin'] = '*';

authInstance.interceptors.request.use(
    (config) => {
      let token = store.state.token;
  
      if (token) {
        config.headers['Authorization'] = `Bearer ${ token }`;
      }
  
      return config;
    }, 
  
    (error) => {
      return Promise.reject(error);
    }
);

Vue.prototype.$auth = authInstance

Vue.use(BootstrapVue)
Vue.use(BootstrapVueIcons)
Vue.use(IconsPlugin)

const app = new Vue({
    el: '#app',
    router,
    store,
    components:{
        App
    },
});
