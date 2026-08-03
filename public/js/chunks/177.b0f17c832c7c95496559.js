(window["webpackJsonp"] = window["webpackJsonp"] || []).push([[177],{

/***/ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/app_pages/VectorFiscal.vue?vue&type=script&lang=js&":
/*!********************************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib??ref--4-0!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/src/views/app_pages/VectorFiscal.vue?vue&type=script&lang=js& ***!
  \********************************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var C_dianasoft_serviciiweb_web_node_modules_babel_runtime_helpers_esm_objectSpread2_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./node_modules/@babel/runtime/helpers/esm/objectSpread2.js */ "./node_modules/@babel/runtime/helpers/esm/objectSpread2.js");
/* harmony import */ var C_dianasoft_serviciiweb_web_node_modules_babel_runtime_helpers_esm_toConsumableArray_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./node_modules/@babel/runtime/helpers/esm/toConsumableArray.js */ "./node_modules/@babel/runtime/helpers/esm/toConsumableArray.js");
/* harmony import */ var core_js_modules_es_array_concat_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! core-js/modules/es.array.concat.js */ "./node_modules/core-js/modules/es.array.concat.js");
/* harmony import */ var core_js_modules_es_array_concat_js__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_es_array_concat_js__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var core_js_modules_es_array_map_js__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! core-js/modules/es.array.map.js */ "./node_modules/core-js/modules/es.array.map.js");
/* harmony import */ var core_js_modules_es_array_map_js__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_es_array_map_js__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var core_js_modules_es_object_to_string_js__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! core-js/modules/es.object.to-string.js */ "./node_modules/core-js/modules/es.object.to-string.js");
/* harmony import */ var core_js_modules_es_object_to_string_js__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_es_object_to_string_js__WEBPACK_IMPORTED_MODULE_4__);





