(window["webpackJsonp"] = window["webpackJsonp"] || []).push([[141],{

/***/ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/app_pages/DianaSoftMenuOption.vue?vue&type=script&lang=js&":
/*!***************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib??ref--4-0!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/src/views/app_pages/DianaSoftMenuOption.vue?vue&type=script&lang=js& ***!
  \***************************************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _DianaSoftMenuOptionaev_vue__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./DianaSoftMenuOptionaev.vue */ "./resources/js/src/views/app_pages/DianaSoftMenuOptionaev.vue");
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
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
    DianaSoftMenuOptionaev: _DianaSoftMenuOptionaev_vue__WEBPACK_IMPORTED_MODULE_0__["default"]
  },
  name: "dianasoftmenuoption",
  data: function data() {
    return {
      refreshLocal: false,
      idselectat: null,
      campFiltruStart: "",
      modelName: "dianasoftmenuoption",
      modelDisplayName: "Menu Options",
      editVar: {
        name: "",
        url: "",
        slug: "",
        icon: "",
        tag: "",
        tagcolor: "",
        i18n: "",
        dropdown: "",
        parent: "",
        position1: "",
        position2: "",
        isdisabled: ""
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
        label: "Name",
        field: "name",
        width: "300px",
        type: "text",
        showSortAsc: true
      }, {
        label: "URL",
        field: "url",
        width: "300px",
        type: "text",
        showSortAsc: true
      }, {
        label: "Slug",
        field: "slug",
        width: "300px",
        type: "text",
        showSortAsc: true
      }, {
        label: "IsDisabled",
        field: "isdisabled",
        width: "200px",
        type: "boolean",
        showSortAsc: true
      }, {
        label: "Icon",
        field: "icon",
        width: "300px",
        type: "text",
        showSortAsc: true
      }, {
        label: "Tag",
        field: "tag",
        width: "300px",
        type: "text",
        showSortAsc: true
      }, {
        label: "Tag color",
        field: "tagcolor",
        width: "300px",
        type: "text",
        showSortAsc: true
      }, {
        label: "Internalization",
        field: "i18n",
        width: "300px",
        type: "text",
        showSortAsc: true
      }, {
        label: "Dropdown group",
        field: "dropdown",
        width: "300px",
        type: "text",
        showSortAsc: true
      }, {
        label: "Parent name",
        field: "parent",
        width: "300px",
        type: "text",
        showSortAsc: true
      }, {
        label: "Position 1",
        field: "position1",
        width: "300px",
        type: "number",
        showSortAsc: true
      }, {
        label: "Position 2",
        field: "position2",
        width: "300px",
        type: "number",
        showSortAsc: true
      }, {
        label: "Disabled",
        field: "isdisabled",
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
        name: "",
        url: "",
        slug: "",
        icon: "",
        tag: "",
        tagcolor: "",
        i18n: "",
        dropdown: "",
        parent: "",
        position1: "",
        position2: "",
        isdisabled: ""
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
        name: "",
        url: "",
        slug: "",
        icon: "",
        tag: "",
        tagcolor: "",
        i18n: "",
        dropdown: "",
        parent: "",
        position1: "",
        position2: "",
        isdisabled: ""
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

/***/ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/app_pages/DianaSoftMenuOptionaev.vue?vue&type=script&lang=js&":
/*!******************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib??ref--4-0!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/src/views/app_pages/DianaSoftMenuOptionaev.vue?vue&type=script&lang=js& ***!
  \******************************************************************************************************************************************************************************************/
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
  name: "dianasoftmenuoptionaev",
  data: function data() {
    return {
      rutainapoiLocal: this.rutainapoi,
      activeEditLocal: false,
      activeActionLocal: this.activeAction,
      editVarLocal: this.editVar,
      nextTodoId: 2,
      modelName: "dianasoftmenuoption",
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
          name: '',
          url: '',
          slug: '',
          icon: '',
          tag: '',
          tagcolor: '',
          i18n: '',
          dropdown: '',
          parent: '',
          position1: '',
          position2: '',
          isdisabled: ''
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
        name: "",
        url: "",
        slug: "",
        icon: "",
        tag: "",
        tagcolor: "",
        i18n: "",
        dropdown: "",
        parent: "",
        position1: "",
        position2: "",
        isdisabled: ""
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
          name: "",
          url: "",
          slug: "",
          icon: "",
          tag: "",
          tagcolor: "",
          i18n: "",
          dropdown: "",
          parent: "",
          position1: "",
          position2: "",
          isdisabled: ""
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

/***/ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/app_pages/DianaSoftMenuOption.vue?vue&type=template&id=3137af6a&":
/*!*******************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/src/views/app_pages/DianaSoftMenuOption.vue?vue&type=template&id=3137af6a& ***!
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
      _c("DianaSoftMenuOptionaev", {
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

/***/ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/app_pages/DianaSoftMenuOptionaev.vue?vue&type=template&id=280f2750&":
/*!**********************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/src/views/app_pages/DianaSoftMenuOptionaev.vue?vue&type=template&id=280f2750& ***!
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
  return _c(
    "div",
    { staticClass: "d-flex justify-content-center" },
    [
      _c(
        "b-modal",
        {
          attrs: {
            id: "Dianasoftmodelaev",
            "no-close-on-backdrop": "",
            "ok-variant": "success",
            "cancel-title": "Cancel",
            "ok-title": "Save",
            "cancel-variant": "warning",
            scrollable: "",
            "modal-class": "modal-success",
            title: _vm.activeActionLocal + " Menu Options",
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
          _c("br"),
          _vm._v(" "),
          _c(
            "b-row",
            [
              _c("b-col", { attrs: { cols: "12" } }, [
                _c(
                  "div",
                  { staticClass: "form-label-group" },
                  [
                    _c("b-form-input", {
                      attrs: {
                        readonly: _vm.activeActionLocal == "Vizualizează",
                        id: "name",
                        placeholder: "Name",
                      },
                      model: {
                        value: _vm.editVarLocal.name,
                        callback: function ($$v) {
                          _vm.$set(_vm.editVarLocal, "name", $$v)
                        },
                        expression: "editVarLocal.name",
                      },
                    }),
                    _vm._v(" "),
                    _c("label", { attrs: { for: "name" } }, [_vm._v("Name")]),
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
              _c("b-col", { attrs: { cols: "12" } }, [
                _c(
                  "div",
                  { staticClass: "form-label-group" },
                  [
                    _c("b-form-input", {
                      attrs: {
                        readonly: _vm.activeActionLocal == "Vizualizează",
                        id: "url",
                        placeholder: "URL",
                      },
                      model: {
                        value: _vm.editVarLocal.url,
                        callback: function ($$v) {
                          _vm.$set(_vm.editVarLocal, "url", $$v)
                        },
                        expression: "editVarLocal.url",
                      },
                    }),
                    _vm._v(" "),
                    _c("label", { attrs: { for: "url" } }, [_vm._v("URL")]),
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
              _c("b-col", { attrs: { cols: "12" } }, [
                _c(
                  "div",
                  { staticClass: "form-label-group" },
                  [
                    _c("b-form-input", {
                      attrs: {
                        readonly: _vm.activeActionLocal == "Vizualizează",
                        id: "slug",
                        placeholder: "Slug",
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
                    _c("label", { attrs: { for: "slug" } }, [_vm._v("Slug")]),
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
              _c("b-col", { attrs: { cols: "12" } }, [
                _c(
                  "div",
                  { staticClass: "form-label-group" },
                  [
                    _c("b-form-input", {
                      attrs: {
                        readonly: _vm.activeActionLocal == "Vizualizează",
                        id: "icon",
                        placeholder: "Icon",
                      },
                      model: {
                        value: _vm.editVarLocal.icon,
                        callback: function ($$v) {
                          _vm.$set(_vm.editVarLocal, "icon", $$v)
                        },
                        expression: "editVarLocal.icon",
                      },
                    }),
                    _vm._v(" "),
                    _c("label", { attrs: { for: "icon" } }, [_vm._v("Icon")]),
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
              _c("b-col", { attrs: { cols: "12" } }, [
                _c(
                  "div",
                  { staticClass: "form-label-group" },
                  [
                    _c("b-form-input", {
                      attrs: {
                        readonly: _vm.activeActionLocal == "Vizualizează",
                        id: "tag",
                        placeholder: "Tag",
                      },
                      model: {
                        value: _vm.editVarLocal.tag,
                        callback: function ($$v) {
                          _vm.$set(_vm.editVarLocal, "tag", $$v)
                        },
                        expression: "editVarLocal.tag",
                      },
                    }),
                    _vm._v(" "),
                    _c("label", { attrs: { for: "tag" } }, [_vm._v("Tag")]),
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
              _c("b-col", { attrs: { cols: "12" } }, [
                _c(
                  "div",
                  { staticClass: "form-label-group" },
                  [
                    _c("b-form-input", {
                      attrs: {
                        readonly: _vm.activeActionLocal == "Vizualizează",
                        id: "tagcolor",
                        placeholder: "Tag color",
                      },
                      model: {
                        value: _vm.editVarLocal.tagcolor,
                        callback: function ($$v) {
                          _vm.$set(_vm.editVarLocal, "tagcolor", $$v)
                        },
                        expression: "editVarLocal.tagcolor",
                      },
                    }),
                    _vm._v(" "),
                    _c("label", { attrs: { for: "tagcolor" } }, [
                      _vm._v("Tag color"),
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
              _c("b-col", { attrs: { cols: "12" } }, [
                _c(
                  "div",
                  { staticClass: "form-label-group" },
                  [
                    _c("b-form-input", {
                      attrs: {
                        readonly: _vm.activeActionLocal == "Vizualizează",
                        id: "i18n",
                        placeholder: "Internalization",
                      },
                      model: {
                        value: _vm.editVarLocal.i18n,
                        callback: function ($$v) {
                          _vm.$set(_vm.editVarLocal, "i18n", $$v)
                        },
                        expression: "editVarLocal.i18n",
                      },
                    }),
                    _vm._v(" "),
                    _c("label", { attrs: { for: "i18n" } }, [
                      _vm._v("Internalization"),
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
              _c(
                "b-col",
                { attrs: { cols: "12" } },
                [
                  _c(
                    "b-form-checkbox",
                    {
                      staticClass: "custom-control-primary",
                      model: {
                        value: _vm.editVarLocal.dropdown,
                        callback: function ($$v) {
                          _vm.$set(_vm.editVarLocal, "dropdown", $$v)
                        },
                        expression: "editVarLocal.dropdown",
                      },
                    },
                    [
                      _vm._v(
                        "\n                                        Dropdown group\n                                      "
                      ),
                    ]
                  ),
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
            [
              _c("b-col", { attrs: { cols: "12" } }, [
                _c(
                  "div",
                  { staticClass: "form-label-group" },
                  [
                    _c("b-form-input", {
                      attrs: {
                        readonly: _vm.activeActionLocal == "Vizualizează",
                        id: "parent",
                        placeholder: "Parent name",
                      },
                      model: {
                        value: _vm.editVarLocal.parent,
                        callback: function ($$v) {
                          _vm.$set(_vm.editVarLocal, "parent", $$v)
                        },
                        expression: "editVarLocal.parent",
                      },
                    }),
                    _vm._v(" "),
                    _c("label", { attrs: { for: "parent" } }, [
                      _vm._v("Parent name"),
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
              _c("b-col", { attrs: { cols: "12" } }, [
                _c(
                  "div",
                  { staticClass: "form-label-group" },
                  [
                    _c("b-form-input", {
                      attrs: {
                        readonly: _vm.activeActionLocal == "Vizualizează",
                        id: "position1",
                        placeholder: "Position 1",
                      },
                      model: {
                        value: _vm.editVarLocal.position1,
                        callback: function ($$v) {
                          _vm.$set(_vm.editVarLocal, "position1", $$v)
                        },
                        expression: "editVarLocal.position1",
                      },
                    }),
                    _vm._v(" "),
                    _c("label", { attrs: { for: "position1" } }, [
                      _vm._v("Position 1"),
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
              _c("b-col", { attrs: { cols: "12" } }, [
                _c(
                  "div",
                  { staticClass: "form-label-group" },
                  [
                    _c("b-form-input", {
                      attrs: {
                        readonly: _vm.activeActionLocal == "Vizualizează",
                        id: "position2",
                        placeholder: "Position 2",
                      },
                      model: {
                        value: _vm.editVarLocal.position2,
                        callback: function ($$v) {
                          _vm.$set(_vm.editVarLocal, "position2", $$v)
                        },
                        expression: "editVarLocal.position2",
                      },
                    }),
                    _vm._v(" "),
                    _c("label", { attrs: { for: "position2" } }, [
                      _vm._v("Position 2"),
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
              _c(
                "b-col",
                { attrs: { cols: "12" } },
                [
                  _c(
                    "b-form-checkbox",
                    {
                      staticClass: "custom-control-primary",
                      model: {
                        value: _vm.editVarLocal.isdisabled,
                        callback: function ($$v) {
                          _vm.$set(_vm.editVarLocal, "isdisabled", $$v)
                        },
                        expression: "editVarLocal.isdisabled",
                      },
                    },
                    [
                      _vm._v(
                        "\n                                        Disabled\n                                      "
                      ),
                    ]
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
  )
}
var staticRenderFns = []
render._withStripped = true



/***/ }),

/***/ "./resources/js/src/views/app_pages/DianaSoftMenuOption.vue":
/*!******************************************************************!*\
  !*** ./resources/js/src/views/app_pages/DianaSoftMenuOption.vue ***!
  \******************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _DianaSoftMenuOption_vue_vue_type_template_id_3137af6a___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./DianaSoftMenuOption.vue?vue&type=template&id=3137af6a& */ "./resources/js/src/views/app_pages/DianaSoftMenuOption.vue?vue&type=template&id=3137af6a&");
/* harmony import */ var _DianaSoftMenuOption_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./DianaSoftMenuOption.vue?vue&type=script&lang=js& */ "./resources/js/src/views/app_pages/DianaSoftMenuOption.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport *//* harmony import */ var _node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../../../../node_modules/vue-loader/lib/runtime/componentNormalizer.js */ "./node_modules/vue-loader/lib/runtime/componentNormalizer.js");





/* normalize component */

var component = Object(_node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__["default"])(
  _DianaSoftMenuOption_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__["default"],
  _DianaSoftMenuOption_vue_vue_type_template_id_3137af6a___WEBPACK_IMPORTED_MODULE_0__["render"],
  _DianaSoftMenuOption_vue_vue_type_template_id_3137af6a___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"],
  false,
  null,
  null,
  null
  
)

/* hot reload */
if (false) { var api; }
component.options.__file = "resources/js/src/views/app_pages/DianaSoftMenuOption.vue"
/* harmony default export */ __webpack_exports__["default"] = (component.exports);

/***/ }),

/***/ "./resources/js/src/views/app_pages/DianaSoftMenuOption.vue?vue&type=script&lang=js&":
/*!*******************************************************************************************!*\
  !*** ./resources/js/src/views/app_pages/DianaSoftMenuOption.vue?vue&type=script&lang=js& ***!
  \*******************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_DianaSoftMenuOption_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../node_modules/babel-loader/lib??ref--4-0!../../../../../node_modules/vue-loader/lib??vue-loader-options!./DianaSoftMenuOption.vue?vue&type=script&lang=js& */ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/app_pages/DianaSoftMenuOption.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__["default"] = (_node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_DianaSoftMenuOption_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__["default"]); 

/***/ }),

