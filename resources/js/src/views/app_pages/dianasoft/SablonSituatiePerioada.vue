<template>

  <div class= "d-flex justify-content-center" >
    <b-modal
             id="ssp"  
             scrollable
             :size="size" 
             no-close-on-backdrop
             ok-variant="success"
             cancel-title="Renunt"              
             ok-title="Afisez"
             cancel-variant="warning"
             modal-class="modal-success"
             v-model="afisezSSPLocal"
             :title="titlu"
              @ok="handleOk"
              @cancel="handleCancel">
    
   <b-overlay
      :show="showLoading"
      rounded="xl"
     
      :variant="'primary'"
      :opacity="0.5"
      :blur="'10px'"
    >
                <b-row  class="d-flex justify-content-center">
                  <b-col  cols="4"> 
                 
                             
                              <datacalendaristica 
                                            :afisez="true"
                                            v-model="editVarLocal.datai" 
                                            name="datai" 
                                             campDisplay="Data început"
                                            >
                                
                              </datacalendaristica>

                             
                 </b-col>        
                  <b-col  cols="4">             
                     
                            
                              <datacalendaristica 
                                            :afisez="true"
                                            v-model="editVarLocal.datasf" 
                                            name="datasf" 
                                            campDisplay="Data sfârșit"
                                            >
                                
                              </datacalendaristica>

                              
                            
                         
                             
                      </b-col>
                     

                    </b-row>
                    <br>
                    <slot >
            
                    </slot>
                              
                       <br><br><br>    
                     
                        <b-row class="d-flex justify-content-center">
                           <b-col cols="3">
                            
                            <selectonearray
                                              :optiuni="[{denumire:'PDF'},{denumire:'Excel'}]"
                                              name="format_fisier"
                                              v-if="!nuAfisezFormatLocal"
                                              colCaut="denumire"
                                              camp="denumire"
                                              campDisplay="Format fisier"
                                              v-model="editVarLocal.format_fisier"
                                              limitToList="true"
                                              :pastrezvaloare="true"  
                                             >
                              </selectonearray>
                            
                       </b-col>   
                       
                     </b-row>
                    
             
             <br><br><br><br><br>
       
  </b-overlay>
   </b-modal>
  </div>
</template>

<script>

import { heightTransition } from "@core/mixins/ui/transition"


