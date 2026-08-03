(window["webpackJsonp"] = window["webpackJsonp"] || []).push([[176],{

/***/ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/app_pages/Documentepdfarhiva.vue?vue&type=script&lang=js&":
/*!**************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib??ref--4-0!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/src/views/app_pages/Documentepdfarhiva.vue?vue&type=script&lang=js& ***!
  \**************************************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var core_js_modules_es_regexp_exec_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! core-js/modules/es.regexp.exec.js */ "./node_modules/core-js/modules/es.regexp.exec.js");
/* harmony import */ var core_js_modules_es_regexp_exec_js__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_es_regexp_exec_js__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var core_js_modules_es_string_replace_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! core-js/modules/es.string.replace.js */ "./node_modules/core-js/modules/es.string.replace.js");
/* harmony import */ var core_js_modules_es_string_replace_js__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_es_string_replace_js__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var core_js_modules_es_string_replace_all_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! core-js/modules/es.string.replace-all.js */ "./node_modules/core-js/modules/es.string.replace-all.js");
/* harmony import */ var core_js_modules_es_string_replace_all_js__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_es_string_replace_all_js__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _Documentepdfaev_vue__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./Documentepdfaev.vue */ "./resources/js/src/views/app_pages/Documentepdfaev.vue");



//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
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
    Documentepdfaev: _Documentepdfaev_vue__WEBPACK_IMPORTED_MODULE_3__["default"]
  },
  name: "documentepdfarhiva",
  data: function data() {
    return {
      refreshLocal: false,
      idselectat: null,
      campFiltruStart: "",
      modelName: "documentepdfarhiva?id=" + this.$route.query.id,
      modelDisplayName: "Arhiva documente",
      editVar: {
        grupa: "",
        denumire: "",
        descriere: "",
        fisier: "",
        data: "",
        acces: ""
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
        label: "Grupa",
        field: "grupa",
        width: "300px",
        type: "text",
        showSortAsc: true
      }, {
        label: "Denumire",
        field: "denumire",
        width: "500px",
        type: "text",
        showSortAsc: true
      }, {
        label: "Data ultimei revizii",
        field: "data",
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
        label: "Acces",
        field: "acces",
        width: "200px",
        type: "text",
        showSortAsc: true
      }, {
        label: "Status",
        field: "status",
        width: "150px",
        type: "text",
        showSortAsc: true
      }]
    };
  },
  watch: {
    id: function id() {
      this.modelName = "documentepdfarhiva?id=" + this.$route.query.id;
    }
  },
  methods: {
    inapoi: function inapoi() {
      this.$router.push({
        name: 'documentepdf'
      });
    },
    aevClosed: function aevClosed() {
      this.idselectat = null;
      this.selectedID = "";
      this.activeEdit = false;
      this.editVar = {
        grupa: "",
        denumire: "",
        descriere: "",
        fisier: "",
        data: "",
        acces: ""
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
        grupa: "",
        denumire: "",
        descriere: "",
        fisier: "",
        data: "",
        acces: ""
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
      var _this = this;

      this.editVar = Object.assign({}, this.selectedID);
      this.showLoading = true;
      var payLoad = Object.assign({}, this.editVar);
      payLoad.requestType = "post";
      payLoad.requestUrl = "documentepdf/show";
      this.$store.dispatch("app/api_blob_Request", payLoad).then(function (response) {
        _this.$router.push({
          name: 'pdfviewer',
          params: {
            blobPDF: response,
            numefis: _this.numefis + "_" + new Date().toLocaleDateString().replaceAll(".", "_") + ".pdf",
            rutainapoi: "documentepdf"
          }
        });

        _this.showLoading = false;
      });
    }
  },
  created: function created() {
    document.title = window.app_name + "->" + this.modelDisplayName; // if(this.id!=null){
    //             this.idselectat=this.id
    //             this.campFiltruStart="id"
    // }
    //   this.listen()
  }
});

/***/ }),

