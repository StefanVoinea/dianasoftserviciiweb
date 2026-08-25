(window["webpackJsonp"] = window["webpackJsonp"] || []).push([[140],{

/***/ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/app_pages/Company.vue?vue&type=script&lang=js&":
/*!***************************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib??ref--4-0!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/src/views/app_pages/Company.vue?vue&type=script&lang=js& ***!
  \***************************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _Companyaev_vue__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./Companyaev.vue */ "./resources/js/src/views/app_pages/Companyaev.vue");
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
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
    Companyaev: _Companyaev_vue__WEBPACK_IMPORTED_MODULE_0__["default"]
  },
  name: "company",
  data: function data() {
    return {
      refreshLocal: false,
      idselectat: null,
      campFiltruStart: "",
      modelName: "company",
      modelDisplayName: "Societăți",
      editVar: {
        denumire: "",
        cui: "",
        regcom: "",
        adresa: "",
        localitate: "",
        judet: "",
        telefon: "",
        email: "",
        capital_social: "",
        banca: "",
        cont: "",
        plan_tarifar: "",
        cod_caen: "",
        email_factura: "",
        email_restante: "",
        slug: "",
        operator_gdpr: "",
        nrautorizatie: "",
        cerber_url: ""
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
        label: "Denumire",
        field: "denumire",
        width: "300px",
        type: "text",
        showSortAsc: true
      }, {
        label: "CUI",
        field: "cui",
        width: "300px",
        type: "text",
        showSortAsc: true
      }, {
        label: "Registrul comertului",
        field: "regcom",
        width: "300px",
        type: "text",
        showSortAsc: true
      }, {
        label: "Adresa",
        field: "adresa",
        width: "300px",
        type: "text",
        showSortAsc: true
      }, {
        label: "Localitate",
        field: "localitate",
        width: "300px",
        type: "text",
        showSortAsc: true
      }, {
        label: "Judet",
        field: "judet",
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
        label: "E-mail",
        field: "email",
        width: "300px",
        type: "text",
        showSortAsc: true
      }, {
        label: "Capital social",
        field: "capital_social",
        width: "300px",
        type: "text",
        showSortAsc: true
      }, {
        label: "Banca",
        field: "banca",
        width: "300px",
        type: "text",
        showSortAsc: true
      }, {
        label: "Cont",
        field: "cont",
        width: "300px",
        type: "text",
        showSortAsc: true
      }, {
        label: "Plan tarifar",
        field: "plan_tarifar",
        width: "300px",
        type: "text",
        showSortAsc: true
      }, {
        label: "Cod caen",
        field: "cod_caen",
        width: "300px",
        type: "text",
        showSortAsc: true
      }, {
        label: "Email factura",
        field: "email_factura",
        width: "300px",
        type: "text",
        showSortAsc: true
      }, {
        label: "Email restante",
        field: "email_restante",
        width: "300px",
        type: "text",
        showSortAsc: true
      }, {
        label: "Prescurtare denumire",
        field: "slug",
        width: "300px",
        type: "text",
        showSortAsc: true
      }, {
        label: "Operator GDPR",
        field: "operator_gdpr",
        width: "300px",
        type: "text",
        showSortAsc: true
      }, {
        label: "Nr autorizatie",
        field: "nrautorizatie",
        width: "300px",
        type: "text",
        showSortAsc: true
      }, {
        label: "Cerber URL",
        field: "cerber_url",
        width: "300px",
        type: "text",
        showSortAsc: true
      }]
    };
  },
  methods: {
    aevClosed: function aevClosed() {
      this.activeEdit = false;
      this.editVar = {
        denumire: "",
        cui: "",
        regcom: "",
        adresa: "",
        localitate: "",
        judet: "",
        telefon: "",
        email: "",
        capital_social: "",
        banca: "",
        cont: "",
        plan_tarifar: "",
        cod_caen: "",
        email_factura: "",
        email_restante: "",
        slug: "",
        operator_gdpr: "",
        nrautorizatie: "",
        cerber_url: ""
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
        denumire: "",
        cui: "",
        regcom: "",
        adresa: "",
        localitate: "",
        judet: "",
        telefon: "",
        email: "",
        capital_social: "",
        banca: "",
        cont: "",
        plan_tarifar: "",
        cod_caen: "",
        email_factura: "",
        email_restante: "",
        slug: "",
        operator_gdpr: "",
        nrautorizatie: "",
        cerber_url: ""
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

/***/ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/app_pages/Companyaev.vue?vue&type=script&lang=js&":
/*!******************************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib??ref--4-0!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/src/views/app_pages/Companyaev.vue?vue&type=script&lang=js& ***!
  \******************************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var C_dianasoft_serviciiweb_web_node_modules_babel_runtime_helpers_esm_defineProperty_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./node_modules/@babel/runtime/helpers/esm/defineProperty.js */ "./node_modules/@babel/runtime/helpers/esm/defineProperty.js");
/* harmony import */ var core_js_modules_es_object_to_string_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! core-js/modules/es.object.to-string.js */ "./node_modules/core-js/modules/es.object.to-string.js");
/* harmony import */ var core_js_modules_es_object_to_string_js__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_es_object_to_string_js__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var core_js_modules_es_regexp_to_string_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! core-js/modules/es.regexp.to-string.js */ "./node_modules/core-js/modules/es.regexp.to-string.js");
/* harmony import */ var core_js_modules_es_regexp_to_string_js__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_es_regexp_to_string_js__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var vue_ripple_directive__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! vue-ripple-directive */ "./node_modules/vue-ripple-directive/src/ripple.js");
/* harmony import */ var _core_mixins_ui_transition__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! @core/mixins/ui/transition */ "./resources/js/src/@core/mixins/ui/transition.js");
/* harmony import */ var bootstrap_vue__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! bootstrap-vue */ "./node_modules/bootstrap-vue/esm/index.js");



