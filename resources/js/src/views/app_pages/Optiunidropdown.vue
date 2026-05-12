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
   
    <b-col cols="8" >
      <b-card>
    
        <tabelcomponent 
                      :cols="8"
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
   </b-row>
      <Optiunidropdownaev
                         @stored="afisezSalvat"
                         @closed="aevClosed"
                         :activeAction="activeAction"
                         :activeEdit="activeEdit"
                         :editVar="editVar"
                         v-show="activeEdit"
                      > 
      </Optiunidropdownaev>  
    
   </b-overlay>
</template>

<script>

import Optiunidropdownaev from "./Optiunidropdownaev.vue"

export default {
  props: {
        id:String,
        }, 
  components: {
    Optiunidropdownaev
  },
  name:"optiunidropdown",
  data() {
    return {
        refreshLocal:false, 
        idselectat:null,
        campFiltruStart:"",
        modelName:"optiunidropdown",
        modelDisplayName:"Optiuni dropdown",
        editVar:{
          comapny_id:"",
          field_name:"",
          field_option:"",},
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
                  label: "Field Name",
                  field: "field_name",
                  width: "300px",
                          type:"text",
                 showSortAsc:true,
                },
               
                
                {
                  label: "Field option",
                  field: "field_option",
                  width: "300px",
                          type:"text",
                 showSortAsc:true,
                },
               
                  
                    
        ],
     
    }
  },
 
  methods: {
    
    aevClosed(){
      this.activeEdit=false
      this.editVar={
          comapny_id:"",
          field_name:"",
          field_option:"",},
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
                     comapny_id:"",
                field_name:"",
                field_option:"",
                

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