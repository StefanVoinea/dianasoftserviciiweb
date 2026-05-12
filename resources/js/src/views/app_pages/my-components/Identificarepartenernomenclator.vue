<template>
 <div class="flex flex-col">
  
  
  <v-select 
  			   class="w-full"
           :label="colCaut" 
           :filterable="false" 
           :options="allRecords" 
           @search="onSearch"
           :value.sync="content" 
           v-model="content"
           :is-selected.sync="content"
           :name="camp" 
           :disabled="readonly"
            @input="handleInputSelectat"
            @search:focus="handleFocus"
            >

    <template slot="no-allRecords">
      căutare Cod fiscal sau Denumire partener..
    </template>
    <template slot="option" slot-scope="option">
      <div class="d-left flex flex-col" >
      	  	<b>
      	  	{{option[colCaut]}} 	
        	  </b>
      	  	  
           {{ "Cod fiscal: "+option.cui + " Reg.Com: "+option.regcom }}
           	<br>
	    	  	<div class="w-full">
			{{ "Adresa: "+option.adresa }}
           </div>
           
		
        </div>
    </template>
    <template slot="selected-option" slot-scope="option">
      <div class="selected d-center">
        <!-- <img :src='option.owner.avatar_url'/>  -->
        {{ option[colCaut] }}
      </div>
    </template>
    <div slot="no-options">Căutare după: Cod fiscal sau Denumire partener</div>

  </v-select>
  <div class="d-flex justify-content-end">
  
 <span class="labelSelect"> Selectare partener...</span>
  
  </div>
	</div>
</template>
<script>
import vSelect from 'vue-select';

export default {
 props: {
        value:[String,Object],
        colCaut:String,
        camp:String,
        campDisplay:String,
        ruta:String,
        limitToList:String,
        tipcamp:String,
        readonly:Boolean,
        pastrezvaloare:Boolean
        },
 components: {
   'v-select': vSelect,
   
  },
  name:"identificarepartenernomenclator",
 
  data() {

    return {
      content:this.value,
      allRecords:[]
    }
  },
  watch:{
      value(){
        this.content = this.value
      }
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
   onSearch(search, loading) {
     
      this.search(loading, search, this);
    },
    search(loading, search, vm) {
    	if (search.length>3)
    	{
        loading(true);
    	clearTimeout(this.timeout);
	       var self = this;
	       this.timeout = setTimeout( ()=> {
	                   
						          const payLoad={} 
						          payLoad.requestType="post"
						          payLoad.requestUrl="/"+this.ruta+"/searchFurnizor"
						          payLoad.colCaut=this.colCaut
						          payLoad.valCaut=search
						         this.$store.dispatch("app/api_Request",payLoad)
						          .then(response=>{
						                       this.allRecords=response

						                       loading(false);
						                      
						          })
						          .catch(error => {
						             this.handleErrors(error)
						             loading(false);
						          })
						        
	                
	        }, 1000);
      
   		}
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
  color:gray;
}
</style>