import Vue from 'vue'
import VueRouter from 'vue-router'

// Routes
import { canNavigate } from '@/libs/acl/routeProtection'
import { moduleleMele, moduleStiute } from '@/libs/module'
import { isUserLoggedIn, getUserData, getHomeRouteForLoggedInUser } from '@/auth/utils'
import apps from './routes/apps'
// import dashboard from './routes/dashboard'
import uiElements from './routes/ui-elements/index'
import pages from './routes/pages'
import chartsMaps from './routes/charts-maps'
import formsTable from './routes/forms-tables'
import others from './routes/others'
import dianasoft from './routes/dianasoft'


Vue.use(VueRouter)

const router = new VueRouter({
  mode: 'history',
  base: process.env.BASE_URL,
  
  scrollBehavior() {
    return { x: 0, y: 0 }
  },
  routes: [
  
    ...dianasoft,
    ...apps,
    
    ...pages,
    ...chartsMaps,
    ...formsTable,
    ...uiElements,
    ...others,
    {
      path: '*',
      redirect: 'error-404',
    },
  ],
})

router.beforeEach((to, _, next) => {
  const isLoggedIn = isUserLoggedIn()
  
  // console.log(to)
  
  // console.log(next)
  // console.log(isLoggedIn)
 
  if (!canNavigate(to)) {
    // Redirect to login if not logged in
    if (!isLoggedIn) return next({ name: 'auth-login' })

    // If logged in => not authorized
    return next({ name: 'misc-not-authorized' })
  }

  // Redirect if logged in
  if (to.meta.redirectIfLoggedIn && isLoggedIn) {
    const userData = getUserData()

    next(getHomeRouteForLoggedInUser(userData ? userData.role : null))
  }

  /*
   * Paginile unui modul nedat nu se deschid deloc: fara asta, omul care scrie
   * adresa de mana ajungea intr-o pagina goala, care doar primea 403-uri de la
   * server. Oprirea adevarata ramane tot acolo, la server.
   *
   * Se hotaraste pe loc daca modulele sunt stiute de la o incarcare anterioara;
   * altfel se asteapta raspunsul serverului, o singura data pe pagina.
   */
  if (to.meta.modul && isLoggedIn) {
    const stiute = moduleStiute()

    // Cerută oricum, ca lista să se împrospăteze după ce administratorul dă sau
    // ia un modul; la prima încărcare, tot ea dă și răspunsul.
    const proaspete = moduleleMele()

    if (stiute) {
      if (stiute.indexOf(to.meta.modul) === -1) return next({ name: 'home' })

      return next()
    }

    // Necunoscut (server vechi sau pana de retea) inseamna „lasa-l sa treaca":
    // pagina se deschide, iar cererile ei sunt oprite oricum de server.
    return proaspete.then(module => (
      module && module.indexOf(to.meta.modul) === -1 ? next({ name: 'home' }) : next()
    ))
  }

  return next()
})

// ? For splash screen
// Remove afterEach hook if you are not using splash screen
router.afterEach(() => {
  // Remove initial loading
  const appLoading = document.getElementById('loading-bg')
  if (appLoading) {
    appLoading.style.display = 'none'
  }
})

export default router
