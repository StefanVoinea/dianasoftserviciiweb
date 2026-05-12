<template>
  <SablonSituatieData  
                          :afisezSSD="afisezSLLocal"
                          size="lg"
                          titlu="Situatie litigii"
                          requestUrl="situatielitigii"
                          rutainapoi="litigii"
                          numefis="Situatie_litigii"
                          :editVar="editVarLocal"
                          @closed="afisezSLClosed">
                          
                              <br><br>
                       
  </SablonSituatieData> 
</template>

<script>
import VuePerfectScrollbar from "vue-perfect-scrollbar"



var FileSaver = require('file-saver');

export default {
  props: {
         afisezSL:Boolean,
          },
  components: {
    VuePerfectScrollbar,
    
  
  },
  name:"SituatieLitigii",
  data() {
    return {
         afisezSLLocal:this.afisezSL,
         showLoading:false,
         settings: {
                  maxScrollbarLength: 60,
                  },
         editVarLocal:{
                       doar_cu_sold:false
                      } ,        
        
      }
  },
  watch: {
    
      afisezSL(){
         
         this.afisezSLLocal=this.afisezSL
         if(this.afisezSL){
          let dataCurenta =new Date()
             this.editVarLocal.data=dataCurenta
          
          
          
         }
       },
       afisezSLLocal(){
            if (this.afisezSLLocal==false){
              this.$emit('closed')
            }
           
       },
     
  },
  
  methods: {
   
    
    
 
   
     afisezSLClosed(){
     
        this.activeSCELocal=false
        this.editVarLocal={doar_cu_sold:false}
        this.$emit('closed')
        
  },
  
    closeEditSideBar(){
        this.activeSCELocal=false
        this.editVarLocal={}
        this.$emit('closed')
        
    },
    
    

  },
 
}

</script>

