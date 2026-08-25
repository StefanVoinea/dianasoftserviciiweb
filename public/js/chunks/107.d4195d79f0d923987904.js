(window["webpackJsonp"] = window["webpackJsonp"] || []).push([[107],{

/***/ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/app_pages/Utilizatori.vue?vue&type=script&lang=js&":
/*!*******************************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib??ref--4-0!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/src/views/app_pages/Utilizatori.vue?vue&type=script&lang=js& ***!
  \*******************************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var core_js_modules_es_object_to_string_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! core-js/modules/es.object.to-string.js */ "./node_modules/core-js/modules/es.object.to-string.js");
/* harmony import */ var core_js_modules_es_object_to_string_js__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_es_object_to_string_js__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var core_js_modules_es_string_iterator_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! core-js/modules/es.string.iterator.js */ "./node_modules/core-js/modules/es.string.iterator.js");
/* harmony import */ var core_js_modules_es_string_iterator_js__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_es_string_iterator_js__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var core_js_modules_web_dom_collections_iterator_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! core-js/modules/web.dom-collections.iterator.js */ "./node_modules/core-js/modules/web.dom-collections.iterator.js");
/* harmony import */ var core_js_modules_web_dom_collections_iterator_js__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_web_dom_collections_iterator_js__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var core_js_modules_web_url_js__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! core-js/modules/web.url.js */ "./node_modules/core-js/modules/web.url.js");
/* harmony import */ var core_js_modules_web_url_js__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_web_url_js__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var core_js_modules_web_url_search_params_js__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! core-js/modules/web.url-search-params.js */ "./node_modules/core-js/modules/web.url-search-params.js");
/* harmony import */ var core_js_modules_web_url_search_params_js__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_web_url_search_params_js__WEBPACK_IMPORTED_MODULE_4__);
/* harmony import */ var _Utilizatoriaev_vue__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ./Utilizatoriaev.vue */ "./resources/js/src/views/app_pages/Utilizatoriaev.vue");
/* harmony import */ var _Utilizatoricopiazadrepturi_vue__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! ./Utilizatoricopiazadrepturi.vue */ "./resources/js/src/views/app_pages/Utilizatoricopiazadrepturi.vue");
/* harmony import */ var _Utilizatorimodificadrepturi_vue__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! ./Utilizatorimodificadrepturi.vue */ "./resources/js/src/views/app_pages/Utilizatorimodificadrepturi.vue");





//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
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
  props: {
    id: String
  },
  components: {
    Utilizatoriaev: _Utilizatoriaev_vue__WEBPACK_IMPORTED_MODULE_5__["default"],
    Utilizatoricopiazadrepturi: _Utilizatoricopiazadrepturi_vue__WEBPACK_IMPORTED_MODULE_6__["default"],
    Utilizatorimodificadrepturi: _Utilizatorimodificadrepturi_vue__WEBPACK_IMPORTED_MODULE_7__["default"]
  },
  name: "utilizatori",
  data: function data() {
    return {
      refreshLocal: false,
      idselectat: null,
      campFiltruStart: "",
      modelName: "utilizatori",
      modelDisplayName: "Utilizatori",
      canAdd: this.$userpermitt.can("addUtilizatori"),
      editVarCopy: {
        to: [],
        from: "",
        from_id: ""
      },
      editVar: {
        name: "",
        user_type: "",
        email: "",
        password: "",
        telefon: "",
        blocat: "",
        functia: "",
        status: "",
        link_poza: "",
        program_de_lucru: "",
        data_expirare_parola: "",
        departament: "",
        sex: "",
        grup: [],
        notificari: []
      },
      activeEdit: false,
      activeCopy: false,
      activeModificaDrepturi: false,
      activeAction: "",
      selectedID: "",
      showLoading: false,
      columnDefs: [// { headerName: "Document...",
      //        children: [
      // columnGroupShow:"open",
      // filter: "agNumberColumnFilter",
      // valueFormatter: function(params) { return new Date(params.value).toLocaleDateString() },
      // cellRenderer: function(params) {
      //            if(params.value!=null){
      //               return "<a href='/contract?id="+params.value.id +"' target='_blank'>"+ params.value.nr_contract+'/'+ new Date(params.value.data_contract).toLocaleDateString()+' '+params.value.nume+'</a>'  
      //            }
      //       },
      {
        label: "Nume",
        field: "name",
        width: "300px",
        type: "text",
        showSortAsc: true
      }, {
        label: "Email",
        field: "email",
        width: "300px",
        type: "text",
        showSortAsc: true
      }, {
        label: "Telefon",
        field: "telefon",
        width: "300px",
        type: "text",
        showSortAsc: true
      }, {
        label: "Functia",
        field: "functia",
        width: "300px",
        type: "text",
        showSortAsc: true
      }, {
        label: "Program de lucru",
        field: "program_de_lucru",
        width: "300px",
        type: "text",
        showSortAsc: true
      }, {
        label: "Departament",
        field: "departament",
        width: "300px",
        type: "text",
        showSortAsc: true
      }]
    };
  },
  methods: {
    fisaUtilizator: function fisaUtilizator() {
      var _this = this;

      if (this.selectedID == '') {
        this.$bvToast.toast("Selectați un utilizator!", {
          title: "Selectati un utilizator! ",
          variant: "warning",
          solid: false,
          appendToast: true,
          autoHideDelay: 3000,
          toaster: "b-toaster-bottom-right"
        });
      } else {
        this.showLoading = true;
        var payLoad = Object.assign({}, this.editVar);
        payLoad.requestType = "post";
        payLoad.requestUrl = "/utilizatori/fisautilizator/" + this.selectedID.id;
        this.$store.dispatch("app/api_blob_Request", payLoad).then(function (response) {
          var fileURL = window.URL.createObjectURL(new Blob([response]));
          var fileLink = document.createElement('a');
          fileLink.href = fileURL;
          fileLink.setAttribute('download', 'fisa_utilizator.xls');
          document.body.appendChild(fileLink);
          fileLink.click();
          _this.showLoading = false;

          _this.$emit('closed');
        });
      }
    },
    situateiDrepturiUtilizatori: function situateiDrepturiUtilizatori() {
      var _this2 = this;

      this.showLoading = true;
      var payLoad = Object.assign({}, this.editVar);
      payLoad.requestType = "post";
      payLoad.requestUrl = "/utilizatori/situatieDrepturiUtilizatori";
      this.$store.dispatch("app/api_blob_Request", payLoad).then(function (response) {
        var fileURL = window.URL.createObjectURL(new Blob([response]));
        var fileLink = document.createElement('a');
        fileLink.href = fileURL;
        fileLink.setAttribute('download', 'situatieDrepturiUtilizatori.xlsx');
        document.body.appendChild(fileLink);
        fileLink.click();
        _this2.showLoading = false;

        _this2.$emit('closed');
      });
    },
    copiazaDrepturi: function copiazaDrepturi() {
      var id = this.selectedID;
      this.fromID = "";

      if (this.selectedID == '') {
        this.$bvToast.toast("Selectați un utilizator!", {
          title: "Selectati un utilizator! ",
          variant: "warning",
          solid: false,
          appendToast: true,
          autoHideDelay: 3000,
          toaster: "b-toaster-bottom-right"
        });
      } else {
        this.editVarCopy.from_id = this.selectedID.id;
        this.editVarCopy.from = this.selectedID;
        this.activeAction = "Copiază";
        this.activeCopy = true;
      }
    },
    modificaDrepturi: function modificaDrepturi() {
      var _this3 = this;

      if (this.selectedID == '') {
        this.$bvToast.toast("Selectați un utilizator!", {
          title: "Selectati un utilizator! ",
          variant: "warning",
          solid: false,
          appendToast: true,
          autoHideDelay: 3000,
          toaster: "b-toaster-bottom-right"
        });
      } else {
        this.showLoading = true;
        var payLoad = {};
        payLoad.requestType = "get";
        payLoad.requestUrl = "/utilizatori/show/" + this.selectedID.id;
        this.$store.dispatch("app/api_Request", payLoad).then(function (response) {
          _this3.editVar = response;
          _this3.activeAction = "Modifică drepturi";
          _this3.showLoading = false;
          _this3.activeModificaDrepturi = true;
        });
      }
    },
    modificaDrepturiClosed: function modificaDrepturiClosed() {
      this.activeModificaDrepturi = false;
      this.editVar = {
        name: "",
        user_type: "",
        email: "",
        password: "",
        telefon: "",
        blocat: "",
        functia: "",
        status: "",
        link_poza: "",
        program_de_lucru: "",
        data_expirare_parola: "",
        departament: "",
        sex: "",
        grup: [],
        notificari: []
      }, this.activeAction = "";
    },
    aevClosed: function aevClosed() {
      this.idselectat = null;
      this.selectedID = "";
      this.activeEdit = false;
      this.editVar = {
        name: "",
        user_type: "",
        email: "",
        password: "",
        telefon: "",
        blocat: "",
        functia: "",
        status: "",
        link_poza: "",
        program_de_lucru: "",
        data_expirare_parola: "",
        departament: "",
        sex: "",
        grup: [],
        notificari: []
      };
      this.activeAction = "";
    },
    copyClosed: function copyClosed() {
      this.activeCopy = false;
      this.editVarCopy = {
        to: [],
        from_id: "",
        from: ""
      };
      this.activeAction = "";
    },
    afisezSalvat: function afisezSalvat(value) {
      //this.idselectat=value.id
      //this.campFiltruStart="id"
      this.refreshLocal = !this.refreshLocal;
    },
    listen: function listen() {//  Echo.channel("cerber_databasechannel")
      //      .listen("."+this.modelName+".updated", (e) => {
      //         this.getRecords()
      //      });
    },
    onSelectionChanged: function onSelectionChanged(value) {
      this.selectedID = value;
    },
    add: function add() {
      this.activeAction = "Adaugă";
      this.editVar = {
        name: "",
        user_type: "",
        email: "",
        password: "",
        telefon: "",
        blocat: "",
        functia: "",
        status: "",
        link_poza: "",
        program_de_lucru: "",
        data_expirare_parola: "",
        departament: "",
        sex: "",
        grup: [],
        notificari: []
      };
      this.activeEdit = true;
    },
    edit: function edit() {
      this.idselectat = null;
      this.editVar = Object.assign({}, this.selectedID);
      this.activeAction = "Modifică";
      this.activeEdit = true;
    },
    view: function view() {
      this.editVar = Object.assign({}, this.selectedID);
      this.activeAction = "Vizualizează";
      this.activeEdit = true;
    }
  },
  created: function created() {
    document.title = window.app_name + "->" + this.modelDisplayName; // if(this.id!=null){
    //             this.idselectat=this.id
    //             this.campFiltruStart="id"
    // }

    this.listen();
  }
});

