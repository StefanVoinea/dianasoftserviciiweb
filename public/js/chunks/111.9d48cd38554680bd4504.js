(window["webpackJsonp"] = window["webpackJsonp"] || []).push([[111],{

/***/ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/extensions/clipboard/Clipboard.vue?vue&type=script&lang=js&":
/*!****************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib??ref--4-0!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/src/views/extensions/clipboard/Clipboard.vue?vue&type=script&lang=js& ***!
  \****************************************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _ClipboardWithDirective_vue__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./ClipboardWithDirective.vue */ "./resources/js/src/views/extensions/clipboard/ClipboardWithDirective.vue");
/* harmony import */ var _ClipboardWithoutDirective_vue__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./ClipboardWithoutDirective.vue */ "./resources/js/src/views/extensions/clipboard/ClipboardWithoutDirective.vue");
//
//
//
//
//
//
//


/* harmony default export */ __webpack_exports__["default"] = ({
  components: {
    ClipboardWithDirective: _ClipboardWithDirective_vue__WEBPACK_IMPORTED_MODULE_0__["default"],
    ClipboardWithoutDirective: _ClipboardWithoutDirective_vue__WEBPACK_IMPORTED_MODULE_1__["default"]
  }
});

/***/ }),

/***/ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/extensions/clipboard/ClipboardWithDirective.vue?vue&type=script&lang=js&":
/*!*****************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib??ref--4-0!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/src/views/extensions/clipboard/ClipboardWithDirective.vue?vue&type=script&lang=js& ***!
  \*****************************************************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _core_components_b_card_code_BCardCode_vue__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @core/components/b-card-code/BCardCode.vue */ "./resources/js/src/@core/components/b-card-code/BCardCode.vue");
/* harmony import */ var bootstrap_vue__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! bootstrap-vue */ "./node_modules/bootstrap-vue/esm/index.js");
/* harmony import */ var _core_components_toastification_ToastificationContent_vue__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @core/components/toastification/ToastificationContent.vue */ "./resources/js/src/@core/components/toastification/ToastificationContent.vue");
/* harmony import */ var vue_ripple_directive__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! vue-ripple-directive */ "./node_modules/vue-ripple-directive/src/ripple.js");
/* harmony import */ var _code__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ./code */ "./resources/js/src/views/extensions/clipboard/code.js");
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
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
  components: {
    BCardCode: _core_components_b_card_code_BCardCode_vue__WEBPACK_IMPORTED_MODULE_0__["default"],
    BFormInput: bootstrap_vue__WEBPACK_IMPORTED_MODULE_1__["BFormInput"],
    BFormGroup: bootstrap_vue__WEBPACK_IMPORTED_MODULE_1__["BFormGroup"],
    BButton: bootstrap_vue__WEBPACK_IMPORTED_MODULE_1__["BButton"],
    BCardText: bootstrap_vue__WEBPACK_IMPORTED_MODULE_1__["BCardText"],
    // eslint-disable-next-line vue/no-unused-components
    ToastificationContent: _core_components_toastification_ToastificationContent_vue__WEBPACK_IMPORTED_MODULE_2__["default"]
  },
  directives: {
    Ripple: vue_ripple_directive__WEBPACK_IMPORTED_MODULE_3__["default"]
  },
  data: function data() {
    return {
      message: 'Copy Me!',
      codeDirective: _code__WEBPACK_IMPORTED_MODULE_4__["codeDirective"]
    };
  },
  methods: {
    onCopy: function onCopy() {
      this.$toast({
        component: _core_components_toastification_ToastificationContent_vue__WEBPACK_IMPORTED_MODULE_2__["default"],
        props: {
          title: 'Text copied',
          icon: 'BellIcon'
        }
      });
    },
    onError: function onError() {
      this.$toast({
        component: _core_components_toastification_ToastificationContent_vue__WEBPACK_IMPORTED_MODULE_2__["default"],
        props: {
          title: 'Failed to copy texts!',
          icon: 'BellIcon'
        }
      });
    }
  }
});

/***/ }),

