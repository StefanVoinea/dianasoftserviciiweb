<template>
   <validation-observer ref="simpleRules">
  <div class= "d-flex justify-content-center" >
   <b-modal id="Dianasoftmodelaev"  
             size="lg" 
             no-close-on-backdrop
             :hide-footer="activeActionLocal=='Vizualizează'"
             ok-variant="success"
             cancel-title="Cancel"              
             ok-title="Save"
             cancel-variant="warning"
             scrollable
             :cancel-disabled="activeActionLocal=='Vizualizează'"
             :ok-disabled="activeActionLocal=='Vizualizează'"
             modal-class="modal-success"
             :title="activeActionLocal+' Interogare ANAF'"
             v-model="activeEditLocal"
             @ok="handleOk"
             @cancel="aevClosed"
             >
<form  ref="form"  @submit.stop.prevent="handleSubmit" >
            
                <b-row class="d-flex justify-content-center" >
                     
                            <b-col  cols="6">
                              <validation-provider
                                           #default="{ errors }"
                                          name="Data"
                                          rules=""
                                        >
                                     <datacalendaristica 
                                          :readonly="true"
                                          id="data" 
                                          v-model="editVarLocal.data"
                                          name="data" 
                                          campDisplay="Data"> 
                                      </datacalendaristica>
                                      <small class="text-danger">{{ errors[0] }}</small>
                                       </validation-provider>     
                            </b-col>
                            </b-row>
                          
                            <b-row class="d-flex justify-content-center" >
                            <b-col  cols="6">
                                <validation-provider
                                           #default="{ errors }"
                                          name="Data creare mesaj"
                                          rules=""
                                        >
                                  <div class="form-label-group">      
                                  <b-form-input
                                    :readonly="true"
                                    autocomplete="off"
                                    id="data_creare_mesaj"
                                    v-model="editVarLocal.data_creare_mesaj"
                                    placeholder="Data creare mesaj" 
                                    
                                  />
                                  <label for="data_creare_mesaj">Data creare mesaj</label>
                                  <small class="text-danger">{{ errors[0] }}</small>
                                   </div>
                                       </validation-provider>
                              
                                    
                            </b-col>
                            </b-row>
                            <b-row class="d-flex justify-content-center" >
                            <b-col  cols="6">
                                <validation-provider
                                           #default="{ errors }"
                                          name="Req id"
                                          rules=""
                                        >
                                  <div class="form-label-group">      
                                  <b-form-input
                                    :readonly="true"
                                    autocomplete="off"
                                    id="req_id"
                                    v-model="editVarLocal.req_id"
                                    placeholder="Req id" 
                                    
                                  />
                                  <label for="req_id">Req id</label>
                                  <small class="text-danger">{{ errors[0] }}</small>
                                   </div>
                                       </validation-provider>
                              
                                    
                            </b-col>
                            </b-row>
                            
                            <b-row class="d-flex justify-content-center" >
                            <b-col  cols="6">
                                <validation-provider
                                           #default="{ errors }"
                                          name="Tip"
                                          rules=""
                                        >
                                   <dropdowncuoptiuni 
                                name="tip" 
                                :readonly="activeAction=='Vizualizează'" 
                                 v-model="editVarLocal.tip" 
                                campDisplay="Tip persoana"
                                field_name="Tip persoana ANAF"
                                limitToList="true"/>
                                  <small class="text-danger">{{ errors[0] }}</small>
                                   
                                       </validation-provider>
                              
                                    
                            </b-col>
                            </b-row>
                            <br>
                            <b-row class="d-flex justify-content-center" >
                            <b-col  cols="6">
                               <validation-provider
                                    #default="{ errors }"
                                     name="Cnp"
                                    rules="required"
                                  >  
                           <cnp-component 
                                          class="w-full"
                                         :readonly="activeActionLocal=='Vizualizează'"
                                         name="Cnp"
                                         placeholder="CNP/CUI" 
                                         :activeEdit="activeEditLocal"
                                         @ciScanat="preiaDateCIScanat"
                                         @CUIIntrodus="CUIIntrodus"
                                         v-model="editVarLocal.cnp"
                                         >
                          </cnp-component>
                           <small class="text-danger">{{ errors[0] }}</small>
                                  
                                </validation-provider>
                                       
                            </b-col>
                            </b-row>
                            <br>
                            <b-row class="d-flex justify-content-center" >
                            <b-col  cols="6">
                                <validation-provider
                                           #default="{ errors }"
                                          name="Nume"
                                          rules="required"
                                        >
                                  <div class="form-label-group">      
                                  <b-form-input
                                    :readonly="activeActionLocal=='Vizualizează'"
                                    autocomplete="off"
                                    id="nume"
                                    v-model="editVarLocal.nume"
                                    placeholder="Nume" 
                                    
                                  />
                                  <label for="nume">Nume</label>
                                  <small class="text-danger">{{ errors[0] }}</small>
                                   </div>
                                       </validation-provider>
                              
                                    
                            </b-col>
                            </b-row>
                            <br>
                            <b-row class="d-flex justify-content-center" >
                            <b-col  cols="6">
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
                                @change="modificaSex"
                                field_name="Sex"
                                limitToList="true"/>
                           <small class="text-danger">{{ errors[0] }}</small>
                                       </validation-provider>     
                            </b-col>
                            </b-row>
                            <br>
                            <b-row class="d-flex justify-content-center" >
                            <b-col  cols="6">
                               <validation-provider
                                           #default="{ errors }"
                                          name="Judet"
                                          rules="required"
                                        >
                                     <judetcomponent 
                                                  :readonly="activeActionLocal=='Vizualizează'"
                                                  v-model="editVarLocal.judet">

                                  </judetcomponent>
                                  <small class="text-danger">{{ errors[0] }}</small>
                                       </validation-provider>     
                            </b-col>
                            </b-row>
                            <br>
                            <b-row class="d-flex justify-content-center" >
                            <b-col  cols="6">
                              <validation-provider
                                           #default="{ errors }"
                                          name="Data nasterii"
                                          rules="required"
                                        >
                                     <datacalendaristica 
                                          :readonly="activeActionLocal=='Vizualizează'"
                                          id="data_nasterii" 
                                          v-model="editVarLocal.data_nasterii"
                                          name="data_nasterii" 
                                          campDisplay="Data nasterii"> 
                                      </datacalendaristica>
                                      <small class="text-danger">{{ errors[0] }}</small>
                                       </validation-provider>     
                            </b-col>
                            </b-row>
                             <br>
                            <b-row class="d-flex justify-content-center" >
                            <b-col  cols="6">
                                <validation-provider
                                           #default="{ errors }"
                                          name="Pfa"
                                          rules=""
                                        >
                                   <dropdowncuoptiuni 
                                name="pfa" 
                                :readonly="activeAction=='Vizualizează'" 
                                 v-model="editVarLocal.pfa" 
                                campDisplay="PFA"
                                field_name="DaNu"
                                limitToList="true"/>
                                  <small class="text-danger">{{ errors[0] }}</small>
                                   
                                       </validation-provider>
                              
                                    
                            </b-col>
                            </b-row>
                            <br>
                            <b-row class="d-flex justify-content-center" >
                            <b-col  cols="6">
                                <validation-provider
                                           #default="{ errors }"
                                          name="Nr aut"
                                          rules="required"
                                        >
                                  <div class="form-label-group">      
                                  <b-form-input
                                    :readonly="activeActionLocal=='Vizualizează'"
                                    autocomplete="off"
                                    id="nr_aut"
                                    v-model="editVarLocal.nr_aut"
                                    placeholder="Nr acord" 
                                    
                                  />
                                  <label for="nr_aut">Nr acord</label>
                                  <small class="text-danger">{{ errors[0] }}</small>
                                   </div>
                                       </validation-provider>
                              
                                    
                            </b-col>
                            </b-row>
                            <br>
                            <b-row class="d-flex justify-content-center" >
                            <b-col  cols="6">
                              <validation-provider
                                           #default="{ errors }"
                                          name="Data acord"
                                          rules="required"
                                        >
                                     <datacalendaristica 
                                          :readonly="activeActionLocal=='Vizualizează'"
                                          id="data_aut" 
                                          v-model="editVarLocal.data_aut"
                                          name="data_aut" 
                                          campDisplay="Data acord"> 
                                      </datacalendaristica>
                                      <small class="text-danger">{{ errors[0] }}</small>
                                       </validation-provider>     
                            </b-col>
                            </b-row>
                           
                            
                            
                          
               
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
  name:"solicitarianafaev",
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
        modelName:"solicitarianaf",
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
    modificaSex(){
         if(this.editVarLocal.sex != "" && this.editVarLocal.cnp != ""){
            if (this.editVarLocal.sex =="Feminin" && this.editVarLocal.cnp.substring(1,2) != "2" && this.editVarLocal.cnp.substring(1,2) != "2"){
                 this.$bvToast.toast("ATENTIE !!! Sexul selectat nu corespunde cu sexul din CNP!", 
                                                 {
                                                    title: `ATENTIE !!! Sexul selectat nu corespunde cu sexul din CNP!`,
                                                    variant:"warning",
                                                    solid: false,
                                                    appendToast: true,
                                                    autoHideDelay: 3000,
                                                    toaster: "b-toaster-bottom-right",
                                                                      }) 
            }     
            if (this.editVarLocal.sex =="Masculin" && this.editVarLocal.cnp.substring(1,2) != "1" && this.editVarLocal.cnp.substring(1,2) != "5"){
                 this.$bvToast.toast("ATENTIE !!! Sexul selectat nu corespunde cu sexul din CNP!", 
                                                 {
                                                    title: `ATENTIE !!! Sexul selectat nu corespunde cu sexul din CNP!`,
                                                    variant:"warning",
                                                    solid: false,
                                                    appendToast: true,
                                                    autoHideDelay: 3000,
                                                    toaster: "b-toaster-bottom-right",
                                                                      }) 
            }
        }
    },
    preiaDateCIScanat(e,index){
        
            this.editVarLocal.cnp=e.cnp
            this.editVarLocal.sex=e.sex
            this.editVarLocal.data_nasterii=e.data_nasterii
            this.editVarLocal.nume=e.nume+" "+e.prenume
            this.editVarLocal.judet=e.judet
    },
    CUIIntrodus(){
        if(this.editVarLocal.sex != "" && this.editVarLocal.cnp != ""){
            if (this.editVarLocal.sex =="Feminin" && this.editVarLocal.cnp.substring(1,2) != "2" && this.editVarLocal.cnp.substring(1,2) != "2"){
                 this.$bvToast.toast("ATENTIE !!! Sexul selectat nu corespunde cu sexul din CNP!", 
                                                 {
                                                    title: `ATENTIE !!! Sexul selectat nu corespunde cu sexul din CNP!`,
                                                    variant:"warning",
                                                    solid: false,
                                                    appendToast: true,
                                                    autoHideDelay: 3000,
                                                    toaster: "b-toaster-bottom-right",
                                                                      }) 
            }     
            if (this.editVarLocal.sex =="Masculin" && this.editVarLocal.cnp.substring(1,2) != "1" && this.editVarLocal.cnp.substring(1,2) != "5"){
                 this.$bvToast.toast("ATENTIE !!! Sexul selectat nu corespunde cu sexul din CNP!", 
                                                 {
                                                    title: `ATENTIE !!! Sexul selectat nu corespunde cu sexul din CNP!`,
                                                    variant:"warning",
                                                    solid: false,
                                                    appendToast: true,
                                                    autoHideDelay: 3000,
                                                    toaster: "b-toaster-bottom-right",
                                                                      }) 
            }
        }

 

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
                                                     data:'',
                                                data_creare_mesaj:'',
                                                req_id:'',
                                                tip:'',
                                                cnp:'',
                                                nr_aut:'',
                                                data_aut:'',
                                                pfa:'',
                                                nume:'',
                                                sex:'',
                                                judet:'',
                                                data_nasterii:'',
                                                

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
                                                     data:"",
                                                data_creare_mesaj:"",
                                                req_id:"",
                                                tip:"",
                                                cnp:"",
                                                nr_aut:"",
                                                data_aut:"",
                                                pfa:"",
                                                nume:"",
                                                sex:"",
                                                judet:"",
                                                data_nasterii:"",
                                                

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
                                                data_creare_mesaj:"",
                                                req_id:"",
                                                tip:"",
                                                cnp:"",
                                                nr_aut:"",
                                                data_aut:"",
                                                pfa:"",
                                                nume:"",
                                                sex:"",
                                                judet:"",
                                                data_nasterii:"",
                                                

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