/***/ }),

/***/ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/app_pages/Utilizatoriaev.vue?vue&type=script&lang=js&":
/*!**********************************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib??ref--4-0!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/src/views/app_pages/Utilizatoriaev.vue?vue&type=script&lang=js& ***!
  \**********************************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var C_dianasoft_serviciiweb_web_node_modules_babel_runtime_helpers_esm_defineProperty_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./node_modules/@babel/runtime/helpers/esm/defineProperty.js */ "./node_modules/@babel/runtime/helpers/esm/defineProperty.js");
/* harmony import */ var vue_ripple_directive__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! vue-ripple-directive */ "./node_modules/vue-ripple-directive/src/ripple.js");
/* harmony import */ var _core_mixins_ui_transition__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @core/mixins/ui/transition */ "./resources/js/src/@core/mixins/ui/transition.js");
/* harmony import */ var bootstrap_vue__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! bootstrap-vue */ "./node_modules/bootstrap-vue/esm/index.js");
/* harmony import */ var _validations__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! @validations */ "./resources/js/src/@core/utils/validations/validations.js");
/* harmony import */ var vee_validate__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! vee-validate */ "./node_modules/vee-validate/dist/vee-validate.esm.js");

//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
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
  props: {
    activeEdit: Boolean,
    activeAction: String,
    editVar: Object,
    rutainapoi: String
  },
  mixins: [_core_mixins_ui_transition__WEBPACK_IMPORTED_MODULE_2__["heightTransition"]],
  components: {
    ValidationProvider: vee_validate__WEBPACK_IMPORTED_MODULE_5__["ValidationProvider"],
    ValidationObserver: vee_validate__WEBPACK_IMPORTED_MODULE_5__["ValidationObserver"]
  },
  directives: {
    "b-modal": bootstrap_vue__WEBPACK_IMPORTED_MODULE_3__["VBModal"],
    Ripple: vue_ripple_directive__WEBPACK_IMPORTED_MODULE_1__["default"]
  },
  name: "utilizatoriaev",
  data: function data() {
    return {
      required: _validations__WEBPACK_IMPORTED_MODULE_4__["required"],
      password: _validations__WEBPACK_IMPORTED_MODULE_4__["password"],
      email: _validations__WEBPACK_IMPORTED_MODULE_4__["email"],
      confirmed: _validations__WEBPACK_IMPORTED_MODULE_4__["confirmed"],
      min: _validations__WEBPACK_IMPORTED_MODULE_4__["min"],
      passwordFieldType: "password",
      rutainapoiLocal: this.rutainapoi,
      activeEditLocal: false,
      activeActionLocal: this.activeAction,
      editVarLocal: this.editVar,
      nextTodoId: 2,
      modelName: "utilizatori",
      showLoading: false
    };
  },
  computed: {
    passwordToggleIcon: function passwordToggleIcon() {
      return this.passwordFieldType === 'password' ? 'EyeIcon' : 'EyeOffIcon';
    }
  },
  watch: {
    activeEdit: function activeEdit() {
      this.activeEditLocal = this.activeEdit;
    },
    activeEditLocal: function activeEditLocal() {
      if (this.activeEditLocal == false) {
        this.$emit('closed');
      }
    },
    activeAction: function activeAction() {
      this.activeActionLocal = this.activeAction;
    },
    editVar: function editVar() {
      this.editVarLocal = this.editVar;
    }
  },
  methods: Object(C_dianasoft_serviciiweb_web_node_modules_babel_runtime_helpers_esm_defineProperty_js__WEBPACK_IMPORTED_MODULE_0__["default"])({
    togglePasswordVisibility: function togglePasswordVisibility() {
      this.passwordFieldType = this.passwordFieldType === 'password' ? 'text' : 'password';
    },
    initTrHeight: function initTrHeight() {
      var _this = this;

      this.trSetHeight(null);
      this.$nextTick(function () {
        if (_this.$refs.form) {
          _this.trSetHeight(_this.$refs.form.scrollHeight);
        }
      });
    },
    handleOk: function handleOk(bvModalEvt) {
      var _this2 = this;

      bvModalEvt.preventDefault();
      this.$refs.simpleRules.validate().then(function (success) {
        if (success) {
          if (_this2.activeActionLocal == "Adaugă") {
            _this2.saveAdd();
          }

          if (_this2.activeActionLocal == "Modifică") {
            _this2.saveEdit();
          }
        }
      });
    },
    saveAdd: function saveAdd() {
      var _this3 = this;

      this.showLoading = true;
      var payLoad = this.editVarLocal;
      payLoad.requestType = "post";
      payLoad.requestUrl = "/" + this.modelName + "/store";
      this.$store.dispatch("app/api_Request", payLoad).then(function (response) {
        _this3.editVarLocal = {
          name: '',
          user_type: '',
          email: '',
          password: '',
          telefon: '',
          blocat: '',
          functia: '',
          status: '',
          link_poza: '',
          program_de_lucru: '',
          data_expirare_parola: '',
          departament: '',
          sex: '',
          grup: ''
        };

        _this3.$bvToast.toast("Salvare efectuata cu success!", {
          title: "Salvare cu succes! ",
          variant: "success",
          solid: false,
          appendToast: true,
          autoHideDelay: 3000,
          toaster: "b-toaster-bottom-right"
        });

        _this3.showLoading = false;
        _this3.activeEditLocal = false;
        _this3.activeActionLocal = "";

        _this3.$emit("stored", "");

        _this3.$emit("closed");
      })["catch"](function (error) {
        _this3.showLoading = true;
      });
    },
    aevClosed: function aevClosed() {
      this.activeEditLocal = false;
      this.editVarLocal = {
        name: "",
        user_type: "",
        email: "",
        password: "",
        telefon: "",
        blocat: "",
        functia: "",
        status: "",
        link_poza: "",
        program_de_lucru: "",
        data_expirare_parola: "",
        departament: "",
        sex: "",
        grup: ""
      };
      this.activeActionLocal = "";
      this.$emit("closed");
    },
    saveEdit: function saveEdit() {
      var _this4 = this;

      this.showLoading = true;
      var payLoad = this.editVarLocal;
      payLoad.requestType = "post";
      payLoad.requestUrl = "/" + this.modelName + "/edit/" + this.editVarLocal.id;
      this.$store.dispatch("app/api_Request", payLoad).then(function (response) {
        _this4.editVarLocal = {
          name: "",
          user_type: "",
          email: "",
          password: "",
          telefon: "",
          blocat: "",
          functia: "",
          status: "",
          link_poza: "",
          program_de_lucru: "",
          data_expirare_parola: "",
          departament: "",
          sex: "",
          grup: ""
        };

        _this4.$bvToast.toast("Modificare efectuata cu success!", {
          title: "Modificare cu succes! ",
          variant: "success",
          solid: false,
          appendToast: true,
          autoHideDelay: 3000,
          toaster: "b-toaster-bottom-right"
        });

        _this4.showLoading = false;

        _this4.$emit("stored", "");

        _this4.activeEditLocal = false;
        _this4.activeActionLocal = "";

        _this4.$emit("closed");
      })["catch"](function (error) {
        _this4.showLoading = false;
      });
    }
  }, "initTrHeight", function initTrHeight() {
    var _this5 = this;

    this.trSetHeight(null);
    this.$nextTick(function () {
      if (_this5.$refs.form) {
        _this5.trSetHeight(_this5.$refs.form.scrollHeight);
      }
    });
  }),
  mounted: function mounted() {
    this.initTrHeight();
  },
  destroyed: function destroyed() {
    window.removeEventListener("resize", this.initTrHeight);
  },
  created: function created() {
    if (!this.rutainapoi) {
      this.rutainapoiLocal = this.modelName;
    }

    window.addEventListener("resize", this.initTrHeight);
  }
});

/***/ }),

/***/ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/app_pages/Utilizatoricopiazadrepturi.vue?vue&type=script&lang=js&":
/*!**********************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib??ref--4-0!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/src/views/app_pages/Utilizatoricopiazadrepturi.vue?vue&type=script&lang=js& ***!
  \**********************************************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var C_dianasoft_serviciiweb_web_node_modules_babel_runtime_helpers_esm_defineProperty_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./node_modules/@babel/runtime/helpers/esm/defineProperty.js */ "./node_modules/@babel/runtime/helpers/esm/defineProperty.js");
/* harmony import */ var vue_ripple_directive__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! vue-ripple-directive */ "./node_modules/vue-ripple-directive/src/ripple.js");
/* harmony import */ var _core_mixins_ui_transition__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @core/mixins/ui/transition */ "./resources/js/src/@core/mixins/ui/transition.js");
/* harmony import */ var bootstrap_vue__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! bootstrap-vue */ "./node_modules/bootstrap-vue/esm/index.js");
/* harmony import */ var _validations__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! @validations */ "./resources/js/src/@core/utils/validations/validations.js");
/* harmony import */ var vee_validate__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! vee-validate */ "./node_modules/vee-validate/dist/vee-validate.esm.js");

