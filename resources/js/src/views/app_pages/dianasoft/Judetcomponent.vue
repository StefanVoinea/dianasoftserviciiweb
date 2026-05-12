<template>  

          <b-form-group >
              <v-select
              label="denumire"
               input-id="id"
                :options="judeteList"
                :value.sync="content" 
                v-model="content"
               class="select-size-sm"
                :name="'judet'" 
                :disabled="readonly"
                @input="handleInputSelectat"
              >

                <template #option="{ denumire }">
                  <span class="ml-50 d-inline-block align-middle"> {{ denumire }}</span>
                </template>

                <template #selected-option="{ denumire }">
                  <span class="ml-50 d-inline-block align-middle"> {{ denumire }}</span>
                </template>
              </v-select>
              <div class="d-flex justify-content-end">
             <label class="labelSelect" for="judet">Judet</label>
             </div>
              
             
            </b-form-group>


  
</template>

<script>
import vSelect from 'vue-select'
export default {
 props: {
        value:String,
        readonly:Boolean
        },

 components: {
   vSelect
   
  },
  name:"judetcomponent",
 
  data() {

    return {
      content:this.value,
      judeteList:[],
      

    }
  },
  watch:{
      value(){
        this.content = this.value
      }
    },
  methods:{
  
    doCopy() {
      // console.log(this.content)
      this.$copyText(this.content)
      },
    handleInputSelectat (e) {
       if(e==null){

       this.content=null
       }else{
        this.content=e.denumire
       }
            
       this.$emit('input', this.content)
       this.$emit('change', this.content)
       
    },
   
   
      
     getjudeteList(){
       this.judeteList=JSON.parse(localStorage.getItem("judet"))

          if(this.judeteList==null){
               this.showLoading=true
              
                 const payLoad={} 
                 payLoad.requestType="get"
                 payLoad.requestUrl="/judet"
                this.$store.dispatch("app/api_Request",payLoad)
                 .then(response=>{
                           
                            this.judeteList=response
                            localStorage.setItem("judet",JSON.stringify(response))
                             this.showLoading=false
                 })
         //  .catch(error => {
         //     this.handleErrors(error)
         //     this.showLoading=false
         //  })
        } 
      },
     

  },
    created(){
        
        this.getjudeteList()
      
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