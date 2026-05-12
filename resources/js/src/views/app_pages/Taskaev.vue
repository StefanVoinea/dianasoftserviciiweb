<template>
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
             modal-class="modal-success"
             :title="activeActionLocal+' Task'"
             v-model="activeEditLocal"
             @ok="handleOk"
             @cancel="aevClosed"
             >
             <br>

            
                <b-row >
                     
                            <b-col  cols="2">
                                     <selectoneuser  @change="modificaAssignedBy"
                                                      labelDisplay="Assigned By"
                                                       v-model="editVarLocal.assignedby"
                                                      :readonly="activeActionLocal=='Vizualizează'"
                                                    >
                                     </selectoneuser>      
                            </b-col>
                            
                            <b-col  cols="2">
                                     <selectoneuser  @change="modificaAssignedTo"
                                                      labelDisplay="Assigned To"
                                                       v-model="editVarLocal.assignedto"
                                                      :readonly="activeActionLocal=='Vizualizează'"
                                                    >
                                     </selectoneuser>      
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
                            
                            <b-col  cols="2">
                                     <div class="form-label-group">
                                      <b-form-textarea
                                        id=".description"
                                         v-model="editVarLocal.description"
                                        rows="3"
                                        placeholder=".Descriere"
                                      />
                                      <label for="label-.description">.Descriere</label>
                                    </div>     
                            </b-col>
                            
                            <b-col  cols="2">
                                     <datasiora 
                                          :readonly="activeActionLocal=='Vizualizează'"
                                          id="duedate" 
                                          v-model="editVarLocal.duedate"
                                          name="duedate" 
                                          campDisplay="Termen executare"> 
                                      </datasiora>     
                            </b-col>
                            
                            <b-col  cols="2"><div class="form-label-group">
                                  <b-form-input
                                    :readonly="activeActionLocal=='Vizualizează'"
                                    id="tags"
                                    v-model="editVarLocal.tags"
                                    placeholder="Tags" 
                                    
                                  />
                                  <label for="tags">Tags</label>
                                <!--   <b-form-valid-feedback tooltip>
                                    Looks good!
                                  </b-form-valid-feedback>
                                  <b-form-invalid-feedback tooltip>
                                    Please provide a Tags.
                                  </b-form-invalid-feedback> -->
                                </div>     
                            </b-col>
                            
                            <b-col  cols="2">
                                     <datasiora 
                                          :readonly="activeActionLocal=='Vizualizează'"
                                          id="completed_at" 
                                          v-model="editVarLocal.completed_at"
                                          name="completed_at" 
                                          campDisplay="Data executarii"> 
                                      </datasiora>     
                            </b-col>
                            
                            <b-col  cols="2">
                                    <b-form-checkbox
                                          v-model="editVarLocal.isCompleted"
                                          class="custom-control-primary">
                                          Executat
                                        </b-form-checkbox>     
                            </b-col>
                            
                            <b-col  cols="2">
                                    <b-form-checkbox
                                          v-model="editVarLocal.isDeleted"
                                          class="custom-control-primary">
                                          Sters
                                        </b-form-checkbox>     
                            </b-col>
                            
                            <b-col  cols="2">
                                    <b-form-checkbox
                                          v-model="editVarLocal.isImportant"
                                          class="custom-control-primary">
                                          Important
                                        </b-form-checkbox>     
                            </b-col>
                            
                            <b-col  cols="2">
                                     <selectoneuser  
                                                      @change="modificaCompletedBy"
                                                      labelDisplay="Executat de catre"
                                                       v-model="editVarLocal.completedby"
                                                      :readonly="activeActionLocal=='Vizualizează'"
                                                    >
                                     </selectoneuser>      
                            </b-col>
                            
                    
                </b-row>
                
              <br><br><br><br><br><br><br><br><br><br><br>
      
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
  name:"taskaev",
  data() {
    return {
        rutainapoiLocal:this.rutainapoi,
        activeEditLocal:false,
        activeActionLocal:this.activeAction,
        editVarLocal:this.editVar,
        nextTodoId: 2,
        modelName:"task",
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
    modificaCompletedBy(){
      
       if(this.editVarLocal.completedby){

        this.editVarLocal.completedby_id=this.editVarLocal.completedby.id
        }else{
          this.editVarLocal.completedby_id=null
        }
      
    },
    modificaAssignedTo(){
      
       if(this.editVarLocal.assignedto){

        this.editVarLocal.assignedto_id=this.editVarLocal.assignedto.id
        }else{
          this.editVarLocal.assignedto_id=null
        }
      
    },
     modificaAssignedBy(){
      
       if(this.editVarLocal.assignedby){

        this.editVarLocal.assignedby_id=this.editVarLocal.assignedby.id
        }else{
          this.editVarLocal.assignedby_id=null
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
                         
                           this.$emit("stored","")              
                            this.editVarLocal={  
                                                     assignedby_id:'',
                                                assignedto_id:'',
                                                title:'',
                                                description:'',
                                                duedate:'',
                                                tags:'',
                                                completed_at:'',
                                                isCompleted:'',
                                                isDeleted:'',
                                                isImportant:'',
                                                completedby_id:'',
                                                

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
                                                     assignedby_id:"",
                                                assignedto_id:"",
                                                title:"",
                                                description:"",
                                                duedate:"",
                                                tags:"",
                                                completed_at:"",
                                                isCompleted:"",
                                                isDeleted:"",
                                                isImportant:"",
                                                completedby_id:"",
                                                

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
                                               
                                               this.$emit("stored","") 
                                               this.activeEditLocal=false
                                               this.activeActionLocal=""
                                               this.editVarLocal={  
                                                     assignedby_id:"",
                                                assignedto_id:"",
                                                title:"",
                                                description:"",
                                                duedate:"",
                                                tags:"",
                                                completed_at:"",
                                                isCompleted:"",
                                                isDeleted:"",
                                                isImportant:"",
                                                completedby_id:"",
                                                

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

