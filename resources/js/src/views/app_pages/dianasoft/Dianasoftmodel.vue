<template>
   <b-overlay
      :show="showLoading"
      rounded="sm"
      no-fade
      variant="primary"
      opacity="0.25"
      blur="2px"
    >
        <b-row class="d-flex align-items-center justify-content-center">
   
    <b-col cols="12" >
      <b-card>
    
        <tabelcomponent 
                      :cols="12"
                      :columnDefs="columnDefs"
                      :modelName="modelName"
                      :titlu="modelDisplayName"
                      :refresh="refreshLocal"
                      :idselectat="idselectat"
                      @onSelectionChanged="onSelectionChanged"
                      @adauga="add"
                      @edit="edit"
                      @view="view">
                      <b-row class="d-flex align-items-center justify-content-center">
                       <b-col  cols="12"  @click="selectFile" >
                    
                    <b-button 
                                          variant="primary"
                                          class="btn-icon "
                                          size="sm"
                                           v-b-tooltip.hover.v-dark
                                          title="OCR"
                                        
                                           > 
                                         <feather-icon icon="UploadCloudIcon" /> 
                                         
                                           
                                
                     
                                </b-button>
                                 <input @change="onFileChange" type="file" :id="'filename'" :ref="'filename'" accept="image/gif,image/jpeg, image/png" hidden/>
                    
                     </b-col>
                    </b-row>
        </tabelcomponent>
     </b-card>  
     </b-col>
   </b-row> 
  <dianasoftmodelaev
                         @stored="afisezSalvat"
                         @closed="aevClosed"
                         :activeAction="activeAction"
                         :activeEdit="activeEdit"
                         :editVar="editVar"
                         v-show="activeEdit"
                      > 
      </dianasoftmodelaev>  
</b-overlay>
</template>

<script>

import Dianasoftmodelaev from './Dianasoftmodelaev'
import axios from "axios"

export default {
  components: {
    Dianasoftmodelaev
  },
  data() {
    return {
      editVar:{
                model_name:"",
                table_name:"",
                display_name:"",
                dianasoftfields:[{
                                    id:1, 
                                    prevHeight: 0,
                                    name: '',
                                    display_name:'',
                                    type: '',
                                    length:'',
                                    input_type:'',
                                    nullable:true,
                                    fillable:true,
                                    required:true, 
                                    indexed:false,
                                }]
               },
      activeEdit:false,
      activeAction:"",

      modelName:"dianasoftmodel",
      modelDisplayName:"Diana Soft Models",
      idselectat:null,
      
      showLoading:false,
      refreshLocal:false,
      columnDefs: [
        {
          label: 'Model name',
          field: 'model_name',
          type:'text',
          width: '300px',
          showSortAsc:true,
          
        },
        {
          label: 'Table name',
          field: 'table_name',
          type:'text',
          width: '300px',
          showSortAsc:true,
        },
       {
          label: 'Model type',
          field: 'model_type',
          type:'text',
          width: '150px',
          showSortAsc:true,
        },
        {
          label: 'Display name',
          field: 'display_name',
          type:'text',
          width: '300px',
          showSortAsc:true,
        },
        
      ],
   }
  },
 
  methods: {
     selectFile(){
        document.getElementById('filename').click()
      },
     onFileChange(e){
     
                this.file = e.target.files[0];
               if(this.file){
                   
                  this.importFILE(e)
                }
    },
   importFILE(e){
                this.showLoading=true
                e.preventDefault()
               
                let currentObj = this
                
                
                
                const config = {
                                headers: { 'content-type': 'multipart/form-data',
                                           'Authorization': 'Bearer ' + this.$store.state.app.token,
                                           'AuthorizationHeader': JSON.parse(this.$store.state.app.societateaCurenta).id
                                           }
                                }
                let formData = new FormData();
                formData.append('file', this.file);
                
                axios.post('/api/importFileModel', formData, config)
                          .then(response => {
                             
                             this.file=[]
                             let raspuns=response.data
                        

                            this.editVar={model_name:raspuns.denumire,
                                             table_name:raspuns.table_name.replaceAll("_",""), 
                                             display_name:raspuns.explicatie,
                                            dianasoftfields:[] 
                                              }
                             
                          let i=1
                   
                         raspuns.fields.map(field=>{

                          this.editVar.dianasoftfields.push({
                                            id: i += i,
                                            name: field.name,
                                            display_name:field.display_name.replaceAll("_"," ").charAt(0).toUpperCase() + field.display_name.replaceAll("_"," ").slice(1),
                                            type: field.type,
                                            length:field.type=='string'?field.size:'',
                                            input_type:field.type=='date'?'datacalendaristica':'input',
                                            nullable:true,
                                            fillable:true,
                                            required:true, 
                                            indexed:false,
                           
                                            })
                          this.activeAction="Adaugă"
                             this.showLoading=false
                              this.activeEdit=true
                              })
                         })
    },
    creezdinnou(){
        this.activeAction="Adaugă"
        this.idselectat=null
          
         /* if(this.$globalHelpers.perioadablocata(this.selectedID.data,"Vanzari")){
            this.$bvToast.toast('Perioada este blocata!', {
                                                                        title: `Atentie! `,
                                                                        variant:'danger',
                                                                        solid: false,
                                                                        appendToast: true,
                                                                        autoHideDelay: 3000,
                                                                        toaster: 'b-toaster-bottom-right',
                                                                      })  
            return false
          }*/
          this.editVar=Object.assign({},this.selectedID)
          this.activeEdit=true
      },
     aevClosed(){
      this.activeEdit=false
      this.editVar={       model_name:"",
                           table_name:"", 
                           display_name:"",
                           dianasoftfields:[{id:1, prevHeight: 0,}]
                    }
      this.activeAction=""
    },
    afisezSalvat(value){
      this.refreshLocal=!this.refreshLocal
    },
    listen(){
                // Echo.channel('cerber_databasechannel')
                //     .listen('.'+this.modelName+'.updated', (e) => {
                //        this.getRecords()
                //      });
    },
    onSelectionChanged(value){
      this.selectedID=value
    },
   add(){
          this.activeAction="Adaugă"
          this.editVar={ model_name:"",
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
          this.activeEdit=true
    },
   edit(){
           this.idselectat=null
          
         /* if(this.$globalHelpers.perioadablocata(this.selectedID.data,"Vanzari")){
            this.$bvToast.toast('Perioada este blocata!', {
                                                                        title: `Atentie! `,
                                                                        variant:'danger',
                                                                        solid: false,
                                                                        appendToast: true,
                                                                        autoHideDelay: 3000,
                                                                        toaster: 'b-toaster-bottom-right',
                                                                      })  
            return false
          }*/
          this.editVar=Object.assign({},this.selectedID)
          this.activeAction="Modifică"
          this.activeEdit=true
   } ,
   view(){

          this.editVar=Object.assign({},this.selectedID)
          this.activeAction="Vizualizează"
          this.activeEdit=true
    },
   
   
  },


  created() {
      
     document.title=window.app_name+"->"+this.modelDisplayName
      
      this.listen()
     


  },
}
</script>