//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
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
  props: {
    activeCopy: Boolean,
    activeAction: String,
    editVar: Object,
    rutainapoi: String
  },
  mixins: [_core_mixins_ui_transition__WEBPACK_IMPORTED_MODULE_2__["heightTransition"]],
  components: {
    ValidationProvider: vee_validate__WEBPACK_IMPORTED_MODULE_5__["ValidationProvider"],
    ValidationObserver: vee_validate__WEBPACK_IMPORTED_MODULE_5__["ValidationObserver"]
  },
  directives: {
    "b-modal": bootstrap_vue__WEBPACK_IMPORTED_MODULE_3__["VBModal"],
    Ripple: vue_ripple_directive__WEBPACK_IMPORTED_MODULE_1__["default"]
  },
  name: "utilizatoricopiazadrepturi",
  data: function data() {
    return {
      required: _validations__WEBPACK_IMPORTED_MODULE_4__["required"],
      password: _validations__WEBPACK_IMPORTED_MODULE_4__["password"],
      email: _validations__WEBPACK_IMPORTED_MODULE_4__["email"],
      confirmed: _validations__WEBPACK_IMPORTED_MODULE_4__["confirmed"],
      min: _validations__WEBPACK_IMPORTED_MODULE_4__["min"],
      rutainapoiLocal: this.rutainapoi,
      activeCopyLocal: false,
      activeActionLocal: this.activeAction,
      editVarLocal: this.editVar,
      nextTodoId: 2,
      modelName: "utilizatori",
      showLoading: false
    };
  },
  watch: {
    activeCopy: function activeCopy() {
      this.activeCopyLocal = this.activeCopy;
    },
    activeCopyLocal: function activeCopyLocal() {
      if (this.activeCopyLocal == false) {
        this.$emit('closed');
      }
    },
    activeAction: function activeAction() {
      this.activeActionLocal = this.activeAction;
    },
    editVar: function editVar() {
      this.editVarLocal = this.editVar;
    }
  },
  methods: Object(C_dianasoft_serviciiweb_web_node_modules_babel_runtime_helpers_esm_defineProperty_js__WEBPACK_IMPORTED_MODULE_0__["default"])({
    modificaTo: function modificaTo() {},
    initTrHeight: function initTrHeight() {
      var _this = this;

      this.trSetHeight(null);
      this.$nextTick(function () {
        if (_this.$refs.form) {
          _this.trSetHeight(_this.$refs.form.scrollHeight);
        }
      });
    },
    handleOk: function handleOk(bvModalEvt) {
      var _this2 = this;

      bvModalEvt.preventDefault();
      this.$refs.simpleRules.validate().then(function (success) {
        if (success) {
          _this2.copyAction();
        }
      });
    },
    copyAction: function copyAction() {
      var _this3 = this;

      this.showLoading = true;
      var payLoad = this.editVarLocal;
      payLoad.requestType = "post";
      payLoad.requestUrl = "/utilizatori/copy";
      this.activeCopyLocal = false;
      this.$store.dispatch("app/api_Request", payLoad).then(function (response) {
        _this3.$bvToast.toast("Copiere efectuată cu succes!", {
          title: "Copiere efectuată cu succes!",
          variant: "success",
          solid: false,
          appendToast: true,
          autoHideDelay: 3000,
          toaster: "b-toaster-bottom-right"
        });

        _this3.showLoading = false;

        _this3.$emit("closed");
      });
    },
    aevClosed: function aevClosed() {
      this.activeCopyLocal = false;
      this.editVarLocal = {
        to: [],
        from: "",
        from_id: ""
      };
      this.activeActionLocal = "";
      this.$emit("closed");
    }
  }, "initTrHeight", function initTrHeight() {
    var _this4 = this;

    this.trSetHeight(null);
    this.$nextTick(function () {
      if (_this4.$refs.form) {
        _this4.trSetHeight(_this4.$refs.form.scrollHeight);
      }
    });
  }),
  mounted: function mounted() {
    this.initTrHeight();
  },
  destroyed: function destroyed() {
    window.removeEventListener("resize", this.initTrHeight);
  },
  created: function created() {
    if (!this.rutainapoi) {
      this.rutainapoiLocal = this.modelName;
    }

    window.addEventListener("resize", this.initTrHeight);
  }
});

/***/ }),

/***/ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/app_pages/Utilizatorimodificadrepturi.vue?vue&type=script&lang=js&":
/*!***********************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib??ref--4-0!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/src/views/app_pages/Utilizatorimodificadrepturi.vue?vue&type=script&lang=js& ***!
  \***********************************************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var C_dianasoft_serviciiweb_web_node_modules_babel_runtime_helpers_esm_defineProperty_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./node_modules/@babel/runtime/helpers/esm/defineProperty.js */ "./node_modules/@babel/runtime/helpers/esm/defineProperty.js");
/* harmony import */ var vue_ripple_directive__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! vue-ripple-directive */ "./node_modules/vue-ripple-directive/src/ripple.js");
/* harmony import */ var _core_mixins_ui_transition__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @core/mixins/ui/transition */ "./resources/js/src/@core/mixins/ui/transition.js");
/* harmony import */ var bootstrap_vue__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! bootstrap-vue */ "./node_modules/bootstrap-vue/esm/index.js");
/* harmony import */ var _validations__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! @validations */ "./resources/js/src/@core/utils/validations/validations.js");
/* harmony import */ var vee_validate__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! vee-validate */ "./node_modules/vee-validate/dist/vee-validate.esm.js");

