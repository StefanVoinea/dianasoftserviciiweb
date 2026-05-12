<template>  
   <div class="flex flex-col">
          <v-select :label="camp" 
                    :options="recordsList" 
                  
                    v-model="content"
                    :name="camp" 
                    :disabled="readonly"
                    @input="handleInputSelectat"
                   
                    >
            <div slot="no-options">Nu există înregistrări...</div>
           <template #option="option">
             <span
                v-for="k in infoAfisate"
                :key="k"
                class="ml-50 d-inline-block align-middle"
              >
                {{ option[k] }}
              </span>
            </template>
          
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
        camp:String,
        campDisplay:String,
        infoAfisate:Array,
        ruta:String,
        limitToList:String,
        tipcamp:String,
        readonly:Boolean,
        
        },
 components: {
   'v-select': vSelect,
   
  },
  name:"selectfromstorageobject",
 
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

          this.recordsList=this.allRecords
          

        
      
    },
  methods:{
    handleInput (e) {
      this.content=e
       this.$emit('input', this.content[this.camp])
       this.$emit('change', e)
    },
    handleInputSelectat (e) {
       this.content=e
       this.$emit('input', this.content[this.camp])
       this.$emit('change', e)
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