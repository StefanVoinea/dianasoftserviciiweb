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
                      :columnDefs="columnDefs"
                      :modelName="modelName"
                      :refresh="refreshLocal"
                      :titlu="modelDisplayName"
                      :idselectat="idselectat"
                      :campFiltruStart="campFiltruStart"
                      @onSelectionChanged="onSelectionChanged"
                      @adauga="add"
                      @edit="edit"
                      @view="view">
                       <b-button 
                                  variant="outline-info"
                                  class="btn-icon mr-1"
                                  size="lg" 
                                  v-b-tooltip.hover.v-light
                                  title="Upload"
                                  @click="upload" 
                                   > 
                                    <feather-icon icon="UploadIcon" />

                        </b-button>
                
        </tabelcomponent>
      </b-card>
      </b-col>
   </b-row > 
    <input :name="'file'" :id="'file'" :ref="'file'" type="file" class="form-control" v-on:change="onFileChange" hidden/>
      <Monitorizaredocumenteaev
                         @stored="afisezSalvat"
                         @closed="aevClosed"
                         :activeAction="activeAction"
                         :activeEdit="activeEdit"
                         :editVar="editVar"
                         v-show="activeEdit"
                      > 
      </Monitorizaredocumenteaev>  
    
   </b-overlay>
</template>

<script>

