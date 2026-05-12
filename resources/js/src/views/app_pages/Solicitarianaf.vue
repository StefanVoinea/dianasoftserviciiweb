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
                                  <b-dropdown
                                    text="Alte actiuni"
                                    variant="outline-success"
                                    v-b-tooltip.hover.v-light
                                    title="Alte actiuni"
                                    class="dropdown-icon-wrapper mr-1">
                                      <template #button-content>
                                        <feather-icon
                                          icon="LayersIcon"
                                          size="16"
                                          class="align-middle"/>
                                      </template>
                                      
                                      <b-dropdown-item v-if="canUploadAcordANAFPDF"
                                        @click="uploadAcordANAFPDF">
                                        Upload Acord ANAF PDF
                                      </b-dropdown-item>
                                      <b-dropdown-divider />
                                       <b-dropdown-item v-if="canAfisezAcordANAFPDF"
                                        @click="afisezAcordANAFPDF">
                                         Afisez Acord ANAF PDF
                                      </b-dropdown-item>
                                      <b-dropdown-divider />                    
                                                
                                       <b-dropdown-item v-if="canCreezSolicitareANAF"
                                        @click="creezSolicitareANAF">
                                         Creez PDF interogare ANAF
                                      </b-dropdown-item>
                                      <b-dropdown-divider />                
                                   </b-dropdown>
                       <b-dropdown
                                                         
                                                          text="Situatii ANAF"
                                                          variant="outline-info"
                                                          class="dropdown-icon-wrapper mr-1"
                                                        >
                                                        <template #button-content>
                                                                     
                                                                      <feather-icon
                                                                        icon="ListIcon"
                                                                        size="16"
                                                                        class="align-middle"
                                                                      />
                                                                    </template>

                                                          
                                                           <b-dropdown-item v-if="canRaporteazaANAF"  @click="raportareANAF" >
                                                            Raportare ANAF
                                                          </b-dropdown-item>  

                                                          <b-dropdown-item  v-if="canViewCentralizatorANAF" @click="centralizatorANAF" >
                                                            Centralizator ANAF
                                                          </b-dropdown-item>                                                     
                                                           <b-dropdown-item  v-if="canViewRaportareNumarInterogariANAF" @click="afisezRNIA=true" >
                                                            Raportare numar interogari ANAF
                                                          </b-dropdown-item>  
                                                        </b-dropdown>
                
        </tabelcomponent>
      </b-card>
      </b-col>
   </b-row > 
      <Solicitarianafaev
                         @stored="afisezSalvat"
                         @closed="aevClosed"
                         :activeAction="activeAction"
                         :activeEdit="activeEdit"
                         :editVar="editVar"
                         v-show="activeEdit"
                      > 
      </Solicitarianafaev>  
     <Raportareanaf  
            @closed="afisezRA=false"
            :afisezRA="afisezRA"
            />    
     <Centralizatoranaf  
            @closed="afisezCA=false"
            :afisezCA="afisezCA"
            />   
        <Creezinterogareanaf  
            @closed="afisezCIA=false"
            requestUrl="creezinterogareanaf"
            :editVar="editVar"
            :afisezCIA="afisezCIA"/>    
            
          <Raportnumarinterogarianaf 
                         @closed="afisezRNIA=false"
                          :afisezRNIA="afisezRNIA" />     

  <input  :name="id" :id="'file'" :ref="'file'" type="file" class="form-control" v-on:change="onFileChange" hidden/>        
   </b-overlay>
</template>

<script>

import Solicitarianafaev from "./Solicitarianafaev.vue"
import Raportareanaf from "./rapoarte/Raportareanaf.vue"
import Centralizatoranaf from "./rapoarte/Centralizatoranaf.vue"
import Creezinterogareanaf from "./rapoarte/Creezinterogareanaf.vue"
import Raportnumarinterogarianaf from "./rapoarte/Raportnumarinterogarianaf.vue"
import axios from "axios"