/***/ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/extensions/clipboard/ClipboardWithoutDirective.vue?vue&type=script&lang=js&":
/*!********************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib??ref--4-0!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/src/views/extensions/clipboard/ClipboardWithoutDirective.vue?vue&type=script&lang=js& ***!
  \********************************************************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _core_components_b_card_code_BCardCode_vue__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @core/components/b-card-code/BCardCode.vue */ "./resources/js/src/@core/components/b-card-code/BCardCode.vue");
/* harmony import */ var bootstrap_vue__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! bootstrap-vue */ "./node_modules/bootstrap-vue/esm/index.js");
/* harmony import */ var _core_components_toastification_ToastificationContent_vue__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @core/components/toastification/ToastificationContent.vue */ "./resources/js/src/@core/components/toastification/ToastificationContent.vue");
/* harmony import */ var vue_ripple_directive__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! vue-ripple-directive */ "./node_modules/vue-ripple-directive/src/ripple.js");
/* harmony import */ var _code__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ./code */ "./resources/js/src/views/extensions/clipboard/code.js");
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
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
  components: {
    BCardCode: _core_components_b_card_code_BCardCode_vue__WEBPACK_IMPORTED_MODULE_0__["default"],
    BFormInput: bootstrap_vue__WEBPACK_IMPORTED_MODULE_1__["BFormInput"],
    BFormGroup: bootstrap_vue__WEBPACK_IMPORTED_MODULE_1__["BFormGroup"],
    BButton: bootstrap_vue__WEBPACK_IMPORTED_MODULE_1__["BButton"],
    BCardText: bootstrap_vue__WEBPACK_IMPORTED_MODULE_1__["BCardText"],
    // eslint-disable-next-line vue/no-unused-components
    ToastificationContent: _core_components_toastification_ToastificationContent_vue__WEBPACK_IMPORTED_MODULE_2__["default"]
  },
  directives: {
    Ripple: vue_ripple_directive__WEBPACK_IMPORTED_MODULE_3__["default"]
  },
  data: function data() {
    return {
      message1: 'Copy Me Without Directive',
      codeWithoutDirective: _code__WEBPACK_IMPORTED_MODULE_4__["codeWithoutDirective"]
    };
  },
  methods: {
    doCopy: function doCopy() {
      var _this = this;

      this.$copyText(this.message1).then(function () {
        _this.$toast({
          component: _core_components_toastification_ToastificationContent_vue__WEBPACK_IMPORTED_MODULE_2__["default"],
          props: {
            title: 'Text copied',
            icon: 'BellIcon'
          }
        });
      }, function () {
        _this.$toast({
          component: _core_components_toastification_ToastificationContent_vue__WEBPACK_IMPORTED_MODULE_2__["default"],
          props: {
            title: 'Can not copy!',
            icon: 'BellIcon'
          }
        });
      });
    }
  }
});

/***/ }),

/***/ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/extensions/clipboard/Clipboard.vue?vue&type=template&id=57c52e4c&":
/*!********************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/src/views/extensions/clipboard/Clipboard.vue?vue&type=template&id=57c52e4c& ***!
  \********************************************************************************************************************************************************************************************************************************/
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
    [
      _c("clipboard-with-directive"),
      _vm._v(" "),
      _c("clipboard-without-directive"),
    ],
    1
  )
}
var staticRenderFns = []
render._withStripped = true



/***/ }),

