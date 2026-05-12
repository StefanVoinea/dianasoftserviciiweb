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
      <Coduricaenrev2aev
                         @stored="afisezSalvat"
                         @closed="aevClosed"
                         :activeAction="activeAction"
                         :activeEdit="activeEdit"
                         :editVar="editVar"
                         v-show="activeEdit"
                      > 
      </Coduricaenrev2aev>  
    
   </b-overlay>
</template>

<script>

import Coduricaenrev2aev from "./Coduricaenrev2aev.vue"

export default {
  props: {
        id:String,
        }, 
  components: {
    Coduricaenrev2aev
  },
  name:"coduricaenrev2",
  data() {
    return {
        refreshLocal:false, 
        idselectat:null,
        campFiltruStart:"",
        modelName:"coduricaenrev2",
        modelDisplayName:"Coduri CAEN rev2",
        editVar:{
          cod_caen:"",
          descriere:"",
          procent:"",},
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
                  label: "Cod CAEN",
                  field: "cod_caen",
                  width: "300px",
                          type:"text",
                 showSortAsc:true,
                },
               
                
                {
                  label: "Descriere",
                  field: "descriere",
                  width: "300px",
                          type:"text",
                 showSortAsc:true,
                },
               
                
                {
                  label: "Procent",
                  field: "procent",
                  width: "300px",
                          type:"number",
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
          cod_caen:"",
          descriere:"",
          procent:"",},
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
                     cod_caen:"",
                descriere:"",
                procent:"",
                

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