<template>
   <div class="nav">
    <ul class="nav navbar-nav">
      <li class="nav-item">
       
         <h4 class="brand-text mt-1 mr-1" style="color:#8075f1;">
          {{denumireSocCurenta}}
        </h4>
       </li>
       <li class="nav-item">
          
           <datepicker @input="modificaLunaCurenta" 
                    class="w-1/2  form-control"

                    v-model="lunaCurenta" 
                    format="MMMM yyyy" 
                    dark
                    :language="languages[language]" 
                    :minimumView="'month'" 
                    :maximumView="'year'">
          </datepicker>
     
          
       </li>
      
       <li class="nav-item">
         
           <selectonewithoutedit
                 @change="this.modificaGestiuneaCurenta"
                  name="gestiune"
                  v-model="gestiuneSocCurenta" 
                  colCaut="denumire" 
                  camp="gestiune" 
                  class="w-1/4 ml-1"
                  ruta="gestiuniPermise"
                  limitToList="true"
                  > 
         </selectonewithoutedit>  
         
        
         
       </li>
    </ul>
  </div>
         
        
</template>

<script>


import store from '@/store'
import Datepicker from 'vuejs-datepicker'
import * as lang from 'vuejs-datepicker/src/locale';
import { BFormDatepicker,BFormInput } from 'bootstrap-vue';


export default {
  components: {
    Datepicker,
    BFormDatepicker,
    BFormInput
  },
  
  setup() {
    
  //const denumireSocCurenta=store.state.societateaCurenta?JSON.parse(store.state.societateaCurenta).denumire:"";
  //const gestiuneSocCurenta=store.state.societateaCurenta?JSON.parse(store.state.gestiuneaCurenta).denumire:""; 
   
     
  },
data(){
 return {
   denumireSocCurenta:store.state.app.societateaCurenta?JSON.parse(store.state.app.societateaCurenta).denumire:"",
   gestiuneSocCurenta:store.state.app.gestiuneaCurenta?JSON.parse(store.state.app.gestiuneaCurenta).denumire:"",
   lunaCurenta:store.state.app.lunaCurenta,
   language:"ro",
   languages:lang,
   selected: null,
  
 }
},
  methods: {
       modificaLunaCurenta(){
       
        if(this.lunaCurenta==null)
        {
           var date = new Date();
           var firstDay = new Date(date.getFullYear(), date.getMonth(), 1);
           store.dispatch('app/lunaCurenta',firstDay)
           this.lunaCurenta=firstDay
        }else{
           var firstDay = this.lunaCurenta;
           store.dispatch('app/lunaCurenta',firstDay)
        }
       },
       modificaGestiuneaCurenta(valoare){
       
        if(this.gestiuneSocCurenta==null)
        {
           this.gestiuneSocCurenta=JSON.parse(store.state.app.gestiuneaCurenta).denumire
       
        }else{
           store.dispatch('app/gestiuneaCurenta',JSON.stringify(valoare))
        }
       },
     }
}
</script>

<style lang="scss" scoped>
@import '~@core/scss/base/bootstrap-extended/include';

ul
{
  list-style: none;
  padding: 0;
  margin: 0;
}
p {
  margin: 0;
}

.nav-bookmar-content-overlay {
    position: fixed;
    opacity: 0;
    width: 100%;
    height: 100%;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: rgba(0, 0, 0, 0.5);
    -webkit-transition: all 0.7s;
    transition: all 0.7s;
    z-index: -1;

    &:not(.show) {
      pointer-events: none;
    }

    &.show {
      cursor: pointer;
      z-index: 10;
      opacity: 1;
    }
}
</style>
