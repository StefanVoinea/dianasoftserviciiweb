(window["webpackJsonp"] = window["webpackJsonp"] || []).push([[149],{

/***/ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/app_pages/Notificationtype.vue?vue&type=script&lang=js&":
/*!************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib??ref--4-0!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/src/views/app_pages/Notificationtype.vue?vue&type=script&lang=js& ***!
  \************************************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _Notificationtypeaev_vue__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./Notificationtypeaev.vue */ "./resources/js/src/views/app_pages/Notificationtypeaev.vue");
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
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
    Notificationtypeaev: _Notificationtypeaev_vue__WEBPACK_IMPORTED_MODULE_0__["default"]
  },
  name: "notificationtype",
  data: function data() {
    return {
      refreshLocal: false,
      idselectat: null,
      campFiltruStart: "",
      modelName: "notificationtype",
      modelDisplayName: "Tip notificare",
      editVar: {
        categoria: "",
        denumire: "",
        notificationuser: [{
          id: 1,
          user_id: '',
          channel: ''
        }]
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
        label: "Categoria",
        field: "categoria",
        width: "200px",
        type: "text",
        showSortAsc: true
      }, {
        label: "Denumire",
        field: "denumire",
        width: "400px",
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
        categoria: "",
        denumire: "",
        notificationuser: [{
          id: 1,
          user_id: '',
          channel: ''
        }]
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
        categoria: "",
        denumire: "",
        notificationuser: [{
          id: 1,
          user_id: '',
          channel: ''
        }]
      };
      this.activeEdit = true;
    },
    edit: function edit() {
      this.idselectat = null;
      this.editVar = Object.assign({}, this.selectedID);

      if (this.editVar.notificationuser.length == 0) {
        this.editVar.notificationuser = [{
          id: 1,
          user_id: '',
          channel: ''
        }];
      }

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

/***/ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/app_pages/Notificationtypeaev.vue?vue&type=script&lang=js&":
/*!***************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib??ref--4-0!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/src/views/app_pages/Notificationtypeaev.vue?vue&type=script&lang=js& ***!
  \***************************************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var C_dianasoft_serviciiweb_web_node_modules_babel_runtime_helpers_esm_defineProperty_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./node_modules/@babel/runtime/helpers/esm/defineProperty.js */ "./node_modules/@babel/runtime/helpers/esm/defineProperty.js");
/* harmony import */ var core_js_modules_es_array_splice_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! core-js/modules/es.array.splice.js */ "./node_modules/core-js/modules/es.array.splice.js");
/* harmony import */ var core_js_modules_es_array_splice_js__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_es_array_splice_js__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var core_js_modules_es_object_to_string_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! core-js/modules/es.object.to-string.js */ "./node_modules/core-js/modules/es.object.to-string.js");
/* harmony import */ var core_js_modules_es_object_to_string_js__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_es_object_to_string_js__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var core_js_modules_es_regexp_to_string_js__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! core-js/modules/es.regexp.to-string.js */ "./node_modules/core-js/modules/es.regexp.to-string.js");
/* harmony import */ var core_js_modules_es_regexp_to_string_js__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_es_regexp_to_string_js__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var vue_ripple_directive__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! vue-ripple-directive */ "./node_modules/vue-ripple-directive/src/ripple.js");
/* harmony import */ var _core_mixins_ui_transition__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! @core/mixins/ui/transition */ "./resources/js/src/@core/mixins/ui/transition.js");
/* harmony import */ var bootstrap_vue__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! bootstrap-vue */ "./node_modules/bootstrap-vue/esm/index.js");




