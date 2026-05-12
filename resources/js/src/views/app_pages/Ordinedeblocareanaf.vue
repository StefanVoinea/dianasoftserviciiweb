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
                
        </tabelcomponent>
      </b-card>
      </b-col>
   </b-row > 
      <Ordinedeblocareanafaev
                         @stored="afisezSalvat"
                         @closed="aevClosed"
                         :activeAction="activeAction"
                         :activeEdit="activeEdit"
                         :editVar="editVar"
                         v-show="activeEdit"
                      > 
      </Ordinedeblocareanafaev>  
    
   </b-overlay>
</template>

<script>

import Ordinedeblocareanafaev from "./Ordinedeblocareanafaev.vue"

export default {
  props: {
        id:String,
        }, 
  components: {
    Ordinedeblocareanafaev
  },
  name:"ordinedeblocareanaf",
  data() {
    return {
        refreshLocal:false, 
        idselectat:null,
        campFiltruStart:"",
        modelName:"ordinedeblocareanaf",
        modelDisplayName:"Ordine de blocare ANAF",
        editVar:{
          nr_ordin:"",
          data_ordin:"",
          suspect:"",
          date_de_identificare:"",
          bunuri_blocate:"",
          ordin_de_revocare:"",
          data_revocarii:"",
          institutia:"",},
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
                  label: "Nr ordin",
                  field: "nr_ordin",
                  width: "150px",
                          type:"text",
                 showSortAsc:true,
                },
               
                
                {
                  label: "Data ordin",
                  field: "data_ordin",
                  width: "200px",
                  type:"date",
                  //dateInputFormat: "yyyy-MM-dd'T'HH:mm:ss.SSSSSS'Z'", // expects 2018-03-16
                  //dateOutputFormat: "dd.MM.yyyy HH:mm:ss", // outputs Mar 16th 2018
                  dateInputFormat: "yyyy-MM-dd", // expects 2018-03-16
                  dateOutputFormat: "dd.MM.yyyy", // outputs Mar 16th 2018 
                 showSortAsc:true,
                },
               
                
                {
                  label: "Suspect",
                  field: "suspect",
                  width: "300px",
                          type:"text",
                 showSortAsc:true,
                },
               
                
                {
                  label: "Date de identificare",
                  field: "date_de_identificare",
                  width: "500px",
                          type:"text",
                 showSortAsc:true,
                },
               
                
                {
                  label: "Bunuri blocate",
                  field: "bunuri_blocate",
                  width: "500px",
                  type:"text",
                  showSortAsc:true,
                },
               
                
                {
                  label: "Ordin de revocare",
                  field: "ordin_de_revocare",
                  width: "150px",
                          type:"text",
                 showSortAsc:true,
                },
               
                
                {
                  label: "Data revocarii",
                  field: "data_revocarii",
                  width: "200px",
                  type:"date",
                  //dateInputFormat: "yyyy-MM-dd'T'HH:mm:ss.SSSSSS'Z'", // expects 2018-03-16
                  //dateOutputFormat: "dd.MM.yyyy HH:mm:ss", // outputs Mar 16th 2018
                  dateInputFormat: "yyyy-MM-dd", // expects 2018-03-16
                  dateOutputFormat: "dd.MM.yyyy", // outputs Mar 16th 2018 
                 showSortAsc:true,
                },
               
                
                {
                  label: "Institutia",
                  field: "institutia",
                  width: "200px",
                          type:"text",
                 showSortAsc:true,
                },
               
                  
                    
        ],
     
    }
  },
 
  methods: {
    
    aevClosed(){
       this.idselectat=null
      this.selectedID=""
      this.activeEdit=false
      this.editVar={
          nr_ordin:"",
          data_ordin:"",
          suspect:"",
          date_de_identificare:"",
          bunuri_blocate:"",
          ordin_de_revocare:"",
          data_revocarii:"",
          institutia:"",},
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
                     nr_ordin:"",
                data_ordin:"",
                suspect:"",
                date_de_identificare:"",
                bunuri_blocate:"",
                ordin_de_revocare:"",
                data_revocarii:"",
                institutia:"",
                

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