<template>
  <div class="auth-wrapper auth-v2">
    <b-row class="auth-inner m-0">

      <!-- Brand logo-->
      <!-- <b-link class="brand-logo">
         <b-img
            fluid
            rounded
            :src="'@/assets/images/logo/logo.png'"
            alt="DianaSoft"
          />
        <h2 class="brand-text text-primary ml-1">
          {{this.app_name}}
        </h2>
      </b-link> -->
      <!-- /Brand logo-->

      <!-- Left Text-->
      <b-col
        lg="9"
        class="d-none d-lg-flex align-items-center p-0"
      >
        <div class="locul-imaginii w-100 h-100 d-lg-flex align-items-center justify-content-center">
          <b-img
            :src="imgUrl"
            alt="DianaSoft"
            class="imagine-login"
          />
        </div>
      </b-col>
      <!-- /Left Text-->

      <!-- Login-->
      <b-col
        lg="3"
        class="d-flex align-items-center auth-bg px-2 p-lg-5"
      >
        <b-col
          sm="8"
          md="6"
          lg="12"
          class="px-xl-2 mx-auto"
        >
        <!--   <b-card-title
            class="mb-1 font-weight-bold"
            title-tag="h2"
          >
            Welcome to {{this.app_name}}! 👋
          </b-card-title>
          <b-card-text class="mb-2">
            Please sign-in to your account and start the adventure
          </b-card-text> 
      
          <b-alert
            variant="primary"
            show
          >
            <div class="alert-body font-small-2">
              <p>
                <small class="mr-50"><span class="font-weight-bold">Admin:</span> admin@demo.com | admin</small>
              </p>
              <p>
                <small class="mr-50"><span class="font-weight-bold">Client:</span> client@demo.com | client</small>
              </p>
            </div>
            <feather-icon
              v-b-tooltip.hover.left="'This is just for ACL demo purpose'"
              icon="HelpCircleIcon"
              size="18"
              class="position-absolute"
              style="top: 10; right: 10;"
            />
          </b-alert>
          -->

          <!-- form -->
          <validation-observer
            ref="loginForm"
            #default="{invalid}"
          >
            <b-form
              class="auth-login-form mt-2"
              @submit.prevent="login"
            >
              <!-- email -->
              <b-form-group
                label="Email"
                label-for="login-email"
              >
                <validation-provider
                  #default="{ errors }"
                  name="Email"
                  vid="email"
                  rules="required|email"
                >
                  <b-form-input
                    id="login-email"
                    v-model="userEmail"
                    :state="errors.length > 0 ? false:null"
                    name="login-email"
                    placeholder="john@example.com"
                  />
                  <small class="text-danger">{{ errors[0] }}</small>
                </validation-provider>
              </b-form-group>

              <!-- forgot password -->
              <b-form-group>
                <div class="d-flex justify-content-between">
                  <label for="login-password">Password</label>
                  <!-- <b-link :to="{name:'auth-forgot-password'}">
                    <small>Forgot Password?</small>
                  </b-link> -->
                </div>
                <validation-provider
                  #default="{ errors }"
                  name="Password"
                  vid="password"
                  rules="required"
                >
                  <b-input-group
                    class="input-group-merge"
                    :class="errors.length > 0 ? 'is-invalid':null"
                  >
                    <b-form-input
                      id="login-password"
                      v-model="password"
                      :state="errors.length > 0 ? false:null"
                      class="form-control-merge"
                      :type="passwordFieldType"
                      name="login-password"
                      placeholder="Parola"
                    />
                    <b-input-group-append is-text>
                      <feather-icon
                        class="cursor-pointer"
                        :icon="passwordToggleIcon"
                        @click="togglePasswordVisibility"
                      />
                    </b-input-group-append>
                  </b-input-group>
                  <small class="text-danger">{{ errors[0] }}</small>
                </validation-provider>
              </b-form-group>

              <!-- checkbox -->
             <!--  <b-form-group>
                <b-form-checkbox
                  id="remember-me"
                  v-model="status"
                  name="checkbox-1"
                >
                  Remember Me
                </b-form-checkbox>
              </b-form-group> -->

              <!-- submit buttons -->
              <b-button
                type="submit"
                variant="primary"
                block
                class="buton-intra"
                :disabled="invalid"
              >
                Sign in
              </b-button>
            </b-form>
          </validation-observer>

          <!-- <b-card-text class="text-center mt-2">
            <span>New on our platform? </span>
            <b-link :to="{name:'auth-register'}">
              <span>&nbsp;Create an account</span>
            </b-link>
          </b-card-text>

           //divider
          <div class="divider my-2">
            <div class="divider-text">
              or
            </div>
          </div>

          // social buttons
          <div class="auth-footer-btn d-flex justify-content-center">
            <b-button
              variant="facebook"
              href="javascript:void(0)"
            >
              <feather-icon icon="FacebookIcon" />
            </b-button>
            <b-button
              variant="twitter"
              href="javascript:void(0)"
            >
              <feather-icon icon="TwitterIcon" />
            </b-button>
            <b-button
              variant="google"
              href="javascript:void(0)"
            >
              <feather-icon icon="MailIcon" />
            </b-button>
            <b-button
              variant="github"
              href="javascript:void(0)"
            >
              <feather-icon icon="GithubIcon" />
            </b-button>
          </div> -->
        </b-col>
      </b-col>
    <!-- /Login-->
    </b-row>
  </div>
