<template>  
  <div >
        
              <v-select
                
                :label="'name'"
                input-id="id"
                :options="allRecords"
                :value.sync="content" 
                v-model="content"
                multiple
                :name="'user'" 
                :disabled="readonly"
                :close-on-select="false"
                @input="handleInputSelectat"
              >

                <template #option="{ link_poza, name }">
                  <b-avatar
                    size="26"
                    :src="link_poza"
                  />
                  <span class="ml-50 d-inline-block align-middle"> {{ name }}</span>
                </template>

                <template #selected-option="{ link_poza, name }">
                  <b-avatar
                    size="26"
                    :src="link_poza"
                    :text="name"
                  />

                  <span class="ml-50 d-inline-block align-middle"> {{ name }}</span>
                </template>
              </v-select>
              <div class="d-flex justify-content-end">
             <label class="labelSelect" :for="'user'">{{this.labelDisplay}}</label>
             </div>


              </div>
  
</template>

<script>

import vSelect from 'vue-select'

export default {

 props: {
         value:[String, Number,Object,Array],
         readonly:Boolean,
         labelDisplay:String
        
        },
 components: {
   'v-select': vSelect,
   
  },
  name:"selectmultipleuser",
 
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
   created(){
          this.getRecordList()
         
      
    },
  methods:{
   
    handleInputSelectat (e) {
       this.content=e
       this.$emit('input', e)
       this.$emit('change', e)
    },
    getRecordList(){
         // this.showLoading=true
          const payLoad={} 
          payLoad.requestType="get"
          payLoad.requestUrl="/utilizatoriOrdonati"
          this.$store.dispatch("app/api_Request",payLoad)
          .then(response=>{
                      
                       this.allRecords=response
                       
          })
          
      },
    

  },
   
    
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