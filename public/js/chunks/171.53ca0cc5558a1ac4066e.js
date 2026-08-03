(window["webpackJsonp"] = window["webpackJsonp"] || []).push([[171],{

/***/ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/app_pages/Etransport.vue?vue&type=script&lang=js&":
/*!******************************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib??ref--4-0!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/src/views/app_pages/Etransport.vue?vue&type=script&lang=js& ***!
  \******************************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var core_js_modules_es_object_to_string_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! core-js/modules/es.object.to-string.js */ "./node_modules/core-js/modules/es.object.to-string.js");
/* harmony import */ var core_js_modules_es_object_to_string_js__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_es_object_to_string_js__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var core_js_modules_es_array_concat_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! core-js/modules/es.array.concat.js */ "./node_modules/core-js/modules/es.array.concat.js");
/* harmony import */ var core_js_modules_es_array_concat_js__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_es_array_concat_js__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var core_js_modules_es_array_join_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! core-js/modules/es.array.join.js */ "./node_modules/core-js/modules/es.array.join.js");
/* harmony import */ var core_js_modules_es_array_join_js__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_es_array_join_js__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var core_js_modules_es_array_map_js__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! core-js/modules/es.array.map.js */ "./node_modules/core-js/modules/es.array.map.js");
/* harmony import */ var core_js_modules_es_array_map_js__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_es_array_map_js__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var core_js_modules_es_json_stringify_js__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! core-js/modules/es.json.stringify.js */ "./node_modules/core-js/modules/es.json.stringify.js");
/* harmony import */ var core_js_modules_es_json_stringify_js__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_es_json_stringify_js__WEBPACK_IMPORTED_MODULE_4__);





