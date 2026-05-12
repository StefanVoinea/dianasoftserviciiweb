<template>  
             <div class="flex flex-col">
              <vue-simple-suggest
                 class="dropdownsearch"
                 @blur="limitToSelectList(procenteTVAList,'procenttva')"
                 :list="procenteTVAList"
                 :filter-by-query="true"
                 display-value="procenttva"
                 display-attribute="procenttva"
                 :readonly="readonly"
                 @suggestion-click="handleInputSelectat"
                 @select="handleInputSelectat"
                
                >
              <vs-input   :readonly="readonly"  @blur="limitToSelectList(procenteTVAList,'procenttva')" @input.native="handleInput"  @keyup.native="removeErrors" :value.sync="content" v-model=content
                 autocomplete="off" name="procenttva" class="w-full" :label-placeholder="this.label" />

                 <div class="text-xs" slot="suggestion-item" slot-scope="{ suggestion, query }">
                      <div>{{ suggestion.procenttva }}</div>
               </div>
              </vue-simple-suggest>
              <span class="text-danger text-sm" v-show="errors.has('procenttva')">{{ errors.first('procenttva') }}</span>
         </div>
  
</template>

<script>
import VueSimpleSuggest from 'vue-simple-suggest';
import 'vue-simple-suggest/dist/styles.css'
export default {
 props: {
        value:[Number,String],
        label:String,
        readonly:Boolean
        },

 components: {
   
    VueSimpleSuggest
   
  },
  name:"ProcentTVAComponent",
 
  data() {

    return {
      content:this.value,
      procenteTVAList:[],
      

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
    },
    handleInputSelectat (e) {
       this.content=e.procenttva
       this.removeErrors()
       this.$emit('input', this.content)
    },
    removeErrors(){
        
        this.errors.items.forEach( each => {
        if (each.el.field == event.target.name)
        {
          each.el.msg=""
        }
      })
    },
     limitToSelectList(arrayList,arrayCol){
      
        let exista= arrayList.filter((val)=>{
                                               return val[arrayCol]==event.target.value
                                    }).length
       
        if(exista==0)
        {
          if(event.target.value!="")
          {
           event.target.value=""
           this.content=""
          this.errors.add({
                field:event.target.name,
                msg: event.target.value+" nu este în lista de opțiuni!"
              })
         
          }
        }
       },
      
     getprocenteTVAList(){
         // this.showLoading=true
         this.procenteTVAList=JSON.parse(this.$store.state.procenttva)
         //  const payLoad={} 
         //  payLoad.requestType="get"
         //  payLoad.requestUrl="/procenteTVAList"
         // this.$store.dispatch("app/api_Request",payLoad)
         //  .then(response=>{
                     
         //             this.procenteTVAList=response
         //               this.showLoading=false
         //  })
         //  .catch(error => {
         //     this.handleErrors(error)
         //     this.showLoading=false
         //  })
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
        
        this.getprocenteTVAList()
      
    }
  
}
  
</script>