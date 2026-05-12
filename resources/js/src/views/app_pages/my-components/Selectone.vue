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
          <span class="labelSelect" >{{campDisplay}}</span>
          
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
        readonly:Boolean,
        
        },
 components: {
   'v-select': vSelect,
   
  },
  name:"selectone",
 
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
    computed:{
      recordsList:{
        get(){
          // // console.log(this.ruta)
          if (this.$store.state.app[this.ruta]==null){
            return []
          }
           let recs=JSON.parse(this.$store.state.app[this.ruta])
           
          //  // console.log(recs)
          this.allRecords=recs

          let recListTmp=[]
          if(recs!=null)
          {
          recs.forEach(record=>{
                        if(record[this.colCaut]!=null)
                        {
                         recListTmp.push(record[this.colCaut])
                        }
                       })

          }else
          {
            recListTmp=this.getRecordList()
          }

          return recListTmp

        }
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
         // this.showLoading=true
          const payLoad={} 
          payLoad.requestType="get"
          payLoad.requestUrl="/"+this.ruta
          
         this.$store.dispatch("app/api_Request",payLoad)
          .then(response=>{
                       this.allRecords=JSON.parse(response)
                       let recTMP=[]
                       response.forEach(record=>{
                                         if(record[this.colCaut]!=null)
                                        { 
                                         recTMP.push(record[this.colCaut])
                                       }
                                      })
                        return recTMP 
                       // this.showLoading=false
                       
          })
          
      },
    

  },
   
    
}
  
</script>
<style lang="scss">
.labelSelect {
  font-size:.9rem;
  color:gray;
}

</style>