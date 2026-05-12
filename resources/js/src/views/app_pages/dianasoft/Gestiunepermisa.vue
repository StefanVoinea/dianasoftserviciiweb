<template>
 <div class="flex flex-col">
  
  <v-select 
           
           label="denumire" 
           :options="allRecords" 
           :value.sync="content" 
           v-model="content"
           class="select-size-sm w-full"
           :is-selected.sync="content"
           :disabled="readonly"
           @input="handleInputSelectat"
           @search:focus="handleFocus">

   
    <template slot="option" slot-scope="option">
      <div class="d-left flex flex-col" >
          
            {{option.denumire}}   
          
        </div>
    </template>
    <template slot="selected-option" slot-scope="option">
      <div class="selected d-center">
      
        {{ option.denumire }}
      </div>
    </template>
    <div slot="no-options">nu exista gestiuni</div>

  </v-select>
  <div class="d-flex justify-content-end">
  <span class="labelSelect"> {{this.eticheta}}</span>
  </div>

  </div>
</template>
<script>
import vSelect from 'vue-select';

export default {
 props: {
        value:[String,Object],
        readonly:Boolean,
        pastrezvaloare:Boolean,
        label:String
        },
 components: {
   'v-select': vSelect,
   
  },
  name:"gestiunepermisa",
 
  data() {

    return {
      content:this.value,
      allRecords:[],
      eticheta:this.label?this.label:"Gestiune"
    }
  },
  watch:{
      label(){
          this.eticheta=this.label?this.label:"Gestiune"   
      },
      value(){
        this.content = this.value
      }
    },
  created(){
    this.getRecordsList()
  }, 
  methods:{
    
    handleFocus () {
      this.$emit('focus', this.content)
    },
    handleInputSelectat (e) {
       this.content=e
       this.$emit('input', this.content)
       if(this.content){
        this.$emit('change', this.allRecords.find((t)=>{ return t["id"]==this.content.id}))
       }else
       {
        this.$emit('change', this.content)
       }
       if(!this.pastrezvaloare){
         this.content=null
      }
    },
  
   getRecordsList(){
           this.allRecords=JSON.parse(this.$store.state.app.gestiuniPermise)
      
  },
 

     handleErrors(error)
     {
        
            if(error.status==401)
            {
              this.showLoading=false 
              this.$vs.notify({
                title: "Acces neautorizat!",
                text: "Accesarea neautorizată a unui sistem informatic reprezintă o infracțiune! <br/> Sistemul monitorizează toate încercarile de accesare neautorizată!", 
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
     }
    
    } 

  }
</script>
<style >
  

.d-center {
  display: flex;
  align-items: center;
}

.selected img {
  width: auto;
  max-height: 23px;
  margin-right: 0.5rem;
}

.v-select .dropdown li {
  border-bottom: 1px solid rgba(112, 128, 144, 0.1);
}

.v-select .dropdown li:last-child {
  border-bottom: none;
}

.v-select .dropdown li a {
  padding: 10px 20px;
  width: 100%;
  font-size: 1.25em;
  color: #3c3c3c;
}

.v-select .dropdown-menu .active > a {
  color: #fff;
}
.labelSelect {
  font-size:.9rem;
  color:rgba(115, 103, 240) !important;
}
</style>