import Monitorizaredocumenteaev from "./Monitorizaredocumenteaev.vue"
import axios from "axios"
export default {
  props: {
        id:String,
        }, 
  components: {
    Monitorizaredocumenteaev
  },
  name:"monitorizaredocumente",
  data() {
    return {
        refreshLocal:false, 
        idselectat:null,
        file:"",
        campFiltruStart:"",
        modelName:"monitorizaredocumente",
        modelDisplayName:"Monitorizare documente agentii",
        editVar:{
          gestiune_id:"",
          contract_id:"",
          user_id:"",
          tip_document:"",
          fisier:"",
          data_incasarii:"",
          suma_incasata:"",
          tip_valuta:"",
          status:"",
          banca:""},
        activeEdit:false,
        activeAction:"",
        selectedID:"",
        showLoading:false,
        columnDefs: [
                       // { headerName: "Document...",
                      //        children: [
                      // columnGroupShow:"open",
                          // filter: "agNumberColumnFilter",
                          // valueFormatter: function(params) { return new Date(params.value).toLocaleDateString() },
                          // cellRenderer: function(params) {
                          //            if(params.value!=null){

                          //               return "<a href='/contract?id="+params.value.id +"' target='_blank'>"+ params.value.nr_contract+'/'+ new Date(params.value.data_contract).toLocaleDateString()+' '+params.value.nume+'</a>'  
                          //            }
                                    
                          //       },
                  
                {
                  label: "Agentia",
                  field: "gestiune.denumire",
                  width: "100px",
                  type:"text",
                  showSortAsc:true,
                },
               
                
                {
                  label: "Nr contract",
                  field: "contract.nr_contract",
                  width: "100px",
                  type:"text",
                  showSortAsc:true,
                },
                {
                  label: "Data contract",
                  field: "contract.data_contract",
                  width: "100px",
                  type:"date",
                  //dateInputFormat: "yyyy-MM-dd'T'HH:mm:ss.SSSSSS'Z'", // expects 2018-03-16
                  //dateOutputFormat: "dd.MM.yyyy HH:mm:ss", // outputs Mar 16th 2018
                  dateInputFormat: "yyyy-MM-dd", // expects 2018-03-16
                  dateOutputFormat: "dd.MM.yyyy", // outputs Mar 16th 2018 
                 showSortAsc:true,
                },
               
                 {
                  label: "Nume",
                  field: "contract.nume",
                  width: "300px",
                  type:"text",
                  showSortAsc:true,
                },
                {
                  label: "User",
                  field: "user.name",
                  width: "200px",
                  type:"text",
                  showSortAsc:true,
                },
               {
                  label: "Tip document",
                  field: "tip_document",
                  width: "300px",
                  type:"text",
                 showSortAsc:true,
                },
                 {
                  label: "Data incasarii",
                  field: "data_incasarii",
                  width: "150px",
                  type:"date",
                  //dateInputFormat: "yyyy-MM-dd'T'HH:mm:ss.SSSSSS'Z'", // expects 2018-03-16
                  //dateOutputFormat: "dd.MM.yyyy HH:mm:ss", // outputs Mar 16th 2018
                  dateInputFormat: "yyyy-MM-dd", // expects 2018-03-16
                  dateOutputFormat: "dd.MM.yyyy", // outputs Mar 16th 2018 
                 showSortAsc:true,
                },
                   {
                  label: "Suma incasata",
                  field: "suma_incasata",
                  width: "150px",
                  type:"text",
                 showSortAsc:true,
                },
                 {
                  label: "Tip valuta",
                  field: "tip_valuta",
                  width: "100px",
                  type:"text",
                 showSortAsc:true,
                },
                   {
                  label: "Banca",
                  field: "banca",
                  width: "200px",
                  type:"text",
                 showSortAsc:true,
                },
                {
                  label: "Status",
                  field: "status",
                  width: "150px",
                  type:"text",
                 showSortAsc:true,
                },
               {
                  label: "Observatii",
                  field: "obs",
                  width: "400px",
                  type:"text",
                 showSortAsc:true,
                },
                  
                    
        ],
     
    }
  },
 
  methods: {
    onFileChange(e){
                
                this.file = e.target.files[0];
                
                if(this.file){
                  this.uploadFile(e)
                }
    },
    uploadFile(e){
              this.showLoading=true
              //e.preventDefault();
              
              
                                let currentObj = this;
                                const config = {
                                                 headers: { 'content-type': 'multipart/form-data',
                                                           'Authorization': 'Bearer ' + this.$store.state.app.token,
                                                           'AuthorizationHeader': JSON.parse(this.$store.state.app.societateaCurenta).id
                                                           }
                                                }
                                let formData = new FormData();
                                formData.append('file', this.file);
                                
                                let url="/api/monitorizaredocumente/uploadfile/"+this.editVarLocal.id
                                 
                                axios.post(url, formData, config)
                                .then(response => {
                                                    this.file=''
                                                    this.editVarLocal={  
                                                gestiune_id:'',
                                                contract_id:'',
                                                user_id:'',
                                                tip_document:'',
                                                fisier:'',
                                                

                                        }
                             this.$bvToast.toast("Upload efectuat cu succes!", 
                                                 {
                                                    title: `Salvare cu succes! `,
                                                    variant:"success",
                                                    solid: false,
                                                    appendToast: true,
                                                    autoHideDelay: 3000,
                                                    toaster: "b-toaster-bottom-right",
                                                                      }) 
                            this.showLoading=false
                            
                              })
                                /*
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

                            }) */
          
                       
                   
    },
    upload(){
      if (this.selectedID==""){
        this.$bvToast.toast("Selectati un document!", 
                                           {
                                                    title: `Selectati un document! `,
                                                    variant:"warning",
                                                    solid: false,
                                                    appendToast: true,
                                                    autoHideDelay: 3000,
                                                    toaster: "b-toaster-bottom-right",
                                                                      }) 
            
      }else{
            this.editVarLocal=Object.assign({},this.selectedID)
            document.getElementById('file').click()
    }
    },
    aevClosed(){
       this.idselectat=null
      this.selectedID=""
      this.activeEdit=false
      this.editVar={
          gestiune_id:"",
          contract_id:"",
          user_id:"",
          tip_document:"",
          fisier:"",
          data_incasarii:"",
          suma_incasata:"",
          tip_valuta:"",
          status:"",
          banca:""},
      this.activeAction=""
    },
    afisezSalvat(value){
      //this.idselectat=value.id
      //this.campFiltruStart="id"
        this.refreshLocal=!this.refreshLocal
    },
    listen(){
              //  Echo.channel("cerber_databasechannel")
              //      .listen("."+this.modelName+".updated", (e) => {
              //         this.getRecords()
               //      });
    },
    onSelectionChanged(value){
      this.selectedID=value
    },
    
    add() {
        this.activeAction="Adaugă"
        this.editVar={  
                gestiune_id:"",
                contract_id:"",
                user_id:"",
                tip_document:"",
                fisier:"",
                data_incasarii:"",
                suma_incasata:"",
                tip_valuta:"",
                status:"In lucru",
                banca:""
                

        }
        this.activeEdit=true
    },
    
    edit() {
          this.idselectat=null
          this.editVar=Object.assign({},this.selectedID)
          this.activeAction="Modifică"
          this.activeEdit=true
         
    },
  
     view(){
            this.showLoading=true
          this.editVar=Object.assign({},this.selectedID)
          let payLoad=this.editVar
          payLoad.requestType="post"
          payLoad.requestUrl="/monitorizaredocumente/viewfile/"+this.editVar.id
            
          this.$store.dispatch("app/api_blob_Request",payLoad)
                    .then(response=>{
                           
                           this.showLoading=false
                           var fileURL = window.URL.createObjectURL(new Blob([response]));
                           var fileLink = document.createElement('a');
                           fileLink.href = fileURL;
                          
                             fileLink.setAttribute('download', this.editVar.fisier);
                             document.body.appendChild(fileLink);
                             fileLink.click();
                           })
                     .catch(error => {
                        this.showLoading=false
                       
                      })
    
    },
    },
    created() {
      document.title=window.app_name+"->"+this.modelDisplayName
     // if(this.id!=null){
     //             this.idselectat=this.id
     //             this.campFiltruStart="id"
     // }
      this.listen()
     
    },
  
}

</script>