//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
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
  mixins: [_core_mixins_ui_transition__WEBPACK_IMPORTED_MODULE_5__["heightTransition"]],
  components: {},
  directives: {
    "b-modal": bootstrap_vue__WEBPACK_IMPORTED_MODULE_6__["VBModal"],
    Ripple: vue_ripple_directive__WEBPACK_IMPORTED_MODULE_4__["default"]
  },
  name: "notificationtypeaev",
  data: function data() {
    var _ref;

    return _ref = {
      nextTodoId: 2,
      rutainapoiLocal: this.rutainapoi,
      activeEditLocal: false,
      activeActionLocal: this.activeAction,
      editVarLocal: this.editVar
    }, Object(C_dianasoft_serviciiweb_web_node_modules_babel_runtime_helpers_esm_defineProperty_js__WEBPACK_IMPORTED_MODULE_0__["default"])(_ref, "nextTodoId", 2), Object(C_dianasoft_serviciiweb_web_node_modules_babel_runtime_helpers_esm_defineProperty_js__WEBPACK_IMPORTED_MODULE_0__["default"])(_ref, "modelName", "notificationtype"), Object(C_dianasoft_serviciiweb_web_node_modules_babel_runtime_helpers_esm_defineProperty_js__WEBPACK_IMPORTED_MODULE_0__["default"])(_ref, "showLoading", false), _ref;
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
    removeItem: function removeItem(index) {
      this.editVarLocal.notificationuser.splice(index, 1);
      this.trTrimHeight(this.$refs.row[0].offsetHeight);
    },
    addRow: function addRow(index) {
      var _this = this;

      this.editVarLocal.notificationuser.push({
        id: this.nextTodoId += this.nextTodoId,
        user_id: '',
        channel: ''
      });
      this.$nextTick(function () {
        _this.trAddHeight(_this.$refs.row[0].offsetHeight);
      });
    },
    repeateAgain: function repeateAgain() {
      var _this2 = this;

      this.editVarLocal.notificationuser.push({
        id: this.nextTodoId += this.nextTodoId,
        user_id: '',
        channel: ''
      });
      this.$nextTick(function () {
        _this2.trAddHeight(_this2.$refs.row[0].offsetHeight);
      });
    },
    initTrHeight: function initTrHeight() {
      var _this3 = this;

      this.trSetHeight(null);
      this.$nextTick(function () {
        if (_this3.$refs.form) {
          _this3.trSetHeight(_this3.$refs.form.scrollHeight);
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
      var _this4 = this;

      this.showLoading = true;
      var payLoad = this.editVarLocal;
      payLoad.requestType = "post";
      payLoad.requestUrl = "/" + this.modelName + "/store";
      this.activeEditLocal = false;
      this.activeActionLocal = "";
      this.$store.dispatch("app/api_Request", payLoad).then(function (response) {
        // this.row=response  
        _this4.idselectat = response.id.toString();

        _this4.$emit("stored", response);

        _this4.editVarLocal = {
          categoria: '',
          denumire: ''
        };
        _this4.showLoading = false;

        _this4.$bvToast.toast("Salvare efectuata cu success!", {
          title: "Salvare cu succes! ",
          variant: "success",
          solid: false,
          appendToast: true,
          autoHideDelay: 3000,
          toaster: "b-toaster-bottom-right"
        });

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
      this.activeEditLocal = false;
      this.editVarLocal = {
        categoria: "",
        denumire: ""
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
        _this5.selectedID = ""; // this.tblRecords=response

        _this5.idselectat = response.id.toString();

        _this5.$emit("stored", response);

        _this5.activeEditLocal = false;
        _this5.activeActionLocal = "";
        _this5.editVarLocal = {
          categoria: "",
          denumire: ""
        };
        _this5.showLoading = false;

        _this5.$bvToast.toast("Modificare efectuata cu success!", {
          title: "Modificare cu succes! ",
          variant: "success",
          solid: false,
          appendToast: true,
          autoHideDelay: 3000,
          toaster: "b-toaster-bottom-right"
        });

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

/***/ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/app_pages/Notificationtype.vue?vue&type=template&id=83841dd0&":
/*!****************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/src/views/app_pages/Notificationtype.vue?vue&type=template&id=83841dd0& ***!
  \****************************************************************************************************************************************************************************************************************************/
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
            { attrs: { cols: "8" } },
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
      _c("Notificationtypeaev", {
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

/***/ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/app_pages/Notificationtypeaev.vue?vue&type=template&id=170da3ea&":
/*!*******************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/src/views/app_pages/Notificationtypeaev.vue?vue&type=template&id=170da3ea& ***!
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
    "div",
    { staticClass: "d-flex justify-content-center" },
    [
      _c(
        "b-modal",
        {
          attrs: {
            id: "Dianasoftmodelaev",
            size: "lg",
            "ok-variant": "success",
            "cancel-title": "Cancel",
            "ok-title": "Save",
            "cancel-variant": "warning",
            scrollable: "",
            "modal-class": "modal-success",
            title: _vm.activeActionLocal + " Tip notificare",
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
              _c("b-col", { attrs: { cols: "4" } }, [
                _c(
                  "div",
                  { staticClass: "form-label-group" },
                  [
                    _c("dropdowncuoptiuni", {
                      attrs: {
                        name: "categoria",
                        readonly: _vm.activeAction == "Vizualizează",
                        campDisplay: "Categoria",
                        field_name: "Categorie notificare",
                        limitToList: "true",
                      },
                      model: {
                        value: _vm.editVarLocal.categoria,
                        callback: function ($$v) {
                          _vm.$set(_vm.editVarLocal, "categoria", $$v)
                        },
                        expression: "editVarLocal.categoria ",
                      },
                    }),
                  ],
                  1
                ),
              ]),
              _vm._v(" "),
              _c("b-col", { attrs: { cols: "8" } }, [
                _c(
                  "div",
                  { staticClass: "form-label-group" },
                  [
                    _c(
                      "b-form-group",
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
                      ],
                      1
                    ),
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
            "b-container",
            { attrs: { fluid: "" } },
            [
              _c(
                "b-form",
                {
                  ref: "form",
                  staticClass: "repeater-form",
                  style: { height: _vm.trHeight },
                  on: {
                    submit: function ($event) {
                      $event.preventDefault()
                      return _vm.repeateAgain.apply(null, arguments)
                    },
                  },
                },
                [
                  _c(
                    "b-row",
                    [
                      _c("b-col", { attrs: { cols: "6" } }, [
                        _vm._v("\n             User\n      "),
                      ]),
                      _vm._v(" "),
                      _c("b-col", { attrs: { cols: "4" } }, [
                        _vm._v("   \n           Canal de comunicare\n        "),
                      ]),
                      _vm._v(" "),
                      _c("b-col", { attrs: { cols: "2" } }),
                    ],
                    1
                  ),
                  _vm._v(" "),
                  _vm._l(
                    _vm.editVarLocal.notificationuser,
                    function (item, index) {
                      return _c(
                        "b-row",
                        {
                          key: item.id,
                          ref: "row",
                          refInFor: true,
                          attrs: { id: item.id },
                        },
                        [
                          _c(
                            "b-col",
                            { attrs: { cols: "6" } },
                            [
                              _c("selectoneuser", {
                                attrs: {
                                  labelDisplay: "",
                                  readonly:
                                    _vm.activeActionLocal == "Vizualizează",
                                },
                                on: {
                                  change: function ($event) {
                                    return _vm.addRow(index)
                                  },
                                },
                                model: {
                                  value: item.user,
                                  callback: function ($$v) {
                                    _vm.$set(item, "user", $$v)
                                  },
                                  expression: "item.user",
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
                              _c("dropdowncuoptiuni", {
                                attrs: {
                                  name: "channel",
                                  readonly: _vm.activeAction == "Vizualizează",
                                  campDisplay: "",
                                  field_name: "Canal de comunicare notificari",
                                  limitToList: "true",
                                },
                                model: {
                                  value: item.channel,
                                  callback: function ($$v) {
                                    _vm.$set(item, "channel", $$v)
                                  },
                                  expression: "item.channel ",
                                },
                              }),
                            ],
                            1
                          ),
                          _vm._v(" "),
                          _c(
                            "b-col",
                            {
                              staticClass: "d-flex justify-content-between",
                              attrs: { cols: "2" },
                            },
                            [
                              _c(
                                "b-button",
                                {
                                  directives: [
                                    {
                                      name: "ripple",
                                      rawName: "v-ripple.400",
                                      value: "rgba(234, 84, 85, 0.15)",
                                      expression: "'rgba(234, 84, 85, 0.15)'",
                                      modifiers: { 400: true },
                                    },
                                    {
                                      name: "show",
                                      rawName: "v-show",
                                      value: _vm.activeAction != "Vizualizează",
                                      expression:
                                        "activeAction!='Vizualizează'",
                                    },
                                  ],
                                  staticClass: "btn-icon",
                                  attrs: {
                                    variant: "flat-success",
                                    size: "sm",
                                  },
                                  on: { click: _vm.addRow },
                                },
                                [
                                  _c("feather-icon", {
                                    attrs: { icon: "PlusIcon" },
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
                                      value:
                                        index > 0 &&
                                        _vm.activeAction != "Vizualizează",
                                      expression:
                                        "index>0&&activeAction!='Vizualizează'",
                                    },
                                    {
                                      name: "ripple",
                                      rawName: "v-ripple.400",
                                      value: "rgba(234, 84, 85, 0.15)",
                                      expression: "'rgba(234, 84, 85, 0.15)'",
                                      modifiers: { 400: true },
                                    },
                                  ],
                                  staticClass: "btn-icon",
                                  attrs: { variant: "flat-danger", size: "sm" },
                                  on: {
                                    click: function ($event) {
                                      return _vm.removeItem(index)
                                    },
                                  },
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
                        ],
                        1
                      )
                    }
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
                ],
                2
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
}
var staticRenderFns = []
render._withStripped = true



/***/ }),

/***/ "./resources/js/src/views/app_pages/Notificationtype.vue":
/*!***************************************************************!*\
  !*** ./resources/js/src/views/app_pages/Notificationtype.vue ***!
  \***************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _Notificationtype_vue_vue_type_template_id_83841dd0___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./Notificationtype.vue?vue&type=template&id=83841dd0& */ "./resources/js/src/views/app_pages/Notificationtype.vue?vue&type=template&id=83841dd0&");
/* harmony import */ var _Notificationtype_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./Notificationtype.vue?vue&type=script&lang=js& */ "./resources/js/src/views/app_pages/Notificationtype.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport *//* harmony import */ var _node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../../../../node_modules/vue-loader/lib/runtime/componentNormalizer.js */ "./node_modules/vue-loader/lib/runtime/componentNormalizer.js");





/* normalize component */

var component = Object(_node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__["default"])(
  _Notificationtype_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__["default"],
  _Notificationtype_vue_vue_type_template_id_83841dd0___WEBPACK_IMPORTED_MODULE_0__["render"],
  _Notificationtype_vue_vue_type_template_id_83841dd0___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"],
  false,
  null,
  null,
  null
  
)

/* hot reload */
if (false) { var api; }
component.options.__file = "resources/js/src/views/app_pages/Notificationtype.vue"
/* harmony default export */ __webpack_exports__["default"] = (component.exports);

/***/ }),

/***/ "./resources/js/src/views/app_pages/Notificationtype.vue?vue&type=script&lang=js&":
/*!****************************************************************************************!*\
  !*** ./resources/js/src/views/app_pages/Notificationtype.vue?vue&type=script&lang=js& ***!
  \****************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_Notificationtype_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../node_modules/babel-loader/lib??ref--4-0!../../../../../node_modules/vue-loader/lib??vue-loader-options!./Notificationtype.vue?vue&type=script&lang=js& */ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/app_pages/Notificationtype.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__["default"] = (_node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_Notificationtype_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__["default"]); 

/***/ }),

/***/ "./resources/js/src/views/app_pages/Notificationtype.vue?vue&type=template&id=83841dd0&":
/*!**********************************************************************************************!*\
  !*** ./resources/js/src/views/app_pages/Notificationtype.vue?vue&type=template&id=83841dd0& ***!
  \**********************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_Notificationtype_vue_vue_type_template_id_83841dd0___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!../../../../../node_modules/vue-loader/lib??vue-loader-options!./Notificationtype.vue?vue&type=template&id=83841dd0& */ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/app_pages/Notificationtype.vue?vue&type=template&id=83841dd0&");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "render", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_Notificationtype_vue_vue_type_template_id_83841dd0___WEBPACK_IMPORTED_MODULE_0__["render"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "staticRenderFns", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_Notificationtype_vue_vue_type_template_id_83841dd0___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"]; });