/***/ "./resources/js/src/views/app_pages/DianaSoftMenuOption.vue?vue&type=template&id=3137af6a&":
/*!*************************************************************************************************!*\
  !*** ./resources/js/src/views/app_pages/DianaSoftMenuOption.vue?vue&type=template&id=3137af6a& ***!
  \*************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_DianaSoftMenuOption_vue_vue_type_template_id_3137af6a___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!../../../../../node_modules/vue-loader/lib??vue-loader-options!./DianaSoftMenuOption.vue?vue&type=template&id=3137af6a& */ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/app_pages/DianaSoftMenuOption.vue?vue&type=template&id=3137af6a&");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "render", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_DianaSoftMenuOption_vue_vue_type_template_id_3137af6a___WEBPACK_IMPORTED_MODULE_0__["render"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "staticRenderFns", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_DianaSoftMenuOption_vue_vue_type_template_id_3137af6a___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"]; });



/***/ }),

/***/ "./resources/js/src/views/app_pages/DianaSoftMenuOptionaev.vue":
/*!*********************************************************************!*\
  !*** ./resources/js/src/views/app_pages/DianaSoftMenuOptionaev.vue ***!
  \*********************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _DianaSoftMenuOptionaev_vue_vue_type_template_id_280f2750___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./DianaSoftMenuOptionaev.vue?vue&type=template&id=280f2750& */ "./resources/js/src/views/app_pages/DianaSoftMenuOptionaev.vue?vue&type=template&id=280f2750&");
