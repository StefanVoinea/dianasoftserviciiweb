import { $themeBreakpoints } from '@themeConfig'
import axios from '@axios'
axios.defaults.baseURL=window.api_url

export default {
  namespaced: true,
  state: {
    windowWidth: 0,
    shallShowOverlay: false,
    token:localStorage.getItem('access_token') || null,
    user:localStorage.getItem('user') || null,
    societateaCurenta:localStorage.getItem('societateaCurenta') || null,
    lunaCurenta:localStorage.getItem('lunaCurenta') || null,
    gestiuneaCurenta:localStorage.getItem('gestiuneaCurenta') || null,
    tari:localStorage.getItem('tari') || null,
    ultimacautare:localStorage.getItem('ultimacautare') || null,
    optiunidropdown:localStorage.getItem('optiunidropdown') || null,
  },
  getters: {
    currentBreakPoint: state => {
      const { windowWidth } = state
      if (windowWidth >= $themeBreakpoints.xl) return 'xl'
      if (windowWidth >= $themeBreakpoints.lg) return 'lg'
      if (windowWidth >= $themeBreakpoints.md) return 'md'
      if (windowWidth >= $themeBreakpoints.sm) return 'sm'
      return 'xs'
    },
    loggedIn(state) {
       
      return state.token !== null&&state.societateaCurenta !== null&&state.user !== null
    },
  },
  mutations: {
    
    user(state,user){
        state.user=user
    },
   
     tari(state,tari){
        state.tari=tari
    },
    judet(state,judet){
        state.judet=judet
    },
    tari(state,tari){
        state.tari=tari
    },
   nomalerte(state,nomalerte){
        state.nomalerte=nomalerte
    },
    optiunidropdown(state,optiunidropdown){
        state.optiunidropdown=optiunidropdown
    },
    ultimacautare(state,ultimacautare){
        state.ultimacautare=ultimacautare
    },
    retrieveUser(state,user){
        state.user=user
      },
     retrieveToken(state,token){
        state.token=token
      },
     destroyToken(state) {
       
         state.token = null
      },
    societateaCurenta(state,societateaCurenta){
        state.societateaCurenta=societateaCurenta
      },
    lunaCurenta(state,lunaCurenta){
        state.lunaCurenta=lunaCurenta
      },
    UPDATE_WINDOW_WIDTH(state, val) {
      state.windowWidth = val
    },
    TOGGLE_OVERLAY(state, val) {
      state.shallShowOverlay = val !== undefined ? val : !state.shallShowOverlay
    },
  },
  actions: {
    
    ultimacautare(context,ultimacautare){
            localStorage.setItem('ultimacautare', ultimacautare)
            context.commit('ultimacautare', ultimacautare)
    },
    societateaCurenta(context,socCurenta)
    {
           localStorage.setItem('societateaCurenta', socCurenta)
            context.commit('societateaCurenta', socCurenta)
     
    },
     user(context,user)
    {
           localStorage.setItem('user', user)
           context.commit('user', user)
     
    },
    lunaCurenta(context,lunaCurenta)
    {
           localStorage.setItem('lunaCurenta', lunaCurenta)
           context.commit('lunaCurenta', lunaCurenta)
     
    },
    tari(context,nomTari)
    {
            localStorage.setItem('tari', nomTari)
            context.commit('tari', nomTari)
     },
    judet(context,judet)
    {
            localStorage.setItem('judet', judet)
            context.commit('judet', judet)
     },
    tari(context,tari)
    {
            localStorage.setItem('tari', tari)
            context.commit('tari', tari)
    }, 
    nomalerte(context,nomalerte)
    {
            localStorage.setItem('nomalerte', nomalerte)
            context.commit('nomalerte', nomalerte)
     }, 
     optiunidropdown(context,optiunidropdown)
    {
            localStorage.setItem('optiunidropdown', optiunidropdown)
            context.commit('optiunidropdown', optiunidropdown)
     },
    api_Request(context,payLoad) {

        const accessToken = context.state.token || localStorage.getItem('access_token')
        const societateaCurenta = context.state.societateaCurenta || localStorage.getItem('societateaCurenta')

        if (accessToken) {
          axios.defaults.headers.common['Authorization'] = 'Bearer ' + accessToken
        }
        if (societateaCurenta) {
          axios.defaults.headers.common['AuthorizationHeader'] = JSON.parse(societateaCurenta).id
        }

        return new Promise((resolve, reject) => {
            axios[payLoad.requestType](payLoad.requestUrl, payLoad)
                .then(response => {
                 
                    resolve(response.data);
                })
                .catch(error => {
                    reject(error.response);
                });
        });
    },
    updateCookiesLocal(context, cookieLocal) {
        
        return context.dispatch('api_Request', {
            requestType: 'post',
            requestUrl: '/utilizatori/cookiesLocal',
            cookieLocal,
        })
    },
    api_faraCompany_Request(context,payLoad) {
        const accessToken = context.state.token || localStorage.getItem('access_token')

        if (accessToken) {
          axios.defaults.headers.common['Authorization'] = 'Bearer ' + accessToken
        }
        
        return new Promise((resolve, reject) => {
            axios[payLoad.requestType](payLoad.requestUrl, payLoad)
                .then(response => {
                    resolve(response.data);
                })
                .catch(error => {
                    reject(error.response);
                });
        });
    },
   api_blob_Request(context,payLoad) {
        const accessToken = context.state.token || localStorage.getItem('access_token')
        const societateaCurenta = context.state.societateaCurenta || localStorage.getItem('societateaCurenta')

        if (accessToken) {
          axios.defaults.headers.common['Authorization'] = 'Bearer ' + accessToken
        }
        if (societateaCurenta) {
          axios.defaults.headers.common['AuthorizationHeader'] = JSON.parse(societateaCurenta).id
        }
        return new Promise((resolve, reject) => {
             axios({
                    url: payLoad.requestUrl,
                    method: payLoad.requestType,
                    responseType: 'blob',
                    data:payLoad
                  })
                .then(response => {
                    resolve(response.data);
                })
                .catch(error => {
                    reject(error.response);
                });
        });
    },
    retrieveUser(context) {

      const accessToken = context.state.token || localStorage.getItem('access_token')
      if (accessToken) {
        axios.defaults.headers.common['Authorization'] = 'Bearer ' + accessToken
      }
      return new Promise((resolve, reject) => {
        axios.get('/user')
          .then(response => {
            
            const user= JSON.stringify({
                          name:response.data.name,
                          userRole:response.data.user_type,
                          email:response.data.email,
                          telefon:response.data.telefon,
                          functia:response.data.functia,
                          id:response.data.id,
                          companies:response.data.companies,
                          dianasoftmenuoptions:response.data.dianasoftmenuoptions,
                          permissions:response.data.permissions,
                          gestiuni:response.data.gestiuni,
                          status:response.data.status,
                          link_poza:response.data.link_poza,
                          ability: [
                                        {
                                          action: 'manage',
                                          subject: 'all',
                                        },
                                     ]   
                        })
           
            localStorage.setItem('user', user)
            context.commit('retrieveUser', user)
            resolve(response)
            
          })
          .catch(error => {
            
            reject(error)
          })
        })
    },
    retrieveToken(context, credentials) {
     
     
      return new Promise((resolve, reject) => {
        axios.post('/login', {
          username: credentials.email,
          password: credentials.password,
        })
          .then(response => {
            const token = response.data.access_token

            localStorage.setItem('access_token', token)
            context.commit('retrieveToken', token)
            resolve(response)
            
          })
          .catch(error => {
            
            reject(error)
          })
        })
    },
    destroyToken(context) {
      const accessToken = context.state.token || localStorage.getItem('access_token')
      if (accessToken) {
        axios.defaults.headers.common['Authorization'] = 'Bearer ' + accessToken
      }

      if (context.getters.loggedIn) {
        return new Promise((resolve, reject) => {
          axios.post('/logoutAPI')
            .then(response => {
               
               localStorage.removeItem('access_token')
               context.commit('destroyToken')
               localStorage.removeItem('user')
               context.commit('retrieveUser')
               localStorage.removeItem('societateaCurenta')
               context.commit('societateaCurenta')
                localStorage.removeItem('tari')
               context.commit('tari')
               localStorage.removeItem('judet')
               context.commit('judet')
               localStorage.removeItem('tari')
               context.commit('tari')
                localStorage.removeItem('lunaCurenta')
               context.commit('lunaCurenta')
               localStorage.removeItem('nomalerte')
               context.commit('nomalerte')
               localStorage.removeItem('optiunidropdown')
               context.commit('optiunidropdown')
               context.commit('ultimacautare')
               resolve(response)

              
            })
            .catch(error => {
               localStorage.removeItem('access_token')
               context.commit('destroyToken')
               localStorage.removeItem('user')
               context.commit('retrieveUser')
               localStorage.removeItem('societateaCurenta')
               context.commit('societateaCurenta')
                localStorage.removeItem('tari')
               context.commit('tari')
               localStorage.removeItem('judet')
               context.commit('judet')
               localStorage.removeItem('tari')
               context.commit('tari')
                localStorage.removeItem('lunaCurenta')
               context.commit('lunaCurenta')
               localStorage.removeItem('nomalerte')
               context.commit('nomalerte')
               localStorage.removeItem('optiunidropdown')
               context.commit('optiunidropdown')
               context.commit('ultimacautare')
              reject(error)
            })
        })
      }
    },
     societateaCurenta(context,socCurenta)
    {
           localStorage.setItem('societateaCurenta', socCurenta)
            context.commit('societateaCurenta', socCurenta)
     
    },
     lunaCurenta(context,lunaCurenta)
    {
          localStorage.setItem('lunaCurenta', lunaCurenta)
          context.commit('lunaCurenta', lunaCurenta)
     
    },
  },
}
