(window["webpackJsonp"] = window["webpackJsonp"] || []).push([[13],{

/***/ "./node_modules/@babel/runtime/helpers/esm/typeof.js":
/*!***********************************************************!*\
  !*** ./node_modules/@babel/runtime/helpers/esm/typeof.js ***!
  \***********************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return _typeof; });
function _typeof(obj) {
  "@babel/helpers - typeof";

  if (typeof Symbol === "function" && typeof Symbol.iterator === "symbol") {
    _typeof = function _typeof(obj) {
      return typeof obj;
    };
  } else {
    _typeof = function _typeof(obj) {
      return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj;
    };
  }

  return _typeof(obj);
}

/***/ }),

/***/ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/@core/layouts/components/AppBreadcrumb.vue?vue&type=script&lang=js&":
/*!******************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib??ref--4-0!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/src/@core/layouts/components/AppBreadcrumb.vue?vue&type=script&lang=js& ***!
  \******************************************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var bootstrap_vue__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! bootstrap-vue */ "./node_modules/bootstrap-vue/esm/index.js");
/* harmony import */ var vue_ripple_directive__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! vue-ripple-directive */ "./node_modules/vue-ripple-directive/src/ripple.js");
/* harmony import */ var _core_libs_i18n__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @core/libs/i18n */ "./resources/js/src/@core/libs/i18n/index.js");
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//



/* harmony default export */ __webpack_exports__["default"] = ({
  directives: {
    Ripple: vue_ripple_directive__WEBPACK_IMPORTED_MODULE_1__["default"]
  },
  components: {
    BBreadcrumb: bootstrap_vue__WEBPACK_IMPORTED_MODULE_0__["BBreadcrumb"],
    BBreadcrumbItem: bootstrap_vue__WEBPACK_IMPORTED_MODULE_0__["BBreadcrumbItem"],
    BRow: bootstrap_vue__WEBPACK_IMPORTED_MODULE_0__["BRow"],
    BCol: bootstrap_vue__WEBPACK_IMPORTED_MODULE_0__["BCol"],
    BDropdown: bootstrap_vue__WEBPACK_IMPORTED_MODULE_0__["BDropdown"],
    BDropdownItem: bootstrap_vue__WEBPACK_IMPORTED_MODULE_0__["BDropdownItem"],
    BButton: bootstrap_vue__WEBPACK_IMPORTED_MODULE_0__["BButton"]
  },
  setup: function setup() {
    var _useI18nUtils = Object(_core_libs_i18n__WEBPACK_IMPORTED_MODULE_2__["useUtils"])(),
        t = _useI18nUtils.t;

    return {
      // i18n
      t: t
    };
  }
});

/***/ }),

/***/ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/@core/layouts/components/AppFooter.vue?vue&type=script&lang=js&":
/*!**************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib??ref--4-0!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/src/@core/layouts/components/AppFooter.vue?vue&type=script&lang=js& ***!
  \**************************************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var bootstrap_vue__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! bootstrap-vue */ "./node_modules/bootstrap-vue/esm/index.js");
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//

/* harmony default export */ __webpack_exports__["default"] = ({
  components: {
    BLink: bootstrap_vue__WEBPACK_IMPORTED_MODULE_0__["BLink"]
  }
});

/***/ }),

/***/ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/@core/layouts/components/app-customizer/AppCustomizer.vue?vue&type=script&lang=js&":
/*!*********************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib??ref--4-0!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/src/@core/layouts/components/app-customizer/AppCustomizer.vue?vue&type=script&lang=js& ***!
  \*********************************************************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var core_js_modules_es_array_find_index_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! core-js/modules/es.array.find-index.js */ "./node_modules/core-js/modules/es.array.find-index.js");
/* harmony import */ var core_js_modules_es_array_find_index_js__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_es_array_find_index_js__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var bootstrap_vue__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! bootstrap-vue */ "./node_modules/bootstrap-vue/esm/index.js");
/* harmony import */ var vue_select__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! vue-select */ "./node_modules/vue-select/dist/vue-select.js");
/* harmony import */ var vue_select__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(vue_select__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var vue_perfect_scrollbar__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! vue-perfect-scrollbar */ "./node_modules/vue-perfect-scrollbar/dist/index.js");
/* harmony import */ var vue_perfect_scrollbar__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(vue_perfect_scrollbar__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _useAppCustomizer__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ./useAppCustomizer */ "./resources/js/src/@core/layouts/components/app-customizer/useAppCustomizer.js");

//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
// eslint-disable-next-line object-curly-newline




/* harmony default export */ __webpack_exports__["default"] = ({
  components: {
    // BSV
    BLink: bootstrap_vue__WEBPACK_IMPORTED_MODULE_1__["BLink"],
    BFormRadioGroup: bootstrap_vue__WEBPACK_IMPORTED_MODULE_1__["BFormRadioGroup"],
    BFormCheckbox: bootstrap_vue__WEBPACK_IMPORTED_MODULE_1__["BFormCheckbox"],
    BFormGroup: bootstrap_vue__WEBPACK_IMPORTED_MODULE_1__["BFormGroup"],
    // 3rd party
    vSelect: vue_select__WEBPACK_IMPORTED_MODULE_2___default.a,
    VuePerfectScrollbar: vue_perfect_scrollbar__WEBPACK_IMPORTED_MODULE_3___default.a
  },
  setup: function setup() {
    var _useAppCustomizer = Object(_useAppCustomizer__WEBPACK_IMPORTED_MODULE_4__["default"])(),
        isVerticalMenuCollapsed = _useAppCustomizer.isVerticalMenuCollapsed,
        isCustomizerOpen = _useAppCustomizer.isCustomizerOpen,
        skin = _useAppCustomizer.skin,
        skinOptions = _useAppCustomizer.skinOptions,
        contentWidth = _useAppCustomizer.contentWidth,
        contentWidthOptions = _useAppCustomizer.contentWidthOptions,
        isRTL = _useAppCustomizer.isRTL,
        routerTransition = _useAppCustomizer.routerTransition,
        routerTransitionOptions = _useAppCustomizer.routerTransitionOptions,
        layoutType = _useAppCustomizer.layoutType,
        layoutTypeOptions = _useAppCustomizer.layoutTypeOptions,
        isNavMenuHidden = _useAppCustomizer.isNavMenuHidden,
        navbarColors = _useAppCustomizer.navbarColors,
        navbarTypes = _useAppCustomizer.navbarTypes,
        navbarBackgroundColor = _useAppCustomizer.navbarBackgroundColor,
        navbarType = _useAppCustomizer.navbarType,
        footerTypes = _useAppCustomizer.footerTypes,
        footerType = _useAppCustomizer.footerType;

    if (layoutType.value === 'horizontal') {
      // Remove semi-dark skin option in horizontal layout
      var skinSemiDarkIndex = skinOptions.findIndex(function (s) {
        return s.value === 'semi-dark';
      });
      delete skinOptions[skinSemiDarkIndex]; // Remove menu hidden radio in horizontal layout => As we already have switch for it

      var menuHiddneIndex = navbarTypes.findIndex(function (t) {
        return t.value === 'hidden';
      });
      delete navbarTypes[menuHiddneIndex];
    } // Perfect Scrollbar


    var perfectScrollbarSettings = {
      maxScrollbarLength: 60,
      wheelPropagation: false
    };
    return {
      // Vertical Menu
      isVerticalMenuCollapsed: isVerticalMenuCollapsed,
      // Customizer
      isCustomizerOpen: isCustomizerOpen,
      // Skin
      skin: skin,
      skinOptions: skinOptions,
      // Content Width
      contentWidth: contentWidth,
      contentWidthOptions: contentWidthOptions,
      // RTL
      isRTL: isRTL,
      // routerTransition
      routerTransition: routerTransition,
      routerTransitionOptions: routerTransitionOptions,
      // Layout Type
      layoutType: layoutType,
      layoutTypeOptions: layoutTypeOptions,
      // NavMenu Hidden
      isNavMenuHidden: isNavMenuHidden,
      // Navbar
      navbarColors: navbarColors,
      navbarTypes: navbarTypes,
      navbarBackgroundColor: navbarBackgroundColor,
      navbarType: navbarType,
      // Footer
      footerTypes: footerTypes,
      footerType: footerType,
      // Perfect Scrollbar
      perfectScrollbarSettings: perfectScrollbarSettings
    };
  }
});

/***/ }),

/***/ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/@core/layouts/components/app-navbar/components/Module.vue?vue&type=script&lang=js&":
/*!*********************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib??ref--4-0!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/src/@core/layouts/components/app-navbar/components/Module.vue?vue&type=script&lang=js& ***!
  \*********************************************************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var C_dianasoft_serviciiweb_web_node_modules_babel_runtime_helpers_esm_toConsumableArray_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./node_modules/@babel/runtime/helpers/esm/toConsumableArray.js */ "./node_modules/@babel/runtime/helpers/esm/toConsumableArray.js");
/* harmony import */ var core_js_modules_es_array_concat_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! core-js/modules/es.array.concat.js */ "./node_modules/core-js/modules/es.array.concat.js");
/* harmony import */ var core_js_modules_es_array_concat_js__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_es_array_concat_js__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var core_js_modules_es_function_name_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! core-js/modules/es.function.name.js */ "./node_modules/core-js/modules/es.function.name.js");
/* harmony import */ var core_js_modules_es_function_name_js__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_es_function_name_js__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var bootstrap_vue__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! bootstrap-vue */ "./node_modules/bootstrap-vue/esm/index.js");



//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//

/*
 * Modulele aplicației, în antet, cu sigla fiecăruia.
 *
 * Au luat locul denumirii firmei și al numelui aplicației cu versiunea: acolo
 * stătea o informație pe care omul o știe oricum, iar acum stă drumul spre
 * lucrul pe care vrea să-l facă.
 *
 * Se folosește varianta „simbol": la înălțimea unui antet, sigla orizontală ar
 * scrie numele modulului cu litere de câțiva pixeli, ilizibile. Numele stă
 * alături, ca text, și dispare pe ecrane înguste — sigla rămâne.
 */

/* harmony default export */ __webpack_exports__["default"] = ({
  components: {
    BLink: bootstrap_vue__WEBPACK_IMPORTED_MODULE_3__["BLink"],
    BImg: bootstrap_vue__WEBPACK_IMPORTED_MODULE_3__["BImg"]
  },
  data: function data() {
    return {
      module: [{
        nume: 'SPV Curier',
        ruta: 'spv',
        descriere: 'Declarații, mesaje și solicitări în Spațiul Privat Virtual',
        // eslint-disable-next-line global-require, import/no-unresolved
        sigla: __webpack_require__(/*! @/assets/images/sigle/spv-curier-simbol.svg */ "./resources/js/src/assets/images/sigle/spv-curier-simbol.svg")
      }, {
        nume: 'Dispecer e-Transport',
        ruta: 'etransport',
        descriere: 'Declararea transporturilor și urmărirea UIT-urilor',
        // eslint-disable-next-line global-require, import/no-unresolved
        sigla: __webpack_require__(/*! @/assets/images/sigle/dispecer-simbol.svg */ "./resources/js/src/assets/images/sigle/dispecer-simbol.svg")
      }, {
        nume: 'Grefier alert',
        ruta: 'portal-just',
        descriere: 'Dosare, termene și monitorizare în Portal Just',
        // eslint-disable-next-line global-require, import/no-unresolved
        sigla: __webpack_require__(/*! @/assets/images/sigle/grefier-simbol.svg */ "./resources/js/src/assets/images/sigle/grefier-simbol.svg")
      }],
      administrare: {
        nume: 'Administrare clienți',
        ruta: 'administrare',
        descriere: 'Firme, conturi, module, tarife și perioade de probă',
        pictograma: 'UsersIcon'
      },
      esteAdministrator: false
    };
  },
  computed: {
    /** Administrarea apare doar la contul care are dreptul. */
    vizibile: function vizibile() {
      return this.esteAdministrator ? [].concat(Object(C_dianasoft_serviciiweb_web_node_modules_babel_runtime_helpers_esm_toConsumableArray_js__WEBPACK_IMPORTED_MODULE_0__["default"])(this.module), [this.administrare]) : this.module;
    }
  },
  created: function created() {
    var _this = this;

    // Serverul spune cine e; dreptul de administrare nu se ține în browser.
    this.$http.get('/context').then(function (_ref) {
      var data = _ref.data;
      _this.esteAdministrator = Boolean(data.data.super_admin);
    })["catch"](function () {
      _this.esteAdministrator = false;
    });
  },
  methods: {
    esteActiv: function esteActiv(modul) {
      return this.$route.name === modul.ruta;
    },

    /** Apăsat pe modulul în care ești deja, nu are rost să reîncarce pagina. */
    mergiLa: function mergiLa(modul) {
      if (this.esteActiv(modul)) return;
      this.$router.push({
        name: modul.ruta
      })["catch"](function () {});
    }
  }
});

/***/ }),

/***/ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/@core/layouts/components/app-navbar/components/NotificationDropdown.vue?vue&type=script&lang=js&":
/*!***********************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib??ref--4-0!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/src/@core/layouts/components/app-navbar/components/NotificationDropdown.vue?vue&type=script&lang=js& ***!
  \***********************************************************************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var core_js_modules_es_array_filter_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! core-js/modules/es.array.filter.js */ "./node_modules/core-js/modules/es.array.filter.js");
/* harmony import */ var core_js_modules_es_array_filter_js__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_es_array_filter_js__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var core_js_modules_es_object_to_string_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! core-js/modules/es.object.to-string.js */ "./node_modules/core-js/modules/es.object.to-string.js");
/* harmony import */ var core_js_modules_es_object_to_string_js__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_es_object_to_string_js__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var bootstrap_vue__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! bootstrap-vue */ "./node_modules/bootstrap-vue/esm/index.js");
/* harmony import */ var vue_perfect_scrollbar__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! vue-perfect-scrollbar */ "./node_modules/vue-perfect-scrollbar/dist/index.js");
/* harmony import */ var vue_perfect_scrollbar__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(vue_perfect_scrollbar__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var vue_ripple_directive__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! vue-ripple-directive */ "./node_modules/vue-ripple-directive/src/ripple.js");


//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//



/* harmony default export */ __webpack_exports__["default"] = ({
  components: {
    BNavItemDropdown: bootstrap_vue__WEBPACK_IMPORTED_MODULE_2__["BNavItemDropdown"],
    BBadge: bootstrap_vue__WEBPACK_IMPORTED_MODULE_2__["BBadge"],
    BMedia: bootstrap_vue__WEBPACK_IMPORTED_MODULE_2__["BMedia"],
    BLink: bootstrap_vue__WEBPACK_IMPORTED_MODULE_2__["BLink"],
    BAvatar: bootstrap_vue__WEBPACK_IMPORTED_MODULE_2__["BAvatar"],
    VuePerfectScrollbar: vue_perfect_scrollbar__WEBPACK_IMPORTED_MODULE_3___default.a,
    BButton: bootstrap_vue__WEBPACK_IMPORTED_MODULE_2__["BButton"],
    BFormCheckbox: bootstrap_vue__WEBPACK_IMPORTED_MODULE_2__["BFormCheckbox"]
  },
  directives: {
    Ripple: vue_ripple_directive__WEBPACK_IMPORTED_MODULE_4__["default"]
  },
  data: function data() {
    return {
      notifications: [{}],
      notificariFiltrate: [{}],
      notificationGrup: [{}],
      idGrup: "",
      newMessages: 0,
      titluGrup: "",
      nrMesajeGrup: 0,
      showAlerte: false,
      perfectScrollbarSettings: {
        maxScrollbarLength: 60,
        wheelPropagation: false
      }
    };
  },
  methods: {
    vizualizeazaGrup: function vizualizeazaGrup() {
      this.showAlerte = false;
      this.idGrup = "";
    },
    afisezAlerteGrup: function afisezAlerteGrup(grupNotificare) {
      this.notificariFiltrate = this.notifications.filter(function (t) {
        return t.notificationtype_id == grupNotificare.notificationtype_id;
      });
      this.nrMesajeGrup = grupNotificare.nrMesaje;
      this.titluGrup = grupNotificare.notificationtype.denumire;
      this.idGrup = grupNotificare.notificationtype_id;
      this.showAlerte = true;
    },
    listen: function listen() {
      /*  Echo.channel("bancomatic_dianasoft")
                    .listen("NotificationEvents", (e) => {
                       this.notifications.unshift(e.notificare)
                       this.newMessages++;
                       console.log(e)
                       console.log("AM PRIMIT NOTIFICARE")
                       if(e.eveniment=="Notificare noua"){
                        this.contacts.map((t)=>{
                          if(t.id==e.user_id){
                            t.status=e.status
                            this.$store.dispatch('updateUserInfo', {id:t.id,status: e.status})
                          }
                        })
                       }
                         if(e.eveniment=="Transmite mesaj"){
                        let payload=e
                        // payload.id=e.user_id
                        
                        this.$store.dispatch('chat/receiveChatMessage', payload)
                        // this.contacts.map((t)=>{
                        //   if(t.id==e.user_id){
                        //     t.status_chat=e.status_chat
                        //     this.$store.dispatch('updateUserInfo', {id:t.id,status_chat: e.status_chat})
                        //   }
                        // })
                       }
                       });
               */
    },
    markAsRead: function markAsRead(id) {
      var _this = this;

      var payLoad = {};
      payLoad.requestType = "post";
      payLoad.requestUrl = "/markasread";
      payLoad.id = id;
      payLoad.notificationtype_id = this.idGrup;
      this.$store.dispatch("app/api_Request", payLoad).then(function (response) {
        /*
            if(id!=null){
             this.newMessages=this.newMessages-1
           }else{
             this.newMessages=0
             } */
        _this.notifications = response.notificari;
        _this.notificationGrup = response.notificariGrup;
        _this.newMessages = response["new"];

        if (_this.showAlerte) {
          var grupCurent = _this.notificationGrup.filter(function (t) {
            return t.notificationtype_id == _this.idGrup;
          });

          if (grupCurent.length > 0) {
            _this.nrMesajeGrup = grupCurent[0].nrMesaje;
            _this.notificariFiltrate = _this.notifications.filter(function (t) {
              return t.notificationtype_id == _this.idGrup;
            });
          } else {
            _this.showAlerte = false;
            _this.idGrup = "";
          }
        }
      });
    },
    getNotificationList: function getNotificationList() {
      var _this2 = this;

      var payLoad = {};
      payLoad.requestType = "get";
      payLoad.requestUrl = "/notificariusercurent";
      this.$store.dispatch("app/api_Request", payLoad).then(function (response) {
        // this.notifications=response.data
        _this2.notifications = response.notificari;
        _this2.notificationGrup = response.notificariGrup;
        _this2.newMessages = response["new"];
      });
    }
  },
  beforeMount: function beforeMount() {
    this.getNotificationList();
  },
  created: function created() {
    this.getNotificationList();
    this.listen();
  }
  /*setup() {
    
    const notifications = [
      {
        title: 'Congratulation Sam 🎉',
        avatar: require('@/assets/images/avatars/6-small.png'),
        subtitle: 'Won the monthly best seller badge',
        type: 'light-success',
      },
      {
        title: 'New message received',
        avatar: require('@/assets/images/avatars/9-small.png'),
        subtitle: 'You have 10 unread messages',
        type: 'light-info',
      },
      {
        title: 'Revised Order 👋',
        avatar: 'MD',
        subtitle: 'MD Inc. order updated',
        type: 'light-danger',
      },
    ]
    
      const systemNotifications = [
      {
        title: 'Server down',
        subtitle: 'USA Server is down due to hight CPU usage',
        type: 'light-danger',
        icon: 'XIcon',
      },
      {
        title: 'Sales report generated',
        subtitle: 'Last month sales report generated',
        type: 'light-success',
        icon: 'CheckIcon',
      },
      {
        title: 'High memory usage',
        subtitle: 'BLR Server using high memory',
        type: 'light-warning',
        icon: 'AlertTriangleIcon',
      },
    ]
    */

});

/***/ }),

/***/ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/@core/layouts/components/app-navbar/components/UserDropdown.vue?vue&type=script&lang=js&":
/*!***************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib??ref--4-0!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/src/@core/layouts/components/app-navbar/components/UserDropdown.vue?vue&type=script&lang=js& ***!
  \***************************************************************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var bootstrap_vue__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! bootstrap-vue */ "./node_modules/bootstrap-vue/esm/index.js");
/* harmony import */ var _store__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @/store */ "./resources/js/src/store/index.js");
/* harmony import */ var _libs_acl_config__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @/libs/acl/config */ "./resources/js/src/libs/acl/config.js");
/* harmony import */ var _auth_jwt_useJwt__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @/auth/jwt/useJwt */ "./resources/js/src/auth/jwt/useJwt.js");
/* harmony import */ var _core_utils_filter__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! @core/utils/filter */ "./resources/js/src/@core/utils/filter.js");
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//





/* harmony default export */ __webpack_exports__["default"] = ({
  components: {
    BNavItemDropdown: bootstrap_vue__WEBPACK_IMPORTED_MODULE_0__["BNavItemDropdown"],
    BDropdownItem: bootstrap_vue__WEBPACK_IMPORTED_MODULE_0__["BDropdownItem"],
    BDropdownDivider: bootstrap_vue__WEBPACK_IMPORTED_MODULE_0__["BDropdownDivider"],
    BAvatar: bootstrap_vue__WEBPACK_IMPORTED_MODULE_0__["BAvatar"]
  },
  data: function data() {
    return {
      userData: JSON.parse(localStorage.getItem('user')),
      avatarText: _core_utils_filter__WEBPACK_IMPORTED_MODULE_4__["avatarText"]
    };
  },
  watch: {
    '$store.state.app.user': function $storeStateAppUser() {
      this.userData = JSON.parse(_store__WEBPACK_IMPORTED_MODULE_1__["default"].state.app.user);
    }
  },
  methods: {
    logout: function logout() {
      localStorage.clear(); // Remove userData from localStorage
      // ? You just removed token from localStorage. If you like, you can also make API call to backend to blacklist used token
      // localStorage.removeItem(useJwt.jwtConfig.storageTokenKeyName)
      //  localStorage.removeItem(useJwt.jwtConfig.storageRefreshTokenKeyName)
      // Remove userData from localStorage
      //   localStorage.removeItem('user')
      // Reset ability

      this.$ability.update(_libs_acl_config__WEBPACK_IMPORTED_MODULE_2__["initialAbility"]); // Redirect to login page

      this.$router.push({
        name: 'auth-login'
      });
    }
  }
});

/***/ }),

/***/ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/@core/layouts/components/layout-content-renderer/LayoutContentRendererDefault.vue?vue&type=script&lang=js&":
/*!*********************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib??ref--4-0!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/src/@core/layouts/components/layout-content-renderer/LayoutContentRendererDefault.vue?vue&type=script&lang=js& ***!
  \*********************************************************************************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _core_layouts_components_AppBreadcrumb_vue__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @core/layouts/components/AppBreadcrumb.vue */ "./resources/js/src/@core/layouts/components/AppBreadcrumb.vue");
/* harmony import */ var _core_app_config_useAppConfig__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @core/app-config/useAppConfig */ "./resources/js/src/@core/app-config/useAppConfig.js");
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//


/* harmony default export */ __webpack_exports__["default"] = ({
  components: {
    AppBreadcrumb: _core_layouts_components_AppBreadcrumb_vue__WEBPACK_IMPORTED_MODULE_0__["default"]
  },
  setup: function setup() {
    var _useAppConfig = Object(_core_app_config_useAppConfig__WEBPACK_IMPORTED_MODULE_1__["default"])(),
        routerTransition = _useAppConfig.routerTransition,
        contentWidth = _useAppConfig.contentWidth;

    return {
      routerTransition: routerTransition,
      contentWidth: contentWidth
    };
  }
});

/***/ }),

/***/ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/@core/layouts/components/layout-content-renderer/LayoutContentRendererLeft.vue?vue&type=script&lang=js&":
/*!******************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib??ref--4-0!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/src/@core/layouts/components/layout-content-renderer/LayoutContentRendererLeft.vue?vue&type=script&lang=js& ***!
  \******************************************************************************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _core_layouts_components_AppBreadcrumb_vue__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @core/layouts/components/AppBreadcrumb.vue */ "./resources/js/src/@core/layouts/components/AppBreadcrumb.vue");
/* harmony import */ var _core_app_config_useAppConfig__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @core/app-config/useAppConfig */ "./resources/js/src/@core/app-config/useAppConfig.js");
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//


/* harmony default export */ __webpack_exports__["default"] = ({
  components: {
    AppBreadcrumb: _core_layouts_components_AppBreadcrumb_vue__WEBPACK_IMPORTED_MODULE_0__["default"]
  },
  setup: function setup() {
    var _useAppConfig = Object(_core_app_config_useAppConfig__WEBPACK_IMPORTED_MODULE_1__["default"])(),
        routerTransition = _useAppConfig.routerTransition,
        contentWidth = _useAppConfig.contentWidth;

    return {
      routerTransition: routerTransition,
      contentWidth: contentWidth
    };
  }
});

/***/ }),

/***/ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/@core/layouts/components/layout-content-renderer/LayoutContentRendererLeftDetached.vue?vue&type=script&lang=js&":
/*!**************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib??ref--4-0!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/src/@core/layouts/components/layout-content-renderer/LayoutContentRendererLeftDetached.vue?vue&type=script&lang=js& ***!
  \**************************************************************************************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _core_layouts_components_AppBreadcrumb_vue__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @core/layouts/components/AppBreadcrumb.vue */ "./resources/js/src/@core/layouts/components/AppBreadcrumb.vue");
/* harmony import */ var _core_app_config_useAppConfig__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @core/app-config/useAppConfig */ "./resources/js/src/@core/app-config/useAppConfig.js");
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//


/* harmony default export */ __webpack_exports__["default"] = ({
  components: {
    AppBreadcrumb: _core_layouts_components_AppBreadcrumb_vue__WEBPACK_IMPORTED_MODULE_0__["default"]
  },
  setup: function setup() {
    var _useAppConfig = Object(_core_app_config_useAppConfig__WEBPACK_IMPORTED_MODULE_1__["default"])(),
        routerTransition = _useAppConfig.routerTransition,
        contentWidth = _useAppConfig.contentWidth;

    return {
      routerTransition: routerTransition,
      contentWidth: contentWidth
    };
  }
});

/***/ }),

/***/ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/@core/layouts/layout-vertical/components/vertical-nav-menu/VerticalNavMenu.vue?vue&type=script&lang=js&":
/*!******************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib??ref--4-0!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/src/@core/layouts/layout-vertical/components/vertical-nav-menu/VerticalNavMenu.vue?vue&type=script&lang=js& ***!
  \******************************************************************************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var core_js_modules_es_array_filter_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! core-js/modules/es.array.filter.js */ "./node_modules/core-js/modules/es.array.filter.js");
/* harmony import */ var core_js_modules_es_array_filter_js__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_es_array_filter_js__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var core_js_modules_es_object_to_string_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! core-js/modules/es.object.to-string.js */ "./node_modules/core-js/modules/es.object.to-string.js");
/* harmony import */ var core_js_modules_es_object_to_string_js__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_es_object_to_string_js__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var core_js_modules_web_dom_collections_for_each_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! core-js/modules/web.dom-collections.for-each.js */ "./node_modules/core-js/modules/web.dom-collections.for-each.js");
/* harmony import */ var core_js_modules_web_dom_collections_for_each_js__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_web_dom_collections_for_each_js__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var core_js_modules_es_function_name_js__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! core-js/modules/es.function.name.js */ "./node_modules/core-js/modules/es.function.name.js");
/* harmony import */ var core_js_modules_es_function_name_js__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_es_function_name_js__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var vue_perfect_scrollbar__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! vue-perfect-scrollbar */ "./node_modules/vue-perfect-scrollbar/dist/index.js");
/* harmony import */ var vue_perfect_scrollbar__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(vue_perfect_scrollbar__WEBPACK_IMPORTED_MODULE_4__);
/* harmony import */ var bootstrap_vue__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! bootstrap-vue */ "./node_modules/bootstrap-vue/esm/index.js");
/* harmony import */ var _vue_composition_api__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! @vue/composition-api */ "./node_modules/@vue/composition-api/dist/vue-composition-api.mjs");
/* harmony import */ var _core_app_config_useAppConfig__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! @core/app-config/useAppConfig */ "./resources/js/src/@core/app-config/useAppConfig.js");
/* harmony import */ var _themeConfig__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! @themeConfig */ "./resources/js/themeConfig.js");
/* harmony import */ var _components_vertical_nav_menu_items_VerticalNavMenuItems_vue__WEBPACK_IMPORTED_MODULE_9__ = __webpack_require__(/*! ./components/vertical-nav-menu-items/VerticalNavMenuItems.vue */ "./resources/js/src/@core/layouts/layout-vertical/components/vertical-nav-menu/components/vertical-nav-menu-items/VerticalNavMenuItems.vue");
/* harmony import */ var _useVerticalNavMenu__WEBPACK_IMPORTED_MODULE_10__ = __webpack_require__(/*! ./useVerticalNavMenu */ "./resources/js/src/@core/layouts/layout-vertical/components/vertical-nav-menu/useVerticalNavMenu.js");
/* harmony import */ var _store__WEBPACK_IMPORTED_MODULE_11__ = __webpack_require__(/*! @/store */ "./resources/js/src/store/index.js");
/* harmony import */ var _router__WEBPACK_IMPORTED_MODULE_12__ = __webpack_require__(/*! @/router */ "./resources/js/src/router/index.js");




//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//









