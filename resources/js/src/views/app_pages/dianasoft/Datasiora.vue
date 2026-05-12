<template>  
       <div >
    <flat-pickr
          v-model="content"
           :disabled="readonly"
          class="form-control"
          @focus.native="handleFocus"
          @input="handleInput"
          :config="{ enableTime: true,dateFormat: 'd.m.Y H:i'}"
        /> 
  
    <div class="d-flex justify-content-end">
    <span class="labelSelect" >{{campDisplay}}</span>
    </div>
  </div>
</template>

<script>

import flatPickr from 'vue-flatpickr-component'

export default {
  components: {
   
    flatPickr,
  },

 props: {
        value:[String,Date],
        name:String,
        campDisplay:String,
        readonly:Boolean,
        
        },

  name:"datasiora",
 
  data() {

    return {
      
      formatted: '',
      selected: '',
      content:this.value,
      
    }
  },
  watch:{
     value(){
         this.content = this.value
       }
    },
  methods:{
    handleFocus (e) {
      
      this.$emit('focus',e)
      
    },
    handleInput (e) {
      this.content=e
      this.$emit('input', this.content)
      this.$emit('change', this.content)
      
    },
  

  },
 
}
  
</script>
<style lang="scss">
.labelSelect {
  font-size:.9rem;
  color:rgba(115, 103, 240) !important;
}

@import '@core/scss/vue/libs/vue-flatpicker.scss';
</style>