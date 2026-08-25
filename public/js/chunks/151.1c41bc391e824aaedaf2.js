(window["webpackJsonp"] = window["webpackJsonp"] || []).push([[151],{

/***/ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/app_pages/Ordinedeblocareanaf.vue?vue&type=script&lang=js&":
/*!***************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib??ref--4-0!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/src/views/app_pages/Ordinedeblocareanaf.vue?vue&type=script&lang=js& ***!
  \***************************************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _Ordinedeblocareanafaev_vue__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./Ordinedeblocareanafaev.vue */ "./resources/js/src/views/app_pages/Ordinedeblocareanafaev.vue");
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
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
    Ordinedeblocareanafaev: _Ordinedeblocareanafaev_vue__WEBPACK_IMPORTED_MODULE_0__["default"]
  },
  name: "ordinedeblocareanaf",
  data: function data() {
    return {
      refreshLocal: false,
      idselectat: null,
      campFiltruStart: "",
      modelName: "ordinedeblocareanaf",
      modelDisplayName: "Ordine de blocare ANAF",
      editVar: {
        nr_ordin: "",
        data_ordin: "",
        suspect: "",
        date_de_identificare: "",
        bunuri_blocate: "",
        ordin_de_revocare: "",
        data_revocarii: "",
        institutia: ""
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
        label: "Nr ordin",
        field: "nr_ordin",
        width: "150px",
        type: "text",
        showSortAsc: true
      }, {
        label: "Data ordin",
        field: "data_ordin",
        width: "200px",
        type: "date",
        //dateInputFormat: "yyyy-MM-dd'T'HH:mm:ss.SSSSSS'Z'", // expects 2018-03-16
        //dateOutputFormat: "dd.MM.yyyy HH:mm:ss", // outputs Mar 16th 2018
        dateInputFormat: "yyyy-MM-dd",
        // expects 2018-03-16
        dateOutputFormat: "dd.MM.yyyy",
        // outputs Mar 16th 2018 
        showSortAsc: true
      }, {
        label: "Suspect",
        field: "suspect",
        width: "300px",
        type: "text",
        showSortAsc: true
      }, {
        label: "Date de identificare",
        field: "date_de_identificare",
        width: "500px",
        type: "text",
        showSortAsc: true
      }, {
        label: "Bunuri blocate",
        field: "bunuri_blocate",
        width: "500px",
        type: "text",
        showSortAsc: true
      }, {
        label: "Ordin de revocare",
        field: "ordin_de_revocare",
        width: "150px",
        type: "text",
        showSortAsc: true
      }, {
        label: "Data revocarii",
        field: "data_revocarii",
        width: "200px",
        type: "date",
        //dateInputFormat: "yyyy-MM-dd'T'HH:mm:ss.SSSSSS'Z'", // expects 2018-03-16
        //dateOutputFormat: "dd.MM.yyyy HH:mm:ss", // outputs Mar 16th 2018
        dateInputFormat: "yyyy-MM-dd",
        // expects 2018-03-16
        dateOutputFormat: "dd.MM.yyyy",
        // outputs Mar 16th 2018 
        showSortAsc: true
      }, {
        label: "Institutia",
        field: "institutia",
        width: "200px",
        type: "text",
        showSortAsc: true
      }]
    };
  },
  methods: {
    aevClosed: function aevClosed() {
      this.idselectat = null;
      this.selectedID = "";
      this.activeEdit = false;
      this.editVar = {
        nr_ordin: "",
        data_ordin: "",
        suspect: "",
        date_de_identificare: "",
        bunuri_blocate: "",
        ordin_de_revocare: "",
        data_revocarii: "",
        institutia: ""
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
        nr_ordin: "",
        data_ordin: "",
        suspect: "",
        date_de_identificare: "",
        bunuri_blocate: "",
        ordin_de_revocare: "",
        data_revocarii: "",
        institutia: ""
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

/***/ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/app_pages/Ordinedeblocareanafaev.vue?vue&type=script&lang=js&":
/*!******************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib??ref--4-0!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/src/views/app_pages/Ordinedeblocareanafaev.vue?vue&type=script&lang=js& ***!
  \******************************************************************************************************************************************************************************************/
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
  name: "ordinedeblocareanafaev",
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
      modelName: "ordinedeblocareanaf",
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
          nr_ordin: '',
          data_ordin: '',
          suspect: '',
          date_de_identificare: '',
          bunuri_blocate: '',
          ordin_de_revocare: '',
          data_revocarii: '',
          institutia: ''
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
    },
    aevClosed: function aevClosed() {
      this.idselectat = null;
      this.selectedID = "";
      this.activeEditLocal = false;
      this.editVarLocal = {
        nr_ordin: "",
        data_ordin: "",
        suspect: "",
        date_de_identificare: "",
        bunuri_blocate: "",
        ordin_de_revocare: "",
        data_revocarii: "",
        institutia: ""
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
          nr_ordin: "",
          data_ordin: "",
          suspect: "",
          date_de_identificare: "",
          bunuri_blocate: "",
          ordin_de_revocare: "",
          data_revocarii: "",
          institutia: ""
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

        _this4.$bvToast.toast(error.data.message, {
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

/***/ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/app_pages/Ordinedeblocareanaf.vue?vue&type=template&id=4b530c45&":
/*!*******************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/src/views/app_pages/Ordinedeblocareanaf.vue?vue&type=template&id=4b530c45& ***!
  \*******************************************************************************************************************************************************************************************************************************/
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
      _c("Ordinedeblocareanafaev", {
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

/***/ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/app_pages/Ordinedeblocareanafaev.vue?vue&type=template&id=02331d9d&":
/*!**********************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/src/views/app_pages/Ordinedeblocareanafaev.vue?vue&type=template&id=02331d9d& ***!
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
              "hide-footer": _vm.activeActionLocal == "Vizualizează",
              "ok-variant": "success",
              "cancel-title": "Cancel",
              "ok-title": "Save",
              "cancel-variant": "warning",
              scrollable: "",
              "cancel-disabled": _vm.activeActionLocal == "Vizualizează",
              "ok-disabled": _vm.activeActionLocal == "Vizualizează",
              "modal-class": "modal-success",
              title: _vm.activeActionLocal + " Ordinedeblocareanaf",
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
                      { attrs: { cols: "1" } },
                      [
                        _c("validation-provider", {
                          attrs: { name: "Nr ordin", rules: "" },
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
                                          id: "nr_ordin",
                                          placeholder: "Nr ordin",
                                        },
                                        model: {
                                          value: _vm.editVarLocal.nr_ordin,
                                          callback: function ($$v) {
                                            _vm.$set(
                                              _vm.editVarLocal,
                                              "nr_ordin",
                                              $$v
                                            )
                                          },
                                          expression: "editVarLocal.nr_ordin",
                                        },
                                      }),
                                      _vm._v(" "),
                                      _c(
                                        "label",
                                        { attrs: { for: "nr_ordin" } },
                                        [_vm._v("Nr ordin")]
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
                          attrs: { name: "Data ordin", rules: "" },
                          scopedSlots: _vm._u([
                            {
                              key: "default",
                              fn: function (ref) {
                                var errors = ref.errors
                                return [
                                  _c("datacalendaristica", {
                                    attrs: {
                                      readonly:
                                        _vm.activeActionLocal == "Vizualizează",
                                      id: "data_ordin",
                                      name: "data_ordin",
                                      campDisplay: "Data ordin",
                                    },
                                    model: {
                                      value: _vm.editVarLocal.data_ordin,
                                      callback: function ($$v) {
                                        _vm.$set(
                                          _vm.editVarLocal,
                                          "data_ordin",
                                          $$v
                                        )
                                      },
                                      expression: "editVarLocal.data_ordin",
                                    },
                                  }),
                                  _vm._v(" "),
                                  _c("small", { staticClass: "text-danger" }, [
                                    _vm._v(_vm._s(errors[0])),
                                  ]),
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
                          attrs: { name: "Ordin de revocare", rules: "" },
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
                                          id: "ordin_de_revocare",
                                          placeholder: "Ordin de revocare",
                                        },
                                        model: {
                                          value:
                                            _vm.editVarLocal.ordin_de_revocare,
                                          callback: function ($$v) {
                                            _vm.$set(
                                              _vm.editVarLocal,
                                              "ordin_de_revocare",
                                              $$v
                                            )
                                          },
                                          expression:
                                            "editVarLocal.ordin_de_revocare",
                                        },
                                      }),
                                      _vm._v(" "),
                                      _c(
                                        "label",
                                        { attrs: { for: "ordin_de_revocare" } },
                                        [_vm._v("Ordin de revocare")]
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
                          attrs: { name: "Data revocarii", rules: "" },
                          scopedSlots: _vm._u([
                            {
                              key: "default",
                              fn: function (ref) {
                                var errors = ref.errors
                                return [
                                  _c("datacalendaristica", {
                                    attrs: {
                                      readonly:
                                        _vm.activeActionLocal == "Vizualizează",
                                      id: "data_revocarii",
                                      name: "data_revocarii",
                                      campDisplay: "Data revocarii",
                                    },
                                    model: {
                                      value: _vm.editVarLocal.data_revocarii,
                                      callback: function ($$v) {
                                        _vm.$set(
                                          _vm.editVarLocal,
                                          "data_revocarii",
                                          $$v
                                        )
                                      },
                                      expression: "editVarLocal.data_revocarii",
                                    },
                                  }),
                                  _vm._v(" "),
                                  _c("small", { staticClass: "text-danger" }, [
                                    _vm._v(_vm._s(errors[0])),
                                  ]),
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
                                        attrs: {
                                          readonly:
                                            _vm.activeActionLocal ==
                                            "Vizualizează",
                                          size: "sm",
                                          autocomplete: "off",
                                          id: "institutia",
                                          placeholder: "Institutia",
                                        },
                                        model: {
                                          value: _vm.editVarLocal.institutia,
                                          callback: function ($$v) {
                                            _vm.$set(
                                              _vm.editVarLocal,
                                              "institutia",
                                              $$v
                                            )
                                          },
                                          expression: "editVarLocal.institutia",
                                        },
                                      }),
                                      _vm._v(" "),
                                      _c(
                                        "label",
                                        { attrs: { for: "institutia" } },
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
                      { attrs: { cols: "2" } },
                      [
                        _c("validation-provider", {
                          attrs: { name: "Suspect", rules: "" },
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
                                          id: "suspect",
                                          rows: "5",
                                          placeholder: "Suspect",
                                        },
                                        model: {
                                          value: _vm.editVarLocal.suspect,
                                          callback: function ($$v) {
                                            _vm.$set(
                                              _vm.editVarLocal,
                                              "suspect",
                                              $$v
                                            )
                                          },
                                          expression: "editVarLocal.suspect",
                                        },
                                      }),
                                      _vm._v(" "),
                                      _c(
                                        "label",
                                        { attrs: { for: "suspect" } },
                                        [_vm._v("Suspect")]
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
                      { attrs: { cols: "5" } },
                      [
                        _c("validation-provider", {
                          attrs: { name: "Date de identificare", rules: "" },
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
                                          id: "date_de_identificare",
                                          rows: "5",
                                          placeholder: "Date de identificare",
                                        },
                                        model: {
                                          value:
                                            _vm.editVarLocal
                                              .date_de_identificare,
                                          callback: function ($$v) {
                                            _vm.$set(
                                              _vm.editVarLocal,
                                              "date_de_identificare",
                                              $$v
                                            )
                                          },
                                          expression:
                                            "editVarLocal.date_de_identificare",
                                        },
                                      }),
                                      _vm._v(" "),
                                      _c(
                                        "label",
                                        {
                                          attrs: {
                                            for: "date_de_identificare",
                                          },
                                        },
                                        [_vm._v("Date de identificare")]
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
                      { attrs: { cols: "5" } },
                      [
                        _c("validation-provider", {
                          attrs: { name: "Bunuri blocate", rules: "" },
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
                                          id: "bunuri_blocate",
                                          rows: "5",
                                          placeholder: "Bunuri blocate",
                                        },
                                        model: {
                                          value:
                                            _vm.editVarLocal.bunuri_blocate,
                                          callback: function ($$v) {
                                            _vm.$set(
                                              _vm.editVarLocal,
                                              "bunuri_blocate",
                                              $$v
                                            )
                                          },
                                          expression:
                                            "editVarLocal.bunuri_blocate",
                                        },
                                      }),
                                      _vm._v(" "),
                                      _c(
                                        "label",
                                        { attrs: { for: "bunuri_blocate" } },
                                        [_vm._v("Bunuri blocate")]
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

/***/ "./resources/js/src/views/app_pages/Ordinedeblocareanaf.vue":
/*!******************************************************************!*\
  !*** ./resources/js/src/views/app_pages/Ordinedeblocareanaf.vue ***!
  \******************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _Ordinedeblocareanaf_vue_vue_type_template_id_4b530c45___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./Ordinedeblocareanaf.vue?vue&type=template&id=4b530c45& */ "./resources/js/src/views/app_pages/Ordinedeblocareanaf.vue?vue&type=template&id=4b530c45&");
/* harmony import */ var _Ordinedeblocareanaf_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./Ordinedeblocareanaf.vue?vue&type=script&lang=js& */ "./resources/js/src/views/app_pages/Ordinedeblocareanaf.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport *//* harmony import */ var _node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../../../../node_modules/vue-loader/lib/runtime/componentNormalizer.js */ "./node_modules/vue-loader/lib/runtime/componentNormalizer.js");





/* normalize component */

var component = Object(_node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__["default"])(
  _Ordinedeblocareanaf_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__["default"],
  _Ordinedeblocareanaf_vue_vue_type_template_id_4b530c45___WEBPACK_IMPORTED_MODULE_0__["render"],
  _Ordinedeblocareanaf_vue_vue_type_template_id_4b530c45___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"],
  false,
  null,
  null,
  null
  
)

/* hot reload */
if (false) { var api; }
component.options.__file = "resources/js/src/views/app_pages/Ordinedeblocareanaf.vue"
/* harmony default export */ __webpack_exports__["default"] = (component.exports);

/***/ }),

/***/ "./resources/js/src/views/app_pages/Ordinedeblocareanaf.vue?vue&type=script&lang=js&":
/*!*******************************************************************************************!*\
  !*** ./resources/js/src/views/app_pages/Ordinedeblocareanaf.vue?vue&type=script&lang=js& ***!
  \*******************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_Ordinedeblocareanaf_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../node_modules/babel-loader/lib??ref--4-0!../../../../../node_modules/vue-loader/lib??vue-loader-options!./Ordinedeblocareanaf.vue?vue&type=script&lang=js& */ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/app_pages/Ordinedeblocareanaf.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__["default"] = (_node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_Ordinedeblocareanaf_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__["default"]); 

/***/ }),

/***/ "./resources/js/src/views/app_pages/Ordinedeblocareanaf.vue?vue&type=template&id=4b530c45&":
/*!*************************************************************************************************!*\
  !*** ./resources/js/src/views/app_pages/Ordinedeblocareanaf.vue?vue&type=template&id=4b530c45& ***!
  \*************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_Ordinedeblocareanaf_vue_vue_type_template_id_4b530c45___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!../../../../../node_modules/vue-loader/lib??vue-loader-options!./Ordinedeblocareanaf.vue?vue&type=template&id=4b530c45& */ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/app_pages/Ordinedeblocareanaf.vue?vue&type=template&id=4b530c45&");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "render", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_Ordinedeblocareanaf_vue_vue_type_template_id_4b530c45___WEBPACK_IMPORTED_MODULE_0__["render"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "staticRenderFns", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_Ordinedeblocareanaf_vue_vue_type_template_id_4b530c45___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"]; });



