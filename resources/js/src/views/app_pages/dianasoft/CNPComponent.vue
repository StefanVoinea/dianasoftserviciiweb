<template>  
   <div >
   
             <b-row clas="d-flex justify-content-center">
              <b-col  cols="1"  @click="selectFile" >
                    
                    <b-button 
                                          variant="primary"
                                          class="btn-icon "
                                          size="sm"
                                           v-b-tooltip.hover.v-dark
                                          title="OCR"
                                          v-show="!readonly"
                                           > 
                                         <feather-icon icon="UploadCloudIcon" /> 
                                         
                                           
                                
                     
                                </b-button>
                                 <input @change="onFileChange" type="file" :id="'file'+name" :ref="'file'+name" accept="image/gif,image/jpeg, image/png" hidden/>
                    
                     </b-col>
             <b-col cols="10">
               <div class="form-label-group">
                      <b-form-input
                                  
                                  :state="cuiSucces"
                                  type="number" 
                                  :readonly="readonly" 
                                  autocomplete="off" 
                                  :name="name"   
                                  :id="id"
                                  size="sm"
                                  :placeholder="campDisplay" 
                                  :value.sync="content" 
                                  v-model="content"
                                  @input="handleInput"
                                  @change="handleChange" />
                    <label for="cui">{{campDisplay}}</label>
                                 
                    
                    <b-form-invalid-feedback>
                    {{cuiMsgWarning}}
                  </b-form-invalid-feedback>
                  <b-form-valid-feedback>
                    {{cuiMsgSucces}}
                  </b-form-valid-feedback>
                  </div>
                    </b-col>
                    
                     </b-row>
                   
              
                  
               
        <OcrCI 
              @scannedCI="preiaDateCIScanat"
              @closed="activateOcrCIClosed"
              :emitator="name"
              :id="name"
              :name="name"
              :activateOcrCI="activateOcrCI"
              :editVar="editVar"
              v-show="activateOcrCI"
           >
        </OcrCI>          
       
    </div>
  
</template>

<script>
import axios from "axios"


