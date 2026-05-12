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

      <Taskaev
                         @stored="afisezSalvat"
                         @closed="aevClosed"
                         :activeAction="activeAction"
                         :activeEdit="activeEdit"
                         :editVar="editVar"
                         v-show="activeEdit"
                      > 
      </Taskaev>  
    
   </b-overlay>
</template>

<script>

import Taskaev from "./Taskaev.vue"

export default {
  props: {
        id:String,
        }, 
  components: {
    Taskaev
  },
  name:"task",
  data() {
    return {
        refreshLocal:false, 
        idselectat:null,
        campFiltruStart:"",
        modelName:"task",
        modelDisplayName:"Task",
        editVar:{
          assignedby_id:"",
          assignedto_id:"",
          title:"",
          description:"",
          duedate:"",
          tags:"",
          completed_at:"",
          iscompleted:"",
          isdeleted:"",
          isimportant:"",
          completedby_id:"",},
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
                  label: "Assigned By",
                  field: "assignedby.name",
                  width: "200px",
                          type:"text",
                 showSortAsc:true,
                },
               
                
                {
                  label: "Assigned To",
                  field: "assignedto.name",
                  width: "200px",
                          type:"text",
                 showSortAsc:true,
                },
               
                
                {
                  label: "Titlu",
                  field: "title",
                  width: "200px",
                          type:"text",
                 showSortAsc:true,
                },
               
                
                {
                  label: "Descriere",
                  field: "description",
                  width: "300px",
                          type:"text",
                 showSortAsc:true,
                },
               
                
                {
                  label: "Termen executare",
                  field: "duedate",
                  width: "200px",
                  type:"date",
                  dateInputFormat: "yyyy-MM-dd'T'HH:mm:ss.SSSSSS'Z'", // expects 2018-03-16
                  dateOutputFormat: "dd.MM.yyyy HH:mm:ss", // outputs Mar 16th 2018
                  //dateInputFormat: "yyyy-MM-dd", // expects 2018-03-16
                  //dateOutputFormat: "dd.MM.yyyy", // outputs Mar 16th 2018 
                 showSortAsc:true,
                },
               
                
                {
                  label: "Tags",
                  field: "tags",
                  width: "300px",
                          type:"text",
                 showSortAsc:true,
                },
               
                
                {
                  label: "Data executarii",
                  field: "completed_at",
                  width: "200px",
                  type:"date",
                  dateInputFormat: "yyyy-MM-dd'T'HH:mm:ss.SSSSSS'Z'", // expects 2018-03-16
                  dateOutputFormat: "dd.MM.yyyy HH:mm:ss", // outputs Mar 16th 2018
                  //dateInputFormat: "yyyy-MM-dd", // expects 2018-03-16
                  //dateOutputFormat: "dd.MM.yyyy", // outputs Mar 16th 2018 
                 showSortAsc:true,
                },
               
                
                {
                  label: "Executat",
                  field: "iscompleted",
                  width: "200px",
                          type:"text",
                 showSortAsc:true,
                },
               
                
                {
                  label: "Sters",
                  field: "isdeleted",
                  width: "200px",
                          type:"text",
                 showSortAsc:true,
                },
               
                
                {
                  label: "Important",
                  field: "isimportant",
                  width: "200px",
                          type:"text",
                 showSortAsc:true,
                },
               
                
                {
                  label: "Executat de catre",
                  field: "completedby.name",
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
          assignedby_id:"",
          assignedto_id:"",
          title:"",
          description:"",
          duedate:"",
          tags:"",
          completed_at:"",
          iscompleted:"",
          isdeleted:"",
          isimportant:"",
          completedby_id:"",},
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
                     assignedby_id:"",
                assignedto_id:"",
                title:"",
                description:"",
                duedate:"",
                tags:"",
                completed_at:"",
                isCompleted:"",
                isDeleted:"",
                isImportant:"",
                completedby_id:"",
                

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