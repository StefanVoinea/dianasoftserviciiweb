<template>
  
   <b-modal id="Dianasoftmodelaev"  
             size="xl" 
             no-close-on-backdrop
             ok-variant="success"
             cancel-title="Cancel"              
             ok-title="Save"
             cancel-variant="warning"
             scrollable

            
             modal-class="modal-success"
             :title="'Modifică drepturi pentru utilizatorul '+this.editVarLocal.name"
             v-model="activeCopyLocal"
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
  <validation-observer ref="simpleRules">
     <form  ref="form"  @submit.stop.prevent="handleSubmit" >
              <b-overlay
      :show="showLoading"
      rounded="sm"
      no-fade
      variant="primary"
      opacity="0.25"
      blur="2px"
    >         
               <b-row class="d-flex justify-content-center">
               <b-col  cols="8" >  
                <dstable 
                        :scrollbar="true"
                        title="Notificari"
                        :fields="fieldsNotificari"
                        :items="editVarLocal.notificari"/>
                </b-col>
                
               <b-col  cols="4" >  
                <dstable 
                        :scrollbar="true"
                        title="Grupuri"
                        :fields="fieldsGrupuri"
                        :items="editVarLocal.grupuri"/>
              </b-col>

                </b-row > 
             <br>
             <b-row class="d-flex justify-content-center">
               <b-col  cols="4" >     
                 <dstable 
                          :scrollbar="true"
                          @rowSelected="randSelectat"
                          title="Opțiuni menu"
                          :fields="fieldsOptiuniMenu"
                          :items="editVarLocal.dianasoftmenuoptions"/>
              </b-col>
              <b-col  cols="6" >  

                 <div class="d-flex flex-row-reverse">
                  <b-button 
                      v-ripple.400="'rgba(113, 102, 240, 0.15)'"
                      variant="outline-danger"
                      class="btn-icon  mr-1"
                      size=""
                      v-b-tooltip.hover.v-light
                      title="Remove filter"
                      @click="removeFilter">
                      <feather-icon icon="XIcon" />
                  </b-button>
                  </div>
                  
                 <dstable 
                          :scrollbar="true"
                          
                          title="Operațiuni"
                          :fields="fieldsOperatiuni"
                          :items="editVarLocal.permissions"/>
             
              </b-col>
              <b-col  cols="2" >  
                <dstable 
                        :scrollbar="true"
                        title="Gestiuni"
                        :fields="fieldsGestiuni"
                        :items="editVarLocal.gestiuni"/>
              </b-col>
              
              </b-row >
              <br>
      </b-overlay>
      </form>
   </validation-observer>
    </b-modal>
  
</template>

<script>

import Ripple from "vue-ripple-directive"
import { heightTransition } from "@core/mixins/ui/transition"
import {VBModal} from "bootstrap-vue"
import {  required, email, confirmed, password,min} from '@validations'
import { ValidationProvider, ValidationObserver} from 'vee-validate'