/***/ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/extensions/clipboard/ClipboardWithDirective.vue?vue&type=template&id=133a5626&":
/*!*********************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/src/views/extensions/clipboard/ClipboardWithDirective.vue?vue&type=template&id=133a5626& ***!
  \*********************************************************************************************************************************************************************************************************************************************/
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
    "b-card-code",
    {
      attrs: { title: "Using Directive" },
      scopedSlots: _vm._u([
        {
          key: "code",
          fn: function () {
            return [_vm._v("\n    " + _vm._s(_vm.codeDirective) + "\n  ")]
          },
          proxy: true,
        },
      ]),
    },
    [
      _c("b-card-text", [_vm._v("Use directive on button to copy text")]),
      _vm._v(" "),
      _c(
        "div",
        { staticClass: "d-flex" },
        [
          _c(
            "b-form-group",
            { staticClass: "mb-0 mr-1" },
            [
              _c("b-form-input", {
                model: {
                  value: _vm.message,
                  callback: function ($$v) {
                    _vm.message = $$v
                  },
                  expression: "message",
                },
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
                  name: "clipboard",
                  rawName: "v-clipboard:copy",
                  value: _vm.message,
                  expression: "message",
                  arg: "copy",
                },
                {
                  name: "clipboard",
                  rawName: "v-clipboard:success",
                  value: _vm.onCopy,
                  expression: "onCopy",
                  arg: "success",
                },
                {
                  name: "clipboard",
                  rawName: "v-clipboard:error",
                  value: _vm.onError,
                  expression: "onError",
                  arg: "error",
                },
                {
                  name: "ripple",
                  rawName: "v-ripple.400",
                  value: "rgba(186, 191, 199, 0.15)",
                  expression: "'rgba(186, 191, 199, 0.15)'",
                  modifiers: { 400: true },
                },
              ],
              attrs: { variant: "primary" },
            },
            [_vm._v("\n      Copy!\n    ")]
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

/***/ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/extensions/clipboard/ClipboardWithoutDirective.vue?vue&type=template&id=638f115a&":
/*!************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/src/views/extensions/clipboard/ClipboardWithoutDirective.vue?vue&type=template&id=638f115a& ***!
  \************************************************************************************************************************************************************************************************************************************************/
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
    "b-card-code",
    {
      attrs: { title: "Without directive" },
      scopedSlots: _vm._u([
        {
          key: "code",
          fn: function () {
            return [
              _vm._v("\n    " + _vm._s(_vm.codeWithoutDirective) + "\n  "),
            ]
          },
          proxy: true,
        },
      ]),
    },
    [
      _c("b-card-text", [
        _c("span", [
          _vm._v("You can copy text without a specific button. Use "),
        ]),
        _vm._v(" "),
        _c("code", [_vm._v("$copyText")]),
        _vm._v(" "),
        _c("span", [_vm._v(" to copy text when event got fired.")]),
      ]),
      _vm._v(" "),
      _c(
        "div",
        { staticClass: "d-flex" },
        [
          _c(
            "b-form-group",
            { staticClass: "mb-0 mr-1" },
            [
              _c("b-form-input", {
                model: {
                  value: _vm.message1,
                  callback: function ($$v) {
                    _vm.message1 = $$v
                  },
                  expression: "message1",
                },
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
                  name: "ripple",
                  rawName: "v-ripple.400",
                  value: "rgba(186, 191, 199, 0.15)",
                  expression: "'rgba(186, 191, 199, 0.15)'",
                  modifiers: { 400: true },
                },
              ],
              attrs: { variant: "primary" },
              on: { click: _vm.doCopy },
            },
            [_vm._v("\n      Copy!\n    ")]
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

/***/ "./resources/js/src/views/extensions/clipboard/Clipboard.vue":
/*!*******************************************************************!*\
  !*** ./resources/js/src/views/extensions/clipboard/Clipboard.vue ***!
  \*******************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _Clipboard_vue_vue_type_template_id_57c52e4c___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./Clipboard.vue?vue&type=template&id=57c52e4c& */ "./resources/js/src/views/extensions/clipboard/Clipboard.vue?vue&type=template&id=57c52e4c&");
/* harmony import */ var _Clipboard_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./Clipboard.vue?vue&type=script&lang=js& */ "./resources/js/src/views/extensions/clipboard/Clipboard.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport *//* harmony import */ var _node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../../../../../node_modules/vue-loader/lib/runtime/componentNormalizer.js */ "./node_modules/vue-loader/lib/runtime/componentNormalizer.js");





/* normalize component */

var component = Object(_node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__["default"])(
  _Clipboard_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__["default"],
  _Clipboard_vue_vue_type_template_id_57c52e4c___WEBPACK_IMPORTED_MODULE_0__["render"],
  _Clipboard_vue_vue_type_template_id_57c52e4c___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"],
  false,
  null,
  null,
  null
  
)

/* hot reload */
if (false) { var api; }
component.options.__file = "resources/js/src/views/extensions/clipboard/Clipboard.vue"
/* harmony default export */ __webpack_exports__["default"] = (component.exports);

/***/ }),

/***/ "./resources/js/src/views/extensions/clipboard/Clipboard.vue?vue&type=script&lang=js&":
/*!********************************************************************************************!*\
  !*** ./resources/js/src/views/extensions/clipboard/Clipboard.vue?vue&type=script&lang=js& ***!
  \********************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_Clipboard_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../../node_modules/babel-loader/lib??ref--4-0!../../../../../../node_modules/vue-loader/lib??vue-loader-options!./Clipboard.vue?vue&type=script&lang=js& */ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/extensions/clipboard/Clipboard.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__["default"] = (_node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_Clipboard_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__["default"]); 

/***/ }),

/***/ "./resources/js/src/views/extensions/clipboard/Clipboard.vue?vue&type=template&id=57c52e4c&":
/*!**************************************************************************************************!*\
  !*** ./resources/js/src/views/extensions/clipboard/Clipboard.vue?vue&type=template&id=57c52e4c& ***!
  \**************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_Clipboard_vue_vue_type_template_id_57c52e4c___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../../node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!../../../../../../node_modules/vue-loader/lib??vue-loader-options!./Clipboard.vue?vue&type=template&id=57c52e4c& */ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/extensions/clipboard/Clipboard.vue?vue&type=template&id=57c52e4c&");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "render", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_Clipboard_vue_vue_type_template_id_57c52e4c___WEBPACK_IMPORTED_MODULE_0__["render"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "staticRenderFns", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_Clipboard_vue_vue_type_template_id_57c52e4c___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"]; });