//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
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
  mixins: [_core_mixins_ui_transition__WEBPACK_IMPORTED_MODULE_4__["heightTransition"]],
  components: {},
  directives: {
    "b-modal": bootstrap_vue__WEBPACK_IMPORTED_MODULE_5__["VBModal"],
    Ripple: vue_ripple_directive__WEBPACK_IMPORTED_MODULE_3__["default"]
  },
  name: "companyaev",
  data: function data() {
    return {
      rutainapoiLocal: this.rutainapoi,
      activeEditLocal: false,
      activeActionLocal: this.activeAction,
      editVarLocal: this.editVar,
      nextTodoId: 2,
      modelName: "company",
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
    initTrHeight: function initTrHeight() {
      var _this = this;

      this.trSetHeight(null);
      this.$nextTick(function () {
        if (_this.$refs.form) {
          _this.trSetHeight(_this.$refs.form.scrollHeight);
        }
      });
    },
    handleOk: function handleOk() {
      if (this.activeActionLocal == "Adaugă") {
        this.saveAdd();
      }

      if (this.activeActionLocal == "Modifică") {
        this.saveEdit();
      }
    },
    saveAdd: function saveAdd() {
      var _this2 = this;

      this.showLoading = true;
      var payLoad = this.editVarLocal;
      payLoad.requestType = "post";
      payLoad.requestUrl = "/" + this.modelName + "/store";
      this.activeEditLocal = false;
      this.activeActionLocal = "";
      this.$store.dispatch("app/api_Request", payLoad).then(function (response) {
        // this.row=response  
        _this2.idselectat = response.id.toString();

        _this2.$emit("stored", response);

        _this2.editVarLocal = {
          denumire: '',
          cui: '',
          regcom: '',
          adresa: '',
          localitate: '',
          judet: '',
          telefon: '',
          email: '',
          capital_social: '',
          banca: '',
          cont: '',
          plan_tarifar: '',
          cod_caen: '',
          email_factura: '',
          email_restante: '',
          slug: '',
          operator_gdpr: '',
          nrautorizatie: '',
          cerber_url: ''
        };
        _this2.showLoading = false;

        _this2.$bvToast.toast("Salvare efectuata cu success!", {
          title: "Salvare cu succes! ",
          variant: "success",
          solid: false,
          appendToast: true,
          autoHideDelay: 3000,
          toaster: "b-toaster-bottom-right"
        });

        _this2.$emit("closed");
      })["catch"](function (error) {
        _this2.showLoading = true;
      });
    },
    aevClosed: function aevClosed() {
      this.activeEditLocal = false;
      this.editVarLocal = {
        denumire: "",
        cui: "",
        regcom: "",
        adresa: "",
        localitate: "",
        judet: "",
        telefon: "",
        email: "",
        capital_social: "",
        banca: "",
        cont: "",
        plan_tarifar: "",
        cod_caen: "",
        email_factura: "",
        email_restante: "",
        slug: "",
        operator_gdpr: "",
        nrautorizatie: "",
        cerber_url: ""
      };
      this.activeActionLocal = "";
      this.$emit("closed");
    },
    saveEdit: function saveEdit() {
      var _this3 = this;

      this.showLoading = true;
      var payLoad = this.editVarLocal;
      payLoad.requestType = "post";
      payLoad.requestUrl = "/" + this.modelName + "/edit/" + this.editVarLocal.id;
      this.$store.dispatch("app/api_Request", payLoad).then(function (response) {
        _this3.selectedID = ""; // this.tblRecords=response

        _this3.idselectat = response.id.toString();

        _this3.$emit("stored", response);

        _this3.activeEditLocal = false;
        _this3.activeActionLocal = "";
        _this3.editVarLocal = {
          denumire: "",
          cui: "",
          regcom: "",
          adresa: "",
          localitate: "",
          judet: "",
          telefon: "",
          email: "",
          capital_social: "",
          banca: "",
          cont: "",
          plan_tarifar: "",
          cod_caen: "",
          email_factura: "",
          email_restante: "",
          slug: "",
          operator_gdpr: "",
          nrautorizatie: "",
          cerber_url: ""
        };
        _this3.showLoading = false;

        _this3.$bvToast.toast("Modificare efectuata cu success!", {
          title: "Modificare cu succes! ",
          variant: "success",
          solid: false,
          appendToast: true,
          autoHideDelay: 3000,
          toaster: "b-toaster-bottom-right"
        });

        _this3.$emit("closed");
      })["catch"](function (error) {
        _this3.showLoading = false;
      });
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

/***/ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/app_pages/Company.vue?vue&type=template&id=9561504c&":
/*!*******************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/src/views/app_pages/Company.vue?vue&type=template&id=9561504c& ***!
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
                  _c("tabelcomponent", {
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
      _c("Companyaev", {
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
    ],
    1
  )
}
var staticRenderFns = []
render._withStripped = true



/***/ }),

/***/ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/app_pages/Companyaev.vue?vue&type=template&id=addaa430&":
/*!**********************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/src/views/app_pages/Companyaev.vue?vue&type=template&id=addaa430& ***!
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
  return _c(
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
            "ok-variant": "success",
            "cancel-title": "Cancel",
            "ok-title": "Save",
            "cancel-variant": "warning",
            scrollable: "",
            "modal-class": "modal-success",
            title: _vm.activeActionLocal + " Societăți",
          },
          on: { ok: _vm.handleOk, cancel: _vm.aevClosed },
          model: {
            value: _vm.activeEditLocal,
            callback: function ($$v) {
              _vm.activeEditLocal = $$v
            },
            expression: "activeEditLocal",
          },
        },
        [
          _c("br"),
          _vm._v(" "),
          _c(
            "b-row",
            [
              _c("b-col", { attrs: { cols: "2" } }, [
                _c(
                  "div",
                  { staticClass: "form-label-group" },
                  [
                    _c("b-form-input", {
                      attrs: {
                        readonly: _vm.activeActionLocal == "Vizualizează",
                        id: "denumire",
                        placeholder: "Denumire",
                      },
                      model: {
                        value: _vm.editVarLocal.denumire,
                        callback: function ($$v) {
                          _vm.$set(_vm.editVarLocal, "denumire", $$v)
                        },
                        expression: "editVarLocal.denumire",
                      },
                    }),
                    _vm._v(" "),
                    _c("label", { attrs: { for: "denumire" } }, [
                      _vm._v("Denumire"),
                    ]),
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
                      attrs: {
                        readonly: _vm.activeActionLocal == "Vizualizează",
                        id: "cui",
                        placeholder: "CUI",
                      },
                      model: {
                        value: _vm.editVarLocal.cui,
                        callback: function ($$v) {
                          _vm.$set(_vm.editVarLocal, "cui", $$v)
                        },
                        expression: "editVarLocal.cui",
                      },
                    }),
                    _vm._v(" "),
                    _c("label", { attrs: { for: "cui" } }, [_vm._v("CUI")]),
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
                      attrs: {
                        readonly: _vm.activeActionLocal == "Vizualizează",
                        id: "regcom",
                        placeholder: "Registrul comertului",
                      },
                      model: {
                        value: _vm.editVarLocal.regcom,
                        callback: function ($$v) {
                          _vm.$set(_vm.editVarLocal, "regcom", $$v)
                        },
                        expression: "editVarLocal.regcom",
                      },
                    }),
                    _vm._v(" "),
                    _c("label", { attrs: { for: "regcom" } }, [
                      _vm._v("Registrul comertului"),
                    ]),
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
                      attrs: {
                        readonly: _vm.activeActionLocal == "Vizualizează",
                        id: "adresa",
                        placeholder: "Adresa",
                      },
                      model: {
                        value: _vm.editVarLocal.adresa,
                        callback: function ($$v) {
                          _vm.$set(_vm.editVarLocal, "adresa", $$v)
                        },
                        expression: "editVarLocal.adresa",
                      },
                    }),
                    _vm._v(" "),
                    _c("label", { attrs: { for: "adresa" } }, [
                      _vm._v("Adresa"),
                    ]),
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
                      attrs: {
                        readonly: _vm.activeActionLocal == "Vizualizează",
                        id: "localitate",
                        placeholder: "Localitate",
                      },
                      model: {
                        value: _vm.editVarLocal.localitate,
                        callback: function ($$v) {
                          _vm.$set(_vm.editVarLocal, "localitate", $$v)
                        },
                        expression: "editVarLocal.localitate",
                      },
                    }),
                    _vm._v(" "),
                    _c("label", { attrs: { for: "localitate" } }, [
                      _vm._v("Localitate"),
                    ]),
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
                      attrs: {
                        readonly: _vm.activeActionLocal == "Vizualizează",
                        id: "judet",
                        placeholder: "Judet",
                      },
                      model: {
                        value: _vm.editVarLocal.judet,
                        callback: function ($$v) {
                          _vm.$set(_vm.editVarLocal, "judet", $$v)
                        },
                        expression: "editVarLocal.judet",
                      },
                    }),
                    _vm._v(" "),
                    _c("label", { attrs: { for: "judet" } }, [_vm._v("Judet")]),
                  ],
                  1
                ),
              ]),
            ],
            1
          ),
          _vm._v(" "),
          _c("br"),
          _vm._v(" "),
          _c(
            "b-row",
            [
              _c("b-col", { attrs: { cols: "2" } }, [
                _c(
                  "div",
                  { staticClass: "form-label-group" },
                  [
                    _c("b-form-input", {
                      attrs: {
                        readonly: _vm.activeActionLocal == "Vizualizează",
                        id: "telefon",
                        placeholder: "Telefon",
                      },
                      model: {
                        value: _vm.editVarLocal.telefon,
                        callback: function ($$v) {
                          _vm.$set(_vm.editVarLocal, "telefon", $$v)
                        },
                        expression: "editVarLocal.telefon",
                      },
                    }),
                    _vm._v(" "),
                    _c("label", { attrs: { for: "telefon" } }, [
                      _vm._v("Telefon"),
                    ]),
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
                      attrs: {
                        readonly: _vm.activeActionLocal == "Vizualizează",
                        id: "email",
                        placeholder: "E-mail",
                      },
                      model: {
                        value: _vm.editVarLocal.email,
                        callback: function ($$v) {
                          _vm.$set(_vm.editVarLocal, "email", $$v)
                        },
                        expression: "editVarLocal.email",
                      },
                    }),
                    _vm._v(" "),
                    _c("label", { attrs: { for: "email" } }, [
                      _vm._v("E-mail"),
                    ]),
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
                      attrs: {
                        readonly: _vm.activeActionLocal == "Vizualizează",
                        id: "capital_social",
                        placeholder: "Capital social",
                      },
                      model: {
                        value: _vm.editVarLocal.capital_social,
                        callback: function ($$v) {
                          _vm.$set(_vm.editVarLocal, "capital_social", $$v)
                        },
                        expression: "editVarLocal.capital_social",
                      },
                    }),
                    _vm._v(" "),
                    _c("label", { attrs: { for: "capital_social" } }, [
                      _vm._v("Capital social"),
                    ]),
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
                      attrs: {
                        readonly: _vm.activeActionLocal == "Vizualizează",
                        id: "banca",
                        placeholder: "Banca",
                      },
                      model: {
                        value: _vm.editVarLocal.banca,
                        callback: function ($$v) {
                          _vm.$set(_vm.editVarLocal, "banca", $$v)
                        },
                        expression: "editVarLocal.banca",
                      },
                    }),
                    _vm._v(" "),
                    _c("label", { attrs: { for: "banca" } }, [_vm._v("Banca")]),
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
                      attrs: {
                        readonly: _vm.activeActionLocal == "Vizualizează",
                        id: "cont",
                        placeholder: "Cont",
                      },
                      model: {
                        value: _vm.editVarLocal.cont,
                        callback: function ($$v) {
                          _vm.$set(_vm.editVarLocal, "cont", $$v)
                        },
                        expression: "editVarLocal.cont",
                      },
                    }),
                    _vm._v(" "),
                    _c("label", { attrs: { for: "cont" } }, [_vm._v("Cont")]),
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
                      attrs: {
                        readonly: _vm.activeActionLocal == "Vizualizează",
                        id: "plan_tarifar",
                        placeholder: "Plan tarifar",
                      },
                      model: {
                        value: _vm.editVarLocal.plan_tarifar,
                        callback: function ($$v) {
                          _vm.$set(_vm.editVarLocal, "plan_tarifar", $$v)
                        },
                        expression: "editVarLocal.plan_tarifar",
                      },
                    }),
                    _vm._v(" "),
                    _c("label", { attrs: { for: "plan_tarifar" } }, [
                      _vm._v("Plan tarifar"),
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
          _vm._v(" "),
          _c(
            "b-row",
            [
              _c("b-col", { attrs: { cols: "2" } }, [
                _c(
                  "div",
                  { staticClass: "form-label-group" },
                  [
                    _c("b-form-input", {
                      attrs: {
                        readonly: _vm.activeActionLocal == "Vizualizează",
                        id: "cod_caen",
                        placeholder: "Cod caen",
                      },
                      model: {
                        value: _vm.editVarLocal.cod_caen,
                        callback: function ($$v) {
                          _vm.$set(_vm.editVarLocal, "cod_caen", $$v)
                        },
                        expression: "editVarLocal.cod_caen",
                      },
                    }),
                    _vm._v(" "),
                    _c("label", { attrs: { for: "cod_caen" } }, [
                      _vm._v("Cod caen"),
                    ]),
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
                      attrs: {
                        readonly: _vm.activeActionLocal == "Vizualizează",
                        id: "email_factura",
                        placeholder: "Email factura",
                      },
                      model: {
                        value: _vm.editVarLocal.email_factura,
                        callback: function ($$v) {
                          _vm.$set(_vm.editVarLocal, "email_factura", $$v)
                        },
                        expression: "editVarLocal.email_factura",
                      },
                    }),
                    _vm._v(" "),
                    _c("label", { attrs: { for: "email_factura" } }, [
                      _vm._v("Email factura"),
                    ]),
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
                      attrs: {
                        readonly: _vm.activeActionLocal == "Vizualizează",
                        id: "email_restante",
                        placeholder: "Email restante",
                      },
                      model: {
                        value: _vm.editVarLocal.email_restante,
                        callback: function ($$v) {
                          _vm.$set(_vm.editVarLocal, "email_restante", $$v)
                        },
                        expression: "editVarLocal.email_restante",
                      },
                    }),
                    _vm._v(" "),
                    _c("label", { attrs: { for: "email_restante" } }, [
                      _vm._v("Email restante"),
                    ]),
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
                      attrs: {
                        readonly: _vm.activeActionLocal == "Vizualizează",
                        id: "slug",
                        placeholder: "Prescurtare denumire",
                      },
                      model: {
                        value: _vm.editVarLocal.slug,
                        callback: function ($$v) {
                          _vm.$set(_vm.editVarLocal, "slug", $$v)
                        },
                        expression: "editVarLocal.slug",
                      },
                    }),
                    _vm._v(" "),
                    _c("label", { attrs: { for: "slug" } }, [
                      _vm._v("Prescurtare denumire"),
                    ]),
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
                      attrs: {
                        readonly: _vm.activeActionLocal == "Vizualizează",
                        id: "operator_gdpr",
                        placeholder: "Operator GDPR",
                      },
                      model: {
                        value: _vm.editVarLocal.operator_gdpr,
                        callback: function ($$v) {
                          _vm.$set(_vm.editVarLocal, "operator_gdpr", $$v)
                        },
                        expression: "editVarLocal.operator_gdpr",
                      },
                    }),
                    _vm._v(" "),
                    _c("label", { attrs: { for: "operator_gdpr" } }, [
                      _vm._v("Operator GDPR"),
                    ]),
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
                      attrs: {
                        readonly: _vm.activeActionLocal == "Vizualizează",
                        id: "nrautorizatie",
                        placeholder: "Nr autorizatie",
                      },
                      model: {
                        value: _vm.editVarLocal.nrautorizatie,
                        callback: function ($$v) {
                          _vm.$set(_vm.editVarLocal, "nrautorizatie", $$v)
                        },
                        expression: "editVarLocal.nrautorizatie",
                      },
                    }),
                    _vm._v(" "),
                    _c("label", { attrs: { for: "nrautorizatie" } }, [
                      _vm._v("Nr autorizatie"),
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
          _vm._v(" "),
          _c(
            "b-row",
            [
              _c("b-col", { attrs: { cols: "2" } }, [
                _c(
                  "div",
                  { staticClass: "form-label-group" },
                  [
                    _c("b-form-input", {
                      attrs: {
                        readonly: _vm.activeActionLocal == "Vizualizează",
                        id: "cerber_url",
                        placeholder: "Cerber URL",
                      },
                      model: {
                        value: _vm.editVarLocal.cerber_url,
                        callback: function ($$v) {
                          _vm.$set(_vm.editVarLocal, "cerber_url", $$v)
                        },
                        expression: "editVarLocal.cerber_url",
                      },
                    }),
                    _vm._v(" "),
                    _c("label", { attrs: { for: "cerber_url" } }, [
                      _vm._v("Cerber URL"),
                    ]),
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
    ],
    1
  )
}
var staticRenderFns = []
render._withStripped = true



/***/ }),

