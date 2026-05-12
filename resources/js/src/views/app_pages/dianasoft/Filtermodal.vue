
    
<template>
  <div id="my-container">
    <div  class="my-1 d-flex align-items-center justify-content-start">
      <!-- Our triggering (target) element -->
     
      
      
       <b-button
        v-ripple.400="'rgba(113, 102, 240, 0.15)'"
        variant="outline-success"
        class="btn-icon"
        id="popover-reactive-1"
        ref="button"
        size=""
         v-b-tooltip.hover.v-dark
        title="Filter records"
         @click="activeFilterLocal = true"
    >
      <i class="bi bi-funnel" style="font-size: 1rem; color:success; " ></i>
    </b-button> 
      
    </div>
   <b-modal
      v-model="activeFilterLocal"
      title="Filter records..."
      :header-bg-variant="'light'"
      :header-text-variant="'dark'"
      :body-bg-variant="'light'"
      :footer-bg-variant="'light'"
      :footer-text-variant="'dark'"
      size="xl"
    >
      <b-container fluid>
      <b-form
        ref="form"
        :style="{height: trHeight}"
        class="repeater-form"
        @submit.prevent="repeateAgain"
      >
        

        <b-row class="mb-1"
                v-for="(item, index) in items"
              :id="item.id"
              :key="item.id"
              ref="row" >
          <b-col cols="3">   
          Column                  
            <b-form-select
              id="column"
              v-model="item.column"
              :options="columnOptions"
              @change="changeColumn(index)"
            />
          </b-col>
          
          <b-col cols="2">     
          Type of filter                     
            <b-form-select
              id="filterType"
              v-model="item.filterType"
              :options="item.filterTypeOptions"
               @change="changeFilterType(index)"
            />
          </b-col>

          <b-col cols="2" v-show="item.filterShow||item.dateFromShow">   
          
          {{(item.filterToShow||item.dateToShow)?"ValueFrom":"Value"}}       
            <b-form-input v-show="item.filterShow" id="filter" v-model="item.filter"/>
          

                     <datacalendaristica 
                                          v-show="item.dateFromShow"
                                          id="dateFrom"
                                          v-model="item.dateFrom"
                                          :name="'Data'"
                                          :campDisplay="''"
                                          :readonly="false"> 
                     </datacalendaristica>
          </b-col>
        <b-col cols="2" v-show="item.filterToShow||item.dateToShow">  
             ValueTo         
            <b-form-input v-show="item.filterToShow" id="filterTo" v-model="item.filterTo"/>
        
                    <datacalendaristica   v-show="item.dateToShow"
                                          id="dateTo"
                                          v-model="item.dateTo"
                                          :name="'DateTo'"
                                          :campDisplay="''"
                                          :readonly="false"> 
                     </datacalendaristica>
          </b-col>

           <b-col cols="2">    
           AND / OR                           
            <b-form-select
               id="plusCondition"
               v-model="item.plusCondition"
               :options="plusConditionOptions"
               @change="addPlusCondition(index)"
            />
            </b-col>
       <!-- Remove Button -->
          <b-col
            lg="2"
            md="3"
            cols="1"
           
          >
            <b-button
                        v-show="index>0"
                        v-ripple.400="'rgba(234, 84, 85, 0.15)'"
                        variant="outline-danger"
                         class="mt-1"
                        @click="removeItem(index)">
              <feather-icon   icon="XIcon"/>
            
            </b-button>
          </b-col>
        
        </b-row>
 
      </b-form>
        
      </b-container>

      <template #modal-footer>
        <div class="w-100">
          <p class="float-left mb-0">
            
          </p>

          <b-button
            v-ripple.400="'rgba(255, 255, 255, 0.15)'"
            variant="primary"
            size="lg"
            class="float-right"
            @click="applyFilter"
          >
            Apply
          </b-button>
        </div>
      </template>
    </b-modal>
   
  </div>
</template>

<script>
import {  VBTooltip,   BModal, VBModal} from 'bootstrap-vue'
import { heightTransition } from '@core/mixins/ui/transition'
import Ripple from 'vue-ripple-directive'

