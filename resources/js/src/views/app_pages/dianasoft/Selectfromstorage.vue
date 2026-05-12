<template>  
   <div class="flex flex-col">
          <v-select :label="colCaut" 
                    :options="recordsList" 
                    :value.sync="content" 
                    v-model="content"
                    
                    :name="camp" 
                    :disabled="readonly"
                    @input="handleInputSelectat"
                    @search:focus="handleFocus"
                   
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
        readonly:Boolean,
        
        },
 components: {
   'v-select': vSelect,
   
  },
  name:"selectfromstorage",
 
  data() {

    return {
      content:this.value,
      allRecords:[],
      recordsList:[]
    }
  },
  watch:{
      value(){
        this.content = this.value
      }
    },
    created(){
         
          if (this.$store.state.app[this.ruta]==null){
            return []
          }
           let recs=JSON.parse(this.$store.state.app[this.ruta])
          
        
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

          }
          this.recordsList=recListTmp
          

        
      
    },
  methods:{
      handleFocus () {
      this.$emit('focus', this.content)
    },
    handleInput (e) {
      this.content=e.target.value
      this.$emit('input', this.content)
      this.$emit('change', this.allRecords.find((t)=>{ return t[this.colCaut]==this.content}))
    },
    handleInputSelectat (e) {
       this.content=e
       
       this.$emit('input', this.content)
       this.$emit('change', this.allRecords.find((t)=>{ return t[this.colCaut]==this.content}))
    }
   
   
  }  
}
  
</script>
<style lang="scss">
.labelSelect {
  font-size:.9rem;
  color:gray;
}

</style>