//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
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
  name: 'Etransport',
  data: function data() {
    return {
      cif: '',
      zile: 30,
      cifDepunere: '',
      fisier: null,
      notificari: [],
      filtre: {
        uit: '',
        cif: '',
        doar_erori: false
      },
      info: '',
      eroare: '',
      listaInCurs: false,
      sincronizareInCurs: false,
      depunereInCurs: false,
      stareVizibila: false,
      stareRaspuns: '',
      mod: 'certificat',
      oauthConfigurat: false,
      oauthRedirect: '',
      autorizare: {
        autorizat: false,
        expira_la: null,
        zile_ramase: null
      },
      campuri: [{
        key: 'uit',
        label: 'UIT / stare'
      }, {
        key: 'cod_decl',
        label: 'Declarant'
      }, {
        key: 'transport',
        label: 'Operațiune'
      }, {
        key: 'parteneri',
        label: 'Parteneri'
      }, {
        key: 'vehicul',
        label: 'Vehicul'
      }, {
        key: 'mesaje',
        label: 'Mesaje ANAF'
      }, {
        key: 'data_creare',
        label: 'Creată la'
      }, {
        key: 'actiuni',
        label: ''
      }]
    };
  },
  created: function created() {
    document.title = "".concat(window.app_name, " -> Dispecer e-Transport"); // Cererile merg autentificate și în contextul societății selectate.

    var token = window.localStorage.getItem('accessToken');

    if (token) {
      this.$http.defaults.headers.common.Authorization = "Bearer ".concat(token);
    }

    var societate = window.localStorage.getItem('societateaCurenta');

    if (societate) {
      try {
        this.$http.defaults.headers.common.AuthorizationHeader = JSON.parse(societate).id;
      } catch (e) {// societate salvată într-un format vechi — se cere reautentificarea
      }
    }

    var certificat = window.localStorage.getItem('anaf_certificat_activ');

    if (certificat) {
      this.$http.defaults.headers.common['X-Certificat-Id'] = certificat;
    }

    this.incarcaLista();
    this.verificaAutorizarea();
  },
  methods: {
    /** Autorizarea se face la ANAF, într-o filă nouă, cu certificatul digital. */
    autorizeaza: function autorizeaza() {
      var _this = this;

      this.eroare = '';
      this.$http.get('/anaf-etransport/oauth/url', {
        params: {
          cif: this.cif
        }
      }).then(function (raspuns) {
        window.open(raspuns.data.url, '_blank');
        _this.info = 'Finalizați autorizarea în fila deschisă, apoi apăsați „Verifică”.';
      })["catch"](function (err) {
        _this.eroare = _this.mesajEroare(err, 'Adresa de autorizare nu a putut fi obținută');
      });
    },
    verificaAutorizarea: function verificaAutorizarea() {
      var _this2 = this;

      var params = this.cif ? {
        cif: this.cif
      } : {};
      this.$http.get('/anaf-etransport/oauth/stare', {
        params: params
      }).then(function (raspuns) {
        _this2.mod = raspuns.data.mod;
        _this2.oauthConfigurat = raspuns.data.configurat;
        _this2.oauthRedirect = raspuns.data.redirect_uri;
        _this2.autorizare = raspuns.data.data;
      })["catch"](function () {// starea autorizării nu blochează restul paginii
      });
    },
    incarcaLista: function incarcaLista() {
      var _this3 = this;

      this.listaInCurs = true;
      var params = {};
      if (this.filtre.uit) params.uit = this.filtre.uit;
      if (this.filtre.cif) params.cif = this.filtre.cif;
      if (this.filtre.doar_erori) params.doar_erori = 1;
      this.$http.get('/anaf-etransport', {
        params: params
      }).then(function (raspuns) {
        _this3.notificari = raspuns.data.data || [];
      })["catch"](function (err) {
        _this3.eroare = _this3.mesajEroare(err, 'Nu s-au putut încărca notificările');
      })["finally"](function () {
        _this3.listaInCurs = false;
      });
    },
    sincronizeaza: function sincronizeaza() {
      var _this4 = this;

      this.eroare = '';
      this.info = '';
      this.sincronizareInCurs = true;
      this.$http.post('/anaf-etransport/sincronizeaza', {
        cif: this.cif,
        zile: this.zile
      }).then(function (raspuns) {
        var r = raspuns.data.data;
        _this4.info = "".concat(r.preluate, " notific\u0103ri primite, ").concat(r.noi, " noi, ").concat(r.cu_erori, " cu erori.");

        _this4.incarcaLista();
      })["catch"](function (err) {
        _this4.eroare = _this4.mesajEroare(err, 'Preluarea notificărilor a eșuat');
      })["finally"](function () {
        _this4.sincronizareInCurs = false;
      });
    },
    depune: function depune() {
      var _this5 = this;

      this.eroare = '';
      this.info = '';
      this.depunereInCurs = true;
      var formular = new FormData();
      formular.append('cif', this.cifDepunere);
      formular.append('fisier', this.fisier);
      this.$http.post('/anaf-etransport/depune', formular, {
        headers: {
          'Content-Type': 'multipart/form-data'
        }
      }).then(function (raspuns) {
        _this5.info = "Declara\u021Bie depus\u0103, index de \xEEnc\u0103rcare ".concat(raspuns.data.index_incarcare, ".");
        _this5.fisier = null;

        _this5.incarcaLista();
      })["catch"](function (err) {
        var date = err.response && err.response.data;
        var erori = date && date.erori ? date.erori : [];
        _this5.eroare = erori.length ? erori.map(function (e) {
          return e.errorMessage || e.mesaj || e;
        }).join(' | ') : _this5.mesajEroare(err, 'Depunerea a eșuat');
      })["finally"](function () {
        _this5.depunereInCurs = false;
      });
    },
    verificaStarea: function verificaStarea(notificare) {
      var _this6 = this;

      this.eroare = '';
      this.$http.get('/anaf-etransport/stare', {
        params: {
          id_incarcare: notificare.id_incarcare
        }
      }).then(function (raspuns) {
        _this6.stareRaspuns = JSON.stringify(raspuns.data.data, null, 2);
        _this6.stareVizibila = true;
      })["catch"](function (err) {
        _this6.eroare = _this6.mesajEroare(err, 'Starea nu a putut fi verificată');
      });
    },
    mesajEroare: function mesajEroare(err, implicit) {
      return err.response && err.response.data && err.response.data.message ? err.response.data.message : implicit;
    }
  }
});