export default {
  components: {
           VBTooltip,
           BModal, 
           VBModal
  },
  directives: {
    Ripple,
    'b-tooltip': VBTooltip,
    'b-modal': VBModal,
  },
  mixins: [heightTransition],
   props: {
        activeFilter:Boolean,
        target:HTMLElement,
        columns:Array,
        },

  name:"Filtermodal",

  data: () => ({
    items: [{
        id: 1,
        column: '',
        columnType:'',
        filterType: '',
        filter:'',
        filterTo:'',
        dateFrom:'',
        dateTo:'',
        plusCondition:'', 
        filterShow:true,
        filterToShow:false,
        dateFromShow:false,
        dateToShow:false,
        filterTypeOptions:[
                  { text: '- Choose type of filter-', value: '' }, 
                  { text: 'Contains', value: 'contains' },
                  { text: 'Not contains', value: 'notContains' },
                  { text: 'Equals with', value: 'equals' },
                  { text: 'Not equal with', value: 'notEqual' },
                  { text: 'Starts with', value: 'startsWith' },
                  { text: 'Ends with', value: 'endsWith' },
                ],   
        prevHeight: 0,
      }],
      nextTodoId: 2,

    bodyBgVariant: 'light',
    bodyTextVariant: 'dark',
    codeVariant:'',
    footerBgVariant: 'warning',
    footerTextVariant: 'dark',
    headerBgVariant: 'info',
    headerTextVariant: 'light',
    labels: {
     
    },
    variants: ['primary', 'secondary', 'success', 'warning', 'danger', 'info', 'light', 'dark'],
    
    plusConditionOptions:[
                            { text: '- Choose conditions-', value: '' },
                            { text: 'AND', value: 'AND' },
                            { text: 'OR', value: 'OR' },
                          ],
    activeFilterLocal:false,
    targetFilterLocal:'',
    
    columnOptions:[{ text: '- Choose 1 -', value: '' }],
    typeOptionsDate:[
                  { text: '- Choose type of filter -', value: '' }, 
                  { text: 'Equals with', value: 'equals' },
                  { text: 'Not equal with', value: 'notEqual' },
                  { text: 'Greater than', value: 'greaterThan' },
                  { text: 'Less than', value: 'lessThan' },
                  { text: 'Interval', value: 'inRange' }, 
                 
                ],
    typeOptionsNumber:[
                  { text: '- Choose type of filter -', value: '' }, 
                  { text: 'Equals with', value: 'equals' },
                  { text: 'Not equal with', value: 'notEqual' },
                  { text: 'Less than', value: 'lessThan' },
                  { text: 'Less than or equal', value: 'lessThanOrEqual' },
                  { text: 'Greater than', value: 'greaterThan' },
                  { text: 'Greater than or equal', value: 'greaterThanOrEqual' },
                  { text: 'Interval', value: 'inRange' }, 
                 
                ],
    typeOptionsText:[
                  { text: '- Choose type of filter-', value: '' }, 
                  { text: 'Contains', value: 'contains' },
                  { text: 'Not contains', value: 'notContains' },
                  { text: 'Equals with', value: 'equals' },
                  { text: 'Not equal with', value: 'notEqual' },
                  { text: 'Starts with', value: 'startsWith' },
                  { text: 'Ends with', value: 'endsWith' },
                ],            
    filtru:[]
    
  }),
  mounted() {
    this.initTrHeight()
  },
   watch: {
      activeFilter(){
        this.filtru=[]
         this.items= [{
        id: 1,
        column: '',
        columnType:'',
        filterType: '',
        filter:'',
        filterTo:'',
        dateFrom:'',
        dateTo:'',
        plusCondition:'', 
        filterShow:true,
        filterToShow:false,
        dateFromShow:false,
        dateToShow:false,
        filterTypeOptions:[
                  { text: '- Choose type of filter-', value: '' }, 
                  { text: 'Contains', value: 'contains' },
                  { text: 'Not contains', value: 'notContains' },
                  { text: 'Equals with', value: 'equals' },
                  { text: 'Not equal with', value: 'notEqual' },
                  { text: 'Starts with', value: 'startsWith' },
                  { text: 'Ends with', value: 'endsWith' },
                ],   
        prevHeight: 0,
      }]
      this.nextTodoId= 2
         this.activeFilterLocal=this.activeFilter
       },
        targetFilter(){
         
         this.targetFilterLocal=this.targetFilter
       },
       activeFilterLocal(){
             
            if (this.activeFilterLocal==false){

              this.$emit('closed')
            }else{
              this.filtru=[]
                this.items= [{
                              id: 1,
                              column: '',
                              columnType:'',
                              filterType: '',
                              filter:'',
                              filterTo:'',
                              dateFrom:'',
                              dateTo:'',
                              plusCondition:'', 
                              filterShow:true,
                              filterToShow:false,
                              dateFromShow:false,
                              dateToShow:false,
                              filterTypeOptions:[
                                        { text: '- Choose type of filter-', value: '' }, 
                                        { text: 'Contains', value: 'contains' },
                                        { text: 'Not contains', value: 'notContains' },
                                        { text: 'Equals with', value: 'equals' },
                                        { text: 'Not equal with', value: 'notEqual' },
                                        { text: 'Starts with', value: 'startsWith' },
                                        { text: 'Ends with', value: 'endsWith' },
                                      ],   
                              prevHeight: 0,
                            }]
      this.nextTodoId= 2
            }
           
       },
      
     
  
    
  },
  
  created() {
    window.addEventListener('resize', this.initTrHeight)
     this.columnOptions=this.columns.map((t)=>{
                    return {text:t.label,value:t.field}
                   }).sort((a, b) => a.text.localeCompare(b.text))
  },
  destroyed() {
    window.removeEventListener('resize', this.initTrHeight)
  },
  methods: {
    applyFilter(){
       
       this.items.map((t)=>{
         let conditie={}
         

    /*   if (t.columnType=="boolean"){
          if(t.filterTo=="true"){
              t.filterTo="1"
            }else{
              
                t.filterTo="0"
              }
            
        } -*/

         conditie[t.column]={
                              'filterType':t.columnType,
                              'type':t.filterType,
                              'filter':t.filter,
                              'filterTo':t.filterTo,
                              'dateFrom':t.dateFrom,
                              'dateTo':t.dateTo,
                              'operator':t.plusCondition,
                              }
                            
          this.filtru.push(conditie)
        })
        
        this.$emit('applyFilter',this.filtru)
        this.activeFilterLocal = false
    },
    removeItem(index) {
      this.items.splice(index, 1)
      this.trTrimHeight(this.$refs.row[0].offsetHeight)
    },
    initTrHeight() {
      this.trSetHeight(null)
      this.$nextTick(() => {
        if(this.$refs.form){
        this.trSetHeight(this.$refs.form.scrollHeight)
        }
      })
    },
    addPlusCondition(index){
     
      if (this.items[index]["plusCondition"]=="AND"||this.items[index]["plusCondition"]=="OR") {
        this.items.push({
                          id: this.nextTodoId += this.nextTodoId,
                          column: '',
                          columnType:'',
                          filterType: '',
                          filter:'',
                          filterTo:'',
                          dateFrom:'',
                          dateTo:'',
                          plusCondition:'', 
                          filterShow:true,
                          filterToShow:false,
                          dateFromShow:false,
                          dateToShow:false,
                          filterTypeOptions:[
                                              { text: '- Choose type of filter-', value: '' }, 
                                              { text: 'Contains', value: 'contains' },
                                              { text: 'Not contains', value: 'notContains' },
                                              { text: 'Equals with', value: 'equals' },
                                              { text: 'Not equal with', value: 'notEqual' },
                                              { text: 'Starts with', value: 'startsWith' },
                                              { text: 'Ends with', value: 'endsWith' },
                                            ],   
                        })
          
          this.$nextTick(() => {
            this.trAddHeight(this.$refs.row[0].offsetHeight)
          })
        }
      },
    changeFilterType(index){
     
      
      this.columns.map((t)=>{
                    if(this.items[index]["column"]==t.field){
                        this.items[index]["columnType"]= t.type         
                    }
                   })

      
        if(this.items[index]["filterType"]=="inRange"){
          
          if (this.items[index]["columnType"]=="date"){
              this.items[index]["filterShow"]=false
              this.items[index]["filterToShow"]=false
              this.items[index]["dateFromShow"]=true
              this.items[index]["dateToShow"]=true
            }else{
              this.items[index]["filterShow"]=true
              this.items[index]["filterToShow"]=true
              this.items[index]["dateFromShow"]=false
              this.items[index]["dateToShow"]=false
            }
        }else{
          if (this.items[index]["columnType"]=="date"){
              this.items[index]["filterShow"]=false
              this.items[index]["filterToShow"]=false
              this.items[index]["dateFromShow"]=true
              this.items[index]["dateToShow"]=false
          }else{
             this.items[index]["filterShow"]=true
              this.items[index]["dateFromShow"]=false 
              this.items[index]["dateToShow"]=false
          }
        }
      },
    changeColumn(index){
       
        this.columns.map((t)=>{
                    if(this.items[index]["column"]==t.field){
                        
                        if(t.type=="date"){

                        this.items[index]["filterTypeOptions"]=this.typeOptionsDate

                      }
                      if(t.type=="text"){
                        this.items[index]["filterTypeOptions"]=this.typeOptionsText
                      }
                      if(t.type=="number"){
                        this.items[index]["filterTypeOptions"]=this.typeOptionsNumber
                      }
                       this.items[index]["filterType"]=''
                    }            
                    
                   })
      },
    onClose() {
      this.activeFilterLocal = false
      this.$emit('closed')
    },
   
   
   
  },
}
</script>


