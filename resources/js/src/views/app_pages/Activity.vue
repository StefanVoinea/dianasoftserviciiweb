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
      <Activityaev
                         @stored="afisezSalvat"
                         @closed="aevClosed"
                         :activeAction="activeAction"
                         :activeEdit="activeEdit"
                         :editVar="editVar"
                         v-show="activeEdit"
                      > 
      </Activityaev>  
    
   </b-overlay>
</template>

<script>

import Activityaev from "./Activityaev.vue"

export default {
  props: {
        id:String,
        }, 
  components: {
    Activityaev
  },
  name:"activity",
  data() {
    return {
        refreshLocal:false, 
        idselectat:null,
        campFiltruStart:"",
        modelName:"activity",
        modelDisplayName:"Jurnal de operare",
        editVar:{
          user_id:"",
          company_id:"",
          subject:"",
          description:"",
          changes:"",},
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
                  label: "User",
                  field: "user.name",
                  width: "200px",
                  type:"text",
                 showSortAsc:true,
                },
               
                {
                  label: "Subiect",
                  field: "subject_type",
                  width: "200px",
                  type:"text",
                 showSortAsc:true,
                },
               
                
                {
                  label: "Description",
                  field: "description",
                  width: "200px",
                          type:"text",
                 showSortAsc:true,
                },
               
                
                {
                  label: "Modificari",
                  field: "changes",
                  width: "300px",
                          type:"text",
                 showSortAsc:true,
                },
                {
                  label: "Data operarii",
                  field: "created_at",
                  width: "200px",
                  type:"date",
                  dateInputFormat: "yyyy-MM-dd'T'HH:mm:ss.SSSSSS'Z'", // expects 2018-03-16
                  dateOutputFormat: "dd.MM.yyyy HH:mm:ss", // outputs Mar 16th 2018
                  //dateInputFormat: "yyyy-MM-dd", // expects 2018-03-16
                  //dateOutputFormat: "dd.MM.yyyy", // outputs Mar 16th 2018 
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
           
                                                     user_id:"",
                                                company_id:"",
                                                subject:"",
                                                description:"",
                                                changes:{before:"",
                                                        after:""} ,
                                                 user:{name:""},       
                                                      }
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
                     user_id:"",
                company_id:"",
                subject:"",
                description:"",
                changes:"",
                

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