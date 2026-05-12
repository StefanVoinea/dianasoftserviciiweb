<template>
  <validation-observer ref="simpleRules">
  <div class= "d-flex justify-content-center" >
   <b-modal id="Dianasoftmodelaev"  
             size="xl" 
             no-close-on-backdrop
             
             ok-variant="success"
             cancel-title="Cancel"              
             ok-title="Save"
             cancel-variant="warning"
             scrollable
             :cancel-disabled="activeActionLocal=='Vizualizează'"
             :ok-disabled="activeActionLocal=='Vizualizează'"
             modal-class="modal-success"
             :title="activeActionLocal+' Activitate'"
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
                                     name="User"
                                    rules=""
                                  >  
                                  <div class="form-label-group">
                                  <b-form-input
                                    :readonly="activeActionLocal=='Vizualizează'"
                                    autocomplete="off"
                                    id="user_id"
                                    v-model="editVarLocal.user.name"
                                    placeholder="User" 
                                    
                                  />
                                  <label for="user_id">User</label>
                                   <small class="text-danger">{{ errors[0] }}</small>
                                  </div>
                                </validation-provider>
                              
                                     
                            </b-col>
                            
                            <b-col  cols="2">
                                 <validation-provider
                                    #default="{ errors }"
                                     name="Operatiune"
                                    rules="required"
                                  >  
                                  <div class="form-label-group">
                                  <b-form-input
                                    :readonly="activeActionLocal=='Vizualizează'"
                                    autocomplete="off"
                                    id="description"
                                    v-model="editVarLocal.description"
                                    placeholder="Operatiune" 
                                    
                                  />
                                  <label for="description">Operatiune</label>
                                   <small class="text-danger">{{ errors[0] }}</small>
                                  </div>
                                </validation-provider>
                              
                                     
                            </b-col>
                            <b-col  cols="2">
                                   
                                  <div class="form-label-group">
                                  <datasiora
                                    :readonly="activeActionLocal=='Vizualizează'"
                                    id="created_at"
                                    campDisplay="Data operarii"
                                    v-model="editVarLocal.created_at"
                                    placeholder="Data operarii" 
                                    
                                  />
                                  
                                  </div>
                                
                                     
                            </b-col>
                            </b-row>
                            <b-row class="d-flex justify-content-center" >
                            <b-col   v-if="editVarLocal.changes.before" cols="6">
                               <dstable 
                                    :title="editVarLocal.description.includes('updated')?'Before':'Valori'"
                                    :fields="fieldsBefore"
                                    :items="itemsBefore"/>
                          
                            </b-col>
                            <b-col v-if="editVarLocal.description.includes('updated')"  cols="6">
                                <dstable 

                                    title="After"
                                    :fields="fieldsAfter"
                                    :items="itemsAfter"/>
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
  name:"activityaev",
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
        modelName:"activity",
        showLoading:false,
        fieldsBefore: [
                        {key:'name',label:"Denumire",field_type:'label',sortable: true,searchable:true,readonly:false},
                        {key:'value',label:'Valoare',field_type:'label',sortable: true,searchable:true,readonly:false}
                        ],
         itemsBefore:[] ,              
         fieldsAfter: [
                        {key:'name',label:"Denumire",field_type:'label',sortable: true,searchable:true,readonly:false},
                        {key:'value',label:'Valoare',field_type:'label',sortable: true,searchable:true,readonly:false}
                        ],
         itemsAfter:[]   
       
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
        if(this.editVar.changes.before!=""||this.editVar.changes.after!=""){
        this.itemsBefore=[]
        this.itemsAfter=[]
         Object.keys(this.editVar.changes.before).map((t)=>{
            this.itemsBefore.push({name:t,value:this.editVar.changes.before[t]})
           })
         Object.keys(this.editVar.changes.after).map((t)=>{
            this.itemsAfter.push({name:t,value:this.editVar.changes.after[t]})
           })
           }
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
                                                     user_id:'',
                                                company_id:'',
                                                subject:'',
                                                description:'',
                                                changes:'',
                                                

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
        this.idselectat=null
        this.selectedID=""
        this.activeEditLocal=false
        this.editVarLocal={  
                                                     user_id:"",
                                                company_id:"",
                                                subject:"",
                                                description:"",
                                                changes:{before:"",
                                                        after:""}                                                

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
                                                    
                                                     user_id:"",
                                                company_id:"",
                                                subject:"",
                                                description:"",
                                                user:{name:""},
                                                changes:{before:"",
                                                        after:""} 
                                                

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