/***/ "./resources/js/src/views/app_pages/Company.vue":
/*!******************************************************!*\
  !*** ./resources/js/src/views/app_pages/Company.vue ***!
  \******************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _Company_vue_vue_type_template_id_9561504c___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./Company.vue?vue&type=template&id=9561504c& */ "./resources/js/src/views/app_pages/Company.vue?vue&type=template&id=9561504c&");
/* harmony import */ var _Company_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./Company.vue?vue&type=script&lang=js& */ "./resources/js/src/views/app_pages/Company.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport *//* harmony import */ var _node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../../../../node_modules/vue-loader/lib/runtime/componentNormalizer.js */ "./node_modules/vue-loader/lib/runtime/componentNormalizer.js");





/* normalize component */

var component = Object(_node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__["default"])(
  _Company_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__["default"],
  _Company_vue_vue_type_template_id_9561504c___WEBPACK_IMPORTED_MODULE_0__["render"],
  _Company_vue_vue_type_template_id_9561504c___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"],
  false,
  null,
  null,
  null
  
)

/* hot reload */
if (false) { var api; }
component.options.__file = "resources/js/src/views/app_pages/Company.vue"
/* harmony default export */ __webpack_exports__["default"] = (component.exports);

/***/ }),

/***/ "./resources/js/src/views/app_pages/Company.vue?vue&type=script&lang=js&":
/*!*******************************************************************************!*\
  !*** ./resources/js/src/views/app_pages/Company.vue?vue&type=script&lang=js& ***!
  \*******************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_Company_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../node_modules/babel-loader/lib??ref--4-0!../../../../../node_modules/vue-loader/lib??vue-loader-options!./Company.vue?vue&type=script&lang=js& */ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/app_pages/Company.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__["default"] = (_node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_Company_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__["default"]); 

