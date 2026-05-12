<template>
   <validation-observer ref="simpleRules">
  <div class= "d-flex justify-content-center" >
   <b-modal id="Dianasoftmodelaev"  
             size="xl" 
             no-close-on-backdrop
             centered
             :hide-footer="activeActionLocal=='Vizualizează'"
             ok-variant="success"
             cancel-title="Cancel"              
             ok-title="Save"
             cancel-variant="warning"
             scrollable
             :cancel-disabled="activeActionLocal=='Vizualizează'"
             :ok-disabled="activeActionLocal=='Vizualizează'"
             modal-class="modal-success"
             :title="activeActionLocal+' Documente'"
             v-model="activeEditLocal"
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

            
                <b-row class="d-flex justify-content-center" >
                     
                            <b-col  cols="2">
                              <validation-provider
                                           #default="{ errors }"
                                          name="Agentia"
                                          rules="required"
                                        >
                                      <gestiunepermisa 
                                                    name=".agentia"
                                                   :pastrezvaloare="true"  
                                                   class="w-full"   
                                                  :readonly="activeActionLocal=='Vizualizează'"
                                                  v-model="editVarLocal.agentia">

                                  </gestiunepermisa>
                                   <small class="text-danger">{{ errors[0] }}</small>
                                   </validation-provider>
                                            
                            </b-col>
                            
                            <b-col  cols="2">
                                <validation-provider
                                           #default="{ errors }"
                                          name="Denumire doc"
                                          rules="required"
                                        >
                                  <div class="form-label-group">      
                                  <b-form-input
                                    :readonly="activeActionLocal=='Vizualizează'"
                                    autocomplete="off"
                                    id="denumire_doc"
                                    v-model="editVarLocal.denumire_doc"
                                    placeholder="Denumire doc" 
                                    
                                  />
                                  <label for="denumire_doc">Denumire doc</label>
                                  <small class="text-danger">{{ errors[0] }}</small>
                                   </div>
                                       </validation-provider>
                              
                                    
                            </b-col>
                            
                            <b-col  cols="2">
                                <validation-provider
                                           #default="{ errors }"
                                          name="Tip doc"
                                          rules="required"
                                        >
                                  <div class="form-label-group">      
                                  <b-form-input
                                    :readonly="activeActionLocal=='Vizualizează'"
                                    autocomplete="off"
                                    id="tip_doc"
                                    v-model="editVarLocal.tip_doc"
                                    placeholder="Tip doc" 
                                    
                                  />
                                  <label for="tip_doc">Tip doc</label>
                                  <small class="text-danger">{{ errors[0] }}</small>
                                   </div>
                                       </validation-provider>
                              
                                    
                            </b-col>
                            
                            <b-col  cols="2">
                                <validation-provider
                                           #default="{ errors }"
                                          name="Aplicatie"
                                          rules="required"
                                        >
                                  <div class="form-label-group">      
                                  <b-form-input
                                    :readonly="activeActionLocal=='Vizualizează'"
                                    autocomplete="off"
                                    id="aplicatie"
                                    v-model="editVarLocal.aplicatie"
                                    placeholder="Aplicatie" 
                                    
                                  />
                                  <label for="aplicatie">Aplicatie</label>
                                  <small class="text-danger">{{ errors[0] }}</small>
                                   </div>
                                       </validation-provider>
                              
                                    
                            </b-col>
                            
                            <b-col  cols="2">
                                <validation-provider
                                           #default="{ errors }"
                                          name="Continut"
                                          rules="required"
                                        >
                                  <div class="form-label-group">      
                                  <b-form-input
                                    :readonly="activeActionLocal=='Vizualizează'"
                                    autocomplete="off"
                                    id="continut"
                                    v-model="editVarLocal.continut"
                                    placeholder="Continut" 
                                    
                                  />
                                  <label for="continut">Continut</label>
                                  <small class="text-danger">{{ errors[0] }}</small>
                                   </div>
                                       </validation-provider>
                              
                                    
                            </b-col>
                            
                            <b-col  cols="2">
                              <validation-provider
                                           #default="{ errors }"
                                          name="Data"
                                          rules="required"
                                        >
                                     <datacalendaristica 
                                          :readonly="activeActionLocal=='Vizualizează'"
                                          id="data" 
                                          v-model="editVarLocal.data"
                                          name="data" 
                                          campDisplay="Data"> 
                                      </datacalendaristica>
                                      <small class="text-danger">{{ errors[0] }}</small>
                                       </validation-provider>     
                            </b-col>
                            
                            <b-col  cols="2">
                                <validation-provider
                                           #default="{ errors }"
                                          name="Utilizator"
                                          rules="required"
                                        >
                                  <div class="form-label-group">      
                                  <b-form-input
                                    :readonly="activeActionLocal=='Vizualizează'"
                                    autocomplete="off"
                                    id="utilizator"
                                    v-model="editVarLocal.utilizator"
                                    placeholder="Utilizator" 
                                    
                                  />
                                  <label for="utilizator">Utilizator</label>
                                  <small class="text-danger">{{ errors[0] }}</small>
                                   </div>
                                       </validation-provider>
                              
                                    
                            </b-col>
                            
                            <b-col  cols="2">
                              <validation-provider
                                           #default="{ errors }"
                                          name="Data operarii"
                                          rules="required"
                                        >
                                     <datacalendaristica 
                                          :readonly="activeActionLocal=='Vizualizează'"
                                          id="data_operarii" 
                                          v-model="editVarLocal.data_operarii"
                                          name="data_operarii" 
                                          campDisplay="Data operarii"> 
                                      </datacalendaristica>
                                      <small class="text-danger">{{ errors[0] }}</small>
                                       </validation-provider>     
                            </b-col>
                            
                            <b-col  cols="2">
                                <validation-provider
                                           #default="{ errors }"
                                          name="Printabil"
                                          rules="required"
                                        >
                                  <div class="form-label-group">      
                                  <b-form-input
                                    :readonly="activeActionLocal=='Vizualizează'"
                                    autocomplete="off"
                                    id="printabil"
                                    v-model="editVarLocal.printabil"
                                    placeholder="Printabil" 
                                    
                                  />
                                  <label for="printabil">Printabil</label>
                                  <small class="text-danger">{{ errors[0] }}</small>
                                   </div>
                                       </validation-provider>
                              
                                    
                            </b-col>
                            
                            <b-col  cols="2">
                                <validation-provider
                                           #default="{ errors }"
                                          name="Exportabil"
                                          rules="required"
                                        >
                                  <div class="form-label-group">      
                                  <b-form-input
                                    :readonly="activeActionLocal=='Vizualizează'"
                                    autocomplete="off"
                                    id="exportabil"
                                    v-model="editVarLocal.exportabil"
                                    placeholder="Exportabil" 
                                    
                                  />
                                  <label for="exportabil">Exportabil</label>
                                  <small class="text-danger">{{ errors[0] }}</small>
                                   </div>
                                       </validation-provider>
                              
                                    
                            </b-col>
                            
                    
                </b-row>
                
             <br><br><br><br><br><br><br><br><br><br><br> 
      </form>
    </b-modal>
  </div>
   </validation-observer>
