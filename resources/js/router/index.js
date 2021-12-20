import Vue from 'vue'
import VueRouter from 'vue-router'
import Home from '../components/Home.vue'
import Selfie from '../components/Selfie.vue'
import RecVideo from '../components/Video.vue'
import VerifyIDNational from '../components/VerifyIDNational.vue'
import Success from '../components/Success.vue'
import Er404 from '../components/Er404.vue'

import store from '../store'

Vue.use(VueRouter)

const routes = [
    {
        path: '/',
        name: 'Home',
        component: Home,
    },
    {
        path: '/take-selfie',
        name: 'Selfie',
        component: Selfie,
    },
    {
        path: '/rec-video',
        name: 'RecVideo',
        component: RecVideo,
    },
    {
        path: '/verify-identite',
        name: 'VerifyIDNational',
        component: VerifyIDNational,
    },
    {
        path: '/success',
        name: 'Success',
        component: Success,
    },
    {
      path: '/er404',
      name: 'Er404',
      component: Er404
    }
  ]
 
  const router = new VueRouter({
    mode: 'history',
    base: process.env.BASE_URL,
    routes,
  })

  router.beforeEach((to, from, next) => {
    let isSuccess = store.state.success;
    let idVerification = store.state.idVerification;
    if ((to.name === 'Selfie' || to.name === 'RecVideo' || to.name === 'VerifyIDNational') && (isSuccess && idVerification == '')) {
        next({ name: 'Home' });
    }
    else if (to.name === 'Success' && !isSuccess) {
        next({ name: 'Home' })
    }
    else if (!to.name) {next({ name: 'Er404' })}
    else next()
  })

  
export default router 