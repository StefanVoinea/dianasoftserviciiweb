(window["webpackJsonp"] = window["webpackJsonp"] || []).push([[110],{

/***/ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/app_pages/Litigiu.vue?vue&type=script&lang=js&":
/*!***************************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib??ref--4-0!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/src/views/app_pages/Litigiu.vue?vue&type=script&lang=js& ***!
  \***************************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _Litigiuaev_vue__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./Litigiuaev.vue */ "./resources/js/src/views/app_pages/Litigiuaev.vue");
/* harmony import */ var _rapoarte_SituatieLitigii_vue__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./rapoarte/SituatieLitigii.vue */ "./resources/js/src/views/app_pages/rapoarte/SituatieLitigii.vue");
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
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
    Litigiuaev: _Litigiuaev_vue__WEBPACK_IMPORTED_MODULE_0__["default"],
    SituatieLitigii: _rapoarte_SituatieLitigii_vue__WEBPACK_IMPORTED_MODULE_1__["default"]
  },
  name: "litigiu",
  data: function data() {
    return {
      canViewSituatieLitigii: this.$userpermitt.can("viewSituatieLitigii"),
      refreshLocal: false,
      idselectat: null,
      campFiltruStart: "",
      afisezSL: false,
      modelName: "litigiu",
      modelDisplayName: "Litigii",
      editVar: {
        numar_dosar: "",
        numar_vechi: "",
        data_dosar: "",
        institutie: "",
        departament: "",
        categorie_caz: "",
        stadiu_procesual: "",
        avocatul_apararii: "",
        avocatul_acuzarii: "",
        observatii: "",
        status: "",
        taxa_de_timbru: "",
        cheltuieli_de_judecata: "",
        parti: "",
        litigiicaleatac: [{}],
        litigiiparti: [{}],
        litigiisedinte: [{}],
        litigii: [{}]
      },
      activeEdit: false,
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
        label: "Numar dosar",
        field: "numar_dosar",
        width: "150px",
        type: "text",
        showSortAsc: true
      }, {
        label: "Data dosar",
        field: "data_dosar",
        width: "150px",
        type: "date",
        //dateInputFormat: "yyyy-MM-dd'T'HH:mm:ss.SSSSSS'Z'", // expects 2018-03-16
        //dateOutputFormat: "dd.MM.yyyy HH:mm:ss", // outputs Mar 16th 2018
        dateInputFormat: "yyyy-MM-dd",
        // expects 2018-03-16
        dateOutputFormat: "dd.MM.yyyy",
        // outputs Mar 16th 2018 
        showSortAsc: true
      }, {
        label: "Data modificare",
        field: "data_modificare",
        width: "200px",
        type: "date",
        dateInputFormat: "yyyy-MM-dd'T'HH:mm:ss.SSSSSS'Z'",
        // expects 2018-03-16
        dateOutputFormat: "dd.MM.yyyy HH:mm:ss",
        // outputs Mar 16th 2018
        //dateInputFormat: "yyyy-MM-dd", // expects 2018-03-16
        //dateOutputFormat: "dd.MM.yyyy", // outputs Mar 16th 2018 
        showSortAsc: true
      }, {
        label: "Parti",
        field: "parti",
        width: "300px",
        type: "text",
        showSortAsc: true
      }, {
        label: "Stadiu procesual",
        field: "stadiu_procesual",
        width: "150px",
        type: "text",
        showSortAsc: true
      }, {
        label: "Institutia",
        field: "institutie",
        width: "250px",
        type: "text",
        showSortAsc: true
      }, {
        label: "Departament",
        field: "departament",
        width: "150px",
        type: "text",
        showSortAsc: true
      }, {
        label: "Categorie caz",
        field: "categorie_caz",
        width: "150px",
        type: "text",
        showSortAsc: true
      }, {
        label: "Status",
        field: "status",
        width: "150px",
        type: "text",
        showSortAsc: true
      }, {
        label: "Data ultimei verificari",
        field: "data_ultimei_verificari",
        width: "200px",
        type: "date",
        dateInputFormat: "yyyy-MM-dd'T'HH:mm:ss.SSSSSS'Z'",
        // expects 2018-03-16
        dateOutputFormat: "dd.MM.yyyy HH:mm:ss",
        // outputs Mar 16th 2018
        //dateInputFormat: "yyyy-MM-dd", // expects 2018-03-16
        //dateOutputFormat: "dd.MM.yyyy", // outputs Mar 16th 2018 
        showSortAsc: true
      }, {
        label: "Avocatul apararii",
        field: "avocatul_apararii",
        width: "150px",
        type: "text",
        showSortAsc: true
      }, {
        label: "Avocatul acuzarii",
        field: "avocatul_acuzarii",
        width: "150px",
        type: "text",
        showSortAsc: true
      }, {
        label: "Observatii",
        field: "observatii",
        width: "300px",
        type: "text",
        showSortAsc: true
      }, {
        label: "Taxa de timbru",
        field: "taxa_de_timbru",
        width: "300px",
        type: "text",
        showSortAsc: true
      }, {
        label: "Cheltuieli de judecata",
        field: "cheltuieli_de_judecata",
        width: "300px",
        type: "text",
        showSortAsc: true
      }]
    };
  },
  methods: {
    situatieLitigii: function situatieLitigii() {
      this.afisezSL = true;
    },
    aevClosed: function aevClosed() {
      this.idselectat = null;
      this.selectedID = "";
      this.activeEdit = false;
      this.editVar = {
        numar_dosar: "",
        numar_vechi: "",
        data_dosar: "",
        institutie: "",
        departament: "",
        categorie_caz: "",
        stadiu_procesual: "",
        avocatul_apararii: "",
        avocatul_acuzarii: "",
        observatii: "",
        status: "",
        taxa_de_timbru: "",
        cheltuieli_de_judecata: "",
        parti: ""
      }, this.activeAction = "";
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
        numar_dosar: "",
        numar_vechi: "",
        data_dosar: "",
        institutie: "",
        departament: "",
        categorie_caz: "",
        stadiu_procesual: "",
        avocatul_apararii: "",
        avocatul_acuzarii: "",
        observatii: "",
        status: "",
        taxa_de_timbru: "",
        cheltuieli_de_judecata: "",
        parti: "",
        litigii: [{}],
        litigiicaleatac: [{}],
        litigiiparti: [{}],
        litigiisedinte: [{}]
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

/***/ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/app_pages/Litigiuaev.vue?vue&type=script&lang=js&":
/*!******************************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib??ref--4-0!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/src/views/app_pages/Litigiuaev.vue?vue&type=script&lang=js& ***!
  \******************************************************************************************************************************************************************************/
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
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
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
  name: "litigiuaev",
  data: function data() {
    return {
      required: _validations__WEBPACK_IMPORTED_MODULE_4__["required"],
      password: _validations__WEBPACK_IMPORTED_MODULE_4__["password"],
      email: _validations__WEBPACK_IMPORTED_MODULE_4__["email"],
      confirmed: _validations__WEBPACK_IMPORTED_MODULE_4__["confirmed"],
      min: _validations__WEBPACK_IMPORTED_MODULE_4__["min"],
      rutainapoiLocal: this.rutainapoi,
      activeEditLocal: false,
      activeActionLocal: this.activeAction,
      editVarLocal: this.editVar,
      nextTodoId: 2,
      modelName: "litigiu",
      litigii: [],
      showLoading: false
    };
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
    modificaNumarDosar: function modificaNumarDosar() {
      var _this = this;

      if (this.editVarLocal.numar_dosar) {
        this.showLoading = true;
        var payLoad = this.editVarLocal;
        payLoad.requestType = "post";
        payLoad.requestUrl = "/" + this.modelName + "/preiaNumarDosar";
        this.$store.dispatch("app/api_Request", payLoad).then(function (response) {
          if (response.length == 0) {
            _this.$bvToast.toast("Nu am preluat niciun dosar!", {
              title: "Preluare cu succes! ",
              variant: "warning",
              solid: false,
              appendToast: true,
              autoHideDelay: 3000,
              toaster: "b-toaster-bottom-right"
            });
          } else {
            _this.editVarLocal = response[0];
            _this.litigii = response;

            _this.$bvToast.toast("Am preluat " + response.length + " dosare cu succes!", {
              title: "Preluare cu succes! ",
              variant: "success",
              solid: false,
              appendToast: true,
              autoHideDelay: 3000,
              toaster: "b-toaster-bottom-right"
            });
          }

          _this.showLoading = false;
        })["catch"](function (error) {
          _this.showLoading = false;

          _this.$bvToast.toast(error.data.message, {
            title: "Eroare! ",
            variant: "danger",
            solid: true,
            appendToast: false,
            noAutoHide: true,
            toaster: "b-toaster-top-right"
          });
        });
      }
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
      this.$refs.simpleRules.validate().then(function (success) {
        if (success) {
          if (_this3.activeActionLocal == "Adaugă") {
            _this3.saveAdd();
          }

          if (_this3.activeActionLocal == "Modifică") {
            _this3.saveEdit();
          }
        }
      });
    },
    saveAdd: function saveAdd() {
      var _this4 = this;

      this.showLoading = true;
      var payLoad = Object.assign({}, this.editVarLocal);
      payLoad.requestType = "post";
      payLoad.requestUrl = "/" + this.modelName + "/store";
      payLoad.litigii = this.litigii;
      this.$store.dispatch("app/api_Request", payLoad).then(function (response) {
        _this4.editVarLocal = {
          numar_dosar: '',
          numar_vechi: '',
          data_dosar: '',
          institutie: '',
          departament: '',
          categorie_caz: '',
          stadiu_procesual: '',
          avocatul_apararii: '',
          avocatul_acuzarii: '',
          observatii: '',
          status: '',
          taxa_de_timbru: '',
          cheltuieli_de_judecata: '',
          parti: '',
          litigiicaleatac: [{}],
          litigiiparti: [{}],
          litigiisedinte: [{}],
          litigii: [{}]
        };

        _this4.$bvToast.toast("Salvare efectuata cu success!", {
          title: "Salvare cu succes! ",
          variant: "success",
          solid: false,
          appendToast: true,
          autoHideDelay: 3000,
          toaster: "b-toaster-bottom-right"
        });

        _this4.showLoading = false;
        _this4.activeEditLocal = false;
        _this4.activeActionLocal = "";

        _this4.$emit("stored", response);

        _this4.$emit("closed");
      })["catch"](function (error) {
        _this4.showLoading = false;

        _this4.$bvToast.toast(error.data.message, {
          title: "Eroare! ",
          variant: "danger",
          solid: true,
          appendToast: false,
          noAutoHide: true,
          toaster: "b-toaster-top-right"
        });
      });
    },
    aevClosed: function aevClosed() {
      this.idselectat = null;
      this.selectedID = "";
      this.activeEditLocal = false;
      this.editVarLocal = {
        numar_dosar: "",
        numar_vechi: "",
        data_dosar: "",
        institutie: "",
        departament: "",
        categorie_caz: "",
        stadiu_procesual: "",
        avocatul_apararii: "",
        avocatul_acuzarii: "",
        observatii: "",
        status: "",
        taxa_de_timbru: "",
        cheltuieli_de_judecata: "",
        parti: "",
        litigiicaleatac: [{}],
        litigiiparti: [{}],
        litigiisedinte: [{}],
        litigii: [{}]
      };
      this.activeActionLocal = "";
      this.$emit("closed");
    },
    saveEdit: function saveEdit() {
      var _this5 = this;

      this.showLoading = true;
      var payLoad = this.editVarLocal;
      payLoad.requestType = "post";
      payLoad.requestUrl = "/" + this.modelName + "/edit/" + this.editVarLocal.id;
      this.$store.dispatch("app/api_Request", payLoad).then(function (response) {
        _this5.selectedID = "";
        _this5.editVarLocal = {
          numar_dosar: "",
          numar_vechi: "",
          data_dosar: "",
          institutie: "",
          departament: "",
          categorie_caz: "",
          stadiu_procesual: "",
          avocatul_apararii: "",
          avocatul_acuzarii: "",
          observatii: "",
          status: "",
          taxa_de_timbru: "",
          cheltuieli_de_judecata: "",
          parti: "",
          litigiicaleatac: [{}],
          litigiiparti: [{}],
          litigiisedinte: [{}],
          litigii: [{}]
        };

        _this5.$bvToast.toast("Modificare efectuata cu success!", {
          title: "Modificare cu succes! ",
          variant: "success",
          solid: false,
          appendToast: true,
          autoHideDelay: 3000,
          toaster: "b-toaster-bottom-right"
        });

        _this5.showLoading = false;
        _this5.activeEditLocal = false;
        _this5.activeActionLocal = "";

        _this5.$emit("stored", "");

        _this5.$emit("closed");
      })["catch"](function (error) {
        _this5.showLoading = false;

        _this5.$bvToast.toast(error.data.message, {
          title: "Eroare! ",
          variant: "danger",
          solid: true,
          appendToast: false,
          noAutoHide: true,
          toaster: "b-toaster-top-right"
        });
      });
    }
  }, "initTrHeight", function initTrHeight() {
    var _this6 = this;

    this.trSetHeight(null);
    this.$nextTick(function () {
      if (_this6.$refs.form) {
        _this6.trSetHeight(_this6.$refs.form.scrollHeight);
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

/***/ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/app_pages/rapoarte/SituatieLitigii.vue?vue&type=script&lang=js&":
/*!********************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib??ref--4-0!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/src/views/app_pages/rapoarte/SituatieLitigii.vue?vue&type=script&lang=js& ***!
  \********************************************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var vue_perfect_scrollbar__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! vue-perfect-scrollbar */ "./node_modules/vue-perfect-scrollbar/dist/index.js");
/* harmony import */ var vue_perfect_scrollbar__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(vue_perfect_scrollbar__WEBPACK_IMPORTED_MODULE_0__);
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//


var FileSaver = __webpack_require__(/*! file-saver */ "./node_modules/file-saver/dist/FileSaver.min.js");

/* harmony default export */ __webpack_exports__["default"] = ({
  props: {
    afisezSL: Boolean
  },
  components: {
    VuePerfectScrollbar: vue_perfect_scrollbar__WEBPACK_IMPORTED_MODULE_0___default.a
  },
  name: "SituatieLitigii",
  data: function data() {
    return {
      afisezSLLocal: this.afisezSL,
      showLoading: false,
      settings: {
        maxScrollbarLength: 60
      },
      editVarLocal: {
        doar_cu_sold: false
      }
    };
  },
  watch: {
    afisezSL: function afisezSL() {
      this.afisezSLLocal = this.afisezSL;

      if (this.afisezSL) {
        var dataCurenta = new Date();
        this.editVarLocal.data = dataCurenta;
      }
    },
    afisezSLLocal: function afisezSLLocal() {
      if (this.afisezSLLocal == false) {
        this.$emit('closed');
      }
    }
  },
  methods: {
    afisezSLClosed: function afisezSLClosed() {
      this.activeSCELocal = false;
      this.editVarLocal = {
        doar_cu_sold: false
      };
      this.$emit('closed');
    },
    closeEditSideBar: function closeEditSideBar() {
      this.activeSCELocal = false;
      this.editVarLocal = {};
      this.$emit('closed');
    }
  }
});

/***/ }),

/***/ "./node_modules/file-saver/dist/FileSaver.min.js":
/*!*******************************************************!*\
  !*** ./node_modules/file-saver/dist/FileSaver.min.js ***!
  \*******************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

/* WEBPACK VAR INJECTION */(function(global) {var __WEBPACK_AMD_DEFINE_FACTORY__, __WEBPACK_AMD_DEFINE_ARRAY__, __WEBPACK_AMD_DEFINE_RESULT__;(function(a,b){if(true)!(__WEBPACK_AMD_DEFINE_ARRAY__ = [], __WEBPACK_AMD_DEFINE_FACTORY__ = (b),
				__WEBPACK_AMD_DEFINE_RESULT__ = (typeof __WEBPACK_AMD_DEFINE_FACTORY__ === 'function' ?
				(__WEBPACK_AMD_DEFINE_FACTORY__.apply(exports, __WEBPACK_AMD_DEFINE_ARRAY__)) : __WEBPACK_AMD_DEFINE_FACTORY__),
				__WEBPACK_AMD_DEFINE_RESULT__ !== undefined && (module.exports = __WEBPACK_AMD_DEFINE_RESULT__));else {}})(this,function(){"use strict";function b(a,b){return"undefined"==typeof b?b={autoBom:!1}:"object"!=typeof b&&(console.warn("Deprecated: Expected third argument to be a object"),b={autoBom:!b}),b.autoBom&&/^\s*(?:text\/\S*|application\/xml|\S*\/\S*\+xml)\s*;.*charset\s*=\s*utf-8/i.test(a.type)?new Blob(["\uFEFF",a],{type:a.type}):a}function c(a,b,c){var d=new XMLHttpRequest;d.open("GET",a),d.responseType="blob",d.onload=function(){g(d.response,b,c)},d.onerror=function(){console.error("could not download file")},d.send()}function d(a){var b=new XMLHttpRequest;b.open("HEAD",a,!1);try{b.send()}catch(a){}return 200<=b.status&&299>=b.status}function e(a){try{a.dispatchEvent(new MouseEvent("click"))}catch(c){var b=document.createEvent("MouseEvents");b.initMouseEvent("click",!0,!0,window,0,0,0,80,20,!1,!1,!1,!1,0,null),a.dispatchEvent(b)}}var f="object"==typeof window&&window.window===window?window:"object"==typeof self&&self.self===self?self:"object"==typeof global&&global.global===global?global:void 0,a=f.navigator&&/Macintosh/.test(navigator.userAgent)&&/AppleWebKit/.test(navigator.userAgent)&&!/Safari/.test(navigator.userAgent),g=f.saveAs||("object"!=typeof window||window!==f?function(){}:"download"in HTMLAnchorElement.prototype&&!a?function(b,g,h){var i=f.URL||f.webkitURL,j=document.createElement("a");g=g||b.name||"download",j.download=g,j.rel="noopener","string"==typeof b?(j.href=b,j.origin===location.origin?e(j):d(j.href)?c(b,g,h):e(j,j.target="_blank")):(j.href=i.createObjectURL(b),setTimeout(function(){i.revokeObjectURL(j.href)},4E4),setTimeout(function(){e(j)},0))}:"msSaveOrOpenBlob"in navigator?function(f,g,h){if(g=g||f.name||"download","string"!=typeof f)navigator.msSaveOrOpenBlob(b(f,h),g);else if(d(f))c(f,g,h);else{var i=document.createElement("a");i.href=f,i.target="_blank",setTimeout(function(){e(i)})}}:function(b,d,e,g){if(g=g||open("","_blank"),g&&(g.document.title=g.document.body.innerText="downloading..."),"string"==typeof b)return c(b,d,e);var h="application/octet-stream"===b.type,i=/constructor/i.test(f.HTMLElement)||f.safari,j=/CriOS\/[\d]+/.test(navigator.userAgent);if((j||h&&i||a)&&"undefined"!=typeof FileReader){var k=new FileReader;k.onloadend=function(){var a=k.result;a=j?a:a.replace(/^data:[^;]*;/,"data:attachment/file;"),g?g.location.href=a:location=a,g=null},k.readAsDataURL(b)}else{var l=f.URL||f.webkitURL,m=l.createObjectURL(b);g?g.location=m:location.href=m,g=null,setTimeout(function(){l.revokeObjectURL(m)},4E4)}});f.saveAs=g.saveAs=g, true&&(module.exports=g)});

//# sourceMappingURL=FileSaver.min.js.map
/* WEBPACK VAR INJECTION */}.call(this, __webpack_require__(/*! ./../../webpack/buildin/global.js */ "./node_modules/webpack/buildin/global.js")))

/***/ }),

/***/ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/app_pages/Litigiu.vue?vue&type=template&id=ba06d984&":
/*!*******************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/src/views/app_pages/Litigiu.vue?vue&type=template&id=ba06d984& ***!
  \*******************************************************************************************************************************************************************************************************************/
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
                        "b-dropdown",
                        {
                          staticClass: "dropdown-icon-wrapper mr-1",
                          attrs: {
                            text: "Situatii litigii",
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
                          _vm.canViewSituatieLitigii
                            ? _c(
                                "b-dropdown-item",
                                { on: { click: _vm.situatieLitigii } },
                                [
                                  _vm._v(
                                    "\n                                                         Situatie litigii\n                                                       "
                                  ),
                                ]
                              )
                            : _vm._e(),
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
      _c("Litigiuaev", {
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
      _c("SituatieLitigii", {
        attrs: { afisezSL: _vm.afisezSL },
        on: {
          closed: function ($event) {
            _vm.afisezSL = false
          },
        },
      }),
    ],
    1
  )
}
var staticRenderFns = []
render._withStripped = true



/***/ }),

