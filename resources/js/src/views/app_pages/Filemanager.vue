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
      <Filemanageraev
                         @stored="afisezSalvat"
                         @closed="aevClosed"
                         :activeAction="activeAction"
                         :activeEdit="activeEdit"
                         :editVar="editVar"
                         v-show="activeEdit"
                      > 
      </Filemanageraev>  
    
   </b-overlay>
</template>

<script>

import Filemanageraev from "./Filemanageraev.vue"

export default {
  props: {
       // grupa:String,
        }, 
  components: {
    Filemanageraev
  },
  name:"filemanager",
  data() {
    return {
        refreshLocal:false, 
        idselectat:null,
        ruta:"",
        campFiltruStart:"",
        modelName:"filemanager?grupa="+this.$route.name,
        modelDisplayName: (this.$route.name.charAt(0).toUpperCase() + this.$route.name.slice(1)).replaceAll("_"," "),
        editVar:{
                  gestiune_id:"",
                  grupa:"",
                  denumire:"",
                  data_ultimei_revizii:"",
                  status:"",
                  obs:"",
                  fisier:"",
                  fisier_original:"",
                  tip_fisier:"",
                  data_inceput:"",
                  data_sfarsit:"",
                },

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
                  label: "Denumire",
                  field: "denumire",
                  width: "500px",
                  type:"text",
                 showSortAsc:true,
                },
               
                
                {
                  label: "Data ultimei revizii",
                  field: "data_ultimei_revizii",
                  width: "150px",
                  type:"date",
                  //dateInputFormat: "yyyy-MM-dd'T'HH:mm:ss.SSSSSS'Z'", // expects 2018-03-16
                  //dateOutputFormat: "dd.MM.yyyy HH:mm:ss", // outputs Mar 16th 2018
                  dateInputFormat: "yyyy-MM-dd", // expects 2018-03-16
                  dateOutputFormat: "dd.MM.yyyy", // outputs Mar 16th 2018 
                 showSortAsc:true,
                },
               
                
          
                
                {
                  label: "Obs",
                  field: "obs",
                  width: "500px",
                  type:"text",
                 showSortAsc:true,
                },
               
                
               
                  
                    
        ],
     
    }
  },
 watch:{
      '$route': {
         handler: 'routeChanged', // Method to call when $route changes
         immediate: true // Call the handler immediately on component mount
         
      }
  },
  methods: {
    routeChanged(newRoute, oldRoute){
     
      if(oldRoute){
      window.location.reload();
    }
    },
      viewFisier(){
      if (!this.selectedID)
        {
               this.$bvToast.toast('Selectați o linie pentru vizualizare fisier!', {
                                                                        title: `Atentie! `,
                                                                        variant:'warning',
                                                                        solid: false,
                                                                        appendToast: true,
                                                                        autoHideDelay: 3000,
                                                                        toaster: 'b-toaster-bottom-right',
                                                                      })  
              
          }else{
          this.showLoading=true
          this.editVar=Object.assign({},this.selectedID)
          let payLoad=this.editVar
          payLoad.requestType="post"
          payLoad.requestUrl="/filemanager/viewfile/"+this.editVar.id
            
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
      }               
    
    },
    aevClosed(){
       this.idselectat=null
      this.selectedID=""
      this.activeEdit=false
      this.editVar={
          gestiune_id:"",
          grupa:"",
          denumire:"",
          data_ultimei_revizii:"",
          status:"",
          obs:"",
          fisier:"",
          fisier_original:"",
          tip_fisier:"",
          data_inceput:"",
          data_sfarsit:"",},
      this.activeAction=""
      this.refreshLocal=!this.refreshLocal
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
                    grupa:this.$route.name,
                    denumire:"",
                    data_ultimei_revizii:"",
                    status:"",
                    obs:"",
                    fisier:"",
                    fisier_original:"",
                    tip_fisier:"",
                    data_inceput:"",
                    data_sfarsit:"",
                  }
        this.activeEdit=true
    },
    
    edit() {
          /*this.idselectat=null
          this.editVar=Object.assign({},this.selectedID)
          this.activeAction="Modifică"
          this.activeEdit=true*/
         
    },
    view() {
        
          
          this.editVar=Object.assign({},this.selectedID)
          this.viewFisier()
          //this.activeAction="Vizualizează"
          //this.activeEdit=true
        
    },
    
    },
    created() {
      this.ruta=this.$route.name
      document.title=window.app_name+"->"+this.modelDisplayName
     // if(this.id!=null){
     //             this.idselectat=this.id
     //             this.campFiltruStart="id"
     // }
      this.listen()
     
    },
  
}

</script>