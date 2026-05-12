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
             :title="activeActionLocal+' File manager'"
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
                     
                          
                            <b-col  cols="4">
                                     <div class="form-label-group">
                                    
                                    <validation-provider
                                           #default="{ errors }"
                                          name="Denumire"
                                          rules=""
                                        >
                                    
                                      <b-form-textarea
                                        id=".denumire"
                                         v-model="editVarLocal.denumire"
                                        rows="3"
                                        placeholder="Denumire"
                                      />
                                       <small class="text-danger">{{ errors[0] }}</small>
                                       </validation-provider>
                                      <label for="label-denumire">Denumire</label>
                                    </div>     
                            </b-col>
                            
                           
                            
                            <b-col  cols="4">
                                     <div class="form-label-group">
                                    
                                    <validation-provider
                                           #default="{ errors }"
                                          name="Obs"
                                          rules=""
                                        >
                                    
                                      <b-form-textarea
                                        id="obs"
                                         v-model="editVarLocal.obs"
                                        rows="3"
                                        placeholder="Obs"
                                      />
                                       <small class="text-danger">{{ errors[0] }}</small>
                                       </validation-provider>
                                      <label for="label-obs">Obs</label>
                                    </div>     
                            </b-col>
                             <b-col  cols="2">
                              <validation-provider
                                           #default="{ errors }"
                                          name="Data ultimei revizii"
                                          rules=""
                                        >
                                     <datacalendaristica 
                                          :readonly="activeActionLocal=='Vizualizează'"
                                          id="data_ultimei_revizii" 
                                          v-model="editVarLocal.data_ultimei_revizii"
                                          name="data_ultimei_revizii" 
                                          campDisplay="Data ultimei revizii"> 
                                      </datacalendaristica>
                                      <small class="text-danger">{{ errors[0] }}</small>
                                       </validation-provider>     
                            </b-col>
                            
                           
                    
                </b-row>
               <input name="files[]" multiple :id="'file'" :ref="'file'" type="file" class="form-control" v-on:change="onFileChange" hidden/>    
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
  name:"filemanageraev",
  data() {
    return {
        file:[],
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
        modelName:"filemanager",
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
    onFileChange(e){
      this.file = e.target.files;
      if(this.file.length>0){
        this.uploadFile(e)
      }
    },
    uploadFile(e){
            this.showLoading=true
            e.preventDefault();
           // console.log( this.$store)
            let payLoad =this.editVarLocal 
            payLoad.requestType= "post"
            payLoad.requestUrl="/"+this.modelName+"/store"
            const config = {
                                                 headers: { 'content-type': 'multipart/form-data',
                                                           'Authorization': 'Bearer ' + this.$store.state.app.token,
                                                           'AuthorizationHeader': JSON.parse(this.$store.state.app.societateaCurenta).id
                                                           }
                                                }
            Array.from(this.file).map((fisier)=>{
            this.$store.dispatch("app/api_Request",payLoad)
                .then(response=>{
                              
                                let currentObj = this;
                                let formData = new FormData();

                                formData.append('file', fisier);
                                let url="/api/filemanager/uploadfile/"+response.id
                                 
                                axios.post(url, formData, config)
                                .then(response => {
                                           
                             
                                                                   
                           
                           
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
            })
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
                           
                           this.$emit("stored")              
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
        document.getElementById('file').click()
        /* this.showLoading=true
          const payLoad=this.editVarLocal
          payLoad.requestType="post"
          payLoad.requestUrl="/"+this.modelName+"/store"
         
          this.$store.dispatch("app/api_Request",payLoad)
                    .then(response=>{
                           
                            this.editVarLocal={  
                                                     gestiune_id:'',
                                                grupa:'',
                                                denumire:'',
                                                data_ultimei_revizii:'',
                                                status:'',
                                                obs:'',
                                                fisier:'',
                                                fisier_original:'',
                                                tip_fisier:'',
                                                data_inceput:'',
                                                data_sfarsit:'',
                                                

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
                      })*/
         
    },
   
    aevClosed(){
       this.idselectat=null
      this.selectedID=""
        this.activeEditLocal=false
        this.editVarLocal={  
                                                     gestiune_id:"",
                                                grupa:"",
                                                denumire:"",
                                                data_ultimei_revizii:"",
                                                status:"",
                                                obs:"",
                                                fisier:"",
                                                fisier_original:"",
                                                tip_fisier:"",
                                                data_inceput:"",
                                                data_sfarsit:"",
                                                

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
                                                grupa:"",
                                                denumire:"",
                                                data_ultimei_revizii:"",
                                                status:"",
                                                obs:"",
                                                fisier:"",
                                                fisier_original:"",
                                                tip_fisier:"",
                                                data_inceput:"",
                                                data_sfarsit:"",
                                                

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