/***/ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/app_pages/Litigiuaev.vue?vue&type=template&id=5b450804&":
/*!**********************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/src/views/app_pages/Litigiuaev.vue?vue&type=template&id=5b450804& ***!
  \**********************************************************************************************************************************************************************************************************************/
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
              size: "xl",
              "no-close-on-backdrop": "",
              "hide-footer": _vm.activeActionLocal == "Vizualizează",
              "ok-variant": "success",
              "cancel-title": "Cancel",
              "ok-title": "Save",
              "cancel-variant": "warning",
              scrollable: "",
              "cancel-disabled": _vm.activeActionLocal == "Vizualizează",
              "ok-disabled": _vm.activeActionLocal == "Vizualizează",
              "modal-class": "modal-success",
              title: _vm.activeActionLocal + " Litigiu",
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
                    _c(
                      "b-col",
                      { attrs: { cols: "6" } },
                      [
                        _c(
                          "b-row",
                          { staticClass: "d-flex justify-content-center" },
                          [
                            _c(
                              "b-col",
                              { attrs: { cols: "2" } },
                              [
                                _c("validation-provider", {
                                  attrs: {
                                    name: "Numar dosar",
                                    rules: "required",
                                  },
                                  scopedSlots: _vm._u([
                                    {
                                      key: "default",
                                      fn: function (ref) {
                                        var errors = ref.errors
                                        return [
                                          _c(
                                            "div",
                                            { staticClass: "form-label-group" },
                                            [
                                              _c("b-form-input", {
                                                attrs: {
                                                  readonly:
                                                    _vm.activeActionLocal ==
                                                    "Vizualizează",
                                                  size: "sm",
                                                  autocomplete: "off",
                                                  id: "numar_dosar",
                                                  placeholder: "Numar dosar",
                                                },
                                                on: {
                                                  change:
                                                    _vm.modificaNumarDosar,
                                                },
                                                model: {
                                                  value:
                                                    _vm.editVarLocal
                                                      .numar_dosar,
                                                  callback: function ($$v) {
                                                    _vm.$set(
                                                      _vm.editVarLocal,
                                                      "numar_dosar",
                                                      $$v
                                                    )
                                                  },
                                                  expression:
                                                    "editVarLocal.numar_dosar",
                                                },
                                              }),
                                              _vm._v(" "),
                                              _c(
                                                "label",
                                                {
                                                  attrs: { for: "numar_dosar" },
                                                },
                                                [_vm._v("Numar dosar")]
                                              ),
                                              _vm._v(" "),
                                              _c(
                                                "small",
                                                { staticClass: "text-danger" },
                                                [_vm._v(_vm._s(errors[0]))]
                                              ),
                                            ],
                                            1
                                          ),
                                        ]
                                      },
                                    },
                                  ]),
                                }),
                              ],
                              1
                            ),
                            _vm._v(" "),
                            _c(
                              "b-col",
                              { attrs: { cols: "3" } },
                              [
                                _c("validation-provider", {
                                  attrs: {
                                    name: "Avocatul apararii",
                                    rules: "",
                                  },
                                  scopedSlots: _vm._u([
                                    {
                                      key: "default",
                                      fn: function (ref) {
                                        var errors = ref.errors
                                        return [
                                          _c(
                                            "div",
                                            { staticClass: "form-label-group" },
                                            [
                                              _c("b-form-input", {
                                                attrs: {
                                                  readonly:
                                                    _vm.activeActionLocal ==
                                                    "Vizualizează",
                                                  size: "sm",
                                                  autocomplete: "off",
                                                  id: "avocatul_apararii",
                                                  placeholder:
                                                    "Avocatul apararii",
                                                },
                                                model: {
                                                  value:
                                                    _vm.editVarLocal
                                                      .avocatul_apararii,
                                                  callback: function ($$v) {
                                                    _vm.$set(
                                                      _vm.editVarLocal,
                                                      "avocatul_apararii",
                                                      $$v
                                                    )
                                                  },
                                                  expression:
                                                    "editVarLocal.avocatul_apararii",
                                                },
                                              }),
                                              _vm._v(" "),
                                              _c(
                                                "label",
                                                {
                                                  attrs: {
                                                    for: "avocatul_apararii",
                                                  },
                                                },
                                                [_vm._v("Avocatul apararii")]
                                              ),
                                              _vm._v(" "),
                                              _c(
                                                "small",
                                                { staticClass: "text-danger" },
                                                [_vm._v(_vm._s(errors[0]))]
                                              ),
                                            ],
                                            1
                                          ),
                                        ]
                                      },
                                    },
                                  ]),
                                }),
                              ],
                              1
                            ),
                            _vm._v(" "),
                            _c(
                              "b-col",
                              { attrs: { cols: "3" } },
                              [
                                _c("validation-provider", {
                                  attrs: {
                                    name: "Avocatul acuzarii",
                                    rules: "",
                                  },
                                  scopedSlots: _vm._u([
                                    {
                                      key: "default",
                                      fn: function (ref) {
                                        var errors = ref.errors
                                        return [
                                          _c(
                                            "div",
                                            { staticClass: "form-label-group" },
                                            [
                                              _c("b-form-input", {
                                                attrs: {
                                                  readonly:
                                                    _vm.activeActionLocal ==
                                                    "Vizualizează",
                                                  size: "sm",
                                                  autocomplete: "off",
                                                  id: "avocatul_acuzarii",
                                                  placeholder:
                                                    "Avocatul acuzarii",
                                                },
                                                model: {
                                                  value:
                                                    _vm.editVarLocal
                                                      .avocatul_acuzarii,
                                                  callback: function ($$v) {
                                                    _vm.$set(
                                                      _vm.editVarLocal,
                                                      "avocatul_acuzarii",
                                                      $$v
                                                    )
                                                  },
                                                  expression:
                                                    "editVarLocal.avocatul_acuzarii",
                                                },
                                              }),
                                              _vm._v(" "),
                                              _c(
                                                "label",
                                                {
                                                  attrs: {
                                                    for: "avocatul_acuzarii",
                                                  },
                                                },
                                                [_vm._v("Avocatul acuzarii")]
                                              ),
                                              _vm._v(" "),
                                              _c(
                                                "small",
                                                { staticClass: "text-danger" },
                                                [_vm._v(_vm._s(errors[0]))]
                                              ),
                                            ],
                                            1
                                          ),
                                        ]
                                      },
                                    },
                                  ]),
                                }),
                              ],
                              1
                            ),
                            _vm._v(" "),
                            _c(
                              "b-col",
                              { attrs: { cols: "2" } },
                              [
                                _c("validation-provider", {
                                  attrs: { name: "Taxa de timbru", rules: "" },
                                  scopedSlots: _vm._u([
                                    {
                                      key: "default",
                                      fn: function (ref) {
                                        var errors = ref.errors
                                        return [
                                          _c(
                                            "div",
                                            { staticClass: "form-label-group" },
                                            [
                                              _c("b-form-input", {
                                                attrs: {
                                                  readonly:
                                                    _vm.activeActionLocal ==
                                                    "Vizualizează",
                                                  size: "sm",
                                                  autocomplete: "off",
                                                  id: "taxa_de_timbru",
                                                  placeholder: "Taxa de timbru",
                                                },
                                                model: {
                                                  value:
                                                    _vm.editVarLocal
                                                      .taxa_de_timbru,
                                                  callback: function ($$v) {
                                                    _vm.$set(
                                                      _vm.editVarLocal,
                                                      "taxa_de_timbru",
                                                      $$v
                                                    )
                                                  },
                                                  expression:
                                                    "editVarLocal.taxa_de_timbru",
                                                },
                                              }),
                                              _vm._v(" "),
                                              _c(
                                                "label",
                                                {
                                                  attrs: {
                                                    for: "taxa_de_timbru",
                                                  },
                                                },
                                                [_vm._v("Taxa de timbru")]
                                              ),
                                              _vm._v(" "),
                                              _c(
                                                "small",
                                                { staticClass: "text-danger" },
                                                [_vm._v(_vm._s(errors[0]))]
                                              ),
                                            ],
                                            1
                                          ),
                                        ]
                                      },
                                    },
                                  ]),
                                }),
                              ],
                              1
                            ),
                            _vm._v(" "),
                            _c(
                              "b-col",
                              { attrs: { cols: "2" } },
                              [
                                _c("validation-provider", {
                                  attrs: {
                                    name: "Cheltuieli de judecata",
                                    rules: "",
                                  },
                                  scopedSlots: _vm._u([
                                    {
                                      key: "default",
                                      fn: function (ref) {
                                        var errors = ref.errors
                                        return [
                                          _c(
                                            "div",
                                            { staticClass: "form-label-group" },
                                            [
                                              _c("b-form-input", {
                                                attrs: {
                                                  readonly:
                                                    _vm.activeActionLocal ==
                                                    "Vizualizează",
                                                  size: "sm",
                                                  autocomplete: "off",
                                                  id: "cheltuieli_de_judecata",
                                                  placeholder:
                                                    "Cheltuieli de judecata",
                                                },
                                                model: {
                                                  value:
                                                    _vm.editVarLocal
                                                      .cheltuieli_de_judecata,
                                                  callback: function ($$v) {
                                                    _vm.$set(
                                                      _vm.editVarLocal,
                                                      "cheltuieli_de_judecata",
                                                      $$v
                                                    )
                                                  },
                                                  expression:
                                                    "editVarLocal.cheltuieli_de_judecata",
                                                },
                                              }),
                                              _vm._v(" "),
                                              _c(
                                                "label",
                                                {
                                                  attrs: {
                                                    for: "cheltuieli_de_judecata",
                                                  },
                                                },
                                                [
                                                  _vm._v(
                                                    "Cheltuieli de judecata"
                                                  ),
                                                ]
                                              ),
                                              _vm._v(" "),
                                              _c(
                                                "small",
                                                { staticClass: "text-danger" },
                                                [_vm._v(_vm._s(errors[0]))]
                                              ),
                                            ],
                                            1
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
                            _c(
                              "b-col",
                              { attrs: { cols: "3" } },
                              [
                                _c("validation-provider", {
                                  attrs: { name: "Data dosar", rules: "" },
                                  scopedSlots: _vm._u([
                                    {
                                      key: "default",
                                      fn: function (ref) {
                                        var errors = ref.errors
                                        return [
                                          _c("datacalendaristica", {
                                            directives: [
                                              {
                                                name: "show",
                                                rawName: "v-show",
                                                value:
                                                  _vm.activeActionLocal !=
                                                  "Adaugă",
                                                expression:
                                                  "activeActionLocal!='Adaugă'",
                                              },
                                            ],
                                            attrs: {
                                              readonly: true,
                                              id: "data_dosar",
                                              name: "data_dosar",
                                              campDisplay: "Data dosar",
                                            },
                                            model: {
                                              value:
                                                _vm.editVarLocal.data_dosar,
                                              callback: function ($$v) {
                                                _vm.$set(
                                                  _vm.editVarLocal,
                                                  "data_dosar",
                                                  $$v
                                                )
                                              },
                                              expression:
                                                "editVarLocal.data_dosar",
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
                            _vm._v(" "),
                            _c(
                              "b-col",
                              { attrs: { cols: "3" } },
                              [
                                _c("validation-provider", {
                                  attrs: { name: "Institutia", rules: "" },
                                  scopedSlots: _vm._u([
                                    {
                                      key: "default",
                                      fn: function (ref) {
                                        var errors = ref.errors
                                        return [
                                          _c(
                                            "div",
                                            { staticClass: "form-label-group" },
                                            [
                                              _c("b-form-input", {
                                                directives: [
                                                  {
                                                    name: "show",
                                                    rawName: "v-show",
                                                    value:
                                                      _vm.activeActionLocal !=
                                                      "Adaugă",
                                                    expression:
                                                      "activeActionLocal!='Adaugă'",
                                                  },
                                                ],
                                                attrs: {
                                                  readonly: true,
                                                  size: "sm",
                                                  autocomplete: "off",
                                                  id: "institutie",
                                                  placeholder: "Institutia",
                                                },
                                                model: {
                                                  value:
                                                    _vm.editVarLocal.institutie,
                                                  callback: function ($$v) {
                                                    _vm.$set(
                                                      _vm.editVarLocal,
                                                      "institutie",
                                                      $$v
                                                    )
                                                  },
                                                  expression:
                                                    "editVarLocal.institutie",
                                                },
                                              }),
                                              _vm._v(" "),
                                              _c(
                                                "label",
                                                {
                                                  directives: [
                                                    {
                                                      name: "show",
                                                      rawName: "v-show",
                                                      value:
                                                        _vm.activeActionLocal !=
                                                        "Adaugă",
                                                      expression:
                                                        "activeActionLocal!='Adaugă'",
                                                    },
                                                  ],
                                                  attrs: { for: "institutie" },
                                                },
                                                [_vm._v("Institutia")]
                                              ),
                                              _vm._v(" "),
                                              _c(
                                                "small",
                                                { staticClass: "text-danger" },
                                                [_vm._v(_vm._s(errors[0]))]
                                              ),
                                            ],
                                            1
                                          ),
                                        ]
                                      },
                                    },
                                  ]),
                                }),
                              ],
                              1
                            ),
                            _vm._v(" "),
                            _c(
                              "b-col",
                              { attrs: { cols: "2" } },
                              [
                                _c("validation-provider", {
                                  attrs: { name: "Departament", rules: "" },
                                  scopedSlots: _vm._u([
                                    {
                                      key: "default",
                                      fn: function (ref) {
                                        var errors = ref.errors
                                        return [
                                          _c(
                                            "div",
                                            { staticClass: "form-label-group" },
                                            [
                                              _c("b-form-input", {
                                                directives: [
                                                  {
                                                    name: "show",
                                                    rawName: "v-show",
                                                    value:
                                                      _vm.activeActionLocal !=
                                                      "Adaugă",
                                                    expression:
                                                      "activeActionLocal!='Adaugă'",
                                                  },
                                                ],
                                                attrs: {
                                                  readonly: true,
                                                  size: "sm",
                                                  autocomplete: "off",
                                                  id: "departament",
                                                  placeholder: "Departament",
                                                },
                                                model: {
                                                  value:
                                                    _vm.editVarLocal
                                                      .departament,
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
                                                "label",
                                                {
                                                  directives: [
                                                    {
                                                      name: "show",
                                                      rawName: "v-show",
                                                      value:
                                                        _vm.activeActionLocal !=
                                                        "Adaugă",
                                                      expression:
                                                        "activeActionLocal!='Adaugă'",
                                                    },
                                                  ],
                                                  attrs: { for: "departament" },
                                                },
                                                [_vm._v("Departament")]
                                              ),
                                              _vm._v(" "),
                                              _c(
                                                "small",
                                                { staticClass: "text-danger" },
                                                [_vm._v(_vm._s(errors[0]))]
                                              ),
                                            ],
                                            1
                                          ),
                                        ]
                                      },
                                    },
                                  ]),
                                }),
                              ],
                              1
                            ),
                            _vm._v(" "),
                            _c(
                              "b-col",
                              { attrs: { cols: "2" } },
                              [
                                _c("validation-provider", {
                                  attrs: { name: "Categorie caz", rules: "" },
                                  scopedSlots: _vm._u([
                                    {
                                      key: "default",
                                      fn: function (ref) {
                                        var errors = ref.errors
                                        return [
                                          _c(
                                            "div",
                                            { staticClass: "form-label-group" },
                                            [
                                              _c("b-form-input", {
                                                directives: [
                                                  {
                                                    name: "show",
                                                    rawName: "v-show",
                                                    value:
                                                      _vm.activeActionLocal !=
                                                      "Adaugă",
                                                    expression:
                                                      "activeActionLocal!='Adaugă'",
                                                  },
                                                ],
                                                attrs: {
                                                  readonly: true,
                                                  size: "sm",
                                                  autocomplete: "off",
                                                  id: "categorie_caz",
                                                  placeholder: "Categorie caz",
                                                },
                                                model: {
                                                  value:
                                                    _vm.editVarLocal
                                                      .categorie_caz,
                                                  callback: function ($$v) {
                                                    _vm.$set(
                                                      _vm.editVarLocal,
                                                      "categorie_caz",
                                                      $$v
                                                    )
                                                  },
                                                  expression:
                                                    "editVarLocal.categorie_caz",
                                                },
                                              }),
                                              _vm._v(" "),
                                              _c(
                                                "label",
                                                {
                                                  directives: [
                                                    {
                                                      name: "show",
                                                      rawName: "v-show",
                                                      value:
                                                        _vm.activeActionLocal !=
                                                        "Adaugă",
                                                      expression:
                                                        "activeActionLocal!='Adaugă'",
                                                    },
                                                  ],
                                                  attrs: {
                                                    for: "categorie_caz",
                                                  },
                                                },
                                                [_vm._v("Categorie caz")]
                                              ),
                                              _vm._v(" "),
                                              _c(
                                                "small",
                                                { staticClass: "text-danger" },
                                                [_vm._v(_vm._s(errors[0]))]
                                              ),
                                            ],
                                            1
                                          ),
                                        ]
                                      },
                                    },
                                  ]),
                                }),
                              ],
                              1
                            ),
                            _vm._v(" "),
                            _c(
                              "b-col",
                              { attrs: { cols: "2" } },
                              [
                                _c("validation-provider", {
                                  attrs: {
                                    name: "Stadiu procesual",
                                    rules: "",
                                  },
                                  scopedSlots: _vm._u([
                                    {
                                      key: "default",
                                      fn: function (ref) {
                                        var errors = ref.errors
                                        return [
                                          _c(
                                            "div",
                                            { staticClass: "form-label-group" },
                                            [
                                              _c("b-form-input", {
                                                directives: [
                                                  {
                                                    name: "show",
                                                    rawName: "v-show",
                                                    value:
                                                      _vm.activeActionLocal !=
                                                      "Adaugă",
                                                    expression:
                                                      "activeActionLocal!='Adaugă'",
                                                  },
                                                ],
                                                attrs: {
                                                  readonly: true,
                                                  size: "sm",
                                                  autocomplete: "off",
                                                  id: "stadiu_procesual",
                                                  placeholder:
                                                    "Stadiu procesual",
                                                },
                                                model: {
                                                  value:
                                                    _vm.editVarLocal
                                                      .stadiu_procesual,
                                                  callback: function ($$v) {
                                                    _vm.$set(
                                                      _vm.editVarLocal,
                                                      "stadiu_procesual",
                                                      $$v
                                                    )
                                                  },
                                                  expression:
                                                    "editVarLocal.stadiu_procesual",
                                                },
                                              }),
                                              _vm._v(" "),
                                              _c(
                                                "label",
                                                {
                                                  directives: [
                                                    {
                                                      name: "show",
                                                      rawName: "v-show",
                                                      value:
                                                        _vm.activeActionLocal !=
                                                        "Adaugă",
                                                      expression:
                                                        "activeActionLocal!='Adaugă'",
                                                    },
                                                  ],
                                                  attrs: {
                                                    for: "stadiu_procesual",
                                                  },
                                                },
                                                [_vm._v("Stadiu procesual")]
                                              ),
                                              _vm._v(" "),
                                              _c(
                                                "small",
                                                { staticClass: "text-danger" },
                                                [_vm._v(_vm._s(errors[0]))]
                                              ),
                                            ],
                                            1
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
                      ],
                      1
                    ),
                    _vm._v(" "),
                    _c(
                      "b-col",
                      { attrs: { cols: "2" } },
                      [
                        _c("validation-provider", {
                          attrs: { name: "email_alerte", rules: "" },
                          scopedSlots: _vm._u([
                            {
                              key: "default",
                              fn: function (ref) {
                                var errors = ref.errors
                                return [
                                  _c(
                                    "div",
                                    { staticClass: "form-label-group" },
                                    [
                                      _c("b-form-textarea", {
                                        attrs: {
                                          id: "observatii",
                                          rows: "3",
                                          placeholder:
                                            "Email pentru alerte (separator ; )",
                                        },
                                        model: {
                                          value: _vm.editVarLocal.email_alerte,
                                          callback: function ($$v) {
                                            _vm.$set(
                                              _vm.editVarLocal,
                                              "email_alerte",
                                              $$v
                                            )
                                          },
                                          expression:
                                            "editVarLocal.email_alerte",
                                        },
                                      }),
                                      _vm._v(" "),
                                      _c(
                                        "label",
                                        {
                                          attrs: { for: "label-email_alerte" },
                                        },
                                        [
                                          _vm._v(
                                            "Email pentru alerte (separator ; )"
                                          ),
                                        ]
                                      ),
                                      _vm._v(" "),
                                      _c(
                                        "small",
                                        { staticClass: "text-danger" },
                                        [_vm._v(_vm._s(errors[0]))]
                                      ),
                                    ],
                                    1
                                  ),
                                ]
                              },
                            },
                          ]),
                        }),
                      ],
                      1
                    ),
                    _vm._v(" "),
                    _c(
                      "b-col",
                      { attrs: { cols: "4" } },
                      [
                        _c("validation-provider", {
                          attrs: { name: "Observatii", rules: "" },
                          scopedSlots: _vm._u([
                            {
                              key: "default",
                              fn: function (ref) {
                                var errors = ref.errors
                                return [
                                  _c(
                                    "div",
                                    { staticClass: "form-label-group" },
                                    [
                                      _c("b-form-textarea", {
                                        attrs: {
                                          id: "observatii",
                                          rows: "3",
                                          placeholder: "Observatii",
                                        },
                                        model: {
                                          value: _vm.editVarLocal.observatii,
                                          callback: function ($$v) {
                                            _vm.$set(
                                              _vm.editVarLocal,
                                              "observatii",
                                              $$v
                                            )
                                          },
                                          expression: "editVarLocal.observatii",
                                        },
                                      }),
                                      _vm._v(" "),
                                      _c(
                                        "label",
                                        { attrs: { for: "label-observatii" } },
                                        [_vm._v("Observatii")]
                                      ),
                                      _vm._v(" "),
                                      _c(
                                        "small",
                                        { staticClass: "text-danger" },
                                        [_vm._v(_vm._s(errors[0]))]
                                      ),
                                    ],
                                    1
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
                    _c(
                      "b-col",
                      { staticClass: "border", attrs: { cols: "7" } },
                      [
                        _c(
                          "h4",
                          {
                            directives: [
                              {
                                name: "show",
                                rawName: "v-show",
                                value: _vm.activeActionLocal != "Adaugă",
                                expression: "activeActionLocal!='Adaugă'",
                              },
                            ],
                          },
                          [_vm._v(" CAI DE ATAC ")]
                        ),
                        _vm._v(" "),
                        _vm._l(
                          _vm.editVarLocal.litigiicaleatac,
                          function (caleatac, key) {
                            return _c(
                              "b-row",
                              {
                                key: caleatac.id,
                                staticClass: "d-flex justify-content-center",
                              },
                              [
                                _c(
                                  "b-col",
                                  { attrs: { cols: "3" } },
                                  [
                                    _c("datacalendaristica", {
                                      directives: [
                                        {
                                          name: "show",
                                          rawName: "v-show",
                                          value:
                                            _vm.activeActionLocal != "Adaugă",
                                          expression:
                                            "activeActionLocal!='Adaugă'",
                                        },
                                      ],
                                      attrs: {
                                        readonly: true,
                                        id: "data_declarare_" + key,
                                        name: "data_declarare_" + key,
                                        campDisplay: "Data declarare",
                                      },
                                      model: {
                                        value: caleatac.data_declarare,
                                        callback: function ($$v) {
                                          _vm.$set(
                                            caleatac,
                                            "data_declarare",
                                            $$v
                                          )
                                        },
                                        expression: "caleatac.data_declarare",
                                      },
                                    }),
                                  ],
                                  1
                                ),
                                _vm._v(" "),
                                _c("b-col", { attrs: { cols: "2" } }, [
                                  _c(
                                    "div",
                                    { staticClass: "form-label-group" },
                                    [
                                      _c("b-form-input", {
                                        directives: [
                                          {
                                            name: "show",
                                            rawName: "v-show",
                                            value:
                                              _vm.activeActionLocal != "Adaugă",
                                            expression:
                                              "activeActionLocal!='Adaugă'",
                                          },
                                        ],
                                        attrs: {
                                          readonly: true,
                                          size: "sm",
                                          autocomplete: "off",
                                          id: "tip_cale_atac_" + key,
                                          placeholder: "Tip cale atac",
                                        },
                                        model: {
                                          value: caleatac.tip_cale_atac,
                                          callback: function ($$v) {
                                            _vm.$set(
                                              caleatac,
                                              "tip_cale_atac",
                                              $$v
                                            )
                                          },
                                          expression: "caleatac.tip_cale_atac",
                                        },
                                      }),
                                      _vm._v(" "),
                                      _c(
                                        "label",
                                        {
                                          directives: [
                                            {
                                              name: "show",
                                              rawName: "v-show",
                                              value:
                                                _vm.activeActionLocal !=
                                                "Adaugă",
                                              expression:
                                                "activeActionLocal!='Adaugă'",
                                            },
                                          ],
                                          attrs: {
                                            for: "tip_cale_atac_" + key,
                                          },
                                        },
                                        [_vm._v("Tip cale atac")]
                                      ),
                                    ],
                                    1
                                  ),
                                ]),
                                _vm._v(" "),
                                _c("b-col", { attrs: { cols: "7" } }, [
                                  _c(
                                    "div",
                                    { staticClass: "form-label-group" },
                                    [
                                      _c("b-form-input", {
                                        directives: [
                                          {
                                            name: "show",
                                            rawName: "v-show",
                                            value:
                                              _vm.activeActionLocal != "Adaugă",
                                            expression:
                                              "activeActionLocal!='Adaugă'",
                                          },
                                        ],
                                        attrs: {
                                          readonly: true,
                                          size: "sm",
                                          autocomplete: "off",
                                          id: "parte_declaratoare_" + key,
                                          placeholder: "Parte declaratoare",
                                        },
                                        model: {
                                          value: caleatac.parte_declaratoare,
                                          callback: function ($$v) {
                                            _vm.$set(
                                              caleatac,
                                              "parte_declaratoare",
                                              $$v
                                            )
                                          },
                                          expression:
                                            "caleatac.parte_declaratoare",
                                        },
                                      }),
                                      _vm._v(" "),
                                      _c(
                                        "label",
                                        {
                                          directives: [
                                            {
                                              name: "show",
                                              rawName: "v-show",
                                              value:
                                                _vm.activeActionLocal !=
                                                "Adaugă",
                                              expression:
                                                "activeActionLocal!='Adaugă'",
                                            },
                                          ],
                                          attrs: {
                                            for: "parte_declaratoare_" + key,
                                          },
                                        },
                                        [_vm._v("Parte declaratoare")]
                                      ),
                                    ],
                                    1
                                  ),
                                ]),
                              ],
                              1
                            )
                          }
                        ),
                      ],
                      2
                    ),
                    _vm._v(" "),
                    _c(
                      "b-col",
                      { staticClass: "border", attrs: { cols: "5" } },
                      [
                        _c("h4", [_vm._v("PARTI DOSAR")]),
                        _vm._v(" "),
                        _c("b-form-textarea", {
                          attrs: {
                            readonly: true,
                            id: "parti",
                            rows: "6",
                            placeholder: "Parti",
                          },
                          model: {
                            value: _vm.editVarLocal.parti,
                            callback: function ($$v) {
                              _vm.$set(_vm.editVarLocal, "parti", $$v)
                            },
                            expression: "editVarLocal.parti",
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
                  "h4",
                  {
                    directives: [
                      {
                        name: "show",
                        rawName: "v-show",
                        value: _vm.activeActionLocal != "Adaugă",
                        expression: "activeActionLocal!='Adaugă'",
                      },
                    ],
                  },
                  [_vm._v("SEDINTE ")]
                ),
                _vm._v(" "),
                _vm._l(
                  _vm.editVarLocal.litigiisedinte,
                  function (sedinta, key) {
                    return _c(
                      "b-row",
                      {
                        key: sedinta.id,
                        staticClass: " border d-flex justify-content-center",
                      },
                      [
                        _c(
                          "b-col",
                          { attrs: { cols: "12" } },
                          [
                            _c("br"),
                            _vm._v(" "),
                            _c(
                              "b-row",
                              { staticClass: "d-flex justify-content-center" },
                              [
                                _c(
                                  "b-col",
                                  { attrs: { cols: "1" } },
                                  [
                                    _c("datacalendaristica", {
                                      directives: [
                                        {
                                          name: "show",
                                          rawName: "v-show",
                                          value:
                                            _vm.activeActionLocal != "Adaugă",
                                          expression:
                                            "activeActionLocal!='Adaugă'",
                                        },
                                      ],
                                      attrs: {
                                        readonly: true,
                                        id: "data_sedinta_" + key,
                                        name: "data_sedinta",
                                        campDisplay: "Data sedinta",
                                      },
                                      model: {
                                        value: sedinta.data_sedinta,
                                        callback: function ($$v) {
                                          _vm.$set(sedinta, "data_sedinta", $$v)
                                        },
                                        expression: "sedinta.data_sedinta",
                                      },
                                    }),
                                  ],
                                  1
                                ),
                                _vm._v(" "),
                                _c("b-col", { attrs: { cols: "1" } }, [
                                  _c(
                                    "div",
                                    { staticClass: "form-label-group" },
                                    [
                                      _c("b-form-input", {
                                        directives: [
                                          {
                                            name: "show",
                                            rawName: "v-show",
                                            value:
                                              _vm.activeActionLocal != "Adaugă",
                                            expression:
                                              "activeActionLocal!='Adaugă'",
                                          },
                                        ],
                                        attrs: {
                                          readonly: true,
                                          size: "sm",
                                          autocomplete: "off",
                                          id: "ora_sedinta_" + key,
                                          placeholder: "Ora sedinta",
                                        },
                                        model: {
                                          value: sedinta.ora_sedinta,
                                          callback: function ($$v) {
                                            _vm.$set(
                                              sedinta,
                                              "ora_sedinta",
                                              $$v
                                            )
                                          },
                                          expression: "sedinta.ora_sedinta",
                                        },
                                      }),
                                      _vm._v(" "),
                                      _c(
                                        "label",
                                        {
                                          directives: [
                                            {
                                              name: "show",
                                              rawName: "v-show",
                                              value:
                                                _vm.activeActionLocal !=
                                                "Adaugă",
                                              expression:
                                                "activeActionLocal!='Adaugă'",
                                            },
                                          ],
                                          attrs: { for: "ora_sedinta_" + key },
                                        },
                                        [_vm._v("Ora sedinta")]
                                      ),
                                    ],
                                    1
                                  ),
                                ]),
                                _vm._v(" "),
                                _c(
                                  "b-col",
                                  { attrs: { cols: "1" } },
                                  [
                                    _c("datacalendaristica", {
                                      directives: [
                                        {
                                          name: "show",
                                          rawName: "v-show",
                                          value:
                                            _vm.activeActionLocal != "Adaugă",
                                          expression:
                                            "activeActionLocal!='Adaugă'",
                                        },
                                      ],
                                      attrs: {
                                        readonly: true,
                                        id: "data_pronuntare_" + key,
                                        name: "data_pronuntare",
                                        campDisplay: "Data pronuntare",
                                      },
                                      model: {
                                        value: sedinta.data_pronuntare,
                                        callback: function ($$v) {
                                          _vm.$set(
                                            sedinta,
                                            "data_pronuntare",
                                            $$v
                                          )
                                        },
                                        expression: "sedinta.data_pronuntare",
                                      },
                                    }),
                                  ],
                                  1
                                ),
                                _vm._v(" "),
                                _c("b-col", { attrs: { cols: "1" } }, [
                                  _c(
                                    "div",
                                    { staticClass: "form-label-group" },
                                    [
                                      _c("b-form-input", {
                                        directives: [
                                          {
                                            name: "show",
                                            rawName: "v-show",
                                            value:
                                              _vm.activeActionLocal != "Adaugă",
                                            expression:
                                              "activeActionLocal!='Adaugă'",
                                          },
                                        ],
                                        attrs: {
                                          readonly: true,
                                          size: "sm",
                                          autocomplete: "off",
                                          id: "complet_" + key,
                                          placeholder: "Complet",
                                        },
                                        model: {
                                          value: sedinta.complet,
                                          callback: function ($$v) {
                                            _vm.$set(sedinta, "complet", $$v)
                                          },
                                          expression: "sedinta.complet",
                                        },
                                      }),
                                      _vm._v(" "),
                                      _c(
                                        "label",
                                        {
                                          directives: [
                                            {
                                              name: "show",
                                              rawName: "v-show",
                                              value:
                                                _vm.activeActionLocal !=
                                                "Adaugă",
                                              expression:
                                                "activeActionLocal!='Adaugă'",
                                            },
                                          ],
                                          attrs: { for: "complet_" + key },
                                        },
                                        [_vm._v("Complet")]
                                      ),
                                    ],
                                    1
                                  ),
                                ]),
                                _vm._v(" "),
                                _c("b-col", { attrs: { cols: "2" } }, [
                                  _c(
                                    "div",
                                    { staticClass: "form-label-group" },
                                    [
                                      _c("b-form-input", {
                                        directives: [
                                          {
                                            name: "show",
                                            rawName: "v-show",
                                            value:
                                              _vm.activeActionLocal != "Adaugă",
                                            expression:
                                              "activeActionLocal!='Adaugă'",
                                          },
                                        ],
                                        attrs: {
                                          readonly: true,
                                          size: "sm",
                                          autocomplete: "off",
                                          id: "document_sedinta_" + key,
                                          placeholder: "Document sedinta",
                                        },
                                        model: {
                                          value: sedinta.document_sedinta,
                                          callback: function ($$v) {
                                            _vm.$set(
                                              sedinta,
                                              "document_sedinta",
                                              $$v
                                            )
                                          },
                                          expression:
                                            "sedinta.document_sedinta",
                                        },
                                      }),
                                      _vm._v(" "),
                                      _c(
                                        "label",
                                        {
                                          directives: [
                                            {
                                              name: "show",
                                              rawName: "v-show",
                                              value:
                                                _vm.activeActionLocal !=
                                                "Adaugă",
                                              expression:
                                                "activeActionLocal!='Adaugă'",
                                            },
                                          ],
                                          attrs: {
                                            for: "document_sedinta_" + key,
                                          },
                                        },
                                        [_vm._v("Document sedinta")]
                                      ),
                                    ],
                                    1
                                  ),
                                ]),
                                _vm._v(" "),
                                _c("b-col", { attrs: { cols: "2" } }, [
                                  _c(
                                    "div",
                                    { staticClass: "form-label-group" },
                                    [
                                      _c("b-form-input", {
                                        directives: [
                                          {
                                            name: "show",
                                            rawName: "v-show",
                                            value:
                                              _vm.activeActionLocal != "Adaugă",
                                            expression:
                                              "activeActionLocal!='Adaugă'",
                                          },
                                        ],
                                        attrs: {
                                          readonly: true,
                                          size: "sm",
                                          autocomplete: "off",
                                          id: "numar_document_" + key,
                                          placeholder: "Numar document",
                                        },
                                        model: {
                                          value: sedinta.numar_document,
                                          callback: function ($$v) {
                                            _vm.$set(
                                              sedinta,
                                              "numar_document",
                                              $$v
                                            )
                                          },
                                          expression: "sedinta.numar_document",
                                        },
                                      }),
                                      _vm._v(" "),
                                      _c(
                                        "label",
                                        {
                                          directives: [
                                            {
                                              name: "show",
                                              rawName: "v-show",
                                              value:
                                                _vm.activeActionLocal !=
                                                "Adaugă",
                                              expression:
                                                "activeActionLocal!='Adaugă'",
                                            },
                                          ],
                                          attrs: {
                                            for: "numar_document_" + key,
                                          },
                                        },
                                        [_vm._v("Numar document")]
                                      ),
                                    ],
                                    1
                                  ),
                                ]),
                                _vm._v(" "),
                                _c(
                                  "b-col",
                                  { attrs: { cols: "1" } },
                                  [
                                    _c("datacalendaristica", {
                                      directives: [
                                        {
                                          name: "show",
                                          rawName: "v-show",
                                          value:
                                            _vm.activeActionLocal != "Adaugă",
                                          expression:
                                            "activeActionLocal!='Adaugă'",
                                        },
                                      ],
                                      attrs: {
                                        readonly: true,
                                        id: "data_document_" + key,
                                        name: "data_document",
                                        campDisplay: "Data document",
                                      },
                                      model: {
                                        value: sedinta.data_document,
                                        callback: function ($$v) {
                                          _vm.$set(
                                            sedinta,
                                            "data_document",
                                            $$v
                                          )
                                        },
                                        expression: "sedinta.data_document",
                                      },
                                    }),
                                  ],
                                  1
                                ),
                                _vm._v(" "),
                                _c("b-col", { attrs: { cols: "3" } }, [
                                  _c(
                                    "div",
                                    { staticClass: "form-label-group" },
                                    [
                                      _c("b-form-input", {
                                        directives: [
                                          {
                                            name: "show",
                                            rawName: "v-show",
                                            value:
                                              _vm.activeActionLocal != "Adaugă",
                                            expression:
                                              "activeActionLocal!='Adaugă'",
                                          },
                                        ],
                                        attrs: {
                                          readonly: true,
                                          size: "sm",
                                          autocomplete: "off",
                                          id: "solutie_" + key,
                                          placeholder: "Solutie",
                                        },
                                        model: {
                                          value: sedinta.solutie,
                                          callback: function ($$v) {
                                            _vm.$set(sedinta, "solutie", $$v)
                                          },
                                          expression: "sedinta.solutie",
                                        },
                                      }),
                                      _vm._v(" "),
                                      _c(
                                        "label",
                                        {
                                          directives: [
                                            {
                                              name: "show",
                                              rawName: "v-show",
                                              value:
                                                _vm.activeActionLocal !=
                                                "Adaugă",
                                              expression:
                                                "activeActionLocal!='Adaugă'",
                                            },
                                          ],
                                          attrs: { for: "solutie_" + key },
                                        },
                                        [_vm._v("Solutie")]
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
                                  { attrs: { cols: "12" } },
                                  [
                                    _c(
                                      "p",
                                      {
                                        directives: [
                                          {
                                            name: "show",
                                            rawName: "v-show",
                                            value:
                                              _vm.activeActionLocal != "Adaugă",
                                            expression:
                                              "activeActionLocal!='Adaugă'",
                                          },
                                        ],
                                      },
                                      [_vm._v("Solutie sumar ")]
                                    ),
                                    _vm._v(" "),
                                    _c("b-form-textarea", {
                                      directives: [
                                        {
                                          name: "show",
                                          rawName: "v-show",
                                          value:
                                            _vm.activeActionLocal != "Adaugă",
                                          expression:
                                            "activeActionLocal!='Adaugă'",
                                        },
                                      ],
                                      attrs: {
                                        id: "solutie_sumar_" + key,
                                        readonly: true,
                                        rows: "8",
                                        placeholder: "Solutie sumar",
                                      },
                                      model: {
                                        value: sedinta.solutie_sumar,
                                        callback: function ($$v) {
                                          _vm.$set(
                                            sedinta,
                                            "solutie_sumar",
                                            $$v
                                          )
                                        },
                                        expression: "sedinta.solutie_sumar",
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
                    )
                  }
                ),
                _vm._v(" "),
                _c("br"),
                _c("br"),
                _c("br"),
              ],
              2
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

/***/ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/app_pages/rapoarte/SituatieLitigii.vue?vue&type=template&id=18e48403&":
/*!************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/src/views/app_pages/rapoarte/SituatieLitigii.vue?vue&type=template&id=18e48403& ***!
  \************************************************************************************************************************************************************************************************************************************/
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
    "SablonSituatieData",
    {
      attrs: {
        afisezSSD: _vm.afisezSLLocal,
        size: "lg",
        titlu: "Situatie litigii",
        requestUrl: "situatielitigii",
        rutainapoi: "litigii",
        numefis: "Situatie_litigii",
        editVar: _vm.editVarLocal,
      },
      on: { closed: _vm.afisezSLClosed },
    },
    [_c("br"), _c("br")]
  )
}
var staticRenderFns = []
render._withStripped = true



/***/ }),

/***/ "./resources/js/src/views/app_pages/Litigiu.vue":
/*!******************************************************!*\
  !*** ./resources/js/src/views/app_pages/Litigiu.vue ***!
  \******************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _Litigiu_vue_vue_type_template_id_ba06d984___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./Litigiu.vue?vue&type=template&id=ba06d984& */ "./resources/js/src/views/app_pages/Litigiu.vue?vue&type=template&id=ba06d984&");
/* harmony import */ var _Litigiu_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./Litigiu.vue?vue&type=script&lang=js& */ "./resources/js/src/views/app_pages/Litigiu.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport *//* harmony import */ var _node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../../../../node_modules/vue-loader/lib/runtime/componentNormalizer.js */ "./node_modules/vue-loader/lib/runtime/componentNormalizer.js");





/* normalize component */

var component = Object(_node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__["default"])(
  _Litigiu_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__["default"],
  _Litigiu_vue_vue_type_template_id_ba06d984___WEBPACK_IMPORTED_MODULE_0__["render"],
  _Litigiu_vue_vue_type_template_id_ba06d984___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"],
  false,
  null,
  null,
  null
  
)

/* hot reload */
if (false) { var api; }
component.options.__file = "resources/js/src/views/app_pages/Litigiu.vue"
/* harmony default export */ __webpack_exports__["default"] = (component.exports);

/***/ }),

/***/ "./resources/js/src/views/app_pages/Litigiu.vue?vue&type=script&lang=js&":
/*!*******************************************************************************!*\
  !*** ./resources/js/src/views/app_pages/Litigiu.vue?vue&type=script&lang=js& ***!
  \*******************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_Litigiu_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../node_modules/babel-loader/lib??ref--4-0!../../../../../node_modules/vue-loader/lib??vue-loader-options!./Litigiu.vue?vue&type=script&lang=js& */ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/app_pages/Litigiu.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__["default"] = (_node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_Litigiu_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__["default"]); 

/***/ }),

/***/ "./resources/js/src/views/app_pages/Litigiu.vue?vue&type=template&id=ba06d984&":
/*!*************************************************************************************!*\
  !*** ./resources/js/src/views/app_pages/Litigiu.vue?vue&type=template&id=ba06d984& ***!
  \*************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_Litigiu_vue_vue_type_template_id_ba06d984___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!../../../../../node_modules/vue-loader/lib??vue-loader-options!./Litigiu.vue?vue&type=template&id=ba06d984& */ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/app_pages/Litigiu.vue?vue&type=template&id=ba06d984&");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "render", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_Litigiu_vue_vue_type_template_id_ba06d984___WEBPACK_IMPORTED_MODULE_0__["render"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "staticRenderFns", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_Litigiu_vue_vue_type_template_id_ba06d984___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"]; });



/***/ }),

/***/ "./resources/js/src/views/app_pages/Litigiuaev.vue":
/*!*********************************************************!*\
  !*** ./resources/js/src/views/app_pages/Litigiuaev.vue ***!
  \*********************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _Litigiuaev_vue_vue_type_template_id_5b450804___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./Litigiuaev.vue?vue&type=template&id=5b450804& */ "./resources/js/src/views/app_pages/Litigiuaev.vue?vue&type=template&id=5b450804&");
/* harmony import */ var _Litigiuaev_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./Litigiuaev.vue?vue&type=script&lang=js& */ "./resources/js/src/views/app_pages/Litigiuaev.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport *//* harmony import */ var _node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../../../../node_modules/vue-loader/lib/runtime/componentNormalizer.js */ "./node_modules/vue-loader/lib/runtime/componentNormalizer.js");





/* normalize component */

var component = Object(_node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__["default"])(
  _Litigiuaev_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__["default"],
  _Litigiuaev_vue_vue_type_template_id_5b450804___WEBPACK_IMPORTED_MODULE_0__["render"],
  _Litigiuaev_vue_vue_type_template_id_5b450804___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"],
  false,
  null,
  null,
  null
  
)

/* hot reload */
if (false) { var api; }
component.options.__file = "resources/js/src/views/app_pages/Litigiuaev.vue"
/* harmony default export */ __webpack_exports__["default"] = (component.exports);

/***/ }),

/***/ "./resources/js/src/views/app_pages/Litigiuaev.vue?vue&type=script&lang=js&":
/*!**********************************************************************************!*\
  !*** ./resources/js/src/views/app_pages/Litigiuaev.vue?vue&type=script&lang=js& ***!
  \**********************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_Litigiuaev_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../node_modules/babel-loader/lib??ref--4-0!../../../../../node_modules/vue-loader/lib??vue-loader-options!./Litigiuaev.vue?vue&type=script&lang=js& */ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/app_pages/Litigiuaev.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__["default"] = (_node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_Litigiuaev_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__["default"]); 

/***/ }),

/***/ "./resources/js/src/views/app_pages/Litigiuaev.vue?vue&type=template&id=5b450804&":
/*!****************************************************************************************!*\
  !*** ./resources/js/src/views/app_pages/Litigiuaev.vue?vue&type=template&id=5b450804& ***!
  \****************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_Litigiuaev_vue_vue_type_template_id_5b450804___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!../../../../../node_modules/vue-loader/lib??vue-loader-options!./Litigiuaev.vue?vue&type=template&id=5b450804& */ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/app_pages/Litigiuaev.vue?vue&type=template&id=5b450804&");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "render", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_Litigiuaev_vue_vue_type_template_id_5b450804___WEBPACK_IMPORTED_MODULE_0__["render"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "staticRenderFns", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_Litigiuaev_vue_vue_type_template_id_5b450804___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"]; });



/***/ }),

/***/ "./resources/js/src/views/app_pages/rapoarte/SituatieLitigii.vue":
/*!***********************************************************************!*\
  !*** ./resources/js/src/views/app_pages/rapoarte/SituatieLitigii.vue ***!
  \***********************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _SituatieLitigii_vue_vue_type_template_id_18e48403___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./SituatieLitigii.vue?vue&type=template&id=18e48403& */ "./resources/js/src/views/app_pages/rapoarte/SituatieLitigii.vue?vue&type=template&id=18e48403&");
/* harmony import */ var _SituatieLitigii_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./SituatieLitigii.vue?vue&type=script&lang=js& */ "./resources/js/src/views/app_pages/rapoarte/SituatieLitigii.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport *//* harmony import */ var _node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../../../../../node_modules/vue-loader/lib/runtime/componentNormalizer.js */ "./node_modules/vue-loader/lib/runtime/componentNormalizer.js");





/* normalize component */

var component = Object(_node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__["default"])(
  _SituatieLitigii_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__["default"],
  _SituatieLitigii_vue_vue_type_template_id_18e48403___WEBPACK_IMPORTED_MODULE_0__["render"],
  _SituatieLitigii_vue_vue_type_template_id_18e48403___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"],
  false,
  null,
  null,
  null
  
)

/* hot reload */
if (false) { var api; }
component.options.__file = "resources/js/src/views/app_pages/rapoarte/SituatieLitigii.vue"
/* harmony default export */ __webpack_exports__["default"] = (component.exports);

/***/ }),

/***/ "./resources/js/src/views/app_pages/rapoarte/SituatieLitigii.vue?vue&type=script&lang=js&":
/*!************************************************************************************************!*\
  !*** ./resources/js/src/views/app_pages/rapoarte/SituatieLitigii.vue?vue&type=script&lang=js& ***!
  \************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_SituatieLitigii_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../../node_modules/babel-loader/lib??ref--4-0!../../../../../../node_modules/vue-loader/lib??vue-loader-options!./SituatieLitigii.vue?vue&type=script&lang=js& */ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/app_pages/rapoarte/SituatieLitigii.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__["default"] = (_node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_SituatieLitigii_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__["default"]); 

/***/ }),

/***/ "./resources/js/src/views/app_pages/rapoarte/SituatieLitigii.vue?vue&type=template&id=18e48403&":
/*!******************************************************************************************************!*\
  !*** ./resources/js/src/views/app_pages/rapoarte/SituatieLitigii.vue?vue&type=template&id=18e48403& ***!
  \******************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_SituatieLitigii_vue_vue_type_template_id_18e48403___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../../node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!../../../../../../node_modules/vue-loader/lib??vue-loader-options!./SituatieLitigii.vue?vue&type=template&id=18e48403& */ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/app_pages/rapoarte/SituatieLitigii.vue?vue&type=template&id=18e48403&");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "render", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_SituatieLitigii_vue_vue_type_template_id_18e48403___WEBPACK_IMPORTED_MODULE_0__["render"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "staticRenderFns", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_SituatieLitigii_vue_vue_type_template_id_18e48403___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"]; });



/***/ })

}]);