/***/ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/app_pages/Documentepdfarhiva.vue?vue&type=template&id=63e3f250&":
/*!******************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/src/views/app_pages/Documentepdfarhiva.vue?vue&type=template&id=63e3f250& ***!
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
                        "b-button",
                        {
                          directives: [
                            {
                              name: "b-tooltip",
                              rawName: "v-b-tooltip.hover.v-light",
                              modifiers: { hover: true, "v-light": true },
                            },
                          ],
                          staticClass: "btn-icon  mr-1",
                          attrs: {
                            variant: "outline-info",
                            size: "",
                            title: "Vizualizează înregistrarea selectată",
                          },
                          on: { click: _vm.view },
                        },
                        [_c("feather-icon", { attrs: { icon: "EyeIcon" } })],
                        1
                      ),
                      _vm._v(" "),
                      _c(
                        "b-button",
                        {
                          directives: [
                            {
                              name: "b-tooltip",
                              rawName: "v-b-tooltip.hover.v-light",
                              modifiers: { hover: true, "v-light": true },
                            },
                          ],
                          staticClass: "btn-icon  mr-1",
                          attrs: {
                            variant: "outline-info",
                            size: "",
                            title: "Inapoi",
                          },
                          on: { click: _vm.inapoi },
                        },
                        [
                          _c("feather-icon", {
                            attrs: { icon: "CornerLeftDownIcon" },
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

/***/ "./resources/js/src/views/app_pages/Documentepdfarhiva.vue":
/*!*****************************************************************!*\
  !*** ./resources/js/src/views/app_pages/Documentepdfarhiva.vue ***!
  \*****************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _Documentepdfarhiva_vue_vue_type_template_id_63e3f250___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./Documentepdfarhiva.vue?vue&type=template&id=63e3f250& */ "./resources/js/src/views/app_pages/Documentepdfarhiva.vue?vue&type=template&id=63e3f250&");
/* harmony import */ var _Documentepdfarhiva_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./Documentepdfarhiva.vue?vue&type=script&lang=js& */ "./resources/js/src/views/app_pages/Documentepdfarhiva.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport *//* harmony import */ var _node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../../../../node_modules/vue-loader/lib/runtime/componentNormalizer.js */ "./node_modules/vue-loader/lib/runtime/componentNormalizer.js");





/* normalize component */

var component = Object(_node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__["default"])(
  _Documentepdfarhiva_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__["default"],
  _Documentepdfarhiva_vue_vue_type_template_id_63e3f250___WEBPACK_IMPORTED_MODULE_0__["render"],
  _Documentepdfarhiva_vue_vue_type_template_id_63e3f250___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"],
  false,
  null,
  null,
  null
  
)

/* hot reload */
if (false) { var api; }
component.options.__file = "resources/js/src/views/app_pages/Documentepdfarhiva.vue"
/* harmony default export */ __webpack_exports__["default"] = (component.exports);

/***/ }),

/***/ "./resources/js/src/views/app_pages/Documentepdfarhiva.vue?vue&type=script&lang=js&":
/*!******************************************************************************************!*\
  !*** ./resources/js/src/views/app_pages/Documentepdfarhiva.vue?vue&type=script&lang=js& ***!
  \******************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_Documentepdfarhiva_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../node_modules/babel-loader/lib??ref--4-0!../../../../../node_modules/vue-loader/lib??vue-loader-options!./Documentepdfarhiva.vue?vue&type=script&lang=js& */ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/app_pages/Documentepdfarhiva.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__["default"] = (_node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_Documentepdfarhiva_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__["default"]); 

/***/ }),

/***/ "./resources/js/src/views/app_pages/Documentepdfarhiva.vue?vue&type=template&id=63e3f250&":
/*!************************************************************************************************!*\
  !*** ./resources/js/src/views/app_pages/Documentepdfarhiva.vue?vue&type=template&id=63e3f250& ***!
  \************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_Documentepdfarhiva_vue_vue_type_template_id_63e3f250___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!../../../../../node_modules/vue-loader/lib??vue-loader-options!./Documentepdfarhiva.vue?vue&type=template&id=63e3f250& */ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/app_pages/Documentepdfarhiva.vue?vue&type=template&id=63e3f250&");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "render", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_Documentepdfarhiva_vue_vue_type_template_id_63e3f250___WEBPACK_IMPORTED_MODULE_0__["render"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "staticRenderFns", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_Documentepdfarhiva_vue_vue_type_template_id_63e3f250___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"]; });



/***/ })

}]);