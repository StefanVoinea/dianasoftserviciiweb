<template>  
<b-overlay
      :show="showLoading"
      rounded="sm"
      no-fade
      variant="primary"
      opacity="0.25"
      blur="2px"
    >
          <b-form-group >
              <v-select
              label="denumire"
               input-id="id"
                :options="localitatiList"
                :value.sync="content" 
                v-model="content"
                :name="'localitate'" 
                class="select-size-sm"
                :disabled="readonly"
                @input="handleInputSelectat"
                :filterable="true" 
                @search="onSearch"
              >

                <template #option="{ denumire }">
                  <span class="ml-50 d-inline-block align-middle"> {{ denumire }}</span>
                </template>

                <template #selected-option="{ denumire }">
                  <span class="ml-50 d-inline-block align-middle"> {{ denumire }}</span>
                </template>
              </v-select>
              <div class="d-flex justify-content-end">
             <label class="labelSelect" for="localitate">Localitate</label>
             </div>
            </b-form-group>

  </b-overlay>
  
</template>

<script>
import vSelect from 'vue-select'
export default {
 props: {
        value:String,
        readonly:Boolean,
        judet:String
        },

 components: {
   vSelect
   
  },
  name:"localitatecomponent",
 
  data() {

    return {
      content:this.value,
      localitatiList:[],
      filtru:"",
      showLoading:false
      

    }
  },
  watch:{
      value(){
        
        this.content = this.value
      },
      judet(){
        this.filtru=""
        this.getlocalitatiList()
      }
    },
  methods:{
    handleInputSelectat (e) {
       if(e==null){

       this.content=null
       }else{
        this.content=e.denumire
       }
       
       this.$emit('input', this.content)
       this.$emit('change', this.content)
       
    },
    onSearch(search, loading) {
     if(this.judet==""||this.judet==null){
      this.search(loading, search, this);
    }
    },
    search(loading, search, vm) {
      
      if (search.length>3)
      {
        // if(this.gestiune==null || this.gestiune==""){
        //   this.$vs.notify({
        //                     title: "",
        //                     text: "Selectați o gestiune!",
        //                     iconPack: "feather",
        //                     icon: "icon-check",
        //                     color: "warning"
        //                 })
        //   return false
        // }
         
         clearTimeout(this.timeout);
         var self = this;
         this.timeout = setTimeout( ()=> {
            this.filtru=search     
            this.content=""
            search=""
            this.getlocalitatiList()
            
          }, 1000);
      
      }
  },

    
   
   
      
     getlocalitatiList(){
      
           // if((this.judet!=""&&this.judet!=null)||(this.filtru!=""&&this.filtru!=null)){
           //this.showLoading=true
           const payLoad={} 
           payLoad.requestType="post"
           payLoad.judet=this.judet
           payLoad.filtru=this.filtru
           payLoad.requestUrl="/localitatiFiltrate"
          this.$store.dispatch("app/api_Request",payLoad)
           .then(response=>{
                     
                         
                        this.showLoading=false
                       this.localitatiList=response
                     //  response.map((t)=>{this.localitatiList.push({"denumire":t.denumire})})
                     //   // console.log(this.localitatiList)
                      
           })
     //    }
         //  .catch(error => {
         //     this.handleErrors(error)
         //     this.showLoading=false
         //  })
      },
     

  },
    created(){
        
        this.getlocalitatiList()
      
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