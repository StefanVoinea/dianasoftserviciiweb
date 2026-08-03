(window["webpackJsonp"] = window["webpackJsonp"] || []).push([[138],{

/***/ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/app_pages/Activity.vue?vue&type=script&lang=js&":
/*!****************************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib??ref--4-0!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/src/views/app_pages/Activity.vue?vue&type=script&lang=js& ***!
  \****************************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _Activityaev_vue__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./Activityaev.vue */ "./resources/js/src/views/app_pages/Activityaev.vue");
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
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
    Activityaev: _Activityaev_vue__WEBPACK_IMPORTED_MODULE_0__["default"]
  },
  name: "activity",
  data: function data() {
    return {
      refreshLocal: false,
      idselectat: null,
      campFiltruStart: "",
      modelName: "activity",
      modelDisplayName: "Jurnal de operare",
      editVar: {
        user_id: "",
        company_id: "",
        subject: "",
        description: "",
        changes: ""
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
        label: "User",
        field: "user.name",
        width: "200px",
        type: "text",
        showSortAsc: true
      }, {
        label: "Subiect",
        field: "subject_type",
        width: "200px",
        type: "text",
        showSortAsc: true
      }, {
        label: "Description",
        field: "description",
        width: "200px",
        type: "text",
        showSortAsc: true
      }, {
        label: "Modificari",
        field: "changes",
        width: "300px",
        type: "text",
        showSortAsc: true
      }, {
        label: "Data operarii",
        field: "created_at",
        width: "200px",
        type: "date",
        dateInputFormat: "yyyy-MM-dd'T'HH:mm:ss.SSSSSS'Z'",
        // expects 2018-03-16
        dateOutputFormat: "dd.MM.yyyy HH:mm:ss",
        // outputs Mar 16th 2018
        //dateInputFormat: "yyyy-MM-dd", // expects 2018-03-16
        //dateOutputFormat: "dd.MM.yyyy", // outputs Mar 16th 2018 
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
        user_id: "",
        company_id: "",
        subject: "",
        description: "",
        changes: {
          before: "",
          after: ""
        },
        user: {
          name: ""
        }
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
        user_id: "",
        company_id: "",
        subject: "",
        description: "",
        changes: ""
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

/***/ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/app_pages/Activityaev.vue?vue&type=script&lang=js&":
/*!*******************************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib??ref--4-0!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/src/views/app_pages/Activityaev.vue?vue&type=script&lang=js& ***!
  \*******************************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var C_dianasoft_serviciiweb_web_node_modules_babel_runtime_helpers_esm_defineProperty_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./node_modules/@babel/runtime/helpers/esm/defineProperty.js */ "./node_modules/@babel/runtime/helpers/esm/defineProperty.js");
/* harmony import */ var core_js_modules_es_array_map_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! core-js/modules/es.array.map.js */ "./node_modules/core-js/modules/es.array.map.js");
/* harmony import */ var core_js_modules_es_array_map_js__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_es_array_map_js__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var core_js_modules_es_object_keys_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! core-js/modules/es.object.keys.js */ "./node_modules/core-js/modules/es.object.keys.js");
/* harmony import */ var core_js_modules_es_object_keys_js__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_es_object_keys_js__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var vue_ripple_directive__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! vue-ripple-directive */ "./node_modules/vue-ripple-directive/src/ripple.js");
/* harmony import */ var _core_mixins_ui_transition__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! @core/mixins/ui/transition */ "./resources/js/src/@core/mixins/ui/transition.js");
/* harmony import */ var bootstrap_vue__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! bootstrap-vue */ "./node_modules/bootstrap-vue/esm/index.js");
/* harmony import */ var _validations__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! @validations */ "./resources/js/src/@core/utils/validations/validations.js");
/* harmony import */ var vee_validate__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! vee-validate */ "./node_modules/vee-validate/dist/vee-validate.esm.js");



