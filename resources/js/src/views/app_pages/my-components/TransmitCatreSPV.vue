<template>

  <div class= "d-flex justify-content-center" >
    <b-modal
             id="ssp"  
             scrollable
             size="xl" 
             no-close-on-backdrop
             cancel-variant="warning"
             modal-class="modal-success"
             v-model="afisezTCSPVLocal"
             :title="titlu"
             :hide-footer="true">
    
   <b-overlay
      :show="showLoading"
      rounded="xl"
     
      :variant="'primary'"
      :opacity="0.5"
      :blur="'10px'"
    >
                <b-row  class="d-flex justify-content-center">
                  <b-col  cols="2"> 
                 
                             
                              <datacalendaristica 
                                            :afisez="true"
                                            v-model="editVarLocal.datai" 
                                            name="datai" 
                                             campDisplay="Data început"
                                            >
                                
                              </datacalendaristica>

                             
                 </b-col>        
                  <b-col  cols="2">             
                     
                            
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
                 
                       <br><br><br>    
                     
                        <b-row class="d-flex justify-content-center">
                          <b-col cols="1">
                            
                            <b-button 
                                          @click="verificareFacturi"
                                          variant="outline-success"
                                          class="btn-icon"
                                          size="lg"
                                           v-b-tooltip.hover.v-dark
                                          title="Verificare facturi"> 
                                           1. Verificare facturi
                                         
                            </b-button>           
                          </b-col>   
                       <b-col cols="1">
                            
                            <b-button 
                                          @click="transmitereFacturi"
                                          variant="outline-success"
                                          class="btn-icon "
                                          size="lg"
                                           v-b-tooltip.hover.v-dark
                                          title="Transmitere facturi"> 
                                           2. Transmitere facturi
                                         
                            </b-button>           
                          </b-col>   
                          <b-col cols="1">
                            
                            <b-button 
                                          @click="verificareStare"
                                          variant="outline-success"
                                          class="btn-icon "
                                          size="lg"
                                           v-b-tooltip.hover.v-dark
                                          title="Verificare stare"> 
                                           3. Verificare stare
                                         
                            </b-button>           
                          </b-col>   
                          <b-col cols="1">
                            
                            <b-button 
                                          @click="descarcareRaspunsuri"
                                          variant="outline-success"
                                          class="btn-icon "
                                          size="lg"
                                           v-b-tooltip.hover.v-dark
                                          title="Descarcare raspunsuri"> 
                                           4. Descarcare raspunsuri
                                         
                            </b-button>           
                          </b-col>   
                          <b-col cols="1">
                            
                            <b-button 
                                          @click="closeEditSideBar"
                                          variant="outline-warning"
                                          class="btn-icon "
                                          size="lg"
                                           v-b-tooltip.hover.v-dark
                                          title="Inchide fereastra"> 
                                           Inchide fereastra
                                         
                            </b-button>           
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
        afisezTCSPV:Boolean,
        titlu:String,
        size:String,
        requestUrl:String,
        rutainapoi:String,
        numefis:String,
         nuAfisezFormat:Boolean
        },
  components: {
    
    
  },
  mixins: [heightTransition],
  name:"TransmitCatreSPV",
  data() {
    return {
          showLoading:false,   
          nuAfisezFormatLocal:this.nuAfisezFormat,    
          afisezTCSPVLocal:this.afisezTCSPV,
         settings: {
                  maxScrollbarLength: 60,
                  },
         editVarLocal:{datai:"",
                       datasf:"" },
       
      }
  },
  watch: {
  
      afisezTCSPV(){
        
         this.afisezTCSPVLocal=this.afisezTCSPV
         if(this.afisezTCSPV){
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
       afisezTCSPVLocal(){
            if (this.afisezTCSPVLocal==false){
              this.$emit('closed')
            }
         
       },
     
  },
  
  methods: {
    verificareFacturi(){
          this.showLoading=true
          const payLoad=this.editVarLocal
          payLoad.requestType="post"
          payLoad.requestUrl="antetvanzare/verificarefacturi"
          this.$store.dispatch("app/api_blob_Request",payLoad)
                    .then(response=>{
                           
                           this.showLoading=false
                           var fileURL = window.URL.createObjectURL(new Blob([response]));
                           var fileLink = document.createElement('a');
                           fileLink.href = fileURL;
                           
                            fileLink.setAttribute('download', 'raport_verificare_facturi_emise_in_perioada.txt');
                             
                           document.body.appendChild(fileLink);
                           fileLink.click();
                           
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
   transmitereFacturi(){
          this.showLoading=true
          const payLoad=this.editVarLocal
          payLoad.requestType="post"
          payLoad.requestUrl="antetvanzare/transmiterefacturi"
          this.$store.dispatch("app/api_blob_Request",payLoad)
                    .then(response=>{
                           
                           this.showLoading=false
                           var fileURL = window.URL.createObjectURL(new Blob([response]));
                           var fileLink = document.createElement('a');
                           fileLink.href = fileURL;
                           
                            fileLink.setAttribute('download', 'raport_transmitere_facturi_emise.txt');
                           document.body.appendChild(fileLink);
                           fileLink.click();
                           
                      
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
   verificareStare(){
          this.showLoading=false
          const payLoad=this.editVarLocal
          payLoad.requestType="post"
          payLoad.requestUrl="antetvanzare/verificarestare"
          this.$store.dispatch("app/api_blob_Request",payLoad)
                    .then(response=>{
                           
                           this.showLoading=false
                           var fileURL = window.URL.createObjectURL(new Blob([response]));
                           var fileLink = document.createElement('a');
                           fileLink.href = fileURL;
                           
                            fileLink.setAttribute('download', 'raport_verificare_stare.txt');
                           document.body.appendChild(fileLink);
                           fileLink.click();
                           
                      
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
   descarcareRaspunsuri(){
          this.showLoading=true
          const payLoad=this.editVarLocal
          payLoad.requestType="post"
          payLoad.requestUrl="antetvanzare/descarcareraspunsuri"
          this.$store.dispatch("app/api_blob_Request",payLoad)
                    .then(response=>{
                           
                           this.showLoading=false
                           var fileURL = window.URL.createObjectURL(new Blob([response]));
                           var fileLink = document.createElement('a');
                           fileLink.href = fileURL;
                           
                            fileLink.setAttribute('download', 'raport_descarcare_raspunsuri.txt');
                           document.body.appendChild(fileLink);
                           fileLink.click();
                           
                      
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

    initTrHeight() {
      this.trSetHeight(null)
      this.$nextTick(() => {
        if(this.$refs.form){
        this.trSetHeight(this.$refs.form.scrollHeight)
        }
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
           let dataCurenta =new Date(this.$store.state.app.lunaCurenta)
          let lastDay = new Date(dataCurenta.getFullYear(), dataCurenta.getMonth()+1, 0)
          lastDay.setHours(23,59,59);
          this.editVarLocal.datai=new Date(dataCurenta.getFullYear(),dataCurenta.getMonth(),1)

          this.editVarLocal.datasf=lastDay
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