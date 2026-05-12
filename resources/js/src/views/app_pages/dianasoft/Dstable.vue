<template>  
    <div>
                               <b-row>
                                <b-col cols="4">
                                <h4>{{title}}</h4>
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
                                     
                                      <template  #cell()="data" >
                                       <div @click="rowSelected(data.item['id'])" v-if="data.field.field_type=='checkbox'&&data.field.key.includes('.')"  class="d-flex justify-content-center">
                                          <b-form-checkbox
                                              v-model="data.item[data.field.key.split('.')[0]][data.field.key.split('.')[1]]"
                                              class="custom-control-primary"
                                              value="1"
                                              unchecked-value="0"
                                            />
                                          
                                        </div>    
                                         <div  @click="rowSelected(data.item['id'])" v-if="data.field.field_type=='checkbox'&&!data.field.key.includes('.')"  class="d-flex justify-content-center">
                                          <b-form-checkbox
                                              v-model="data.item[data.field.key]"
                                              class="custom-control-primary"
                                              value="1"
                                              unchecked-value="0"
                                            />
                                          
                                        </div>    
                                         <div @click="rowSelected(data.item['id'])" v-if="data.field.field_type=='label' &&data.field.key.includes('.')" >
                                           
                                            {{data.item[data.field.key.split('.')[0]][data.field.key.split('.')[1]]}}
                                        </div>   
                                        <div @click="rowSelected(data.item['id'])" v-if="data.field.field_type=='label' &&!data.field.key.includes('.')" >
                                                  {{data.item[data.field.key]}}
                                        </div>   
                                         <div  v-else >
                                          
                                       
                                         <div  @click="rowSelected(data.item['id'])" v-if="data.field.field_type=='input'" >
                                              <b-form-input
                                                  :readonly="data.field.readonly"
                                                  autocomplete="off"
                                                  id="data.field.key"
                                                  v-model="data.item[data.field.key]"
                                                  />
                                         


                                        </div>     
                                        <div  @click="rowSelected(data.item['id'])" v-if="data.field.field_type=='selectonearray'" >
                                              <selectonearray 
                                                      name="raspuns_select"
                                                      :readonly="data.field.readonly" 
                                                      class="w-full"
                                                      v-model="data.item[data.field.key]" 
                                                      colCaut="denumire" 
                                                      camp="denumire" 
                                                      campDisplay=""  
                                                      :optiuni="data.field.lista"
                                                      limitToList="true"> 
                                               </selectonearray>

                                             


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

  name:"dstable",
 
  data() {

    return {
      filter: null,
      filterOn:[],
     
      
    }
  },
  watch:{
    
     value(){
         this.content = this.value
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
     rowSelected(value){
       this.$emit('rowSelected',value)
     }
   
  

  },
 
}
  
</script>