//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
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
  components: {
    ValidationProvider: vee_validate__WEBPACK_IMPORTED_MODULE_7__["ValidationProvider"],
    ValidationObserver: vee_validate__WEBPACK_IMPORTED_MODULE_7__["ValidationObserver"]
  },
  directives: {
    "b-modal": bootstrap_vue__WEBPACK_IMPORTED_MODULE_5__["VBModal"],
    Ripple: vue_ripple_directive__WEBPACK_IMPORTED_MODULE_3__["default"]
  },
  name: "activityaev",
  data: function data() {
    return {
      required: _validations__WEBPACK_IMPORTED_MODULE_6__["required"],
      password: _validations__WEBPACK_IMPORTED_MODULE_6__["password"],
      email: _validations__WEBPACK_IMPORTED_MODULE_6__["email"],
      confirmed: _validations__WEBPACK_IMPORTED_MODULE_6__["confirmed"],
      min: _validations__WEBPACK_IMPORTED_MODULE_6__["min"],
      rutainapoiLocal: this.rutainapoi,
      activeEditLocal: false,
      activeActionLocal: this.activeAction,
      editVarLocal: this.editVar,
      nextTodoId: 2,
      modelName: "activity",
      showLoading: false,
      fieldsBefore: [{
        key: 'name',
        label: "Denumire",
        field_type: 'label',
        sortable: true,
        searchable: true,
        readonly: false
      }, {
        key: 'value',
        label: 'Valoare',
        field_type: 'label',
        sortable: true,
        searchable: true,
        readonly: false
      }],
      itemsBefore: [],
      fieldsAfter: [{
        key: 'name',
        label: "Denumire",
        field_type: 'label',
        sortable: true,
        searchable: true,
        readonly: false
      }, {
        key: 'value',
        label: 'Valoare',
        field_type: 'label',
        sortable: true,
        searchable: true,
        readonly: false
      }],
      itemsAfter: []
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
      var _this = this;

      if (this.editVar.changes.before != "" || this.editVar.changes.after != "") {
        this.itemsBefore = [];
        this.itemsAfter = [];
        Object.keys(this.editVar.changes.before).map(function (t) {
          _this.itemsBefore.push({
            name: t,
            value: _this.editVar.changes.before[t]
          });
        });
        Object.keys(this.editVar.changes.after).map(function (t) {
          _this.itemsAfter.push({
            name: t,
            value: _this.editVar.changes.after[t]
          });
        });
      }

      this.editVarLocal = this.editVar;
    }
  },
  methods: Object(C_dianasoft_serviciiweb_web_node_modules_babel_runtime_helpers_esm_defineProperty_js__WEBPACK_IMPORTED_MODULE_0__["default"])({
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
      var payLoad = this.editVarLocal;
      payLoad.requestType = "post";
      payLoad.requestUrl = "/" + this.modelName + "/store";
      this.$store.dispatch("app/api_Request", payLoad).then(function (response) {
        _this4.editVarLocal = {
          user_id: '',
          company_id: '',
          subject: '',
          description: '',
          changes: ''
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

        _this4.$emit("stored", "");

        _this4.$emit("closed");
      })["catch"](function (error) {
        _this4.showLoading = true;
      });
    },
    aevClosed: function aevClosed() {
      this.idselectat = null;
      this.selectedID = "";
      this.activeEditLocal = false;
      this.editVarLocal = {
        user_id: "",
        company_id: "",
        subject: "",
        description: "",
        changes: {
          before: "",
          after: ""
        }
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
          user_id: "",
          company_id: "",
          subject: "",
          description: "",
          user: {
            name: ""
          },
          changes: {
            before: "",
            after: ""
          }
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

        _this5.activeEditLocal = false;
        _this5.activeActionLocal = "";

        _this5.$emit("closed");
      })["catch"](function (error) {
        _this5.showLoading = false;
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

/***/ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/app_pages/Activity.vue?vue&type=template&id=03147f82&":
/*!********************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/src/views/app_pages/Activity.vue?vue&type=template&id=03147f82& ***!
  \********************************************************************************************************************************************************************************************************************/
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
      _c("Activityaev", {
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

/***/ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/app_pages/Activityaev.vue?vue&type=template&id=4996f580&":
/*!***********************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/src/views/app_pages/Activityaev.vue?vue&type=template&id=4996f580& ***!
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
              "ok-variant": "success",
              "cancel-title": "Cancel",
              "ok-title": "Save",
              "cancel-variant": "warning",
              scrollable: "",
              "cancel-disabled": _vm.activeActionLocal == "Vizualizează",
              "ok-disabled": _vm.activeActionLocal == "Vizualizează",
              "modal-class": "modal-success",
              title: _vm.activeActionLocal + " Activitate",
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
                          attrs: { name: "User", rules: "" },
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
                                          id: "user_id",
                                          placeholder: "User",
                                        },
                                        model: {
                                          value: _vm.editVarLocal.user.name,
                                          callback: function ($$v) {
                                            _vm.$set(
                                              _vm.editVarLocal.user,
                                              "name",
                                              $$v
                                            )
                                          },
                                          expression: "editVarLocal.user.name",
                                        },
                                      }),
                                      _vm._v(" "),
                                      _c(
                                        "label",
                                        { attrs: { for: "user_id" } },
                                        [_vm._v("User")]
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
                          attrs: { name: "Operatiune", rules: "required" },
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
                                          id: "description",
                                          placeholder: "Operatiune",
                                        },
                                        model: {
                                          value: _vm.editVarLocal.description,
                                          callback: function ($$v) {
                                            _vm.$set(
                                              _vm.editVarLocal,
                                              "description",
                                              $$v
                                            )
                                          },
                                          expression:
                                            "editVarLocal.description",
                                        },
                                      }),
                                      _vm._v(" "),
                                      _c(
                                        "label",
                                        { attrs: { for: "description" } },
                                        [_vm._v("Operatiune")]
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
                          _c("datasiora", {
                            attrs: {
                              readonly: _vm.activeActionLocal == "Vizualizează",
                              id: "created_at",
                              campDisplay: "Data operarii",
                              placeholder: "Data operarii",
                            },
                            model: {
                              value: _vm.editVarLocal.created_at,
                              callback: function ($$v) {
                                _vm.$set(_vm.editVarLocal, "created_at", $$v)
                              },
                              expression: "editVarLocal.created_at",
                            },
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
                    _vm.editVarLocal.changes.before
                      ? _c(
                          "b-col",
                          { attrs: { cols: "6" } },
                          [
                            _c("dstable", {
                              attrs: {
                                title: _vm.editVarLocal.description.includes(
                                  "updated"
                                )
                                  ? "Before"
                                  : "Valori",
                                fields: _vm.fieldsBefore,
                                items: _vm.itemsBefore,
                              },
                            }),
                          ],
                          1
                        )
                      : _vm._e(),
                    _vm._v(" "),
                    _vm.editVarLocal.description.includes("updated")
                      ? _c(
                          "b-col",
                          { attrs: { cols: "6" } },
                          [
                            _c("dstable", {
                              attrs: {
                                title: "After",
                                fields: _vm.fieldsAfter,
                                items: _vm.itemsAfter,
                              },
                            }),
                          ],
                          1
                        )
                      : _vm._e(),
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

/***/ "./resources/js/src/views/app_pages/Activity.vue":
/*!*******************************************************!*\
  !*** ./resources/js/src/views/app_pages/Activity.vue ***!
  \*******************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _Activity_vue_vue_type_template_id_03147f82___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./Activity.vue?vue&type=template&id=03147f82& */ "./resources/js/src/views/app_pages/Activity.vue?vue&type=template&id=03147f82&");
/* harmony import */ var _Activity_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./Activity.vue?vue&type=script&lang=js& */ "./resources/js/src/views/app_pages/Activity.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport *//* harmony import */ var _node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../../../../node_modules/vue-loader/lib/runtime/componentNormalizer.js */ "./node_modules/vue-loader/lib/runtime/componentNormalizer.js");





/* normalize component */

var component = Object(_node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__["default"])(
  _Activity_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__["default"],
  _Activity_vue_vue_type_template_id_03147f82___WEBPACK_IMPORTED_MODULE_0__["render"],
  _Activity_vue_vue_type_template_id_03147f82___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"],
  false,
  null,
  null,
  null
  
)

/* hot reload */
if (false) { var api; }
component.options.__file = "resources/js/src/views/app_pages/Activity.vue"
/* harmony default export */ __webpack_exports__["default"] = (component.exports);

/***/ }),

/***/ "./resources/js/src/views/app_pages/Activity.vue?vue&type=script&lang=js&":
/*!********************************************************************************!*\
  !*** ./resources/js/src/views/app_pages/Activity.vue?vue&type=script&lang=js& ***!
  \********************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_Activity_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../node_modules/babel-loader/lib??ref--4-0!../../../../../node_modules/vue-loader/lib??vue-loader-options!./Activity.vue?vue&type=script&lang=js& */ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/app_pages/Activity.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__["default"] = (_node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_Activity_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__["default"]); 

/***/ }),

/***/ "./resources/js/src/views/app_pages/Activity.vue?vue&type=template&id=03147f82&":
/*!**************************************************************************************!*\
  !*** ./resources/js/src/views/app_pages/Activity.vue?vue&type=template&id=03147f82& ***!
  \**************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_Activity_vue_vue_type_template_id_03147f82___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!../../../../../node_modules/vue-loader/lib??vue-loader-options!./Activity.vue?vue&type=template&id=03147f82& */ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/app_pages/Activity.vue?vue&type=template&id=03147f82&");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "render", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_Activity_vue_vue_type_template_id_03147f82___WEBPACK_IMPORTED_MODULE_0__["render"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "staticRenderFns", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_Activity_vue_vue_type_template_id_03147f82___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"]; });



