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
             :title="activeActionLocal+' Ordinedeblocareanaf'"
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
                     
                            <b-col  cols="1">
                                <validation-provider
                                           #default="{ errors }"
                                          name="Nr ordin"
                                          rules=""
                                        >
                                  <div class="form-label-group">      
                                  <b-form-input
                                    :readonly="activeActionLocal=='Vizualizează'"
                                    size="sm"
                                    autocomplete="off"
                                    id="nr_ordin"
                                    v-model="editVarLocal.nr_ordin"
                                    placeholder="Nr ordin" 
                                    
                                  />
                                  <label for="nr_ordin">Nr ordin</label>
                                  <small class="text-danger">{{ errors[0] }}</small>
                                   </div>
                                       </validation-provider>
                              
                                    
                            </b-col>
                            
                            <b-col  cols="2">
                              <validation-provider
                                           #default="{ errors }"
                                          name="Data ordin"
                                          rules=""
                                        >
                                     <datacalendaristica 
                                          :readonly="activeActionLocal=='Vizualizează'"
                                          id="data_ordin" 
                                          v-model="editVarLocal.data_ordin"
                                          name="data_ordin" 
                                          campDisplay="Data ordin"> 
                                      </datacalendaristica>
                                      <small class="text-danger">{{ errors[0] }}</small>
                                       </validation-provider>     
                            </b-col>
                            
                          
                            
                            <b-col  cols="2">
                                <validation-provider
                                           #default="{ errors }"
                                          name="Ordin de revocare"
                                          rules=""
                                        >
                                  <div class="form-label-group">      
                                  <b-form-input
                                    :readonly="activeActionLocal=='Vizualizează'"
                                    size="sm"
                                    autocomplete="off"
                                    id="ordin_de_revocare"
                                    v-model="editVarLocal.ordin_de_revocare"
                                    placeholder="Ordin de revocare" 
                                    
                                  />
                                  <label for="ordin_de_revocare">Ordin de revocare</label>
                                  <small class="text-danger">{{ errors[0] }}</small>
                                   </div>
                                       </validation-provider>
                              
                                    
                            </b-col>
                            
                            <b-col  cols="2">
                              <validation-provider
                                           #default="{ errors }"
                                          name="Data revocarii"
                                          rules=""
                                        >
                                     <datacalendaristica 
                                          :readonly="activeActionLocal=='Vizualizează'"
                                          id="data_revocarii" 
                                          v-model="editVarLocal.data_revocarii"
                                          name="data_revocarii" 
                                          campDisplay="Data revocarii"> 
                                      </datacalendaristica>
                                      <small class="text-danger">{{ errors[0] }}</small>
                                       </validation-provider>     
                            </b-col>
                            
                            <b-col  cols="2">
                                <validation-provider
                                           #default="{ errors }"
                                          name="Institutia"
                                          rules=""
                                        >
                                  <div class="form-label-group">      
                                  <b-form-input
                                    :readonly="activeActionLocal=='Vizualizează'"
                                    size="sm"
                                    autocomplete="off"
                                    id="institutia"
                                    v-model="editVarLocal.institutia"
                                    placeholder="Institutia" 
                                    
                                  />
                                  <label for="institutia">Institutia</label>
                                  <small class="text-danger">{{ errors[0] }}</small>
                                   </div>
                                       </validation-provider>
                              
                                    
                            </b-col>
                        </b-row>
                         <b-row class="d-flex justify-content-center" >
                         
                      <b-col  cols="2">
                                <validation-provider
                                           #default="{ errors }"
                                          name="Suspect"
                                          rules=""
                                        >
                                  <div class="form-label-group">      
                                  <b-form-textarea
                                        id="suspect"
                                         v-model="editVarLocal.suspect"
                                        rows="5"
                                        placeholder="Suspect"
                                      />
                                  <label for="suspect">Suspect</label>
                                  <small class="text-danger">{{ errors[0] }}</small>
                                   </div>
                                       </validation-provider>
                              
                                    
                            </b-col>
                            
                            <b-col  cols="5">
                                <validation-provider
                                           #default="{ errors }"
                                          name="Date de identificare"
                                          rules=""
                                        >
                                  <div class="form-label-group">      
                                <b-form-textarea
                                        id="date_de_identificare"
                                         v-model="editVarLocal.date_de_identificare"
                                        rows="5"
                                        placeholder="Date de identificare"
                                      />
                                  <label for="date_de_identificare">Date de identificare</label>
                                  <small class="text-danger">{{ errors[0] }}</small>
                                   </div>
                                       </validation-provider>
                              
                                    
                            </b-col>
                            
                            <b-col  cols="5">
                                <validation-provider
                                           #default="{ errors }"
                                          name="Bunuri blocate"
                                          rules=""
                                        >
                                  <div class="form-label-group">      
                                  <b-form-textarea
                                        id="bunuri_blocate"
                                         v-model="editVarLocal.bunuri_blocate"
                                        rows="5"
                                        placeholder="Bunuri blocate"
                                      />
                                  <label for="bunuri_blocate">Bunuri blocate</label>
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
  name:"ordinedeblocareanafaev",
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
        modelName:"ordinedeblocareanaf",
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
                                                     nr_ordin:'',
                                                data_ordin:'',
                                                suspect:'',
                                                date_de_identificare:'',
                                                bunuri_blocate:'',
                                                ordin_de_revocare:'',
                                                data_revocarii:'',
                                                institutia:'',
                                                

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
                               
                                    this.showLoading=false
                                    this.$bvToast.toast(error.data.message, 
                                                             {
                                                                title: `Eroare! `,
                                                                variant:"danger",
                                                                solid: true,
                                                                appendToast: false,
                                                                noAutoHide:true,
                                                                toaster: "b-toaster-top-right",
                                                                                  }) 
                                                  
                                    })
         
    },
   
    aevClosed(){
       this.idselectat=null
      this.selectedID=""
        this.activeEditLocal=false
        this.editVarLocal={  
                                                     nr_ordin:"",
                                                data_ordin:"",
                                                suspect:"",
                                                date_de_identificare:"",
                                                bunuri_blocate:"",
                                                ordin_de_revocare:"",
                                                data_revocarii:"",
                                                institutia:"",
                                                

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
                                                     nr_ordin:"",
                                                data_ordin:"",
                                                suspect:"",
                                                date_de_identificare:"",
                                                bunuri_blocate:"",
                                                ordin_de_revocare:"",
                                                data_revocarii:"",
                                                institutia:"",
                                                

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
                                    this.$bvToast.toast(error.data.message, 
                                                             {
                                                                title: `Eroare! `,
                                                                variant:"danger",
                                                                solid: true,
                                                                appendToast: false,
                                                                noAutoHide:true,
                                                                toaster: "b-toaster-top-right",
                                                                                  }) 
                                                  
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

