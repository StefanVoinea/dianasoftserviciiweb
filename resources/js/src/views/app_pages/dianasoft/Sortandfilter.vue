<template>  
   <div >
   
            <b-row >
        <b-col cols="1">
       
    </b-col>
     
    <b-col cols="2">
      
    </b-col>
    <b-col cols="1">
       
    </b-col>
    <b-col cols="1">
       
    </b-col>
    <b-col cols="1">
       
    </b-col>
      <b-col class="d-flex justify-content-between" cols="1"> 
                
                   
                </b-col> 
    
    
      <b-col class="d-flex justify-content-around" cols="2"> 
                   <b-button 
                                  v-ripple.400="'rgba(234, 84, 85, 0.15)'"
                                  variant="outline-info"
                                  size="sm"
                                   class="btn-icon mb-1 mt-1"
                                  v-b-tooltip.hover.v-light
                                  title="Sort ascending"
                                  @click="sortAscending">
                                 <div class="d-flex flex-column align-items-center">
                                 <feather-icon   icon="ArrowDownIcon"/>
                                 <span>A-Z</span>
                                 </div>
                          </b-button> 
                          <b-button 
                                  v-ripple.400="'rgba(234, 84, 85, 0.15)'"
                                  variant="outline-info"
                                  size="sm"
                                   class="btn-icon mb-1 mt-1"
                                  v-b-tooltip.hover.v-light
                                  title="Sort descending"
                                  @click="sortDescending">
                                 <div class="d-flex flex-column align-items-center">
                                 <feather-icon   icon="ArrowUpIcon"/>
                                 <span>Z-A</span>
                                 </div>
                          </b-button> 

                         <b-button 
                                  v-ripple.400="'rgba(234, 84, 85, 0.15)'"
                                  variant="outline-success"
                                  
                                  class="btn-icon mb-1 mt-1"
                                  v-b-tooltip.hover.v-light
                                  title="Filter by selection"
                                  @click="modificaFiltru('incasari')">
                          <feather-icon   icon="FilterIcon"/>
                          </b-button> 
                           
             
  
                
                         <b-button 
                                  v-ripple.400="'rgba(234, 84, 85, 0.15)'"
                                  variant="outline-warning"
                                  
                                  class="btn-icon mb-1 mt-1"
                                  v-b-tooltip.hover.v-light
                                  title="Filter excluding selection"
                                  @click="modificaFiltruExcluding('incasari')">
                          <feather-icon   icon="FramerIcon"/>
                          </b-button> 
                           
             
    
       <b-button 
                              v-ripple.400="'rgba(113, 102, 240, 0.15)'"
                              variant="outline-danger"
                              class="btn-icon  mb-1 mt-1"
                              v-b-tooltip.hover.v-light
                              title="Remove filter"
                              @click="removeFilter('incasari')">
                              <feather-icon icon="XIcon" />
                          </b-button>
    </b-col>
    </b-row>
                   
       
    </div>
  
</template>

<script>
import Ripple from "vue-ripple-directive"