/***/ }),

/***/ "./resources/js/src/views/app_pages/Notificationtypeaev.vue":
/*!******************************************************************!*\
  !*** ./resources/js/src/views/app_pages/Notificationtypeaev.vue ***!
  \******************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _Notificationtypeaev_vue_vue_type_template_id_170da3ea___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./Notificationtypeaev.vue?vue&type=template&id=170da3ea& */ "./resources/js/src/views/app_pages/Notificationtypeaev.vue?vue&type=template&id=170da3ea&");
/* harmony import */ var _Notificationtypeaev_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./Notificationtypeaev.vue?vue&type=script&lang=js& */ "./resources/js/src/views/app_pages/Notificationtypeaev.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport *//* harmony import */ var _node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../../../../node_modules/vue-loader/lib/runtime/componentNormalizer.js */ "./node_modules/vue-loader/lib/runtime/componentNormalizer.js");





/* normalize component */

var component = Object(_node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__["default"])(
  _Notificationtypeaev_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__["default"],
  _Notificationtypeaev_vue_vue_type_template_id_170da3ea___WEBPACK_IMPORTED_MODULE_0__["render"],
  _Notificationtypeaev_vue_vue_type_template_id_170da3ea___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"],
  false,
  null,
  null,
  null
  
)

/* hot reload */
if (false) { var api; }
component.options.__file = "resources/js/src/views/app_pages/Notificationtypeaev.vue"
/* harmony default export */ __webpack_exports__["default"] = (component.exports);