/***/ }),

/***/ "./resources/js/src/views/app_pages/Company.vue?vue&type=template&id=9561504c&":
/*!*************************************************************************************!*\
  !*** ./resources/js/src/views/app_pages/Company.vue?vue&type=template&id=9561504c& ***!
  \*************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_Company_vue_vue_type_template_id_9561504c___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!../../../../../node_modules/vue-loader/lib??vue-loader-options!./Company.vue?vue&type=template&id=9561504c& */ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/app_pages/Company.vue?vue&type=template&id=9561504c&");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "render", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_Company_vue_vue_type_template_id_9561504c___WEBPACK_IMPORTED_MODULE_0__["render"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "staticRenderFns", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_Company_vue_vue_type_template_id_9561504c___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"]; });



/***/ }),

/***/ "./resources/js/src/views/app_pages/Companyaev.vue":
/*!*********************************************************!*\
  !*** ./resources/js/src/views/app_pages/Companyaev.vue ***!
  \*********************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _Companyaev_vue_vue_type_template_id_addaa430___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./Companyaev.vue?vue&type=template&id=addaa430& */ "./resources/js/src/views/app_pages/Companyaev.vue?vue&type=template&id=addaa430&");
/* harmony import */ var _Companyaev_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./Companyaev.vue?vue&type=script&lang=js& */ "./resources/js/src/views/app_pages/Companyaev.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport *//* harmony import */ var _node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../../../../node_modules/vue-loader/lib/runtime/componentNormalizer.js */ "./node_modules/vue-loader/lib/runtime/componentNormalizer.js");