/***/ }),

/***/ "./resources/js/src/views/extensions/clipboard/ClipboardWithDirective.vue":
/*!********************************************************************************!*\
  !*** ./resources/js/src/views/extensions/clipboard/ClipboardWithDirective.vue ***!
  \********************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _ClipboardWithDirective_vue_vue_type_template_id_133a5626___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./ClipboardWithDirective.vue?vue&type=template&id=133a5626& */ "./resources/js/src/views/extensions/clipboard/ClipboardWithDirective.vue?vue&type=template&id=133a5626&");
/* harmony import */ var _ClipboardWithDirective_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./ClipboardWithDirective.vue?vue&type=script&lang=js& */ "./resources/js/src/views/extensions/clipboard/ClipboardWithDirective.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport *//* harmony import */ var _node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../../../../../node_modules/vue-loader/lib/runtime/componentNormalizer.js */ "./node_modules/vue-loader/lib/runtime/componentNormalizer.js");





/* normalize component */

var component = Object(_node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__["default"])(
  _ClipboardWithDirective_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__["default"],
  _ClipboardWithDirective_vue_vue_type_template_id_133a5626___WEBPACK_IMPORTED_MODULE_0__["render"],
  _ClipboardWithDirective_vue_vue_type_template_id_133a5626___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"],
  false,
  null,
  null,
  null
  
)

/* hot reload */
if (false) { var api; }
component.options.__file = "resources/js/src/views/extensions/clipboard/ClipboardWithDirective.vue"
/* harmony default export */ __webpack_exports__["default"] = (component.exports);

/***/ }),

/***/ "./resources/js/src/views/extensions/clipboard/ClipboardWithDirective.vue?vue&type=script&lang=js&":
/*!*********************************************************************************************************!*\
  !*** ./resources/js/src/views/extensions/clipboard/ClipboardWithDirective.vue?vue&type=script&lang=js& ***!
  \*********************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_ClipboardWithDirective_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../../node_modules/babel-loader/lib??ref--4-0!../../../../../../node_modules/vue-loader/lib??vue-loader-options!./ClipboardWithDirective.vue?vue&type=script&lang=js& */ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/extensions/clipboard/ClipboardWithDirective.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__["default"] = (_node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_ClipboardWithDirective_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__["default"]); 

