<template>  
    <div>
                               <b-row>
                                <b-col cols="4">
                                <h4>{{title}} {{modificaRaspuns}}</h4>
                                </b-col>
                                <b-col cols="8">

                                      <b-form-group
                                       
                                        label-cols-sm="3"
                                        label-align-sm="right"
                                        label-size="sm"
                                        label-for="filterInput"
                                        class="mt-0"
                                      >
                                        <b-input-group size="sm">
                                          <b-form-input
                                            id="filterInput"
                                            v-model="filter"
                                            type="search"
                                            placeholder="Căutare..."
                                          />

                                        </b-input-group>
                                      </b-form-group>
                                    </b-col>
                               
                                </b-row>

                                <b-table 
                                         :filter="filter"
                                         :filter-included-fields="filterOn"
                                         small
                                         hover
                                         :sticky-header="scrollbar"
                                         responsive="sm" 
                                         :items="items" 
                                         :fields="fields">
                                        <template  #cell(pas_nr)="data" >
                                      <div  v-if="data.field.field_type=='label'" >
                                            {{data.value}}
                                        </div>   
                                     </template> 
                                     <template  #cell(intrebare)="data" >
                                      <div  v-if="data.field.field_type=='label'" >
                                            {{data.value}}
                                        </div>   
                                     </template> 
                                      <template  #cell(raspuns)="data" >
                                       <div  v-if="data.field.field_type=='checkbox'&&data.field.key.includes('.')"  class="d-flex justify-content-center">
                                          <b-form-checkbox
                                              v-model="data.item[data.field.key.split('.')[0]][data.field.key.split('.')[1]]"
                                              class="custom-control-primary"
                                              value="1"
                                              unchecked-value="0"
                                            />
                                          
                                        </div>    
                                         <div  v-if="data.field.field_type=='checkbox'&&!data.field.key.includes('.')"  class="d-flex justify-content-center">
                                          <b-form-checkbox
                                              v-model="data.item[data.field.key]"
                                              class="custom-control-primary"
                                              value="1"
                                              unchecked-value="0"
                                            />
                                          
                                        </div>    
                                         <div  v-if="data.field.field_type=='label'" >
                                            {{data.value}}
                                        </div>   
                                         <div  v-else="data.field.field_type=='label'" >
                                          
                                        <div  v-if="data.item.nomchestionar" >

                                          <div  v-if="data.item.nomchestionar.lista_raspunsuri" >
                                            
                                               <selectonearray 
                                                      name="raspuns"
                                                     
                                                      :readonly="data.field.readonly"    
                                                      v-model="data.item[data.field.key]" 
                                                      colCaut="denumire" 
                                                      camp="denumire" 
                                                      campDisplay=""  
                                                      :optiuni="data.item.nomchestionar.lista_raspunsuri"
                                                      limitToList="true"> 
                                                </selectonearray>  
                                              
                                          </div>
                                           <div  v-else="data.field.field_type=='input'" >
                                              
                                              <b-form-input
                                                  :readonly="data.field.readonly || (data.item.nomchestionar.formula_calcul!='' && data.item.nomchestionar.formula_calcul!=null)"
                                                 
                                                  autocomplete="off"
                                                  id="data.field.key"
                                                  v-model="data.item[data.field.key]"
                                                  />
                                             
                                         


                                        </div>      
                                         </div> 
                                         <div  v-else="data.field.field_type=='input'" >
                                           
                                              <b-form-input
                                                 :readonly="data.field.readonly || (data.item.nomchestionar.formula_calcul!='' && data.item.nomchestionar.formula_calcul!=null)"
                                                  
                                                  autocomplete="off"
                                                  id="data.field.key"
                                                  v-model="data.item[data.field.key]"
                                                  />
                                         


                                        </div>     
                                      </div>
                                        </template>
                                </b-table>
                           
   </div>                        
</template>

<script>