/* normalize component */

var component = Object(_node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__["default"])(
  _Companyaev_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__["default"],
  _Companyaev_vue_vue_type_template_id_addaa430___WEBPACK_IMPORTED_MODULE_0__["render"],
  _Companyaev_vue_vue_type_template_id_addaa430___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"],
  false,
  null,
  null,
  null
  
)

/* hot reload */
if (false) { var api; }
component.options.__file = "resources/js/src/views/app_pages/Companyaev.vue"
/* harmony default export */ __webpack_exports__["default"] = (component.exports);

/***/ }),

/***/ "./resources/js/src/views/app_pages/Companyaev.vue?vue&type=script&lang=js&":
/*!**********************************************************************************!*\
  !*** ./resources/js/src/views/app_pages/Companyaev.vue?vue&type=script&lang=js& ***!
  \**********************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_Companyaev_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../node_modules/babel-loader/lib??ref--4-0!../../../../../node_modules/vue-loader/lib??vue-loader-options!./Companyaev.vue?vue&type=script&lang=js& */ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/app_pages/Companyaev.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__["default"] = (_node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_Companyaev_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__["default"]); 

/***/ }),

/***/ "./resources/js/src/views/app_pages/Companyaev.vue?vue&type=template&id=addaa430&":
/*!****************************************************************************************!*\
  !*** ./resources/js/src/views/app_pages/Companyaev.vue?vue&type=template&id=addaa430& ***!
  \****************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_Companyaev_vue_vue_type_template_id_addaa430___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!../../../../../node_modules/vue-loader/lib??vue-loader-options!./Companyaev.vue?vue&type=template&id=addaa430& */ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/app_pages/Companyaev.vue?vue&type=template&id=addaa430&");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "render", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_Companyaev_vue_vue_type_template_id_addaa430___WEBPACK_IMPORTED_MODULE_0__["render"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "staticRenderFns", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_Companyaev_vue_vue_type_template_id_addaa430___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"]; });



/***/ })

}]);