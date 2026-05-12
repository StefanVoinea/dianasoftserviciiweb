<template>  

  <div class="flex flex-col">
         
          
            
         
          <v-select :label="colCaut" 
                    :options="allRecords" 
                    :value.sync="content" 
                    v-model="content"
                    :name="camp" 
                    class="select-size-sm w-full"
                    :disabled="readonly"
                    @input="handleInputSelectat"
                    @search:focus="handleFocus"
                   >
              <template slot="option" slot-scope="option">
                <div class="d-left flex flex-col" >
                  {{option.label}}   
                </div>
              </template>
                <template slot="selected-option" slot-scope="option">
                <div class="selected d-center">
                 
                  {{ option.label }}
                </div>
              </template>
            <div slot="no-options">Nu există înregistrări...</div>
           </v-select>
           <div class="d-flex justify-content-between">
             <div class="d-flex justify-content-start">
              
                <small>{{campDisplay}}</small>  
            
             </div>
           
           
           <b-button
                  size="sm"
                  v-clipboard:copy="content"
                  v-clipboard:success="onCopy"
                  v-clipboard:error="onError"
                  v-ripple.400="'rgba(186, 191, 199, 0.15)'"
                  variant="flat-primary"
                  class="btn-icon"
                >
                <feather-icon icon="CopyIcon" />
                </b-button>
          
            </div>
              
              </div>
     
      
</template>

<script>
import vSelect from 'vue-select';
import Ripple from 'vue-ripple-directive'
import ToastificationContent from '@core/components/toastification/ToastificationContent.vue'

export default {
 props: {
        value:[String, Number,Object],
        colCaut:String,
        camp:String,
        field_name:String,
        campDisplay:String,
        limitToList:String,
        readonly:Boolean,
        
        },
 components: {
   'v-select': vSelect,
   ToastificationContent,
   
  },
  name:"dropdowncuoptiuni",
 directives: {
    Ripple,
  },
  data() {

    return {
      content:this.value,
      allRecords:[]
    }
  },
  watch:{
      value(){
        this.content = this.value
      }
    },
    
  methods:{
    handleFocus (e) {
      this.$emit('focus',e, this.content)
    },
       onCopy() {
          
            this.$toast({
              component: ToastificationContent,
              props: {
                title: this.content+' copiat!',
                icon: 'BellIcon',
              },
            })
          },
          onError() {
            this.$toast({
              component: ToastificationContent,
              props: {
                title: 'Eroare la copiere text!',
                icon: 'BellIcon',
              },
            })
          },
    handleInput (e) {
      
      this.content=e
      this.$emit('input', this.content)
      this.$emit('change', this.content)
    },
    handleInputSelectat (e) {
       
       this.content=e
       this.$emit('input', this.content)
       this.$emit('change', this.content)
    },
  },
   created(){
        if (this.$store.state.app.optiunidropdown==null){
            return []
          }
           let recs=JSON.parse(this.$store.state.app.optiunidropdown)
           // // console.log(recs)

          let recListTmp=[]
          recs.map((t)=>{
              if(t.field_name==this.field_name){
               recListTmp.push(t.field_option)
              }
          })

          this.allRecords=recListTmp

        
          
      
    

  },
   
    
}
  
</script>
<style lang="scss">
.labelSelect {
  font-size:.9rem;
  color:gray;
}

</style>