/* harmony default export */ __webpack_exports__["default"] = ({
  components: {
    VuePerfectScrollbar: vue_perfect_scrollbar__WEBPACK_IMPORTED_MODULE_4___default.a,
    VerticalNavMenuItems: _components_vertical_nav_menu_items_VerticalNavMenuItems_vue__WEBPACK_IMPORTED_MODULE_9__["default"],
    BLink: bootstrap_vue__WEBPACK_IMPORTED_MODULE_5__["BLink"],
    BImg: bootstrap_vue__WEBPACK_IMPORTED_MODULE_5__["BImg"]
  },
  props: {
    isVerticalMenuActive: {
      type: Boolean,
      required: true
    },
    toggleVerticalMenuActive: {
      type: Function,
      required: true
    }
    /*   navMenuItems: {
         type: Array,
         required: true,
       },
       */

  },
  watch: {
    '$store.state.app.user': function $storeStateAppUser() {
      this.creezmenu();
    }
  },
  data: function data() {
    return {
      navMenuItems: []
    };
  },
  created: function created() {
    this.creezmenu();
  },
  methods: {
    creezmenu: function creezmenu() {
      var company_id = JSON.parse(_store__WEBPACK_IMPORTED_MODULE_11__["default"].state.app.societateaCurenta).id;
      var menuoptions = JSON.parse(_store__WEBPACK_IMPORTED_MODULE_11__["default"].state.app.user).dianasoftmenuoptions.filter(function (e) {
        return e.pivot.company_id == company_id;
      });
      var menu = this.createMenuOptions(menuoptions, "\\");
      this.navMenuItems = menu;
    },
    createMenuOptions: function createMenuOptions(menuoptions, parinte) {
      var _this = this;

      var menuoptionsGroup = menuoptions.filter(function (menuoption) {
        return menuoption.parent === parinte;
      });
      var menu = [];
      menuoptionsGroup.forEach(function (value, index) {
        var menuoption = {};

        if (value.pivot.isactive == 1) {
          if (value.dropdown == 1) {
            //  menuoption.header=value.name
            menuoption.title = value.name;
            menuoption.icon = value.icon;

            var menuSubGroup = _this.createMenuOptions(menuoptions, value.name);

            menuoption.children = menuSubGroup;
          } else {
            menuoption.url = value.url;
            menuoption.route = value.url; //    menuoption.header=value.name

            menuoption.title = value.name;
            menuoption.slug = value.slug;
            menuoption.icon = value.icon;
            menuoption.tag = value.tag;
            menuoption.tagColor = value.tagColor;
            menuoption.i18n = value.i18n;
          }

          menu.push(menuoption);
        }
      });
      return menu;
    }
  },
  setup: function setup(props) {
    var _useVerticalNavMenu = Object(_useVerticalNavMenu__WEBPACK_IMPORTED_MODULE_10__["default"])(props),
        isMouseHovered = _useVerticalNavMenu.isMouseHovered,
        isVerticalMenuCollapsed = _useVerticalNavMenu.isVerticalMenuCollapsed,
        collapseTogglerIcon = _useVerticalNavMenu.collapseTogglerIcon,
        toggleCollapsed = _useVerticalNavMenu.toggleCollapsed,
        updateMouseHovered = _useVerticalNavMenu.updateMouseHovered;

    var _useAppConfig = Object(_core_app_config_useAppConfig__WEBPACK_IMPORTED_MODULE_7__["default"])(),
        skin = _useAppConfig.skin; // Shadow bottom is UI specific and can be removed by user => It's not in `useVerticalNavMenu`


    var shallShadowBottom = Object(_vue_composition_api__WEBPACK_IMPORTED_MODULE_6__["ref"])(false);
    Object(_vue_composition_api__WEBPACK_IMPORTED_MODULE_6__["provide"])('isMouseHovered', isMouseHovered);
    var perfectScrollbarSettings = {
      maxScrollbarLength: 60,
      wheelPropagation: false
    };
    var collapseTogglerIconFeather = Object(_vue_composition_api__WEBPACK_IMPORTED_MODULE_6__["computed"])(function () {
      return collapseTogglerIcon.value === 'unpinned' ? 'CircleIcon' : 'DiscIcon';
    }); // App Name

    var _$themeConfig$app = _themeConfig__WEBPACK_IMPORTED_MODULE_8__["$themeConfig"].app,
        appName = _$themeConfig$app.appName,
        appLogoImage = _$themeConfig$app.appLogoImage;
    return {
      perfectScrollbarSettings: perfectScrollbarSettings,
      isVerticalMenuCollapsed: isVerticalMenuCollapsed,
      collapseTogglerIcon: collapseTogglerIcon,
      toggleCollapsed: toggleCollapsed,
      isMouseHovered: isMouseHovered,
      updateMouseHovered: updateMouseHovered,
      collapseTogglerIconFeather: collapseTogglerIconFeather,
      // Shadow Bottom
      shallShadowBottom: shallShadowBottom,
      // Skin
      skin: skin,
      // App Name
      appName: appName,
      appLogoImage: appLogoImage
    };
  }
});

/***/ }),

/***/ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/@core/layouts/layout-vertical/components/vertical-nav-menu/components/vertical-nav-menu-group/VerticalNavMenuGroup.vue?vue&type=script&lang=js&":
/*!**********************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib??ref--4-0!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/src/@core/layouts/layout-vertical/components/vertical-nav-menu/components/vertical-nav-menu-group/VerticalNavMenuGroup.vue?vue&type=script&lang=js& ***!
  \**********************************************************************************************************************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var bootstrap_vue__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! bootstrap-vue */ "./node_modules/bootstrap-vue/esm/index.js");
/* harmony import */ var _core_layouts_utils__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @core/layouts/utils */ "./resources/js/src/@core/layouts/utils.js");
/* harmony import */ var _core_libs_i18n__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @core/libs/i18n */ "./resources/js/src/@core/libs/i18n/index.js");
/* harmony import */ var _core_libs_acl__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @core/libs/acl */ "./resources/js/src/@core/libs/acl/index.js");
/* harmony import */ var _vertical_nav_menu_header__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ../vertical-nav-menu-header */ "./resources/js/src/@core/layouts/layout-vertical/components/vertical-nav-menu/components/vertical-nav-menu-header/index.js");
/* harmony import */ var _vertical_nav_menu_link_VerticalNavMenuLink_vue__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ../vertical-nav-menu-link/VerticalNavMenuLink.vue */ "./resources/js/src/@core/layouts/layout-vertical/components/vertical-nav-menu/components/vertical-nav-menu-link/VerticalNavMenuLink.vue");
/* harmony import */ var _useVerticalNavMenuGroup__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! ./useVerticalNavMenuGroup */ "./resources/js/src/@core/layouts/layout-vertical/components/vertical-nav-menu/components/vertical-nav-menu-group/useVerticalNavMenuGroup.js");
/* harmony import */ var _mixinVerticalNavMenuGroup__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! ./mixinVerticalNavMenuGroup */ "./resources/js/src/@core/layouts/layout-vertical/components/vertical-nav-menu/components/vertical-nav-menu-group/mixinVerticalNavMenuGroup.js");
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//





 // Composition Function



/* harmony default export */ __webpack_exports__["default"] = ({
  name: 'VerticalNavMenuGroup',
  components: {
    VerticalNavMenuHeader: _vertical_nav_menu_header__WEBPACK_IMPORTED_MODULE_4__["default"],
    VerticalNavMenuLink: _vertical_nav_menu_link_VerticalNavMenuLink_vue__WEBPACK_IMPORTED_MODULE_5__["default"],
    BLink: bootstrap_vue__WEBPACK_IMPORTED_MODULE_0__["BLink"],
    BBadge: bootstrap_vue__WEBPACK_IMPORTED_MODULE_0__["BBadge"],
    BCollapse: bootstrap_vue__WEBPACK_IMPORTED_MODULE_0__["BCollapse"]
  },
  mixins: [_mixinVerticalNavMenuGroup__WEBPACK_IMPORTED_MODULE_7__["default"]],
  props: {
    item: {
      type: Object,
      required: true
    }
  },
  setup: function setup(props) {
    var _useVerticalNavMenuGr = Object(_useVerticalNavMenuGroup__WEBPACK_IMPORTED_MODULE_6__["default"])(props.item),
        isOpen = _useVerticalNavMenuGr.isOpen,
        isActive = _useVerticalNavMenuGr.isActive,
        updateGroupOpen = _useVerticalNavMenuGr.updateGroupOpen,
        updateIsActive = _useVerticalNavMenuGr.updateIsActive;

    var _useI18nUtils = Object(_core_libs_i18n__WEBPACK_IMPORTED_MODULE_2__["useUtils"])(),
        t = _useI18nUtils.t;

    var _useAclUtils = Object(_core_libs_acl__WEBPACK_IMPORTED_MODULE_3__["useUtils"])(),
        canViewVerticalNavMenuGroup = _useAclUtils.canViewVerticalNavMenuGroup;

    return {
      resolveNavItemComponent: _core_layouts_utils__WEBPACK_IMPORTED_MODULE_1__["resolveVerticalNavMenuItemComponent"],
      isOpen: isOpen,
      isActive: isActive,
      updateGroupOpen: updateGroupOpen,
      updateIsActive: updateIsActive,
      // ACL
      canViewVerticalNavMenuGroup: canViewVerticalNavMenuGroup,
      // i18n
      t: t
    };
  }
});

/***/ }),

/***/ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/@core/layouts/layout-vertical/components/vertical-nav-menu/components/vertical-nav-menu-items/VerticalNavMenuItems.vue?vue&type=script&lang=js&":
/*!**********************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib??ref--4-0!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/src/@core/layouts/layout-vertical/components/vertical-nav-menu/components/vertical-nav-menu-items/VerticalNavMenuItems.vue?vue&type=script&lang=js& ***!
  \**********************************************************************************************************************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _core_layouts_utils__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @core/layouts/utils */ "./resources/js/src/@core/layouts/utils.js");
/* harmony import */ var _vue_composition_api__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @vue/composition-api */ "./node_modules/@vue/composition-api/dist/vue-composition-api.mjs");
/* harmony import */ var _vertical_nav_menu_header__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../vertical-nav-menu-header */ "./resources/js/src/@core/layouts/layout-vertical/components/vertical-nav-menu/components/vertical-nav-menu-header/index.js");
/* harmony import */ var _vertical_nav_menu_link_VerticalNavMenuLink_vue__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../vertical-nav-menu-link/VerticalNavMenuLink.vue */ "./resources/js/src/@core/layouts/layout-vertical/components/vertical-nav-menu/components/vertical-nav-menu-link/VerticalNavMenuLink.vue");
/* harmony import */ var _vertical_nav_menu_group_VerticalNavMenuGroup_vue__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ../vertical-nav-menu-group/VerticalNavMenuGroup.vue */ "./resources/js/src/@core/layouts/layout-vertical/components/vertical-nav-menu/components/vertical-nav-menu-group/VerticalNavMenuGroup.vue");
//
//
//
//
//
//
//
//
//
//
//





/* harmony default export */ __webpack_exports__["default"] = ({
  components: {
    VerticalNavMenuHeader: _vertical_nav_menu_header__WEBPACK_IMPORTED_MODULE_2__["default"],
    VerticalNavMenuLink: _vertical_nav_menu_link_VerticalNavMenuLink_vue__WEBPACK_IMPORTED_MODULE_3__["default"],
    VerticalNavMenuGroup: _vertical_nav_menu_group_VerticalNavMenuGroup_vue__WEBPACK_IMPORTED_MODULE_4__["default"]
  },
  props: {
    items: {
      type: Array,
      required: true
    }
  },
  setup: function setup() {
    Object(_vue_composition_api__WEBPACK_IMPORTED_MODULE_1__["provide"])('openGroups', Object(_vue_composition_api__WEBPACK_IMPORTED_MODULE_1__["ref"])([]));
    return {
      resolveNavItemComponent: _core_layouts_utils__WEBPACK_IMPORTED_MODULE_0__["resolveVerticalNavMenuItemComponent"]
    };
  }
});

/***/ }),

/***/ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/@core/layouts/layout-vertical/components/vertical-nav-menu/components/vertical-nav-menu-link/VerticalNavMenuLink.vue?vue&type=script&lang=js&":
/*!********************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib??ref--4-0!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/src/@core/layouts/layout-vertical/components/vertical-nav-menu/components/vertical-nav-menu-link/VerticalNavMenuLink.vue?vue&type=script&lang=js& ***!
  \********************************************************************************************************************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _core_libs_acl__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @core/libs/acl */ "./resources/js/src/@core/libs/acl/index.js");
/* harmony import */ var bootstrap_vue__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! bootstrap-vue */ "./node_modules/bootstrap-vue/esm/index.js");
/* harmony import */ var _core_libs_i18n__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @core/libs/i18n */ "./resources/js/src/@core/libs/i18n/index.js");
/* harmony import */ var _useVerticalNavMenuLink__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./useVerticalNavMenuLink */ "./resources/js/src/@core/layouts/layout-vertical/components/vertical-nav-menu/components/vertical-nav-menu-link/useVerticalNavMenuLink.js");
/* harmony import */ var _mixinVerticalNavMenuLink__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ./mixinVerticalNavMenuLink */ "./resources/js/src/@core/layouts/layout-vertical/components/vertical-nav-menu/components/vertical-nav-menu-link/mixinVerticalNavMenuLink.js");
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//





/* harmony default export */ __webpack_exports__["default"] = ({
  components: {
    BLink: bootstrap_vue__WEBPACK_IMPORTED_MODULE_1__["BLink"],
    BBadge: bootstrap_vue__WEBPACK_IMPORTED_MODULE_1__["BBadge"]
  },
  mixins: [_mixinVerticalNavMenuLink__WEBPACK_IMPORTED_MODULE_4__["default"]],
  props: {
    item: {
      type: Object,
      required: true
    }
  },
  setup: function setup(props) {
    var _useVerticalNavMenuLi = Object(_useVerticalNavMenuLink__WEBPACK_IMPORTED_MODULE_3__["default"])(props.item),
        isActive = _useVerticalNavMenuLi.isActive,
        linkProps = _useVerticalNavMenuLi.linkProps,
        updateIsActive = _useVerticalNavMenuLi.updateIsActive;

    var _useI18nUtils = Object(_core_libs_i18n__WEBPACK_IMPORTED_MODULE_2__["useUtils"])(),
        t = _useI18nUtils.t;

    var _useAclUtils = Object(_core_libs_acl__WEBPACK_IMPORTED_MODULE_0__["useUtils"])(),
        canViewVerticalNavMenuLink = _useAclUtils.canViewVerticalNavMenuLink;

    return {
      isActive: isActive,
      linkProps: linkProps,
      updateIsActive: updateIsActive,
      // ACL
      canViewVerticalNavMenuLink: canViewVerticalNavMenuLink,
      // i18n
      t: t
    };
  }
});

/***/ }),

/***/ "./node_modules/core-js/modules/es.array.join.js":
/*!*******************************************************!*\
  !*** ./node_modules/core-js/modules/es.array.join.js ***!
  \*******************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

"use strict";

var $ = __webpack_require__(/*! ../internals/export */ "./node_modules/core-js/internals/export.js");
var uncurryThis = __webpack_require__(/*! ../internals/function-uncurry-this */ "./node_modules/core-js/internals/function-uncurry-this.js");
var IndexedObject = __webpack_require__(/*! ../internals/indexed-object */ "./node_modules/core-js/internals/indexed-object.js");
var toIndexedObject = __webpack_require__(/*! ../internals/to-indexed-object */ "./node_modules/core-js/internals/to-indexed-object.js");
var arrayMethodIsStrict = __webpack_require__(/*! ../internals/array-method-is-strict */ "./node_modules/core-js/internals/array-method-is-strict.js");

var un$Join = uncurryThis([].join);

var ES3_STRINGS = IndexedObject != Object;
var STRICT_METHOD = arrayMethodIsStrict('join', ',');

// `Array.prototype.join` method
// https://tc39.es/ecma262/#sec-array.prototype.join
$({ target: 'Array', proto: true, forced: ES3_STRINGS || !STRICT_METHOD }, {
  join: function join(separator) {
    return un$Join(toIndexedObject(this), separator === undefined ? ',' : separator);
  }
});


/***/ }),

/***/ "./node_modules/css-loader/dist/cjs.js!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src/index.js?!./node_modules/sass-loader/dist/cjs.js?!./node_modules/sass-loader/dist/cjs.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/@core/layouts/components/app-customizer/AppCustomizer.vue?vue&type=style&index=0&lang=scss&":
/*!**********************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/css-loader/dist/cjs.js!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src??ref--7-2!./node_modules/sass-loader/dist/cjs.js??ref--7-3!./node_modules/sass-loader/dist/cjs.js??ref--11-0!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/src/@core/layouts/components/app-customizer/AppCustomizer.vue?vue&type=style&index=0&lang=scss& ***!
  \**********************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

// Imports
var ___CSS_LOADER_API_IMPORT___ = __webpack_require__(/*! ../../../../../../../node_modules/css-loader/dist/runtime/api.js */ "./node_modules/css-loader/dist/runtime/api.js");
exports = ___CSS_LOADER_API_IMPORT___(false);
// Module
exports.push([module.i, ".v-select {\n  position: relative;\n  font-family: inherit;\n}\n.v-select,\n.v-select * {\n  box-sizing: border-box;\n}\n\n/* KeyFrames */\n@-webkit-keyframes vSelectSpinner-ltr {\n0% {\n    transform: rotate(0deg);\n}\n100% {\n    transform: rotate(360deg);\n}\n}\n@-webkit-keyframes vSelectSpinner-rtl {\n0% {\n    transform: rotate(0deg);\n}\n100% {\n    transform: rotate(-360deg);\n}\n}\n@keyframes vSelectSpinner-ltr {\n0% {\n    transform: rotate(0deg);\n}\n100% {\n    transform: rotate(360deg);\n}\n}\n@keyframes vSelectSpinner-rtl {\n0% {\n    transform: rotate(0deg);\n}\n100% {\n    transform: rotate(-360deg);\n}\n}\n/* Dropdown Default Transition */\n.vs__fade-enter-active,\n.vs__fade-leave-active {\n  pointer-events: none;\n  transition: opacity 0.15s cubic-bezier(1, 0.5, 0.8, 1);\n}\n.vs__fade-enter,\n.vs__fade-leave-to {\n  opacity: 0;\n}\n\n/** Component States */\n/*\n * Disabled\n *\n * When the component is disabled, all interaction\n * should be prevented. Here we modify the bg color,\n * and change the cursor displayed on the interactive\n * components.\n */\n[dir] .vs--disabled .vs__dropdown-toggle, [dir] .vs--disabled .vs__clear, [dir] .vs--disabled .vs__search, [dir] .vs--disabled .vs__selected, [dir] .vs--disabled .vs__open-indicator {\n  cursor: not-allowed;\n  background-color: #f8f8f8;\n}\n\n/*\n *  RTL - Right to Left Support\n *\n *  Because we're using a flexbox layout, the `dir=\"rtl\"`\n *  HTML attribute does most of the work for us by\n *  rearranging the child elements visually.\n */\n.v-select[dir=rtl] .vs__actions {\n  padding: 0 3px 0 6px;\n}\n.v-select[dir=rtl] .vs__clear {\n  margin-left: 6px;\n  margin-right: 0;\n}\n.v-select[dir=rtl] .vs__deselect {\n  margin-left: 0;\n  margin-right: 2px;\n}\n.v-select[dir=rtl] .vs__dropdown-menu {\n  text-align: right;\n}\n\n/**\n    Dropdown Toggle\n\n    The dropdown toggle is the primary wrapper of the component. It\n    has two direct descendants: .vs__selected-options, and .vs__actions.\n\n    .vs__selected-options holds the .vs__selected's as well as the\n    main search input.\n\n    .vs__actions holds the clear button and dropdown toggle.\n */\n.vs__dropdown-toggle {\n  appearance: none;\n  display: flex;\n  white-space: normal;\n}\n[dir] .vs__dropdown-toggle {\n  padding: 0 0 4px 0;\n  background: none;\n  border: 1px solid #d8d6de;\n  border-radius: 0.357rem;\n}\n.vs__selected-options {\n  display: flex;\n  flex-basis: 100%;\n  flex-grow: 1;\n  flex-wrap: wrap;\n  position: relative;\n}\n[dir] .vs__selected-options {\n  padding: 0 2px;\n}\n.vs__actions {\n  display: flex;\n  align-items: center;\n}\n[dir=ltr] .vs__actions {\n  padding: 4px 6px 0 3px;\n}\n[dir=rtl] .vs__actions {\n  padding: 4px 3px 0 6px;\n}\n\n/* Dropdown Toggle States */\n[dir] .vs--searchable .vs__dropdown-toggle {\n  cursor: text;\n}\n[dir] .vs--unsearchable .vs__dropdown-toggle {\n  cursor: pointer;\n}\n[dir] .vs--open .vs__dropdown-toggle {\n  border-bottom-color: transparent;\n}\n[dir=ltr] .vs--open .vs__dropdown-toggle {\n  border-bottom-left-radius: 0;\n  border-bottom-right-radius: 0;\n}\n[dir=rtl] .vs--open .vs__dropdown-toggle {\n  border-bottom-right-radius: 0;\n  border-bottom-left-radius: 0;\n}\n.vs__open-indicator {\n  fill: rgba(60, 60, 60, 0.5);\n  transition: transform 150ms cubic-bezier(1, -0.115, 0.975, 0.855);\n}\n[dir] .vs__open-indicator {\n  transform: scale(1);\n  transition-timing-function: cubic-bezier(1, -0.115, 0.975, 0.855);\n}\n[dir=ltr] .vs--open .vs__open-indicator {\n  transform: rotate(180deg) scale(1);\n}\n[dir=rtl] .vs--open .vs__open-indicator {\n  transform: rotate(-180deg) scale(1);\n}\n.vs--loading .vs__open-indicator {\n  opacity: 0;\n}\n\n/* Clear Button */\n.vs__clear {\n  fill: rgba(60, 60, 60, 0.5);\n}\n[dir] .vs__clear {\n  padding: 0;\n  border: 0;\n  background-color: transparent;\n  cursor: pointer;\n}\n[dir=ltr] .vs__clear {\n  margin-right: 8px;\n}\n[dir=rtl] .vs__clear {\n  margin-left: 8px;\n}\n\n/* Dropdown Menu */\n.vs__dropdown-menu {\n  display: block;\n  box-sizing: border-box;\n  position: absolute;\n  top: calc(100% - 1px);\n  z-index: 1000;\n  width: 100%;\n  max-height: 350px;\n  min-width: 160px;\n  overflow-y: auto;\n  list-style: none;\n}\n[dir] .vs__dropdown-menu {\n  padding: 5px 0;\n  margin: 0;\n  box-shadow: 0px 4px 25px 0px rgba(0, 0, 0, 0.1);\n  border: 1px solid #d8d6de;\n  border-top-style: none;\n  border-radius: 0 0 0.357rem 0.357rem;\n  background: #fff;\n}\n[dir=ltr] .vs__dropdown-menu {\n  left: 0;\n  text-align: left;\n}\n[dir=rtl] .vs__dropdown-menu {\n  right: 0;\n  text-align: right;\n}\n[dir] .vs__no-options {\n  text-align: center;\n}\n\n/* List Items */\n.vs__dropdown-option {\n  line-height: 1.42857143;\n  /* Normalize line height */\n  display: block;\n  color: #333;\n  /* Overrides most CSS frameworks */\n  white-space: nowrap;\n}\n[dir] .vs__dropdown-option {\n  padding: 3px 20px;\n  clear: both;\n  cursor: pointer;\n}\n.vs__dropdown-option--highlight {\n  color: #7367f0 !important;\n}\n[dir] .vs__dropdown-option--highlight {\n  background: rgba(115, 103, 240, 0.12);\n}\n.vs__dropdown-option--deselect {\n  color: #fff;\n}\n[dir] .vs__dropdown-option--deselect {\n  background: #fb5858;\n}\n.vs__dropdown-option--disabled {\n  color: rgba(60, 60, 60, 0.5);\n}\n[dir] .vs__dropdown-option--disabled {\n  background: inherit;\n  cursor: inherit;\n}\n\n/* Selected Tags */\n.vs__selected {\n  display: flex;\n  align-items: center;\n  color: #333;\n  line-height: 1.8;\n  z-index: 0;\n}\n[dir] .vs__selected {\n  background-color: #7367f0;\n  border: 0 solid rgba(60, 60, 60, 0.26);\n  border-radius: 0.357rem;\n  margin: 4px 2px 0px 2px;\n  padding: 0 0.25em;\n}\n.vs__deselect {\n  display: inline-flex;\n  appearance: none;\n  fill: rgba(60, 60, 60, 0.5);\n}\n[dir] .vs__deselect {\n  padding: 0;\n  border: 0;\n  cursor: pointer;\n  background: none;\n  text-shadow: 0 1px 0 #fff;\n}\n[dir=ltr] .vs__deselect {\n  margin-left: 4px;\n}\n[dir=rtl] .vs__deselect {\n  margin-right: 4px;\n}\n\n/* States */\n[dir] .vs--single .vs__selected {\n  background-color: transparent;\n  border-color: transparent;\n}\n.vs--single.vs--open .vs__selected, .vs--single.vs--loading .vs__selected {\n  position: absolute;\n  opacity: 0.4;\n}\n.vs--single.vs--searching .vs__selected {\n  display: none;\n}\n\n/* Search Input */\n/**\n * Super weird bug... If this declaration is grouped\n * below, the cancel button will still appear in chrome.\n * If it's up here on it's own, it'll hide it.\n */\n.vs__search::-webkit-search-cancel-button {\n  display: none;\n}\n.vs__search::-webkit-search-decoration,\n.vs__search::-webkit-search-results-button,\n.vs__search::-webkit-search-results-decoration,\n.vs__search::-ms-clear {\n  display: none;\n}\n.vs__search,\n.vs__search:focus {\n  appearance: none;\n  line-height: 1.8;\n  font-size: 1em;\n  outline: none;\n  width: 0;\n  max-width: 100%;\n  flex-grow: 1;\n  z-index: 1;\n}\n[dir] .vs__search, [dir] .vs__search:focus {\n  border: 1px solid transparent;\n  margin: 4px 0 0 0;\n  padding: 0 7px;\n  background: none;\n  box-shadow: none;\n}\n[dir=ltr] .vs__search, [dir=ltr] .vs__search:focus {\n  border-left: none;\n}\n[dir=rtl] .vs__search, [dir=rtl] .vs__search:focus {\n  border-right: none;\n}\n.vs__search::placeholder {\n  color: #6e6b7b;\n}\n\n/**\n    States\n */\n.vs--unsearchable .vs__search {\n  opacity: 1;\n}\n[dir] .vs--unsearchable:not(.vs--disabled) .vs__search {\n  cursor: pointer;\n}\n.vs--single.vs--searching:not(.vs--open):not(.vs--loading) .vs__search {\n  opacity: 0.2;\n}\n\n/* Loading Spinner */\n.vs__spinner {\n  align-self: center;\n  opacity: 0;\n  font-size: 5px;\n  text-indent: -9999em;\n  overflow: hidden;\n  transition: opacity 0.1s;\n}\n[dir] .vs__spinner {\n  border-top: 0.9em solid rgba(100, 100, 100, 0.1);\n  border-bottom: 0.9em solid rgba(100, 100, 100, 0.1);\n  transform: translateZ(0);\n}\n[dir=ltr] .vs__spinner {\n  border-right: 0.9em solid rgba(100, 100, 100, 0.1);\n  border-left: 0.9em solid rgba(60, 60, 60, 0.45);\n  animation:  vSelectSpinner-ltr 1.1s infinite linear;\n}\n[dir=rtl] .vs__spinner {\n  border-left: 0.9em solid rgba(100, 100, 100, 0.1);\n  border-right: 0.9em solid rgba(60, 60, 60, 0.45);\n  animation:  vSelectSpinner-rtl 1.1s infinite linear;\n}\n.vs__spinner,\n.vs__spinner:after {\n  width: 5em;\n  height: 5em;\n}\n[dir] .vs__spinner, [dir] .vs__spinner:after {\n  border-radius: 50%;\n}\n\n/* Loading Spinner States */\n.vs--loading .vs__spinner {\n  opacity: 1;\n}\n.vs__open-indicator {\n  fill: none;\n}\n[dir] .vs__open-indicator {\n  margin-top: 0.15rem;\n}\n.vs__dropdown-toggle {\n  transition: all 0.25s ease-in-out;\n}\n[dir] .vs__dropdown-toggle {\n  padding: 0.59px 0 4px 0;\n}\n[dir=ltr] .vs--single .vs__dropdown-toggle {\n  padding-left: 6px;\n}\n[dir=rtl] .vs--single .vs__dropdown-toggle {\n  padding-right: 6px;\n}\n.vs__dropdown-option--disabled {\n  opacity: 0.5;\n}\n[dir] .vs__dropdown-option--disabled.vs__dropdown-option--selected {\n  background: #7367f0 !important;\n}\n.vs__dropdown-option {\n  color: #6e6b7b;\n}\n[dir] .vs__dropdown-option, [dir] .vs__no-options {\n  padding: 7px 20px;\n}\n.vs__dropdown-option--selected {\n  background-color: #7367f0;\n  color: #fff;\n  position: relative;\n}\n.vs__dropdown-option--selected::after {\n  content: \"\";\n  height: 1.1rem;\n  width: 1.1rem;\n  display: inline-block;\n  position: absolute;\n  top: 50%;\n  transform: translateY(-50%);\n  right: 20px;\n  background-image: url(\"data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='24' height='24' viewBox='0 0 24 24' fill='none' stroke='%23fff' stroke-width='2' stroke-linecap='round' stroke-linejoin='round' class='feather feather-check'%3E%3Cpolyline points='20 6 9 17 4 12'%3E%3C/polyline%3E%3C/svg%3E\");\n  background-repeat: no-repeat;\n  background-position: center;\n  background-size: 1.1rem;\n}\n[dir=rtl] .vs__dropdown-option--selected::after {\n  left: 20px;\n  right: unset;\n}\n.vs__dropdown-option--selected.vs__dropdown-option--highlight {\n  color: #fff !important;\n  background-color: #7367f0 !important;\n}\n.vs__clear svg {\n  color: #6e6b7b;\n}\n.vs__selected {\n  color: #fff;\n}\n.v-select.vs--single .vs__selected {\n  color: #6e6b7b;\n  transition: transform 0.2s ease;\n}\n[dir] .v-select.vs--single .vs__selected {\n  margin-top: 5px;\n}\n[dir=ltr] .v-select.vs--single .vs__selected input {\n  padding-left: 0;\n}\n[dir=rtl] .v-select.vs--single .vs__selected input {\n  padding-right: 0;\n}\n[dir=ltr] .vs--single.vs--open .vs__selected {\n  transform: translateX(5px);\n}\n[dir=rtl] .vs--single.vs--open .vs__selected {\n  transform: translateX(-5px);\n}\n.vs__selected .vs__deselect {\n  color: inherit;\n}\n.v-select:not(.vs--single) .vs__selected {\n  font-size: 0.9rem;\n}\n[dir] .v-select:not(.vs--single) .vs__selected {\n  border-radius: 3px;\n  padding: 0 0.6em;\n}\n[dir=ltr] .v-select:not(.vs--single) .vs__selected {\n  margin: 5px 2px 2px 5px;\n}\n[dir=rtl] .v-select:not(.vs--single) .vs__selected {\n  margin: 5px 5px 2px 2px;\n}\n.v-select:not(.vs--single) .vs__deselect svg {\n  vertical-align: text-top;\n}\n[dir] .v-select:not(.vs--single) .vs__deselect svg {\n  transform: scale(0.8);\n}\n.vs__dropdown-menu {\n  top: calc(100% + 1rem);\n}\n[dir] .vs__dropdown-menu {\n  border: none;\n  border-radius: 6px;\n  padding: 0;\n}\n[dir] .vs--open .vs__dropdown-toggle {\n  border-color: #7367f0;\n  border-bottom-color: #7367f0;\n  box-shadow: 0 3px 10px 0 rgba(34, 41, 47, 0.1);\n}\n[dir=ltr] .vs--open .vs__dropdown-toggle {\n  border-bottom-left-radius: 0.357rem;\n  border-bottom-right-radius: 0.357rem;\n}\n[dir=rtl] .vs--open .vs__dropdown-toggle {\n  border-bottom-right-radius: 0.357rem;\n  border-bottom-left-radius: 0.357rem;\n}\n.select-size-lg .vs__selected {\n  font-size: 1rem !important;\n}\n[dir] .select-size-lg.vs--single.vs--open .vs__selected {\n  margin-top: 6px;\n}\n.select-size-lg .vs__dropdown-toggle,\n.select-size-lg .vs__selected {\n  font-size: 1.25rem;\n}\n[dir] .select-size-lg .vs__dropdown-toggle {\n  padding: 5px;\n}\n[dir] .select-size-lg .vs__dropdown-toggle input {\n  margin-top: 0;\n}\n.select-size-lg .vs__deselect svg {\n  vertical-align: middle !important;\n}\n[dir] .select-size-lg .vs__deselect svg {\n  transform: scale(1) !important;\n}\n[dir] .select-size-sm .vs__dropdown-toggle {\n  padding-bottom: 0;\n  padding: 1px;\n}\n[dir] .select-size-sm.vs--single .vs__dropdown-toggle {\n  padding: 2px;\n}\n.select-size-sm .vs__dropdown-toggle,\n.select-size-sm .vs__selected {\n  font-size: 0.9rem;\n}\n[dir] .select-size-sm .vs__actions {\n  padding-top: 2px;\n  padding-bottom: 2px;\n}\n.select-size-sm .vs__deselect svg {\n  vertical-align: middle !important;\n}\n[dir] .select-size-sm .vs__search {\n  margin-top: 0;\n}\n.select-size-sm.v-select .vs__selected {\n  font-size: 0.75rem;\n}\n[dir] .select-size-sm.v-select .vs__selected {\n  padding: 0 0.3rem;\n}\n[dir] .select-size-sm.v-select:not(.vs--single) .vs__selected {\n  margin: 4px 5px;\n}\n[dir] .select-size-sm.v-select.vs--single .vs__selected {\n  margin-top: 1px;\n}\n[dir] .select-size-sm.vs--single.vs--open .vs__selected {\n  margin-top: 4px;\n}\n.dark-layout .vs__dropdown-toggle {\n  color: #b4b7bd;\n}\n[dir] .dark-layout .vs__dropdown-toggle {\n  background: #283046;\n  border-color: #404656;\n}\n.dark-layout .vs__selected-options input {\n  color: #b4b7bd;\n}\n.dark-layout .vs__selected-options input::placeholder {\n  color: #676d7d;\n}\n.dark-layout .vs__actions svg {\n  fill: #404656;\n}\n[dir] .dark-layout .vs__dropdown-menu {\n  background: #283046;\n}\n.dark-layout .vs__dropdown-menu li {\n  color: #b4b7bd;\n}\n.dark-layout .v-select:not(.vs--single) .vs__selected {\n  color: #7367f0;\n}\n[dir] .dark-layout .v-select:not(.vs--single) .vs__selected {\n  background-color: rgba(115, 103, 240, 0.12);\n}\n.dark-layout .v-select.vs--single .vs__selected {\n  color: #b4b7bd !important;\n}\n.dark-layout .vs--disabled .vs__dropdown-toggle,\n.dark-layout .vs--disabled .vs__clear,\n.dark-layout .vs--disabled .vs__search,\n.dark-layout .vs--disabled .vs__selected,\n.dark-layout .vs--disabled .vs__open-indicator {\n  opacity: 0.5;\n}\n[dir] .dark-layout .vs--disabled .vs__dropdown-toggle, [dir] .dark-layout .vs--disabled .vs__clear, [dir] .dark-layout .vs--disabled .vs__search, [dir] .dark-layout .vs--disabled .vs__selected, [dir] .dark-layout .vs--disabled .vs__open-indicator {\n  background-color: #283046;\n}", ""]);
// Exports
module.exports = exports;


