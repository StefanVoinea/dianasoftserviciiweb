import Vue from 'vue'
// import { ToastPlugin, ModalPlugin } from 'bootstrap-vue'
import VueCompositionAPI from '@vue/composition-api'

import i18n from '@/libs/i18n'
import router from './router'
import store from './store'
import App from './App.vue'

// Global Components
import './global-components'




// 3rd party plugins
import '@axios'
import '@/libs/acl'
import '@/libs/portal-vue'
import '@/libs/clipboard'
import '@/libs/toastification'
import '@/libs/sweet-alerts'
import '@/libs/vue-select'
import '@/libs/tour'
import { pornesteSkinul } from '@/libs/ds-skinuri'

// Axios Mock Adapter
import '@/@fake-db/db'

// BSV Plugin Registration
// Vue.use(ToastPlugin)
// Vue.use(ModalPlugin)

// Composition API
Vue.use(VueCompositionAPI)

// Feather font icon - For form-wizard
// * Shall remove it if not using font-icons of feather-icons - For form-wizard
require('@core/assets/fonts/feather/iconfont.css') // For form-wizard

// import core styles
require('@core/scss/core.scss')

// import assets styles
require('@/assets/scss/style.scss')

/*
 * Skinurile DianaSoft, ultimele.
 *
 * Stilurile Vuexy nu vin din fisierul de CSS al paginii, ci sunt puse in <head>
 * de JavaScript, dupa el. Skinurile scrise acolo ajungeau deci sub ele si nu se
 * vedeau decat schimbarea de light/dark. Aduse aici, intra pe acelasi drum si
 * dupa ele — la specificitate egala, ale noastre sunt cele care raman.
 */
require('@/assets/scss/ds-skinuri.scss')
import permisiuni from './plugins/userpermitt'
Vue.use(permisiuni);
import {  localize } from 'vee-validate'
localize('ro')


/*
 import Echo from "laravel-echo"

 // import io from 'socket.io-client'
    window.io = require('socket.io-client')
//import { io } from "socket.io-client";

 const token =  localStorage.getItem('access_token')
 //window.io=io
   //console.log(io)
  if (typeof window.io !== 'undefined') {
   window.Echo = new Echo({
           namespace: 'App\\Events',
           broadcaster: 'socket.io',
  //        client: require('socket.io-client'),
         key: '80fa8097785fb5a9db6c1eed41c0f8ca',
            host: 'https://' + window.location.hostname + ':6001',
            auth: {
                   headers: {

                     Authorization: 'Bearer '+ token
                   }
                 }

       })
  } 
*/

import {globalHelpers} from './plugins/globalHelpers.js'
Vue.prototype.$globalHelpers = globalHelpers

Vue.config.productionTip = false
import VueClipboard from 'vue-clipboard2'
VueClipboard.config.autoSetContainer = true
Vue.use(VueClipboard)

/*
 * Skinul ales data trecuta se pune inainte de a se desena ceva: pus dupa, omul
 * ar vedea o clipa interfata standard, apoi sarind in skinul lui.
 */
pornesteSkinul()

new Vue({
  router,
  store,
  i18n,
  render: h => h(App),
}).$mount('#app')