</template>

<script>
import Ripple from "vue-ripple-directive"
import { heightTransition } from "@core/mixins/ui/transition"
import {VBModal} from "bootstrap-vue"
import {  required, email, confirmed, password,min} from "@validations"
import { ValidationProvider, ValidationObserver} from "vee-validate"
export default {
  props: {
        activeEdit:Boolean,
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
  name:"documenteaev",
  data() {
    return {
        required, 
        password,
        email,
        confirmed,
         min,
        rutainapoiLocal:this.rutainapoi,
        activeEditLocal:false,
        activeActionLocal:this.activeAction,
        editVarLocal:this.editVar,
        nextTodoId: 2,
        modelName:"documente",
        showLoading:false,
       
      }
  },
  watch: {
      activeEdit(){
         
         this.activeEditLocal=this.activeEdit
       },
       activeEditLocal(){
            if (this.activeEditLocal==false){
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
       if (this.activeActionLocal=="Adaugă") 
        {
          this.saveAdd()
        }
        if (this.activeActionLocal=="Modifică") 
        {
          this.saveEdit()
        }
         }
      })
    },
    saveAdd(){
        
          this.showLoading=true
          const payLoad=this.editVarLocal
          payLoad.requestType="post"
          payLoad.requestUrl="/"+this.modelName+"/store"
         
          this.$store.dispatch("app/api_Request",payLoad)
                    .then(response=>{
                           
                            this.editVarLocal={  
                                                     agentia:'',
                                                denumire_doc:'',
                                                tip_doc:'',
                                                aplicatie:'',
                                                continut:'',
                                                data:'',
                                                utilizator:'',
                                                data_operarii:'',
                                                printabil:'',
                                                exportabil:'',
                                                

                                        }
                             this.$bvToast.toast("Salvare efectuata cu success!", 
                                                 {
                                                    title: `Salvare cu succes! `,
                                                    variant:"success",
                                                    solid: false,
                                                    appendToast: true,
                                                    autoHideDelay: 3000,
                                                    toaster: "b-toaster-bottom-right",
                                                                      }) 
                            this.showLoading=false
                             this.activeEditLocal=false
                           this.activeActionLocal=""
                           this.$emit("stored",response)              
                            this.$emit("closed")
          
                       })
                      .catch(error => {
                        this.showLoading=true
                      })
         
    },
   
    aevClosed(){
       this.idselectat=null
      this.selectedID=""
        this.activeEditLocal=false
        this.editVarLocal={  
                                                     agentia:"",
                                                denumire_doc:"",
                                                tip_doc:"",
                                                aplicatie:"",
                                                continut:"",
                                                data:"",
                                                utilizator:"",
                                                data_operarii:"",
                                                printabil:"",
                                                exportabil:"",
                                                

                                        }
        this.activeActionLocal=""
        this.$emit("closed")
        
    },
    
    saveEdit(){
               
                  this.showLoading=true
                  const payLoad=this.editVarLocal 
                  payLoad.requestType="post"
                  payLoad.requestUrl="/"+this.modelName+"/edit/"+this.editVarLocal.id
                  
                  this.$store.dispatch("app/api_Request",payLoad)
                              .then(response=>{
                                               this.selectedID=""
                                               
                                               this.editVarLocal={  
                                                     agentia:"",
                                                denumire_doc:"",
                                                tip_doc:"",
                                                aplicatie:"",
                                                continut:"",
                                                data:"",
                                                utilizator:"",
                                                data_operarii:"",
                                                printabil:"",
                                                exportabil:"",
                                                

                                        }
                                         this.$bvToast.toast("Modificare efectuata cu success!", {
                                                                        title: "Modificare cu succes! ",
                                                                        variant:"success",
                                                                        solid: false,
                                                                        appendToast: true,
                                                                        autoHideDelay: 3000,
                                                                        toaster: "b-toaster-bottom-right",
                                                                      }) 
                                               this.showLoading=false
                                                this.activeEditLocal=false
                                               this.activeActionLocal=""
                                               this.$emit("stored","") 
                                               this.$emit("closed")
          
                               })
                              .catch(error => {

                               this.showLoading=false
                              })
               
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

