<template>
   <validation-observer ref="simpleRules">
  <div class= "d-flex justify-content-center" >
   <b-modal id="Dianasoftmodelaev"  
             size="xl" 
             no-close-on-backdrop
             
             :hide-footer="activeActionLocal=='Vizualizează'"
             ok-variant="success"
             cancel-title="Cancel"              
             ok-title="Save"
             cancel-variant="warning"
             scrollable
             :cancel-disabled="activeActionLocal=='Vizualizează'"
             :ok-disabled="activeActionLocal=='Vizualizează'"
             modal-class="modal-success"
             :title="activeActionLocal+' Litigiu'"
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
                <b-col  cols="6">
                <b-row class="d-flex justify-content-center" >
                     
                            <b-col  cols="2">
                                <validation-provider
                                           #default="{ errors }"
                                          name="Numar dosar"
                                          rules="required"
                                        >
                                  <div class="form-label-group">      
                                  <b-form-input
                                    :readonly="activeActionLocal=='Vizualizează'"
                                    size="sm"
                                    @change="modificaNumarDosar"
                                    autocomplete="off"
                                    id="numar_dosar"
                                    v-model="editVarLocal.numar_dosar"
                                    placeholder="Numar dosar" 
                                    
                                  />
                                  <label for="numar_dosar">Numar dosar</label>
                                  <small class="text-danger">{{ errors[0] }}</small>
                                   </div>
                                       </validation-provider>
                              
                                    
                            </b-col>
                            
                           
                            
                          
                            
                            <b-col  cols="3">
                                <validation-provider
                                           #default="{ errors }"
                                          name="Avocatul apararii"
                                          rules=""
                                        >
                                  <div class="form-label-group">      
                                  <b-form-input
                                    :readonly="activeActionLocal=='Vizualizează'"
                                    size="sm"
                                    autocomplete="off"
                                    id="avocatul_apararii"
                                    v-model="editVarLocal.avocatul_apararii"
                                    placeholder="Avocatul apararii" 
                                    
                                  />
                                  <label for="avocatul_apararii">Avocatul apararii</label>
                                  <small class="text-danger">{{ errors[0] }}</small>
                                   </div>
                                       </validation-provider>
                              
                                    
                            </b-col>
                            
                            <b-col  cols="3">
                                <validation-provider
                                           #default="{ errors }"
                                          name="Avocatul acuzarii"
                                          rules=""
                                        >
                                  <div class="form-label-group">      
                                  <b-form-input
                                    :readonly="activeActionLocal=='Vizualizează'"
                                    size="sm"
                                    autocomplete="off"
                                    id="avocatul_acuzarii"
                                    v-model="editVarLocal.avocatul_acuzarii"
                                    placeholder="Avocatul acuzarii" 
                                    
                                  />
                                  <label for="avocatul_acuzarii">Avocatul acuzarii</label>
                                  <small class="text-danger">{{ errors[0] }}</small>
                                   </div>
                                       </validation-provider>
                              
                                    
                            </b-col>
                            
                           
                            
                            
                            <b-col  cols="2">
                                <validation-provider
                                           #default="{ errors }"
                                          name="Taxa de timbru"
                                          rules=""
                                        >
                                  <div class="form-label-group">      
                                  <b-form-input
                                    :readonly="activeActionLocal=='Vizualizează'"
                                    size="sm"
                                    autocomplete="off"
                                    id="taxa_de_timbru"
                                    v-model="editVarLocal.taxa_de_timbru"
                                    placeholder="Taxa de timbru" 
                                    
                                  />
                                  <label for="taxa_de_timbru">Taxa de timbru</label>
                                  <small class="text-danger">{{ errors[0] }}</small>
                                   </div>
                                       </validation-provider>
                              
                                    
                            </b-col>
                            
                            <b-col  cols="2">
                                <validation-provider
                                           #default="{ errors }"
                                          name="Cheltuieli de judecata"
                                          rules=""
                                        >
                                  <div class="form-label-group">      
                                  <b-form-input
                                    :readonly="activeActionLocal=='Vizualizează'"
                                    size="sm"
                                    autocomplete="off"
                                    id="cheltuieli_de_judecata"
                                    v-model="editVarLocal.cheltuieli_de_judecata"
                                    placeholder="Cheltuieli de judecata" 
                                    
                                  />
                                  <label for="cheltuieli_de_judecata">Cheltuieli de judecata</label>
                                  <small class="text-danger">{{ errors[0] }}</small>
                                   </div>
                                       </validation-provider>
                              
                                    
                            </b-col>
                            
                            </b-row>
                            <b-row class="d-flex justify-content-center" >

                              <b-col  cols="3">
                              <validation-provider
                                           #default="{ errors }"
                                          name="Data dosar"
                                          rules=""
                                        >
                                     <datacalendaristica 
                                          :readonly="true"
                                          v-show="activeActionLocal!='Adaugă'"
                                          id="data_dosar" 
                                          v-model="editVarLocal.data_dosar"
                                          name="data_dosar" 
                                          campDisplay="Data dosar"> 
                                      </datacalendaristica>
                                       <small class="text-danger">{{ errors[0] }}</small>
                                       </validation-provider>     
                            </b-col>
                            
                            <b-col  cols="3">
                                <validation-provider
                                           #default="{ errors }"
                                          name="Institutia"
                                          rules=""
                                        >
                                  <div class="form-label-group">      
                                  <b-form-input
                                    :readonly="true"
                                    v-show="activeActionLocal!='Adaugă'"
                                          
                                    size="sm"
                                    autocomplete="off"
                                    id="institutie"
                                    v-model="editVarLocal.institutie"
                                    placeholder="Institutia" 
                                    
                                  />
                                  <label v-show="activeActionLocal!='Adaugă'" for="institutie">Institutia</label>
                                  <small class="text-danger">{{ errors[0] }}</small>
                                   </div>
                                       </validation-provider>
                              
                                    
                            </b-col>
                            
                            <b-col  cols="2">
                                <validation-provider
                                           #default="{ errors }"
                                          name="Departament"
                                          rules=""
                                        >
                                  <div class="form-label-group">      
                                  <b-form-input
                                    :readonly="true"
                                    v-show="activeActionLocal!='Adaugă'"
                                          
                                    size="sm"
                                    autocomplete="off"
                                    id="departament"
                                    v-model="editVarLocal.departament"
                                    placeholder="Departament" 
                                    
                                  />
                                  <label v-show="activeActionLocal!='Adaugă'" for="departament">Departament</label>
                                  <small class="text-danger">{{ errors[0] }}</small>
                                   </div>
                                       </validation-provider>
                              
                                    
                            </b-col>
                            
                            <b-col  cols="2">
                                <validation-provider
                                           #default="{ errors }"
                                          name="Categorie caz"
                                          rules=""
                                        >
                                  <div class="form-label-group">      
                                  <b-form-input
                                    :readonly="true"
                                    v-show="activeActionLocal!='Adaugă'"
                                          
                                    size="sm"
                                    autocomplete="off"
                                    id="categorie_caz"
                                    v-model="editVarLocal.categorie_caz"
                                    placeholder="Categorie caz" 
                                    
                                  />
                                  <label v-show="activeActionLocal!='Adaugă'" for="categorie_caz">Categorie caz</label>
                                  <small class="text-danger">{{ errors[0] }}</small>
                                   </div>
                                       </validation-provider>
                              
                                    
                            </b-col>
                            
                            <b-col  cols="2">
                                <validation-provider
                                           #default="{ errors }"
                                          name="Stadiu procesual"
                                          rules=""
                                        >
                                  <div class="form-label-group">      
                                  <b-form-input
                                    :readonly="true"
                                    v-show="activeActionLocal!='Adaugă'"
                                          
                                    size="sm"
                                    autocomplete="off"
                                    id="stadiu_procesual"
                                    v-model="editVarLocal.stadiu_procesual"
                                    placeholder="Stadiu procesual" 
                                    
                                  />
                                  <label v-show="activeActionLocal!='Adaugă'" for="stadiu_procesual">Stadiu procesual</label>
                                  <small class="text-danger">{{ errors[0] }}</small>
                                   </div>
                                       </validation-provider>
                              
                                    
                            </b-col>

                           
                            </b-row>
                            </b-col>
                           <!--  <b-col  cols="2">
                                    
                                    <validation-provider
                                           #default="{ errors }"
                                          name="telefon_alerte"
                                          rules=""
                                        >
                                     <div class="form-label-group">
                                    
                                      <b-form-textarea
                                        id="telefon_alerte"
                                         v-model="editVarLocal.telefon_alerte"
                                        rows="3"
                                        placeholder="Nr telefon pentru alerte (separator ; )"
                                      />
                                      <label for="label-telefon_alerte">Nr telefon pentru alerte (separator ; )</label>
                                       <small class="text-danger">{{ errors[0] }}</small>
                                      </div>     
                                       </validation-provider>
                            </b-col> -->
                            <b-col  cols="2">
                                    
                                    <validation-provider
                                           #default="{ errors }"
                                          name="email_alerte"
                                          rules=""
                                        >
                                    
                                     <div class="form-label-group">
                                      <b-form-textarea
                                        id="observatii"
                                         v-model="editVarLocal.email_alerte"
                                        rows="3"
                                        placeholder="Email pentru alerte (separator ; )"
                                      />
                                      <label for="label-email_alerte">Email pentru alerte (separator ; )</label>
                                       <small class="text-danger">{{ errors[0] }}</small>
                                        </div>     
                                       </validation-provider>
                            </b-col>
                            <b-col  cols="4">
                                    
                                    <validation-provider
                                           #default="{ errors }"
                                          name="Observatii"
                                          rules=""
                                        >
                                    
                                     <div class="form-label-group">
                                      <b-form-textarea
                                        id="observatii"
                                         v-model="editVarLocal.observatii"
                                        rows="3"
                                        placeholder="Observatii"
                                      />
                                      <label for="label-observatii">Observatii</label>
                                       <small class="text-danger">{{ errors[0] }}</small>
                                    </div>     
                                       </validation-provider>
                            </b-col>
                            </b-row>
                            <b-row class="d-flex justify-content-center" >
                            <b-col class="border" cols="7">
                           <h4 v-show="activeActionLocal!='Adaugă'"> CAI DE ATAC </h4>
                            <b-row v-for="(caleatac,key) in editVarLocal.litigiicaleatac" :key="caleatac.id" class="d-flex justify-content-center" >
                            <b-col  cols="3">
                                 <datacalendaristica 
                                          :readonly="true"
                                          v-show="activeActionLocal!='Adaugă'"
                                          
                                          :id="'data_declarare_'+key" 
                                          v-model="caleatac.data_declarare"
                                          :name="'data_declarare_'+key" 
                                          campDisplay="Data declarare"> 
                                      </datacalendaristica>
                            </b-col>
                            <b-col cols="2">
                             <div class="form-label-group">      
                                  <b-form-input
                                    :readonly="true"
                                    v-show="activeActionLocal!='Adaugă'"
                                          
                                    size="sm"
                                    autocomplete="off"
                                    :id="'tip_cale_atac_'+key"
                                    v-model="caleatac.tip_cale_atac"
                                    placeholder="Tip cale atac" 
                                    
                                  />
                                  <label v-show="activeActionLocal!='Adaugă'" :for="'tip_cale_atac_'+key">Tip cale atac</label>
                                  </div>  
                            </b-col>
                            <b-col cols="7">
                             <div class="form-label-group">      
                                  <b-form-input
                                    :readonly="true"
                                    v-show="activeActionLocal!='Adaugă'"
                                          
                                    size="sm"
                                    autocomplete="off"
                                    :id="'parte_declaratoare_'+key"
                                    v-model="caleatac.parte_declaratoare"
                                    placeholder="Parte declaratoare" 
                                    
                                  />
                                  <label v-show="activeActionLocal!='Adaugă'" :for="'parte_declaratoare_'+key">Parte declaratoare</label>
                                </div>  
                            </b-col>
                            </b-row>
                            </b-col>
                            <b-col  class="border" cols="5">
                                
                                <h4>PARTI DOSAR</h4>
                                      <b-form-textarea
                                        :readonly="true"
                                         id="parti"
                                         v-model="editVarLocal.parti"
                                        rows="6"
                                        placeholder="Parti"
                                      />
                                     
                                
                                    
                            </b-col>
                            
                    
                </b-row>
                <br>
                <h4 v-show="activeActionLocal!='Adaugă'" >SEDINTE </h4>
                <b-row v-for="(sedinta,key) in editVarLocal.litigiisedinte" :key="sedinta.id" class=" border d-flex justify-content-center" >

                  <b-col  cols="12">
                  <br>
                    <b-row  class="d-flex justify-content-center" >
                     
                     <b-col cols="1">
                       <datacalendaristica 
                                              :readonly="true"
                                              v-show="activeActionLocal!='Adaugă'"
                                          
                                              :id="'data_sedinta_'+key"
                                              v-model="sedinta.data_sedinta"
                                              name="data_sedinta" 
                                              campDisplay="Data sedinta"> 
                                          </datacalendaristica>
                     </b-col>
                     <b-col  cols="1">
                      <div class="form-label-group">      
                                      <b-form-input
                                        :readonly="true"
                                        v-show="activeActionLocal!='Adaugă'"
                                          
                                        size="sm"
                                        autocomplete="off"
                                        :id="'ora_sedinta_'+key"
                                        v-model="sedinta.ora_sedinta"
                                        placeholder="Ora sedinta" 
                                        
                                      />
                                      <label v-show="activeActionLocal!='Adaugă'" :for="'ora_sedinta_'+key">Ora sedinta</label>
                                    </div>  
                     </b-col>
                     <b-col  cols="1">
                      <datacalendaristica 
                                              :readonly="true"
                                              v-show="activeActionLocal!='Adaugă'"
                                          
                                              :id="'data_pronuntare_'+key"
                                              v-model="sedinta.data_pronuntare"
                                              name="data_pronuntare" 
                                              campDisplay="Data pronuntare"> 
                                          </datacalendaristica>
                     </b-col>
                      <b-col  cols="1">
                      <div class="form-label-group">      
                                      <b-form-input
                                        :readonly="true"
                                        v-show="activeActionLocal!='Adaugă'"
                                          
                                        size="sm"
                                        autocomplete="off"
                                        :id="'complet_'+key"
                                        v-model="sedinta.complet"
                                        placeholder="Complet" 
                                        
                                      />
                                      <label v-show="activeActionLocal!='Adaugă'" :for="'complet_'+key">Complet</label>
                                    </div>  
                     </b-col>
                      <b-col  cols="2">
                      <div class="form-label-group">      
                                      <b-form-input
                                        :readonly="true"
                                        v-show="activeActionLocal!='Adaugă'"
                                          
                                        size="sm"
                                        autocomplete="off"
                                        :id="'document_sedinta_'+key"
                                        v-model="sedinta.document_sedinta"
                                        placeholder="Document sedinta" />
                                      <label v-show="activeActionLocal!='Adaugă'" :for="'document_sedinta_'+key">Document sedinta</label>
                                    </div>  
                     </b-col>
                     <b-col  cols="2">
                      <div class="form-label-group">      
                                      <b-form-input
                                        :readonly="true"
                                        v-show="activeActionLocal!='Adaugă'"
                                          
                                        size="sm"
                                        autocomplete="off"
                                        :id="'numar_document_'+key"
                                        v-model="sedinta.numar_document"
                                        placeholder="Numar document" />
                                      <label v-show="activeActionLocal!='Adaugă'" :for="'numar_document_'+key">Numar document</label>
                                    </div>  
                     </b-col>
                      <b-col  cols="1">
                      <datacalendaristica 
                                              :readonly="true"
                                              v-show="activeActionLocal!='Adaugă'"
                                          
                                              :id="'data_document_'+key"
                                              v-model="sedinta.data_document"
                                              name="data_document" 
                                              campDisplay="Data document"> 
                                          </datacalendaristica>
                     </b-col>
                       <b-col  cols="3">
                          <div class="form-label-group">      
                                  <b-form-input
                                    :readonly="true"
                                    v-show="activeActionLocal!='Adaugă'"
                                          
                                    size="sm"
                                    autocomplete="off"
                                    :id="'solutie_'+key"
                                    v-model="sedinta.solutie"
                                    placeholder="Solutie" />
                                  <label v-show="activeActionLocal!='Adaugă'" :for="'solutie_'+key">Solutie</label>
                                </div>  
                        </b-col>
                    </b-row>

                    <b-row  class="d-flex justify-content-center" >
                        <b-col  cols="12">
                        <p v-show="activeActionLocal!='Adaugă'">Solutie sumar </p>
                            <b-form-textarea
                                                :id="'solutie_sumar_'+key"
                                                :readonly="true"
                                                v-show="activeActionLocal!='Adaugă'"
                                          
                                                 v-model="sedinta.solutie_sumar"
                                                rows="8"
                                                placeholder="Solutie sumar"/>
                        </b-col>
                    </b-row>
                    <br>
                    </b-col>
                </b-row>
             <br><br><br>
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
  name:"litigiuaev",
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
        modelName:"litigiu",
        litigii:[],
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
    modificaNumarDosar(){
        if(this.editVarLocal.numar_dosar){

          this.showLoading=true
          const payLoad=this.editVarLocal
          payLoad.requestType="post"
          payLoad.requestUrl="/"+this.modelName+"/preiaNumarDosar"
         
          this.$store.dispatch("app/api_Request",payLoad)
                    .then(response=>{
                            if(response.length==0){
                                this.$bvToast.toast("Nu am preluat niciun dosar!", 
                                                 {
                                                    title: `Preluare cu succes! `,
                                                    variant:"warning",
                                                    solid: false,
                                                    appendToast: true,
                                                    autoHideDelay: 3000,
                                                    toaster: "b-toaster-bottom-right",
                                                })
                            }else{

                                    this.editVarLocal=response[0]
                                    this.litigii=response
                                    this.$bvToast.toast("Am preluat "+response.length+" dosare cu succes!", 
                                                 {
                                                    title: `Preluare cu succes! `,
                                                    variant:"success",
                                                    solid: false,
                                                    appendToast: true,
                                                    autoHideDelay: 3000,
                                                    toaster: "b-toaster-bottom-right",
                                                
                                               }) 
                            }        
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
          const payLoad=Object.assign({},this.editVarLocal)
          payLoad.requestType="post"
          payLoad.requestUrl="/"+this.modelName+"/store"
          payLoad.litigii=this.litigii
        
          this.$store.dispatch("app/api_Request",payLoad)
                    .then(response=>{
                           
                            this.editVarLocal={  
                                                     numar_dosar:'',
                                                numar_vechi:'',
                                                data_dosar:'',
                                                institutie:'',
                                                departament:'',
                                                categorie_caz:'',
                                                stadiu_procesual:'',
                                                avocatul_apararii:'',
                                                avocatul_acuzarii:'',
                                                observatii:'',
                                                status:'',
                                                taxa_de_timbru:'',
                                                cheltuieli_de_judecata:'',
                                                parti:'',
                                                litigiicaleatac:[{}],
                                                  litigiiparti:[{}],
                                                  litigiisedinte:[{}],
                                                  litigii:[{}],

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
   
    aevClosed(){
       this.idselectat=null
      this.selectedID=""
        this.activeEditLocal=false
        this.editVarLocal={  
                                                     numar_dosar:"",
                                                numar_vechi:"",
                                                data_dosar:"",
                                                institutie:"",
                                                departament:"",
                                                categorie_caz:"",
                                                stadiu_procesual:"",
                                                avocatul_apararii:"",
                                                avocatul_acuzarii:"",
                                                observatii:"",
                                                status:"",
                                                taxa_de_timbru:"",
                                                cheltuieli_de_judecata:"",
                                                parti:"",
                                                litigiicaleatac:[{}],
                                                  litigiiparti:[{}],
                                                  litigiisedinte:[{}],
                                                  litigii:[{}],

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
                                                     numar_dosar:"",
                                                numar_vechi:"",
                                                data_dosar:"",
                                                institutie:"",
                                                departament:"",
                                                categorie_caz:"",
                                                stadiu_procesual:"",
                                                avocatul_apararii:"",
                                                avocatul_acuzarii:"",
                                                observatii:"",
                                                status:"",
                                                taxa_de_timbru:"",
                                                cheltuieli_de_judecata:"",
                                                parti:"",
                                                litigiicaleatac:[{}],
                                                  litigiiparti:[{}],
                                                  litigiisedinte:[{}],
                                                  litigii:[{}],

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