//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
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
  props: {
    activeCopy: Boolean,
    activeAction: String,
    editVar: Object,
    rutainapoi: String
  },
  mixins: [_core_mixins_ui_transition__WEBPACK_IMPORTED_MODULE_2__["heightTransition"]],
  components: {
    ValidationProvider: vee_validate__WEBPACK_IMPORTED_MODULE_5__["ValidationProvider"],
    ValidationObserver: vee_validate__WEBPACK_IMPORTED_MODULE_5__["ValidationObserver"]
  },
  directives: {
    "b-modal": bootstrap_vue__WEBPACK_IMPORTED_MODULE_3__["VBModal"],
    Ripple: vue_ripple_directive__WEBPACK_IMPORTED_MODULE_1__["default"]
  },
  name: "utilizatorimodificadrepturi",
  data: function data() {
    return {
      required: _validations__WEBPACK_IMPORTED_MODULE_4__["required"],
      password: _validations__WEBPACK_IMPORTED_MODULE_4__["password"],
      email: _validations__WEBPACK_IMPORTED_MODULE_4__["email"],
      confirmed: _validations__WEBPACK_IMPORTED_MODULE_4__["confirmed"],
      min: _validations__WEBPACK_IMPORTED_MODULE_4__["min"],
      rutainapoiLocal: this.rutainapoi,
      activeCopyLocal: false,
      activeActionLocal: this.activeAction,
      editVarLocal: this.editVar,
      nextTodoId: 2,
      modelName: "utilizatori",
      showLoading: false,
      fieldsGestiuni: [{
        key: 'denumire',
        label: "Denumire",
        field_type: 'label',
        sortable: true,
        stickyColumn: false,
        isRowHeader: true,
        searchable: true,
        readonly: false
      }, {
        key: 'pivot.isactive',
        label: 'Permisiune',
        field_type: 'checkbox'
      }],
      fieldsOptiuniMenu: [{
        key: 'parent',
        label: "Grup de optiuni",
        field_type: 'label',
        sortable: true,
        stickyColumn: false,
        isRowHeader: true,
        searchable: true,
        readonly: false
      }, {
        key: 'name',
        label: "Denumire",
        field_type: 'label',
        sortable: true,
        stickyColumn: false,
        isRowHeader: true,
        searchable: true,
        readonly: false
      }, {
        key: 'pivot.isactive',
        label: 'Permisiune',
        field_type: 'checkbox'
      }],
      fieldsOperatiuni: [{
        key: 'dianasoftmenuoption.name',
        label: "Optiune",
        field_type: 'label',
        sortable: true,
        stickyColumn: false,
        isRowHeader: true,
        searchable: true,
        readonly: false
      }, {
        key: 'display_name',
        label: "Denumire",
        field_type: 'label',
        sortable: true,
        stickyColumn: false,
        isRowHeader: true,
        searchable: true,
        readonly: false
      }, {
        key: 'pivot.isactive',
        label: 'Permisiune',
        field_type: 'checkbox'
      }],
      fieldsGrupuri: [{
        key: 'name',
        label: "Denumire",
        field_type: 'label',
        sortable: true,
        stickyColumn: false,
        isRowHeader: true,
        searchable: true,
        readonly: false
      }, {
        key: 'isactive',
        label: 'Permisiune',
        field_type: 'checkbox'
      }],
      fieldsNotificari: [{
        key: 'denumire',
        label: "Denumire",
        field_type: 'label',
        sortable: true,
        stickyColumn: false,
        isRowHeader: true,
        searchable: true,
        readonly: false
      }, {
        key: 'channel',
        label: "Canal",
        field_type: 'selectonearray',
        sortable: true,
        stickyColumn: false,
        isRowHeader: true,
        searchable: true,
        readonly: false,
        lista: [{
          "denumire": "Email"
        }, {
          "denumire": "Application"
        }]
      } //{key:'isactive',label:'Permisiune',field_type:'checkbox'} 
      ]
    };
  },
  watch: {
    activeCopy: function activeCopy() {
      this.activeCopyLocal = this.activeCopy;
    },
    activeCopyLocal: function activeCopyLocal() {
      if (this.activeCopyLocal == false) {
        this.$emit('closed');
      }
    },
    activeAction: function activeAction() {
      this.activeActionLocal = this.activeAction;
    },
    editVar: function editVar() {
      this.editVarLocal = this.editVar;
    }
  },
  methods: Object(C_dianasoft_serviciiweb_web_node_modules_babel_runtime_helpers_esm_defineProperty_js__WEBPACK_IMPORTED_MODULE_0__["default"])({
    removeFilter: function removeFilter() {
      this.permisiuniFiltrate("");
    },
    randSelectat: function randSelectat(value) {
      if (value) {
        this.permisiuniFiltrate(value);
      }
    },
    permisiuniFiltrate: function permisiuniFiltrate(id_optiune) {
      var _this = this;

      this.showLoading = true;
      var payLoad = this.editVarLocal;
      payLoad.id_optiune = id_optiune;
      payLoad.requestType = "post";
      payLoad.requestUrl = "/utilizatori/permisiunifiltrate";
      this.$store.dispatch("app/api_Request", payLoad).then(function (response) {
        _this.editVarLocal.permissions = response;
        _this.showLoading = false;
      });
    },
    initTrHeight: function initTrHeight() {
      var _this2 = this;

      this.trSetHeight(null);
      this.$nextTick(function () {
        if (_this2.$refs.form) {
          _this2.trSetHeight(_this2.$refs.form.scrollHeight);
        }
      });
    },
    handleOk: function handleOk(bvModalEvt) {
      var _this3 = this;

      bvModalEvt.preventDefault();
      this.showLoading = true;
      this.$refs.simpleRules.validate().then(function (success) {
        if (success) {
          _this3.showLoading = true;
          var payLoad = _this3.editVarLocal;
          payLoad.id_optiune = "";
          payLoad.requestType = "post";
          payLoad.requestUrl = "/utilizatori/permisiunifiltrate";

          _this3.$store.dispatch("app/api_Request", payLoad).then(function (response) {
            var payLoad = _this3.editVarLocal;
            payLoad.requestType = "post";
            payLoad.requestUrl = "/utilizatori/updatedrepturi";

            _this3.$store.dispatch("app/api_Request", payLoad).then(function (response) {
              _this3.$bvToast.toast("Salvare efectuată cu succes!", {
                title: "Salvare efectuată cu succes!",
                variant: "success",
                solid: false,
                appendToast: true,
                autoHideDelay: 3000,
                toaster: "b-toaster-bottom-right"
              });

              _this3.editVarLocal = {
                name: "",
                user_type: "",
                email: "",
                password: "",
                telefon: "",
                blocat: "",
                functia: "",
                status: "",
                link_poza: "",
                program_de_lucru: "",
                data_expirare_parola: "",
                departament: "",
                sex: "",
                grup: [],
                notificari: []
              };
              _this3.showLoading = false;

              _this3.$emit("closed");
            })["catch"](function (error) {
              _this3.showLoading = false;

              _this3.$bvToast.toast(error.data.message, {
                title: "Eroare! ",
                variant: "danger",
                solid: true,
                appendToast: false,
                noAutoHide: true,
                toaster: "b-toaster-top-right"
              });
            });
          });
        }
      });
    },
    aevClosed: function aevClosed() {
      this.activeCopyLocal = false;
      this.editVarLocal = {
        name: "",
        user_type: "",
        email: "",
        password: "",
        telefon: "",
        blocat: "",
        functia: "",
        status: "",
        link_poza: "",
        program_de_lucru: "",
        data_expirare_parola: "",
        departament: "",
        sex: "",
        grup: [],
        notificari: []
      };
      this.activeActionLocal = "";
      this.$emit("closed");
    }
  }, "initTrHeight", function initTrHeight() {
    var _this4 = this;

    this.trSetHeight(null);
    this.$nextTick(function () {
      if (_this4.$refs.form) {
        _this4.trSetHeight(_this4.$refs.form.scrollHeight);
      }
    });
  }),
  mounted: function mounted() {
    this.initTrHeight();
  },
  destroyed: function destroyed() {
    window.removeEventListener("resize", this.initTrHeight);
  },
  created: function created() {
    if (!this.rutainapoi) {
      this.rutainapoiLocal = this.modelName;
    }

    window.addEventListener("resize", this.initTrHeight);
  }
});

/***/ }),

