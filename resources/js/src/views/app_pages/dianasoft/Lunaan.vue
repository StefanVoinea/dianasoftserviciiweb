<template>  
   <div class="flex flex-col ">
    
          <datepicker 
                        style="font-size:larger;"
                        v-show="afisezLocal"
                       :language="languages[language]"
                       format="MMMM yyyy"
                       :minimumView="'month'"
                       :maximumView="'year'"
                        class="  form-control"
                       :name="name"
                       :placeholder="campDisplay"
                       :value.sync="content" 
                       v-model="content"
                       @input="handleInput" 
                       >
          </datepicker>
    <div class="d-flex justify-content-end">
    <span class="labelSelect"> Luna </span>
    </div>  
    </div>
  
</template>

<script>

import Datepicker from 'vuejs-datepicker'
import * as lang from 'vuejs-datepicker/src/locale';
export default {
 props: {
        value:[String,Date],
        name:String,
        campDisplay:String,
        activeEdit:Boolean,
        afisez:Boolean
        },
  components: {
    Datepicker
  },
  name:"LunaAn",
 
  data() {

    return {
      afisezLocal:this.afisez,
      content:this.value,
      language: "ro",
      languages: lang,
        
    }
  },
  watch:{
   afisez(){
    this.afisezLocal=this.afisez
   },
    value(){
        this.content = this.value
      }
    },
  methods:{
   
    handleInput (e) {
      this.content=e
      this.$emit('input', this.content)
      
    },
  

  },
 
}
  
</script>
