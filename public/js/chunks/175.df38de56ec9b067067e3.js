(window["webpackJsonp"] = window["webpackJsonp"] || []).push([[175],{

/***/ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/app_pages/Datefirmeregcom.vue?vue&type=script&lang=js&":
/*!***********************************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib??ref--4-0!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/src/views/app_pages/Datefirmeregcom.vue?vue&type=script&lang=js& ***!
  \***********************************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
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
  components: {},
  name: "datefirmeregcomComponent",
  data: function data() {
    return {
      refreshLocal: false,
      idselectat: null,
      campFiltruStart: "",
      modelName: "datefirmeregcom",
      modelDisplayName: "Date firme registrul comertului",
      editVar: {},
      activeEdit: false,
      activeAction: "",
      selectedID: "",
      columnDefs: [{
        label: "Denumire",
        field: "denumire",
        width: 500,
        filter: true,
        checkboxSelection: false,
        headerCheckboxSelectionFilteredOnly: false,
        headerCheckboxSelection: false,
        pinned: "left"
      }, {
        label: "Cod fiscal",
        field: "cui",
        width: 200,
        filter: true,
        checkboxSelection: false,
        headerCheckboxSelectionFilteredOnly: false,
        headerCheckboxSelection: false,
        pinned: "left"
      }, {
        label: "Reg Com",
        field: "regcom",
        width: 200,
        filter: true,
        checkboxSelection: false,
        headerCheckboxSelectionFilteredOnly: false,
        headerCheckboxSelection: false
      }, {
        label: "Adresa",
        field: "adresa",
        width: 500,
        filter: true,
        checkboxSelection: false,
        headerCheckboxSelectionFilteredOnly: false,
        headerCheckboxSelection: false
      }, {
        label: "Localitate",
        field: "localitate",
        width: 200,
        filter: true,
        checkboxSelection: false,
        headerCheckboxSelectionFilteredOnly: false,
        headerCheckboxSelection: false
      }, {
        label: "Judet",
        field: "judet",
        width: 200,
        filter: true,
        checkboxSelection: false,
        headerCheckboxSelectionFilteredOnly: false,
        headerCheckboxSelection: false
      }, {
        label: "Telefon",
        field: "telefon",
        width: 200,
        filter: true,
        checkboxSelection: false,
        headerCheckboxSelectionFilteredOnly: false,
        headerCheckboxSelection: false
      }, {
        label: "Email",
        field: "email",
        width: 200,
        filter: true,
        checkboxSelection: false,
        headerCheckboxSelectionFilteredOnly: false,
        headerCheckboxSelection: false
      }]
    };
  },
  methods: {
    aevClosed: function aevClosed() {
      this.activeEdit = false;
      this.editVar = {};
      this.activeAction = "";
    },
    listen: function listen() {// Echo.channel("bancomatic_dianasoft")
      //     .listen("UserSignedUp", (e) => {
      //        // this.getRecords()
      //        // console.log("AM PRIMIT")
      //      });
    },
    onSelectionChanged: function onSelectionChanged(value) {
      this.selectedID = value;
    },
    add: function add() {},
    edit: function edit() {},
    view: function view() {},
    importXLS: function importXLS(e) {
      var _this = this;

      this.showLoading = true;
      e.preventDefault();
      this.uploadFileActive = false;
      var currentObj = this;
      var config = {
        headers: {
          'content-type': 'multipart/form-data'
        }
      };
      var formData = new FormData();
      formData.append('file', this.file);
      axios.post('/' + this.modelName + '/import', formData, config).then(function (response) {
        _this.tblRecords = response;
        _this.file = '';
        _this.showLoading = false;

        _this.$vs.notify({
          title: "Upload efectuat cu succes!",
          text: "Upload efectuat cu succes!",
          iconPack: "feather",
          icon: "icon-check",
          color: "success"
        });
      })["catch"](function (error) {
        _this.showLoading = false;

        _this.handleErrors(error);
      });
    }
  },
  created: function created() {
    document.title = window.app_name + "->Date firme";

    if (this.id != null) {
      this.idselectat = this.id;
      this.campFiltruStart = "id";
    }

    this.listen();
  }
});

/***/ }),

/***/ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/app_pages/Datefirmeregcom.vue?vue&type=template&id=068c7a83&":
/*!***************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/src/views/app_pages/Datefirmeregcom.vue?vue&type=template&id=068c7a83& ***!
  \***************************************************************************************************************************************************************************************************************************/
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
  return _c("div", { staticClass: "d-flex justify-content-center" }, [
    _c(
      "div",
      [
        _c(
          "b-card",
          [
            _c("tabelcomponent", {
              attrs: {
                columnDefs: _vm.columnDefs,
                modelName: _vm.modelName,
                titlu: _vm.modelDisplayName,
                refresh: _vm.refreshLocal,
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
  ])
}
var staticRenderFns = []
render._withStripped = true



/***/ }),

/***/ "./resources/js/src/views/app_pages/Datefirmeregcom.vue":
/*!**************************************************************!*\
  !*** ./resources/js/src/views/app_pages/Datefirmeregcom.vue ***!
  \**************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _Datefirmeregcom_vue_vue_type_template_id_068c7a83___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./Datefirmeregcom.vue?vue&type=template&id=068c7a83& */ "./resources/js/src/views/app_pages/Datefirmeregcom.vue?vue&type=template&id=068c7a83&");
/* harmony import */ var _Datefirmeregcom_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./Datefirmeregcom.vue?vue&type=script&lang=js& */ "./resources/js/src/views/app_pages/Datefirmeregcom.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport *//* harmony import */ var _node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../../../../node_modules/vue-loader/lib/runtime/componentNormalizer.js */ "./node_modules/vue-loader/lib/runtime/componentNormalizer.js");





/* normalize component */

var component = Object(_node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__["default"])(
  _Datefirmeregcom_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__["default"],
  _Datefirmeregcom_vue_vue_type_template_id_068c7a83___WEBPACK_IMPORTED_MODULE_0__["render"],
  _Datefirmeregcom_vue_vue_type_template_id_068c7a83___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"],
  false,
  null,
  null,
  null
  
)

/* hot reload */
if (false) { var api; }
component.options.__file = "resources/js/src/views/app_pages/Datefirmeregcom.vue"
/* harmony default export */ __webpack_exports__["default"] = (component.exports);

/***/ }),

/***/ "./resources/js/src/views/app_pages/Datefirmeregcom.vue?vue&type=script&lang=js&":
/*!***************************************************************************************!*\
  !*** ./resources/js/src/views/app_pages/Datefirmeregcom.vue?vue&type=script&lang=js& ***!
  \***************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_Datefirmeregcom_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../node_modules/babel-loader/lib??ref--4-0!../../../../../node_modules/vue-loader/lib??vue-loader-options!./Datefirmeregcom.vue?vue&type=script&lang=js& */ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/app_pages/Datefirmeregcom.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__["default"] = (_node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_Datefirmeregcom_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__["default"]); 

/***/ }),

/***/ "./resources/js/src/views/app_pages/Datefirmeregcom.vue?vue&type=template&id=068c7a83&":
/*!*********************************************************************************************!*\
  !*** ./resources/js/src/views/app_pages/Datefirmeregcom.vue?vue&type=template&id=068c7a83& ***!
  \*********************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_Datefirmeregcom_vue_vue_type_template_id_068c7a83___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!../../../../../node_modules/vue-loader/lib??vue-loader-options!./Datefirmeregcom.vue?vue&type=template&id=068c7a83& */ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/app_pages/Datefirmeregcom.vue?vue&type=template&id=068c7a83&");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "render", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_Datefirmeregcom_vue_vue_type_template_id_068c7a83___WEBPACK_IMPORTED_MODULE_0__["render"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "staticRenderFns", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_Datefirmeregcom_vue_vue_type_template_id_068c7a83___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"]; });



/***/ })

}]);