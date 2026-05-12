<template>  
   <div class="flex flex-col">
          <v-select :label="colCaut" 
                    :options="recordsList" 
                    
                    :value.sync="content" 
                    v-model="content"
                    :name="camp" 
                    :disabled="readonly"
                    @input="handleInputSelectat"
                    v-show="afisezLocal"
                    >
            <div slot="no-options">Nu există înregistrări...</div>
           </v-select>
          <span v-show="afisezLocal" class="labelSelect" >{{campDisplay}}</span>
          
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
        optiuni:[Array,Object,String],
        limitToList:String,
        tipcamp:String,
        afisez:Boolean,
        readonly:Boolean
        },
 components: {
   'v-select': vSelect,
   
  },
  name:"selectonearray",
 
  data() {

    return {
      content:this.value,
      afisezLocal:true,
      optiuniLocal:[]

    }
  },
  computed:{
      recordsList:{
        get(){
          let recListTmp=[]
          
          if(this.optiuni.includes(";"))   {
            this.optiuniLocal=this.arrayFromLista(this.optiuni)
          }else{
            this.optiuniLocal=this.optiuni
          }
          this.optiuniLocal.forEach(record=>{
                          
                         recListTmp.push(record[this.colCaut])
                       })
          return recListTmp

        }
      },

    },
  watch:{
      value(){
        this.content = this.value
      },
      afisez(){
        this.afisezLocal=this.afisez
      }
    },
    
  methods:{
     arrayFromLista(lista){
      
      if(lista){

      let optiuni= lista.split(";");
      return optiuni.map(t=>{
      
             return  t={denumire:t}
      })}
      else{
        return {denumire:""}
      }
      },
    handleInput (e) {
      this.content=e.target.value
      this.$emit('input', this.content)
      this.$emit('change',  this.optiuniLocal.find((t)=>{ return t[this.colCaut]==this.content}))
    },
    handleInputSelectat (e) {
       this.content=e
       this.$emit('input', this.content)
       this.$emit('change',  this.optiuniLocal.find((t)=>{ return t[this.colCaut]==this.content}))
    },
    }
 
   
}
  
</script>
<style lang="scss">
.labelSelect {
  font-size:.9rem;
  color:gray;
}

</style>