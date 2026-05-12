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
                        <b-button 
                        v-if="canAbrogaDocument"
                      variant="outline-warning"
                      class="btn-icon mr-1"
                      size="" 
                      v-b-tooltip.hover.v-light
                      title="Abrogare document"
                      @click="confirmAbrogare"> 
                        <feather-icon icon="FileMinusIcon" />
                      </b-button>
                      <b-button 
                      variant="outline-success"
                      class="btn-icon mr-1"
                      size="" 
                      v-b-tooltip.hover.v-light
                      title="Arhiva"
                      @click="arhiva" 
                       > 
                        <feather-icon icon="ArchiveIcon" />

            </b-button>
                
        </tabelcomponent>
      </b-card>
      </b-col>
   </b-row > 
      <Documentepdfaev
                         @stored="afisezSalvat"
                         @closed="aevClosed"
                         :activeAction="activeAction"
                         :activeEdit="activeEdit"
                         :editVar="editVar"
                         v-show="activeEdit"
                      > 
      </Documentepdfaev>  
     
   </b-overlay>
</template>

<script>

import Documentepdfaev from "./Documentepdfaev.vue"

export default {
  props: {
        id:String,
        }, 
  components: {
    Documentepdfaev
  },
  name:"documentepdf",
  data() {
    return {
        canAbrogaDocument:this.$userpermitt.can("abrogaDocumente"),
        refreshLocal:false, 
        idselectat:null,
        campFiltruStart:"",
        modelName:"documentepdf",
        modelDisplayName:"Documente",
        editVar:{
          grupa:"",
          denumire:"",
          descriere:"",
          fisier:"",
          data:"",
          acces:"",},
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
                  label: "Grupa",
                  field: "grupa",
                  width: "300px",
                          type:"text",
                 showSortAsc:true,
                },
               
                
                {
                  label: "Denumire",
                  field: "denumire",
                  width: "500px",
                          type:"text",
                 showSortAsc:true,
                },
               
                
               
                
                {
                  label: "Data ultimei revizii",
                  field: "data",
                  width: "150px",
                  type:"date",
                  //dateInputFormat: "yyyy-MM-dd'T'HH:mm:ss.SSSSSS'Z'", // expects 2018-03-16
                  //dateOutputFormat: "dd.MM.yyyy HH:mm:ss", // outputs Mar 16th 2018
                  dateInputFormat: "yyyy-MM-dd", // expects 2018-03-16
                  dateOutputFormat: "dd.MM.yyyy", // outputs Mar 16th 2018 
                 showSortAsc:true,
                },
               
                
                {
                  label: "Acces",
                  field: "acces",
                  width: "200px",
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
               
                  
                    
        ],
     
    }
  },
 
  methods: {
    confirmAbrogare(){
      
        if (this.selectedID==null||this.selectedID=="")
          {
           this.$bvToast.toast('Selectați documentul de abrogat!', {
                                                                        title: `Atentie! `,
                                                                        variant:'warning',
                                                                        solid: false,
                                                                        appendToast: true,
                                                                        autoHideDelay: 3000,
                                                                        toaster: 'b-toaster-bottom-right',
                                                                      })  
            
          }else
          {
                    this.$bvModal
                                            .msgBoxConfirm('Abrogati documentul '+this.selectedID.denumire+' ?', {
                                              title: 'Abrogati?',
                                              size: 'sm',
                                              okVariant: 'warning',
                                              okTitle: 'Abrogare',
                                              cancelTitle: 'Cancel',
                                              cancelVariant: 'outline-secondary',
                                              hideHeaderClose: false,
                                              centered: true,
                                            })
                                            .then(value => {
                                              if(value){
                                                this.abrogareDocument()
                                              }
                                              

                                            })
                             
           }                
    },
     abrogareDocument() {
        
       
              this.editVar=Object.assign({},this.selectedID)
              this.showLoading=true
              const payLoad=Object.assign({},this.editVar)
              payLoad.requestType="post"
              payLoad.requestUrl="documentepdf/abrogare"
              this.$store.dispatch("app/api_Request",payLoad)
                        .then(response=>{
                          this.refreshLocal=!this.refreshLocal
                                    this.$bvToast.toast('Abrogare efectuata cu succes!', {
                                                                            title: `Abrogare cu succes! `,
                                                                            variant:'success',
                                                                            solid: false,
                                                                            appendToast: true,
                                                                            autoHideDelay: 3000,
                                                                            toaster: 'b-toaster-bottom-right',
                                                                          })              
                           
                                this.showLoading=false
                         })      
                    
                       
        
    },
    arhiva(){
         if (this.selectedID==null)
            {
               this.$bvToast.toast('Selectați un document!', {
                                                                        title: `Atentie! `,
                                                                        variant:'warning',
                                                                        solid: false,
                                                                        appendToast: true,
                                                                        autoHideDelay: 3000,
                                                                        toaster: 'b-toaster-bottom-right',
                                                                      })  
              
            }else
            {
            
               this.$router.push({ path: 'documentepdfarhiva?id='+this.selectedID.id})
          }
      },
    aevClosed(){
       this.idselectat=null
      this.selectedID=""
      this.activeEdit=false
      this.editVar={
          grupa:"",
          denumire:"",
          descriere:"",
          fisier:"",
          data:"",
          acces:"",},
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
                     grupa:"",
                denumire:"",
                descriere:"",
                fisier:"",
                data:"",
                acces:"",
                

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
          this.showLoading=true
          const payLoad=Object.assign({},this.editVar)
          payLoad.requestType="post"
          payLoad.requestUrl="documentepdf/show"
          this.$store.dispatch("app/api_blob_Request",payLoad)
                    .then(response=>{
                         this.$router.push({ name: 'pdfviewer',  params: { blobPDF: response,
                                                                              numefis:this.numefis+"_"+(new Date().toLocaleDateString()).replaceAll(".","_")+".pdf",
                                                                              rutainapoi:"documentepdf"}})
                       
                            this.showLoading=false
                     })       
                       
        
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