export default {
  props: {
        afisezSSP:Boolean,
        titlu:String,
        size:String,
        requestUrl:String,
        rutainapoi:String,
        numefis:String,
        editVar:Object,
        nuAfisezFormat:Boolean
        },
  components: {
    
    
  },
  mixins: [heightTransition],
  name:"SablonSituatiePerioada",
  data() {
    return {
          showLoading:false,   
          nuAfisezFormatLocal:this.nuAfisezFormat,    
          
          afisezSSPLocal:this.afisezSSP,
         settings: {
                  maxScrollbarLength: 60,
                  },
         editVarLocal:this.editVar ,
       
      }
  },
  watch: {
    nuAfisezFormat(){
          this.nuAfisezFormatLocal=this.nuAfisezFormat
      },
      
      editVar(){
          this.editVarLocal=Object.assign(this.editVar,this.editVarLocal)
      },
      afisezSSP(){
         // console.log(this.editVarLocal)
         this.afisezSSPLocal=this.afisezSSP
         if(this.afisezSSP){
          let dataCurenta =new Date(this.$store.state.app.lunaCurenta)
          let lastDay = new Date(dataCurenta.getFullYear(), dataCurenta.getMonth()+1, 0)
          lastDay.setHours(23,59,59);
          this.editVarLocal.datai=new Date(dataCurenta.getFullYear(),dataCurenta.getMonth(),1)

          this.editVarLocal.datasf=lastDay
          
          if(!this.editVarLocal.format_fisier){
          this.editVarLocal.format_fisier="Excel"
          }
          
         }
       },
       afisezSSPLocal(){
            if (this.afisezSSPLocal==false){
              this.$emit('closed')
            }
         
       },
     
  },
  
  methods: {
   
    initTrHeight() {
      this.trSetHeight(null)
      this.$nextTick(() => {
        if(this.$refs.form){
        this.trSetHeight(this.$refs.form.scrollHeight)
        }
      })
    },
   
      handleOk(bvModalEvent) {
          bvModalEvent.preventDefault()
          this.afisez()
        
    },
    handleCancel(){
             this.$router.push({ name: this.rutainapoi,  query: ""})
      },
    afisez(){
       
          this.showLoading=true
          const payLoad=Object.assign({},this.editVarLocal)
          payLoad.requestType="post"
        
          if(!String(this.editVarLocal.datai).includes(".")){
            payLoad.datai=new Date(this.editVarLocal.datai).toLocaleDateString()
          }else{
            payLoad.datai=this.editVarLocal.datai
          }
          if(!String(this.editVarLocal.datasf).includes(".")){

          payLoad.datasf=new Date(this.editVarLocal.datasf).toLocaleDateString()
          }else{
            payLoad.datasf=this.editVarLocal.datasf
          }
          
          payLoad.requestUrl=this.requestUrl
         this.$store.dispatch("app/api_blob_Request",payLoad)
                    .then(response=>{
                          
                    
                      if(this.editVarLocal.format_fisier=="PDF" && !this.nuAfisezFormatLocal){
                           this.editVarLocal={}
                           this.$router.push({ name: 'pdfviewer',  params: { blobPDF: response,
                                                                              numefis:this.numefis+".pdf",
                                                                              rutainapoi:this.rutainapoi}})
                      }
                        
                      if(this.editVarLocal.format_fisier=="Excel"  && !this.nuAfisezFormatLocal){
                           var fileURL = window.URL.createObjectURL(new Blob([response]));
                           var fileLink = document.createElement('a');
                           fileLink.href = fileURL;
                           
                             fileLink.setAttribute('download', this.numefis+'_'+(new Date().toLocaleDateString()).replaceAll(".","_")+'.xls');
                             this.editVarLocal={}
                           document.body.appendChild(fileLink);
                           fileLink.click();
                           this.$router.push({ name: this.rutainapoi,  query: ""})
                           
                      }
                      if(this.nuAfisezFormatLocal){
                           var fileURL = window.URL.createObjectURL(new Blob([response]));
                           var fileLink = document.createElement('a');
                           fileLink.href = fileURL;
                           
                             fileLink.setAttribute('download', this.numefis);
                             this.editVarLocal={}
                           document.body.appendChild(fileLink);
                           fileLink.click();
                           this.$router.push({ name: this.rutainapoi,  query: ""})
                           
                      }
                            this.showLoading=false
                           this.afisezSSPLocal=false
                           this.$emit('closed')
                       })
                     .catch(error => {

                                this.showLoading=false
                                 this.$bvToast.toast(error.data.message, 
                                                         {
                                                            title: `Eroare! `,
                                                            variant:"danger",
                                                            solid: true,
                                                            appendToast: false,
                                                            noAutoHide:true,
                                                            toaster: "b-toaster-top-right",
                                                                              }) 
                                              
                                })
                      
        
    },
   
    closeEditSideBar(){
        this.activeSSPLocal=false
        this.editVarLocal={}
        this.$emit('closed')
        
    },
    
    

  },
  mounted() {
    this.initTrHeight()
  },
  destroyed() {
    window.removeEventListener("resize", this.initTrHeight)
  },
    created() {

         if(!this.rutainapoi){
          this.rutainapoiLocal=this.modelName
         }
          window.addEventListener("resize", this.initTrHeight)
          
     
    },
}

</script>

<style lang="scss">
.datelabel{
  font-size:.75rem;
  color:gray;
}
#ssp .modal-header .close {
  display:none;
}
</style>