<template>
   <validation-observer ref="simpleRules">
  <div class= "d-flex justify-content-center" >
   <b-modal id="Dianasoftmodelaev"  
             size="xl" 
             no-close-on-backdrop
             centered
             :hide-footer="activeActionLocal=='Vizualizează'"
             ok-variant="success"
             cancel-title="Renunt"              
             ok-title="Salvez"
             cancel-variant="warning"
             scrollable
             :cancel-disabled="activeActionLocal=='Vizualizează'"
             :ok-disabled="activeActionLocal=='Vizualizează'"
             modal-class="modal-success"
             :title="activeActionLocal+' Monitorizare documente agentii'"
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
                               <gestiunepermisa

                                              @change="modificaGestiune"
                                              name="gestiune"
                                              v-model="editVarLocal.gestiune"
                                              :readonly="activeActionLocal=='Vizualizează'" 
                                              :pastrezvaloare="true"  
                                              class="w-full"
                                              >
                              </gestiunepermisa>
                              
                                    
                            </b-col>
                            
                            <b-col  cols="4">
                              <identificarecontract 
                                              name="nume"
                                              colCaut="nume" 
                                              camp="nume" 
                                              campDisplay="Nume"  
                                              ruta="contract"
                                              @change="modificaContract"
                                              :pastrezvaloare="false"
                                              limitToList="true"
                                              v-model="editVarLocal.contract"
                                              class="w-full"/>
                               
                            </b-col>
                            
                           
                            
                            <b-col  cols="4">
                                     <validation-provider
                                           #default="{ errors }"
                                          name="Tip document"
                                          rules=""
                                        >
                              <dropdowncuoptiuni 
                                name="tip_document" 
                                :readonly="activeAction=='Vizualizează'" 
                                 v-model="editVarLocal.tip_document" 
                                campDisplay="Tip document"
                                field_name="Tip document monitorizare"
                                limitToList="true"
                                > 
                          </dropdowncuoptiuni>
                           <small class="text-danger">{{ errors[0] }}</small>
                                       </validation-provider>     
                            </b-col>
                            
                            
                    
                </b-row>
                <b-row class="d-flex justify-content-center" >
                  <b-col  cols="2">
                              <validation-provider
                                           #default="{ errors }"
                                          name="Data incasarii"
                                          rules=""
                                        >
                                     <datacalendaristica 
                                          :readonly="activeActionLocal=='Vizualizează'"
                                          id="data_incasarii" 
                                          v-model="editVarLocal.data_incasarii"
                                          name="data_incasarii" 
                                          campDisplay="Data incasarii"/> 
                                      
                                      <small class="text-danger">{{ errors[0] }}</small>
                                       </validation-provider>     
                            </b-col>
                          <b-col     cols="2">
                                   <validation-provider
                                           #default="{ errors }"
                                          name="Suma_incasata"
                                          rules=""
                                        >
                                  <div class="form-label-group">      
                                  <b-form-input
                                    :readonly="activeActionLocal=='Vizualizează'"
                                    size="sm"
                                    autocomplete="off"
                                    id="suma_incasata"
                                    v-model="editVarLocal.suma_incasata"
                                    placeholder="Suma incasata" 
                                    
                                  />
                                  <label for="suma incasata">Suma incasata</label>
                                  <small class="text-danger">{{ errors[0] }}</small>
                                   </div>
                                       </validation-provider>
                             </b-col>
                            
                            <b-col vs-type="flex flex-row"  vs-justify="space-around" vs-align="bottom" cols="2">
                             <validation-provider
                                    #default="{ errors }"
                                     name="Tip valuta"
                                    rules=""
                                  >   
                              <dropdowncuoptiuni 
                                name="tip_valuta" 
                                :readonly="activeAction=='Vizualizează'" 
                                 v-model="editVarLocal.tip_valuta" 
                                campDisplay="Tip valuta"
                                field_name="Tip valuta"
                                limitToList="true"
                                > 
                          </dropdowncuoptiuni>
                           <small class="text-danger">{{ errors[0] }}</small>
                                </validation-provider>     
                              
                          
                       </b-col> 
                            
                            <b-col  cols="2">
                               <selectoneserver 
                                        name="banca"
                                        @change="modificaBanca"
                                        :readonly="activeActionLocal=='Vizualizează'" 
                                        class="w-full"
                                        v-model="editVarLocal.banca" 
                                        colCaut="banca" 
                                        camp="banca" 
                                        campDisplay="Banca"  
                                        ruta="bancidelegatii"
                                        limitToList="true"/>
                              
                                    
                            </b-col>
                            <b-col vs-type="flex flex-row"  vs-justify="space-around" vs-align="bottom" cols="2">
                             <validation-provider
                                    #default="{ errors }"
                                     name="Status"
                                    rules=""
                                  >   
                              <dropdowncuoptiuni 
                                name="status" 
                                :readonly="activeAction=='Vizualizează'" 
                                 v-model="editVarLocal.status" 
                                campDisplay="Status"
                                field_name="Status monitorizare documente"
                                limitToList="true"
                                > 
                          </dropdowncuoptiuni>
                           <small class="text-danger">{{ errors[0] }}</small>
                                </validation-provider>     
                              
                          
                       </b-col> 
                            </b-row>
                <b-row class="d-flex justify-content-center" >
                 <b-col  cols="10">
                                     <div class="form-label-group">
                                    
                                  
                                      <b-form-textarea
                                        id="obs"
                                         v-model="editVarLocal.obs"
                                        rows="3"
                                        placeholder="Observatii"
                                      />
                                  
                                      <label for="label-obs">Observatii</label>
                                    </div>     
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
import axios from "axios"
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
  name:"monitorizaredocumenteaev",
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
        modelName:"monitorizaredocumente",
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
   modificaBanca(){
      if(this.editVarLocal.banca){
        this.editVarLocal.banca=this.editVarLocal.banca.banca
      }else{
          this.editVarLocal.banca=""          
      }
    } ,
    
    modificaGestiune(value){
      
    },
    modificaContract(value){
      
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
      if(!this.editVarLocal.gestiune){
         this.$bvToast.toast("Completati agentia!", 
                                                 {
                                                    title: `Completati agentia! `,
                                                    variant:"warning",
                                                    solid: false,
                                                    appendToast: true,
                                                    autoHideDelay: 3000,
                                                    toaster: "b-toaster-top-right",
                                                                      }) 
        return 
      }
           this.showLoading=true
              
              let payLoad =this.editVarLocal 
              this.editVarLocal.requestType= "post"
              this.editVarLocal.requestUrl="/"+this.modelName+"/store"
              this.$store.dispatch("app/api_Request",payLoad)
                    .then(response=>{
                             
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
                                                     gestiune_id:"",
                                                contract_id:"",
                                                user_id:"",
                                                tip_document:"",
                                                fisier:"",
                                                data_incasarii:"",
                                        suma_incasata:"",
                                        tip_valuta:"",
                                        status:"",
                                        banca:""

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
                                                     gestiune_id:"",
                                                contract_id:"",
                                                user_id:"",
                                                tip_document:"",
                                                fisier:"",
                                                data_incasarii:"",
                                                suma_incasata:"",
                                                tip_valuta:"",
                                                status:"",
                                                banca:""

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

