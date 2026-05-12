<template>  
       <div>
   <b-input-group :state="stateLocal" >
      <b-form-input
        :disabled="readonly"   
        :id="name"
        :name="name"
        size="sm"
        v-model="formatted"
        @input="handleInput" 
        @focus="handleFocus"
        type="text"
         :state="stateLocal"
        
        autocomplete="off"
        show-decade-nav
      />
      
      <b-input-group-append :state="stateLocal">
   
        <b-form-datepicker
          v-show="!readonly"
          v-model="content"      
          show-decade-nav
          button-only
          right
          :state="stateLocal"
          :min="startDateLocal"
          :max="endDateLocal"
          :date-disabled-fn="dateDisabled"
          size="sm"
          @focus="handleFocus"
          locale="ro-RO"
          :date-format-options="{ day: '2-digit',month: '2-digit',year: 'numeric'  }"
          :aria-controls="name"
          @context="onContext"
          @input="handleInput" 
          
        />

      </b-input-group-append>
    </b-input-group>
    
    <div class="d-flex justify-content-end">
    <span class="labelSelect" >{{campDisplay}}</span>
    </div>
  </div>
</template>

<script>

import {BFormDatepicker, BInputGroup, BInputGroupAppend, BFormInput} from 'bootstrap-vue'

export default {
  components: {
   
    BFormDatepicker,
    BInputGroup,
    BInputGroupAppend,
    BFormInput,
  },

 props: {
        value:[String,Date],
        name:String,
        campDisplay:String,
        readonly:Boolean,
        disableWeekEnd:Boolean,
        startDate:Date,
        endDate:Date,
        state:{type:Boolean,
               default:null
               } 
       
        },

  name:"datacalendaristica",
 
  data() {

    return {
      
      formatted: '',
      selected: '',
      content:this.value,
      disableWeekEndLocal:this.disableWeekEnd,
      stateLocal:null,
      startDateLocal:null,
      endDateLocal:null

    }
  },
  watch:{

     value(){
         this.content = this.value
       },
       disableWeekEnd(){
          this.disableWeekEndLocal=this.disableWeekEnd
        },
        state(){
          this.stateLocal=this.state
          
        },
         startDate(){
          this.startDateLocal=this.startDate
        },
        endDate(){
          this.endDateLocal=this.endDate
        },
    },
  methods:{
     dateDisabled(ymd, date) {
      
      if(this.disableWeekEndLocal){
     
           // Disable weekends (Sunday = `0`, Saturday = `6`) and
      // disable days that fall on the 13th of the month
      const weekday = date.getDay()
      const day = date.getDate()
      // Return `true` if the date should be disabled
      return weekday === 0 || weekday === 6    //|| day === 13
    }
    },
   onContext(ctx) {
      // The date formatted in the locale, or the `label-no - date - selected` string
      // this.content = ctx.selectedFormatted
      this.formatted = ctx.selectedFormatted
      if(this.formatted=='No date selected'){
        this.formatted=null
      }
      // this.content=this.formatted
      // The following will be an empty string until a valid date is entered
      this.selected = ctx.selectedYMD
    },
    handleInput (e) {
      this.content=e
      //console.log(e)
      //// console.log("INLCUDES "+this.content.includes("."))
      if (this.content.includes(".")){
        let dataArr=this.content.split(".")

        if (dataArr.length==3 && dataArr[2].length==4){
          if(dataArr[0].length==1){
            dataArr[0]="0"+dataArr[0]
          }
          if(dataArr[1].length==1){
            dataArr[1]="0"+dataArr[1]
          }
          this.content=dataArr[2]+"-"+dataArr[1]+"-"+dataArr[0]
       // console.log("FINAL "+this.content)    
        }
      }  

      if (this.content.includes("-")){
        let dataArr=this.content.split("-")

        if (dataArr.length==3 && dataArr[0].length==4){
          if(dataArr[2].length==1){
            dataArr[2]="0"+dataArr[0]
          }
          if(dataArr[1].length==1){
            dataArr[1]="0"+dataArr[1]
          }
          this.content=dataArr[0]+"-"+dataArr[1]+"-"+dataArr[2]
       // console.log("FINAL "+this.content)    
        
        }
      }  
        this.$emit('input', this.content)
        this.$emit('change', this.content)
      //// console.log(this.content)
      
    },
   handleFocus (e) {
      
      this.$emit('focus',e)
      
    },

  },
  created(){
   

      this.stateLocal=this.state
          
   
  }
 
}
  
</script>
<style lang="scss">
.labelSelect {
  font-size:.9rem;
  color:rgba(115, 103, 240) !important;
}

