<template>
    <b-overlay
      :show="showLoading"
      rounded="sm"
      no-fade
      variant="primary"
      opacity="0.25"
      blur="2px"
    >
    
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
                                                         
                                                          text="Situatii jurnal SMS"
                                                          v-b-tooltip.hover.v-light
                                                          title="Situatii jurnal SMS"
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
                                                                   
                                                         

                                                        

                                                          


                                                        </b-dropdown>
                                                  <b-dropdown
                                                          text="Alte actiuni"
                                                          variant="outline-success"
                                                           v-b-tooltip.hover.v-light
                                                          title="Alte actiuni"
                                                          class="dropdown-icon-wrapper mr-1"
                                                        >
                                                        <template #button-content>
                                                                     
                                                                      <feather-icon
                                                                        icon="LayersIcon"
                                                                        size="16"
                                                                        class="align-middle"
                                                                      />
                                                                    </template>
                                                           
                                                          <b-dropdown-item
                                                               v-if="canTransmiteSMS"
                                                               @click="transmiteSMS" 
                                                          >
                                                            Transmite SMS
                                                          </b-dropdown-item>
                                                         
                                                          <b-dropdown-divider />
                                                         
                                                        </b-dropdown>

                                               
                
        </tabelcomponent>
      </b-card>

      <Jurnalsmsaev
                         @stored="afisezSalvat"
                         @closed="aevClosed"
                         :activeAction="activeAction"
                         :activeEdit="activeEdit"
                         :editVar="editVar"
                         v-show="activeEdit"
                      > 
      </Jurnalsmsaev>  
    
         <input @change="onFileChange" type="file" :id="'file'+name" :ref="'file'+name" accept="application/vnd.ms-excel" hidden/>
   </b-overlay>
</template>

<script>

import Jurnalsmsaev from "./Jurnalsmsaev.vue"

import axios from "axios"

export default {
  props: {
        id:String,
        }, 
  components: {
    Jurnalsmsaev
  },
  name:"jurnalsms",
  data() {
    return {
      name:"",
      canTransmiteSMS:this.$userpermitt.can("transmiteSMS"),
      afisezRSMS:false,
        refreshLocal:false, 
        idselectat:null,
        campFiltruStart:"",
        modelName:"jurnalsms",
        modelDisplayName:"Jurnal SMS",
        editVar:{
          nr_contract:"",
          telefon:"",
          mesaj:"",
          status:"",
          utilizator:"",
          data_operare:"",
          data_transmitere:"",
          catre:"",
          id_site:"",
          data_verificare_status:"",},
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
                  field: "contract.agentia",
                  width: "150px",
                          type:"text",
                 showSortAsc:true,
                },  
                {
                  label: "Nr contract",
                  field: "nr_contract",
                  width: "150px",
                          type:"text",
                 showSortAsc:true,
                },
               
                
                {
                  label: "Telefon",
                  field: "telefon",
                  width: "150px",
                          type:"text",
                 showSortAsc:true,
                },
               
                
                {
                  label: "Mesaj",
                  field: "mesaj",
                  width: "600px",
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
                  label: "Utilizator",
                  field: "utilizator",
                  width: "150px",
                          type:"text",
                 showSortAsc:true,
                },
               
                
                {
                  label: "Data operarii",
                  field: "data_operare",
                  width: "150px",
                  type:"date",
                  dateInputFormat: "yyyy-MM-dd'T'HH:mm:ss.SSSSSS'Z'", // expects 2018-03-16
                  dateOutputFormat: "dd.MM.yyyy HH:mm:ss", // outputs Mar 16th 2018
                 showSortAsc:true,
                },
               
                
                {
                  label: "Data transmitere",
                  field: "data_transmitere",
                  width: "150px",
                  type:"date",
                  dateInputFormat: "yyyy-MM-dd'T'HH:mm:ss.SSSSSS'Z'", // expects 2018-03-16
                  dateOutputFormat: "dd.MM.yyyy HH:mm:ss", // outputs Mar 16th 2018

                 showSortAsc:true,
                },
               
                
                {
                  label: "Catre",
                  field: "catre",
                  width: "150px",
                          type:"text",
                 showSortAsc:true,
                },
               
                
                {
                  label: "ID Site",
                  field: "id_site",
                  width: "150px",
                          type:"text",
                 showSortAsc:true,
                },
               
                
                {
                  label: "Data verificare status",
                  field: "data_verificare_status",
                  width: "150px",
                                    type:"date",
                  dateInputFormat: "yyyy-MM-dd'T'HH:mm:ss.SSSSSS'Z'", // expects 2018-03-16
                  dateOutputFormat: "dd.MM.yyyy HH:mm:ss", // outputs Mar 16th 2018

                 showSortAsc:true,
                },
               
                  
                    
        ],
     
    }
  },
 
  methods: {
    
      transmiteSMS(){
         document.getElementById('file'+this.name).click()
      },
       onFileChange(e){
     
                this.file = e.target.files[0];
                
                if(this.file){
                   
                   this.importFisier(e)
                }
    },
    importFisier(e){
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
                axios.post('/api/transmitesms', formData, config)
                          .then(response => {
                            this.name=""

                                 this.$bvToast.toast("Transmitere efectuata cu success!", 
                                                 {
                                                    title: `Transmitere cu succes! `,
                                                    variant:"success",
                                                    solid: false,
                                                    appendToast: true,
                                                    autoHideDelay: 3000,
                                                    toaster: "b-toaster-bottom-right",
                                                                      }) 
                           this.showLoading=false
                           })
    },
    raportSMS(){
        this.afisezRSMS=true
      },
    aevClosed(){
       this.idselectat=null
      this.selectedID=""
      this.activeEdit=false
      this.editVar={
          nr_contract:"",
          telefon:"",
          mesaj:"",
          status:"",
          utilizator:"",
          data_operare:"",
          data_transmitere:"",
          catre:"",
          id_site:"",
          data_verificare_status:"",},
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
                     nr_contract:"",
                telefon:"",
                mesaj:"",
                status:"",
                utilizator:"",
                data_operare:"",
                data_transmitere:"",
                catre:"",
                id_site:"",
                data_verificare_status:"",
                

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