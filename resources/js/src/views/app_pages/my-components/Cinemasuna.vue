dd<template>
   <div class= "flex flex-col justify-center" >
     
             
              <div class="flex flex-col" > 

             <b-row vs-type="flex " vs-justify="space-between" vs-align="top" >   <!-- R1 -->
                 
                          
                    <b-col vs-type="flex flex-row"  vs-justify="center" vs-align="top" vs-w="9">  
                       <span class="text-dark text-center text-2xl font-semibold">Cine mă suna?</span>
                     </b-col> 
                   <b-col vs-type="flex flex-col"  vs-justify="center" vs-align="flex-end" vs-w="2">  
                      <div class="flex " style="align-items:flex-end;">
                       <vs-input 
                                autocomplete="off" 
                                name="telefon" 
                                class="w-full"
                                label-placeholder="Telefon" 
                                v-model="editVarLocal.telefon"  />

                        <vs-button size="large" v-show="true" color="primary"  icon="search"
                        @click="cinemasuna"> </vs-button> 
                       </div> 
                     </b-col> 
                   
                  </b-row>
                  
          </div>
        
       
      <vs-tabs>      
          <!-- TAB CONTACTE CLIENTI POTENTIALI -->
      <vs-tab id="clientiPotentiali" label="Contacte clienti potentiali">        
        <div v-if="editVarLocal.dateclientipotentiali">
          <vs-table search sort stripe pagination max-items="10" :data="editVarLocal.dateclientipotentiali">
          <template slot="header" >
         
          </template>
          <template slot="thead">
            <vs-th sort-key="cui" >Data</vs-th>
            <vs-th sort-key="denumire" >Agentia</vs-th>
            <vs-th sort-key="nume" >Nume</vs-th>
            <vs-th sort-key="email" >E-mail</vs-th>
            <vs-th sort-key="localitate" >Localitate</vs-th>
            <!-- <vs-th sort-key="utilizator" >Utilizator</vs-th> -->

           </template>

            <template slot-scope="{data}">
        <vs-tr :key="indextr" v-for="(tr, indextr) in data">
          <vs-td :data="tr.data">
            {{ tr.data }}
          </vs-td>
          <vs-td :data="tr.gestiune">
            {{ tr.gestiune }}
          </vs-td>
         <vs-td :data="tr.Nume">
            {{ tr.nume }}
          </vs-td>
          <vs-td :data="tr.email">
            {{ tr.email }}
          </vs-td>
          <vs-td :data="tr.localitate">
            {{ tr.localitate}}
          </vs-td>
         <!--  <vs-td :data="tr.user">
            {{ tr.user?tr.user.name:null}}
          </vs-td> -->
        </vs-tr>
           </template>
          </vs-table>
        </div>
      </vs-tab>
            <!-- TAB DATE FIRME ANAF -->
      <vs-tab id="dateFirmeAnaf" label="Date firme ANAF">
        <div v-if="editVarLocal.datefirmeanaf">
          <vs-table search sort stripe pagination max-items="10" :data="editVarLocal.datefirmeanaf">
          <template slot="header" >
            <div >
             <!-- <h4>{{'Total comanda: '+totalComanda+' '+editVarLocal.tip_valuta}}</h4> -->
            </div>
          </template>3
          <template slot="thead">
            <vs-th sort-key="cui" >C.U.I.</vs-th>
            <vs-th sort-key="denumire" >Denumire</vs-th>
            <vs-th sort-key="regcom" >Nr.inreg.reg.com.</vs-th>
            <vs-th sort-key="adresa" >Adresa</vs-th>
           </template>

            <template slot-scope="{data}">
        <vs-tr :key="indextr" v-for="(tr, indextr) in data">
          <vs-td :data="tr.cui">
            {{ tr.cui }}
          </vs-td>
          <vs-td :data="tr.denumire">
            {{ tr.denumire }}
          </vs-td>
         <vs-td :data="tr.regcom">
            {{ tr.regcom }}
          </vs-td>
          <vs-td :data="tr.adresa">
            {{ tr.adresa }}
          </vs-td>
        </vs-tr>
           </template>
          </vs-table>
        </div>
      </vs-tab>
     
      </vs-tabs>
     
  </div>
</template>

<script>
import axios from "axios"
import DatePicker from 'vue2-datepicker'