/***/ }),

/***/ "./resources/js/src/views/app_pages/Ordinedeblocareanafaev.vue":
/*!*********************************************************************!*\
  !*** ./resources/js/src/views/app_pages/Ordinedeblocareanafaev.vue ***!
  \*********************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _Ordinedeblocareanafaev_vue_vue_type_template_id_02331d9d___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./Ordinedeblocareanafaev.vue?vue&type=template&id=02331d9d& */ "./resources/js/src/views/app_pages/Ordinedeblocareanafaev.vue?vue&type=template&id=02331d9d&");
/* harmony import */ var _Ordinedeblocareanafaev_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./Ordinedeblocareanafaev.vue?vue&type=script&lang=js& */ "./resources/js/src/views/app_pages/Ordinedeblocareanafaev.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport *//* harmony import */ var _node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../../../../node_modules/vue-loader/lib/runtime/componentNormalizer.js */ "./node_modules/vue-loader/lib/runtime/componentNormalizer.js");





/* normalize component */

var component = Object(_node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__["default"])(
  _Ordinedeblocareanafaev_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__["default"],
  _Ordinedeblocareanafaev_vue_vue_type_template_id_02331d9d___WEBPACK_IMPORTED_MODULE_0__["render"],
  _Ordinedeblocareanafaev_vue_vue_type_template_id_02331d9d___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"],
  false,
  null,
  null,
  null
  
)

