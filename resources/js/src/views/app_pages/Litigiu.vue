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
                      <b-dropdown
                                                         
                                                          text="Situatii litigii"
                                                          variant="outline-info"
                                                          class="dropdown-icon-wrapper mr-1"
                                                        >
                                                        <template #button-content>
                                                                     
                                                                      <feather-icon
                                                                        icon="ListIcon"
                                                                        size="16"
                                                                        class="align-middle"
                                                                      />
                                                                    </template>

                                                          

                                                          <b-dropdown-item v-if="canViewSituatieLitigii" @click="situatieLitigii" >
                                                            Situatie litigii
                                                          </b-dropdown-item>                                                     
                                                        </b-dropdown>

                                              
                
                
        </tabelcomponent>
      </b-card>
      </b-col>
   </b-row > 
      <Litigiuaev
                         @stored="afisezSalvat"
                         @closed="aevClosed"
                         :activeAction="activeAction"
                         :activeEdit="activeEdit"
                         :editVar="editVar"
                         v-show="activeEdit"
                      > 
      </Litigiuaev>  
     <SituatieLitigii  
            @closed="afisezSL=false"
            :afisezSL="afisezSL"
            />   
   </b-overlay>
</template>

<script>

import Litigiuaev from "./Litigiuaev.vue"
import SituatieLitigii from "./rapoarte/SituatieLitigii.vue"

export default {
  props: {
        id:String,
        }, 
  components: {
    Litigiuaev,
    SituatieLitigii
  },
  name:"litigiu",
  data() {
    return {
        canViewSituatieLitigii:this.$userpermitt.can("viewSituatieLitigii"),
        refreshLocal:false, 
        idselectat:null,
        campFiltruStart:"",
        afisezSL:false,
        modelName:"litigiu",
        modelDisplayName:"Litigii",
        editVar:{
          numar_dosar:"",
          numar_vechi:"",
          data_dosar:"",
          institutie:"",
          departament:"",
          categorie_caz:"",
          stadiu_procesual:"",
          avocatul_apararii:"",
          avocatul_acuzarii:"",
          observatii:"",
          status:"",
          taxa_de_timbru:"",
          cheltuieli_de_judecata:"",
          parti:"",
          litigiicaleatac:[{}],
          litigiiparti:[{}],
          litigiisedinte:[{}],
          litigii:[{}],
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
                  label: "Numar dosar",
                  field: "numar_dosar",
                  width: "150px",
                          type:"text",
                 showSortAsc:true,
                },
               
                
               
                
                {
                  label: "Data dosar",
                  field: "data_dosar",
                  width: "150px",
                  type:"date",
                  //dateInputFormat: "yyyy-MM-dd'T'HH:mm:ss.SSSSSS'Z'", // expects 2018-03-16
                  //dateOutputFormat: "dd.MM.yyyy HH:mm:ss", // outputs Mar 16th 2018
                  dateInputFormat: "yyyy-MM-dd", // expects 2018-03-16
                  dateOutputFormat: "dd.MM.yyyy", // outputs Mar 16th 2018 
                 showSortAsc:true,
                },
                {
                  label: "Data modificare",
                  field: "data_modificare",
                  width: "200px",
                  type:"date",
                  dateInputFormat: "yyyy-MM-dd'T'HH:mm:ss.SSSSSS'Z'", // expects 2018-03-16
                  dateOutputFormat: "dd.MM.yyyy HH:mm:ss", // outputs Mar 16th 2018
                  //dateInputFormat: "yyyy-MM-dd", // expects 2018-03-16
                  //dateOutputFormat: "dd.MM.yyyy", // outputs Mar 16th 2018 
                 showSortAsc:true,
                },
                {
                  label: "Parti",
                  field: "parti",
                  width: "300px",
                          type:"text",
                 showSortAsc:true,
                },
                {
                  label: "Stadiu procesual",
                  field: "stadiu_procesual",
                  width: "150px",
                          type:"text",
                 showSortAsc:true,
                },
                {
                  label: "Institutia",
                  field: "institutie",
                  width: "250px",
                          type:"text",
                 showSortAsc:true,
                },
               
                
                {
                  label: "Departament",
                  field: "departament",
                  width: "150px",
                          type:"text",
                 showSortAsc:true,
                },
               
                
                {
                  label: "Categorie caz",
                  field: "categorie_caz",
                  width: "150px",
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
                
                 {
                  label: "Data ultimei verificari",
                  field: "data_ultimei_verificari",
                  width: "200px",
                  type:"date",
                  dateInputFormat: "yyyy-MM-dd'T'HH:mm:ss.SSSSSS'Z'", // expects 2018-03-16
                  dateOutputFormat: "dd.MM.yyyy HH:mm:ss", // outputs Mar 16th 2018
                  //dateInputFormat: "yyyy-MM-dd", // expects 2018-03-16
                  //dateOutputFormat: "dd.MM.yyyy", // outputs Mar 16th 2018 
                 showSortAsc:true,
                },
               
                
                {
                  label: "Avocatul apararii",
                  field: "avocatul_apararii",
                  width: "150px",
                          type:"text",
                 showSortAsc:true,
                },
               
                
                {
                  label: "Avocatul acuzarii",
                  field: "avocatul_acuzarii",
                  width: "150px",
                          type:"text",
                 showSortAsc:true,
                },
               
                
                {
                  label: "Observatii",
                  field: "observatii",
                  width: "300px",
                          type:"text",
                 showSortAsc:true,
                },
                 
                {
                  label: "Taxa de timbru",
                  field: "taxa_de_timbru",
                  width: "300px",
                          type:"text",
                 showSortAsc:true,
                },
               
                
                {
                  label: "Cheltuieli de judecata",
                  field: "cheltuieli_de_judecata",
                  width: "300px",
                          type:"text",
                 showSortAsc:true,
                },
                 
                    
        ],
     
    }
  },
 
  methods: {
    situatieLitigii(){
      this.afisezSL=true
      },
    aevClosed(){
       this.idselectat=null
      this.selectedID=""
      this.activeEdit=false
      this.editVar={
          numar_dosar:"",
          numar_vechi:"",
          data_dosar:"",
          institutie:"",
          departament:"",
          categorie_caz:"",
          stadiu_procesual:"",
          avocatul_apararii:"",
          avocatul_acuzarii:"",
          observatii:"",
          status:"",
          taxa_de_timbru:"",
          cheltuieli_de_judecata:"",
          parti:"",},
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
                     numar_dosar:"",
                numar_vechi:"",
                data_dosar:"",
                institutie:"",
                departament:"",
                categorie_caz:"",
                stadiu_procesual:"",
                avocatul_apararii:"",
                avocatul_acuzarii:"",
                observatii:"",
                status:"",
                taxa_de_timbru:"",
                cheltuieli_de_judecata:"",
                parti:"",
                litigii:[{}],
                litigiicaleatac:[{}],
                litigiiparti:[{}],
                litigiisedinte:[{}],

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