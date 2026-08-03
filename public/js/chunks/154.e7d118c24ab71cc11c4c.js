(window["webpackJsonp"] = window["webpackJsonp"] || []).push([[154],{

/***/ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/app_pages/Task.vue?vue&type=script&lang=js&":
/*!************************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib??ref--4-0!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/src/views/app_pages/Task.vue?vue&type=script&lang=js& ***!
  \************************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _Taskaev_vue__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./Taskaev.vue */ "./resources/js/src/views/app_pages/Taskaev.vue");
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
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
    Taskaev: _Taskaev_vue__WEBPACK_IMPORTED_MODULE_0__["default"]
  },
  name: "task",
  data: function data() {
    return {
      refreshLocal: false,
      idselectat: null,
      campFiltruStart: "",
      modelName: "task",
      modelDisplayName: "Task",
      editVar: {
        assignedby_id: "",
        assignedto_id: "",
        title: "",
        description: "",
        duedate: "",
        tags: "",
        completed_at: "",
        iscompleted: "",
        isdeleted: "",
        isimportant: "",
        completedby_id: ""
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
        label: "Assigned By",
        field: "assignedby.name",
        width: "200px",
        type: "text",
        showSortAsc: true
      }, {
        label: "Assigned To",
        field: "assignedto.name",
        width: "200px",
        type: "text",
        showSortAsc: true
      }, {
        label: "Titlu",
        field: "title",
        width: "200px",
        type: "text",
        showSortAsc: true
      }, {
        label: "Descriere",
        field: "description",
        width: "300px",
        type: "text",
        showSortAsc: true
      }, {
        label: "Termen executare",
        field: "duedate",
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
        label: "Tags",
        field: "tags",
        width: "300px",
        type: "text",
        showSortAsc: true
      }, {
        label: "Data executarii",
        field: "completed_at",
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
        label: "Executat",
        field: "iscompleted",
        width: "200px",
        type: "text",
        showSortAsc: true
      }, {
        label: "Sters",
        field: "isdeleted",
        width: "200px",
        type: "text",
        showSortAsc: true
      }, {
        label: "Important",
        field: "isimportant",
        width: "200px",
        type: "text",
        showSortAsc: true
      }, {
        label: "Executat de catre",
        field: "completedby.name",
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
        assignedby_id: "",
        assignedto_id: "",
        title: "",
        description: "",
        duedate: "",
        tags: "",
        completed_at: "",
        iscompleted: "",
        isdeleted: "",
        isimportant: "",
        completedby_id: ""
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
        assignedby_id: "",
        assignedto_id: "",
        title: "",
        description: "",
        duedate: "",
        tags: "",
        completed_at: "",
        isCompleted: "",
        isDeleted: "",
        isImportant: "",
        completedby_id: ""
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

/***/ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/app_pages/Taskaev.vue?vue&type=script&lang=js&":
/*!***************************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib??ref--4-0!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/src/views/app_pages/Taskaev.vue?vue&type=script&lang=js& ***!
  \***************************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var C_dianasoft_serviciiweb_web_node_modules_babel_runtime_helpers_esm_defineProperty_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./node_modules/@babel/runtime/helpers/esm/defineProperty.js */ "./node_modules/@babel/runtime/helpers/esm/defineProperty.js");
/* harmony import */ var vue_ripple_directive__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! vue-ripple-directive */ "./node_modules/vue-ripple-directive/src/ripple.js");
/* harmony import */ var _core_mixins_ui_transition__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @core/mixins/ui/transition */ "./resources/js/src/@core/mixins/ui/transition.js");
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
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
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
  components: {},
  directives: {
    "b-modal": bootstrap_vue__WEBPACK_IMPORTED_MODULE_3__["VBModal"],
    Ripple: vue_ripple_directive__WEBPACK_IMPORTED_MODULE_1__["default"]
  },
  name: "taskaev",
  data: function data() {
    return {
      rutainapoiLocal: this.rutainapoi,
      activeEditLocal: false,
      activeActionLocal: this.activeAction,
      editVarLocal: this.editVar,
      nextTodoId: 2,
      modelName: "task",
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
    modificaCompletedBy: function modificaCompletedBy() {
      if (this.editVarLocal.completedby) {
        this.editVarLocal.completedby_id = this.editVarLocal.completedby.id;
      } else {
        this.editVarLocal.completedby_id = null;
      }
    },
    modificaAssignedTo: function modificaAssignedTo() {
      if (this.editVarLocal.assignedto) {
        this.editVarLocal.assignedto_id = this.editVarLocal.assignedto.id;
      } else {
        this.editVarLocal.assignedto_id = null;
      }
    },
    modificaAssignedBy: function modificaAssignedBy() {
      if (this.editVarLocal.assignedby) {
        this.editVarLocal.assignedby_id = this.editVarLocal.assignedby.id;
      } else {
        this.editVarLocal.assignedby_id = null;
      }
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
        _this2.$emit("stored", "");

        _this2.editVarLocal = {
          assignedby_id: '',
          assignedto_id: '',
          title: '',
          description: '',
          duedate: '',
          tags: '',
          completed_at: '',
          isCompleted: '',
          isDeleted: '',
          isImportant: '',
          completedby_id: ''
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
        assignedby_id: "",
        assignedto_id: "",
        title: "",
        description: "",
        duedate: "",
        tags: "",
        completed_at: "",
        isCompleted: "",
        isDeleted: "",
        isImportant: "",
        completedby_id: ""
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
        _this3.$emit("stored", "");

        _this3.activeEditLocal = false;
        _this3.activeActionLocal = "";
        _this3.editVarLocal = {
          assignedby_id: "",
          assignedto_id: "",
          title: "",
          description: "",
          duedate: "",
          tags: "",
          completed_at: "",
          isCompleted: "",
          isDeleted: "",
          isImportant: "",
          completedby_id: ""
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

/***/ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/app_pages/Task.vue?vue&type=template&id=151d7c78&":
/*!****************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/src/views/app_pages/Task.vue?vue&type=template&id=151d7c78& ***!
  \****************************************************************************************************************************************************************************************************************/
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
      _vm._v(" "),
      _c("Taskaev", {
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

/***/ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/app_pages/Taskaev.vue?vue&type=template&id=c1ac46ec&":
/*!*******************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/src/views/app_pages/Taskaev.vue?vue&type=template&id=c1ac46ec& ***!
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
            "modal-class": "modal-success",
            title: _vm.activeActionLocal + " Task",
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
              _c(
                "b-col",
                { attrs: { cols: "2" } },
                [
                  _c("selectoneuser", {
                    attrs: {
                      labelDisplay: "Assigned By",
                      readonly: _vm.activeActionLocal == "Vizualizează",
                    },
                    on: { change: _vm.modificaAssignedBy },
                    model: {
                      value: _vm.editVarLocal.assignedby,
                      callback: function ($$v) {
                        _vm.$set(_vm.editVarLocal, "assignedby", $$v)
                      },
                      expression: "editVarLocal.assignedby",
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
                  _c("selectoneuser", {
                    attrs: {
                      labelDisplay: "Assigned To",
                      readonly: _vm.activeActionLocal == "Vizualizează",
                    },
                    on: { change: _vm.modificaAssignedTo },
                    model: {
                      value: _vm.editVarLocal.assignedto,
                      callback: function ($$v) {
                        _vm.$set(_vm.editVarLocal, "assignedto", $$v)
                      },
                      expression: "editVarLocal.assignedto",
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
                      attrs: {
                        readonly: _vm.activeActionLocal == "Vizualizează",
                        id: "title",
                        placeholder: "Titlu",
                      },
                      model: {
                        value: _vm.editVarLocal.title,
                        callback: function ($$v) {
                          _vm.$set(_vm.editVarLocal, "title", $$v)
                        },
                        expression: "editVarLocal.title",
                      },
                    }),
                    _vm._v(" "),
                    _c("label", { attrs: { for: "title" } }, [_vm._v("Titlu")]),
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
                    _c("b-form-textarea", {
                      attrs: {
                        id: ".description",
                        rows: "3",
                        placeholder: ".Descriere",
                      },
                      model: {
                        value: _vm.editVarLocal.description,
                        callback: function ($$v) {
                          _vm.$set(_vm.editVarLocal, "description", $$v)
                        },
                        expression: "editVarLocal.description",
                      },
                    }),
                    _vm._v(" "),
                    _c("label", { attrs: { for: "label-.description" } }, [
                      _vm._v(".Descriere"),
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
                  _c("datasiora", {
                    attrs: {
                      readonly: _vm.activeActionLocal == "Vizualizează",
                      id: "duedate",
                      name: "duedate",
                      campDisplay: "Termen executare",
                    },
                    model: {
                      value: _vm.editVarLocal.duedate,
                      callback: function ($$v) {
                        _vm.$set(_vm.editVarLocal, "duedate", $$v)
                      },
                      expression: "editVarLocal.duedate",
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
                      attrs: {
                        readonly: _vm.activeActionLocal == "Vizualizează",
                        id: "tags",
                        placeholder: "Tags",
                      },
                      model: {
                        value: _vm.editVarLocal.tags,
                        callback: function ($$v) {
                          _vm.$set(_vm.editVarLocal, "tags", $$v)
                        },
                        expression: "editVarLocal.tags",
                      },
                    }),
                    _vm._v(" "),
                    _c("label", { attrs: { for: "tags" } }, [_vm._v("Tags")]),
                  ],
                  1
                ),
              ]),
              _vm._v(" "),
              _c(
                "b-col",
                { attrs: { cols: "2" } },
                [
                  _c("datasiora", {
                    attrs: {
                      readonly: _vm.activeActionLocal == "Vizualizează",
                      id: "completed_at",
                      name: "completed_at",
                      campDisplay: "Data executarii",
                    },
                    model: {
                      value: _vm.editVarLocal.completed_at,
                      callback: function ($$v) {
                        _vm.$set(_vm.editVarLocal, "completed_at", $$v)
                      },
                      expression: "editVarLocal.completed_at",
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
                  _c(
                    "b-form-checkbox",
                    {
                      staticClass: "custom-control-primary",
                      model: {
                        value: _vm.editVarLocal.isCompleted,
                        callback: function ($$v) {
                          _vm.$set(_vm.editVarLocal, "isCompleted", $$v)
                        },
                        expression: "editVarLocal.isCompleted",
                      },
                    },
                    [
                      _vm._v(
                        "\n                                        Executat\n                                      "
                      ),
                    ]
                  ),
                ],
                1
              ),
              _vm._v(" "),
              _c(
                "b-col",
                { attrs: { cols: "2" } },
                [
                  _c(
                    "b-form-checkbox",
                    {
                      staticClass: "custom-control-primary",
                      model: {
                        value: _vm.editVarLocal.isDeleted,
                        callback: function ($$v) {
                          _vm.$set(_vm.editVarLocal, "isDeleted", $$v)
                        },
                        expression: "editVarLocal.isDeleted",
                      },
                    },
                    [
                      _vm._v(
                        "\n                                        Sters\n                                      "
                      ),
                    ]
                  ),
                ],
                1
              ),
              _vm._v(" "),
              _c(
                "b-col",
                { attrs: { cols: "2" } },
                [
                  _c(
                    "b-form-checkbox",
                    {
                      staticClass: "custom-control-primary",
                      model: {
                        value: _vm.editVarLocal.isImportant,
                        callback: function ($$v) {
                          _vm.$set(_vm.editVarLocal, "isImportant", $$v)
                        },
                        expression: "editVarLocal.isImportant",
                      },
                    },
                    [
                      _vm._v(
                        "\n                                        Important\n                                      "
                      ),
                    ]
                  ),
                ],
                1
              ),
              _vm._v(" "),
              _c(
                "b-col",
                { attrs: { cols: "2" } },
                [
                  _c("selectoneuser", {
                    attrs: {
                      labelDisplay: "Executat de catre",
                      readonly: _vm.activeActionLocal == "Vizualizează",
                    },
                    on: { change: _vm.modificaCompletedBy },
                    model: {
                      value: _vm.editVarLocal.completedby,
                      callback: function ($$v) {
                        _vm.$set(_vm.editVarLocal, "completedby", $$v)
                      },
                      expression: "editVarLocal.completedby",
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
    ],
    1
  )
}
var staticRenderFns = []
render._withStripped = true



/***/ }),

/***/ "./resources/js/src/views/app_pages/Task.vue":
/*!***************************************************!*\
  !*** ./resources/js/src/views/app_pages/Task.vue ***!
  \***************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _Task_vue_vue_type_template_id_151d7c78___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./Task.vue?vue&type=template&id=151d7c78& */ "./resources/js/src/views/app_pages/Task.vue?vue&type=template&id=151d7c78&");
/* harmony import */ var _Task_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./Task.vue?vue&type=script&lang=js& */ "./resources/js/src/views/app_pages/Task.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport *//* harmony import */ var _node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../../../../node_modules/vue-loader/lib/runtime/componentNormalizer.js */ "./node_modules/vue-loader/lib/runtime/componentNormalizer.js");





/* normalize component */

var component = Object(_node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__["default"])(
  _Task_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__["default"],
  _Task_vue_vue_type_template_id_151d7c78___WEBPACK_IMPORTED_MODULE_0__["render"],
  _Task_vue_vue_type_template_id_151d7c78___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"],
  false,
  null,
  null,
  null
  
)

/* hot reload */
if (false) { var api; }
component.options.__file = "resources/js/src/views/app_pages/Task.vue"
/* harmony default export */ __webpack_exports__["default"] = (component.exports);

/***/ }),

/***/ "./resources/js/src/views/app_pages/Task.vue?vue&type=script&lang=js&":
/*!****************************************************************************!*\
  !*** ./resources/js/src/views/app_pages/Task.vue?vue&type=script&lang=js& ***!
  \****************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_Task_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../node_modules/babel-loader/lib??ref--4-0!../../../../../node_modules/vue-loader/lib??vue-loader-options!./Task.vue?vue&type=script&lang=js& */ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/app_pages/Task.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__["default"] = (_node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_Task_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__["default"]); 

/***/ }),

/***/ "./resources/js/src/views/app_pages/Task.vue?vue&type=template&id=151d7c78&":
/*!**********************************************************************************!*\
  !*** ./resources/js/src/views/app_pages/Task.vue?vue&type=template&id=151d7c78& ***!
  \**********************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_Task_vue_vue_type_template_id_151d7c78___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!../../../../../node_modules/vue-loader/lib??vue-loader-options!./Task.vue?vue&type=template&id=151d7c78& */ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/app_pages/Task.vue?vue&type=template&id=151d7c78&");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "render", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_Task_vue_vue_type_template_id_151d7c78___WEBPACK_IMPORTED_MODULE_0__["render"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "staticRenderFns", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_Task_vue_vue_type_template_id_151d7c78___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"]; });