//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
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
  name: 'VectorFiscal',
  data: function data() {
    return {
      tabActiv: 0,
      vectori: [],
      vectorSpv: [],
      situatie: [],
      tipuriDeclaratii: [],
      optiuniPeriodicitate: [{
        value: null,
        text: '-'
      }],
      filtruCui: '',
      filtruCuiSpv: '',
      eroare: '',
      listaInCurs: false,
      formularVizibil: false,
      formular: {},
      perioada: {
        luna: new Date().getMonth() + 1,
        anul: new Date().getFullYear()
      },
      campuriSpv: [{
        key: 'cui',
        label: 'CUI'
      }, {
        key: 'cod_imp',
        label: 'Cod obligație'
      }, {
        key: 'semnificatie',
        label: 'Semnificație'
      }, {
        key: 'perfisc',
        label: 'Periodicitate'
      }, {
        key: 'data_inceput',
        label: 'Data început'
      }, {
        key: 'data_sfarsit',
        label: 'Data sfârșit'
      }],
      campuriSituatie: [{
        key: 'cui',
        label: 'CUI'
      }, {
        key: 'denumire',
        label: 'Denumire'
      }, {
        key: 'obligatii',
        label: 'Obligații (verde = depusă)'
      }, {
        key: 'lipsa',
        label: 'Nedepuse'
      }]
    };
  },
  computed: {
    campuriVector: function campuriVector() {
      return [{
        key: 'cui',
        label: 'CUI'
      }, {
        key: 'denumire',
        label: 'Denumire'
      }].concat(Object(C_dianasoft_serviciiweb_web_node_modules_babel_runtime_helpers_esm_toConsumableArray_js__WEBPACK_IMPORTED_MODULE_1__["default"])(this.tipuriDeclaratii.map(function (tip) {
        return {
          key: tip,
          label: tip
        };
      })), [{
        key: 'actiuni',
        label: 'Acțiuni'
      }]);
    }
  },
  created: function created() {
    document.title = "".concat(window.app_name, " -> Vector fiscal");
    this.incarcaLista();
  },
  methods: {
    incarcaLista: function incarcaLista() {
      var _this = this;

      this.listaInCurs = true;
      var params = {};
      if (this.filtruCui) params.cui = this.filtruCui;
      this.$http.get('/vector-fiscal', {
        params: params
      }).then(function (raspuns) {
        _this.vectori = raspuns.data.data || [];
        _this.tipuriDeclaratii = raspuns.data.declaratii || [];
        _this.optiuniPeriodicitate = [{
          value: null,
          text: '-'
        }].concat((raspuns.data.periodicitati || []).map(function (p) {
          return {
            value: p,
            text: p
          };
        }));
      })["catch"](function (err) {
        _this.eroare = _this.mesajEroare(err, 'Nu s-a putut încărca vectorul fiscal');
      })["finally"](function () {
        _this.listaInCurs = false;
      });
    },
    incarcaSpv: function incarcaSpv() {
      var _this2 = this;

      var params = {};
      if (this.filtruCuiSpv) params.cui = this.filtruCuiSpv;
      this.$http.get('/vector-fiscal/spv', {
        params: params
      }).then(function (raspuns) {
        _this2.vectorSpv = raspuns.data.data || [];
      })["catch"](function (err) {
        _this2.eroare = _this2.mesajEroare(err, 'Nu s-au putut încărca datele din SPV');
      });
    },
    incarcaSituatie: function incarcaSituatie() {
      var _this3 = this;

      this.$http.get('/vector-fiscal/situatie', {
        params: this.perioada
      }).then(function (raspuns) {
        _this3.situatie = raspuns.data.data || [];
      })["catch"](function (err) {
        _this3.eroare = _this3.mesajEroare(err, 'Nu s-a putut genera situația');
      });
    },
    deschideFormular: function deschideFormular(vector) {
      this.formular = vector ? Object(C_dianasoft_serviciiweb_web_node_modules_babel_runtime_helpers_esm_objectSpread2_js__WEBPACK_IMPORTED_MODULE_0__["default"])({}, vector) : {
        cui: '',
        denumire: ''
      };
      this.formularVizibil = true;
    },
    salveaza: function salveaza() {
      var _this4 = this;

      this.eroare = '';
      var cerere = this.formular.id ? this.$http.put("/vector-fiscal/".concat(this.formular.id), this.formular) : this.$http.post('/vector-fiscal', this.formular);
      cerere.then(function () {
        _this4.notifica('Vectorul fiscal a fost salvat', 'success');

        _this4.incarcaLista();
      })["catch"](function (err) {
        _this4.eroare = _this4.mesajEroare(err, 'Salvarea a eșuat');
      });
    },
    sterge: function sterge(vector) {
      var _this5 = this;

      this.$http["delete"]("/vector-fiscal/".concat(vector.id)).then(function () {
        _this5.notifica('Contribuabilul a fost șters', 'success');

        _this5.incarcaLista();
      })["catch"](function (err) {
        _this5.eroare = _this5.mesajEroare(err, 'Ștergerea a eșuat');
      });
    },
    mesajEroare: function mesajEroare(err, implicit) {
      return err.response && err.response.data && err.response.data.message ? err.response.data.message : implicit;
    },
    notifica: function notifica(mesaj, variant) {
      this.$bvToast.toast(mesaj, {
        title: 'Vector fiscal',
        variant: variant,
        solid: true
      });
    }
  }
});

/***/ }),