/***/ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/app_pages/Utilizatori.vue?vue&type=template&id=525f3c5d&":
/*!***********************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/src/views/app_pages/Utilizatori.vue?vue&type=template&id=525f3c5d& ***!
  \***********************************************************************************************************************************************************************************************************************/
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
    "b-overlay",
    {
      attrs: {
        show: _vm.showLoading,
        rounded: "sm",
        "no-fade": "",
        variant: "primary",
        opacity: "0.25",
        blur: "2px",
      },
    },
    [
      _c(
        "b-row",
        { staticClass: "d-flex align-items-center justify-content-center" },
        [
          _c(
            "b-col",
            { attrs: { cols: "12" } },
            [
              _c(
                "b-card",
                [
                  _c(
                    "tabelcomponent",
                    {
                      attrs: {
                        cols: 12,
                        columnDefs: _vm.columnDefs,
                        modelName: _vm.modelName,
                        refresh: _vm.refreshLocal,
                        titlu: _vm.modelDisplayName,
                        idselectat: _vm.idselectat,
                        campFiltruStart: _vm.campFiltruStart,
                      },
                      on: {
                        onSelectionChanged: _vm.onSelectionChanged,
                        adauga: _vm.add,
                        edit: _vm.edit,
                        view: _vm.view,
                      },
                    },
                    [
                      _c(
                        "b-button",
                        {
                          directives: [
                            {
                              name: "show",
                              rawName: "v-show",
                              value: _vm.canAdd,
                              expression: "canAdd",
                            },
                            {
                              name: "b-tooltip",
                              rawName: "v-b-tooltip.hover.v-dark",
                              modifiers: { hover: true, "v-dark": true },
                            },
                          ],
                          staticClass: "btn-icon mr-1",
                          attrs: {
                            variant: "outline-success",
                            size: "lg",
                            title: "Drepturi utilizator",
                          },
                          on: { click: _vm.modificaDrepturi },
                        },
                        [
                          _c("feather-icon", {
                            attrs: { icon: "UserCheckIcon" },
                          }),
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
                              value: _vm.canAdd,
                              expression: "canAdd",
                            },
                            {
                              name: "b-tooltip",
                              rawName: "v-b-tooltip.hover.v-dark",
                              modifiers: { hover: true, "v-dark": true },
                            },
                          ],
                          staticClass: "btn-icon mr-1",
                          attrs: {
                            variant: "outline-success",
                            size: "lg",
                            title: "Copiază drepturi utilizator",
                          },
                          on: { click: _vm.copiazaDrepturi },
                        },
                        [
                          _c("feather-icon", {
                            attrs: { icon: "UserCheckIcon" },
                          }),
                          _vm._v(
                            "\n                       Copiază drepturi catre\n\n           "
                          ),
                        ],
                        1
                      ),
                      _vm._v(" "),
                      _c(
                        "b-dropdown",
                        {
                          staticClass: "dropdown-icon-wrapper mr-1",
                          attrs: {
                            text: "Situații utilizatori",
                            variant: "outline-info",
                          },
                          scopedSlots: _vm._u([
                            {
                              key: "button-content",
                              fn: function () {
                                return [
                                  _c("feather-icon", {
                                    staticClass: "align-middle",
                                    attrs: { icon: "ListIcon", size: "16" },
                                  }),
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
                            { on: { click: _vm.fisaUtilizator } },
                            [
                              _vm._v(
                                "\n                                                          Fisa utilizator\n                                     "
                              ),
                            ]
                          ),
                          _vm._v(" "),
                          _c("b-dropdown-divider"),
                          _vm._v(" "),
                          _c(
                            "b-dropdown-item",
                            { on: { click: _vm.situateiDrepturiUtilizatori } },
                            [
                              _vm._v(
                                "\n                                                          Situatie drepturi utilizatori\n                                     "
                              ),
                            ]
                          ),
                          _vm._v(" "),
                          _c("b-dropdown-divider"),
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
          ),
        ],
        1
      ),
      _vm._v(" "),
      _c("Utilizatoriaev", {
        directives: [
          {
            name: "show",
            rawName: "v-show",
            value: _vm.activeEdit,
            expression: "activeEdit",
          },
        ],
        attrs: {
          activeAction: _vm.activeAction,
          activeEdit: _vm.activeEdit,
          editVar: _vm.editVar,
        },
        on: { stored: _vm.afisezSalvat, closed: _vm.aevClosed },
      }),
      _vm._v(" "),
      _c("Utilizatoricopiazadrepturi", {
        directives: [
          {
            name: "show",
            rawName: "v-show",
            value: _vm.activeCopy,
            expression: "activeCopy",
          },
        ],
        attrs: {
          activeAction: _vm.activeAction,
          activeCopy: _vm.activeCopy,
          editVar: _vm.editVarCopy,
        },
        on: { stored: _vm.afisezSalvat, closed: _vm.copyClosed },
      }),
      _vm._v(" "),
      _c("Utilizatorimodificadrepturi", {
        directives: [
          {
            name: "show",
            rawName: "v-show",
            value: _vm.activeModificaDrepturi,
            expression: "activeModificaDrepturi",
          },
        ],
        attrs: {
          activeAction: _vm.activeAction,
          activeCopy: _vm.activeModificaDrepturi,
          editVar: _vm.editVar,
        },
        on: { stored: _vm.afisezSalvat, closed: _vm.modificaDrepturiClosed },
      }),
    ],
    1
  )
}
var staticRenderFns = []
render._withStripped = true



/***/ }),

/***/ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/app_pages/Utilizatoriaev.vue?vue&type=template&id=2583d685&":
/*!**************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/src/views/app_pages/Utilizatoriaev.vue?vue&type=template&id=2583d685& ***!
  \**************************************************************************************************************************************************************************************************************************/
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
  return _c("validation-observer", { ref: "simpleRules" }, [
    _c(
      "div",
      { staticClass: "d-flex justify-content-center" },
      [
        _c(
          "b-modal",
          {
            attrs: {
              id: "Dianasoftmodelaev",
              size: "lg",
              "no-close-on-backdrop": "",
              "ok-variant": "success",
              "cancel-title": "Cancel",
              "ok-title": "Save",
              "cancel-variant": "warning",
              scrollable: "",
              "cancel-disabled": _vm.activeActionLocal == "Vizualizează",
              "ok-disabled": _vm.activeActionLocal == "Vizualizează",
              "modal-class": "modal-success",
              title: _vm.activeActionLocal + " Utilizatori",
            },
            on: { ok: _vm.handleOk, cancel: _vm.aevClosed },
            scopedSlots: _vm._u([
              {
                key: "modal-footer",
                fn: function () {
                  return [
                    !_vm.showLoading
                      ? _c(
                          "div",
                          [
                            _c(
                              "b-button",
                              {
                                attrs: { variant: "warning" },
                                on: { click: _vm.aevClosed },
                              },
                              [_vm._v("\n         Cancel\n      ")]
                            ),
                            _vm._v(" "),
                            _c(
                              "b-button",
                              {
                                attrs: { variant: "success" },
                                on: { click: _vm.handleOk },
                              },
                              [_vm._v("\n         Salvez\n      ")]
                            ),
                          ],
                          1
                        )
                      : _c(
                          "div",
                          { staticClass: "text-center w-100" },
                          [
                            _c("b-spinner", {
                              attrs: {
                                variant: "info",
                                small: "",
                                label: "Salvez...",
                              },
                            }),
                            _vm._v(" "),
                            _c("label", { staticClass: "labelSelect" }, [
                              _vm._v(" Vă rugăm așteptați..."),
                            ]),
                          ],
                          1
                        ),
                  ]
                },
                proxy: true,
              },
            ]),
            model: {
              value: _vm.activeEditLocal,
              callback: function ($$v) {
                _vm.activeEditLocal = $$v
              },
              expression: "activeEditLocal",
            },
          },
          [
            _vm._v(" "),
            _c(
              "b-overlay",
              {
                attrs: {
                  show: _vm.showLoading,
                  rounded: "sm",
                  "no-fade": "",
                  variant: "primary",
                  opacity: "0.25",
                  blur: "2px",
                },
              },
              [
                _c(
                  "form",
                  {
                    ref: "form",
                    on: {
                      submit: function ($event) {
                        $event.stopPropagation()
                        $event.preventDefault()
                        return _vm.handleSubmit.apply(null, arguments)
                      },
                    },
                  },
                  [
                    _c("br"),
                    _vm._v(" "),
                    _c(
                      "b-row",
                      { staticClass: "d-flex justify-content-center" },
                      [
                        _c("b-col", { attrs: { cols: "8" } }, [
                          _c(
                            "div",
                            { staticClass: "form-label-group" },
                            [
                              _c("validation-provider", {
                                attrs: { name: "Nume", rules: "required" },
                                scopedSlots: _vm._u([
                                  {
                                    key: "default",
                                    fn: function (ref) {
                                      var errors = ref.errors
                                      return [
                                        _c("b-form-input", {
                                          attrs: {
                                            readonly:
                                              _vm.activeActionLocal ==
                                              "Vizualizează",
                                            autocomplete: "off",
                                            id: "name",
                                            placeholder: "Nume",
                                          },
                                          model: {
                                            value: _vm.editVarLocal.name,
                                            callback: function ($$v) {
                                              _vm.$set(
                                                _vm.editVarLocal,
                                                "name",
                                                $$v
                                              )
                                            },
                                            expression: "editVarLocal.name",
                                          },
                                        }),
                                        _vm._v(" "),
                                        _c(
                                          "small",
                                          { staticClass: "text-danger" },
                                          [_vm._v(_vm._s(errors[0]))]
                                        ),
                                      ]
                                    },
                                  },
                                ]),
                              }),
                              _vm._v(" "),
                              _c("label", { attrs: { for: "name" } }, [
                                _vm._v("Nume"),
                              ]),
                            ],
                            1
                          ),
                        ]),
                      ],
                      1
                    ),
                    _vm._v(" "),
                    _c(
                      "b-row",
                      { staticClass: "d-flex justify-content-center" },
                      [
                        _c(
                          "b-col",
                          { attrs: { cols: "8" } },
                          [
                            _c("validation-provider", {
                              attrs: { name: "Sex", rules: "required" },
                              scopedSlots: _vm._u([
                                {
                                  key: "default",
                                  fn: function (ref) {
                                    var errors = ref.errors
                                    return [
                                      _c("dropdowncuoptiuni", {
                                        attrs: {
                                          name: "sex",
                                          readonly:
                                            _vm.activeAction == "Vizualizează",
                                          campDisplay: "Sex",
                                          field_name: "Sex",
                                          limitToList: "true",
                                        },
                                        model: {
                                          value: _vm.editVarLocal.sex,
                                          callback: function ($$v) {
                                            _vm.$set(
                                              _vm.editVarLocal,
                                              "sex",
                                              $$v
                                            )
                                          },
                                          expression: "editVarLocal.sex",
                                        },
                                      }),
                                      _vm._v(" "),
                                      _c(
                                        "small",
                                        { staticClass: "text-danger" },
                                        [_vm._v(_vm._s(errors[0]))]
                                      ),
                                    ]
                                  },
                                },
                              ]),
                            }),
                          ],
                          1
                        ),
                      ],
                      1
                    ),
                    _vm._v(" "),
                    _c(
                      "b-row",
                      { staticClass: "d-flex justify-content-center" },
                      [
                        _c("b-col", { attrs: { cols: "8" } }, [
                          _c(
                            "div",
                            { staticClass: "form-label-group" },
                            [
                              _c("validation-provider", {
                                attrs: {
                                  name: "Email",
                                  rules: "required|email",
                                },
                                scopedSlots: _vm._u([
                                  {
                                    key: "default",
                                    fn: function (ref) {
                                      var errors = ref.errors
                                      return [
                                        _c("b-form-input", {
                                          attrs: {
                                            readonly:
                                              _vm.activeActionLocal ==
                                              "Vizualizează",
                                            autocomplete: "off",
                                            id: "email",
                                            placeholder: "Email",
                                          },
                                          model: {
                                            value: _vm.editVarLocal.email,
                                            callback: function ($$v) {
                                              _vm.$set(
                                                _vm.editVarLocal,
                                                "email",
                                                $$v
                                              )
                                            },
                                            expression: "editVarLocal.email",
                                          },
                                        }),
                                        _vm._v(" "),
                                        _c(
                                          "small",
                                          { staticClass: "text-danger" },
                                          [_vm._v(_vm._s(errors[0]))]
                                        ),
                                      ]
                                    },
                                  },
                                ]),
                              }),
                              _vm._v(" "),
                              _c("label", { attrs: { for: "email" } }, [
                                _vm._v("Email"),
                              ]),
                            ],
                            1
                          ),
                        ]),
                      ],
                      1
                    ),
                    _vm._v(" "),
                    _c(
                      "b-row",
                      { staticClass: "d-flex justify-content-center" },
                      [
                        _c("b-col", { attrs: { cols: "8" } }, [
                          _c(
                            "div",
                            { staticClass: "form-label-group" },
                            [
                              _c("validation-provider", {
                                attrs: {
                                  name: "Parola",
                                  rules: "required|min:6",
                                },
                                scopedSlots: _vm._u([
                                  {
                                    key: "default",
                                    fn: function (ref) {
                                      var errors = ref.errors
                                      return [
                                        _c(
                                          "b-input-group",
                                          { staticClass: "input-group-merge" },
                                          [
                                            _c("b-form-input", {
                                              attrs: {
                                                readonly:
                                                  _vm.activeActionLocal ==
                                                  "Vizualizează",
                                                autocomplete: "off",
                                                id: "password",
                                                type: _vm.passwordFieldType,
                                                placeholder: "Parola",
                                              },
                                              model: {
                                                value:
                                                  _vm.editVarLocal.password,
                                                callback: function ($$v) {
                                                  _vm.$set(
                                                    _vm.editVarLocal,
                                                    "password",
                                                    $$v
                                                  )
                                                },
                                                expression:
                                                  "editVarLocal.password",
                                              },
                                            }),
                                            _vm._v(" "),
                                            _c(
                                              "b-input-group-append",
                                              { attrs: { "is-text": "" } },
                                              [
                                                _c("feather-icon", {
                                                  staticClass: "cursor-pointer",
                                                  attrs: {
                                                    icon: _vm.passwordToggleIcon,
                                                  },
                                                  on: {
                                                    click:
                                                      _vm.togglePasswordVisibility,
                                                  },
                                                }),
                                              ],
                                              1
                                            ),
                                          ],
                                          1
                                        ),
                                        _vm._v(" "),
                                        _c(
                                          "label",
                                          { attrs: { for: "password" } },
                                          [_vm._v("Parola")]
                                        ),
                                        _vm._v(" "),
                                        _c(
                                          "small",
                                          { staticClass: "text-danger" },
                                          [_vm._v(_vm._s(errors[0]))]
                                        ),
                                      ]
                                    },
                                  },
                                ]),
                              }),
                            ],
                            1
                          ),
                        ]),
                      ],
                      1
                    ),
                    _vm._v(" "),
                    _c(
                      "b-row",
                      { staticClass: "d-flex justify-content-center" },
                      [
                        _c("b-col", { attrs: { cols: "8" } }, [
                          _c(
                            "div",
                            { staticClass: "form-label-group" },
                            [
                              _c("validation-provider", {
                                attrs: { name: "Telefon", rules: "required" },
                                scopedSlots: _vm._u([
                                  {
                                    key: "default",
                                    fn: function (ref) {
                                      var errors = ref.errors
                                      return [
                                        _c("b-form-input", {
                                          attrs: {
                                            readonly:
                                              _vm.activeActionLocal ==
                                              "Vizualizează",
                                            id: "telefon",
                                            autocomplete: "off",
                                            placeholder: "Telefon",
                                          },
                                          model: {
                                            value: _vm.editVarLocal.telefon,
                                            callback: function ($$v) {
                                              _vm.$set(
                                                _vm.editVarLocal,
                                                "telefon",
                                                $$v
                                              )
                                            },
                                            expression: "editVarLocal.telefon",
                                          },
                                        }),
                                        _vm._v(" "),
                                        _c(
                                          "small",
                                          { staticClass: "text-danger" },
                                          [_vm._v(_vm._s(errors[0]))]
                                        ),
                                      ]
                                    },
                                  },
                                ]),
                              }),
                              _vm._v(" "),
                              _c("label", { attrs: { for: "telefon" } }, [
                                _vm._v("Telefon"),
                              ]),
                            ],
                            1
                          ),
                        ]),
                      ],
                      1
                    ),
                    _vm._v(" "),
                    _c(
                      "b-row",
                      { staticClass: "d-flex justify-content-center" },
                      [
                        _c("b-col", { attrs: { cols: "8" } }, [
                          _c(
                            "div",
                            { staticClass: "form-label-group" },
                            [
                              _c("validation-provider", {
                                attrs: { name: "Functia", rules: "required" },
                                scopedSlots: _vm._u([
                                  {
                                    key: "default",
                                    fn: function (ref) {
                                      var errors = ref.errors
                                      return [
                                        _c("b-form-input", {
                                          attrs: {
                                            readonly:
                                              _vm.activeActionLocal ==
                                              "Vizualizează",
                                            id: "functia",
                                            autocomplete: "off",
                                            placeholder: "Functia",
                                          },
                                          model: {
                                            value: _vm.editVarLocal.functia,
                                            callback: function ($$v) {
                                              _vm.$set(
                                                _vm.editVarLocal,
                                                "functia",
                                                $$v
                                              )
                                            },
                                            expression: "editVarLocal.functia",
                                          },
                                        }),
                                        _vm._v(" "),
                                        _c(
                                          "small",
                                          { staticClass: "text-danger" },
                                          [_vm._v(_vm._s(errors[0]))]
                                        ),
                                      ]
                                    },
                                  },
                                ]),
                              }),
                              _vm._v(" "),
                              _c("label", { attrs: { for: "functia" } }, [
                                _vm._v("Functia"),
                              ]),
                            ],
                            1
                          ),
                        ]),
                      ],
                      1
                    ),
                    _vm._v(" "),
                    _c(
                      "b-row",
                      { staticClass: "d-flex justify-content-center" },
                      [
                        _c("b-col", { attrs: { cols: "8" } }, [
                          _c(
                            "div",
                            { staticClass: "form-label-group" },
                            [
                              _c("b-form-input", {
                                attrs: {
                                  autocomplete: "off",
                                  readonly:
                                    _vm.activeActionLocal == "Vizualizează",
                                  id: "program_de_lucru",
                                  placeholder: "Program de lucru",
                                },
                                model: {
                                  value: _vm.editVarLocal.program_de_lucru,
                                  callback: function ($$v) {
                                    _vm.$set(
                                      _vm.editVarLocal,
                                      "program_de_lucru",
                                      $$v
                                    )
                                  },
                                  expression: "editVarLocal.program_de_lucru",
                                },
                              }),
                              _vm._v(" "),
                              _c(
                                "label",
                                { attrs: { for: "program_de_lucru" } },
                                [_vm._v("Program de lucru")]
                              ),
                            ],
                            1
                          ),
                        ]),
                      ],
                      1
                    ),
                    _vm._v(" "),
                    _c(
                      "b-row",
                      { staticClass: "d-flex justify-content-center" },
                      [
                        _c(
                          "b-col",
                          { attrs: { cols: "8" } },
                          [
                            _c("validation-provider", {
                              attrs: { name: "Departament", rules: "required" },
                              scopedSlots: _vm._u([
                                {
                                  key: "default",
                                  fn: function (ref) {
                                    var errors = ref.errors
                                    return [
                                      _c("dropdowncuoptiuni", {
                                        attrs: {
                                          name: "departament",
                                          readonly:
                                            _vm.activeAction == "Vizualizează",
                                          campDisplay: "Departament",
                                          field_name: "Departament",
                                          limitToList: "true",
                                        },
                                        model: {
                                          value: _vm.editVarLocal.departament,
                                          callback: function ($$v) {
                                            _vm.$set(
                                              _vm.editVarLocal,
                                              "departament",
                                              $$v
                                            )
                                          },
                                          expression:
                                            "editVarLocal.departament",
                                        },
                                      }),
                                      _vm._v(" "),
                                      _c(
                                        "small",
                                        { staticClass: "text-danger" },
                                        [_vm._v(_vm._s(errors[0]))]
                                      ),
                                    ]
                                  },
                                },
                              ]),
                            }),
                          ],
                          1
                        ),
                      ],
                      1
                    ),
                    _vm._v(" "),
                    _c("br"),
                    _c("br"),
                    _c("br"),
                    _c("br"),
                    _c("br"),
                    _c("br"),
                    _c("br"),
                  ],
                  1
                ),
              ]
            ),
          ],
          1
        ),
      ],
      1
    ),
  ])
}
var staticRenderFns = []
render._withStripped = true



/***/ }),

/***/ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/app_pages/Utilizatoricopiazadrepturi.vue?vue&type=template&id=9b710eb2&":
/*!**************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/src/views/app_pages/Utilizatoricopiazadrepturi.vue?vue&type=template&id=9b710eb2& ***!
  \**************************************************************************************************************************************************************************************************************************************/
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
  return _c("validation-observer", { ref: "simpleRules" }, [
    _c(
      "div",
      { staticClass: "d-flex justify-content-center" },
      [
        _c(
          "b-modal",
          {
            attrs: {
              id: "Dianasoftmodelaev",
              size: "lg",
              "no-close-on-backdrop": "",
              "ok-variant": "success",
              "cancel-title": "Cancel",
              "ok-title": "Save",
              "cancel-variant": "warning",
              scrollable: "",
              "modal-class": "modal-success",
              title:
                _vm.activeActionLocal +
                " drepturi de la utilizatorul " +
                this.editVarLocal.from.name,
            },
            on: { ok: _vm.handleOk, cancel: _vm.aevClosed },
            scopedSlots: _vm._u([
              {
                key: "modal-footer",
                fn: function () {
                  return [
                    !_vm.showLoading
                      ? _c(
                          "div",
                          [
                            _c(
                              "b-button",
                              {
                                attrs: { variant: "warning" },
                                on: { click: _vm.aevClosed },
                              },
                              [_vm._v("\n         Cancel\n      ")]
                            ),
                            _vm._v(" "),
                            _c(
                              "b-button",
                              {
                                attrs: { variant: "success" },
                                on: { click: _vm.handleOk },
                              },
                              [_vm._v("\n         Salvez\n      ")]
                            ),
                          ],
                          1
                        )
                      : _c(
                          "div",
                          { staticClass: "text-center w-100" },
                          [
                            _c("b-spinner", {
                              attrs: {
                                variant: "info",
                                small: "",
                                label: "Salvez...",
                              },
                            }),
                            _vm._v(" "),
                            _c("label", { staticClass: "labelSelect" }, [
                              _vm._v(" Vă rugăm așteptați..."),
                            ]),
                          ],
                          1
                        ),
                  ]
                },
                proxy: true,
              },
            ]),
            model: {
              value: _vm.activeCopyLocal,
              callback: function ($$v) {
                _vm.activeCopyLocal = $$v
              },
              expression: "activeCopyLocal",
            },
          },
          [
            _vm._v(" "),
            _c(
              "form",
              {
                ref: "form",
                on: {
                  submit: function ($event) {
                    $event.stopPropagation()
                    $event.preventDefault()
                    return _vm.handleSubmit.apply(null, arguments)
                  },
                },
              },
              [
                _c("br"),
                _vm._v(" "),
                _c(
                  "b-row",
                  { staticClass: "d-flex justify-content-center" },
                  [
                    _c("b-col", { attrs: { cols: "8" } }, [
                      _c(
                        "div",
                        { staticClass: "form-label-group" },
                        [
                          _c("validation-provider", {
                            attrs: { name: "Utilizator", rules: "required" },
                            scopedSlots: _vm._u([
                              {
                                key: "default",
                                fn: function (ref) {
                                  var errors = ref.errors
                                  return [
                                    _c("selectmultipleuser", {
                                      attrs: { labelDisplay: "Catre" },
                                      on: { change: _vm.modificaTo },
                                      model: {
                                        value: _vm.editVarLocal.to,
                                        callback: function ($$v) {
                                          _vm.$set(_vm.editVarLocal, "to", $$v)
                                        },
                                        expression: "editVarLocal.to",
                                      },
                                    }),
                                    _vm._v(" "),
                                    _c(
                                      "small",
                                      { staticClass: "text-danger" },
                                      [_vm._v(_vm._s(errors[0]))]
                                    ),
                                  ]
                                },
                              },
                            ]),
                          }),
                          _vm._v(" "),
                          _c("label", { attrs: { for: "name" } }, [
                            _vm._v("Utilizatori"),
                          ]),
                        ],
                        1
                      ),
                    ]),
                  ],
                  1
                ),
                _vm._v(" "),
                _c("br"),
                _c("br"),
                _c("br"),
                _c("br"),
                _c("br"),
                _c("br"),
                _c("br"),
                _c("br"),
                _c("br"),
                _c("br"),
                _c("br"),
                _c("br"),
                _c("br"),
                _c("br"),
                _c("br"),
                _c("br"),
                _c("br"),
                _c("br"),
                _c("br"),
                _c("br"),
                _c("br"),
              ],
              1
            ),
          ]
        ),
      ],
      1
    ),
  ])
}
var staticRenderFns = []
render._withStripped = true



/***/ }),