/***/ }),

/***/ "./resources/js/src/views/app_pages/Activityaev.vue":
/*!**********************************************************!*\
  !*** ./resources/js/src/views/app_pages/Activityaev.vue ***!
  \**********************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _Activityaev_vue_vue_type_template_id_4996f580___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./Activityaev.vue?vue&type=template&id=4996f580& */ "./resources/js/src/views/app_pages/Activityaev.vue?vue&type=template&id=4996f580&");
/* harmony import */ var _Activityaev_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./Activityaev.vue?vue&type=script&lang=js& */ "./resources/js/src/views/app_pages/Activityaev.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport *//* harmony import */ var _node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../../../../node_modules/vue-loader/lib/runtime/componentNormalizer.js */ "./node_modules/vue-loader/lib/runtime/componentNormalizer.js");





/* normalize component */

var component = Object(_node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__["default"])(
  _Activityaev_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__["default"],
  _Activityaev_vue_vue_type_template_id_4996f580___WEBPACK_IMPORTED_MODULE_0__["render"],
  _Activityaev_vue_vue_type_template_id_4996f580___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"],
  false,
  null,
  null,
  null
  
)

/* hot reload */
if (false) { var api; }
component.options.__file = "resources/js/src/views/app_pages/Activityaev.vue"
/* harmony default export */ __webpack_exports__["default"] = (component.exports);