</template>

<script>
/* eslint-disable global-require */
import { ValidationProvider, ValidationObserver } from 'vee-validate'
import VuexyLogo from '@core/layouts/components/Logo.vue'
import {
  BRow,
  BCol,
  BLink,
  BFormGroup,
  BFormInput,
  BInputGroupAppend,
  BInputGroup,
  BFormCheckbox,
  BCardText,
  BCardTitle,
  BImg,
  BForm,
  BButton,
  BAlert,
  VBTooltip,
} from 'bootstrap-vue'
import useJwt from '@/auth/jwt/useJwt'
import { required, email } from '@validations'
import { togglePasswordVisibility } from '@core/mixins/ui/forms'
import store from '@/store/index'
import { getHomeRouteForLoggedInUser } from '@/auth/utils'

import ToastificationContent from '@core/components/toastification/ToastificationContent.vue'

export default {
  directives: {
    'b-tooltip': VBTooltip,
  },
  components: {
    BRow,
    BCol,
    BLink,
    BFormGroup,
    BFormInput,
    BInputGroupAppend,
    BInputGroup,
    BFormCheckbox,
    BCardText,
    BCardTitle,
    BImg,
    BForm,
    BButton,
    BAlert,
    VuexyLogo,
    ValidationProvider,
    ValidationObserver,
  },
  mixins: [togglePasswordVisibility],
  data() {
    return {
      status: '',
      password: '',
      userEmail: '',
      sideImg: require('@/assets/images/pages/login-dianasoft.svg'),

      // validation rules
      required,
      email,
    }
  },
  computed: {
   app_name(){
            return window.app_name;
    },
    passwordToggleIcon() {
      return this.passwordFieldType === 'password' ? 'EyeIcon' : 'EyeOffIcon'
    },
    imgUrl() {
      if (store.state.appConfig.layout.skin === 'dark') {
        // eslint-disable-next-line vue/no-side-effects-in-computed-properties
        this.sideImg = require('@/assets/images/pages/login-dianasoft.svg')
        return this.sideImg
      }
      return this.sideImg
    },
  },

  created(){
      document.title=window.app_name+"->Login"
  },

  methods: {
  acceptAlert(color){
            
      
       let socCurenta=JSON.stringify(this.userCompanies.find((company)=>{return company.value===this.societateSelectata}))
   
      
     
      if (socCurenta!=undefined)
      {
        // localStorage.setItem('societateaCurenta', socCurenta)
        this.$store.dispatch('app/societateaCurenta',socCurenta)
                  .then(response=>{
                                  this.getCookiesLocal()
                                
                  })
        
       
      

      }
       else{
               this.$store.dispatch('app/destroyToken')
                  .then(response=>{
                    this.activePrompt=false
                  })
         //this.$router.push({name:'pageLogout'}) 
       }
       
    },
    close(){
      
      this.$store.dispatch('app/destroyToken')
          .then(response=>{
            this.activePrompt=false
          })
     
    },
getCookiesLocal(){
            var date = new Date();
            var firstDay = new Date(date.getFullYear(), date.getMonth(), 1);
            this.$store.dispatch('app/lunaCurenta',firstDay)
            
            this.$globalHelpers.updateCookiesLocal()
            .then(response=>{
                 
                this.activePrompt=false
              
                
                 if((new Date(this.userData.data_expirare_parola))<=(new Date())){

                                   this.$router.replace({name:'pages-account-setting'}).then(()=>{
                                              this.$bvToast.toast("Parola expirata, va rugam , modificati parola!", 
                                                                                                         {
                                                                                                            title: `ATENTIE! `,
                                                                                                            variant:"danger",
                                                                                                            solid: true,
                                                                                                            appendToast: false,
                                                                                                            noAutoHide:true,
                                                                                                            toaster: "b-toaster-top-right",
                                                                                                                              }) 
                                   })   
                  }else{
                      this.$router.replace(getHomeRouteForLoggedInUser('admin')) 

                  }
            })
            .catch(error => {
              this.$refs.loginForm.setErrors(error.response.data.error)
            })
                        // this.$router.push({name: 'dashboard-ecommerce'})
                        // this.$router.replace('/')
            //             this.$toast({
            //                                               component: ToastificationContent,
            //                                               position: 'top-right',
            //                                               props: {
            //                                                 title: `Welcome ${this.userData.name }`,
            //                                                 icon: 'CoffeeIcon',
            //                                                 variant: 'success',
            //                                                 text: `You have successfully logged in as ${this.userData.name}. Now you can start to explore!`,
            //                                               },
                                                        
            //                                           })
            // })

                        
           
        },
    login() {
       this.$refs.loginForm.validate().then(success => {
        if (success) {
          
          //this.$vs.loading({color:"success"})
         this.activePrompt=false
         
         this.$store.dispatch('app/retrieveToken',{
                                                  email: this.userEmail,
                                                  password:this.password,
                                                  })
                    .then(response=>{
                         
                         this.$store.dispatch('app/retrieveUser')
                          .then(response=>{
                                 
                               this.userData  = response.data
                               this.userData.role="admin"
                               if(response.data.companies.length>1)  //LUCREAZA PE MAI MULTE SOCIETATI 
                               {
                                let companies=[];
                                 response.data.companies.forEach(function(company,key){
                                        
                                        company.text=company.denumire
                                        company.value=company.id
                                        companies.push(company)
                                        
                                    })
                                this.userCompanies=companies
                                this.activePrompt=true

                               }else{
                                if(response.data.companies.length==1) //LUCREAZA PE O SOCIETATE
                               {
                                 const socCurenta=JSON.stringify(response.data.companies[0])
                                   
                                 this.$store.dispatch('app/societateaCurenta',socCurenta)
                                    .then(response=>{
                                                      this.getCookiesLocal()
                                                      
                                                      
                                                    })
                              
                                }
                                else                        //NU ARE SETATA NICIO SOCIETATE
                                {
                                   /*
                                     this.$vs.notify({
                                            color:'warning',
                                            title:'User fără societate',
                                            text:'Userul dumneavoastră nu are asociata nicio societate! <br /> Luați legătura cu administratorul aplicației!'
                                          }) */
                                  this.$router.replace({name:'pageLogout'})   
                                }
                               
                               
                               
                                }
                           
                            })
                           .catch(error => {
          
                                 this.$bvToast.toast("Credentiale incorecte!", 
                                                                                   {
                                                                                      title: `ATENTIE! `,
                                                                                      variant:"danger",
                                                                                      solid: true,
                                                                                      appendToast: false,
                                                                                      noAutoHide:true,
                                                                                      toaster: "b-toaster-top-right",
                                                                                                        }) 
                                   

                              }) 

            
          })
          .catch(error => {
          
           this.$bvToast.toast("Credentiale incorecte!", 
                                                             {
                                                                title: `ATENTIE! `,
                                                                variant:"danger",
                                                                solid: true,
                                                                appendToast: false,
                                                                noAutoHide:true,
                                                                toaster: "b-toaster-top-right",
                                                                                  }) 
             

          })
          }
        })

      }
   }
}
</script>

