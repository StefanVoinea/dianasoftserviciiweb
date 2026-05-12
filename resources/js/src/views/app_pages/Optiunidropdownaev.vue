<template>
  <div class= "d-flex justify-content-center" >
   <b-modal id="Dianasoftmodelaev"  
             size="lg" 
             no-close-on-backdrop
             
             ok-variant="success"
             cancel-title="Cancel"              
             ok-title="Save"
             cancel-variant="warning"
             scrollable
             modal-class="modal-success"
             :title="activeActionLocal+' Optiuni dropdown'"
             v-model="activeEditLocal"
             @ok="handleOk"
             @cancel="aevClosed"
             >
             <br>

            
                <b-row >
                     
                            
                            <b-col  cols="6"><div class="form-label-group">
                                  <b-form-input
                                    :readonly="activeActionLocal=='Vizualizează'"
                                    id="field_name" 
                                    v-model="editVarLocal.field_name"
                                    placeholder="Field Name" 
                                    :state="editVarLocal.field_name.length > 0" 
                                  />
                                  <label for="field_name">Field Name</label>
                                <!--   <b-form-valid-feedback tooltip>
                                    Looks good!
                                  </b-form-valid-feedback>
                                  <b-form-invalid-feedback tooltip>
                                    Please provide a Field Name.
                                  </b-form-invalid-feedback> -->
                                </div>     
                            </b-col>
                            
                            <b-col  cols="6"><div class="form-label-group">
                                  <b-form-input
                                    :readonly="activeActionLocal=='Vizualizează'"
                                    id="field_option" 
                                    v-model="editVarLocal.field_option"
                                    placeholder="Field option" 
                                    :state="editVarLocal.field_option.length > 0" 
                                  />
                                  <label for="field_option">Field option</label>
                                <!--   <b-form-valid-feedback tooltip>
                                    Looks good!
                                  </b-form-valid-feedback>
                                  <b-form-invalid-feedback tooltip>
                                    Please provide a Field option.
                                  </b-form-invalid-feedback> -->
                                </div>     
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
  name:"optiunidropdownaev",
  data() {
    return {
        rutainapoiLocal:this.rutainapoi,
        activeEditLocal:false,
        activeActionLocal:this.activeAction,
        editVarLocal:this.editVar,
        nextTodoId: 2,
        modelName:"optiunidropdown",
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
                                                     comapny_id:'',
                                                field_name:'',
                                                field_option:'',
                                                

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
                                                     comapny_id:"",
                                                field_name:"",
                                                field_option:"",
                                                

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
                                                     comapny_id:"",
                                                field_name:"",
                                                field_option:"",
                                                

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

