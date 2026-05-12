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
             :title="activeActionLocal+' Valute'"
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
                                          name="Simbol"
                                          rules="required"
                                        >
                                  <div class="form-label-group">      
                                  <b-form-input
                                    :readonly="activeActionLocal=='Vizualizează'"
                                    autocomplete="off"
                                    id="simbol"
                                    v-model="editVarLocal.simbol"
                                    placeholder="Simbol" 
                                    
                                  />
                                  <label for="simbol">Simbol</label>
                                  <small class="text-danger">{{ errors[0] }}</small>
                                   </div>
                                       </validation-provider>
                              
                                    
                            </b-col>
                            
                            <b-col  cols="2">
                                <validation-provider
                                           #default="{ errors }"
                                          name="Denumire"
                                          rules="required"
                                        >
                                  <div class="form-label-group">      
                                  <b-form-input
                                    :readonly="activeActionLocal=='Vizualizează'"
                                    autocomplete="off"
                                    id="denumire"
                                    v-model="editVarLocal.denumire"
                                    placeholder="Denumire" 
                                    
                                  />
                                  <label for="denumire">Denumire</label>
                                  <small class="text-danger">{{ errors[0] }}</small>
                                   </div>
                                       </validation-provider>
                              
                                    
                            </b-col>
                            
                            <b-col  cols="2">
                                <validation-provider
                                           #default="{ errors }"
                                          name="Paritate"
                                          rules="required"
                                        >
                                  <div class="form-label-group">      
                                  <b-form-input
                                    :readonly="activeActionLocal=='Vizualizează'"
                                    autocomplete="off"
                                    id="paritate"
                                    v-model="editVarLocal.paritate"
                                    placeholder="Paritate" 
                                    
                                  />
                                  <label for="paritate">Paritate</label>
                                  <small class="text-danger">{{ errors[0] }}</small>
                                   </div>
                                       </validation-provider>
                              
                                    
                            </b-col>
                            
                            <b-col  cols="2">
                                <validation-provider
                                           #default="{ errors }"
                                          name="Curs"
                                          rules="required"
                                        >
                                  <div class="form-label-group">      
                                  <b-form-input
                                    :readonly="activeActionLocal=='Vizualizează'"
                                    autocomplete="off"
                                    id="curs"
                                    v-model="editVarLocal.curs"
                                    placeholder="Curs" 
                                    
                                  />
                                  <label for="curs">Curs</label>
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
  name:"valuteaev",
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
        modelName:"valute",
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
                                                     data:'',
                                                simbol:'',
                                                denumire:'',
                                                paritate:'',
                                                curs:'',
                                                

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
                                                     data:"",
                                                simbol:"",
                                                denumire:"",
                                                paritate:"",
                                                curs:"",
                                                

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
                                                     data:"",
                                                simbol:"",
                                                denumire:"",
                                                paritate:"",
                                                curs:"",
                                                

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

