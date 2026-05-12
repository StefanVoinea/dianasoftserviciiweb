<template>
  <div class= "d-flex justify-content-center" >
   <b-modal id="Dianasoftmodelaev"  
          
             no-close-on-backdrop
             
             ok-variant="success"
             cancel-title="Cancel"              
             ok-title="Save"
             cancel-variant="warning"
             scrollable
             modal-class="modal-success"
             :title="activeActionLocal+' Menu Options'"
             v-model="activeEditLocal"
             @ok="handleOk"
             @cancel="aevClosed"
             >
             <br>
            <br>
            
                <b-row >
                     
                            <b-col  cols="12"><div class="form-label-group">
                                  <b-form-input
                                    :readonly="activeActionLocal=='Vizualizează'"
                                    id="name" 
                                    v-model="editVarLocal.name"
                                    placeholder="Name" 
                                    
                                  />
                                  <label for="name">Name</label>
                                <!--   <b-form-valid-feedback tooltip>
                                    Looks good!
                                  </b-form-valid-feedback>
                                  <b-form-invalid-feedback tooltip>
                                    Please provide a Name.
                                  </b-form-invalid-feedback> -->
                                </div>     
                            </b-col>
                          </b-row >
                            <br>
                          <b-row >   
                            <b-col  cols="12"><div class="form-label-group">
                                  <b-form-input
                                    :readonly="activeActionLocal=='Vizualizează'"
                                    id="url" 
                                    v-model="editVarLocal.url"
                                    placeholder="URL" 
                                    
                                  />
                                  <label for="url">URL</label>
                                <!--   <b-form-valid-feedback tooltip>
                                    Looks good!
                                  </b-form-valid-feedback>
                                  <b-form-invalid-feedback tooltip>
                                    Please provide a URL.
                                  </b-form-invalid-feedback> -->
                                </div>     
                            </b-col>
                            
                            </b-row >
                            <br>
                          <b-row >  
                            <b-col  cols="12"><div class="form-label-group">
                                  <b-form-input
                                    :readonly="activeActionLocal=='Vizualizează'"
                                    id="slug" 
                                    v-model="editVarLocal.slug"
                                    placeholder="Slug" 
                                    
                                  />
                                  <label for="slug">Slug</label>
                                <!--   <b-form-valid-feedback tooltip>
                                    Looks good!
                                  </b-form-valid-feedback>
                                  <b-form-invalid-feedback tooltip>
                                    Please provide a Slug.
                                  </b-form-invalid-feedback> -->
                                </div>     
                            </b-col>
                            </b-row >
                            <br>
                          <b-row >  
                            <b-col  cols="12"><div class="form-label-group">
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
                             </b-row >
                            <br>
                          <b-row >  
                            <b-col  cols="12"><div class="form-label-group">
                                  <b-form-input
                                    :readonly="activeActionLocal=='Vizualizează'"
                                    id="tag" 
                                    v-model="editVarLocal.tag"
                                    placeholder="Tag" 
                                    
                                  />
                                  <label for="tag">Tag</label>
                                <!--   <b-form-valid-feedback tooltip>
                                    Looks good!
                                  </b-form-valid-feedback>
                                  <b-form-invalid-feedback tooltip>
                                    Please provide a Tag.
                                  </b-form-invalid-feedback> -->
                                </div>     
                            </b-col>
                             </b-row >
                            <br>
                          <b-row >  
                            <b-col  cols="12"><div class="form-label-group">
                                  <b-form-input
                                    :readonly="activeActionLocal=='Vizualizează'"
                                    id="tagcolor" 
                                    v-model="editVarLocal.tagcolor"
                                    placeholder="Tag color" 
                                    
                                  />
                                  <label for="tagcolor">Tag color</label>
                                <!--   <b-form-valid-feedback tooltip>
                                    Looks good!
                                  </b-form-valid-feedback>
                                  <b-form-invalid-feedback tooltip>
                                    Please provide a Tag color.
                                  </b-form-invalid-feedback> -->
                                </div>     
                            </b-col>
                             </b-row >
                            <br>
                          <b-row >  
                            <b-col  cols="12"><div class="form-label-group">
                                  <b-form-input
                                    :readonly="activeActionLocal=='Vizualizează'"
                                    id="i18n" 
                                    v-model="editVarLocal.i18n"
                                    placeholder="Internalization" 
                                    
                                  />
                                  <label for="i18n">Internalization</label>
                                <!--   <b-form-valid-feedback tooltip>
                                    Looks good!
                                  </b-form-valid-feedback>
                                  <b-form-invalid-feedback tooltip>
                                    Please provide a Internalization.
                                  </b-form-invalid-feedback> -->
                                </div>     
                            </b-col>
                             </b-row >
                            <br>
                          <b-row >  
                            <b-col  cols="12">
                                    <b-form-checkbox
                                          v-model="editVarLocal.dropdown"
                                          class="custom-control-primary">
                                          Dropdown group
                                        </b-form-checkbox>     
                            </b-col>
                             </b-row >
                            <br>
                          <b-row >  
                            <b-col  cols="12"><div class="form-label-group">
                                  <b-form-input
                                    :readonly="activeActionLocal=='Vizualizează'"
                                    id="parent" 
                                    v-model="editVarLocal.parent"
                                    placeholder="Parent name" 
                                    
                                  />
                                  <label for="parent">Parent name</label>
                                <!--   <b-form-valid-feedback tooltip>
                                    Looks good!
                                  </b-form-valid-feedback>
                                  <b-form-invalid-feedback tooltip>
                                    Please provide a Parent name.
                                  </b-form-invalid-feedback> -->
                                </div>     
                            </b-col>
                             </b-row >
                            <br>
                          <b-row >  
                            <b-col  cols="12"><div class="form-label-group">
                                  <b-form-input
                                    :readonly="activeActionLocal=='Vizualizează'"
                                    id="position1" 
                                    v-model="editVarLocal.position1"
                                    placeholder="Position 1" 
                                    
                                  />
                                  <label for="position1">Position 1</label>
                                <!--   <b-form-valid-feedback tooltip>
                                    Looks good!
                                  </b-form-valid-feedback>
                                  <b-form-invalid-feedback tooltip>
                                    Please provide a Position 1.
                                  </b-form-invalid-feedback> -->
                                </div>     
                            </b-col>
                             </b-row >
                            <br>
                          <b-row >  
                            <b-col  cols="12"><div class="form-label-group">
                                  <b-form-input
                                    :readonly="activeActionLocal=='Vizualizează'"
                                    id="position2" 
                                    v-model="editVarLocal.position2"
                                    placeholder="Position 2" 
                                    
                                  />
                                  <label for="position2">Position 2</label>
                                <!--   <b-form-valid-feedback tooltip>
                                    Looks good!
                                  </b-form-valid-feedback>
                                  <b-form-invalid-feedback tooltip>
                                    Please provide a Position 2.
                                  </b-form-invalid-feedback> -->
                                </div>     
                            </b-col>
                             </b-row >
                            <br>
                          <b-row >  
                            <b-col  cols="12">
                                    <b-form-checkbox
                                          v-model="editVarLocal.isdisabled"
                                          class="custom-control-primary">
                                          Disabled
                                        </b-form-checkbox>     
                            </b-col>
                            
                    
                </b-row>
                
              
      
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
  name:"dianasoftmenuoptionaev",
  data() {
    return {
        rutainapoiLocal:this.rutainapoi,
        activeEditLocal:false,
        activeActionLocal:this.activeAction,
        editVarLocal:this.editVar,
        nextTodoId: 2,
        modelName:"dianasoftmenuoption",
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
                                                     name:'',
                                                url:'',
                                                slug:'',
                                                icon:'',
                                                tag:'',
                                                tagcolor:'',
                                                i18n:'',
                                                dropdown:'',
                                                parent:'',
                                                position1:'',
                                                position2:'',
                                                isdisabled:'',
                                                

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
                                                     name:"",
                                                url:"",
                                                slug:"",
                                                icon:"",
                                                tag:"",
                                                tagcolor:"",
                                                i18n:"",
                                                dropdown:"",
                                                parent:"",
                                                position1:"",
                                                position2:"",
                                                isdisabled:"",
                                                

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
                                                     name:"",
                                                url:"",
                                                slug:"",
                                                icon:"",
                                                tag:"",
                                                tagcolor:"",
                                                i18n:"",
                                                dropdown:"",
                                                parent:"",
                                                position1:"",
                                                position2:"",
                                                isdisabled:"",
                                                

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