export default {
 props: {
        rutainapoi:String,
        telefon:String,
        }, 
  components: {
    DatePicker,
    
    
    
  },
  name:"cinemasuna",
  data() {
    return {
        editVarContacte:{
                           nume:"",
                           telefon:"",
                        },
        activeEditContacte:false,
        activeActionContacte:"",
        ruta_inapoi:"",
        activeEditLocal:true,
        activeActionLocal:"Vizualizează",
        modelName:"contract",
        editVarLocal:{dateclientipotentiali:[],
                      datefirmeanaf:[],
                      nr_firme:"",
                      nr_clientipotentiali:"",
                      },
               
        
        settings: {
                  maxScrollbarLength: 60,
                  },
      }
  },
  
  computed: {
   
  },
  methods: {
    cinemasuna(){
        if(!this.editVarLocal.telefon){
          this.$vs.notify({
                title: "Completati un numar de telefon!",
                text: "Completati un numar de telefon!", 
                // error.data.error,
                iconPack: "feather",
                icon: "icon-alert-circle",
                color: "warning",
                time:1000,
            })
          return false
        }
        this.showLoading=true
        let payLoad={} 
        payLoad.requestType="post"
        payLoad.requestUrl="/cinemasuna"
        payLoad.telefon=this.editVarLocal.telefon
        this.$store.dispatch("app/api_Request",payLoad)
                              .then(response=>{
                                this.editVarLocal.datefirmeanaf=JSON.parse(response.datefirmeanaf)
                                this.editVarLocal.nr_firme=JSON.parse(response.datefirmeanaf).length
                                this.editVarLocal.dateclientipotentiali=JSON.parse(response.dateclientipotentiali)
                                this.editVarLocal.nr_clientipotentiali=JSON.parse(response.dateclientipotentiali).length
                                document.getElementById('clientiPotentiali').innerHTML = "Contacte clienti potential  <div class='con-vs-chip number vs-chip-success con-color' style='color: rgba(255, 255, 255, 0.9);'><span class='text-chip vs-chip--text'>"+this.editVarLocal.nr_clientipotentiali+"</span><!----></div>"
                                document.getElementById('dateFirmeAnaf').innerHTML = "Date firme ANAF  <div class='con-vs-chip number vs-chip-success con-color' style='color: rgba(255, 255, 255, 0.9);'><span class='text-chip vs-chip--text'>"+this.editVarLocal.nr_firme+"</span><!----></div>"
                                // console.log(this.editVarLocal)
                                 this.showLoading=false
                                     
                           })
                         
                           // .catch(error => {
                           //    this.showLoading=false
                           //    this.handleErrors(error)
                           //  })    
    },
    aevContacteClosed(){
      this.activeEditContacte=false
      this.editVarContacte={ }
      this.activeActionContacte=""
    },
    afisezSalvatContacte(value){
      
    },
    adaugaContacte(){
        this.activeActionContacte="Adaugă"
        this.editVarContacte={  nume:this.editVarLocal.nume,
                                telefon:this.editVarLocal.telefon,
                                                             
                              }
        this.activeEditContacte=true
    },
    
   

    containsKey(obj, key ) {
            return Object.keys(obj).includes(key);
    },
   
    handleErrors(error){

           if(error.status==401)
            {
              this.showLoading=false 
              this.$vs.notify({
                title: "Acces neautorizat!",
                text: "Accesarea neautorizată a unui sistem informatic reprezintă o infracțiune! <br/> Sistemul monitorizează toate încercarile de accesare neautorizată!", 
                // error.data.error,
                iconPack: "feather",
                icon: "icon-alert-circle",
                color: "danger",
                time:8000,
            })
              this.$router.push({name:'pageLogout'})
              
            }else
            {
            this.showLoading=false  
            this.$vs.notify({
                title: "Eroare la conectarea la server!",
                text: error.data.error,
                iconPack: "feather",
                icon: "icon-alert-circle",
                color: "danger"
            })
        }
    },  
  
   
    closeEditSideBar(){
        editVarLocal:{}
        
        this.$router.push({name:this.ruta_inapoi})
    },
   
  },
  created() {
            document.title=window.app_name+"->Cine ma suna"
            
             if (this.telefon){
              if(this.rutainapoi){
                this.ruta_inapoi=this.rutainapoi
              
              }
              
                this.showLoading=true
                const payLoad={}
                payLoad.requestType="post"
                payLoad.telefon=this.telefon
                payLoad.requestUrl="/cinemasuna"
              
               this.$store.dispatch("app/api_Request",payLoad)
                          .then(response=>{
                                
                                  this.editVarLocal=response
                                  
                                  this.showLoading=false
                             })
                            .catch(error => {
                              
                              this.showLoading=false
                              this.handleErrors(error)
                            })

           }  
               
  },
 
}

</script>

<style lang="scss">
.datelabel{
  font-size:.75rem;
  color:gray;
}
.con-vs-popup .vs-popup {
    width:95%;
  
  }
</style>