/***/ }),

/***/ "./node_modules/css-loader/dist/cjs.js!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src/index.js?!./node_modules/sass-loader/dist/cjs.js?!./node_modules/sass-loader/dist/cjs.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/@core/layouts/components/app-customizer/AppCustomizer.vue?vue&type=style&index=1&id=4b46fe46&lang=scss&scoped=true&":
/*!**********************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/css-loader/dist/cjs.js!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src??ref--7-2!./node_modules/sass-loader/dist/cjs.js??ref--7-3!./node_modules/sass-loader/dist/cjs.js??ref--11-0!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/src/@core/layouts/components/app-customizer/AppCustomizer.vue?vue&type=style&index=1&id=4b46fe46&lang=scss&scoped=true& ***!
  \**********************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

// Imports
var ___CSS_LOADER_API_IMPORT___ = __webpack_require__(/*! ../../../../../../../node_modules/css-loader/dist/runtime/api.js */ "./node_modules/css-loader/dist/runtime/api.js");
exports = ___CSS_LOADER_API_IMPORT___(false);
// Module
exports.push([module.i, "[dir] .customizer-section[data-v-4b46fe46] {\n  padding: 1.5rem;\n  border-bottom: 1px solid #ebe9f1;\n}\n[dir] .dark-layout .customizer-section[data-v-4b46fe46] {\n  border-color: #3b4253;\n}\n[dir=ltr] .customizer-section #skin-radio-group[data-v-4b46fe46]  .custom-control-inline {\n  margin-right: 0.7rem;\n}\n[dir=rtl] .customizer-section #skin-radio-group[data-v-4b46fe46]  .custom-control-inline {\n  margin-left: 0.7rem;\n}\n[dir] .customizer-section .form-group[data-v-4b46fe46] {\n  margin-bottom: 1.5rem;\n}\n[dir] .customizer-section .form-group[data-v-4b46fe46]:last-of-type {\n  margin-bottom: 0;\n}\n.customizer-section .form-group[data-v-4b46fe46]  legend {\n  font-weight: 500;\n}\n[dir] .mark-active[data-v-4b46fe46] {\n  box-shadow: 0 0 0 0.2rem rgba(38, 143, 255, 0.5);\n}\n.ps-customizer-area[data-v-4b46fe46] {\n  height: calc(100% - 83px);\n}", ""]);
// Exports
module.exports = exports;


/***/ }),

/***/ "./node_modules/css-loader/dist/cjs.js!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src/index.js?!./node_modules/sass-loader/dist/cjs.js?!./node_modules/sass-loader/dist/cjs.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/@core/layouts/components/app-navbar/components/Module.vue?vue&type=style&index=0&id=552012bc&lang=scss&scoped=true&":
/*!**********************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/css-loader/dist/cjs.js!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src??ref--7-2!./node_modules/sass-loader/dist/cjs.js??ref--7-3!./node_modules/sass-loader/dist/cjs.js??ref--11-0!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/src/@core/layouts/components/app-navbar/components/Module.vue?vue&type=style&index=0&id=552012bc&lang=scss&scoped=true& ***!
  \**********************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

// Imports
var ___CSS_LOADER_API_IMPORT___ = __webpack_require__(/*! ../../../../../../../../node_modules/css-loader/dist/runtime/api.js */ "./node_modules/css-loader/dist/runtime/api.js");
exports = ___CSS_LOADER_API_IMPORT___(false);
// Module
exports.push([module.i, "@charset \"UTF-8\";\n.module-antet[data-v-552012bc] {\n  list-style: none;\n}\n[dir] .module-antet[data-v-552012bc] {\n  padding: 0;\n  margin: 0;\n}\n.module-antet .nav-link[data-v-552012bc] {\n  font-weight: 500;\n  white-space: nowrap;\n}\n[dir] .module-antet .nav-link[data-v-552012bc] {\n  padding: 0.25rem 0.6rem;\n  border-radius: 0.4rem;\n}\n[dir] .module-antet .nav-link[data-v-552012bc]:hover {\n  background: rgba(115, 103, 240, 0.08);\n}\n\n/* Modulul deschis se deosebește, ca să se vadă unde ești */\n[dir] .module-antet .nav-link.activ[data-v-552012bc] {\n  background: rgba(115, 103, 240, 0.12);\n}\n\n/*\n * Fiecare siglă în chenarul ei: siglele au fonduri diferite, iar fără chenar\n * se scurg una într-alta și par un singur desen lung.\n */\n.sigla-modul[data-v-552012bc] {\n  width: 26px;\n  height: 26px;\n  flex-shrink: 0;\n}\n[dir] .sigla-modul[data-v-552012bc] {\n  padding: 2px;\n  background: #fff;\n  border: 1px solid rgba(115, 103, 240, 0.35);\n  border-radius: 0.35rem;\n}\n[dir] .module-antet .nav-link.activ .sigla-modul[data-v-552012bc] {\n  border-color: #7367f0;\n}\n\n/* Pictograma administrării, în chenarul siglelor */\n.module-antet .sigla-modul.d-inline-flex[data-v-552012bc] {\n  color: #7367f0;\n}", ""]);
// Exports
module.exports = exports;


/***/ }),

/***/ "./node_modules/css-loader/dist/cjs.js!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src/index.js?!./node_modules/sass-loader/dist/cjs.js?!./node_modules/sass-loader/dist/cjs.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/@core/layouts/layout-vertical/components/vertical-nav-menu/VerticalNavMenu.vue?vue&type=style&index=0&lang=scss&":
/*!*******************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/css-loader/dist/cjs.js!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src??ref--7-2!./node_modules/sass-loader/dist/cjs.js??ref--7-3!./node_modules/sass-loader/dist/cjs.js??ref--11-0!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/src/@core/layouts/layout-vertical/components/vertical-nav-menu/VerticalNavMenu.vue?vue&type=style&index=0&lang=scss& ***!
  \*******************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

// Imports
var ___CSS_LOADER_API_IMPORT___ = __webpack_require__(/*! ../../../../../../../../node_modules/css-loader/dist/runtime/api.js */ "./node_modules/css-loader/dist/runtime/api.js");
exports = ___CSS_LOADER_API_IMPORT___(false);
// Module
exports.push([module.i, "/*=========================================================================================\n    File Name: vertical-menu.scss\n    Description: A classic vertical modern style menu with expand and collops support. It support\n    light & dark version, flipped layout, right side icons, native scroll and borders menu\n    item separation.\n    ----------------------------------------------------------------------------------------\n    Item Name: Vuexy - Vuejs, React, Angular, HTML & Laravel Admin Dashboard Template\n    Author: PIXINVENT\n    Author URL: http://www.themeforest.net/user/pixinvent\n==========================================================================================*/\n.vertical-layout.vertical-menu-modern .main-menu {\n  transition: 0.4s cubic-bezier(0.25, 0.8, 0.25, 1), background 0s;\n  backface-visibility: hidden;\n}\n[dir] .vertical-layout.vertical-menu-modern .main-menu {\n  transform: translate3d(0, 0, 0);\n}\n.vertical-layout.vertical-menu-modern .main-menu .navigation li a {\n  align-items: center;\n}\n.vertical-layout.vertical-menu-modern .main-menu .navigation > li > a svg,\n.vertical-layout.vertical-menu-modern .main-menu .navigation > li > a i {\n  height: 20px;\n  width: 20px;\n  font-size: 1.45rem;\n  flex-shrink: 0;\n}\n[dir=ltr] .vertical-layout.vertical-menu-modern .main-menu .navigation > li > a svg, [dir=ltr] .vertical-layout.vertical-menu-modern .main-menu .navigation > li > a i {\n  margin-right: 1.1rem;\n}\n[dir=rtl] .vertical-layout.vertical-menu-modern .main-menu .navigation > li > a svg, [dir=rtl] .vertical-layout.vertical-menu-modern .main-menu .navigation > li > a i {\n  margin-left: 1.1rem;\n}\n.vertical-layout.vertical-menu-modern .main-menu .navigation .menu-content > li > a svg,\n.vertical-layout.vertical-menu-modern .main-menu .navigation .menu-content > li > a i {\n  font-size: 11px;\n  height: 11px;\n  width: 11px;\n}\n[dir=ltr] .vertical-layout.vertical-menu-modern .main-menu .navigation .menu-content > li > a svg, [dir=ltr] .vertical-layout.vertical-menu-modern .main-menu .navigation .menu-content > li > a i {\n  margin-right: 1.45rem;\n}\n[dir=rtl] .vertical-layout.vertical-menu-modern .main-menu .navigation .menu-content > li > a svg, [dir=rtl] .vertical-layout.vertical-menu-modern .main-menu .navigation .menu-content > li > a i {\n  margin-left: 1.45rem;\n}\n.vertical-layout.vertical-menu-modern.menu-expanded .main-menu {\n  width: 260px;\n}\n.vertical-layout.vertical-menu-modern.menu-expanded .main-menu .navigation .navigation-header .feather-more-horizontal {\n  display: none;\n}\n.vertical-layout.vertical-menu-modern.menu-expanded .main-menu .navigation > li > a > i:before,\n.vertical-layout.vertical-menu-modern.menu-expanded .main-menu .navigation > li > a > svg:before {\n  height: 20px;\n  width: 20px;\n  font-size: 1.45rem;\n}\n.vertical-layout.vertical-menu-modern.menu-expanded .main-menu .navigation li.has-sub > a:after {\n  content: \"\";\n  height: 1.1rem;\n  width: 1.1rem;\n  display: inline-block;\n  position: absolute;\n  top: 14px;\n  transition: all 0.2s ease-out;\n}\n[dir] .vertical-layout.vertical-menu-modern.menu-expanded .main-menu .navigation li.has-sub > a:after {\n  background-image: url(\"data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%236e6b7b' stroke-width='2' stroke-linecap='round' stroke-linejoin='round' class='feather feather-chevron-right'%3E%3Cpolyline points='9 18 15 12 9 6'%3E%3C/polyline%3E%3C/svg%3E\");\n  background-repeat: no-repeat;\n  background-position: center;\n  background-size: 1.1rem;\n  transform: rotate(0deg);\n}\n[dir=ltr] .vertical-layout.vertical-menu-modern.menu-expanded .main-menu .navigation li.has-sub > a:after {\n  right: 20px;\n}\n[dir=rtl] .vertical-layout.vertical-menu-modern.menu-expanded .main-menu .navigation li.has-sub > a:after {\n  left: 20px;\n}\n[dir=ltr] .vertical-layout.vertical-menu-modern.menu-expanded .main-menu .navigation li.has-sub.open:not(.menu-item-closing) > a:after {\n  transform: rotate(90deg);\n}\n[dir=rtl] .vertical-layout.vertical-menu-modern.menu-expanded .main-menu .navigation li.has-sub.open:not(.menu-item-closing) > a:after {\n  transform: rotate(-90deg);\n}\n[dir=ltr] .vertical-layout.vertical-menu-modern.menu-expanded .footer {\n  margin-left: 260px;\n}\n[dir=rtl] .vertical-layout.vertical-menu-modern.menu-expanded .footer {\n  margin-right: 260px;\n}\n.vertical-layout.vertical-menu-modern.menu-collapsed .navbar .navbar-header {\n  width: 80px;\n}\n[dir=ltr] .vertical-layout.vertical-menu-modern.menu-collapsed .navbar .navbar-header {\n  float: left;\n}\n[dir=rtl] .vertical-layout.vertical-menu-modern.menu-collapsed .navbar .navbar-header {\n  float: right;\n}\n.vertical-layout.vertical-menu-modern.menu-collapsed .navbar .navbar-header .modern-nav-toggle {\n  display: none;\n}\n.vertical-layout.vertical-menu-modern.menu-collapsed .navbar .navbar-header.expanded {\n  width: 260px;\n  z-index: 1000;\n}\n.vertical-layout.vertical-menu-modern.menu-collapsed .navbar .navbar-header.expanded .modern-nav-toggle {\n  display: block;\n}\n[dir=ltr] .vertical-layout.vertical-menu-modern.menu-collapsed .navbar.fixed-top, [dir=ltr] .vertical-layout.vertical-menu-modern.menu-collapsed .navbar.floating-nav {\n  left: 80px;\n}\n[dir=rtl] .vertical-layout.vertical-menu-modern.menu-collapsed .navbar.fixed-top, [dir=rtl] .vertical-layout.vertical-menu-modern.menu-collapsed .navbar.floating-nav {\n  right: 80px;\n}\n.vertical-layout.vertical-menu-modern.menu-collapsed .main-menu {\n  width: 80px;\n}\n.vertical-layout.vertical-menu-modern.menu-collapsed .main-menu .navbar-header .brand-text,\n.vertical-layout.vertical-menu-modern.menu-collapsed .main-menu .modern-nav-toggle {\n  display: none;\n}\n[dir=ltr] .vertical-layout.vertical-menu-modern.menu-collapsed .main-menu:not(.expanded) .navigation-header {\n  margin-left: 2.2rem;\n}\n[dir=rtl] .vertical-layout.vertical-menu-modern.menu-collapsed .main-menu:not(.expanded) .navigation-header {\n  margin-right: 2.2rem;\n}\n.vertical-layout.vertical-menu-modern.menu-collapsed .main-menu:not(.expanded) .navigation-header span {\n  display: none;\n}\n.vertical-layout.vertical-menu-modern.menu-collapsed .main-menu:not(.expanded) .navigation-header .feather-more-horizontal {\n  display: block;\n  font-size: 1.285rem;\n  width: 18px;\n  height: 18px;\n}\n[dir] .vertical-layout.vertical-menu-modern.menu-collapsed .main-menu:not(.expanded) .navigation li:last-child {\n  margin-bottom: 1.25rem !important;\n}\n.vertical-layout.vertical-menu-modern.menu-collapsed .main-menu:not(.expanded) .navigation li.active a {\n  color: #565656;\n}\n[dir] .vertical-layout.vertical-menu-modern.menu-collapsed .main-menu:not(.expanded) .navigation li.active a {\n  background: whitesmoke;\n  box-shadow: none;\n}\n.vertical-layout.vertical-menu-modern.menu-collapsed .main-menu.expanded {\n  width: 260px;\n}\n.vertical-layout.vertical-menu-modern.menu-collapsed .main-menu.expanded .navigation > li.navigation-header span {\n  display: block;\n}\n.vertical-layout.vertical-menu-modern.menu-collapsed .main-menu.expanded .navigation > li.navigation-header .feather-more-horizontal {\n  display: none;\n}\n.vertical-layout.vertical-menu-modern.menu-collapsed .main-menu.expanded .navigation li.has-sub > a:after {\n  content: \"\";\n  height: 1rem;\n  width: 1rem;\n  display: inline-block;\n  position: absolute;\n  top: 14px;\n  transition: all 0.2s ease-out;\n}\n[dir] .vertical-layout.vertical-menu-modern.menu-collapsed .main-menu.expanded .navigation li.has-sub > a:after {\n  background-image: url(\"data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%236e6b7b' stroke-width='2' stroke-linecap='round' stroke-linejoin='round' class='feather feather-chevron-right'%3E%3Cpolyline points='9 18 15 12 9 6'%3E%3C/polyline%3E%3C/svg%3E\");\n  background-repeat: no-repeat;\n  background-position: center;\n  background-size: 1rem;\n  transform: rotate(0deg);\n}\n[dir=ltr] .vertical-layout.vertical-menu-modern.menu-collapsed .main-menu.expanded .navigation li.has-sub > a:after {\n  right: 20px;\n}\n[dir=rtl] .vertical-layout.vertical-menu-modern.menu-collapsed .main-menu.expanded .navigation li.has-sub > a:after {\n  left: 20px;\n}\n[dir=ltr] .vertical-layout.vertical-menu-modern.menu-collapsed .main-menu.expanded .navigation li.has-sub.open:not(.menu-item-closing) > a:after {\n  transform: rotate(90deg);\n}\n[dir=rtl] .vertical-layout.vertical-menu-modern.menu-collapsed .main-menu.expanded .navigation li.has-sub.open:not(.menu-item-closing) > a:after {\n  transform: rotate(-90deg);\n}\n.vertical-layout.vertical-menu-modern.menu-collapsed .main-menu.expanded .navbar-header .brand-text {\n  display: inline;\n}\n.vertical-layout.vertical-menu-modern.menu-collapsed .main-menu.expanded .modern-nav-toggle {\n  display: block;\n}\n.vertical-layout.vertical-menu-modern.menu-collapsed .main-menu .navigation {\n  overflow: visible;\n}\n.vertical-layout.vertical-menu-modern.menu-collapsed .main-menu .navigation > li.navigation-header span {\n  display: none;\n}\n.vertical-layout.vertical-menu-modern.menu-collapsed .main-menu .navigation > li > a {\n  text-overflow: inherit;\n}\n[dir=ltr] .vertical-layout.vertical-menu-modern.menu-collapsed .app-content, [dir=ltr] .vertical-layout.vertical-menu-modern.menu-collapsed .footer {\n  margin-left: 80px;\n}\n[dir=rtl] .vertical-layout.vertical-menu-modern.menu-collapsed .app-content, [dir=rtl] .vertical-layout.vertical-menu-modern.menu-collapsed .footer {\n  margin-right: 80px;\n}\n.vertical-layout.vertical-menu-modern.menu-collapsed .header-navbar.floating-nav {\n  width: calc(100vw - (100vw - 100%) - 4.4rem - 74px);\n}\n.vertical-layout.vertical-menu-modern.menu-collapsed .header-navbar.navbar-static-top {\n  width: calc(100vw - (100vw - 100%) - 74px);\n}\n[dir=ltr] .vertical-layout.vertical-menu-modern.menu-collapsed .header-navbar.navbar-static-top {\n  left: 74px;\n}\n[dir=rtl] .vertical-layout.vertical-menu-modern.menu-collapsed .header-navbar.navbar-static-top {\n  right: 74px;\n}\n[dir=ltr] .vertical-layout.vertical-menu-modern .toggle-icon, [dir=ltr] .vertical-layout.vertical-menu-modern .collapse-toggle-icon {\n  margin-right: 0.425rem;\n}\n[dir=rtl] .vertical-layout.vertical-menu-modern .toggle-icon, [dir=rtl] .vertical-layout.vertical-menu-modern .collapse-toggle-icon {\n  margin-left: 0.425rem;\n}\n.vertical-layout.vertical-menu-modern .toggle-icon:focus,\n.vertical-layout.vertical-menu-modern .collapse-toggle-icon:focus {\n  outline: none;\n}\n@media (min-width: 992px) {\n.vertical-layout.vertical-menu-modern .main-menu {\n    width: 260px;\n}\n}\n@media (max-width: 1199.98px) {\n.vertical-layout.vertical-menu-modern .main-menu {\n    width: 0;\n}\n.vertical-layout.vertical-menu-modern .navbar .navbar-header {\n    width: 0;\n}\n[dir=ltr] .vertical-layout.vertical-menu-modern .content, [dir=ltr] .vertical-layout.vertical-menu-modern .footer {\n    margin-left: 0;\n}\n[dir=rtl] .vertical-layout.vertical-menu-modern .content, [dir=rtl] .vertical-layout.vertical-menu-modern .footer {\n    margin-right: 0;\n}\n[dir=ltr] .vertical-layout.vertical-menu-modern.menu-collapsed .app-content, [dir=ltr] .vertical-layout.vertical-menu-modern.menu-collapsed .footer {\n    margin-left: 0;\n}\n[dir=rtl] .vertical-layout.vertical-menu-modern.menu-collapsed .app-content, [dir=rtl] .vertical-layout.vertical-menu-modern.menu-collapsed .footer {\n    margin-right: 0;\n}\n.vertical-layout.vertical-menu-modern.menu-collapsed .main-menu {\n    width: 0;\n}\n}\n@media (max-width: 767.98px) {\n.vertical-layout.vertical-menu-modern .main-menu {\n    width: 0;\n}\n.vertical-layout.vertical-menu-modern .navbar .navbar-header {\n    width: 0;\n}\n[dir=ltr] .vertical-layout.vertical-menu-modern .content, [dir=ltr] .vertical-layout.vertical-menu-modern .footer {\n    margin-left: 0;\n}\n[dir=rtl] .vertical-layout.vertical-menu-modern .content, [dir=rtl] .vertical-layout.vertical-menu-modern .footer {\n    margin-right: 0;\n}\n}\n@keyframes fadein {\nfrom {\n    opacity: 0;\n}\nto {\n    opacity: 1;\n}\n}\n@keyframes fadeout {\nfrom {\n    opacity: 1;\n}\nto {\n    opacity: 0;\n}\n}\n@media screen and (-ms-high-contrast: active), (-ms-high-contrast: none) {\n[dir=ltr] .vertical-menu-modern.vertical-layout .main-menu .navigation > li > a > span {\n    animation: none;\n}\n[dir=rtl] .vertical-menu-modern.vertical-layout .main-menu .navigation > li > a > span {\n    animation: none;\n}\n}\n/*=========================================================================================\n    File Name: vertical-overlay-menu.scss\n    Description: A overlay style vertical menu with show and hide support. It support\n    light & dark version, filpped layout, right side icons, native scroll and borders menu\n    item seperation.\n    ----------------------------------------------------------------------------------------\n    Item Name: Vuexy - Vuejs, React, Angular, HTML & Laravel Admin Dashboard Template\n    Author: PIXINVENT\n    Author URL: http://www.themeforest.net/user/pixinvent\n==========================================================================================*/\n[dir=ltr] .vertical-overlay-menu .content {\n  margin-left: 0;\n}\n[dir=rtl] .vertical-overlay-menu .content {\n  margin-right: 0;\n}\n.vertical-overlay-menu .navbar .navbar-header {\n  width: 260px;\n}\n[dir=ltr] .vertical-overlay-menu .navbar .navbar-header {\n  float: left;\n}\n[dir=rtl] .vertical-overlay-menu .navbar .navbar-header {\n  float: right;\n}\n.vertical-overlay-menu .main-menu, .vertical-overlay-menu.menu-hide .main-menu {\n  opacity: 0;\n  transition: width 0.25s, opacity 0.25s, transform 0.25s;\n  width: 260px;\n}\n[dir] .vertical-overlay-menu .main-menu, [dir] .vertical-overlay-menu.menu-hide .main-menu {\n  transform: translate3d(0, 0, 0);\n}\n[dir=ltr] .vertical-overlay-menu .main-menu, [dir=ltr] .vertical-overlay-menu.menu-hide .main-menu {\n  left: -260px;\n}\n[dir=rtl] .vertical-overlay-menu .main-menu, [dir=rtl] .vertical-overlay-menu.menu-hide .main-menu {\n  right: -260px;\n}\n.vertical-overlay-menu .main-menu .navigation > li > a > svg,\n.vertical-overlay-menu .main-menu .navigation > li > a > i {\n  transition: 200ms ease all;\n  height: 20px;\n  width: 20px;\n}\n[dir=ltr] .vertical-overlay-menu .main-menu .navigation > li > a > svg, [dir=ltr] .vertical-overlay-menu .main-menu .navigation > li > a > i {\n  margin-right: 14px;\n  float: left;\n}\n[dir=rtl] .vertical-overlay-menu .main-menu .navigation > li > a > svg, [dir=rtl] .vertical-overlay-menu .main-menu .navigation > li > a > i {\n  margin-left: 14px;\n  float: right;\n}\n.vertical-overlay-menu .main-menu .navigation > li > a > svg:before,\n.vertical-overlay-menu .main-menu .navigation > li > a > i:before {\n  transition: 200ms ease all;\n  font-size: 1.429rem;\n}\n.vertical-overlay-menu .main-menu .navigation li.has-sub > a:after {\n  content: \"\";\n  height: 1rem;\n  width: 1rem;\n  display: inline-block;\n  position: absolute;\n  top: 14px;\n  transition: all 0.2s ease-out;\n}\n[dir] .vertical-overlay-menu .main-menu .navigation li.has-sub > a:after {\n  background-image: url(\"data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%236e6b7b' stroke-width='2' stroke-linecap='round' stroke-linejoin='round' class='feather feather-chevron-right'%3E%3Cpolyline points='9 18 15 12 9 6'%3E%3C/polyline%3E%3C/svg%3E\");\n  background-repeat: no-repeat;\n  background-position: center;\n  background-size: 1rem;\n  transform: rotate(0deg);\n}\n[dir=ltr] .vertical-overlay-menu .main-menu .navigation li.has-sub > a:after {\n  right: 20px;\n}\n[dir=rtl] .vertical-overlay-menu .main-menu .navigation li.has-sub > a:after {\n  left: 20px;\n}\n[dir=ltr] .vertical-overlay-menu .main-menu .navigation li.has-sub.open:not(.menu-item-closing) > a:after {\n  transform: rotate(90deg);\n}\n[dir=rtl] .vertical-overlay-menu .main-menu .navigation li.has-sub.open:not(.menu-item-closing) > a:after {\n  transform: rotate(-90deg);\n}\n.vertical-overlay-menu .main-menu .navigation .navigation-header .feather-more-horizontal {\n  display: none;\n}\n.vertical-overlay-menu.menu-open .main-menu {\n  opacity: 1;\n  transition: width 0.25s, opacity 0.25s, transform 0.25s;\n}\n[dir=ltr] .vertical-overlay-menu.menu-open .main-menu {\n  transform: translate3d(260px, 0, 0);\n}\n[dir=rtl] .vertical-overlay-menu.menu-open .main-menu {\n  transform: translate3d(-260px, 0, 0);\n}", ""]);
// Exports
module.exports = exports;


/***/ }),

