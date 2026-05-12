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
             :title="activeActionLocal+' Țări'"
             v-model="activeEditLocal"
             @ok="handleOk"
             @cancel="aevClosed"
             >
             <br>

            
                <b-row >
                     
                            <b-col  cols="2"><div class="form-label-group">
                                  <b-form-input
                                    :readonly="activeActionLocal=='Vizualizează'"
                                    id="cod" 
                                    v-model="editVarLocal.cod"
                                    placeholder="Cod" 
                            
                                  />
                                  <label for="cod">Cod</label>
                                <!--   <b-form-valid-feedback tooltip>
                                    Looks good!
                                  </b-form-valid-feedback>
                                  <b-form-invalid-feedback tooltip>
                                    Please provide a Cod.
                                  </b-form-invalid-feedback> -->
                                </div>     
                            </b-col>
                            
                            <b-col  cols="2"><div class="form-label-group">
                                  <b-form-input
                                    :readonly="activeActionLocal=='Vizualizează'"
                                    id="denumire" 
                                    v-model="editVarLocal.denumire"
                                    placeholder="Denumire" 
                            
                                  />
                                  <label for="denumire">Denumire</label>
                                <!--   <b-form-valid-feedback tooltip>
                                    Looks good!
                                  </b-form-valid-feedback>
                                  <b-form-invalid-feedback tooltip>
                                    Please provide a Denumire.
                                  </b-form-invalid-feedback> -->
                                </div>     
                            </b-col>
                            
                            <b-col  cols="2"><div class="form-label-group">
                                  <b-form-input
                                    :readonly="activeActionLocal=='Vizualizează'"
                                    id="capitala" 
                                    v-model="editVarLocal.capitala"
                                    placeholder="Capitala" 
                           
                                  />
                                  <label for="capitala">Capitala</label>
                                <!--   <b-form-valid-feedback tooltip>
                                    Looks good!
                                  </b-form-valid-feedback>
                                  <b-form-invalid-feedback tooltip>
                                    Please provide a Capitala.
                                  </b-form-invalid-feedback> -->
                                </div>     
                            </b-col>
                            
                            <b-col  cols="2"><div class="form-label-group">
                                  <b-form-input
                                    :readonly="activeActionLocal=='Vizualizează'"
                                    id="forma_guvernare" 
                                    v-model="editVarLocal.forma_guvernare"
                                    placeholder="Forma de guvernare" 
                           
                                  />
                                  <label for="forma_guvernare">Forma de guvernare</label>
                                <!--   <b-form-valid-feedback tooltip>
                                    Looks good!
                                  </b-form-valid-feedback>
                                  <b-form-invalid-feedback tooltip>
                                    Please provide a Forma de guvernare.
                                  </b-form-invalid-feedback> -->
                                </div>     
                            </b-col>
                            
                            <b-col  cols="2"><div class="form-label-group">
                                  <b-form-input
                                    :readonly="activeActionLocal=='Vizualizează'"
                                    id="cod_tara_fiscal" 
                                    v-model="editVarLocal.cod_tara_fiscal"
                                    placeholder="Cod tara fiscal" 
                          
                                  />
                                  <label for="cod_tara_fiscal">Cod tara fiscal</label>
                                <!--   <b-form-valid-feedback tooltip>
                                    Looks good!
                                  </b-form-valid-feedback>
                                  <b-form-invalid-feedback tooltip>
                                    Please provide a Cod tara fiscal.
                                  </b-form-invalid-feedback> -->
                                </div>     
                            </b-col>
                            
                            <b-col  cols="2"><div class="form-label-group">
                                  <b-form-input
                                    :readonly="activeActionLocal=='Vizualizează'"
                                    id="cod_bnr" 
                                    v-model="editVarLocal.cod_bnr"
                                    placeholder="Cod BNR" 
                           
                                  />
                                  <label for="cod_bnr">Cod BNR</label>
                                <!--   <b-form-valid-feedback tooltip>
                                    Looks good!
                                  </b-form-valid-feedback>
                                  <b-form-invalid-feedback tooltip>
                                    Please provide a Cod BNR.
                                  </b-form-invalid-feedback> -->
                                </div>     
                            </b-col>
                            
                            <b-col  cols="2"><div class="form-label-group">
                                  <b-form-input
                                    :readonly="activeActionLocal=='Vizualizează'"
                                    id="valuta" 
                                    v-model="editVarLocal.valuta"
                                    placeholder="Valuta" 
                     
                                  />
                                  <label for="valuta">Valuta</label>
                                <!--   <b-form-valid-feedback tooltip>
                                    Looks good!
                                  </b-form-valid-feedback>
                                  <b-form-invalid-feedback tooltip>
                                    Please provide a Valuta.
                                  </b-form-invalid-feedback> -->
                                </div>     
                            </b-col>
                            
                            <b-col  cols="2"><div class="form-label-group">
                                  <b-form-input
                                    :readonly="activeActionLocal=='Vizualizează'"
                                    id="cod_sm" 
                                    v-model="editVarLocal.cod_sm"
                                    placeholder="Cod SM" 
                            <!--        :state="editVarLocal.cod_sm.length > 0" --!>
                                  />
                                  <label for="cod_sm">Cod SM</label>
                                <!--   <b-form-valid-feedback tooltip>
                                    Looks good!
                                  </b-form-valid-feedback>
                                  <b-form-invalid-feedback tooltip>
                                    Please provide a Cod SM.
                                  </b-form-invalid-feedback> -->
                                </div>     
                            </b-col>
                            
                            <b-col  cols="2">
                                    <b-form-checkbox
                                          v-model="editVarLocal.ue"
                                          class="custom-control-primary">
                                          UE
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
  name:"tariaev",
  data() {
    return {
        rutainapoiLocal:this.rutainapoi,
        activeEditLocal:false,
        activeActionLocal:this.activeAction,
        editVarLocal:this.editVar,
        nextTodoId: 2,
        modelName:"tari",
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
                                                     cod:'',
                                                denumire:'',
                                                capitala:'',
                                                forma_guvernare:'',
                                                cod_tara_fiscal:'',
                                                cod_bnr:'',
                                                valuta:'',
                                                cod_sm:'',
                                                ue:'',
                                                

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
                                                     cod:"",
                                                denumire:"",
                                                capitala:"",
                                                forma_guvernare:"",
                                                cod_tara_fiscal:"",
                                                cod_bnr:"",
                                                valuta:"",
                                                cod_sm:"",
                                                ue:"",
                                                

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
                                                     cod:"",
                                                denumire:"",
                                                capitala:"",
                                                forma_guvernare:"",
                                                cod_tara_fiscal:"",
                                                cod_bnr:"",
                                                valuta:"",
                                                cod_sm:"",
                                                ue:"",
                                                

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

