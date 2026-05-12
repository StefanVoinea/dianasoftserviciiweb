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

                      <b-button 
                      variant="outline-success"
                      class="btn-icon mr-1"
                      size="lg" 
                      v-show="canAdd" 
                      v-b-tooltip.hover.v-dark
                      title="Drepturi utilizator"
                      @click="modificaDrepturi" 
                       > 
                        <feather-icon icon="UserCheckIcon" />

            </b-button>
                 <b-button 
                      variant="outline-success"
                      class="btn-icon mr-1"
                      size="lg" 
                      v-show="canAdd" 
                      v-b-tooltip.hover.v-dark
                      title="Copiază drepturi utilizator"
                      @click="copiazaDrepturi" 
                       > 
                        <feather-icon icon="UserCheckIcon" />
                        Copiază drepturi catre

            </b-button>
             <b-dropdown  text="Situații utilizatori"
                                      variant="outline-info"
                                      class="dropdown-icon-wrapper mr-1">
                                      <template #button-content>
                                       <feather-icon icon="ListIcon" size="16" class="align-middle"/>
                                      </template>

                                      <b-dropdown-item   @click="fisaUtilizator">
                                                           Fisa utilizator
                                      </b-dropdown-item>
                                      <b-dropdown-divider />
                                      <b-dropdown-item   @click="situateiDrepturiUtilizatori">
                                                           Situatie drepturi utilizatori
                                      </b-dropdown-item>
                                      <b-dropdown-divider />
                                                          
                                </b-dropdown>      
        </tabelcomponent>
      </b-card>
 </b-col>
   </b-row>
      <Utilizatoriaev
                         @stored="afisezSalvat"
                         @closed="aevClosed"
                         :activeAction="activeAction"
                         :activeEdit="activeEdit"
                         :editVar="editVar"
                         v-show="activeEdit"
                      > 
      </Utilizatoriaev>  
     <Utilizatoricopiazadrepturi
                         @stored="afisezSalvat"
                         @closed="copyClosed"
                         :activeAction="activeAction"
                         :activeCopy="activeCopy"
                         :editVar="editVarCopy"
                         v-show="activeCopy"
                      > 
      </Utilizatoricopiazadrepturi>  
      <Utilizatorimodificadrepturi
                         @stored="afisezSalvat"
                         @closed="modificaDrepturiClosed"
                         :activeAction="activeAction"
                         :activeCopy="activeModificaDrepturi"
                         :editVar="editVar"
                         v-show="activeModificaDrepturi"
                      > 
      </Utilizatorimodificadrepturi>  
   </b-overlay>
</template>

<script>

import Utilizatoriaev from "./Utilizatoriaev.vue"
import Utilizatoricopiazadrepturi from "./Utilizatoricopiazadrepturi.vue"
import Utilizatorimodificadrepturi from "./Utilizatorimodificadrepturi.vue"