/***/ "./node_modules/style-loader/index.js!./node_modules/css-loader/dist/cjs.js!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src/index.js?!./node_modules/sass-loader/dist/cjs.js?!./node_modules/sass-loader/dist/cjs.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/@core/layouts/components/app-customizer/AppCustomizer.vue?vue&type=style&index=0&lang=scss&":
/*!**************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/style-loader!./node_modules/css-loader/dist/cjs.js!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src??ref--7-2!./node_modules/sass-loader/dist/cjs.js??ref--7-3!./node_modules/sass-loader/dist/cjs.js??ref--11-0!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/src/@core/layouts/components/app-customizer/AppCustomizer.vue?vue&type=style&index=0&lang=scss& ***!
  \**************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {


var content = __webpack_require__(/*! !../../../../../../../node_modules/css-loader/dist/cjs.js!../../../../../../../node_modules/vue-loader/lib/loaders/stylePostLoader.js!../../../../../../../node_modules/postcss-loader/src??ref--7-2!../../../../../../../node_modules/sass-loader/dist/cjs.js??ref--7-3!../../../../../../../node_modules/sass-loader/dist/cjs.js??ref--11-0!../../../../../../../node_modules/vue-loader/lib??vue-loader-options!./AppCustomizer.vue?vue&type=style&index=0&lang=scss& */ "./node_modules/css-loader/dist/cjs.js!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src/index.js?!./node_modules/sass-loader/dist/cjs.js?!./node_modules/sass-loader/dist/cjs.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/@core/layouts/components/app-customizer/AppCustomizer.vue?vue&type=style&index=0&lang=scss&");

if(typeof content === 'string') content = [[module.i, content, '']];

var transform;
var insertInto;



var options = {"hmr":true}

options.transform = transform
options.insertInto = undefined;

var update = __webpack_require__(/*! ../../../../../../../node_modules/style-loader/lib/addStyles.js */ "./node_modules/style-loader/lib/addStyles.js")(content, options);

if(content.locals) module.exports = content.locals;

if(false) {}

/***/ }),

/***/ "./node_modules/style-loader/index.js!./node_modules/css-loader/dist/cjs.js!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src/index.js?!./node_modules/sass-loader/dist/cjs.js?!./node_modules/sass-loader/dist/cjs.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/@core/layouts/components/app-customizer/AppCustomizer.vue?vue&type=style&index=1&id=4b46fe46&lang=scss&scoped=true&":
/*!**************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/style-loader!./node_modules/css-loader/dist/cjs.js!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src??ref--7-2!./node_modules/sass-loader/dist/cjs.js??ref--7-3!./node_modules/sass-loader/dist/cjs.js??ref--11-0!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/src/@core/layouts/components/app-customizer/AppCustomizer.vue?vue&type=style&index=1&id=4b46fe46&lang=scss&scoped=true& ***!
  \**************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {


var content = __webpack_require__(/*! !../../../../../../../node_modules/css-loader/dist/cjs.js!../../../../../../../node_modules/vue-loader/lib/loaders/stylePostLoader.js!../../../../../../../node_modules/postcss-loader/src??ref--7-2!../../../../../../../node_modules/sass-loader/dist/cjs.js??ref--7-3!../../../../../../../node_modules/sass-loader/dist/cjs.js??ref--11-0!../../../../../../../node_modules/vue-loader/lib??vue-loader-options!./AppCustomizer.vue?vue&type=style&index=1&id=4b46fe46&lang=scss&scoped=true& */ "./node_modules/css-loader/dist/cjs.js!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src/index.js?!./node_modules/sass-loader/dist/cjs.js?!./node_modules/sass-loader/dist/cjs.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/@core/layouts/components/app-customizer/AppCustomizer.vue?vue&type=style&index=1&id=4b46fe46&lang=scss&scoped=true&");

if(typeof content === 'string') content = [[module.i, content, '']];

var transform;
var insertInto;



var options = {"hmr":true}

options.transform = transform
options.insertInto = undefined;

var update = __webpack_require__(/*! ../../../../../../../node_modules/style-loader/lib/addStyles.js */ "./node_modules/style-loader/lib/addStyles.js")(content, options);

if(content.locals) module.exports = content.locals;

if(false) {}

/***/ }),

/***/ "./node_modules/style-loader/index.js!./node_modules/css-loader/dist/cjs.js!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src/index.js?!./node_modules/sass-loader/dist/cjs.js?!./node_modules/sass-loader/dist/cjs.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/@core/layouts/components/app-navbar/components/Module.vue?vue&type=style&index=0&id=552012bc&lang=scss&scoped=true&":
/*!**************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/style-loader!./node_modules/css-loader/dist/cjs.js!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src??ref--7-2!./node_modules/sass-loader/dist/cjs.js??ref--7-3!./node_modules/sass-loader/dist/cjs.js??ref--11-0!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/src/@core/layouts/components/app-navbar/components/Module.vue?vue&type=style&index=0&id=552012bc&lang=scss&scoped=true& ***!
  \**************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {


var content = __webpack_require__(/*! !../../../../../../../../node_modules/css-loader/dist/cjs.js!../../../../../../../../node_modules/vue-loader/lib/loaders/stylePostLoader.js!../../../../../../../../node_modules/postcss-loader/src??ref--7-2!../../../../../../../../node_modules/sass-loader/dist/cjs.js??ref--7-3!../../../../../../../../node_modules/sass-loader/dist/cjs.js??ref--11-0!../../../../../../../../node_modules/vue-loader/lib??vue-loader-options!./Module.vue?vue&type=style&index=0&id=552012bc&lang=scss&scoped=true& */ "./node_modules/css-loader/dist/cjs.js!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src/index.js?!./node_modules/sass-loader/dist/cjs.js?!./node_modules/sass-loader/dist/cjs.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/@core/layouts/components/app-navbar/components/Module.vue?vue&type=style&index=0&id=552012bc&lang=scss&scoped=true&");

if(typeof content === 'string') content = [[module.i, content, '']];

var transform;
var insertInto;



var options = {"hmr":true}

options.transform = transform
options.insertInto = undefined;

var update = __webpack_require__(/*! ../../../../../../../../node_modules/style-loader/lib/addStyles.js */ "./node_modules/style-loader/lib/addStyles.js")(content, options);

if(content.locals) module.exports = content.locals;

if(false) {}

/***/ }),

/***/ "./node_modules/style-loader/index.js!./node_modules/css-loader/dist/cjs.js!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src/index.js?!./node_modules/sass-loader/dist/cjs.js?!./node_modules/sass-loader/dist/cjs.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/@core/layouts/layout-vertical/components/vertical-nav-menu/VerticalNavMenu.vue?vue&type=style&index=0&lang=scss&":
/*!***********************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/style-loader!./node_modules/css-loader/dist/cjs.js!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src??ref--7-2!./node_modules/sass-loader/dist/cjs.js??ref--7-3!./node_modules/sass-loader/dist/cjs.js??ref--11-0!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/src/@core/layouts/layout-vertical/components/vertical-nav-menu/VerticalNavMenu.vue?vue&type=style&index=0&lang=scss& ***!
  \***********************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {


var content = __webpack_require__(/*! !../../../../../../../../node_modules/css-loader/dist/cjs.js!../../../../../../../../node_modules/vue-loader/lib/loaders/stylePostLoader.js!../../../../../../../../node_modules/postcss-loader/src??ref--7-2!../../../../../../../../node_modules/sass-loader/dist/cjs.js??ref--7-3!../../../../../../../../node_modules/sass-loader/dist/cjs.js??ref--11-0!../../../../../../../../node_modules/vue-loader/lib??vue-loader-options!./VerticalNavMenu.vue?vue&type=style&index=0&lang=scss& */ "./node_modules/css-loader/dist/cjs.js!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src/index.js?!./node_modules/sass-loader/dist/cjs.js?!./node_modules/sass-loader/dist/cjs.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/@core/layouts/layout-vertical/components/vertical-nav-menu/VerticalNavMenu.vue?vue&type=style&index=0&lang=scss&");

if(typeof content === 'string') content = [[module.i, content, '']];

var transform;
var insertInto;



var options = {"hmr":true}

options.transform = transform
options.insertInto = undefined;

var update = __webpack_require__(/*! ../../../../../../../../node_modules/style-loader/lib/addStyles.js */ "./node_modules/style-loader/lib/addStyles.js")(content, options);

if(content.locals) module.exports = content.locals;

if(false) {}

/***/ }),

/***/ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/@core/layouts/components/AppBreadcrumb.vue?vue&type=template&id=0c43242a&":
/*!**********************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/src/@core/layouts/components/AppBreadcrumb.vue?vue&type=template&id=0c43242a& ***!
  \**********************************************************************************************************************************************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "render", function() { return render; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "staticRenderFns", function() { return staticRenderFns; });
var render = function () {
  var _vm = this
  var _h = _vm.$createElement
  var _c = _vm._self._c || _h
  return _vm.$route.meta.breadcrumb || _vm.$route.meta.pageTitle
    ? _c(
        "b-row",
        { staticClass: "content-header" },
        [
          _c(
            "b-col",
            {
              staticClass: "content-header-left mb-2",
              attrs: { cols: "12", md: "9" },
            },
            [
              _c(
                "b-row",
                { staticClass: "breadcrumbs-top" },
                [
                  _c("b-col", { attrs: { cols: "12" } }, [
                    _c(
                      "h2",
                      {
                        staticClass:
                          "content-header-title float-left pr-1 mb-0",
                      },
                      [
                        _vm._v(
                          "\n           " +
                            _vm._s(_vm.t(_vm.$route.meta.pageTitle)) +
                            "\n         "
                        ),
                      ]
                    ),
                    _vm._v(" "),
                    _c(
                      "div",
                      { staticClass: "breadcrumb-wrapper" },
                      [
                        _c(
                          "b-breadcrumb",
                          [
                            _c(
                              "b-breadcrumb-item",
                              { attrs: { to: "/" } },
                              [
                                _c("feather-icon", {
                                  staticClass: "align-text-top",
                                  attrs: { icon: "HomeIcon", size: "16" },
                                }),
                              ],
                              1
                            ),
                            _vm._v(" "),
                            _vm._l(_vm.$route.meta.breadcrumb, function (item) {
                              return _c(
                                "b-breadcrumb-item",
                                {
                                  key: item.text,
                                  attrs: { active: item.active, to: item.to },
                                },
                                [
                                  _vm._v(
                                    "\n               " +
                                      _vm._s(_vm.t(item.text)) +
                                      "\n             "
                                  ),
                                ]
                              )
                            }),
                          ],
                          2
                        ),
                      ],
                      1
                    ),
                  ]),
                ],
                1
              ),
            ],
            1
          ),
          _vm._v(" "),
          _c(
            "b-col",
            {
              staticClass:
                "content-header-right text-md-right d-md-block d-none mb-1",
              attrs: { md: "3", cols: "12" },
            },
            [
              _c(
                "b-dropdown",
                {
                  attrs: {
                    variant: "link",
                    "no-caret": "",
                    "toggle-class": "p-0",
                    right: "",
                  },
                  scopedSlots: _vm._u(
                    [
                      {
                        key: "button-content",
                        fn: function () {
                          return [
                            _c(
                              "b-button",
                              {
                                directives: [
                                  {
                                    name: "ripple",
                                    rawName: "v-ripple.400",
                                    value: "rgba(255, 255, 255, 0.15)",
                                    expression: "'rgba(255, 255, 255, 0.15)'",
                                    modifiers: { 400: true },
                                  },
                                ],
                                staticClass: "btn-icon",
                                attrs: { variant: "primary" },
                              },
                              [
                                _c("feather-icon", {
                                  attrs: { icon: "SettingsIcon" },
                                }),
                              ],
                              1
                            ),
                          ]
                        },
                        proxy: true,
                      },
                    ],
                    null,
                    false,
                    1621059081
                  ),
                },
                [
                  _vm._v(" "),
                  _c(
                    "b-dropdown-item",
                    { attrs: { to: { name: "apps-todo" } } },
                    [
                      _c("feather-icon", {
                        attrs: { icon: "CheckSquareIcon", size: "16" },
                      }),
                      _vm._v(" "),
                      _c("span", { staticClass: "align-middle ml-50" }, [
                        _vm._v("Todo"),
                      ]),
                    ],
                    1
                  ),
                  _vm._v("\n\n     /* "),
                  _c(
                    "b-dropdown-item",
                    { attrs: { to: { name: "apps-chat" } } },
                    [
                      _c("feather-icon", {
                        attrs: { icon: "MessageSquareIcon", size: "16" },
                      }),
                      _vm._v(" "),
                      _c("span", { staticClass: "align-middle ml-50" }, [
                        _vm._v("Chat"),
                      ]),
                    ],
                    1
                  ),
                  _vm._v(" "),
                  _c(
                    "b-dropdown-item",
                    { attrs: { to: { name: "apps-email" } } },
                    [
                      _c("feather-icon", {
                        attrs: { icon: "MailIcon", size: "16" },
                      }),
                      _vm._v(" "),
                      _c("span", { staticClass: "align-middle ml-50" }, [
                        _vm._v("Email"),
                      ]),
                    ],
                    1
                  ),
                  _vm._v("\n*/\n       "),
                  _c(
                    "b-dropdown-item",
                    { attrs: { to: { name: "apps-calendar" } } },
                    [
                      _c("feather-icon", {
                        attrs: { icon: "CalendarIcon", size: "16" },
                      }),
                      _vm._v(" "),
                      _c("span", { staticClass: "align-middle ml-50" }, [
                        _vm._v("Calendar"),
                      ]),
                    ],
                    1
                  ),
                ],
                1
              ),
            ],
            1
          ),
        ],
        1
      )
    : _vm._e()
}
var staticRenderFns = []
render._withStripped = true



/***/ }),

/***/ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/@core/layouts/components/AppFooter.vue?vue&type=template&id=3448057c&":
/*!******************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/src/@core/layouts/components/AppFooter.vue?vue&type=template&id=3448057c& ***!
  \******************************************************************************************************************************************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "render", function() { return render; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "staticRenderFns", function() { return staticRenderFns; });
var render = function () {
  var _vm = this
  var _h = _vm.$createElement
  var _c = _vm._self._c || _h
  return _c("p", { staticClass: "clearfix mb-0" }, [
    _c(
      "span",
      { staticClass: "float-md-left d-block d-md-inline-block mt-25" },
      [
        _vm._v(
          "\n    COPYRIGHT  © " + _vm._s(new Date().getFullYear()) + "\n    "
        ),
        _c(
          "b-link",
          {
            staticClass: "ml-25",
            attrs: { href: "http://dianasoft.ro", target: "_blank" },
          },
          [_vm._v("Diana Soft S.R.L.")]
        ),
        _vm._v(" "),
        _c("span", { staticClass: "d-none d-sm-inline-block" }, [
          _vm._v(", All rights Reserved"),
        ]),
      ],
      1
    ),
  ])
}
var staticRenderFns = []
render._withStripped = true



/***/ }),

/***/ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/@core/layouts/components/app-customizer/AppCustomizer.vue?vue&type=template&id=4b46fe46&scoped=true&":
/*!*************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/src/@core/layouts/components/app-customizer/AppCustomizer.vue?vue&type=template&id=4b46fe46&scoped=true& ***!
  \*************************************************************************************************************************************************************************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "render", function() { return render; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "staticRenderFns", function() { return staticRenderFns; });
var render = function () {
  var _vm = this
  var _h = _vm.$createElement
  var _c = _vm._self._c || _h
  return _c(
    "div",
    {
      staticClass: "customizer d-none d-md-block",
      class: { open: _vm.isCustomizerOpen },
    },
    [
      _c(
        "b-link",
        {
          staticClass:
            "customizer-toggle d-flex align-items-center justify-content-center",
          on: {
            click: function ($event) {
              _vm.isCustomizerOpen = !_vm.isCustomizerOpen
            },
          },
        },
        [
          _c("feather-icon", {
            staticClass: "spinner",
            attrs: { icon: "SettingsIcon", size: "15" },
          }),
        ],
        1
      ),
      _vm._v(" "),
      _c(
        "div",
        {
          staticClass:
            "customizer-section d-flex justify-content-between align-items-center",
        },
        [
          _vm._m(0),
          _vm._v(" "),
          _c("feather-icon", {
            staticClass: "cursor-pointer",
            attrs: { icon: "XIcon", size: "18" },
            on: {
              click: function ($event) {
                _vm.isCustomizerOpen = !_vm.isCustomizerOpen
              },
            },
          }),
        ],
        1
      ),
      _vm._v(" "),
      _c(
        "vue-perfect-scrollbar",
        {
          staticClass: "ps-customizer-area scroll-area",
          attrs: { settings: _vm.perfectScrollbarSettings },
        },
        [
          _c(
            "div",
            { staticClass: "customizer-section" },
            [
              _c(
                "b-form-group",
                { attrs: { label: "Menu Layout" } },
                [
                  _c("b-form-radio-group", {
                    attrs: {
                      name: "layout-type",
                      options: _vm.layoutTypeOptions,
                    },
                    model: {
                      value: _vm.layoutType,
                      callback: function ($$v) {
                        _vm.layoutType = $$v
                      },
                      expression: "layoutType",
                    },
                  }),
                ],
                1
              ),
              _vm._v(" "),
              _vm.layoutType === "vertical"
                ? _c(
                    "div",
                    {
                      staticClass:
                        "d-flex justify-content-between align-items-center mt-2",
                    },
                    [
                      _c("span", { staticClass: "font-weight-bold" }, [
                        _vm._v("Menu Collapsed"),
                      ]),
                      _vm._v(" "),
                      _c("b-form-checkbox", {
                        staticClass: "mr-0",
                        attrs: {
                          name: "is-vertical-menu-collapsed",
                          switch: "",
                          inline: "",
                        },
                        model: {
                          value: _vm.isVerticalMenuCollapsed,
                          callback: function ($$v) {
                            _vm.isVerticalMenuCollapsed = $$v
                          },
                          expression: "isVerticalMenuCollapsed",
                        },
                      }),
                    ],
                    1
                  )
                : _vm._e(),
            ],
            1
          ),
        ]
      ),
    ],
    1
  )
}
var staticRenderFns = [
  function () {
    var _vm = this
    var _h = _vm.$createElement
    var _c = _vm._self._c || _h
    return _c("div", [
      _c("h4", { staticClass: "text-uppercase mb-0" }, [
        _vm._v("\n        Theme Customizer\n      "),
      ]),
      _vm._v(" "),
      _c("small", [_vm._v("Customize & Preview in Real Time")]),
    ])
  },
]
render._withStripped = true



/***/ }),

/***/ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/@core/layouts/components/app-navbar/components/Module.vue?vue&type=template&id=552012bc&scoped=true&":
/*!*************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/src/@core/layouts/components/app-navbar/components/Module.vue?vue&type=template&id=552012bc&scoped=true& ***!
  \*************************************************************************************************************************************************************************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "render", function() { return render; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "staticRenderFns", function() { return staticRenderFns; });
var render = function () {
  var _vm = this
  var _h = _vm.$createElement
  var _c = _vm._self._c || _h
  return _c(
    "ul",
    { staticClass: "nav navbar-nav module-antet" },
    _vm._l(_vm.vizibile, function (modul) {
      return _c(
        "li",
        { key: modul.ruta, staticClass: "nav-item" },
        [
          _c(
            "b-link",
            {
              staticClass: "nav-link d-flex align-items-center",
              class: { activ: _vm.esteActiv(modul) },
              attrs: { title: modul.descriere },
              on: {
                click: function ($event) {
                  return _vm.mergiLa(modul)
                },
              },
            },
            [
              modul.lockup
                ? _c("b-img", {
                    staticClass: "sigla-lockup d-none d-xl-block",
                    attrs: { src: modul.lockup, alt: modul.nume },
                  })
                : _vm._e(),
              _vm._v(" "),
              modul.sigla
                ? _c("b-img", {
                    staticClass: "sigla-modul",
                    class: { "d-xl-none": modul.lockup },
                    attrs: { src: modul.sigla, alt: modul.nume },
                  })
                : _vm._e(),
              _vm._v(" "),
              !modul.sigla
                ? _c(
                    "span",
                    {
                      staticClass:
                        "sigla-modul d-inline-flex align-items-center justify-content-center",
                    },
                    [
                      _c("feather-icon", {
                        attrs: { icon: modul.pictograma, size: "16" },
                      }),
                    ],
                    1
                  )
                : _vm._e(),
              _vm._v(" "),
              !modul.lockup
                ? _c("span", { staticClass: "d-none d-xl-inline ml-50" }, [
                    _vm._v(_vm._s(modul.nume)),
                  ])
                : _vm._e(),
            ],
            1
          ),
        ],
        1
      )
    }),
    0
  )
}
var staticRenderFns = []
render._withStripped = true



/***/ }),

/***/ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/@core/layouts/components/app-navbar/components/NotificationDropdown.vue?vue&type=template&id=5b31486c&":
/*!***************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/src/@core/layouts/components/app-navbar/components/NotificationDropdown.vue?vue&type=template&id=5b31486c& ***!
  \***************************************************************************************************************************************************************************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "render", function() { return render; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "staticRenderFns", function() { return staticRenderFns; });
var render = function () {
  var _vm = this
  var _h = _vm.$createElement
  var _c = _vm._self._c || _h
  return _c(
    "b-nav-item-dropdown",
    {
      staticClass: "dropdown-notification mr-25",
      attrs: { "menu-class": "dropdown-menu-media", right: "" },
      scopedSlots: _vm._u([
        {
          key: "button-content",
          fn: function () {
            return [
              _c("feather-icon", {
                staticClass: "text-body",
                attrs: {
                  badge: _vm.newMessages,
                  "badge-classes": "bg-danger",
                  icon: "BellIcon",
                  size: "21",
                },
              }),
            ]
          },
          proxy: true,
        },
      ]),
    },
    [
      _vm._v(" "),
      _c("li", { staticClass: "dropdown-menu-header" }, [
        !_vm.showAlerte
          ? _c("div", { staticClass: "dropdown-header d-flex" }, [
              _c("h4", { staticClass: "notification-title mb-0 mr-auto" }, [
                _vm._v("\n        Notificări\n      "),
              ]),
            ])
          : _c(
              "div",
              {
                staticClass: "dropdown-header d-flex",
                on: { click: _vm.vizualizeazaGrup },
              },
              [
                _c("h4", { staticClass: "notification-title mb-0 mr-auto" }, [
                  _vm._v("\n        " + _vm._s(this.titluGrup) + "\n      "),
                ]),
                _vm._v(" "),
                _c(
                  "b-badge",
                  { attrs: { pill: "", variant: "light-primary" } },
                  [
                    _vm._v(
                      "\n        " + _vm._s(this.nrMesajeGrup) + " New\n      "
                    ),
                  ]
                ),
                _vm._v(" "),
                _c("feather-icon", { attrs: { icon: "ChevronUpIcon" } }),
              ],
              1
            ),
      ]),
      _vm._v(" "),
      _c(
        "vue-perfect-scrollbar",
        {
          staticClass: "scrollable-container media-list scroll-area",
          attrs: { settings: _vm.perfectScrollbarSettings, tagname: "li" },
        },
        [
          _vm._l(_vm.notificationGrup, function (notification) {
            return _c(
              "b-link",
              {
                directives: [
                  {
                    name: "show",
                    rawName: "v-show",
                    value: !_vm.showAlerte,
                    expression: "!showAlerte",
                  },
                ],
                key: notification.notificationtype_id,
              },
              [
                _c(
                  "b-media",
                  {
                    on: {
                      click: function ($event) {
                        return _vm.afisezAlerteGrup(notification)
                      },
                    },
                  },
                  [
                    _c(
                      "p",
                      {
                        staticClass:
                          "media-heading d-flex justify-content-between",
                      },
                      [
                        _c("span", { staticClass: "font-weight-bolder" }, [
                          _vm._v(
                            "\n            " +
                              _vm._s(
                                notification.notificationtype
                                  ? notification.notificationtype.denumire
                                  : ""
                              ) +
                              " \n          "
                          ),
                        ]),
                        _vm._v(" "),
                        _c(
                          "b-badge",
                          { attrs: { pill: "", variant: "light-primary" } },
                          [
                            _vm._v(
                              "\n                  " +
                                _vm._s(notification.nrMesaje) +
                                " New\n                 "
                            ),
                            _c("feather-icon", {
                              attrs: { icon: "ChevronDownIcon" },
                            }),
                          ],
                          1
                        ),
                      ],
                      1
                    ),
                  ]
                ),
              ],
              1
            )
          }),
          _vm._v(" "),
          _vm._l(_vm.notificariFiltrate, function (notification) {
            return _c(
              "div",
              { key: notification.id, staticClass: "d-flex d-flex-row" },
              [
                _c(
                  "b-link",
                  {
                    directives: [
                      {
                        name: "show",
                        rawName: "v-show",
                        value: _vm.showAlerte,
                        expression: "showAlerte",
                      },
                    ],
                    attrs: { href: notification.link },
                  },
                  [
                    _c(
                      "b-media",
                      {
                        scopedSlots: _vm._u(
                          [
                            {
                              key: "aside",
                              fn: function () {
                                return [
                                  notification.icon
                                    ? _c(
                                        "b-avatar",
                                        {
                                          attrs: {
                                            size: "32",
                                            variant: notification.type,
                                          },
                                        },
                                        [
                                          _c("feather-icon", {
                                            attrs: { icon: notification.icon },
                                          }),
                                        ],
                                        1
                                      )
                                    : _vm._e(),
                                ]
                              },
                              proxy: true,
                            },
                          ],
                          null,
                          true
                        ),
                      },
                      [
                        _vm._v(" "),
                        _c(
                          "p",
                          {
                            staticClass:
                              "media-heading d-flex justify-content-between",
                          },
                          [
                            _c("span", { staticClass: "font-weight-bolder" }, [
                              _vm._v(
                                "\n            " +
                                  _vm._s(notification.title) +
                                  " \n          "
                              ),
                            ]),
                            _vm._v(" "),
                            _c(
                              "span",
                              {
                                directives: [
                                  {
                                    name: "show",
                                    rawName: "v-show",
                                    value: notification.created_at,
                                    expression: "notification.created_at",
                                  },
                                ],
                                staticClass: "notification-text",
                              },
                              [
                                _vm._v(
                                  "\n            " +
                                    _vm._s(
                                      new Date(
                                        notification.created_at
                                      ).toLocaleString("ro-RO")
                                    ) +
                                    "\n          "
                                ),
                              ]
                            ),
                          ]
                        ),
                        _vm._v(" "),
                        _c("small", { staticClass: "notification-text" }, [
                          _vm._v(_vm._s(notification.subtitle)),
                        ]),
                      ]
                    ),
                  ],
                  1
                ),
                _vm._v(" "),
                _c(
                  "b-button",
                  {
                    directives: [
                      {
                        name: "show",
                        rawName: "v-show",
                        value: _vm.showAlerte,
                        expression: "showAlerte",
                      },
                    ],
                    attrs: { size: "sm", variant: "flat-danger" },
                    on: {
                      click: function ($event) {
                        return _vm.markAsRead(notification.id)
                      },
                    },
                  },
                  [_c("feather-icon", { attrs: { icon: "XIcon" } })],
                  1
                ),
              ],
              1
            )
          }),
        ],
        2
      ),
      _vm._v(" "),
      _c(
        "li",
        { staticClass: "dropdown-menu-footer" },
        [
          _c(
            "b-button",
            {
              directives: [
                {
                  name: "show",
                  rawName: "v-show",
                  value: this.newMessages > 1,
                  expression: "this.newMessages>1",
                },
                {
                  name: "ripple",
                  rawName: "v-ripple.400",
                  value: "rgba(255, 255, 255, 0.15)",
                  expression: "'rgba(255, 255, 255, 0.15)'",
                  modifiers: { 400: true },
                },
              ],
              attrs: { variant: "flat-danger", block: "" },
              on: {
                click: function ($event) {
                  return _vm.markAsRead()
                },
              },
            },
            [
              _vm._v("Sterge toate\n    "),
              _c("feather-icon", { attrs: { icon: "XIcon" } }),
            ],
            1
          ),
          _vm._v(" "),
          _c(
            "small",
            {
              directives: [
                {
                  name: "show",
                  rawName: "v-show",
                  value: this.newMessages == 0,
                  expression: "this.newMessages==0",
                },
              ],
              staticClass: "notification-text",
            },
            [_vm._v("Nu exista notificari")]
          ),
        ],
        1
      ),
    ],
    1
  )
}
var staticRenderFns = []
render._withStripped = true



/***/ }),

/***/ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/@core/layouts/components/app-navbar/components/UserDropdown.vue?vue&type=template&id=75d5934c&":
/*!*******************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/src/@core/layouts/components/app-navbar/components/UserDropdown.vue?vue&type=template&id=75d5934c& ***!
  \*******************************************************************************************************************************************************************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "render", function() { return render; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "staticRenderFns", function() { return staticRenderFns; });
var render = function () {
  var _vm = this
  var _h = _vm.$createElement
  var _c = _vm._self._c || _h
  return _c(
    "b-nav-item-dropdown",
    {
      staticClass: "dropdown-user",
      attrs: {
        right: "",
        "toggle-class": "d-flex align-items-center dropdown-user-link",
      },
      scopedSlots: _vm._u([
        {
          key: "button-content",
          fn: function () {
            return [
              _c("div", { staticClass: "d-sm-flex d-none user-nav" }, [
                _c("p", { staticClass: "user-name font-weight-bolder mb-0" }, [
                  _vm._v("\n        " + _vm._s(_vm.userData.name) + "\n      "),
                ]),
                _vm._v(" "),
                _c("span", { staticClass: "user-status" }, [
                  _vm._v(_vm._s(_vm.userData.functia)),
                ]),
              ]),
              _vm._v(" "),
              _c(
                "b-avatar",
                {
                  staticClass: "badge-minimal",
                  attrs: {
                    size: "40",
                    src: _vm.userData.link_poza,
                    variant: "light-primary",
                    badge: "",
                    "badge-variant": "success",
                  },
                },
                [
                  !_vm.userData.name
                    ? _c("feather-icon", {
                        attrs: { icon: "UserIcon", size: "22" },
                      })
                    : _vm._e(),
                ],
                1
              ),
            ]
          },
          proxy: true,
        },
      ]),
    },
    [
      _vm._v(" "),
      _c(
        "b-dropdown-item",
        {
          attrs: {
            to: { name: "pages-account-setting" },
            "link-class": "d-flex align-items-center",
          },
        },
        [
          _c("feather-icon", {
            staticClass: "mr-50",
            attrs: { size: "16", icon: "SettingsIcon" },
          }),
          _vm._v(" "),
          _c("span", [_vm._v("Settings")]),
        ],
        1
      ),
      _vm._v(" "),
      _c("b-dropdown-divider"),
      _vm._v(" "),
      _c(
        "b-dropdown-item",
        {
          attrs: { "link-class": "d-flex align-items-center" },
          on: { click: _vm.logout },
        },
        [
          _c("feather-icon", {
            staticClass: "mr-50",
            attrs: { size: "16", icon: "LogOutIcon" },
          }),
          _vm._v(" "),
          _c("span", [_vm._v("Logout")]),
        ],
        1
      ),
    ],
    1
  )
}
var staticRenderFns = []
render._withStripped = true



/***/ }),

