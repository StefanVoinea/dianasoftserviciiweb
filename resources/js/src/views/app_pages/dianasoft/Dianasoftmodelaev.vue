<template>
  <div class= "d-flex justify-content-center" >
    <b-modal id="Dianasoftmodelaev"  
             scrollable
             size="xl" 
             no-close-on-backdrop
             
             ok-variant="success"
             cancel-title="Cancel"              
             ok-title="Save"
             cancel-variant="warning"
             modal-class="modal-success"
             :title="activeActionLocal+' diansoftmodel'" 
             v-model="activeEditLocal"
             @ok="handleOk"
             @cancel="aevClosed"
             >
             <div>
             <br>
        <b-row >
        <b-col cols="3">
            <div class="form-label-group">
              <b-form-input
                :readonly="activeActionLocal=='Vizualizează'"
                id="modelName"
                v-model="editVarLocal.model_name"
                placeholder="Model name"
                :state="editVarLocal.model_name.length > 0"
              />
              <label for="modelName">Model name</label>
            <!--   <b-form-valid-feedback tooltip>
                Looks good!
              </b-form-valid-feedback>
              <b-form-invalid-feedback tooltip>
                Please provide a Model name.
              </b-form-invalid-feedback> -->
      </div>
        </b-col>
        <b-col cols="3">
           <div class="form-label-group">
              <b-form-input
                :readonly="activeActionLocal=='Vizualizează'"
                id="displayName"
                v-model="editVarLocal.display_name"
                placeholder="Display name"
                :state="editVarLocal.display_name.length > 0"
              />
              <label for="tableName">Display name</label>
            <!--   <b-form-valid-feedback tooltip>
                Looks good!
              </b-form-valid-feedback>
              <b-form-invalid-feedback tooltip>
                Please provide a Display name.
              </b-form-invalid-feedback> -->
      </div>
        </b-col>
         <b-col cols="3">
          <div class="form-label-group">
              <b-form-input
                :readonly="activeActionLocal=='Vizualizează'"
                id="tableName"
                v-model="editVarLocal.table_name"
                placeholder="Table name"
                :state="editVarLocal.table_name.length > 0"
              />
              <label for="tableName">Table name</label>
            <!--   <b-form-valid-feedback tooltip>
                Looks good!
              </b-form-valid-feedback>
              <b-form-invalid-feedback tooltip>
                Please provide a Table name.
              </b-form-invalid-feedback> -->
      </div>
        </b-col>
         
        
        </b-col>
        </b-row>        
        <br>
        <!-- FIELDS -->
         <b-container fluid>
      <b-form
        ref="form"
        :style="{height: trHeight}"
        class="repeater-form"
        @submit.prevent="repeateAgain"
      >
        

        <b-row class="mb-1"
                v-for="(item, index) in editVarLocal.dianasoftfields"
              :id="item.id"
              :key="item.id"
              ref="row" >
              <b-col cols="2">
               <div class="form-label-group">
               <b-form-input
                :readonly="activeActionLocal=='Vizualizează'"
                id="name"
                v-model="item.name"
                placeholder="Field name"
              
              />
              <label for="name">Field name</label>
              </div>
              </b-col>
                <b-col cols="2">
                <div class="form-label-group">
               <b-form-input
                :readonly="activeActionLocal=='Vizualizează'"
                id="displayName"
                v-model="item.display_name"
                placeholder="Display name"
              
              />
              <label for="displayName">Display name</label>
              </div>
              </b-col>
          <b-col cols="2">   
            <b-form-select
              id="type"
              v-model="item.type"
              :options="typeOptions"
              placeholder="Type"
              
            />
           <label for="type">Type</label>
          </b-col>
          
           <b-col cols="2">
            <div class="form-label-group">
               <b-form-input
                :readonly="activeActionLocal=='Vizualizează'"
                id="length"
                v-model="item.length"
                placeholder="Length"
                
              />
              <label for="length">Length</label>
              </div>
              </b-col>

             <b-col cols="2">   
               <b-form-select
                  id="input_type"
                  v-model="item.input_type"
                  :options="inputtypeOptions"
                  placeholder="Input Type"
                
                />
              <label for="input_type">Input Type</label>
          </b-col>
          <b-col cols="2"> 
             <div class="d-flex ">
              <div>  
                <b-form-checkbox v-model="item.nullable" > Nullable </b-form-checkbox>
                <b-form-checkbox v-model="item.fillable" > Fillable </b-form-checkbox>
               </div>
               <div> 
                <b-form-checkbox v-model="item.required" > Required </b-form-checkbox>
                <b-form-checkbox v-model="item.indexed" > Indexed </b-form-checkbox>
                </div>
                  <!-- Add Button -->
          <b-col
            lg="2"
            md="3"
            cols="1"
           
          >
            <b-button
                        v-ripple.400="'rgba(234, 84, 85, 0.15)'"
                        variant="flat-success"
                        class="btn-icon"
                        
                        @click="addRow">
              <feather-icon   icon="PlusIcon"/>
            
            </b-button>
          </b-col>
       <!-- Remove Button -->
            <b-button
                        v-show="index>0"
                        v-ripple.400="'rgba(234, 84, 85, 0.15)'"
                        variant="flat-danger"
                        class="btn-icon"
                        
                        @click="removeItem(index)">
              <feather-icon   icon="XIcon"/>
            
            </b-button>

        
           </div> 
          </b-col>

         
        
        </b-row>
       
      </b-form>
      
      </b-container>
      </div>
    </b-modal>
  </div>
