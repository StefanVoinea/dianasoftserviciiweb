<template>
  <validation-observer ref="simpleRules">
  <div class= "d-flex justify-content-center" >
   <b-modal :id="emitator"  
             size="xl" 
             no-close-on-backdrop
             
             ok-variant="success"
             cancel-title="Cancel"              
             ok-title="Save"
             cancel-variant="warning"
             scrollable
             modal-class="modal-success"
             :title="'OCR carte de identitate'"
             v-model="activateOcrCILocal"
             @ok="handleOk"
             @cancel="aevClosed"
             >
     <form  ref="form"  @submit.stop.prevent="handleSubmit" >
             <br>

           <b-row class="d-flex justify-content-center" >
            <b-col  cols="6">
            <b-img :src="editVarLocal.filename" fluid alt="Carte de identitate" />

             </b-col> 
                <b-col  cols="6">
                 <b-row class="d-flex justify-content-center" >
                 <b-col  cols="4">
                <b-row class="d-flex justify-content-center" >
                  <b-col  cols="12">
                      <label> CNP</label>
                      <b-form-input
                                  @blur="modificaCNP"
                                  :state="cnpValid"
                                  type="number" 
                                  name="cnp"   
                                  v-model="editVarLocal.cnp"
                                   />

                   <b-form-invalid-feedback>
                    {{"CNP invalid!"}}
                  </b-form-invalid-feedback>
                  <b-form-valid-feedback>
                    {{"CNP valid!"}}
                  </b-form-valid-feedback>
                </b-form-group>
                  </b-col>
                </b-row>
                <b-row class="d-flex justify-content-center" >
                  <b-col  cols="12">
                      <label> Nume</label>
                      <b-form-input
                                  name="nume"   
                                  v-model="editVarLocal.nume"
                                   />
                  </b-col>
                </b-row>
                 <b-row class="d-flex justify-content-center" >
                  <b-col  cols="12">
                      <label> Prenume</label>
                      <b-form-input
                                  name="prenume"   
                                  v-model="editVarLocal.prenume"
                                   />
                  </b-col>
                </b-row>
                 <b-row class="d-flex justify-content-center" >
                  <b-col  cols="12">
                      <label>Sex</label>
                      <b-form-input
                                  name="sex"   
                                  v-model="editVarLocal.sex"
                                   />
                  </b-col>
                </b-row>
                <b-row class="d-flex justify-content-center" >
                  <b-col  cols="12">
                      <label> Seria</label>
                      <b-form-input
                                  name="seria"   
                                  v-model="editVarLocal.ci_seria"
                                   />
                  </b-col>
                </b-row>
                <b-row class="d-flex justify-content-center" >
                  <b-col  cols="12">
                      <label> Numar</label>
                      <b-form-input
                                  name="numar"   
                                  v-model="editVarLocal.ci_numar"
                                   />
                  </b-col>
                </b-row>
                <b-row class="d-flex justify-content-center" >
                  <b-col  cols="12">
                       <datacalendaristica v-model="editVarLocal.data_nasterii" 
                                          id="data_nasterii" 
                                          name="data_nasterii" 
                                          :endDate="new Date()"
                                          :state="(new Date(dateFormatGeneral(editVarLocal.data_nasterii)))<(new Date())" 
                                          campDisplay="Data nasterii" />
                  </b-col>
                </b-row>
                </b-col>

            <b-col  cols="8">
                <b-row class="d-flex justify-content-center" >
                  <b-col  cols="12">
                      <label> Domiciliu</label>
                      <b-form-input
                                  name="adresa"   
                                  v-model="editVarLocal.adresa"
                                   />
                  </b-col>
                </b-row>
                <b-row class="d-flex justify-content-center" >
                  <b-col  cols="12">
                      <label> Locul nasterii</label>
                      <b-form-input
                                  name="loc_nastere"   
                                  v-model="editVarLocal.loc_nastere"
                                   />
                  </b-col>
                </b-row>
                <b-row class="d-flex justify-content-center" >
                  <b-col  cols="12">
                      <label> Emis de</label>
                      <b-form-input
                                  name="emisde"   
                                  v-model="editVarLocal.emisde"
                                   />
                  </b-col>
                </b-row>
                <br>
                <b-row class="d-flex justify-content-center" >
                  <b-col  cols="12">
                       
                      <datacalendaristica v-model="editVarLocal.dataemitere" 
                                          id="dataemitere" 
                                          name="dataemitere" 
                                          :endDate="new Date()"
                                          :state="(new Date(dateFormatGeneral(editVarLocal.dataemitere)))<(new Date())" 
                                          campDisplay="Data emitere" >
                       </datacalendaristica>                   
                  </b-col>
                   </b-row>
                  <b-row class="d-flex justify-content-center" >
                  <b-col  cols="12">
                      <datacalendaristica v-model="editVarLocal.valabilitate" 
                                          id="valabilitate" 
                                          name="valabilitate" 
                                          :startDate="new Date()"
                                          :state="(new Date(dateFormatGeneral(editVarLocal.valabilitate)))>(new Date())" 
                                          campDisplay="Data expirare"  />
                      
                    </b-col>
                </b-row>
                <br>
                 <b-row class="d-flex justify-content-center" >
                            <b-col  cols="12">
                               
                                     <judetcomponent 
                                                  v-model="editVarLocal.judet">

                                  </judetcomponent>
                                
                            </b-col>
                           </b-row> 
                             <b-row class="d-flex justify-content-center" >
                            <b-col  cols="12">
                             
                                      <localitatecomponent 
                                                   :judet="editVarLocal.judet"
                                                  v-model="editVarLocal.localitate">

                                  </localitatecomponent>
                                 
                            </b-col>
                           </b-row> 
                  </b-col>
                </b-row>
                 </b-col>
                </b-row>
              <br><br><br><br><br><br><br><br><br><br><br>
      </form>
    </b-modal>
  </div>
   </validation-observer>