/***/ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/@core/layouts/components/layout-content-renderer/LayoutContentRendererDefault.vue?vue&type=template&id=215a1b24&":
/*!*************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/src/@core/layouts/components/layout-content-renderer/LayoutContentRendererDefault.vue?vue&type=template&id=215a1b24& ***!
  \*************************************************************************************************************************************************************************************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "render", function() { return render; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "staticRenderFns", function() { return staticRenderFns; });
var render = function () {
  var _vm = this
  var _h = _vm.$createElement
  var _c = _vm._self._c || _h
  return _c(
    "div",
    {
      staticClass: "app-content content",
      class: [
        { "show-overlay": _vm.$store.state.app.shallShowOverlay },
        _vm.$route.meta.contentClass,
      ],
    },
    [
      _c("div", { staticClass: "content-overlay" }),
      _vm._v(" "),
      _c("div", { staticClass: "header-navbar-shadow" }),
      _vm._v(" "),
      _c(
        "div",
        {
          staticClass: "content-wrapper",
          class: _vm.contentWidth === "boxed" ? "container p-0" : null,
        },
        [
          _vm._t("breadcrumb", function () {
            return [_c("app-breadcrumb")]
          }),
          _vm._v(" "),
          _c(
            "div",
            { staticClass: "content-body" },
            [
              _c(
                "transition",
                { attrs: { name: _vm.routerTransition, mode: "out-in" } },
                [_vm._t("default")],
                2
              ),
            ],
            1
          ),
        ],
        2
      ),
    ]
  )
}
var staticRenderFns = []
render._withStripped = true



/***/ }),

/***/ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/@core/layouts/components/layout-content-renderer/LayoutContentRendererLeft.vue?vue&type=template&id=7f5275ac&":
/*!**********************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/src/@core/layouts/components/layout-content-renderer/LayoutContentRendererLeft.vue?vue&type=template&id=7f5275ac& ***!
  \**********************************************************************************************************************************************************************************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "render", function() { return render; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "staticRenderFns", function() { return staticRenderFns; });
var render = function () {
  var _vm = this
  var _h = _vm.$createElement
  var _c = _vm._self._c || _h
  return _c(
    "div",
    {
      staticClass: "app-content content",
      class: [
        { "show-overlay": _vm.$store.state.app.shallShowOverlay },
        _vm.$route.meta.contentClass,
      ],
    },
    [
      _c("div", { staticClass: "content-overlay" }),
      _vm._v(" "),
      _c("div", { staticClass: "header-navbar-shadow" }),
      _vm._v(" "),
      _c(
        "transition",
        { attrs: { name: _vm.routerTransition, mode: "out-in" } },
        [
          _c(
            "div",
            {
              staticClass: "content-area-wrapper",
              class: _vm.contentWidth === "boxed" ? "container p-0" : null,
            },
            [
              _vm._t("breadcrumb", function () {
                return [_c("app-breadcrumb")]
              }),
              _vm._v(" "),
              _c("portal-target", {
                attrs: { name: "content-renderer-sidebar-left", slim: "" },
              }),
              _vm._v(" "),
              _c("div", { staticClass: "content-right" }, [
                _c("div", { staticClass: "content-wrapper" }, [
                  _c(
                    "div",
                    { staticClass: "content-body" },
                    [_vm._t("default")],
                    2
                  ),
                ]),
              ]),
            ],
            2
          ),
        ]
      ),
    ],
    1
  )
}
var staticRenderFns = []
render._withStripped = true



/***/ }),

/***/ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/@core/layouts/components/layout-content-renderer/LayoutContentRendererLeftDetached.vue?vue&type=template&id=397a2c88&":
/*!******************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/src/@core/layouts/components/layout-content-renderer/LayoutContentRendererLeftDetached.vue?vue&type=template&id=397a2c88& ***!
  \******************************************************************************************************************************************************************************************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "render", function() { return render; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "staticRenderFns", function() { return staticRenderFns; });
var render = function () {
  var _vm = this
  var _h = _vm.$createElement
  var _c = _vm._self._c || _h
  return _c(
    "div",
    {
      staticClass: "app-content content",
      class: [
        { "show-overlay": _vm.$store.state.app.shallShowOverlay },
        _vm.$route.meta.contentClass,
      ],
    },
    [
      _c("div", { staticClass: "content-overlay" }),
      _vm._v(" "),
      _c("div", { staticClass: "header-navbar-shadow" }),
      _vm._v(" "),
      _c(
        "transition",
        { attrs: { name: _vm.routerTransition, mode: "out-in" } },
        [
          _c(
            "div",
            {
              staticClass: "content-wrapper clearfix",
              class: _vm.contentWidth === "boxed" ? "container p-0" : null,
            },
            [
              _vm._t("breadcrumb", function () {
                return [_c("app-breadcrumb")]
              }),
              _vm._v(" "),
              _c("div", { staticClass: "content-detached content-right" }, [
                _c("div", { staticClass: "content-wrapper" }, [
                  _c(
                    "div",
                    { staticClass: "content-body" },
                    [_vm._t("default")],
                    2
                  ),
                ]),
              ]),
              _vm._v(" "),
              _c("portal-target", {
                attrs: {
                  name: "content-renderer-sidebar-detached-left",
                  slim: "",
                },
              }),
            ],
            2
          ),
        ]
      ),
    ],
    1
  )
}
var staticRenderFns = []
render._withStripped = true



/***/ }),

/***/ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/@core/layouts/layout-vertical/components/vertical-nav-menu/VerticalNavMenu.vue?vue&type=template&id=2f85298e&":
/*!**********************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/src/@core/layouts/layout-vertical/components/vertical-nav-menu/VerticalNavMenu.vue?vue&type=template&id=2f85298e& ***!
  \**********************************************************************************************************************************************************************************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "render", function() { return render; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "staticRenderFns", function() { return staticRenderFns; });
var render = function () {
  var _vm = this
  var _h = _vm.$createElement
  var _c = _vm._self._c || _h
  return _c(
    "div",
    {
      staticClass: "main-menu menu-fixed menu-accordion menu-shadow",
      class: [
        {
          expanded:
            !_vm.isVerticalMenuCollapsed ||
            (_vm.isVerticalMenuCollapsed && _vm.isMouseHovered),
        },
        _vm.skin === "light" || _vm.skin === "bordered"
          ? "menu-light"
          : "menu-dark",
      ],
      on: {
        mouseenter: function ($event) {
          return _vm.updateMouseHovered(true)
        },
        mouseleave: function ($event) {
          return _vm.updateMouseHovered(false)
        },
      },
    },
    [
      _c(
        "div",
        { staticClass: "navbar-header expanded" },
        [
          _vm._t(
            "header",
            function () {
              return [
                _c("ul", { staticClass: "nav navbar-nav flex-row" }, [
                  _c(
                    "li",
                    { staticClass: "nav-item mr-auto" },
                    [
                      _c(
                        "b-link",
                        { staticClass: "navbar-brand", attrs: { to: "/" } },
                        [
                          _c(
                            "span",
                            { staticClass: "brand-logo" },
                            [
                              _c("b-img", {
                                attrs: { src: _vm.appLogoImage, alt: "logo" },
                              }),
                            ],
                            1
                          ),
                          _vm._v(" "),
                          _c("h2", { staticClass: "brand-text" }, [
                            _vm._v(
                              "\n              " +
                                _vm._s(_vm.appName) +
                                "\n            "
                            ),
                          ]),
                        ]
                      ),
                    ],
                    1
                  ),
                  _vm._v(" "),
                  _c(
                    "li",
                    { staticClass: "nav-item nav-toggle" },
                    [
                      _c(
                        "b-link",
                        { staticClass: "nav-link modern-nav-toggle" },
                        [
                          _c("feather-icon", {
                            staticClass: "d-block d-xl-none",
                            attrs: { icon: "XIcon", size: "20" },
                            on: { click: _vm.toggleVerticalMenuActive },
                          }),
                          _vm._v(" "),
                          _c("feather-icon", {
                            staticClass:
                              "d-none d-xl-block collapse-toggle-icon",
                            attrs: {
                              icon: _vm.collapseTogglerIconFeather,
                              size: "20",
                            },
                            on: { click: _vm.toggleCollapsed },
                          }),
                        ],
                        1
                      ),
                    ],
                    1
                  ),
                ]),
              ]
            },
            {
              toggleVerticalMenuActive: _vm.toggleVerticalMenuActive,
              toggleCollapsed: _vm.toggleCollapsed,
              collapseTogglerIcon: _vm.collapseTogglerIcon,
            }
          ),
        ],
        2
      ),
      _vm._v(" "),
      _c("div", {
        staticClass: "shadow-bottom",
        class: { "d-block": _vm.shallShadowBottom },
      }),
      _vm._v(" "),
      _c(
        "vue-perfect-scrollbar",
        {
          staticClass: "main-menu-content scroll-area",
          attrs: { settings: _vm.perfectScrollbarSettings, tagname: "ul" },
          on: {
            "ps-scroll-y": function (evt) {
              _vm.shallShadowBottom = evt.srcElement.scrollTop > 0
            },
          },
        },
        [
          _c("vertical-nav-menu-items", {
            staticClass: "navigation navigation-main",
            attrs: { items: _vm.navMenuItems },
          }),
        ],
        1
      ),
    ],
    1
  )
}
var staticRenderFns = []
render._withStripped = true



/***/ }),

/***/ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/@core/layouts/layout-vertical/components/vertical-nav-menu/components/vertical-nav-menu-group/VerticalNavMenuGroup.vue?vue&type=template&id=4ef8cc54&":
/*!**************************************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/src/@core/layouts/layout-vertical/components/vertical-nav-menu/components/vertical-nav-menu-group/VerticalNavMenuGroup.vue?vue&type=template&id=4ef8cc54& ***!
  \**************************************************************************************************************************************************************************************************************************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "render", function() { return render; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "staticRenderFns", function() { return staticRenderFns; });
var render = function () {
  var _vm = this
  var _h = _vm.$createElement
  var _c = _vm._self._c || _h
  return _vm.canViewVerticalNavMenuGroup(_vm.item)
    ? _c(
        "li",
        {
          staticClass: "nav-item has-sub",
          class: {
            open: _vm.isOpen,
            disabled: _vm.item.disabled,
            "sidebar-group-active": _vm.isActive,
          },
        },
        [
          _c(
            "b-link",
            {
              staticClass: "d-flex align-items-center",
              on: {
                click: function () {
                  return _vm.updateGroupOpen(!_vm.isOpen)
                },
              },
            },
            [
              _c("feather-icon", {
                attrs: { icon: _vm.item.icon || "CircleIcon" },
              }),
              _vm._v(" "),
              _c("span", { staticClass: "menu-title text-truncate" }, [
                _vm._v(_vm._s(_vm.t(_vm.item.title))),
              ]),
              _vm._v(" "),
              _vm.item.tag
                ? _c(
                    "b-badge",
                    {
                      staticClass: "mr-1 ml-auto",
                      attrs: {
                        pill: "",
                        variant: _vm.item.tagVariant || "primary",
                      },
                    },
                    [_vm._v("\n      " + _vm._s(_vm.item.tag) + "\n    ")]
                  )
                : _vm._e(),
            ],
            1
          ),
          _vm._v(" "),
          _c(
            "b-collapse",
            {
              staticClass: "menu-content",
              attrs: { tag: "ul" },
              model: {
                value: _vm.isOpen,
                callback: function ($$v) {
                  _vm.isOpen = $$v
                },
                expression: "isOpen",
              },
            },
            _vm._l(_vm.item.children, function (child) {
              return _c(_vm.resolveNavItemComponent(child), {
                key: child.header || child.title,
                ref: "groupChild",
                refInFor: true,
                tag: "component",
                attrs: { item: child },
              })
            }),
            1
          ),
        ],
        1
      )
    : _vm._e()
}
var staticRenderFns = []
render._withStripped = true



/***/ }),

/***/ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/@core/layouts/layout-vertical/components/vertical-nav-menu/components/vertical-nav-menu-items/VerticalNavMenuItems.vue?vue&type=template&id=4db36714&":
/*!**************************************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/src/@core/layouts/layout-vertical/components/vertical-nav-menu/components/vertical-nav-menu-items/VerticalNavMenuItems.vue?vue&type=template&id=4db36714& ***!
  \**************************************************************************************************************************************************************************************************************************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "render", function() { return render; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "staticRenderFns", function() { return staticRenderFns; });
var render = function () {
  var _vm = this
  var _h = _vm.$createElement
  var _c = _vm._self._c || _h
  return _c(
    "ul",
    _vm._l(_vm.items, function (item) {
      return _c(_vm.resolveNavItemComponent(item), {
        key: item.header || item.title,
        tag: "component",
        attrs: { item: item },
      })
    }),
    1
  )
}
var staticRenderFns = []
render._withStripped = true



/***/ }),

/***/ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/@core/layouts/layout-vertical/components/vertical-nav-menu/components/vertical-nav-menu-link/VerticalNavMenuLink.vue?vue&type=template&id=4f4927f8&":
/*!************************************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/src/@core/layouts/layout-vertical/components/vertical-nav-menu/components/vertical-nav-menu-link/VerticalNavMenuLink.vue?vue&type=template&id=4f4927f8& ***!
  \************************************************************************************************************************************************************************************************************************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "render", function() { return render; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "staticRenderFns", function() { return staticRenderFns; });
var render = function () {
  var _vm = this
  var _h = _vm.$createElement
  var _c = _vm._self._c || _h
  return _vm.canViewVerticalNavMenuLink(_vm.item)
    ? _c(
        "li",
        {
          staticClass: "nav-item",
          class: {
            active: _vm.isActive,
            disabled: _vm.item.disabled,
          },
        },
        [
          _c(
            "b-link",
            _vm._b(
              { staticClass: "d-flex align-items-center" },
              "b-link",
              _vm.linkProps,
              false
            ),
            [
              _c("feather-icon", {
                attrs: { icon: _vm.item.icon || "CircleIcon" },
              }),
              _vm._v(" "),
              _c("span", { staticClass: "menu-title text-truncate" }, [
                _vm._v(_vm._s(_vm.t(_vm.item.title))),
              ]),
              _vm._v(" "),
              _vm.item.tag
                ? _c(
                    "b-badge",
                    {
                      staticClass: "mr-1 ml-auto",
                      attrs: {
                        pill: "",
                        variant: _vm.item.tagVariant || "primary",
                      },
                    },
                    [_vm._v("\n      " + _vm._s(_vm.item.tag) + "\n    ")]
                  )
                : _vm._e(),
            ],
            1
          ),
        ],
        1
      )
    : _vm._e()
}
var staticRenderFns = []
render._withStripped = true



/***/ }),

/***/ "./resources/js/src/@core/layouts/components/AppBreadcrumb.vue":
/*!*********************************************************************!*\
  !*** ./resources/js/src/@core/layouts/components/AppBreadcrumb.vue ***!
  \*********************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _AppBreadcrumb_vue_vue_type_template_id_0c43242a___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./AppBreadcrumb.vue?vue&type=template&id=0c43242a& */ "./resources/js/src/@core/layouts/components/AppBreadcrumb.vue?vue&type=template&id=0c43242a&");
/* harmony import */ var _AppBreadcrumb_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./AppBreadcrumb.vue?vue&type=script&lang=js& */ "./resources/js/src/@core/layouts/components/AppBreadcrumb.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport *//* harmony import */ var _node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../../../../../node_modules/vue-loader/lib/runtime/componentNormalizer.js */ "./node_modules/vue-loader/lib/runtime/componentNormalizer.js");





/* normalize component */

var component = Object(_node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__["default"])(
  _AppBreadcrumb_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__["default"],
  _AppBreadcrumb_vue_vue_type_template_id_0c43242a___WEBPACK_IMPORTED_MODULE_0__["render"],
  _AppBreadcrumb_vue_vue_type_template_id_0c43242a___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"],
  false,
  null,
  null,
  null
  
)

/* hot reload */
if (false) { var api; }
component.options.__file = "resources/js/src/@core/layouts/components/AppBreadcrumb.vue"
/* harmony default export */ __webpack_exports__["default"] = (component.exports);

/***/ }),

/***/ "./resources/js/src/@core/layouts/components/AppBreadcrumb.vue?vue&type=script&lang=js&":
/*!**********************************************************************************************!*\
  !*** ./resources/js/src/@core/layouts/components/AppBreadcrumb.vue?vue&type=script&lang=js& ***!
  \**********************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_AppBreadcrumb_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../../node_modules/babel-loader/lib??ref--4-0!../../../../../../node_modules/vue-loader/lib??vue-loader-options!./AppBreadcrumb.vue?vue&type=script&lang=js& */ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/@core/layouts/components/AppBreadcrumb.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__["default"] = (_node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_AppBreadcrumb_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__["default"]); 

/***/ }),

/***/ "./resources/js/src/@core/layouts/components/AppBreadcrumb.vue?vue&type=template&id=0c43242a&":
/*!****************************************************************************************************!*\
  !*** ./resources/js/src/@core/layouts/components/AppBreadcrumb.vue?vue&type=template&id=0c43242a& ***!
  \****************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_AppBreadcrumb_vue_vue_type_template_id_0c43242a___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../../node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!../../../../../../node_modules/vue-loader/lib??vue-loader-options!./AppBreadcrumb.vue?vue&type=template&id=0c43242a& */ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/@core/layouts/components/AppBreadcrumb.vue?vue&type=template&id=0c43242a&");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "render", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_AppBreadcrumb_vue_vue_type_template_id_0c43242a___WEBPACK_IMPORTED_MODULE_0__["render"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "staticRenderFns", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_AppBreadcrumb_vue_vue_type_template_id_0c43242a___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"]; });



/***/ }),

/***/ "./resources/js/src/@core/layouts/components/AppFooter.vue":
/*!*****************************************************************!*\
  !*** ./resources/js/src/@core/layouts/components/AppFooter.vue ***!
  \*****************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _AppFooter_vue_vue_type_template_id_3448057c___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./AppFooter.vue?vue&type=template&id=3448057c& */ "./resources/js/src/@core/layouts/components/AppFooter.vue?vue&type=template&id=3448057c&");
/* harmony import */ var _AppFooter_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./AppFooter.vue?vue&type=script&lang=js& */ "./resources/js/src/@core/layouts/components/AppFooter.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport *//* harmony import */ var _node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../../../../../node_modules/vue-loader/lib/runtime/componentNormalizer.js */ "./node_modules/vue-loader/lib/runtime/componentNormalizer.js");





/* normalize component */

var component = Object(_node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__["default"])(
  _AppFooter_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__["default"],
  _AppFooter_vue_vue_type_template_id_3448057c___WEBPACK_IMPORTED_MODULE_0__["render"],
  _AppFooter_vue_vue_type_template_id_3448057c___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"],
  false,
  null,
  null,
  null
  
)

/* hot reload */
if (false) { var api; }
component.options.__file = "resources/js/src/@core/layouts/components/AppFooter.vue"
/* harmony default export */ __webpack_exports__["default"] = (component.exports);

/***/ }),

/***/ "./resources/js/src/@core/layouts/components/AppFooter.vue?vue&type=script&lang=js&":
/*!******************************************************************************************!*\
  !*** ./resources/js/src/@core/layouts/components/AppFooter.vue?vue&type=script&lang=js& ***!
  \******************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_AppFooter_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../../node_modules/babel-loader/lib??ref--4-0!../../../../../../node_modules/vue-loader/lib??vue-loader-options!./AppFooter.vue?vue&type=script&lang=js& */ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/@core/layouts/components/AppFooter.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__["default"] = (_node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_AppFooter_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__["default"]); 

/***/ }),

/***/ "./resources/js/src/@core/layouts/components/AppFooter.vue?vue&type=template&id=3448057c&":
/*!************************************************************************************************!*\
  !*** ./resources/js/src/@core/layouts/components/AppFooter.vue?vue&type=template&id=3448057c& ***!
  \************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_AppFooter_vue_vue_type_template_id_3448057c___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../../node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!../../../../../../node_modules/vue-loader/lib??vue-loader-options!./AppFooter.vue?vue&type=template&id=3448057c& */ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/@core/layouts/components/AppFooter.vue?vue&type=template&id=3448057c&");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "render", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_AppFooter_vue_vue_type_template_id_3448057c___WEBPACK_IMPORTED_MODULE_0__["render"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "staticRenderFns", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_AppFooter_vue_vue_type_template_id_3448057c___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"]; });



/***/ }),

/***/ "./resources/js/src/@core/layouts/components/app-customizer/AppCustomizer.vue":
/*!************************************************************************************!*\
  !*** ./resources/js/src/@core/layouts/components/app-customizer/AppCustomizer.vue ***!
  \************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _AppCustomizer_vue_vue_type_template_id_4b46fe46_scoped_true___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./AppCustomizer.vue?vue&type=template&id=4b46fe46&scoped=true& */ "./resources/js/src/@core/layouts/components/app-customizer/AppCustomizer.vue?vue&type=template&id=4b46fe46&scoped=true&");
/* harmony import */ var _AppCustomizer_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./AppCustomizer.vue?vue&type=script&lang=js& */ "./resources/js/src/@core/layouts/components/app-customizer/AppCustomizer.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport *//* harmony import */ var _AppCustomizer_vue_vue_type_style_index_0_lang_scss___WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./AppCustomizer.vue?vue&type=style&index=0&lang=scss& */ "./resources/js/src/@core/layouts/components/app-customizer/AppCustomizer.vue?vue&type=style&index=0&lang=scss&");
/* harmony import */ var _AppCustomizer_vue_vue_type_style_index_1_id_4b46fe46_lang_scss_scoped_true___WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./AppCustomizer.vue?vue&type=style&index=1&id=4b46fe46&lang=scss&scoped=true& */ "./resources/js/src/@core/layouts/components/app-customizer/AppCustomizer.vue?vue&type=style&index=1&id=4b46fe46&lang=scss&scoped=true&");
/* harmony import */ var _node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ../../../../../../../node_modules/vue-loader/lib/runtime/componentNormalizer.js */ "./node_modules/vue-loader/lib/runtime/componentNormalizer.js");







/* normalize component */

var component = Object(_node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_4__["default"])(
  _AppCustomizer_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__["default"],
  _AppCustomizer_vue_vue_type_template_id_4b46fe46_scoped_true___WEBPACK_IMPORTED_MODULE_0__["render"],
  _AppCustomizer_vue_vue_type_template_id_4b46fe46_scoped_true___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"],
  false,
  null,
  "4b46fe46",
  null
  
)

/* hot reload */
if (false) { var api; }
component.options.__file = "resources/js/src/@core/layouts/components/app-customizer/AppCustomizer.vue"
/* harmony default export */ __webpack_exports__["default"] = (component.exports);

/***/ }),

/***/ "./resources/js/src/@core/layouts/components/app-customizer/AppCustomizer.vue?vue&type=script&lang=js&":
/*!*************************************************************************************************************!*\
  !*** ./resources/js/src/@core/layouts/components/app-customizer/AppCustomizer.vue?vue&type=script&lang=js& ***!
  \*************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_AppCustomizer_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../../../node_modules/babel-loader/lib??ref--4-0!../../../../../../../node_modules/vue-loader/lib??vue-loader-options!./AppCustomizer.vue?vue&type=script&lang=js& */ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/@core/layouts/components/app-customizer/AppCustomizer.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__["default"] = (_node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_AppCustomizer_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__["default"]); 

/***/ }),

/***/ "./resources/js/src/@core/layouts/components/app-customizer/AppCustomizer.vue?vue&type=style&index=0&lang=scss&":
/*!**********************************************************************************************************************!*\
  !*** ./resources/js/src/@core/layouts/components/app-customizer/AppCustomizer.vue?vue&type=style&index=0&lang=scss& ***!
  \**********************************************************************************************************************/
/*! no static exports found */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_style_loader_index_js_node_modules_css_loader_dist_cjs_js_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_7_2_node_modules_sass_loader_dist_cjs_js_ref_7_3_node_modules_sass_loader_dist_cjs_js_ref_11_0_node_modules_vue_loader_lib_index_js_vue_loader_options_AppCustomizer_vue_vue_type_style_index_0_lang_scss___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../../../node_modules/style-loader!../../../../../../../node_modules/css-loader/dist/cjs.js!../../../../../../../node_modules/vue-loader/lib/loaders/stylePostLoader.js!../../../../../../../node_modules/postcss-loader/src??ref--7-2!../../../../../../../node_modules/sass-loader/dist/cjs.js??ref--7-3!../../../../../../../node_modules/sass-loader/dist/cjs.js??ref--11-0!../../../../../../../node_modules/vue-loader/lib??vue-loader-options!./AppCustomizer.vue?vue&type=style&index=0&lang=scss& */ "./node_modules/style-loader/index.js!./node_modules/css-loader/dist/cjs.js!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src/index.js?!./node_modules/sass-loader/dist/cjs.js?!./node_modules/sass-loader/dist/cjs.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/@core/layouts/components/app-customizer/AppCustomizer.vue?vue&type=style&index=0&lang=scss&");
/* harmony import */ var _node_modules_style_loader_index_js_node_modules_css_loader_dist_cjs_js_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_7_2_node_modules_sass_loader_dist_cjs_js_ref_7_3_node_modules_sass_loader_dist_cjs_js_ref_11_0_node_modules_vue_loader_lib_index_js_vue_loader_options_AppCustomizer_vue_vue_type_style_index_0_lang_scss___WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_node_modules_style_loader_index_js_node_modules_css_loader_dist_cjs_js_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_7_2_node_modules_sass_loader_dist_cjs_js_ref_7_3_node_modules_sass_loader_dist_cjs_js_ref_11_0_node_modules_vue_loader_lib_index_js_vue_loader_options_AppCustomizer_vue_vue_type_style_index_0_lang_scss___WEBPACK_IMPORTED_MODULE_0__);
/* harmony reexport (unknown) */ for(var __WEBPACK_IMPORT_KEY__ in _node_modules_style_loader_index_js_node_modules_css_loader_dist_cjs_js_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_7_2_node_modules_sass_loader_dist_cjs_js_ref_7_3_node_modules_sass_loader_dist_cjs_js_ref_11_0_node_modules_vue_loader_lib_index_js_vue_loader_options_AppCustomizer_vue_vue_type_style_index_0_lang_scss___WEBPACK_IMPORTED_MODULE_0__) if(["default"].indexOf(__WEBPACK_IMPORT_KEY__) < 0) (function(key) { __webpack_require__.d(__webpack_exports__, key, function() { return _node_modules_style_loader_index_js_node_modules_css_loader_dist_cjs_js_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_7_2_node_modules_sass_loader_dist_cjs_js_ref_7_3_node_modules_sass_loader_dist_cjs_js_ref_11_0_node_modules_vue_loader_lib_index_js_vue_loader_options_AppCustomizer_vue_vue_type_style_index_0_lang_scss___WEBPACK_IMPORTED_MODULE_0__[key]; }) }(__WEBPACK_IMPORT_KEY__));


/***/ }),

/***/ "./resources/js/src/@core/layouts/components/app-customizer/AppCustomizer.vue?vue&type=style&index=1&id=4b46fe46&lang=scss&scoped=true&":
/*!**********************************************************************************************************************************************!*\
  !*** ./resources/js/src/@core/layouts/components/app-customizer/AppCustomizer.vue?vue&type=style&index=1&id=4b46fe46&lang=scss&scoped=true& ***!
  \**********************************************************************************************************************************************/
/*! no static exports found */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_style_loader_index_js_node_modules_css_loader_dist_cjs_js_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_7_2_node_modules_sass_loader_dist_cjs_js_ref_7_3_node_modules_sass_loader_dist_cjs_js_ref_11_0_node_modules_vue_loader_lib_index_js_vue_loader_options_AppCustomizer_vue_vue_type_style_index_1_id_4b46fe46_lang_scss_scoped_true___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../../../node_modules/style-loader!../../../../../../../node_modules/css-loader/dist/cjs.js!../../../../../../../node_modules/vue-loader/lib/loaders/stylePostLoader.js!../../../../../../../node_modules/postcss-loader/src??ref--7-2!../../../../../../../node_modules/sass-loader/dist/cjs.js??ref--7-3!../../../../../../../node_modules/sass-loader/dist/cjs.js??ref--11-0!../../../../../../../node_modules/vue-loader/lib??vue-loader-options!./AppCustomizer.vue?vue&type=style&index=1&id=4b46fe46&lang=scss&scoped=true& */ "./node_modules/style-loader/index.js!./node_modules/css-loader/dist/cjs.js!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src/index.js?!./node_modules/sass-loader/dist/cjs.js?!./node_modules/sass-loader/dist/cjs.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/@core/layouts/components/app-customizer/AppCustomizer.vue?vue&type=style&index=1&id=4b46fe46&lang=scss&scoped=true&");
/* harmony import */ var _node_modules_style_loader_index_js_node_modules_css_loader_dist_cjs_js_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_7_2_node_modules_sass_loader_dist_cjs_js_ref_7_3_node_modules_sass_loader_dist_cjs_js_ref_11_0_node_modules_vue_loader_lib_index_js_vue_loader_options_AppCustomizer_vue_vue_type_style_index_1_id_4b46fe46_lang_scss_scoped_true___WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_node_modules_style_loader_index_js_node_modules_css_loader_dist_cjs_js_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_7_2_node_modules_sass_loader_dist_cjs_js_ref_7_3_node_modules_sass_loader_dist_cjs_js_ref_11_0_node_modules_vue_loader_lib_index_js_vue_loader_options_AppCustomizer_vue_vue_type_style_index_1_id_4b46fe46_lang_scss_scoped_true___WEBPACK_IMPORTED_MODULE_0__);
/* harmony reexport (unknown) */ for(var __WEBPACK_IMPORT_KEY__ in _node_modules_style_loader_index_js_node_modules_css_loader_dist_cjs_js_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_7_2_node_modules_sass_loader_dist_cjs_js_ref_7_3_node_modules_sass_loader_dist_cjs_js_ref_11_0_node_modules_vue_loader_lib_index_js_vue_loader_options_AppCustomizer_vue_vue_type_style_index_1_id_4b46fe46_lang_scss_scoped_true___WEBPACK_IMPORTED_MODULE_0__) if(["default"].indexOf(__WEBPACK_IMPORT_KEY__) < 0) (function(key) { __webpack_require__.d(__webpack_exports__, key, function() { return _node_modules_style_loader_index_js_node_modules_css_loader_dist_cjs_js_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_7_2_node_modules_sass_loader_dist_cjs_js_ref_7_3_node_modules_sass_loader_dist_cjs_js_ref_11_0_node_modules_vue_loader_lib_index_js_vue_loader_options_AppCustomizer_vue_vue_type_style_index_1_id_4b46fe46_lang_scss_scoped_true___WEBPACK_IMPORTED_MODULE_0__[key]; }) }(__WEBPACK_IMPORT_KEY__));