/***/ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/app_pages/VectorFiscal.vue?vue&type=template&id=bfbb1258&":
/*!************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/src/views/app_pages/VectorFiscal.vue?vue&type=template&id=bfbb1258& ***!
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
  return _c(
    "div",
    [
      _c(
        "b-card",
        { attrs: { title: "Vector fiscal" } },
        [
          _c(
            "b-tabs",
            {
              model: {
                value: _vm.tabActiv,
                callback: function ($$v) {
                  _vm.tabActiv = $$v
                },
                expression: "tabActiv",
              },
            },
            [
              _c(
                "b-tab",
                { attrs: { title: "Vector declarat" } },
                [
                  _c(
                    "b-row",
                    { staticClass: "my-3" },
                    [
                      _c(
                        "b-col",
                        { attrs: { md: "3" } },
                        [
                          _c("b-form-input", {
                            attrs: { placeholder: "Filtrează după CUI" },
                            on: { change: _vm.incarcaLista },
                            model: {
                              value: _vm.filtruCui,
                              callback: function ($$v) {
                                _vm.filtruCui = $$v
                              },
                              expression: "filtruCui",
                            },
                          }),
                        ],
                        1
                      ),
                      _vm._v(" "),
                      _c(
                        "b-col",
                        { attrs: { md: "3" } },
                        [
                          _c(
                            "b-button",
                            {
                              attrs: { variant: "primary" },
                              on: {
                                click: function ($event) {
                                  return _vm.deschideFormular(null)
                                },
                              },
                            },
                            [
                              _vm._v(
                                "\n              Adaugă contribuabil\n            "
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
                  _vm.eroare
                    ? _c(
                        "b-alert",
                        { attrs: { show: "", variant: "danger" } },
                        [
                          _vm._v(
                            "\n          " + _vm._s(_vm.eroare) + "\n        "
                          ),
                        ]
                      )
                    : _vm._e(),
                  _vm._v(" "),
                  _c("b-table", {
                    attrs: {
                      items: _vm.vectori,
                      fields: _vm.campuriVector,
                      busy: _vm.listaInCurs,
                      responsive: "",
                      striped: "",
                      small: "",
                      "show-empty": "",
                      "empty-text":
                        "Nu există contribuabili în vectorul fiscal.",
                    },
                    scopedSlots: _vm._u([
                      {
                        key: "table-busy",
                        fn: function () {
                          return [
                            _c(
                              "div",
                              { staticClass: "text-center my-2" },
                              [
                                _c("b-spinner", {
                                  staticClass: "align-middle mr-1",
                                }),
                                _vm._v("Se încarcă...\n            "),
                              ],
                              1
                            ),
                          ]
                        },
                        proxy: true,
                      },
                      {
                        key: "cell(actiuni)",
                        fn: function (rand) {
                          return [
                            _c(
                              "b-button",
                              {
                                staticClass: "mr-1",
                                attrs: {
                                  size: "sm",
                                  variant: "outline-primary",
                                },
                                on: {
                                  click: function ($event) {
                                    return _vm.deschideFormular(rand.item)
                                  },
                                },
                              },
                              [_vm._v("\n              Modifică\n            ")]
                            ),
                            _vm._v(" "),
                            _c(
                              "b-button",
                              {
                                attrs: {
                                  size: "sm",
                                  variant: "outline-danger",
                                },
                                on: {
                                  click: function ($event) {
                                    return _vm.sterge(rand.item)
                                  },
                                },
                              },
                              [_vm._v("\n              Șterge\n            ")]
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
                "b-tab",
                {
                  attrs: { title: "Vector din SPV" },
                  on: { click: _vm.incarcaSpv },
                },
                [
                  _c(
                    "b-row",
                    { staticClass: "my-3" },
                    [
                      _c(
                        "b-col",
                        { attrs: { md: "3" } },
                        [
                          _c("b-form-input", {
                            attrs: { placeholder: "Filtrează după CUI" },
                            on: { change: _vm.incarcaSpv },
                            model: {
                              value: _vm.filtruCuiSpv,
                              callback: function ($$v) {
                                _vm.filtruCuiSpv = $$v
                              },
                              expression: "filtruCuiSpv",
                            },
                          }),
                        ],
                        1
                      ),
                    ],
                    1
                  ),
                  _vm._v(" "),
                  _c("b-table", {
                    attrs: {
                      items: _vm.vectorSpv,
                      fields: _vm.campuriSpv,
                      responsive: "",
                      striped: "",
                      small: "",
                      "show-empty": "",
                      "empty-text":
                        "Nu există date. Solicitați documentul „Vector Fiscal” din pagina Solicitări SPV.",
                    },
                  }),
                ],
                1
              ),
              _vm._v(" "),
              _c(
                "b-tab",
                {
                  attrs: { title: "Situație depuneri" },
                  on: { click: _vm.incarcaSituatie },
                },
                [
                  _c(
                    "b-row",
                    { staticClass: "my-3" },
                    [
                      _c(
                        "b-col",
                        { attrs: { md: "2" } },
                        [
                          _c("label", [_vm._v("Luna")]),
                          _vm._v(" "),
                          _c("b-form-input", {
                            attrs: { type: "number", min: "1", max: "12" },
                            model: {
                              value: _vm.perioada.luna,
                              callback: function ($$v) {
                                _vm.$set(_vm.perioada, "luna", _vm._n($$v))
                              },
                              expression: "perioada.luna",
                            },
                          }),
                        ],
                        1
                      ),
                      _vm._v(" "),
                      _c(
                        "b-col",
                        { attrs: { md: "2" } },
                        [
                          _c("label", [_vm._v("Anul")]),
                          _vm._v(" "),
                          _c("b-form-input", {
                            attrs: { type: "number", min: "2000", max: "2100" },
                            model: {
                              value: _vm.perioada.anul,
                              callback: function ($$v) {
                                _vm.$set(_vm.perioada, "anul", _vm._n($$v))
                              },
                              expression: "perioada.anul",
                            },
                          }),
                        ],
                        1
                      ),
                      _vm._v(" "),
                      _c(
                        "b-col",
                        {
                          staticClass: "d-flex align-items-end",
                          attrs: { md: "2" },
                        },
                        [
                          _c(
                            "b-button",
                            {
                              attrs: { variant: "primary" },
                              on: { click: _vm.incarcaSituatie },
                            },
                            [_vm._v("\n              Generează\n            ")]
                          ),
                        ],
                        1
                      ),
                    ],
                    1
                  ),
                  _vm._v(" "),
                  _c("b-table", {
                    attrs: {
                      items: _vm.situatie,
                      fields: _vm.campuriSituatie,
                      responsive: "",
                      striped: "",
                      small: "",
                      "show-empty": "",
                      "empty-text":
                        "Nu există obligații declarative pentru perioada selectată.",
                    },
                    scopedSlots: _vm._u([
                      {
                        key: "cell(obligatii)",
                        fn: function (rand) {
                          return _vm._l(
                            rand.item.obligatii,
                            function (obligatie) {
                              return _c(
                                "b-badge",
                                {
                                  key: obligatie.tip,
                                  staticClass: "mr-1 mb-1",
                                  attrs: {
                                    variant: obligatie.depusa
                                      ? "success"
                                      : "danger",
                                  },
                                },
                                [
                                  _vm._v(
                                    "\n              " +
                                      _vm._s(obligatie.tip) +
                                      " (" +
                                      _vm._s(obligatie.periodicitate) +
                                      ")\n            "
                                  ),
                                ]
                              )
                            }
                          )
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
        ],
        1
      ),
      _vm._v(" "),
      _c(
        "b-modal",
        {
          attrs: {
            title: _vm.formular.id
              ? "Modifică vectorul fiscal"
              : "Adaugă contribuabil",
            "ok-title": "Salvează",
            "cancel-title": "Renunță",
            size: "lg",
          },
          on: { ok: _vm.salveaza },
          model: {
            value: _vm.formularVizibil,
            callback: function ($$v) {
              _vm.formularVizibil = $$v
            },
            expression: "formularVizibil",
          },
        },
        [
          _c(
            "b-row",
            [
              _c(
                "b-col",
                { attrs: { md: "4" } },
                [
                  _c("label", [_vm._v("CUI")]),
                  _vm._v(" "),
                  _c("b-form-input", {
                    model: {
                      value: _vm.formular.cui,
                      callback: function ($$v) {
                        _vm.$set(_vm.formular, "cui", $$v)
                      },
                      expression: "formular.cui",
                    },
                  }),
                ],
                1
              ),
              _vm._v(" "),
              _c(
                "b-col",
                { attrs: { md: "8" } },
                [
                  _c("label", [_vm._v("Denumire")]),
                  _vm._v(" "),
                  _c("b-form-input", {
                    model: {
                      value: _vm.formular.denumire,
                      callback: function ($$v) {
                        _vm.$set(_vm.formular, "denumire", $$v)
                      },
                      expression: "formular.denumire",
                    },
                  }),
                ],
                1
              ),
            ],
            1
          ),
          _vm._v(" "),
          _c("hr"),
          _vm._v(" "),
          _c(
            "b-row",
            _vm._l(_vm.tipuriDeclaratii, function (tip) {
              return _c(
                "b-col",
                { key: tip, staticClass: "mb-2", attrs: { md: "3" } },
                [
                  _c("label", [_vm._v(_vm._s(tip))]),
                  _vm._v(" "),
                  _c("b-form-select", {
                    attrs: { options: _vm.optiuniPeriodicitate },
                    model: {
                      value: _vm.formular[tip],
                      callback: function ($$v) {
                        _vm.$set(_vm.formular, tip, $$v)
                      },
                      expression: "formular[tip]",
                    },
                  }),
                ],
                1
              )
            }),
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

/***/ "./resources/js/src/views/app_pages/VectorFiscal.vue":
/*!***********************************************************!*\
  !*** ./resources/js/src/views/app_pages/VectorFiscal.vue ***!
  \***********************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _VectorFiscal_vue_vue_type_template_id_bfbb1258___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./VectorFiscal.vue?vue&type=template&id=bfbb1258& */ "./resources/js/src/views/app_pages/VectorFiscal.vue?vue&type=template&id=bfbb1258&");
