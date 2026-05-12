<template>
  <div class= "d-flex justify-content-center" >
   <b-modal id="Dianasoftmodelaev"  
             size="xl" 
             no-close-on-backdrop
             
             ok-variant="success"
             cancel-title="Cancel"              
             ok-title="Save"
             cancel-variant="warning"
             scrollable
             modal-class="modal-success"
             :title="activeActionLocal+' Jurnal notificari'"
             v-model="activeEditLocal"
             @ok="handleOk"
             @cancel="aevClosed"
             >
             <br>

            
                <b-row >
                     
                            <b-col  cols="2">
                                   <dropdowncuruta 
                                                  @change="modificaNotificationtype"
                                                  name="tip_notificare" 
                                                  :readonly="activeAction=='Vizualizează'" 
                                                  v-model="editVarLocal.notificationtype" 
                                                  campDisplay="Tip notificare"
                                                  ruta="notificationtype"
                                                  limitToList="true"
                                                  colCaut="denumire"
                                                  > 
                                            </dropdowncuruta>         
                            </b-col>
                            
                            <b-col  cols="2">
                                     <selectoneuser  labelDisplay="De la"
                                                      @change="modificaFrom"
                                                       v-model="editVarLocal.from"
                                                      :readonly="activeActionLocal=='Vizualizează'"
                                                    >
                                     </selectoneuser>      
                            </b-col>
                            
                            <b-col  cols="2">
                                     <selectoneuser  
                                                    @change="modificaUser"
                                                    labelDisplay="Catre"
                                                    v-model="editVarLocal.user"
                                                    :readonly="activeActionLocal=='Vizualizează'"
                                                    >
                                     </selectoneuser>      
                            </b-col>
                            
                            <b-col  cols="2">
                                     
                              <dropdowncuoptiuni 
                                name="channel" 
                                :readonly="activeAction=='Vizualizează'" 
                                 v-model="editVarLocal.channel" 
                                campDisplay="Canal de comunicare"
                                field_name="Canal de comunicare notificari"
                                limitToList="true"
                                > 
                          </dropdowncuoptiuni>     
                            </b-col>
                            
                            <b-col  cols="2"><div class="form-label-group">
                                  <b-form-input
                                    :readonly="activeActionLocal=='Vizualizează'"
                                    id="email"
                                    v-model="editVarLocal.email"
                                    placeholder="Email" 
                                    
                                  />
                                  <label for="email">Email</label>
                                <!--   <b-form-valid-feedback tooltip>
                                    Looks good!
                                  </b-form-valid-feedback>
                                  <b-form-invalid-feedback tooltip>
                                    Please provide a Email.
                                  </b-form-invalid-feedback> -->
                                </div>     
                            </b-col>
                            
                            <b-col  cols="2"><div class="form-label-group">
                                  <b-form-input
                                    :readonly="activeActionLocal=='Vizualizează'"
                                    id="telefon"
                                    v-model="editVarLocal.telefon"
                                    placeholder="Telefon" 
                                    
                                  />
                                  <label for="telefon">Telefon</label>
                                <!--   <b-form-valid-feedback tooltip>
                                    Looks good!
                                  </b-form-valid-feedback>
                                  <b-form-invalid-feedback tooltip>
                                    Please provide a Telefon.
                                  </b-form-invalid-feedback> -->
                                </div>     
                            </b-col>
                            
                            <b-col  cols="2"><div class="form-label-group">
                                  <b-form-input
                                    :readonly="activeActionLocal=='Vizualizează'"
                                    id="title"
                                    v-model="editVarLocal.title"
                                    placeholder="Titlu" 
                                    
                                  />
                                  <label for="title">Titlu</label>
                                <!--   <b-form-valid-feedback tooltip>
                                    Looks good!
                                  </b-form-valid-feedback>
                                  <b-form-invalid-feedback tooltip>
                                    Please provide a Titlu.
                                  </b-form-invalid-feedback> -->
                                </div>     
                            </b-col>
                            
                            <b-col  cols="2"><div class="form-label-group">
                                  <b-form-input
                                    :readonly="activeActionLocal=='Vizualizează'"
                                    id="subtitle"
                                    v-model="editVarLocal.subtitle"
                                    placeholder="Descriere" 
                                    
                                  />
                                  <label for="subtitle">Descriere</label>
                                <!--   <b-form-valid-feedback tooltip>
                                    Looks good!
                                  </b-form-valid-feedback>
                                  <b-form-invalid-feedback tooltip>
                                    Please provide a Descriere.
                                  </b-form-invalid-feedback> -->
                                </div>     
                            </b-col>
                            
                            <b-col  cols="2"><div class="form-label-group">
                                  <b-form-input
                                    :readonly="activeActionLocal=='Vizualizează'"
                                    id="type"
                                    v-model="editVarLocal.type"
                                    placeholder="Tip" 
                                    
                                  />
                                  <label for="type">Tip</label>
                                <!--   <b-form-valid-feedback tooltip>
                                    Looks good!
                                  </b-form-valid-feedback>
                                  <b-form-invalid-feedback tooltip>
                                    Please provide a Tip.
                                  </b-form-invalid-feedback> -->
                                </div>     
                            </b-col>
                            
                            <b-col  cols="2"><div class="form-label-group">
                                  <b-form-input
                                    :readonly="activeActionLocal=='Vizualizează'"
                                    id="icon"
                                    v-model="editVarLocal.icon"
                                    placeholder="Icon" 
                                    
                                  />
                                  <label for="icon">Icon</label>
                                <!--   <b-form-valid-feedback tooltip>
                                    Looks good!
                                  </b-form-valid-feedback>
                                  <b-form-invalid-feedback tooltip>
                                    Please provide a Icon.
                                  </b-form-invalid-feedback> -->
                                </div>     
                            </b-col>
                            
                            <b-col  cols="2"><div class="form-label-group">
                                  <b-form-input
                                    :readonly="activeActionLocal=='Vizualizează'"
                                    id="avatar"
                                    v-model="editVarLocal.avatar"
                                    placeholder="Avatar" 
                                    
                                  />
                                  <label for="avatar">Avatar</label>
                                <!--   <b-form-valid-feedback tooltip>
                                    Looks good!
                                  </b-form-valid-feedback>
                                  <b-form-invalid-feedback tooltip>
                                    Please provide a Avatar.
                                  </b-form-invalid-feedback> -->
                                </div>     
                            </b-col>
                            
                            <b-col  cols="2"><div class="form-label-group">
                                  <b-form-input
                                    :readonly="activeActionLocal=='Vizualizează'"
                                    id="link"
                                    v-model="editVarLocal.link"
                                    placeholder="Link" 
                                    
                                  />
                                  <label for="link">Link</label>
                                <!--   <b-form-valid-feedback tooltip>
                                    Looks good!
                                  </b-form-valid-feedback>
                                  <b-form-invalid-feedback tooltip>
                                    Please provide a Link.
                                  </b-form-invalid-feedback> -->
                                </div>     
                            </b-col>
                            
                            <b-col  cols="2">
                                     
                              <dropdowncuoptiuni 
                                name="category" 
                                :readonly="activeAction=='Vizualizează'" 
                                 v-model="editVarLocal.category" 
                                campDisplay="Categorie notificare"
                                field_name="Categorie notificare"
                                limitToList="true"
                                > 
                          </dropdowncuoptiuni>     
                            </b-col>
                            
                            <b-col  cols="2"><div class="form-label-group">
                                  <b-form-input
                                    :readonly="activeActionLocal=='Vizualizează'"
                                    id="read_at"
                                    v-model="editVarLocal.read_at"
                                    placeholder="Citit la" 
                                    
                                  />
                                  <label for="read_at">Citit la</label>
                                <!--   <b-form-valid-feedback tooltip>
                                    Looks good!
                                  </b-form-valid-feedback>
                                  <b-form-invalid-feedback tooltip>
                                    Please provide a Citit la.
                                  </b-form-invalid-feedback> -->
                                </div>     
                            </b-col>
                            
                    
                </b-row>
                
              <br><br><br><br><br><br><br>
      
    </b-modal>
  </div>