export default {
  props: {
        id:String,
        }, 
  components: {
    Solicitarianafaev,
    Raportareanaf,
    Centralizatoranaf,
    Creezinterogareanaf,
    Raportnumarinterogarianaf
  },
  name:"solicitarianaf",
  data() {
    return {
      canViewRaportareNumarInterogariANAF:this.$userpermitt.can("viewRaportareNumarInterogariANAF"),
      canViewCentralizatorANAF:this.$userpermitt.can("viewCentralizatorANAF"),
      canRaporteazaANAF:this.$userpermitt.can("raporteazaANAF"),
      canCreezSolicitareANAF:this.$userpermitt.can("creezSolicitareANAF"),
      canAfisezAcordANAFPDF:this.$userpermitt.can("afisezAcordANAFPDF"),
      canUploadAcordANAFPDF:this.$userpermitt.can("uploadAcordANAFPDF"),
        afisezRNIA:false,
        afisezCA:false,
        afisezCIA:false,
        afisezRA:false,
        refreshLocal:false, 
        idselectat:null,
        campFiltruStart:"",
        modelName:"solicitarianaf",
        modelDisplayName:"Interogari ANAF",
        editVar:{
          data:"",
          data_creare_mesaj:"",
          req_id:"",
          tip:"",
          cnp:"",
          nr_aut:"",
          data_aut:"",
          pfa:"",
          nume:"",
          sex:"",
          judet:"",
          data_nasterii:"",},
        activeEdit:false,
        activeAction:"",
        selectedID:"",
        file:"",
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
                  label: "Data",
                  field: "data",
                  width: "180px",
                  type:"date",
                  //dateInputFormat: "yyyy-MM-dd'T'HH:mm:ss.SSSSSS'Z'", // expects 2018-03-16
                  //dateOutputFormat: "dd.MM.yyyy HH:mm:ss", // outputs Mar 16th 2018
                  dateInputFormat: "yyyy-MM-dd", // expects 2018-03-16
                  dateOutputFormat: "dd.MM.yyyy", // outputs Mar 16th 2018 
                 showSortAsc:true,
                },
               
                
                {
                  label: "Data creare mesaj",
                  field: "data_creare_mesaj",
                  width: "180px",
                          type:"text",
                 showSortAsc:true,
                },
               
                
                {
                  label: "Req id",
                  field: "req_id",
                  width: "150px",
                          type:"text",
                 showSortAsc:true,
                },
               
                
                {
                  label: "Tip persoana",
                  field: "tip",
                  width: "150px",
                  type:"text",
                 showSortAsc:true,
                },
               
                
                {
                  label: "CNP/CUI",
                  field: "cnp",
                  width: "150px",
                  type:"text",
                 showSortAsc:true,
                },
               {
                  label: "Nume",
                  field: "nume",
                  width: "300px",
                          type:"text",
                 showSortAsc:true,
                },
               
                
                {
                  label: "Sex",
                  field: "sex",
                  width: "150px",
                          type:"text",
                 showSortAsc:true,
                },
               
                
                {
                  label: "Judet",
                  field: "judet",
                  width: "150px",
                          type:"text",
                 showSortAsc:true,
                },
               
                
                {
                  label: "Data nasterii",
                  field: "data_nasterii",
                  width: "180px",
                  type:"date",
                  //dateInputFormat: "yyyy-MM-dd'T'HH:mm:ss.SSSSSS'Z'", // expects 2018-03-16
                  //dateOutputFormat: "dd.MM.yyyy HH:mm:ss", // outputs Mar 16th 2018
                  dateInputFormat: "yyyy-MM-dd", // expects 2018-03-16
                  dateOutputFormat: "dd.MM.yyyy", // outputs Mar 16th 2018 
                 showSortAsc:true,
                },
                 {
                  label: "Nr acord",
                  field: "nr_aut",
                  width: "150px",
                  type:"text",
                  showSortAsc:true,
                },
               
                
                {
                  label: "Data acord",
                  field: "data_aut",
                  width: "180px",
                  type:"date",
                  //dateInputFormat: "yyyy-MM-dd'T'HH:mm:ss.SSSSSS'Z'", // expects 2018-03-16
                  //dateOutputFormat: "dd.MM.yyyy HH:mm:ss", // outputs Mar 16th 2018
                  dateInputFormat: "yyyy-MM-dd", // expects 2018-03-16
                  dateOutputFormat: "dd.MM.yyyy", // outputs Mar 16th 2018 
                 showSortAsc:true,
                },
               
                
                {
                  label: "PFA",
                  field: "pfa",
                  width: "150px",
                          type:"text",
                 showSortAsc:true,
                },
               
                  
                    
        ],
     
    }
  },
 
  methods: {
    creezSolicitareANAF(){
      
        this.afisezCIA=true
      
    },
    afisezAcordANAFPDF(){
      if (this.selectedID==null){
          this.$bvToast.toast('Selectați o interogare!',
                              {
                                title: `Atentie! `,
                                variant:'warning',
                                solid: false,
                                appendToast: true,
                                autoHideDelay: 3000,
                                toaster: 'b-toaster-bottom-right',
                              })  
              
      }else{
        this.editVar=Object.assign({},this.selectedID)
        if(this.editVar.acordpdf){ 
    
         this.showLoading=true
                  const payLoad=this.editVar 
                  payLoad.requestType="get"
                  payLoad.requestUrl="/afisezacordanafpdf/"+this.editVar.id
                  
                  this.$store.dispatch("app/api_blob_Request",payLoad)
                              .then(response=>{
                                               this.selectedID=""
                                               this.showLoading=false
                                               this.$router.push({ name: 'pdfviewer',  params: { blobPDF: response,
                                                                              numefis:"acord_anaf.pdf",
                                                                              rutainapoi:"solicitarianaf"}})
          
                               })
                              .catch(error => {

                               this.showLoading=false
                              })
        }else{
            this.$bvToast.toast('Nu a fost incarcat un acord pdf pentru aceasta solicitare!',
                              {
                                title: `Atentie! `,
                                variant:'warning',
                                solid: false,
                                appendToast: true,
                                autoHideDelay: 3000,
                                toaster: 'b-toaster-bottom-right',
                              })  
        } 
      }                       
    },  
     onFileChange(e){
                
                this.file = e.target.files[0];
                if(this.file){
                  this.uploadFile(e)
                }
    
    },
    uploadFile(e){
            if (this.selectedID==null)
            {
              this.$bvToast.toast('Selectați o interogare!', {
                                                                        title: `Atentie! `,
                                                                        variant:'warning',
                                                                        solid: false,
                                                                        appendToast: true,
                                                                        autoHideDelay: 3000,
                                                                        toaster: 'b-toaster-bottom-right',
                                                                      })  
              
            }else
            {
                this.showLoading=true
                e.preventDefault();
                // this.uploadFileActive=false
                let currentObj = this;
                const config = {
                                 headers: { 'content-type': 'multipart/form-data',
                                           'Authorization': 'Bearer ' + this.$store.state.app.token,
                                           'AuthorizationHeader': JSON.parse(this.$store.state.app.societateaCurenta).id
                                           }
                                }
                let formData = new FormData();
                formData.append('file', this.file);
                let url='/api/uploadacordanafpdf/'+this.selectedID.id
                axios.post(url, formData, config)
                          .then(response => {
                             this.file=''
                             
                            
                             this.showLoading=false
                              })
                           .catch(error => {
                              this.showLoading=false
                             
                           })
                   }        
    },
    uploadAcordANAFPDF(){
      document.getElementById('file').click()
    },
    raportareANAF(){
      this.afisezRA=true
    },
    centralizatorANAF(){
      this.afisezCA=true
    },  
    aevClosed(){
       this.idselectat=null
      this.selectedID=""
      this.activeEdit=false
      this.editVar={
          data:"",
          data_creare_mesaj:"",
          req_id:"",
          tip:"",
          cnp:"",
          nr_aut:"",
          data_aut:"",
          pfa:"",
          nume:"",
          sex:"",
          judet:"",
          data_nasterii:"",},
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
                     data:"",
                data_creare_mesaj:"",
                req_id:"",
                tip:"",
                cnp:"",
                nr_aut:"",
                data_aut:"",
                pfa:"",
                nume:"",
                sex:"",
                judet:"",
                data_nasterii:"",
                

        }
        this.activeEdit=true
    },
    
    edit() {
          this.idselectat=null
          this.editVar=Object.assign({},this.selectedID)
          this.activeAction="Modifică"
          this.activeEdit=true
         
    },
    view() {
        
          
          this.editVar=Object.assign({},this.selectedID)
          this.activeAction="Vizualizează"
          this.activeEdit=true
        
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