export default {
 props: {
        value:String,
        name:String,
        id:String,
        campDisplay:String,
        readonly:Boolean,
        activeEdit:Boolean
        },
 
  name:"cnp-component",
  component:{
    
  },
  data() {

    return {
      content:this.value,
      editVar:{
               cnp:"",
               ci_seria:"",
               ci_numar:"",
               nume:"",
               prenume:"",
               denumire:"",
               loc_nastere:"",
               adresa:"",
               emisde:"",
               dataemitere:"",
               valabilitate:"",
               filename:"",
               sex:"",
               judet:"",
               localitate:"",
               data_nasterii:""
               },
      
        activateOcrCI:false,
        cuiSucces:false,
        cuiWarning:false,
        cuiDanger:false,
        cuiMsgSucces:"Verificare ANAF cu succes!",
        cuiMsgWarning:"Nu a putut fi verificat la ANAF!",
        cuiMsgDanger:"La ANAF nu figurează!",
        uploadCIFileActive:false,
    
      
        activateOcrCI:false,
        cuiSucces:false,
        cuiWarning:false,
        cuiDanger:false,
        cuiMsgSucces:"Verificare ANAF cu succes!",
        cuiMsgWarning:"Nu a putut fi verificat la ANAF!",
        cuiMsgDanger:"La ANAF nu figurează!",
        uploadCIFileActive:false,
      file:[]
      
    }
  },
  watch:{
   activeEdit(){

      this.setCUImessage("reset")
    },
  

    value(){

        this.content = this.value
      }
    },
  methods:{
    activateOcrCIClosed(){
          this.activateOcrCI=false
      },
    selectFile(){
        document.getElementById('file'+this.name).click()
      },
    preiaDateCIScanat(e){
      this.content=e.cnp
      this.editVar=e
      this.$emit('ciScanat',e)
      this.$emit('change',e)
    },
    onFileChange(e){
     
                this.file = e.target.files[0];
                
                if(this.file){
                   
                   this.importCI(e)
                }
    },
   importCI(e){
                this.showLoading=true
                e.preventDefault()
               
                let currentObj = this
                
                
                
                const config = {
                                headers: { 'content-type': 'multipart/form-data',
                                           'Authorization': 'Bearer ' + this.$store.state.app.token,
                                           'AuthorizationHeader': JSON.parse(this.$store.state.app.societateaCurenta).id
                                           }
                                }
                let formData = new FormData();
                formData.append('file', this.file);
                
                axios.post('/api/ocrCI', formData, config)
                          .then(response => {
                             
                             this.file=[]
                             let client=response.data
                             this.content=client.cnp
                             this.editVar.cnp=client.cnp
                             this.editVar.nume= client.nume
                             this.editVar.prenume=client.prenume
                             this.editVar.ci_seria=client.seria
                             this.editVar.ci_numar=client.numar
                             this.editVar.adresa=client.adresa
                             
                          //   this.editVar.data_nasterii=client.data_nasterii.toLocaleDateString()
                             this.editVar.loc_nastere=client.loc_nastere
                             this.editVar.emisde=client.emisde
                             this.editVar.dataemitere=client.dataemitere
                             this.editVar.valabilitate=client.valabilitate
                             this.editVar.data_nasterii=client.data_nasterii
                             /*try{
                             let ziuaemitere=client.dataemitere.split(".")[0]
                             let lunaemitere=client.dataemitere.split(".")[1]
                             let anulemitere=client.dataemitere.split(".")[2]
                             if(anulemitere.length==2){
                               anulemitere="20"+anulemitere
                             }
                             this.editVar.dataemitere=(new Date(anulemitere,lunaemitere-1,ziuaemitere)).toLocaleDateString()
                             }catch(error){}
                             try{
                             let ziuaexpirare=client.valabilitate.split(".")[0]
                             let lunaexpirare=client.valabilitate.split(".")[1]
                             let anulexpirare=client.valabilitate.split(".")[2]
                             if(anulexpirare.length==2){
                               anulexpirare="20"+anulexpirare
                             }

                             this.editVar.valabilitate=(new Date(anulexpirare,lunaexpirare-1,ziuaexpirare)).toLocaleDateString()
                             
                             }catch(error){}
                             */
                             this.editVar.filename=client.filename
                             this.editVar.sex=(client.sex=='M'?'Masculin':(client.sex=='F'?'Feminin':''))
                             this.editVar.Valid=client.Valid

                             this.showLoading=false
                             this.editVar.emitator=this.name
                             this.$emit('CIUpload', this.editVar)
                             this.$emit('change', this.editVar)
                             this.verificaCUI()
                             
                             this.activateOcrCI=true
                              })
                      /*     .catch(error => {
                              this.showLoading=false
                              this.handleErrors(error)
                           }) */
    },
    handleInput (e) {
      this.content=e
      this.editVar.emitator=this.name
      this.$emit('input', this.content)
      //this.$emit('change', this.content)
    //  this.$emit('verificaCIFalse', this.editVar)
      
      
    },
    handleChange (e) {
     
      this.content=e
      this.editVar.emitator=this.name
      this.verificaCUI()

      this.$emit('change', this.editVar)
      this.$emit('verificaCIFalse', this.editVar)
      
    },
    verificaCUI(){
      
      if(this.content){
              if(this.activeAction=="Vizualizează")
              {
                return
              }
              if(this.content.length==0)
              {
                    this.setCUImessage("reset")
                    return
              }
                if(this.content.length==13)
                {
                  if (this.validCNP(this.content))
                  {
                     this.setCUImessage("success","CNP valid!")
                  }else
                  {
                      this.setCUImessage("warning","CNP invalid!")
                  }
                  return
                }
                if (!this.validCUI(this.content))
                {
                  
                   this.$bvToast.toast("ATENȚIE! Cod fiscal România eronat!", 
                                                 {
                                                    title: `ATENȚIE! Cod fiscal România eronat! `,
                                                    variant:"warning",
                                                    solid: false,
                                                    appendToast: true,
                                                    autoHideDelay: 3000,
                                                    toaster: "b-toaster-bottom-right",
                                                                      }) 
                   
                    this.$emit('CUIIntrodus', this.editVar)

                }
                this.showLoading=true
                const payLoad={} 
                payLoad.requestType="post"
                //payLoad.requestUrl="/partener/verificaCUI"
                payLoad.requestUrl="/partener/verificaCUIFaraDateFinanciare"
                
                payLoad.cui=this.content
               
                this.$store.dispatch("app/api_Request",payLoad)
                .then(response=>{
                            
                            this.showLoading=false
                            // if(response.exista && this.activeAction=="Adaugă"){
                            //   this.$vs.notify({
                            //       title: "",
                            //       text: "ATENȚIE! Partenerul cu codul fiscal "+this.content+ " există în baza de date cu denumirea "+response.denumire+" !",
                            //       iconPack: "feather",
                            //       icon: "warning",
                            //       color: "warning"
                            //   })
                            //  // this.content=""
                            //   this.setCUImessage("warning","Cod fiscal existent!")

                            //       return
                            // }
                            //console.log(response)
                            //if(response.cod=="200"||response.cod==200){
                            if(response.found){
                            if(response.denumire)
                            {
                             this.editVar.denumire=response.denumire
                            }else
                            {
                              this.editVar.denumire=response.found[0].date_generale.denumire
                            }
                            this.editVar.nume=""
                            this.editVar.adresa=response.found[0].date_generale.adresa
                            this.editVar.localitate=response.found[0].date_generale.localitate
                            this.editVar.judet=response.found[0].date_generale.judet
                            //if(response.exista){
                              
                            //  this.editVar.adresa=response.firmaLocal.adresa
                            //  this.editVar.denumire=response.firmaLocal.denumire
                            //  this.editVar.localitate=response.firmaLocal.localitate
                            //  this.editVar.judet=response.firmaLocal.judet
                           // }
                            
                            this.editVar.regcom=response.found[0].date_generale.nrRegCom
                            this.editVar.adresa=response.found[0].date_generale.adresa
                            this.editVar.email=response.email
                             this.editVar.localitate=response.found[0].date_generale.localitate
                            this.editVar.judet=response.found[0].date_generale.judet
                            this.editVar.telefon=response.found[0].date_generale.telefon
                            this.editVar.platitor_tva=response.found[0].inregistrare_scop_Tva.scpTVA
                            this.editVar.tva_split=response.found[0].inregistrare_SplitTVA.statusSplitTVA
                            this.editVar.tva_incasare=response.found[0].inregistrare_RTVAI.statusTvaIncasare
                            this.editVar.inactiv_fiscal=response.found[0].stare_inactiv.statusInactivi
                            this.editVar.cont_bancar=response.found[0].date_generale.iban
                            this.editVar.banca=response.bancaANAF
                            this.editVar.emitator=this.name
                            
                            this.$emit('CUIIntrodus', this.editVar)
                            if(this.editVar.inactiv_fiscal){
                              this.setCUImessage("danger","INACTIV FISCAL!!!")
                              
                              
                            
                               this.$bvToast.toast("ATENȚIE! Partener inactiv fiscal!", 
                                                 {
                                                    title: `ATENȚIE! Partener inactiv fiscal!`,
                                                    variant:"danger",
                                                    solid: false,
                                                    appendToast: true,
                                                    autoHideDelay: 3000,
                                                    toaster: "b-toaster-bottom-right",
                                                                      }) 
                            }else
                            {
                               this.setCUImessage("success","Verificare ANAF cu success!")
                              
                            }
                            if(!this.editVar.denumire){
                              this.setCUImessage("warning","Partenerul nu figureaza la ANAF!!!")
                             
                            
                                this.$bvToast.toast("ATENTIE! Partenerul nu figureaza la ANAF!", 
                                                 {
                                                    title: `ATENTIE! Partenerul nu figureaza la ANAF!`,
                                                    variant:"warning",
                                                    solid: false,
                                                    appendToast: true,
                                                    autoHideDelay: 3000,
                                                    toaster: "b-toaster-bottom-right",
                                                                      }) 

                            }else
                            {
                              this.setCUImessage("success","Verificare ANAF cu succes!")
                              
                            }
                                  }else
                                  {
                                     this.setCUImessage("warning","Verificare ANAF eșuata!")
                                  }
                })
               // .catch(error => {
               //   this.showLoading=false
                 // this.handleErrors(error)
               // })

          }

        },
        limitToSelectList(model,arrayList,arrayCol){
            
              let exista= arrayList.filter((val)=>{
                                                     return val[arrayCol]==event.target.value
                                          }).length
             
              if(exista==0)
              {
                if(event.target.value!="")
                {
                  model[event.target.name]=""
                
                this.errors.add({
                      field:event.target.name,
                      msg: event.target.value+" nu este în lista de opțiuni!"
                    })
               
               }
              
             }
        },
        isNumeric(n) {
          return !isNaN(parseFloat(n)) && isFinite(n);
        },
        validCUI(s) {

          if (parseInt(s)!=s) // CIF is of form ROxxxxxxxxx
          {
              if (s.substring(0,2).toUpperCase()!="RO" || s.length > 12)
                  return false
              
              s = s.substring(2, s.length) //Extract only the numeric content
          }
          else // CIF is only numeric
          {
              if (s.length > 10)
                  return false
          }
          var ultima=s.length-1
          var cifraControl = s.charAt(ultima)
          var content = s.substring(0, ultima)
          while (content.length < 9)
          {
              content = '0' + content;
          }
          var suma = content.charAt(0) * 7 + content.charAt(1) * 5 + content.charAt(2)
                  * 3 + content.charAt(3) * 2 + content.charAt(4) * 1
                  + content.charAt(5) * 7 + content.charAt(6) * 5 + content.charAt(7)
                  * 3 + content.charAt(8) * 2;
          suma = suma * 10;
          var rest = suma % 11;
          if (rest == 10)
              rest = 0;

          if (rest == cifraControl) {
              return true;
          }
          else
              return false;
        },
        setCUImessage($type,$message){
              if($type=="reset"){ 
                this.cuiSucces=null
                this.cuiMsgSucces=""
                this.cuiMsgWarning=""
               
              }
              if($type=="success"){ 
                this.cuiSucces=true
                this.cuiMsgSucces=$message
               this.cuiMsgWarning=""
              }
              if($type=="warning"){ 
                this.cuiSucces=false
                this.cuiMsgWarning=$message
                 this.cuiMsgSucces=""
              }
              if($type=="danger"){ 
                this.cuiSucces=false
                this.cuiMsgWarning=$message
                 this.cuiMsgSucces=""
                
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
    handleErrors(error){
           if(error.status==401)
            {
              this.showLoading=false 
           
                this.$bvToast.toast("Acces neautorizat!", 
                                                 {
                                                    title: "Accesarea neautorizată a unui sistem informatic reprezintă o infracțiune! <br/> Sistemul monitorizează toate încercarile de accesare neautorizată!", 
                                                    variant:"danger",
                                                    solid: false,
                                                    appendToast: true,
                                                    autoHideDelay: 3000,
                                                    toaster: "b-toaster-bottom-right",
                                                                      }) 
              this.$router.push({name:'pageLogout'})
              
            }else
            {
            this.showLoading=false  
         
            this.$bvToast.toast("Eroare la conectarea la server!", 
                                                 {
                                                    title: "Eroare la conectarea la server!", 
                                                    variant:"danger",
                                                    solid: false,
                                                    appendToast: true,
                                                    autoHideDelay: 3000,
                                                    toaster: "b-toaster-bottom-right",
                                                                      }) 
        }
    }, 

  },
  mounted(){

     this.setCUImessage("reset")
  }
   
}
  
</script>