export default {
  components: {
   
   
  },

 props: {
        title:String,
        fields:Array,
        items:Array,
        scrollbar:Boolean
         },

  name:"dstablechestionar",
 
  data() {

    return {
      filter: null,
      filterOn:[],
      
    }
  },
  watch:{
     value(){
         this.content = this.value
       },
       
    },
    computed:{
          modificaRaspuns(){
           
                    //ESTE IMPORTANTA ORDINEA INTREBARILOR PENTRU CALCUL PENTRU A CALCULA IN ORDINE VARIABILELE NECESARE
                    let alterate=0
                    let venitfiscalizat=0
                    let graddeindatorare=0
                    let procdobanda=0
                    let procdiscount=0
                    let ratacredit=0
                    let ratacudiscount=0
                    let sumasolicitata=0
                    let tipvaluta=""
                    let tiprambursare=""
                    let nrluni=0
                    let curs=1

                    this.items.map(t=>{


                     if (t.nomchestionar.prescurtare=="Suma solicitata"){
                       sumasolicitata=Number(t.raspuns)
                     }  
                     if (t.nomchestionar.prescurtare=="Moneda"){
                       tipvaluta=t.raspuns
                      if(tipvaluta =='EUR'){
                        curs=Number(JSON.parse(localStorage.getItem('cursBNR')))
                        
                      }
                     }
                     if (t.nomchestionar.prescurtare=="Procent dobanda"){
                       procdobanda=Number(t.raspuns)
                     }  
                     if (t.nomchestionar.prescurtare=="Procent discount"){
                       procdiscount=Number(t.raspuns)
                     }  
                     if (t.nomchestionar.prescurtare=="Perioada de creditare"){
                       nrluni=Number(t.raspuns)
                     }
                     if (t.nomchestionar.prescurtare=="Modalitate rambursare"){
                       tiprambursare=t.raspuns
                     }
                      if (t.nomchestionar.prescurtare=="Rata cu discount"){
                       if(tiprambursare=='la finalul contractului'){
                         ratacudiscount=this.$globalHelpers.roundFormat(Number(sumasolicitata)*(Number(procdobanda)-Number(procdiscount))/100,2)
                        }else{
                        if (nrluni>0){

                          ratacudiscount=this.$globalHelpers.roundFormat(this.$globalHelpers.PMT((Number(procdobanda)-Number(procdiscount)), Number(nrluni), Number(sumasolicitata)),2)

                        }else{
                          ratacudiscount=0
                        }
                       }

                       t.raspuns=ratacudiscount
                     }

                      if (t.nomchestionar.prescurtare=="Rata credit solicitat"){
                       if(tiprambursare=='la finalul contractului'){
                         ratacredit=this.$globalHelpers.roundFormat(Number(sumasolicitata)*Number(procdobanda)/100,2)
                       }else{
                        if (nrluni>0){
                              ratacredit=this.$globalHelpers.roundFormat(Number(ratacudiscount)+(Number(sumasolicitata)*Number(procdiscount)/100),2)
                        }else{
                          ratacredit=0
                        }
                       }

                       t.raspuns=ratacredit
                     }
                      

                      if (t.nomchestionar.prescurtare=="Alte rate"){
                      alterate=Number(t.raspuns)
                    }
                    if (t.nomchestionar.prescurtare=="Venit fiscalizat"){
                      venitfiscalizat=Number(t.raspuns)
                    }
                    if (t.nomchestionar.prescurtare=="Grad de indatorare" && venitfiscalizat!=0){
                        // console.log(curs)
                        t.raspuns=this.$globalHelpers.roundFormat(100*((Number(alterate)+Number(curs)*Number(ratacredit)))/Number(venitfiscalizat),2)
                        graddeindatorare=Number(t.raspuns)
                    }
                     if (t.nomchestionar.prescurtare=="Venit suplimentar incadrare grad" ){
                      if ( graddeindatorare>40){
                        t.raspuns=this.$globalHelpers.roundFormat((100*((Number(alterate)+Number(curs)*Number(ratacredit)))/40)-Number(venitfiscalizat),2)
                    }else{
                      t.raspuns=0
                    }
                  }
                  })
         
       }  

    },
  
    created(){

          this.fields.map((t)=>{
              if (t.searchable){
                this.filterOn.push(t.key)
              }
          })
    },
  methods:{

   
  

  },
 
}
  
</script>