/* harmony import */ var _DianaSoftMenuOptionaev_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./DianaSoftMenuOptionaev.vue?vue&type=script&lang=js& */ "./resources/js/src/views/app_pages/DianaSoftMenuOptionaev.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport *//* harmony import */ var _node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../../../../node_modules/vue-loader/lib/runtime/componentNormalizer.js */ "./node_modules/vue-loader/lib/runtime/componentNormalizer.js");





/* normalize component */

var component = Object(_node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__["default"])(
  _DianaSoftMenuOptionaev_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__["default"],
  _DianaSoftMenuOptionaev_vue_vue_type_template_id_280f2750___WEBPACK_IMPORTED_MODULE_0__["render"],
  _DianaSoftMenuOptionaev_vue_vue_type_template_id_280f2750___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"],
  false,
  null,
  null,
  null
  
)

/* hot reload */
if (false) { var api; }
component.options.__file = "resources/js/src/views/app_pages/DianaSoftMenuOptionaev.vue"
/* harmony default export */ __webpack_exports__["default"] = (component.exports);

/***/ }),

/***/ "./resources/js/src/views/app_pages/DianaSoftMenuOptionaev.vue?vue&type=script&lang=js&":
/*!**********************************************************************************************!*\
  !*** ./resources/js/src/views/app_pages/DianaSoftMenuOptionaev.vue?vue&type=script&lang=js& ***!
  \**********************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_DianaSoftMenuOptionaev_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../node_modules/babel-loader/lib??ref--4-0!../../../../../node_modules/vue-loader/lib??vue-loader-options!./DianaSoftMenuOptionaev.vue?vue&type=script&lang=js& */ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/app_pages/DianaSoftMenuOptionaev.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__["default"] = (_node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_DianaSoftMenuOptionaev_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__["default"]); 

/***/ }),

/***/ "./resources/js/src/views/app_pages/DianaSoftMenuOptionaev.vue?vue&type=template&id=280f2750&":
/*!****************************************************************************************************!*\
  !*** ./resources/js/src/views/app_pages/DianaSoftMenuOptionaev.vue?vue&type=template&id=280f2750& ***!
  \****************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_DianaSoftMenuOptionaev_vue_vue_type_template_id_280f2750___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!../../../../../node_modules/vue-loader/lib??vue-loader-options!./DianaSoftMenuOptionaev.vue?vue&type=template&id=280f2750& */ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/app_pages/DianaSoftMenuOptionaev.vue?vue&type=template&id=280f2750&");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "render", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_DianaSoftMenuOptionaev_vue_vue_type_template_id_280f2750___WEBPACK_IMPORTED_MODULE_0__["render"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "staticRenderFns", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_DianaSoftMenuOptionaev_vue_vue_type_template_id_280f2750___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"]; });



/***/ })

}]);