</template>

<script>
import { BFormValidFeedback, BFormInvalidFeedback, BFormGroup, BFormInput, BFormSelect, BDropdown, BDropdownItem,BRow, BCol,BModal, BButton, VBModal, BAlert,BForm, BFormCheckbox,BContainer} from 'bootstrap-vue'
import Ripple from 'vue-ripple-directive'
import { heightTransition } from '@core/mixins/ui/transition'
export default {
  props: {
        activeEdit:Boolean,
        activeAction:String,
        editVar:Object,
        rutainapoi:String,
        },
        mixins: [heightTransition],
  components: {
    BButton,
    BModal,
    BAlert,
    BFormGroup, BFormInput, BFormSelect, BDropdown, BDropdownItem,BRow, BCol,BFormValidFeedback, BFormInvalidFeedback,BForm, BFormCheckbox,BContainer
  },
  directives: {
    'b-modal': VBModal,
    Ripple,
  },
  name:"dianasoftmodelaev",
  data() {
    return {
        
        rutainapoiLocal:this.rutainapoi,
        activeEditLocal:false,
        activeActionLocal:this.activeAction,
        editVarLocal:this.editVar,
        nextTodoId: 2,
        modelName:"dianasoftmodel",
        inputtypeOptions:[
                    'input',
                    'select',
                    'checkbox',
                    'switch',
                    'produsedefinantare',
                    'textarea',
                    'dropdown',
                    'numberinput',
                    'datacalendaristica',
                    'datasiora',
                    'dropdowncuoptiuni',
                    'gestiunepermisa',
                    'contcontabil',
                    'selectoneuser',
                    'selectusername',
                    'selectmultipleuser',,
                    'localitatecomponent',
                    'judetcomponent',
                    'taricomponent',
                    'cnpcomponent',
                    'upload',
                    'slider',
                    ],
       


        typeOptions:[ "string"  ,
                          "boolean" ,
                          "date"  ,
                          "dateTime"  ,
                          "timestamp" ,
                          "double"  ,
                          "integer" ,
                          "enum"  ,
                          "text"  ,
                          "bigIncrements"  ,
                          "bigInteger"  ,
                          "binary"  ,
                          "char"  ,
                          "dateTimeTz"  ,
                          "decimal" ,
                          "float" ,
                          "geometry"  ,
                          "geometryCollection"  ,
                          "increments"  ,
                          "ipAddress" ,
                          "json"  ,
                          "jsonb" ,
                          "lineString"  ,
                          "longText"  ,
                          "macAddress"  ,
                          "mediumIncrements"  ,
                          "mediumInteger" ,
                          "mediumText"  ,
                          "morphs"  ,
                          "multiLineString" ,
                          "multiPoint"  ,
                          "multiPolygon"  ,
                          "nullableMorphs"  ,
                          "nullableTimestamps"  ,
                          "nullableUuidMorphs"  ,
                          "point" ,
                          "polygon" ,
                          "rememberToken" ,
                          "set" ,
                          "smallIncrements" ,
                          "smallInteger"  ,
                          "softDeletes" ,
                          "softDeletesTz" ,
                          "time"  ,
                          "timestamps"  ,
                          "timestampsTz"  ,
                          "timestampTz" ,
                          "timeTz"  ,
                          "tinyIncrements"  ,
                          "tinyInteger" ,
                          "unsignedBigInteger"  ,
                          "unsignedDecimal" ,
                          "unsignedInteger" ,
                          "unsignedMediumInteger" ,
                          "unsignedSmallInteger"  ,
                          "unsignedTinyInteger" ,
                          "uuid"  ,
                          "uuidMorphs"  ,
                          "year"  
                          ]
        
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
    removeItem(index) {
      this.editVarLocal.dianasoftfields.splice(index, 1)
      this.trTrimHeight(this.$refs.row[0].offsetHeight)
    },
    initTrHeight() {
      this.trSetHeight(null)
      this.$nextTick(() => {
        if(this.$refs.form){
        this.trSetHeight(this.$refs.form.scrollHeight)
        }
      })
    },

    addRow(index){
     
      
        this.editVarLocal.dianasoftfields.push({
                          id: this.nextTodoId += this.nextTodoId,
                          name: '',
                          display_name:'',
                          type: 'string',
                          length:'50',
                          input_type:'input',
                          nullable:true,
                          fillable:true,
                          required:true, 
                          indexed:false,
                           
                        })
          
          this.$nextTick(() => {
            this.trAddHeight(this.$refs.row[0].offsetHeight)
          })
      
      },
    repeateAgain() {
      this.editVarLocal.dianasoftfields.push({
        id: this.nextTodoId += this.nextTodoId,
         name: '',
         display_name:'',
         type: '',
         length:'',
         input_type:'',
         nullable:true,
         fillable:true,
         required:true, 
         indexed:false,
      })

      this.$nextTick(() => {
        this.trAddHeight(this.$refs.row[0].offsetHeight)
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
          const payLoad=this.editVarLocal
          payLoad.requestType="post"
          payLoad.requestUrl="/developerPanel/addModel"
          this.activeEditLocal=false
          this.activeActionLocal=""
          this.$store.dispatch("app/api_Request",payLoad)
                    .then(response=>{
                           this.rows=response  
                            this.$emit("stored",response) 
                           this.editVarLocal={  model_name:"",
                                                 table_name:"", 
                                                 display_name:"",
                                                 dianasoftfields:[{id:1, 
                                                                    prevHeight: 0,
                                                                     name: '',
                                                                    display_name:'',
                                                                    type: '',
                                                                    length:'',
                                                                    input_type:'',
                                                                    nullable:true,
                                                                    fillable:true,
                                                                    required:true, 
                                                                    indexed:false,}]}
                            this.showLoading=false
                             this.$bvToast.toast('Salvare efectuata cu success!', {
                                                                        title: `Salvare cu succes! `,
                                                                        variant:'success',
                                                                        solid: false,
                                                                        appendToast: true,
                                                                        autoHideDelay: 3000,
                                                                        toaster: 'b-toaster-bottom-right',
                                                                      }) 
                            this.$emit('closed')
                            
                       })
                      .catch(error => {
                        
                       // this.handleErrors(error)
                      })
         
    },
   
    aevClosed(){
        this.activeEditLocal=false
        this.editVarLocal={  
                           model_name:"",
                           table_name:"", 
                           display_name:"",
                           dianasoftfields:[{id:1, 
                                                                    prevHeight: 0,
                                                                     name: '',
                                                                    display_name:'',
                                                                    type: '',
                                                                    length:'',
                                                                    input_type:'',
                                                                    nullable:true,
                                                                    fillable:true,
                                                                    required:true, 
                                                                    indexed:false,}]
                           }
        this.activeActionLocal=""
        this.$emit('closed')
      
    },
    
    saveEdit(){
               
                 this.showLoading=true
                  
                  const payLoad=this.editVarLocal 
                  payLoad.requestType="post"
                   payLoad.requestUrl="/developerPanel/editModel"
                  this.$store.dispatch("app/api_Request",payLoad)
                              .then(response=>{
                                               this.selectedID=""
                                               // this.tblRecords=response
                                               this.idselectat=response.id.toString()
                                               this.$emit("stored",response) 
                                               this.activeEditLocal=false
                                               this.activeActionLocal=""
                                               this.editVarLocal={ model_name:"",
                                                                   table_name:"", 
                                                                   display_name:"",
                                                                   dianasoftfields:[{id:1, 
                                                                    prevHeight: 0,
                                                                     name: '',
                                                                    display_name:'',
                                                                    type: '',
                                                                    length:'',
                                                                    input_type:'',
                                                                    nullable:true,
                                                                    fillable:true,
                                                                    required:true, 
                                                                    indexed:false,}]}
                                               this.showLoading=false
                                                this.activeEditLocal=false
                                              this.activeActionLocal=""
                                               this.$bvToast.toast('Modificare efectuata cu success!', {
                                                                        title: `Modificare cu succes! `,
                                                                        variant:'success',
                                                                        solid: false,
                                                                        appendToast: true,
                                                                        autoHideDelay: 3000,
                                                                        toaster: 'b-toaster-bottom-right',
                                                                      }) 
                                               this.$emit('closed')
          
                               })
                              .catch(error => {

                               //this.handleErrors(error)
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
    window.removeEventListener('resize', this.initTrHeight)
  },
 created() {

         if(!this.rutainapoi){
          this.rutainapoiLocal=this.modelName
         }
          window.addEventListener('resize', this.initTrHeight)
          
     
    },
 
}

</script>
