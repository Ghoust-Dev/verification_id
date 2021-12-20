import Vue from 'vue'
import Vuex from 'vuex'
import createPersistedState from 'vuex-persistedstate'
import * as Cookies from 'js-cookie';

Vue.use(Vuex)

export default new Vuex.Store({
  state: {
    idVerification: '',
    success: false,
  },
  mutations: {
    success (state, params) {
      state.success = params;
    }, 
    idVerification (state, params) {
      state.idVerification = params;
    },    
  },
  actions: {
    success (context, params) {
      context.commit('success', params)
    },
    idVerification (context, params) {
      context.commit('idVerification', params)
    },
    
  },
  modules: {
  },
  plugins: [
    createPersistedState({
      storage: window.sessionStorage,
      getState: (key) => Cookies.getJSON(key),
      setState: (key, state) => Cookies.set(key, state, { expires: 3, secure: true }),
      removeItem: (key) => Cookies.remove(key),
    })
  ]
})
