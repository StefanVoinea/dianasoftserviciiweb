<template>
   <validation-observer ref="simpleRules">
  <div class= "d-flex justify-content-center" >
   <b-modal id="Dianasoftmodelaev"  
             size="xl" 
             no-close-on-backdrop
             centered
             ok-variant="success"
             cancel-title="Cancel"              
             ok-title="Save"
             cancel-variant="warning"
             scrollable
             :cancel-disabled="activeActionLocal=='Vizualizează'"
             :ok-disabled="activeActionLocal=='Vizualizează'"
             modal-class="modal-success"
             :title="activeActionLocal+' Jurnal SMS'"
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
                                          name="Nr contract"
                                          rules="required"
                                        >
                                  <div class="form-label-group">      
                                  <b-form-input
                                    :readonly="activeActionLocal=='Vizualizează'"
                                    autocomplete="off"
                                    id="nr_contract"
                                    v-model="editVarLocal.nr_contract"
                                    placeholder="Nr contract" 
                                    
                                  />
                                  <label for="nr_contract">Nr contract</label>
                                  <small class="text-danger">{{ errors[0] }}</small>
                                   </div>
                                       </validation-provider>
                              
                                    
                            </b-col>
                            
                            <b-col  cols="2">
                                <validation-provider
                                           #default="{ errors }"
                                          name="Telefon"
                                          rules="required"
                                        >
                                  <div class="form-label-group">      
                                  <b-form-input
                                    :readonly="activeActionLocal=='Vizualizează'"
                                    autocomplete="off"
                                    id="telefon"
                                    v-model="editVarLocal.telefon"
                                    placeholder="Telefon" 
                                    
                                  />
                                  <label for="telefon">Telefon</label>
                                  <small class="text-danger">{{ errors[0] }}</small>
                                   </div>
                                       </validation-provider>
                              
                                    
                            </b-col>
                            
                            <b-col  cols="2">
                                     <div class="form-label-group">
                                    
                                    <validation-provider
                                           #default="{ errors }"
                                          name="Mesaj"
                                          rules="required"
                                        >
                                    
                                      <b-form-textarea
                                        id=".mesaj"
                                         v-model="editVarLocal.mesaj"
                                        rows="3"
                                        placeholder=".Mesaj"
                                      />
                                       <small class="text-danger">{{ errors[0] }}</small>
                                       </validation-provider>
                                      <label for="label-.mesaj">.Mesaj</label>
                                    </div>     
                            </b-col>
                            
                            <b-col  cols="2">
                                <validation-provider
                                           #default="{ errors }"
                                          name="Status"
                                          rules="required"
                                        >
                                  <div class="form-label-group">      
                                  <b-form-input
                                    :readonly="activeActionLocal=='Vizualizează'"
                                    autocomplete="off"
                                    id="status"
                                    v-model="editVarLocal.status"
                                    placeholder="Status" 
                                    
                                  />
                                  <label for="status">Status</label>
                                  <small class="text-danger">{{ errors[0] }}</small>
                                   </div>
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
                                
                               
                                  <datasiora
                                    :readonly="activeActionLocal=='Vizualizează'"
                                    name="Data operarii"
                                    v-model="editVarLocal.data_operare"
                                    campDisplay="Data operarii" 
                                    
                                  />
                                 
                                 
                              
                                    
                            </b-col>
                            
                            <b-col  cols="2">
                                  
                                  <datasiora
                                    :readonly="activeActionLocal=='Vizualizează'"
                                    name="Data transmitere"
                                    v-model="editVarLocal.data_transmitere"
                                    campDisplay="Data transmitere" 
                                    
                                  />
                                
                                    
                            </b-col>
                            
                            <b-col  cols="2">
                                <validation-provider
                                           #default="{ errors }"
                                          name="Catre"
                                          rules="required"
                                        >
                                  <div class="form-label-group">      
                                  <b-form-input
                                    :readonly="activeActionLocal=='Vizualizează'"
                                    autocomplete="off"
                                    id="catre"
                                    v-model="editVarLocal.catre"
                                    placeholder="Catre" 
                                    
                                  />
                                  <label for="catre">Catre</label>
                                  <small class="text-danger">{{ errors[0] }}</small>
                                   </div>
                                       </validation-provider>
                              
                                    
                            </b-col>
                            
                            <b-col  cols="2">
                                <validation-provider
                                           #default="{ errors }"
                                          name="ID Site"
                                          rules="required"
                                        >
                                  <div class="form-label-group">      
                                  <b-form-input
                                    :readonly="activeActionLocal=='Vizualizează'"
                                    autocomplete="off"
                                    id="id_site"
                                    v-model="editVarLocal.id_site"
                                    placeholder="ID Site" 
                                    
                                  />
                                  <label for="id_site">ID Site</label>
                                  <small class="text-danger">{{ errors[0] }}</small>
                                   </div>
                                       </validation-provider>
                              
                                    
                            </b-col>
                            
                            <b-col  cols="2">
                              <datasiora
                                    :readonly="activeActionLocal=='Vizualizează'"
                                    name="Data verificare status"
                                    v-model="editVarLocal.data_verificare_status"
                                    campDisplay="Data verificare status" />
                              
                                    
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
  name:"jurnalsmsaev",
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
        modelName:"jurnalsms",
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
                                                     nr_contract:'',
                                                telefon:'',
                                                mesaj:'',
                                                status:'',
                                                utilizator:'',
                                                data_operare:'',
                                                data_transmitere:'',
                                                catre:'',
                                                id_site:'',
                                                data_verificare_status:'',
                                                

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
                                                     nr_contract:"",
                                                telefon:"",
                                                mesaj:"",
                                                status:"",
                                                utilizator:"",
                                                data_operare:"",
                                                data_transmitere:"",
                                                catre:"",
                                                id_site:"",
                                                data_verificare_status:"",
                                                

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
                                                     nr_contract:"",
                                                telefon:"",
                                                mesaj:"",
                                                status:"",
                                                utilizator:"",
                                                data_operare:"",
                                                data_transmitere:"",
                                                catre:"",
                                                id_site:"",
                                                data_verificare_status:"",
                                                

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

