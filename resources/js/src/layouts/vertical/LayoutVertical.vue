<template>
  <layout-vertical :nav-menu-items="navMenuItems">

    <router-view />

    <app-customizer
      v-if="showCustomizer"
      slot="customizer"
    />

  </layout-vertical>
</template>

<script>
import LayoutVertical from '@core/layouts/layout-vertical/LayoutVertical.vue'
import AppCustomizer from '@core/layouts/components/app-customizer/AppCustomizer.vue'
import { $themeConfig } from '@themeConfig'
//import navMenuItems from '@/navigation/vertical'
import store from "@/store";
import router from "@/router";

export default {
  components: {
    AppCustomizer,
    LayoutVertical,
  },
  data() {
    return {
      showCustomizer: $themeConfig.layout.customizer,
      navMenuItems:[{}],
    }
  },
   created() {
    
      
        let company_id=JSON.parse(store.state.app.societateaCurenta).id
        let menuoptions=JSON.parse(store.state.app.user).dianasoftmenuoptions.filter((e)=>e.pivot.company_id == company_id )
        let menu=this.createMenuOptions(menuoptions,"\\")

        
        
        this.navMenuItems=menu
     
    
  },
  methods: {
     
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
                
                let menuSubGroup=this.createMenuOptions(menuoptions,value.name)
                menuoption.children=menuSubGroup
               }
              if(value.dropdown==1){
               menuoption.title=value.name
               }
               menuoption.icon=value.icon

               if(value.dropdown!=1){
               //  menuoption.url=value.url
                 menuoption.route=value.url
              
               menuoption.title=value.name
             //  menuoption.slug=value.slug
               menuoption.tag=value.tag
               menuoption.tagColor=value.tagColor
              // menuoption.i18n=value.i18n
                menuoption.i18n=value.name
                 }
               
                menu.push(menuoption)
            }
                
            });
              
                return menu

        },
      }
}
</script>