/* hot reload */
if (false) { var api; }
component.options.__file = "resources/js/src/views/app_pages/Ordinedeblocareanafaev.vue"
/* harmony default export */ __webpack_exports__["default"] = (component.exports);

/***/ }),

/***/ "./resources/js/src/views/app_pages/Ordinedeblocareanafaev.vue?vue&type=script&lang=js&":
/*!**********************************************************************************************!*\
  !*** ./resources/js/src/views/app_pages/Ordinedeblocareanafaev.vue?vue&type=script&lang=js& ***!
  \**********************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_Ordinedeblocareanafaev_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../node_modules/babel-loader/lib??ref--4-0!../../../../../node_modules/vue-loader/lib??vue-loader-options!./Ordinedeblocareanafaev.vue?vue&type=script&lang=js& */ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/app_pages/Ordinedeblocareanafaev.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__["default"] = (_node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_Ordinedeblocareanafaev_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__["default"]); 

/***/ }),

/***/ "./resources/js/src/views/app_pages/Ordinedeblocareanafaev.vue?vue&type=template&id=02331d9d&":
/*!****************************************************************************************************!*\
  !*** ./resources/js/src/views/app_pages/Ordinedeblocareanafaev.vue?vue&type=template&id=02331d9d& ***!
  \****************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_Ordinedeblocareanafaev_vue_vue_type_template_id_02331d9d___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!../../../../../node_modules/vue-loader/lib??vue-loader-options!./Ordinedeblocareanafaev.vue?vue&type=template&id=02331d9d& */ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/app_pages/Ordinedeblocareanafaev.vue?vue&type=template&id=02331d9d&");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "render", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_Ordinedeblocareanafaev_vue_vue_type_template_id_02331d9d___WEBPACK_IMPORTED_MODULE_0__["render"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "staticRenderFns", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_Ordinedeblocareanafaev_vue_vue_type_template_id_02331d9d___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"]; });



/***/ })

}]);