<style lang="scss">
@import '~@core/scss/vue/pages/page-auth.scss';
</style>

<style lang="scss" scoped>
// Culorile din desenul de alături, ca pagina să fie dintr-o bucată.
$albastru-diana: #3b82f6;
$fundal-diana: #0d1729;

/*
  Desenul ocupă tot locul care i se dă, cât se poate fără să se deformeze.
  „contain" alege singur latura care se lovește prima de margine, iar fundalul
  de sub el poartă chiar culoarea din desen — așa marginile rămase, oricât de
  late ar fi ecranul, se pierd în el în loc să se vadă ca niște benzi albe.
*/
.locul-imaginii {
  background-color: $fundal-diana;
}

.imagine-login {
  width: 100%;
  height: 100%;
  max-width: 100%;
  max-height: 100%;
  object-fit: contain;
}

/*
  Butonul poartă albastrul din desen în toate stările lui.

  „!important" nu e din lene: tema scrie chiar ea
  „.btn-primary { background-color: #7367f0 !important }", iar unei reguli cu
  „!important" nu i se poate răspunde decât la fel, oricât de precis ai ținti.

  Starea „.disabled" e scrisă anume: formularul pornește gol, deci butonul se
  vede întâi așa.
*/
.buton-intra {
  &,
  &.disabled,
  &:disabled,
  &:not(:disabled):not(.disabled) {
    background-color: $albastru-diana !important;
    border-color: $albastru-diana !important;
  }

  // Umbra de la trecerea cu mouse-ul poartă aceeași culoare, altfel ar lumina mov.
  &:hover:not(:disabled):not(.disabled) {
    background-color: darken($albastru-diana, 7%) !important;
    border-color: darken($albastru-diana, 7%) !important;
    box-shadow: 0 8px 25px -8px $albastru-diana;
  }

  &:focus,
  &:active,
  &:not(:disabled):not(.disabled):active,
  &:not(:disabled):not(.disabled):focus {
    background-color: darken($albastru-diana, 12%) !important;
    border-color: darken($albastru-diana, 12%) !important;
    box-shadow: none;
  }
}
</style>