export default {
  props: {
        id:String,
        }, 
  components: {
    Utilizatoriaev,
    Utilizatoricopiazadrepturi,
    Utilizatorimodificadrepturi
  },
  name:"utilizatori",
  data() {
    return {
        refreshLocal:false, 
        idselectat:null,
        campFiltruStart:"",
        modelName:"utilizatori",
        modelDisplayName:"Utilizatori",
        canAdd:this.$userpermitt.can("addUtilizatori"),
        editVarCopy:{ to:[],
                      from:"",
                      from_id:"",
          },
        editVar:{
          name:"",
          user_type:"",
          email:"",
          password:"",
          telefon:"",
          blocat:"",
          functia:"",
          status:"",
          link_poza:"",
          program_de_lucru:"",
          data_expirare_parola:"",
          departament:"",
          sex:"",
          grup:[],
          notificari:[]},
        activeEdit:false,
        activeCopy:false,
        activeModificaDrepturi:false,
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
                  label: "Nume",
                  field: "name",
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
                  width: "300px",
                          type:"text",
                 showSortAsc:true,
                },
               
             
                
                {
                  label: "Functia",
                  field: "functia",
                  width: "300px",
                          type:"text",
                 showSortAsc:true,
                },
               
              
                {
                  label: "Program de lucru",
                  field: "program_de_lucru",
                  width: "300px",
                          type:"text",
                 showSortAsc:true,
                },
               
             
                
                {
                  label: "Departament",
                  field: "departament",
                  width: "300px",
                          type:"text",
                 showSortAsc:true,
                },
               
             
                  
                    
        ],
     
    }
  },
 
  methods: {
    fisaUtilizator() {
          
         
          if (this.selectedID=='')
          {
             this.$bvToast.toast("Selectați un utilizator!", 
                                                 {
                                                    title: `Selectati un utilizator! `,
                                                    variant:"warning",
                                                    solid: false,
                                                    appendToast: true,
                                                    autoHideDelay: 3000,
                                                    toaster: "b-toaster-bottom-right",
                                                                      }) 
          
            
          }else
          {
              this.showLoading=true
              const payLoad=Object.assign({},this.editVar)
              payLoad.requestType="post"
              payLoad.requestUrl="/utilizatori/fisautilizator/"+this.selectedID.id
              this.$store.dispatch("app/api_blob_Request",payLoad)
                    .then(response=>{
                          
           
                    
                           var fileURL = window.URL.createObjectURL(new Blob([response]));
                           var fileLink = document.createElement('a');
                           fileLink.href = fileURL;
                           
                             fileLink.setAttribute('download', 'fisa_utilizator.xls');
                             
                           document.body.appendChild(fileLink);
                           fileLink.click();
                           
                           
                    
                            this.showLoading=false
                          
                           this.$emit('closed')
                       })
          
          
        }
          
      },
      situateiDrepturiUtilizatori() {
              this.showLoading=true
              const payLoad=Object.assign({},this.editVar)
              payLoad.requestType="post"
              payLoad.requestUrl="/utilizatori/situatieDrepturiUtilizatori"
              this.$store.dispatch("app/api_blob_Request",payLoad)
                    .then(response=>{
                          
           
                    
                           var fileURL = window.URL.createObjectURL(new Blob([response]));
                           var fileLink = document.createElement('a');
                           fileLink.href = fileURL;
                           
                             fileLink.setAttribute('download', 'situatieDrepturiUtilizatori.xlsx');
                             
                           document.body.appendChild(fileLink);
                           fileLink.click();
                           
                           
                    
                            this.showLoading=false
                          
                           this.$emit('closed')
                       })
          
          
        
          
      },
     copiazaDrepturi() {
          let id=this.selectedID
          this.fromID=""
          if (this.selectedID=='')
          {
            this.$bvToast.toast("Selectați un utilizator!", 
                                                 {
                                                    title: `Selectati un utilizator! `,
                                                    variant:"warning",
                                                    solid: false,
                                                    appendToast: true,
                                                    autoHideDelay: 3000,
                                                    toaster: "b-toaster-bottom-right",
                                                                      }) 
            
          }else
          {
          this.editVarCopy.from_id=this.selectedID.id
          this.editVarCopy.from=this.selectedID
          this.activeAction="Copiază"
          this.activeCopy=true
         
        }
      },
      
      modificaDrepturi() {
          
         
          if (this.selectedID=='')
          {
             this.$bvToast.toast("Selectați un utilizator!", 
                                                 {
                                                    title: `Selectati un utilizator! `,
                                                    variant:"warning",
                                                    solid: false,
                                                    appendToast: true,
                                                    autoHideDelay: 3000,
                                                    toaster: "b-toaster-bottom-right",
                                                                      }) 
          
            
          }else
          {
              this.showLoading=true
              const payLoad={}  
              payLoad.requestType="get"
              payLoad.requestUrl="/utilizatori/show/"+this.selectedID.id
              this.$store.dispatch("app/api_Request",payLoad)
              .then(response=>{
                           
                           this.editVar=response 
                           this.activeAction="Modifică drepturi"
                           this.showLoading=false
                           this.activeModificaDrepturi=true                 
              })
          
          
        }
          
      },
      modificaDrepturiClosed(){
      this.activeModificaDrepturi=false
      this.editVar={
          name:"",
          user_type:"",
          email:"",
          password:"",
          telefon:"",
          blocat:"",
          functia:"",
          status:"",
          link_poza:"",
          program_de_lucru:"",
          data_expirare_parola:"",
          departament:"",
          sex:"",
          grup:[],
          notificari:[]},
      this.activeAction=""
    }, 
    aevClosed(){
      this.idselectat=null
      this.selectedID=""
      this.activeEdit=false
      this.editVar={
          name:"",
          user_type:"",
          email:"",
          password:"",
          telefon:"",
          blocat:"",
          functia:"",
          status:"",
          link_poza:"",
          program_de_lucru:"",
          data_expirare_parola:"",
          departament:"",
          sex:"",
          grup:[],
          notificari:[]
        }
      this.activeAction=""
    },
    copyClosed(){
      this.activeCopy=false
      this.editVarCopy={
          to:[],
          
          from_id:"",
          from:""}
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
                     name:"",
                user_type:"",
                email:"",
                password:"",
                telefon:"",
                blocat:"",
                functia:"",
                status:"",
                link_poza:"",
                program_de_lucru:"",
                data_expirare_parola:"",
                departament:"",
                sex:"",
                grup:[],
                notificari:[]
                

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