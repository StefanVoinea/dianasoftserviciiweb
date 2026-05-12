<template>
   <b-overlay
      :show="showLoading"
      rounded="sm"
      no-fade
      :variant="'primary'"
      :opacity="0.25"
      :blur="'2px'"
    >
  <b-row class="d-flex align-items-center justify-content-center">
   
    <b-col cols="12" >
    <div>
     <b-row>
        <b-col cols="2">
        <h3 class="text-primary">{{titlu}}</h3> 
   

    <!-- search input -->
    <div class="custom-search d-flex justify-content-start">
    <div class="d-flex justify-content-between">
      <b-form-group>
        <div class="d-flex align-items-center justify-content-between">
          
          <b-form-input
            v-model="searchTerm"
            size="sm"
            @change="searchBy"
            :placeholder="this.cautareDupaDisplayName?'Search by '+cautareDupaDisplayName:'Search by...'"
            type="text"
            class="d-inline-block mr-1"
            v-b-tooltip.hover.v-light
            title="First click on the column where the search will be made! "
          />
          <b-button 
              v-ripple.400="'rgba(113, 102, 240, 0.15)'"
              variant="outline-primary"
              class="btn-icon  mr-1"
              size=""
               v-b-tooltip.hover.v-light
              :title="'Search '+this.searchTerm+ ' in column ' +this.cautareDupaDisplayName"
              @click="searchBy">
              <feather-icon icon="SearchIcon" />
          </b-button>
          <b-button 
              v-ripple.400="'rgba(113, 102, 240, 0.15)'"
              variant="outline-danger"
              class="btn-icon  mr-1"
              size=""
              v-b-tooltip.hover.v-light
              title="Remove filter"
              @click="removeFilter">
              <feather-icon icon="XIcon" />
          </b-button>
          <Filtermodal
             :activeFilter="activeFilter"
             :editVar="filterModel"
             :target="targetFilter"
             :columns="columns"
             @applyFilter="applyFilter"
            @closed="filterClosed">
          </Filtermodal>
        </div>
       
      </b-form-group>
      
    </div>
           
      <slot name="stanga">
            
       </slot>
      </div>   
      </b-col>
      <b-col cols="7"> 
    
       <slot name="centru">
            
       </slot>
    
      </b-col>
       
       <b-col cols="3">
        <b-container fluid>
          <div  class=" d-flex justify-content-end mt-2 mr-5"> 
          <slot>
            
          </slot>
          
            <b-button 
                      variant="outline-success"
                      class="btn-icon mr-1"
                      size="" 
                      v-show="canAdd&&!faraAEVD" 
                      v-b-tooltip.hover.v-light
                      title="Adaugă"
                      @click="add"> 
                      <feather-icon icon="PlusIcon" />

            </b-button>
             <b-button 
                      variant="outline-primary"
                      class="btn-icon  mr-1"
                      size="" 
                      v-show="canEdit&&!faraAEVD" 
                      v-b-tooltip.hover.v-light
                      title="Modifică înregistrarea selectată"
                      @click="edit" 
                       > 
                        <feather-icon icon="Edit2Icon"/>

            </b-button>
           
            
             <b-button 
                      variant="outline-info"
                      class="btn-icon  mr-1"
                      size="" 
                      v-show="canView&&!faraAEVD" 
                      v-b-tooltip.hover.v-light
                      title="Vizualizează înregistrarea selectată"
                      @click="view" 
                       > 
                        <feather-icon
                                      icon="EyeIcon"/>

            </b-button>

             <b-button 
                      variant="outline-danger"
                      class="btn-icon  mr-1"
                      size="" 
                      v-show="canDelete&&!faraAEVD" 
                      v-b-tooltip.hover.v-light
                      title="Sterge înregistrarea selectată"
                      @click="openConfirm" 
                       > 
                       <feather-icon icon="Trash2Icon" />
            </b-button>           
            
             <b-button variant="outline-warning"
                       class="btn-icon mr-1"
                       v-show="deblocare"
                       size="lg" 
                       v-b-tooltip.hover.v-light
                       title="Deblocheaza inregistrare"
                       @click="deblocheazaInregistrare" > 
                       <feather-icon icon="UnlockIcon" />
            </b-button>           
            <b-button 
                      variant="outline-success"
                      class="btn-icon  mr-1"
                      size="" 
                      v-show="canExport" 
                      v-b-tooltip.hover.v-light
                      title="Export to Excel"
                      @click="exportXLS" 
                       > 
                        <i  class="bi bi-file-earmark-excel" ></i> 
                       
 
            </b-button>
          </div>
        
         </b-container> 
       </b-col>
      </b-row>
    <!-- table -->

     <b-row class="d-flex align-items-center justify-content-center">
   
    <b-col :cols="cols" >
    <vue-good-table
       
      compactMode
      :columns="columns"
      :rows="rows"
      :row-style-class="rowStyleClassFn"
     

     
      styleClass="vgt-table condensed bordered "
      
      
      :sort-options="{
                        enabled: false,
                        
                        
                     }" 

      
      :select-options="{
                          enabled: true,
                          selectOnCheckboxOnly: false, 
                          selectionInfoClass: 'row-selected',
                          selectionText: 'rows selected',
                          clearSelectionText: 'clear',
                          disableSelectInfo: true,
                          selectAllByGroup: true, 
                        }"
     
     :pagination-options="{
        enabled: true,
        perPage:pageLength
      }"

      max-height="650px"
      :group-options="{
                          enabled: false,
                          rowKey:'id',
                          collapsable: false, 
                          
                          
                        }"
      :fixed-header="fixedHeaderLocal" 
      :line-numbers="true"
      :totalRows="totalRecords"
      @on-row-click="onRowClick"
      @on-row-dblclick="onRowDoubleClick"
      @on-cell-click="onCellClick"
      @on-row-mouseenter="onRowMouseover"
      @on-row-mouseleave="onRowMouseleave"
      @on-search="onSearch"
      @on-page-change="onPageChange"
      @on-per-page-change="onPerPageChange"
      @on-sort-change="onSortChange"
      @on-column-filter="onColumnFilter"
      @on-select-all="onSelectAll"
      @on-selected-rows-change="selectionChanged"
      theme="polar-bear" 
      
    >
    //CUSTOM ROW TEMPLATE
        <template slot="table-row" slot-scope="props">
         
         
      <!-- <span v-if="props.column.field == 'status'">
        <span style="font-weight: bold; color: blue;">{{props.row.status}}</span> 
      </span>
      <span v-else>
        {{props.formattedRow[props.column.field]}}
      </span> -->
    </template> 

    //CUSTOM COLUMN HEADERS
     <template slot="table-column" slot-scope="props">
     <b-row>
      <b-col cols="9" class="d-flex justify-content-end">
         
        {{props.column.label}} 
       </b-col> 
       <b-col cols="3">
        <div class="d-flex justify-content-between">
       
        <span v-if="props.column.showSortAsc">
         <i class="bi bi-sort-alpha-down" style="font-size: 1.5rem; " @click="sortAsc(props.column.field)"> </i>
        </span>
        <span v-else>
          <i class="bi bi-sort-alpha-up" style="font-size: 1.5rem; color: cornflowerblue;" @click="sortDesc(props.column.field)"> </i>
        </span> 

         </div> 
        </b-col>
        
        </b-row> 
          
     
        
  </template>
   
   //Custom column filters