/***/ }),

/***/ "./resources/js/src/@core/layouts/components/app-customizer/AppCustomizer.vue?vue&type=template&id=4b46fe46&scoped=true&":
/*!*******************************************************************************************************************************!*\
  !*** ./resources/js/src/@core/layouts/components/app-customizer/AppCustomizer.vue?vue&type=template&id=4b46fe46&scoped=true& ***!
  \*******************************************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_AppCustomizer_vue_vue_type_template_id_4b46fe46_scoped_true___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../../../node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!../../../../../../../node_modules/vue-loader/lib??vue-loader-options!./AppCustomizer.vue?vue&type=template&id=4b46fe46&scoped=true& */ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/@core/layouts/components/app-customizer/AppCustomizer.vue?vue&type=template&id=4b46fe46&scoped=true&");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "render", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_AppCustomizer_vue_vue_type_template_id_4b46fe46_scoped_true___WEBPACK_IMPORTED_MODULE_0__["render"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "staticRenderFns", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_AppCustomizer_vue_vue_type_template_id_4b46fe46_scoped_true___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"]; });



/***/ }),

/***/ "./resources/js/src/@core/layouts/components/app-customizer/useAppCustomizer.js":
/*!**************************************************************************************!*\
  !*** ./resources/js/src/@core/layouts/components/app-customizer/useAppCustomizer.js ***!
  \**************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return useAppCustomizer; });
/* harmony import */ var _vue_composition_api__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @vue/composition-api */ "./node_modules/@vue/composition-api/dist/vue-composition-api.mjs");
/* harmony import */ var _core_app_config_useAppConfig__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @core/app-config/useAppConfig */ "./resources/js/src/@core/app-config/useAppConfig.js");


function useAppCustomizer() {
  // Customizer
  var isCustomizerOpen = Object(_vue_composition_api__WEBPACK_IMPORTED_MODULE_0__["ref"])(false); // Skin

  var skinOptions = [{
    text: 'Light',
    value: 'light'
  }, {
    text: 'Bordered',
    value: 'bordered'
  }, {
    text: 'Dark',
    value: 'dark'
  }, {
    text: 'Semi Dark',
    value: 'semi-dark'
  }]; // Content Width Options

  var contentWidthOptions = [{
    text: 'Full  Width',
    value: 'full'
  }, {
    text: 'Boxed',
    value: 'boxed'
  }]; // Router Transition

  var routerTransitionOptions = [{
    title: 'Zoom Fade',
    value: 'zoom-fade'
  }, {
    title: 'Fade',
    value: 'fade'
  }, {
    title: 'Fade Bottom',
    value: 'fade-bottom'
  }, {
    title: 'Slide Fade',
    value: 'slide-fade'
  }, {
    title: 'Zoom Out',
    value: 'zoom-out'
  }, {
    title: 'None',
    value: 'none'
  }]; // Router Transition

  var layoutTypeOptions = [{
    text: 'Vertical',
    value: 'vertical'
  }, {
    text: 'Horizontal',
    value: 'horizontal'
  }]; // Navbar

  var navbarColors = ['', 'primary', 'secondary', 'success', 'danger', 'warning', 'info', 'dark']; // Navbar Types

  var navbarTypes = [{
    text: 'Floating',
    value: 'floating'
  }, {
    text: 'Sticky',
    value: 'sticky'
  }, {
    text: 'Static',
    value: 'static'
  }, {
    text: 'Hidden',
    value: 'hidden'
  }]; // Footer Types

  var footerTypes = [{
    text: 'Sticky',
    value: 'sticky'
  }, {
    text: 'Static',
    value: 'static'
  }, {
    text: 'Hidden',
    value: 'hidden'
  }]; // eslint-disable-next-line object-curly-newline

  var _useAppConfig = Object(_core_app_config_useAppConfig__WEBPACK_IMPORTED_MODULE_1__["default"])(),
      isRTL = _useAppConfig.isRTL,
      skin = _useAppConfig.skin,
      contentWidth = _useAppConfig.contentWidth,
      routerTransition = _useAppConfig.routerTransition,
      layoutType = _useAppConfig.layoutType,
      isNavMenuHidden = _useAppConfig.isNavMenuHidden,
      isVerticalMenuCollapsed = _useAppConfig.isVerticalMenuCollapsed,
      navbarBackgroundColor = _useAppConfig.navbarBackgroundColor,
      navbarType = _useAppConfig.navbarType,
      footerType = _useAppConfig.footerType;

  return {
    // Customizer
    isCustomizerOpen: isCustomizerOpen,
    // Vertical Menu
    isVerticalMenuCollapsed: isVerticalMenuCollapsed,
    // Skin
    skin: skin,
    skinOptions: skinOptions,
    // Content Width
    contentWidth: contentWidth,
    contentWidthOptions: contentWidthOptions,
    // RTL
    isRTL: isRTL,
    // routerTransition
    routerTransition: routerTransition,
    routerTransitionOptions: routerTransitionOptions,
    // Layout Type
    layoutType: layoutType,
    layoutTypeOptions: layoutTypeOptions,
    // NavMenu Hidden
    isNavMenuHidden: isNavMenuHidden,
    // Navbar
    navbarColors: navbarColors,
    navbarTypes: navbarTypes,
    navbarBackgroundColor: navbarBackgroundColor,
    navbarType: navbarType,
    // Footer
    footerTypes: footerTypes,
    footerType: footerType
  };
}

/***/ }),

/***/ "./resources/js/src/@core/layouts/components/app-navbar/components/Module.vue":
/*!************************************************************************************!*\
  !*** ./resources/js/src/@core/layouts/components/app-navbar/components/Module.vue ***!
  \************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _Module_vue_vue_type_template_id_552012bc_scoped_true___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./Module.vue?vue&type=template&id=552012bc&scoped=true& */ "./resources/js/src/@core/layouts/components/app-navbar/components/Module.vue?vue&type=template&id=552012bc&scoped=true&");
/* harmony import */ var _Module_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./Module.vue?vue&type=script&lang=js& */ "./resources/js/src/@core/layouts/components/app-navbar/components/Module.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport *//* harmony import */ var _Module_vue_vue_type_style_index_0_id_552012bc_lang_scss_scoped_true___WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./Module.vue?vue&type=style&index=0&id=552012bc&lang=scss&scoped=true& */ "./resources/js/src/@core/layouts/components/app-navbar/components/Module.vue?vue&type=style&index=0&id=552012bc&lang=scss&scoped=true&");
/* harmony import */ var _node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../../../../../../../../node_modules/vue-loader/lib/runtime/componentNormalizer.js */ "./node_modules/vue-loader/lib/runtime/componentNormalizer.js");






/* normalize component */

var component = Object(_node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_3__["default"])(
  _Module_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__["default"],
  _Module_vue_vue_type_template_id_552012bc_scoped_true___WEBPACK_IMPORTED_MODULE_0__["render"],
  _Module_vue_vue_type_template_id_552012bc_scoped_true___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"],
  false,
  null,
  "552012bc",
  null
  
)

/* hot reload */
if (false) { var api; }
component.options.__file = "resources/js/src/@core/layouts/components/app-navbar/components/Module.vue"
/* harmony default export */ __webpack_exports__["default"] = (component.exports);

/***/ }),

/***/ "./resources/js/src/@core/layouts/components/app-navbar/components/Module.vue?vue&type=script&lang=js&":
/*!*************************************************************************************************************!*\
  !*** ./resources/js/src/@core/layouts/components/app-navbar/components/Module.vue?vue&type=script&lang=js& ***!
  \*************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_Module_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../../../../node_modules/babel-loader/lib??ref--4-0!../../../../../../../../node_modules/vue-loader/lib??vue-loader-options!./Module.vue?vue&type=script&lang=js& */ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/@core/layouts/components/app-navbar/components/Module.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__["default"] = (_node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_Module_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__["default"]); 

/***/ }),

/***/ "./resources/js/src/@core/layouts/components/app-navbar/components/Module.vue?vue&type=style&index=0&id=552012bc&lang=scss&scoped=true&":
/*!**********************************************************************************************************************************************!*\
  !*** ./resources/js/src/@core/layouts/components/app-navbar/components/Module.vue?vue&type=style&index=0&id=552012bc&lang=scss&scoped=true& ***!
  \**********************************************************************************************************************************************/
/*! no static exports found */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_style_loader_index_js_node_modules_css_loader_dist_cjs_js_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_7_2_node_modules_sass_loader_dist_cjs_js_ref_7_3_node_modules_sass_loader_dist_cjs_js_ref_11_0_node_modules_vue_loader_lib_index_js_vue_loader_options_Module_vue_vue_type_style_index_0_id_552012bc_lang_scss_scoped_true___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../../../../node_modules/style-loader!../../../../../../../../node_modules/css-loader/dist/cjs.js!../../../../../../../../node_modules/vue-loader/lib/loaders/stylePostLoader.js!../../../../../../../../node_modules/postcss-loader/src??ref--7-2!../../../../../../../../node_modules/sass-loader/dist/cjs.js??ref--7-3!../../../../../../../../node_modules/sass-loader/dist/cjs.js??ref--11-0!../../../../../../../../node_modules/vue-loader/lib??vue-loader-options!./Module.vue?vue&type=style&index=0&id=552012bc&lang=scss&scoped=true& */ "./node_modules/style-loader/index.js!./node_modules/css-loader/dist/cjs.js!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src/index.js?!./node_modules/sass-loader/dist/cjs.js?!./node_modules/sass-loader/dist/cjs.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/@core/layouts/components/app-navbar/components/Module.vue?vue&type=style&index=0&id=552012bc&lang=scss&scoped=true&");
/* harmony import */ var _node_modules_style_loader_index_js_node_modules_css_loader_dist_cjs_js_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_7_2_node_modules_sass_loader_dist_cjs_js_ref_7_3_node_modules_sass_loader_dist_cjs_js_ref_11_0_node_modules_vue_loader_lib_index_js_vue_loader_options_Module_vue_vue_type_style_index_0_id_552012bc_lang_scss_scoped_true___WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_node_modules_style_loader_index_js_node_modules_css_loader_dist_cjs_js_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_7_2_node_modules_sass_loader_dist_cjs_js_ref_7_3_node_modules_sass_loader_dist_cjs_js_ref_11_0_node_modules_vue_loader_lib_index_js_vue_loader_options_Module_vue_vue_type_style_index_0_id_552012bc_lang_scss_scoped_true___WEBPACK_IMPORTED_MODULE_0__);
/* harmony reexport (unknown) */ for(var __WEBPACK_IMPORT_KEY__ in _node_modules_style_loader_index_js_node_modules_css_loader_dist_cjs_js_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_7_2_node_modules_sass_loader_dist_cjs_js_ref_7_3_node_modules_sass_loader_dist_cjs_js_ref_11_0_node_modules_vue_loader_lib_index_js_vue_loader_options_Module_vue_vue_type_style_index_0_id_552012bc_lang_scss_scoped_true___WEBPACK_IMPORTED_MODULE_0__) if(["default"].indexOf(__WEBPACK_IMPORT_KEY__) < 0) (function(key) { __webpack_require__.d(__webpack_exports__, key, function() { return _node_modules_style_loader_index_js_node_modules_css_loader_dist_cjs_js_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_7_2_node_modules_sass_loader_dist_cjs_js_ref_7_3_node_modules_sass_loader_dist_cjs_js_ref_11_0_node_modules_vue_loader_lib_index_js_vue_loader_options_Module_vue_vue_type_style_index_0_id_552012bc_lang_scss_scoped_true___WEBPACK_IMPORTED_MODULE_0__[key]; }) }(__WEBPACK_IMPORT_KEY__));


/***/ }),

/***/ "./resources/js/src/@core/layouts/components/app-navbar/components/Module.vue?vue&type=template&id=552012bc&scoped=true&":
/*!*******************************************************************************************************************************!*\
  !*** ./resources/js/src/@core/layouts/components/app-navbar/components/Module.vue?vue&type=template&id=552012bc&scoped=true& ***!
  \*******************************************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_Module_vue_vue_type_template_id_552012bc_scoped_true___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../../../../node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!../../../../../../../../node_modules/vue-loader/lib??vue-loader-options!./Module.vue?vue&type=template&id=552012bc&scoped=true& */ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/@core/layouts/components/app-navbar/components/Module.vue?vue&type=template&id=552012bc&scoped=true&");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "render", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_Module_vue_vue_type_template_id_552012bc_scoped_true___WEBPACK_IMPORTED_MODULE_0__["render"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "staticRenderFns", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_Module_vue_vue_type_template_id_552012bc_scoped_true___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"]; });



/***/ }),

/***/ "./resources/js/src/@core/layouts/components/app-navbar/components/NotificationDropdown.vue":
/*!**************************************************************************************************!*\
  !*** ./resources/js/src/@core/layouts/components/app-navbar/components/NotificationDropdown.vue ***!
  \**************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _NotificationDropdown_vue_vue_type_template_id_5b31486c___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./NotificationDropdown.vue?vue&type=template&id=5b31486c& */ "./resources/js/src/@core/layouts/components/app-navbar/components/NotificationDropdown.vue?vue&type=template&id=5b31486c&");
/* harmony import */ var _NotificationDropdown_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./NotificationDropdown.vue?vue&type=script&lang=js& */ "./resources/js/src/@core/layouts/components/app-navbar/components/NotificationDropdown.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport *//* harmony import */ var _node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../../../../../../../node_modules/vue-loader/lib/runtime/componentNormalizer.js */ "./node_modules/vue-loader/lib/runtime/componentNormalizer.js");





/* normalize component */

var component = Object(_node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__["default"])(
  _NotificationDropdown_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__["default"],
  _NotificationDropdown_vue_vue_type_template_id_5b31486c___WEBPACK_IMPORTED_MODULE_0__["render"],
  _NotificationDropdown_vue_vue_type_template_id_5b31486c___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"],
  false,
  null,
  null,
  null
  
)

/* hot reload */
if (false) { var api; }
component.options.__file = "resources/js/src/@core/layouts/components/app-navbar/components/NotificationDropdown.vue"
/* harmony default export */ __webpack_exports__["default"] = (component.exports);

/***/ }),

/***/ "./resources/js/src/@core/layouts/components/app-navbar/components/NotificationDropdown.vue?vue&type=script&lang=js&":
/*!***************************************************************************************************************************!*\
  !*** ./resources/js/src/@core/layouts/components/app-navbar/components/NotificationDropdown.vue?vue&type=script&lang=js& ***!
  \***************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_NotificationDropdown_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../../../../node_modules/babel-loader/lib??ref--4-0!../../../../../../../../node_modules/vue-loader/lib??vue-loader-options!./NotificationDropdown.vue?vue&type=script&lang=js& */ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/@core/layouts/components/app-navbar/components/NotificationDropdown.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__["default"] = (_node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_NotificationDropdown_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__["default"]); 

/***/ }),

/***/ "./resources/js/src/@core/layouts/components/app-navbar/components/NotificationDropdown.vue?vue&type=template&id=5b31486c&":
/*!*********************************************************************************************************************************!*\
  !*** ./resources/js/src/@core/layouts/components/app-navbar/components/NotificationDropdown.vue?vue&type=template&id=5b31486c& ***!
  \*********************************************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_NotificationDropdown_vue_vue_type_template_id_5b31486c___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../../../../node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!../../../../../../../../node_modules/vue-loader/lib??vue-loader-options!./NotificationDropdown.vue?vue&type=template&id=5b31486c& */ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/@core/layouts/components/app-navbar/components/NotificationDropdown.vue?vue&type=template&id=5b31486c&");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "render", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_NotificationDropdown_vue_vue_type_template_id_5b31486c___WEBPACK_IMPORTED_MODULE_0__["render"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "staticRenderFns", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_NotificationDropdown_vue_vue_type_template_id_5b31486c___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"]; });



/***/ }),

/***/ "./resources/js/src/@core/layouts/components/app-navbar/components/UserDropdown.vue":
/*!******************************************************************************************!*\
  !*** ./resources/js/src/@core/layouts/components/app-navbar/components/UserDropdown.vue ***!
  \******************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _UserDropdown_vue_vue_type_template_id_75d5934c___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./UserDropdown.vue?vue&type=template&id=75d5934c& */ "./resources/js/src/@core/layouts/components/app-navbar/components/UserDropdown.vue?vue&type=template&id=75d5934c&");
/* harmony import */ var _UserDropdown_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./UserDropdown.vue?vue&type=script&lang=js& */ "./resources/js/src/@core/layouts/components/app-navbar/components/UserDropdown.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport *//* harmony import */ var _node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../../../../../../../node_modules/vue-loader/lib/runtime/componentNormalizer.js */ "./node_modules/vue-loader/lib/runtime/componentNormalizer.js");





/* normalize component */

var component = Object(_node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__["default"])(
  _UserDropdown_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__["default"],
  _UserDropdown_vue_vue_type_template_id_75d5934c___WEBPACK_IMPORTED_MODULE_0__["render"],
  _UserDropdown_vue_vue_type_template_id_75d5934c___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"],
  false,
  null,
  null,
  null
  
)

/* hot reload */
if (false) { var api; }
component.options.__file = "resources/js/src/@core/layouts/components/app-navbar/components/UserDropdown.vue"
/* harmony default export */ __webpack_exports__["default"] = (component.exports);

/***/ }),

/***/ "./resources/js/src/@core/layouts/components/app-navbar/components/UserDropdown.vue?vue&type=script&lang=js&":
/*!*******************************************************************************************************************!*\
  !*** ./resources/js/src/@core/layouts/components/app-navbar/components/UserDropdown.vue?vue&type=script&lang=js& ***!
  \*******************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_UserDropdown_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../../../../node_modules/babel-loader/lib??ref--4-0!../../../../../../../../node_modules/vue-loader/lib??vue-loader-options!./UserDropdown.vue?vue&type=script&lang=js& */ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/@core/layouts/components/app-navbar/components/UserDropdown.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__["default"] = (_node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_UserDropdown_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__["default"]); 

/***/ }),

/***/ "./resources/js/src/@core/layouts/components/app-navbar/components/UserDropdown.vue?vue&type=template&id=75d5934c&":
/*!*************************************************************************************************************************!*\
  !*** ./resources/js/src/@core/layouts/components/app-navbar/components/UserDropdown.vue?vue&type=template&id=75d5934c& ***!
  \*************************************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_UserDropdown_vue_vue_type_template_id_75d5934c___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../../../../node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!../../../../../../../../node_modules/vue-loader/lib??vue-loader-options!./UserDropdown.vue?vue&type=template&id=75d5934c& */ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/@core/layouts/components/app-navbar/components/UserDropdown.vue?vue&type=template&id=75d5934c&");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "render", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_UserDropdown_vue_vue_type_template_id_75d5934c___WEBPACK_IMPORTED_MODULE_0__["render"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "staticRenderFns", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_UserDropdown_vue_vue_type_template_id_75d5934c___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"]; });



/***/ }),

/***/ "./resources/js/src/@core/layouts/components/layout-content-renderer/LayoutContentRendererDefault.vue":
/*!************************************************************************************************************!*\
  !*** ./resources/js/src/@core/layouts/components/layout-content-renderer/LayoutContentRendererDefault.vue ***!
  \************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _LayoutContentRendererDefault_vue_vue_type_template_id_215a1b24___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./LayoutContentRendererDefault.vue?vue&type=template&id=215a1b24& */ "./resources/js/src/@core/layouts/components/layout-content-renderer/LayoutContentRendererDefault.vue?vue&type=template&id=215a1b24&");
/* harmony import */ var _LayoutContentRendererDefault_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./LayoutContentRendererDefault.vue?vue&type=script&lang=js& */ "./resources/js/src/@core/layouts/components/layout-content-renderer/LayoutContentRendererDefault.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport *//* harmony import */ var _node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../../../../../../node_modules/vue-loader/lib/runtime/componentNormalizer.js */ "./node_modules/vue-loader/lib/runtime/componentNormalizer.js");





/* normalize component */

var component = Object(_node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__["default"])(
  _LayoutContentRendererDefault_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__["default"],
  _LayoutContentRendererDefault_vue_vue_type_template_id_215a1b24___WEBPACK_IMPORTED_MODULE_0__["render"],
  _LayoutContentRendererDefault_vue_vue_type_template_id_215a1b24___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"],
  false,
  null,
  null,
  null
  
)

/* hot reload */
if (false) { var api; }
component.options.__file = "resources/js/src/@core/layouts/components/layout-content-renderer/LayoutContentRendererDefault.vue"
/* harmony default export */ __webpack_exports__["default"] = (component.exports);

/***/ }),

/***/ "./resources/js/src/@core/layouts/components/layout-content-renderer/LayoutContentRendererDefault.vue?vue&type=script&lang=js&":
/*!*************************************************************************************************************************************!*\
  !*** ./resources/js/src/@core/layouts/components/layout-content-renderer/LayoutContentRendererDefault.vue?vue&type=script&lang=js& ***!
  \*************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_LayoutContentRendererDefault_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../../../node_modules/babel-loader/lib??ref--4-0!../../../../../../../node_modules/vue-loader/lib??vue-loader-options!./LayoutContentRendererDefault.vue?vue&type=script&lang=js& */ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/@core/layouts/components/layout-content-renderer/LayoutContentRendererDefault.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__["default"] = (_node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_LayoutContentRendererDefault_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__["default"]); 

/***/ }),

/***/ "./resources/js/src/@core/layouts/components/layout-content-renderer/LayoutContentRendererDefault.vue?vue&type=template&id=215a1b24&":
/*!*******************************************************************************************************************************************!*\
  !*** ./resources/js/src/@core/layouts/components/layout-content-renderer/LayoutContentRendererDefault.vue?vue&type=template&id=215a1b24& ***!
  \*******************************************************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_LayoutContentRendererDefault_vue_vue_type_template_id_215a1b24___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../../../node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!../../../../../../../node_modules/vue-loader/lib??vue-loader-options!./LayoutContentRendererDefault.vue?vue&type=template&id=215a1b24& */ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/@core/layouts/components/layout-content-renderer/LayoutContentRendererDefault.vue?vue&type=template&id=215a1b24&");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "render", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_LayoutContentRendererDefault_vue_vue_type_template_id_215a1b24___WEBPACK_IMPORTED_MODULE_0__["render"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "staticRenderFns", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_LayoutContentRendererDefault_vue_vue_type_template_id_215a1b24___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"]; });



/***/ }),

/***/ "./resources/js/src/@core/layouts/components/layout-content-renderer/LayoutContentRendererLeft.vue":
/*!*********************************************************************************************************!*\
  !*** ./resources/js/src/@core/layouts/components/layout-content-renderer/LayoutContentRendererLeft.vue ***!
  \*********************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _LayoutContentRendererLeft_vue_vue_type_template_id_7f5275ac___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./LayoutContentRendererLeft.vue?vue&type=template&id=7f5275ac& */ "./resources/js/src/@core/layouts/components/layout-content-renderer/LayoutContentRendererLeft.vue?vue&type=template&id=7f5275ac&");
/* harmony import */ var _LayoutContentRendererLeft_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./LayoutContentRendererLeft.vue?vue&type=script&lang=js& */ "./resources/js/src/@core/layouts/components/layout-content-renderer/LayoutContentRendererLeft.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport *//* harmony import */ var _node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../../../../../../node_modules/vue-loader/lib/runtime/componentNormalizer.js */ "./node_modules/vue-loader/lib/runtime/componentNormalizer.js");





/* normalize component */

var component = Object(_node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__["default"])(
  _LayoutContentRendererLeft_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__["default"],
  _LayoutContentRendererLeft_vue_vue_type_template_id_7f5275ac___WEBPACK_IMPORTED_MODULE_0__["render"],
  _LayoutContentRendererLeft_vue_vue_type_template_id_7f5275ac___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"],
  false,
  null,
  null,
  null
  
)

/* hot reload */
if (false) { var api; }
component.options.__file = "resources/js/src/@core/layouts/components/layout-content-renderer/LayoutContentRendererLeft.vue"
/* harmony default export */ __webpack_exports__["default"] = (component.exports);

/***/ }),

/***/ "./resources/js/src/@core/layouts/components/layout-content-renderer/LayoutContentRendererLeft.vue?vue&type=script&lang=js&":
/*!**********************************************************************************************************************************!*\
  !*** ./resources/js/src/@core/layouts/components/layout-content-renderer/LayoutContentRendererLeft.vue?vue&type=script&lang=js& ***!
  \**********************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_LayoutContentRendererLeft_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../../../node_modules/babel-loader/lib??ref--4-0!../../../../../../../node_modules/vue-loader/lib??vue-loader-options!./LayoutContentRendererLeft.vue?vue&type=script&lang=js& */ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/@core/layouts/components/layout-content-renderer/LayoutContentRendererLeft.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__["default"] = (_node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_LayoutContentRendererLeft_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__["default"]); 

/***/ }),

/***/ "./resources/js/src/@core/layouts/components/layout-content-renderer/LayoutContentRendererLeft.vue?vue&type=template&id=7f5275ac&":
/*!****************************************************************************************************************************************!*\
  !*** ./resources/js/src/@core/layouts/components/layout-content-renderer/LayoutContentRendererLeft.vue?vue&type=template&id=7f5275ac& ***!
  \****************************************************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_LayoutContentRendererLeft_vue_vue_type_template_id_7f5275ac___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../../../node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!../../../../../../../node_modules/vue-loader/lib??vue-loader-options!./LayoutContentRendererLeft.vue?vue&type=template&id=7f5275ac& */ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/@core/layouts/components/layout-content-renderer/LayoutContentRendererLeft.vue?vue&type=template&id=7f5275ac&");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "render", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_LayoutContentRendererLeft_vue_vue_type_template_id_7f5275ac___WEBPACK_IMPORTED_MODULE_0__["render"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "staticRenderFns", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_LayoutContentRendererLeft_vue_vue_type_template_id_7f5275ac___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"]; });



/***/ }),

/***/ "./resources/js/src/@core/layouts/components/layout-content-renderer/LayoutContentRendererLeftDetached.vue":
/*!*****************************************************************************************************************!*\
  !*** ./resources/js/src/@core/layouts/components/layout-content-renderer/LayoutContentRendererLeftDetached.vue ***!
  \*****************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _LayoutContentRendererLeftDetached_vue_vue_type_template_id_397a2c88___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./LayoutContentRendererLeftDetached.vue?vue&type=template&id=397a2c88& */ "./resources/js/src/@core/layouts/components/layout-content-renderer/LayoutContentRendererLeftDetached.vue?vue&type=template&id=397a2c88&");
/* harmony import */ var _LayoutContentRendererLeftDetached_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./LayoutContentRendererLeftDetached.vue?vue&type=script&lang=js& */ "./resources/js/src/@core/layouts/components/layout-content-renderer/LayoutContentRendererLeftDetached.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport *//* harmony import */ var _node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../../../../../../node_modules/vue-loader/lib/runtime/componentNormalizer.js */ "./node_modules/vue-loader/lib/runtime/componentNormalizer.js");





/* normalize component */

var component = Object(_node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__["default"])(
  _LayoutContentRendererLeftDetached_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__["default"],
  _LayoutContentRendererLeftDetached_vue_vue_type_template_id_397a2c88___WEBPACK_IMPORTED_MODULE_0__["render"],
  _LayoutContentRendererLeftDetached_vue_vue_type_template_id_397a2c88___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"],
  false,
  null,
  null,
  null
  
)

/* hot reload */
if (false) { var api; }
component.options.__file = "resources/js/src/@core/layouts/components/layout-content-renderer/LayoutContentRendererLeftDetached.vue"
/* harmony default export */ __webpack_exports__["default"] = (component.exports);

/***/ }),

/***/ "./resources/js/src/@core/layouts/components/layout-content-renderer/LayoutContentRendererLeftDetached.vue?vue&type=script&lang=js&":
/*!******************************************************************************************************************************************!*\
  !*** ./resources/js/src/@core/layouts/components/layout-content-renderer/LayoutContentRendererLeftDetached.vue?vue&type=script&lang=js& ***!
  \******************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_LayoutContentRendererLeftDetached_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../../../node_modules/babel-loader/lib??ref--4-0!../../../../../../../node_modules/vue-loader/lib??vue-loader-options!./LayoutContentRendererLeftDetached.vue?vue&type=script&lang=js& */ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/@core/layouts/components/layout-content-renderer/LayoutContentRendererLeftDetached.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__["default"] = (_node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_LayoutContentRendererLeftDetached_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__["default"]); 

/***/ }),

/***/ "./resources/js/src/@core/layouts/components/layout-content-renderer/LayoutContentRendererLeftDetached.vue?vue&type=template&id=397a2c88&":
/*!************************************************************************************************************************************************!*\
  !*** ./resources/js/src/@core/layouts/components/layout-content-renderer/LayoutContentRendererLeftDetached.vue?vue&type=template&id=397a2c88& ***!
  \************************************************************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_LayoutContentRendererLeftDetached_vue_vue_type_template_id_397a2c88___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../../../node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!../../../../../../../node_modules/vue-loader/lib??vue-loader-options!./LayoutContentRendererLeftDetached.vue?vue&type=template&id=397a2c88& */ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/@core/layouts/components/layout-content-renderer/LayoutContentRendererLeftDetached.vue?vue&type=template&id=397a2c88&");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "render", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_LayoutContentRendererLeftDetached_vue_vue_type_template_id_397a2c88___WEBPACK_IMPORTED_MODULE_0__["render"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "staticRenderFns", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_LayoutContentRendererLeftDetached_vue_vue_type_template_id_397a2c88___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"]; });