/***/ }),

/***/ "./node_modules/core-js/modules/es.array.join.js":
/*!*******************************************************!*\
  !*** ./node_modules/core-js/modules/es.array.join.js ***!
  \*******************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

"use strict";

var $ = __webpack_require__(/*! ../internals/export */ "./node_modules/core-js/internals/export.js");
var uncurryThis = __webpack_require__(/*! ../internals/function-uncurry-this */ "./node_modules/core-js/internals/function-uncurry-this.js");
var IndexedObject = __webpack_require__(/*! ../internals/indexed-object */ "./node_modules/core-js/internals/indexed-object.js");
var toIndexedObject = __webpack_require__(/*! ../internals/to-indexed-object */ "./node_modules/core-js/internals/to-indexed-object.js");
var arrayMethodIsStrict = __webpack_require__(/*! ../internals/array-method-is-strict */ "./node_modules/core-js/internals/array-method-is-strict.js");

var un$Join = uncurryThis([].join);

var ES3_STRINGS = IndexedObject != Object;
var STRICT_METHOD = arrayMethodIsStrict('join', ',');

// `Array.prototype.join` method
// https://tc39.es/ecma262/#sec-array.prototype.join
$({ target: 'Array', proto: true, forced: ES3_STRINGS || !STRICT_METHOD }, {
  join: function join(separator) {
    return un$Join(toIndexedObject(this), separator === undefined ? ',' : separator);
  }
});


/***/ }),

