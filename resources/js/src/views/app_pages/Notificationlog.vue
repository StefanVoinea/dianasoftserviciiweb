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
      <Notificationlogaev
                         @stored="afisezSalvat"
                         @closed="aevClosed"
                         :activeAction="activeAction"
                         :activeEdit="activeEdit"
                         :editVar="editVar"
                         v-show="activeEdit"
                      > 
      </Notificationlogaev>  
    
   </b-overlay>
</template>

<script>

import Notificationlogaev from "./Notificationlogaev.vue"

export default {
  props: {
        id:String,
        }, 
  components: {
    Notificationlogaev
  },
  name:"notificationlog",
  data() {
    return {
        refreshLocal:false, 
        idselectat:null,
        campFiltruStart:"",
        modelName:"notificationlog",
        modelDisplayName:"Jurnal notificari",
        editVar:{
          notificationtype_id:"",
          from_id:"",
          user_id:"",
          channel:"",
          email:"",
          telefon:"",
          title:"",
          subtitle:"",
          type:"",
          icon:"",
          avatar:"",
          link:"",
          category:"",
          read_at:"",},
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
                  label: "Tip notificare",
                  field: "notificationtype.denumire",
                  width: "200px",
                          type:"text",
                 showSortAsc:true,
                },
               
                
                {
                  label: "De la",
                  field: "from.name",
                  width: "200px",
                          type:"text",
                 showSortAsc:true,
                },
               
                
                {
                  label: "Catre",
                  field: "user.name",
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
                  field: "subtitle",
                  width: "300px",
                          type:"text",
                 showSortAsc:true,
                },
                {
                  label: "Canal de comunicare",
                  field: "channel",
                  width: "200px",
                          type:"text",
                 showSortAsc:true,
                },
                {
                  label: "Citit la",
                  field: "read_at",
                  width: "300px",
                  type:"date",
                  dateInputFormat: "yyyy-MM-dd'T'HH:mm:ss.SSSSSS'Z'", // expects 2018-03-16
                                    
                  dateOutputFormat: "dd.MM.yyyy HH:mm:ss", // outputs Mar 16th 2018
                  //dateInputFormat: "yyyy-MM-dd", // expects 2018-03-16
                  //dateOutputFormat: "dd.MM.yyyy", // outputs Mar 16th 2018 
                 showSortAsc:true,
                },
                {
                  label: "Link",
                  field: "link",
                  width: "300px",
                          type:"text",
                 showSortAsc:true,
                },
                
                {
                  label: "Email",
                  field: "email",
                  width: "300px",
                          type:"text",
                 showSortAsc:true,
                },
               
                
                {
                  label: "Telefon",
                  field: "telefon",
                  width: "200px",
                          type:"text",
                 showSortAsc:true,
                },
               
                
               
               
                
                {
                  label: "Tip",
                  field: "type",
                  width: "300px",
                          type:"text",
                 showSortAsc:true,
                },
               
                
                {
                  label: "Icon",
                  field: "icon",
                  width: "300px",
                          type:"text",
                 showSortAsc:true,
                },
               
                
                {
                  label: "Avatar",
                  field: "avatar",
                  width: "300px",
                          type:"text",
                 showSortAsc:true,
                },
               
                 
               
                
                {
                  label: "Categorie notificari",
                  field: "category",
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
          notificationtype_id:"",
          from_id:"",
          user_id:"",
          channel:"",
          email:"",
          telefon:"",
          title:"",
          subtitle:"",
          type:"",
          icon:"",
          avatar:"",
          link:"",
          category:"",
          read_at:"",},
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
                     notificationtype_id:"",
                from_id:"",
                user_id:"",
                channel:"",
                email:"",
                telefon:"",
                title:"",
                subtitle:"",
                type:"",
                icon:"",
                avatar:"",
                link:"",
                category:"",
                read_at:"",
                

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