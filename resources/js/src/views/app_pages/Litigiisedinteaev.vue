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
             :title="activeActionLocal+' Sedinte litigii'"
             v-model="activeEditLocal"
             @ok="handleOk"
             @cancel="aevClosed"
             >
<form  ref="form"  @submit.stop.prevent="handleSubmit" >
             <br>

            
                <b-row class="d-flex justify-content-center" >
                     
                            <b-col  cols="2">
                                <validation-provider
                                           #default="{ errors }"
                                          name="IDLitigiu"
                                          rules=""
                                        >
                                  <div class="form-label-group">      
                                  <b-form-input
                                    :readonly="activeActionLocal=='Vizualizează'"
                                    size="sm"
                                    autocomplete="off"
                                    id="litigiu_id"
                                    v-model="editVarLocal.litigiu_id"
                                    placeholder="IDLitigiu" 
                                    
                                  />
                                  <label for="litigiu_id">IDLitigiu</label>
                                  <small class="text-danger">{{ errors[0] }}</small>
                                   </div>
                                       </validation-provider>
                              
                                    
                            </b-col>
                            
                            <b-col  cols="2">
                                <validation-provider
                                           #default="{ errors }"
                                          name="Complet"
                                          rules=""
                                        >
                                  <div class="form-label-group">      
                                  <b-form-input
                                    :readonly="activeActionLocal=='Vizualizează'"
                                    size="sm"
                                    autocomplete="off"
                                    id="complet"
                                    v-model="editVarLocal.complet"
                                    placeholder="Complet" 
                                    
                                  />
                                  <label for="complet">Complet</label>
                                  <small class="text-danger">{{ errors[0] }}</small>
                                   </div>
                                       </validation-provider>
                              
                                    
                            </b-col>
                            
                            <b-col  cols="2">
                              <validation-provider
                                           #default="{ errors }"
                                          name="Data sedinta"
                                          rules=""
                                        >
                                     <datacalendaristica 
                                          :readonly="activeActionLocal=='Vizualizează'"
                                          id="data_sedinta" 
                                          v-model="editVarLocal.data_sedinta"
                                          name="data_sedinta" 
                                          campDisplay="Data sedinta"> 
                                      </datacalendaristica>
                                      <small class="text-danger">{{ errors[0] }}</small>
                                       </validation-provider>     
                            </b-col>
                            
                            <b-col  cols="2">
                                <validation-provider
                                           #default="{ errors }"
                                          name="Ora sedinta"
                                          rules=""
                                        >
                                  <div class="form-label-group">      
                                  <b-form-input
                                    :readonly="activeActionLocal=='Vizualizează'"
                                    size="sm"
                                    autocomplete="off"
                                    id="ora_sedinta"
                                    v-model="editVarLocal.ora_sedinta"
                                    placeholder="Ora sedinta" 
                                    
                                  />
                                  <label for="ora_sedinta">Ora sedinta</label>
                                  <small class="text-danger">{{ errors[0] }}</small>
                                   </div>
                                       </validation-provider>
                              
                                    
                            </b-col>
                            
                            <b-col  cols="2">
                                <validation-provider
                                           #default="{ errors }"
                                          name="Solutie"
                                          rules=""
                                        >
                                  <div class="form-label-group">      
                                  <b-form-input
                                    :readonly="activeActionLocal=='Vizualizează'"
                                    size="sm"
                                    autocomplete="off"
                                    id="solutie"
                                    v-model="editVarLocal.solutie"
                                    placeholder="Solutie" 
                                    
                                  />
                                  <label for="solutie">Solutie</label>
                                  <small class="text-danger">{{ errors[0] }}</small>
                                   </div>
                                       </validation-provider>
                              
                                    
                            </b-col>
                            
                            <b-col  cols="2">
                                     <div class="form-label-group">
                                    
                                    <validation-provider
                                           #default="{ errors }"
                                          name="Solutie sumar"
                                          rules=""
                                        >
                                    
                                      <b-form-textarea
                                        id=".solutie_sumar"
                                         v-model="editVarLocal.solutie_sumar"
                                        rows="3"
                                        placeholder=".Solutie sumar"
                                      />
                                       <small class="text-danger">{{ errors[0] }}</small>
                                       </validation-provider>
                                      <label for="label-.solutie_sumar">.Solutie sumar</label>
                                    </div>     
                            </b-col>
                            
                            <b-col  cols="2">
                              <validation-provider
                                           #default="{ errors }"
                                          name="Data pronuntare"
                                          rules=""
                                        >
                                     <datasiora 
                                          :readonly="activeActionLocal=='Vizualizează'"
                                          id="data_pronuntare" 
                                          v-model="editVarLocal.data_pronuntare"
                                          name="data_pronuntare" 
                                          campDisplay="Data pronuntare"> 
                                      </datasiora>
                                       <small class="text-danger">{{ errors[0] }}</small>
                                       </validation-provider>     
                            </b-col>
                            
                            <b-col  cols="2">
                                <validation-provider
                                           #default="{ errors }"
                                          name="Document sedinta"
                                          rules=""
                                        >
                                  <div class="form-label-group">      
                                  <b-form-input
                                    :readonly="activeActionLocal=='Vizualizează'"
                                    size="sm"
                                    autocomplete="off"
                                    id="document_sedinta"
                                    v-model="editVarLocal.document_sedinta"
                                    placeholder="Document sedinta" 
                                    
                                  />
                                  <label for="document_sedinta">Document sedinta</label>
                                  <small class="text-danger">{{ errors[0] }}</small>
                                   </div>
                                       </validation-provider>
                              
                                    
                            </b-col>
                            
                            <b-col  cols="2">
                                <validation-provider
                                           #default="{ errors }"
                                          name="Numar document"
                                          rules=""
                                        >
                                  <div class="form-label-group">      
                                  <b-form-input
                                    :readonly="activeActionLocal=='Vizualizează'"
                                    size="sm"
                                    autocomplete="off"
                                    id="numar_document"
                                    v-model="editVarLocal.numar_document"
                                    placeholder="Numar document" 
                                    
                                  />
                                  <label for="numar_document">Numar document</label>
                                  <small class="text-danger">{{ errors[0] }}</small>
                                   </div>
                                       </validation-provider>
                              
                                    
                            </b-col>
                            
                            <b-col  cols="2">
                              <validation-provider
                                           #default="{ errors }"
                                          name="Data document"
                                          rules=""
                                        >
                                     <datasiora 
                                          :readonly="activeActionLocal=='Vizualizează'"
                                          id="data_document" 
                                          v-model="editVarLocal.data_document"
                                          name="data_document" 
                                          campDisplay="Data document"> 
                                      </datasiora>
                                       <small class="text-danger">{{ errors[0] }}</small>
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
  name:"litigiisedinteaev",
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
        modelName:"litigiisedinte",
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
                                                     litigiu_id:'',
                                                complet:'',
                                                data_sedinta:'',
                                                ora_sedinta:'',
                                                solutie:'',
                                                solutie_sumar:'',
                                                data_pronuntare:'',
                                                document_sedinta:'',
                                                numar_document:'',
                                                data_document:'',
                                                

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
                                                     litigiu_id:"",
                                                complet:"",
                                                data_sedinta:"",
                                                ora_sedinta:"",
                                                solutie:"",
                                                solutie_sumar:"",
                                                data_pronuntare:"",
                                                document_sedinta:"",
                                                numar_document:"",
                                                data_document:"",
                                                

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
                                                     litigiu_id:"",
                                                complet:"",
                                                data_sedinta:"",
                                                ora_sedinta:"",
                                                solutie:"",
                                                solutie_sumar:"",
                                                data_pronuntare:"",
                                                document_sedinta:"",
                                                numar_document:"",
                                                data_document:"",
                                                

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