/***/ }),

/***/ "./resources/js/src/@core/layouts/layout-vertical/components/vertical-nav-menu/VerticalNavMenu.vue":
/*!*********************************************************************************************************!*\
  !*** ./resources/js/src/@core/layouts/layout-vertical/components/vertical-nav-menu/VerticalNavMenu.vue ***!
  \*********************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _VerticalNavMenu_vue_vue_type_template_id_2f85298e___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./VerticalNavMenu.vue?vue&type=template&id=2f85298e& */ "./resources/js/src/@core/layouts/layout-vertical/components/vertical-nav-menu/VerticalNavMenu.vue?vue&type=template&id=2f85298e&");
/* harmony import */ var _VerticalNavMenu_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./VerticalNavMenu.vue?vue&type=script&lang=js& */ "./resources/js/src/@core/layouts/layout-vertical/components/vertical-nav-menu/VerticalNavMenu.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport *//* harmony import */ var _VerticalNavMenu_vue_vue_type_style_index_0_lang_scss___WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./VerticalNavMenu.vue?vue&type=style&index=0&lang=scss& */ "./resources/js/src/@core/layouts/layout-vertical/components/vertical-nav-menu/VerticalNavMenu.vue?vue&type=style&index=0&lang=scss&");
/* harmony import */ var _node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../../../../../../../../node_modules/vue-loader/lib/runtime/componentNormalizer.js */ "./node_modules/vue-loader/lib/runtime/componentNormalizer.js");






/* normalize component */

var component = Object(_node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_3__["default"])(
  _VerticalNavMenu_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__["default"],
  _VerticalNavMenu_vue_vue_type_template_id_2f85298e___WEBPACK_IMPORTED_MODULE_0__["render"],
  _VerticalNavMenu_vue_vue_type_template_id_2f85298e___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"],
  false,
  null,
  null,
  null
  
)

/* hot reload */
if (false) { var api; }
component.options.__file = "resources/js/src/@core/layouts/layout-vertical/components/vertical-nav-menu/VerticalNavMenu.vue"
/* harmony default export */ __webpack_exports__["default"] = (component.exports);

/***/ }),

/***/ "./resources/js/src/@core/layouts/layout-vertical/components/vertical-nav-menu/VerticalNavMenu.vue?vue&type=script&lang=js&":
/*!**********************************************************************************************************************************!*\
  !*** ./resources/js/src/@core/layouts/layout-vertical/components/vertical-nav-menu/VerticalNavMenu.vue?vue&type=script&lang=js& ***!
  \**********************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_VerticalNavMenu_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../../../../node_modules/babel-loader/lib??ref--4-0!../../../../../../../../node_modules/vue-loader/lib??vue-loader-options!./VerticalNavMenu.vue?vue&type=script&lang=js& */ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/@core/layouts/layout-vertical/components/vertical-nav-menu/VerticalNavMenu.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__["default"] = (_node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_VerticalNavMenu_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__["default"]); 

/***/ }),

/***/ "./resources/js/src/@core/layouts/layout-vertical/components/vertical-nav-menu/VerticalNavMenu.vue?vue&type=style&index=0&lang=scss&":
/*!*******************************************************************************************************************************************!*\
  !*** ./resources/js/src/@core/layouts/layout-vertical/components/vertical-nav-menu/VerticalNavMenu.vue?vue&type=style&index=0&lang=scss& ***!
  \*******************************************************************************************************************************************/
/*! no static exports found */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_style_loader_index_js_node_modules_css_loader_dist_cjs_js_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_7_2_node_modules_sass_loader_dist_cjs_js_ref_7_3_node_modules_sass_loader_dist_cjs_js_ref_11_0_node_modules_vue_loader_lib_index_js_vue_loader_options_VerticalNavMenu_vue_vue_type_style_index_0_lang_scss___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../../../../node_modules/style-loader!../../../../../../../../node_modules/css-loader/dist/cjs.js!../../../../../../../../node_modules/vue-loader/lib/loaders/stylePostLoader.js!../../../../../../../../node_modules/postcss-loader/src??ref--7-2!../../../../../../../../node_modules/sass-loader/dist/cjs.js??ref--7-3!../../../../../../../../node_modules/sass-loader/dist/cjs.js??ref--11-0!../../../../../../../../node_modules/vue-loader/lib??vue-loader-options!./VerticalNavMenu.vue?vue&type=style&index=0&lang=scss& */ "./node_modules/style-loader/index.js!./node_modules/css-loader/dist/cjs.js!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src/index.js?!./node_modules/sass-loader/dist/cjs.js?!./node_modules/sass-loader/dist/cjs.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/@core/layouts/layout-vertical/components/vertical-nav-menu/VerticalNavMenu.vue?vue&type=style&index=0&lang=scss&");
/* harmony import */ var _node_modules_style_loader_index_js_node_modules_css_loader_dist_cjs_js_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_7_2_node_modules_sass_loader_dist_cjs_js_ref_7_3_node_modules_sass_loader_dist_cjs_js_ref_11_0_node_modules_vue_loader_lib_index_js_vue_loader_options_VerticalNavMenu_vue_vue_type_style_index_0_lang_scss___WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_node_modules_style_loader_index_js_node_modules_css_loader_dist_cjs_js_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_7_2_node_modules_sass_loader_dist_cjs_js_ref_7_3_node_modules_sass_loader_dist_cjs_js_ref_11_0_node_modules_vue_loader_lib_index_js_vue_loader_options_VerticalNavMenu_vue_vue_type_style_index_0_lang_scss___WEBPACK_IMPORTED_MODULE_0__);
/* harmony reexport (unknown) */ for(var __WEBPACK_IMPORT_KEY__ in _node_modules_style_loader_index_js_node_modules_css_loader_dist_cjs_js_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_7_2_node_modules_sass_loader_dist_cjs_js_ref_7_3_node_modules_sass_loader_dist_cjs_js_ref_11_0_node_modules_vue_loader_lib_index_js_vue_loader_options_VerticalNavMenu_vue_vue_type_style_index_0_lang_scss___WEBPACK_IMPORTED_MODULE_0__) if(["default"].indexOf(__WEBPACK_IMPORT_KEY__) < 0) (function(key) { __webpack_require__.d(__webpack_exports__, key, function() { return _node_modules_style_loader_index_js_node_modules_css_loader_dist_cjs_js_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_7_2_node_modules_sass_loader_dist_cjs_js_ref_7_3_node_modules_sass_loader_dist_cjs_js_ref_11_0_node_modules_vue_loader_lib_index_js_vue_loader_options_VerticalNavMenu_vue_vue_type_style_index_0_lang_scss___WEBPACK_IMPORTED_MODULE_0__[key]; }) }(__WEBPACK_IMPORT_KEY__));


/***/ }),

/***/ "./resources/js/src/@core/layouts/layout-vertical/components/vertical-nav-menu/VerticalNavMenu.vue?vue&type=template&id=2f85298e&":
/*!****************************************************************************************************************************************!*\
  !*** ./resources/js/src/@core/layouts/layout-vertical/components/vertical-nav-menu/VerticalNavMenu.vue?vue&type=template&id=2f85298e& ***!
  \****************************************************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_VerticalNavMenu_vue_vue_type_template_id_2f85298e___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../../../../node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!../../../../../../../../node_modules/vue-loader/lib??vue-loader-options!./VerticalNavMenu.vue?vue&type=template&id=2f85298e& */ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/@core/layouts/layout-vertical/components/vertical-nav-menu/VerticalNavMenu.vue?vue&type=template&id=2f85298e&");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "render", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_VerticalNavMenu_vue_vue_type_template_id_2f85298e___WEBPACK_IMPORTED_MODULE_0__["render"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "staticRenderFns", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_VerticalNavMenu_vue_vue_type_template_id_2f85298e___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"]; });



/***/ }),

/***/ "./resources/js/src/@core/layouts/layout-vertical/components/vertical-nav-menu/components/vertical-nav-menu-group/VerticalNavMenuGroup.vue":
/*!*************************************************************************************************************************************************!*\
  !*** ./resources/js/src/@core/layouts/layout-vertical/components/vertical-nav-menu/components/vertical-nav-menu-group/VerticalNavMenuGroup.vue ***!
  \*************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _VerticalNavMenuGroup_vue_vue_type_template_id_4ef8cc54___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./VerticalNavMenuGroup.vue?vue&type=template&id=4ef8cc54& */ "./resources/js/src/@core/layouts/layout-vertical/components/vertical-nav-menu/components/vertical-nav-menu-group/VerticalNavMenuGroup.vue?vue&type=template&id=4ef8cc54&");
/* harmony import */ var _VerticalNavMenuGroup_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./VerticalNavMenuGroup.vue?vue&type=script&lang=js& */ "./resources/js/src/@core/layouts/layout-vertical/components/vertical-nav-menu/components/vertical-nav-menu-group/VerticalNavMenuGroup.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport *//* harmony import */ var _node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../../../../../../../../../node_modules/vue-loader/lib/runtime/componentNormalizer.js */ "./node_modules/vue-loader/lib/runtime/componentNormalizer.js");





/* normalize component */

var component = Object(_node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__["default"])(
  _VerticalNavMenuGroup_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__["default"],
  _VerticalNavMenuGroup_vue_vue_type_template_id_4ef8cc54___WEBPACK_IMPORTED_MODULE_0__["render"],
  _VerticalNavMenuGroup_vue_vue_type_template_id_4ef8cc54___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"],
  false,
  null,
  null,
  null
  
)

/* hot reload */
if (false) { var api; }
component.options.__file = "resources/js/src/@core/layouts/layout-vertical/components/vertical-nav-menu/components/vertical-nav-menu-group/VerticalNavMenuGroup.vue"
/* harmony default export */ __webpack_exports__["default"] = (component.exports);

/***/ }),

/***/ "./resources/js/src/@core/layouts/layout-vertical/components/vertical-nav-menu/components/vertical-nav-menu-group/VerticalNavMenuGroup.vue?vue&type=script&lang=js&":
/*!**************************************************************************************************************************************************************************!*\
  !*** ./resources/js/src/@core/layouts/layout-vertical/components/vertical-nav-menu/components/vertical-nav-menu-group/VerticalNavMenuGroup.vue?vue&type=script&lang=js& ***!
  \**************************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_VerticalNavMenuGroup_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../../../../../../node_modules/babel-loader/lib??ref--4-0!../../../../../../../../../../node_modules/vue-loader/lib??vue-loader-options!./VerticalNavMenuGroup.vue?vue&type=script&lang=js& */ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/@core/layouts/layout-vertical/components/vertical-nav-menu/components/vertical-nav-menu-group/VerticalNavMenuGroup.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__["default"] = (_node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_VerticalNavMenuGroup_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__["default"]); 

/***/ }),

/***/ "./resources/js/src/@core/layouts/layout-vertical/components/vertical-nav-menu/components/vertical-nav-menu-group/VerticalNavMenuGroup.vue?vue&type=template&id=4ef8cc54&":
/*!********************************************************************************************************************************************************************************!*\
  !*** ./resources/js/src/@core/layouts/layout-vertical/components/vertical-nav-menu/components/vertical-nav-menu-group/VerticalNavMenuGroup.vue?vue&type=template&id=4ef8cc54& ***!
  \********************************************************************************************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_VerticalNavMenuGroup_vue_vue_type_template_id_4ef8cc54___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../../../../../../node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!../../../../../../../../../../node_modules/vue-loader/lib??vue-loader-options!./VerticalNavMenuGroup.vue?vue&type=template&id=4ef8cc54& */ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/@core/layouts/layout-vertical/components/vertical-nav-menu/components/vertical-nav-menu-group/VerticalNavMenuGroup.vue?vue&type=template&id=4ef8cc54&");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "render", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_VerticalNavMenuGroup_vue_vue_type_template_id_4ef8cc54___WEBPACK_IMPORTED_MODULE_0__["render"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "staticRenderFns", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_VerticalNavMenuGroup_vue_vue_type_template_id_4ef8cc54___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"]; });



/***/ }),

/***/ "./resources/js/src/@core/layouts/layout-vertical/components/vertical-nav-menu/components/vertical-nav-menu-group/mixinVerticalNavMenuGroup.js":
/*!*****************************************************************************************************************************************************!*\
  !*** ./resources/js/src/@core/layouts/layout-vertical/components/vertical-nav-menu/components/vertical-nav-menu-group/mixinVerticalNavMenuGroup.js ***!
  \*****************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = ({
  watch: {
    $route: {
      immediate: true,
      handler: function handler() {
        this.updateIsActive();
      }
    }
  }
});

/***/ }),

/***/ "./resources/js/src/@core/layouts/layout-vertical/components/vertical-nav-menu/components/vertical-nav-menu-group/useVerticalNavMenuGroup.js":
/*!***************************************************************************************************************************************************!*\
  !*** ./resources/js/src/@core/layouts/layout-vertical/components/vertical-nav-menu/components/vertical-nav-menu-group/useVerticalNavMenuGroup.js ***!
  \***************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return useVerticalNavMenuGroup; });
/* harmony import */ var core_js_modules_es_object_to_string_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! core-js/modules/es.object.to-string.js */ "./node_modules/core-js/modules/es.object.to-string.js");
/* harmony import */ var core_js_modules_es_object_to_string_js__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_es_object_to_string_js__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _vue_composition_api__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @vue/composition-api */ "./node_modules/@vue/composition-api/dist/vue-composition-api.mjs");
/* harmony import */ var _store__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @/store */ "./resources/js/src/store/index.js");
/* harmony import */ var _core_layouts_utils__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @core/layouts/utils */ "./resources/js/src/@core/layouts/utils.js");

// eslint-disable-next-line object-curly-newline



function useVerticalNavMenuGroup(item) {
  // ------------------------------------------------
  // isVerticalMenuCollapsed
  // ------------------------------------------------
  var isVerticalMenuCollapsed = Object(_vue_composition_api__WEBPACK_IMPORTED_MODULE_1__["computed"])(function () {
    return _store__WEBPACK_IMPORTED_MODULE_2__["default"].state.verticalMenu.isVerticalMenuCollapsed;
  });
  Object(_vue_composition_api__WEBPACK_IMPORTED_MODULE_1__["watch"])(isVerticalMenuCollapsed, function (val) {
    /* eslint-disable no-use-before-define */
    // * Handles case if routing is done outside of vertical menu
    // i.e. From Customizer Collapse or Using Link
    if (!isMouseHovered.value) {
      if (val) isOpen.value = false;else if (!val && isActive.value) isOpen.value = true;
    }
    /* eslint-enable */

  }); // ------------------------------------------------
  // isMouseHovered
  // ------------------------------------------------

  var isMouseHovered = Object(_vue_composition_api__WEBPACK_IMPORTED_MODULE_1__["inject"])('isMouseHovered'); // Collapse menu when menu is collapsed and show on open

  Object(_vue_composition_api__WEBPACK_IMPORTED_MODULE_1__["watch"])(isMouseHovered, function (val) {
    if (isVerticalMenuCollapsed.value) {
      // * we have used `val && val && isActive.value` to only open active menu on mouseEnter and close all menu on mouseLeave
      // * If we don't use `isActive.value` with `val` it can open other groups which are not active as well
      // eslint-disable-next-line no-use-before-define
      isOpen.value = val && isActive.value;
    }
  }); // ------------------------------------------------
  // openGroups
  // ------------------------------------------------

  var openGroups = Object(_vue_composition_api__WEBPACK_IMPORTED_MODULE_1__["inject"])('openGroups'); // Collapse other groups if one group is opened

  Object(_vue_composition_api__WEBPACK_IMPORTED_MODULE_1__["watch"])(openGroups, function (currentOpenGroups) {
    var clickedGroup = currentOpenGroups[currentOpenGroups.length - 1]; // If current group is not clicked group or current group is not active => Proceed with closing it
    // eslint-disable-next-line no-use-before-define

    if (clickedGroup !== item.title && !isActive.value) {
      // If clicked group is not child of current group
      // eslint-disable-next-line no-use-before-define
      if (!doesHaveChild(clickedGroup)) isOpen.value = false;
    }
  }); // ------------------------------------------------
  // isOpen
  // ------------------------------------------------

  var isOpen = Object(_vue_composition_api__WEBPACK_IMPORTED_MODULE_1__["ref"])(false);
  Object(_vue_composition_api__WEBPACK_IMPORTED_MODULE_1__["watch"])(isOpen, function (val) {
    // if group is opened push it to the array
    if (val) openGroups.value.push(item.title);
  });

  var updateGroupOpen = function updateGroupOpen(val) {
    // eslint-disable-next-line no-use-before-define
    isOpen.value = val;
  }; // ------------------------------------------------
  // isActive
  // ------------------------------------------------


  var isActive = Object(_vue_composition_api__WEBPACK_IMPORTED_MODULE_1__["ref"])(false);
  Object(_vue_composition_api__WEBPACK_IMPORTED_MODULE_1__["watch"])(isActive, function (val) {
    /*
      If menu is collapsed and not hovered(optional) then don't open group
    */
    if (val) {
      if (!isVerticalMenuCollapsed.value) isOpen.value = val;
    } else {
      isOpen.value = val;
    }
  });

  var updateIsActive = function updateIsActive() {
    isActive.value = Object(_core_layouts_utils__WEBPACK_IMPORTED_MODULE_3__["isNavGroupActive"])(item.children);
  }; // ------------------------------------------------
  // Other Methods
  // ------------------------------------------------


  var doesHaveChild = function doesHaveChild(title) {
    return item.children.some(function (child) {
      return child.title === title;
    });
  };

  return {
    isOpen: isOpen,
    isActive: isActive,
    updateGroupOpen: updateGroupOpen,
    openGroups: openGroups,
    isMouseHovered: isMouseHovered,
    updateIsActive: updateIsActive
  };
}

/***/ }),

/***/ "./resources/js/src/@core/layouts/layout-vertical/components/vertical-nav-menu/components/vertical-nav-menu-header/index.js":
/*!**********************************************************************************************************************************!*\
  !*** ./resources/js/src/@core/layouts/layout-vertical/components/vertical-nav-menu/components/vertical-nav-menu-header/index.js ***!
  \**********************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _core_libs_i18n__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @core/libs/i18n */ "./resources/js/src/@core/libs/i18n/index.js");
/* harmony import */ var _core_libs_acl__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @core/libs/acl */ "./resources/js/src/@core/libs/acl/index.js");



var _useI18nUtils = Object(_core_libs_i18n__WEBPACK_IMPORTED_MODULE_0__["useUtils"])(),
    t = _useI18nUtils.t;

var _useAclUtils = Object(_core_libs_acl__WEBPACK_IMPORTED_MODULE_1__["useUtils"])(),
    canViewVerticalNavMenuHeader = _useAclUtils.canViewVerticalNavMenuHeader;

/* harmony default export */ __webpack_exports__["default"] = ({
  props: {
    item: {
      type: Object,
      required: true
    }
  },
  render: function render(h) {
    var span = h('span', {}, t(this.item.header));
    var icon = h('feather-icon', {
      props: {
        icon: 'MoreHorizontalIcon',
        size: '18'
      }
    });

    if (canViewVerticalNavMenuHeader(this.item)) {
      return h('li', {
        "class": 'navigation-header text-truncate'
      }, [span, icon]);
    }

    return h();
  }
});

/***/ }),

/***/ "./resources/js/src/@core/layouts/layout-vertical/components/vertical-nav-menu/components/vertical-nav-menu-items/VerticalNavMenuItems.vue":
/*!*************************************************************************************************************************************************!*\
  !*** ./resources/js/src/@core/layouts/layout-vertical/components/vertical-nav-menu/components/vertical-nav-menu-items/VerticalNavMenuItems.vue ***!
  \*************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _VerticalNavMenuItems_vue_vue_type_template_id_4db36714___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./VerticalNavMenuItems.vue?vue&type=template&id=4db36714& */ "./resources/js/src/@core/layouts/layout-vertical/components/vertical-nav-menu/components/vertical-nav-menu-items/VerticalNavMenuItems.vue?vue&type=template&id=4db36714&");
/* harmony import */ var _VerticalNavMenuItems_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./VerticalNavMenuItems.vue?vue&type=script&lang=js& */ "./resources/js/src/@core/layouts/layout-vertical/components/vertical-nav-menu/components/vertical-nav-menu-items/VerticalNavMenuItems.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport *//* harmony import */ var _node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../../../../../../../../../node_modules/vue-loader/lib/runtime/componentNormalizer.js */ "./node_modules/vue-loader/lib/runtime/componentNormalizer.js");





/* normalize component */

var component = Object(_node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__["default"])(
  _VerticalNavMenuItems_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__["default"],
  _VerticalNavMenuItems_vue_vue_type_template_id_4db36714___WEBPACK_IMPORTED_MODULE_0__["render"],
  _VerticalNavMenuItems_vue_vue_type_template_id_4db36714___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"],
  false,
  null,
  null,
  null
  
)

/* hot reload */
if (false) { var api; }
component.options.__file = "resources/js/src/@core/layouts/layout-vertical/components/vertical-nav-menu/components/vertical-nav-menu-items/VerticalNavMenuItems.vue"
/* harmony default export */ __webpack_exports__["default"] = (component.exports);

/***/ }),

/***/ "./resources/js/src/@core/layouts/layout-vertical/components/vertical-nav-menu/components/vertical-nav-menu-items/VerticalNavMenuItems.vue?vue&type=script&lang=js&":
/*!**************************************************************************************************************************************************************************!*\
  !*** ./resources/js/src/@core/layouts/layout-vertical/components/vertical-nav-menu/components/vertical-nav-menu-items/VerticalNavMenuItems.vue?vue&type=script&lang=js& ***!
  \**************************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_VerticalNavMenuItems_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../../../../../../node_modules/babel-loader/lib??ref--4-0!../../../../../../../../../../node_modules/vue-loader/lib??vue-loader-options!./VerticalNavMenuItems.vue?vue&type=script&lang=js& */ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/@core/layouts/layout-vertical/components/vertical-nav-menu/components/vertical-nav-menu-items/VerticalNavMenuItems.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__["default"] = (_node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_VerticalNavMenuItems_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__["default"]); 

/***/ }),

/***/ "./resources/js/src/@core/layouts/layout-vertical/components/vertical-nav-menu/components/vertical-nav-menu-items/VerticalNavMenuItems.vue?vue&type=template&id=4db36714&":
/*!********************************************************************************************************************************************************************************!*\
  !*** ./resources/js/src/@core/layouts/layout-vertical/components/vertical-nav-menu/components/vertical-nav-menu-items/VerticalNavMenuItems.vue?vue&type=template&id=4db36714& ***!
  \********************************************************************************************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_VerticalNavMenuItems_vue_vue_type_template_id_4db36714___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../../../../../../node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!../../../../../../../../../../node_modules/vue-loader/lib??vue-loader-options!./VerticalNavMenuItems.vue?vue&type=template&id=4db36714& */ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/@core/layouts/layout-vertical/components/vertical-nav-menu/components/vertical-nav-menu-items/VerticalNavMenuItems.vue?vue&type=template&id=4db36714&");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "render", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_VerticalNavMenuItems_vue_vue_type_template_id_4db36714___WEBPACK_IMPORTED_MODULE_0__["render"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "staticRenderFns", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_VerticalNavMenuItems_vue_vue_type_template_id_4db36714___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"]; });



/***/ }),

/***/ "./resources/js/src/@core/layouts/layout-vertical/components/vertical-nav-menu/components/vertical-nav-menu-link/VerticalNavMenuLink.vue":
/*!***********************************************************************************************************************************************!*\
  !*** ./resources/js/src/@core/layouts/layout-vertical/components/vertical-nav-menu/components/vertical-nav-menu-link/VerticalNavMenuLink.vue ***!
  \***********************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _VerticalNavMenuLink_vue_vue_type_template_id_4f4927f8___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./VerticalNavMenuLink.vue?vue&type=template&id=4f4927f8& */ "./resources/js/src/@core/layouts/layout-vertical/components/vertical-nav-menu/components/vertical-nav-menu-link/VerticalNavMenuLink.vue?vue&type=template&id=4f4927f8&");
/* harmony import */ var _VerticalNavMenuLink_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./VerticalNavMenuLink.vue?vue&type=script&lang=js& */ "./resources/js/src/@core/layouts/layout-vertical/components/vertical-nav-menu/components/vertical-nav-menu-link/VerticalNavMenuLink.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport *//* harmony import */ var _node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../../../../../../../../../node_modules/vue-loader/lib/runtime/componentNormalizer.js */ "./node_modules/vue-loader/lib/runtime/componentNormalizer.js");





/* normalize component */

var component = Object(_node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__["default"])(
  _VerticalNavMenuLink_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__["default"],
  _VerticalNavMenuLink_vue_vue_type_template_id_4f4927f8___WEBPACK_IMPORTED_MODULE_0__["render"],
  _VerticalNavMenuLink_vue_vue_type_template_id_4f4927f8___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"],
  false,
  null,
  null,
  null
  
)

/* hot reload */
if (false) { var api; }
component.options.__file = "resources/js/src/@core/layouts/layout-vertical/components/vertical-nav-menu/components/vertical-nav-menu-link/VerticalNavMenuLink.vue"
/* harmony default export */ __webpack_exports__["default"] = (component.exports);

/***/ }),

/***/ "./resources/js/src/@core/layouts/layout-vertical/components/vertical-nav-menu/components/vertical-nav-menu-link/VerticalNavMenuLink.vue?vue&type=script&lang=js&":
/*!************************************************************************************************************************************************************************!*\
  !*** ./resources/js/src/@core/layouts/layout-vertical/components/vertical-nav-menu/components/vertical-nav-menu-link/VerticalNavMenuLink.vue?vue&type=script&lang=js& ***!
  \************************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_VerticalNavMenuLink_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../../../../../../node_modules/babel-loader/lib??ref--4-0!../../../../../../../../../../node_modules/vue-loader/lib??vue-loader-options!./VerticalNavMenuLink.vue?vue&type=script&lang=js& */ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/@core/layouts/layout-vertical/components/vertical-nav-menu/components/vertical-nav-menu-link/VerticalNavMenuLink.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__["default"] = (_node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_VerticalNavMenuLink_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__["default"]); 

/***/ }),

/***/ "./resources/js/src/@core/layouts/layout-vertical/components/vertical-nav-menu/components/vertical-nav-menu-link/VerticalNavMenuLink.vue?vue&type=template&id=4f4927f8&":
/*!******************************************************************************************************************************************************************************!*\
  !*** ./resources/js/src/@core/layouts/layout-vertical/components/vertical-nav-menu/components/vertical-nav-menu-link/VerticalNavMenuLink.vue?vue&type=template&id=4f4927f8& ***!
  \******************************************************************************************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_VerticalNavMenuLink_vue_vue_type_template_id_4f4927f8___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../../../../../../node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!../../../../../../../../../../node_modules/vue-loader/lib??vue-loader-options!./VerticalNavMenuLink.vue?vue&type=template&id=4f4927f8& */ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/@core/layouts/layout-vertical/components/vertical-nav-menu/components/vertical-nav-menu-link/VerticalNavMenuLink.vue?vue&type=template&id=4f4927f8&");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "render", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_VerticalNavMenuLink_vue_vue_type_template_id_4f4927f8___WEBPACK_IMPORTED_MODULE_0__["render"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "staticRenderFns", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_VerticalNavMenuLink_vue_vue_type_template_id_4f4927f8___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"]; });



/***/ }),

/***/ "./resources/js/src/@core/layouts/layout-vertical/components/vertical-nav-menu/components/vertical-nav-menu-link/mixinVerticalNavMenuLink.js":
/*!***************************************************************************************************************************************************!*\
  !*** ./resources/js/src/@core/layouts/layout-vertical/components/vertical-nav-menu/components/vertical-nav-menu-link/mixinVerticalNavMenuLink.js ***!
  \***************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony default export */ __webpack_exports__["default"] = ({
  watch: {
    $route: {
      immediate: true,
      handler: function handler() {
        this.updateIsActive();
      }
    }
  }
});

/***/ }),

/***/ "./resources/js/src/@core/layouts/layout-vertical/components/vertical-nav-menu/components/vertical-nav-menu-link/useVerticalNavMenuLink.js":
/*!*************************************************************************************************************************************************!*\
  !*** ./resources/js/src/@core/layouts/layout-vertical/components/vertical-nav-menu/components/vertical-nav-menu-link/useVerticalNavMenuLink.js ***!
  \*************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return useVerticalNavMenuLink; });
/* harmony import */ var _vue_composition_api__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @vue/composition-api */ "./node_modules/@vue/composition-api/dist/vue-composition-api.mjs");
/* harmony import */ var _core_layouts_utils__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @core/layouts/utils */ "./resources/js/src/@core/layouts/utils.js");


function useVerticalNavMenuLink(item) {
  var isActive = Object(_vue_composition_api__WEBPACK_IMPORTED_MODULE_0__["ref"])(false);
  var linkProps = Object(_core_layouts_utils__WEBPACK_IMPORTED_MODULE_1__["navLinkProps"])(item);

  var updateIsActive = function updateIsActive() {
    isActive.value = Object(_core_layouts_utils__WEBPACK_IMPORTED_MODULE_1__["isNavLinkActive"])(item);
  };

  return {
    isActive: isActive,
    linkProps: linkProps,
    updateIsActive: updateIsActive
  };
}

/***/ }),

/***/ "./resources/js/src/@core/layouts/layout-vertical/components/vertical-nav-menu/useVerticalNavMenu.js":
/*!***********************************************************************************************************!*\
  !*** ./resources/js/src/@core/layouts/layout-vertical/components/vertical-nav-menu/useVerticalNavMenu.js ***!
  \***********************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return useVerticalNavMenu; });
/* harmony import */ var _vue_composition_api__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @vue/composition-api */ "./node_modules/@vue/composition-api/dist/vue-composition-api.mjs");
/* harmony import */ var _store__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @/store */ "./resources/js/src/store/index.js");