</template>

<script>
import Ripple from "vue-ripple-directive"
import { heightTransition } from "@core/mixins/ui/transition"
import {VBModal} from "bootstrap-vue"
import {  required, email, confirmed, password,min} from "@validations"
import { ValidationProvider, ValidationObserver} from "vee-validate"

export default {
  props: {
         activateOcrCI:Boolean,
         editVar:Object,
         emitator:String
       
        },
  mixins: [heightTransition],
  components: {
     ValidationProvider, ValidationObserver
  },
   directives: {
    "b-modal": VBModal,
    Ripple,
  },
  name:"OcrCI",
  data() {
    return {
       required,
        password,
        email,
        confirmed,
         min,
        rutainapoiLocal:this.rutainapoi,
        activateOcrCILocal:false,
        editVarLocal:this.editVar,
        cnpValid:null,
        nextTodoId: 2,
        showLoading:false,
       
      }
  },
  watch: {
      
      activateOcrCI(){
         
         this.activateOcrCILocal=this.activateOcrCI
         this.editVarLocal=this.editVar
         if(this.editVarLocal.sex=="M"){
                this.editVarLocal.sex=="Masculin"
              }else{
                this.editVarLocal.sex=="Feminin"
              }
         if(this.editVarLocal.Valid=='Da'){
           /* try{
            this.editVarLocal.data_nasterii=new Date(Number("19"+this.editVarLocal.cnp.substr(1,2)),Number(this.editVarLocal.cnp.substr(3,2))-1,Number(this.editVarLocal.cnp.substr(5,2))).toLocaleDateString()
            }catch(error){}
            */
            this.cnpValid=true
         }else{
            if(this.editVarLocal.Valid=='Nu'){
            this.cnpValid=false
             }else{
                this.cnpValid=null
             }

         }
          if(this.editVarLocal.data_nasterii){
          this.editVarLocal.data_nasterii=this.dateFormatGeneral(this.editVarLocal.data_nasterii)
         } 
         if(this.editVarLocal.dataemitere){
          this.editVarLocal.dataemitere=this.dateFormatGeneral(this.editVarLocal.dataemitere)
         }
        if(this.editVarLocal.valabilitate){
          this.editVarLocal.valabilitate=this.dateFormatGeneral(this.editVarLocal.valabilitate)
         }

       },
       activateOcrCILocal(){
            if (this.activateOcrCILocal==false){
              this.$emit('closed')
            }
            
       },
     emitator(){
      this.editVarLocal.emitator=this.emitator
     },
      editVar(){
        this.editVarLocal=Object.assign({},this.editVar)
        if(this.editVarLocal.sex=="M"){
                this.editVarLocal.sex=="Masculin"
              }else{
                this.editVarLocal.sex=="Feminin"
              }
         if(this.editVarLocal.Valid=='Da'){
          //  this.editVarLocal.data_nasterii=new Date(this.editVarLocal.cnp.substr(5,2)+"."+this.editVarLocal.cnp.substr(3,2)+".19"+this.editVarLocal.cnp.substr(1,2)).toLocaleDateString()
           
            this.cnpValid=true
         }else{
            if(this.editVarLocal.Valid=='Nu'){
            this.cnpValid=false
             }else{
                this.cnpValid=null
             }

         }  
          if(this.editVarLocal.data_nasterii){
          this.editVarLocal.data_nasterii=this.dateFormatGeneral(this.editVarLocal.data_nasterii)
         }     
         if(this.editVarLocal.dataemitere){
          this.editVarLocal.dataemitere=this.dateFormatGeneral(this.editVarLocal.dataemitere)
         }     
         if(this.editVarLocal.valabilitate){
          this.editVarLocal.valabilitate=this.dateFormatGeneral(this.editVarLocal.valabilitate)
         }     
      }
  },
  
  methods: {
     dateFormatGeneral(dataLocala){

        if(dataLocala){
        if(dataLocala.toString().includes(".")){
        let data=dataLocala.split(".")
        return new Date(data[2],Number(data[1])-1,data[0])
        }else{
            return dataLocala
        }
        }else{
            return dataLocala
        }
        },
    modificaCNP(){
        if (this.validCNP(this.editVarLocal.cnp)){
            this.editVarLocal.Valid="Da" 
           // this.editVarLocal.data_nasterii=new Date(this.editVarLocal.cnp.substr(5,2)+"."+this.editVarLocal.cnp.substr(3,2)+".19"+this.editVarLocal.cnp.substr(1,2)).toLocaleDateString()
            }else{
                this.editVarLocal.Valid="Nu"
            }
        if(this.editVarLocal.Valid=='Da'){
          //this.editVarLocal.data_nasterii=new Date(this.editVarLocal.cnp.substr(5,2)+"."+this.editVarLocal.cnp.substr(3,2)+".19"+this.editVarLocal.cnp.substr(1,2)).toLocaleDateString()
            this.cnpValid=true
         }else{
            if(this.editVarLocal.Valid=='Nu'){
            this.cnpValid=false
             }else{
                this.cnpValid=null
             }

         }    
    },
    validCNP( p_cnp ) {
          var i=0 , year=0 , hashResult=0 , cnp=[] , hashTable=[2,7,9,1,4,6,3,5,8,2,7,9];
          if( p_cnp.length != 13 ) { 
           
            return false; }
          for( i=0 ; i<13 ; i++ ) {
              cnp[i] = parseInt( p_cnp.charAt(i) , 10 );
              if( isNaN( cnp[i] ) ) { 
               
                return false; 
                }
              if( i < 12 ) { hashResult = hashResult + ( cnp[i] * hashTable[i] ); }
          }
          hashResult = hashResult % 11;
          if( hashResult === 10 ) { hashResult = 1; }
          year = (cnp[1]*10)+cnp[2];
          switch( cnp[0] ) {
              case 1  : case 2 : { year += 1900; } break;
              case 3  : case 4 : { year += 1800; } break;
              case 5  : case 6 : { year += 2000; } break;
              case 7  : case 8 : case 9 : { year += 2000; if( year > ( parseInt( new Date().getYear() , 10 ) - 14 ) ) { year -= 100; } } break;
              default : { 
                
                return false; }
          }
          if( year < 1800 || year > 2099 ) { 
           
            return false; }
           
          return ( cnp[12] == hashResult );
        },
    initTrHeight() {
      this.trSetHeight(null)
      this.$nextTick(() => {
        if(this.$refs.form){
        this.trSetHeight(this.$refs.form.scrollHeight)
        }
      })
    },
   
    
     handleOk(bvModalEvt){
       bvModalEvt.preventDefault()
        this.$refs.simpleRules.validate().then(success => {
        if (success) {
       
          this.saveScan()
       
         }
      })
    },
    saveScan(){
        
          this.showLoading=true
                            this.editVarLocal.dataemitere=this.editVarLocal.dataemitere
                            this.editVarLocal.data_nasterii=this.editVarLocal.data_nasterii
                            this.editVarLocal.valabilitate=this.editVarLocal.valabilitate
                            this.editVarLocal.dataemitere
                            this.editVarLocal.data_nasterii
                            this.editVarLocal.valabilitate
                            this.editVarLocal.emitator=this.emitator
                            
                            this.$emit("scannedCI",this.editVarLocal)              
                            this.editVarLocal={ filename:""}
                            
                            this.showLoading=false
                            this.activateOcrCILocal=false
                           
                            this.$emit("closed")
          
                   
         
    },
   
    aevClosed(){
        this.activateOcrCILocal=false
        this.editVarLocal={filename:"" }
        
        this.$emit("closed")
        
    },
  },
  mounted() {
    
    this.initTrHeight()
  },
  destroyed() {
    window.removeEventListener("resize", this.initTrHeight)
  },
  created() {
         
          window.addEventListener("resize", this.initTrHeight)
          
     
    },
}

</script>
