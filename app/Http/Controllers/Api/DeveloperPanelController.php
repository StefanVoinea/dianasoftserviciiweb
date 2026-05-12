<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Models\DianaSoftModel;
use App\Models\DianaSoftField;
use App\Models\DianaSoftRelationship;
use App\Models\Permission;
use App\Models\User;


class DeveloperPanelController extends Controller
{
    //EDIT MODEL
   public function editModel(Request $request)
  {
        
    $appPath= env('DEVELOPER_PANEL_APP_PATH');
    
    $modelName=$request->model_name;
    $modelDisplayName=$request->display_name;
    $tableName=$request->table_name;
    $dianasoftmodel=DianaSoftModel::where("model_name",$request->model_name)->get()->first();  
    $fieldArray=$request->dianasoftfields;
  
 


//vue cu aggrid vers 2
$vuestr='<template>

    <b-overlay
      :show="showLoading"
      rounded="sm"
      no-fade
      variant="primary"
      opacity="0.25"
      blur="2px"
    >
      <b-row class="d-flex align-items-center justify-content-center">
      <b-col cols="12" >
      <b-card>
    
        <tabelcomponent 
                      :cols="12"
                      :columnDefs="columnDefs"
                      :modelName="modelName"
                      :refresh="refreshLocal"
                      :titlu="modelDisplayName"
                      :idselectat="idselectat"
                      :campFiltruStart="campFiltruStart"
                      @onSelectionChanged="onSelectionChanged"
                      @adauga="add"
                      @edit="edit"
                      @view="view">
                
        </tabelcomponent>
      </b-card>
 </b-col>
   </b-row>
      <'.$modelName.'aev
                         @stored="afisezSalvat"
                         @closed="aevClosed"
                         :activeAction="activeAction"
                         :activeEdit="activeEdit"
                         :editVar="editVar"
                         v-show="activeEdit"
                      > 
      </'.$modelName.'aev>  
    
   </b-overlay>
</template>

<script>

import '.$modelName.'aev from "./'.$modelName.'aev.vue"

export default {
  props: {
        id:String,
        }, 
  components: {
    '.$modelName.'aev
  },
  name:"'.strtolower($modelName).'",
  data() {
    return {
        refreshLocal:false, 
        idselectat:null,
        campFiltruStart:"",
        modelName:"'.strtolower($modelName).'",
        modelDisplayName:"'.$modelDisplayName.'",
        editVar:{';
   if($fieldArray)
    {
            
    foreach ($fieldArray as $camp) {
       
        $vuestr = $vuestr.'
          '.strtolower($camp["name"]).':"",';
        
    }
}
$vuestr = $vuestr.'},
        activeEdit:false,
        activeAction:"",
        selectedID:"",
        showLoading:false,
        columnDefs: [
                       // { headerName: "Document...",
                      //        children: [
                      // columnGroupShow:"open",
                          // filter: "agNumberColumnFilter",
                          // valueFormatter: function(params) { return new Date(params.value).toLocaleDateString() },
                          // cellRenderer: function(params) {
                          //            if(params.value!=null){

                          //               return "<a href=\'/contract?id="+params.value.id +"\' target=\'_blank\'>"+ params.value.nr_contract+\'/\'+ new Date(params.value.data_contract).toLocaleDateString()+\' \'+params.value.nume+\'</a>\'  
                          //            }
                                    
                          //       },
                  ';

            if($fieldArray)
            {
                    
            foreach ($fieldArray as $camp) {
               
                $vuestr = $vuestr.'
                {
                  label: "'.$camp["display_name"].'",
                  field: "'.strtolower($camp["name"]).'",
                  width: "300px",';
                  if($camp["type"]=="date"||$camp["type"]=="dateTime"){
                  $vuestr = $vuestr.'
                  type:"date",
                  //dateInputFormat: "yyyy-MM-dd\'T\'HH:mm:ss.SSSSSS\'Z\'", // expects 2018-03-16
                  //dateOutputFormat: "dd.MM.yyyy HH:mm:ss", // outputs Mar 16th 2018
                  dateInputFormat: "yyyy-MM-dd", // expects 2018-03-16
                  dateOutputFormat: "dd.MM.yyyy", // outputs Mar 16th 2018 ';  
                  }else{
                          if($camp["type"]=="double"||$camp["type"]=="integer"){
                          $vuestr = $vuestr.'
                  type:"number",';  
                          }else{
                             $vuestr = $vuestr.'
                  type:"text",';  
                          } 
                     }
                  $vuestr = $vuestr.'
                 showSortAsc:true,
                },
               
                ';
                
                }
            }

            $vuestr=$vuestr.'  
                    
        ],
     
    }
  },
 
  methods: {
    
    aevClosed(){
      this.idselectat=null
      this.selectedID=""
      this.activeEdit=false
      this.editVar={';
   if($fieldArray)
    {
            
    foreach ($fieldArray as $camp) {
       
        $vuestr = $vuestr.'
          '.strtolower($camp["name"]).':"",';
        
    }
}
$vuestr = $vuestr.'},
      this.activeAction=""
    },
    afisezSalvat(value){
      //this.idselectat=value.id
      //this.campFiltruStart="id"
        this.refreshLocal=!this.refreshLocal
    },
    listen(){
              //  Echo.channel("cerber_databasechannel")
              //      .listen("."+this.modelName+".updated", (e) => {
              //         this.getRecords()
               //      });
    },
    onSelectionChanged(value){
      this.selectedID=value
    },
    
    add() {
        this.activeAction="Adaugă"
        this.editVar={  
                     ';

            if($fieldArray)
            {
                    
            foreach ($fieldArray as $camp) {
               
                $vuestr = $vuestr.$camp["name"].':"",
                ';
                
                }
            }

            $vuestr=$vuestr.'

        }
        this.activeEdit=true
    },
    
    edit() {
          this.idselectat=null
          this.editVar=Object.assign({},this.selectedID)
          this.activeAction="Modifică"
          this.activeEdit=true
         
    },
    view() {
        
          
          this.editVar=Object.assign({},this.selectedID)
          this.activeAction="Vizualizează"
          this.activeEdit=true
        
    },
    
    },
    created() {

      document.title=window.app_name+"->"+this.modelDisplayName
     // if(this.id!=null){
     //             this.idselectat=this.id
     //             this.campFiltruStart="id"
     // }
      this.listen()
     
    },
  
}

</script>';
  $filename="\\resources\\js\\src\\views\\app_pages\\".$modelName.".vue";
  
  File::put($appPath.$filename,$vuestr);
  // VUE COMPONENT

  //AEV VUE COMPONENT
  $aevvuestr='<template>
  <validation-observer ref="simpleRules">
  <div class= "d-flex justify-content-center" >
   <b-modal id="Dianasoftmodelaev"  
             size="xl" 
             no-close-on-backdrop
             :hide-footer="activeActionLocal==\'Vizualizează\'"
             ok-variant="success"
             cancel-title="Cancel"              
             ok-title="Save"
             cancel-variant="warning"
             scrollable
             :cancel-disabled="activeActionLocal==\'Vizualizează\'"
             :ok-disabled="activeActionLocal==\'Vizualizează\'"
             modal-class="modal-success"
             :title="activeActionLocal+\' '.$modelDisplayName.'\'"
             v-model="activeEditLocal"
             @ok="handleOk"
             @cancel="aevClosed"
             >
     <form  ref="form"  @submit.stop.prevent="handleSubmit" >
             <br>

            
                <b-row class="d-flex justify-content-center" >
                     ';

                        if($fieldArray)
                        {
                                
                        foreach ($fieldArray as $camp) {
                           
                            $aevvuestr = $aevvuestr.'
                            <b-col  cols="2">';
                             if($camp["input_type"]=="produsedefinantare"){
                              $aevvuestr = $aevvuestr.'
                               <validation-provider
                                    #default="{ errors }"
                                     name="'.$camp["display_name"].'"
                                    rules=""
                                  >
                                      <produsedefinantare 
                                                  :readonly="activeActionLocal==\'Vizualizează\'"
                                                  v-model="editVarLocal.'.$camp["name"].'">

                                  </produsedefinantare>
                                   <small class="text-danger">{{ errors[0] }}</small>
                                </validation-provider>';
                            }else{
                             if($camp["input_type"]=="cnpcomponent"){
                              $aevvuestr = $aevvuestr.'
                               <validation-provider
                                    #default="{ errors }"
                                     name="'.$camp["display_name"].'"
                                    rules=""
                                  >  
                           <cnp-component 
                                          class="w-full"
                                         :readonly="activeActionLocal==\'Vizualizează\'"
                                         name="'.$camp["display_name"].'"
                                         placeholder=".'.$camp["display_name"].'" 
                                         :activeEdit="activeEditLocal"
                                         @ciScanat="preiaDateCIScanat"
                                         @CUIIntrodus="CUIIntrodus"
                                         v-model="editVarLocal.'.$camp["name"].'"
                                         >
                          </cnp-component>
                           <small class="text-danger">{{ errors[0] }}</small>
                                  
                                </validation-provider>
                                  ';
                            }else{
                            if($camp["input_type"]=="textarea"){
                              $aevvuestr = $aevvuestr.'
                                     <div class="form-label-group">
                                      <validation-provider
                                    #default="{ errors }"
                                     name="'.$camp["display_name"].'"
                                    rules=""
                                  >
                                      <b-form-textarea
                                        id=".'.$camp["name"].'"
                                         v-model="editVarLocal.'.$camp["name"].'"
                                        rows="3"
                                        placeholder=".'.$camp["display_name"].'"
                                      />
                                       <small class="text-danger">{{ errors[0] }}</small>
                                </validation-provider>
                                      <label for="label-.'.$camp["name"].'">.'.$camp["display_name"].'</label>
                                    </div>';
                            }else{
                            if($camp["input_type"]=="localitatecomponent"){
                              $aevvuestr = $aevvuestr.'
                               <validation-provider
                                    #default="{ errors }"
                                     name="'.$camp["display_name"].'"
                                    rules=""
                                  >
                                      <localitatecomponent 
                                                  :readonly="activeActionLocal==\'Vizualizează\'"
                                                  :judet="editVarLocal.judet"
                                                  v-model="editVarLocal.'.$camp["name"].'">

                                  </localitatecomponent>
                                   <small class="text-danger">{{ errors[0] }}</small>
                                </validation-provider>';
                            }else{
                               if($camp["input_type"]=="gestiunepermisa"){
                              $aevvuestr = $aevvuestr.'
                              <validation-provider
                                           #default="{ errors }"
                                          name="'.$camp["display_name"].'"
                                          rules=""
                                        >
                                      <gestiunepermisa 
                                                    name="'.$camp["name"].'"
                                                   :pastrezvaloare="true"  
                                                   class="w-full"   
                                                  :readonly="activeActionLocal==\'Vizualizează\'"
                                                  v-model="editVarLocal.'.$camp["name"].'">

                                  </gestiunepermisa>
                                   <small class="text-danger">{{ errors[0] }}</small>
                                   </validation-provider>
                                       ';
                            }else{
                            if($camp["input_type"]=="taricomponent"){
                              $aevvuestr = $aevvuestr.'
                               <validation-provider
                                    #default="{ errors }"
                                     name="'.$camp["display_name"].'"
                                    rules=""
                                  >
                                     <taricomponent 
                                                  :readonly="activeActionLocal==\'Vizualizează\'"
                                                  v-model="editVarLocal.'.$camp["name"].'">

                                  </taricomponent>
                                   <small class="text-danger">{{ errors[0] }}</small>
                                </validation-provider>';
                            }else{
                             if($camp["input_type"]=="judetcomponent"){
                              $aevvuestr = $aevvuestr.'
                               <validation-provider
                                    #default="{ errors }"
                                     name="'.$camp["display_name"].'"
                                    rules=""
                                  >
                                     <judetcomponent 
                                                  :readonly="activeActionLocal==\'Vizualizează\'"
                                                  v-model="editVarLocal.'.$camp["name"].'">

                                  </judetcomponent>
                                   <small class="text-danger">{{ errors[0] }}</small>
                                </validation-provider>';
                            }else{
                            if($camp["input_type"]=="contcontabil"){
                              $aevvuestr = $aevvuestr.'
                               <validation-provider
                                    #default="{ errors }"
                                     name="'.$camp["display_name"].'"
                                    rules=""
                                  >
                                     <contcontabil  
                                         name="'.$camp["name"].'"
                                          :readonly="activeActionLocal==\'Vizualizează\'"
                                           v-model="editVarLocal.'.$camp["name"].'"
                                          label="'.$camp["display_name"].'">
                                                        
                                </contcontabil>
                                 <small class="text-danger">{{ errors[0] }}</small>
                                </validation-provider>';
                            }else{
                            if($camp["input_type"]=="selectoneuser"){
                              $aevvuestr = $aevvuestr.'
                              <validation-provider
                                    #default="{ errors }"
                                     name="'.$camp["display_name"].'"
                                    rules=""
                                  >
                                     <selectoneuser  labelDisplay="'.$camp["display_name"].'"
                                                       v-model="editVarLocal.'.$camp["name"].'"
                                                      :readonly="activeActionLocal==\'Vizualizează\'"
                                                    >
                                     </selectoneuser> 
                                      <small class="text-danger">{{ errors[0] }}</small>
                                </validation-provider>';
                            }else{
                            if($camp["input_type"]=="dropdowncuoptiuni"){
                              $aevvuestr = $aevvuestr.'
                                  <validation-provider
                                    #default="{ errors }"
                                     name="'.$camp["display_name"].'"
                                    rules=""
                                  >   
                              <dropdowncuoptiuni 
                                name="'.$camp["name"].'" 
                                :readonly="activeAction==\'Vizualizează\'" 
                                 v-model="editVarLocal.'.$camp["name"].'" 
                                campDisplay="'.$camp["display_name"].'"
                                field_name="'.$camp["display_name"].'"
                                limitToList="true"
                                > 
                          </dropdowncuoptiuni>
                           <small class="text-danger">{{ errors[0] }}</small>
                                </validation-provider>';
                            }else{
                              if($camp["type"]=="dateTime"){
                              $aevvuestr = $aevvuestr.'
                              <validation-provider
                                    #default="{ errors }"
                                     name="'.$camp["display_name"].'"
                                    rules=""
                                  >  
                                     <datasiora 
                                          :readonly="activeActionLocal==\'Vizualizează\'"
                                          id="'.$camp["name"].'" 
                                          v-model="editVarLocal.'.$camp["name"].'"
                                          name="'.$camp["name"].'" 
                                          campDisplay="'.$camp["display_name"].'"> 
                                      </datasiora>
                                       <small class="text-danger">{{ errors[0] }}</small>
                                </validation-provider>';
                            }else{
                            if($camp["type"]=="date"){
                              $aevvuestr = $aevvuestr.'
                              <validation-provider
                                    #default="{ errors }"
                                     name="'.$camp["display_name"].'"
                                    rules=""
                                  >  
                                     <datacalendaristica 
                                          :readonly="activeActionLocal==\'Vizualizează\'"
                                          id="'.$camp["name"].'" 
                                          v-model="editVarLocal.'.$camp["name"].'"
                                          name="'.$camp["name"].'" 
                                          campDisplay="'.$camp["display_name"].'"> 
                                      </datacalendaristica>
                                       <small class="text-danger">{{ errors[0] }}</small>
                                </validation-provider>';
                            }else{
                                if($camp["input_type"]=="checkbox"){
                              $aevvuestr = $aevvuestr.'
                                    <b-form-checkbox
                                          v-model="editVarLocal.'.$camp["name"].'"
                                          class="custom-control-primary">
                                          '.$camp["display_name"].'
                                        </b-form-checkbox>';
                            }else{
                                $aevvuestr = $aevvuestr.'
                                 <validation-provider
                                    #default="{ errors }"
                                     name="'.$camp["display_name"].'"
                                    rules=""
                                  >  
                                  <div class="form-label-group">
                                  <b-form-input
                                    :readonly="activeActionLocal==\'Vizualizează\'"
                                    size="sm"
                                    autocomplete="off"
                                    id="'.$camp["name"].'"'; 
                                    if($camp["input_type"]=="double"||$camp["input_type"]=="integer"){
                                          $aevvuestr = $aevvuestr.'
                                    type="number"';
                                  }
                                  $aevvuestr = $aevvuestr.'
                                    v-model="editVarLocal.'.$camp["name"].'"
                                    placeholder="'.$camp["display_name"].'" 
                                    
                                  />
                                  <label for="'.$camp["name"].'">'.$camp["display_name"].'</label>
                                   <small class="text-danger">{{ errors[0] }}</small>
                                  </div>
                                </validation-provider>
                              
                                ';
                            }}}}}}}}}}}}}
                            
                           $aevvuestr = $aevvuestr.'     
                            </b-col>
                            ';
                            
                            }
                        }

                        $aevvuestr=$aevvuestr.'
                    
                </b-row>
                
              <br><br><br><br><br><br><br><br><br><br><br>
      </form>
    </b-modal>
  </div>
   </validation-observer>
</template>

<script>
import Ripple from "vue-ripple-directive"
import { heightTransition } from "@core/mixins/ui/transition"
import {VBModal} from "bootstrap-vue"
import {  required, email, confirmed, password,min} from "@validations"
import { ValidationProvider, ValidationObserver} from "vee-validate"
export default {
  props: {
        activeEdit:Boolean,
        activeAction:String,
        editVar:Object,
        rutainapoi:String,
        },
  mixins: [heightTransition],
  components: {
     ValidationProvider, ValidationObserver 
  },
   directives: {
    "b-modal": VBModal,
    Ripple,
  },
  name:"'.strtolower($modelName).'aev",
  data() {
    return {
       required,
        password,
        email,
        confirmed,
         min,
        rutainapoiLocal:this.rutainapoi,
        activeEditLocal:false,
        activeActionLocal:this.activeAction,
        editVarLocal:this.editVar,
        nextTodoId: 2,
        modelName:"'.strtolower($modelName).'",
        showLoading:false,
       
      }
  },
  watch: {
      activeEdit(){
         
         this.activeEditLocal=this.activeEdit
       },
       activeEditLocal(){
            if (this.activeEditLocal==false){
              this.$emit(\'closed\')
            }
            
       },
      activeAction(){
        this.activeActionLocal=this.activeAction
      },
      editVar(){
        this.editVarLocal=this.editVar
      }
  },
  
  methods: {
    
    initTrHeight() {
      this.trSetHeight(null)
      this.$nextTick(() => {
        if(this.$refs.form){
        this.trSetHeight(this.$refs.form.scrollHeight)
        }
      })
    },
   
    
     handleOk(bvModalEvt){
       bvModalEvt.preventDefault()
        this.$refs.simpleRules.validate().then(success => {
        if (success) {
       if (this.activeActionLocal=="Adaugă") 
        {
          this.saveAdd()
        }
        if (this.activeActionLocal=="Modifică") 
        {
          this.saveEdit()
        }
         }
      })
    },
    saveAdd(){
        
          this.showLoading=true
          const payLoad=this.editVarLocal
          payLoad.requestType="post"
          payLoad.requestUrl="/"+this.modelName+"/store"
          this.$store.dispatch("app/api_Request",payLoad)
                    .then(response=>{
                            
                            this.editVarLocal={  
                                                     ';

                                            if($fieldArray)
                                            {
                                                    
                                            foreach ($fieldArray as $camp) {
                                               
                                                $aevvuestr = $aevvuestr.$camp["name"].':\'\',
                                                ';
                                                
                                                }
                                            }

                                            $aevvuestr=$aevvuestr.'

                                        }
                             this.$bvToast.toast("Salvare efectuata cu success!", 
                                                 {
                                                    title: `Salvare cu succes! `,
                                                    variant:"success",
                                                    solid: false,
                                                    appendToast: true,
                                                    autoHideDelay: 3000,
                                                    toaster: "b-toaster-bottom-right",
                                                                      }) 
                            this.showLoading=false
                            this.activeEditLocal=false
                            this.activeActionLocal=""
                           this.$emit("stored","")              
                            this.$emit("closed")
          
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
         
    },
   
    aevClosed(){
        this.idselectat=null
        this.selectedID=""
        this.activeEditLocal=false
        this.editVarLocal={  
                                                     ';

                                            if($fieldArray)
                                            {
                                                    
                                            foreach ($fieldArray as $camp) {
                                               
                                                $aevvuestr = $aevvuestr.$camp["name"].':"",
                                                ';
                                                
                                                }
                                            }

                                            $aevvuestr=$aevvuestr.'

                                        }
        this.activeActionLocal=""
        this.$emit("closed")
        
    },
    
    saveEdit(){
               
                  this.showLoading=true
                  const payLoad=this.editVarLocal 
                  payLoad.requestType="post"
                  payLoad.requestUrl="/"+this.modelName+"/edit/"+this.editVarLocal.id
                  
                  this.$store.dispatch("app/api_Request",payLoad)
                              .then(response=>{
                                               this.selectedID=""
                                               
                                               this.editVarLocal={  
                                                     ';

                                            if($fieldArray)
                                            {
                                                    
                                            foreach ($fieldArray as $camp) {
                                               
                                                $aevvuestr = $aevvuestr.$camp["name"].':"",
                                                ';
                                                
                                                }
                                            }

                                            $aevvuestr=$aevvuestr.'

                                        }
                                         this.$bvToast.toast("Modificare efectuata cu success!", {
                                                                        title: "Modificare cu succes! ",
                                                                        variant:"success",
                                                                        solid: false,
                                                                        appendToast: true,
                                                                        autoHideDelay: 3000,
                                                                        toaster: "b-toaster-bottom-right",
                                                                      }) 
                                               this.showLoading=false
                                               this.activeEditLocal=false
                                               this.activeActionLocal=""
                                               this.$emit("stored","") 
                                               this.activeEditLocal=false
                                               this.activeActionLocal=""
                                               this.$emit("closed")
          
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
               
    },
     initTrHeight() {
      this.trSetHeight(null)
      this.$nextTick(() => {
        if(this.$refs.form){
        this.trSetHeight(this.$refs.form.scrollHeight)
        }
      })
    },
  },
  mounted() {
    this.initTrHeight()
  },
  destroyed() {
    window.removeEventListener("resize", this.initTrHeight)
  },
  created() {
         
         if(!this.rutainapoi){
          this.rutainapoiLocal=this.modelName
         }
          window.addEventListener("resize", this.initTrHeight)
          
     
    },
}

</script>

';
   $filename="\\resources\\js\\src\\views\\app_pages\\".$modelName."aev.vue";
  File::put($appPath.$filename,$aevvuestr);
  //AEV VUE COMPONENT

  


  return response()->json('Model was created successfully',200);
  }


    //ADD MODEL

  public function addModel(Request $request)
  {
  		
  	$appPath= env('DEVELOPER_PANEL_APP_PATH');
  	
  	$modelName=$request->model_name;
  	$modelDisplayName=$request->display_name;
  	$tableName=$request->table_name;
    $dianasoftmodel=DianaSoftModel::create([
    	  "model_name"=>$request->model_name,
    	  "table_name"=>$request->table_name,
    	  "display_name"=>$request->display_name,
    	 
    	  ]);  

     $permission=Permission::create([
          'name'=>"view".$request->model_name,
          "display_name"=>"Vizualizeaza ".$request->display_name,  
        ]);

         $currentuser=$request->user();
         $permission->users()->attach($currentuser->id,['isactive'=>true,
                                                        'company_id'=>1  ]);
         $allUsers=User::get();
         foreach ($allUsers as $user) {
            if($user->id != $request->user()->id )
            {
                $permission->users()->attach($user->id,['isactive'=>false,
                                                        'company_id'=>1]);    
            }
         }
         //  Vue ACL view model
      /*   $aclstr='import { AclInstaller, AclCreate, AclRule } from "vue-acl"
                  export  const view'.$modelName.'= {
                        view'.$modelName.': new AclRule("view'.$modelName.'").or("owner").generate(),
                  }';
         $filename="\\resources\\js\\src\acl\details\\view".$modelName.".js";
         File::put($appPath.$filename,$aclstr);*/
         //
         $permission=Permission::create([
          'name'=>"add".$request->model_name,
          "display_name"=>"Adauga ".$request->display_name,  
        ]);

      
         $permission->users()->attach($currentuser->id,['isactive'=>true,
                                                        'company_id'=>1]);
         
         foreach ($allUsers as $user) {
            if($user->id != $request->user()->id )
            {
                $permission->users()->attach($user->id,['isactive'=>false,
                                                        'company_id'=>1]);    
            }
         }
        //  Vue ACL add model
      /*   $aclstr='import { AclInstaller, AclCreate, AclRule } from "vue-acl"
                  export  const add'.$modelName.'= {
                        add'.$modelName.': new AclRule("add'.$modelName.'").or("owner").generate(),
                  }';
         $filename="\\resources\\js\\src\acl\details\\add".$modelName.".js";
         File::put($appPath.$filename,$aclstr);*/
         //
         $permission=Permission::create([
          'name'=>"edit".$request->model_name,
          "display_name"=>"Modifica ".$request->display_name,  
        ]);

      
         $permission->users()->attach($currentuser->id,['isactive'=>true,
                                                        'company_id'=>1]);
        
         foreach ($allUsers as $user) {
            if($user->id != $request->user()->id )
            {
                $permission->users()->attach($user->id,['isactive'=>false,
                                                        'company_id'=>1]);    
            }
         }
         //  Vue ACL edit model
       /*  $aclstr='import { AclInstaller, AclCreate, AclRule } from "vue-acl"
                  export  const edit'.$modelName.'= {
                        edit'.$modelName.': new AclRule("edit'.$modelName.'").or("owner").generate(),
                  }';
         $filename="\\resources\\js\\src\acl\details\\edit".$modelName.".js";
         File::put($appPath.$filename,$aclstr);*/
         //
         $permission=Permission::create([
          'name'=>"delete".$request->model_name,
          "display_name"=>"Sterge ".$request->display_name,  
        ]);

     
         $permission->users()->attach($currentuser->id,['isactive'=>true,
                                                        'company_id'=>1]);
         
         foreach ($allUsers as $user) {
            if($user->id != $request->user()->id )
            {
                $permission->users()->attach($user->id,['isactive'=>false,
                                                        'company_id'=>1]);    
            }
         }
         //  Vue ACL delete model
       /*  $aclstr='import { AclInstaller, AclCreate, AclRule } from "vue-acl"
                  export  const delete'.$modelName.'= {
                        delete'.$modelName.': new AclRule("delete'.$modelName.'").or("owner").generate(),
                  }';
         $filename="\\resources\\js\\src\acl\details\\delete".$modelName.".js";
         File::put($appPath.$filename,$aclstr);*/
         //
         $permission=Permission::create([
          'name'=>"import".$request->model_name,
          "display_name"=>"Import ".$request->display_name,  
        ]);

     
         $permission->users()->attach($currentuser->id,['isactive'=>true,
                                                        'company_id'=>1]);
         
         foreach ($allUsers as $user) {
            if($user->id != $request->user()->id )
            {
                $permission->users()->attach($user->id,['isactive'=>false,
                                                        'company_id'=>1]);    
            }
         }
         //  Vue ACL import model
      /*   $aclstr='import { AclInstaller, AclCreate, AclRule } from "vue-acl"
                  export  const import'.$modelName.'= {
                        import'.$modelName.': new AclRule("import'.$modelName.'").or("owner").generate(),
                  }';
         $filename="\\resources\\js\\src\acl\details\\import".$modelName.".js";
         File::put($appPath.$filename,$aclstr);*/
         //
         $permission=Permission::create([
          'name'=>"export".$request->model_name,
          "display_name"=>"Export ".$request->display_name,  
        ]);

     
         $permission->users()->attach($currentuser->id,['isactive'=>true,
                                                        'company_id'=>1]);
         
         foreach ($allUsers as $user) {
            if($user->id != $request->user()->id )
            {
                $permission->users()->attach($user->id,['isactive'=>false,
                                                        'company_id'=>1]);    
            }
         }
          //  Vue ACL export model
      /*   $aclstr='import { AclInstaller, AclCreate, AclRule } from "vue-acl"
                  export  const export'.$modelName.'= {
                        export'.$modelName.': new AclRule("export'.$modelName.'").or("owner").generate(),
                  }';
         $filename="\\resources\\js\\src\acl\details\\export".$modelName.".js";
         File::put($appPath.$filename,$aclstr);*/
         //
  	$fieldArray=$request->dianasoftfields;

    foreach ($fieldArray as $camp) {
     	$dianasoftfield=DianaSoftField::create([
     	  "dianasoftmodel_id"=>$dianasoftmodel->id,	
    	  "name"=>$camp["name"],
    	  "type"=>$camp["type"],
    	  "length"=>$camp["length"],
    	  "nullable"=>$camp["nullable"],
    	 // "default"=>$camp["default"],
    	  "fillable"=>$camp["fillable"],
    	  "required"=>$camp["required"],
    	  "indexed"=>$camp["indexed"],
    	  //"frontendvalidation"=>$camp["frontendvalidation"],
    	  //"backendvalidation"=>$camp["backendvalidation"],
    	  //"faker"=>$camp["faker"],
    	  "display_name"=>$camp["display_name"],
    	  "input_type"=>$camp["input_type"],
    	  //"input_source"=>$camp["input_source"],
    	  //"input_source_type"=>$camp["input_source_type"],
    	  ]); 
     } 
/*
  	$relationshipArray=$request->relationshipArray;
  	foreach ($relationshipArray as $relation) {
     	$dianasoftrelationship=DianaSoftRelationship::create([
     	  "dianasoftmodel_id"=>$dianasoftmodel->id,	
    	  "name"=>$relation["name"],
    	  "type"=>$relation["type"],
    	  "model_name"=>$relation["model_name"],
    	  "foreign_key"=>$relation["foreign_key"],
    	  "local_key"=>$relation["local_key"],  
    	  ]); 
     } 
     */
  	   // MIGRATION
  	$migration='<?php
      
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Create'.ucfirst($tableName).'Table extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create("'.strtolower($tableName).'", function (Blueprint $table) {
            $table->bigIncrements("id");
            $table->integer("company_id")->index();';

    
    if($fieldArray)
    {
    		
    foreach ($fieldArray as $camp) {
       
    	$migration = $migration.'
    	    $table->'.$camp["type"].'("'.strtolower($camp["name"]).'"';
    	if($camp["length"]!=null)
    	{        
    	  $migration = $migration.','.$camp["length"].')';
	    }else
	    {
          $migration = $migration.')';
	    }
	    if($camp["nullable"])
    	{        
    	  $migration = $migration.'->nullable()';
	    }
	    if($camp["indexed"])
    	{        
    	  $migration = $migration.'->index()';
	    }
        /*
	    if($camp["default"]!=null)
    	{    
    	  if($camp["type"]=="string"){
           $migration = $migration.'->default("'.$camp["default"].'")';
    	  } 
    	  else{   
    	   $migration = $migration.'->default('.$camp["default"].')';
    	  }
	    }
        */
  		$migration = $migration.';';
    }
    }
          
       $migration = $migration.'
            $table->timestamps();
        });
    }

     /**
      * Reverse the migrations.
      *
      * @return void
      */
     public function down()
     {
         Schema::dropIfExists("'.strtolower($tableName).'");
     }
 }';
   // return $migration; 
 $filename="\database\migrations\\".Carbon::now()->format('Y_m_d_his')."_create_".strtolower($tableName)."_table.php";
  
  File::put($appPath.$filename,$migration);
  // MIGRATION
  
  //MODEL

      $modelstr='<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class '.$modelName.' extends Model
{
    use RecordsActivity;
    protected $table ="'.strtolower($tableName).'";
    protected $fillable = ["company_id",';

    if($fieldArray)
    {
    		
    foreach ($fieldArray as $camp) {
       if($camp["fillable"])
       {
    	$modelstr = $modelstr.'"'.strtolower($camp["name"]).'",';
       }
  		
    }
    }
    $modelstr = $modelstr.'];
    protected $casts = [';

    if($fieldArray)
    {
            
    foreach ($fieldArray as $camp) {
       if($camp["input_type"]=="checkbox")
       {
        $modelstr = $modelstr.'"'.strtolower($camp["name"]).'" => "boolean",';
       }
        
    }
    }
    $modelstr = $modelstr.'
    ];
    public  $groupByXLS=[
                            // ["col"=>"denumire_partener","denumire"=>"Partener","type"=>"","align"=>"center","width"=>"100%"],
                            // ["col"=>"contd","denumire"=>"Debit","type"=>"","align"=>"center","width"=>"100%"],
                           ];   
   public $totalByXLS=[];
   public $titluRaportXLS="'.$modelDisplayName.'";
   public $columnFormatXLS=[
                                 // "D" => NumberFormat::FORMAT_DATE_DDMMYYYY,
                                 // "I" => NumberFormat::FORMAT_CURRENCY_EUR_SIMPLE
                               ];
    public  $antetTabelXLS=[
                      //["col"=>"test","denumire"=>"Test","type"=>""],
                      //["col"=>"testdata","denumire"=>"TestData","type"=>"Date"],
    ';
  
    if($fieldArray)
    {
            
    foreach ($fieldArray as $camp) {
       
        $modelstr = $modelstr.'["col"=>"'.strtolower($camp["name"]).'","denumire"=>"'.strtolower($camp["display_name"]).'","type"=>""],';
       
        
    }
    }
    $modelstr = $modelstr.'
                      
                      
 ];';
    /*
    if($relationshipArray)
    {
    		
    foreach ($relationshipArray as $relation) {
      
    	
    	$modelstr=$modelstr.'

    public function '.strtolower($relation["name"]).'() {
        return $this->'.$relation["type"].'("App\\'.$relation["model_name"].'","'.
         $relation["foreign_key"].'","'.$relation["local_key"].'");
    }';  	
    }
    } 
   */ 

    $modelstr=$modelstr.'
}';
   $filename="\app\\models\\".$modelName.".php";
  
  File::put($appPath.$filename,$modelstr);
  //MODEL

  //CONTROLLER
   $controllerstr='<?php

namespace App\Http\Controllers\Api;

use App\Models\\'.$modelName.';
// use App\Events\\'.$modelName.'Updated;
use App\Models\Exports\\'.$modelName.'Export;
//use App\Models\Imports\\'.$modelName.'Import;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Mail;
use App\Mail\AlertaEroareEmail;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class '.$modelName.'Controller extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function indexPaginat(Request $request)
    {
          try{
          $records= '.$modelName.'::select(\'*\')->where("company_id",session("company_id"));
          $records=filterRequest($records,$request->searchModel,$request->cautareDupa,$request->sortModel,$request->filterModel);
        $records=  $records->orderBy(\'id\',\'desc\');
        $records=  $records->paginate($request->pageLength,
                                                                    ["page"=>$request->page]);
        
                              //::where("user_id",auth()->user()->id)
                                
        return json_encode($records);

      } catch (\Exception $e) {
        $methodName = __FUNCTION__;
          $fileName = basename(__FILE__);
        Mail::to("stefan.voinea@gmail.com")
        ->send(new AlertaEroareEmail($methodName." ".$fileName,$e->getMessage(),$e,Auth::user()));
        return response()->json(["message" => $e->getMessage()], 500);
      }  
    }
     public function index()
    {
      try{
          $'.strtolower($modelName).'= '.$modelName.'::where("company_id",session("company_id"))->get();
          return json_encode($'.strtolower($modelName).');

      } catch (\Exception $e) {
        $methodName = __FUNCTION__;
        $fileName = basename(__FILE__);
        Mail::to("stefan.voinea@gmail.com")
        ->send(new AlertaEroareEmail($methodName." ".$fileName,$e->getMessage(),$e,Auth::user()));
        return response()->json(["message" => $e->getMessage()], 500);
      }      
    }
    public function export() 
        {
             
            ob_end_clean(); 
            ob_start(); 
             $company_id=session("company_id");
            return Excel::download((new '.$modelName.'Export)->forCompany($company_id),"'.strtolower($modelName).'.xls");


        }

    public function import(Request $request) 
        {
          $fileName = "'.strtolower($modelName).'_".time().".".$request->file->getClientOriginalExtension();
          $request->file->move(public_path("upload"), $fileName);
          
          Excel::import(new '.$modelName.'Import, public_path("upload")."/".$fileName);

          
            $'.strtolower($modelName).'= '.$modelName.'::where("company_id",session("company_id"))
                                                     ->paginate($request->pageLength,['page'=>$request->page]);
         
            return json_encode($'.strtolower($modelName).');
             
        
           
         }
        
    public function storeExcel() 
        {
            ob_end_clean(); 
            ob_start(); 
            $company_id=session("company_id");
            
            Excel::store((new '.$modelName.'Export)->forCompany($company_id), "'.strtolower($modelName).'.xls","public",null,[
        "visibility" => "private",
    ]);
        }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {


      //   $request->validate( [';
    /*
    if($fieldArray)
    {
    		
    foreach ($fieldArray as $camp) {
       if($camp["backendvalidation"])
       {
    	$controllerstr = $controllerstr.'
         
    	  "'.strtolower($camp["name"]).'"=>["'.strtolower($camp["backendvalidation"]).'"],';
       }
  		
    }
    }
    */
            // '"denumire" => ["required", "string", "max:255"],
            // "email" => ["required", "string", "email", "max:255"],

            
       $controllerstr=$controllerstr. '
       //]);

        // event(new '.$modelName.'Updated());
      DB::beginTransaction();
      try{  

         $'.strtolower($camp["name"]).'= '.$modelName.'::create([
        "company_id"=>session("company_id"),
        ';

    if($fieldArray)
    {
    		
    foreach ($fieldArray as $camp) {
       if($camp["type"]=="date"){
        $controllerstr = $controllerstr.'
        "'.strtolower($camp["name"]).'"=>$request->'.strtolower($camp["name"]).'?dateFormatStocare($request->'.strtolower($camp["name"]).'):null,';
       }else{

    	$controllerstr = $controllerstr.'
    	  "'.strtolower($camp["name"]).'"=>$request->'.strtolower($camp["name"]).',';

       }
    	
    }
    }

    $controllerstr=$controllerstr.'           
        ]);
        DB::commit();
        return $'.strtolower($camp["name"]).';
        
      } catch (\Exception $e) {
        DB::rollback();
        $methodName = __FUNCTION__;
          $fileName = basename(__FILE__);
        Mail::to("stefan.voinea@gmail.com")
        ->send(new AlertaEroareEmail($methodName." ".$fileName,$e->getMessage(),$e,Auth::user()));
        return response()->json(["message" => $e->getMessage()], 500);
      }  
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\\Models\\'.$modelName.'  $'.strtolower($modelName).'
     * @return \Illuminate\Http\Response
     */
    public function show('.$modelName.' $'.strtolower($modelName).')
    {
      try{
        $resp= '.$modelName.'::where("id",$'.strtolower($modelName).'->id)
                               ->where("company_id",session("company_id"))
        					   ->get()->first();
                                
        return json_encode($resp);

      } catch (\Exception $e) {
        $methodName = __FUNCTION__;
          $fileName = basename(__FILE__);
        Mail::to("stefan.voinea@gmail.com")
        ->send(new AlertaEroareEmail($methodName." ".$fileName,$e->getMessage(),$e,Auth::user()));
        return response()->json(["message" => $e->getMessage()], 500);
      }  
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\\'.$modelName.'  $'.strtolower($modelName).'
     * @return \Illuminate\Http\Response
     */
    public function edit('.$modelName.' $'.strtolower($modelName).')
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\\'.$modelName.'  $'.strtolower($modelName).'
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, '.$modelName.' $'.strtolower($modelName).')
    {
      DB::beginTransaction();
      try{
              $'.strtolower($modelName).'->update([';
      
    if($fieldArray)
    {
    		
    foreach ($fieldArray as $camp) {
      if($camp["type"]=="date"){
        $controllerstr = $controllerstr.'
        "'.strtolower($camp["name"]).'"=>$request->'.strtolower($camp["name"]).'?dateFormatStocare($request->'.strtolower($camp["name"]).'):null,';
       }else{ 
    	$controllerstr = $controllerstr.'
    	  "'.strtolower($camp["name"]).'"=>$request->'.strtolower($camp["name"]).',';
    	}
    }
    }
     
    $controllerstr=$controllerstr.'
        ]);
       // event(new '.$modelName.'Updated());
          DB::commit();
        return $'.strtolower($modelName).';
        
      } catch (\Exception $e) {
        DB::rollback();
        $methodName = __FUNCTION__;
          $fileName = basename(__FILE__);
        Mail::to("stefan.voinea@gmail.com")
        ->send(new AlertaEroareEmail($methodName." ".$fileName,$e->getMessage(),$e,Auth::user()));
        return response()->json(["message" => $e->getMessage()], 500);
      }  
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\\'.$modelName.'  $'.strtolower($modelName).'
     * @return \Illuminate\Http\Response
     */
    public function destroy('. $modelName.' $'.strtolower($modelName).')
    {
      DB::beginTransaction();
      try{
              $'.strtolower($modelName).'->delete();
      
      //  event(new '.$modelName.'Updated());
        DB::commit();
        
      } catch (\Exception $e) {
        DB::rollback();
        $methodName = __FUNCTION__;
          $fileName = basename(__FILE__);
        Mail::to("stefan.voinea@gmail.com")
        ->send(new AlertaEroareEmail($methodName." ".$fileName,$e->getMessage(),$e,Auth::user()));
        return response()->json(["message" => $e->getMessage()], 500);
      }          
    }
}';
  $filename="\app\Http\Controllers\Api\\".$modelName."Controller.php";
  
  File::put($appPath.$filename,$controllerstr);
  //CONTROLLER

  //API ROUTE
  $routesstr='
  <?php

    Route::middleware("auth:api")->group(function () {
	    Route::post("/'.strtolower($modelName).'", "Api\\'.$modelName.'Controller@indexPaginat")
            ->middleware("permission:view'.$modelName.'");
        Route::get("/'.strtolower($modelName).'", "Api\\'.$modelName.'Controller@index")
            ->middleware("permission:view'.$modelName.'");
	    Route::get("/'.strtolower($modelName).'/show/{'.strtolower($modelName).'}", "Api\\'.$modelName.
	    'Controller@show")
            ->middleware("permission:view'.$modelName.'");

	    Route::post("/'.strtolower($modelName).'/store", "Api\\'.$modelName.'Controller@store")
            ->middleware("permission:add'.$modelName.'");

	    Route::post("/'.strtolower($modelName).'/delete/{'.strtolower($modelName).'}", "Api\\'.$modelName.'Controller@destroy")
            ->middleware("permission:delete'.$modelName.'");

	    Route::post("/'.strtolower($modelName).'/edit/{'.strtolower($modelName).'}", "Api\\'.$modelName.'Controller@update")
            ->middleware("permission:edit'.$modelName.'");
      Route::get("/'.strtolower($modelName).'/export", "Api\\'.$modelName.'Controller@export")
            ->middleware("permission:export'.$modelName.'");  

         Route::post("/'.strtolower($modelName).'/import", "Api\\'.$modelName.'Controller@import")
                     ->middleware("permission:import'.$modelName.'");       
 });
  ';
  $filename="\\routes\api_routes\\".strtolower($modelName)."_routes.php";
  
  File::put($appPath.$filename,$routesstr);
  
  //API ROUTE




//vue cu aggrid vers 2
$vuestr='<template>
    <b-overlay
      :show="showLoading"
      rounded="sm"
      no-fade
      variant="primary"
      opacity="0.25"
      blur="2px"
    >
    <b-row class="d-flex align-items-center justify-content-center">
      <b-col cols="12" >
      <b-card>
    
        <tabelcomponent 
                      :columnDefs="columnDefs"
                      :modelName="modelName"
                      :refresh="refreshLocal"
                      :titlu="modelDisplayName"
                      :idselectat="idselectat"
                      :campFiltruStart="campFiltruStart"
                      @onSelectionChanged="onSelectionChanged"
                      @adauga="add"
                      @edit="edit"
                      @view="view">
                
        </tabelcomponent>
      </b-card>
      </b-col>
   </b-row > 
      <'.$modelName.'aev
                         @stored="afisezSalvat"
                         @closed="aevClosed"
                         :activeAction="activeAction"
                         :activeEdit="activeEdit"
                         :editVar="editVar"
                         v-show="activeEdit"
                      > 
      </'.$modelName.'aev>  
    
   </b-overlay>
</template>

<script>

import '.$modelName.'aev from "./'.$modelName.'aev.vue"

export default {
  props: {
        id:String,
        }, 
  components: {
    '.$modelName.'aev
  },
  name:"'.strtolower($modelName).'",
  data() {
    return {
        refreshLocal:false, 
        idselectat:null,
        campFiltruStart:"",
        modelName:"'.strtolower($modelName).'",
        modelDisplayName:"'.$modelDisplayName.'",
        editVar:{';
   if($fieldArray)
    {
            
    foreach ($fieldArray as $camp) {
       
        $vuestr = $vuestr.'
          '.strtolower($camp["name"]).':"",';
        
    }
}
$vuestr = $vuestr.'},
        activeEdit:false,
        activeAction:"",
        selectedID:"",
        showLoading:false,
        columnDefs: [
                       // { headerName: "Document...",
                      //        children: [
                      // columnGroupShow:"open",
                          // filter: "agNumberColumnFilter",
                          // valueFormatter: function(params) { return new Date(params.value).toLocaleDateString() },
                          // cellRenderer: function(params) {
                          //            if(params.value!=null){

                          //               return "<a href=\'/contract?id="+params.value.id +"\' target=\'_blank\'>"+ params.value.nr_contract+\'/\'+ new Date(params.value.data_contract).toLocaleDateString()+\' \'+params.value.nume+\'</a>\'  
                          //            }
                                    
                          //       },
                  ';

            if($fieldArray)
            {
                    
            foreach ($fieldArray as $camp) {
               
                $vuestr = $vuestr.'
                {
                  label: "'.$camp["display_name"].'",
                  field: "'.strtolower($camp["name"]).'",
                  width: "300px",';
                  if($camp["type"]=="date"||$camp["type"]=="dateTime"){
                  $vuestr = $vuestr.'
                  type:"date",
                  //dateInputFormat: "yyyy-MM-dd\'T\'HH:mm:ss.SSSSSS\'Z\'", // expects 2018-03-16
                  //dateOutputFormat: "dd.MM.yyyy HH:mm:ss", // outputs Mar 16th 2018
                  dateInputFormat: "yyyy-MM-dd", // expects 2018-03-16
                  dateOutputFormat: "dd.MM.yyyy", // outputs Mar 16th 2018 ';  
                  }else{
                          if($camp["type"]=="double"||$camp["type"]=="integer"){
                          $vuestr = $vuestr.'
                          type:"number",';  
                          }else{
                             $vuestr = $vuestr.'
                          type:"text",';  
                          } 
                     }
                  $vuestr = $vuestr.'
                 showSortAsc:true,
                },
               
                ';
                
                }
            }

            $vuestr=$vuestr.'  
                    
        ],
     
    }
  },
 
  methods: {
    
    aevClosed(){
       this.idselectat=null
      this.selectedID=""
      this.activeEdit=false
      this.editVar={';
   if($fieldArray)
    {
            
    foreach ($fieldArray as $camp) {
       
        $vuestr = $vuestr.'
          '.strtolower($camp["name"]).':"",';
        
    }
}
$vuestr = $vuestr.'},
      this.activeAction=""
    },
    afisezSalvat(value){
      //this.idselectat=value.id
      //this.campFiltruStart="id"
        this.refreshLocal=!this.refreshLocal
    },
    listen(){
              //  Echo.channel("cerber_databasechannel")
              //      .listen("."+this.modelName+".updated", (e) => {
              //         this.getRecords()
               //      });
    },
    onSelectionChanged(value){
      this.selectedID=value
    },
    
    add() {
        this.activeAction="Adaugă"
        this.editVar={  
                     ';

            if($fieldArray)
            {
                    
            foreach ($fieldArray as $camp) {
               
                $vuestr = $vuestr.$camp["name"].':"",
                ';
                
                }
            }

            $vuestr=$vuestr.'

        }
        this.activeEdit=true
    },
    
    edit() {
          this.idselectat=null
          this.editVar=Object.assign({},this.selectedID)
          this.activeAction="Modifică"
          this.activeEdit=true
         
    },
    view() {
        
          
          this.editVar=Object.assign({},this.selectedID)
          this.activeAction="Vizualizează"
          this.activeEdit=true
        
    },
    
    },
    created() {
      document.title=window.app_name+"->"+this.modelDisplayName
     // if(this.id!=null){
     //             this.idselectat=this.id
     //             this.campFiltruStart="id"
     // }
      this.listen()
     
    },
  
}

</script>';
  $filename="\\resources\\js\\src\\views\\app_pages\\".$modelName.".vue";
  File::put($appPath.$filename,$vuestr);
  // VUE COMPONENT

  //AEV VUE COMPONENT
  $aevvuestr='<template>
   <validation-observer ref="simpleRules">
  <div class= "d-flex justify-content-center" >
   <b-modal id="Dianasoftmodelaev"  
             size="xl" 
             no-close-on-backdrop
             centered
             :hide-footer="activeActionLocal==\'Vizualizează\'"
             ok-variant="success"
             cancel-title="Cancel"              
             ok-title="Save"
             cancel-variant="warning"
             scrollable
             :cancel-disabled="activeActionLocal==\'Vizualizează\'"
             :ok-disabled="activeActionLocal==\'Vizualizează\'"
             modal-class="modal-success"
             :title="activeActionLocal+\' '.$modelDisplayName.'\'"
             v-model="activeEditLocal"
             @ok="handleOk"
             @cancel="aevClosed"
             >
<form  ref="form"  @submit.stop.prevent="handleSubmit" >
             <br>

            
                <b-row class="d-flex justify-content-center" >
                     ';

                         if($fieldArray)
                        {
                                
                        foreach ($fieldArray as $camp) {
                           
                            $aevvuestr = $aevvuestr.'
                            <b-col  cols="2">';
                             if($camp["input_type"]=="produsedefinantare"){
                              $aevvuestr = $aevvuestr.'
                               <validation-provider
                                    #default="{ errors }"
                                     name="'.$camp["display_name"].'"
                                    rules=""
                                  >
                                      <produsedefinantare 
                                                  :readonly="activeActionLocal==\'Vizualizează\'"
                                                  v-model="editVarLocal.'.$camp["name"].'">

                                  </produsedefinantare>
                                   <small class="text-danger">{{ errors[0] }}</small>
                                </validation-provider>';
                            }else{
                            if($camp["input_type"]=="cnpcomponent"){
                              $aevvuestr = $aevvuestr.'
                               <validation-provider
                                    #default="{ errors }"
                                     name="'.$camp["display_name"].'"
                                    rules=""
                                  >  
                           <cnp-component 
                                          class="w-full"
                                         :readonly="activeActionLocal==\'Vizualizează\'"
                                         name="'.$camp["display_name"].'"
                                         placeholder=".'.$camp["display_name"].'" 
                                         :activeEdit="activeEditLocal"
                                         @ciScanat="preiaDateCIScanat"
                                         @CUIIntrodus="CUIIntrodus"
                                         v-model="editVarLocal.'.$camp["name"].'"
                                         >
                          </cnp-component>
                           <small class="text-danger">{{ errors[0] }}</small>
                                  
                                </validation-provider>
                                  ';
                            }else{
                            if($camp["input_type"]=="textarea"){
                              $aevvuestr = $aevvuestr.'
                                     <div class="form-label-group">
                                    
                                    <validation-provider
                                           #default="{ errors }"
                                          name="'.$camp["display_name"].'"
                                          rules=""
                                        >
                                    
                                      <b-form-textarea
                                        id=".'.$camp["name"].'"
                                         v-model="editVarLocal.'.$camp["name"].'"
                                        rows="3"
                                        placeholder=".'.$camp["display_name"].'"
                                      />
                                       <small class="text-danger">{{ errors[0] }}</small>
                                       </validation-provider>
                                      <label for="label-.'.$camp["name"].'">.'.$camp["display_name"].'</label>
                                    </div>';
                            }else{
                            if($camp["input_type"]=="localitatecomponent"){
                              $aevvuestr = $aevvuestr.'
                              <validation-provider
                                           #default="{ errors }"
                                          name="'.$camp["display_name"].'"
                                          rules=""
                                        >
                                      <localitatecomponent 
                                                  :readonly="activeActionLocal==\'Vizualizează\'"
                                                  :judet="editVarLocal.judet"
                                                  v-model="editVarLocal.'.$camp["name"].'">

                                  </localitatecomponent>
                                   <small class="text-danger">{{ errors[0] }}</small>
                                   </validation-provider>
                                       ';
                            }else{
                               if($camp["input_type"]=="gestiunepermisa"){
                              $aevvuestr = $aevvuestr.'
                              <validation-provider
                                           #default="{ errors }"
                                          name="'.$camp["display_name"].'"
                                          rules=""
                                        >
                                      <gestiunepermisa 
                                                    name=".'.$camp["name"].'"
                                                   :pastrezvaloare="true"  
                                                   class="w-full"   
                                                  :readonly="activeActionLocal==\'Vizualizează\'"
                                                  v-model="editVarLocal.'.$camp["name"].'">

                                  </gestiunepermisa>
                                   <small class="text-danger">{{ errors[0] }}</small>
                                   </validation-provider>
                                       ';
                            }else{
                            if($camp["input_type"]=="taricomponent"){
                              $aevvuestr = $aevvuestr.'
                               <validation-provider
                                           #default="{ errors }"
                                          name="'.$camp["display_name"].'"
                                          rules=""
                                        >
                                     <taricomponent 
                                                  :readonly="activeActionLocal==\'Vizualizează\'"
                                                  v-model="editVarLocal.'.$camp["name"].'">

                                  </taricomponent>
                                   <small class="text-danger">{{ errors[0] }}</small>
                                       </validation-provider>';
                            }else{
                             if($camp["input_type"]=="judetcomponent"){
                              $aevvuestr = $aevvuestr.'
                               <validation-provider
                                           #default="{ errors }"
                                          name="'.$camp["display_name"].'"
                                          rules=""
                                        >
                                     <judetcomponent 
                                                  :readonly="activeActionLocal==\'Vizualizează\'"
                                                  v-model="editVarLocal.'.$camp["name"].'">

                                  </judetcomponent>
                                  <small class="text-danger">{{ errors[0] }}</small>
                                       </validation-provider>';
                            }else{
                            if($camp["input_type"]=="contcontabil"){
                              $aevvuestr = $aevvuestr.'
                              <validation-provider
                                           #default="{ errors }"
                                          name="'.$camp["display_name"].'"
                                          rules=""
                                        >
                                     <contcontabil  
                                         name="'.$camp["name"].'"
                                          :readonly="activeActionLocal==\'Vizualizează\'"
                                           v-model="editVarLocal.'.$camp["name"].'"
                                          label="'.$camp["display_name"].'">
                                                        
                                </contcontabil>
                                 <small class="text-danger">{{ errors[0] }}</small>
                                       </validation-provider>';
                            }else{
                            if($camp["input_type"]=="selectoneuser"){
                              $aevvuestr = $aevvuestr.'
                              <validation-provider
                                           #default="{ errors }"
                                          name="'.$camp["display_name"].'"
                                          rules=""
                                        >
                                     <selectoneuser  labelDisplay="'.$camp["display_name"].'"
                                                       v-model="editVarLocal.'.$camp["name"].'"
                                                      :readonly="activeActionLocal==\'Vizualizează\'"
                                                    >
                                     </selectoneuser> 
                                      <small class="text-danger">{{ errors[0] }}</small>
                                       </validation-provider>';
                            }else{
                            if($camp["input_type"]=="dropdowncuoptiuni"){
                              $aevvuestr = $aevvuestr.'
                                     <validation-provider
                                           #default="{ errors }"
                                          name="'.$camp["display_name"].'"
                                          rules=""
                                        >
                              <dropdowncuoptiuni 
                                name="'.$camp["name"].'" 
                                :readonly="activeAction==\'Vizualizează\'" 
                                 v-model="editVarLocal.'.$camp["name"].'" 
                                campDisplay="'.$camp["display_name"].'"
                                field_name="'.$camp["display_name"].'"
                                limitToList="true"
                                > 
                          </dropdowncuoptiuni>
                           <small class="text-danger">{{ errors[0] }}</small>
                                       </validation-provider>';
                            }else{
                              if($camp["type"]=="dateTime"){
                              $aevvuestr = $aevvuestr.'
                              <validation-provider
                                           #default="{ errors }"
                                          name="'.$camp["display_name"].'"
                                          rules=""
                                        >
                                     <datasiora 
                                          :readonly="activeActionLocal==\'Vizualizează\'"
                                          id="'.$camp["name"].'" 
                                          v-model="editVarLocal.'.$camp["name"].'"
                                          name="'.$camp["name"].'" 
                                          campDisplay="'.$camp["display_name"].'"> 
                                      </datasiora>
                                       <small class="text-danger">{{ errors[0] }}</small>
                                       </validation-provider>';
                            }else{
                            if($camp["type"]=="date"){
                              $aevvuestr = $aevvuestr.'
                              <validation-provider
                                           #default="{ errors }"
                                          name="'.$camp["display_name"].'"
                                          rules=""
                                        >
                                     <datacalendaristica 
                                          :readonly="activeActionLocal==\'Vizualizează\'"
                                          id="'.$camp["name"].'" 
                                          v-model="editVarLocal.'.$camp["name"].'"
                                          name="'.$camp["name"].'" 
                                          campDisplay="'.$camp["display_name"].'"> 
                                      </datacalendaristica>
                                      <small class="text-danger">{{ errors[0] }}</small>
                                       </validation-provider>';
                            }else{
                                if($camp["input_type"]=="checkbox"){
                              $aevvuestr = $aevvuestr.'
                                    <b-form-checkbox
                                          v-model="editVarLocal.'.$camp["name"].'"
                                          class="custom-control-primary">
                                          '.$camp["display_name"].'
                                        </b-form-checkbox>';
                            }else{
                                $aevvuestr = $aevvuestr.'
                                <validation-provider
                                           #default="{ errors }"
                                          name="'.$camp["display_name"].'"
                                          rules=""
                                        >
                                  <div class="form-label-group">      
                                  <b-form-input
                                    :readonly="activeActionLocal==\'Vizualizează\'"
                                    size="sm"
                                    autocomplete="off"
                                    id="'.$camp["name"].'"'; 
                                    if($camp["input_type"]=="double"||$camp["input_type"]=="integer"){
                                          $aevvuestr = $aevvuestr.'
                                    type="number"';
                                  }
                                  $aevvuestr = $aevvuestr.'
                                    v-model="editVarLocal.'.$camp["name"].'"
                                    placeholder="'.$camp["display_name"].'" 
                                    
                                  />
                                  <label for="'.$camp["name"].'">'.$camp["display_name"].'</label>
                                  <small class="text-danger">{{ errors[0] }}</small>
                                   </div>
                                       </validation-provider>
                              
                               ';
                            }}}}}}}}}}}}}
                           
                           $aevvuestr = $aevvuestr.'     
                            </b-col>
                            ';
                            
                            }
                        }

                        $aevvuestr=$aevvuestr.'
                    
                </b-row>
                
             <br><br><br><br><br><br><br><br><br><br><br> 
      </form>
    </b-modal>
  </div>
   </validation-observer>
</template>

<script>
import Ripple from "vue-ripple-directive"
import { heightTransition } from "@core/mixins/ui/transition"
import {VBModal} from "bootstrap-vue"
import {  required, email, confirmed, password,min} from "@validations"
import { ValidationProvider, ValidationObserver} from "vee-validate"
export default {
  props: {
        activeEdit:Boolean,
        activeAction:String,
        editVar:Object,
        rutainapoi:String,
        },
  mixins: [heightTransition],
  components: {
     ValidationProvider, ValidationObserver  
  },
   directives: {
    "b-modal": VBModal,
    Ripple,
  },
  name:"'.strtolower($modelName).'aev",
  data() {
    return {
        required, 
        password,
        email,
        confirmed,
         min,
        rutainapoiLocal:this.rutainapoi,
        activeEditLocal:false,
        activeActionLocal:this.activeAction,
        editVarLocal:this.editVar,
        nextTodoId: 2,
        modelName:"'.strtolower($modelName).'",
        showLoading:false,
       
      }
  },
  watch: {
      activeEdit(){
         
         this.activeEditLocal=this.activeEdit
       },
       activeEditLocal(){
            if (this.activeEditLocal==false){
              this.$emit(\'closed\')
            }
            
       },
      activeAction(){
        this.activeActionLocal=this.activeAction
      },
      editVar(){
        this.editVarLocal=this.editVar
      }
  },
  
  methods: {
    
    initTrHeight() {
      this.trSetHeight(null)
      this.$nextTick(() => {
        if(this.$refs.form){
        this.trSetHeight(this.$refs.form.scrollHeight)
        }
      })
    },
   
    
     handleOk(bvModalEvt){
      bvModalEvt.preventDefault()
        this.$refs.simpleRules.validate().then(success => {
        if (success) {
       if (this.activeActionLocal=="Adaugă") 
        {
          this.saveAdd()
        }
        if (this.activeActionLocal=="Modifică") 
        {
          this.saveEdit()
        }
         }
      })
    },
    saveAdd(){
        
          this.showLoading=true
          const payLoad=this.editVarLocal
          payLoad.requestType="post"
          payLoad.requestUrl="/"+this.modelName+"/store"
         
          this.$store.dispatch("app/api_Request",payLoad)
                    .then(response=>{
                           
                            this.editVarLocal={  
                                                     ';

                                            if($fieldArray)
                                            {
                                                    
                                            foreach ($fieldArray as $camp) {
                                               
                                                $aevvuestr = $aevvuestr.$camp["name"].':\'\',
                                                ';
                                                
                                                }
                                            }

                                            $aevvuestr=$aevvuestr.'

                                        }
                             this.$bvToast.toast("Salvare efectuata cu success!", 
                                                 {
                                                    title: `Salvare cu succes! `,
                                                    variant:"success",
                                                    solid: false,
                                                    appendToast: true,
                                                    autoHideDelay: 3000,
                                                    toaster: "b-toaster-bottom-right",
                                                                      }) 
                            this.showLoading=false
                             this.activeEditLocal=false
                           this.activeActionLocal=""
                           this.$emit("stored",response)              
                            this.$emit("closed")
          
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
         
    },
   
    aevClosed(){
       this.idselectat=null
      this.selectedID=""
        this.activeEditLocal=false
        this.editVarLocal={  
                                                     ';

                                            if($fieldArray)
                                            {
                                                    
                                            foreach ($fieldArray as $camp) {
                                               
                                                $aevvuestr = $aevvuestr.$camp["name"].':"",
                                                ';
                                                
                                                }
                                            }

                                            $aevvuestr=$aevvuestr.'

                                        }
        this.activeActionLocal=""
        this.$emit("closed")
        
    },
    
    saveEdit(){
               
                  this.showLoading=true
                  const payLoad=this.editVarLocal 
                  payLoad.requestType="post"
                  payLoad.requestUrl="/"+this.modelName+"/edit/"+this.editVarLocal.id
                  
                  this.$store.dispatch("app/api_Request",payLoad)
                              .then(response=>{
                                               this.selectedID=""
                                               
                                               this.editVarLocal={  
                                                     ';

                                            if($fieldArray)
                                            {
                                                    
                                            foreach ($fieldArray as $camp) {
                                               
                                                $aevvuestr = $aevvuestr.$camp["name"].':"",
                                                ';
                                                
                                                }
                                            }

                                            $aevvuestr=$aevvuestr.'

                                        }
                                         this.$bvToast.toast("Modificare efectuata cu success!", {
                                                                        title: "Modificare cu succes! ",
                                                                        variant:"success",
                                                                        solid: false,
                                                                        appendToast: true,
                                                                        autoHideDelay: 3000,
                                                                        toaster: "b-toaster-bottom-right",
                                                                      }) 
                                               this.showLoading=false
                                                this.activeEditLocal=false
                                               this.activeActionLocal=""
                                               this.$emit("stored","") 
                                               this.$emit("closed")
          
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
               
    },
     initTrHeight() {
      this.trSetHeight(null)
      this.$nextTick(() => {
        if(this.$refs.form){
        this.trSetHeight(this.$refs.form.scrollHeight)
        }
      })
    },
  },
  mounted() {
    this.initTrHeight()
  },
  destroyed() {
    window.removeEventListener("resize", this.initTrHeight)
  },
  created() {
         
         if(!this.rutainapoi){
          this.rutainapoiLocal=this.modelName
         }
          window.addEventListener("resize", this.initTrHeight)
          
     
    },
}

</script>

';
   $filename="\\resources\\js\\src\\views\\app_pages\\".$modelName."aev.vue";
  File::put($appPath.$filename,$aevvuestr);
  //AEV VUE COMPONENT

  //EXPORT

      $exportstr='<?php
namespace App\Exports;

use App\Models\\'.$modelName.';
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;

class '.$modelName.'Export implements FromQuery, WithHeadings
{
  use Exportable;
  public function forCompany(int $company_id)
    {
        $this->company_id = $company_id;
        return $this;
    }
    public function headings(): array
    {
        return [
            ';
    if($fieldArray)
    {
     foreach ($fieldArray as $camp) {
       $exportstr=$exportstr.'
        "'.strtolower($camp["display_name"]).'",';        
     }
    }
    $exportstr=$exportstr.  '
        ];
    }
    public function query()
    {
        return '.$modelName.'::query()->select(';
    if($fieldArray)
    {
     foreach ($fieldArray as $camp) {
       $exportstr=$exportstr.'
        "'.strtolower($camp["name"]).'",';        
     }
    }
    $exportstr=$exportstr.  ')->where("company_id",$this->company_id);
    }
}
';
   $filename="\app\\Exports\\".$modelName."Export.php";
  
  File::put($appPath.$filename,$exportstr);
  //EXPORT

  //IMPORT

      $importstr='<?php
namespace App\Imports;

use App\Models\\'.$modelName.';
use Maatwebsite\Excel\Concerns\ToModel;
use Illuminate\Support\Str;

class '.$modelName.'Import implements ToModel
{
    public function model(array $row)
    {
    //  if ($row[0]=="Cont") {
    //        return null;
    //    };
    // if ('.$modelName.'::where("company_id",session("company_id"))
    //             ->where("cont",$row[0])
    //              ->get()
    //              ->count()>0) 
    //    {
    //        return null;
    //    };
        return new '.$modelName.'([ 
        "company_id"=>session("company_id"),
        ';
        $nr=0;
    if($fieldArray)
    {
     foreach ($fieldArray as $camp) {
       $importstr=$importstr.'
        "'.strtolower($camp["name"]).'"=>$row['.$nr.'],';        
        $nr++;
     }
    }
    $importstr=$importstr.  '
        ]);
    }
}';
  
   $filename="\app\\Imports\\".$modelName."Import.php";
  
  File::put($appPath.$filename,$importstr);
  //IMPORT



  return response()->json('Model was created successfully',200);
  }
}