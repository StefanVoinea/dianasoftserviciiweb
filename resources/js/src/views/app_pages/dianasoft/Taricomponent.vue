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
                :options="tariList"
                :value.sync="content" 
                v-model="content"
                class="select-size-sm"
                :name="'tara'" 
                size="sm"
                :disabled="readonly"
                @input="handleInputSelectat"
              >

                <template #option="{ denumire }">
                  <span class="ml-50 d-inline-block align-middle"> {{ denumire }}</span>
                </template>

                <template #selected-option="{ denumire }">
                <div class="selected d-center">
                  <span class="ml-50 d-inline-block align-middle"> {{ denumire }}</span>
                 </div> 
                </template>
              </v-select>
              <div class="d-flex justify-content-end">
             <label class="labelSelect" for="tara">{{campDisplay}}</label>
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
        campDisplay:String
        },

 components: {
   vSelect
   
  },
  name:"taricomponent",
 
  data() {

    return {
      content:this.value,
      tariList:[],
      showLoading:false,
      

    }
  },
  watch:{
      value(){
        this.content = this.value
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
   
   
      
     gettariList(){
            this.tariList=JSON.parse(localStorage.getItem("tari"))

          if(this.tariList==null){
             this.showLoading=true

           const payLoad={} 
           payLoad.requestType="get"
           payLoad.requestUrl="/tari"
          this.$store.dispatch("app/api_Request",payLoad)
           .then(response=>{
                     
                      this.tariList=response
                      localStorage.setItem("tari",JSON.stringify(response))
                        this.showLoading=false
           })
         }
         //  .catch(error => {
         //     this.handleErrors(error)
         //     this.showLoading=false
         //  })
      },
     

  },
    created(){
        
        this.gettariList()
      
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