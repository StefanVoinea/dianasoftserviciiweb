<template>

   
  <div class= "d-flex justify-content-center" >
    <b-modal
             id="ssd"  
             scrollable
             :size="size" 
             no-close-on-backdrop
             ok-variant="success"
             cancel-title="Renunt"              
             ok-title="Afisez"
             cancel-variant="warning"
             modal-class="modal-success"
             v-model="afisezSSGLocal"
             :title="titlu"
              @ok="handleOk"
               @cancel="handleCancel">

              <b-overlay
                  :show="showLoading"
                  rounded="lg"
                  :variant="'primary'"
                  :opacity="0.5"
                  :blur="'10px'">

             
                    <br>
                    <slot >
            
                    </slot>
                              
                       <br><br><br>    
                     
                        <b-row class="d-flex justify-content-center">
                           <b-col cols="3">
                                                    
                            <selectonearray
                                              :optiuni="[{denumire:'PDF'},{denumire:'Excel'}]"
                                              name="format_fisier"
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
        afisezSSG:Boolean,
        titlu:String,
        size:String,
        requestUrl:String,
        rutainapoi:String,
        numefis:String,
        editVar:Object,

        },
  components: {
    
    
  },
  mixins: [heightTransition],
  name:"SablonSituatieGol",
  data() {
    return {
          showLoading:false,       
         afisezSSGLocal:this.afisezSSG,
         settings: {
                  maxScrollbarLength: 60,
                  },
         editVarLocal:this.editVar ,
       
      }
  },
  watch: {
      editVar(){
          this.editVarLocal=Object.assign(this.editVar,this.editVarLocal)
      },
      afisezSSG(){
        
         this.afisezSSGLocal=this.afisezSSG
         if(this.afisezSSG){
          
          this.editVarLocal.format_fisier="Excel"
          
         }
       },
       afisezSSGLocal(){
            if (this.afisezSSGLocal==false){
              
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
          payLoad.requestUrl=this.requestUrl
       
          this.$store.dispatch("app/api_blob_Request",payLoad)
                    .then(response=>{
                          
                           if(this.numefis.includes(".zip")){
                           var fileURL = window.URL.createObjectURL(new Blob([response]));
                           var fileLink = document.createElement('a');
                           fileLink.href = fileURL;
                           
                             fileLink.setAttribute('download', this.numefis);
                             this.editVarLocal={}
                           document.body.appendChild(fileLink);
                           fileLink.click();
                           this.$router.push({ name: this.rutainapoi,  query: ""})
                        }

                           if(this.editVarLocal.format_fisier=="PDF"){
                           this.editVarLocal={}
                           this.$router.push({ name: 'pdfviewer',  params: { blobPDF: response,
                                                                              numefis:this.numefis+"_"+(new Date().toLocaleDateString()).replaceAll(".","_")+".pdf",
                                                                              rutainapoi:this.rutainapoi}})
                        }
                        
                        if(this.editVarLocal.format_fisier=="Excel"){
                           var fileURL = window.URL.createObjectURL(new Blob([response]));
                           var fileLink = document.createElement('a');
                           fileLink.href = fileURL;
                           
                             fileLink.setAttribute('download', this.numefis+'_'+(new Date().toLocaleDateString()).replaceAll(".","_")+'.xls');
                             this.editVarLocal={}
                           document.body.appendChild(fileLink);
                           fileLink.click();
                           this.$router.push({ name: this.rutainapoi,  query: ""})
                           
                        }
                           this.showLoading=false
                            this.afisezSSGLocal=false
                           
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
#ssd .modal-header .close {
  display:none;
}
</style>