/***/ }),

/***/ "./resources/js/src/views/extensions/clipboard/ClipboardWithDirective.vue?vue&type=template&id=133a5626&":
/*!***************************************************************************************************************!*\
  !*** ./resources/js/src/views/extensions/clipboard/ClipboardWithDirective.vue?vue&type=template&id=133a5626& ***!
  \***************************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_ClipboardWithDirective_vue_vue_type_template_id_133a5626___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../../node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!../../../../../../node_modules/vue-loader/lib??vue-loader-options!./ClipboardWithDirective.vue?vue&type=template&id=133a5626& */ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/extensions/clipboard/ClipboardWithDirective.vue?vue&type=template&id=133a5626&");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "render", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_ClipboardWithDirective_vue_vue_type_template_id_133a5626___WEBPACK_IMPORTED_MODULE_0__["render"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "staticRenderFns", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_ClipboardWithDirective_vue_vue_type_template_id_133a5626___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"]; });



/***/ }),

/***/ "./resources/js/src/views/extensions/clipboard/ClipboardWithoutDirective.vue":
/*!***********************************************************************************!*\
  !*** ./resources/js/src/views/extensions/clipboard/ClipboardWithoutDirective.vue ***!
  \***********************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _ClipboardWithoutDirective_vue_vue_type_template_id_638f115a___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./ClipboardWithoutDirective.vue?vue&type=template&id=638f115a& */ "./resources/js/src/views/extensions/clipboard/ClipboardWithoutDirective.vue?vue&type=template&id=638f115a&");
/* harmony import */ var _ClipboardWithoutDirective_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./ClipboardWithoutDirective.vue?vue&type=script&lang=js& */ "./resources/js/src/views/extensions/clipboard/ClipboardWithoutDirective.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport *//* harmony import */ var _node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../../../../../node_modules/vue-loader/lib/runtime/componentNormalizer.js */ "./node_modules/vue-loader/lib/runtime/componentNormalizer.js");





/* normalize component */

var component = Object(_node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__["default"])(
  _ClipboardWithoutDirective_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__["default"],
  _ClipboardWithoutDirective_vue_vue_type_template_id_638f115a___WEBPACK_IMPORTED_MODULE_0__["render"],
  _ClipboardWithoutDirective_vue_vue_type_template_id_638f115a___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"],
  false,
  null,
  null,
  null
  
)

/* hot reload */
if (false) { var api; }
component.options.__file = "resources/js/src/views/extensions/clipboard/ClipboardWithoutDirective.vue"
/* harmony default export */ __webpack_exports__["default"] = (component.exports);

/***/ }),

/***/ "./resources/js/src/views/extensions/clipboard/ClipboardWithoutDirective.vue?vue&type=script&lang=js&":
/*!************************************************************************************************************!*\
  !*** ./resources/js/src/views/extensions/clipboard/ClipboardWithoutDirective.vue?vue&type=script&lang=js& ***!
  \************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_ClipboardWithoutDirective_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../../node_modules/babel-loader/lib??ref--4-0!../../../../../../node_modules/vue-loader/lib??vue-loader-options!./ClipboardWithoutDirective.vue?vue&type=script&lang=js& */ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/extensions/clipboard/ClipboardWithoutDirective.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__["default"] = (_node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_ClipboardWithoutDirective_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__["default"]); 

/***/ }),

/***/ "./resources/js/src/views/extensions/clipboard/ClipboardWithoutDirective.vue?vue&type=template&id=638f115a&":
/*!******************************************************************************************************************!*\
  !*** ./resources/js/src/views/extensions/clipboard/ClipboardWithoutDirective.vue?vue&type=template&id=638f115a& ***!
  \******************************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_ClipboardWithoutDirective_vue_vue_type_template_id_638f115a___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../../node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!../../../../../../node_modules/vue-loader/lib??vue-loader-options!./ClipboardWithoutDirective.vue?vue&type=template&id=638f115a& */ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/extensions/clipboard/ClipboardWithoutDirective.vue?vue&type=template&id=638f115a&");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "render", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_ClipboardWithoutDirective_vue_vue_type_template_id_638f115a___WEBPACK_IMPORTED_MODULE_0__["render"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "staticRenderFns", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_ClipboardWithoutDirective_vue_vue_type_template_id_638f115a___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"]; });