<!--  <template slot="column-filter" slot-scope="props">
    <my-custom-filter
      v-if="props.column.filterOptions.customFilter"       
      @input="handleCustomFilter"/>
  </template>  -->


     <!--  <div slot="table-actions">
        This will show up on the top right of the table. 
      </div> -->

      <!-- <div slot="table-actions-bottom">
        This will show up on the bottom of the table. 
      </div> -->

     <div slot="emptystate">
      Nu exista inregistrari
     </div> 

      <template
        slot="table-row"
        slot-scope="props"
      >

        <!-- Column: Name -->
        <span
          v-if="props.column.field === 'fullName'"
          class="text-nowrap"
        >
          <b-avatar
            :src="props.row.avatar"
            class="mx-1"
          />
          <span class="text-nowrap">{{ props.row.fullName }}</span>
        </span>
         <!-- Column: Status -->
         <span v-else-if="props.row.blocat">
          <span style="color:red">
               {{props.formattedRow[props.column.field]}}
          </span>
        </span> 
        <!-- Column: Status -->
         <span v-else-if="props.column.field === 'status' && props.row.status=='Administrare'">
          <span style="color:#1E90FF">
            {{ props.row.status }}
          </span>
        </span> 
         <span v-else-if="props.column.field === 'status' && props.row.status=='Finalizat'">
          <span style="color:#66CDAA">
            {{ props.row.status }}
          </span>
        </span> 
        <span v-else-if="props.column.field === 'status' && (props.row.status=='Executare' || props.row.status=='Executare suspendata')">
          <span style="color:  #FF0000">
            {{ props.row.status }}
          </span>
        </span> 
        <!-- Column: Action -->
        <span v-else-if="props.column.field === 'action'">
          <span>
            <b-dropdown
              variant="link"
              toggle-class="text-decoration-none"
              no-caret
            >
              <template v-slot:button-content>
                <feather-icon
                  icon="MoreVerticalIcon"
                  size="16"
                  class="text-body align-middle mr-25"
                />
              </template>
              <b-dropdown-item>
                <feather-icon
                  icon="Edit2Icon"
                  class="mr-50"
                />
                <span>Edit</span>
              </b-dropdown-item>
              <b-dropdown-item>
                <feather-icon
                  icon="TrashIcon"
                  class="mr-50"
                />
                <span>Delete</span>
              </b-dropdown-item>
            </b-dropdown>
          </span>
        </span>

        <!-- Column: HTML -->
        <span v-else-if="props.column.type === 'html'" v-html="props.formattedRow[props.column.field]">
        </span>

        <!-- Column: Common -->
        <span v-else>
          {{
                props.column.field === 'zile_intarziere'
                  ? (props.row.zile_intarziere == null || props.row.zile_intarziere === '' ? 0 : props.row.zile_intarziere)
                  : props.formattedRow[props.column.field]
              }}
        </span>
      </template>

      <!-- pagination -->
      <template
        slot="pagination-bottom"
        slot-scope="props"
      >
        <div class="d-flex justify-content-between flex-wrap">
          <div class="d-flex align-items-center mb-0 mt-1">
            <span class="text-nowrap ">
              Showing 1 to
            </span>
            <b-form-select
              v-model="pageLength"
              :options="['10','50','100']"
              class="mx-1"
              @input="(value)=>props.perPageChanged({currentPerPage:value})"
            />
            <span class="text-nowrap"> of {{ props.total }} entries </span>
          </div>
          <div>
            <b-pagination
              :value="1"
              :total-rows="props.total"
              :per-page="pageLength"
              first-number
              last-number
              align="right"
              prev-class="prev-item"
              next-class="next-item"
              class="mt-1 mb-0"
              @input="(value)=>props.pageChanged({currentPage:value})"
            >
              <template #prev-text>
                <feather-icon
                  icon="ChevronLeftIcon"
                  size="18"
                />
              </template>
              <template #next-text>
                <feather-icon
                  icon="ChevronRightIcon"
                  size="18"
                />
              </template>
            </b-pagination>
          </div>
        </div>
      </template>
    </vue-good-table>
    </b-col>
    </b-row>
  </div>
    </b-col>
