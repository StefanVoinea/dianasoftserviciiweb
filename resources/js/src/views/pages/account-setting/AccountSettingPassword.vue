<template>
  <b-card>
    <!-- form -->
    <b-form>
     
      <b-row>
        <!-- new password -->
        <b-col md="6">
          <b-form-group
            label-for="account-new-password"
            label="Parola noua"
          >
            <b-input-group class="input-group-merge">
              <b-form-input
                id="account-new-password"
                v-model="newPasswordValue"
                @change="verificaParola(newPasswordValue)"
                :type="passwordFieldTypeNew"
                name="new-password"
                placeholder="Parola noua"
              />
              <b-input-group-append is-text>
                <feather-icon
                  :icon="passwordToggleIconNew"
                  class="cursor-pointer"
                  @click="togglePasswordNew"
                />
              </b-input-group-append>
            </b-input-group>
          </b-form-group>
        </b-col>
        <!--/ new password -->
        </b-row>
        <b-row>
        <!-- retype password -->
        <b-col md="6">
          <b-form-group
            label-for="account-retype-new-password"
            label="Confirmare parola"
          >
            <b-input-group class="input-group-merge">
              <b-form-input
                id="account-retype-new-password"
                v-model="RetypePassword"
                :type="passwordFieldTypeRetype"
                name="retype-password"
                placeholder="Confirmare parola"
              />
              <b-input-group-append is-text>
                <feather-icon
                  :icon="passwordToggleIconRetype"
                  class="cursor-pointer"
                  @click="togglePasswordRetype"
                />
              </b-input-group-append>
            </b-input-group>
          </b-form-group>
        </b-col>
        <!--/ retype password -->

        <!-- buttons -->
        <b-col cols="12">
          <b-button
            v-ripple.400="'rgba(255, 255, 255, 0.15)'"
            variant="primary"
            class="mt-1 mr-1"
            @click="salvez"
          >
            Salvez
          </b-button>
          <b-button
            v-ripple.400="'rgba(186, 191, 199, 0.15)'"
            type="reset"
            variant="outline-secondary"
            class="mt-1"
            @click="resetPassword"
          >
            Reset
          </b-button>
        </b-col>
        <!--/ buttons -->
      </b-row>
    </b-form>
  </b-card>
</template>

<script>
import {
  BButton, BForm, BFormGroup, BFormInput, BRow, BCol, BCard, BInputGroup, BInputGroupAppend,
} from 'bootstrap-vue'
import Ripple from 'vue-ripple-directive'

