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
             :cancel-disabled="activeActionLocal=='Vizualizează'"
             :ok-disabled="activeActionLocal=='Vizualizează'"
             
             modal-class="modal-success"
             :title="activeActionLocal+' Utilizatori'"
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
              <b-overlay
                  :show="showLoading"
                  rounded="sm"
                  no-fade
                  variant="primary"
                  opacity="0.25"
                  blur="2px"
                >
     <form  ref="form"  @submit.stop.prevent="handleSubmit" >
             <br>

            
                <b-row class="d-flex justify-content-center">
                     
                            <b-col  cols="8" >     
                           
                            <div class="form-label-group">
                             <validation-provider
                                    #default="{ errors }"
                                    name="Nume"
                                    rules="required"
                                  >
                                  <b-form-input
                                    :readonly="activeActionLocal=='Vizualizează'"
                                    autocomplete="off"
                                    id="name"
                                    v-model="editVarLocal.name"
                                    placeholder="Nume" 
                                    
                                  />
                                 <small class="text-danger">{{ errors[0] }}</small>
                                </validation-provider>
                                  <label for="name">Nume</label>
                                </div> 
                            </b-col>
                           </b-row >
                            <b-row class="d-flex justify-content-center"> 
                            <b-col  cols="8">      
                         <validation-provider
                                    #default="{ errors }"
                                    name="Sex"
                                    rules="required"
                                  >
                            
                                     
                              <dropdowncuoptiuni 
                                name="sex" 
                                :readonly="activeAction=='Vizualizează'" 
                                 v-model="editVarLocal.sex" 
                                campDisplay="Sex"
                                field_name="Sex"
                                limitToList="true"
                                > 
                          </dropdowncuoptiuni>     
                            <small class="text-danger">{{ errors[0] }}</small>
                                </validation-provider>
                            </b-col>
                            
                          
                            
                    
                </b-row>
                           <b-row class="d-flex justify-content-center"> 
                            <b-col  cols="8">     
                          
                            <div class="form-label-group">
                            <validation-provider
                                    #default="{ errors }"
                                    name="Email"
                                    rules="required|email"
                                  >
                                  <b-form-input
                                    :readonly="activeActionLocal=='Vizualizează'"
                                    autocomplete="off"
                                    id="email"
                                    v-model="editVarLocal.email"
                                    placeholder="Email" 
                                    
                                  />
                                   <small class="text-danger">{{ errors[0] }}</small>
                                </validation-provider>
                                  <label for="email">Email</label>
                               
                                 </div>
                            </b-col>
                            </b-row >
                           <b-row class="d-flex justify-content-center"> 
                            <b-col  cols="8">         
                            
                            <div class="form-label-group">
                             
                             <validation-provider
                                    #default="{ errors }"
                                    name="Parola"
                                    rules="required|min:6"
                                  >
                                  <b-input-group  class="input-group-merge">
                                  <b-form-input
                                    :readonly="activeActionLocal=='Vizualizează'"
                                    autocomplete="off"
                                    id="password"
                                    :type="passwordFieldType"
                                    v-model="editVarLocal.password"
                                    placeholder="Parola" 
                                    
                                  />
                                  <b-input-group-append is-text>
                                    <feather-icon
                                      class="cursor-pointer"
                                      :icon="passwordToggleIcon"
                                      @click="togglePasswordVisibility"
                                    />
                                  </b-input-group-append>
                                  </b-input-group>
                                  <label for="password">Parola</label>
                                   <small class="text-danger">{{ errors[0] }}</small>
                                </validation-provider>
                                   
                            </div>
                            </b-col>
                           </b-row >
                           <b-row class="d-flex justify-content-center"> 
                            <b-col  cols="8">          
                         
                            <div class="form-label-group">
                             <validation-provider
                                    #default="{ errors }"
                                    name="Telefon"
                                    rules="required"
                                  >
                                  <b-form-input
                                    :readonly="activeActionLocal=='Vizualizează'"
                                    id="telefon"
                                    autocomplete="off"
                                    v-model="editVarLocal.telefon"
                                    placeholder="Telefon" 
                                    
                                  />
                                    <small class="text-danger">{{ errors[0] }}</small>
                                </validation-provider>
                                  <label for="telefon">Telefon</label>
                            </div>
                                 
                            </b-col>
                            
                          </b-row >
                           <b-row class="d-flex justify-content-center"> 
                            <b-col  cols="8">     
                         
                            <div class="form-label-group">
                             <validation-provider
                                    #default="{ errors }"
                                    name="Functia"
                                    rules="required"
                                  >
                                  <b-form-input
                                    :readonly="activeActionLocal=='Vizualizează'"
                                    id="functia"
                                    autocomplete="off"
                                    v-model="editVarLocal.functia"
                                    placeholder="Functia" 
                                    
                                  />
                                  <small class="text-danger">{{ errors[0] }}</small>
                                </validation-provider>
                                  <label for="functia">Functia</label>
                            
                                 </div>
                            </b-col>
                            </b-row >
                           
                           <b-row class="d-flex justify-content-center"> 
                            <b-col  cols="8">     
                            <div class="form-label-group">
                                  <b-form-input
                                    autocomplete="off"
                                    :readonly="activeActionLocal=='Vizualizează'"
                                    id="program_de_lucru"
                                    v-model="editVarLocal.program_de_lucru"
                                    placeholder="Program de lucru" 
                                    
                                  />
                                  <label for="program_de_lucru">Program de lucru</label>
                             </div>
                                 
                            </b-col>
                            
                           </b-row >
                           <b-row class="d-flex justify-content-center"> 
                            <b-col  cols="8">        
                         
                            
                                 <validation-provider
                                    #default="{ errors }"
                                    name="Departament"
                                    rules="required"
                                  >     
                              <dropdowncuoptiuni 
                                name="departament" 
                                :readonly="activeAction=='Vizualizează'" 
                                 v-model="editVarLocal.departament" 
                                campDisplay="Departament"
                                field_name="Departament"
                                limitToList="true"
                                > 
                          </dropdowncuoptiuni>     
                         <small class="text-danger">{{ errors[0] }}</small>
                                </validation-provider>
                            </b-col>
                           </b-row >
                          
                
              <br><br><br><br><br><br><br>
      </form>
      </b-overlay>
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
  name:"utilizatoriaev",
  data() {
    return {
        required,
        password,
        email,
        confirmed,
         min,
         passwordFieldType:"password",
        rutainapoiLocal:this.rutainapoi,
        activeEditLocal:false,
        activeActionLocal:this.activeAction,
        editVarLocal:this.editVar,
        nextTodoId: 2,
        modelName:"utilizatori",
        showLoading:false,
       
      }
  },
  computed:{
passwordToggleIcon() {
      return this.passwordFieldType === 'password' ? 'EyeIcon' : 'EyeOffIcon'
    },
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
     togglePasswordVisibility() {
      this.passwordFieldType = this.passwordFieldType === 'password' ? 'text' : 'password'
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
                                                     name:'',
                                                user_type:'',
                                                email:'',
                                                password:'',
                                                telefon:'',
                                                blocat:'',
                                                functia:'',
                                                status:'',
                                                link_poza:'',
                                                program_de_lucru:'',
                                                data_expirare_parola:'',
                                                departament:'',
                                                sex:'',
                                                grup:'',
                                                

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
                           this.$emit("stored","")     
                            this.$emit("closed")
          
                       })
                      .catch(error => {
                        this.showLoading=true
                      })
         
    },
   
    aevClosed(){
        this.activeEditLocal=false
        this.editVarLocal={  
                                                     name:"",
                                                user_type:"",
                                                email:"",
                                                password:"",
                                                telefon:"",
                                                blocat:"",
                                                functia:"",
                                                status:"",
                                                link_poza:"",
                                                program_de_lucru:"",
                                                data_expirare_parola:"",
                                                departament:"",
                                                sex:"",
                                                grup:"",
                                                

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
                                               
                                               
                                               this.editVarLocal={  
                                                     name:"",
                                                user_type:"",
                                                email:"",
                                                password:"",
                                                telefon:"",
                                                blocat:"",
                                                functia:"",
                                                status:"",
                                                link_poza:"",
                                                program_de_lucru:"",
                                                data_expirare_parola:"",
                                                departament:"",
                                                sex:"",
                                                grup:"",
                                                

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
                                        this.$emit("stored","") 
                                               this.activeEditLocal=false
                                               this.activeActionLocal=""
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