/* harmony import */ var _VectorFiscal_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./VectorFiscal.vue?vue&type=script&lang=js& */ "./resources/js/src/views/app_pages/VectorFiscal.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport *//* harmony import */ var _node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../../../../node_modules/vue-loader/lib/runtime/componentNormalizer.js */ "./node_modules/vue-loader/lib/runtime/componentNormalizer.js");





/* normalize component */

var component = Object(_node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__["default"])(
  _VectorFiscal_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__["default"],
  _VectorFiscal_vue_vue_type_template_id_bfbb1258___WEBPACK_IMPORTED_MODULE_0__["render"],
  _VectorFiscal_vue_vue_type_template_id_bfbb1258___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"],
  false,
  null,
  null,
  null
  
)

/* hot reload */
if (false) { var api; }
component.options.__file = "resources/js/src/views/app_pages/VectorFiscal.vue"
/* harmony default export */ __webpack_exports__["default"] = (component.exports);

/***/ }),

/***/ "./resources/js/src/views/app_pages/VectorFiscal.vue?vue&type=script&lang=js&":
/*!************************************************************************************!*\
  !*** ./resources/js/src/views/app_pages/VectorFiscal.vue?vue&type=script&lang=js& ***!
  \************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_VectorFiscal_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../node_modules/babel-loader/lib??ref--4-0!../../../../../node_modules/vue-loader/lib??vue-loader-options!./VectorFiscal.vue?vue&type=script&lang=js& */ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/app_pages/VectorFiscal.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__["default"] = (_node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_VectorFiscal_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__["default"]); 

/***/ }),

/***/ "./resources/js/src/views/app_pages/VectorFiscal.vue?vue&type=template&id=bfbb1258&":
/*!******************************************************************************************!*\
  !*** ./resources/js/src/views/app_pages/VectorFiscal.vue?vue&type=template&id=bfbb1258& ***!
  \******************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_VectorFiscal_vue_vue_type_template_id_bfbb1258___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!../../../../../node_modules/vue-loader/lib??vue-loader-options!./VectorFiscal.vue?vue&type=template&id=bfbb1258& */ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/app_pages/VectorFiscal.vue?vue&type=template&id=bfbb1258&");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "render", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_VectorFiscal_vue_vue_type_template_id_bfbb1258___WEBPACK_IMPORTED_MODULE_0__["render"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "staticRenderFns", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_VectorFiscal_vue_vue_type_template_id_bfbb1258___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"]; });



/***/ })

}]);