/***/ }),

/***/ "./resources/js/src/views/app_pages/Taskaev.vue":
/*!******************************************************!*\
  !*** ./resources/js/src/views/app_pages/Taskaev.vue ***!
  \******************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _Taskaev_vue_vue_type_template_id_c1ac46ec___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./Taskaev.vue?vue&type=template&id=c1ac46ec& */ "./resources/js/src/views/app_pages/Taskaev.vue?vue&type=template&id=c1ac46ec&");
/* harmony import */ var _Taskaev_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./Taskaev.vue?vue&type=script&lang=js& */ "./resources/js/src/views/app_pages/Taskaev.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport *//* harmony import */ var _node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../../../../node_modules/vue-loader/lib/runtime/componentNormalizer.js */ "./node_modules/vue-loader/lib/runtime/componentNormalizer.js");





/* normalize component */

var component = Object(_node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__["default"])(
  _Taskaev_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__["default"],
  _Taskaev_vue_vue_type_template_id_c1ac46ec___WEBPACK_IMPORTED_MODULE_0__["render"],
  _Taskaev_vue_vue_type_template_id_c1ac46ec___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"],
  false,
  null,
  null,
  null
  
)

/* hot reload */
if (false) { var api; }
component.options.__file = "resources/js/src/views/app_pages/Taskaev.vue"
/* harmony default export */ __webpack_exports__["default"] = (component.exports);