/***/ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/app_pages/Etransport.vue?vue&type=template&id=233f8797&":
/*!**********************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/src/views/app_pages/Etransport.vue?vue&type=template&id=233f8797& ***!
  \**********************************************************************************************************************************************************************************************************************/
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
        "b-row",
        { staticClass: "mb-2" },
        [
          _c(
            "b-col",
            { staticClass: "mb-2", attrs: { md: "6" } },
            [
              _c(
                "b-card",
                {
                  staticClass: "h-100 border mb-0",
                  attrs: { "body-class": "p-2" },
                },
                [
                  _c("h6", { staticClass: "mb-2" }, [
                    _vm._v(
                      "\n          Preia notificările din e-Transport\n        "
                    ),
                  ]),
                  _vm._v(" "),
                  _c(
                    "b-row",
                    { attrs: { "no-gutters": "" } },
                    [
                      _c(
                        "b-col",
                        { staticClass: "pr-1", attrs: { cols: "7" } },
                        [
                          _c("b-form-input", {
                            attrs: { size: "sm", placeholder: "CIF declarant" },
                            model: {
                              value: _vm.cif,
                              callback: function ($$v) {
                                _vm.cif = $$v
                              },
                              expression: "cif",
                            },
                          }),
                        ],
                        1
                      ),
                      _vm._v(" "),
                      _c(
                        "b-col",
                        { attrs: { cols: "5" } },
                        [
                          _c("b-form-input", {
                            attrs: {
                              type: "number",
                              min: "1",
                              max: "60",
                              size: "sm",
                              placeholder: "Zile",
                            },
                            model: {
                              value: _vm.zile,
                              callback: function ($$v) {
                                _vm.zile = _vm._n($$v)
                              },
                              expression: "zile",
                            },
                          }),
                        ],
                        1
                      ),
                    ],
                    1
                  ),
                  _vm._v(" "),
                  _c(
                    "div",
                    { staticClass: "d-flex align-items-center mt-2" },
                    [
                      _c(
                        "b-button",
                        {
                          attrs: {
                            variant: "primary",
                            size: "sm",
                            disabled: !_vm.cif || _vm.sincronizareInCurs,
                          },
                          on: { click: _vm.sincronizeaza },
                        },
                        [
                          _vm.sincronizareInCurs
                            ? _c("b-spinner", {
                                staticClass: "mr-1",
                                attrs: { small: "" },
                              })
                            : _vm._e(),
                          _vm._v("\n            Preia\n          "),
                        ],
                        1
                      ),
                      _vm._v(" "),
                      _c("small", { staticClass: "text-muted ml-2" }, [
                        _vm._v(
                          "\n            Maximum 60 de zile. Se preiau stările finale și notificările cu erori.\n          "
                        ),
                      ]),
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
            "b-col",
            { staticClass: "mb-2", attrs: { md: "6" } },
            [
              _c(
                "b-card",
                {
                  staticClass: "h-100 border mb-0",
                  attrs: { "body-class": "p-2" },
                },
                [
                  _c("h6", { staticClass: "mb-2" }, [
                    _vm._v(
                      "\n          Depune o declarație de transport\n        "
                    ),
                  ]),
                  _vm._v(" "),
                  _c(
                    "b-row",
                    { attrs: { "no-gutters": "" } },
                    [
                      _c(
                        "b-col",
                        { staticClass: "pr-1", attrs: { cols: "5" } },
                        [
                          _c("b-form-input", {
                            attrs: { size: "sm", placeholder: "CIF declarant" },
                            model: {
                              value: _vm.cifDepunere,
                              callback: function ($$v) {
                                _vm.cifDepunere = $$v
                              },
                              expression: "cifDepunere",
                            },
                          }),
                        ],
                        1
                      ),
                      _vm._v(" "),
                      _c(
                        "b-col",
                        { attrs: { cols: "7" } },
                        [
                          _c("b-form-file", {
                            attrs: {
                              size: "sm",
                              accept: ".xml",
                              placeholder: "Declarația XML...",
                              "browse-text": "Alege",
                            },
                            model: {
                              value: _vm.fisier,
                              callback: function ($$v) {
                                _vm.fisier = $$v
                              },
                              expression: "fisier",
                            },
                          }),
                        ],
                        1
                      ),
                    ],
                    1
                  ),
                  _vm._v(" "),
                  _c(
                    "div",
                    { staticClass: "d-flex align-items-center mt-2" },
                    [
                      _c(
                        "b-button",
                        {
                          attrs: {
                            variant: "outline-primary",
                            size: "sm",
                            disabled:
                              !_vm.fisier ||
                              !_vm.cifDepunere ||
                              _vm.depunereInCurs,
                          },
                          on: { click: _vm.depune },
                        },
                        [
                          _vm.depunereInCurs
                            ? _c("b-spinner", {
                                staticClass: "mr-1",
                                attrs: { small: "" },
                              })
                            : _vm._e(),
                          _vm._v("\n            Depune\n          "),
                        ],
                        1
                      ),
                      _vm._v(" "),
                      _c("small", { staticClass: "text-muted ml-2" }, [
                        _vm._v(
                          "\n            La succes ANAF întoarce indexul de încărcare și UIT-ul.\n          "
                        ),
                      ]),
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
      _vm._v(" "),
      _c(
        "b-card",
        { staticClass: "border mb-2", attrs: { "body-class": "p-2" } },
        [
          _c(
            "b-row",
            { staticClass: "align-items-center", attrs: { "no-gutters": "" } },
            [
              _c("b-col", { attrs: { md: "7" } }, [
                _c(
                  "h6",
                  { staticClass: "mb-1" },
                  [
                    _vm._v("\n          Autorizare OAuth2 la ANAF\n          "),
                    _c(
                      "b-badge",
                      {
                        staticClass: "ml-1",
                        attrs: {
                          variant:
                            _vm.mod === "oauth"
                              ? "light-primary"
                              : "light-secondary",
                        },
                      },
                      [
                        _vm._v(
                          "\n            " +
                            _vm._s(
                              _vm.mod === "oauth"
                                ? "mod activ"
                                : "mod inactiv — se folosește certificatul digital"
                            ) +
                            "\n          "
                        ),
                      ]
                    ),
                  ],
                  1
                ),
                _vm._v(" "),
                !_vm.oauthConfigurat
                  ? _c("small", { staticClass: "text-danger d-block" }, [
                      _vm._v(
                        "\n          Aplicația nu are datele de client primite de la ANAF (CLIENT_ANAF_ID și CLIENT_ANAF_SECRET).\n        "
                      ),
                    ])
                  : !_vm.cif
                  ? _c("small", { staticClass: "text-muted d-block" }, [
                      _vm._v(
                        "\n          Completați CIF-ul de mai sus, apoi autorizați aplicația la ANAF.\n        "
                      ),
                    ])
                  : _vm.autorizare.autorizat
                  ? _c("small", { staticClass: "text-success d-block" }, [
                      _vm._v(
                        "\n          Autorizat pentru " +
                          _vm._s(_vm.cif) +
                          " până la " +
                          _vm._s(_vm.autorizare.expira_la) +
                          "\n          "
                      ),
                      _vm.autorizare.zile_ramase !== null
                        ? _c("span", [
                            _vm._v(
                              "(" +
                                _vm._s(_vm.autorizare.zile_ramase) +
                                " zile rămase)"
                            ),
                          ])
                        : _vm._e(),
                    ])
                  : _c("small", { staticClass: "text-muted d-block" }, [
                      _vm._v(
                        "\n          Nicio autorizare pentru " +
                          _vm._s(_vm.cif) +
                          ".\n          "
                      ),
                      _vm.mod === "oauth"
                        ? _c("span", [
                            _vm._v("Apelurile vor fi refuzate de ANAF."),
                          ])
                        : _vm._e(),
                    ]),
                _vm._v(" "),
                _vm.oauthRedirect
                  ? _c("small", { staticClass: "text-muted d-block" }, [
                      _vm._v(
                        "\n          Adresa de retur declarată la ANAF: " +
                          _vm._s(_vm.oauthRedirect) +
                          "\n        "
                      ),
                    ])
                  : _vm._e(),
              ]),
              _vm._v(" "),
              _c(
                "b-col",
                { staticClass: "text-md-right", attrs: { md: "5" } },
                [
                  _c(
                    "b-button",
                    {
                      attrs: {
                        variant: "outline-primary",
                        size: "sm",
                        disabled: !_vm.cif || !_vm.oauthConfigurat,
                      },
                      on: { click: _vm.autorizeaza },
                    },
                    [_vm._v("\n          Autorizează la ANAF\n        ")]
                  ),
                  _vm._v(" "),
                  _c(
                    "b-button",
                    {
                      staticClass: "ml-1",
                      attrs: {
                        variant: "outline-secondary",
                        size: "sm",
                        disabled: !_vm.cif,
                      },
                      on: { click: _vm.verificaAutorizarea },
                    },
                    [_vm._v("\n          Verifică\n        ")]
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
      _vm._v(" "),
      _vm.info
        ? _c(
            "b-alert",
            { staticClass: "py-2", attrs: { show: "", variant: "info" } },
            [_vm._v("\n    " + _vm._s(_vm.info) + "\n  ")]
          )
        : _vm._e(),
      _vm._v(" "),
      _vm.eroare
        ? _c(
            "b-alert",
            { staticClass: "py-2", attrs: { show: "", variant: "danger" } },
            [_vm._v("\n    " + _vm._s(_vm.eroare) + "\n  ")]
          )
        : _vm._e(),
      _vm._v(" "),
      _c(
        "b-card",
        { staticClass: "border", attrs: { "body-class": "p-2" } },
        [
          _c(
            "b-row",
            {
              staticClass: "mb-2 align-items-center",
              attrs: { "no-gutters": "" },
            },
            [
              _c(
                "b-col",
                { staticClass: "pr-1", attrs: { md: "3" } },
                [
                  _c("b-form-input", {
                    attrs: { size: "sm", placeholder: "Caută după UIT" },
                    on: { change: _vm.incarcaLista },
                    model: {
                      value: _vm.filtre.uit,
                      callback: function ($$v) {
                        _vm.$set(_vm.filtre, "uit", $$v)
                      },
                      expression: "filtre.uit",
                    },
                  }),
                ],
                1
              ),
              _vm._v(" "),
              _c(
                "b-col",
                { staticClass: "pr-1", attrs: { md: "3" } },
                [
                  _c("b-form-input", {
                    attrs: {
                      size: "sm",
                      placeholder: "Caută după CIF declarant",
                    },
                    on: { change: _vm.incarcaLista },
                    model: {
                      value: _vm.filtre.cif,
                      callback: function ($$v) {
                        _vm.$set(_vm.filtre, "cif", $$v)
                      },
                      expression: "filtre.cif",
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
                    "b-form-checkbox",
                    {
                      on: { change: _vm.incarcaLista },
                      model: {
                        value: _vm.filtre.doar_erori,
                        callback: function ($$v) {
                          _vm.$set(_vm.filtre, "doar_erori", $$v)
                        },
                        expression: "filtre.doar_erori",
                      },
                    },
                    [_vm._v("\n          Doar cele cu erori\n        ")]
                  ),
                ],
                1
              ),
            ],
            1
          ),
          _vm._v(" "),
          _c("b-table", {
            staticClass: "mb-0",
            attrs: {
              items: _vm.notificari,
              fields: _vm.campuri,
              busy: _vm.listaInCurs,
              responsive: "",
              striped: "",
              small: "",
              "show-empty": "",
              "empty-text":
                "Nicio notificare. Apăsați „Preia” pentru a le aduce de la ANAF.",
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
                        _c("b-spinner", { staticClass: "align-middle mr-1" }),
                        _vm._v("\n          Se încarcă...\n        "),
                      ],
                      1
                    ),
                  ]
                },
                proxy: true,
              },
              {
                key: "cell(uit)",
                fn: function (rand) {
                  return [
                    _c("div", [_vm._v(_vm._s(rand.item.uit || "-"))]),
                    _vm._v(" "),
                    _c(
                      "b-badge",
                      {
                        attrs: {
                          variant: rand.item.are_erori ? "danger" : "success",
                        },
                      },
                      [
                        _vm._v(
                          "\n          " +
                            _vm._s(
                              rand.item.are_erori ? "cu erori" : "validă"
                            ) +
                            "\n        "
                        ),
                      ]
                    ),
                    _vm._v(" "),
                    _c(
                      "b-badge",
                      { staticClass: "ml-1", attrs: { variant: "light" } },
                      [
                        _vm._v(
                          "\n          " +
                            _vm._s(rand.item.tip_eticheta) +
                            "\n        "
                        ),
                      ]
                    ),
                  ]
                },
              },
              {
                key: "cell(transport)",
                fn: function (rand) {
                  return [
                    _c("div", { staticClass: "small" }, [
                      _vm._v(
                        "\n          " +
                          _vm._s(rand.item.operatiune || "-") +
                          "\n        "
                      ),
                    ]),
                    _vm._v(" "),
                    _c("div", { staticClass: "small text-muted" }, [
                      _vm._v(
                        "\n          transport " +
                          _vm._s(rand.item.data_transp || "-") +
                          "\n        "
                      ),
                    ]),
                  ]
                },
              },
              {
                key: "cell(parteneri)",
                fn: function (rand) {
                  return [
                    _c("div", { staticClass: "small" }, [
                      _vm._v(
                        "\n          " +
                          _vm._s(rand.item.partener || "-") +
                          "\n        "
                      ),
                    ]),
                    _vm._v(" "),
                    _c("div", { staticClass: "small text-muted" }, [
                      _vm._v(
                        "\n          transportator: " +
                          _vm._s(rand.item.transportator || "-") +
                          "\n        "
                      ),
                    ]),
                  ]
                },
              },
              {
                key: "cell(mesaje)",
                fn: function (rand) {
                  return [
                    _vm._l(rand.item.mesaje || [], function (mesaj, index) {
                      return _c(
                        "div",
                        {
                          key: index,
                          staticClass: "small",
                          class:
                            mesaj.tip === "ERR" ? "text-danger" : "text-muted",
                        },
                        [
                          _vm._v(
                            "\n          " + _vm._s(mesaj.mesaj) + "\n        "
                          ),
                        ]
                      )
                    }),
                    _vm._v(" "),
                    !(rand.item.mesaje || []).length
                      ? _c("span", { staticClass: "small text-muted" }, [
                          _vm._v("—"),
                        ])
                      : _vm._e(),
                  ]
                },
              },
              {
                key: "cell(actiuni)",
                fn: function (rand) {
                  return [
                    rand.item.id_incarcare
                      ? _c(
                          "b-button",
                          {
                            directives: [
                              {
                                name: "b-tooltip",
                                rawName: "v-b-tooltip.hover.top.window.v-light",
                                modifiers: {
                                  hover: true,
                                  top: true,
                                  window: true,
                                  "v-light": true,
                                },
                              },
                            ],
                            staticClass: "btn-icon",
                            attrs: {
                              size: "sm",
                              variant: "outline-secondary",
                              title: "Verifică starea la ANAF",
                            },
                            on: {
                              click: function ($event) {
                                return _vm.verificaStarea(rand.item)
                              },
                            },
                          },
                          [
                            _c("feather-icon", {
                              attrs: { icon: "RefreshCwIcon" },
                            }),
                          ],
                          1
                        )
                      : _vm._e(),
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
        "b-modal",
        {
          attrs: {
            title: "Starea declarației la ANAF",
            "ok-only": "",
            "ok-title": "Închide",
            size: "lg",
          },
          model: {
            value: _vm.stareVizibila,
            callback: function ($$v) {
              _vm.stareVizibila = $$v
            },
            expression: "stareVizibila",
          },
        },
        [
          _c("pre", { staticClass: "small mb-0" }, [
            _vm._v(_vm._s(_vm.stareRaspuns)),
          ]),
        ]
      ),
    ],
    1
  )
}
var staticRenderFns = []
render._withStripped = true



/***/ }),

/***/ "./resources/js/src/views/app_pages/Etransport.vue":
/*!*********************************************************!*\
  !*** ./resources/js/src/views/app_pages/Etransport.vue ***!
  \*********************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _Etransport_vue_vue_type_template_id_233f8797___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./Etransport.vue?vue&type=template&id=233f8797& */ "./resources/js/src/views/app_pages/Etransport.vue?vue&type=template&id=233f8797&");
/* harmony import */ var _Etransport_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./Etransport.vue?vue&type=script&lang=js& */ "./resources/js/src/views/app_pages/Etransport.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport *//* harmony import */ var _node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../../../../node_modules/vue-loader/lib/runtime/componentNormalizer.js */ "./node_modules/vue-loader/lib/runtime/componentNormalizer.js");





/* normalize component */

var component = Object(_node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__["default"])(
  _Etransport_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__["default"],
  _Etransport_vue_vue_type_template_id_233f8797___WEBPACK_IMPORTED_MODULE_0__["render"],
  _Etransport_vue_vue_type_template_id_233f8797___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"],
  false,
  null,
  null,
  null
  
)

/* hot reload */
if (false) { var api; }
component.options.__file = "resources/js/src/views/app_pages/Etransport.vue"
/* harmony default export */ __webpack_exports__["default"] = (component.exports);

/***/ }),

/***/ "./resources/js/src/views/app_pages/Etransport.vue?vue&type=script&lang=js&":
/*!**********************************************************************************!*\
  !*** ./resources/js/src/views/app_pages/Etransport.vue?vue&type=script&lang=js& ***!
  \**********************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_Etransport_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../node_modules/babel-loader/lib??ref--4-0!../../../../../node_modules/vue-loader/lib??vue-loader-options!./Etransport.vue?vue&type=script&lang=js& */ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/app_pages/Etransport.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__["default"] = (_node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_Etransport_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__["default"]); 

/***/ }),

/***/ "./resources/js/src/views/app_pages/Etransport.vue?vue&type=template&id=233f8797&":
/*!****************************************************************************************!*\
  !*** ./resources/js/src/views/app_pages/Etransport.vue?vue&type=template&id=233f8797& ***!
  \****************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_Etransport_vue_vue_type_template_id_233f8797___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!../../../../../node_modules/vue-loader/lib??vue-loader-options!./Etransport.vue?vue&type=template&id=233f8797& */ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/app_pages/Etransport.vue?vue&type=template&id=233f8797&");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "render", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_Etransport_vue_vue_type_template_id_233f8797___WEBPACK_IMPORTED_MODULE_0__["render"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "staticRenderFns", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_Etransport_vue_vue_type_template_id_233f8797___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"]; });



/***/ })

}]);