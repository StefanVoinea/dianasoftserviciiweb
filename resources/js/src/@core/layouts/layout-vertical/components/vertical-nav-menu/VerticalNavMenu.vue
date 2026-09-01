<template>
  <div
    class="main-menu menu-fixed menu-accordion menu-shadow"
    :class="[
      { 'expanded': !isVerticalMenuCollapsed || (isVerticalMenuCollapsed && isMouseHovered) },
      skin === 'light'|| skin === 'bordered' ? 'menu-light' : 'menu-dark'
    ]"
    @mouseenter="updateMouseHovered(true)"
    @mouseleave="updateMouseHovered(false)"
  >
    <!-- main menu header-->
    <div class="navbar-header expanded">
      <slot
        name="header"
        :toggleVerticalMenuActive="toggleVerticalMenuActive"
        :toggleCollapsed="toggleCollapsed"
        :collapseTogglerIcon="collapseTogglerIcon"
      >
        <ul class="nav navbar-nav flex-row">

          <!-- Logo & Text -->
          <li class="nav-item mr-auto">
            <b-link
              class="navbar-brand"
              to="/"
            >
              <span class="brand-logo">
                <b-img
                  :src="appLogoImage"
                  alt="logo"
                />
              </span>
              <h2 class="brand-text">
                {{ appName }}
              </h2>
            </b-link>
          </li>

          <!-- Toggler Button -->
          <li class="nav-item nav-toggle">
            <b-link class="nav-link modern-nav-toggle">
              <feather-icon
                icon="XIcon"
                size="20"
                class="d-block d-xl-none"
                @click="toggleVerticalMenuActive"
              />
              <feather-icon
                :icon="collapseTogglerIconFeather"
                size="20"
                class="d-none d-xl-block collapse-toggle-icon"
                @click="toggleCollapsed"
              />
            </b-link>
          </li>
        </ul>
      </slot>
    </div>
    <!-- / main menu header-->

    <!-- Shadow -->
    <div
      :class="{'d-block': shallShadowBottom}"
      class="shadow-bottom"
    />

    <!-- main menu content-->
    <vue-perfect-scrollbar
      :settings="perfectScrollbarSettings"
      class="main-menu-content scroll-area"
      tagname="ul"
      @ps-scroll-y="evt => { shallShadowBottom = evt.srcElement.scrollTop > 0 }"
    >
      <vertical-nav-menu-items
        :items="navMenuItems"
        class="navigation navigation-main"
      />
    </vue-perfect-scrollbar>
    <!-- /main menu content-->
  </div>
</template>

<script>
import VuePerfectScrollbar from 'vue-perfect-scrollbar'
import { BLink, BImg } from 'bootstrap-vue'
import { provide, computed, ref } from '@vue/composition-api'
import useAppConfig from '@core/app-config/useAppConfig'
import { $themeConfig } from '@themeConfig'
import VerticalNavMenuItems from './components/vertical-nav-menu-items/VerticalNavMenuItems.vue'
import useVerticalNavMenu from './useVerticalNavMenu'
import store from "@/store";
import router from "@/router";
import { faraModuleleNedate, meniulOprit, meniulOpritProaspat } from '@/libs/module'

export default {
  components: {
    VuePerfectScrollbar,
    VerticalNavMenuItems,
    BLink,
    BImg,
  },
  props: {
    isVerticalMenuActive: {
      type: Boolean,
      required: true,
    },
    toggleVerticalMenuActive: {
      type: Function,
      required: true,
    },
 /*   navMenuItems: {
      type: Array,
      required: true,
    },
    */
  },
   watch: {
   '$store.state.app.user': function() {
               this.creezmenu()
   }
  },
 data(){
  return {
    navMenuItems:[],
  }
 },
 created() {
    
        this.creezmenu()
     
    
  },
  methods: {
      creezmenu () {
        let company_id=JSON.parse(store.state.app.societateaCurenta).id
        let menuoptions=JSON.parse(store.state.app.user).dianasoftmenuoptions.filter((e)=>e.pivot.company_id == company_id )

        /*
         * Intrarile modulelor nedate nu se arata: ar fi drumuri care duc la un
         * „nu aveti acces". Meniul se face pe loc, cu ce se stia de data
         * trecuta, si inca o data cand raspunde serverul — altfel ar clipi la
         * fiecare incarcare de pagina.
         */
        this.navMenuItems = this.createMenuOptions(faraModuleleNedate(menuoptions, meniulOprit()), '\\')

        meniulOpritProaspat().then(oprite => {
          this.navMenuItems = this.createMenuOptions(faraModuleleNedate(menuoptions, oprite), '\\')
        })
    },
    createMenuOptions(menuoptions,parinte){
               let menuoptionsGroup=menuoptions.filter((menuoption)=>{
                                return menuoption.parent===parinte})
               let menu=[]

               menuoptionsGroup.forEach((value, index) => {
               let menuoption={}
              
               if(value.pivot.isactive==1)
               {
             if(value.dropdown==1)
               {
               //  menuoption.header=value.name
                 menuoption.title=value.name
                 menuoption.icon=value.icon    
                 let menuSubGroup=this.createMenuOptions(menuoptions,value.name)
                 menuoption.children=menuSubGroup
               }else{
                     menuoption.url=value.url
                     menuoption.route=value.url
                  //    menuoption.header=value.name
                     menuoption.title=value.name
                     menuoption.slug=value.slug
                     menuoption.icon=value.icon
                     menuoption.tag=value.tag
                     menuoption.tagColor=value.tagColor
                     menuoption.i18n=value.i18n
               }
                menu.push(menuoption)
            }
                
            });
              
                return menu

        },
      },
  setup(props) {
    const {
      isMouseHovered,
      isVerticalMenuCollapsed,
      collapseTogglerIcon,
      toggleCollapsed,
      updateMouseHovered,
    } = useVerticalNavMenu(props)


    const { skin } = useAppConfig()

    // Shadow bottom is UI specific and can be removed by user => It's not in `useVerticalNavMenu`
    const shallShadowBottom = ref(false)

    provide('isMouseHovered', isMouseHovered)

    const perfectScrollbarSettings = {
      maxScrollbarLength: 60,
      wheelPropagation: false,
    }

    const collapseTogglerIconFeather = computed(() => (collapseTogglerIcon.value === 'unpinned' ? 'CircleIcon' : 'DiscIcon'))

    // App Name
    const { appName, appLogoImage } = $themeConfig.app

    return {
      perfectScrollbarSettings,
      isVerticalMenuCollapsed,
      collapseTogglerIcon,
      toggleCollapsed,
      isMouseHovered,
      updateMouseHovered,
      collapseTogglerIconFeather,

      // Shadow Bottom
      shallShadowBottom,

      // Skin
      skin,

      // App Name
      appName,
      appLogoImage,
    }
  },
}
</script>

<style lang="scss">
@import "~@core/scss/base/core/menu/menu-types/vertical-menu.scss";
</style>