/***/ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/app_pages/Utilizatorimodificadrepturi.vue?vue&type=template&id=71957216&":
/*!***************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/src/views/app_pages/Utilizatorimodificadrepturi.vue?vue&type=template&id=71957216& ***!
  \***************************************************************************************************************************************************************************************************************************************/
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
    "b-modal",
    {
      attrs: {
        id: "Dianasoftmodelaev",
        size: "xl",
        "no-close-on-backdrop": "",
        "ok-variant": "success",
        "cancel-title": "Cancel",
        "ok-title": "Save",
        "cancel-variant": "warning",
        scrollable: "",
        "modal-class": "modal-success",
        title:
          "Modifică drepturi pentru utilizatorul " + this.editVarLocal.name,
      },
      on: { ok: _vm.handleOk, cancel: _vm.aevClosed },
      scopedSlots: _vm._u([
        {
          key: "modal-footer",
          fn: function () {
            return [
              !_vm.showLoading
                ? _c(
                    "div",
                    [
                      _c(
                        "b-button",
                        {
                          attrs: { variant: "warning" },
                          on: { click: _vm.aevClosed },
                        },
                        [_vm._v("\n         Cancel\n      ")]
                      ),
                      _vm._v(" "),
                      _c(
                        "b-button",
                        {
                          attrs: { variant: "success" },
                          on: { click: _vm.handleOk },
                        },
                        [_vm._v("\n         Salvez\n      ")]
                      ),
                    ],
                    1
                  )
                : _c(
                    "div",
                    { staticClass: "text-center w-100" },
                    [
                      _c("b-spinner", {
                        attrs: {
                          variant: "info",
                          small: "",
                          label: "Salvez...",
                        },
                      }),
                      _vm._v(" "),
                      _c("label", { staticClass: "labelSelect" }, [
                        _vm._v(" Vă rugăm așteptați..."),
                      ]),
                    ],
                    1
                  ),
            ]
          },
          proxy: true,
        },
      ]),
      model: {
        value: _vm.activeCopyLocal,
        callback: function ($$v) {
          _vm.activeCopyLocal = $$v
        },
        expression: "activeCopyLocal",
      },
    },
    [
      _vm._v(" "),
      _c("validation-observer", { ref: "simpleRules" }, [
        _c(
          "form",
          {
            ref: "form",
            on: {
              submit: function ($event) {
                $event.stopPropagation()
                $event.preventDefault()
                return _vm.handleSubmit.apply(null, arguments)
              },
            },
          },
          [
            _c(
              "b-overlay",
              {
                attrs: {
                  show: _vm.showLoading,
                  rounded: "sm",
                  "no-fade": "",
                  variant: "primary",
                  opacity: "0.25",
                  blur: "2px",
                },
              },
              [
                _c(
                  "b-row",
                  { staticClass: "d-flex justify-content-center" },
                  [
                    _c(
                      "b-col",
                      { attrs: { cols: "8" } },
                      [
                        _c("dstable", {
                          attrs: {
                            scrollbar: true,
                            title: "Notificari",
                            fields: _vm.fieldsNotificari,
                            items: _vm.editVarLocal.notificari,
                          },
                        }),
                      ],
                      1
                    ),
                    _vm._v(" "),
                    _c(
                      "b-col",
                      { attrs: { cols: "4" } },
                      [
                        _c("dstable", {
                          attrs: {
                            scrollbar: true,
                            title: "Grupuri",
                            fields: _vm.fieldsGrupuri,
                            items: _vm.editVarLocal.grupuri,
                          },
                        }),
                      ],
                      1
                    ),
                  ],
                  1
                ),
                _vm._v(" "),
                _c("br"),
                _vm._v(" "),
                _c(
                  "b-row",
                  { staticClass: "d-flex justify-content-center" },
                  [
                    _c(
                      "b-col",
                      { attrs: { cols: "4" } },
                      [
                        _c("dstable", {
                          attrs: {
                            scrollbar: true,
                            title: "Opțiuni menu",
                            fields: _vm.fieldsOptiuniMenu,
                            items: _vm.editVarLocal.dianasoftmenuoptions,
                          },
                          on: { rowSelected: _vm.randSelectat },
                        }),
                      ],
                      1
                    ),
                    _vm._v(" "),
                    _c(
                      "b-col",
                      { attrs: { cols: "6" } },
                      [
                        _c(
                          "div",
                          { staticClass: "d-flex flex-row-reverse" },
                          [
                            _c(
                              "b-button",
                              {
                                directives: [
                                  {
                                    name: "ripple",
                                    rawName: "v-ripple.400",
                                    value: "rgba(113, 102, 240, 0.15)",
                                    expression: "'rgba(113, 102, 240, 0.15)'",
                                    modifiers: { 400: true },
                                  },
                                  {
                                    name: "b-tooltip",
                                    rawName: "v-b-tooltip.hover.v-light",
                                    modifiers: { hover: true, "v-light": true },
                                  },
                                ],
                                staticClass: "btn-icon  mr-1",
                                attrs: {
                                  variant: "outline-danger",
                                  size: "",
                                  title: "Remove filter",
                                },
                                on: { click: _vm.removeFilter },
                              },
                              [
                                _c("feather-icon", {
                                  attrs: { icon: "XIcon" },
                                }),
                              ],
                              1
                            ),
                          ],
                          1
                        ),
                        _vm._v(" "),
                        _c("dstable", {
                          attrs: {
                            scrollbar: true,
                            title: "Operațiuni",
                            fields: _vm.fieldsOperatiuni,
                            items: _vm.editVarLocal.permissions,
                          },
                        }),
                      ],
                      1
                    ),
                    _vm._v(" "),
                    _c(
                      "b-col",
                      { attrs: { cols: "2" } },
                      [
                        _c("dstable", {
                          attrs: {
                            scrollbar: true,
                            title: "Gestiuni",
                            fields: _vm.fieldsGestiuni,
                            items: _vm.editVarLocal.gestiuni,
                          },
                        }),
                      ],
                      1
                    ),
                  ],
                  1
                ),
                _vm._v(" "),
                _c("br"),
              ],
              1
            ),
          ],
          1
        ),
      ]),
    ],
    1
  )
}
var staticRenderFns = []
render._withStripped = true