</b-row>

</b-overlay>
</template>

<script>

import Filtermodal from './Filtermodal'
import { VueGoodTable } from 'vue-good-table'
import store from '@/store/index'
import Ripple from 'vue-ripple-directive'
import {VBTooltip} from 'bootstrap-vue'
export default {
  components: {
    VueGoodTable,
    Filtermodal,
   
  },
  directives: {
    Ripple,
    'b-tooltip': VBTooltip,
  },
  props: {
        value:String,
        columnDefs:Array,
        modelName:String,
        titlu:String,
        campFiltruStart:String,
        idselectat:[String,Number],
        filtruRuta:[String,Number,Object],
        cookie:String,
        refresh:Boolean,
        deblocheaza:Boolean,
        faraAEVD:Boolean,
        dataToCheck:Object,
        fixedHeader:Boolean,
        deblocare:Boolean,
       
        cols:{
          type:Number,
          default: 12
        }, 
},
  name:'tabelcomponent',
  data() {
    return {
      canEdit:this.$userpermitt.can("edit"+this.capitalize(this.modelName.indexOf("?")!=-1?this.modelName.substr(0,this.modelName.indexOf("?")):this.modelName)),
      canDelete:this.$userpermitt.can("delete"+this.capitalize(this.modelName.indexOf("?")!=-1?this.modelName.substr(0,this.modelName.indexOf("?")):this.modelName)),
      canAdd:this.$userpermitt.can("add"+this.capitalize(this.modelName.indexOf("?")!=-1?this.modelName.substr(0,this.modelName.indexOf("?")):this.modelName)),
      canView:this.$userpermitt.can("view"+this.capitalize(this.modelName.indexOf("?")!=-1?this.modelName.substr(0,this.modelName.indexOf("?")):this.modelName)),
      canImport:this.$userpermitt.can("import"+this.capitalize(this.modelName.indexOf("?")!=-1?this.modelName.substr(0,this.modelName.indexOf("?")):this.modelName)),
      canExport:this.$userpermitt.can("export"+this.capitalize(this.modelName.indexOf("?")!=-1?this.modelName.substr(0,this.modelName.indexOf("?")):this.modelName)),
      activeFilter:false,
      selectedRow: null,
      totalRecords:0,
      targetFilter:Object,
      searchedVal:"",
      cautareDupaDisplayName:"",
      cautareDupa:"",
      //fixedHeaderLocal:this.fixedHeader,
      fixedHeaderLocal:false,
      currentPageTblRecords:1,
      modelFilter:[],
      filterModel:[],
      showLoading:false,
      sortModel:[{
                 colId:"",
                 sort:""
                 }] ,
      searchQuery: '',
      
     pageLength: 10,
      page:1,
      dir: false,
      columns: this.columnDefs,
     
      rows: [  ],
      searchTerm: '',
      
    }
  },
 watch: {
 
  fixedHeader(){
      this.fixedHeaderLocal=this.fixedHeader
    },
  filtruRuta(){
       
      if(this.$store.state.app.ultimacautare){
        if(JSON.parse(this.$store.state.app.ultimacautare).modelName==this.modelName){
          this.cautareDupa=JSON.parse(this.$store.state.app.ultimacautare).cautareDupa
          this.searchedVal=JSON.parse(this.$store.state.app.ultimacautare).searchedVal 
        
        }else{
        this.cautareDupa=""
        this.searchedVal=""
        
      }
        
      }else{
        this.cautareDupa=""
        this.searchedVal=""
        
      }
      this.getRecords(this.currentPageTblRecords,this.pageLength,this.sortModel,this.searchedVal,this.cautareDupa,this.modelFilter)
    },
    refresh(){
      
      if(this.$store.state.app.ultimacautare){
        if(JSON.parse(this.$store.state.app.ultimacautare).modelName==this.modelName){
          this.cautareDupa=JSON.parse(this.$store.state.app.ultimacautare).cautareDupa
          this.searchedVal=JSON.parse(this.$store.state.app.ultimacautare).searchedVal 
        
        }else{
        this.cautareDupa=""
        this.searchedVal=""
        
      }
        
      }else{
        this.cautareDupa=""
        this.searchedVal=""
        
      }
      this.getRecords(this.currentPageTblRecords,this.pageLength,this.sortModel,this.searchedVal,this.cautareDupa,this.modelFilter)

    },
    deblocheaza(){
      
           this.showLoading=true
                const payLoad={}
                payLoad.requestType="post"
                payLoad.requestUrl="/"+this.modelName+"/deblocheaza"
                this.$store.dispatch("app/api_Request",payLoad)
                              .then(response=>{
                                 if(this.$store.state.app.ultimacautare){
        if(JSON.parse(this.$store.state.app.ultimacautare).modelName==this.modelName){
          this.cautareDupa=JSON.parse(this.$store.state.app.ultimacautare).cautareDupa
          this.searchedVal=JSON.parse(this.$store.state.app.ultimacautare).searchedVal 
        
        }else{
        this.cautareDupa=""
        this.searchedVal=""
        
      }
        
      }else{
        this.cautareDupa=""
        this.searchedVal=""
        
      }
      this.getRecords(this.currentPageTblRecords,this.pageLength,this.sortModel,this.searchedVal,this.cautareDupa,this.modelFilter)
                                        this.showLoading=false

                                        })
    },
   showSortAscLocal(){
                
                       this.columns.map((t)=>{
                                             if(this.showAscSortLocal[t.field]){
                                              this.showSortAsc[t.field]=true
                                              }else{
                                                this.showSortAsc[t.field]=false
                                              }

                    })
       },
  },
 
  methods: {
    exportXLS(){
                  

          this.showLoading=true   
          const payLoad={} 
          
          payLoad.requestType="post"
          payLoad.requestUrl='/exportXLS'
          payLoad.filtruRuta=this.filtruRuta
          payLoad.modelName=this.modelName
          payLoad.sortModel=this.sortModel
          payLoad.searchModel=this.searchedVal
          payLoad.cautareDupa=this.cautareDupa
          payLoad.filterModel=this.modelFilter
          this.$store.dispatch("app/api_blob_Request",payLoad)
          .then(response=>{
                       
                      
                           var fileURL = window.URL.createObjectURL(new Blob([response]));
                           var fileLink = document.createElement('a');
                           fileLink.href = fileURL;
                           
                             fileLink.setAttribute('download', this.modelName+'_'+(new Date().toLocaleDateString()).replace(".","_")+'.xls');
                         
                           document.body.appendChild(fileLink);
                           fileLink.click();
                           // this.$router.push({name:'antetvanzari'})
                    
                        
                       this.showLoading=false
                    
          })
          .catch(error => {
            this.showLoading=false
           this.handleErrors(error)
          
          
          })

    },
    handleErrors(error){
        
             this.showLoading=false
            if(error.status==401)
            {

              this.$bvToast.toast("Acces neautorizat!", {
                                title: "Accesarea neautorizată a unui sistem informatic reprezintă o infracțiune! <br/> Sistemul monitorizează toate încercarile de accesare neautorizată!",
                                variant:'danger',
                                solid: false,
                                appendToast: true,
                                autoHideDelay: 3000,
                                toaster: 'b-toaster-bottom-right',
                              })  

             
              this.$router.replace({name:'pageLogout'})
              
            }else
            {
            this.$bvToast.toast(error.statusText, {
                                title: `Eroare la conectarea la server!`,
                                variant:'danger',
                                solid: false,
                                appendToast: true,
                                autoHideDelay: 3000,
                                toaster: 'b-toaster-bottom-right',
                              })  
           
                }
    },  
    applyFilter(value){
      this.activeFilter=false 
      this.modelFilter=value
      this.getRecords( this.currentPageTblRecords,this.pageLength,this.sortModel,this.searchedVal,this.cautareDupa,this.modelFilter)
     },
    removeFilter(){
      this.searchedVal=""
      this.cautareDupa=""
      this.cautareDupaDisplayName=""
      this.searchTerm=""
      this.modelFilter=[]
      this.sortModel=[{
                 colId:"",
                 sort:""
                 }] 
      let ultimacautare={
                                          "modelName":this.modelName,
                                          "cautareDupa":this.cautareDupa,
                                          "searchedVal":this.searchedVal
                      }
                      
    this.$store.dispatch('app/ultimacautare',JSON.stringify(ultimacautare))
      this.getRecords( this.currentPageTblRecords,this.pageLength,this.sortModel,this.searchedVal,this.cautareDupa,this.modelFilter)
    },
    filterClosed(){
      this.activeFilter=false
    },
   
    
    onPerPageChange(params) {
    // params.currentPage - current page that pagination is at
    // params.currentPerPage - number of items per page
    // params.total - total number of items in the table
    this.pageLength=params.currentPerPage
    this.getRecords( this.currentPageTblRecords,this.pageLength,this.sortModel,this.searchedVal,this.cautareDupa,this.modelFilter)
   },
   sortAsc(params){
    this.columns.map((t)=>{
                 if(t.field==params){
                    t.showSortAsc=false
                 }else{
                   t.showSortAsc=true
                 }
      })
    this.sortModel[0].colId=params
    this.sortModel[0].sort="asc"
    this.getRecords( this.currentPageTblRecords,this.pageLength,this.sortModel,this.searchedVal,this.cautareDupa,this.modelFilter)

   },
   sortDesc(params){
    
    this.columns.map((t)=>{
                 if(t.field==params){
                    t.showSortAsc=true
                 }else{
                   t.showSortAsc=true
                 }
      })
   this.sortModel[0].colId=params
   this.sortModel[0].sort="desc"
   this.getRecords( this.currentPageTblRecords,this.pageLength,this.sortModel,this.searchedVal,this.cautareDupa,this.modelFilter)

   },
   filterColumn(params){
    this.targetFilter= this.$refs['filter'+params]
    this.activeFilter=!this.activeFilter

   
   },
    onSortChange(params) {
    // params[0].sortType - ascending or descending
    // params[0].columnIndex - index of column being sorted
  },
    onColumnFilter(params) {
    // params.columnFilters - filter values for each column in the following format:
    // {field1: 'filterTerm', field3: 'filterTerm2')
  },
    onSelectAll(params) {
    // params.selected - whether the select-all checkbox is checked or unchecked
    // params.selectedRows - all rows that are selected (this page)
    
  },
    selectionChanged(params) {
    // params.selectedRows - all rows that are selected (this page)

  },
    onPageChange(params) {
    // params.currentPage - current page that pagination is at
    // params.currentPerPage - number of items per page
    // params.total - total number of items in the table
    
    this.currentPageTblRecords=params.currentPage
    this.getRecords( this.currentPageTblRecords,this.pageLength,this.sortModel,this.searchedVal,this.cautareDupa,this.modelFilter)
  },
 
    searchBy(){
      

      if(this.cautareDupa){
      const coloana = this.columnDefs.find(col => col.field === this.cautareDupa);
      if (coloana.type=="boolean"){
          if(this.searchTerm.toUpperCase()=="TRUE" || this.searchTerm=="1" || this.searchTerm.toUpperCase()=="DA"){
              this.searchedVal="1"
            }else{
              
                this.searchedVal="0"
              }
            
        }else{
        this.searchedVal=this.searchTerm

        }
        let ultimacautare={
                                              "modelName":this.modelName,
                                              "cautareDupa":this.cautareDupa,
                                              "searchedVal":this.searchedVal
                          }
                      
    this.$store.dispatch('app/ultimacautare',JSON.stringify(ultimacautare))

    this.getRecords( this.currentPageTblRecords,this.pageLength,this.sortModel,this.searchedVal,this.cautareDupa,this.modelFilter)
    }else{

      this.$bvToast.toast('Dati click pe coloana in care doriti sa efectuati cautarea!', {
        title: `Atentie! `,
        variant:'warning',
        solid: false,
        appendToast: true,
        autoHideDelay: 3000,
        toaster: 'b-toaster-bottom-right',
      })  
    }
    },
    onSearch(params) {
    // params.searchTerm - term being searched for
    // params.rowCount - number of rows that match search
    
    
    
  },
    onRowMouseleave(row, pageIndex) {
    // row - row object 
    // pageIndex - index of this row on the current page.
  },
    onRowMouseover(params) {
    // params.row - row object 
    // params.pageIndex - index of this row on the current page.
  },
    onCellClick(params) {
    // params.row - row object 
    // params.column - column object
    // params.rowIndex - index of this row on the current page.
    // params.event - click event
    this.cautareDupa=params.column.field
    this.cautareDupaDisplayName=params.column.label
  },
    onRowClick(params) {
    // params.row - row object 
    // params.pageIndex - index of this row on the current page.
    // params.selected - if selection is enabled this argument 
    // indicates selected or not
    // params.event - click event
    
    this.selectedID=params.row
    
     this.rows.forEach(item=>{
      
        if(item.id!=params.row.id){
          this.$set(item,'vgtSelected', false);
        }else{
          this.$set(item,'vgtSelected', true);
        }
      },this);
    this.$emit('onSelectionChanged',this.selectedID)
  },
  onRowDoubleClick(params) {
    // params.row - row object 
    // params.pageIndex - index of this row on the current page.
    // params.selected - if selection is enabled this argument 
    // indicates selected or not
    // params.event - click event
    this.view()
  },
  rowStyleClassFn(row) {
    if(this.selectedID){
      if(this.selectedID.id==row.id){
        return 'info fontsizeMarit' ;
      }else{
        if(row.anulat){
          return (row.anulat?'warning':'')+' fontsize'
        }
        if(row.status){
          return (row.status.includes("Aprobat")?'success':(row.status.includes("Respins")||row.status.includes("Renunt")?'warning':''))+' fontsize'
        }else{
          return ''+' fontsize'
        }
      }  
    }else{
        if(row.anulat){
          return (row.anulat?'warning':'')+' fontsize'
        }
        if(row.status){
          return (row.status.includes("Aprobat")?'success':(row.status.includes("Respins")||row.status.includes("Renunt")?'warning':''))+' fontsize'
        }else{
          return ''+' fontsize'
        }
       
    } 
  
  },

  // load items is what brings back the rows from server

  //this.serverParams
  getRecords(page=1,pageLength=null,sortModel=null,searchModel=null,cautareDupa=null,filterModel=null){
         // this.isLoading=true
          this.showLoading=true
          
          const payLoad={} 
          payLoad.requestType="post"
          payLoad.requestUrl="/"+this.modelName+"?page="+page
          payLoad.filtruRuta=this.filtruRuta
          payLoad.page=page
          payLoad.pageLength=pageLength
          payLoad.sortModel=sortModel
          payLoad.searchModel=searchModel
          payLoad.cautareDupa=cautareDupa
          payLoad.filterModel=filterModel
          
          this.$store.dispatch("app/api_Request",payLoad)
          .then(response=>{
            
                      this.totalRecords = response.total;
                      this.rows = response.data; 
                      
                      this.$emit("refresh",this.rows)
                    //  this.isLoading=false
                      this.showLoading=false
                    
          })
          .catch(error => {

         //  this.isLoading=true
           this.showLoading=false
          })

    },
      capitalize(sir){
        
      return sir.charAt(0).toUpperCase() + sir.slice(1)
    },
    add(){
          this.$emit("adauga")
    },
    edit(){
             if (this.selectedID==null)
            {
              this.$bvToast.toast('Selectați înregistrarea de modificat!', {
                                                                        title: `Atentie! `,
                                                                        variant:'warning',
                                                                        solid: false,
                                                                        appendToast: true,
                                                                        autoHideDelay: 3000,
                                                                        toaster: 'b-toaster-bottom-right',
                                                                      })  
              
            }else
            {

                 if(this.dataToCheck){
                  if(this.$globalHelpers.perioadablocata(this.selectedID[this.dataToCheck.dt],this.dataToCheck.tip)){
                    this.$bvToast.toast('Perioada este blocata!', {
                                                                                title: `Atentie! `,
                                                                                variant:'danger',
                                                                                solid: false,
                                                                                appendToast: true,
                                                                                autoHideDelay: 3000,
                                                                                toaster: 'b-toaster-bottom-right',
                                                                              })  
                    return false
                  }
                }
               if ((this.selectedID.tip_nota=="GESTIUNE"||this.selectedID.tip_nota=="INCHIDERE TVA"||this.selectedID.tip_nota=="INCHIDERE 121") && this.modelName!="cheltuieliinavans")
                {
                   this.$bvToast.toast("Notele contabile generate nu pot fi modificate decât prin modificarea documentului sursă!"
                                       , {
                                                                              title: "Nota contabila generată!",
                                                                              variant:'warning',
                                                                              solid: false,
                                                                              appendToast: true,
                                                                              autoHideDelay: 3000,
                                                                              toaster: 'b-toaster-bottom-right',
                                                                            })  
                  
                  return false
                  
                }else{
                 if(this.deblocare){
                    this.showLoading=true
                    const payLoad=this.selectedID 
                    payLoad.requestType="post"
                    payLoad.requestUrl="/"+this.modelName+"/blocheaza/"+this.selectedID.id
                    this.$store.dispatch("app/api_Request",payLoad)
                              .then(response=>{
                                        this.showLoading=false
                                          if(response=="OK"){     
                                              this.$emit("edit")
                                          }else{
                                            if(response=="NOK"){
                                            this.$bvToast.toast("Eroare la blocarea inregistrarii!", {
                                                                        title: "Eroare la blocare! ",
                                                                        variant:"danger",
                                                                        solid: false,
                                                                        appendToast: true,
                                                                        autoHideDelay: 3000,
                                                                        toaster: "b-toaster-bottom-right",
                                                                      }) 
                                         
                                          }else{
                                              this.$bvToast.toast("Inregistrare aflata in lucru la "+response+" !", {
                                                                        title: "Inregistrare blocata! ",
                                                                        variant:"warning",
                                                                        solid: false,
                                                                        appendToast: true,
                                                                        autoHideDelay: 5000,
                                                                        toaster: "b-toaster-top-right",
                                                                      }) 
                                          }    
                                        }
                               })
                              .catch(error => {
                            this.showLoading=false
                            this.$bvToast.toast(error.data.message, 
                            {
                                title: `Eroare! `,
                                variant:"danger",
                                solid: true,
                                appendToast: false,
                                noAutoHide:true,
                                toaster: "b-toaster-top-right",
                                }) 

                            })
                  }else{
                    this.$emit("edit")
                   }
                }
            }
    },
      deblocheazaInregistrare(){
                if(this.selectedID){
                this.showLoading=true
                const payLoad=Object.assign({},this.selectedID)
                payLoad.requestType="post"
                payLoad.requestUrl="/"+this.modelName+"/deblocheaza"
                this.$store.dispatch("app/api_Request",payLoad)
                              .then(response=>{
                                        this.showLoading=false
                                        if(this.$store.state.app.ultimacautare){
                                              if(JSON.parse(this.$store.state.app.ultimacautare).modelName==this.modelName){
                                                this.cautareDupa=JSON.parse(this.$store.state.app.ultimacautare).cautareDupa
                                                this.searchedVal=JSON.parse(this.$store.state.app.ultimacautare).searchedVal 
                                              
                                              }else{
                                              this.cautareDupa=""
                                              this.searchedVal=""
                                              
                                            }
                                              
                                            }else{
                                              this.cautareDupa=""
                                              this.searchedVal=""
                                              
                                            }
                                            this.getRecords(this.currentPageTblRecords,this.pageLength,this.sortModel,this.searchedVal,this.cautareDupa,this.modelFilter)

                                        this.$bvToast.toast("Deblocare efectuata cu succes!", {
                                                                        title: "Deblocare efectuata cu succes!",
                                                                        variant:"success",
                                                                        solid: false,
                                                                        appendToast: true,
                                                                        autoHideDelay: 3000,
                                                                        toaster: "b-toaster-bottom-right",
                                                                      }) 
                                     })   
                }else{
                    this.$bvToast.toast("Selectati o inregistrare!", {
                                                                        title: "Selectati o inregistrare!",
                                                                        variant:"danger",
                                                                        solid: false,
                                                                        appendToast: true,
                                                                        autoHideDelay: 3000,
                                                                        toaster: "b-toaster-bottom-right",
                                                                      }) 
                }              
      },
     view(){
        if (this.selectedID==null)
            {
               this.$bvToast.toast('Selectați înregistrarea de vizualizat!', {
                                                                        title: `Atentie! `,
                                                                        variant:'warning',
                                                                        solid: false,
                                                                        appendToast: true,
                                                                        autoHideDelay: 3000,
                                                                        toaster: 'b-toaster-bottom-right',
                                                                      })  
              
            }else
            {
            
          // this.editVar=Object.assign({},this.tblRecords.data.find(t => t.id == id))
         // this.editVar=Object.assign({},this.selectedID)
          //this.activeAction="Vizualizează"
          //this.activeEdit=true
          this.$emit("view")
          }
    },
    openConfirm(){
      
        if (this.selectedID==null||this.selectedID=="")
          {
           this.$bvToast.toast('Selectați înregistrarea de sters!', {
                                                                        title: `Atentie! `,
                                                                        variant:'warning',
                                                                        solid: false,
                                                                        appendToast: true,
                                                                        autoHideDelay: 3000,
                                                                        toaster: 'b-toaster-bottom-right',
                                                                      })  
            
          }else
          {
            if (this.selectedID.tip_nota=="GESTIUNE"||this.selectedID.tip_nota=="INCHIDERE TVA"||this.selectedID.tip_nota=="INCHIDERE 121")
          {
             this.$bvToast.toast("Notele contabile generate nu pot fi modificate decât prin modificarea documentului sursă!"
                                 , {
                                                                        title: "Nota contabila generată!",
                                                                        variant:'warning',
                                                                        solid: false,
                                                                        appendToast: true,
                                                                        autoHideDelay: 3000,
                                                                        toaster: 'b-toaster-bottom-right',
                                                                      })  
            
            return false
            
          }else{
                              if(this.selectedID.antetincasaricontracte_id){
                                   this.$bvModal
                                            .msgBoxConfirm('Stergeti '+this.selectedID.tip_doc+' nr '+this.selectedID.nr_doc+' din '+(new Date(this.selectedID.data_doc).toLocaleDateString())+' '+this.selectedID.partener+'?.', {
                                              title: 'Stergeti?',
                                              size: 'sm',
                                              okVariant: 'danger',
                                              okTitle: 'Sterge',
                                              cancelTitle: 'Cancel',
                                              cancelVariant: 'outline-secondary',
                                              hideHeaderClose: false,
                                              centered: true,
                                            })
                                            .then(value => {
                                              if(value){
                                                this.sterge()
                                              }
                                              

                                            })
                                }else{
                                     this.$bvModal
                                            .msgBoxConfirm('Stergeti inregistrarea curenta?.', {
                                              title: 'Stergeti?',
                                              size: 'sm',
                                              okVariant: 'danger',
                                              okTitle: 'Sterge',
                                              cancelTitle: 'Cancel',
                                              cancelVariant: 'outline-secondary',
                                              hideHeaderClose: false,
                                              centered: true,
                                            })
                                            .then(value => {
                                              if(value){
                                                this.sterge()
                                              }
                                              

                                            })     
                                }
                             
                             }
           }                
    },
   sterge() {
          if (this.selectedID==null)
          {
            this.$bvToast.toast('Selectați înregistrarea de sters!', {
                                                                        title: `Atentie! `,
                                                                        variant:'warning',
                                                                        solid: false,
                                                                        appendToast: true,
                                                                        autoHideDelay: 3000,
                                                                        toaster: 'b-toaster-bottom-right',
                                                                      })  
          
            
          }else
          {

          if(this.dataToCheck){
          if(this.$globalHelpers.perioadablocata(this.selectedID[this.dataToCheck.dt],this.dataToCheck.tip)){
            this.$bvToast.toast('Perioada este blocata!', {
                                                                        title: `Atentie! `,
                                                                        variant:'danger',
                                                                        solid: false,
                                                                        appendToast: true,
                                                                        autoHideDelay: 3000,
                                                                        toaster: 'b-toaster-bottom-right',
                                                                      })  
            return false
          }
          }
          if(this.selectedID.status){
          let status=this.selectedID.status.toUpperCase()
          if(status=="VALID"||status=="SEMNAT"||status=="ANULAT"||status=="INCHIS"){
            this.$bvToast.toast("Stergerea nu este permisa! Documentul are statusul "+this.selectedID.status,
                                 {
                                  title: `Atentie! `,
                                  variant:'warning',
                                  solid: false,
                                  appendToast: true,
                                  autoHideDelay: 3000,
                                  toaster: 'b-toaster-bottom-right',
                                  })  
            
          return false
          }  
        }
          let id=this.selectedID.id
          this.showLoading=true
          const payLoad={} 
          payLoad.requestType="post"
          payLoad.requestUrl="/"+this.modelName+"/delete/" +id
          
          this.rows=this.rows.filter((e)=>e.id != id )
          this.$store.dispatch("app/api_Request",payLoad)
          .then(response=>{
                           this.selectedID=""
                           if(this.cookie){
                             this.$globalHelpers.updateCookiesLocal(this.cookie)  
                           }
                           this.getRecords(this.currentPageTblRecords,this.pageLength,this.sortModel,this.searchedVal,this.cautareDupa,this.modelFilter)
                           this.showLoading=false
                         
                           this.$bvToast.toast("Ștergere efectuată cu succes!",
                                 {
                                  title: `Info `,
                                  variant:'success',
                                  solid: false,
                                  appendToast: true,
                                  autoHideDelay: 3000,
                                  toaster: 'b-toaster-bottom-right',
                                  })  
                          
                           
          
          })
          .catch(error => {
           //this.handleErrors(error)
          })
         }
    },
  },


  created() {
      
     if(this.$store.state.app.ultimacautare){
        if(JSON.parse(this.$store.state.app.ultimacautare).modelName==this.modelName){
          this.cautareDupa=JSON.parse(this.$store.state.app.ultimacautare).cautareDupa
          this.searchedVal=JSON.parse(this.$store.state.app.ultimacautare).searchedVal 
          this.searchTerm=this.searchedVal
        }else{
        this.cautareDupa=""
        this.searchedVal=""
        
      }
    }
     
      this.getRecords( this.currentPageTblRecords,this.pageLength,this.sortModel,this.searchedVal,this.cautareDupa,this.modelFilter)
      
      this.targetFilter=this.$refs['filter'+this.columns[0].field]
     


  },
}
</script>
<style lang="scss" >
@import '~@core/scss/vue/libs/vue-good-table.scss';
.green {
  background-color: green;
}
.success{
 background-color: aquamarine; 
}
.administrare{
 background-color:paleturquoise;
 
}
.executare{
 background-color: peachpuff; 
}
.warning{
 background-color: blanchedalmond; 
}
.info{
 background-color:#AFEEEE;// #E6E6FA; 
}
.row-selected{
  text-color:red
}
.fontsizeMarit{
  font-size:14px;
}
.fontsize{
  font-size:12px;
}
.vgt-table .th{
 font-size:12px; 
}
</style>