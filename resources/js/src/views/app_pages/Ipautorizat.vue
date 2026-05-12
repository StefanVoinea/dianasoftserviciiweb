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
                
        </tabelcomponent>
      </b-card>

      <Ipautorizataev
                         @stored="afisezSalvat"
                         @closed="aevClosed"
                         :activeAction="activeAction"
                         :activeEdit="activeEdit"
                         :editVar="editVar"
                         v-show="activeEdit"
                      > 
      </Ipautorizataev>  
    
   </b-overlay>
</template>

<script>

import Ipautorizataev from "./Ipautorizataev.vue"

export default {
  props: {
        id:String,
        }, 
  components: {
    Ipautorizataev
  },
  name:"ipautorizat",
  data() {
    return {
        refreshLocal:false, 
        idselectat:null,
        campFiltruStart:"",
        modelName:"ipautorizat",
        modelDisplayName:"IP-uri autorizate",
        editVar:{
          ip:"",
          utilizator:"",},
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
                  label: "IP",
                  field: "ip",
                  width: "300px",
                          type:"text",
                 showSortAsc:true,
                },
               
                
                {
                  label: "Utilizator",
                  field: "utilizator",
                  width: "300px",
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
          ip:"",
          utilizator:"",},
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
                     ip:"",
                utilizator:"",
                

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