/***/ }),

/***/ "./resources/js/src/views/app_pages/Utilizatori.vue":
/*!**********************************************************!*\
  !*** ./resources/js/src/views/app_pages/Utilizatori.vue ***!
  \**********************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _Utilizatori_vue_vue_type_template_id_525f3c5d___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./Utilizatori.vue?vue&type=template&id=525f3c5d& */ "./resources/js/src/views/app_pages/Utilizatori.vue?vue&type=template&id=525f3c5d&");
/* harmony import */ var _Utilizatori_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./Utilizatori.vue?vue&type=script&lang=js& */ "./resources/js/src/views/app_pages/Utilizatori.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport *//* harmony import */ var _node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../../../../node_modules/vue-loader/lib/runtime/componentNormalizer.js */ "./node_modules/vue-loader/lib/runtime/componentNormalizer.js");





/* normalize component */

var component = Object(_node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__["default"])(
  _Utilizatori_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__["default"],
  _Utilizatori_vue_vue_type_template_id_525f3c5d___WEBPACK_IMPORTED_MODULE_0__["render"],
  _Utilizatori_vue_vue_type_template_id_525f3c5d___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"],
  false,
  null,
  null,
  null
  
)

/* hot reload */
if (false) { var api; }
component.options.__file = "resources/js/src/views/app_pages/Utilizatori.vue"
/* harmony default export */ __webpack_exports__["default"] = (component.exports);

/***/ }),

/***/ "./resources/js/src/views/app_pages/Utilizatori.vue?vue&type=script&lang=js&":
/*!***********************************************************************************!*\
  !*** ./resources/js/src/views/app_pages/Utilizatori.vue?vue&type=script&lang=js& ***!
  \***********************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_Utilizatori_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../node_modules/babel-loader/lib??ref--4-0!../../../../../node_modules/vue-loader/lib??vue-loader-options!./Utilizatori.vue?vue&type=script&lang=js& */ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/app_pages/Utilizatori.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__["default"] = (_node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_Utilizatori_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__["default"]); 

/***/ }),

/***/ "./resources/js/src/views/app_pages/Utilizatori.vue?vue&type=template&id=525f3c5d&":
/*!*****************************************************************************************!*\
  !*** ./resources/js/src/views/app_pages/Utilizatori.vue?vue&type=template&id=525f3c5d& ***!
  \*****************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_Utilizatori_vue_vue_type_template_id_525f3c5d___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!../../../../../node_modules/vue-loader/lib??vue-loader-options!./Utilizatori.vue?vue&type=template&id=525f3c5d& */ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/app_pages/Utilizatori.vue?vue&type=template&id=525f3c5d&");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "render", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_Utilizatori_vue_vue_type_template_id_525f3c5d___WEBPACK_IMPORTED_MODULE_0__["render"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "staticRenderFns", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_Utilizatori_vue_vue_type_template_id_525f3c5d___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"]; });