export default {
 props: {
         valoareActiveControl:[String,Number,Date],
         previousActiveControl:String,
         tipActiveControl:String,
         recordSet:Array ,
         recordSetOriginal:Array
        },
  directives: {
    
    Ripple,
  },
  name:"Sortandfilter",
  component:{
    
  },
  data() {

    return {
      valoareActiveControlLocal:"",
      tipActiveControlLocal:"",
      previousActiveControlLocal:"",
      recordSetLocal:[],
      recordSetOriginalLocal:[],
      combinatieFiltruSelectat:[],
    }
  },
  watch:{
    valoareActiveControl(){
      this.valoareActiveControlLocal=this.valoareActiveControl
     
    },
    previousActiveControl(){
      this.previousActiveControlLocal=this.previousActiveControl
      
    },
    tipActiveControl(){
      this.tipActiveControlLocal=this.tipActiveControl
      
    },
    recordSet(){
      this.recordSetLocal=Object.assign([],this.recordSet)
     
    },
     recordSetOriginal(){
      this.recordSetOriginalLocal=Object.assign([],this.recordSetOriginal)
     
    },
  },
  methods:{
      getSelectedText(tipfiltru) {
        var selectedText = this.valoareActiveControlLocal;
        var selectedControl = this.previousActiveControlLocal;
        var tipControl = this.tipActiveControlLocal;
        
        if (window.getSelection().toString()) {
            selectedText = window.getSelection().toString();
        } 
      
        if (document.selection) {
            selectedText = document.selection.createRange().text;
        } 
        
        this.combinatieFiltruSelectat.push({ selectedText: selectedText, selectedControl: selectedControl,filtru:tipfiltru,tipControl:tipControl })
        return this.combinatieFiltruSelectat;
    },
    
    sortAscending(){
        if(this.previousActiveControlLocal=="partenerul"){
            this.previousActiveControlLocal="partener"
            this.valoareActiveControlLocal=this.valoareActiveControlLocal.denumire
        }
        if(this.previousActiveControlLocal=="contract"){
            this.previousActiveControlLocal="nr_contract"
            this.valoareActiveControlLocal=this.valoareActiveControlLocal.nr_contract
        }
         this.recordSetLocal=Object.assign([],this.recordSetLocal.sort((a, b) =>{
                  const valA = a[this.previousActiveControl];
                  const valB = b[this.previousActiveControl];
                
                  // Nulle la început
                  if (valA == null && valB == null) return 0;
                  if (valA == null) return -1;
                  if (valB == null) return 1;
                  
                  return valA > valB ? 1 : valA < valB ? -1 : 0;
                }));
       

       
        this.$emit('refreshSortAndFilter', this.recordSetLocal)
        
    },
    sortDescending(){
        if(this.previousActiveControlLocal=="partenerul"){
            this.previousActiveControlLocal="partener"
            this.valoareActiveControlLocal=this.valoareActiveControlLocal.denumire
        }
         if(this.previousActiveControlLLocal=="contract"){
            this.previousActiveControlLocal="nr_contract"
            this.valoareActiveControlLocal=this.valoareActiveControlLocal.nr_contract
        }
        this.recordSetLocal=Object.assign([],this.recordSetLocal.sort((a, b) => {
                  const valA = a[this.previousActiveControl];
                  const valB = b[this.previousActiveControl];

                  // Nulle la început
                  if (valA == null && valB == null) return 0;
                  if (valA == null) return 1;
                  if (valB == null) return -1;

                  return valA > valB ? -1 : valA < valB ? 1 : 0;
                }));
               

       
        this.$emit('refreshSortAndFilter', this.recordSetLocal)        
        
    },
    removeFilter(){
        this.combinatieFiltruSelectat=[]
        this.recordSetLocal=Object.assign([],this.recordSetOriginalLocal)
        this.$emit('refreshSortAndFilter', this.recordSetLocal)        
    },
    modificaFiltru(){
      let selectedTextAndControl=this.getSelectedText("with")
      
      if(selectedTextAndControl.length>0){  
        this.recordSetLocal=Object.assign([],this.recordSetLocal.filter(item=>{
                                                                          let  filtru=true
                                                                          selectedTextAndControl.map(el=>{
                                                                           if (el.filtru=="without"){
                                                                              
                                                                                  if(el.tipControl=="Date"){
                                                                                    if(((new Date(item[el.selectedControl])).toLocaleDateString().includes(el.selectedText))){
                                                                                            filtru=false
                                                                                        }   
                                                                                  
                                                                                }else{ 
                                                                                  
                                                                                      if(item[el.selectedControl]){
                                                                                          if((item[el.selectedControl].toString().includes(el.selectedText))){
                                                                                              filtru=false
                                                                                          
                                                                                          }
                                                                                      }else{
                                                                                        filtru=false
                                                                                      }    
                                                                                 }
                                                                              }
                                                                               if (el.filtru=="with"){
                                                                                
                                                                                if(el.tipControl=="Date"){
                                                                                    
                                                                                    if(!((new Date(item[el.selectedControl])).toLocaleDateString().includes(el.selectedText))){
                                                                                            filtru=false
                                                                                     }
                                                                                }else{  
                                                                                      if(item[el.selectedControl]){
                                                                                       if(!(item[el.selectedControl].toString().includes(el.selectedText))){
                                                                                              filtru=false
                                                                                       }   
                                                                                      }else{
                                                                                        filtru=false
                                                                                      }    
                                                                                 }
                                                                              }   
                                                                                   
                                                                          })
                                                                          return filtru
                                                                      })) 
        }
         this.$emit('refreshSortAndFilter', this.recordSetLocal)                    
    },
    modificaFiltruExcluding(){
      let selectedTextAndControl=this.getSelectedText("without")
      if(selectedTextAndControl.length>0){  
        this.recordSetLocal=Object.assign([],this.recordSetLocal.filter(item=>{
                            let  filtru=true
                            selectedTextAndControl.map(el=>{
                                if (el.filtru=="without"){
                                 if(el.tipControl=="Date"){
                                      if(((new Date(item[el.selectedControl])).toLocaleDateString().includes(el.selectedText))){
                                              filtru=false
                                       }
                                  }else{ 
                                      if(item[el.selectedControl]){
                                         if((item[el.selectedControl].toString().includes(el.selectedText))){
                                                filtru=false
                                         }   
                                      }else{
                                        filtru=false
                                      }   
                                   }
                                }
                                 if (el.filtru=="with"){
                                  if(el.selectedControl=="data_scadenta"||el.selectedControl=="data_document"){
                                      if(!((new Date(item[el.selectedControl])).toLocaleDateString().includes(el.selectedText))){
                                              filtru=false
                                       }
                                  }else{ 
                                      if(item[el.selectedControl]){
                                         if(!(item[el.selectedControl].toString().includes(el.selectedText))){
                                                filtru=false
                                         }   
                                      }else{
                                                  filtru=false
                                         
                                      }   
                                   }
                                }   
                                     
                            })
                            return filtru
                        })) 
                          
          }
      this.$emit('refreshSortAndFilter', this.recordSetLocal)                        
    },
   
    

  },
  mounted(){
     /*
      window.addEventListener('focusin', () => {
        console.log('Element activ:', document.activeElement)
        console.log('Valoare:', document.activeElement?.value)
      })
      */
  }
   
}
  
</script>
