s<template>  
  <div >
      
          <v-select :label="colCaut" 
                    :options="allRecords" 
                    :value.sync="content" 
                    v-model="content"
                    :name="camp" 
                    :disabled="readonly"
                    @input="handleInputSelectat"
                   
                    >
            <div slot="no-options">Nu există înregistrări...</div>
           </v-select>
    
              <div class="d-flex justify-content-end">
             <label class="labelSelect" :for="camp">{{campDisplay}}</label>
             </div>

              </div>
  
</template>

<script>
import vSelect from 'vue-select';

export default {
 props: {
        value:[String, Number,Object],
        colCaut:String,
        camp:String,
        ruta:String,
        campDisplay:String,
        limitToList:String,
        readonly:Boolean,
        
        },
 components: {
   'v-select': vSelect,
   
  },
  name:"dropdowncuruta",
 
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
   
    handleInputSelectat (e) {
       
       this.content=e
       this.$emit('input', this.content)
       this.$emit('change', this.content)
    },
     getRecordList(){
      this.allRecords=JSON.parse(localStorage.getItem(this.ruta))

          if(this.allRecords==null){
         // this.showLoading=true
          const payLoad={} 
          payLoad.requestType="get"
          payLoad.requestUrl="/"+this.ruta
          this.$store.dispatch("app/api_Request",payLoad)
          .then(response=>{
                      
                       this.allRecords=response
                        localStorage.setItem(this.ruta,JSON.stringify(response))
                       
          })
          }
      },
    
  },
   created(){
        this.getRecordList()

        
          
      
    

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


.v-select .dropdown-menu .active > a {
  color: #fff;
}
.labelSelect {
  font-size:.9rem;
  color:rgba(115, 103, 240) !important;
}
</style>