/***/ }),

/***/ "./resources/js/src/views/app_pages/Notificationtypeaev.vue?vue&type=script&lang=js&":
/*!*******************************************************************************************!*\
  !*** ./resources/js/src/views/app_pages/Notificationtypeaev.vue?vue&type=script&lang=js& ***!
  \*******************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_Notificationtypeaev_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../node_modules/babel-loader/lib??ref--4-0!../../../../../node_modules/vue-loader/lib??vue-loader-options!./Notificationtypeaev.vue?vue&type=script&lang=js& */ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/app_pages/Notificationtypeaev.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__["default"] = (_node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_Notificationtypeaev_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__["default"]); 

/***/ }),

/***/ "./resources/js/src/views/app_pages/Notificationtypeaev.vue?vue&type=template&id=170da3ea&":
/*!*************************************************************************************************!*\
  !*** ./resources/js/src/views/app_pages/Notificationtypeaev.vue?vue&type=template&id=170da3ea& ***!
  \*************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_Notificationtypeaev_vue_vue_type_template_id_170da3ea___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!../../../../../node_modules/vue-loader/lib??vue-loader-options!./Notificationtypeaev.vue?vue&type=template&id=170da3ea& */ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/app_pages/Notificationtypeaev.vue?vue&type=template&id=170da3ea&");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "render", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_Notificationtypeaev_vue_vue_type_template_id_170da3ea___WEBPACK_IMPORTED_MODULE_0__["render"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "staticRenderFns", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_Notificationtypeaev_vue_vue_type_template_id_170da3ea___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"]; });



/***/ })

}]);