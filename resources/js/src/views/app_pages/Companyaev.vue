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
             :title="activeActionLocal+' Societăți'"
             v-model="activeEditLocal"
             @ok="handleOk"
             @cancel="aevClosed"
             >
             <br>

            
                <b-row >
                     
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
                                    id="cui" 
                                    v-model="editVarLocal.cui"
                                    placeholder="CUI" 
                                    
                                  />
                                  <label for="cui">CUI</label>
                                <!--   <b-form-valid-feedback tooltip>
                                    Looks good!
                                  </b-form-valid-feedback>
                                  <b-form-invalid-feedback tooltip>
                                    Please provide a CUI.
                                  </b-form-invalid-feedback> -->
                                </div>     
                            </b-col>
                            
                            <b-col  cols="2"><div class="form-label-group">
                                  <b-form-input
                                    :readonly="activeActionLocal=='Vizualizează'"
                                    id="regcom" 
                                    v-model="editVarLocal.regcom"
                                    placeholder="Registrul comertului" 
                                    
                                  />
                                  <label for="regcom">Registrul comertului</label>
                                <!--   <b-form-valid-feedback tooltip>
                                    Looks good!
                                  </b-form-valid-feedback>
                                  <b-form-invalid-feedback tooltip>
                                    Please provide a Registrul comertului.
                                  </b-form-invalid-feedback> -->
                                </div>     
                            </b-col>
                            
                            <b-col  cols="2"><div class="form-label-group">
                                  <b-form-input
                                    :readonly="activeActionLocal=='Vizualizează'"
                                    id="adresa" 
                                    v-model="editVarLocal.adresa"
                                    placeholder="Adresa" 
                                    
                                  />
                                  <label for="adresa">Adresa</label>
                                <!--   <b-form-valid-feedback tooltip>
                                    Looks good!
                                  </b-form-valid-feedback>
                                  <b-form-invalid-feedback tooltip>
                                    Please provide a Adresa.
                                  </b-form-invalid-feedback> -->
                                </div>     
                            </b-col>
                            
                            <b-col  cols="2"><div class="form-label-group">
                                  <b-form-input
                                    :readonly="activeActionLocal=='Vizualizează'"
                                    id="localitate" 
                                    v-model="editVarLocal.localitate"
                                    placeholder="Localitate" 
                                    
                                  />
                                  <label for="localitate">Localitate</label>
                                <!--   <b-form-valid-feedback tooltip>
                                    Looks good!
                                  </b-form-valid-feedback>
                                  <b-form-invalid-feedback tooltip>
                                    Please provide a Localitate.
                                  </b-form-invalid-feedback> -->
                                </div>     
                            </b-col>
                            
                            <b-col  cols="2"><div class="form-label-group">
                                  <b-form-input
                                    :readonly="activeActionLocal=='Vizualizează'"
                                    id="judet" 
                                    v-model="editVarLocal.judet"
                                    placeholder="Judet" 
                                    
                                  />
                                  <label for="judet">Judet</label>
                                <!--   <b-form-valid-feedback tooltip>
                                    Looks good!
                                  </b-form-valid-feedback>
                                  <b-form-invalid-feedback tooltip>
                                    Please provide a Judet.
                                  </b-form-invalid-feedback> -->
                                </div>     
                            </b-col>
                            </b-row>
                            <br>
                            <b-row>
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
                                    id="email" 
                                    v-model="editVarLocal.email"
                                    placeholder="E-mail" 
                                    
                                  />
                                  <label for="email">E-mail</label>
                                <!--   <b-form-valid-feedback tooltip>
                                    Looks good!
                                  </b-form-valid-feedback>
                                  <b-form-invalid-feedback tooltip>
                                    Please provide a E-mail.
                                  </b-form-invalid-feedback> -->
                                </div>     
                            </b-col>
                            
                            <b-col  cols="2"><div class="form-label-group">
                                  <b-form-input
                                    :readonly="activeActionLocal=='Vizualizează'"
                                    id="capital_social" 
                                    v-model="editVarLocal.capital_social"
                                    placeholder="Capital social" 
                                    
                                  />
                                  <label for="capital_social">Capital social</label>
                                <!--   <b-form-valid-feedback tooltip>
                                    Looks good!
                                  </b-form-valid-feedback>
                                  <b-form-invalid-feedback tooltip>
                                    Please provide a Capital social.
                                  </b-form-invalid-feedback> -->
                                </div>     
                            </b-col>
                            
                            <b-col  cols="2"><div class="form-label-group">
                                  <b-form-input
                                    :readonly="activeActionLocal=='Vizualizează'"
                                    id="banca" 
                                    v-model="editVarLocal.banca"
                                    placeholder="Banca" 
                                    
                                  />
                                  <label for="banca">Banca</label>
                                <!--   <b-form-valid-feedback tooltip>
                                    Looks good!
                                  </b-form-valid-feedback>
                                  <b-form-invalid-feedback tooltip>
                                    Please provide a Banca.
                                  </b-form-invalid-feedback> -->
                                </div>     
                            </b-col>
                            
                            <b-col  cols="2"><div class="form-label-group">
                                  <b-form-input
                                    :readonly="activeActionLocal=='Vizualizează'"
                                    id="cont" 
                                    v-model="editVarLocal.cont"
                                    placeholder="Cont" 
                                    
                                  />
                                  <label for="cont">Cont</label>
                                <!--   <b-form-valid-feedback tooltip>
                                    Looks good!
                                  </b-form-valid-feedback>
                                  <b-form-invalid-feedback tooltip>
                                    Please provide a Cont.
                                  </b-form-invalid-feedback> -->
                                </div>     
                            </b-col>
                            
                            <b-col  cols="2"><div class="form-label-group">
                                  <b-form-input
                                    :readonly="activeActionLocal=='Vizualizează'"
                                    id="plan_tarifar" 
                                    v-model="editVarLocal.plan_tarifar"
                                    placeholder="Plan tarifar" 
                                    
                                  />
                                  <label for="plan_tarifar">Plan tarifar</label>
                                <!--   <b-form-valid-feedback tooltip>
                                    Looks good!
                                  </b-form-valid-feedback>
                                  <b-form-invalid-feedback tooltip>
                                    Please provide a Plan tarifar.
                                  </b-form-invalid-feedback> -->
                                </div>     
                            </b-col>
                            </b-row>
                            <br>
                            <b-row>
                            <b-col  cols="2"><div class="form-label-group">
                                  <b-form-input
                                    :readonly="activeActionLocal=='Vizualizează'"
                                    id="cod_caen" 
                                    v-model="editVarLocal.cod_caen"
                                    placeholder="Cod caen" 
                                    
                                  />
                                  <label for="cod_caen">Cod caen</label>
                                <!--   <b-form-valid-feedback tooltip>
                                    Looks good!
                                  </b-form-valid-feedback>
                                  <b-form-invalid-feedback tooltip>
                                    Please provide a Cod caen.
                                  </b-form-invalid-feedback> -->
                                </div>     
                            </b-col>
                            
                            <b-col  cols="2"><div class="form-label-group">
                                  <b-form-input
                                    :readonly="activeActionLocal=='Vizualizează'"
                                    id="email_factura" 
                                    v-model="editVarLocal.email_factura"
                                    placeholder="Email factura" 
                                    
                                  />
                                  <label for="email_factura">Email factura</label>
                                <!--   <b-form-valid-feedback tooltip>
                                    Looks good!
                                  </b-form-valid-feedback>
                                  <b-form-invalid-feedback tooltip>
                                    Please provide a Email factura.
                                  </b-form-invalid-feedback> -->
                                </div>     
                            </b-col>
                            
                            <b-col  cols="2"><div class="form-label-group">
                                  <b-form-input
                                    :readonly="activeActionLocal=='Vizualizează'"
                                    id="email_restante" 
                                    v-model="editVarLocal.email_restante"
                                    placeholder="Email restante" 
                                    
                                  />
                                  <label for="email_restante">Email restante</label>
                                <!--   <b-form-valid-feedback tooltip>
                                    Looks good!
                                  </b-form-valid-feedback>
                                  <b-form-invalid-feedback tooltip>
                                    Please provide a Email restante.
                                  </b-form-invalid-feedback> -->
                                </div>     
                            </b-col>
                            
                            <b-col  cols="2"><div class="form-label-group">
                                  <b-form-input
                                    :readonly="activeActionLocal=='Vizualizează'"
                                    id="slug" 
                                    v-model="editVarLocal.slug"
                                    placeholder="Prescurtare denumire" 
                                    
                                  />
                                  <label for="slug">Prescurtare denumire</label>
                                <!--   <b-form-valid-feedback tooltip>
                                    Looks good!
                                  </b-form-valid-feedback>
                                  <b-form-invalid-feedback tooltip>
                                    Please provide a Prescurtare denumire.
                                  </b-form-invalid-feedback> -->
                                </div>     
                            </b-col>
                            
                            <b-col  cols="2"><div class="form-label-group">
                                  <b-form-input
                                    :readonly="activeActionLocal=='Vizualizează'"
                                    id="operator_gdpr" 
                                    v-model="editVarLocal.operator_gdpr"
                                    placeholder="Operator GDPR" 
                                    
                                  />
                                  <label for="operator_gdpr">Operator GDPR</label>
                                <!--   <b-form-valid-feedback tooltip>
                                    Looks good!
                                  </b-form-valid-feedback>
                                  <b-form-invalid-feedback tooltip>
                                    Please provide a Operator GDPR.
                                  </b-form-invalid-feedback> -->
                                </div>     
                            </b-col>
                            
                            <b-col  cols="2"><div class="form-label-group">
                                  <b-form-input
                                    :readonly="activeActionLocal=='Vizualizează'"
                                    id="nrautorizatie" 
                                    v-model="editVarLocal.nrautorizatie"
                                    placeholder="Nr autorizatie" 
                                    
                                  />
                                  <label for="nrautorizatie">Nr autorizatie</label>
                                <!--   <b-form-valid-feedback tooltip>
                                    Looks good!
                                  </b-form-valid-feedback>
                                  <b-form-invalid-feedback tooltip>
                                    Please provide a Nr autorizatie.
                                  </b-form-invalid-feedback> -->
                                </div>     
                            </b-col>
                            </b-row>
                            <br>
                            <b-row>
                            <b-col  cols="2"><div class="form-label-group">
                                  <b-form-input
                                    :readonly="activeActionLocal=='Vizualizează'"
                                    id="cerber_url" 
                                    v-model="editVarLocal.cerber_url"
                                    placeholder="Cerber URL" 
                                    
                                  />
                                  <label for="cerber_url">Cerber URL</label>
                                <!--   <b-form-valid-feedback tooltip>
                                    Looks good!
                                  </b-form-valid-feedback>
                                  <b-form-invalid-feedback tooltip>
                                    Please provide a Cerber URL.
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
  name:"companyaev",
  data() {
    return {
        rutainapoiLocal:this.rutainapoi,
        activeEditLocal:false,
        activeActionLocal:this.activeAction,
        editVarLocal:this.editVar,
        nextTodoId: 2,
        modelName:"company",
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
                                                     denumire:'',
                                                cui:'',
                                                regcom:'',
                                                adresa:'',
                                                localitate:'',
                                                judet:'',
                                                telefon:'',
                                                email:'',
                                                capital_social:'',
                                                banca:'',
                                                cont:'',
                                                plan_tarifar:'',
                                                cod_caen:'',
                                                email_factura:'',
                                                email_restante:'',
                                                slug:'',
                                                operator_gdpr:'',
                                                nrautorizatie:'',
                                                cerber_url:'',
                                                

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
                                                     denumire:"",
                                                cui:"",
                                                regcom:"",
                                                adresa:"",
                                                localitate:"",
                                                judet:"",
                                                telefon:"",
                                                email:"",
                                                capital_social:"",
                                                banca:"",
                                                cont:"",
                                                plan_tarifar:"",
                                                cod_caen:"",
                                                email_factura:"",
                                                email_restante:"",
                                                slug:"",
                                                operator_gdpr:"",
                                                nrautorizatie:"",
                                                cerber_url:"",
                                                

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
                                                     denumire:"",
                                                cui:"",
                                                regcom:"",
                                                adresa:"",
                                                localitate:"",
                                                judet:"",
                                                telefon:"",
                                                email:"",
                                                capital_social:"",
                                                banca:"",
                                                cont:"",
                                                plan_tarifar:"",
                                                cod_caen:"",
                                                email_factura:"",
                                                email_restante:"",
                                                slug:"",
                                                operator_gdpr:"",
                                                nrautorizatie:"",
                                                cerber_url:"",
                                                

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

