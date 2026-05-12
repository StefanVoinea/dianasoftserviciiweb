<template>  
   <div class="flex flex-col">
         
          <v-select :label="colCaut" 
                    :options="recordsList" 
                    :value.sync="content" 
                    v-model="content"
                    :name="camp" 
                    :disabled="readonly"
                    @input="handleInputSelectat"
                   
                    >
            <div slot="no-options">Nu există înregistrări...</div>
           </v-select>
           <div class="d-flex justify-content-end">
          <span class="labelSelect" >{{campDisplay}}</span> 
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
        campDisplay:String,
        ruta:String,
        limitToList:String,
        tipcamp:String,
        readonly:Boolean
        },
 components: {
   'v-select': vSelect,
   
  },
  name:"selectoneserver",
 
  data() {

    return {
      content:this.value,
      recordsList:[],
      allRecords:[]
    }
  },
  watch:{
      value(){
        this.content = this.value
      }
    },
    
  methods:{
    handleInput (e) {
      this.content=e.target.value
      
      this.$emit('input', this.content)
      this.$emit('change', this.allRecords.find((t)=>{ return t[this.colCaut]==this.content}))
    },
    handleInputSelectat (e) {
       this.content=e
       this.$emit('input', this.content)
       this.$emit('change', this.allRecords.find((t)=>{ return t[this.colCaut]==this.content}))
    },
    getRecordList(){
         this.showLoading=true
         this.recordsList=[]
          this.allRecords=JSON.parse(localStorage.getItem(this.ruta))

          if(this.allRecords!=null){
          this.allRecords.forEach(record=>{
                                         this.recordsList.push(record)
                                      })
        }
          if(this.allRecords==null){
          const payLoad={} 
          payLoad.requestType="get"
          payLoad.requestUrl="/"+this.ruta
          
         this.$store.dispatch("app/api_Request",payLoad)
          .then(response=>{
                     this.allRecords=response
                       response.forEach(record=>{
                                         this.recordsList.push(record)
                                      })
                      localStorage.setItem(this.ruta,JSON.stringify(response))
                         
                       this.showLoading=false
                       
          })
          .catch(error => {
             this.handleErrors(error)
             this.showLoading=false
             return []
          })
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
      },

  },
  created(){
     this.getRecordList()
  }
   
}
  
</script>
<style lang="scss">
.labelSelect {
  font-size:.9rem;
  color:gray;
}

</style>