</template>

<script>
import Ripple from "vue-ripple-directive"
import { heightTransition } from "@core/mixins/ui/transition"
import {VBModal} from "bootstrap-vue"
export default {
  props: {
        activeEdit:Boolean,
        activeAction:String,
        editVar:Object,
        rutainapoi:String,
        },
  mixins: [heightTransition],
  components: {

  },
   directives: {
    "b-modal": VBModal,
    Ripple,
  },
  name:"notificationlogaev",
  data() {
    return {
        rutainapoiLocal:this.rutainapoi,
        activeEditLocal:false,
        activeActionLocal:this.activeAction,
        editVarLocal:this.editVar,
        nextTodoId: 2,
        modelName:"notificationlog",
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
    modificaNotificationtype(){
       if(this.editVarLocal.notificationtype){
         this.editVarLocal.notificationtype_id=this.editVarLocal.notificationtype.id
        }else{
          this.editVarLocal.notificationtype_id=null
        }
      
    },
    modificaUser(){
      
       if(this.editVarLocal.user){

        this.editVarLocal.user_id=this.editVarLocal.user.id
        }else{
          this.editVarLocal.user_id=null
        }
      
    },
    modificaFrom(){
       if(this.editVarLocal.from){

        this.editVarLocal.from_id=this.editVarLocal.from.id
        }else{
          this.editVarLocal.from_id=null
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
   
    
     handleOk(){
       
       if (this.activeActionLocal=="Adaugă") 
        {
          this.saveAdd()
        }
        if (this.activeActionLocal=="Modifică") 
        {
          this.saveEdit()
        }
    },
    saveAdd(){
        
          this.showLoading=true
          const payLoad=this.editVarLocal
          payLoad.requestType="post"
          payLoad.requestUrl="/"+this.modelName+"/store"
          this.activeEditLocal=false
          this.activeActionLocal=""
          this.$store.dispatch("app/api_Request",payLoad)
                    .then(response=>{
                           // this.row=response  

                           this.idselectat=response.id.toString()
                           this.$emit("stored",response)              
                            this.editVarLocal={  
                                                     notificationtype_id:'',
                                                from_id:'',
                                                user_id:'',
                                                channel:'',
                                                email:'',
                                                telefon:'',
                                                title:'',
                                                subtitle:'',
                                                type:'',
                                                icon:'',
                                                avatar:'',
                                                link:'',
                                                category:'',
                                                read_at:'',
                                                

                                        }
                            this.showLoading=false
                             this.$bvToast.toast("Salvare efectuata cu success!", 
                                                 {
                                                    title: `Salvare cu succes! `,
                                                    variant:"success",
                                                    solid: false,
                                                    appendToast: true,
                                                    autoHideDelay: 3000,
                                                    toaster: "b-toaster-bottom-right",
                                                                      }) 
                            this.$emit("closed")
          
                       })
                      .catch(error => {
                        this.showLoading=true
                      })
         
    },
   
    aevClosed(){
        this.activeEditLocal=false
        this.editVarLocal={  
                                                     notificationtype_id:"",
                                                from_id:"",
                                                user_id:"",
                                                channel:"",
                                                email:"",
                                                telefon:"",
                                                title:"",
                                                subtitle:"",
                                                type:"",
                                                icon:"",
                                                avatar:"",
                                                link:"",
                                                category:"",
                                                read_at:"",
                                                

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
                                               // this.tblRecords=response
                                               this.idselectat=response.id.toString()
                                               this.$emit("stored",response) 
                                               this.activeEditLocal=false
                                               this.activeActionLocal=""
                                               this.editVarLocal={  
                                                     notificationtype_id:"",
                                                from_id:"",
                                                user_id:"",
                                                channel:"",
                                                email:"",
                                                telefon:"",
                                                title:"",
                                                subtitle:"",
                                                type:"",
                                                icon:"",
                                                avatar:"",
                                                link:"",
                                                category:"",
                                                read_at:"",
                                                

                                        }
                                         this.showLoading=false
                                         this.$bvToast.toast("Modificare efectuata cu success!", {
                                                                        title: "Modificare cu succes! ",
                                                                        variant:"success",
                                                                        solid: false,
                                                                        appendToast: true,
                                                                        autoHideDelay: 3000,
                                                                        toaster: "b-toaster-bottom-right",
                                                                      }) 
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

