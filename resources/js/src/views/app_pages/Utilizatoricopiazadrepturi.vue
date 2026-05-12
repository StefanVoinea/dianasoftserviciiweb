<template>
  <validation-observer ref="simpleRules">
  <div class= "d-flex justify-content-center" >
   <b-modal id="Dianasoftmodelaev"  
             size="lg" 
             no-close-on-backdrop
             ok-variant="success"
             cancel-title="Cancel"              
             ok-title="Save"
             cancel-variant="warning"
             scrollable
             
             modal-class="modal-success"
             :title="activeActionLocal+' drepturi de la utilizatorul '+this.editVarLocal.from.name"
             v-model="activeCopyLocal"
             @ok="handleOk"
             @cancel="aevClosed"
             >
          <template #modal-footer>
   <div v-if="!showLoading">
      <b-button variant="warning"
                @click="aevClosed">
         Cancel
      </b-button>

      <b-button variant="success"
                @click="handleOk">
         Salvez
      </b-button>
   </div>
   <div v-else class="text-center w-100">
       <b-spinner variant="info" small label="Salvez..."></b-spinner>
       <label class="labelSelect"> Vă rugăm așteptați...</label>
   </div>
</template>   
     <form  ref="form"  @submit.stop.prevent="handleSubmit" >
             <br>

            
                <b-row class="d-flex justify-content-center">
                     
                            <b-col  cols="8" >     
                           
                            <div class="form-label-group">
                             <validation-provider
                                    #default="{ errors }"
                                    name="Utilizator"
                                    rules="required"
                                  >
                                <selectmultipleuser  labelDisplay="Catre"
                                                @change="modificaTo"
                                                v-model="editVarLocal.to"
                                                 />
                                           
                                 <small class="text-danger">{{ errors[0] }}</small>
                                </validation-provider>
                                  <label for="name">Utilizatori</label>
                                </div> 
                            </b-col>
                           </b-row >
                           
                
              <br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br>
      </form>
      
    </b-modal>
  </div>
   </validation-observer>
</template>

<script>

import Ripple from "vue-ripple-directive"
import { heightTransition } from "@core/mixins/ui/transition"
import {VBModal} from "bootstrap-vue"
import {  required, email, confirmed, password,min} from '@validations'
import { ValidationProvider, ValidationObserver} from 'vee-validate'

export default {
  props: {
        activeCopy:Boolean,
        activeAction:String,
        editVar:Object,
        rutainapoi:String,
        },
  mixins: [heightTransition],
  components: {
      ValidationProvider, ValidationObserver  
  },
   directives: {
    "b-modal": VBModal,
    Ripple,
  },
  name:"utilizatoricopiazadrepturi",
  data() {
    return {
        required,
        password,
        email,
        confirmed,
        min,
        rutainapoiLocal:this.rutainapoi,
        activeCopyLocal:false,
        activeActionLocal:this.activeAction,
        editVarLocal:this.editVar,
        nextTodoId: 2,
        modelName:"utilizatori",
        showLoading:false,
       
      }
  },
 
  watch: {
      activeCopy(){
         
         this.activeCopyLocal=this.activeCopy
       },
       activeCopyLocal(){
            if (this.activeCopyLocal==false){
              this.$emit('closed')
            }
            
       },
      activeAction(){
        this.activeActionLocal=this.activeAction
      },
      editVar(){
        this.editVarLocal=this.editVar
      }
  },

  methods: {
    modificaTo(){
     
     
    },
    initTrHeight() {
      this.trSetHeight(null)
      this.$nextTick(() => {
        if(this.$refs.form){
        this.trSetHeight(this.$refs.form.scrollHeight)
        }
      })
    },
   
    
     handleOk(bvModalEvt){
      bvModalEvt.preventDefault()
        this.$refs.simpleRules.validate().then(success => {
        
        if (success) {
            
              this.copyAction()
            
          }
      })
    },
    copyAction(){
        
        
          this.showLoading=true
          const payLoad=this.editVarLocal 
          payLoad.requestType="post"
          payLoad.requestUrl="/utilizatori/copy"
                   
                            this.activeCopyLocal=false
          this.$store.dispatch("app/api_Request",payLoad)
          .then(response=>{
                           
                           this.$bvToast.toast("Copiere efectuată cu succes!", {
                                                                        title: "Copiere efectuată cu succes!",
                                                                        variant:"success",
                                                                        solid: false,
                                                                        appendToast: true,
                                                                        autoHideDelay: 3000,
                                                                        toaster: "b-toaster-bottom-right",
                                                                      }) 
                           this.showLoading=false
                            this.$emit("closed")
                          
          
          })
         
      
      },
  
   
    aevClosed(){
        this.activeCopyLocal=false
        this.editVarLocal={ to:[],
                              from:"",
                              from_id:"",
                                }
        this.activeActionLocal=""
        this.$emit("closed")
        
    },
    
   
     initTrHeight() {
      this.trSetHeight(null)
      this.$nextTick(() => {
        if(this.$refs.form){
        this.trSetHeight(this.$refs.form.scrollHeight)
        }
      })
    },
  },
  mounted() {
    this.initTrHeight()
  },
  destroyed() {
    window.removeEventListener("resize", this.initTrHeight)
  },
  created() {
         
         if(!this.rutainapoi){
          this.rutainapoiLocal=this.modelName
         }
          window.addEventListener("resize", this.initTrHeight)
          
     
    },
}

</script>

