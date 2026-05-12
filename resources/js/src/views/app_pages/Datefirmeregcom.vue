<template>
   <div class= "d-flex justify-content-center" >
    <div>
      <b-card>
    
        <tabelcomponent 
                      :columnDefs="columnDefs"
                      :modelName="modelName"
                      :titlu="modelDisplayName"
                      :refresh="refreshLocal"
                      :idselectat="idselectat"
                      :campFiltruStart="campFiltruStart"
                      @onSelectionChanged="onSelectionChanged"
                      @adauga="add"
                      @edit="edit"
                      @view="view">

                    </vx-tooltip>  
        </tabelcomponent>
      </b-card>

     
    </div>
  </div>
</template>

<script>


export default {
  props: {
        id:String,
        }, 
  components: {
   

  },
  name:"datefirmeregcomComponent",
  data() {
    return {
        refreshLocal:false,
      
        idselectat:null,
        campFiltruStart:"",
        modelName:"datefirmeregcom",
        modelDisplayName:"Date firme registrul comertului",
        editVar:{},
        activeEdit:false,
        activeAction:"",
        
        selectedID:"",
        columnDefs: [
                {
                  label: "Denumire",
                  field: "denumire",
                  width: 500,
                  filter: true,
                  checkboxSelection: false,
                  headerCheckboxSelectionFilteredOnly: false,
                  headerCheckboxSelection: false,
                  pinned: "left"
                },
                {
                  label: "Cod fiscal",
                  field: "cui",
                  width: 200,
                  filter: true,
                  checkboxSelection: false,
                  headerCheckboxSelectionFilteredOnly: false,
                  headerCheckboxSelection: false,
                  pinned: "left"
                },
                {
                  label: "Reg Com",
                  field: "regcom",
                  width: 200,
                  filter: true,
                  checkboxSelection: false,
                  headerCheckboxSelectionFilteredOnly: false,
                  headerCheckboxSelection: false,
                  
                },
                {
                  label: "Adresa",
                  field: "adresa",
                  width: 500,
                  filter: true,
                  checkboxSelection: false,
                  headerCheckboxSelectionFilteredOnly: false,
                  headerCheckboxSelection: false,
                
                },
                {
                  label: "Localitate",
                  field: "localitate",
                  width: 200,
                  filter: true,
                  checkboxSelection: false,
                  headerCheckboxSelectionFilteredOnly: false,
                  headerCheckboxSelection: false,
                  
                },
                {
                  label: "Judet",
                  field: "judet",
                  width: 200,
                  filter: true,
                  checkboxSelection: false,
                  headerCheckboxSelectionFilteredOnly: false,
                  headerCheckboxSelection: false,
                 
                },
                {
                  label: "Telefon",
                  field: "telefon",
                  width: 200,
                  filter: true,
                  checkboxSelection: false,
                  headerCheckboxSelectionFilteredOnly: false,
                  headerCheckboxSelection: false,
                 
                },
                {
                  label: "Email",
                  field: "email",
                  width: 200,
                  filter: true,
                  checkboxSelection: false,
                  headerCheckboxSelectionFilteredOnly: false,
                  headerCheckboxSelection: false,
                 
                },
               
      ],
     
     
    }
  },
 
  methods: {
    
    aevClosed(){
      this.activeEdit=false
      this.editVar={}
      this.activeAction=""
    },
   
    listen(){
                // Echo.channel("bancomatic_dianasoft")
                //     .listen("UserSignedUp", (e) => {
                //        // this.getRecords()
                //        // console.log("AM PRIMIT")
                //      });
    },
    onSelectionChanged(value){
      this.selectedID=value
      
    },
    
   add(){

   },
   edit(){

   },
   view(){

   },
    
   importXLS(e){
                        this.showLoading=true
                        e.preventDefault();
                        this.uploadFileActive=false
                       let currentObj = this;
                       const config = {
                            headers: { 'content-type': 'multipart/form-data' }
                       }
                      let formData = new FormData();
                      formData.append('file', this.file);
                       axios.post('/'+this.modelName+'/import', formData, config)
                          .then(response => {
                           
                             this.tblRecords=response
                             this.file=''
                             this.showLoading=false
                             this.$vs.notify({
                            title: "Upload efectuat cu succes!",
                            text: "Upload efectuat cu succes!",
                            iconPack: "feather",
                            icon: "icon-check",
                            color: "success"
                        })
                             
                              })
                        .catch(error => {
                           this.showLoading=false
                            this.handleErrors(error)
                          })
      },
    
    },
    created() {
      document.title=window.app_name+"->Date firme"
      if(this.id!=null){
                  this.idselectat=this.id
                  this.campFiltruStart="id"
      }
      this.listen()
     
    },
  
}

</script>

