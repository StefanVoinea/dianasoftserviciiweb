(window["webpackJsonp"] = window["webpackJsonp"] || []).push([[144],{

/***/ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/app_pages/Jurnalsms.vue?vue&type=script&lang=js&":
/*!*****************************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib??ref--4-0!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/src/views/app_pages/Jurnalsms.vue?vue&type=script&lang=js& ***!
  \*****************************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var core_js_modules_es_function_name_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! core-js/modules/es.function.name.js */ "./node_modules/core-js/modules/es.function.name.js");
/* harmony import */ var core_js_modules_es_function_name_js__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_es_function_name_js__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _Jurnalsmsaev_vue__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./Jurnalsmsaev.vue */ "./resources/js/src/views/app_pages/Jurnalsmsaev.vue");
/* harmony import */ var axios__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! axios */ "./node_modules/axios/index.js");
/* harmony import */ var axios__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(axios__WEBPACK_IMPORTED_MODULE_2__);

//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
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
    Jurnalsmsaev: _Jurnalsmsaev_vue__WEBPACK_IMPORTED_MODULE_1__["default"]
  },
  name: "jurnalsms",
  data: function data() {
    return {
      name: "",
      canTransmiteSMS: this.$userpermitt.can("transmiteSMS"),
      afisezRSMS: false,
      refreshLocal: false,
      idselectat: null,
      campFiltruStart: "",
      modelName: "jurnalsms",
      modelDisplayName: "Jurnal SMS",
      editVar: {
        nr_contract: "",
        telefon: "",
        mesaj: "",
        status: "",
        utilizator: "",
        data_operare: "",
        data_transmitere: "",
        catre: "",
        id_site: "",
        data_verificare_status: ""
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
        label: "Agentia",
        field: "contract.agentia",
        width: "150px",
        type: "text",
        showSortAsc: true
      }, {
        label: "Nr contract",
        field: "nr_contract",
        width: "150px",
        type: "text",
        showSortAsc: true
      }, {
        label: "Telefon",
        field: "telefon",
        width: "150px",
        type: "text",
        showSortAsc: true
      }, {
        label: "Mesaj",
        field: "mesaj",
        width: "600px",
        type: "text",
        showSortAsc: true
      }, {
        label: "Status",
        field: "status",
        width: "150px",
        type: "text",
        showSortAsc: true
      }, {
        label: "Utilizator",
        field: "utilizator",
        width: "150px",
        type: "text",
        showSortAsc: true
      }, {
        label: "Data operarii",
        field: "data_operare",
        width: "150px",
        type: "date",
        dateInputFormat: "yyyy-MM-dd'T'HH:mm:ss.SSSSSS'Z'",
        // expects 2018-03-16
        dateOutputFormat: "dd.MM.yyyy HH:mm:ss",
        // outputs Mar 16th 2018
        showSortAsc: true
      }, {
        label: "Data transmitere",
        field: "data_transmitere",
        width: "150px",
        type: "date",
        dateInputFormat: "yyyy-MM-dd'T'HH:mm:ss.SSSSSS'Z'",
        // expects 2018-03-16
        dateOutputFormat: "dd.MM.yyyy HH:mm:ss",
        // outputs Mar 16th 2018
        showSortAsc: true
      }, {
        label: "Catre",
        field: "catre",
        width: "150px",
        type: "text",
        showSortAsc: true
      }, {
        label: "ID Site",
        field: "id_site",
        width: "150px",
        type: "text",
        showSortAsc: true
      }, {
        label: "Data verificare status",
        field: "data_verificare_status",
        width: "150px",
        type: "date",
        dateInputFormat: "yyyy-MM-dd'T'HH:mm:ss.SSSSSS'Z'",
        // expects 2018-03-16
        dateOutputFormat: "dd.MM.yyyy HH:mm:ss",
        // outputs Mar 16th 2018
        showSortAsc: true
      }]
    };
  },
  methods: {
    transmiteSMS: function transmiteSMS() {
      document.getElementById('file' + this.name).click();
    },
    onFileChange: function onFileChange(e) {
      this.file = e.target.files[0];

      if (this.file) {
        this.importFisier(e);
      }
    },
    importFisier: function importFisier(e) {
      var _this = this;

      this.showLoading = true;
      e.preventDefault();
      var currentObj = this;
      var config = {
        headers: {
          'content-type': 'multipart/form-data',
          'Authorization': 'Bearer ' + this.$store.state.app.token,
          'AuthorizationHeader': JSON.parse(this.$store.state.app.societateaCurenta).id
        }
      };
      var formData = new FormData();
      formData.append('file', this.file);
      axios__WEBPACK_IMPORTED_MODULE_2___default.a.post('/api/transmitesms', formData, config).then(function (response) {
        _this.name = "";

        _this.$bvToast.toast("Transmitere efectuata cu success!", {
          title: "Transmitere cu succes! ",
          variant: "success",
          solid: false,
          appendToast: true,
          autoHideDelay: 3000,
          toaster: "b-toaster-bottom-right"
        });

        _this.showLoading = false;
      });
    },
    raportSMS: function raportSMS() {
      this.afisezRSMS = true;
    },
    aevClosed: function aevClosed() {
      this.idselectat = null;
      this.selectedID = "";
      this.activeEdit = false;
      this.editVar = {
        nr_contract: "",
        telefon: "",
        mesaj: "",
        status: "",
        utilizator: "",
        data_operare: "",
        data_transmitere: "",
        catre: "",
        id_site: "",
        data_verificare_status: ""
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
        nr_contract: "",
        telefon: "",
        mesaj: "",
        status: "",
        utilizator: "",
        data_operare: "",
        data_transmitere: "",
        catre: "",
        id_site: "",
        data_verificare_status: ""
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

/***/ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/app_pages/Jurnalsmsaev.vue?vue&type=script&lang=js&":
/*!********************************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib??ref--4-0!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/src/views/app_pages/Jurnalsmsaev.vue?vue&type=script&lang=js& ***!
  \********************************************************************************************************************************************************************************/
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
  name: "jurnalsmsaev",
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
      modelName: "jurnalsms",
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
          nr_contract: '',
          telefon: '',
          mesaj: '',
          status: '',
          utilizator: '',
          data_operare: '',
          data_transmitere: '',
          catre: '',
          id_site: '',
          data_verificare_status: ''
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

        _this3.$emit("stored", response);

        _this3.$emit("closed");
      })["catch"](function (error) {
        _this3.showLoading = true;
      });
    },
    aevClosed: function aevClosed() {
      this.idselectat = null;
      this.selectedID = "";
      this.activeEditLocal = false;
      this.editVarLocal = {
        nr_contract: "",
        telefon: "",
        mesaj: "",
        status: "",
        utilizator: "",
        data_operare: "",
        data_transmitere: "",
        catre: "",
        id_site: "",
        data_verificare_status: ""
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
        _this4.selectedID = "";
        _this4.editVarLocal = {
          nr_contract: "",
          telefon: "",
          mesaj: "",
          status: "",
          utilizator: "",
          data_operare: "",
          data_transmitere: "",
          catre: "",
          id_site: "",
          data_verificare_status: ""
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
        _this4.activeEditLocal = false;
        _this4.activeActionLocal = "";

        _this4.$emit("stored", "");

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

/***/ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/app_pages/Jurnalsms.vue?vue&type=template&id=9ee873f8&":
/*!*********************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/src/views/app_pages/Jurnalsms.vue?vue&type=template&id=9ee873f8& ***!
  \*********************************************************************************************************************************************************************************************************************/
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
              _c("b-dropdown", {
                directives: [
                  {
                    name: "b-tooltip",
                    rawName: "v-b-tooltip.hover.v-light",
                    modifiers: { hover: true, "v-light": true },
                  },
                ],
                staticClass: "dropdown-icon-wrapper mr-1",
                attrs: {
                  text: "Situatii jurnal SMS",
                  title: "Situatii jurnal SMS",
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
              }),
              _vm._v(" "),
              _c(
                "b-dropdown",
                {
                  directives: [
                    {
                      name: "b-tooltip",
                      rawName: "v-b-tooltip.hover.v-light",
                      modifiers: { hover: true, "v-light": true },
                    },
                  ],
                  staticClass: "dropdown-icon-wrapper mr-1",
                  attrs: {
                    text: "Alte actiuni",
                    variant: "outline-success",
                    title: "Alte actiuni",
                  },
                  scopedSlots: _vm._u([
                    {
                      key: "button-content",
                      fn: function () {
                        return [
                          _c("feather-icon", {
                            staticClass: "align-middle",
                            attrs: { icon: "LayersIcon", size: "16" },
                          }),
                        ]
                      },
                      proxy: true,
                    },
                  ]),
                },
                [
                  _vm._v(" "),
                  _vm.canTransmiteSMS
                    ? _c(
                        "b-dropdown-item",
                        { on: { click: _vm.transmiteSMS } },
                        [
                          _vm._v(
                            "\n                                                         Transmite SMS\n                                                       "
                          ),
                        ]
                      )
                    : _vm._e(),
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
      _vm._v(" "),
      _c("Jurnalsmsaev", {
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
      _c("input", {
        ref: "file" + _vm.name,
        attrs: {
          type: "file",
          id: "file" + _vm.name,
          accept: "application/vnd.ms-excel",
          hidden: "",
        },
        on: { change: _vm.onFileChange },
      }),
    ],
    1
  )
}
var staticRenderFns = []
render._withStripped = true



/***/ }),

/***/ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/app_pages/Jurnalsmsaev.vue?vue&type=template&id=466a977e&":
/*!************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/src/views/app_pages/Jurnalsmsaev.vue?vue&type=template&id=466a977e& ***!
  \************************************************************************************************************************************************************************************************************************/
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
              centered: "",
              "ok-variant": "success",
              "cancel-title": "Cancel",
              "ok-title": "Save",
              "cancel-variant": "warning",
              scrollable: "",
              "cancel-disabled": _vm.activeActionLocal == "Vizualizează",
              "ok-disabled": _vm.activeActionLocal == "Vizualizează",
              "modal-class": "modal-success",
              title: _vm.activeActionLocal + " Jurnal SMS",
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
                      { attrs: { cols: "2" } },
                      [
                        _c("validation-provider", {
                          attrs: { name: "Nr contract", rules: "required" },
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
                                          autocomplete: "off",
                                          id: "nr_contract",
                                          placeholder: "Nr contract",
                                        },
                                        model: {
                                          value: _vm.editVarLocal.nr_contract,
                                          callback: function ($$v) {
                                            _vm.$set(
                                              _vm.editVarLocal,
                                              "nr_contract",
                                              $$v
                                            )
                                          },
                                          expression:
                                            "editVarLocal.nr_contract",
                                        },
                                      }),
                                      _vm._v(" "),
                                      _c(
                                        "label",
                                        { attrs: { for: "nr_contract" } },
                                        [_vm._v("Nr contract")]
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
                          attrs: { name: "Telefon", rules: "required" },
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
                                          autocomplete: "off",
                                          id: "telefon",
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
                                        "label",
                                        { attrs: { for: "telefon" } },
                                        [_vm._v("Telefon")]
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
                    _c("b-col", { attrs: { cols: "2" } }, [
                      _c(
                        "div",
                        { staticClass: "form-label-group" },
                        [
                          _c("validation-provider", {
                            attrs: { name: "Mesaj", rules: "required" },
                            scopedSlots: _vm._u([
                              {
                                key: "default",
                                fn: function (ref) {
                                  var errors = ref.errors
                                  return [
                                    _c("b-form-textarea", {
                                      attrs: {
                                        id: ".mesaj",
                                        rows: "3",
                                        placeholder: ".Mesaj",
                                      },
                                      model: {
                                        value: _vm.editVarLocal.mesaj,
                                        callback: function ($$v) {
                                          _vm.$set(
                                            _vm.editVarLocal,
                                            "mesaj",
                                            $$v
                                          )
                                        },
                                        expression: "editVarLocal.mesaj",
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
                          _c("label", { attrs: { for: "label-.mesaj" } }, [
                            _vm._v(".Mesaj"),
                          ]),
                        ],
                        1
                      ),
                    ]),
                    _vm._v(" "),
                    _c(
                      "b-col",
                      { attrs: { cols: "2" } },
                      [
                        _c("validation-provider", {
                          attrs: { name: "Status", rules: "required" },
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
                                          autocomplete: "off",
                                          id: "status",
                                          placeholder: "Status",
                                        },
                                        model: {
                                          value: _vm.editVarLocal.status,
                                          callback: function ($$v) {
                                            _vm.$set(
                                              _vm.editVarLocal,
                                              "status",
                                              $$v
                                            )
                                          },
                                          expression: "editVarLocal.status",
                                        },
                                      }),
                                      _vm._v(" "),
                                      _c(
                                        "label",
                                        { attrs: { for: "status" } },
                                        [_vm._v("Status")]
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
                          attrs: { name: "Utilizator", rules: "required" },
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
                                          autocomplete: "off",
                                          id: "utilizator",
                                          placeholder: "Utilizator",
                                        },
                                        model: {
                                          value: _vm.editVarLocal.utilizator,
                                          callback: function ($$v) {
                                            _vm.$set(
                                              _vm.editVarLocal,
                                              "utilizator",
                                              $$v
                                            )
                                          },
                                          expression: "editVarLocal.utilizator",
                                        },
                                      }),
                                      _vm._v(" "),
                                      _c(
                                        "label",
                                        { attrs: { for: "utilizator" } },
                                        [_vm._v("Utilizator")]
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
                        _c("datasiora", {
                          attrs: {
                            readonly: _vm.activeActionLocal == "Vizualizează",
                            name: "Data operarii",
                            campDisplay: "Data operarii",
                          },
                          model: {
                            value: _vm.editVarLocal.data_operare,
                            callback: function ($$v) {
                              _vm.$set(_vm.editVarLocal, "data_operare", $$v)
                            },
                            expression: "editVarLocal.data_operare",
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
                        _c("datasiora", {
                          attrs: {
                            readonly: _vm.activeActionLocal == "Vizualizează",
                            name: "Data transmitere",
                            campDisplay: "Data transmitere",
                          },
                          model: {
                            value: _vm.editVarLocal.data_transmitere,
                            callback: function ($$v) {
                              _vm.$set(
                                _vm.editVarLocal,
                                "data_transmitere",
                                $$v
                              )
                            },
                            expression: "editVarLocal.data_transmitere",
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
                        _c("validation-provider", {
                          attrs: { name: "Catre", rules: "required" },
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
                                          autocomplete: "off",
                                          id: "catre",
                                          placeholder: "Catre",
                                        },
                                        model: {
                                          value: _vm.editVarLocal.catre,
                                          callback: function ($$v) {
                                            _vm.$set(
                                              _vm.editVarLocal,
                                              "catre",
                                              $$v
                                            )
                                          },
                                          expression: "editVarLocal.catre",
                                        },
                                      }),
                                      _vm._v(" "),
                                      _c("label", { attrs: { for: "catre" } }, [
                                        _vm._v("Catre"),
                                      ]),
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
                          attrs: { name: "ID Site", rules: "required" },
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
                                          autocomplete: "off",
                                          id: "id_site",
                                          placeholder: "ID Site",
                                        },
                                        model: {
                                          value: _vm.editVarLocal.id_site,
                                          callback: function ($$v) {
                                            _vm.$set(
                                              _vm.editVarLocal,
                                              "id_site",
                                              $$v
                                            )
                                          },
                                          expression: "editVarLocal.id_site",
                                        },
                                      }),
                                      _vm._v(" "),
                                      _c(
                                        "label",
                                        { attrs: { for: "id_site" } },
                                        [_vm._v("ID Site")]
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
                        _c("datasiora", {
                          attrs: {
                            readonly: _vm.activeActionLocal == "Vizualizează",
                            name: "Data verificare status",
                            campDisplay: "Data verificare status",
                          },
                          model: {
                            value: _vm.editVarLocal.data_verificare_status,
                            callback: function ($$v) {
                              _vm.$set(
                                _vm.editVarLocal,
                                "data_verificare_status",
                                $$v
                              )
                            },
                            expression: "editVarLocal.data_verificare_status",
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

/***/ "./resources/js/src/views/app_pages/Jurnalsms.vue":
/*!********************************************************!*\
  !*** ./resources/js/src/views/app_pages/Jurnalsms.vue ***!
  \********************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _Jurnalsms_vue_vue_type_template_id_9ee873f8___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./Jurnalsms.vue?vue&type=template&id=9ee873f8& */ "./resources/js/src/views/app_pages/Jurnalsms.vue?vue&type=template&id=9ee873f8&");
/* harmony import */ var _Jurnalsms_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./Jurnalsms.vue?vue&type=script&lang=js& */ "./resources/js/src/views/app_pages/Jurnalsms.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport *//* harmony import */ var _node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../../../../node_modules/vue-loader/lib/runtime/componentNormalizer.js */ "./node_modules/vue-loader/lib/runtime/componentNormalizer.js");





/* normalize component */

var component = Object(_node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__["default"])(
  _Jurnalsms_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__["default"],
  _Jurnalsms_vue_vue_type_template_id_9ee873f8___WEBPACK_IMPORTED_MODULE_0__["render"],
  _Jurnalsms_vue_vue_type_template_id_9ee873f8___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"],
  false,
  null,
  null,
  null
  
)

/* hot reload */
if (false) { var api; }
component.options.__file = "resources/js/src/views/app_pages/Jurnalsms.vue"
/* harmony default export */ __webpack_exports__["default"] = (component.exports);

/***/ }),

/***/ "./resources/js/src/views/app_pages/Jurnalsms.vue?vue&type=script&lang=js&":
/*!*********************************************************************************!*\
  !*** ./resources/js/src/views/app_pages/Jurnalsms.vue?vue&type=script&lang=js& ***!
  \*********************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_Jurnalsms_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../node_modules/babel-loader/lib??ref--4-0!../../../../../node_modules/vue-loader/lib??vue-loader-options!./Jurnalsms.vue?vue&type=script&lang=js& */ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/app_pages/Jurnalsms.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__["default"] = (_node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_Jurnalsms_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__["default"]); 

/***/ }),

/***/ "./resources/js/src/views/app_pages/Jurnalsms.vue?vue&type=template&id=9ee873f8&":
/*!***************************************************************************************!*\
  !*** ./resources/js/src/views/app_pages/Jurnalsms.vue?vue&type=template&id=9ee873f8& ***!
  \***************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_Jurnalsms_vue_vue_type_template_id_9ee873f8___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!../../../../../node_modules/vue-loader/lib??vue-loader-options!./Jurnalsms.vue?vue&type=template&id=9ee873f8& */ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/app_pages/Jurnalsms.vue?vue&type=template&id=9ee873f8&");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "render", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_Jurnalsms_vue_vue_type_template_id_9ee873f8___WEBPACK_IMPORTED_MODULE_0__["render"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "staticRenderFns", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_Jurnalsms_vue_vue_type_template_id_9ee873f8___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"]; });



/***/ }),

/***/ "./resources/js/src/views/app_pages/Jurnalsmsaev.vue":
/*!***********************************************************!*\
  !*** ./resources/js/src/views/app_pages/Jurnalsmsaev.vue ***!
  \***********************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _Jurnalsmsaev_vue_vue_type_template_id_466a977e___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./Jurnalsmsaev.vue?vue&type=template&id=466a977e& */ "./resources/js/src/views/app_pages/Jurnalsmsaev.vue?vue&type=template&id=466a977e&");
/* harmony import */ var _Jurnalsmsaev_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./Jurnalsmsaev.vue?vue&type=script&lang=js& */ "./resources/js/src/views/app_pages/Jurnalsmsaev.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport *//* harmony import */ var _node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../../../../node_modules/vue-loader/lib/runtime/componentNormalizer.js */ "./node_modules/vue-loader/lib/runtime/componentNormalizer.js");





/* normalize component */

var component = Object(_node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__["default"])(
  _Jurnalsmsaev_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__["default"],
  _Jurnalsmsaev_vue_vue_type_template_id_466a977e___WEBPACK_IMPORTED_MODULE_0__["render"],
  _Jurnalsmsaev_vue_vue_type_template_id_466a977e___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"],
  false,
  null,
  null,
  null
  
)

/* hot reload */
if (false) { var api; }
component.options.__file = "resources/js/src/views/app_pages/Jurnalsmsaev.vue"
/* harmony default export */ __webpack_exports__["default"] = (component.exports);

/***/ }),

/***/ "./resources/js/src/views/app_pages/Jurnalsmsaev.vue?vue&type=script&lang=js&":
/*!************************************************************************************!*\
  !*** ./resources/js/src/views/app_pages/Jurnalsmsaev.vue?vue&type=script&lang=js& ***!
  \************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_Jurnalsmsaev_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../node_modules/babel-loader/lib??ref--4-0!../../../../../node_modules/vue-loader/lib??vue-loader-options!./Jurnalsmsaev.vue?vue&type=script&lang=js& */ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/app_pages/Jurnalsmsaev.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__["default"] = (_node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_Jurnalsmsaev_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__["default"]); 

/***/ }),

/***/ "./resources/js/src/views/app_pages/Jurnalsmsaev.vue?vue&type=template&id=466a977e&":
/*!******************************************************************************************!*\
  !*** ./resources/js/src/views/app_pages/Jurnalsmsaev.vue?vue&type=template&id=466a977e& ***!
  \******************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_Jurnalsmsaev_vue_vue_type_template_id_466a977e___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!../../../../../node_modules/vue-loader/lib??vue-loader-options!./Jurnalsmsaev.vue?vue&type=template&id=466a977e& */ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/app_pages/Jurnalsmsaev.vue?vue&type=template&id=466a977e&");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "render", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_Jurnalsmsaev_vue_vue_type_template_id_466a977e___WEBPACK_IMPORTED_MODULE_0__["render"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "staticRenderFns", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_Jurnalsmsaev_vue_vue_type_template_id_466a977e___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"]; });



/***/ })

}]);