export default {
  components: {
    BButton,
    BForm,
    BFormGroup,
    BFormInput,
    BRow,
    BCol,
    BCard,
    BInputGroup,
    BInputGroupAppend,
  },
  directives: {
    Ripple,
  },
  data() {
    return {
      
      newPasswordValue: '',
      RetypePassword: '',
      passwordFieldTypeNew: 'password',
      passwordFieldTypeRetype: 'password',
    }
  },
  computed: {
   
    passwordToggleIconNew() {
      return this.passwordFieldTypeNew === 'password' ? 'EyeIcon' : 'EyeOffIcon'
    },
    passwordToggleIconRetype() {
      return this.passwordFieldTypeRetype === 'password' ? 'EyeIcon' : 'EyeOffIcon'
    },
  },
  methods: {
    resetPassword(){
        this.newPasswordValue=""
        this.RetypePassword=""
      },
  containsDigit(str) {
    let digitRegex = /\d/
    return digitRegex.test(str)
  },

containsCapital(str) {
    const capitalRegex = /[A-Z]/;
    return capitalRegex.test(str);
},
containsSpecialCharacter(str) {
    const specialCharRegex = /[!@#\$%\^\&*\)\(+=._-]+/;
    return specialCharRegex.test(str);
},



    verificaParola(parola){
        if(parola.length<8) {
          this.$bvToast.toast('Lungimea minimna este de 8 caractere!', {
                                                                        title: `Atenție!`,
                                                                        variant:'warning',
                                                                        solid: false,
                                                                        appendToast: true,
                                                                        autoHideDelay: 3000,
                                                                        toaster: 'b-toaster-bottom-right',
                                                                      }) 
           return false                                                           
       }
       
       if(!this.containsDigit(parola)) {
          this.$bvToast.toast('Parola trebuie sa contina cel putin o cifra!', {
                                                                        title: `Atentie!`,
                                                                        variant:'warning',
                                                                        solid: false,
                                                                        appendToast: true,
                                                                        autoHideDelay: 3000,
                                                                        toaster: 'b-toaster-bottom-right',
                                                                      }) 
          return false                                                           
       } 
         if(!this.containsCapital(parola)) {
          this.$bvToast.toast('Parola trebuie sa contina cel putin o majuscula!', {
                                                                        title: `Atentie!`,
                                                                        variant:'warning',
                                                                        solid: false,
                                                                        appendToast: true,
                                                                        autoHideDelay: 3000,
                                                                        toaster: 'b-toaster-bottom-right',
                                                                      }) 
          return false                                                           
       } 
        if(!this.containsSpecialCharacter(parola)) {
          this.$bvToast.toast('Parola trebuie sa contina cel putin un caracter special!', {
                                                                        title: `Atentie!`,
                                                                        variant:'warning',
                                                                        solid: false,
                                                                        appendToast: true,
                                                                        autoHideDelay: 3000,
                                                                        toaster: 'b-toaster-bottom-right',
                                                                      }) 
          return false                                                           
       } 
    },
    salvez(){
             if(this.newPasswordValue==""){
               this.$bvToast.toast('Atenție! Completati parola!', {
                                                                        title: `Completati parola!`,
                                                                        variant:'warning',
                                                                        solid: false,
                                                                        appendToast: true,
                                                                        autoHideDelay: 3000,
                                                                        toaster: 'b-toaster-bottom-right',
                                                                      }) 
              
            }else{
            if(this.newPasswordValue!=this.RetypePassword){
              this.$bvToast.toast('Atenție! Parola confirmata diferita de parola!', {
                                                                        title: `Confirmare parola esuata!`,
                                                                        variant:'warning',
                                                                        solid: false,
                                                                        appendToast: true,
                                                                        autoHideDelay: 3000,
                                                                        toaster: 'b-toaster-bottom-right',
                                                                      }) 
              
            }else{
           this.verificaParola(this.newPasswordValue)
          const payLoad={}
          payLoad.requestType="post"
          payLoad.requestUrl="/utilizatori/modificaparola"
          payLoad.password=this.newPasswordValue
          this.$store.dispatch("app/api_Request",payLoad)
                    .then(response=>{
                      if(response=="PAROLA NESCHIMBATA"){
                          this.$bvToast.toast('Parola identica cu parola existenta!', {
                                                                        title: `Atentie!`,
                                                                        variant:'danger',
                                                                        solid: false,
                                                                        appendToast: true,
                                                                        autoHideDelay: 3000,
                                                                        toaster: 'b-toaster-bottom-right',
                                                                      }) 
                      }else{

                      this.$bvToast.toast('Parola a fost modificata cu succes!', {
                                                                        title: `Parola modificata cu succes!`,
                                                                        variant:'success',
                                                                        solid: false,
                                                                        appendToast: true,
                                                                        autoHideDelay: 3000,
                                                                        toaster: 'b-toaster-bottom-right',
                                                                      }) 
                          
                            this.$router.push({name:'home'})
                        }

                    })
               

            }
        }
        },
    togglePasswordNew() {
      this.passwordFieldTypeNew = this.passwordFieldTypeNew === 'password' ? 'text' : 'password'
    },
    togglePasswordRetype() {
      this.passwordFieldTypeRetype = this.passwordFieldTypeRetype === 'password' ? 'text' : 'password'
    },
  },
}
</script>