/***/ }),

/***/ "./resources/js/src/views/app_pages/Utilizatoriaev.vue":
/*!*************************************************************!*\
  !*** ./resources/js/src/views/app_pages/Utilizatoriaev.vue ***!
  \*************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _Utilizatoriaev_vue_vue_type_template_id_2583d685___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./Utilizatoriaev.vue?vue&type=template&id=2583d685& */ "./resources/js/src/views/app_pages/Utilizatoriaev.vue?vue&type=template&id=2583d685&");
/* harmony import */ var _Utilizatoriaev_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./Utilizatoriaev.vue?vue&type=script&lang=js& */ "./resources/js/src/views/app_pages/Utilizatoriaev.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport *//* harmony import */ var _node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../../../../node_modules/vue-loader/lib/runtime/componentNormalizer.js */ "./node_modules/vue-loader/lib/runtime/componentNormalizer.js");





/* normalize component */

var component = Object(_node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__["default"])(
  _Utilizatoriaev_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__["default"],
  _Utilizatoriaev_vue_vue_type_template_id_2583d685___WEBPACK_IMPORTED_MODULE_0__["render"],
  _Utilizatoriaev_vue_vue_type_template_id_2583d685___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"],
  false,
  null,
  null,
  null
  
)

/* hot reload */
if (false) { var api; }
component.options.__file = "resources/js/src/views/app_pages/Utilizatoriaev.vue"
/* harmony default export */ __webpack_exports__["default"] = (component.exports);

/***/ }),

/***/ "./resources/js/src/views/app_pages/Utilizatoriaev.vue?vue&type=script&lang=js&":
/*!**************************************************************************************!*\
  !*** ./resources/js/src/views/app_pages/Utilizatoriaev.vue?vue&type=script&lang=js& ***!
  \**************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_Utilizatoriaev_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../node_modules/babel-loader/lib??ref--4-0!../../../../../node_modules/vue-loader/lib??vue-loader-options!./Utilizatoriaev.vue?vue&type=script&lang=js& */ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/app_pages/Utilizatoriaev.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__["default"] = (_node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_Utilizatoriaev_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__["default"]); 

/***/ }),

/***/ "./resources/js/src/views/app_pages/Utilizatoriaev.vue?vue&type=template&id=2583d685&":
/*!********************************************************************************************!*\
  !*** ./resources/js/src/views/app_pages/Utilizatoriaev.vue?vue&type=template&id=2583d685& ***!
  \********************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_Utilizatoriaev_vue_vue_type_template_id_2583d685___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!../../../../../node_modules/vue-loader/lib??vue-loader-options!./Utilizatoriaev.vue?vue&type=template&id=2583d685& */ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/app_pages/Utilizatoriaev.vue?vue&type=template&id=2583d685&");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "render", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_Utilizatoriaev_vue_vue_type_template_id_2583d685___WEBPACK_IMPORTED_MODULE_0__["render"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "staticRenderFns", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_Utilizatoriaev_vue_vue_type_template_id_2583d685___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"]; });



/***/ }),

/***/ "./resources/js/src/views/app_pages/Utilizatoricopiazadrepturi.vue":
/*!*************************************************************************!*\
  !*** ./resources/js/src/views/app_pages/Utilizatoricopiazadrepturi.vue ***!
  \*************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _Utilizatoricopiazadrepturi_vue_vue_type_template_id_9b710eb2___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./Utilizatoricopiazadrepturi.vue?vue&type=template&id=9b710eb2& */ "./resources/js/src/views/app_pages/Utilizatoricopiazadrepturi.vue?vue&type=template&id=9b710eb2&");
/* harmony import */ var _Utilizatoricopiazadrepturi_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./Utilizatoricopiazadrepturi.vue?vue&type=script&lang=js& */ "./resources/js/src/views/app_pages/Utilizatoricopiazadrepturi.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport *//* harmony import */ var _node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../../../../node_modules/vue-loader/lib/runtime/componentNormalizer.js */ "./node_modules/vue-loader/lib/runtime/componentNormalizer.js");





/* normalize component */

var component = Object(_node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__["default"])(
  _Utilizatoricopiazadrepturi_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__["default"],
  _Utilizatoricopiazadrepturi_vue_vue_type_template_id_9b710eb2___WEBPACK_IMPORTED_MODULE_0__["render"],
  _Utilizatoricopiazadrepturi_vue_vue_type_template_id_9b710eb2___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"],
  false,
  null,
  null,
  null
  
)

/* hot reload */
if (false) { var api; }
component.options.__file = "resources/js/src/views/app_pages/Utilizatoricopiazadrepturi.vue"
/* harmony default export */ __webpack_exports__["default"] = (component.exports);

/***/ }),

/***/ "./resources/js/src/views/app_pages/Utilizatoricopiazadrepturi.vue?vue&type=script&lang=js&":
/*!**************************************************************************************************!*\
  !*** ./resources/js/src/views/app_pages/Utilizatoricopiazadrepturi.vue?vue&type=script&lang=js& ***!
  \**************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_Utilizatoricopiazadrepturi_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../node_modules/babel-loader/lib??ref--4-0!../../../../../node_modules/vue-loader/lib??vue-loader-options!./Utilizatoricopiazadrepturi.vue?vue&type=script&lang=js& */ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/app_pages/Utilizatoricopiazadrepturi.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__["default"] = (_node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_Utilizatoricopiazadrepturi_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__["default"]); 

/***/ }),

/***/ "./resources/js/src/views/app_pages/Utilizatoricopiazadrepturi.vue?vue&type=template&id=9b710eb2&":
/*!********************************************************************************************************!*\
  !*** ./resources/js/src/views/app_pages/Utilizatoricopiazadrepturi.vue?vue&type=template&id=9b710eb2& ***!
  \********************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_Utilizatoricopiazadrepturi_vue_vue_type_template_id_9b710eb2___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!../../../../../node_modules/vue-loader/lib??vue-loader-options!./Utilizatoricopiazadrepturi.vue?vue&type=template&id=9b710eb2& */ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/app_pages/Utilizatoricopiazadrepturi.vue?vue&type=template&id=9b710eb2&");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "render", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_Utilizatoricopiazadrepturi_vue_vue_type_template_id_9b710eb2___WEBPACK_IMPORTED_MODULE_0__["render"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "staticRenderFns", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_Utilizatoricopiazadrepturi_vue_vue_type_template_id_9b710eb2___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"]; });



/***/ }),

/***/ "./resources/js/src/views/app_pages/Utilizatorimodificadrepturi.vue":
/*!**************************************************************************!*\
  !*** ./resources/js/src/views/app_pages/Utilizatorimodificadrepturi.vue ***!
  \**************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _Utilizatorimodificadrepturi_vue_vue_type_template_id_71957216___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./Utilizatorimodificadrepturi.vue?vue&type=template&id=71957216& */ "./resources/js/src/views/app_pages/Utilizatorimodificadrepturi.vue?vue&type=template&id=71957216&");
/* harmony import */ var _Utilizatorimodificadrepturi_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./Utilizatorimodificadrepturi.vue?vue&type=script&lang=js& */ "./resources/js/src/views/app_pages/Utilizatorimodificadrepturi.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport *//* harmony import */ var _node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../../../../node_modules/vue-loader/lib/runtime/componentNormalizer.js */ "./node_modules/vue-loader/lib/runtime/componentNormalizer.js");





/* normalize component */

var component = Object(_node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__["default"])(
  _Utilizatorimodificadrepturi_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__["default"],
  _Utilizatorimodificadrepturi_vue_vue_type_template_id_71957216___WEBPACK_IMPORTED_MODULE_0__["render"],
  _Utilizatorimodificadrepturi_vue_vue_type_template_id_71957216___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"],
  false,
  null,
  null,
  null
  
)

/* hot reload */
if (false) { var api; }
component.options.__file = "resources/js/src/views/app_pages/Utilizatorimodificadrepturi.vue"
/* harmony default export */ __webpack_exports__["default"] = (component.exports);

/***/ }),

/***/ "./resources/js/src/views/app_pages/Utilizatorimodificadrepturi.vue?vue&type=script&lang=js&":
/*!***************************************************************************************************!*\
  !*** ./resources/js/src/views/app_pages/Utilizatorimodificadrepturi.vue?vue&type=script&lang=js& ***!
  \***************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_Utilizatorimodificadrepturi_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../node_modules/babel-loader/lib??ref--4-0!../../../../../node_modules/vue-loader/lib??vue-loader-options!./Utilizatorimodificadrepturi.vue?vue&type=script&lang=js& */ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/app_pages/Utilizatorimodificadrepturi.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__["default"] = (_node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_Utilizatorimodificadrepturi_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__["default"]); 

/***/ }),

/***/ "./resources/js/src/views/app_pages/Utilizatorimodificadrepturi.vue?vue&type=template&id=71957216&":
/*!*********************************************************************************************************!*\
  !*** ./resources/js/src/views/app_pages/Utilizatorimodificadrepturi.vue?vue&type=template&id=71957216& ***!
  \*********************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_Utilizatorimodificadrepturi_vue_vue_type_template_id_71957216___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!../../../../../node_modules/vue-loader/lib??vue-loader-options!./Utilizatorimodificadrepturi.vue?vue&type=template&id=71957216& */ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/app_pages/Utilizatorimodificadrepturi.vue?vue&type=template&id=71957216&");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "render", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_Utilizatorimodificadrepturi_vue_vue_type_template_id_71957216___WEBPACK_IMPORTED_MODULE_0__["render"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "staticRenderFns", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_Utilizatorimodificadrepturi_vue_vue_type_template_id_71957216___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"]; });



/***/ })

}]);