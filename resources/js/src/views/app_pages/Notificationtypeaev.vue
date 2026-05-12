<template>
  <div class= "d-flex justify-content-center" >
   <b-modal id="Dianasoftmodelaev"  
             size="lg" 
             ok-variant="success"
             cancel-title="Cancel"              
             ok-title="Save"
             cancel-variant="warning"
             scrollable
             modal-class="modal-success"
             :title="activeActionLocal+' Tip notificare'"
             v-model="activeEditLocal"
             @ok="handleOk"
             @cancel="aevClosed"
             >
             <br>

            
                <b-row >
                     
                            <b-col  cols="4"><div class="form-label-group">
                                   <dropdowncuoptiuni 
                                                        name="categoria"
                                                        :readonly="activeAction=='Vizualizează'" 
                                                        v-model="editVarLocal.categoria " 
                                                        campDisplay="Categoria"  
                                                        field_name="Categorie notificare"
                                                        limitToList="true"
                                                        > 
                                  </dropdowncuoptiuni>
                                    </div> 
                            </b-col>
                            
                            <b-col  cols="8">
                            <div class="form-label-group">
                             <b-form-group >
                                  <b-form-input
                                    :readonly="activeActionLocal=='Vizualizează'"
                                    id="denumire" 
                                    placeholder="Denumire"
                                    v-model="editVarLocal.denumire"/>
                             </b-form-group>
                            </div>     
                            </b-col>
                            
                    
                </b-row>
                
              <br>
                <!-- USERS -->
         <b-container fluid>
      <b-form
        ref="form"
        :style="{height: trHeight}"
        class="repeater-form"
        @submit.prevent="repeateAgain"
      >
        
         <b-row >
        <b-col cols="6">
               User
        </b-col>
                
          <b-col cols="4">   
             Canal de comunicare
          </b-col>
          
          
         
             
                  <!-- Add Button -->
          <b-col
           cols="2"
           
          >
            

        
          </b-col>

         
        
        </b-row>

        <b-row 
                v-for="(item, index) in editVarLocal.notificationuser"
              :id="item.id"
              :key="item.id"
              ref="row" >
        <b-col cols="6">
               <selectoneuser  labelDisplay=""
                                v-model="item.user" 
                                :readonly="activeActionLocal=='Vizualizează'"
                                @change="addRow(index)">
               </selectoneuser>
        </b-col>
                
          <b-col cols="4">   
           <dropdowncuoptiuni 
                                name="channel"
                                :readonly="activeAction=='Vizualizează'" 
                                v-model="item.channel " 
                                campDisplay=""  
                                field_name="Canal de comunicare notificari"
                                limitToList="true"
                                > 
          </dropdowncuoptiuni>
          </b-col>
          
          
         
             
                  <!-- Add Button -->
          <b-col class="d-flex justify-content-between"
           cols="2"
           
          >
            <b-button
                        v-ripple.400="'rgba(234, 84, 85, 0.15)'"
                        variant="flat-success"
                        v-show="activeAction!='Vizualizează'" 
                        size="sm"
                        class="btn-icon"
                        @click="addRow">
              <feather-icon   icon="PlusIcon"/>
            
            </b-button>
        
       <!-- Remove Button -->
            <b-button
                        v-show="index>0&&activeAction!='Vizualizează'"
                        v-ripple.400="'rgba(234, 84, 85, 0.15)'"
                        variant="flat-danger"
                        
                        size="sm"
                        class="btn-icon"
                        @click="removeItem(index)">
              <feather-icon   icon="XIcon"/>
            
            </b-button>

        
          </b-col>

         
        
        </b-row>
       <br><br><br><br><br><br><br><br><br><br><br><br><br><br><br>
      </b-form>
      
      </b-container>
      
    </b-modal>
  </div>
</template>

<script>
import Ripple from "vue-ripple-directive"
import { heightTransition } from "@core/mixins/ui/transition"
import {VBModal} from "bootstrap-vue"
export default {
  props: {
        activeEdit:Boolean,
        activeAction:String,
        editVar:Object,
        rutainapoi:String,
        },
  mixins: [heightTransition],
  components: {

  },
   directives: {
    "b-modal": VBModal,
    Ripple,
  },
  name:"notificationtypeaev",
  data() {
    return {
         nextTodoId: 2,
        rutainapoiLocal:this.rutainapoi,
        activeEditLocal:false,
        activeActionLocal:this.activeAction,
        editVarLocal:this.editVar,
        nextTodoId: 2,
        modelName:"notificationtype",
        showLoading:false,
       
      }
  },
  watch: {
      activeEdit(){
         
         this.activeEditLocal=this.activeEdit
       },
       activeEditLocal(){
            if (this.activeEditLocal==false){
              this.$emit('closed')
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
     removeItem(index) {
      this.editVarLocal.notificationuser.splice(index, 1)
      this.trTrimHeight(this.$refs.row[0].offsetHeight)
    },
     addRow(index){
     
      
        this.editVarLocal.notificationuser.push({
                          id: this.nextTodoId += this.nextTodoId,
                          user_id: '',
                          channel:'',
                          })
          
          this.$nextTick(() => {
            this.trAddHeight(this.$refs.row[0].offsetHeight)
          })
      
      },
    repeateAgain() {
      this.editVarLocal.notificationuser.push({
                          id: this.nextTodoId += this.nextTodoId,
                          user_id: '',
                          channel:'',
                          
      })

      this.$nextTick(() => {
        this.trAddHeight(this.$refs.row[0].offsetHeight)
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
   
    
     handleOk(){
       
       if (this.activeActionLocal=="Adaugă") 
        {
          this.saveAdd()
        }
        if (this.activeActionLocal=="Modifică") 
        {
          this.saveEdit()
        }
    },
    saveAdd(){
        
          this.showLoading=true
          const payLoad=this.editVarLocal
          payLoad.requestType="post"
          payLoad.requestUrl="/"+this.modelName+"/store"
          this.activeEditLocal=false
          this.activeActionLocal=""
          this.$store.dispatch("app/api_Request",payLoad)
                    .then(response=>{
                           // this.row=response  

                           this.idselectat=response.id.toString()
                           this.$emit("stored",response)              
                            this.editVarLocal={  
                                                     categoria:'',
                                                denumire:'',
                                                

                                        }
                            this.showLoading=false
                             this.$bvToast.toast("Salvare efectuata cu success!", 
                                                 {
                                                    title: `Salvare cu succes! `,
                                                    variant:"success",
                                                    solid: false,
                                                    appendToast: true,
                                                    autoHideDelay: 3000,
                                                    toaster: "b-toaster-bottom-right",
                                                                      }) 
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
        this.activeEditLocal=false
        this.editVarLocal={  
                                                     categoria:"",
                                                denumire:"",
                                                

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
                                               // this.tblRecords=response
                                               this.idselectat=response.id.toString()
                                               this.$emit("stored",response) 
                                               this.activeEditLocal=false
                                               this.activeActionLocal=""
                                               this.editVarLocal={  
                                                     categoria:"",
                                                denumire:"",
                                                

                                        }
                                         this.showLoading=false
                                         this.$bvToast.toast("Modificare efectuata cu success!", {
                                                                        title: "Modificare cu succes! ",
                                                                        variant:"success",
                                                                        solid: false,
                                                                        appendToast: true,
                                                                        autoHideDelay: 3000,
                                                                        toaster: "b-toaster-bottom-right",
                                                                      }) 
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