export default {
  props: {
        activeCopy:Boolean,
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
  name:"utilizatorimodificadrepturi",
  data() {
    return {
   
        required,
        password,
        email,
        confirmed,
        min,
        rutainapoiLocal:this.rutainapoi,
        activeCopyLocal:false,
        activeActionLocal:this.activeAction,
        editVarLocal:this.editVar,
        nextTodoId: 2,
        
        modelName:"utilizatori",
        showLoading:false,
        fieldsGestiuni: [

                        {key:'denumire',label:"Denumire",field_type:'label',sortable: true,stickyColumn: false,isRowHeader: true,searchable:true,readonly:false},
                        {key:'pivot.isactive',label:'Permisiune',field_type:'checkbox'}
                        ],
        fieldsOptiuniMenu: [
                         {key:'parent',label:"Grup de optiuni",field_type:'label',sortable: true,stickyColumn: false,isRowHeader: true,searchable:true,readonly:false},
                        {key:'name',label:"Denumire",field_type:'label',sortable: true,stickyColumn: false,isRowHeader: true,searchable:true,readonly:false},
                        {key:'pivot.isactive',label:'Permisiune',field_type:'checkbox'}
                        ],
        fieldsOperatiuni: [
                         {key:'dianasoftmenuoption.name',label:"Optiune",field_type:'label',sortable: true,stickyColumn: false,isRowHeader: true,searchable:true,readonly:false}, 
                        {key:'display_name',label:"Denumire",field_type:'label',sortable: true,stickyColumn: false,isRowHeader: true,searchable:true,readonly:false},
                        {key:'pivot.isactive',label:'Permisiune',field_type:'checkbox'}
                        ],
        fieldsGrupuri: [
                        {key:'name',label:"Denumire",field_type:'label',sortable: true,stickyColumn: false,isRowHeader: true,searchable:true,readonly:false},
                        {key:'isactive',label:'Permisiune',field_type:'checkbox'}
                        ],
          fieldsNotificari: [
                        {key:'denumire',label:"Denumire",field_type:'label',sortable: true,stickyColumn: false,isRowHeader: true,searchable:true,readonly:false},
                        {key:'channel',label:"Canal",field_type:'selectonearray',sortable: true,stickyColumn: false,isRowHeader: true,searchable:true,readonly:false,
                                                      lista:[{"denumire":"Email"},
                                                             {"denumire":"Application"}, 
                                                      ]},
                         //{key:'isactive',label:'Permisiune',field_type:'checkbox'} 
                        ],
      }
  },
 
  watch: {
      activeCopy(){
         
         this.activeCopyLocal=this.activeCopy
       },
       activeCopyLocal(){
            if (this.activeCopyLocal==false){
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
    removeFilter(){
      this.permisiuniFiltrate("")
    },
    randSelectat(value){
      
      
      if(value){
        
        this.permisiuniFiltrate(value)
      }
    },
    permisiuniFiltrate(id_optiune){
      this.showLoading=true
                let payLoad=this.editVarLocal
                payLoad.id_optiune=id_optiune
                
                payLoad.requestType="post"
                payLoad.requestUrl="/utilizatori/permisiunifiltrate" 
                
                this.$store.dispatch("app/api_Request",payLoad)
                .then(response=>{
                                 
                                 this.editVarLocal.permissions=response
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
   
    
     handleOk(bvModalEvt){
      bvModalEvt.preventDefault()
              this.showLoading=true
        this.$refs.simpleRules.validate().then(success => {
        
        if (success) {
              this.showLoading=true
                let payLoad=this.editVarLocal
                payLoad.id_optiune=""
                
                payLoad.requestType="post"
                payLoad.requestUrl="/utilizatori/permisiunifiltrate" 
                
                this.$store.dispatch("app/api_Request",payLoad)
                .then(response=>{
                                 
                                let payLoad=this.editVarLocal
                payLoad.requestType="post"
                payLoad.requestUrl="/utilizatori/updatedrepturi" 
                
                this.$store.dispatch("app/api_Request",payLoad)
                .then(response=>{
                                 
                                 this.$bvToast.toast("Salvare efectuată cu succes!", {
                                                                        title: "Salvare efectuată cu succes!",
                                                                        variant:"success",
                                                                        solid: false,
                                                                        appendToast: true,
                                                                        autoHideDelay: 3000,
                                                                        toaster: "b-toaster-bottom-right",
                                                                      }) 
                                  this.editVarLocal={  name:"",
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
                                  grup:[],
                                  notificari:[]}
                                  this.showLoading=false
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
                                 
                                 
                })
               
            
          }
      })
    },
    
  
   
    aevClosed(){
        this.activeCopyLocal=false
        this.editVarLocal={  name:"",
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
          grup:[],
          notificari:[]}
        this.activeActionLocal=""
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
         
         if(!this.rutainapoi){
          this.rutainapoiLocal=this.modelName
         }
          window.addEventListener("resize", this.initTrHeight)
          
     
    },
}

</script>