/***/ }),

/***/ "./resources/js/src/views/extensions/clipboard/code.js":
/*!*************************************************************!*\
  !*** ./resources/js/src/views/extensions/clipboard/code.js ***!
  \*************************************************************/
/*! exports provided: codeDirective, codeWithoutDirective */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "codeDirective", function() { return codeDirective; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "codeWithoutDirective", function() { return codeWithoutDirective; });
var codeDirective = "\n<template>\n  <div class=\"d-flex\">\n\n    <!-- form input -->\n    <b-form-group class=\"mb-0 mr-1\">\n      <b-form-input\n        v-model=\"message\"\n      />\n    </b-form-group>\n\n    <!-- button -->\n    <b-button\n      v-clipboard:copy=\"message\"\n      v-clipboard:success=\"onCopy\"\n      v-clipboard:error=\"onError\"\n      v-ripple.400=\"'rgba(186, 191, 199, 0.15)'\"\n      variant=\"primary\"\n    >\n      Copy!\n    </b-button>\n  </div>\n</template>\n\n<script>\nimport { BFormInput, BFormGroup, BButton } from 'bootstrap-vue'\nimport ToastificationContent from '@core/components/toastification/ToastificationContent.vue'\nimport Ripple from 'vue-ripple-directive'\n\nexport default {\n  components: {\n    BFormInput,\n    BFormGroup,\n    BButton,\n    // eslint-disable-next-line vue/no-unused-components\n    ToastificationContent,\n  },\n  directives: {\n    Ripple,\n  },\n  data() {\n    return {\n      message: 'Copy Me!',\n    }\n  },\n  methods: {\n    onCopy() {\n      this.$toast({\n        component: ToastificationContent,\n        props: {\n          title: 'Text copied',\n          icon: 'BellIcon',\n        },\n      })\n    },\n    onError() {\n      this.$toast({\n        component: ToastificationContent,\n        props: {\n          title: 'Failed to copy texts!',\n          icon: 'BellIcon',\n        },\n      })\n    },\n  },\n}\n</script>\n";
var codeWithoutDirective = "\n<template>\n  <div class=\"d-flex\">\n\n    <!-- input -->\n    <b-form-group class=\"mb-0 mr-1\">\n      <b-form-input\n        v-model=\"message1\"\n      />\n    </b-form-group>\n\n    <!-- button -->\n    <b-button\n      v-ripple.400=\"'rgba(186, 191, 199, 0.15)'\"\n      variant=\"primary\"\n      @click=\"doCopy\"\n    >\n      Copy!\n    </b-button>\n  </div>\n</template>\n\n<script>\nimport {BFormInput, BFormGroup, BButton, BCardText} from 'bootstrap-vue'\nimport ToastificationContent from '@core/components/toastification/ToastificationContent.vue'\nimport Ripple from 'vue-ripple-directive'\n\nexport default {\n  components: {\n    BFormInput,\n    BFormGroup,\n    BButton,\n    BCardText,\n    // eslint-disable-next-line vue/no-unused-components\n    ToastificationContent,\n  },\n  directives: {\n    Ripple,\n  },\n  data() {\n    return {\n      message1: 'Copy Me Without Directive',\n    }\n  },\n  methods: {\n    doCopy() {\n      this.$copyText(this.message1).then(() => {\n        this.$toast({\n          component: ToastificationContent,\n          props: {\n            title: 'Text copied',\n            icon: 'BellIcon',\n          },\n        })\n      }, e => {\n        this.$toast({\n          component: ToastificationContent,\n          props: {\n            title: 'Can not copy!',\n            icon: 'BellIcon',\n          },\n        })\n      })\n    },\n  },\n}\n</script>\n";

/***/ })

}]);