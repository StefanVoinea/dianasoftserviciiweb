<template>
  <validation-observer ref="simpleRules">
  <div class= "d-flex justify-content-center" >
   <b-modal id="Dianasoftmodelaev"  
             size="xl" 
             no-close-on-backdrop
             
             ok-variant="success"
             cancel-title="Cancel"              
             ok-title="Ok"
             cancel-variant="warning"
             scrollable
              hide-footer
             :cancel-disabled="true"
             :ok-disabled="activeActionLocal=='Vizualizează'"
             modal-class="modal-success"
             :title="editVarLocal.titlu_document_dosar"
             v-model="activeEditLocal"
             @ok="handleOk"
             @cancel="aevClosed"
             >
     <form  ref="form"  @submit.stop.prevent="handleSubmit" >
             <br>

                
              <b-container fluid>
      <b-form
        ref="form"
        :style="{height: trHeight}"
        class="repeater-form"
        @submit.prevent="repeateAgain"
      >
        <b>
         <b-row >
        <b-col cols="1">
               Nr crt
        </b-col>
                
          <b-col cols="1">   
              Grupa
          </b-col>
          <b-col cols="3">   
              Document
          </b-col>
          <b-col cols="4">   
              Obs
          </b-col>
          <b-col cols="1">   
              Utilizator
          </b-col>
          <b-col cols="1">   
              Fisier
          </b-col>         
        <b-col cols="1">   
              Actiuni
          </b-col>  
        </b-row>
        </b>
        </br>
        
        <b-row 
        v-for="(item, index) in editVarLocal.dosarelectronic"
              :id="item.id"
              :key="item.id"
              ref="row" class="mt-1" >

        <b-col cols="1">
               {{item.configopis.nr_crt}}
        </b-col>
                
        
          
            <!-- Add Button -->
          <b-col class="d-flex justify-content-between" cols="1">
          {{item.configopis.grupadocument}}

           
        
     
        
          </b-col>
           <b-col cols="3">   
            {{item.configopis.tipdocumentopis}}
          </b-col>
          <b-col cols="4">
            <b-form-input
                :readonly="activeActionLocal=='Vizualizează'||!canModificaObsDosarElectronic"
                autocomplete="off"
                :id="'obs_'+item.id"
                v-model="item.obs"
                @change=modificaOBSOpis(item.id)
                placeholder="Observatii"/>

          </b-col>
          <b-col cols="1">   
            {{item.user_name}}
          </b-col>
          <b-col cols="1">
          <b-button
                        variant="outline-success"
                        v-show="item.user_name" 
                        size=""
                        v-b-tooltip.hover.v-dark
                       title="Descarca fisier"
                        class="btn-icon"
                        @click="view(item.id)">
                       <feather-icon   icon="EyeIcon"/>
            
            </b-button>
            <div v-show="(!item.user_name)&&canUploadFileDosarElectronic" @click="selectFile(item.id)">
            <b-button  variant="primary"
                       class="btn-icon "
                       size=""
                       v-b-tooltip.hover.v-dark
                       title="Upload file"> 
                       <feather-icon icon="UploadCloudIcon" /> 
              </b-button>
            <input v-show="!item.user_name" :name="item.id" :id="'file_'+item.id" :ref="'file_'+item.id" type="file" class="form-control" v-on:change="onFileChange" hidden/>
            </div>
            </b-col> 
            <b-col cols="1">
             <b-button v-show="item.user_name"
                        v-ripple.400="'rgba(234, 84, 85, 0.15)'"
                        variant="outline-success"
                        size="sm"
                        class="btn-icon"
                       v-b-tooltip.hover.v-dark
                       title="Copiaza linie"
                        @click="addOpis(item.id)">
                       <feather-icon   icon="PlusIcon"/>
            
            </b-button>

             <b-button v-show="item.user_name&&canDeleteFileDosarElectronic"
                        v-ripple.400="'rgba(234, 84, 85, 0.15)'"
                        variant="outline-danger"
                        size="sm"
                        class="btn-icon"
                       v-b-tooltip.hover.v-dark
                       title="Sterge fisier"
                        @click="deleteFile(item.id)">
                       <feather-icon   icon="XIcon"/>
            
            </b-button>
          </b-col> 
        </b-row>
       <br><br><br><br>
      </b-form>
      
      </b-container>
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
        
        },
  mixins: [heightTransition],
  components: {
     ValidationProvider, ValidationObserver 
  },
   directives: {
    "b-modal": VBModal,
    Ripple,
  },
  name:"dosarelectronic",
  data() {
    return {
       required,
        password,
        email,
        confirmed,
         min,
        canUploadFileDosarElectronic:this.$userpermitt.can("uploadFileDosarElectronic"),
        canDeleteFileDosarElectronic:this.$userpermitt.can("deleteFileDosarElectronic"),
        canModificaObsDosarElectronic:this.$userpermitt.can("modificaObsDosarElectronic"),
        rutainapoiLocal:this.rutainapoi,
        activeEditLocal:false,
        activeActionLocal:this.activeAction,
        editVarLocal:this.editVar,
        nextTodoId: 2,
        modelName:"opisdosaranaliza",
        showLoading:false,
        file:"",
       
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
    selectFile(id){
     document.getElementById('file_'+id).click()
     },
     modificaOBSOpis(id){
                        this.showLoading=true
          const payLoad=this.editVarLocal
          payLoad.requestType="post"
          payLoad.opis=this.editVarLocal.dosarelectronic.filter(doc=>doc.id==id)
          if(this.editVarLocal.tip_document_dosar=="Solicitare"){
            payLoad.requestUrl="/opisdosaranaliza/modificaOBSOpis/"+id
          }
        
          this.$store.dispatch("app/api_Request",payLoad)
                    .then(response=>{
                           
                          

                            this.showLoading=false
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
    addOpis(id){
                  this.showLoading=true
                  const payLoad=this.editVarLocal
                  payLoad.requestType="post"
                  payLoad.requestUrl=""
                 if(this.editVarLocal.tip_document_dosar=="Solicitare"){
                  payLoad.requestUrl='/opisdosaranaliza/addOpis/'+id
                 }
                 
                 this.$store.dispatch("app/api_Request",payLoad)
                          .then(response => {
                             this.file='' 
                             
                            this.editVarLocal.dosarelectronic=response.sort((a,b)=>{

                                                         if(a.configopis.nr_crt<b.configopis.nr_crt){
                                                          return -1
                                                         }else{
                                                          return 0
                                                         }
                                                    })
                             this.showLoading=false
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
    view(indextr){
                        this.showLoading=true
          const payLoad=this.editVarLocal
          payLoad.requestType="post"
          payLoad.requestUrl=""
           if(this.editVarLocal.tip_document_dosar=="Solicitare"){
              payLoad.requestUrl="/opisdosaranaliza/viewFile/"+indextr
            }
          this.$store.dispatch("app/api_blob_Request",payLoad)
                    .then(response=>{
                           
                           this.showLoading=false
                           var fileURL = window.URL.createObjectURL(new Blob([response]));
                           var fileLink = document.createElement('a');
                           fileLink.href = fileURL;
                           let doc=this.editVarLocal.dosarelectronic.filter(doc=>doc.id==indextr)
                             fileLink.setAttribute('download', 'Document_'+(new Date().toLocaleDateString()).replace(".","_")+"."+doc[0].tip_fisier);
                             document.body.appendChild(fileLink);
                             fileLink.click();
                           
                      
                        
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
   
    deleteFile(id){
                                this.showLoading=true
                 const payLoad=this.editVarLocal
                 payLoad.requestUrl=""
                 if(this.editVarLocal.tip_document_dosar=="Solicitare"){
                  payLoad.requestUrl='/opisdosaranaliza/deleteFile/'+id
                 }
                
                  payLoad.requestType="post"
                 
                 this.$store.dispatch("app/api_Request",payLoad)
                          .then(response => {
                            this.file=''
                             
                            this.editVarLocal.dosarelectronic=response.sort((a,b)=>{

                                                         if(a.configopis.nr_crt<b.configopis.nr_crt){
                                                          return -1
                                                         }else{
                                                          return 0
                                                         }
                                                    })
                            
                             this.showLoading=false
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
    onFileChange(e){
                
                this.file = e.target.files[0];
                if(this.file){
                  this.uploadFile(e)
                }
    },
    uploadFile(e){
                this.showLoading=true
                e.preventDefault();
                // this.uploadFileActive=false
                let currentObj = this;
                const config = {
                                 headers: { 'content-type': 'multipart/form-data',
                                           'Authorization': 'Bearer ' + this.$store.state.app.token,
                                           'AuthorizationHeader': JSON.parse(this.$store.state.app.societateaCurenta).id
                                           }
                                }
                let formData = new FormData();
                formData.append('file', this.file);
                let url=""
                 if(this.editVarLocal.tip_document_dosar=="Solicitare"){
                  url='/api/opisdosaranaliza/uploadFile/'+e.target.name
                 }
                axios.post(url, formData, config)
                          .then(response => {
                             this.file=''
                             
                            this.editVarLocal.dosarelectronic=response.data.sort((a,b)=>{

                                                         if(a.configopis.nr_crt<b.configopis.nr_crt){
                                                          return -1
                                                         }else{
                                                          return 0
                                                         }
                                                    })
                           
                             this.showLoading=false
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
   
    
     handleOk(bvModalEvt){
       bvModalEvt.preventDefault()
        this.$refs.simpleRules.validate().then(success => {
        if (success) {
      
          this.saveEdit()
      
         }
      })
    },
 
   
    aevClosed(){
        this.idselectat=null
        this.selectedID=""
        this.activeEditLocal=false
        this.editVarLocal={opisdosaranaliza:[],
                                                                  nume:"",
                                                                  data:"",
                                                                data_solicitare:""}
        this.activeActionLocal=""
        this.$emit("closed")
        
    },
    
    saveEdit(){
               this.editVarOpis={dosarelectronic:[{configopis:{nr_crt:"",
                                                    grupadocument:"",
                                                     tipdocumentopis:"" },
                                        obs:"",  
                                        }],
                                      }
              
                                               this.activeEditLocal=false
                                               this.activeActionLocal=""
                                               this.$emit("stored","") 
                                             
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
         
        
          window.addEventListener("resize", this.initTrHeight)
          
     
    },
}

</script>