function useVerticalNavMenu(props) {
  // ------------------------------------------------
  // isVerticalMenuCollapsed
  // ------------------------------------------------
  var isVerticalMenuCollapsed = Object(_vue_composition_api__WEBPACK_IMPORTED_MODULE_0__["computed"])({
    get: function get() {
      return _store__WEBPACK_IMPORTED_MODULE_1__["default"].state.verticalMenu.isVerticalMenuCollapsed;
    },
    set: function set(val) {
      _store__WEBPACK_IMPORTED_MODULE_1__["default"].commit('verticalMenu/UPDATE_VERTICAL_MENU_COLLAPSED', val);
    }
  }); // ------------------------------------------------
  // collapseTogglerIcon
  // ------------------------------------------------

  var collapseTogglerIcon = Object(_vue_composition_api__WEBPACK_IMPORTED_MODULE_0__["computed"])(function () {
    if (props.isVerticalMenuActive) {
      return isVerticalMenuCollapsed.value ? 'unpinned' : 'pinned';
    }

    return 'close';
  });
  var isMouseHovered = Object(_vue_composition_api__WEBPACK_IMPORTED_MODULE_0__["ref"])(false);

  var updateMouseHovered = function updateMouseHovered(val) {
    isMouseHovered.value = val;
  };

  var toggleCollapsed = function toggleCollapsed() {
    isVerticalMenuCollapsed.value = !isVerticalMenuCollapsed.value;
  };

  return {
    isMouseHovered: isMouseHovered,
    isVerticalMenuCollapsed: isVerticalMenuCollapsed,
    collapseTogglerIcon: collapseTogglerIcon,
    toggleCollapsed: toggleCollapsed,
    updateMouseHovered: updateMouseHovered
  };
}

/***/ }),

/***/ "./resources/js/src/@core/layouts/layout-vertical/useVerticalLayout.js":
/*!*****************************************************************************!*\
  !*** ./resources/js/src/@core/layouts/layout-vertical/useVerticalLayout.js ***!
  \*****************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return useVerticalLayout; });
/* harmony import */ var _vue_composition_api__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @vue/composition-api */ "./node_modules/@vue/composition-api/dist/vue-composition-api.mjs");
/* harmony import */ var _store__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @/store */ "./resources/js/src/store/index.js");


function useVerticalLayout(navbarType, footerType) {
  var isVerticalMenuActive = Object(_vue_composition_api__WEBPACK_IMPORTED_MODULE_0__["ref"])(true);

  var toggleVerticalMenuActive = function toggleVerticalMenuActive() {
    isVerticalMenuActive.value = !isVerticalMenuActive.value;
  };

  var currentBreakpoint = Object(_vue_composition_api__WEBPACK_IMPORTED_MODULE_0__["ref"])('xl');
  var isVerticalMenuCollapsed = Object(_vue_composition_api__WEBPACK_IMPORTED_MODULE_0__["computed"])(function () {
    return _store__WEBPACK_IMPORTED_MODULE_1__["default"].state.verticalMenu.isVerticalMenuCollapsed;
  });
  var layoutClasses = Object(_vue_composition_api__WEBPACK_IMPORTED_MODULE_0__["computed"])(function () {
    var classes = [];

    if (currentBreakpoint.value === 'xl') {
      classes.push('vertical-menu-modern');
      classes.push(isVerticalMenuCollapsed.value ? 'menu-collapsed' : 'menu-expanded');
    } else {
      classes.push('vertical-overlay-menu');
      classes.push(isVerticalMenuActive.value ? 'menu-open' : 'menu-hide');
    } // Navbar


    classes.push("navbar-".concat(navbarType.value)); // Footer

    if (footerType.value === 'sticky') classes.push('footer-fixed');
    if (footerType.value === 'static') classes.push('footer-static');
    if (footerType.value === 'hidden') classes.push('footer-hidden');
    return classes;
  }); // ------------------------------------------------
  // Resize handler for Breakpoint
  // ------------------------------------------------

  Object(_vue_composition_api__WEBPACK_IMPORTED_MODULE_0__["watch"])(currentBreakpoint, function (val) {
    isVerticalMenuActive.value = val === 'xl';
  });

  var resizeHandler = function resizeHandler() {
    // ? This closes vertical menu when title bar is shown/hidden in mobile browsers.
    // ? We will watch for breakpoint to overcome this issue
    // isVerticalMenuActive.value = window.innerWidth >= 1200
    // ! You can use store getter to get breakpoint
    if (window.innerWidth >= 1200) currentBreakpoint.value = 'xl';else if (window.innerWidth >= 992) currentBreakpoint.value = 'lg';else if (window.innerWidth >= 768) currentBreakpoint.value = 'md';else if (window.innerWidth >= 576) currentBreakpoint.value = 'sm';else currentBreakpoint.value = 'xs';
  };

  var overlayClasses = Object(_vue_composition_api__WEBPACK_IMPORTED_MODULE_0__["computed"])(function () {
    if (currentBreakpoint.value !== 'xl' && isVerticalMenuActive.value) return 'show';
    return null;
  });
  var navbarTypeClass = Object(_vue_composition_api__WEBPACK_IMPORTED_MODULE_0__["computed"])(function () {
    if (navbarType.value === 'sticky') return 'fixed-top';
    if (navbarType.value === 'static') return 'navbar-static-top';
    if (navbarType.value === 'hidden') return 'd-none';
    return 'floating-nav';
  });
  var footerTypeClass = Object(_vue_composition_api__WEBPACK_IMPORTED_MODULE_0__["computed"])(function () {
    if (footerType.value === 'static') return 'footer-static';
    if (footerType.value === 'hidden') return 'd-none';
    return '';
  });
  return {
    isVerticalMenuActive: isVerticalMenuActive,
    toggleVerticalMenuActive: toggleVerticalMenuActive,
    isVerticalMenuCollapsed: isVerticalMenuCollapsed,
    layoutClasses: layoutClasses,
    overlayClasses: overlayClasses,
    navbarTypeClass: navbarTypeClass,
    footerTypeClass: footerTypeClass,
    resizeHandler: resizeHandler
  };
}

/***/ }),

/***/ "./resources/js/src/@core/layouts/utils.js":
/*!*************************************************!*\
  !*** ./resources/js/src/@core/layouts/utils.js ***!
  \*************************************************/
/*! exports provided: resolveVerticalNavMenuItemComponent, resolveHorizontalNavMenuItemComponent, resolveNavDataRouteName, isNavLinkActive, isNavGroupActive, navLinkProps */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "resolveVerticalNavMenuItemComponent", function() { return resolveVerticalNavMenuItemComponent; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "resolveHorizontalNavMenuItemComponent", function() { return resolveHorizontalNavMenuItemComponent; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "resolveNavDataRouteName", function() { return resolveNavDataRouteName; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "isNavLinkActive", function() { return isNavLinkActive; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "isNavGroupActive", function() { return isNavGroupActive; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "navLinkProps", function() { return navLinkProps; });
/* harmony import */ var core_js_modules_es_function_name_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! core-js/modules/es.function.name.js */ "./node_modules/core-js/modules/es.function.name.js");
/* harmony import */ var core_js_modules_es_function_name_js__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_es_function_name_js__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var core_js_modules_es_object_to_string_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! core-js/modules/es.object.to-string.js */ "./node_modules/core-js/modules/es.object.to-string.js");
/* harmony import */ var core_js_modules_es_object_to_string_js__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_es_object_to_string_js__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _router__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @/router */ "./resources/js/src/router/index.js");
/* harmony import */ var _core_utils_utils__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @core/utils/utils */ "./resources/js/src/@core/utils/utils.js");
/* harmony import */ var _vue_composition_api__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! @vue/composition-api */ "./node_modules/@vue/composition-api/dist/vue-composition-api.mjs");





/**
 * Return which component to render based on it's data/context
 * @param {Object} item nav menu item
 */

var resolveVerticalNavMenuItemComponent = function resolveVerticalNavMenuItemComponent(item) {
  if (item.header) return 'vertical-nav-menu-header';
  if (item.children) return 'vertical-nav-menu-group';
  return 'vertical-nav-menu-link';
};
/**
 * Return which component to render based on it's data/context
 * @param {Object} item nav menu item
 */

var resolveHorizontalNavMenuItemComponent = function resolveHorizontalNavMenuItemComponent(item) {
  if (item.children) return 'horizontal-nav-menu-group';
  return 'horizontal-nav-menu-link';
};
/**
 * Return route name for navigation link
 * If link is string then it will assume it is route-name
 * IF link is object it will resolve the object and will return the link
 * @param {Object, String} link navigation link object/string
 */

var resolveNavDataRouteName = function resolveNavDataRouteName(link) {
  if (Object(_core_utils_utils__WEBPACK_IMPORTED_MODULE_3__["isObject"])(link.route)) {
    var _router$resolve = _router__WEBPACK_IMPORTED_MODULE_2__["default"].resolve(link.route),
        route = _router$resolve.route;

    return route.name;
  }

  return link.route;
};
/**
 * Check if nav-link is active
 * @param {Object} link nav-link object
 */

var isNavLinkActive = function isNavLinkActive(link) {
  // Matched routes array of current route
  var matchedRoutes = _router__WEBPACK_IMPORTED_MODULE_2__["default"].currentRoute.matched; // Check if provided route matches route's matched route

  var resolveRoutedName = resolveNavDataRouteName(link);
  if (!resolveRoutedName) return false;
  return matchedRoutes.some(function (route) {
    return route.name === resolveRoutedName || route.meta.navActiveLink === resolveRoutedName;
  });
};
/**
 * Check if nav group is
 * @param {Array} children Group children
 */

var isNavGroupActive = function isNavGroupActive(children) {
  // Matched routes array of current route
  var matchedRoutes = _router__WEBPACK_IMPORTED_MODULE_2__["default"].currentRoute.matched;
  return children.some(function (child) {
    // If child have children => It's group => Go deeper(recursive)
    if (child.children) {
      return isNavGroupActive(child.children);
    } // else it's link => Check for matched Route


    return isNavLinkActive(child, matchedRoutes);
  });
};
/**
 * Return b-link props to use
 * @param {Object, String} item navigation routeName or route Object provided in navigation data
 */
// prettier-ignore

var navLinkProps = function navLinkProps(item) {
  return Object(_vue_composition_api__WEBPACK_IMPORTED_MODULE_4__["computed"])(function () {
    var props = {}; // If route is string => it assumes => Create route object from route name
    // If route is not string => It assumes it's route object => returns route object

    if (item.route) props.to = typeof item.route === 'string' ? {
      name: item.route
    } : item.route;else {
      props.href = item.href;
      props.target = '_blank';
      props.rel = 'nofollow';
    }
    if (!props.target) props.target = item.target || null;
    return props;
  });
};

/***/ }),

/***/ "./resources/js/src/@core/libs/acl/index.js":
/*!**************************************************!*\
  !*** ./resources/js/src/@core/libs/acl/index.js ***!
  \**************************************************/
/*! exports provided: useUtils, _ */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "useUtils", function() { return useUtils; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "_", function() { return _; });
/* harmony import */ var C_dianasoft_serviciiweb_web_node_modules_babel_runtime_helpers_esm_objectSpread2_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./node_modules/@babel/runtime/helpers/esm/objectSpread2.js */ "./node_modules/@babel/runtime/helpers/esm/objectSpread2.js");
/* harmony import */ var _utils__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./utils */ "./resources/js/src/@core/libs/acl/utils.js");


var useUtils = function useUtils() {
  return Object(C_dianasoft_serviciiweb_web_node_modules_babel_runtime_helpers_esm_objectSpread2_js__WEBPACK_IMPORTED_MODULE_0__["default"])({}, _utils__WEBPACK_IMPORTED_MODULE_1__);
};
var _ = null;

/***/ }),

/***/ "./resources/js/src/@core/libs/acl/utils.js":
/*!**************************************************!*\
  !*** ./resources/js/src/@core/libs/acl/utils.js ***!
  \**************************************************/
/*! exports provided: can, canViewVerticalNavMenuLink, canViewVerticalNavMenuGroup, canViewVerticalNavMenuHeader, canViewHorizontalNavMenuLink, canViewHorizontalNavMenuHeaderLink, canViewHorizontalNavMenuGroup, canViewHorizontalNavMenuHeaderGroup */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "can", function() { return can; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "canViewVerticalNavMenuLink", function() { return canViewVerticalNavMenuLink; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "canViewVerticalNavMenuGroup", function() { return canViewVerticalNavMenuGroup; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "canViewVerticalNavMenuHeader", function() { return canViewVerticalNavMenuHeader; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "canViewHorizontalNavMenuLink", function() { return canViewHorizontalNavMenuLink; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "canViewHorizontalNavMenuHeaderLink", function() { return canViewHorizontalNavMenuHeaderLink; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "canViewHorizontalNavMenuGroup", function() { return canViewHorizontalNavMenuGroup; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "canViewHorizontalNavMenuHeaderGroup", function() { return canViewHorizontalNavMenuHeaderGroup; });
/* harmony import */ var core_js_modules_es_object_to_string_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! core-js/modules/es.object.to-string.js */ "./node_modules/core-js/modules/es.object.to-string.js");
/* harmony import */ var core_js_modules_es_object_to_string_js__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_es_object_to_string_js__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _vue_composition_api__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @vue/composition-api */ "./node_modules/@vue/composition-api/dist/vue-composition-api.mjs");


/**
 * Returns ability result if ACL is configured or else just return true
 * Useful if you don't know if ACL is configured or not
 * Used in @core files to handle absence of ACL without errors
 * @param {String} action CASL Actions // https://casl.js.org/v4/en/guide/intro#basics
 * @param {String} subject CASL Subject // https://casl.js.org/v4/en/guide/intro#basics
 */

var can = function can(action, subject) {
  return true;
  /*  const vm = getCurrentInstance().proxy
    return vm.$can ? vm.$can(action, subject) : true
    */
};
/**
 * Check if user can view item based on it's ability
 * Based on item's action and resource
 * @param {Object} item navigation object item
 */

var canViewVerticalNavMenuLink = function canViewVerticalNavMenuLink(item) {
  return can(item.action, item.resource);
};
/**
 * Check if user can view item based on it's ability
 * Based on item's action and resource & Hide group if all of it's children are hidden
 * @param {Object} item navigation object item
 */
// eslint-disable-next-line arrow-body-style

var canViewVerticalNavMenuGroup = function canViewVerticalNavMenuGroup(item) {
  // ! This same logic is used in canViewHorizontalNavMenuGroup and canViewHorizontalNavMenuHeaderGroup. So make sure to update logic in them as well
  var hasAnyVisibleChild = item.children.some(function (i) {
    return can(i.action, i.resource);
  }); // If resource and action is defined in item => Return based on children visibility (Hide group if no child is visible)
  // Else check for ability using provided resource and action along with checking if has any visible child

  if (!(item.action && item.resource)) {
    return hasAnyVisibleChild;
  }

  return can(item.action, item.resource) && hasAnyVisibleChild;
};
/**
 * Check if user can view item based on it's ability
 * Based on item's action and resource
 * @param {Object} item navigation object item
 */

var canViewVerticalNavMenuHeader = function canViewVerticalNavMenuHeader(item) {
  return can(item.action, item.resource);
};
/**
 * Check if user can view item based on it's ability
 * Based on item's action and resource
 * @param {Object} item navigation object item
 */

var canViewHorizontalNavMenuLink = function canViewHorizontalNavMenuLink(item) {
  return can(item.action, item.resource);
};
/**
 * Check if user can view item based on it's ability
 * Based on item's action and resource
 * @param {Object} item navigation object item
 */

var canViewHorizontalNavMenuHeaderLink = function canViewHorizontalNavMenuHeaderLink(item) {
  return can(item.action, item.resource);
};
/**
 * Check if user can view item based on it's ability
 * Based on item's action and resource & Hide group if all of it's children are hidden
 * @param {Object} item navigation object item
 */
// eslint-disable-next-line arrow-body-style

var canViewHorizontalNavMenuGroup = function canViewHorizontalNavMenuGroup(item) {
  // ? Same logic as canViewVerticalNavMenuGroup
  var hasAnyVisibleChild = item.children.some(function (i) {
    return can(i.action, i.resource);
  }); // If resource and action is defined in item => Return based on children visibility (Hide group if no child is visible)
  // Else check for ability using provided resource and action along with checking if has any visible child

  if (!(item.action && item.resource)) {
    return hasAnyVisibleChild;
  }

  return can(item.action, item.resource) && hasAnyVisibleChild;
}; // eslint-disable-next-line arrow-body-style

var canViewHorizontalNavMenuHeaderGroup = function canViewHorizontalNavMenuHeaderGroup(item) {
  // ? Same logic as canViewVerticalNavMenuGroup but with extra content
  // eslint-disable-next-line arrow-body-style
  var hasAnyVisibleChild = item.children.some(function (grpOrItem) {
    // If it have children => It's grp
    // Call ACL function based on grp/link
    return grpOrItem.children ? canViewHorizontalNavMenuGroup(grpOrItem) : canViewHorizontalNavMenuLink(grpOrItem);
  }); // If resource and action is defined in item => Return based on children visibility (Hide group if no child is visible)
  // Else check for ability using provided resource and action along with checking if has any visible child

  if (!(item.action && item.resource)) {
    return hasAnyVisibleChild;
  }

  return can(item.action, item.resource) && hasAnyVisibleChild;
};

/***/ }),

/***/ "./resources/js/src/@core/libs/i18n/index.js":
/*!***************************************************!*\
  !*** ./resources/js/src/@core/libs/i18n/index.js ***!
  \***************************************************/
/*! exports provided: useUtils, _ */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "useUtils", function() { return useUtils; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "_", function() { return _; });
/* harmony import */ var C_dianasoft_serviciiweb_web_node_modules_babel_runtime_helpers_esm_objectSpread2_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./node_modules/@babel/runtime/helpers/esm/objectSpread2.js */ "./node_modules/@babel/runtime/helpers/esm/objectSpread2.js");
/* harmony import */ var _utils__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./utils */ "./resources/js/src/@core/libs/i18n/utils.js");


var useUtils = function useUtils() {
  return Object(C_dianasoft_serviciiweb_web_node_modules_babel_runtime_helpers_esm_objectSpread2_js__WEBPACK_IMPORTED_MODULE_0__["default"])({}, _utils__WEBPACK_IMPORTED_MODULE_1__);
};
var _ = null;

/***/ }),

/***/ "./resources/js/src/@core/libs/i18n/utils.js":
/*!***************************************************!*\
  !*** ./resources/js/src/@core/libs/i18n/utils.js ***!
  \***************************************************/
/*! exports provided: t, _ */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "t", function() { return t; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "_", function() { return _; });
/* harmony import */ var _vue_composition_api__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @vue/composition-api */ "./node_modules/@vue/composition-api/dist/vue-composition-api.mjs");

/**
 * Returns translated string if i18n package is available to Vue
 * If i18n is not configured then it will simply return what is being passed
 * Useful if you don't know if i18n is configured or not
 * Used in @core files to handle absence of i18n without errors
 * @param {String} key i18n key to use for translation
 */

var t = function t(key) {
  var vm = Object(_vue_composition_api__WEBPACK_IMPORTED_MODULE_0__["getCurrentInstance"])().proxy;
  return vm.$t ? vm.$t(key) : key;
};
var _ = null;

/***/ }),

/***/ "./resources/js/src/@core/utils/filter.js":
/*!************************************************!*\
  !*** ./resources/js/src/@core/utils/filter.js ***!
  \************************************************/
/*! exports provided: kFormatter, title, avatarText, formatDate, formatDateToMonthShort, filterTags */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "kFormatter", function() { return kFormatter; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "title", function() { return title; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "avatarText", function() { return avatarText; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "formatDate", function() { return formatDate; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "formatDateToMonthShort", function() { return formatDateToMonthShort; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "filterTags", function() { return filterTags; });
/* harmony import */ var core_js_modules_es_number_to_fixed_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! core-js/modules/es.number.to-fixed.js */ "./node_modules/core-js/modules/es.number.to-fixed.js");
/* harmony import */ var core_js_modules_es_number_to_fixed_js__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_es_number_to_fixed_js__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var core_js_modules_es_object_to_string_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! core-js/modules/es.object.to-string.js */ "./node_modules/core-js/modules/es.object.to-string.js");
/* harmony import */ var core_js_modules_es_object_to_string_js__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_es_object_to_string_js__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var core_js_modules_es_regexp_to_string_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! core-js/modules/es.regexp.to-string.js */ "./node_modules/core-js/modules/es.regexp.to-string.js");
/* harmony import */ var core_js_modules_es_regexp_to_string_js__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_es_regexp_to_string_js__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var core_js_modules_es_regexp_exec_js__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! core-js/modules/es.regexp.exec.js */ "./node_modules/core-js/modules/es.regexp.exec.js");
/* harmony import */ var core_js_modules_es_regexp_exec_js__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_es_regexp_exec_js__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var core_js_modules_es_string_split_js__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! core-js/modules/es.string.split.js */ "./node_modules/core-js/modules/es.string.split.js");
/* harmony import */ var core_js_modules_es_string_split_js__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_es_string_split_js__WEBPACK_IMPORTED_MODULE_4__);
/* harmony import */ var core_js_modules_web_dom_collections_for_each_js__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! core-js/modules/web.dom-collections.for-each.js */ "./node_modules/core-js/modules/web.dom-collections.for-each.js");
/* harmony import */ var core_js_modules_web_dom_collections_for_each_js__WEBPACK_IMPORTED_MODULE_5___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_web_dom_collections_for_each_js__WEBPACK_IMPORTED_MODULE_5__);
/* harmony import */ var core_js_modules_es_array_slice_js__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! core-js/modules/es.array.slice.js */ "./node_modules/core-js/modules/es.array.slice.js");
/* harmony import */ var core_js_modules_es_array_slice_js__WEBPACK_IMPORTED_MODULE_6___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_es_array_slice_js__WEBPACK_IMPORTED_MODULE_6__);
/* harmony import */ var core_js_modules_es_array_join_js__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! core-js/modules/es.array.join.js */ "./node_modules/core-js/modules/es.array.join.js");
/* harmony import */ var core_js_modules_es_array_join_js__WEBPACK_IMPORTED_MODULE_7___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_es_array_join_js__WEBPACK_IMPORTED_MODULE_7__);
/* harmony import */ var core_js_modules_es_array_map_js__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! core-js/modules/es.array.map.js */ "./node_modules/core-js/modules/es.array.map.js");
/* harmony import */ var core_js_modules_es_array_map_js__WEBPACK_IMPORTED_MODULE_8___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_es_array_map_js__WEBPACK_IMPORTED_MODULE_8__);
/* harmony import */ var core_js_modules_es_string_replace_js__WEBPACK_IMPORTED_MODULE_9__ = __webpack_require__(/*! core-js/modules/es.string.replace.js */ "./node_modules/core-js/modules/es.string.replace.js");
/* harmony import */ var core_js_modules_es_string_replace_js__WEBPACK_IMPORTED_MODULE_9___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_es_string_replace_js__WEBPACK_IMPORTED_MODULE_9__);
/* harmony import */ var _utils__WEBPACK_IMPORTED_MODULE_10__ = __webpack_require__(/*! ./utils */ "./resources/js/src/@core/utils/utils.js");











var kFormatter = function kFormatter(num) {
  return num > 999 ? "".concat((num / 1000).toFixed(1), "k") : num;
};
var title = function title(value) {
  var replacer = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : ' ';
  if (!value) return '';
  var str = value.toString();
  var arr = str.split(replacer);
  var capitalizedArray = [];
  arr.forEach(function (word) {
    var capitalized = word.charAt(0).toUpperCase() + word.slice(1);
    capitalizedArray.push(capitalized);
  });
  return capitalizedArray.join(' ');
};
var avatarText = function avatarText(value) {
  if (!value) return '';
  var nameArray = value.split(' ');
  return nameArray.map(function (word) {
    return word.charAt(0).toUpperCase();
  }).join('');
};
/**
 * Format and return date in Humanize format
 * Intl docs: https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/Intl/DateTimeFormat/format
 * Intl Constructor: https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/Intl/DateTimeFormat/DateTimeFormat
 * @param {String} value date to format
 * @param {Object} formatting Intl object to format with
 */

var formatDate = function formatDate(value) {
  var formatting = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : {
    month: 'short',
    day: 'numeric',
    year: 'numeric'
  };
  if (!value) return value;
  return new Intl.DateTimeFormat('en-US', formatting).format(new Date(value));
};
/**
 * Return short human friendly month representation of date
 * Can also convert date to only time if date is of today (Better UX)
 * @param {String} value date to format
 * @param {Boolean} toTimeForCurrentDay Shall convert to time if day is today/current
 */

var formatDateToMonthShort = function formatDateToMonthShort(value) {
  var toTimeForCurrentDay = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : true;
  var date = new Date(value);
  var formatting = {
    month: 'short',
    day: 'numeric'
  };

  if (toTimeForCurrentDay && Object(_utils__WEBPACK_IMPORTED_MODULE_10__["isToday"])(date)) {
    formatting = {
      hour: 'numeric',
      minute: 'numeric'
    };
  }

  return new Intl.DateTimeFormat('en-US', formatting).format(new Date(value));
}; // Strip all the tags from markup and return plain text

var filterTags = function filterTags(value) {
  return value.replace(/<\/?[^>]+(>|$)/g, '');
};

/***/ }),

/***/ "./resources/js/src/@core/utils/utils.js":
/*!***********************************************!*\
  !*** ./resources/js/src/@core/utils/utils.js ***!
  \***********************************************/
/*! exports provided: isObject, isToday, getRandomBsVariant, isDynamicRouteActive, useRouter */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "isObject", function() { return isObject; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "isToday", function() { return isToday; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "getRandomBsVariant", function() { return getRandomBsVariant; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "isDynamicRouteActive", function() { return isDynamicRouteActive; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "useRouter", function() { return useRouter; });
/* harmony import */ var C_dianasoft_serviciiweb_web_node_modules_babel_runtime_helpers_esm_objectSpread2_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./node_modules/@babel/runtime/helpers/esm/objectSpread2.js */ "./node_modules/@babel/runtime/helpers/esm/objectSpread2.js");
/* harmony import */ var C_dianasoft_serviciiweb_web_node_modules_babel_runtime_helpers_esm_typeof_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./node_modules/@babel/runtime/helpers/esm/typeof.js */ "./node_modules/@babel/runtime/helpers/esm/typeof.js");
/* harmony import */ var _router__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @/router */ "./resources/js/src/router/index.js");
/* harmony import */ var _vue_composition_api__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @vue/composition-api */ "./node_modules/@vue/composition-api/dist/vue-composition-api.mjs");


 // eslint-disable-next-line object-curly-newline


var isObject = function isObject(obj) {
  return Object(C_dianasoft_serviciiweb_web_node_modules_babel_runtime_helpers_esm_typeof_js__WEBPACK_IMPORTED_MODULE_1__["default"])(obj) === 'object' && obj !== null;
};
var isToday = function isToday(date) {
  var today = new Date();
  return (
    /* eslint-disable operator-linebreak */
    date.getDate() === today.getDate() && date.getMonth() === today.getMonth() && date.getFullYear() === today.getFullYear()
    /* eslint-enable */

  );
};

var getRandomFromArray = function getRandomFromArray(array) {
  return array[Math.floor(Math.random() * array.length)];
}; // ? Light and Dark variant is not included
// prettier-ignore


var getRandomBsVariant = function getRandomBsVariant() {
  return getRandomFromArray(['primary', 'secondary', 'success', 'warning', 'danger', 'info']);
};
var isDynamicRouteActive = function isDynamicRouteActive(route) {
  var _router$resolve = _router__WEBPACK_IMPORTED_MODULE_2__["default"].resolve(route),
      resolvedRoute = _router$resolve.route;

  return resolvedRoute.path === _router__WEBPACK_IMPORTED_MODULE_2__["default"].currentRoute.path;
}; // Thanks: https://medium.com/better-programming/reactive-vue-routes-with-the-composition-api-18c1abd878d1

var useRouter = function useRouter() {
  var vm = Object(_vue_composition_api__WEBPACK_IMPORTED_MODULE_3__["getCurrentInstance"])().proxy;
  var state = Object(_vue_composition_api__WEBPACK_IMPORTED_MODULE_3__["reactive"])({
    route: vm.$route
  });
  Object(_vue_composition_api__WEBPACK_IMPORTED_MODULE_3__["watch"])(function () {
    return vm.$route;
  }, function (r) {
    state.route = r;
  });
  return Object(C_dianasoft_serviciiweb_web_node_modules_babel_runtime_helpers_esm_objectSpread2_js__WEBPACK_IMPORTED_MODULE_0__["default"])(Object(C_dianasoft_serviciiweb_web_node_modules_babel_runtime_helpers_esm_objectSpread2_js__WEBPACK_IMPORTED_MODULE_0__["default"])({}, Object(_vue_composition_api__WEBPACK_IMPORTED_MODULE_3__["toRefs"])(state)), {}, {
    router: vm.$router
  });
};
/**
 * This is just enhancement over Object.extend [Gives deep extend]
 * @param {target} a Object which contains values to be overridden
 * @param {source} b Object which contains values to override
 */
// export const objectExtend = (a, b) => {
//   // Don't touch 'null' or 'undefined' objects.
//   if (a == null || b == null) {
//     return a
//   }
//   Object.keys(b).forEach(key => {
//     if (Object.prototype.toString.call(b[key]) === '[object Object]') {
//       if (Object.prototype.toString.call(a[key]) !== '[object Object]') {
//         // eslint-disable-next-line no-param-reassign
//         a[key] = b[key]
//       } else {
//         // eslint-disable-next-line no-param-reassign
//         a[key] = objectExtend(a[key], b[key])
//       }
//     } else {
//       // eslint-disable-next-line no-param-reassign
//       a[key] = b[key]
//     }
//   })
//   return a
// }

/***/ }),

/***/ "./resources/js/src/assets/images/sigle/dispecer-simbol.svg":
/*!******************************************************************!*\
  !*** ./resources/js/src/assets/images/sigle/dispecer-simbol.svg ***!
  \******************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

module.exports = "/images/sigle/dispecer-simbol.svg";

/***/ }),

/***/ "./resources/js/src/assets/images/sigle/grefier-simbol.svg":
/*!*****************************************************************!*\
  !*** ./resources/js/src/assets/images/sigle/grefier-simbol.svg ***!
  \*****************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

module.exports = "/images/sigle/grefier-simbol.svg";

/***/ }),

/***/ "./resources/js/src/assets/images/sigle/spv-curier-simbol.svg":
/*!********************************************************************!*\
  !*** ./resources/js/src/assets/images/sigle/spv-curier-simbol.svg ***!
  \********************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

module.exports = "/images/sigle/spv-curier-simbol.svg";

/***/ })

}]);