/***/ }),

/***/ "./resources/js/src/views/app_pages/Activityaev.vue?vue&type=script&lang=js&":
/*!***********************************************************************************!*\
  !*** ./resources/js/src/views/app_pages/Activityaev.vue?vue&type=script&lang=js& ***!
  \***********************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_Activityaev_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../node_modules/babel-loader/lib??ref--4-0!../../../../../node_modules/vue-loader/lib??vue-loader-options!./Activityaev.vue?vue&type=script&lang=js& */ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/app_pages/Activityaev.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__["default"] = (_node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_Activityaev_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__["default"]); 

/***/ }),

/***/ "./resources/js/src/views/app_pages/Activityaev.vue?vue&type=template&id=4996f580&":
/*!*****************************************************************************************!*\
  !*** ./resources/js/src/views/app_pages/Activityaev.vue?vue&type=template&id=4996f580& ***!
  \*****************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_Activityaev_vue_vue_type_template_id_4996f580___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!../../../../../node_modules/vue-loader/lib??vue-loader-options!./Activityaev.vue?vue&type=template&id=4996f580& */ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/app_pages/Activityaev.vue?vue&type=template&id=4996f580&");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "render", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_Activityaev_vue_vue_type_template_id_4996f580___WEBPACK_IMPORTED_MODULE_0__["render"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "staticRenderFns", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_Activityaev_vue_vue_type_template_id_4996f580___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"]; });



/***/ })

}]);