/***/ }),

/***/ "./resources/js/src/views/app_pages/Taskaev.vue?vue&type=script&lang=js&":
/*!*******************************************************************************!*\
  !*** ./resources/js/src/views/app_pages/Taskaev.vue?vue&type=script&lang=js& ***!
  \*******************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_Taskaev_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../node_modules/babel-loader/lib??ref--4-0!../../../../../node_modules/vue-loader/lib??vue-loader-options!./Taskaev.vue?vue&type=script&lang=js& */ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/app_pages/Taskaev.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__["default"] = (_node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_Taskaev_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__["default"]); 

/***/ }),

/***/ "./resources/js/src/views/app_pages/Taskaev.vue?vue&type=template&id=c1ac46ec&":
/*!*************************************************************************************!*\
  !*** ./resources/js/src/views/app_pages/Taskaev.vue?vue&type=template&id=c1ac46ec& ***!
  \*************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_Taskaev_vue_vue_type_template_id_c1ac46ec___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!../../../../../node_modules/vue-loader/lib??vue-loader-options!./Taskaev.vue?vue&type=template&id=c1ac46ec& */ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/app_pages/Taskaev.vue?vue&type=template&id=c1ac46ec&");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "render", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_Taskaev_vue_vue_type_template_id_c1ac46ec___WEBPACK_IMPORTED_MODULE_0__["render"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "staticRenderFns", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_Taskaev_vue_vue_type_template_id_c1ac46ec___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"]; });



/***/ })

}]);