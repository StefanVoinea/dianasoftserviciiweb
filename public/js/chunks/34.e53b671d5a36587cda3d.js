(window["webpackJsonp"] = window["webpackJsonp"] || []).push([[34],{

/***/ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/app_pages/Spv.vue?vue&type=script&lang=js&":
/*!***********************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib??ref--4-0!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/src/views/app_pages/Spv.vue?vue&type=script&lang=js& ***!
  \***********************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var C_dianasoft_serviciiweb_web_node_modules_babel_runtime_helpers_esm_objectSpread2_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./node_modules/@babel/runtime/helpers/esm/objectSpread2.js */ "./node_modules/@babel/runtime/helpers/esm/objectSpread2.js");
/* harmony import */ var core_js_modules_es_array_filter_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! core-js/modules/es.array.filter.js */ "./node_modules/core-js/modules/es.array.filter.js");
/* harmony import */ var core_js_modules_es_array_filter_js__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_es_array_filter_js__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var core_js_modules_es_object_to_string_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! core-js/modules/es.object.to-string.js */ "./node_modules/core-js/modules/es.object.to-string.js");
/* harmony import */ var core_js_modules_es_object_to_string_js__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_es_object_to_string_js__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var core_js_modules_es_array_concat_js__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! core-js/modules/es.array.concat.js */ "./node_modules/core-js/modules/es.array.concat.js");
/* harmony import */ var core_js_modules_es_array_concat_js__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_es_array_concat_js__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var core_js_modules_es_regexp_exec_js__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! core-js/modules/es.regexp.exec.js */ "./node_modules/core-js/modules/es.regexp.exec.js");
/* harmony import */ var core_js_modules_es_regexp_exec_js__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_es_regexp_exec_js__WEBPACK_IMPORTED_MODULE_4__);
/* harmony import */ var core_js_modules_es_string_replace_js__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! core-js/modules/es.string.replace.js */ "./node_modules/core-js/modules/es.string.replace.js");
/* harmony import */ var core_js_modules_es_string_replace_js__WEBPACK_IMPORTED_MODULE_5___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_es_string_replace_js__WEBPACK_IMPORTED_MODULE_5__);
/* harmony import */ var core_js_modules_es_array_find_index_js__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! core-js/modules/es.array.find-index.js */ "./node_modules/core-js/modules/es.array.find-index.js");
/* harmony import */ var core_js_modules_es_array_find_index_js__WEBPACK_IMPORTED_MODULE_6___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_es_array_find_index_js__WEBPACK_IMPORTED_MODULE_6__);
/* harmony import */ var _spv_Societati_vue__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! ./spv/Societati.vue */ "./resources/js/src/views/app_pages/spv/Societati.vue");
/* harmony import */ var _spv_Declaratii_vue__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! ./spv/Declaratii.vue */ "./resources/js/src/views/app_pages/spv/Declaratii.vue");
/* harmony import */ var _spv_Mesaje_vue__WEBPACK_IMPORTED_MODULE_9__ = __webpack_require__(/*! ./spv/Mesaje.vue */ "./resources/js/src/views/app_pages/spv/Mesaje.vue");
/* harmony import */ var _spv_Solicitari_vue__WEBPACK_IMPORTED_MODULE_10__ = __webpack_require__(/*! ./spv/Solicitari.vue */ "./resources/js/src/views/app_pages/spv/Solicitari.vue");
/* harmony import */ var _spv_Certificate_vue__WEBPACK_IMPORTED_MODULE_11__ = __webpack_require__(/*! ./spv/Certificate.vue */ "./resources/js/src/views/app_pages/spv/Certificate.vue");
/* harmony import */ var _spv_Jurnal_vue__WEBPACK_IMPORTED_MODULE_12__ = __webpack_require__(/*! ./spv/Jurnal.vue */ "./resources/js/src/views/app_pages/spv/Jurnal.vue");
/* harmony import */ var _spv_Utilizatori_vue__WEBPACK_IMPORTED_MODULE_13__ = __webpack_require__(/*! ./spv/Utilizatori.vue */ "./resources/js/src/views/app_pages/spv/Utilizatori.vue");
/* harmony import */ var _spv_CereriDePin_vue__WEBPACK_IMPORTED_MODULE_14__ = __webpack_require__(/*! ./spv/CereriDePin.vue */ "./resources/js/src/views/app_pages/spv/CereriDePin.vue");







//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//








/*
 * Drepturile pe operațiuni sunt dezactivate: accesul ține doar de societatea
 * selectată. Pentru reactivare se completează „drepturi” (ex. ['verificareMesajeSpv'])
 * și se pune middleware-ul corespunzător pe rutele din routes/api.php.
 */

var TABURI = [{
  cheie: 'certificate',
  titlu: 'Certificate digitale',
  componenta: 'CertificateTab',
  drepturi: []
}, {
  cheie: 'entitati',
  titlu: 'Entități înrolate',
  componenta: 'SocietatiTab',
  drepturi: []
}, {
  cheie: 'declaratii',
  titlu: 'Declarații fiscale',
  componenta: 'DeclaratiiTab',
  drepturi: []
}, {
  cheie: 'mesaje',
  titlu: 'Mesaje ANAF',
  componenta: 'MesajeTab',
  drepturi: []
}, {
  cheie: 'solicitari',
  titlu: 'Solicitări ANAF',
  componenta: 'SolicitariTab',
  drepturi: []
}, {
  cheie: 'jurnal',
  titlu: 'Jurnal activitate',
  componenta: 'JurnalTab',
  drepturi: []
}, // Doar administratorul firmei; ceilalți nici nu văd fila.
{
  cheie: 'utilizatori',
  titlu: 'Utilizatori',
  componenta: 'UtilizatoriTab',
  drepturi: [],
  doarAdministrator: true
}];
/* harmony default export */ __webpack_exports__["default"] = ({
  name: 'Spv',
  components: {
    SocietatiTab: _spv_Societati_vue__WEBPACK_IMPORTED_MODULE_7__["default"],
    DeclaratiiTab: _spv_Declaratii_vue__WEBPACK_IMPORTED_MODULE_8__["default"],
    MesajeTab: _spv_Mesaje_vue__WEBPACK_IMPORTED_MODULE_9__["default"],
    SolicitariTab: _spv_Solicitari_vue__WEBPACK_IMPORTED_MODULE_10__["default"],
    CertificateTab: _spv_Certificate_vue__WEBPACK_IMPORTED_MODULE_11__["default"],
    JurnalTab: _spv_Jurnal_vue__WEBPACK_IMPORTED_MODULE_12__["default"],
    UtilizatoriTab: _spv_Utilizatori_vue__WEBPACK_IMPORTED_MODULE_13__["default"],
    CereriDePin: _spv_CereriDePin_vue__WEBPACK_IMPORTED_MODULE_14__["default"]
  },
  data: function data() {
    return {
      tabActiv: 0,
      administrator: false
    };
  },
  computed: {
    taburiVizibile: function taburiVizibile() {
      var _this = this;

      return TABURI.filter(function (tab) {
        if (tab.doarAdministrator && !_this.administrator) return false;
        return tab.drepturi.length === 0 || tab.drepturi.some(function (drept) {
          return _this.poate(drept);
        });
      });
    }
  },
  watch: {
    // Tabul rămâne în adresă, ca să poată fi trimis ca link sau reîncărcat.
    tabActiv: function tabActiv(index) {
      var tab = this.taburiVizibile[index];
      if (!tab) return;
      document.title = "".concat(window.app_name, " -> SPV Curier: ").concat(tab.titlu);

      if (this.$route.query.tab !== tab.cheie) {
        this.$router.replace({
          query: Object(C_dianasoft_serviciiweb_web_node_modules_babel_runtime_helpers_esm_objectSpread2_js__WEBPACK_IMPORTED_MODULE_0__["default"])(Object(C_dianasoft_serviciiweb_web_node_modules_babel_runtime_helpers_esm_objectSpread2_js__WEBPACK_IMPORTED_MODULE_0__["default"])({}, this.$route.query), {}, {
            tab: tab.cheie
          })
        })["catch"](function () {});
      }
    }
  },
  created: function created() {
    var _this2 = this;

    // Toate cererile modulului merg autentificate și în contextul societății
    // selectate: serverul le folosește ca să returneze doar datele acelui client.
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
    } // Certificatul ales însoțește cererile, ca acestea să ajungă la calculatorul
    // pe care este conectat tokenul respectiv.


    var certificat = window.localStorage.getItem('anaf_certificat_activ');

    if (certificat) {
      this.$http.defaults.headers.common['X-Certificat-Id'] = certificat;
    } // Fila „Utilizatori” o vede doar administratorul firmei. Răspunsul se cere
    // de la server, nu din localStorage: dreptul se poate schimba între timp.


    this.$http.get('/context').then(function (_ref) {
      var data = _ref.data;
      _this2.administrator = data.data.administrator_client || data.data.super_admin;
    })["catch"](function () {
      _this2.administrator = false;
    }); // Tabul se identifică prin nume, nu prin poziție: pozițiile diferă de la un
    // utilizator la altul, în funcție de drepturi.

    var cerut = this.taburiVizibile.findIndex(function (tab) {
      return tab.cheie === _this2.$route.query.tab;
    });
    this.tabActiv = cerut >= 0 ? cerut : 0;
    var activ = this.taburiVizibile[this.tabActiv];

    if (activ) {
      document.title = "".concat(window.app_name, " -> SPV Curier: ").concat(activ.titlu);
    }
  },
  methods: {
    // Proprietarul societății are toate drepturile, ca și pe server.
    poate: function poate(drept) {
      if (window.localStorage.getItem('userRole') === 'owner') return true;
      return this.$userpermitt.can(drept);
    }
  }
});

/***/ }),

/***/ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/app_pages/spv/CereriDePin.vue?vue&type=script&lang=js&":
/*!***********************************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib??ref--4-0!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/src/views/app_pages/spv/CereriDePin.vue?vue&type=script&lang=js& ***!
  \***********************************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var core_js_modules_es_object_to_string_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! core-js/modules/es.object.to-string.js */ "./node_modules/core-js/modules/es.object.to-string.js");
/* harmony import */ var core_js_modules_es_object_to_string_js__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_es_object_to_string_js__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var bootstrap_vue__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! bootstrap-vue */ "./node_modules/bootstrap-vue/esm/index.js");

//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//

/*
 * Cererea PIN-ului pentru tokenul care îl așteaptă.
 *
 * Când o lucrare se oprește fiindcă tokenul cere PIN-ul, fereastra aceea stă
 * deschisă pe calculatorul clientului — adesea pe alt ecran decât al omului
 * care a apăsat butonul, uneori în alt oraș. Până acum lucrarea pica, iar din
 * aplicație pana semăna cu una de rețea.
 *
 * Se cere numai pentru tokenele la care omul a pornit anume facilitatea, din
 * fila Certificate. Pentru celelalte, aplicația spune doar care token așteaptă,
 * iar codul se scrie de mână, acolo.
 */

/** Din cât în cât se întreabă dacă vreun token își așteaptă codul. */

var LA_CATE_SECUNDE = 20;
/* harmony default export */ __webpack_exports__["default"] = ({
  components: {
    BModal: bootstrap_vue__WEBPACK_IMPORTED_MODULE_1__["BModal"],
    BFormInput: bootstrap_vue__WEBPACK_IMPORTED_MODULE_1__["BFormInput"],
    BAlert: bootstrap_vue__WEBPACK_IMPORTED_MODULE_1__["BAlert"]
  },
  data: function data() {
    return {
      tokenul: {},
      pin: '',
      vizibil: false,
      inCurs: false,
      eroare: '',
      ceasul: null
    };
  },
  created: function created() {
    this.intreaba();
    this.ceasul = setInterval(this.intreaba, LA_CATE_SECUNDE * 1000);
  },
  beforeDestroy: function beforeDestroy() {
    if (this.ceasul) clearInterval(this.ceasul);
  },
  methods: {
    /** Tokenurile care își așteaptă codul acum și pentru care e voie să-l trimitem. */
    intreaba: function intreaba() {
      var _this = this;

      // Fila ascunsă nu are cui să arate fereastra; se întreabă când revine.
      if (document.hidden || this.vizibil || this.inCurs) return null;
      return this.$http.get('/anaf-certificate/pin/asteptare').then(function (_ref) {
        var data = _ref.data;
        var asteapta = (data.data || [])[0];
        if (!asteapta) return;
        _this.tokenul = asteapta;
        _this.pin = '';
        _this.eroare = '';
        _this.vizibil = true;
      })["catch"](function () {// O întrebare picată nu are de ce să tulbure fila.
      });
    },
    trimite: function trimite() {
      var _this2 = this;

      if (!this.pin || this.inCurs) return null;
      this.inCurs = true;
      this.eroare = '';
      return this.$http.post("/anaf-certificate/".concat(this.tokenul.id, "/pin"), {
        pin: this.pin
      }).then(function () {
        _this2.vizibil = false;

        _this2.uita();
      })["catch"](function (err) {
        _this2.eroare = err.response && err.response.data && err.response.data.message || 'Codul nu a putut fi trimis.';
      })["finally"](function () {
        _this2.inCurs = false;
      });
    },

    /** Codul nu rămâne în filă nici după ce fereastra s-a închis. */
    uita: function uita() {
      this.pin = '';
    }
  }
});

/***/ }),

/***/ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/app_pages/spv/Certificate.vue?vue&type=script&lang=js&":
/*!***********************************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib??ref--4-0!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/src/views/app_pages/spv/Certificate.vue?vue&type=script&lang=js& ***!
  \***********************************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var core_js_modules_es_array_concat_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! core-js/modules/es.array.concat.js */ "./node_modules/core-js/modules/es.array.concat.js");
/* harmony import */ var core_js_modules_es_array_concat_js__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_es_array_concat_js__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var core_js_modules_es_array_map_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! core-js/modules/es.array.map.js */ "./node_modules/core-js/modules/es.array.map.js");
/* harmony import */ var core_js_modules_es_array_map_js__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_es_array_map_js__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var core_js_modules_es_number_constructor_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! core-js/modules/es.number.constructor.js */ "./node_modules/core-js/modules/es.number.constructor.js");
/* harmony import */ var core_js_modules_es_number_constructor_js__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_es_number_constructor_js__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var core_js_modules_es_object_to_string_js__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! core-js/modules/es.object.to-string.js */ "./node_modules/core-js/modules/es.object.to-string.js");
/* harmony import */ var core_js_modules_es_object_to_string_js__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_es_object_to_string_js__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var core_js_modules_es_regexp_exec_js__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! core-js/modules/es.regexp.exec.js */ "./node_modules/core-js/modules/es.regexp.exec.js");
/* harmony import */ var core_js_modules_es_regexp_exec_js__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_es_regexp_exec_js__WEBPACK_IMPORTED_MODULE_4__);
/* harmony import */ var core_js_modules_es_string_match_js__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! core-js/modules/es.string.match.js */ "./node_modules/core-js/modules/es.string.match.js");
/* harmony import */ var core_js_modules_es_string_match_js__WEBPACK_IMPORTED_MODULE_5___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_es_string_match_js__WEBPACK_IMPORTED_MODULE_5__);
/* harmony import */ var core_js_modules_es_array_find_js__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! core-js/modules/es.array.find.js */ "./node_modules/core-js/modules/es.array.find.js");
/* harmony import */ var core_js_modules_es_array_find_js__WEBPACK_IMPORTED_MODULE_6___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_es_array_find_js__WEBPACK_IMPORTED_MODULE_6__);
/* harmony import */ var core_js_modules_es_string_iterator_js__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! core-js/modules/es.string.iterator.js */ "./node_modules/core-js/modules/es.string.iterator.js");
/* harmony import */ var core_js_modules_es_string_iterator_js__WEBPACK_IMPORTED_MODULE_7___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_es_string_iterator_js__WEBPACK_IMPORTED_MODULE_7__);
/* harmony import */ var core_js_modules_web_dom_collections_iterator_js__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! core-js/modules/web.dom-collections.iterator.js */ "./node_modules/core-js/modules/web.dom-collections.iterator.js");
/* harmony import */ var core_js_modules_web_dom_collections_iterator_js__WEBPACK_IMPORTED_MODULE_8___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_web_dom_collections_iterator_js__WEBPACK_IMPORTED_MODULE_8__);
/* harmony import */ var core_js_modules_web_url_js__WEBPACK_IMPORTED_MODULE_9__ = __webpack_require__(/*! core-js/modules/web.url.js */ "./node_modules/core-js/modules/web.url.js");
/* harmony import */ var core_js_modules_web_url_js__WEBPACK_IMPORTED_MODULE_9___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_web_url_js__WEBPACK_IMPORTED_MODULE_9__);
/* harmony import */ var core_js_modules_web_url_search_params_js__WEBPACK_IMPORTED_MODULE_10__ = __webpack_require__(/*! core-js/modules/web.url-search-params.js */ "./node_modules/core-js/modules/web.url-search-params.js");
/* harmony import */ var core_js_modules_web_url_search_params_js__WEBPACK_IMPORTED_MODULE_10___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_web_url_search_params_js__WEBPACK_IMPORTED_MODULE_10__);
/* harmony import */ var core_js_modules_es_array_join_js__WEBPACK_IMPORTED_MODULE_11__ = __webpack_require__(/*! core-js/modules/es.array.join.js */ "./node_modules/core-js/modules/es.array.join.js");
/* harmony import */ var core_js_modules_es_array_join_js__WEBPACK_IMPORTED_MODULE_11___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_es_array_join_js__WEBPACK_IMPORTED_MODULE_11__);












//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
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
  name: 'SpvCertificate',
  data: function data() {
    return {
      certificate: [],
      // Certificatul caruia i se probeaza PIN-ul chiar acum
      pinInCurs: null,
      abonati: [],
      zileAvertizare: 30,
      // Versiunea programului local pe care o are serverul acum
      versiuneProgram: '',
      emailNou: '',
      certificatAles: null,
      eroare: '',
      listaInCurs: false,
      sincronizareInCurs: false,
      campuri: [{
        key: 'titular',
        label: 'Titular'
      }, {
        key: 'emitent',
        label: 'Emitent'
      }, {
        key: 'valabilitate',
        label: 'Valabil până la'
      }, {
        key: 'bridge',
        label: 'Calculator / utilizare'
      }, {
        key: 'entitati',
        label: 'Entități'
      }, {
        key: 'utilizatori',
        label: 'Utilizatori'
      }, {
        key: 'avertizare',
        label: 'Ultima avertizare'
      }],
      campuriAbonati: [{
        key: 'email',
        label: 'Email'
      }, {
        key: 'certificat_id',
        label: 'Certificat'
      }, {
        key: 'actiuni',
        label: 'Acțiuni'
      }],
      info: '',
      kitInCurs: false,
      // Certificatul pentru care se trimite acum licența
      licentaInCurs: null,
      // Certificatul care se scoate din uz sau se repune chiar acum
      activareInCurs: null,
      // Ajutorul pentru firewall și antivirus, de pe calculatorul clientului
      ajutorVizibil: false,
      bridgeNou: {
        bridge_url: '',
        bridge_token: ''
      },
      bridgeVizibil: false,
      bridgeFormular: {},
      foldereVizibil: false,
      campFoldere: 'arhiva_cale',
      foldereInCurs: false,
      foldere: [],
      folderCurent: '',
      folderParinte: null,
      eroareFoldere: '',
      certificatActiv: null,
      utilizatoriVizibil: false,
      certificatCurent: {},
      utilizatorNou: {
        email: '',
        nume: '',
        avertizare: false
      },
      eroareModal: '',
      // Din cât în cât se verifică dosarul urmărit al certificatului
      cadenteMonitorizare: [{
        value: 1,
        text: 'La 1 minut'
      }, {
        value: 3,
        text: 'La 3 minute'
      }, {
        value: 5,
        text: 'La 5 minute'
      }, {
        value: 10,
        text: 'La 10 minute'
      }, {
        value: 15,
        text: 'La 15 minute'
      }, {
        value: 30,
        text: 'La 30 de minute'
      }, {
        value: 60,
        text: 'La 60 de minute'
      }],
      campuriUtilizatori: [{
        key: 'email',
        label: 'Email'
      }, {
        key: 'nume',
        label: 'Nume'
      }, {
        key: 'are_cont',
        label: 'Stare'
      }, {
        key: 'actiuni',
        label: 'Acțiuni'
      }]
    };
  },
  computed: {
    optiuniCertificat: function optiuniCertificat() {
      return [{
        value: null,
        text: 'Toate certificatele'
      }].concat(this.certificate.map(function (c) {
        return {
          value: c.id,
          text: "".concat(c.cn, " (expir\u0103 ").concat(c.expira_la, ")")
        };
      }));
    }
  },
  created: function created() {
    var salvat = window.localStorage.getItem('anaf_certificat_activ');

    if (salvat) {
      this.certificatActiv = Number(salvat);
    }

    this.incarcaLista();
  },
  methods: {
    /**
     * Ce scrie lângă certificat despre PIN-ul de pe token.
     *
     * „gata" înseamnă că driverul îl are în minte și cheia se poate folosi acum
     * — nu că l-am ști noi: PIN-ul nu trece prin aplicație și nu se păstrează
     * nicăieri, rămâne între om și driver.
     */
    textulPinului: function textulPinului(certificat) {
      if (certificat.pin_stare === 'gata') return 'PIN introdus — tokenul e gata de lucru';
      if (certificat.pin_stare === 'refuzat') return 'PIN neintrodus — semnarea și SPV vor eșua';
      return 'tokenul nu a putut fi întrebat de PIN';
    },

    /**
     * Cere probarea PIN-ului și reîncarcă lista.
     *
     * Se cheamă și singură, la intrarea în aplicație. Aici e pentru cazul în
     * care omul a conectat tokenul între timp, sau a închis fereastra fără să
     * scrie PIN-ul și vrea să încerce din nou.
     */
    verificaPinul: function verificaPinul(certificat) {
      var _this = this;

      this.pinInCurs = certificat ? certificat.id : 'toate';
      return this.$http.post('/anaf-certificate/verifica-pin', certificat ? {
        certificat: certificat.id
      } : {}).then(function () {
        return _this.incarcaLista();
      })["catch"](function () {// Un token care nu răspunde nu e o eroare de arătat aici: starea lui
        // se vede oricum lângă el, scrisă de server.
      })["finally"](function () {
        _this.pinInCurs = null;
      });
    },
    scurt: function scurt(text) {
      if (!text) return '-';
      var cn = text.match(/CN=([^,]+)/);
      return cn ? cn[1] : text;
    },
    numeCertificat: function numeCertificat(id) {
      if (!id) return 'Toate';
      var certificat = this.certificate.find(function (c) {
        return c.id === id;
      });
      return certificat ? certificat.cn : "#".concat(id);
    },
    textExpirare: function textExpirare(certificat) {
      if (certificat.expirat) return 'expirat';
      if (certificat.zile_ramase === null) return 'necunoscut';
      return "".concat(certificat.zile_ramase, " zile r\u0103mase");
    },
    variantaExpirare: function variantaExpirare(certificat) {
      if (certificat.expirat) return 'danger';
      if (certificat.zile_ramase !== null && certificat.zile_ramase <= this.zileAvertizare) return 'warning';
      return 'success';
    },

    /** Rândul certificatului scos din uz se stinge, fără să dispară din listă. */
    clasaRand: function clasaRand(certificat) {
      return certificat && !certificat.activ ? 'rand-scos-din-uz' : '';
    },
    incarcaLista: function incarcaLista() {
      var _this2 = this;

      this.listaInCurs = true;
      this.$http.get('/anaf-certificate').then(function (raspuns) {
        _this2.certificate = raspuns.data.data || [];
        _this2.abonati = raspuns.data.abonati || [];
        _this2.zileAvertizare = raspuns.data.zile_avertizare || 30;
        _this2.versiuneProgram = raspuns.data.versiune_program || '';
      })["catch"](function (err) {
        _this2.eroare = _this2.mesajEroare(err, 'Nu s-au putut încărca certificatele');
      })["finally"](function () {
        _this2.listaInCurs = false;
      });
    },

    /**
     * Kitul se cere prin API (cu token) și se salvează local; tokenul bridge-ului
     * generat pentru acel calculator se afișează pentru configurare.
     */
    descarcaKit: function descarcaKit() {
      var _this3 = this;

      this.eroare = '';
      this.info = '';
      this.kitInCurs = true;
      this.$http.get('/anaf-certificate/kit', {
        responseType: 'blob'
      }).then(function (raspuns) {
        var url = window.URL.createObjectURL(new Blob([raspuns.data], {
          type: 'application/zip'
        }));
        var link = document.createElement('a');
        link.href = url;
        link.download = 'kit_spv_curier.zip';
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        setTimeout(function () {
          return window.URL.revokeObjectURL(url);
        }, 60000);
        var token = raspuns.headers['x-bridge-token'];
        _this3.info = token ? "Kit desc\u0103rcat. Codul de acces pentru acel calculator: ".concat(token) : 'Kit descărcat.';
      })["catch"](function () {
        _this3.eroare = 'Kitul nu a putut fi generat.';
      })["finally"](function () {
        _this3.kitInCurs = false;
      });
    },
    descopera: function descopera() {
      var _this4 = this;

      this.eroare = '';
      this.info = '';
      this.sincronizareInCurs = true;
      this.$http.post('/anaf-certificate/descopera', this.bridgeNou).then(function (raspuns) {
        var gasite = raspuns.data.data || [];
        var entitati = raspuns.data.entitati || {};
        var parti = ["".concat(gasite.length, " certificat(e) \xEEnregistrate")];
        if (entitati.total) parti.push("".concat(entitati.total, " entit\u0103\u021Bi \xEEnrolate preluate"));
        _this4.info = "".concat(parti.join(', '), ".");

        if (entitati.erori && entitati.erori.length) {
          _this4.eroare = entitati.erori.join(' | ');
        }

        _this4.incarcaLista();
      })["catch"](function (err) {
        _this4.eroare = _this4.mesajEroare(err, 'Certificatele nu au putut fi citite');
      })["finally"](function () {
        _this4.sincronizareInCurs = false;
      });
    },
    deschideBridge: function deschideBridge(certificat) {
      this.bridgeFormular = {
        id: certificat.id,
        cn: certificat.cn,
        bridge_url: certificat.bridge_implicit ? '' : certificat.bridge_url,
        bridge_token: '',
        arhiva_cale: certificat.arhiva_cale || '',
        monitorizare_cale: certificat.monitorizare_cale || '',
        monitorizare_activa: Boolean(certificat.monitorizare_activa),
        monitorizare_cadenta: certificat.monitorizare_cadenta || 5,
        monitorizare_semneaza: certificat.monitorizare_semneaza !== false,
        monitorizare_depune: Boolean(certificat.monitorizare_depune),
        pin_de_la_distanta: Boolean(certificat.pin_de_la_distanta),
        monitorizare_la: certificat.monitorizare_la,
        mod_legatura: certificat.mod_legatura || 'direct',
        implicit: certificat.implicit
      };
      this.bridgeVizibil = true;
    },

    /**
     * Răsfoirea pornește din dosarul deja scris, dacă există.
     *
     * @param {string} camp câmpul în care se scrie dosarul ales
     */
    deschideFoldere: function deschideFoldere(camp) {
      this.eroareFoldere = '';
      this.campFoldere = camp;
      this.foldereVizibil = true;
      this.rasfoieste(this.bridgeFormular[camp] || '');
    },

    /**
     * Cere dosarele calculatorului cu tokenul. Cale goală = discurile lui.
     */
    rasfoieste: function rasfoieste(cale) {
      var _this5 = this;

      if (cale === null) return;
      this.foldereInCurs = true;
      this.eroareFoldere = '';
      this.$http.get("/anaf-certificate/".concat(this.bridgeFormular.id, "/foldere"), {
        params: {
          cale: cale
        }
      }).then(function (_ref) {
        var data = _ref.data;
        _this5.foldere = data.data.foldere;
        _this5.folderCurent = data.data.cale;
        _this5.folderParinte = data.data.parinte;
      })["catch"](function (err) {
        _this5.eroareFoldere = _this5.mesajEroare(err, 'Dosarele nu au putut fi citite'); // La o cale greșită se rămâne totuși cu ceva de ales: discurile.

        if (cale !== '') _this5.rasfoieste('');
      })["finally"](function () {
        _this5.foldereInCurs = false;
      });
    },
    alegeFolder: function alegeFolder() {
      this.$set(this.bridgeFormular, this.campFoldere, this.folderCurent);
    },
    salveazaBridge: function salveazaBridge() {
      var _this6 = this;

      this.eroare = '';
      this.$http.put("/anaf-certificate/".concat(this.bridgeFormular.id), {
        bridge_url: this.bridgeFormular.bridge_url || null,
        bridge_token: this.bridgeFormular.bridge_token || null,
        arhiva_cale: this.bridgeFormular.arhiva_cale || null,
        monitorizare_cale: this.bridgeFormular.monitorizare_cale || null,
        monitorizare_activa: this.bridgeFormular.monitorizare_activa,
        monitorizare_cadenta: this.bridgeFormular.monitorizare_cadenta,
        monitorizare_semneaza: this.bridgeFormular.monitorizare_semneaza,
        monitorizare_depune: this.bridgeFormular.monitorizare_depune,
        pin_de_la_distanta: this.bridgeFormular.pin_de_la_distanta,
        mod_legatura: this.bridgeFormular.mod_legatura,
        implicit: this.bridgeFormular.implicit
      }).then(function () {
        _this6.incarcaLista();
      })["catch"](function (err) {
        _this6.eroare = _this6.mesajEroare(err, 'Configurarea nu a putut fi salvată');
      });
    },

    /**
     * Certificatul ales este trimis ca antet la toate cererile modulului, deci
     * operațiile merg pe bridge-ul calculatorului unde e tokenul respectiv.
     */
    alegeActiv: function alegeActiv(certificat) {
      this.certificatActiv = certificat.id;
      this.$http.defaults.headers.common['X-Certificat-Id'] = certificat.id;
      window.localStorage.setItem('anaf_certificat_activ', certificat.id);
      this.info = "Opera\u021Biile vor folosi certificatul \u201E".concat(certificat.cn, "\u201D.");
    },

    /** Trimite acum licența programului local, fără să aștepte reînnoirea de noapte. */
    reinnoiesteLicenta: function reinnoiesteLicenta(certificat) {
      var _this7 = this;

      this.eroare = '';
      this.info = '';
      this.licentaInCurs = certificat.id;
      this.$http.post("/anaf-certificate/".concat(certificat.id, "/licenta")).then(function (_ref2) {
        var data = _ref2.data;
        _this7.info = data.message;

        _this7.incarcaLista();
      })["catch"](function (err) {
        _this7.eroare = _this7.mesajEroare(err, 'Licența nu a putut fi trimisă');
      })["finally"](function () {
        _this7.licentaInCurs = null;
      });
    },

    /**
     * Scoate certificatul din uz, sau îl repune.
     *
     * Dacă era chiar cel ales pentru operațiile mele, alegerea se șterge pe loc:
     * altfel cererile ar pleca mai departe cu el în antet, iar serverul, care nu
     * mai lucrează cu el, le-ar rezolva pe altul, fără să se vadă de ce.
     */
    comutaActiv: function comutaActiv(certificat) {
      var _this8 = this;

      this.eroare = '';
      this.info = '';
      this.activareInCurs = certificat.id;
      this.$http.post("/anaf-certificate/".concat(certificat.id, "/activare")).then(function (_ref3) {
        var data = _ref3.data;
        _this8.info = data.message;

        if (!data.data.activ && _this8.certificatActiv === certificat.id) {
          _this8.certificatActiv = null;
          delete _this8.$http.defaults.headers.common['X-Certificat-Id'];
          window.localStorage.removeItem('anaf_certificat_activ');
        }

        _this8.incarcaLista();
      })["catch"](function (err) {
        _this8.eroare = _this8.mesajEroare(err, 'Starea certificatului nu a putut fi schimbată');
      })["finally"](function () {
        _this8.activareInCurs = null;
      });
    },
    deschideUtilizatori: function deschideUtilizatori(certificat) {
      this.certificatCurent = certificat;
      this.utilizatorNou = {
        email: '',
        nume: '',
        avertizare: false
      };
      this.eroareModal = '';
      this.utilizatoriVizibil = true;
    },
    ataseaza: function ataseaza() {
      var _this9 = this;

      this.eroareModal = '';
      this.$http.post("/anaf-certificate/".concat(this.certificatCurent.id, "/utilizatori"), this.utilizatorNou).then(function () {
        _this9.utilizatorNou = {
          email: '',
          nume: '',
          avertizare: false
        };

        _this9.reincarcaSiActualizeazaModalul();
      })["catch"](function (err) {
        _this9.eroareModal = _this9.mesajEroare(err, 'Utilizatorul nu a putut fi atașat');
      });
    },
    detaseaza: function detaseaza(utilizator) {
      var _this10 = this;

      this.eroareModal = '';
      this.$http["delete"]("/anaf-certificate/utilizatori/".concat(utilizator.id)).then(function () {
        _this10.reincarcaSiActualizeazaModalul();
      })["catch"](function (err) {
        _this10.eroareModal = _this10.mesajEroare(err, 'Utilizatorul nu a putut fi eliminat');
      });
    },
    // Modalul ramane deschis, dar trebuie sa arate lista actualizata.
    reincarcaSiActualizeazaModalul: function reincarcaSiActualizeazaModalul() {
      var _this11 = this;

      var id = this.certificatCurent.id;
      this.$http.get('/anaf-certificate').then(function (raspuns) {
        _this11.certificate = raspuns.data.data || [];
        _this11.abonati = raspuns.data.abonati || [];
        _this11.certificatCurent = _this11.certificate.find(function (c) {
          return c.id === id;
        }) || _this11.certificatCurent;
      })["catch"](function (err) {
        _this11.eroareModal = _this11.mesajEroare(err, 'Lista nu a putut fi reîncărcată');
      });
    },
    aboneaza: function aboneaza() {
      var _this12 = this;

      this.eroare = '';
      this.$http.post('/anaf-certificate/abonare', {
        email: this.emailNou,
        certificat_id: this.certificatAles
      }).then(function () {
        _this12.emailNou = '';

        _this12.incarcaLista();
      })["catch"](function (err) {
        _this12.eroare = _this12.mesajEroare(err, 'Adresa nu a putut fi înscrisă');
      });
    },
    dezaboneaza: function dezaboneaza(abonat) {
      var _this13 = this;

      this.$http["delete"]("/anaf-certificate/abonare/".concat(abonat.id)).then(function () {
        _this13.incarcaLista();
      })["catch"](function (err) {
        _this13.eroare = _this13.mesajEroare(err, 'Ștergerea a eșuat');
      });
    },
    mesajEroare: function mesajEroare(err, implicit) {
      return err.response && err.response.data && err.response.data.message ? err.response.data.message : implicit;
    }
  }
});

/***/ }),

/***/ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/app_pages/spv/Declaratii.vue?vue&type=script&lang=js&":
/*!**********************************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib??ref--4-0!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/src/views/app_pages/spv/Declaratii.vue?vue&type=script&lang=js& ***!
  \**********************************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var C_dianasoft_serviciiweb_web_node_modules_babel_runtime_helpers_esm_objectSpread2_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./node_modules/@babel/runtime/helpers/esm/objectSpread2.js */ "./node_modules/@babel/runtime/helpers/esm/objectSpread2.js");
/* harmony import */ var C_dianasoft_serviciiweb_web_node_modules_babel_runtime_helpers_esm_toConsumableArray_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./node_modules/@babel/runtime/helpers/esm/toConsumableArray.js */ "./node_modules/@babel/runtime/helpers/esm/toConsumableArray.js");
/* harmony import */ var core_js_modules_es_array_filter_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! core-js/modules/es.array.filter.js */ "./node_modules/core-js/modules/es.array.filter.js");
/* harmony import */ var core_js_modules_es_array_filter_js__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_es_array_filter_js__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var core_js_modules_es_object_to_string_js__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! core-js/modules/es.object.to-string.js */ "./node_modules/core-js/modules/es.object.to-string.js");
/* harmony import */ var core_js_modules_es_object_to_string_js__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_es_object_to_string_js__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var core_js_modules_es_array_concat_js__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! core-js/modules/es.array.concat.js */ "./node_modules/core-js/modules/es.array.concat.js");
/* harmony import */ var core_js_modules_es_array_concat_js__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_es_array_concat_js__WEBPACK_IMPORTED_MODULE_4__);
/* harmony import */ var core_js_modules_es_regexp_exec_js__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! core-js/modules/es.regexp.exec.js */ "./node_modules/core-js/modules/es.regexp.exec.js");
/* harmony import */ var core_js_modules_es_regexp_exec_js__WEBPACK_IMPORTED_MODULE_5___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_es_regexp_exec_js__WEBPACK_IMPORTED_MODULE_5__);
/* harmony import */ var core_js_modules_es_regexp_test_js__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! core-js/modules/es.regexp.test.js */ "./node_modules/core-js/modules/es.regexp.test.js");
/* harmony import */ var core_js_modules_es_regexp_test_js__WEBPACK_IMPORTED_MODULE_6___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_es_regexp_test_js__WEBPACK_IMPORTED_MODULE_6__);
/* harmony import */ var core_js_modules_es_function_name_js__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! core-js/modules/es.function.name.js */ "./node_modules/core-js/modules/es.function.name.js");
/* harmony import */ var core_js_modules_es_function_name_js__WEBPACK_IMPORTED_MODULE_7___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_es_function_name_js__WEBPACK_IMPORTED_MODULE_7__);
/* harmony import */ var core_js_modules_es_array_join_js__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! core-js/modules/es.array.join.js */ "./node_modules/core-js/modules/es.array.join.js");
/* harmony import */ var core_js_modules_es_array_join_js__WEBPACK_IMPORTED_MODULE_8___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_es_array_join_js__WEBPACK_IMPORTED_MODULE_8__);
/* harmony import */ var core_js_modules_es_array_map_js__WEBPACK_IMPORTED_MODULE_9__ = __webpack_require__(/*! core-js/modules/es.array.map.js */ "./node_modules/core-js/modules/es.array.map.js");
/* harmony import */ var core_js_modules_es_array_map_js__WEBPACK_IMPORTED_MODULE_9___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_es_array_map_js__WEBPACK_IMPORTED_MODULE_9__);
/* harmony import */ var core_js_modules_es_string_pad_start_js__WEBPACK_IMPORTED_MODULE_10__ = __webpack_require__(/*! core-js/modules/es.string.pad-start.js */ "./node_modules/core-js/modules/es.string.pad-start.js");
/* harmony import */ var core_js_modules_es_string_pad_start_js__WEBPACK_IMPORTED_MODULE_10___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_es_string_pad_start_js__WEBPACK_IMPORTED_MODULE_10__);
/* harmony import */ var core_js_modules_es_json_stringify_js__WEBPACK_IMPORTED_MODULE_11__ = __webpack_require__(/*! core-js/modules/es.json.stringify.js */ "./node_modules/core-js/modules/es.json.stringify.js");
/* harmony import */ var core_js_modules_es_json_stringify_js__WEBPACK_IMPORTED_MODULE_11___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_es_json_stringify_js__WEBPACK_IMPORTED_MODULE_11__);
/* harmony import */ var core_js_modules_es_number_constructor_js__WEBPACK_IMPORTED_MODULE_12__ = __webpack_require__(/*! core-js/modules/es.number.constructor.js */ "./node_modules/core-js/modules/es.number.constructor.js");
/* harmony import */ var core_js_modules_es_number_constructor_js__WEBPACK_IMPORTED_MODULE_12___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_es_number_constructor_js__WEBPACK_IMPORTED_MODULE_12__);
/* harmony import */ var core_js_modules_es_string_iterator_js__WEBPACK_IMPORTED_MODULE_13__ = __webpack_require__(/*! core-js/modules/es.string.iterator.js */ "./node_modules/core-js/modules/es.string.iterator.js");
/* harmony import */ var core_js_modules_es_string_iterator_js__WEBPACK_IMPORTED_MODULE_13___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_es_string_iterator_js__WEBPACK_IMPORTED_MODULE_13__);
/* harmony import */ var core_js_modules_web_dom_collections_iterator_js__WEBPACK_IMPORTED_MODULE_14__ = __webpack_require__(/*! core-js/modules/web.dom-collections.iterator.js */ "./node_modules/core-js/modules/web.dom-collections.iterator.js");
/* harmony import */ var core_js_modules_web_dom_collections_iterator_js__WEBPACK_IMPORTED_MODULE_14___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_web_dom_collections_iterator_js__WEBPACK_IMPORTED_MODULE_14__);
/* harmony import */ var core_js_modules_web_url_js__WEBPACK_IMPORTED_MODULE_15__ = __webpack_require__(/*! core-js/modules/web.url.js */ "./node_modules/core-js/modules/web.url.js");
/* harmony import */ var core_js_modules_web_url_js__WEBPACK_IMPORTED_MODULE_15___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_web_url_js__WEBPACK_IMPORTED_MODULE_15__);
/* harmony import */ var core_js_modules_web_url_search_params_js__WEBPACK_IMPORTED_MODULE_16__ = __webpack_require__(/*! core-js/modules/web.url-search-params.js */ "./node_modules/core-js/modules/web.url-search-params.js");
/* harmony import */ var core_js_modules_web_url_search_params_js__WEBPACK_IMPORTED_MODULE_16___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_web_url_search_params_js__WEBPACK_IMPORTED_MODULE_16__);
/* harmony import */ var core_js_modules_web_dom_collections_for_each_js__WEBPACK_IMPORTED_MODULE_17__ = __webpack_require__(/*! core-js/modules/web.dom-collections.for-each.js */ "./node_modules/core-js/modules/web.dom-collections.for-each.js");
/* harmony import */ var core_js_modules_web_dom_collections_for_each_js__WEBPACK_IMPORTED_MODULE_17___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_web_dom_collections_for_each_js__WEBPACK_IMPORTED_MODULE_17__);
/* harmony import */ var core_js_modules_es_object_keys_js__WEBPACK_IMPORTED_MODULE_18__ = __webpack_require__(/*! core-js/modules/es.object.keys.js */ "./node_modules/core-js/modules/es.object.keys.js");
/* harmony import */ var core_js_modules_es_object_keys_js__WEBPACK_IMPORTED_MODULE_18___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_es_object_keys_js__WEBPACK_IMPORTED_MODULE_18__);
/* harmony import */ var core_js_modules_es_array_slice_js__WEBPACK_IMPORTED_MODULE_19__ = __webpack_require__(/*! core-js/modules/es.array.slice.js */ "./node_modules/core-js/modules/es.array.slice.js");
/* harmony import */ var core_js_modules_es_array_slice_js__WEBPACK_IMPORTED_MODULE_19___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_es_array_slice_js__WEBPACK_IMPORTED_MODULE_19__);
/* harmony import */ var core_js_modules_es_set_js__WEBPACK_IMPORTED_MODULE_20__ = __webpack_require__(/*! core-js/modules/es.set.js */ "./node_modules/core-js/modules/es.set.js");
/* harmony import */ var core_js_modules_es_set_js__WEBPACK_IMPORTED_MODULE_20___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_es_set_js__WEBPACK_IMPORTED_MODULE_20__);
/* harmony import */ var core_js_modules_es_string_trim_js__WEBPACK_IMPORTED_MODULE_21__ = __webpack_require__(/*! core-js/modules/es.string.trim.js */ "./node_modules/core-js/modules/es.string.trim.js");
/* harmony import */ var core_js_modules_es_string_trim_js__WEBPACK_IMPORTED_MODULE_21___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_es_string_trim_js__WEBPACK_IMPORTED_MODULE_21__);
/* harmony import */ var core_js_modules_es_string_replace_js__WEBPACK_IMPORTED_MODULE_22__ = __webpack_require__(/*! core-js/modules/es.string.replace.js */ "./node_modules/core-js/modules/es.string.replace.js");
/* harmony import */ var core_js_modules_es_string_replace_js__WEBPACK_IMPORTED_MODULE_22___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_es_string_replace_js__WEBPACK_IMPORTED_MODULE_22__);
/* harmony import */ var core_js_modules_es_array_splice_js__WEBPACK_IMPORTED_MODULE_23__ = __webpack_require__(/*! core-js/modules/es.array.splice.js */ "./node_modules/core-js/modules/es.array.splice.js");
/* harmony import */ var core_js_modules_es_array_splice_js__WEBPACK_IMPORTED_MODULE_23___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_es_array_splice_js__WEBPACK_IMPORTED_MODULE_23__);
/* harmony import */ var _libs_flux__WEBPACK_IMPORTED_MODULE_24__ = __webpack_require__(/*! @/libs/flux */ "./resources/js/src/libs/flux.js");
























//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
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
  name: 'SpvDeclaratii',
  data: function data() {
    return {
      fisiere: [],
      dinFolder: [],
      declaratii: [],
      // Câte fișiere se trimit deodată la validare (vezi trimiteInGrupuri)
      FISIERE_DEODATA: 3,
      explicatie: null,
      explicatieVizibila: false,
      explicatieInCurs: false,
      // Câte probleme a anunțat serverul că are de prelucrat
      explicatieTotal: 0,
      explicatieEroare: '',
      // Ce declarație se semnează chiar acum, după încărcare
      semnareInCurs: '',

      /*
       * Drepturile omului în firma aleasă, cerute de la server. Ascunderea din
       * interfață e doar înlesnire; refuzul adevărat vine tot de la server.
       */
      poateSemna: false,
      poateDepune: false,
      // Semnarea de la sine a ce tocmai s-a validat
      semneazaDupaValidare: false,
      // La sfârșitul prelucrării, un singur fișier cu tot ce s-a semnat
      tiparire: true,

      /*
       * Depunerea de la sine a ce s-a semnat acum. Nebifată din start: la ANAF
       * nu se depune din greșeală, iar depunerea nu se poate retrage.
       */
      depuneDupaSemnare: false,
      // La fel, pentru recipisele aduse într-o rulare
      tiparireRecipise: true,
      // Denumirea firmei, scrisă în filigran pe fiecare pagină de recipisă
      filigran: true,
      depunereInCurs: false,
      depunereMesaj: '',
      // Descărcarea repetată a recipiselor, cu intervalul ales de utilizator
      automat: {
        activ: false,
        minute: 10
      },
      optiuniMinute: [{
        value: 5,
        text: '5 minute'
      }, {
        value: 10,
        text: '10 minute'
      }, {
        value: 15,
        text: '15 minute'
      }, {
        value: 30,
        text: '30 minute'
      }, {
        value: 60,
        text: '60 minute'
      }],
      cronometru: null,
      ultimaDescarcare: '',
      ultimaDescarcareNumar: 0,
      // Consistența SAF-T, în fereastra ei: declarația, rezultatul, mersul cererii
      consistentaVizibila: false,
      consistentaInCurs: false,
      consistentaEroare: '',
      consistentaDeclaratie: null,
      consistentaDate: null,
      campuriConsistenta: [{
        key: 'test',
        label: 'Test'
      }, {
        key: 'transactionid',
        label: 'Notă'
      }, {
        key: 'recordid',
        label: 'Linie'
      }, {
        key: 'accountid',
        label: 'Cont'
      }, {
        key: 'customerid',
        label: 'Client'
      }, {
        key: 'supplierid',
        label: 'Furnizor'
      }, {
        key: 'debitamount',
        label: 'Debit'
      }, {
        key: 'creditamount',
        label: 'Credit'
      }, {
        key: 'taxtype',
        label: 'Tip taxă'
      }, {
        key: 'taxcode',
        label: 'Cod TVA'
      }, {
        key: 'taxpercentage',
        label: 'Cotă'
      }, {
        key: 'taxbase',
        label: 'Bază'
      }, {
        key: 'taxamount',
        label: 'TVA'
      }],
      // Potrivirea dintre decontul depus și cel din SAF-T
      potrivireVizibila: false,
      potrivireInCurs: false,
      potrivireEroare: '',
      potrivireDate: null,
      potrivireDeclaratie: null,
      campuriPotrivire: [{
        key: 'rand',
        label: 'Rând'
      }, {
        key: 'din_saft',
        label: 'Din SAF-T',
        "class": 'text-right'
      }, {
        key: 'din_d300',
        label: 'Din D300',
        "class": 'text-right'
      }, {
        key: 'diferenta',
        label: 'Diferență',
        "class": 'text-right'
      }],
      // Decontul de TVA scos din SAF-T, în fereastra lui
      decontVizibil: false,
      decontInCurs: false,
      decontEroare: '',
      decontDate: null,
      decontDeclaratie: null,
      campuriDecont: [{
        key: 'eticheta',
        label: 'Rând'
      }, {
        key: 'valoare',
        label: 'Valoare',
        "class": 'text-right'
      }],
      // Id-urile declarațiilor la care eroarea e arătată întreagă
      eroriDesfasurate: [],
      eroare: '',
      listaInCurs: false,
      incarcaInCurs: false,
      recipiseInCurs: false,
      // Mersul lucrului, cat timp se cerceteaza: „7 din 42 — D112 15208744"
      mersul: '',
      cercetate: 0,
      deCercetat: 0,
      ocupat: null,
      filtre: {
        tip: '',
        cui: '',
        den_firma: '',
        luna: '',
        anul: '',
        index_recipisa: '',
        pas: null
      },
      optiuniPas: [{
        value: null,
        text: 'Toate'
      }, // „Încărcate" și „Validate" sunt pași de trecere: după încărcare,
      // declarațiile ajung singure la semnat sau la eroare de validare.
      // Fisierele din dosarul urmarit care n-au putut fi citite deloc
      {
        value: 'eroare_preluare',
        text: 'Nu au putut fi citite'
      }, {
        value: 'eroare_validare',
        text: 'Erori de validare'
      }, {
        value: 'eroare_semnare',
        text: 'Erori la semnare'
      }, {
        value: 'semnat',
        text: 'Semnate'
      }, {
        value: 'depus',
        text: 'Depuse'
      }, {
        value: 'finalizat',
        text: 'Finalizate'
      }, {
        value: 'eroare_depunere',
        text: 'Erori la depunere'
      }],
      campuri: [{
        key: 'tip',
        label: 'Tip'
      }, {
        key: 'cui',
        label: 'CUI'
      }, {
        key: 'den_firma',
        label: 'Denumire'
      }, {
        key: 'perioada',
        label: 'Perioada'
      }, {
        key: 'pas',
        label: 'Stare flux'
      }, {
        key: 'eroare',
        label: 'Eroare'
      }, {
        key: 'consistenta',
        label: 'Consistență'
      }, {
        key: 'potrivire',
        label: 'D300 ↔ SAF-T'
      }, {
        key: 'index_recipisa',
        label: 'Index încărcare'
      }, {
        key: 'data_depunere',
        label: 'Depusă la'
      }, {
        key: 'stare_declaratie',
        label: 'Rezultat ANAF'
      }, {
        key: 'certificat_nume',
        label: 'Semnat cu'
      }, {
        key: 'actiuni',
        label: 'Acțiuni'
      }],
      pagina: 1,
      // „auto" înseamnă câte rânduri încap pe ecran fără derulare.
      pePaginaAles: 'auto',
      pePaginaAuto: 15,
      marimiPagina: [{
        value: 'auto',
        text: 'cât încape'
      }, {
        value: 10,
        text: '10 / pagină'
      }, {
        value: 25,
        text: '25 / pagină'
      }, {
        value: 50,
        text: '50 / pagină'
      }, {
        value: 100,
        text: '100 / pagină'
      }]
    };
  },
  computed: {
    /** Fișierele selectate direct și cele din folder, doar XML și PDF. */
    deIncarcat: function deIncarcat() {
      return [].concat(Object(C_dianasoft_serviciiweb_web_node_modules_babel_runtime_helpers_esm_toConsumableArray_js__WEBPACK_IMPORTED_MODULE_1__["default"])(this.fisiere || []), Object(C_dianasoft_serviciiweb_web_node_modules_babel_runtime_helpers_esm_toConsumableArray_js__WEBPACK_IMPORTED_MODULE_1__["default"])(this.dinFolder || [])).filter(function (fisier) {
        return /\.(xml|pdf)$/i.test(fisier.name);
      });
    },

    /**
     * Declarațiile semnate care încă n-au ajuns la ANAF.
     *
     * Se ia din lista afișată, deci filtrele active se aplică și aici: se
     * depune ce se vede, nu ce nu se vede.
     */
    deDepus: function deDepus() {
      // Istoricul importat din programul vechi (pas „finalizat") e încheiat:
      // n-are ce căuta la depunere, oricât ar fi de semnat.
      return this.declaratii.filter(function (d) {
        return d.semnat && !d.index_recipisa && d.pas !== 'finalizat';
      });
    },

    /**
     * Declarațiile trecute de validare care așteaptă semnătura.
     *
     * Ca și la depunere, se ia din lista afișată: se semnează ce se vede.
     */
    deSemnat: function deSemnat() {
      return this.declaratii.filter(function (d) {
        return d.pas === 'validat';
      });
    },

    /**
     * Declarațiile depuse a căror recipisă n-a fost încă descărcată.
     *
     * Istoricul importat din programul vechi (pas „finalizat") rămâne pe
     * dinafară: recipisele acelea nu mai sunt de adus din SPV.
     */
    deDescarcat: function deDescarcat() {
      return this.declaratii.filter(function (d) {
        return d.index_recipisa && !d.cale_recipisa && !d.arhiva_recipisa && d.pas !== 'finalizat';
      });
    },

    /** Ce a adus ultima descărcare, scris pe scurt. */
    recipiseAduse: function recipiseAduse() {
      if (!this.ultimaDescarcareNumar) return 'fără recipise noi';
      return this.ultimaDescarcareNumar === 1 ? 'o recipisă' : "".concat(this.ultimaDescarcareNumar, " recipise");
    },

    /**
     * Butonul lucrează cât ține oricare dintre pași — inclusiv depunerea
     * pornită de bifa de sub el, ca să nu se înceapă un lot nou peste ea.
     */
    lucreaza: function lucreaza() {
      return this.incarcaInCurs || !!this.semnareInCurs || this.depunereInCurs;
    },

    /**
     * Ce pas se face acum.
     *
     * Încărcarea și validarea se fac în aceeași cerere — serverul validează
     * fiecare fișier pe măsură ce îl primește — deci ele se aprind împreună.
     */
    pasulActiv: function pasulActiv() {
      if (this.semnareInCurs) return 'semneaza';
      return this.incarcaInCurs ? 'incarca' : '';
    },

    /** După încărcare, primii doi pași rămân bifați cât ține semnarea. */
    incarcaTerminat: function incarcaTerminat() {
      return !!this.semnareInCurs;
    },

    /** Câte rânduri se arată: fie alegerea omului, fie cât încape pe ecran. */
    pePagina: function pePagina() {
      return this.pePaginaAles === 'auto' ? this.pePaginaAuto : this.pePaginaAles;
    },

    /** Numărul primului rând de pe pagina curentă. */
    deLaRand: function deLaRand() {
      return this.declaratii.length ? (this.pagina - 1) * this.pePagina + 1 : 0;
    },

    /** Numărul ultimului rând de pe pagina curentă. */
    panaLaRand: function panaLaRand() {
      return Math.min(this.pagina * this.pePagina, this.declaratii.length);
    }
  },
  watch: {
    // După filtrare sau reîncărcare, pagina veche poate să nu mai existe.
    declaratii: function declaratii() {
      var ultima = Math.max(1, Math.ceil(this.declaratii.length / this.pePagina));
      if (this.pagina > ultima) this.pagina = ultima; // Abia acum există rânduri de măsurat, ca să se știe câte încap.

      this.$nextTick(this.masoaraPagina);
    },
    pePagina: function pePagina() {
      this.pagina = 1;
    },
    // Orice schimbare a setării repornește cronometrul și o ține minte.
    'automat.activ': function reporneste() {
      this.salveazaSetarea();
      this.reglaCronometrul();
    },
    'automat.minute': function reporneste() {
      this.salveazaSetarea();
      this.reglaCronometrul();
    }
  },
  created: function created() {
    this.incarcaDrepturile();
    this.incarcaSetarea();
    this.incarcaLista();
    this.reglaCronometrul();
  },
  mounted: function mounted() {
    this.masoaraPagina();
    window.addEventListener('resize', this.masoaraPagina);
  },
  beforeDestroy: function beforeDestroy() {
    // Fără asta, cronometrul ar continua să ceară recipise după plecarea din filă.
    this.opresteCronometrul();
    window.removeEventListener('resize', this.masoaraPagina);
  },
  methods: {
    /**
     * Câte rânduri încap pe ecran sub tabel, fără derulare.
     *
     * Se măsoară un rând adevărat, nu o valoare presupusă: înălțimea depinde de
     * temă, de zoom-ul din browser și de ce scrie în coloana de eroare.
     */
    masoaraPagina: function masoaraPagina() {
      var tabel = this.$refs.tabel && this.$refs.tabel.$el;
      if (!tabel) return;
      var rand = tabel.querySelector('tbody tr');
      var antet = tabel.querySelector('thead');
      var inaltimeRand = rand ? rand.getBoundingClientRect().height : 0;
      var inaltimeAntet = antet ? antet.getBoundingClientRect().height : 0; // Sub tabel mai stau bara de paginare și marginea de jos a paginii.

      var REZERVA = 70;
      var disponibil = window.innerHeight - tabel.getBoundingClientRect().top - inaltimeAntet - REZERVA;
      var incap = Math.floor(disponibil / (inaltimeRand || 31)); // Sub cinci rânduri tabelul nu mai spune nimic; mai bine se derulează.

      this.pePaginaAuto = Math.max(5, incap);
    },

    /**
     * Ce înseamnă lipsa firmei din Entități înrolate și ce are de făcut omul.
     *
     * Textul e lung pentru un tooltip, dar aici stă tot ce ar trebui altfel
     * căutat prin altă filă: de ce apare, ce merge totuși și cum se rezolvă.
     */
    explicatieNeinrolata: function explicatieNeinrolata(declaratie) {
      var cui = declaratie.cui || 'acest CUI';
      return ['<div class="text-left">', '<b>Firma nu este înrolată</b><br>', "CUI-ul <b>".concat(cui, "</b> nu apare \xEEn <i>Entit\u0103\u021Bi \xEEnrolate</i>, adic\u0103 niciun certificat "), 'din aplicație nu are drept de reprezentare la ANAF pentru el.', '<hr class="my-50">', '<b>Denumirea</b> afișată este cea scrisă în declarație, nu cea de la ANAF, ', 'deci poate fi prescurtată sau greșită. Tot ea ajunge și pe watermarkul recipisei.', '<hr class="my-50">', '<b>Validarea și semnarea</b> se pot face. <b>Depunerea</b> va fi respinsă de SPV.', '<hr class="my-50">', '<b>De făcut:</b> depuneți la ANAF formularul 150 pentru acest CUI, ', 'iar după aprobare apăsați <i>Inițializează / actualizează lista</i> în fila ', '<i>Entități înrolate</i>. ', 'Dacă firma e deja înrolată cu alt certificat, adăugați acel certificat în ', 'fila <i>Certificate digitale</i>.', '</div>'].join('');
    },
    incarcaSetarea: function incarcaSetarea() {
      try {
        var salvat = JSON.parse(window.localStorage.getItem('declaratii_recipise_automat'));

        if (salvat && typeof salvat.minute === 'number' && salvat.minute > 0) {
          // O valoare salvată înainte poate să nu fie printre cele din listă;
          // atunci lista ar rămâne goală, așa că se ia cea mai apropiată.
          var permise = this.optiuniMinute.map(function (o) {
            return o.value;
          });
          var minute = permise.indexOf(salvat.minute) !== -1 ? salvat.minute : permise.reduce(function (a, b) {
            return Math.abs(b - salvat.minute) < Math.abs(a - salvat.minute) ? b : a;
          });
          this.automat = {
            activ: !!salvat.activ,
            minute: minute
          };
        }
      } catch (e) {// setare veche sau stricată — se rămâne pe valorile implicite
      }

      var brut = window.localStorage.getItem('declaratii_recipise_ultima') || '';

      try {
        var ultima = JSON.parse(brut);
        this.ultimaDescarcare = ultima.la || '';
        this.ultimaDescarcareNumar = ultima.descarcate || 0;
      } catch (e) {
        // Forma veche păstra doar momentul, ca text simplu.
        this.ultimaDescarcare = brut;
        this.ultimaDescarcareNumar = 0;
      }
    },

    /** Momentul de acum, scris ca peste tot în modul: zz.ll.aaaa hh:mm:ss. */
    acum: function acum() {
      var d = new Date();

      var doua = function doua(n) {
        return String(n).padStart(2, '0');
      };

      return "".concat(doua(d.getDate()), ".").concat(doua(d.getMonth() + 1), ".").concat(d.getFullYear(), " ") + "".concat(doua(d.getHours()), ":").concat(doua(d.getMinutes()), ":").concat(doua(d.getSeconds()));
    },
    salveazaSetarea: function salveazaSetarea() {
      window.localStorage.setItem('declaratii_recipise_automat', JSON.stringify(this.automat));
    },
    opresteCronometrul: function opresteCronometrul() {
      if (this.cronometru) {
        window.clearInterval(this.cronometru);
        this.cronometru = null;
      }
    },
    reglaCronometrul: function reglaCronometrul() {
      var _this = this;

      this.opresteCronometrul();
      var minute = Number(this.automat.minute);

      if (!this.automat.activ || !minute || minute < 1) {
        return;
      }

      this.cronometru = window.setInterval(function () {
        // Se cere ANAF-ului doar când chiar e ceva de adus: fără recipise în
        // așteptare, o interogare la fiecare interval ar fi în gol. Și nu se
        // suprapune peste o descărcare pornită de mână sau peste ea însăși.
        if (!_this.recipiseInCurs && _this.deDescarcat.length) {
          _this.verificaRecipise(true);
        }
      }, minute * 60 * 1000);
    },
    // Fisierele stau pe discul privat (storage/app), deci se cer prin API (cu
    // token) si se deschid dintr-un blob local, nu printr-un link catre /storage.
    deschide: function deschide(declaratie, tip) {
      var _this2 = this;

      this.eroare = '';
      this.$http.get("/declaratii/".concat(declaratie.id, "/fisier/").concat(tip), {
        responseType: 'blob'
      }).then(function (raspuns) {
        var url = window.URL.createObjectURL(new Blob([raspuns.data], {
          type: 'application/pdf'
        }));
        window.open(url, '_blank');
        setTimeout(function () {
          return window.URL.revokeObjectURL(url);
        }, 60000);
      })["catch"](function () {
        _this2.eroare = 'Fișierul nu a putut fi deschis.';
      });
    },
    etichetaPas: function etichetaPas(pas) {
      var etichete = {
        incarcat: 'Încărcată',
        validat: 'Validată',
        eroare_preluare: 'Nu a putut fi citită',
        eroare_validare: 'Eroare validare',
        eroare_semnare: 'Eroare semnare',
        semnat: 'Semnată',
        depus: 'Depusă',
        finalizat: 'Finalizată',
        eroare_depunere: 'Eroare depunere'
      };
      return etichete[pas] || pas;
    },
    variantaPas: function variantaPas(pas) {
      if (pas === 'finalizat') return 'success';
      if (pas === 'depus' || pas === 'semnat') return 'info';
      if (pas === 'validat') return 'primary';
      if (pas && pas.indexOf('eroare') === 0) return 'danger';
      return 'secondary';
    },
    clasaStare: function clasaStare(clasificare) {
      if (clasificare === 'valid') return 'text-success';
      if (clasificare === 'invalid') return 'text-danger';
      if (clasificare === 'valid_cu_atentionari') return 'text-warning';
      return 'text-muted';
    },
    incarcaLista: function incarcaLista() {
      var _this3 = this;

      this.listaInCurs = true; // Se trimit doar filtrele completate, ca serverul să nu le trateze pe
      // cele goale ca pe o căutare după șir vid.

      var params = {};
      Object.keys(this.filtre).forEach(function (cheie) {
        var valoare = _this3.filtre[cheie];

        if (valoare !== null && valoare !== '') {
          params[cheie] = valoare;
        }
      });
      this.$http.get('/declaratii', {
        params: params
      }).then(function (raspuns) {
        _this3.declaratii = raspuns.data.data || [];
      })["catch"](function (err) {
        _this3.eroare = _this3.mesajEroare(err, 'Nu s-au putut încărca declarațiile');
      })["finally"](function () {
        _this3.listaInCurs = false;
      });
    },
    // Din folder se rețin doar declarațiile, restul fișierelor se ignoră.
    numeFisiere: function numeFisiere(fisiere) {
      var declaratii = fisiere.filter(function (f) {
        return /\.(xml|pdf)$/i.test(f.name);
      });
      if (declaratii.length === 1) return declaratii[0].name;
      return "".concat(declaratii.length, " declara\u021Bii din ").concat(fisiere.length, " fi\u0219iere");
    },

    /**
     * Ce are voie omul să facă aici.
     *
     * Se cere de la server, nu din localStorage: drepturile se pot schimba, iar
     * bifele care le privesc n-au ce căuta pe ecranul cuiva care nu le are.
     */
    incarcaDrepturile: function incarcaDrepturile() {
      var _this4 = this;

      this.$http.get('/context').then(function (_ref) {
        var data = _ref.data;
        _this4.poateSemna = !!data.data.poate_semna;
        _this4.poateDepune = !!data.data.poate_depune;
      })["catch"](function () {
        _this4.poateSemna = false;
        _this4.poateDepune = false;
      }).then(function () {
        // Bifele rămase fără drept nu trebuie să tragă după ele nicio acțiune.
        if (!_this4.poateSemna) _this4.semneazaDupaValidare = false;
        if (!_this4.poateDepune) _this4.depuneDupaSemnare = false;
      });
    },

    /**
     * Trimite fișierele în grupuri care merg deodată.
     *
     * Într-o singură cerere, serverul le lua unul câte unul, iar fiecare trece
     * prin validatorul ANAF — un program Java care pornește din nou pentru
     * fiecare fișier și ține câteva secunde. Zece declarații însemnau astfel
     * minute de așteptare, cu un singur fir de lucru ocupat. Trimise câte trei
     * deodată, serverul le validează în paralel.
     *
     * Mai mult de atât n-are rost: validatorul mănâncă procesor, iar cererile
     * peste puterea serverului s-ar aștepta oricum una pe alta.
     */
    trimiteInGrupuri: function trimiteInGrupuri(fisiere, cateOdata) {
      var _this5 = this;

      var rezultate = [];
      var erori = [];

      var unul = function unul(fisier) {
        var formular = new FormData();
        formular.append('fisiere[]', fisier);
        return _this5.$http.post('/declaratii', formular, {
          headers: {
            'Content-Type': 'multipart/form-data'
          }
        }).then(function (raspuns) {
          rezultate.push.apply(rezultate, Object(C_dianasoft_serviciiweb_web_node_modules_babel_runtime_helpers_esm_toConsumableArray_js__WEBPACK_IMPORTED_MODULE_1__["default"])(raspuns.data.data || []));
          erori.push.apply(erori, Object(C_dianasoft_serviciiweb_web_node_modules_babel_runtime_helpers_esm_toConsumableArray_js__WEBPACK_IMPORTED_MODULE_1__["default"])(raspuns.data.erori || []));
        })["catch"](function (err) {
          var date = err.response && err.response.data;

          if (date && date.erori && date.erori.length) {
            erori.push.apply(erori, Object(C_dianasoft_serviciiweb_web_node_modules_babel_runtime_helpers_esm_toConsumableArray_js__WEBPACK_IMPORTED_MODULE_1__["default"])(date.erori));
          } else {
            erori.push("".concat(fisier.name, ": ").concat(_this5.mesajEroare(err, 'nu a putut fi încărcat')));
          }
        });
      }; // Grupurile pleacă unul după altul, dar fișierele dintr-un grup, deodată.


      var lant = Promise.resolve();

      var _loop = function _loop(i) {
        var grup = fisiere.slice(i, i + cateOdata);
        lant = lant.then(function () {
          return Promise.all(grup.map(unul));
        });
      };

      for (var i = 0; i < fisiere.length; i += cateOdata) {
        _loop(i);
      }

      return lant.then(function () {
        return {
          rezultate: rezultate,
          erori: erori
        };
      });
    },
    incarca: function incarca() {
      var _this6 = this;

      this.eroare = '';
      this.incarcaInCurs = true;
      this.trimiteInGrupuri(this.deIncarcat.slice(), this.FISIERE_DEODATA).then(function (_ref2) {
        var incarcate = _ref2.rezultate,
            erori = _ref2.erori;
        _this6.fisiere = [];
        _this6.dinFolder = [];
        var respinse = incarcate.filter(function (d) {
          return d.pas === 'eroare_validare';
        }).length;
        var dejaSemnate = incarcate.filter(function (d) {
          return d.pas === 'semnat';
        }).length;
        var parti = ["".concat(incarcate.length, " \xEEnc\u0103rcate")];
        if (dejaSemnate) parti.push("".concat(dejaSemnate, " veneau deja semnate"));
        if (respinse) parti.push("".concat(respinse, " respinse la validare"));

        _this6.notifica(parti.join(', '), respinse || erori.length ? 'warning' : 'success');

        if (erori.length) {
          _this6.eroare = erori.join(' | ');
        } // Cele validate se semnează acum doar dacă s-a cerut prin bifă; cele
        // care veneau deja semnate (PDF-uri semnate în altă parte) rămân cum
        // sunt, dar intră la depunere: sunt și ele semnate în această sesiune.


        var deSemnatAcum = _this6.semneazaDupaValidare && _this6.poateSemna ? incarcate.filter(function (d) {
          return d.pas === 'validat';
        }) : [];
        return _this6.semneazaValidate(deSemnatAcum, incarcate.filter(function (d) {
          return d.pas === 'semnat';
        }));
      })["catch"](function (err) {
        var date = err.response && err.response.data;
        _this6.eroare = date && date.erori && date.erori.length ? date.erori.join(' | ') : _this6.mesajEroare(err, 'Nu s-au putut încărca declarațiile');
      })["finally"](function () {
        _this6.incarcaInCurs = false;

        _this6.incarcaLista();
      });
    },

    /**
     * Semnează, una câte una, declarațiile tocmai validate.
     *
     * Semnarea se face pe rând, nu deodată: fiecare trece prin tokenul de pe
     * calculatorul cu certificatul, iar cererile paralele s-ar încurca la
     * dialogul de PIN. O semnare eșuată nu le oprește pe următoarele —
     * declarația rămâne validată și poate fi semnată din tabel.
     *
     * @param {Array} declaratii     cele validate, de semnat acum
     * @param {Array} venitesemnate  cele încărcate deja semnate, doar de depus
     */

    /**
     * Semnează declarațiile valide rămase în tabel.
     *
     * E aceeași lucrare ca după încărcare, doar că pornită de om, peste tot ce
     * s-a strâns nesemnat — nu doar peste lotul din sesiunea de acum.
     */
    semneazaDinTabel: function semneazaDinTabel() {
      var _this7 = this;

      var declaratii = this.deSemnat;

      if (!declaratii.length) {
        return Promise.resolve();
      }

      this.eroare = '';
      return this.semneazaValidate(declaratii).then(function () {
        return _this7.incarcaLista();
      });
    },
    semneazaValidate: function semneazaValidate(declaratii) {
      var _this8 = this;

      var venitesemnate = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : [];

      if (!declaratii.length) {
        return this.depuneDinSesiune(venitesemnate);
      }

      var esuate = [];
      var reusite = [];

      var urmatoarea = function urmatoarea(i) {
        if (i >= declaratii.length) {
          if (esuate.length) {
            _this8.eroare = "Nu s-au putut semna: ".concat(esuate.join(' | '));
          }

          _this8.notifica("".concat(declaratii.length - esuate.length, " din ").concat(declaratii.length, " semnate"), esuate.length ? 'warning' : 'success');

          return null;
        }

        var declaratie = declaratii[i];
        _this8.semnareInCurs = "".concat(declaratie.tip, " (").concat(i + 1, " din ").concat(declaratii.length, ")");
        return _this8.$http.post("/declaratii/".concat(declaratie.id, "/semneaza")).then(function () {
          reusite.push(declaratie);
        })["catch"](function (err) {
          esuate.push("".concat(declaratie.tip, " ").concat(declaratie.cui, ": ").concat(_this8.mesajEroare(err, 'semnare eșuată')));
        }).then(function () {
          return urmatoarea(i + 1);
        });
      };

      return urmatoarea(0).then(function () {
        _this8.semnareInCurs = ''; // Se adună doar cele semnate acum, nu tot ce e semnat în tabel.

        if (_this8.tiparire && reusite.length) {
          _this8.semnareInCurs = 'fișierul pentru tipărire';
          return _this8.descarcaPentruTiparire(reusite.map(function (d) {
            return d.id;
          })).then(function () {
            _this8.semnareInCurs = '';
          });
        }

        return null;
      }).then(function () {
        return _this8.depuneDinSesiune([].concat(Object(C_dianasoft_serviciiweb_web_node_modules_babel_runtime_helpers_esm_toConsumableArray_js__WEBPACK_IMPORTED_MODULE_1__["default"])(venitesemnate), reusite));
      });
    },

    /**
     * Depunerea de la sine, când bifa de sub buton e pusă.
     *
     * Nu se mai cere confirmare: bifa e chiar consimțământul, dată de fiecare
     * dată de la zero. Pleacă doar declarațiile din această sesiune; restul
     * celor semnate în tabel rămân pe butonul „Depune”.
     */
    depuneDinSesiune: function depuneDinSesiune(declaratii) {
      if (!this.depuneDupaSemnare || !this.poateDepune || !declaratii.length) {
        return Promise.resolve();
      }

      return this.trimiteLaAnaf(declaratii);
    },

    /**
     * Trimite documentele la imprimanta utilizatorului.
     *
     * Hârtia iese pe calculatorul lui, nu se mai descarcă niciun fișier. Dacă
     * n-are imprimantă aleasă sau calculatorul e închis, se cade pe descărcare:
     * mai bine un fișier de tipărit de mână decât nimic după o semnare reușită.
     *
     * @param {string} tip 'semnat' pentru declarații, 'recipisa' pentru recipise
     */
    descarcaPentruTiparire: function descarcaPentruTiparire(ids) {
      var _this9 = this;

      var tip = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : 'semnat';
      // Filigranul cu denumirea firmei se pune deocamdată doar pe recipise.
      var date = {
        id: ids,
        tip: tip,
        filigran: tip === 'recipisa' && this.filigran
      };
      var ceAnume = tip === 'recipisa' ? 'Recipisele' : 'Declarațiile';
      return this.$http.post('/declaratii/concateneaza', Object(C_dianasoft_serviciiweb_web_node_modules_babel_runtime_helpers_esm_objectSpread2_js__WEBPACK_IMPORTED_MODULE_0__["default"])(Object(C_dianasoft_serviciiweb_web_node_modules_babel_runtime_helpers_esm_objectSpread2_js__WEBPACK_IMPORTED_MODULE_0__["default"])({}, date), {}, {
        tipareste: true
      })).then(function (_ref3) {
        var data = _ref3.data;

        _this9.$bvToast.toast("".concat(data.data.documente, " documente trimise la \u201E").concat(data.data.imprimanta, "\u201D."), {
          title: 'Trimis la imprimantă',
          variant: 'success'
        });
      })["catch"](function (err) {
        var motiv = err.response && err.response.data && err.response.data.message ? err.response.data.message : 'Programul local nu a răspuns.';
        _this9.eroare = "".concat(ceAnume, " sunt gata, dar tip\u0103rirea nu a reu\u0219it: ").concat(motiv, " Se descarc\u0103 fi\u0219ierul.");
        return _this9.descarcaFisierUnit(date, ids.length);
      });
    },

    /** Varianta de rezervă: un singur PDF, salvat local. */
    descarcaFisierUnit: function descarcaFisierUnit(date, cate) {
      var _this10 = this;

      var numeFisier = date.tip === 'recipisa' ? "recipise_".concat(cate, ".pdf") : "declaratii_semnate_".concat(cate, ".pdf");
      return this.$http.post('/declaratii/concateneaza', date, {
        responseType: 'blob'
      }).then(function (raspuns) {
        var url = window.URL.createObjectURL(new Blob([raspuns.data], {
          type: 'application/pdf'
        }));
        var legatura = document.createElement('a');
        legatura.href = url;
        legatura.download = numeFisier;
        document.body.appendChild(legatura);
        legatura.click();
        document.body.removeChild(legatura);
        setTimeout(function () {
          return window.URL.revokeObjectURL(url);
        }, 60000);
      })["catch"](function () {
        _this10.eroare = 'Nici fișierul pentru tipărire nu a putut fi creat.';
      });
    },

    /**
     * Depune, una câte una, toate declarațiile semnate din listă.
     *
     * Se cere confirmarea o dată, dar cu firmele numite: depunerea la ANAF nu
     * se poate lua înapoi, iar aici pleacă mai multe declarații dintr-o
     * singură apăsare.
     */
    depuneSemnate: function depuneSemnate() {
      var _this11 = this;

      var declaratii = this.deDepus;

      if (!declaratii.length) {
        return;
      }

      var cate = declaratii.length;
      var numite = cate === 1 ? 'o declarație semnată' : "".concat(cate, " declara\u021Bii semnate");

      var firme = Object(C_dianasoft_serviciiweb_web_node_modules_babel_runtime_helpers_esm_toConsumableArray_js__WEBPACK_IMPORTED_MODULE_1__["default"])(new Set(declaratii.map(function (d) {
        return d.den_firma || d.cui;
      })));

      var listate = firme.slice(0, 4).join(', ') + (firme.length > 4 ? ' și altele' : '');
      this.$bvModal.msgBoxConfirm("Se trimit acum la ANAF ".concat(numite, " (").concat(listate, "). Dup\u0103 trimitere nu mai pot fi retrase."), {
        okTitle: 'Depune',
        cancelTitle: 'Renunță',
        okVariant: 'danger'
      }).then(function (confirmat) {
        if (!confirmat) {
          return null;
        }

        return _this11.trimiteLaAnaf(declaratii);
      });
    },

    /** Trimiterea propriu-zisă: una câte una, un eșec nu le oprește pe următoarele. */

    /**
     * Trimite declarațiile la ANAF dintr-o singură cerere.
     *
     * Pe rând, fiecare declarație însemna o cerere a ei: patruzeci de porniri
     * ale aplicației și tot atâtea autentificări, pentru o lucrare care e una
     * singură. Serverul le ia acum pe toate și spune pe drum a câta e din câte,
     * iar mesajul de sub buton se face din ce spune el.
     *
     * Browserele fără fetch cu flux rămân pe calea dinainte: ea merge oriunde.
     */
    trimiteLaAnaf: function trimiteLaAnaf(declaratii) {
      var _this12 = this;

      this.eroare = '';
      this.depunereInCurs = true;

      if (!Object(_libs_flux__WEBPACK_IMPORTED_MODULE_24__["areFlux"])()) {
        return this.trimiteLaAnafPeRand(declaratii);
      }

      var esuate = [];
      var depuse = 0;
      return Object(_libs_flux__WEBPACK_IMPORTED_MODULE_24__["default"])('/declaratii/depune/flux', function (pas) {
        if (pas.tip === 'pas') {
          _this12.depunereMesaj = "".concat(pas.ce, " (").concat(pas.facute, " din ").concat(pas.total, ")");
        }

        if (pas.tip === 'gata') {
          depuse = pas.depuse;
          esuate.push.apply(esuate, Object(C_dianasoft_serviciiweb_web_node_modules_babel_runtime_helpers_esm_toConsumableArray_js__WEBPACK_IMPORTED_MODULE_1__["default"])(pas.erori || []));
        }
      }, {
        corp: {
          id: declaratii.map(function (declaratie) {
            return declaratie.id;
          })
        }
      })["catch"](function (err) {
        _this12.eroare = "Depunerea nu s-a putut face: ".concat(err.message || err);
      }).then(function () {
        if (esuate.length) {
          _this12.eroare = "Nu s-au putut depune: ".concat(esuate.join(' | '));
        }

        if (!_this12.eroare || esuate.length) {
          _this12.notifica("".concat(depuse, " din ").concat(declaratii.length, " depuse"), esuate.length ? 'warning' : 'success');
        }

        _this12.depunereInCurs = false;
        _this12.depunereMesaj = '';

        _this12.incarcaLista();
      });
    },

    /** Calea dinainte: câte o cerere de fiecare declarație. */
    trimiteLaAnafPeRand: function trimiteLaAnafPeRand(declaratii) {
      var _this13 = this;

      var esuate = [];

      var urmatoarea = function urmatoarea(i) {
        if (i >= declaratii.length) {
          if (esuate.length) {
            _this13.eroare = "Nu s-au putut depune: ".concat(esuate.join(' | '));
          }

          _this13.notifica("".concat(declaratii.length - esuate.length, " din ").concat(declaratii.length, " depuse"), esuate.length ? 'warning' : 'success');

          return null;
        }

        var declaratie = declaratii[i];
        _this13.depunereMesaj = "".concat(declaratie.tip, " (").concat(i + 1, " din ").concat(declaratii.length, ")");
        return _this13.$http.post("/declaratii/".concat(declaratie.id, "/depune"))["catch"](function (err) {
          esuate.push("".concat(declaratie.tip, " ").concat(declaratie.cui, ": ").concat(_this13.mesajEroare(err, 'depunere eșuată')));
        }).then(function () {
          return urmatoarea(i + 1);
        });
      };

      return urmatoarea(0).then(function () {
        _this13.depunereInCurs = false;
        _this13.depunereMesaj = '';

        _this13.incarcaLista();
      });
    },
    actiune: function actiune(declaratie, tip) {
      var _this14 = this;

      this.eroare = '';
      this.ocupat = declaratie.id;
      var mesaje = {
        semneaza: 'Declarația a fost semnată',
        depune: 'Declarația a fost depusă la ANAF'
      };
      this.$http.post("/declaratii/".concat(declaratie.id, "/").concat(tip)).then(function () {
        _this14.notifica(mesaje[tip], 'success');

        _this14.incarcaLista();
      })["catch"](function (err) {
        _this14.eroare = _this14.mesajEroare(err, 'Operația a eșuat');

        _this14.incarcaLista();
      })["finally"](function () {
        _this14.ocupat = null;
      });
    },

    /**
     * Rezultatul verificării de consistență, pentru declarația aleasă.
     *
     * Liniile găsite nu vin odată cu tabelul — la un SAF-T încâlcit sunt cu
     * miile —, așa că se cer abia acum, când e cine să le vadă.
     */
    deschideConsistenta: function deschideConsistenta(declaratie) {
      var _this15 = this;

      this.consistentaDeclaratie = declaratie;
      this.consistentaDate = null;
      this.consistentaEroare = '';
      this.consistentaVizibila = true;
      this.consistentaInCurs = true;
      this.$http.get("/declaratii/".concat(declaratie.id, "/consistenta")).then(function (raspuns) {
        _this15.consistentaDate = raspuns.data.data;
      })["catch"](function (err) {
        _this15.consistentaEroare = _this15.mesajEroare(err, 'Rezultatul verificării nu a putut fi citit');
      })["finally"](function () {
        _this15.consistentaInCurs = false;
      });
    },

    /** Rândurile care nu se potrivesc, aduse de la server. */
    deschidePotrivirea: function deschidePotrivirea(declaratie) {
      var _this16 = this;

      this.potrivireDeclaratie = declaratie;
      this.potrivireDate = null;
      this.potrivireEroare = '';
      this.potrivireVizibila = true;
      this.potrivireInCurs = true;
      this.$http.get("/declaratii/".concat(declaratie.id, "/potrivire")).then(function (raspuns) {
        _this16.potrivireDate = raspuns.data.data;
      })["catch"](function (err) {
        _this16.potrivireEroare = _this16.mesajEroare(err, 'Rezultatul comparației nu a putut fi citit');
      })["finally"](function () {
        _this16.potrivireInCurs = false;
      });
    },

    /**
     * Decontul de TVA socotit din jurnalele declarației.
     *
     * Se socotește de fiecare dată, nu se ține minte: e o privire asupra
     * fișierului, nu o declarație care să aibă nevoie de istoric.
     */
    deschideDecontul: function deschideDecontul(declaratie) {
      var _this17 = this;

      this.decontDeclaratie = declaratie;
      this.decontDate = null;
      this.decontEroare = '';
      this.decontVizibil = true;
      this.decontInCurs = true;
      this.ocupat = declaratie.id;
      this.$http.post("/declaratii/".concat(declaratie.id, "/decont")).then(function (raspuns) {
        _this17.decontDate = raspuns.data.data;
      })["catch"](function (err) {
        _this17.decontEroare = _this17.mesajEroare(err, 'Decontul nu a putut fi scos');
      })["finally"](function () {
        _this17.decontInCurs = false;
        _this17.ocupat = null;
      });
    },

    /**
     * Declarația D300 scrisă din decont, adusă ca fișier.
     *
     * Se cere prin XHR, ca restul: așa vin și mesajele de eroare pe înțeles —
     * de pildă când mai lipsește ceva din antetul luat din fișa firmei.
     */
    descarcaDecontXml: function descarcaDecontXml() {
      this.aduFisierulDecontului('xml', 'D300.xml', 'Declarația D300 nu a putut fi scrisă');
    },

    /**
     * Aceleași cifre, scrise pentru PDF-ul inteligent al ANAF.
     *
     * ANAF n-a pus buton de încărcare în formular; fișierul se ia din Acrobat
     * Reader, din „Import Data".
     */
    descarcaDecontFormular: function descarcaDecontFormular() {
      this.aduFisierulDecontului('formular', 'D300_formular.xml', 'Fișierul pentru formularul ANAF nu a putut fi scris');
    },

    /** Cere serverului fișierul cerut și îl dă omului. */
    aduFisierulDecontului: function aduFisierulDecontului(fel, numeImplicit, mesajDeEroare) {
      var _this18 = this;

      this.decontEroare = '';
      this.decontInCurs = true;
      this.$http.post("/declaratii/".concat(this.decontDeclaratie.id, "/decont/").concat(fel), {}, {
        responseType: 'blob'
      }).then(function (raspuns) {
        var nume = raspuns.headers['x-nume-fisier'] || numeImplicit;
        var url = window.URL.createObjectURL(new Blob([raspuns.data], {
          type: 'application/xml'
        }));
        var legatura = document.createElement('a');
        legatura.href = url;
        legatura.download = nume;
        legatura.click();
        setTimeout(function () {
          return window.URL.revokeObjectURL(url);
        }, 60000);

        _this18.notifica("Fi\u0219ierul ".concat(nume, " a fost scris"), 'success');
      })["catch"](function (err) {
        _this18.decontEroare = _this18.mesajEroareDinBlob(err, mesajDeEroare);
      })["finally"](function () {
        _this18.decontInCurs = false;
      });
    },

    /**
     * Mesajul de eroare al unui răspuns cerut ca fișier.
     *
     * Cu „responseType: blob", și răspunsul de eroare vine tot blob, iar
     * mesajul serverului ar rămâne necitit dacă nu e desfăcut aici.
     */
    mesajEroareDinBlob: function mesajEroareDinBlob(err, implicit) {
      var _this19 = this;

      var date = err.response && err.response.data;

      if (date instanceof Blob) {
        date.text().then(function (text) {
          try {
            var raspuns = JSON.parse(text);

            if (raspuns.message) {
              _this19.decontEroare = raspuns.message;
            }
          } catch (e) {// Nu era json: rămâne mesajul obișnuit.
          }
        });
      }

      return this.mesajEroare(err, implicit);
    },

    /** Suma, scrisă cum se scriu banii la noi. */
    leiDeAfisat: function leiDeAfisat(valoare) {
      return new Intl.NumberFormat('ro-RO', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
      }).format(valoare);
    },

    /** Trece încă o dată prin declarație cu unealta ANAF. */
    verificaConsistenta: function verificaConsistenta(declaratie) {
      var _this20 = this;

      if (!declaratie) {
        return;
      }

      this.eroare = '';
      this.consistentaEroare = '';
      this.ocupat = declaratie.id;
      this.consistentaInCurs = true;
      this.$http.post("/declaratii/".concat(declaratie.id, "/verifica-consistenta")).then(function (raspuns) {
        _this20.consistentaDeclaratie = raspuns.data.data;
        _this20.consistentaDate = raspuns.data.consistenta;
        _this20.consistentaVizibila = true;

        _this20.incarcaLista();
      })["catch"](function (err) {
        _this20.consistentaEroare = _this20.mesajEroare(err, 'Verificarea de consistență a eșuat');
      })["finally"](function () {
        _this20.ocupat = null;
        _this20.consistentaInCurs = false;
      });
    },

    /**
     * @param {boolean} tacut pornit de cronometru: anunță doar când chiar a
     *                        descărcat ceva, ca să nu bombardeze utilizatorul
     *                        cu mesaje „0 recipise" la fiecare interval.
     */
    verificaRecipise: function verificaRecipise() {
      var _this21 = this;

      var tacut = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : false;
      this.eroare = '';
      this.recipiseInCurs = true;
      this.mersul = '';
      this.cercetate = 0;
      this.deCercetat = 0;
      this.cereRecipisele().then(function (rezultat) {
        _this21.ultimaDescarcare = _this21.acum();
        _this21.ultimaDescarcareNumar = rezultat.descarcate || 0;
        window.localStorage.setItem('declaratii_recipise_ultima', JSON.stringify({
          la: _this21.ultimaDescarcare,
          descarcate: _this21.ultimaDescarcareNumar
        }));

        if (!tacut || rezultat.descarcate > 0) {
          _this21.notifica("Verificate: ".concat(rezultat.verificate, ", recipise desc\u0103rcate: ").concat(rezultat.descarcate), rezultat.descarcate > 0 ? 'success' : 'info');
        }

        if (rezultat.erori && rezultat.erori.length) {
          _this21.eroare = rezultat.erori.join(' | ');
        }

        _this21.incarcaLista(); // Recipisele venite acum se pot aduna într-un singur fișier de tipărit.


        var aduse = rezultat.descarcate_id || [];

        if (_this21.tiparireRecipise && aduse.length) {
          return _this21.descarcaPentruTiparire(aduse, 'recipisa');
        }

        return null;
      })["catch"](function (err) {
        _this21.eroare = _this21.mesajEroare(err, 'Descărcarea recipiselor a eșuat');
      })["finally"](function () {
        _this21.recipiseInCurs = false;
        _this21.mersul = '';
        _this21.cercetate = 0;
        _this21.deCercetat = 0;
      });
    },

    /**
     * Cere recipisele, spunând la fiecare a câta declarație e din câte.
     *
     * Răspunsul curge — câte un rând pe măsură ce se lucrează —, așa că omul
     * vede mersul, nu doar o rotiță: pentru fiecare declarație se întreabă ANAF
     * de starea ei, iar recipisa găsită se aduce prin tokenul clientului. O
     * sesiune cu zeci de declarații ține minute, iar din afară lucrul și
     * împotmolirea arată la fel.
     *
     * @returns {Promise<object>} rezultatul, în forma răspunsului obișnuit
     */
    cereRecipisele: function cereRecipisele() {
      var _this22 = this;

      // Browserele fără fetch cu flux rămân pe calea dinainte, fără numărătoare.
      if (!Object(_libs_flux__WEBPACK_IMPORTED_MODULE_24__["areFlux"])()) {
        return this.$http.post('/declaratii/recipise').then(function (raspuns) {
          return raspuns.data.data;
        });
      }

      var rezultat = {
        verificate: 0,
        descarcate: 0,
        descarcate_id: [],
        erori: []
      };
      return Object(_libs_flux__WEBPACK_IMPORTED_MODULE_24__["default"])('declaratii/recipise/flux', function (pas) {
        if (pas.tip === 'inceput') {
          _this22.deCercetat = pas.total;
          _this22.cercetate = 0;
          if (pas.total) _this22.mersul = "0 din ".concat(pas.total, " declara\u021Bii cercetate...");
          return;
        }

        if (pas.tip === 'pas') {
          _this22.cercetate = pas.facute;
          var care = pas.ce ? " \u2014 ".concat(pas.ce) : '';
          var adusa = pas.adus ? ' (recipisă adusă)' : '';
          _this22.mersul = "".concat(pas.facute, " din ").concat(pas.total, " declara\u021Bii cercetate").concat(care).concat(adusa);
          return;
        }

        if (pas.tip === 'gata') rezultat = pas;
      }).then(function () {
        return rezultat;
      });
    },
    sterge: function sterge(declaratie) {
      var _this23 = this;

      this.$bvModal.msgBoxConfirm("\u0218terge\u021Bi declara\u021Bia ".concat(declaratie.tip, " pentru CUI ").concat(declaratie.cui, "?"), {
        okTitle: 'Șterge',
        cancelTitle: 'Renunță',
        okVariant: 'danger'
      }).then(function (confirmat) {
        if (!confirmat) return;

        _this23.$http["delete"]("/declaratii/".concat(declaratie.id)).then(function () {
          _this23.notifica('Declarația a fost ștearsă', 'success');

          _this23.incarcaLista();
        })["catch"](function (err) {
          _this23.eroare = _this23.mesajEroare(err, 'Ștergerea a eșuat');
        });
      });
    },
    mesajEroare: function mesajEroare(err, implicit) {
      return err.response && err.response.data && err.response.data.message ? err.response.data.message : implicit;
    },

    /**
     * Erorile validatorului vin pe mai multe rânduri; în tabel se arată ca un
     * text continuu, ca să nu rămână doar antetul de secțiune vizibil.
     */
    peUnRand: function peUnRand(text) {
      return (text || '').replace(/\s*\r?\n\s*/g, ' · ').trim();
    },

    /**
     * Săgeata apare doar când textul chiar nu încape pe un rând. Pragul e
     * estimat după lățimea coloanei, nu măsurat: o săgeată în plus la o eroare
     * scurtă ar fi doar zgomot.
     */
    eroareaEsteLunga: function eroareaEsteLunga(declaratie) {
      return this.peUnRand(declaratie.erori_validare).length > 45;
    },
    desfasurata: function desfasurata(declaratie) {
      return this.eroriDesfasurate.indexOf(declaratie.id) !== -1;
    },
    comutaEroarea: function comutaEroarea(declaratie) {
      var pozitie = this.eroriDesfasurate.indexOf(declaratie.id);

      if (pozitie === -1) {
        this.eroriDesfasurate.push(declaratie.id);
      } else {
        this.eroriDesfasurate.splice(pozitie, 1);
      }
    },

    /** A ajuns la ANAF: fie pasul o spune, fie există deja un index de încărcare. */
    esteDepusa: function esteDepusa(declaratie) {
      return ['depus', 'finalizat'].indexOf(declaratie.pas) !== -1 || !!declaratie.index_recipisa;
    },
    culoareSeveritate: function culoareSeveritate(severitate) {
      if (severitate === 'blocant') return 'danger';
      return severitate === 'atentionare' ? 'info' : 'warning';
    },
    etichetaSeveritate: function etichetaSeveritate(severitate) {
      if (severitate === 'blocant') return 'Blochează validarea';
      return severitate === 'atentionare' ? 'Atenționare' : 'Eroare';
    },

    /**
     * Cere serverului traducerea erorilor pentru declarația aleasă.
     *
     * Răspunsul curge pe bucăți (câte un obiect JSON pe rând), așa că fiecare
     * eroare se arată de îndată ce sosește, fără să se aștepte terminarea
     * tuturor. Se folosește fetch, nu axios: numai el dă acces la conținut pe
     * măsură ce vine.
     */
    explicaEroarea: function explicaEroarea(declaratie) {
      var _this24 = this;

      this.explicatie = {
        rezumat: '',
        probleme: []
      };
      this.explicatieTotal = 0;
      this.explicatieInCurs = true;
      this.explicatieVizibila = true;
      this.explicatieEroare = '';
      Object(_libs_flux__WEBPACK_IMPORTED_MODULE_24__["default"])("declaratii/".concat(declaratie.id, "/erori"), function (pas) {
        return _this24.adaugaPas(pas);
      })["catch"](function (err) {
        // Eșecul se arată în fereastră, nu în spatele ei: altfel utilizatorul
        // vede doar o fereastră goală și nu știe ce s-a întâmplat.
        _this24.explicatieEroare = "Explica\u021Bia nu a putut fi ob\u021Binut\u0103: ".concat(err.message, ".");
      })["finally"](function () {
        _this24.explicatieInCurs = false;
      });
    },
    adaugaPas: function adaugaPas(pas) {
      if (pas.tip === 'inceput') {
        this.explicatieTotal = pas.total;
      } else if (pas.tip === 'problema') {
        this.explicatie.probleme.push(pas.data);
      } else if (pas.tip === 'gata') {
        this.explicatie.rezumat = pas.rezumat;
        this.explicatie.netradus = pas.netradus;
        this.explicatieInCurs = false;
      }
    },
    notifica: function notifica(mesaj, variant) {
      this.$bvToast.toast(mesaj, {
        title: 'Declarații ANAF',
        variant: variant,
        solid: true
      });
    }
  }
});

/***/ }),

/***/ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/app_pages/spv/Jurnal.vue?vue&type=script&lang=js&":
/*!******************************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib??ref--4-0!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/src/views/app_pages/spv/Jurnal.vue?vue&type=script&lang=js& ***!
  \******************************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var core_js_modules_es_array_concat_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! core-js/modules/es.array.concat.js */ "./node_modules/core-js/modules/es.array.concat.js");
/* harmony import */ var core_js_modules_es_array_concat_js__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_es_array_concat_js__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var core_js_modules_es_array_map_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! core-js/modules/es.array.map.js */ "./node_modules/core-js/modules/es.array.map.js");
/* harmony import */ var core_js_modules_es_array_map_js__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_es_array_map_js__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var core_js_modules_es_object_keys_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! core-js/modules/es.object.keys.js */ "./node_modules/core-js/modules/es.object.keys.js");
/* harmony import */ var core_js_modules_es_object_keys_js__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_es_object_keys_js__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var core_js_modules_es_object_to_string_js__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! core-js/modules/es.object.to-string.js */ "./node_modules/core-js/modules/es.object.to-string.js");
/* harmony import */ var core_js_modules_es_object_to_string_js__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_es_object_to_string_js__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var core_js_modules_web_dom_collections_for_each_js__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! core-js/modules/web.dom-collections.for-each.js */ "./node_modules/core-js/modules/web.dom-collections.for-each.js");
/* harmony import */ var core_js_modules_web_dom_collections_for_each_js__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_web_dom_collections_for_each_js__WEBPACK_IMPORTED_MODULE_4__);
/* harmony import */ var core_js_modules_es_string_iterator_js__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! core-js/modules/es.string.iterator.js */ "./node_modules/core-js/modules/es.string.iterator.js");
/* harmony import */ var core_js_modules_es_string_iterator_js__WEBPACK_IMPORTED_MODULE_5___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_es_string_iterator_js__WEBPACK_IMPORTED_MODULE_5__);
/* harmony import */ var core_js_modules_web_dom_collections_iterator_js__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! core-js/modules/web.dom-collections.iterator.js */ "./node_modules/core-js/modules/web.dom-collections.iterator.js");
/* harmony import */ var core_js_modules_web_dom_collections_iterator_js__WEBPACK_IMPORTED_MODULE_6___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_web_dom_collections_iterator_js__WEBPACK_IMPORTED_MODULE_6__);
/* harmony import */ var core_js_modules_web_url_js__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! core-js/modules/web.url.js */ "./node_modules/core-js/modules/web.url.js");
/* harmony import */ var core_js_modules_web_url_js__WEBPACK_IMPORTED_MODULE_7___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_web_url_js__WEBPACK_IMPORTED_MODULE_7__);
/* harmony import */ var core_js_modules_web_url_search_params_js__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! core-js/modules/web.url-search-params.js */ "./node_modules/core-js/modules/web.url-search-params.js");
/* harmony import */ var core_js_modules_web_url_search_params_js__WEBPACK_IMPORTED_MODULE_8___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_web_url_search_params_js__WEBPACK_IMPORTED_MODULE_8__);
/* harmony import */ var core_js_modules_es_array_slice_js__WEBPACK_IMPORTED_MODULE_9__ = __webpack_require__(/*! core-js/modules/es.array.slice.js */ "./node_modules/core-js/modules/es.array.slice.js");
/* harmony import */ var core_js_modules_es_array_slice_js__WEBPACK_IMPORTED_MODULE_9___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_es_array_slice_js__WEBPACK_IMPORTED_MODULE_9__);










//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
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
  name: 'SpvJurnal',
  data: function data() {
    return {
      intrari: [],
      utilizatori: [],
      actiuni: {},
      eroare: '',
      pagina: 1,
      pePagina: 25,
      exportInCurs: false,
      listaInCurs: false,
      filtre: {
        user_id: null,
        actiune: null,
        de_la: '',
        pana_la: '',
        cautare: '',
        doar_esecuri: false
      },
      campuri: [{
        key: 'cand',
        label: 'Când'
      }, {
        key: 'utilizator',
        label: 'Utilizator'
      }, {
        key: 'email',
        label: 'Email'
      }, {
        key: 'actiune_eticheta',
        label: 'Acțiune'
      }, {
        key: 'descriere',
        label: 'Detalii'
      }, {
        key: 'cif',
        label: 'CIF'
      }, {
        key: 'ip',
        label: 'IP'
      }]
    };
  },
  computed: {
    optiuniUtilizatori: function optiuniUtilizatori() {
      return [{
        value: null,
        text: 'Toți'
      }].concat(this.utilizatori.map(function (u) {
        return {
          value: u.user_id,
          text: u.user_nume || "#".concat(u.user_id)
        };
      }));
    },
    optiuniActiuni: function optiuniActiuni() {
      var _this = this;

      return [{
        value: null,
        text: 'Toate'
      }].concat(Object.keys(this.actiuni).map(function (cheie) {
        return {
          value: cheie,
          text: _this.actiuni[cheie]
        };
      }));
    }
  },
  created: function created() {
    this.incarcaLista();
  },
  methods: {
    resetFiltre: function resetFiltre() {
      this.filtre = {
        user_id: null,
        actiune: null,
        de_la: '',
        pana_la: '',
        cautare: '',
        doar_esecuri: false
      };
      this.incarcaLista();
    },

    /** Filtrele scrise, fără cele lăsate goale. */
    parametri: function parametri() {
      var _this2 = this;

      var params = {};
      Object.keys(this.filtre).forEach(function (cheie) {
        var valoare = _this2.filtre[cheie];

        if (valoare !== null && valoare !== '' && valoare !== false) {
          params[cheie] = valoare;
        }
      });
      return params;
    },

    /**
     * Descarcă în Excel exact ce se vede: aceleași filtre, aceeași ordine.
     *
     * Fișierul vine ca blob, nu printr-un link direct: ruta cere tokenul, iar
     * un `window.open` nu-l poartă cu el.
     */
    exporta: function exporta() {
      var _this3 = this;

      this.eroare = '';
      this.exportInCurs = true;
      this.$http.get('/anaf-jurnal/export', {
        params: this.parametri(),
        responseType: 'blob'
      }).then(function (raspuns) {
        var url = window.URL.createObjectURL(new Blob([raspuns.data]));
        var legatura = document.createElement('a');
        legatura.href = url;
        legatura.download = "jurnal_activitate_".concat(new Date().toISOString().slice(0, 10), ".xlsx");
        document.body.appendChild(legatura);
        legatura.click();
        document.body.removeChild(legatura);
        setTimeout(function () {
          return window.URL.revokeObjectURL(url);
        }, 60000);
      })["catch"](function () {
        _this3.eroare = 'Exportul nu a putut fi creat.';
      })["finally"](function () {
        _this3.exportInCurs = false;
      });
    },
    incarcaLista: function incarcaLista() {
      var _this4 = this;

      this.listaInCurs = true;
      this.$http.get('/anaf-jurnal', {
        params: this.parametri()
      }).then(function (raspuns) {
        _this4.intrari = raspuns.data.data || [];
        _this4.utilizatori = raspuns.data.utilizatori || [];
        _this4.actiuni = raspuns.data.actiuni || {};
      })["catch"](function (err) {
        _this4.eroare = err.response && err.response.data && err.response.data.message ? err.response.data.message : 'Nu s-a putut încărca jurnalul';
      })["finally"](function () {
        _this4.listaInCurs = false;
      });
    }
  }
});

/***/ }),

/***/ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/app_pages/spv/Mesaje.vue?vue&type=script&lang=js&":
/*!******************************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib??ref--4-0!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/src/views/app_pages/spv/Mesaje.vue?vue&type=script&lang=js& ***!
  \******************************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var C_dianasoft_serviciiweb_web_node_modules_babel_runtime_helpers_esm_toConsumableArray_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./node_modules/@babel/runtime/helpers/esm/toConsumableArray.js */ "./node_modules/@babel/runtime/helpers/esm/toConsumableArray.js");
/* harmony import */ var C_dianasoft_serviciiweb_web_node_modules_babel_runtime_helpers_esm_slicedToArray_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./node_modules/@babel/runtime/helpers/esm/slicedToArray.js */ "./node_modules/@babel/runtime/helpers/esm/slicedToArray.js");
/* harmony import */ var C_dianasoft_serviciiweb_web_node_modules_babel_runtime_helpers_esm_objectSpread2_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./node_modules/@babel/runtime/helpers/esm/objectSpread2.js */ "./node_modules/@babel/runtime/helpers/esm/objectSpread2.js");
/* harmony import */ var core_js_modules_es_array_filter_js__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! core-js/modules/es.array.filter.js */ "./node_modules/core-js/modules/es.array.filter.js");
/* harmony import */ var core_js_modules_es_array_filter_js__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_es_array_filter_js__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var core_js_modules_es_object_to_string_js__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! core-js/modules/es.object.to-string.js */ "./node_modules/core-js/modules/es.object.to-string.js");
/* harmony import */ var core_js_modules_es_object_to_string_js__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_es_object_to_string_js__WEBPACK_IMPORTED_MODULE_4__);
/* harmony import */ var core_js_modules_es_string_trim_js__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! core-js/modules/es.string.trim.js */ "./node_modules/core-js/modules/es.string.trim.js");
/* harmony import */ var core_js_modules_es_string_trim_js__WEBPACK_IMPORTED_MODULE_5___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_es_string_trim_js__WEBPACK_IMPORTED_MODULE_5__);
/* harmony import */ var core_js_modules_es_array_slice_js__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! core-js/modules/es.array.slice.js */ "./node_modules/core-js/modules/es.array.slice.js");
/* harmony import */ var core_js_modules_es_array_slice_js__WEBPACK_IMPORTED_MODULE_6___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_es_array_slice_js__WEBPACK_IMPORTED_MODULE_6__);
/* harmony import */ var core_js_modules_es_array_map_js__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! core-js/modules/es.array.map.js */ "./node_modules/core-js/modules/es.array.map.js");
/* harmony import */ var core_js_modules_es_array_map_js__WEBPACK_IMPORTED_MODULE_7___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_es_array_map_js__WEBPACK_IMPORTED_MODULE_7__);
/* harmony import */ var core_js_modules_es_number_constructor_js__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! core-js/modules/es.number.constructor.js */ "./node_modules/core-js/modules/es.number.constructor.js");
/* harmony import */ var core_js_modules_es_number_constructor_js__WEBPACK_IMPORTED_MODULE_8___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_es_number_constructor_js__WEBPACK_IMPORTED_MODULE_8__);
/* harmony import */ var core_js_modules_es_array_concat_js__WEBPACK_IMPORTED_MODULE_9__ = __webpack_require__(/*! core-js/modules/es.array.concat.js */ "./node_modules/core-js/modules/es.array.concat.js");
/* harmony import */ var core_js_modules_es_array_concat_js__WEBPACK_IMPORTED_MODULE_9___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_es_array_concat_js__WEBPACK_IMPORTED_MODULE_9__);
/* harmony import */ var core_js_modules_es_array_find_js__WEBPACK_IMPORTED_MODULE_10__ = __webpack_require__(/*! core-js/modules/es.array.find.js */ "./node_modules/core-js/modules/es.array.find.js");
/* harmony import */ var core_js_modules_es_array_find_js__WEBPACK_IMPORTED_MODULE_10___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_es_array_find_js__WEBPACK_IMPORTED_MODULE_10__);
/* harmony import */ var core_js_modules_es_array_join_js__WEBPACK_IMPORTED_MODULE_11__ = __webpack_require__(/*! core-js/modules/es.array.join.js */ "./node_modules/core-js/modules/es.array.join.js");
/* harmony import */ var core_js_modules_es_array_join_js__WEBPACK_IMPORTED_MODULE_11___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_es_array_join_js__WEBPACK_IMPORTED_MODULE_11__);
/* harmony import */ var core_js_modules_es_string_iterator_js__WEBPACK_IMPORTED_MODULE_12__ = __webpack_require__(/*! core-js/modules/es.string.iterator.js */ "./node_modules/core-js/modules/es.string.iterator.js");
/* harmony import */ var core_js_modules_es_string_iterator_js__WEBPACK_IMPORTED_MODULE_12___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_es_string_iterator_js__WEBPACK_IMPORTED_MODULE_12__);
/* harmony import */ var core_js_modules_web_dom_collections_iterator_js__WEBPACK_IMPORTED_MODULE_13__ = __webpack_require__(/*! core-js/modules/web.dom-collections.iterator.js */ "./node_modules/core-js/modules/web.dom-collections.iterator.js");
/* harmony import */ var core_js_modules_web_dom_collections_iterator_js__WEBPACK_IMPORTED_MODULE_13___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_web_dom_collections_iterator_js__WEBPACK_IMPORTED_MODULE_13__);
/* harmony import */ var core_js_modules_web_url_js__WEBPACK_IMPORTED_MODULE_14__ = __webpack_require__(/*! core-js/modules/web.url.js */ "./node_modules/core-js/modules/web.url.js");
/* harmony import */ var core_js_modules_web_url_js__WEBPACK_IMPORTED_MODULE_14___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_web_url_js__WEBPACK_IMPORTED_MODULE_14__);
/* harmony import */ var core_js_modules_web_url_search_params_js__WEBPACK_IMPORTED_MODULE_15__ = __webpack_require__(/*! core-js/modules/web.url-search-params.js */ "./node_modules/core-js/modules/web.url-search-params.js");
/* harmony import */ var core_js_modules_web_url_search_params_js__WEBPACK_IMPORTED_MODULE_15___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_web_url_search_params_js__WEBPACK_IMPORTED_MODULE_15__);
/* harmony import */ var core_js_modules_es_json_stringify_js__WEBPACK_IMPORTED_MODULE_16__ = __webpack_require__(/*! core-js/modules/es.json.stringify.js */ "./node_modules/core-js/modules/es.json.stringify.js");
/* harmony import */ var core_js_modules_es_json_stringify_js__WEBPACK_IMPORTED_MODULE_16___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_es_json_stringify_js__WEBPACK_IMPORTED_MODULE_16__);
/* harmony import */ var core_js_modules_es_string_pad_start_js__WEBPACK_IMPORTED_MODULE_17__ = __webpack_require__(/*! core-js/modules/es.string.pad-start.js */ "./node_modules/core-js/modules/es.string.pad-start.js");
/* harmony import */ var core_js_modules_es_string_pad_start_js__WEBPACK_IMPORTED_MODULE_17___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_es_string_pad_start_js__WEBPACK_IMPORTED_MODULE_17__);
/* harmony import */ var vue_select__WEBPACK_IMPORTED_MODULE_18__ = __webpack_require__(/*! vue-select */ "./node_modules/vue-select/dist/vue-select.js");
/* harmony import */ var vue_select__WEBPACK_IMPORTED_MODULE_18___default = /*#__PURE__*/__webpack_require__.n(vue_select__WEBPACK_IMPORTED_MODULE_18__);
/* harmony import */ var _libs_flux__WEBPACK_IMPORTED_MODULE_19__ = __webpack_require__(/*! @/libs/flux */ "./resources/js/src/libs/flux.js");


















//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
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
  name: 'SpvMesaje',
  components: {
    vSelect: vue_select__WEBPACK_IMPORTED_MODULE_18___default.a
  },
  data: function data() {
    return {
      // Firmele pentru care se cer mesaje, din Entități înrolate
      firme: [],
      // Tokenul de la care se descarcă; null înseamnă toate
      certificatAles: null,
      zile: 30,
      loading: false,
      error: '',
      messages: [],
      pagina: 1,
      pePagina: 25,
      infoDescarcare: '',
      // Mersul lucrului, cat timp se descarca: „7 din 42 — DECIZIE 15208744"
      mersul: '',
      aduse: 0,
      deAdus: 0,
      // Filtre pe coloanele tabelului; lucrează pe ce e deja adus
      filtre: {
        tip: '',
        cif: '',
        den_firma: '',
        data_creare: '',
        id_solicitare: '',
        detalii: '',
        certificat: '',
        descarcat: ''
      },
      campuriFiltrabile: [{
        cheie: 'tip',
        eticheta: 'Tip'
      }, {
        cheie: 'cif',
        eticheta: 'CIF'
      }, {
        cheie: 'den_firma',
        eticheta: 'Denumire'
      }, {
        cheie: 'data_creare',
        eticheta: 'Data'
      }, {
        cheie: 'id_solicitare',
        eticheta: 'ID solicitare'
      }, {
        cheie: 'detalii',
        eticheta: 'Detalii'
      }, {
        cheie: 'certificat',
        eticheta: 'Certificat'
      }],
      // Descărcarea repetată, ca la recipise
      automat: {
        activ: false,
        minute: 10
      },
      optiuniMinute: [{
        value: 5,
        text: '5 minute'
      }, {
        value: 10,
        text: '10 minute'
      }, {
        value: 15,
        text: '15 minute'
      }, {
        value: 30,
        text: '30 minute'
      }, {
        value: 60,
        text: '60 minute'
      }],
      cronometru: null,
      ultimaDescarcare: '',
      ultimaDescarcareNumar: 0,
      alerteVizibile: false,
      alerte: [],
      certificate: [],
      societati: [],
      tipuri: [],
      alerta: {},
      eroareAlerte: '',
      campuriAlerte: [{
        key: 'email',
        label: 'Email'
      }, {
        key: 'tip_document',
        label: 'Document'
      }, {
        key: 'unde',
        label: 'Pentru'
      }, {
        key: 'trimise',
        label: 'Trimise'
      }, {
        key: 'actiuni',
        label: ''
      }]
    };
  },
  computed: {
    /** Mesajele care trec de filtrele scrise pe coloane. */
    mesajeFiltrate: function mesajeFiltrate() {
      var _this = this;

      var contine = function contine(valoare, cautat) {
        return String(valoare || '').toLowerCase().indexOf(cautat.toLowerCase()) !== -1;
      };

      return this.messages.filter(function (mesaj) {
        if (_this.filtre.descarcat === 'da' && !mesaj.descarcat) return false;
        if (_this.filtre.descarcat === 'nu' && mesaj.descarcat) return false;
        return _this.campuriFiltrabile.every(function (camp) {
          var cautat = (_this.filtre[camp.cheie] || '').trim();
          return cautat === '' || contine(mesaj[camp.cheie], cautat);
        });
      });
    },

    /** Doar randurile paginii curente: tabelul e scris de mana, nu e b-table. */
    mesajePagina: function mesajePagina() {
      var inceput = (this.pagina - 1) * this.pePagina;
      return this.mesajeFiltrate.slice(inceput, inceput + this.pePagina);
    },

    /**
     * Câte documente are de adus chiar acest buton.
     *
     * Recipisele și răspunsurile la solicitări nu se numără: ele se aduc din
     * filele lor, iar aici numărul ar rămâne aprins fără ca butonul să-l poată
     * stinge vreodată.
     */
    fisiereLipsa: function fisiereLipsa() {
      return this.messages.filter(function (mesaj) {
        return !mesaj.descarcat && !mesaj.fila_care_aduce;
      }).length;
    },
    mesajeAduse: function mesajeAduse() {
      if (!this.ultimaDescarcareNumar) return 'fără mesaje noi';
      return this.ultimaDescarcareNumar === 1 ? 'un mesaj nou' : "".concat(this.ultimaDescarcareNumar, " mesaje noi");
    },

    /** Firmele alese, ca listă de CIF-uri pentru interogarea ANAF. */
    listaCif: function listaCif() {
      return this.firme.map(function (firma) {
        return firma.cif;
      });
    },
    optiuniFirmeInrolate: function optiuniFirmeInrolate() {
      var _this2 = this;

      // Cu un certificat ales, lista arată doar firmele înrolate pe el.
      var potrivite = this.certificatAles ? this.societati.filter(function (societate) {
        return Number(societate.certificat_id) === Number(_this2.certificatAles);
      }) : this.societati;
      return potrivite.map(function (societate) {
        return {
          cif: societate.cif,
          eticheta: societate.denumire ? "".concat(societate.denumire, " (").concat(societate.cif, ")") : societate.cif
        };
      });
    },
    optiuniCertificatToken: function optiuniCertificatToken() {
      return [{
        value: null,
        text: 'toate certificatele'
      }].concat(this.certificate.filter(function (certificat) {
        return certificat.activ !== false;
      }).map(function (certificat) {
        return {
          value: certificat.id,
          text: certificat.cn
        };
      }));
    },

    /** Antetul care spune serverului cu ce token să lucreze această cerere. */
    antetCertificat: function antetCertificat() {
      // Gol, cererea nu poartă certificat: serverul întreabă toate tokenurile.
      return {
        'X-Certificat-Id': this.certificatAles ? String(this.certificatAles) : ''
      };
    },

    /**
     * Felurile de document din listă. Cele deja întâlnite se scot în față cu un
     * semn, ca să se vadă ce chiar a venit față de ce e doar posibil.
     */
    optiuniTipuri: function optiuniTipuri() {
      // Doar valoarea, fără vreun semn în plus: din datalist se scrie în câmp
      // exact ce e aici, iar un caracter străin ar strica potrivirea.
      return this.tipuri.map(function (tip) {
        return tip.valoare;
      });
    },

    /** Câte dintre felurile din listă au apărut deja în mesaje. */
    tipuriVazute: function tipuriVazute() {
      return this.tipuri.filter(function (tip) {
        return tip.vazut;
      }).length;
    },

    /**
     * Ce așteaptă alerta: o hârtie sosită, sau o constatare din ea.
     *
     * Constatările se trimit numai la firmele la care s-a găsit chiar acel
     * lucru. O alertă pe „vector fiscal” ca fel de document ar trimite, la 250
     * de firme, 250 de emailuri — deși numai la câteva s-a schimbat ceva.
     */
    optiuniConstatari: function optiuniConstatari() {
      return [{
        value: null,
        text: 'la sosirea unui document de tipul ales'
      }, {
        value: 'vector_modificat',
        text: 'doar când vectorul fiscal s-a modificat'
      }, {
        value: 'restante',
        text: 'doar când situația sintetică arată restanțe'
      }];
    },
    optiuniCertificate: function optiuniCertificate() {
      return [{
        value: null,
        text: 'orice certificat'
      }].concat(this.certificate.map(function (certificat) {
        return {
          value: certificat.id,
          text: certificat.activ === false ? "".concat(certificat.cn, " (scos din uz)") : certificat.cn
        };
      }));
    },

    /** Firmele se restrâng la certificatul ales, dacă e vreunul. */
    optiuniFirme: function optiuniFirme() {
      var _this3 = this;

      var potrivite = this.alerta.certificat_id ? this.societati.filter(function (societate) {
        return societate.certificat_id === _this3.alerta.certificat_id;
      }) : this.societati;
      return [{
        value: null,
        text: 'toate firmele înrolate'
      }].concat(potrivite.map(function (societate) {
        return {
          value: societate.cif,
          text: "".concat(societate.denumire || 'fără denumire', " (").concat(societate.cif, ")")
        };
      }));
    }
  },
  watch: {
    // Orice schimbare a setarii o tine minte si reporneste cronometrul.
    'automat.activ': function reporneste() {
      this.salveazaSetarea();
      this.reglaCronometrul();
    },
    'automat.minute': function reporneste() {
      this.salveazaSetarea();
      this.reglaCronometrul();
    },
    // Dupa filtrare, pagina veche poate sa nu mai existe.
    mesajeFiltrate: function mesajeFiltrate() {
      var ultima = Math.max(1, Math.ceil(this.mesajeFiltrate.length / this.pePagina));
      if (this.pagina > ultima) this.pagina = ultima;
    },
    // La schimbarea certificatului, firmele alese de pe alt token ies din alegere.
    certificatAles: function certificatAles() {
      var permise = this.optiuniFirmeInrolate.map(function (optiune) {
        return optiune.cif;
      });
      this.firme = this.firme.filter(function (firma) {
        return permise.indexOf(firma.cif) !== -1;
      });
    }
  },
  created: function created() {
    this.incarcaSetarea();
    this.incarcaStocate();
    this.incarcaSocietatiInrolate();
    this.incarcaCertificateToken();
    this.reglaCronometrul(); // Certificatul activ ales în fila Certificate rămâne alegerea de pornire.

    var activ = Number(window.localStorage.getItem('anaf_certificat_activ'));
    if (activ) this.certificatAles = activ;
  },
  beforeDestroy: function beforeDestroy() {
    this.opresteCronometrul();
  },
  methods: {
    deschideAlerte: function deschideAlerte() {
      var _this4 = this;

      this.eroareAlerte = '';
      this.alerteVizibile = true;
      this.alertaNoua();
      this.$http.get('/spv-alerte').then(function (_ref) {
        var data = _ref.data;
        _this4.alerte = data.data;
        _this4.certificate = data.certificate;
        _this4.societati = data.societati;
        _this4.tipuri = data.tipuri;
      })["catch"](function (err) {
        _this4.eroareAlerte = _this4.mesajEroareAlerte(err, 'Alertele nu au putut fi încărcate');
      });
    },
    alertaNoua: function alertaNoua() {
      this.alerta = {
        email: '',
        tip_document: '',
        doar_cand: null,
        certificat_id: null,
        cif: null,
        activ: true
      };
    },
    editeazaAlerta: function editeazaAlerta(alerta) {
      this.alerta = Object(C_dianasoft_serviciiweb_web_node_modules_babel_runtime_helpers_esm_objectSpread2_js__WEBPACK_IMPORTED_MODULE_2__["default"])({}, alerta);
    },
    salveazaAlerta: function salveazaAlerta() {
      var _this5 = this;

      this.eroareAlerte = '';
      var cerere = this.alerta.id ? this.$http.put("/spv-alerte/".concat(this.alerta.id), this.alerta) : this.$http.post('/spv-alerte', this.alerta);
      cerere.then(function () {
        _this5.alertaNoua();

        _this5.reincarcaAlerte();
      })["catch"](function (err) {
        _this5.eroareAlerte = _this5.mesajEroareAlerte(err, 'Alerta nu a putut fi salvată');
      });
    },
    stergeAlerta: function stergeAlerta(alerta) {
      var _this6 = this;

      this.$bvModal.msgBoxConfirm("\u0218terge\u021Bi alerta c\u0103tre ".concat(alerta.email, "?"), {
        title: 'Ștergere alertă',
        okTitle: 'Șterge',
        cancelTitle: 'Renunță',
        okVariant: 'danger'
      }).then(function (confirmat) {
        if (!confirmat) return;

        _this6.$http["delete"]("/spv-alerte/".concat(alerta.id)).then(function () {
          _this6.reincarcaAlerte();
        })["catch"](function (err) {
          _this6.eroareAlerte = _this6.mesajEroareAlerte(err, 'Alerta nu a putut fi ștearsă');
        });
      });
    },
    reincarcaAlerte: function reincarcaAlerte() {
      var _this7 = this;

      this.$http.get('/spv-alerte').then(function (_ref2) {
        var data = _ref2.data;
        _this7.alerte = data.data;
      });
    },
    numeFirma: function numeFirma(cif) {
      if (!cif) return 'toate firmele înrolate';
      var societate = this.societati.find(function (alta) {
        return alta.cif === cif;
      });
      return societate ? "".concat(societate.denumire || '', " (").concat(cif, ")") : cif;
    },

    /** Firmele înrolate, pentru lista de la CIF și pentru fereastra de alerte. */
    incarcaSocietatiInrolate: function incarcaSocietatiInrolate() {
      var _this8 = this;

      return this.$http.get('/anaf-societati', {
        params: {
          doar_active: 1
        }
      }).then(function (_ref3) {
        var data = _ref3.data;
        _this8.societati = data.data.map(function (societate) {
          return {
            cif: societate.cif,
            denumire: societate.denumire,
            certificat_id: societate.certificat_id
          };
        });
      })["catch"](function () {
        _this8.societati = [];
      });
    },
    mesajEroareAlerte: function mesajEroareAlerte(err, implicit) {
      return err.response && err.response.data && err.response.data.message ? err.response.data.message : implicit;
    },

    /** Certificatele clientului, pentru lista de tokenuri de descărcare. */
    incarcaCertificateToken: function incarcaCertificateToken() {
      var _this9 = this;

      this.$http.get('/anaf-certificate').then(function (_ref4) {
        var data = _ref4.data;
        _this9.certificate = data.data || [];
      })["catch"](function () {// fara lista, ramane doar alegerea „toate certificatele"
      });
    },
    // Fisierele se descarca automat la citirea listei; cand asta nu a reusit,
    // in locul butonului de deschidere se afiseaza motivul.
    statusFisier: function statusFisier(item) {
      // Recipisele și răspunsurile se aduc din filele lor, nu de aici: altfel
      // ar rămâne veșnic „nedescărcate” și ar părea o defecțiune.
      if (item.fila_care_aduce) return "Se aduce din ".concat(item.fila_care_aduce);
      if (item.ultima_eroare) return "Desc\u0103rcare e\u0219uat\u0103: ".concat(item.ultima_eroare);
      return 'Nedescărcat — se preia la următoarea descărcare';
    },

    /** Ce a adus apăsarea, spus în cuvinte, chiar și când n-a adus nimic. */
    rezumatCitire: function rezumatCitire(payload, descarcare) {
      var parti = [];

      if (payload.noi) {
        parti.push(payload.noi === 1 ? 'un mesaj nou' : "".concat(payload.noi, " mesaje noi"));
      }

      var desprefisiere = this.rezumatDescarcare(descarcare);
      if (desprefisiere) parti.push(desprefisiere);
      /*
       * Solicitările cărora li s-a găsit răspunsul, dar care nu erau în listă:
       * cereri făcute de pe site-ul ANAF sau înainte de a folosi aplicația. Se
       * spune aici, pentru că rândurile lor apar în altă filă și omul n-ar avea
       * de unde să afle că s-a întâmplat ceva.
       */

      if (payload.solicitari_gasite) {
        parti.push(payload.solicitari_gasite === 1 ? 'o solicitare găsită, trecută în „Solicitări ANAF”' : "".concat(payload.solicitari_gasite, " solicit\u0103ri g\u0103site, trecute \xEEn \u201ESolicit\u0103ri ANAF\u201D"));
      }
      /*
       * Tokenele care n-au răspuns se spun întotdeauna, chiar când restul a mers
       * bine: altfel lista pare întreagă, iar mesajele tokenului lipsă par a nu
       * exista. Cel mai des e doar unul neconectat acum.
       */


      var tacute = (payload.tacute || []).length ? " Nu s-a putut \xEEntreba cu: ".concat(payload.tacute.join('; '), ".") : '';
      if (parti.length) return "ANAF a r\u0103spuns: ".concat(parti.join(', '), ".").concat(tacute);
      var intoarse = payload.intoarse || 0;
      return "ANAF a r\u0103spuns, dar nu e nimic nou: cele ".concat(intoarse, " mesaje din ") + "ultimele ".concat(this.zile, " zile erau deja aduse.").concat(tacute);
    },
    rezumatDescarcare: function rezumatDescarcare(rezultat) {
      if (!rezultat) return '';
      var parti = [];
      if (rezultat.descarcate) parti.push("".concat(rezultat.descarcate, " fi\u0219iere desc\u0103rcate"));
      /*
       * Loturile se cer acum unul după altul, până se golesc. Dacă tot au rămas
       * documente, înseamnă că un lot n-a mai adus nimic și s-a oprit — nu că
       * mai e ceva de apăsat.
       */

      if (rezultat.ramase) parti.push("".concat(rezultat.ramase, " care nu s-au putut aduce acum"));
      if (rezultat.erori && rezultat.erori.length) parti.push("".concat(rezultat.erori.length, " e\u0219uate"));
      /*
       * Când s-a oprit singură, pricina e lucrul cel mai de folos din tot
       * rezumatul: ea spune ce trebuie îndreptat ca restul să poată fi aduse.
       */

      if (rezultat.oprit) {
        return "".concat(parti.join(', '), ". S-a oprit dup\u0103 ").concat(rezultat.opriteLaRand, " e\u0219ecuri la r\xE2nd: ").concat(rezultat.oprit);
      }

      return parti.join(', ');
    },
    // Mesajele SPV stau pe discul privat, deci se cer prin API si se deschid
    // dintr-un blob local, nu printr-un link direct catre /storage.
    openFile: function openFile(id) {
      var _this10 = this;

      this.error = '';
      this.$http.get('/spv/fisier', {
        params: {
          id: id
        },
        responseType: 'blob'
      }).then(function (response) {
        var url = window.URL.createObjectURL(new Blob([response.data], {
          type: 'application/pdf'
        }));
        window.open(url, '_blank');
        setTimeout(function () {
          return window.URL.revokeObjectURL(url);
        }, 60000);
      })["catch"](function () {
        _this10.error = 'Fișierul nu a putut fi deschis.';
      });
    },

    /**
     * Mesajele deja stocate. Nu întreabă ANAF: deschiderea filei și reîncărcarea
     * paginii n-au de ce să consume din limita de apeluri.
     */
    incarcaStocate: function incarcaStocate() {
      var _this11 = this;

      this.error = '';
      return this.$http.get('/spv/stocate').then(function (_ref5) {
        var data = _ref5.data;
        _this11.messages = Array.isArray(data.data.mesaje) ? data.data.mesaje : [];
      })["catch"](function (err) {
        _this11.error = _this11.mesajEroareAlerte(err, 'Mesajele stocate nu au putut fi încărcate');
      });
    },

    /**
     * Aduce de la ANAF mesajele noi și documentele lipsă.
     *
     * @param {boolean} tacut pornit de cronometru: nu deranjează când n-a venit nimic
     */
    loadMessages: function loadMessages() {
      var _this12 = this;

      var tacut = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : false;
      this.loading = true;
      this.error = '';
      this.mersul = '';
      this.aduse = 0;
      this.deAdus = 0;
      /*
       * Lista se cere fără documente: ea vine repede și se vede pe loc, iar
       * documentele se aduc pe urmă, cu numărătoarea la vedere. Într-o singură
       * cerere, tabelul ar fi apărut abia după ultimul document.
       */

      var params = {
        zile: this.zile,
        descarca: 0
      }; // O singură firmă se poate cere direct de la ANAF; pentru mai multe,
      // interogarea merge pe toate și se filtrează la afișare.

      if (this.listaCif.length === 1) {
        var _this$listaCif = Object(C_dianasoft_serviciiweb_web_node_modules_babel_runtime_helpers_esm_slicedToArray_js__WEBPACK_IMPORTED_MODULE_1__["default"])(this.listaCif, 1);

        params.cif = _this$listaCif[0];
      } // Cu un certificat ales, se întreabă doar tokenul lui — și doar PIN-ul
      // lui se cere. Antetul per-cerere bate certificatul activ global.


      if (this.certificatAles) {
        params.certificat_id = this.certificatAles;
      }

      return this.$http.get('/spv', {
        params: params,
        headers: this.antetCertificat
      }).then(function (response) {
        if (response.data && response.data.success) {
          var payload = response.data.data || {};
          _this12.messages = Array.isArray(payload.mesaje) ? payload.mesaje : [];
          _this12.ultimaDescarcare = _this12.acum();
          _this12.ultimaDescarcareNumar = payload.noi || 0;
          window.localStorage.setItem('mesaje_ultima_descarcare', JSON.stringify({
            la: _this12.ultimaDescarcare,
            noi: _this12.ultimaDescarcareNumar
          }));
          /*
           * O apăsare trebuie să spună întotdeauna ce a găsit — și când n-a
           * găsit nimic. Altfel butonul pare că nu face nimic, iar omul crede
           * pe bună dreptate că e stricat.
           */

          var arata = !(tacut && !payload.noi);
          return _this12.aduceDocumentele(payload, arata);
        }

        _this12.error = response.data.message || 'Nu s-au putut încărca datele SPV';
        return Promise.resolve();
      })["catch"](function (err) {
        _this12.error = err.response && err.response.data && err.response.data.message ? err.response.data.message : 'Eroare la conectarea la SPV';
      })["finally"](function () {
        _this12.loading = false;
      });
    },

    /**
     * Aduce documentele lipsă, spunând la fiecare al câtelea e din câte.
     *
     * Răspunsul curge — câte un rând pe măsură ce se lucrează —, așa că omul
     * vede mersul, nu doar o rotiță. O descărcare de zeci de documente ține
     * minute: fiecare are pauza cerută de ANAF și drumul până la tokenul
     * clientului, iar din afară lucrul și împotmolirea arată la fel.
     *
     * @param {object} payload ce a răspuns ANAF la citirea listei
     * @param {boolean} arata se scrie mersul lucrului pe ecran?
     */
    aduceDocumentele: function aduceDocumentele(payload, arata) {
      var _this13 = this;

      var parametri = [];
      if (this.listaCif.length === 1) parametri.push("cif=".concat(encodeURIComponent(this.listaCif[0]))); // Fluxul nu poartă antete proprii, așa că certificatul merge în adresă.

      if (this.certificatAles) parametri.push("certificat_id=".concat(this.certificatAles));
      var intrebare = parametri.length ? "?".concat(parametri.join('&')) : ''; // Browserele fără fetch cu flux rămân pe calea dinainte: loturi, fără
      // numărătoare. Mai bine fără mers decât fără descărcare.

      if (!Object(_libs_flux__WEBPACK_IMPORTED_MODULE_19__["areFlux"])()) {
        var params = {};

        if (this.listaCif.length === 1) {
          var _this$listaCif2 = Object(C_dianasoft_serviciiweb_web_node_modules_babel_runtime_helpers_esm_slicedToArray_js__WEBPACK_IMPORTED_MODULE_1__["default"])(this.listaCif, 1);

          params.cif = _this$listaCif2[0];
        }

        if (this.certificatAles) params.certificat_id = this.certificatAles;
        return this.$http.get('/spv/descarca-lipsa', {
          params: params,
          headers: this.antetCertificat
        }).then(function (raspuns) {
          return _this13.aduceRestul(payload, raspuns.data && raspuns.data.descarcare || {}, arata);
        });
      }

      var rezultat = {
        descarcate: 0,
        ramase: 0,
        erori: []
      };
      return Object(_libs_flux__WEBPACK_IMPORTED_MODULE_19__["default"])("spv/descarca-lipsa/flux".concat(intrebare), function (pas) {
        if (pas.tip === 'inceput') {
          _this13.deAdus = pas.total;
          _this13.aduse = 0;
          if (arata && pas.total) _this13.mersul = "0 din ".concat(pas.total, " documente aduse...");
          return;
        }

        if (pas.tip === 'pas') {
          _this13.aduse = pas.facute;

          if (arata) {
            var care = pas.ce ? " \u2014 ".concat(pas.ce) : ''; // Cand cade, se spune si de ce: „nu s-a putut aduce” singur nu
            // ajuta pe nimeni, iar cand cad sute la rand pricina e aceeasi si
            // se vede din primul.

            var cazut = pas.reusit ? '' : " (nu s-a putut aduce: ".concat(pas.de_ce || 'motiv necunoscut', ")");
            _this13.mersul = "".concat(pas.facute, " din ").concat(pas.total, " documente aduse").concat(care).concat(cazut);
          }

          return;
        } // Aducerea s-a oprit singura: nu mai avea rost sa incerce restul.


        if (pas.tip === 'oprit') {
          rezultat.oprit = pas.de_ce || 'motiv necunoscut';
          rezultat.opriteLaRand = pas.la_rand;

          if (arata) {
            _this13.mersul = "Oprit dup\u0103 ".concat(pas.la_rand, " e\u0219ecuri la r\xE2nd: ").concat(rezultat.oprit);
          }

          return;
        }

        if (pas.tip === 'gata') {
          rezultat.descarcate = pas.descarcate || 0;
          rezultat.ramase = pas.ramase || 0;
          rezultat.erori = pas.erori || [];
          if (Array.isArray(pas.mesaje)) _this13.messages = pas.mesaje;
        }
      }).then(function () {
        if (arata) _this13.infoDescarcare = _this13.rezumatCitire(payload, rezultat);
      })["catch"](function (err) {
        _this13.error = "Aducerea documentelor s-a oprit (".concat(err.message, ").");
      })["finally"](function () {
        _this13.mersul = '';
        _this13.aduse = 0;
        _this13.deAdus = 0;
      });
    },

    /**
     * Aduce loturile rămase, unul după altul, până nu mai rămâne nimic.
     *
     * Nu se mai cere lista de la ANAF la fiecare lot: mesajele sunt deja în
     * baza de date, iar o listă cerută de cinci ori ar consuma degeaba din
     * limita de apeluri. Se cer doar documentele lipsă.
     *
     * @param {object} payload ce a răspuns ANAF la citirea de la început
     * @param {object} descarcare rezultatul primului lot
     * @param {boolean} arata se scrie mersul lucrului pe ecran?
     */
    aduceRestul: function aduceRestul(payload, descarcare, arata) {
      var _this14 = this;

      var params = {};

      if (this.listaCif.length === 1) {
        var _this$listaCif3 = Object(C_dianasoft_serviciiweb_web_node_modules_babel_runtime_helpers_esm_slicedToArray_js__WEBPACK_IMPORTED_MODULE_1__["default"])(this.listaCif, 1);

        params.cif = _this$listaCif3[0];
      }

      if (this.certificatAles) {
        params.certificat_id = this.certificatAles;
      }

      var adunate = {
        descarcate: descarcare.descarcate || 0,
        ramase: descarcare.ramase || 0,
        erori: Object(C_dianasoft_serviciiweb_web_node_modules_babel_runtime_helpers_esm_toConsumableArray_js__WEBPACK_IMPORTED_MODULE_0__["default"])(descarcare.erori || [])
      };

      var incaUnLot = function incaUnLot() {
        if (!adunate.ramase) return Promise.resolve();

        if (arata) {
          _this14.infoDescarcare = "".concat(adunate.descarcate, " documente aduse, ") + "".concat(adunate.ramase, " de adus. V\u0103 rug\u0103m a\u0219tepta\u021Bi...");
        }

        return _this14.$http.get('/spv/descarca-lipsa', {
          params: params,
          headers: _this14.antetCertificat
        }).then(function (response) {
          var _adunate$erori;

          var lot = response.data && response.data.descarcare || {};
          var acum = lot.descarcate || 0;

          if (response.data && response.data.data && Array.isArray(response.data.data.mesaje)) {
            _this14.messages = response.data.data.mesaje;
          }

          adunate.descarcate += acum;
          adunate.ramase = lot.ramase || 0;
          if (lot.erori && lot.erori.length) (_adunate$erori = adunate.erori).push.apply(_adunate$erori, Object(C_dianasoft_serviciiweb_web_node_modules_babel_runtime_helpers_esm_toConsumableArray_js__WEBPACK_IMPORTED_MODULE_0__["default"])(lot.erori));
          /*
           * Un lot din care n-a venit nimic nu are de ce să fie urmat de
           * altul: ori documentele acelea nu se pot aduce, ori ANAF nu le dă
           * acum. Fără oprirea asta, fila ar cere la nesfârșit același lot.
           */

          if (!acum) return Promise.resolve();
          return incaUnLot();
        });
      };

      return incaUnLot().then(function () {
        if (arata) _this14.infoDescarcare = _this14.rezumatCitire(payload, adunate);
      })["catch"](function (err) {
        _this14.error = err.response && err.response.data && err.response.data.message ? err.response.data.message : 'Aducerea documentelor rămase s-a oprit';
      });
    },
    alegeToateFirmele: function alegeToateFirmele() {
      this.firme = this.firme.length === this.optiuniFirmeInrolate.length ? [] : Object(C_dianasoft_serviciiweb_web_node_modules_babel_runtime_helpers_esm_toConsumableArray_js__WEBPACK_IMPORTED_MODULE_0__["default"])(this.optiuniFirmeInrolate);
    },

    /** Momentul de acum, scris ca peste tot în modul: zz.ll.aaaa hh:mm:ss. */
    acum: function acum() {
      var d = new Date();

      var doua = function doua(n) {
        return String(n).padStart(2, '0');
      };

      return "".concat(doua(d.getDate()), ".").concat(doua(d.getMonth() + 1), ".").concat(d.getFullYear(), " ") + "".concat(doua(d.getHours()), ":").concat(doua(d.getMinutes()), ":").concat(doua(d.getSeconds()));
    },
    incarcaSetarea: function incarcaSetarea() {
      try {
        var salvat = JSON.parse(window.localStorage.getItem('mesaje_descarcare_automat'));

        if (salvat && typeof salvat.minute === 'number' && salvat.minute > 0) {
          var permise = this.optiuniMinute.map(function (o) {
            return o.value;
          });
          var minute = permise.indexOf(salvat.minute) !== -1 ? salvat.minute : permise.reduce(function (a, b) {
            return Math.abs(b - salvat.minute) < Math.abs(a - salvat.minute) ? b : a;
          });
          this.automat = {
            activ: !!salvat.activ,
            minute: minute
          };
        }

        var ultima = JSON.parse(window.localStorage.getItem('mesaje_ultima_descarcare'));

        if (ultima) {
          this.ultimaDescarcare = ultima.la || '';
          this.ultimaDescarcareNumar = ultima.noi || 0;
        }
      } catch (e) {// setare veche sau stricată — se rămâne pe valorile implicite
      }
    },
    salveazaSetarea: function salveazaSetarea() {
      window.localStorage.setItem('mesaje_descarcare_automat', JSON.stringify(this.automat));
    },
    opresteCronometrul: function opresteCronometrul() {
      if (this.cronometru) {
        window.clearInterval(this.cronometru);
        this.cronometru = null;
      }
    },
    reglaCronometrul: function reglaCronometrul() {
      var _this15 = this;

      this.opresteCronometrul();
      var minute = Number(this.automat.minute);
      if (!this.automat.activ || !minute || minute < 1) return;
      this.cronometru = window.setInterval(function () {
        if (!_this15.loading) _this15.loadMessages(true);
      }, minute * 60 * 1000);
    }
  }
});

/***/ }),

/***/ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/app_pages/spv/Societati.vue?vue&type=script&lang=js&":
/*!*********************************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib??ref--4-0!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/src/views/app_pages/spv/Societati.vue?vue&type=script&lang=js& ***!
  \*********************************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var C_dianasoft_serviciiweb_web_node_modules_babel_runtime_helpers_esm_objectSpread2_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./node_modules/@babel/runtime/helpers/esm/objectSpread2.js */ "./node_modules/@babel/runtime/helpers/esm/objectSpread2.js");
/* harmony import */ var C_dianasoft_serviciiweb_web_node_modules_babel_runtime_helpers_esm_toConsumableArray_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./node_modules/@babel/runtime/helpers/esm/toConsumableArray.js */ "./node_modules/@babel/runtime/helpers/esm/toConsumableArray.js");
/* harmony import */ var core_js_modules_es_array_filter_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! core-js/modules/es.array.filter.js */ "./node_modules/core-js/modules/es.array.filter.js");
/* harmony import */ var core_js_modules_es_array_filter_js__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_es_array_filter_js__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var core_js_modules_es_object_to_string_js__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! core-js/modules/es.object.to-string.js */ "./node_modules/core-js/modules/es.object.to-string.js");
/* harmony import */ var core_js_modules_es_object_to_string_js__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_es_object_to_string_js__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var core_js_modules_es_array_find_js__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! core-js/modules/es.array.find.js */ "./node_modules/core-js/modules/es.array.find.js");
/* harmony import */ var core_js_modules_es_array_find_js__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_es_array_find_js__WEBPACK_IMPORTED_MODULE_4__);
/* harmony import */ var core_js_modules_es_array_map_js__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! core-js/modules/es.array.map.js */ "./node_modules/core-js/modules/es.array.map.js");
/* harmony import */ var core_js_modules_es_array_map_js__WEBPACK_IMPORTED_MODULE_5___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_es_array_map_js__WEBPACK_IMPORTED_MODULE_5__);
/* harmony import */ var core_js_modules_es_array_from_js__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! core-js/modules/es.array.from.js */ "./node_modules/core-js/modules/es.array.from.js");
/* harmony import */ var core_js_modules_es_array_from_js__WEBPACK_IMPORTED_MODULE_6___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_es_array_from_js__WEBPACK_IMPORTED_MODULE_6__);
/* harmony import */ var core_js_modules_es_string_iterator_js__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! core-js/modules/es.string.iterator.js */ "./node_modules/core-js/modules/es.string.iterator.js");
/* harmony import */ var core_js_modules_es_string_iterator_js__WEBPACK_IMPORTED_MODULE_7___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_es_string_iterator_js__WEBPACK_IMPORTED_MODULE_7__);
/* harmony import */ var core_js_modules_es_string_trim_js__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! core-js/modules/es.string.trim.js */ "./node_modules/core-js/modules/es.string.trim.js");
/* harmony import */ var core_js_modules_es_string_trim_js__WEBPACK_IMPORTED_MODULE_8___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_es_string_trim_js__WEBPACK_IMPORTED_MODULE_8__);
/* harmony import */ var core_js_modules_es_array_join_js__WEBPACK_IMPORTED_MODULE_9__ = __webpack_require__(/*! core-js/modules/es.array.join.js */ "./node_modules/core-js/modules/es.array.join.js");
/* harmony import */ var core_js_modules_es_array_join_js__WEBPACK_IMPORTED_MODULE_9___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_es_array_join_js__WEBPACK_IMPORTED_MODULE_9__);
/* harmony import */ var core_js_modules_es_array_slice_js__WEBPACK_IMPORTED_MODULE_10__ = __webpack_require__(/*! core-js/modules/es.array.slice.js */ "./node_modules/core-js/modules/es.array.slice.js");
/* harmony import */ var core_js_modules_es_array_slice_js__WEBPACK_IMPORTED_MODULE_10___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_es_array_slice_js__WEBPACK_IMPORTED_MODULE_10__);
/* harmony import */ var core_js_modules_es_array_concat_js__WEBPACK_IMPORTED_MODULE_11__ = __webpack_require__(/*! core-js/modules/es.array.concat.js */ "./node_modules/core-js/modules/es.array.concat.js");
/* harmony import */ var core_js_modules_es_array_concat_js__WEBPACK_IMPORTED_MODULE_11___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_es_array_concat_js__WEBPACK_IMPORTED_MODULE_11__);
/* harmony import */ var core_js_modules_web_dom_collections_iterator_js__WEBPACK_IMPORTED_MODULE_12__ = __webpack_require__(/*! core-js/modules/web.dom-collections.iterator.js */ "./node_modules/core-js/modules/web.dom-collections.iterator.js");
/* harmony import */ var core_js_modules_web_dom_collections_iterator_js__WEBPACK_IMPORTED_MODULE_12___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_web_dom_collections_iterator_js__WEBPACK_IMPORTED_MODULE_12__);
/* harmony import */ var core_js_modules_web_url_js__WEBPACK_IMPORTED_MODULE_13__ = __webpack_require__(/*! core-js/modules/web.url.js */ "./node_modules/core-js/modules/web.url.js");
/* harmony import */ var core_js_modules_web_url_js__WEBPACK_IMPORTED_MODULE_13___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_web_url_js__WEBPACK_IMPORTED_MODULE_13__);
/* harmony import */ var core_js_modules_web_url_search_params_js__WEBPACK_IMPORTED_MODULE_14__ = __webpack_require__(/*! core-js/modules/web.url-search-params.js */ "./node_modules/core-js/modules/web.url-search-params.js");
/* harmony import */ var core_js_modules_web_url_search_params_js__WEBPACK_IMPORTED_MODULE_14___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_web_url_search_params_js__WEBPACK_IMPORTED_MODULE_14__);
/* harmony import */ var core_js_modules_es_string_pad_start_js__WEBPACK_IMPORTED_MODULE_15__ = __webpack_require__(/*! core-js/modules/es.string.pad-start.js */ "./node_modules/core-js/modules/es.string.pad-start.js");
/* harmony import */ var core_js_modules_es_string_pad_start_js__WEBPACK_IMPORTED_MODULE_15___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_es_string_pad_start_js__WEBPACK_IMPORTED_MODULE_15__);
/* harmony import */ var core_js_modules_es_object_keys_js__WEBPACK_IMPORTED_MODULE_16__ = __webpack_require__(/*! core-js/modules/es.object.keys.js */ "./node_modules/core-js/modules/es.object.keys.js");
/* harmony import */ var core_js_modules_es_object_keys_js__WEBPACK_IMPORTED_MODULE_16___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_es_object_keys_js__WEBPACK_IMPORTED_MODULE_16__);

















//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
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
  name: 'SpvSocietati',
  data: function data() {
    return {
      societati: [],
      // Implicit se arata doar entitatile in lucru — ele sunt firmele clientului
      arataToate: false,
      // Entitatea careia i se schimba starea chiar acum
      schimbaStarea: null,
      eroare: '',
      info: '',
      pagina: 1,
      pePagina: 25,
      filtre: {
        cif: '',
        denumire: '',
        tip: ''
      },
      listaInCurs: false,
      sincronizareInCurs: false,
      solicitareInCurs: false,

      /*
       * Unde s-a ajuns cu preluarea datelor: pasul, cât s-a făcut și textul de
       * sub bară. Gol înseamnă că nu se lucrează nimic acum.
       */
      progres: null,
      // Necazurile strânse pe drum; se arată toate la sfârșit, nu una câte una.
      erori: [],
      // Câte firme intră într-o cerere: fiecare apel la ANAF are pauza lui.
      FIRME_PE_LOT: 5,
      // Câte răspunsuri se citesc într-o cerere
      RASPUNSURI_PE_LOT: 10,
      // Câte reluări se fac cel mult, ca o listă lungă să nu învârtă la nesfârșit
      RUNDE_MAXIME: 40,
      formularVizibil: false,
      formular: {},
      // Datele firmei care intra in antetul declaratiilor
      dateleVizibile: false,
      dateleInCurs: false,
      dateleEroare: '',
      dateleFirmei: null,
      dateleScrise: {},
      felurileDecontului: [],
      vectorVizibil: false,
      vectorInCurs: false,
      vectorEroare: '',

      /*
       * Luna raportată implicit e cea trecută: pe cea în curs abia se depune,
       * deci raportul ei ar fi mai mereu plin de „nedepusă" fără vină.
       */
      vector: {
        luna: 0,
        anul: 0,
        format: 'pdf'
      },
      // Tabelul declaratiilor asteptate pe CUI: deduse + scrise de om
      actualizareVizibila: false,
      actualizareInCurs: false,
      actualizareEroare: '',
      declaratii: [],
      // Randul din tabel aflat in lucru si valorile lui de scris
      editataId: null,
      editata: {
        perfisc: 'Lunar',
        data_inceput: '',
        data_sfarsit: ''
      },
      // Entitatea al carei buton a deschis fereastra de actualizare
      actualizareCui: '',
      // Vectorul fiscal ANAF al unei entitati: obligatiile ei, doar de citit
      vfVizibil: false,
      vfInCurs: false,
      vfEroare: '',
      vfCui: '',
      vfRanduri: [],
      vfArataIstoricul: false,
      noua: {
        tip: '',
        perfisc: 'Lunar',
        data_inceput: '',
        data_sfarsit: ''
      },
      periodicitati: ['Lunar', 'Trimestrial', 'Semestrial', 'Anual'],
      // Sugestii pentru campul de tip; se poate scrie si altceva
      tipuriCunoscute: ['D100', 'D101', 'D112', 'D205', 'D212', 'D300', 'D301', 'D307', 'D311', 'D390', 'D394', 'D406', 'BILANT'],
      campuriDeclaratii: [{
        key: 'tip',
        label: 'Tip'
      }, {
        key: 'perfisc',
        label: 'Periodicitate'
      }, {
        key: 'valabilitate',
        label: 'Valabilitate'
      }, {
        key: 'sursa',
        label: 'Sursa'
      }, {
        key: 'actiuni',
        label: ''
      }],
      campuri: [{
        key: 'cif',
        label: 'CIF'
      }, {
        key: 'denumire',
        label: 'Denumire'
      }, {
        key: 'tip',
        label: 'Tip'
      }, {
        key: 'activ',
        label: 'Stare'
      }, {
        key: 'date',
        label: 'Date preluate din SPV'
      }, {
        key: 'certificat',
        label: 'Certificat'
      }, {
        key: 'actiuni',
        label: 'Acțiuni'
      }]
    };
  },
  computed: {
    /** Fereastra e legată de entitatea de pe rândul al cărei buton a deschis-o. */
    declaratiiFiltrate: function declaratiiFiltrate() {
      var _this = this;

      return this.declaratii.filter(function (d) {
        return d.cui === _this.actualizareCui;
      });
    },
    actualizareDenumire: function actualizareDenumire() {
      return this.etichetaEntitate(this.actualizareCui);
    },

    /** Implicit doar obligațiile în vigoare; istoricul se cere anume. */
    vfRanduriFiltrate: function vfRanduriFiltrate() {
      if (this.vfArataIstoricul) return this.vfRanduri;
      return this.vfRanduri.filter(function (r) {
        return !r.data_sfarsit;
      });
    },
    vfDataVector: function vfDataVector() {
      var cuData = this.vfRanduri.find(function (r) {
        return r.data_vector;
      });
      return cuData ? cuData.data_vector : '';
    },
    lunileAnului: function lunileAnului() {
      return ['ianuarie', 'februarie', 'martie', 'aprilie', 'mai', 'iunie', 'iulie', 'august', 'septembrie', 'octombrie', 'noiembrie', 'decembrie'].map(function (nume, index) {
        return {
          value: index + 1,
          text: nume
        };
      });
    },

    /** Anii de ales: de la anul viitor înapoi, cât ține istoricul rezonabil. */
    aniiDeAles: function aniiDeAles() {
      var anulAcesta = new Date().getFullYear();
      return Array.from({
        length: 7
      }, function (v, i) {
        return anulAcesta + 1 - i;
      });
    },

    /** Entitățile care trec de filtrele scrise pe coloane. */
    societatiFiltrate: function societatiFiltrate() {
      var _this2 = this;

      var contine = function contine(valoare, cautat) {
        return String(valoare || '').toLowerCase().indexOf(cautat.toLowerCase()) !== -1;
      };

      return this.societati.filter(function (societate) {
        if (_this2.filtre.tip && societate.tip !== _this2.filtre.tip) return false;
        return ['cif', 'denumire'].every(function (cheie) {
          var cautat = (_this2.filtre[cheie] || '').trim();
          return cautat === '' || contine(societate[cheie], cautat);
        });
      });
    }
  },
  created: function created() {
    this.incarcaLista();
  },
  methods: {
    etichetaSursa: function etichetaSursa(sursa) {
      return {
        vector: 'vector fiscal',
        date_identificare: 'date identificare',
        manual: 'manual'
      }[sursa] || sursa;
    },

    /**
     * Scoate din uz o entitate, sau o pune iar în lucru.
     *
     * Un client are adesea în certificat firme pe care nu le mai ține: ele
     * încărcau degeaba fiecare interogare la ANAF și fiecare listă de ales.
     * Scoasă din uz, entitatea e ignorată peste tot — la mesaje, la solicitări,
     * la alerte — dar rămâne în evidență, cu documentele ei.
     *
     * Alegerea aceasta stă deoparte de „activ", care e cuvântul ANAF-ului:
     * altfel prima sincronizare ar șterge-o.
     */
    schimbaUzul: function schimbaUzul(societate) {
      var _this3 = this;

      this.eroare = '';
      this.schimbaStarea = societate.id;
      var scoasa = !societate.scos_din_uz;
      this.$http.put("/anaf-societati/".concat(societate.id), {
        scos_din_uz: scoasa
      }).then(function () {
        _this3.info = scoasa ? "".concat(societate.denumire || societate.cif, " a fost scoas\u0103 din uz \u0219i va fi ignorat\u0103 peste tot.") : "".concat(societate.denumire || societate.cif, " este iar \xEEn lucru.");
        return _this3.incarcaLista();
      })["catch"](function (err) {
        _this3.eroare = _this3.mesajEroare(err, 'Starea entității nu a putut fi schimbată');
      })["finally"](function () {
        _this3.schimbaStarea = null;
      });
    },
    incarcaLista: function incarcaLista() {
      var _this4 = this;

      this.listaInCurs = true; // Filtrarea pe CIF, denumire si tip se face pe coloanele tabelului.

      var params = {};
      if (!this.arataToate) params.doar_active = 1;
      return this.$http.get('/anaf-societati', {
        params: params
      }).then(function (raspuns) {
        _this4.societati = raspuns.data.data || [];
      })["catch"](function (err) {
        _this4.eroare = _this4.mesajEroare(err, 'Nu s-a putut încărca lista de entități');
      })["finally"](function () {
        _this4.listaInCurs = false;
      });
    },
    sincronizeaza: function sincronizeaza() {
      var _this5 = this;

      this.eroare = '';
      this.info = '';
      this.sincronizareInCurs = true;
      this.$http.post('/anaf-societati/sincronizeaza').then(function (raspuns) {
        var r = raspuns.data.data;
        var parti = ["".concat(r.gasite, " CIF-uri \xEEn certificat")];
        if (r.noi) parti.push("".concat(r.noi, " noi"));
        if (r.dezactivate) parti.push("".concat(r.dezactivate, " f\u0103r\u0103 drepturi acum"));
        _this5.info = parti.join(', ');

        _this5.incarcaLista();
      })["catch"](function (err) {
        _this5.eroare = _this5.mesajEroare(err, 'Sincronizarea a eșuat');
      })["finally"](function () {
        _this5.sincronizareInCurs = false;
      });
    },

    /**
     * Aduce datele lipsă ale firmelor, în trei pași.
     *
     * Întâi pleacă solicitările către ANAF, firmă cu firmă; apoi se descarcă
     * mesajele nou intrate, în loturi, până nu mai rămâne niciunul; abia la
     * urmă se citesc din documentele descărcate denumirile și datele firmelor.
     *
     * Se lucrează în tranșe pentru că fiecare apel la ANAF are pauza lui
     * impusă: cu zeci de firme, totul într-o singură cerere web ar depăși orice
     * răbdare a serverului. Așa se vede și la a câta firmă s-a ajuns.
     */
    solicita: function solicita() {
      var _this6 = this;

      this.eroare = '';
      this.info = '';
      this.solicitareInCurs = true;
      this.erori = []; // Pe entitatile scoase din uz nu se cheltuie apeluri la ANAF.

      var cifuri = this.societati.filter(function (s) {
        return s.in_lucru;
      }).map(function (s) {
        return s.cif;
      });
      this.progres = {
        pas: 'mesaje',
        facut: 0,
        total: 1,
        text: 'Se caută documentele venite deja...'
      }; // Întâi se vede ce a intrat de curând: poate datele sunt deja acolo și
      // n-are rost să se mai ceară o dată de la ANAF.

      this.ceEDeja().then(function () {
        return _this6.trimiteSolicitari(cifuri);
      }).then(function (rezumat) {
        return _this6.descarcaMesajeNoi(rezumat);
      }).then(function (rezumat) {
        return _this6.preiaRaspunsurile(rezumat);
      }).then(function (rezumat) {
        var parti = [];
        if (rezumat.trimise) parti.push("".concat(rezumat.trimise, " solicit\u0103ri trimise"));
        if (rezumat.descarcate) parti.push("".concat(rezumat.descarcate, " mesaje desc\u0103rcate"));
        if (rezumat.preluate) parti.push("".concat(rezumat.preluate, " r\u0103spunsuri citite"));
        if (rezumat.reinterpretate) parti.push("".concat(rezumat.reinterpretate, " documente reinterpretate"));
        if (rezumat.sarite) parti.push("".concat(rezumat.sarite, " s\u0103rite (deja cerute azi sau persoane fizice)"));
        _this6.info = parti.length ? parti.join(', ') : 'Nu era nimic de solicitat.';
        if (_this6.erori.length) _this6.eroare = _this6.erori.join(' | ');

        _this6.incarcaLista();
      })["catch"](function (err) {
        _this6.eroare = _this6.mesajEroare(err, 'Solicitarea a eșuat');
      })["finally"](function () {
        _this6.solicitareInCurs = false;
        _this6.progres = null;
      });
    },

    /**
     * Pasul dinaintea tuturor: ce a intrat deja în SPV.
     *
     * Se aduc mesajele din ultima zi și se citesc răspunsurile venite la
     * solicitările vechi. Documentul cerut ieri poate fi deja acolo, iar firma
     * ale cărei date se află astfel nu mai intră în lista de solicitat — nici
     * ANAF nu are de ce să răspundă a doua oară la aceeași întrebare.
     */
    ceEDeja: function ceEDeja() {
      var _this7 = this;

      return this.$http.get('/spv', {
        params: {
          zile: 1,
          descarca: 1
        }
      }).then(function (raspuns) {
        var _this7$erori;

        var d = raspuns.data.descarcare || {};
        if (d.erori && d.erori.length) (_this7$erori = _this7.erori).push.apply(_this7$erori, Object(C_dianasoft_serviciiweb_web_node_modules_babel_runtime_helpers_esm_toConsumableArray_js__WEBPACK_IMPORTED_MODULE_1__["default"])(d.erori));
        _this7.progres = {
          pas: 'mesaje',
          facut: 1,
          total: 1,
          text: 'Se citesc documentele venite deja...'
        };
        return _this7.$http.post("/spv/solicitari/preia?zile=1&limita=".concat(_this7.RASPUNSURI_PE_LOT));
      }).then(function () {
        return _this7.incarcaLista();
      })["catch"](function (err) {
        // Un pas de aflare nu are voie să oprească lucrarea: dacă n-a mers,
        // se merge mai departe și se cer datele de la ANAF, ca înainte.
        _this7.erori.push(_this7.mesajEroare(err, 'Documentele venite deja nu au putut fi citite'));
      });
    },

    /** Pasul întâi: solicitările, în tranșe de câteva firme. */
    trimiteSolicitari: function trimiteSolicitari(cifuri) {
      var _this8 = this;

      var rezumat = {
        trimise: 0,
        sarite: 0,
        reinterpretate: 0,
        descarcate: 0,
        preluate: 0
      };

      var lot = function lot(i) {
        if (i >= cifuri.length) return Promise.resolve(rezumat);
        var acum = cifuri.slice(i, i + _this8.FIRME_PE_LOT);
        _this8.progres = {
          pas: 'solicitari',
          facut: i,
          total: cifuri.length,
          text: "Se trimit solicit\u0103rile: ".concat(i, " din ").concat(cifuri.length, " firme")
        }; // Documentele vechi se recitesc o singură dată, la primul lot.

        return _this8.$http.post('/anaf-societati/solicita', {
          cif: acum,
          reinterpreteaza: i === 0
        }).then(function (raspuns) {
          var _this8$erori;

          var r = raspuns.data.data;
          rezumat.trimise += r.trimise || 0;
          rezumat.sarite += r.sarite || 0;
          rezumat.reinterpretate += r.reinterpretate || 0;
          if (r.erori && r.erori.length) (_this8$erori = _this8.erori).push.apply(_this8$erori, Object(C_dianasoft_serviciiweb_web_node_modules_babel_runtime_helpers_esm_toConsumableArray_js__WEBPACK_IMPORTED_MODULE_1__["default"])(r.erori));
          return lot(i + _this8.FIRME_PE_LOT);
        });
      };

      return lot(0);
    },

    /**
     * Pasul al doilea: mesajele nou intrate în SPV.
     *
     * Serverul aduce fișierele în loturi și spune câte au mai rămas; se cere
     * din nou până nu mai rămâne niciunul.
     */
    descarcaMesajeNoi: function descarcaMesajeNoi(rezumatul) {
      var _this9 = this;

      var rezumat = Object(C_dianasoft_serviciiweb_web_node_modules_babel_runtime_helpers_esm_objectSpread2_js__WEBPACK_IMPORTED_MODULE_0__["default"])({}, rezumatul);

      var runda = function runda(trecute) {
        _this9.progres = {
          pas: 'mesaje',
          facut: rezumat.descarcate,
          total: rezumat.descarcate + 1,
          text: "Se descarc\u0103 mesajele noi: ".concat(rezumat.descarcate, " aduse")
        };
        return _this9.$http.get('/spv', {
          params: {
            zile: 60,
            descarca: 1
          }
        }).then(function (raspuns) {
          var _this9$erori;

          var d = raspuns.data.descarcare || {};
          rezumat.descarcate += d.descarcate || 0;
          if (d.erori && d.erori.length) (_this9$erori = _this9.erori).push.apply(_this9$erori, Object(C_dianasoft_serviciiweb_web_node_modules_babel_runtime_helpers_esm_toConsumableArray_js__WEBPACK_IMPORTED_MODULE_1__["default"])(d.erori)); // Se oprește când nu mai rămâne nimic sau când o rundă n-a mai adus nimic.

          if (!d.ramase || !d.descarcate || trecute >= _this9.RUNDE_MAXIME) {
            return rezumat;
          }

          return runda(trecute + 1);
        });
      };

      return runda(0);
    },

    /** Pasul al treilea: din documentele descărcate se iau denumirile și datele. */
    preiaRaspunsurile: function preiaRaspunsurile(rezumatul) {
      var _this10 = this;

      var rezumat = Object(C_dianasoft_serviciiweb_web_node_modules_babel_runtime_helpers_esm_objectSpread2_js__WEBPACK_IMPORTED_MODULE_0__["default"])({}, rezumatul);

      var runda = function runda(trecute) {
        _this10.progres = {
          pas: 'date',
          facut: rezumat.preluate,
          total: rezumat.preluate + 1,
          text: "Se citesc datele firmelor: ".concat(rezumat.preluate, " r\u0103spunsuri prelucrate")
        };
        return _this10.$http.post("/spv/solicitari/preia?limita=".concat(_this10.RASPUNSURI_PE_LOT)).then(function (raspuns) {
          var _this10$erori;

          var r = raspuns.data.data || {};
          rezumat.preluate += r.preluate || 0;
          if (r.erori && r.erori.length) (_this10$erori = _this10.erori).push.apply(_this10$erori, Object(C_dianasoft_serviciiweb_web_node_modules_babel_runtime_helpers_esm_toConsumableArray_js__WEBPACK_IMPORTED_MODULE_1__["default"])(r.erori)); // Cele fără răspuns încă nu se mai așteaptă: vin la o rulare viitoare.

          if (!r.preluate || trecute >= _this10.RUNDE_MAXIME) {
            return rezumat;
          }

          return runda(trecute + 1);
        });
      };

      return runda(0);
    },
    etichetaEntitate: function etichetaEntitate(cui) {
      var gasita = this.societati.find(function (s) {
        return s.cif === cui;
      });
      return gasita ? "".concat(gasita.denumire || 'fără denumire', " (").concat(gasita.cif, ")") : cui;
    },

    /** Vectorul fiscal ANAF al entității: obligațiile ei, doar de citit. */
    deschideVectorFiscal: function deschideVectorFiscal(societate) {
      var _this11 = this;

      this.vfCui = societate.cif;
      this.vfEroare = '';
      this.vfRanduri = [];
      this.vfArataIstoricul = false;
      this.vfVizibil = true;
      this.vfInCurs = true;
      this.$http.get('/vector-fiscal/spv', {
        params: {
          cui: societate.cif
        }
      }).then(function (raspuns) {
        _this11.vfRanduri = raspuns.data.data || [];
      })["catch"](function (err) {
        _this11.vfEroare = _this11.mesajEroare(err, 'Vectorul fiscal nu a putut fi încărcat');
      })["finally"](function () {
        _this11.vfInCurs = false;
      });
    },
    deschideActualizare: function deschideActualizare(societate) {
      this.actualizareCui = societate.cif;
      this.actualizareEroare = '';
      this.noua = {
        tip: '',
        perfisc: 'Lunar',
        data_inceput: '',
        data_sfarsit: ''
      };
      this.actualizareVizibila = true; // La deschidere, deducția se face pe loc: tabelul are ce arăta și
      // înainte de primul raport descărcat.

      this.incarcaDeclaratii(true);
    },
    incarcaDeclaratii: function incarcaDeclaratii(deduce) {
      var _this12 = this;

      this.actualizareInCurs = true; // Filtrarea pe entitate se face pe lista deja adusă; se cere tot.

      return this.$http.get('/vector-fiscal/declaratii', {
        params: deduce ? {
          deduce: 1
        } : {}
      }).then(function (raspuns) {
        _this12.declaratii = raspuns.data.data || [];
      })["catch"](function (err) {
        _this12.actualizareEroare = _this12.mesajEroare(err, 'Declarațiile nu au putut fi încărcate');
      })["finally"](function () {
        _this12.actualizareInCurs = false;
      });
    },
    adaugaDeclaratie: function adaugaDeclaratie() {
      var _this13 = this;

      this.actualizareEroare = '';
      this.actualizareInCurs = true;
      var date = {
        cui: this.actualizareCui,
        tip: this.noua.tip,
        perfisc: this.noua.perfisc,
        data_inceput: this.noua.data_inceput || null,
        data_sfarsit: this.noua.data_sfarsit || null
      };
      this.$http.post('/vector-fiscal/declaratii', date).then(function () {
        // Firma rămâne aleasă: de obicei se adaugă mai multe tipuri la rând.
        _this13.noua.tip = '';
        _this13.noua.data_inceput = '';
        _this13.noua.data_sfarsit = '';
        return _this13.incarcaDeclaratii();
      })["catch"](function (err) {
        _this13.actualizareEroare = _this13.mesajEroare(err, 'Declarația nu a putut fi adăugată');
        _this13.actualizareInCurs = false;
      });
    },
    incepeModificarea: function incepeModificarea(declaratie) {
      this.actualizareEroare = '';
      this.editataId = declaratie.id;
      this.editata = {
        perfisc: declaratie.perfisc,
        data_inceput: declaratie.data_inceput || '',
        data_sfarsit: declaratie.data_sfarsit || ''
      };
    },

    /**
     * Salvează îndreptarea — și pe rândurile deduse.
     *
     * Serverul le trece atunci la „manuală": altfel următoarea întocmire a
     * raportului ar scrie deducția la loc peste ce a îndreptat omul.
     */
    salveazaModificarea: function salveazaModificarea() {
      var _this14 = this;

      this.actualizareEroare = '';
      this.actualizareInCurs = true;
      var date = {
        perfisc: this.editata.perfisc,
        data_inceput: this.editata.data_inceput || null,
        data_sfarsit: this.editata.data_sfarsit || null
      };
      this.$http.put("/vector-fiscal/declaratii/".concat(this.editataId), date).then(function () {
        _this14.editataId = null;
        return _this14.incarcaDeclaratii();
      })["catch"](function (err) {
        _this14.actualizareEroare = _this14.mesajEroare(err, 'Modificarea nu a putut fi salvată');
        _this14.actualizareInCurs = false;
      });
    },
    stergeDeclaratie: function stergeDeclaratie(declaratie) {
      var _this15 = this;

      this.actualizareEroare = '';
      this.actualizareInCurs = true;
      this.$http["delete"]("/vector-fiscal/declaratii/".concat(declaratie.id)).then(function () {
        return _this15.incarcaDeclaratii();
      })["catch"](function (err) {
        _this15.actualizareEroare = _this15.mesajEroare(err, 'Declarația nu a putut fi ștearsă');
        _this15.actualizareInCurs = false;
      });
    },
    deschideVector: function deschideVector() {
      // Luna trecută, propusă de fiecare dată: cea aleasă rândul trecut poate
      // fi departe în urmă, iar omul vine de obicei pentru luna abia încheiată.
      var lunaTrecuta = new Date();
      lunaTrecuta.setDate(1);
      lunaTrecuta.setMonth(lunaTrecuta.getMonth() - 1);
      this.vector.luna = lunaTrecuta.getMonth() + 1;
      this.vector.anul = lunaTrecuta.getFullYear();
      this.vectorEroare = '';
      this.vectorVizibil = true;
    },

    /**
     * Descarcă vectorul fiscal al lunii alese, în forma aleasă.
     *
     * Fișierul vine ca blob, nu printr-un link direct: ruta cere tokenul, iar
     * un `window.open` nu-l poartă cu el.
     */
    descarcaVector: function descarcaVector() {
      var _this16 = this;

      this.vectorEroare = '';
      this.vectorInCurs = true;
      var params = {
        luna: this.vector.luna,
        anul: this.vector.anul,
        format: this.vector.format
      };
      this.$http.get('/vector-fiscal/lunar', {
        params: params,
        responseType: 'blob'
      }).then(function (raspuns) {
        var url = window.URL.createObjectURL(new Blob([raspuns.data]));
        var legatura = document.createElement('a');
        var extensie = _this16.vector.format === 'excel' ? 'xlsx' : 'pdf';
        var luna = String(_this16.vector.luna).padStart(2, '0');
        legatura.href = url;
        legatura.download = "vector_fiscal_".concat(luna, "_").concat(_this16.vector.anul, ".").concat(extensie);
        document.body.appendChild(legatura);
        legatura.click();
        document.body.removeChild(legatura);
        setTimeout(function () {
          return window.URL.revokeObjectURL(url);
        }, 60000);
        _this16.vectorVizibil = false;
      })["catch"](function (err) {
        return _this16.aratEroareVector(err);
      })["finally"](function () {
        _this16.vectorInCurs = false;
      });
    },

    /**
     * Eroarea vine tot ca blob (răspunsul a fost cerut așa) — se citește din
     * el mesajul serverului, ca omul să afle de ce n-a primit fișierul.
     */
    aratEroareVector: function aratEroareVector(err) {
      var _this17 = this;

      var implicit = 'Raportul nu a putut fi întocmit.';

      if (!(err.response && err.response.data instanceof Blob)) {
        this.vectorEroare = this.mesajEroare(err, implicit);
        return;
      }

      err.response.data.text().then(function (text) {
        var date = JSON.parse(text);
        _this17.vectorEroare = date.message || implicit;
      })["catch"](function () {
        _this17.vectorEroare = implicit;
      });
    },
    editeaza: function editeaza(societate) {
      this.formular = Object(C_dianasoft_serviciiweb_web_node_modules_babel_runtime_helpers_esm_objectSpread2_js__WEBPACK_IMPORTED_MODULE_0__["default"])({}, societate);
      this.formularVizibil = true;
    },

    /**
     * Datele de declarație ale entității, aduse de la server.
     *
     * Se cer de fiecare dată: ele se schimbă rar, dar când se schimbă contează
     * să nu lucrăm cu ce s-a încărcat la deschiderea filei.
     */
    deschideDateleDeclaratiilor: function deschideDateleDeclaratiilor(societate) {
      var _this18 = this;

      this.dateleFirmei = null;
      this.dateleScrise = {};
      this.dateleEroare = '';
      this.dateleVizibile = true;
      this.dateleInCurs = true;
      this.$http.get("/anaf-societati/".concat(societate.id, "/date-declaratii")).then(function (raspuns) {
        _this18.arataDatele(raspuns.data.data, societate.id);
      })["catch"](function (err) {
        _this18.dateleEroare = _this18.mesajEroare(err, 'Datele nu au putut fi citite');
      })["finally"](function () {
        _this18.dateleInCurs = false;
      });
    },
    salveazaDatele: function salveazaDatele() {
      var _this19 = this;

      this.dateleEroare = '';
      this.dateleInCurs = true;
      this.$http.put("/anaf-societati/".concat(this.dateleFirmei.id, "/date-declaratii"), this.dateleScrise).then(function (raspuns) {
        _this19.arataDatele(raspuns.data.data, _this19.dateleFirmei.id); // Fereastra rămâne deschisă cât timp mai lipsește ceva: omul vede pe
        // loc ce anume, fără s-o deschidă din nou.


        if (_this19.dateleFirmei.gata) {
          _this19.dateleVizibile = false;
        }
      })["catch"](function (err) {
        _this19.dateleEroare = _this19.mesajEroare(err, 'Salvarea a eșuat');
      })["finally"](function () {
        _this19.dateleInCurs = false;
      });
    },

    /** Răspunsul serverului, pus în formular. */
    arataDatele: function arataDatele(date, id) {
      this.dateleFirmei = Object(C_dianasoft_serviciiweb_web_node_modules_babel_runtime_helpers_esm_objectSpread2_js__WEBPACK_IMPORTED_MODULE_0__["default"])(Object(C_dianasoft_serviciiweb_web_node_modules_babel_runtime_helpers_esm_objectSpread2_js__WEBPACK_IMPORTED_MODULE_0__["default"])({}, date), {}, {
        id: id
      });
      this.dateleScrise = Object(C_dianasoft_serviciiweb_web_node_modules_babel_runtime_helpers_esm_objectSpread2_js__WEBPACK_IMPORTED_MODULE_0__["default"])({}, date.date);
      this.felurileDecontului = Object.keys(date.feluri_decont).map(function (cheie) {
        return {
          value: cheie,
          text: "".concat(cheie, " \u2014 ").concat(date.feluri_decont[cheie])
        };
      });
    },
    salveaza: function salveaza() {
      var _this20 = this;

      this.$http.put("/anaf-societati/".concat(this.formular.id), {
        denumire: this.formular.denumire
      }).then(function () {
        _this20.incarcaLista();
      })["catch"](function (err) {
        _this20.eroare = _this20.mesajEroare(err, 'Salvarea a eșuat');
      });
    },
    mesajEroare: function mesajEroare(err, implicit) {
      return err.response && err.response.data && err.response.data.message ? err.response.data.message : implicit;
    }
  }
});

/***/ }),

/***/ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/app_pages/spv/Solicitari.vue?vue&type=script&lang=js&":
/*!**********************************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib??ref--4-0!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/src/views/app_pages/spv/Solicitari.vue?vue&type=script&lang=js& ***!
  \**********************************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var C_dianasoft_serviciiweb_web_node_modules_babel_runtime_helpers_esm_toConsumableArray_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./node_modules/@babel/runtime/helpers/esm/toConsumableArray.js */ "./node_modules/@babel/runtime/helpers/esm/toConsumableArray.js");
/* harmony import */ var core_js_modules_es_array_map_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! core-js/modules/es.array.map.js */ "./node_modules/core-js/modules/es.array.map.js");
/* harmony import */ var core_js_modules_es_array_map_js__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_es_array_map_js__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var core_js_modules_es_array_concat_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! core-js/modules/es.array.concat.js */ "./node_modules/core-js/modules/es.array.concat.js");
/* harmony import */ var core_js_modules_es_array_concat_js__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_es_array_concat_js__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var core_js_modules_es_array_filter_js__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! core-js/modules/es.array.filter.js */ "./node_modules/core-js/modules/es.array.filter.js");
/* harmony import */ var core_js_modules_es_array_filter_js__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_es_array_filter_js__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var core_js_modules_es_object_to_string_js__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! core-js/modules/es.object.to-string.js */ "./node_modules/core-js/modules/es.object.to-string.js");
/* harmony import */ var core_js_modules_es_object_to_string_js__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_es_object_to_string_js__WEBPACK_IMPORTED_MODULE_4__);
/* harmony import */ var core_js_modules_es_number_constructor_js__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! core-js/modules/es.number.constructor.js */ "./node_modules/core-js/modules/es.number.constructor.js");
/* harmony import */ var core_js_modules_es_number_constructor_js__WEBPACK_IMPORTED_MODULE_5___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_es_number_constructor_js__WEBPACK_IMPORTED_MODULE_5__);
/* harmony import */ var core_js_modules_es_object_keys_js__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! core-js/modules/es.object.keys.js */ "./node_modules/core-js/modules/es.object.keys.js");
/* harmony import */ var core_js_modules_es_object_keys_js__WEBPACK_IMPORTED_MODULE_6___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_es_object_keys_js__WEBPACK_IMPORTED_MODULE_6__);
/* harmony import */ var core_js_modules_es_string_trim_js__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! core-js/modules/es.string.trim.js */ "./node_modules/core-js/modules/es.string.trim.js");
/* harmony import */ var core_js_modules_es_string_trim_js__WEBPACK_IMPORTED_MODULE_7___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_es_string_trim_js__WEBPACK_IMPORTED_MODULE_7__);
/* harmony import */ var core_js_modules_es_string_iterator_js__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! core-js/modules/es.string.iterator.js */ "./node_modules/core-js/modules/es.string.iterator.js");
/* harmony import */ var core_js_modules_es_string_iterator_js__WEBPACK_IMPORTED_MODULE_8___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_es_string_iterator_js__WEBPACK_IMPORTED_MODULE_8__);
/* harmony import */ var core_js_modules_web_dom_collections_iterator_js__WEBPACK_IMPORTED_MODULE_9__ = __webpack_require__(/*! core-js/modules/web.dom-collections.iterator.js */ "./node_modules/core-js/modules/web.dom-collections.iterator.js");
/* harmony import */ var core_js_modules_web_dom_collections_iterator_js__WEBPACK_IMPORTED_MODULE_9___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_web_dom_collections_iterator_js__WEBPACK_IMPORTED_MODULE_9__);
/* harmony import */ var core_js_modules_web_url_js__WEBPACK_IMPORTED_MODULE_10__ = __webpack_require__(/*! core-js/modules/web.url.js */ "./node_modules/core-js/modules/web.url.js");
/* harmony import */ var core_js_modules_web_url_js__WEBPACK_IMPORTED_MODULE_10___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_web_url_js__WEBPACK_IMPORTED_MODULE_10__);
/* harmony import */ var core_js_modules_web_url_search_params_js__WEBPACK_IMPORTED_MODULE_11__ = __webpack_require__(/*! core-js/modules/web.url-search-params.js */ "./node_modules/core-js/modules/web.url-search-params.js");
/* harmony import */ var core_js_modules_web_url_search_params_js__WEBPACK_IMPORTED_MODULE_11___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_web_url_search_params_js__WEBPACK_IMPORTED_MODULE_11__);
/* harmony import */ var core_js_modules_es_array_join_js__WEBPACK_IMPORTED_MODULE_12__ = __webpack_require__(/*! core-js/modules/es.array.join.js */ "./node_modules/core-js/modules/es.array.join.js");
/* harmony import */ var core_js_modules_es_array_join_js__WEBPACK_IMPORTED_MODULE_12___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_es_array_join_js__WEBPACK_IMPORTED_MODULE_12__);
/* harmony import */ var core_js_modules_es_json_stringify_js__WEBPACK_IMPORTED_MODULE_13__ = __webpack_require__(/*! core-js/modules/es.json.stringify.js */ "./node_modules/core-js/modules/es.json.stringify.js");
/* harmony import */ var core_js_modules_es_json_stringify_js__WEBPACK_IMPORTED_MODULE_13___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_es_json_stringify_js__WEBPACK_IMPORTED_MODULE_13__);
/* harmony import */ var core_js_modules_es_string_pad_start_js__WEBPACK_IMPORTED_MODULE_14__ = __webpack_require__(/*! core-js/modules/es.string.pad-start.js */ "./node_modules/core-js/modules/es.string.pad-start.js");
/* harmony import */ var core_js_modules_es_string_pad_start_js__WEBPACK_IMPORTED_MODULE_14___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_es_string_pad_start_js__WEBPACK_IMPORTED_MODULE_14__);
/* harmony import */ var vue_select__WEBPACK_IMPORTED_MODULE_15__ = __webpack_require__(/*! vue-select */ "./node_modules/vue-select/dist/vue-select.js");
/* harmony import */ var vue_select__WEBPACK_IMPORTED_MODULE_15___default = /*#__PURE__*/__webpack_require__.n(vue_select__WEBPACK_IMPORTED_MODULE_15__);
/* harmony import */ var _libs_flux__WEBPACK_IMPORTED_MODULE_16__ = __webpack_require__(/*! @/libs/flux */ "./resources/js/src/libs/flux.js");















//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
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
  name: 'SpvSolicitariTab',
  components: {
    vSelect: vue_select__WEBPACK_IMPORTED_MODULE_15___default.a
  },
  data: function data() {
    return {
      // Firmele alese, luate din Entități înrolate
      firme: [],
      societati: [],
      societatiInCurs: false,
      // Tokenul cu care pleacă solicitările; null înseamnă oricare
      certificatAles: null,
      certificate: [],
      tipDocument: null,
      an: null,
      luna: null,
      motiv: '',
      numarInregistrare: '',
      cuiPui: '',
      tipuri: [],
      // tip => parametrii suplimentari ceruti de ANAF
      parametriTip: {},
      solicitari: [],
      eroare: '',
      pagina: 1,
      pePagina: 25,
      // Filtre pe coloanele tabelului; lucreaza pe ce e deja adus
      filtre: {
        cif: '',
        den_firma: '',
        tip_document: '',
        data_solicitarii: '',
        data_afisare: '',
        stare: '',
        obs: '',
        certificat_nume: ''
      },
      listaInCurs: false,
      trimiteInCurs: false,
      preiaInCurs: false,
      // Mersul lucrului, cat timp se preia: „3 din 7 — Fisa rol 15208744"
      mersul: '',
      aduse: 0,
      deAdus: 0,
      // Preluarea repetată, ca la recipise: ANAF răspunde după un timp
      automat: {
        activ: false,
        minute: 10
      },
      optiuniMinute: [{
        value: 5,
        text: '5 minute'
      }, {
        value: 10,
        text: '10 minute'
      }, {
        value: 15,
        text: '15 minute'
      }, {
        value: 30,
        text: '30 minute'
      }, {
        value: 60,
        text: '60 minute'
      }],
      cronometru: null,
      ultimaPreluare: '',
      ultimaPreluareNumar: 0,
      tiparire: false,
      filigran: false,
      campuri: [{
        key: 'cif',
        label: 'CIF'
      }, {
        key: 'den_firma',
        label: 'Denumire'
      }, {
        key: 'tip_document',
        label: 'Tip document'
      }, {
        key: 'data_solicitarii',
        label: 'Solicitat la'
      }, {
        key: 'data_afisare',
        label: 'Răspuns la'
      }, {
        key: 'stare',
        label: 'Stare'
      }, {
        key: 'obs',
        label: 'Observații'
      }, {
        key: 'certificat_nume',
        label: 'Certificat'
      }, {
        key: 'actiuni',
        label: 'Acțiuni'
      }]
    };
  },
  computed: {
    listaCui: function listaCui() {
      return this.firme.map(function (firma) {
        return firma.cif;
      });
    },
    optiuniCertificatToken: function optiuniCertificatToken() {
      return [{
        value: null,
        text: 'toate certificatele'
      }].concat(this.certificate.filter(function (certificat) {
        return certificat.activ !== false;
      }).map(function (certificat) {
        return {
          value: certificat.id,
          text: certificat.cn
        };
      }));
    },

    /** Firmele din listă: cu un certificat ales, doar cele înrolate pe el. */
    firmeDisponibile: function firmeDisponibile() {
      var _this = this;

      if (!this.certificatAles) return this.societati;
      return this.societati.filter(function (societate) {
        return Number(societate.certificat_id) === Number(_this.certificatAles);
      });
    },

    /** Solicitările care trec de filtrele scrise pe coloane. */
    solicitariFiltrate: function solicitariFiltrate() {
      var _this2 = this;

      var contine = function contine(valoare, cautat) {
        return String(valoare || '').toLowerCase().indexOf(cautat.toLowerCase()) !== -1;
      };

      return this.solicitari.filter(function (solicitare) {
        if (_this2.filtre.stare && solicitare.stare !== _this2.filtre.stare) return false;
        return Object.keys(_this2.filtre).filter(function (cheie) {
          return cheie !== 'stare';
        }).every(function (cheie) {
          var cautat = (_this2.filtre[cheie] || '').trim();
          return cautat === '' || contine(solicitare[cheie], cautat);
        });
      });
    },

    /** Câte solicitări încă așteaptă răspuns de la ANAF. */
    inAsteptare: function inAsteptare() {
      return this.solicitari.filter(function (s) {
        return s.stare !== 'preluata';
      }).length;
    },
    raspunsuriAduse: function raspunsuriAduse() {
      if (!this.ultimaPreluareNumar) return 'fără răspunsuri noi';
      return this.ultimaPreluareNumar === 1 ? 'un răspuns' : "".concat(this.ultimaPreluareNumar, " r\u0103spunsuri");
    },
    poateTrimite: function poateTrimite() {
      var _this3 = this;

      if (this.listaCui.length === 0 || !this.tipDocument) return false; // cui_pui e optional; restul parametrilor ceruti de ANAF sunt obligatorii

      return (this.parametriTip[this.tipDocument] || []).filter(function (p) {
        return p !== 'cui_pui';
      }).every(function (p) {
        var valoare = {
          an: _this3.an,
          luna: _this3.luna,
          motiv: _this3.motiv,
          numar_inregistrare: _this3.numarInregistrare
        }[p];
        return valoare !== null && valoare !== '' && valoare !== undefined;
      });
    }
  },
  watch: {
    // Fără imprimare, filigranul n-are pe ce sta: se stinge odată cu ea, ca
    // bifa să nu rămână aprinsă degeaba.
    tiparire: function tiparire(activa) {
      if (!activa) this.filigran = false;
    },
    // Orice schimbare a setării o ține minte și repornește cronometrul.
    'automat.activ': function reporneste() {
      this.salveazaSetarea();
      this.reglaCronometrul();
    },
    'automat.minute': function reporneste() {
      this.salveazaSetarea();
      this.reglaCronometrul();
    },
    // La schimbarea certificatului, firmele alese de pe alt token ies din alegere.
    certificatAles: function certificatAles() {
      var permise = this.firmeDisponibile.map(function (societate) {
        return societate.cif;
      });
      this.firme = this.firme.filter(function (firma) {
        return permise.indexOf(firma.cif) !== -1;
      });
    }
  },
  created: function created() {
    this.incarcaSetarea();
    this.incarcaLista();
    this.incarcaSocietati();
    this.incarcaCertificate();
    this.reglaCronometrul(); // Certificatul activ ales în fila Certificate rămâne alegerea de pornire.

    var activ = Number(window.localStorage.getItem('anaf_certificat_activ'));
    if (activ) this.certificatAles = activ;
  },
  beforeDestroy: function beforeDestroy() {
    // Fără asta, cronometrul ar cere răspunsuri și după plecarea din filă.
    this.opresteCronometrul();
  },
  methods: {
    /**
     * Firmele din Entități înrolate — doar cele active: pentru celelalte, SPV
     * respinge oricum solicitarea.
     */
    incarcaSocietati: function incarcaSocietati() {
      var _this4 = this;

      this.societatiInCurs = true;
      this.$http.get('/anaf-societati', {
        params: {
          doar_active: 1
        }
      }).then(function (_ref) {
        var data = _ref.data;
        _this4.societati = data.data.map(function (societate) {
          return {
            cif: societate.cif,
            certificat_id: societate.certificat_id,
            eticheta: societate.denumire ? "".concat(societate.denumire, " (").concat(societate.cif, ")") : societate.cif
          };
        });
      })["catch"](function (err) {
        _this4.eroare = _this4.mesajEroare(err, 'Entitățile înrolate nu au putut fi încărcate');
      })["finally"](function () {
        _this4.societatiInCurs = false;
      });
    },

    /** Certificatele clientului, pentru lista de tokenuri. */
    incarcaCertificate: function incarcaCertificate() {
      var _this5 = this;

      this.$http.get('/anaf-certificate').then(function (_ref2) {
        var data = _ref2.data;
        _this5.certificate = data.data || [];
      })["catch"](function () {// fara lista, ramane doar alegerea „toate certificatele"
      });
    },
    alegeToate: function alegeToate() {
      this.firme = this.firme.length === this.firmeDisponibile.length ? [] : Object(C_dianasoft_serviciiweb_web_node_modules_babel_runtime_helpers_esm_toConsumableArray_js__WEBPACK_IMPORTED_MODULE_0__["default"])(this.firmeDisponibile);
    },
    clasaObs: function clasaObs(obs) {
      if (!obs) return '';
      return obs.indexOf('ATENȚIE') === 0 ? 'text-danger font-weight-bold' : 'text-muted';
    },
    cere: function cere(parametru) {
      return (this.parametriTip[this.tipDocument] || []).indexOf(parametru) !== -1;
    },

    /*
     * Răspunsul are document de arătat?
     *
     * De acum el stă în arhiva de pe calculatorul clientului (arhiva_cale), nu
     * pe server. Cele preluate înainte sunt încă pe server (cale_fisier), deci
     * se ține seama de amândouă.
     */
    areDocument: function areDocument(solicitare) {
      return Boolean(solicitare.arhiva_cale || solicitare.cale_fisier);
    },
    // Documentele stau pe discul privat, deci se cer prin API (cu token) si se
    // deschid dintr-un blob local, nu printr-un link direct catre /storage.
    deschide: function deschide(solicitare) {
      var _this6 = this;

      this.eroare = '';
      this.$http.get("/spv/solicitari/".concat(solicitare.id, "/fisier"), {
        responseType: 'blob'
      }).then(function (raspuns) {
        var url = window.URL.createObjectURL(new Blob([raspuns.data], {
          type: 'application/pdf'
        }));
        window.open(url, '_blank');
        setTimeout(function () {
          return window.URL.revokeObjectURL(url);
        }, 60000);
      })["catch"](function () {
        _this6.eroare = 'Documentul nu a putut fi deschis.';
      });
    },

    /** Întoarce promisiunea: preluarea are nevoie de lista proaspătă. */
    incarcaLista: function incarcaLista() {
      var _this7 = this;

      this.listaInCurs = true; // Filtrarea se face pe coloanele tabelului, dupa ce lista e adusa intreaga.

      return this.$http.get('/spv/solicitari').then(function (raspuns) {
        _this7.solicitari = raspuns.data.data || [];
        _this7.parametriTip = raspuns.data.tipuri || {};
        _this7.tipuri = Object.keys(_this7.parametriTip).map(function (tip) {
          return {
            value: tip,
            text: tip
          };
        });

        if (!_this7.tipDocument && _this7.tipuri.length) {
          _this7.tipDocument = _this7.tipuri[0].value;
        }
      })["catch"](function (err) {
        _this7.eroare = _this7.mesajEroare(err, 'Nu s-au putut încărca solicitările');
      })["finally"](function () {
        _this7.listaInCurs = false;
      });
    },
    trimite: function trimite() {
      var _this8 = this;

      this.eroare = '';
      this.trimiteInCurs = true;
      this.$http.post('/spv/solicitari', {
        cui: this.listaCui,
        tip_document: this.tipDocument,
        an: this.an || null,
        luna: this.luna || null,
        motiv: this.motiv || null,
        numar_inregistrare: this.numarInregistrare || null,
        cui_pui: this.cuiPui || null
      }).then(function (raspuns) {
        var trimise = (raspuns.data.data || []).length;

        _this8.notifica("".concat(trimise, " solicitare/solicit\u0103ri trimise c\u0103tre SPV"), 'success');

        if (raspuns.data.erori && raspuns.data.erori.length) {
          _this8.eroare = raspuns.data.erori.join(' | ');
        }

        _this8.firme = [];

        _this8.incarcaLista();
      })["catch"](function (err) {
        var date = err.response && err.response.data;
        _this8.eroare = date && date.erori && date.erori.length ? date.erori.join(' | ') : _this8.mesajEroare(err, 'Solicitarea a eșuat');
      })["finally"](function () {
        _this8.trimiteInCurs = false;
      });
    },

    /**
     * @param {boolean} tacut pornit de cronometru: nu deranjează cu mesaje când
     *                        n-a venit nimic nou
     */
    preia: function preia() {
      var _this9 = this;

      var tacut = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : false;
      this.eroare = '';
      this.preiaInCurs = true;
      this.mersul = '';
      this.aduse = 0;
      this.deAdus = 0; // Ce era deja preluat înainte: la sfârșit se tipăresc doar noutățile.

      var inainte = this.solicitari.filter(function (s) {
        return _this9.areDocument(s);
      }).map(function (s) {
        return s.id;
      });
      return this.cereRaspunsurile().then(function (rezultat) {
        _this9.ultimaPreluare = _this9.acum();
        _this9.ultimaPreluareNumar = rezultat.preluate || 0;
        window.localStorage.setItem('solicitari_ultima_preluare', JSON.stringify({
          la: _this9.ultimaPreluare,
          preluate: _this9.ultimaPreluareNumar
        }));

        if (!tacut || rezultat.preluate > 0) {
          _this9.notifica("Verificate: ".concat(rezultat.verificate, ", r\u0103spunsuri noi: ").concat(rezultat.preluate), rezultat.preluate > 0 ? 'success' : 'info');
        }

        if (rezultat.erori && rezultat.erori.length) {
          _this9.eroare = rezultat.erori.join(' | ');
        }

        return _this9.incarcaLista().then(function () {
          if (!_this9.tiparire) return null;

          var noi = _this9.solicitari.filter(function (s) {
            return _this9.areDocument(s) && inainte.indexOf(s.id) === -1;
          }).map(function (s) {
            return s.id;
          });

          return noi.length ? _this9.tipareste(noi) : null;
        });
      })["catch"](function (err) {
        _this9.eroare = _this9.mesajEroare(err, 'Preluarea răspunsurilor a eșuat');
      })["finally"](function () {
        _this9.preiaInCurs = false;
        _this9.mersul = '';
        _this9.aduse = 0;
        _this9.deAdus = 0;
      });
    },

    /**
     * Cere răspunsurile, spunând la fiecare al câtelea e din câte.
     *
     * Răspunsul curge — câte un rând pe măsură ce se lucrează —, așa că omul
     * vede mersul, nu doar o rotiță: fiecare document are pauza cerută de ANAF
     * și drumul până la tokenul clientului, iar din afară lucrul și
     * împotmolirea arată la fel.
     *
     * Se numără cele care chiar au ce aduce, nu toate solicitările în
     * așteptare: „3 din 3" spune adevărul, „3 din 120" ar părea că s-a oprit.
     *
     * @returns {Promise<object>} rezultatul, în forma răspunsului obișnuit
     */
    cereRaspunsurile: function cereRaspunsurile() {
      var _this10 = this;

      // Browserele fără fetch cu flux rămân pe calea dinainte, fără numărătoare.
      if (!Object(_libs_flux__WEBPACK_IMPORTED_MODULE_16__["areFlux"])()) {
        return this.$http.post('/spv/solicitari/preia').then(function (raspuns) {
          return raspuns.data.data;
        });
      }

      var rezultat = {
        verificate: 0,
        preluate: 0,
        ramase: 0,
        erori: []
      };
      return Object(_libs_flux__WEBPACK_IMPORTED_MODULE_16__["default"])('spv/solicitari/preia/flux', function (pas) {
        if (pas.tip === 'inceput') {
          _this10.deAdus = pas.total;
          _this10.aduse = 0;
          if (pas.total) _this10.mersul = "0 din ".concat(pas.total, " r\u0103spunsuri aduse...");
          return;
        }

        if (pas.tip === 'pas') {
          _this10.aduse = pas.facute;
          var care = pas.ce ? " \u2014 ".concat(pas.ce) : '';
          var cazut = pas.reusit ? '' : ' (nu s-a putut aduce)';
          _this10.mersul = "".concat(pas.facute, " din ").concat(pas.total, " r\u0103spunsuri aduse").concat(care).concat(cazut);
          return;
        }

        if (pas.tip === 'gata') rezultat = pas;
      }).then(function () {
        return rezultat;
      });
    },

    /**
     * Trimite documentele la imprimanta utilizatorului.
     *
     */
    tipareste: function tipareste(ids) {
      var _this11 = this;

      return this.$http.post('/spv/solicitari/tipareste', {
        id: ids,
        filigran: this.filigran
      }).then(function (_ref3) {
        var data = _ref3.data;

        _this11.notifica("".concat(data.data.documente, " documente trimise la \u201E").concat(data.data.imprimanta, "\u201D"), 'success');
      })["catch"](function (err) {
        var motiv = _this11.mesajEroare(err, 'programul local nu a răspuns.');

        _this11.eroare = "R\u0103spunsurile au fost preluate, dar tip\u0103rirea nu a reu\u0219it: ".concat(motiv);
      });
    },

    /** Momentul de acum, scris ca peste tot în modul: zz.ll.aaaa hh:mm:ss. */
    acum: function acum() {
      var d = new Date();

      var doua = function doua(n) {
        return String(n).padStart(2, '0');
      };

      return "".concat(doua(d.getDate()), ".").concat(doua(d.getMonth() + 1), ".").concat(d.getFullYear(), " ") + "".concat(doua(d.getHours()), ":").concat(doua(d.getMinutes()), ":").concat(doua(d.getSeconds()));
    },
    incarcaSetarea: function incarcaSetarea() {
      try {
        var salvat = JSON.parse(window.localStorage.getItem('solicitari_preluare_automat'));

        if (salvat && typeof salvat.minute === 'number' && salvat.minute > 0) {
          var permise = this.optiuniMinute.map(function (o) {
            return o.value;
          });
          var minute = permise.indexOf(salvat.minute) !== -1 ? salvat.minute : permise.reduce(function (a, b) {
            return Math.abs(b - salvat.minute) < Math.abs(a - salvat.minute) ? b : a;
          });
          this.automat = {
            activ: !!salvat.activ,
            minute: minute
          };
        }

        var ultima = JSON.parse(window.localStorage.getItem('solicitari_ultima_preluare'));

        if (ultima) {
          this.ultimaPreluare = ultima.la || '';
          this.ultimaPreluareNumar = ultima.preluate || 0;
        }
      } catch (e) {// setare veche sau stricată — se rămâne pe valorile implicite
      }
    },
    salveazaSetarea: function salveazaSetarea() {
      window.localStorage.setItem('solicitari_preluare_automat', JSON.stringify(this.automat));
    },
    opresteCronometrul: function opresteCronometrul() {
      if (this.cronometru) {
        window.clearInterval(this.cronometru);
        this.cronometru = null;
      }
    },
    reglaCronometrul: function reglaCronometrul() {
      var _this12 = this;

      this.opresteCronometrul();
      var minute = Number(this.automat.minute);

      if (!this.automat.activ || !minute || minute < 1) {
        return;
      }

      this.cronometru = window.setInterval(function () {
        // Se întreabă ANAF-ul doar când chiar e ceva de așteptat, și nu peste o
        // preluare pornită de mână sau peste ea însăși.
        if (!_this12.preiaInCurs && _this12.inAsteptare) {
          _this12.preia(true);
        }
      }, minute * 60 * 1000);
    },
    sterge: function sterge(solicitare) {
      var _this13 = this;

      this.$http["delete"]("/spv/solicitari/".concat(solicitare.id)).then(function () {
        _this13.notifica('Solicitarea a fost ștearsă', 'success');

        _this13.incarcaLista();
      })["catch"](function (err) {
        _this13.eroare = _this13.mesajEroare(err, 'Ștergerea a eșuat');
      });
    },
    mesajEroare: function mesajEroare(err, implicit) {
      return err.response && err.response.data && err.response.data.message ? err.response.data.message : implicit;
    },
    notifica: function notifica(mesaj, variant) {
      this.$bvToast.toast(mesaj, {
        title: 'Solicitări SPV',
        variant: variant,
        solid: true
      });
    }
  }
});

/***/ }),

/***/ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/app_pages/spv/Utilizatori.vue?vue&type=script&lang=js&":
/*!***********************************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib??ref--4-0!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/src/views/app_pages/spv/Utilizatori.vue?vue&type=script&lang=js& ***!
  \***********************************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var C_dianasoft_serviciiweb_web_node_modules_babel_runtime_helpers_esm_objectSpread2_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./node_modules/@babel/runtime/helpers/esm/objectSpread2.js */ "./node_modules/@babel/runtime/helpers/esm/objectSpread2.js");
/* harmony import */ var core_js_modules_es_string_trim_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! core-js/modules/es.string.trim.js */ "./node_modules/core-js/modules/es.string.trim.js");
/* harmony import */ var core_js_modules_es_string_trim_js__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_es_string_trim_js__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var core_js_modules_es_array_filter_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! core-js/modules/es.array.filter.js */ "./node_modules/core-js/modules/es.array.filter.js");
/* harmony import */ var core_js_modules_es_array_filter_js__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_es_array_filter_js__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var core_js_modules_es_object_to_string_js__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! core-js/modules/es.object.to-string.js */ "./node_modules/core-js/modules/es.object.to-string.js");
/* harmony import */ var core_js_modules_es_object_to_string_js__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_es_object_to_string_js__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var core_js_modules_es_array_concat_js__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! core-js/modules/es.array.concat.js */ "./node_modules/core-js/modules/es.array.concat.js");
/* harmony import */ var core_js_modules_es_array_concat_js__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_es_array_concat_js__WEBPACK_IMPORTED_MODULE_4__);
/* harmony import */ var core_js_modules_es_array_map_js__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! core-js/modules/es.array.map.js */ "./node_modules/core-js/modules/es.array.map.js");
/* harmony import */ var core_js_modules_es_array_map_js__WEBPACK_IMPORTED_MODULE_5___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_es_array_map_js__WEBPACK_IMPORTED_MODULE_5__);
/* harmony import */ var core_js_modules_es_array_includes_js__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! core-js/modules/es.array.includes.js */ "./node_modules/core-js/modules/es.array.includes.js");
/* harmony import */ var core_js_modules_es_array_includes_js__WEBPACK_IMPORTED_MODULE_6___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_es_array_includes_js__WEBPACK_IMPORTED_MODULE_6__);
/* harmony import */ var core_js_modules_es_string_includes_js__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! core-js/modules/es.string.includes.js */ "./node_modules/core-js/modules/es.string.includes.js");
/* harmony import */ var core_js_modules_es_string_includes_js__WEBPACK_IMPORTED_MODULE_7___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_es_string_includes_js__WEBPACK_IMPORTED_MODULE_7__);








//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//

/*
 * Utilizatorii din firma clientului, gestionați de administratorul firmei.
 * Serverul refuză rutele pentru ceilalți, dar fila nici nu li se arată.
 */
/* harmony default export */ __webpack_exports__["default"] = ({
  name: 'Utilizatori',
  data: function data() {
    return {
      utilizatori: [],
      certificate: [],
      cautare: '',
      ipCurent: '',
      listaInCurs: false,
      eroare: '',
      eroareFormular: '',
      formularVizibil: false,
      formular: {},
      imprimante: [],
      imprimanteInCurs: false,
      eroareImprimante: '',
      campuri: [{
        key: 'persoana',
        label: 'Utilizator'
      }, {
        key: 'certificate',
        label: 'Certificate digitale'
      }, {
        key: 'imprimanta',
        label: 'Imprimantă'
      }, {
        key: 'ip_permise',
        label: 'IP permise'
      }, {
        key: 'stare',
        label: 'Stare'
      }, {
        key: 'actiuni',
        label: 'Acțiuni'
      }]
    };
  },
  computed: {
    /** Utilizatorii care se potrivesc cu ce s-a scris in cautare. */
    utilizatoriFiltrati: function utilizatoriFiltrati() {
      var cautat = this.cautare.trim().toLowerCase();
      if (!cautat) return this.utilizatori;
      return this.utilizatori.filter(function (utilizator) {
        return [utilizator.nume, utilizator.email, utilizator.telefon].some(function (valoare) {
          return String(valoare || '').toLowerCase().indexOf(cautat) !== -1;
        });
      });
    },

    /** Calculatoarele sunt cele ale certificatelor bifate pentru acest om. */
    optiuniCalculatoare: function optiuniCalculatoare() {
      var alese = this.formular.certificate || [];
      return [{
        value: null,
        text: 'fără imprimantă'
      }].concat(this.certificate.filter(function (certificat) {
        return alese.includes(certificat.id);
      }).map(function (certificat) {
        return {
          value: certificat.id,
          text: certificat.cn
        };
      }));
    },
    optiuniImprimante: function optiuniImprimante() {
      if (this.imprimanteInCurs) return [{
        value: null,
        text: 'se citesc...'
      }];
      return [{
        value: null,
        text: 'alegeți imprimanta'
      }].concat(this.imprimante.map(function (imprimanta) {
        return {
          value: imprimanta.nume,
          text: imprimanta.nume + (imprimanta.implicita ? ' (implicită pe acel calculator)' : ''),
          disabled: imprimanta.stare === 'Offline'
        };
      }));
    }
  },
  created: function created() {
    var _this = this;

    this.incarca(); // Adresa vazuta de server: ajuta la scrierea listei si arata din prima
    // daca in fata aplicatiei sta un proxy care ascunde adresa adevarata.

    this.$http.get('/context').then(function (_ref) {
      var data = _ref.data;
      _this.ipCurent = data.data.ip_curent || '';
    })["catch"](function () {
      _this.ipCurent = '';
    });
  },
  methods: {
    incarca: function incarca() {
      var _this2 = this;

      this.listaInCurs = true;
      this.eroare = '';
      this.$http.get('/client/utilizatori').then(function (_ref2) {
        var data = _ref2.data;
        _this2.utilizatori = data.data;
        _this2.certificate = data.certificate;
      })["catch"](function (err) {
        _this2.eroare = _this2.mesajEroare(err, 'Utilizatorii nu au putut fi încărcați');
      })["finally"](function () {
        _this2.listaInCurs = false;
      });
    },

    /** Pune adresa de acum in lista, fara sa stearga ce era scris. */
    adaugaIpCurent: function adaugaIpCurent() {
      var scris = (this.formular.ip_permise || '').trim();
      if (scris.indexOf(this.ipCurent) !== -1) return;
      this.$set(this.formular, 'ip_permise', scris ? "".concat(scris, ", ").concat(this.ipCurent) : this.ipCurent);
    },
    deschide: function deschide(utilizator) {
      this.eroareFormular = '';
      this.eroareImprimante = '';
      this.imprimante = [];
      this.formular = utilizator ? Object(C_dianasoft_serviciiweb_web_node_modules_babel_runtime_helpers_esm_objectSpread2_js__WEBPACK_IMPORTED_MODULE_0__["default"])(Object(C_dianasoft_serviciiweb_web_node_modules_babel_runtime_helpers_esm_objectSpread2_js__WEBPACK_IMPORTED_MODULE_0__["default"])({}, utilizator), {}, {
        parola: '',
        certificate: utilizator.certificate.map(function (c) {
          return c.id;
        })
      }) : {
        nume: '',
        email: '',
        telefon: '',
        parola: '',
        administrator: false,
        poate_semna: false,
        poate_depune: false,
        blocat: false,
        certificate: [],
        imprimanta: null,
        imprimanta_certificat_id: null,
        ip_permise: ''
      };
      this.formularVizibil = true;
      if (this.formular.imprimanta_certificat_id) this.incarcaImprimante();
    },

    /**
     * Imprimantele vin de la calculatorul certificatului ales — trebuie să fie
     * pornit, altfel n-avem de unde ști ce imprimante are.
     */
    incarcaImprimante: function incarcaImprimante() {
      var _this3 = this;

      var certificat = this.formular.imprimanta_certificat_id;
      this.imprimante = [];
      this.eroareImprimante = '';

      if (!certificat) {
        this.formular.imprimanta = null;
        return;
      }

      this.imprimanteInCurs = true;
      this.$http.get("/anaf-certificate/".concat(certificat, "/imprimante")).then(function (_ref3) {
        var data = _ref3.data;
        _this3.imprimante = data.data;
      })["catch"](function (err) {
        _this3.eroareImprimante = _this3.mesajEroare(err, 'Imprimantele nu au putut fi citite');
      })["finally"](function () {
        _this3.imprimanteInCurs = false;
      });
    },
    salveaza: function salveaza() {
      var _this4 = this;

      this.eroareFormular = ''; // Câmpul de parolă lăsat gol nu pleacă deloc: la modificare înseamnă
      // „parola rămâne cum era", nu „pune asta în loc".

      var trimise = Object(C_dianasoft_serviciiweb_web_node_modules_babel_runtime_helpers_esm_objectSpread2_js__WEBPACK_IMPORTED_MODULE_0__["default"])({}, this.formular);

      if (!trimise.parola || !trimise.parola.trim()) delete trimise.parola;
      var cerere = this.formular.id ? this.$http.put("/client/utilizatori/".concat(this.formular.id), trimise) : this.$http.post('/client/utilizatori', trimise);
      cerere.then(function () {
        _this4.formularVizibil = false;

        _this4.incarca();
      })["catch"](function (err) {
        _this4.eroareFormular = _this4.mesajEroare(err, 'Utilizatorul nu a putut fi salvat');
      });
    },
    deconecteaza: function deconecteaza(utilizator) {
      var _this5 = this;

      this.$bvModal.msgBoxConfirm("\xCEnchide\u021Bi acum sesiunile lui ".concat(utilizator.email, "?"), {
        title: 'Deconectare',
        okTitle: 'Deconectează',
        cancelTitle: 'Renunță',
        okVariant: 'warning'
      }).then(function (confirmat) {
        if (!confirmat) return;

        _this5.$http.post("/client/utilizatori/".concat(utilizator.id, "/deconectare")).then(function (_ref4) {
          var data = _ref4.data;

          _this5.$bvToast.toast("".concat(data.data.sesiuni, " sesiuni \xEEnchise."), {
            title: 'Deconectat',
            variant: 'success'
          });
        })["catch"](function (err) {
          _this5.eroare = _this5.mesajEroare(err, 'Deconectarea a eșuat');
        });
      });
    },
    mesajEroare: function mesajEroare(err, implicit) {
      return err.response && err.response.data && err.response.data.message ? err.response.data.message : implicit;
    }
  }
});

/***/ }),

/***/ "./node_modules/css-loader/dist/cjs.js!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src/index.js?!./node_modules/sass-loader/dist/cjs.js?!./node_modules/sass-loader/dist/cjs.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/app_pages/Spv.vue?vue&type=style&index=0&id=50cb4596&lang=scss&scoped=true&":
/*!************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/css-loader/dist/cjs.js!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src??ref--7-2!./node_modules/sass-loader/dist/cjs.js??ref--7-3!./node_modules/sass-loader/dist/cjs.js??ref--11-0!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/src/views/app_pages/Spv.vue?vue&type=style&index=0&id=50cb4596&lang=scss&scoped=true& ***!
  \************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

// Imports
var ___CSS_LOADER_API_IMPORT___ = __webpack_require__(/*! ../../../../../node_modules/css-loader/dist/runtime/api.js */ "./node_modules/css-loader/dist/runtime/api.js");
exports = ___CSS_LOADER_API_IMPORT___(false);
// Module
exports.push([module.i, "/*\n * Culoarea institutionala din sigla (#22406f) se vede si pe filele modulului:\n * fila deschisa poarta albastrul SPV Curier, nu movul temei.\n */\n[data-v-50cb4596] .nav-tabs .nav-link.active {\n  color: #22406f;\n}\n[dir=ltr][data-v-50cb4596]  .nav-tabs .nav-link.active:after {\n  background: linear-gradient(30deg, #22406f, rgba(34, 64, 111, 0.5)) !important;\n}\n[dir=rtl][data-v-50cb4596]  .nav-tabs .nav-link.active:after {\n  background: linear-gradient(-30deg, #22406f, rgba(34, 64, 111, 0.5)) !important;\n}\n[data-v-50cb4596] .nav-tabs .nav-link:hover {\n  color: #22406f;\n}", ""]);
// Exports
module.exports = exports;


/***/ }),

/***/ "./node_modules/css-loader/dist/cjs.js!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src/index.js?!./node_modules/sass-loader/dist/cjs.js?!./node_modules/sass-loader/dist/cjs.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/app_pages/Spv.vue?vue&type=style&index=1&lang=scss&":
/*!************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/css-loader/dist/cjs.js!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src??ref--7-2!./node_modules/sass-loader/dist/cjs.js??ref--7-3!./node_modules/sass-loader/dist/cjs.js??ref--11-0!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/src/views/app_pages/Spv.vue?vue&type=style&index=1&lang=scss& ***!
  \************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

// Imports
var ___CSS_LOADER_API_IMPORT___ = __webpack_require__(/*! ../../../../../node_modules/css-loader/dist/runtime/api.js */ "./node_modules/css-loader/dist/runtime/api.js");
exports = ___CSS_LOADER_API_IMPORT___(false);
// Module
exports.push([module.i, "@charset \"UTF-8\";\n/*\n * In modulul SPV Curier, varianta \"info\" (albastrul deschis al temei) poarta\n * albastrul institutional din sigla, #22406f — butoane, ecusoane, instiintari,\n * peste tot. Blocul nu e \"scoped\": paginile filelor sunt componente separate,\n * iar regula trebuie sa le prinda pe toate; de aceea sta sub .modul-spv, ca\n * restul aplicatiei sa ramana cum e.\n */\n.modul-spv {\n  /*\n   * Si varianta \"primary\" (movul temei) poarta aici albastrul din sigla:\n   * butoane, ecusoane, paginare, bife si campuri in focus. Tot sub .modul-spv,\n   * ca celelalte module sa ramana pe culorile temei.\n   */\n}\n[dir] .modul-spv .btn-info {\n  background-color: #22406f !important;\n  border-color: #22406f !important;\n}\n[dir] .modul-spv .btn-info:hover, [dir] .modul-spv .btn-info:focus, [dir] .modul-spv .btn-info:active {\n  background-color: #1c355b !important;\n  border-color: #1c355b !important;\n  box-shadow: 0 8px 25px -8px rgba(34, 64, 111, 0.6) !important;\n}\n.modul-spv .btn-outline-info {\n  color: #22406f !important;\n}\n[dir] .modul-spv .btn-outline-info {\n  border-color: #22406f !important;\n}\n.modul-spv .btn-outline-info:hover:not(:disabled) {\n  color: #22406f !important;\n}\n[dir] .modul-spv .btn-outline-info:hover:not(:disabled) {\n  background-color: rgba(34, 64, 111, 0.06) !important;\n}\n.modul-spv .btn-outline-info:not(:disabled):active, .modul-spv .btn-outline-info.active {\n  color: #fff !important;\n}\n[dir] .modul-spv .btn-outline-info:not(:disabled):active, [dir] .modul-spv .btn-outline-info.active {\n  background-color: #22406f !important;\n}\n.modul-spv .btn-flat-info {\n  color: #22406f !important;\n}\n.modul-spv .btn-flat-info:hover, .modul-spv .btn-flat-info:active, .modul-spv .btn-flat-info:focus {\n  color: #22406f !important;\n}\n[dir] .modul-spv .btn-flat-info:hover, [dir] .modul-spv .btn-flat-info:active, [dir] .modul-spv .btn-flat-info:focus {\n  background-color: rgba(34, 64, 111, 0.12) !important;\n}\n[dir] .modul-spv .badge-info {\n  background-color: #22406f !important;\n}\n.modul-spv .badge-light-info {\n  color: #22406f !important;\n}\n[dir] .modul-spv .badge-light-info {\n  background-color: rgba(34, 64, 111, 0.12) !important;\n}\n.modul-spv .alert-info {\n  color: #22406f !important;\n}\n[dir] .modul-spv .alert-info {\n  background: rgba(34, 64, 111, 0.12) !important;\n}\n.modul-spv .alert-info .alert-heading,\n.modul-spv .alert-info .alert-body,\n.modul-spv .alert-info .alert-link {\n  color: #22406f !important;\n}\n.modul-spv .text-info {\n  color: #22406f !important;\n}\n[dir] .modul-spv .border-info {\n  border-color: #22406f !important;\n}\n.modul-spv .spinner-border.text-info,\n.modul-spv .spinner-grow.text-info {\n  color: #22406f !important;\n}\n[dir] .modul-spv .btn-primary {\n  background-color: #22406f !important;\n  border-color: #22406f !important;\n}\n[dir] .modul-spv .btn-primary:hover, [dir] .modul-spv .btn-primary:focus, [dir] .modul-spv .btn-primary:active {\n  background-color: #1c355b !important;\n  border-color: #1c355b !important;\n  box-shadow: 0 8px 25px -8px rgba(34, 64, 111, 0.6) !important;\n}\n.modul-spv .btn-outline-primary {\n  color: #22406f !important;\n}\n[dir] .modul-spv .btn-outline-primary {\n  border-color: #22406f !important;\n}\n.modul-spv .btn-outline-primary:hover:not(:disabled) {\n  color: #22406f !important;\n}\n[dir] .modul-spv .btn-outline-primary:hover:not(:disabled) {\n  background-color: rgba(34, 64, 111, 0.06) !important;\n}\n.modul-spv .btn-outline-primary:not(:disabled):active, .modul-spv .btn-outline-primary.active {\n  color: #fff !important;\n}\n[dir] .modul-spv .btn-outline-primary:not(:disabled):active, [dir] .modul-spv .btn-outline-primary.active {\n  background-color: #22406f !important;\n}\n.modul-spv .btn-flat-primary {\n  color: #22406f !important;\n}\n.modul-spv .btn-flat-primary:hover, .modul-spv .btn-flat-primary:active, .modul-spv .btn-flat-primary:focus {\n  color: #22406f !important;\n}\n[dir] .modul-spv .btn-flat-primary:hover, [dir] .modul-spv .btn-flat-primary:active, [dir] .modul-spv .btn-flat-primary:focus {\n  background-color: rgba(34, 64, 111, 0.12) !important;\n}\n[dir] .modul-spv .badge-primary {\n  background-color: #22406f !important;\n}\n.modul-spv .badge-light-primary {\n  color: #22406f !important;\n}\n[dir] .modul-spv .badge-light-primary {\n  background-color: rgba(34, 64, 111, 0.12) !important;\n}\n.modul-spv .alert-primary {\n  color: #22406f !important;\n}\n[dir] .modul-spv .alert-primary {\n  background: rgba(34, 64, 111, 0.12) !important;\n}\n.modul-spv .alert-primary .alert-heading,\n.modul-spv .alert-primary .alert-body,\n.modul-spv .alert-primary .alert-link {\n  color: #22406f !important;\n}\n.modul-spv .text-primary {\n  color: #22406f !important;\n}\n[dir] .modul-spv .border-primary {\n  border-color: #22406f !important;\n}\n.modul-spv .spinner-border.text-primary,\n.modul-spv .spinner-grow.text-primary {\n  color: #22406f !important;\n}\n.modul-spv .pagination .page-item.active .page-link {\n  color: #fff !important;\n}\n[dir] .modul-spv .pagination .page-item.active .page-link {\n  background-color: #22406f !important;\n  border-color: #22406f !important;\n}\n.modul-spv .pagination .page-link:hover {\n  color: #22406f;\n}\n[dir] .modul-spv .custom-control-input:checked ~ .custom-control-label::before {\n  background-color: #22406f !important;\n  border-color: #22406f !important;\n}\n[dir] .modul-spv .form-control:focus, [dir] .modul-spv .custom-select:focus {\n  border-color: #22406f !important;\n}\n.modul-spv .dropdown-item.active,\n.modul-spv .dropdown-item:active {\n  color: #22406f !important;\n}\n[dir] .modul-spv .dropdown-item.active, [dir] .modul-spv .dropdown-item:active {\n  background-color: rgba(34, 64, 111, 0.12) !important;\n}", ""]);
// Exports
module.exports = exports;


/***/ }),

/***/ "./node_modules/css-loader/dist/cjs.js!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src/index.js?!./node_modules/sass-loader/dist/cjs.js?!./node_modules/sass-loader/dist/cjs.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/app_pages/spv/Mesaje.vue?vue&type=style&index=0&lang=scss&":
/*!*******************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/css-loader/dist/cjs.js!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src??ref--7-2!./node_modules/sass-loader/dist/cjs.js??ref--7-3!./node_modules/sass-loader/dist/cjs.js??ref--11-0!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/src/views/app_pages/spv/Mesaje.vue?vue&type=style&index=0&lang=scss& ***!
  \*******************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

// Imports
var ___CSS_LOADER_API_IMPORT___ = __webpack_require__(/*! ../../../../../../node_modules/css-loader/dist/runtime/api.js */ "./node_modules/css-loader/dist/runtime/api.js");
exports = ___CSS_LOADER_API_IMPORT___(false);
// Module
exports.push([module.i, ".v-select {\n  position: relative;\n  font-family: inherit;\n}\n.v-select,\n.v-select * {\n  box-sizing: border-box;\n}\n\n/* KeyFrames */\n@-webkit-keyframes vSelectSpinner-ltr {\n0% {\n    transform: rotate(0deg);\n}\n100% {\n    transform: rotate(360deg);\n}\n}\n@-webkit-keyframes vSelectSpinner-rtl {\n0% {\n    transform: rotate(0deg);\n}\n100% {\n    transform: rotate(-360deg);\n}\n}\n@keyframes vSelectSpinner-ltr {\n0% {\n    transform: rotate(0deg);\n}\n100% {\n    transform: rotate(360deg);\n}\n}\n@keyframes vSelectSpinner-rtl {\n0% {\n    transform: rotate(0deg);\n}\n100% {\n    transform: rotate(-360deg);\n}\n}\n/* Dropdown Default Transition */\n.vs__fade-enter-active,\n.vs__fade-leave-active {\n  pointer-events: none;\n  transition: opacity 0.15s cubic-bezier(1, 0.5, 0.8, 1);\n}\n.vs__fade-enter,\n.vs__fade-leave-to {\n  opacity: 0;\n}\n\n/** Component States */\n/*\n * Disabled\n *\n * When the component is disabled, all interaction\n * should be prevented. Here we modify the bg color,\n * and change the cursor displayed on the interactive\n * components.\n */\n[dir] .vs--disabled .vs__dropdown-toggle, [dir] .vs--disabled .vs__clear, [dir] .vs--disabled .vs__search, [dir] .vs--disabled .vs__selected, [dir] .vs--disabled .vs__open-indicator {\n  cursor: not-allowed;\n  background-color: #f8f8f8;\n}\n\n/*\n *  RTL - Right to Left Support\n *\n *  Because we're using a flexbox layout, the `dir=\"rtl\"`\n *  HTML attribute does most of the work for us by\n *  rearranging the child elements visually.\n */\n.v-select[dir=rtl] .vs__actions {\n  padding: 0 3px 0 6px;\n}\n.v-select[dir=rtl] .vs__clear {\n  margin-left: 6px;\n  margin-right: 0;\n}\n.v-select[dir=rtl] .vs__deselect {\n  margin-left: 0;\n  margin-right: 2px;\n}\n.v-select[dir=rtl] .vs__dropdown-menu {\n  text-align: right;\n}\n\n/**\n    Dropdown Toggle\n\n    The dropdown toggle is the primary wrapper of the component. It\n    has two direct descendants: .vs__selected-options, and .vs__actions.\n\n    .vs__selected-options holds the .vs__selected's as well as the\n    main search input.\n\n    .vs__actions holds the clear button and dropdown toggle.\n */\n.vs__dropdown-toggle {\n  appearance: none;\n  display: flex;\n  white-space: normal;\n}\n[dir] .vs__dropdown-toggle {\n  padding: 0 0 4px 0;\n  background: none;\n  border: 1px solid #d8d6de;\n  border-radius: 0.357rem;\n}\n.vs__selected-options {\n  display: flex;\n  flex-basis: 100%;\n  flex-grow: 1;\n  flex-wrap: wrap;\n  position: relative;\n}\n[dir] .vs__selected-options {\n  padding: 0 2px;\n}\n.vs__actions {\n  display: flex;\n  align-items: center;\n}\n[dir=ltr] .vs__actions {\n  padding: 4px 6px 0 3px;\n}\n[dir=rtl] .vs__actions {\n  padding: 4px 3px 0 6px;\n}\n\n/* Dropdown Toggle States */\n[dir] .vs--searchable .vs__dropdown-toggle {\n  cursor: text;\n}\n[dir] .vs--unsearchable .vs__dropdown-toggle {\n  cursor: pointer;\n}\n[dir] .vs--open .vs__dropdown-toggle {\n  border-bottom-color: transparent;\n}\n[dir=ltr] .vs--open .vs__dropdown-toggle {\n  border-bottom-left-radius: 0;\n  border-bottom-right-radius: 0;\n}\n[dir=rtl] .vs--open .vs__dropdown-toggle {\n  border-bottom-right-radius: 0;\n  border-bottom-left-radius: 0;\n}\n.vs__open-indicator {\n  fill: rgba(60, 60, 60, 0.5);\n  transition: transform 150ms cubic-bezier(1, -0.115, 0.975, 0.855);\n}\n[dir] .vs__open-indicator {\n  transform: scale(1);\n  transition-timing-function: cubic-bezier(1, -0.115, 0.975, 0.855);\n}\n[dir=ltr] .vs--open .vs__open-indicator {\n  transform: rotate(180deg) scale(1);\n}\n[dir=rtl] .vs--open .vs__open-indicator {\n  transform: rotate(-180deg) scale(1);\n}\n.vs--loading .vs__open-indicator {\n  opacity: 0;\n}\n\n/* Clear Button */\n.vs__clear {\n  fill: rgba(60, 60, 60, 0.5);\n}\n[dir] .vs__clear {\n  padding: 0;\n  border: 0;\n  background-color: transparent;\n  cursor: pointer;\n}\n[dir=ltr] .vs__clear {\n  margin-right: 8px;\n}\n[dir=rtl] .vs__clear {\n  margin-left: 8px;\n}\n\n/* Dropdown Menu */\n.vs__dropdown-menu {\n  display: block;\n  box-sizing: border-box;\n  position: absolute;\n  top: calc(100% - 1px);\n  z-index: 1000;\n  width: 100%;\n  max-height: 350px;\n  min-width: 160px;\n  overflow-y: auto;\n  list-style: none;\n}\n[dir] .vs__dropdown-menu {\n  padding: 5px 0;\n  margin: 0;\n  box-shadow: 0px 4px 25px 0px rgba(0, 0, 0, 0.1);\n  border: 1px solid #d8d6de;\n  border-top-style: none;\n  border-radius: 0 0 0.357rem 0.357rem;\n  background: #fff;\n}\n[dir=ltr] .vs__dropdown-menu {\n  left: 0;\n  text-align: left;\n}\n[dir=rtl] .vs__dropdown-menu {\n  right: 0;\n  text-align: right;\n}\n[dir] .vs__no-options {\n  text-align: center;\n}\n\n/* List Items */\n.vs__dropdown-option {\n  line-height: 1.42857143;\n  /* Normalize line height */\n  display: block;\n  color: #333;\n  /* Overrides most CSS frameworks */\n  white-space: nowrap;\n}\n[dir] .vs__dropdown-option {\n  padding: 3px 20px;\n  clear: both;\n  cursor: pointer;\n}\n.vs__dropdown-option--highlight {\n  color: #7367f0 !important;\n}\n[dir] .vs__dropdown-option--highlight {\n  background: rgba(115, 103, 240, 0.12);\n}\n.vs__dropdown-option--deselect {\n  color: #fff;\n}\n[dir] .vs__dropdown-option--deselect {\n  background: #fb5858;\n}\n.vs__dropdown-option--disabled {\n  color: rgba(60, 60, 60, 0.5);\n}\n[dir] .vs__dropdown-option--disabled {\n  background: inherit;\n  cursor: inherit;\n}\n\n/* Selected Tags */\n.vs__selected {\n  display: flex;\n  align-items: center;\n  color: #333;\n  line-height: 1.8;\n  z-index: 0;\n}\n[dir] .vs__selected {\n  background-color: #7367f0;\n  border: 0 solid rgba(60, 60, 60, 0.26);\n  border-radius: 0.357rem;\n  margin: 4px 2px 0px 2px;\n  padding: 0 0.25em;\n}\n.vs__deselect {\n  display: inline-flex;\n  appearance: none;\n  fill: rgba(60, 60, 60, 0.5);\n}\n[dir] .vs__deselect {\n  padding: 0;\n  border: 0;\n  cursor: pointer;\n  background: none;\n  text-shadow: 0 1px 0 #fff;\n}\n[dir=ltr] .vs__deselect {\n  margin-left: 4px;\n}\n[dir=rtl] .vs__deselect {\n  margin-right: 4px;\n}\n\n/* States */\n[dir] .vs--single .vs__selected {\n  background-color: transparent;\n  border-color: transparent;\n}\n.vs--single.vs--open .vs__selected, .vs--single.vs--loading .vs__selected {\n  position: absolute;\n  opacity: 0.4;\n}\n.vs--single.vs--searching .vs__selected {\n  display: none;\n}\n\n/* Search Input */\n/**\n * Super weird bug... If this declaration is grouped\n * below, the cancel button will still appear in chrome.\n * If it's up here on it's own, it'll hide it.\n */\n.vs__search::-webkit-search-cancel-button {\n  display: none;\n}\n.vs__search::-webkit-search-decoration,\n.vs__search::-webkit-search-results-button,\n.vs__search::-webkit-search-results-decoration,\n.vs__search::-ms-clear {\n  display: none;\n}\n.vs__search,\n.vs__search:focus {\n  appearance: none;\n  line-height: 1.8;\n  font-size: 1em;\n  outline: none;\n  width: 0;\n  max-width: 100%;\n  flex-grow: 1;\n  z-index: 1;\n}\n[dir] .vs__search, [dir] .vs__search:focus {\n  border: 1px solid transparent;\n  margin: 4px 0 0 0;\n  padding: 0 7px;\n  background: none;\n  box-shadow: none;\n}\n[dir=ltr] .vs__search, [dir=ltr] .vs__search:focus {\n  border-left: none;\n}\n[dir=rtl] .vs__search, [dir=rtl] .vs__search:focus {\n  border-right: none;\n}\n.vs__search::placeholder {\n  color: #6e6b7b;\n}\n\n/**\n    States\n */\n.vs--unsearchable .vs__search {\n  opacity: 1;\n}\n[dir] .vs--unsearchable:not(.vs--disabled) .vs__search {\n  cursor: pointer;\n}\n.vs--single.vs--searching:not(.vs--open):not(.vs--loading) .vs__search {\n  opacity: 0.2;\n}\n\n/* Loading Spinner */\n.vs__spinner {\n  align-self: center;\n  opacity: 0;\n  font-size: 5px;\n  text-indent: -9999em;\n  overflow: hidden;\n  transition: opacity 0.1s;\n}\n[dir] .vs__spinner {\n  border-top: 0.9em solid rgba(100, 100, 100, 0.1);\n  border-bottom: 0.9em solid rgba(100, 100, 100, 0.1);\n  transform: translateZ(0);\n}\n[dir=ltr] .vs__spinner {\n  border-right: 0.9em solid rgba(100, 100, 100, 0.1);\n  border-left: 0.9em solid rgba(60, 60, 60, 0.45);\n  animation:  vSelectSpinner-ltr 1.1s infinite linear;\n}\n[dir=rtl] .vs__spinner {\n  border-left: 0.9em solid rgba(100, 100, 100, 0.1);\n  border-right: 0.9em solid rgba(60, 60, 60, 0.45);\n  animation:  vSelectSpinner-rtl 1.1s infinite linear;\n}\n.vs__spinner,\n.vs__spinner:after {\n  width: 5em;\n  height: 5em;\n}\n[dir] .vs__spinner, [dir] .vs__spinner:after {\n  border-radius: 50%;\n}\n\n/* Loading Spinner States */\n.vs--loading .vs__spinner {\n  opacity: 1;\n}\n.vs__open-indicator {\n  fill: none;\n}\n[dir] .vs__open-indicator {\n  margin-top: 0.15rem;\n}\n.vs__dropdown-toggle {\n  transition: all 0.25s ease-in-out;\n}\n[dir] .vs__dropdown-toggle {\n  padding: 0.59px 0 4px 0;\n}\n[dir=ltr] .vs--single .vs__dropdown-toggle {\n  padding-left: 6px;\n}\n[dir=rtl] .vs--single .vs__dropdown-toggle {\n  padding-right: 6px;\n}\n.vs__dropdown-option--disabled {\n  opacity: 0.5;\n}\n[dir] .vs__dropdown-option--disabled.vs__dropdown-option--selected {\n  background: #7367f0 !important;\n}\n.vs__dropdown-option {\n  color: #6e6b7b;\n}\n[dir] .vs__dropdown-option, [dir] .vs__no-options {\n  padding: 7px 20px;\n}\n.vs__dropdown-option--selected {\n  background-color: #7367f0;\n  color: #fff;\n  position: relative;\n}\n.vs__dropdown-option--selected::after {\n  content: \"\";\n  height: 1.1rem;\n  width: 1.1rem;\n  display: inline-block;\n  position: absolute;\n  top: 50%;\n  transform: translateY(-50%);\n  right: 20px;\n  background-image: url(\"data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='24' height='24' viewBox='0 0 24 24' fill='none' stroke='%23fff' stroke-width='2' stroke-linecap='round' stroke-linejoin='round' class='feather feather-check'%3E%3Cpolyline points='20 6 9 17 4 12'%3E%3C/polyline%3E%3C/svg%3E\");\n  background-repeat: no-repeat;\n  background-position: center;\n  background-size: 1.1rem;\n}\n[dir=rtl] .vs__dropdown-option--selected::after {\n  left: 20px;\n  right: unset;\n}\n.vs__dropdown-option--selected.vs__dropdown-option--highlight {\n  color: #fff !important;\n  background-color: #7367f0 !important;\n}\n.vs__clear svg {\n  color: #6e6b7b;\n}\n.vs__selected {\n  color: #fff;\n}\n.v-select.vs--single .vs__selected {\n  color: #6e6b7b;\n  transition: transform 0.2s ease;\n}\n[dir] .v-select.vs--single .vs__selected {\n  margin-top: 5px;\n}\n[dir=ltr] .v-select.vs--single .vs__selected input {\n  padding-left: 0;\n}\n[dir=rtl] .v-select.vs--single .vs__selected input {\n  padding-right: 0;\n}\n[dir=ltr] .vs--single.vs--open .vs__selected {\n  transform: translateX(5px);\n}\n[dir=rtl] .vs--single.vs--open .vs__selected {\n  transform: translateX(-5px);\n}\n.vs__selected .vs__deselect {\n  color: inherit;\n}\n.v-select:not(.vs--single) .vs__selected {\n  font-size: 0.9rem;\n}\n[dir] .v-select:not(.vs--single) .vs__selected {\n  border-radius: 3px;\n  padding: 0 0.6em;\n}\n[dir=ltr] .v-select:not(.vs--single) .vs__selected {\n  margin: 5px 2px 2px 5px;\n}\n[dir=rtl] .v-select:not(.vs--single) .vs__selected {\n  margin: 5px 5px 2px 2px;\n}\n.v-select:not(.vs--single) .vs__deselect svg {\n  vertical-align: text-top;\n}\n[dir] .v-select:not(.vs--single) .vs__deselect svg {\n  transform: scale(0.8);\n}\n.vs__dropdown-menu {\n  top: calc(100% + 1rem);\n}\n[dir] .vs__dropdown-menu {\n  border: none;\n  border-radius: 6px;\n  padding: 0;\n}\n[dir] .vs--open .vs__dropdown-toggle {\n  border-color: #7367f0;\n  border-bottom-color: #7367f0;\n  box-shadow: 0 3px 10px 0 rgba(34, 41, 47, 0.1);\n}\n[dir=ltr] .vs--open .vs__dropdown-toggle {\n  border-bottom-left-radius: 0.357rem;\n  border-bottom-right-radius: 0.357rem;\n}\n[dir=rtl] .vs--open .vs__dropdown-toggle {\n  border-bottom-right-radius: 0.357rem;\n  border-bottom-left-radius: 0.357rem;\n}\n.select-size-lg .vs__selected {\n  font-size: 1rem !important;\n}\n[dir] .select-size-lg.vs--single.vs--open .vs__selected {\n  margin-top: 6px;\n}\n.select-size-lg .vs__dropdown-toggle,\n.select-size-lg .vs__selected {\n  font-size: 1.25rem;\n}\n[dir] .select-size-lg .vs__dropdown-toggle {\n  padding: 5px;\n}\n[dir] .select-size-lg .vs__dropdown-toggle input {\n  margin-top: 0;\n}\n.select-size-lg .vs__deselect svg {\n  vertical-align: middle !important;\n}\n[dir] .select-size-lg .vs__deselect svg {\n  transform: scale(1) !important;\n}\n[dir] .select-size-sm .vs__dropdown-toggle {\n  padding-bottom: 0;\n  padding: 1px;\n}\n[dir] .select-size-sm.vs--single .vs__dropdown-toggle {\n  padding: 2px;\n}\n.select-size-sm .vs__dropdown-toggle,\n.select-size-sm .vs__selected {\n  font-size: 0.9rem;\n}\n[dir] .select-size-sm .vs__actions {\n  padding-top: 2px;\n  padding-bottom: 2px;\n}\n.select-size-sm .vs__deselect svg {\n  vertical-align: middle !important;\n}\n[dir] .select-size-sm .vs__search {\n  margin-top: 0;\n}\n.select-size-sm.v-select .vs__selected {\n  font-size: 0.75rem;\n}\n[dir] .select-size-sm.v-select .vs__selected {\n  padding: 0 0.3rem;\n}\n[dir] .select-size-sm.v-select:not(.vs--single) .vs__selected {\n  margin: 4px 5px;\n}\n[dir] .select-size-sm.v-select.vs--single .vs__selected {\n  margin-top: 1px;\n}\n[dir] .select-size-sm.vs--single.vs--open .vs__selected {\n  margin-top: 4px;\n}\n.dark-layout .vs__dropdown-toggle {\n  color: #b4b7bd;\n}\n[dir] .dark-layout .vs__dropdown-toggle {\n  background: #283046;\n  border-color: #404656;\n}\n.dark-layout .vs__selected-options input {\n  color: #b4b7bd;\n}\n.dark-layout .vs__selected-options input::placeholder {\n  color: #676d7d;\n}\n.dark-layout .vs__actions svg {\n  fill: #404656;\n}\n[dir] .dark-layout .vs__dropdown-menu {\n  background: #283046;\n}\n.dark-layout .vs__dropdown-menu li {\n  color: #b4b7bd;\n}\n.dark-layout .v-select:not(.vs--single) .vs__selected {\n  color: #7367f0;\n}\n[dir] .dark-layout .v-select:not(.vs--single) .vs__selected {\n  background-color: rgba(115, 103, 240, 0.12);\n}\n.dark-layout .v-select.vs--single .vs__selected {\n  color: #b4b7bd !important;\n}\n.dark-layout .vs--disabled .vs__dropdown-toggle,\n.dark-layout .vs--disabled .vs__clear,\n.dark-layout .vs--disabled .vs__search,\n.dark-layout .vs--disabled .vs__selected,\n.dark-layout .vs--disabled .vs__open-indicator {\n  opacity: 0.5;\n}\n[dir] .dark-layout .vs--disabled .vs__dropdown-toggle, [dir] .dark-layout .vs--disabled .vs__clear, [dir] .dark-layout .vs--disabled .vs__search, [dir] .dark-layout .vs--disabled .vs__selected, [dir] .dark-layout .vs--disabled .vs__open-indicator {\n  background-color: #283046;\n}\n\n/* Lista de intervale: doar cat sa incapa \"60 minute\". */\n.lista-minute {\n  width: 6.5rem;\n  height: 1.6rem;\n  line-height: 1.2;\n}\n[dir=ltr] .lista-minute {\n  padding: 0 1.2rem 0 0.4rem;\n  background-position: right 0.35rem center;\n}\n[dir=rtl] .lista-minute {\n  padding: 0 0.4rem 0 1.2rem;\n  background-position: left 0.35rem center;\n}\n.automat-mesaje small {\n  font-size: 0.72rem;\n}\n\n/* Randul de filtre: casute scunde, ca sa nu ingroase antetul. */\n[dir] .rand-filtre th {\n  padding: 0.15rem 0.25rem;\n}\n.rand-filtre input,\n.rand-filtre select {\n  height: 1.7rem;\n  font-size: 0.78rem;\n}\n[dir] .rand-filtre input, [dir] .rand-filtre select {\n  padding: 0 0.35rem;\n}\n\n/* Multe firme alese ar impinge randul in jos; lista se deruleaza in loc. */\n.select-firme .vs__selected-options {\n  max-height: 6rem;\n  overflow-y: auto;\n}", ""]);
// Exports
module.exports = exports;


/***/ }),

/***/ "./node_modules/css-loader/dist/cjs.js!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src/index.js?!./node_modules/sass-loader/dist/cjs.js?!./node_modules/sass-loader/dist/cjs.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/app_pages/spv/Solicitari.vue?vue&type=style&index=0&lang=scss&":
/*!***********************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/css-loader/dist/cjs.js!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src??ref--7-2!./node_modules/sass-loader/dist/cjs.js??ref--7-3!./node_modules/sass-loader/dist/cjs.js??ref--11-0!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/src/views/app_pages/spv/Solicitari.vue?vue&type=style&index=0&lang=scss& ***!
  \***********************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

// Imports
var ___CSS_LOADER_API_IMPORT___ = __webpack_require__(/*! ../../../../../../node_modules/css-loader/dist/runtime/api.js */ "./node_modules/css-loader/dist/runtime/api.js");
exports = ___CSS_LOADER_API_IMPORT___(false);
// Module
exports.push([module.i, ".v-select {\n  position: relative;\n  font-family: inherit;\n}\n.v-select,\n.v-select * {\n  box-sizing: border-box;\n}\n\n/* KeyFrames */\n@-webkit-keyframes vSelectSpinner-ltr {\n0% {\n    transform: rotate(0deg);\n}\n100% {\n    transform: rotate(360deg);\n}\n}\n@-webkit-keyframes vSelectSpinner-rtl {\n0% {\n    transform: rotate(0deg);\n}\n100% {\n    transform: rotate(-360deg);\n}\n}\n@keyframes vSelectSpinner-ltr {\n0% {\n    transform: rotate(0deg);\n}\n100% {\n    transform: rotate(360deg);\n}\n}\n@keyframes vSelectSpinner-rtl {\n0% {\n    transform: rotate(0deg);\n}\n100% {\n    transform: rotate(-360deg);\n}\n}\n/* Dropdown Default Transition */\n.vs__fade-enter-active,\n.vs__fade-leave-active {\n  pointer-events: none;\n  transition: opacity 0.15s cubic-bezier(1, 0.5, 0.8, 1);\n}\n.vs__fade-enter,\n.vs__fade-leave-to {\n  opacity: 0;\n}\n\n/** Component States */\n/*\n * Disabled\n *\n * When the component is disabled, all interaction\n * should be prevented. Here we modify the bg color,\n * and change the cursor displayed on the interactive\n * components.\n */\n[dir] .vs--disabled .vs__dropdown-toggle, [dir] .vs--disabled .vs__clear, [dir] .vs--disabled .vs__search, [dir] .vs--disabled .vs__selected, [dir] .vs--disabled .vs__open-indicator {\n  cursor: not-allowed;\n  background-color: #f8f8f8;\n}\n\n/*\n *  RTL - Right to Left Support\n *\n *  Because we're using a flexbox layout, the `dir=\"rtl\"`\n *  HTML attribute does most of the work for us by\n *  rearranging the child elements visually.\n */\n.v-select[dir=rtl] .vs__actions {\n  padding: 0 3px 0 6px;\n}\n.v-select[dir=rtl] .vs__clear {\n  margin-left: 6px;\n  margin-right: 0;\n}\n.v-select[dir=rtl] .vs__deselect {\n  margin-left: 0;\n  margin-right: 2px;\n}\n.v-select[dir=rtl] .vs__dropdown-menu {\n  text-align: right;\n}\n\n/**\n    Dropdown Toggle\n\n    The dropdown toggle is the primary wrapper of the component. It\n    has two direct descendants: .vs__selected-options, and .vs__actions.\n\n    .vs__selected-options holds the .vs__selected's as well as the\n    main search input.\n\n    .vs__actions holds the clear button and dropdown toggle.\n */\n.vs__dropdown-toggle {\n  appearance: none;\n  display: flex;\n  white-space: normal;\n}\n[dir] .vs__dropdown-toggle {\n  padding: 0 0 4px 0;\n  background: none;\n  border: 1px solid #d8d6de;\n  border-radius: 0.357rem;\n}\n.vs__selected-options {\n  display: flex;\n  flex-basis: 100%;\n  flex-grow: 1;\n  flex-wrap: wrap;\n  position: relative;\n}\n[dir] .vs__selected-options {\n  padding: 0 2px;\n}\n.vs__actions {\n  display: flex;\n  align-items: center;\n}\n[dir=ltr] .vs__actions {\n  padding: 4px 6px 0 3px;\n}\n[dir=rtl] .vs__actions {\n  padding: 4px 3px 0 6px;\n}\n\n/* Dropdown Toggle States */\n[dir] .vs--searchable .vs__dropdown-toggle {\n  cursor: text;\n}\n[dir] .vs--unsearchable .vs__dropdown-toggle {\n  cursor: pointer;\n}\n[dir] .vs--open .vs__dropdown-toggle {\n  border-bottom-color: transparent;\n}\n[dir=ltr] .vs--open .vs__dropdown-toggle {\n  border-bottom-left-radius: 0;\n  border-bottom-right-radius: 0;\n}\n[dir=rtl] .vs--open .vs__dropdown-toggle {\n  border-bottom-right-radius: 0;\n  border-bottom-left-radius: 0;\n}\n.vs__open-indicator {\n  fill: rgba(60, 60, 60, 0.5);\n  transition: transform 150ms cubic-bezier(1, -0.115, 0.975, 0.855);\n}\n[dir] .vs__open-indicator {\n  transform: scale(1);\n  transition-timing-function: cubic-bezier(1, -0.115, 0.975, 0.855);\n}\n[dir=ltr] .vs--open .vs__open-indicator {\n  transform: rotate(180deg) scale(1);\n}\n[dir=rtl] .vs--open .vs__open-indicator {\n  transform: rotate(-180deg) scale(1);\n}\n.vs--loading .vs__open-indicator {\n  opacity: 0;\n}\n\n/* Clear Button */\n.vs__clear {\n  fill: rgba(60, 60, 60, 0.5);\n}\n[dir] .vs__clear {\n  padding: 0;\n  border: 0;\n  background-color: transparent;\n  cursor: pointer;\n}\n[dir=ltr] .vs__clear {\n  margin-right: 8px;\n}\n[dir=rtl] .vs__clear {\n  margin-left: 8px;\n}\n\n/* Dropdown Menu */\n.vs__dropdown-menu {\n  display: block;\n  box-sizing: border-box;\n  position: absolute;\n  top: calc(100% - 1px);\n  z-index: 1000;\n  width: 100%;\n  max-height: 350px;\n  min-width: 160px;\n  overflow-y: auto;\n  list-style: none;\n}\n[dir] .vs__dropdown-menu {\n  padding: 5px 0;\n  margin: 0;\n  box-shadow: 0px 4px 25px 0px rgba(0, 0, 0, 0.1);\n  border: 1px solid #d8d6de;\n  border-top-style: none;\n  border-radius: 0 0 0.357rem 0.357rem;\n  background: #fff;\n}\n[dir=ltr] .vs__dropdown-menu {\n  left: 0;\n  text-align: left;\n}\n[dir=rtl] .vs__dropdown-menu {\n  right: 0;\n  text-align: right;\n}\n[dir] .vs__no-options {\n  text-align: center;\n}\n\n/* List Items */\n.vs__dropdown-option {\n  line-height: 1.42857143;\n  /* Normalize line height */\n  display: block;\n  color: #333;\n  /* Overrides most CSS frameworks */\n  white-space: nowrap;\n}\n[dir] .vs__dropdown-option {\n  padding: 3px 20px;\n  clear: both;\n  cursor: pointer;\n}\n.vs__dropdown-option--highlight {\n  color: #7367f0 !important;\n}\n[dir] .vs__dropdown-option--highlight {\n  background: rgba(115, 103, 240, 0.12);\n}\n.vs__dropdown-option--deselect {\n  color: #fff;\n}\n[dir] .vs__dropdown-option--deselect {\n  background: #fb5858;\n}\n.vs__dropdown-option--disabled {\n  color: rgba(60, 60, 60, 0.5);\n}\n[dir] .vs__dropdown-option--disabled {\n  background: inherit;\n  cursor: inherit;\n}\n\n/* Selected Tags */\n.vs__selected {\n  display: flex;\n  align-items: center;\n  color: #333;\n  line-height: 1.8;\n  z-index: 0;\n}\n[dir] .vs__selected {\n  background-color: #7367f0;\n  border: 0 solid rgba(60, 60, 60, 0.26);\n  border-radius: 0.357rem;\n  margin: 4px 2px 0px 2px;\n  padding: 0 0.25em;\n}\n.vs__deselect {\n  display: inline-flex;\n  appearance: none;\n  fill: rgba(60, 60, 60, 0.5);\n}\n[dir] .vs__deselect {\n  padding: 0;\n  border: 0;\n  cursor: pointer;\n  background: none;\n  text-shadow: 0 1px 0 #fff;\n}\n[dir=ltr] .vs__deselect {\n  margin-left: 4px;\n}\n[dir=rtl] .vs__deselect {\n  margin-right: 4px;\n}\n\n/* States */\n[dir] .vs--single .vs__selected {\n  background-color: transparent;\n  border-color: transparent;\n}\n.vs--single.vs--open .vs__selected, .vs--single.vs--loading .vs__selected {\n  position: absolute;\n  opacity: 0.4;\n}\n.vs--single.vs--searching .vs__selected {\n  display: none;\n}\n\n/* Search Input */\n/**\n * Super weird bug... If this declaration is grouped\n * below, the cancel button will still appear in chrome.\n * If it's up here on it's own, it'll hide it.\n */\n.vs__search::-webkit-search-cancel-button {\n  display: none;\n}\n.vs__search::-webkit-search-decoration,\n.vs__search::-webkit-search-results-button,\n.vs__search::-webkit-search-results-decoration,\n.vs__search::-ms-clear {\n  display: none;\n}\n.vs__search,\n.vs__search:focus {\n  appearance: none;\n  line-height: 1.8;\n  font-size: 1em;\n  outline: none;\n  width: 0;\n  max-width: 100%;\n  flex-grow: 1;\n  z-index: 1;\n}\n[dir] .vs__search, [dir] .vs__search:focus {\n  border: 1px solid transparent;\n  margin: 4px 0 0 0;\n  padding: 0 7px;\n  background: none;\n  box-shadow: none;\n}\n[dir=ltr] .vs__search, [dir=ltr] .vs__search:focus {\n  border-left: none;\n}\n[dir=rtl] .vs__search, [dir=rtl] .vs__search:focus {\n  border-right: none;\n}\n.vs__search::placeholder {\n  color: #6e6b7b;\n}\n\n/**\n    States\n */\n.vs--unsearchable .vs__search {\n  opacity: 1;\n}\n[dir] .vs--unsearchable:not(.vs--disabled) .vs__search {\n  cursor: pointer;\n}\n.vs--single.vs--searching:not(.vs--open):not(.vs--loading) .vs__search {\n  opacity: 0.2;\n}\n\n/* Loading Spinner */\n.vs__spinner {\n  align-self: center;\n  opacity: 0;\n  font-size: 5px;\n  text-indent: -9999em;\n  overflow: hidden;\n  transition: opacity 0.1s;\n}\n[dir] .vs__spinner {\n  border-top: 0.9em solid rgba(100, 100, 100, 0.1);\n  border-bottom: 0.9em solid rgba(100, 100, 100, 0.1);\n  transform: translateZ(0);\n}\n[dir=ltr] .vs__spinner {\n  border-right: 0.9em solid rgba(100, 100, 100, 0.1);\n  border-left: 0.9em solid rgba(60, 60, 60, 0.45);\n  animation:  vSelectSpinner-ltr 1.1s infinite linear;\n}\n[dir=rtl] .vs__spinner {\n  border-left: 0.9em solid rgba(100, 100, 100, 0.1);\n  border-right: 0.9em solid rgba(60, 60, 60, 0.45);\n  animation:  vSelectSpinner-rtl 1.1s infinite linear;\n}\n.vs__spinner,\n.vs__spinner:after {\n  width: 5em;\n  height: 5em;\n}\n[dir] .vs__spinner, [dir] .vs__spinner:after {\n  border-radius: 50%;\n}\n\n/* Loading Spinner States */\n.vs--loading .vs__spinner {\n  opacity: 1;\n}\n.vs__open-indicator {\n  fill: none;\n}\n[dir] .vs__open-indicator {\n  margin-top: 0.15rem;\n}\n.vs__dropdown-toggle {\n  transition: all 0.25s ease-in-out;\n}\n[dir] .vs__dropdown-toggle {\n  padding: 0.59px 0 4px 0;\n}\n[dir=ltr] .vs--single .vs__dropdown-toggle {\n  padding-left: 6px;\n}\n[dir=rtl] .vs--single .vs__dropdown-toggle {\n  padding-right: 6px;\n}\n.vs__dropdown-option--disabled {\n  opacity: 0.5;\n}\n[dir] .vs__dropdown-option--disabled.vs__dropdown-option--selected {\n  background: #7367f0 !important;\n}\n.vs__dropdown-option {\n  color: #6e6b7b;\n}\n[dir] .vs__dropdown-option, [dir] .vs__no-options {\n  padding: 7px 20px;\n}\n.vs__dropdown-option--selected {\n  background-color: #7367f0;\n  color: #fff;\n  position: relative;\n}\n.vs__dropdown-option--selected::after {\n  content: \"\";\n  height: 1.1rem;\n  width: 1.1rem;\n  display: inline-block;\n  position: absolute;\n  top: 50%;\n  transform: translateY(-50%);\n  right: 20px;\n  background-image: url(\"data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='24' height='24' viewBox='0 0 24 24' fill='none' stroke='%23fff' stroke-width='2' stroke-linecap='round' stroke-linejoin='round' class='feather feather-check'%3E%3Cpolyline points='20 6 9 17 4 12'%3E%3C/polyline%3E%3C/svg%3E\");\n  background-repeat: no-repeat;\n  background-position: center;\n  background-size: 1.1rem;\n}\n[dir=rtl] .vs__dropdown-option--selected::after {\n  left: 20px;\n  right: unset;\n}\n.vs__dropdown-option--selected.vs__dropdown-option--highlight {\n  color: #fff !important;\n  background-color: #7367f0 !important;\n}\n.vs__clear svg {\n  color: #6e6b7b;\n}\n.vs__selected {\n  color: #fff;\n}\n.v-select.vs--single .vs__selected {\n  color: #6e6b7b;\n  transition: transform 0.2s ease;\n}\n[dir] .v-select.vs--single .vs__selected {\n  margin-top: 5px;\n}\n[dir=ltr] .v-select.vs--single .vs__selected input {\n  padding-left: 0;\n}\n[dir=rtl] .v-select.vs--single .vs__selected input {\n  padding-right: 0;\n}\n[dir=ltr] .vs--single.vs--open .vs__selected {\n  transform: translateX(5px);\n}\n[dir=rtl] .vs--single.vs--open .vs__selected {\n  transform: translateX(-5px);\n}\n.vs__selected .vs__deselect {\n  color: inherit;\n}\n.v-select:not(.vs--single) .vs__selected {\n  font-size: 0.9rem;\n}\n[dir] .v-select:not(.vs--single) .vs__selected {\n  border-radius: 3px;\n  padding: 0 0.6em;\n}\n[dir=ltr] .v-select:not(.vs--single) .vs__selected {\n  margin: 5px 2px 2px 5px;\n}\n[dir=rtl] .v-select:not(.vs--single) .vs__selected {\n  margin: 5px 5px 2px 2px;\n}\n.v-select:not(.vs--single) .vs__deselect svg {\n  vertical-align: text-top;\n}\n[dir] .v-select:not(.vs--single) .vs__deselect svg {\n  transform: scale(0.8);\n}\n.vs__dropdown-menu {\n  top: calc(100% + 1rem);\n}\n[dir] .vs__dropdown-menu {\n  border: none;\n  border-radius: 6px;\n  padding: 0;\n}\n[dir] .vs--open .vs__dropdown-toggle {\n  border-color: #7367f0;\n  border-bottom-color: #7367f0;\n  box-shadow: 0 3px 10px 0 rgba(34, 41, 47, 0.1);\n}\n[dir=ltr] .vs--open .vs__dropdown-toggle {\n  border-bottom-left-radius: 0.357rem;\n  border-bottom-right-radius: 0.357rem;\n}\n[dir=rtl] .vs--open .vs__dropdown-toggle {\n  border-bottom-right-radius: 0.357rem;\n  border-bottom-left-radius: 0.357rem;\n}\n.select-size-lg .vs__selected {\n  font-size: 1rem !important;\n}\n[dir] .select-size-lg.vs--single.vs--open .vs__selected {\n  margin-top: 6px;\n}\n.select-size-lg .vs__dropdown-toggle,\n.select-size-lg .vs__selected {\n  font-size: 1.25rem;\n}\n[dir] .select-size-lg .vs__dropdown-toggle {\n  padding: 5px;\n}\n[dir] .select-size-lg .vs__dropdown-toggle input {\n  margin-top: 0;\n}\n.select-size-lg .vs__deselect svg {\n  vertical-align: middle !important;\n}\n[dir] .select-size-lg .vs__deselect svg {\n  transform: scale(1) !important;\n}\n[dir] .select-size-sm .vs__dropdown-toggle {\n  padding-bottom: 0;\n  padding: 1px;\n}\n[dir] .select-size-sm.vs--single .vs__dropdown-toggle {\n  padding: 2px;\n}\n.select-size-sm .vs__dropdown-toggle,\n.select-size-sm .vs__selected {\n  font-size: 0.9rem;\n}\n[dir] .select-size-sm .vs__actions {\n  padding-top: 2px;\n  padding-bottom: 2px;\n}\n.select-size-sm .vs__deselect svg {\n  vertical-align: middle !important;\n}\n[dir] .select-size-sm .vs__search {\n  margin-top: 0;\n}\n.select-size-sm.v-select .vs__selected {\n  font-size: 0.75rem;\n}\n[dir] .select-size-sm.v-select .vs__selected {\n  padding: 0 0.3rem;\n}\n[dir] .select-size-sm.v-select:not(.vs--single) .vs__selected {\n  margin: 4px 5px;\n}\n[dir] .select-size-sm.v-select.vs--single .vs__selected {\n  margin-top: 1px;\n}\n[dir] .select-size-sm.vs--single.vs--open .vs__selected {\n  margin-top: 4px;\n}\n.dark-layout .vs__dropdown-toggle {\n  color: #b4b7bd;\n}\n[dir] .dark-layout .vs__dropdown-toggle {\n  background: #283046;\n  border-color: #404656;\n}\n.dark-layout .vs__selected-options input {\n  color: #b4b7bd;\n}\n.dark-layout .vs__selected-options input::placeholder {\n  color: #676d7d;\n}\n.dark-layout .vs__actions svg {\n  fill: #404656;\n}\n[dir] .dark-layout .vs__dropdown-menu {\n  background: #283046;\n}\n.dark-layout .vs__dropdown-menu li {\n  color: #b4b7bd;\n}\n.dark-layout .v-select:not(.vs--single) .vs__selected {\n  color: #7367f0;\n}\n[dir] .dark-layout .v-select:not(.vs--single) .vs__selected {\n  background-color: rgba(115, 103, 240, 0.12);\n}\n.dark-layout .v-select.vs--single .vs__selected {\n  color: #b4b7bd !important;\n}\n.dark-layout .vs--disabled .vs__dropdown-toggle,\n.dark-layout .vs--disabled .vs__clear,\n.dark-layout .vs--disabled .vs__search,\n.dark-layout .vs--disabled .vs__selected,\n.dark-layout .vs--disabled .vs__open-indicator {\n  opacity: 0.5;\n}\n[dir] .dark-layout .vs--disabled .vs__dropdown-toggle, [dir] .dark-layout .vs--disabled .vs__clear, [dir] .dark-layout .vs--disabled .vs__search, [dir] .dark-layout .vs--disabled .vs__selected, [dir] .dark-layout .vs--disabled .vs__open-indicator {\n  background-color: #283046;\n}\n\n/* Lista de intervale: doar cat sa incapa \"60 minute\", scunda ca sa nu ingroase randul. */\n.lista-minute {\n  width: 6.5rem;\n  height: 1.6rem;\n  line-height: 1.2;\n}\n[dir=ltr] .lista-minute {\n  padding: 0 1.2rem 0 0.4rem;\n  background-position: right 0.35rem center;\n}\n[dir=rtl] .lista-minute {\n  padding: 0 0.4rem 0 1.2rem;\n  background-position: left 0.35rem center;\n}\n\n/* Casutele de filtru din antet: scunde, ca sa nu ingroase capul tabelului. */\n.filtru-coloana {\n  height: 1.6rem;\n  font-size: 0.75rem;\n  font-weight: 400;\n}\n[dir] .filtru-coloana {\n  padding: 0 0.35rem;\n  margin-top: 0.15rem;\n}\n\n/* Bifele stau lipite de buton, discret, fara chenar. */\n[dir] .pentru-tiparire {\n  margin-bottom: 0.15rem;\n}\n\n/* Bifa incepe din coltul din stanga al butonului, nu mai la stanga de el. */\n[dir=ltr] .pentru-tiparire ::v-deep .custom-control {\n  padding-left: 1.35rem;\n}\n[dir=rtl] .pentru-tiparire ::v-deep .custom-control {\n  padding-right: 1.35rem;\n}\n[dir=ltr] .pentru-tiparire ::v-deep .custom-control-label::before, [dir=ltr] .pentru-tiparire ::v-deep .custom-control-label::after {\n  left: -1.35rem;\n}\n[dir=rtl] .pentru-tiparire ::v-deep .custom-control-label::before, [dir=rtl] .pentru-tiparire ::v-deep .custom-control-label::after {\n  right: -1.35rem;\n}\n.pentru-tiparire small {\n  font-size: 0.75rem;\n}\n.automat-raspunsuri small {\n  font-size: 0.72rem;\n}\n\n/* Multe firme alese ar impinge tot randul in jos; lista se deruleaza in loc. */\n.select-firme .vs__selected-options {\n  max-height: 7rem;\n  overflow-y: auto;\n}", ""]);
// Exports
module.exports = exports;


/***/ }),

/***/ "./node_modules/css-loader/dist/cjs.js?!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/app_pages/spv/Certificate.vue?vue&type=style&index=0&id=2163fac4&scoped=true&lang=css&":
/*!******************************************************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/css-loader/dist/cjs.js??ref--6-1!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src??ref--6-2!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/src/views/app_pages/spv/Certificate.vue?vue&type=style&index=0&id=2163fac4&scoped=true&lang=css& ***!
  \******************************************************************************************************************************************************************************************************************************************************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

// Imports
var ___CSS_LOADER_API_IMPORT___ = __webpack_require__(/*! ../../../../../../node_modules/css-loader/dist/runtime/api.js */ "./node_modules/css-loader/dist/runtime/api.js");
exports = ___CSS_LOADER_API_IMPORT___(false);
// Module
exports.push([module.i, "\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n/* Lista de dosare: cat sa se vada cateva randuri, restul se deruleaza. */\n.lista-foldere[data-v-2163fac4] {\n  max-height: 15rem;\n  overflow-y: auto;\n}\n\n/*\n * Certificatul scos din uz: randul se stinge, ca sa se vada dintr-o privire ca\n * aplicatia nu lucreaza cu el, dar ramane citibil — datele lui se cauta si dupa\n * scoatere. Randurile le scrie b-table, adica alt component, de aceea ::v-deep.\n */\n[data-v-2163fac4] .rand-scos-din-uz {\n  opacity: 0.55;\n}\n\n/* Comenzile de verificare: de citit si de copiat asa cum sunt scrise. */\n.verificari-ajutor[data-v-2163fac4] {\n  font-size: 0.8rem;\n  white-space: pre-wrap;\n  word-break: break-word;\n}\n[dir] .verificari-ajutor[data-v-2163fac4] {\n  padding: 0.5rem 0.75rem;\n  background: rgba(115, 103, 240, 0.06);\n  border-radius: 0.2rem;\n}\n[dir=ltr] .verificari-ajutor[data-v-2163fac4] {\n  border-left: 3px solid rgba(115, 103, 240, 0.5);\n}\n[dir=rtl] .verificari-ajutor[data-v-2163fac4] {\n  border-right: 3px solid rgba(115, 103, 240, 0.5);\n}\n", ""]);
// Exports
module.exports = exports;


/***/ }),

/***/ "./node_modules/css-loader/dist/cjs.js?!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/app_pages/spv/Declaratii.vue?vue&type=style&index=0&id=fcac4d92&scoped=true&lang=css&":
/*!*****************************************************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/css-loader/dist/cjs.js??ref--6-1!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src??ref--6-2!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/src/views/app_pages/spv/Declaratii.vue?vue&type=style&index=0&id=fcac4d92&scoped=true&lang=css& ***!
  \*****************************************************************************************************************************************************************************************************************************************************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

// Imports
var ___CSS_LOADER_API_IMPORT___ = __webpack_require__(/*! ../../../../../../node_modules/css-loader/dist/runtime/api.js */ "./node_modules/css-loader/dist/runtime/api.js");
exports = ___CSS_LOADER_API_IMPORT___(false);
// Module
exports.push([module.i, "\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n/*\n * Eroarea ocupa un singur rand in tabel; sageata de alaturi arata ca textul\n * continua si il desfasoara pe loc. Textul se taie fara puncte de suspensie,\n * fiindca sageata spune deja ca urmeaza.\n */\n.coloana-eroare[data-v-fcac4d92] {\n  max-width: 280px;\n  min-width: 0;\n  white-space: nowrap;\n  overflow: hidden;\n  text-overflow: clip;\n  /* Se stinge spre capat, ca taietura sa nu para o litera lipsa */\n  mask-image: linear-gradient(to right, black 85%, transparent 100%);\n  -webkit-mask-image: linear-gradient(to right, black 85%, transparent 100%);\n}\n\n/* Insigna care duce undeva se poarta ca un buton */\n[dir] .badge-apasabil[data-v-fcac4d92] {\n  cursor: pointer;\n}\n.coloana-eroare-desfasurata[data-v-fcac4d92] {\n  max-width: 280px;\n  min-width: 0;\n  white-space: normal;\n}\n\n/*\n * Randuri stranse: incap mai multe declaratii pe ecran, fara derulare.\n * Celulele sunt desenate de componenta de tabel, nu de sablonul acesta, deci\n * regulile trebuie sa treaca dincolo de limita ei (::v-deep).\n */\n.tabel-compact[data-v-fcac4d92]  th,\n.tabel-compact[data-v-fcac4d92]  td {\n  vertical-align: middle;\n  font-size: 0.85rem;\n}\n[dir] .tabel-compact[data-v-fcac4d92]  th, [dir] .tabel-compact[data-v-fcac4d92]  td {\n  padding: 0.3rem 0.4rem !important;\n}\n[dir] .tabel-compact[data-v-fcac4d92]  .badge {\n  padding: 0.25rem 0.4rem;\n}\n.tabel-compact[data-v-fcac4d92]  .btn {\n  font-size: 0.75rem;\n}\n[dir] .tabel-compact[data-v-fcac4d92]  .btn {\n  padding: 0.15rem 0.4rem;\n}\n\n/* Cate randuri pe pagina: cat sa incapa „100 / pagina\", nu mai mult. */\n.selector-pagina[data-v-fcac4d92] {\n  width: 8rem;\n  height: 1.7rem;\n  line-height: 1.2;\n}\n[dir=ltr] .selector-pagina[data-v-fcac4d92] {\n  padding: 0 1.2rem 0 0.4rem;\n  background-position: right 0.35rem center;\n}\n[dir=rtl] .selector-pagina[data-v-fcac4d92] {\n  padding: 0 0.4rem 0 1.2rem;\n  background-position: left 0.35rem center;\n}\n\n/* Campul de minute: doar cat sa incapa un numar de trei cifre, scrise la mijloc. */\n/* Lista de intervale: doar cat sa incapa „60 minute\", scunda ca sa nu ingroase randul. */\n.lista-minute[data-v-fcac4d92] {\n  width: 6.5rem;\n  height: 1.6rem;\n  line-height: 1.2;\n}\n[dir=ltr] .lista-minute[data-v-fcac4d92] {\n  padding: 0 1.2rem 0 0.4rem;\n  background-position: right 0.35rem center;\n}\n[dir=rtl] .lista-minute[data-v-fcac4d92] {\n  padding: 0 0.4rem 0 1.2rem;\n  background-position: left 0.35rem center;\n}\n\n/*\n * Comutatorul se face verde doar cand e pornit, ca si textul de langa el:\n * stins, ramane cenusiu. Bulina si sina lui sunt desenate de componenta, deci\n * regulile trebuie sa treaca dincolo de ea.\n */\n[dir] .comutator-verde[data-v-fcac4d92]  .custom-control-input:checked ~ .custom-control-label::before {\n  background-color: #28c76f;\n  border-color: #28c76f;\n}\n[dir] .comutator-verde[data-v-fcac4d92]  .custom-control-input:focus ~ .custom-control-label::before {\n  box-shadow: 0 0 0 0.15rem rgba(40, 199, 111, 0.35);\n}\n\n/* Comutatorul de langa butonul de prelucrare poarta culoarea lui. */\n[dir] .comutator-primar[data-v-fcac4d92]  .custom-control-input:checked ~ .custom-control-label::before {\n  background-color: #7367f0;\n  border-color: #7367f0;\n}\n[dir] .comutator-primar[data-v-fcac4d92]  .custom-control-input:focus ~ .custom-control-label::before {\n  box-shadow: 0 0 0 0.15rem rgba(115, 103, 240, 0.35);\n}\n\n/*\n * Comutatoarele stau langa butoane, fara chenar: sunt reglaje, nu actiuni, si\n * nu trebuie sa concureze cu butoanele. Textul e mic si sters cand e stins.\n */\n/*\n * Bifa incepe fix in dreptul coltului din stanga al butonului si sta lipita de\n * el: se citesc ca un singur lucru, reglajul si actiunea pe care o priveste.\n */\n.pentru-tiparire[data-v-fcac4d92] {\n  line-height: 1.1;\n}\n[dir] .pentru-tiparire[data-v-fcac4d92] {\n  padding: 0;\n  margin-bottom: 0.15rem;\n}\n\n/* Textul incepe imediat dupa bifa sau comutator, fara golul lasat de tema. */\n[dir=ltr] .pentru-tiparire[data-v-fcac4d92]  .custom-switch {\n  padding-left: 3rem;\n}\n[dir=rtl] .pentru-tiparire[data-v-fcac4d92]  .custom-switch {\n  padding-right: 3rem;\n}\n[dir=ltr] .pentru-tiparire[data-v-fcac4d92]  .custom-checkbox {\n  padding-left: 1.35rem;\n}\n[dir=rtl] .pentru-tiparire[data-v-fcac4d92]  .custom-checkbox {\n  padding-right: 1.35rem;\n}\n.pentru-tiparire small[data-v-fcac4d92],\n.automat-recipise small[data-v-fcac4d92] {\n  font-size: 0.72rem;\n}\n\n/* Textul incepe imediat dupa comutator: tema lasa 0,5rem in plus fata de el. */\n[dir=ltr] .pentru-tiparire[data-v-fcac4d92]  .custom-switch {\n  padding-left: 3rem;\n}\n[dir=rtl] .pentru-tiparire[data-v-fcac4d92]  .custom-switch {\n  padding-right: 3rem;\n}\n\n/*\n * Bara care desparte cele trei parti ale lucrului: incarcare, depunere si\n * recipise. Se intinde pe toata inaltimea randului, nu doar cat elementul,\n * iar continutul fiecarei parti sta la mijlocul spatiului ei.\n */\n\n/* Explicatia se rupe pe randuri, ca sa nu iasa din chenar. */\n.pentru-tiparire small[data-v-fcac4d92] {\n  white-space: normal;\n  overflow-wrap: anywhere;\n  word-break: break-word;\n}\n\n/* Reglajul descarcarii automate: fara chenar, doar strans sub buton. */\n[dir] .automat-recipise[data-v-fcac4d92] {\n  padding: 0.1rem 0.2rem;\n}\n\n/* Butonul cu optiuni pastreaza latimea intreaga, ca cel simplu de dinainte. */\n[dir=ltr] .flex-shrink-0[data-v-fcac4d92]  .dropdown-toggle-split {\n  padding-left: 0.5rem;\n  padding-right: 0.5rem;\n}\n[dir=rtl] .flex-shrink-0[data-v-fcac4d92]  .dropdown-toggle-split {\n  padding-right: 0.5rem;\n  padding-left: 0.5rem;\n}\n\n/* Acelasi lucru la comutatorul verde: fara gol intre el si text. */\n[dir=ltr] .automat-recipise[data-v-fcac4d92]  .custom-switch {\n  padding-left: 3rem;\n}\n[dir=rtl] .automat-recipise[data-v-fcac4d92]  .custom-switch {\n  padding-right: 3rem;\n}\n\n/* Cei trei pasi din buton stau stransi, ca butonul sa nu creasca peste masura. */\n.buton-pasi div[data-v-fcac4d92] {\n  line-height: 1.5;\n}\n\n/* Mesajul brut al validatorului: se deosebeste de explicatie, fara sa o acopere. */\n.mesaj-original[data-v-fcac4d92] {\n  overflow-x: auto;\n}\n[dir] .mesaj-original[data-v-fcac4d92] {\n  padding: 0.25rem 0.5rem;\n  background: rgba(130, 134, 139, 0.08);\n  border-radius: 0.2rem;\n}\n[dir=ltr] .mesaj-original[data-v-fcac4d92] {\n  border-left: 3px solid rgba(130, 134, 139, 0.4);\n}\n[dir=rtl] .mesaj-original[data-v-fcac4d92] {\n  border-right: 3px solid rgba(130, 134, 139, 0.4);\n}\n.mesaj-original code[data-v-fcac4d92] {\n  white-space: pre-wrap;\n  word-break: break-word;\n}\n\n/* Randul din XML: se deruleaza pe orizontala, nu rupe fereastra. */\n.rand-xml[data-v-fcac4d92] {\n  overflow-x: auto;\n  white-space: pre;\n}\n[dir] .rand-xml[data-v-fcac4d92] {\n  padding: 0.25rem 0.5rem;\n  background: rgba(115, 103, 240, 0.06);\n  border-radius: 0.2rem;\n}\n[dir=ltr] .rand-xml[data-v-fcac4d92] {\n  border-left: 3px solid rgba(115, 103, 240, 0.5);\n}\n[dir=rtl] .rand-xml[data-v-fcac4d92] {\n  border-right: 3px solid rgba(115, 103, 240, 0.5);\n}\n\n/* Randul se scrie cu albastru, iar bucata gresita cu rosu, ca sa sara in ochi. */\n.xml-rand[data-v-fcac4d92] {\n  color: #1565c0;\n}\n.xml-gresit[data-v-fcac4d92] {\n  color: #d32f2f;\n  font-weight: 600;\n}\n[dir] .xml-gresit[data-v-fcac4d92] {\n  background: rgba(211, 47, 47, 0.1);\n  border-radius: 0.15rem;\n}\n\n/*\n * Indicatia de deschidere a fisierului: verde si mai mare decat textul din jur,\n * fiind lucrul pe care utilizatorul il cauta cu ochii cand vrea sa corecteze.\n */\n.indicatie-fisier[data-v-fcac4d92] {\n  color: #2e7d32;\n  font-size: 1rem;\n  font-weight: 500;\n  line-height: 1.4;\n}\n\n/* Combinatia de taste, pe fundal albastru: se vede ca e de apasat, nu de citit. */\n.indicatie-fisier kbd[data-v-fcac4d92] {\n  color: #fff;\n  font-weight: 600;\n}\n[dir] .indicatie-fisier kbd[data-v-fcac4d92] {\n  background: #1565c0;\n  padding: 0.1rem 0.35rem;\n  border-radius: 0.2rem;\n}\n", ""]);
// Exports
module.exports = exports;


/***/ }),

/***/ "./node_modules/css-loader/dist/cjs.js?!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/app_pages/spv/Societati.vue?vue&type=style&index=0&id=e2aaca68&scoped=true&lang=css&":
/*!****************************************************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/css-loader/dist/cjs.js??ref--6-1!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src??ref--6-2!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/src/views/app_pages/spv/Societati.vue?vue&type=style&index=0&id=e2aaca68&scoped=true&lang=css& ***!
  \****************************************************************************************************************************************************************************************************************************************************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

// Imports
var ___CSS_LOADER_API_IMPORT___ = __webpack_require__(/*! ../../../../../../node_modules/css-loader/dist/runtime/api.js */ "./node_modules/css-loader/dist/runtime/api.js");
exports = ___CSS_LOADER_API_IMPORT___(false);
// Module
exports.push([module.i, "\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n/* Casutele de filtru din antet: scunde, ca sa nu ingroase capul tabelului. */\n.filtru-coloana[data-v-e2aaca68] {\n  height: 1.6rem;\n  font-size: 0.75rem;\n  font-weight: 400;\n}\n[dir] .filtru-coloana[data-v-e2aaca68] {\n  padding: 0 0.35rem;\n  margin-top: 0.15rem;\n}\n", ""]);
// Exports
module.exports = exports;


/***/ }),

/***/ "./node_modules/css-loader/dist/cjs.js?!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/app_pages/spv/Utilizatori.vue?vue&type=style&index=0&id=17852987&scoped=true&lang=css&":
/*!******************************************************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/css-loader/dist/cjs.js??ref--6-1!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src??ref--6-2!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/src/views/app_pages/spv/Utilizatori.vue?vue&type=style&index=0&id=17852987&scoped=true&lang=css& ***!
  \******************************************************************************************************************************************************************************************************************************************************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

// Imports
var ___CSS_LOADER_API_IMPORT___ = __webpack_require__(/*! ../../../../../../node_modules/css-loader/dist/runtime/api.js */ "./node_modules/css-loader/dist/runtime/api.js");
exports = ___CSS_LOADER_API_IMPORT___(false);
// Module
exports.push([module.i, "\n.tabel-compact[data-v-17852987]  th,\r\n.tabel-compact[data-v-17852987]  td {\r\n  vertical-align: middle;\r\n  font-size: 0.85rem;\n}\n[dir] .tabel-compact[data-v-17852987]  th, [dir] .tabel-compact[data-v-17852987]  td {\r\n  padding: 0.4rem 0.5rem !important;\n}\r\n", ""]);
// Exports
module.exports = exports;


/***/ }),

/***/ "./node_modules/style-loader/index.js!./node_modules/css-loader/dist/cjs.js!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src/index.js?!./node_modules/sass-loader/dist/cjs.js?!./node_modules/sass-loader/dist/cjs.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/app_pages/Spv.vue?vue&type=style&index=0&id=50cb4596&lang=scss&scoped=true&":
/*!****************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/style-loader!./node_modules/css-loader/dist/cjs.js!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src??ref--7-2!./node_modules/sass-loader/dist/cjs.js??ref--7-3!./node_modules/sass-loader/dist/cjs.js??ref--11-0!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/src/views/app_pages/Spv.vue?vue&type=style&index=0&id=50cb4596&lang=scss&scoped=true& ***!
  \****************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {


var content = __webpack_require__(/*! !../../../../../node_modules/css-loader/dist/cjs.js!../../../../../node_modules/vue-loader/lib/loaders/stylePostLoader.js!../../../../../node_modules/postcss-loader/src??ref--7-2!../../../../../node_modules/sass-loader/dist/cjs.js??ref--7-3!../../../../../node_modules/sass-loader/dist/cjs.js??ref--11-0!../../../../../node_modules/vue-loader/lib??vue-loader-options!./Spv.vue?vue&type=style&index=0&id=50cb4596&lang=scss&scoped=true& */ "./node_modules/css-loader/dist/cjs.js!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src/index.js?!./node_modules/sass-loader/dist/cjs.js?!./node_modules/sass-loader/dist/cjs.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/app_pages/Spv.vue?vue&type=style&index=0&id=50cb4596&lang=scss&scoped=true&");

if(typeof content === 'string') content = [[module.i, content, '']];

var transform;
var insertInto;



var options = {"hmr":true}

options.transform = transform
options.insertInto = undefined;

var update = __webpack_require__(/*! ../../../../../node_modules/style-loader/lib/addStyles.js */ "./node_modules/style-loader/lib/addStyles.js")(content, options);

if(content.locals) module.exports = content.locals;

if(false) {}

/***/ }),

/***/ "./node_modules/style-loader/index.js!./node_modules/css-loader/dist/cjs.js!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src/index.js?!./node_modules/sass-loader/dist/cjs.js?!./node_modules/sass-loader/dist/cjs.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/app_pages/Spv.vue?vue&type=style&index=1&lang=scss&":
/*!****************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/style-loader!./node_modules/css-loader/dist/cjs.js!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src??ref--7-2!./node_modules/sass-loader/dist/cjs.js??ref--7-3!./node_modules/sass-loader/dist/cjs.js??ref--11-0!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/src/views/app_pages/Spv.vue?vue&type=style&index=1&lang=scss& ***!
  \****************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {


var content = __webpack_require__(/*! !../../../../../node_modules/css-loader/dist/cjs.js!../../../../../node_modules/vue-loader/lib/loaders/stylePostLoader.js!../../../../../node_modules/postcss-loader/src??ref--7-2!../../../../../node_modules/sass-loader/dist/cjs.js??ref--7-3!../../../../../node_modules/sass-loader/dist/cjs.js??ref--11-0!../../../../../node_modules/vue-loader/lib??vue-loader-options!./Spv.vue?vue&type=style&index=1&lang=scss& */ "./node_modules/css-loader/dist/cjs.js!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src/index.js?!./node_modules/sass-loader/dist/cjs.js?!./node_modules/sass-loader/dist/cjs.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/app_pages/Spv.vue?vue&type=style&index=1&lang=scss&");

if(typeof content === 'string') content = [[module.i, content, '']];

var transform;
var insertInto;



var options = {"hmr":true}

options.transform = transform
options.insertInto = undefined;

var update = __webpack_require__(/*! ../../../../../node_modules/style-loader/lib/addStyles.js */ "./node_modules/style-loader/lib/addStyles.js")(content, options);

if(content.locals) module.exports = content.locals;

if(false) {}

/***/ }),

/***/ "./node_modules/style-loader/index.js!./node_modules/css-loader/dist/cjs.js!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src/index.js?!./node_modules/sass-loader/dist/cjs.js?!./node_modules/sass-loader/dist/cjs.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/app_pages/spv/Mesaje.vue?vue&type=style&index=0&lang=scss&":
/*!***********************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/style-loader!./node_modules/css-loader/dist/cjs.js!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src??ref--7-2!./node_modules/sass-loader/dist/cjs.js??ref--7-3!./node_modules/sass-loader/dist/cjs.js??ref--11-0!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/src/views/app_pages/spv/Mesaje.vue?vue&type=style&index=0&lang=scss& ***!
  \***********************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {


var content = __webpack_require__(/*! !../../../../../../node_modules/css-loader/dist/cjs.js!../../../../../../node_modules/vue-loader/lib/loaders/stylePostLoader.js!../../../../../../node_modules/postcss-loader/src??ref--7-2!../../../../../../node_modules/sass-loader/dist/cjs.js??ref--7-3!../../../../../../node_modules/sass-loader/dist/cjs.js??ref--11-0!../../../../../../node_modules/vue-loader/lib??vue-loader-options!./Mesaje.vue?vue&type=style&index=0&lang=scss& */ "./node_modules/css-loader/dist/cjs.js!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src/index.js?!./node_modules/sass-loader/dist/cjs.js?!./node_modules/sass-loader/dist/cjs.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/app_pages/spv/Mesaje.vue?vue&type=style&index=0&lang=scss&");

if(typeof content === 'string') content = [[module.i, content, '']];

var transform;
var insertInto;



var options = {"hmr":true}

options.transform = transform
options.insertInto = undefined;

var update = __webpack_require__(/*! ../../../../../../node_modules/style-loader/lib/addStyles.js */ "./node_modules/style-loader/lib/addStyles.js")(content, options);

if(content.locals) module.exports = content.locals;

if(false) {}

/***/ }),

/***/ "./node_modules/style-loader/index.js!./node_modules/css-loader/dist/cjs.js!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src/index.js?!./node_modules/sass-loader/dist/cjs.js?!./node_modules/sass-loader/dist/cjs.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/app_pages/spv/Solicitari.vue?vue&type=style&index=0&lang=scss&":
/*!***************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/style-loader!./node_modules/css-loader/dist/cjs.js!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src??ref--7-2!./node_modules/sass-loader/dist/cjs.js??ref--7-3!./node_modules/sass-loader/dist/cjs.js??ref--11-0!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/src/views/app_pages/spv/Solicitari.vue?vue&type=style&index=0&lang=scss& ***!
  \***************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {


var content = __webpack_require__(/*! !../../../../../../node_modules/css-loader/dist/cjs.js!../../../../../../node_modules/vue-loader/lib/loaders/stylePostLoader.js!../../../../../../node_modules/postcss-loader/src??ref--7-2!../../../../../../node_modules/sass-loader/dist/cjs.js??ref--7-3!../../../../../../node_modules/sass-loader/dist/cjs.js??ref--11-0!../../../../../../node_modules/vue-loader/lib??vue-loader-options!./Solicitari.vue?vue&type=style&index=0&lang=scss& */ "./node_modules/css-loader/dist/cjs.js!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src/index.js?!./node_modules/sass-loader/dist/cjs.js?!./node_modules/sass-loader/dist/cjs.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/app_pages/spv/Solicitari.vue?vue&type=style&index=0&lang=scss&");

if(typeof content === 'string') content = [[module.i, content, '']];

var transform;
var insertInto;



var options = {"hmr":true}

options.transform = transform
options.insertInto = undefined;

var update = __webpack_require__(/*! ../../../../../../node_modules/style-loader/lib/addStyles.js */ "./node_modules/style-loader/lib/addStyles.js")(content, options);

if(content.locals) module.exports = content.locals;

if(false) {}

/***/ }),

/***/ "./node_modules/style-loader/index.js!./node_modules/css-loader/dist/cjs.js?!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/app_pages/spv/Certificate.vue?vue&type=style&index=0&id=2163fac4&scoped=true&lang=css&":
/*!**********************************************************************************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/style-loader!./node_modules/css-loader/dist/cjs.js??ref--6-1!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src??ref--6-2!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/src/views/app_pages/spv/Certificate.vue?vue&type=style&index=0&id=2163fac4&scoped=true&lang=css& ***!
  \**********************************************************************************************************************************************************************************************************************************************************************************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {


var content = __webpack_require__(/*! !../../../../../../node_modules/css-loader/dist/cjs.js??ref--6-1!../../../../../../node_modules/vue-loader/lib/loaders/stylePostLoader.js!../../../../../../node_modules/postcss-loader/src??ref--6-2!../../../../../../node_modules/vue-loader/lib??vue-loader-options!./Certificate.vue?vue&type=style&index=0&id=2163fac4&scoped=true&lang=css& */ "./node_modules/css-loader/dist/cjs.js?!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/app_pages/spv/Certificate.vue?vue&type=style&index=0&id=2163fac4&scoped=true&lang=css&");

if(typeof content === 'string') content = [[module.i, content, '']];

var transform;
var insertInto;



var options = {"hmr":true}

options.transform = transform
options.insertInto = undefined;

var update = __webpack_require__(/*! ../../../../../../node_modules/style-loader/lib/addStyles.js */ "./node_modules/style-loader/lib/addStyles.js")(content, options);

if(content.locals) module.exports = content.locals;

if(false) {}

/***/ }),

/***/ "./node_modules/style-loader/index.js!./node_modules/css-loader/dist/cjs.js?!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/app_pages/spv/Declaratii.vue?vue&type=style&index=0&id=fcac4d92&scoped=true&lang=css&":
/*!*********************************************************************************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/style-loader!./node_modules/css-loader/dist/cjs.js??ref--6-1!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src??ref--6-2!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/src/views/app_pages/spv/Declaratii.vue?vue&type=style&index=0&id=fcac4d92&scoped=true&lang=css& ***!
  \*********************************************************************************************************************************************************************************************************************************************************************************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {


var content = __webpack_require__(/*! !../../../../../../node_modules/css-loader/dist/cjs.js??ref--6-1!../../../../../../node_modules/vue-loader/lib/loaders/stylePostLoader.js!../../../../../../node_modules/postcss-loader/src??ref--6-2!../../../../../../node_modules/vue-loader/lib??vue-loader-options!./Declaratii.vue?vue&type=style&index=0&id=fcac4d92&scoped=true&lang=css& */ "./node_modules/css-loader/dist/cjs.js?!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/app_pages/spv/Declaratii.vue?vue&type=style&index=0&id=fcac4d92&scoped=true&lang=css&");

if(typeof content === 'string') content = [[module.i, content, '']];

var transform;
var insertInto;



var options = {"hmr":true}

options.transform = transform
options.insertInto = undefined;

var update = __webpack_require__(/*! ../../../../../../node_modules/style-loader/lib/addStyles.js */ "./node_modules/style-loader/lib/addStyles.js")(content, options);

if(content.locals) module.exports = content.locals;

if(false) {}

/***/ }),

/***/ "./node_modules/style-loader/index.js!./node_modules/css-loader/dist/cjs.js?!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/app_pages/spv/Societati.vue?vue&type=style&index=0&id=e2aaca68&scoped=true&lang=css&":
/*!********************************************************************************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/style-loader!./node_modules/css-loader/dist/cjs.js??ref--6-1!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src??ref--6-2!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/src/views/app_pages/spv/Societati.vue?vue&type=style&index=0&id=e2aaca68&scoped=true&lang=css& ***!
  \********************************************************************************************************************************************************************************************************************************************************************************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {


var content = __webpack_require__(/*! !../../../../../../node_modules/css-loader/dist/cjs.js??ref--6-1!../../../../../../node_modules/vue-loader/lib/loaders/stylePostLoader.js!../../../../../../node_modules/postcss-loader/src??ref--6-2!../../../../../../node_modules/vue-loader/lib??vue-loader-options!./Societati.vue?vue&type=style&index=0&id=e2aaca68&scoped=true&lang=css& */ "./node_modules/css-loader/dist/cjs.js?!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/app_pages/spv/Societati.vue?vue&type=style&index=0&id=e2aaca68&scoped=true&lang=css&");

if(typeof content === 'string') content = [[module.i, content, '']];

var transform;
var insertInto;



var options = {"hmr":true}

options.transform = transform
options.insertInto = undefined;

var update = __webpack_require__(/*! ../../../../../../node_modules/style-loader/lib/addStyles.js */ "./node_modules/style-loader/lib/addStyles.js")(content, options);

if(content.locals) module.exports = content.locals;

if(false) {}

/***/ }),

/***/ "./node_modules/style-loader/index.js!./node_modules/css-loader/dist/cjs.js?!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/app_pages/spv/Utilizatori.vue?vue&type=style&index=0&id=17852987&scoped=true&lang=css&":
/*!**********************************************************************************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/style-loader!./node_modules/css-loader/dist/cjs.js??ref--6-1!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src??ref--6-2!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/src/views/app_pages/spv/Utilizatori.vue?vue&type=style&index=0&id=17852987&scoped=true&lang=css& ***!
  \**********************************************************************************************************************************************************************************************************************************************************************************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {


var content = __webpack_require__(/*! !../../../../../../node_modules/css-loader/dist/cjs.js??ref--6-1!../../../../../../node_modules/vue-loader/lib/loaders/stylePostLoader.js!../../../../../../node_modules/postcss-loader/src??ref--6-2!../../../../../../node_modules/vue-loader/lib??vue-loader-options!./Utilizatori.vue?vue&type=style&index=0&id=17852987&scoped=true&lang=css& */ "./node_modules/css-loader/dist/cjs.js?!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/app_pages/spv/Utilizatori.vue?vue&type=style&index=0&id=17852987&scoped=true&lang=css&");

if(typeof content === 'string') content = [[module.i, content, '']];

var transform;
var insertInto;



var options = {"hmr":true}

options.transform = transform
options.insertInto = undefined;

var update = __webpack_require__(/*! ../../../../../../node_modules/style-loader/lib/addStyles.js */ "./node_modules/style-loader/lib/addStyles.js")(content, options);

if(content.locals) module.exports = content.locals;

if(false) {}

/***/ }),

/***/ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/app_pages/Spv.vue?vue&type=template&id=50cb4596&scoped=true&":
/*!***************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/src/views/app_pages/Spv.vue?vue&type=template&id=50cb4596&scoped=true& ***!
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
  return _c(
    "div",
    { staticClass: "modul-spv" },
    [
      _c(
        "b-card",
        { attrs: { "no-body": "" } },
        [
          _c(
            "b-tabs",
            {
              attrs: { card: "" },
              model: {
                value: _vm.tabActiv,
                callback: function ($$v) {
                  _vm.tabActiv = $$v
                },
                expression: "tabActiv",
              },
            },
            _vm._l(_vm.taburiVizibile, function (tab) {
              return _c(
                "b-tab",
                {
                  key: tab.cheie,
                  staticClass: "pt-0",
                  attrs: { title: tab.titlu, lazy: "" },
                },
                [_c(tab.componenta, { tag: "component" })],
                1
              )
            }),
            1
          ),
          _vm._v(" "),
          !_vm.taburiVizibile.length
            ? _c("div", { staticClass: "p-3 text-muted" }, [
                _vm._v(
                  "\n      Nu aveți drepturi pentru nicio operațiune din acest modul.\n    "
                ),
              ])
            : _vm._e(),
        ],
        1
      ),
      _vm._v(" "),
      _c("cereri-de-pin"),
    ],
    1
  )
}
var staticRenderFns = []
render._withStripped = true



/***/ }),

/***/ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/app_pages/spv/CereriDePin.vue?vue&type=template&id=6119d22f&":
/*!***************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/src/views/app_pages/spv/CereriDePin.vue?vue&type=template&id=6119d22f& ***!
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
  return _c(
    "b-modal",
    {
      attrs: {
        title: "Tokenul „" + (_vm.tokenul.cn || "") + "” își așteaptă PIN-ul",
        "ok-title": "Trimite codul",
        "cancel-title": "Îl scriu acolo",
        "ok-variant": "primary",
        "modal-class": "modul-spv",
        "ok-disabled": !_vm.pin || _vm.inCurs,
      },
      on: {
        ok: function ($event) {
          $event.preventDefault()
          return _vm.trimite.apply(null, arguments)
        },
        hidden: _vm.uita,
      },
      model: {
        value: _vm.vizibil,
        callback: function ($$v) {
          _vm.vizibil = $$v
        },
        expression: "vizibil",
      },
    },
    [
      _c("p", { staticClass: "mb-2" }, [
        _vm._v(
          "\n    Pe calculatorul clientului stă deschisă o fereastră care cere PIN-ul\n    tokenului. Până nu e scris acolo, lucrarea nu poate merge mai departe.\n  "
        ),
      ]),
      _vm._v(" "),
      _vm.tokenul.fereastra
        ? _c("p", { staticClass: "text-muted small mb-2" }, [
            _vm._v("\n    Fereastra: "),
            _c("code", [_vm._v(_vm._s(_vm.tokenul.fereastra))]),
            _vm._v(" "),
            _vm.tokenul.de_cand
              ? _c("span", [_vm._v(" · " + _vm._s(_vm.tokenul.de_cand))])
              : _vm._e(),
          ])
        : _vm._e(),
      _vm._v(" "),
      _c("label", [_vm._v("PIN-ul tokenului")]),
      _vm._v(" "),
      _c("b-form-input", {
        ref: "camp",
        attrs: {
          type: "password",
          autocomplete: "off",
          disabled: _vm.inCurs,
          placeholder: "codul se scrie o singură dată",
        },
        on: {
          keyup: function ($event) {
            if (
              !$event.type.indexOf("key") &&
              _vm._k($event.keyCode, "enter", 13, $event.key, "Enter")
            ) {
              return null
            }
            return _vm.trimite.apply(null, arguments)
          },
        },
        model: {
          value: _vm.pin,
          callback: function ($$v) {
            _vm.pin = $$v
          },
          expression: "pin",
        },
      }),
      _vm._v(" "),
      _c("small", { staticClass: "d-block text-muted mt-1" }, [
        _vm._v(
          "\n    Codul pleacă o singură dată, prin cererea aceasta, până la programul local\n    de pe calculatorul clientului, care îl scrie în fereastra deschisă. Nu se\n    păstrează nicăieri — nici aici, nici pe server — și nu intră în jurnal.\n  "
        ),
      ]),
      _vm._v(" "),
      _c(
        "b-alert",
        {
          staticClass: "mt-2 mb-0 p-1 small",
          attrs: { show: Boolean(_vm.eroare), variant: "danger" },
        },
        [_vm._v("\n    " + _vm._s(_vm.eroare) + "\n  ")]
      ),
    ],
    1
  )
}
var staticRenderFns = []
render._withStripped = true



/***/ }),

/***/ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/app_pages/spv/Certificate.vue?vue&type=template&id=2163fac4&scoped=true&":
/*!***************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/src/views/app_pages/spv/Certificate.vue?vue&type=template&id=2163fac4&scoped=true& ***!
  \***************************************************************************************************************************************************************************************************************************************/
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
            { staticClass: "mb-0", attrs: { md: "6" } },
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
                      "\n            Citește token-urile conectate\n          "
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
                            attrs: {
                              size: "sm",
                              placeholder: "Adresa calculatorului cu token-ul",
                            },
                            model: {
                              value: _vm.bridgeNou.bridge_url,
                              callback: function ($$v) {
                                _vm.$set(_vm.bridgeNou, "bridge_url", $$v)
                              },
                              expression: "bridgeNou.bridge_url",
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
                            attrs: { size: "sm", placeholder: "Cod de acces" },
                            model: {
                              value: _vm.bridgeNou.bridge_token,
                              callback: function ($$v) {
                                _vm.$set(_vm.bridgeNou, "bridge_token", $$v)
                              },
                              expression: "bridgeNou.bridge_token",
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
                            disabled: _vm.sincronizareInCurs,
                          },
                          on: { click: _vm.descopera },
                        },
                        [
                          _vm.sincronizareInCurs
                            ? _c("b-spinner", {
                                staticClass: "mr-1",
                                attrs: { small: "" },
                              })
                            : _vm._e(),
                          _vm._v("\n              Citește\n            "),
                        ],
                        1
                      ),
                      _vm._v(" "),
                      _c("small", { staticClass: "text-muted ml-2" }, [
                        _vm._v(
                          "\n              Gol = calculatorul din configurație. Reapăsați după schimbarea token-ului.\n            "
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
            { staticClass: "mb-0", attrs: { md: "6" } },
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
                      "\n            Calculator nou cu token?\n          "
                    ),
                  ]),
                  _vm._v(" "),
                  _c("small", { staticClass: "text-muted d-block mb-2" }, [
                    _vm._v("\n            Dezarhivați kitul în folderul "),
                    _c("code", [_vm._v("C:\\DianaSoft_SPV_Curier")]),
                    _vm._v(" și rulați cu Run As Administrator\n            "),
                    _c("code", [_vm._v("instaleaza.bat")]),
                    _vm._v(
                      " din kit pe acel calculator: programul pornește\n            apoi automat la fiecare autentificare. Fiecare kit are cod de acces propriu.\n          "
                    ),
                  ]),
                  _vm._v(" "),
                  _c(
                    "b-button",
                    {
                      attrs: {
                        variant: "outline-primary",
                        size: "sm",
                        disabled: _vm.kitInCurs,
                      },
                      on: { click: _vm.descarcaKit },
                    },
                    [
                      _vm.kitInCurs
                        ? _c("b-spinner", {
                            staticClass: "mr-1",
                            attrs: { small: "" },
                          })
                        : _vm._e(),
                      _vm._v(
                        "\n            Descarcă kitul de instalare a programului de acces la certificatul digital\n          "
                      ),
                    ],
                    1
                  ),
                  _vm._v(" "),
                  _c(
                    "b-button",
                    {
                      staticClass: "mt-1",
                      attrs: { variant: "flat-warning", size: "sm" },
                      on: {
                        click: function ($event) {
                          _vm.ajutorVizibil = true
                        },
                      },
                    },
                    [
                      _c("feather-icon", {
                        staticClass: "mr-25",
                        attrs: { icon: "HelpCircleIcon", size: "14" },
                      }),
                      _vm._v(
                        "\n            Nu comunică? Firewall și antivirus\n          "
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
      _vm._v(" "),
      _vm.info
        ? _c(
            "b-alert",
            { staticClass: "py-2", attrs: { show: "", variant: "info" } },
            [_vm._v("\n      " + _vm._s(_vm.info) + "\n    ")]
          )
        : _vm._e(),
      _vm._v(" "),
      _vm.eroare
        ? _c(
            "b-alert",
            { staticClass: "py-2", attrs: { show: "", variant: "danger" } },
            [_vm._v("\n      " + _vm._s(_vm.eroare) + "\n    ")]
          )
        : _vm._e(),
      _vm._v(" "),
      _c(
        "b-card",
        { staticClass: "border mb-2", attrs: { "body-class": "p-2" } },
        [
          _c("b-table", {
            staticClass: "mb-0",
            attrs: {
              items: _vm.certificate,
              fields: _vm.campuri,
              busy: _vm.listaInCurs,
              "tbody-tr-class": _vm.clasaRand,
              responsive: "",
              striped: "",
              small: "",
              "show-empty": "",
              "empty-text":
                "Niciun certificat înregistrat. Apăsați „Citește token-urile conectate”.",
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
                        _vm._v("\n            Se încarcă...\n          "),
                      ],
                      1
                    ),
                  ]
                },
                proxy: true,
              },
              {
                key: "cell(titular)",
                fn: function (rand) {
                  return [
                    _c("div", [_vm._v(_vm._s(rand.item.cn || "-"))]),
                    _vm._v(" "),
                    _c("div", { staticClass: "small text-muted" }, [
                      _vm._v(
                        "\n            " +
                          _vm._s(rand.item.email || "") +
                          "\n          "
                      ),
                    ]),
                  ]
                },
              },
              {
                key: "cell(emitent)",
                fn: function (rand) {
                  return [
                    _c("div", { staticClass: "small" }, [
                      _vm._v(
                        "\n            " +
                          _vm._s(_vm.scurt(rand.item.emitent)) +
                          "\n          "
                      ),
                    ]),
                    _vm._v(" "),
                    _c("div", { staticClass: "small text-muted" }, [
                      _vm._v(
                        "\n            serie " +
                          _vm._s(rand.item.serie || "-") +
                          "\n          "
                      ),
                    ]),
                  ]
                },
              },
              {
                key: "cell(bridge)",
                fn: function (rand) {
                  return [
                    _c(
                      "div",
                      { staticClass: "d-flex align-items-center" },
                      [
                        _c(
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
                            staticClass: "btn-icon mr-2",
                            attrs: {
                              size: "sm",
                              variant: "outline-secondary",
                              title:
                                "Configurează calculatorul și codul de acces",
                            },
                            on: {
                              click: function ($event) {
                                return _vm.deschideBridge(rand.item)
                              },
                            },
                          },
                          [
                            _c("feather-icon", {
                              attrs: { icon: "SettingsIcon" },
                            }),
                          ],
                          1
                        ),
                        _vm._v(" "),
                        _c(
                          "div",
                          [
                            _c("div", { staticClass: "small" }, [
                              _vm._v(
                                "\n                " +
                                  _vm._s(rand.item.bridge_url) +
                                  "\n              "
                              ),
                            ]),
                            _vm._v(" "),
                            rand.item.arhiva_cale
                              ? _c(
                                  "div",
                                  { staticClass: "small text-muted" },
                                  [
                                    _c("feather-icon", {
                                      staticClass: "mr-25",
                                      attrs: { icon: "FolderIcon", size: "12" },
                                    }),
                                    _vm._v(
                                      _vm._s(rand.item.arhiva_cale) +
                                        "\n              "
                                    ),
                                  ],
                                  1
                                )
                              : _vm._e(),
                            _vm._v(" "),
                            rand.item.monitorizare_activa &&
                            rand.item.monitorizare_cale
                              ? _c(
                                  "div",
                                  { staticClass: "small text-muted" },
                                  [
                                    _c("feather-icon", {
                                      staticClass: "mr-25",
                                      attrs: { icon: "EyeIcon", size: "12" },
                                    }),
                                    _vm._v(
                                      _vm._s(rand.item.monitorizare_cale) +
                                        "\n              "
                                    ),
                                  ],
                                  1
                                )
                              : _vm._e(),
                            _vm._v(" "),
                            rand.item.mod_legatura === "tunel"
                              ? _c(
                                  "div",
                                  {
                                    staticClass: "small",
                                    class: rand.item.agent_treaz
                                      ? "text-success"
                                      : "text-danger",
                                  },
                                  [
                                    _c("feather-icon", {
                                      staticClass: "mr-25",
                                      attrs: {
                                        icon: rand.item.agent_treaz
                                          ? "WifiIcon"
                                          : "WifiOffIcon",
                                        size: "12",
                                      },
                                    }),
                                    _vm._v(
                                      _vm._s(
                                        rand.item.agent_treaz
                                          ? "prin tunel, program pornit"
                                          : rand.item.agent_vazut_la
                                          ? "prin tunel, oprit din " +
                                            rand.item.agent_vazut_la
                                          : "prin tunel, încă nepornit"
                                      ) + "\n              "
                                    ),
                                  ],
                                  1
                                )
                              : _vm._e(),
                            _vm._v(" "),
                            rand.item.versiune_bridge
                              ? _c(
                                  "div",
                                  {
                                    staticClass: "small",
                                    class:
                                      rand.item.versiune_bridge ===
                                      _vm.versiuneProgram
                                        ? "text-muted"
                                        : "text-warning",
                                  },
                                  [
                                    _c("feather-icon", {
                                      staticClass: "mr-25",
                                      attrs: {
                                        icon:
                                          rand.item.versiune_bridge ===
                                          _vm.versiuneProgram
                                            ? "CheckCircleIcon"
                                            : "DownloadIcon",
                                        size: "12",
                                      },
                                    }),
                                    _vm._v(
                                      _vm._s(
                                        rand.item.versiune_bridge ===
                                          _vm.versiuneProgram
                                          ? "program la zi"
                                          : "program vechi — se înnoiește singur"
                                      ) + "\n              "
                                    ),
                                  ],
                                  1
                                )
                              : _vm._e(),
                            _vm._v(" "),
                            rand.item.licenta_pana_la
                              ? _c(
                                  "div",
                                  { staticClass: "small text-muted" },
                                  [
                                    _c("feather-icon", {
                                      staticClass: "mr-25",
                                      attrs: { icon: "KeyIcon", size: "12" },
                                    }),
                                    _vm._v(
                                      "licență până la " +
                                        _vm._s(rand.item.licenta_pana_la) +
                                        "\n              "
                                    ),
                                  ],
                                  1
                                )
                              : _vm._e(),
                            _vm._v(" "),
                            rand.item.pin_stare
                              ? _c(
                                  "div",
                                  {
                                    staticClass: "small",
                                    class:
                                      rand.item.pin_stare === "gata"
                                        ? "text-success"
                                        : "text-danger",
                                    attrs: {
                                      title:
                                        rand.item.pin_motiv ||
                                        "Verificat la " +
                                          (rand.item.pin_verificat_la || "—"),
                                    },
                                  },
                                  [
                                    _c("feather-icon", {
                                      staticClass: "mr-25",
                                      attrs: {
                                        icon:
                                          rand.item.pin_stare === "gata"
                                            ? "UnlockIcon"
                                            : "LockIcon",
                                        size: "12",
                                      },
                                    }),
                                    _vm._v(
                                      _vm._s(_vm.textulPinului(rand.item)) +
                                        "\n              "
                                    ),
                                  ],
                                  1
                                )
                              : _vm._e(),
                            _vm._v(" "),
                            !rand.item.activ
                              ? _c(
                                  "b-badge",
                                  { attrs: { variant: "secondary" } },
                                  [
                                    _vm._v(
                                      "\n                scos din uz\n              "
                                    ),
                                  ]
                                )
                              : _vm._e(),
                            _vm._v(" "),
                            rand.item.implicit
                              ? _c(
                                  "b-badge",
                                  { attrs: { variant: "primary" } },
                                  [
                                    _vm._v(
                                      "\n                implicit\n              "
                                    ),
                                  ]
                                )
                              : _vm._e(),
                            _vm._v(" "),
                            _vm.certificatActiv === rand.item.id &&
                            rand.item.activ
                              ? _c(
                                  "b-badge",
                                  { attrs: { variant: "success" } },
                                  [
                                    _vm._v(
                                      "\n                activ acum\n              "
                                    ),
                                  ]
                                )
                              : _vm._e(),
                          ],
                          1
                        ),
                        _vm._v(" "),
                        _c(
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
                            staticClass: "btn-icon ml-2",
                            attrs: {
                              size: "sm",
                              variant: "outline-secondary",
                              disabled:
                                _vm.licentaInCurs === rand.item.id ||
                                !rand.item.activ,
                              title:
                                "Reînnoiește acum licența programului local",
                            },
                            on: {
                              click: function ($event) {
                                return _vm.reinnoiesteLicenta(rand.item)
                              },
                            },
                          },
                          [
                            _vm.licentaInCurs === rand.item.id
                              ? _c("b-spinner", { attrs: { small: "" } })
                              : _c("feather-icon", {
                                  attrs: { icon: "KeyIcon" },
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
                            staticClass: "btn-icon ml-2",
                            attrs: {
                              size: "sm",
                              variant: "outline-success",
                              disabled:
                                _vm.certificatActiv === rand.item.id ||
                                !rand.item.activ,
                              title:
                                "Folosește acest certificat pentru operațiile mele",
                            },
                            on: {
                              click: function ($event) {
                                return _vm.alegeActiv(rand.item)
                              },
                            },
                          },
                          [
                            _c("feather-icon", {
                              attrs: { icon: "CheckCircleIcon" },
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
                            staticClass: "btn-icon ml-2",
                            attrs: {
                              size: "sm",
                              variant: rand.item.activ
                                ? "outline-danger"
                                : "outline-primary",
                              disabled: _vm.activareInCurs === rand.item.id,
                              title: rand.item.activ
                                ? "Scoate certificatul din uz — aplicația îl va ignora"
                                : "Repune certificatul în uz",
                            },
                            on: {
                              click: function ($event) {
                                return _vm.comutaActiv(rand.item)
                              },
                            },
                          },
                          [
                            _vm.activareInCurs === rand.item.id
                              ? _c("b-spinner", { attrs: { small: "" } })
                              : _c("feather-icon", {
                                  attrs: {
                                    icon: rand.item.activ
                                      ? "PowerIcon"
                                      : "RefreshCwIcon",
                                  },
                                }),
                          ],
                          1
                        ),
                      ],
                      1
                    ),
                  ]
                },
              },
              {
                key: "cell(valabilitate)",
                fn: function (rand) {
                  return [
                    _c("div", [
                      _vm._v(_vm._s(rand.item.valabil_pana_la || "-")),
                    ]),
                    _vm._v(" "),
                    _c(
                      "b-badge",
                      { attrs: { variant: _vm.variantaExpirare(rand.item) } },
                      [
                        _vm._v(
                          "\n            " +
                            _vm._s(_vm.textExpirare(rand.item)) +
                            "\n          "
                        ),
                      ]
                    ),
                  ]
                },
              },
              {
                key: "cell(entitati)",
                fn: function (rand) {
                  return [
                    _vm._v(
                      "\n          " + _vm._s(rand.item.entitati) + "\n        "
                    ),
                  ]
                },
              },
              {
                key: "cell(utilizatori)",
                fn: function (rand) {
                  return [
                    rand.item.utilizatori.length
                      ? _c("div", { staticClass: "small" }, [
                          _vm._v(
                            "\n            " +
                              _vm._s(rand.item.utilizatori[0].email) +
                              "\n            "
                          ),
                          rand.item.utilizatori.length > 1
                            ? _c("span", [
                                _vm._v(
                                  "\n              +" +
                                    _vm._s(rand.item.utilizatori.length - 1) +
                                    "\n            "
                                ),
                              ])
                            : _vm._e(),
                        ])
                      : _c("span", { staticClass: "small text-muted" }, [
                          _vm._v("niciunul"),
                        ]),
                    _vm._v(" "),
                    _c(
                      "b-button",
                      {
                        staticClass: "mt-1",
                        attrs: { size: "sm", variant: "outline-primary" },
                        on: {
                          click: function ($event) {
                            return _vm.deschideUtilizatori(rand.item)
                          },
                        },
                      },
                      [_vm._v("\n            Gestionează\n          ")]
                    ),
                  ]
                },
              },
              {
                key: "cell(avertizare)",
                fn: function (rand) {
                  return [
                    rand.item.avertizat_la
                      ? _c("span", { staticClass: "small" }, [
                          _vm._v(
                            "\n            trimisă la " +
                              _vm._s(rand.item.avertizat_la) +
                              "\n          "
                          ),
                        ])
                      : _c("span", { staticClass: "small text-muted" }, [
                          _vm._v("—"),
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
        "b-card",
        { staticClass: "border mb-0", attrs: { "body-class": "p-2" } },
        [
          _c("h6", { staticClass: "mb-2" }, [
            _vm._v("\n        Avertizare expirare pe email\n      "),
          ]),
          _vm._v(" "),
          _c(
            "b-row",
            {
              staticClass: "align-items-center mb-2",
              attrs: { "no-gutters": "" },
            },
            [
              _c(
                "b-col",
                { staticClass: "pr-1", attrs: { md: "4" } },
                [
                  _c("b-form-input", {
                    attrs: {
                      type: "email",
                      size: "sm",
                      placeholder: "Adresă de email",
                    },
                    model: {
                      value: _vm.emailNou,
                      callback: function ($$v) {
                        _vm.emailNou = $$v
                      },
                      expression: "emailNou",
                    },
                  }),
                ],
                1
              ),
              _vm._v(" "),
              _c(
                "b-col",
                { staticClass: "pr-1", attrs: { md: "4" } },
                [
                  _c("b-form-select", {
                    attrs: { options: _vm.optiuniCertificat, size: "sm" },
                    model: {
                      value: _vm.certificatAles,
                      callback: function ($$v) {
                        _vm.certificatAles = $$v
                      },
                      expression: "certificatAles",
                    },
                  }),
                ],
                1
              ),
              _vm._v(" "),
              _c(
                "b-col",
                { attrs: { md: "4" } },
                [
                  _c(
                    "b-button",
                    {
                      attrs: {
                        variant: "primary",
                        size: "sm",
                        disabled: !_vm.emailNou,
                      },
                      on: { click: _vm.aboneaza },
                    },
                    [_vm._v("\n            Înscrie\n          ")]
                  ),
                  _vm._v(" "),
                  _c("small", { staticClass: "text-muted ml-2" }, [
                    _vm._v(
                      "\n            Avertizare cu " +
                        _vm._s(_vm.zileAvertizare) +
                        " de zile înainte, repetată până la înlocuire.\n          "
                    ),
                  ]),
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
              items: _vm.abonati,
              fields: _vm.campuriAbonati,
              responsive: "",
              striped: "",
              small: "",
              "show-empty": "",
              "empty-text":
                "Nicio adresă înscrisă — avertizările de expirare nu vor fi trimise.",
            },
            scopedSlots: _vm._u([
              {
                key: "cell(certificat_id)",
                fn: function (rand) {
                  return [
                    _vm._v(
                      "\n          " +
                        _vm._s(_vm.numeCertificat(rand.item.certificat_id)) +
                        "\n        "
                    ),
                  ]
                },
              },
              {
                key: "cell(actiuni)",
                fn: function (rand) {
                  return [
                    _c(
                      "b-button",
                      {
                        attrs: { size: "sm", variant: "outline-danger" },
                        on: {
                          click: function ($event) {
                            return _vm.dezaboneaza(rand.item)
                          },
                        },
                      },
                      [_vm._v("\n            Șterge\n          ")]
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
        "b-modal",
        {
          attrs: {
            title:
              "Calculatorul certificatului " + (_vm.bridgeFormular.cn || ""),
            "ok-title": "Salvează",
            "cancel-title": "Renunță",
            "modal-class": "modul-spv",
          },
          on: { ok: _vm.salveazaBridge },
          model: {
            value: _vm.bridgeVizibil,
            callback: function ($$v) {
              _vm.bridgeVizibil = $$v
            },
            expression: "bridgeVizibil",
          },
        },
        [
          _c("p", { staticClass: "text-muted small" }, [
            _vm._v(
              "\n        Calculatorul din rețea pe care este conectat acest token. Lăsat gol, se folosește\n        cel din configurația aplicației.\n      "
            ),
          ]),
          _vm._v(" "),
          _c("label", [_vm._v("Adresa calculatorului")]),
          _vm._v(" "),
          _c("b-form-input", {
            staticClass: "mb-3",
            attrs: { placeholder: "http://192.168.1.20:8099" },
            model: {
              value: _vm.bridgeFormular.bridge_url,
              callback: function ($$v) {
                _vm.$set(_vm.bridgeFormular, "bridge_url", $$v)
              },
              expression: "bridgeFormular.bridge_url",
            },
          }),
          _vm._v(" "),
          _c("label", [_vm._v("Cod de acces (gol = cel din configurație)")]),
          _vm._v(" "),
          _c("b-form-input", {
            staticClass: "mb-3",
            model: {
              value: _vm.bridgeFormular.bridge_token,
              callback: function ($$v) {
                _vm.$set(_vm.bridgeFormular, "bridge_token", $$v)
              },
              expression: "bridgeFormular.bridge_token",
            },
          }),
          _vm._v(" "),
          _c("label", [_vm._v("Dosarul arhivei pe acel calculator")]),
          _vm._v(" "),
          _c(
            "b-input-group",
            { staticClass: "mb-1" },
            [
              _c("b-form-input", {
                attrs: { placeholder: "D:\\Documente fiscale" },
                model: {
                  value: _vm.bridgeFormular.arhiva_cale,
                  callback: function ($$v) {
                    _vm.$set(_vm.bridgeFormular, "arhiva_cale", $$v)
                  },
                  expression: "bridgeFormular.arhiva_cale",
                },
              }),
              _vm._v(" "),
              _c(
                "b-input-group-append",
                [
                  _c(
                    "b-button",
                    {
                      attrs: { variant: "outline-secondary" },
                      on: {
                        click: function ($event) {
                          return _vm.deschideFoldere("arhiva_cale")
                        },
                      },
                    },
                    [
                      _c("feather-icon", {
                        staticClass: "mr-50",
                        attrs: { icon: "FolderIcon" },
                      }),
                      _vm._v("Alege...\n          "),
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
          _c("small", { staticClass: "text-muted d-block mb-3" }, [
            _vm._v(
              "\n        Aici se strâng declarațiile semnate, recipisele și documentele aduse din SPV,\n        pe firme și pe tipuri. Merge și un folder din rețea\n        ("
            ),
            _c("code", [_vm._v("\\\\server\\arhiva")]),
            _vm._v("). Lăsat gol, se folosește dosarul scris în\n        "),
            _c("code", [_vm._v("bridge.env")]),
            _vm._v(" pe acel calculator.\n      "),
          ]),
          _vm._v(" "),
          _c(
            "b-form-group",
            { attrs: { label: "Legătura cu acest calculator" } },
            [
              _c(
                "b-form-radio",
                {
                  staticClass: "mb-50",
                  attrs: { value: "direct" },
                  model: {
                    value: _vm.bridgeFormular.mod_legatura,
                    callback: function ($$v) {
                      _vm.$set(_vm.bridgeFormular, "mod_legatura", $$v)
                    },
                    expression: "bridgeFormular.mod_legatura",
                  },
                },
                [
                  _vm._v(
                    "\n          Directă — serverul îl caută la adresa de mai sus\n          "
                  ),
                  _c("small", { staticClass: "d-block text-muted" }, [
                    _vm._v(
                      "\n            Merge când aplicația și calculatorul sunt în aceeași rețea, sau când\n            acesta are adresă publică.\n          "
                    ),
                  ]),
                ]
              ),
              _vm._v(" "),
              _c(
                "b-form-radio",
                {
                  attrs: { value: "tunel" },
                  model: {
                    value: _vm.bridgeFormular.mod_legatura,
                    callback: function ($$v) {
                      _vm.$set(_vm.bridgeFormular, "mod_legatura", $$v)
                    },
                    expression: "bridgeFormular.mod_legatura",
                  },
                },
                [
                  _vm._v(
                    "\n          Prin tunel — programul întreabă singur serverul\n          "
                  ),
                  _c("small", { staticClass: "d-block text-muted" }, [
                    _vm._v(
                      "\n            Pentru calculatoare din spatele unui router. Nu se deschide niciun\n            port: legătura pleacă dinspre client, pe 443. Cere agentul din kitul\n            nou, instalat pe acel calculator.\n          "
                    ),
                  ]),
                ]
              ),
            ],
            1
          ),
          _vm._v(" "),
          _c("hr"),
          _vm._v(" "),
          _c(
            "b-form-checkbox",
            {
              staticClass: "mb-1",
              model: {
                value: _vm.bridgeFormular.monitorizare_activa,
                callback: function ($$v) {
                  _vm.$set(_vm.bridgeFormular, "monitorizare_activa", $$v)
                },
                expression: "bridgeFormular.monitorizare_activa",
              },
            },
            [
              _vm._v(
                "\n        Urmărește un dosar și prelucrează singur declarațiile puse acolo\n      "
              ),
            ]
          ),
          _vm._v(" "),
          _vm.bridgeFormular.monitorizare_activa
            ? _c(
                "b-input-group",
                { staticClass: "mb-1" },
                [
                  _c("b-form-input", {
                    attrs: { placeholder: "D:\\Declarații de semnat" },
                    model: {
                      value: _vm.bridgeFormular.monitorizare_cale,
                      callback: function ($$v) {
                        _vm.$set(_vm.bridgeFormular, "monitorizare_cale", $$v)
                      },
                      expression: "bridgeFormular.monitorizare_cale",
                    },
                  }),
                  _vm._v(" "),
                  _c(
                    "b-input-group-append",
                    [
                      _c(
                        "b-button",
                        {
                          attrs: { variant: "outline-secondary" },
                          on: {
                            click: function ($event) {
                              return _vm.deschideFoldere("monitorizare_cale")
                            },
                          },
                        },
                        [
                          _c("feather-icon", {
                            staticClass: "mr-50",
                            attrs: { icon: "FolderIcon" },
                          }),
                          _vm._v("Alege...\n          "),
                        ],
                        1
                      ),
                    ],
                    1
                  ),
                ],
                1
              )
            : _vm._e(),
          _vm._v(" "),
          _vm.bridgeFormular.monitorizare_activa
            ? _c(
                "b-form-group",
                {
                  staticClass: "mb-1",
                  attrs: { label: "Cât de des se verifică dosarul" },
                },
                [
                  _c("b-form-select", {
                    attrs: { options: _vm.cadenteMonitorizare },
                    model: {
                      value: _vm.bridgeFormular.monitorizare_cadenta,
                      callback: function ($$v) {
                        _vm.$set(
                          _vm.bridgeFormular,
                          "monitorizare_cadenta",
                          $$v
                        )
                      },
                      expression: "bridgeFormular.monitorizare_cadenta",
                    },
                  }),
                ],
                1
              )
            : _vm._e(),
          _vm._v(" "),
          _vm.bridgeFormular.monitorizare_activa
            ? _c(
                "b-form-checkbox",
                {
                  staticClass: "mb-50",
                  model: {
                    value: _vm.bridgeFormular.monitorizare_semneaza,
                    callback: function ($$v) {
                      _vm.$set(_vm.bridgeFormular, "monitorizare_semneaza", $$v)
                    },
                    expression: "bridgeFormular.monitorizare_semneaza",
                  },
                },
                [
                  _vm._v("\n        Semnează declarațiile valide\n        "),
                  _c("small", { staticClass: "d-block text-muted" }, [
                    _vm._v(
                      "\n          Nebifat, declarațiile doar se validează; semnarea rămâne de făcut\n          din fila Declarații fiscale.\n        "
                    ),
                  ]),
                ]
              )
            : _vm._e(),
          _vm._v(" "),
          _vm.bridgeFormular.monitorizare_activa
            ? _c(
                "b-form-checkbox",
                {
                  staticClass: "mb-1",
                  model: {
                    value: _vm.bridgeFormular.monitorizare_depune,
                    callback: function ($$v) {
                      _vm.$set(_vm.bridgeFormular, "monitorizare_depune", $$v)
                    },
                    expression: "bridgeFormular.monitorizare_depune",
                  },
                },
                [
                  _vm._v("\n        Depune declarațiile semnate\n        "),
                  _c("small", { staticClass: "d-block text-muted" }, [
                    _vm._v(
                      "\n          Pleacă la ANAF doar ce a ajuns semnat — semnat aici sau venit gata\n          semnat. Depunerea nu se mai poate lua înapoi.\n        "
                    ),
                  ]),
                ]
              )
            : _vm._e(),
          _vm._v(" "),
          _c("hr"),
          _vm._v(" "),
          _c(
            "b-form-checkbox",
            {
              staticClass: "mb-1",
              model: {
                value: _vm.bridgeFormular.pin_de_la_distanta,
                callback: function ($$v) {
                  _vm.$set(_vm.bridgeFormular, "pin_de_la_distanta", $$v)
                },
                expression: "bridgeFormular.pin_de_la_distanta",
              },
            },
            [
              _vm._v(
                "\n        Pot trimite PIN-ul acestui token din aplicație\n        "
              ),
              _c("small", { staticClass: "d-block text-muted" }, [
                _vm._v(
                  "\n          Când o lucrare se oprește fiindcă tokenul își cere PIN-ul, aplicația\n          îl cere aici și îl scrie în fereastra deschisă pe calculatorul\n          clientului. Codul trece o singură dată și nu se păstrează nicăieri —\n          nici în aplicație, nici pe server.\n        "
                ),
              ]),
              _vm._v(" "),
              _c("small", { staticClass: "d-block text-warning mt-25" }, [
                _vm._v(
                  "\n          Nebifat, aplicația doar vă spune care token așteaptă, iar codul se\n          scrie de mână, acolo. Bifați numai dacă tokenul e al dumneavoastră\n          sau aveți învoirea celui care răspunde de el: PIN-ul e dovada că\n          semnătura vă aparține.\n        "
                ),
              ]),
            ]
          ),
          _vm._v(" "),
          _vm.bridgeFormular.monitorizare_activa
            ? _c("small", { staticClass: "text-muted d-block mb-3" }, [
                _vm._v(
                  "\n        La cadența aleasă, declarațiile puse acolo — XML sau PDF — se încarcă\n        și se validează singure, apoi, după bifele de mai sus, se semnează și\n        se depun; la final trec în subdosarul\n        "
                ),
                _c("code", [_vm._v("prelucrate")]),
                _vm._v(
                  ". Un PDF venit deja semnat nu se mai semnează încă\n        o dată. Ce nu trece de validare ajunge în\n        "
                ),
                _c("code", [_vm._v("erori")]),
                _vm._v(
                  ", iar utilizatorii atașați certificatului firmei\n        primesc email cu motivul.\n        "
                ),
                _vm.bridgeFormular.monitorizare_la
                  ? _c("span", { staticClass: "d-block" }, [
                      _vm._v(
                        "Ultima verificare: " +
                          _vm._s(_vm.bridgeFormular.monitorizare_la)
                      ),
                    ])
                  : _vm._e(),
              ])
            : _vm._e(),
          _vm._v(" "),
          _c(
            "b-form-checkbox",
            {
              model: {
                value: _vm.bridgeFormular.implicit,
                callback: function ($$v) {
                  _vm.$set(_vm.bridgeFormular, "implicit", $$v)
                },
                expression: "bridgeFormular.implicit",
              },
            },
            [
              _vm._v(
                "\n        Certificat implicit (folosit când utilizatorul nu are unul atribuit)\n      "
              ),
            ]
          ),
        ],
        1
      ),
      _vm._v(" "),
      _c(
        "b-modal",
        {
          attrs: {
            title: "Alegeți dosarul arhivei",
            "ok-title": "Alege acest dosar",
            "cancel-title": "Renunță",
            "ok-disabled": !_vm.folderCurent,
            "modal-class": "modul-spv",
          },
          on: { ok: _vm.alegeFolder },
          model: {
            value: _vm.foldereVizibil,
            callback: function ($$v) {
              _vm.foldereVizibil = $$v
            },
            expression: "foldereVizibil",
          },
        },
        [
          _vm.eroareFoldere
            ? _c(
                "b-alert",
                {
                  staticClass: "py-1 px-2 small",
                  attrs: { show: "", variant: "danger" },
                },
                [_vm._v("\n        " + _vm._s(_vm.eroareFoldere) + "\n      ")]
              )
            : _vm._e(),
          _vm._v(" "),
          _c(
            "div",
            { staticClass: "d-flex align-items-center mb-1" },
            [
              _c(
                "b-button",
                {
                  staticClass: "btn-icon mr-1",
                  attrs: {
                    size: "sm",
                    variant: "outline-secondary",
                    disabled: _vm.folderParinte === null,
                    title: "Un nivel mai sus",
                  },
                  on: {
                    click: function ($event) {
                      return _vm.rasfoieste(_vm.folderParinte)
                    },
                  },
                },
                [_c("feather-icon", { attrs: { icon: "CornerLeftUpIcon" } })],
                1
              ),
              _vm._v(" "),
              _c("code", { staticClass: "small" }, [
                _vm._v(_vm._s(_vm.folderCurent || "Acest calculator")),
              ]),
              _vm._v(" "),
              _vm.foldereInCurs
                ? _c("b-spinner", { staticClass: "ml-1", attrs: { small: "" } })
                : _vm._e(),
            ],
            1
          ),
          _vm._v(" "),
          _c(
            "b-list-group",
            { staticClass: "lista-foldere" },
            [
              _vm._l(_vm.foldere, function (folder) {
                return _c(
                  "b-list-group-item",
                  {
                    key: folder.cale,
                    staticClass: "py-50 px-1 small",
                    attrs: { button: "" },
                    on: {
                      click: function ($event) {
                        return _vm.rasfoieste(folder.cale)
                      },
                    },
                  },
                  [
                    _c("feather-icon", {
                      staticClass: "mr-50 text-muted",
                      attrs: {
                        icon: _vm.folderCurent ? "FolderIcon" : "HardDriveIcon",
                        size: "14",
                      },
                    }),
                    _vm._v(_vm._s(folder.nume) + "\n        "),
                  ],
                  1
                )
              }),
              _vm._v(" "),
              !_vm.foldere.length && !_vm.foldereInCurs
                ? _c(
                    "b-list-group-item",
                    { staticClass: "py-50 px-1 small text-muted" },
                    [_vm._v("\n          Niciun subdosar aici.\n        ")]
                  )
                : _vm._e(),
            ],
            2
          ),
        ],
        1
      ),
      _vm._v(" "),
      _c(
        "b-modal",
        {
          attrs: {
            title:
              "Utilizatorii certificatului " + (_vm.certificatCurent.cn || ""),
            size: "lg",
            "ok-only": "",
            "ok-title": "Închide",
            "modal-class": "modul-spv",
          },
          model: {
            value: _vm.utilizatoriVizibil,
            callback: function ($$v) {
              _vm.utilizatoriVizibil = $$v
            },
            expression: "utilizatoriVizibil",
          },
        },
        [
          _c("p", { staticClass: "text-muted small" }, [
            _vm._v(
              "\n        Persoanele din rețea care folosesc acest certificat. Adresa poate fi înscrisă chiar\n        dacă nu are încă un cont în aplicație.\n      "
            ),
          ]),
          _vm._v(" "),
          _c(
            "b-row",
            { staticClass: "mb-3" },
            [
              _c(
                "b-col",
                { attrs: { md: "5" } },
                [
                  _c("label", [_vm._v("Adresă de email")]),
                  _vm._v(" "),
                  _c("b-form-input", {
                    attrs: { type: "email", placeholder: "coleg@firma.ro" },
                    model: {
                      value: _vm.utilizatorNou.email,
                      callback: function ($$v) {
                        _vm.$set(_vm.utilizatorNou, "email", $$v)
                      },
                      expression: "utilizatorNou.email",
                    },
                  }),
                ],
                1
              ),
              _vm._v(" "),
              _c(
                "b-col",
                { attrs: { md: "4" } },
                [
                  _c("label", [_vm._v("Nume (opțional)")]),
                  _vm._v(" "),
                  _c("b-form-input", {
                    model: {
                      value: _vm.utilizatorNou.nume,
                      callback: function ($$v) {
                        _vm.$set(_vm.utilizatorNou, "nume", $$v)
                      },
                      expression: "utilizatorNou.nume",
                    },
                  }),
                ],
                1
              ),
              _vm._v(" "),
              _c(
                "b-col",
                { staticClass: "d-flex align-items-end", attrs: { md: "3" } },
                [
                  _c(
                    "b-button",
                    {
                      attrs: {
                        variant: "primary",
                        disabled: !_vm.utilizatorNou.email,
                      },
                      on: { click: _vm.ataseaza },
                    },
                    [_vm._v("\n            Atașează\n          ")]
                  ),
                ],
                1
              ),
            ],
            1
          ),
          _vm._v(" "),
          _c(
            "b-form-checkbox",
            {
              staticClass: "mb-3",
              model: {
                value: _vm.utilizatorNou.avertizare,
                callback: function ($$v) {
                  _vm.$set(_vm.utilizatorNou, "avertizare", $$v)
                },
                expression: "utilizatorNou.avertizare",
              },
            },
            [
              _vm._v(
                "\n        Înscrie adresa și la avertizările de expirare\n      "
              ),
            ]
          ),
          _vm._v(" "),
          _vm.eroareModal
            ? _c("b-alert", { attrs: { show: "", variant: "danger" } }, [
                _vm._v("\n        " + _vm._s(_vm.eroareModal) + "\n      "),
              ])
            : _vm._e(),
          _vm._v(" "),
          _c("b-table", {
            attrs: {
              items: _vm.certificatCurent.utilizatori || [],
              fields: _vm.campuriUtilizatori,
              responsive: "",
              striped: "",
              small: "",
              "show-empty": "",
              "empty-text": "Niciun utilizator atașat acestui certificat.",
            },
            scopedSlots: _vm._u([
              {
                key: "cell(are_cont)",
                fn: function (rand) {
                  return [
                    _c(
                      "b-badge",
                      {
                        attrs: {
                          variant: rand.item.are_cont ? "success" : "secondary",
                        },
                      },
                      [
                        _vm._v(
                          "\n            " +
                            _vm._s(
                              rand.item.are_cont
                                ? "cont în aplicație"
                                : "doar email"
                            ) +
                            "\n          "
                        ),
                      ]
                    ),
                  ]
                },
              },
              {
                key: "cell(actiuni)",
                fn: function (rand) {
                  return [
                    _c(
                      "b-button",
                      {
                        attrs: { size: "sm", variant: "outline-danger" },
                        on: {
                          click: function ($event) {
                            return _vm.detaseaza(rand.item)
                          },
                        },
                      },
                      [_vm._v("\n            Elimină\n          ")]
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
        "b-modal",
        {
          attrs: {
            size: "lg",
            "ok-only": "",
            "ok-title": "Am înțeles",
            scrollable: "",
            "modal-class": "modul-spv",
          },
          scopedSlots: _vm._u([
            {
              key: "modal-title",
              fn: function () {
                return [
                  _c("feather-icon", {
                    staticClass: "text-primary mr-50",
                    attrs: { icon: "ShieldIcon", size: "18" },
                  }),
                  _vm._v(
                    "\n        Firewall și antivirus — ce trebuie deblocat\n      "
                  ),
                ]
              },
              proxy: true,
            },
          ]),
          model: {
            value: _vm.ajutorVizibil,
            callback: function ($$v) {
              _vm.ajutorVizibil = $$v
            },
            expression: "ajutorVizibil",
          },
        },
        [
          _vm._v(" "),
          _c("p", { staticClass: "mb-2" }, [
            _vm._v(
              "\n        Legătura care a mers și apoi s-a oprit nu e o pană a aplicației: e, aproape\n        întotdeauna, filtrarea traficului criptat din antivirus, aprinsă singură la o\n        actualizare sau la reînnoirea abonamentului. Toate setările de mai jos se fac pe\n        calculatorul unde stă tokenul.\n      "
            ),
          ]),
          _vm._v(" "),
          _c("b-alert", { attrs: { show: "", variant: "warning" } }, [
            _c("div", { staticClass: "alert-body" }, [
              _c("strong", [_vm._v("Regula de aur.")]),
              _vm._v(
                " Programul local nu trimite parole la ANAF: se\n          legitimează cu certificatul de pe token, într-o legătură în care și clientul își\n          arată certificatul. Antivirusul care scanează HTTPS desface legătura și o reface\n          cu certificatul lui — dar cheia de pe token n-o are, deci legitimarea se rupe.\n          Adresele de mai jos trebuie "
              ),
              _c("strong", [_vm._v("scoase de sub scanarea HTTPS")]),
              _vm._v(
                "\n          („filtrare SSL/TLS”, „scanare conexiuni criptate”, „Scan SSL”), nu doar permise.\n        "
              ),
            ]),
          ]),
          _vm._v(" "),
          _c("h6", { staticClass: "mt-2" }, [
            _vm._v(
              "\n        1. Ce trebuie să poată ieși, pe portul 443\n      "
            ),
          ]),
          _vm._v(" "),
          _c("ul", { staticClass: "mb-2 pl-3" }, [
            _c("li", [
              _c("code", [_vm._v("app.dianasoft.ro")]),
              _vm._v(" — agentul întreabă serverul ce are de lucru"),
            ]),
            _vm._v(" "),
            _c("li", [
              _c("code", [_vm._v("webserviced.anaf.ro")]),
              _vm._v(" — mesajele și documentele din SPV"),
            ]),
            _vm._v(" "),
            _c("li", [
              _c("code", [_vm._v("decl.anaf.mfinante.gov.ro")]),
              _vm._v(" — depunerea declarațiilor"),
            ]),
            _vm._v(" "),
            _c("li", [
              _c("code", [_vm._v("webserviceapl.anaf.ro")]),
              _vm._v(" — e-Transport, dacă se folosește"),
            ]),
          ]),
          _vm._v(" "),
          _c("p", { staticClass: "small text-muted mb-2" }, [
            _vm._v(
              "\n        Pe legătura „prin tunel” nu se deschide niciun port de intrare și nu se umblă la\n        router. Portul "
            ),
            _c("code", [_vm._v("8099")]),
            _vm._v(
              " se deschide numai dacă certificatul e configurat\n        „direct”, și numai către adresa serverului aplicației.\n      "
            ),
          ]),
          _vm._v(" "),
          _c("h6", [_vm._v("2. Ce se scoate de sub scanare")]),
          _vm._v(" "),
          _c("ul", { staticClass: "mb-2 pl-3" }, [
            _c("li", [
              _vm._v("dosarul de instalare, cu tot ce e sub el — de obicei "),
              _c("code", [_vm._v("C:\\DianaSoft_SPV_Curier")]),
            ]),
            _vm._v(" "),
            _c("li", [
              _vm._v("procesele "),
              _c("code", [_vm._v("php.exe")]),
              _vm._v(" (din dosarul kitului) și "),
              _c("code", [_vm._v("C:\\Windows\\System32\\curl.exe")]),
            ]),
            _vm._v(" "),
            _c("li", [
              _c("code", [_vm._v("powershell.exe")]),
              _vm._v(", "),
              _c("code", [_vm._v("PDFtoPrinter.exe")]),
              _vm._v(" și "),
              _c("code", [_vm._v("itextsharp.dll")]),
              _vm._v(" — citirea certificatelor, semnarea, tipărirea"),
            ]),
          ]),
          _vm._v(" "),
          _c("p", { staticClass: "small text-muted mb-2" }, [
            _vm._v("\n        La ESET, în plus: "),
            _c("em", [
              _vm._v("Configurare avansată (F5) → Protecții → SSL/TLS"),
            ]),
            _vm._v(
              ", cele patru\n        adrese la „excluse din filtrare”; iar dacă HIPS e pe mod interactiv, o regulă prin care\n        "
            ),
            _c("code", [_vm._v("php.exe")]),
            _vm._v(" are voie să pornească alte programe.\n      "),
          ]),
          _vm._v(" "),
          _c("h6", [_vm._v("3. Imprimantele")]),
          _vm._v(" "),
          _c("p", { staticClass: "small mb-2" }, [
            _vm._v(
              "\n        Tipărirea se face chiar pe calculatorul cu tokenul, deci nu cere nicio regulă de\n        firewall — decât dacă imprimanta e în rețea (ieșire pe "
            ),
            _c("code", [_vm._v("9100")]),
            _vm._v(" RAW,\n        "),
            _c("code", [_vm._v("445")]),
            _vm._v(" partajată de pe un server, "),
            _c("code", [_vm._v("631")]),
            _vm._v(" IPP, "),
            _c("code", [_vm._v("515")]),
            _vm._v(" LPR).\n        Două pricini obișnuite: "),
            _c("code", [_vm._v("PDFtoPrinter.exe")]),
            _vm._v(
              " pus în carantină ca „aplicație\n        nedorită”, sau imprimanta instalată în alt cont Windows decât cel sub care rulează\n        programul — atunci nici nu apare în listă.\n      "
            ),
          ]),
          _vm._v(" "),
          _c("h6", [_vm._v("4. Verificarea, în ordinea aceasta")]),
          _vm._v(" "),
          _c("p", { staticClass: "small mb-1" }, [
            _vm._v(
              "\n        Pe calculatorul cu tokenul, în PowerShell:\n      "
            ),
          ]),
          _vm._v(" "),
          _c("pre", { staticClass: "verificari-ajutor mb-2" }, [
            _vm._v(
              'Get-ScheduledTask "Acces token ANAF*"\ncurl.exe -sS -o NUL -w "%{http_code}`n" http://127.0.0.1:8099/certificate\nGet-Content C:\\DianaSoft_SPV_Curier\\agent.log -Tail 30\nTest-NetConnection app.dianasoft.ro -Port 443'
            ),
          ]),
          _vm._v(" "),
          _c("ul", { staticClass: "small mb-2 pl-3" }, [
            _c("li", [
              _vm._v("a doua comandă trebuie să răspundă "),
              _c("code", [_vm._v("401")]),
              _vm._v(" — programul cere codul de acces, deci trăiește;"),
            ]),
            _vm._v(" "),
            _c("li", [
              _vm._v("rândurile „"),
              _c("em", [_vm._v("Serverul nu răspunde: …")]),
              _vm._v(
                "” poartă cu ele și pricina — port închis, TLS desfăcut de antivirus, sau un răspuns venit de la altcineva de pe drum;"
              ),
            ]),
            _vm._v(" "),
            _c("li", [
              _vm._v("la kiturile mai vechi, „"),
              _c("em", [_vm._v("Serverul nu răspunde; reîncerc peste 5s")]),
              _vm._v(
                "” apare și când totul merge: dacă printre acele rânduri sunt și rânduri „"
              ),
              _c("em", [_vm._v("Comanda …")]),
              _vm._v("”, legătura e bună;"),
            ]),
            _vm._v(" "),
            _c("li", [
              _vm._v("„"),
              _c("em", [_vm._v("Serverul nu-mi recunoaște codul de acces")]),
              _vm._v(
                "” nu e firewall: apăsați „Citește token-urile conectate”."
              ),
            ]),
          ]),
          _vm._v(" "),
          _c("p", { staticClass: "small text-muted mb-0" }, [
            _vm._v(
              "\n        Pașii întregi, pe fiecare antivirus în parte (ESET, Defender, Bitdefender, Kaspersky,\n        Avast/AVG, Norton, McAfee), sunt în ghidul "
            ),
            _c("em", [
              _vm._v(
                "Firewall și antivirus pentru programul\n          de acces la token"
              ),
            ]),
            _vm._v(", de cerut de la DianaSoft.\n      "),
          ]),
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

/***/ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/app_pages/spv/Declaratii.vue?vue&type=template&id=fcac4d92&scoped=true&":
/*!**************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/src/views/app_pages/spv/Declaratii.vue?vue&type=template&id=fcac4d92&scoped=true& ***!
  \**************************************************************************************************************************************************************************************************************************************/
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
        { staticClass: "border mb-2", attrs: { "body-class": "p-2" } },
        [
          _c(
            "b-row",
            { staticClass: "align-items-end" },
            [
              _c(
                "b-col",
                { attrs: { md: "3" } },
                [
                  _c("label", { staticClass: "mb-0" }, [
                    _vm._v("Selectați declarații (XML sau PDF) de prelucrat"),
                  ]),
                  _vm._v(" "),
                  _c("b-form-file", {
                    attrs: {
                      multiple: "",
                      accept: ".xml,.pdf",
                      placeholder: "Alegeți unul sau mai multe fișiere...",
                      "browse-text": "Răsfoiește",
                      "file-name-formatter": _vm.numeFisiere,
                    },
                    model: {
                      value: _vm.fisiere,
                      callback: function ($$v) {
                        _vm.fisiere = $$v
                      },
                      expression: "fisiere",
                    },
                  }),
                  _vm._v(" "),
                  _c("b-form-file", {
                    ref: "folder",
                    staticClass: "mt-1",
                    attrs: {
                      multiple: "",
                      directory: "",
                      webkitdirectory: "",
                      placeholder: "...sau alegeți un folder întreg",
                      "browse-text": "Folder",
                      "file-name-formatter": _vm.numeFisiere,
                    },
                    model: {
                      value: _vm.dinFolder,
                      callback: function ($$v) {
                        _vm.dinFolder = $$v
                      },
                      expression: "dinFolder",
                    },
                  }),
                ],
                1
              ),
              _vm._v(" "),
              _c("b-col", { attrs: { md: "auto" } }, [
                _c(
                  "div",
                  [
                    _c(
                      "b-button",
                      {
                        staticClass: "text-left buton-pasi",
                        attrs: {
                          variant: "outline-primary",
                          disabled: !_vm.deIncarcat.length || _vm.lucreaza,
                        },
                        on: { click: _vm.incarca },
                      },
                      [
                        _c(
                          "div",
                          {
                            class:
                              _vm.pasulActiv === "incarca"
                                ? "font-weight-bolder"
                                : "",
                          },
                          [
                            _vm.pasulActiv === "incarca"
                              ? _c("b-spinner", {
                                  staticClass: "mr-1",
                                  attrs: { small: "" },
                                })
                              : _c("feather-icon", {
                                  staticClass: "mr-1",
                                  class: _vm.incarcaTerminat
                                    ? "text-success"
                                    : "",
                                  attrs: {
                                    icon: _vm.incarcaTerminat
                                      ? "CheckIcon"
                                      : "UploadIcon",
                                    size: "15",
                                  },
                                }),
                            _vm._v("\n              Încarcă\n              "),
                            _vm._v(" "),
                            _vm.deIncarcat.length
                              ? _c(
                                  "b-badge",
                                  {
                                    staticClass: "ml-1",
                                    attrs: { variant: "warning" },
                                  },
                                  [
                                    _vm._v(
                                      "\n                " +
                                        _vm._s(_vm.deIncarcat.length) +
                                        "\n              "
                                    ),
                                  ]
                                )
                              : _vm._e(),
                          ],
                          1
                        ),
                        _vm._v(" "),
                        _c(
                          "div",
                          {
                            class:
                              _vm.pasulActiv === "incarca"
                                ? "font-weight-bolder"
                                : "",
                          },
                          [
                            _vm.pasulActiv === "incarca"
                              ? _c("b-spinner", {
                                  staticClass: "mr-1",
                                  attrs: { small: "" },
                                })
                              : _c("feather-icon", {
                                  staticClass: "mr-1",
                                  class: _vm.incarcaTerminat
                                    ? "text-success"
                                    : "",
                                  attrs: {
                                    icon: _vm.incarcaTerminat
                                      ? "CheckIcon"
                                      : "CheckCircleIcon",
                                    size: "15",
                                  },
                                }),
                            _vm._v("\n              Validează\n            "),
                          ],
                          1
                        ),
                      ]
                    ),
                    _vm._v(" "),
                    _vm.poateSemna
                      ? _c(
                          "div",
                          {
                            staticClass: "pentru-tiparire mt-50",
                            attrs: {
                              title:
                                "După validare, semnează pe loc declarațiile valide din acest lot",
                            },
                          },
                          [
                            _c(
                              "b-form-checkbox",
                              {
                                staticClass: "comutator-primar",
                                attrs: { size: "sm" },
                                model: {
                                  value: _vm.semneazaDupaValidare,
                                  callback: function ($$v) {
                                    _vm.semneazaDupaValidare = $$v
                                  },
                                  expression: "semneazaDupaValidare",
                                },
                              },
                              [
                                _c(
                                  "small",
                                  {
                                    staticClass: "text-nowrap",
                                    class: _vm.semneazaDupaValidare
                                      ? "text-primary"
                                      : "text-muted",
                                  },
                                  [
                                    _vm._v(
                                      "\n                Semnează declarațiile valide\n              "
                                    ),
                                  ]
                                ),
                              ]
                            ),
                          ],
                          1
                        )
                      : _vm._e(),
                  ],
                  1
                ),
              ]),
              _vm._v(" "),
              _vm.poateSemna
                ? _c("b-col", { attrs: { md: "auto" } }, [
                    _c(
                      "div",
                      [
                        _c(
                          "b-button",
                          {
                            staticClass: "text-left",
                            attrs: {
                              variant: "outline-primary",
                              disabled: !_vm.deSemnat.length || _vm.lucreaza,
                            },
                            on: { click: _vm.semneazaDinTabel },
                          },
                          [
                            _vm.semnareInCurs
                              ? _c("b-spinner", {
                                  staticClass: "mr-1",
                                  attrs: { small: "" },
                                })
                              : _c("feather-icon", {
                                  staticClass: "mr-1",
                                  attrs: { icon: "Edit3Icon", size: "15" },
                                }),
                            _vm._v(
                              "\n            Semnează declarațiile valide\n            "
                            ),
                            _vm.deSemnat.length
                              ? _c(
                                  "b-badge",
                                  {
                                    staticClass: "ml-1",
                                    attrs: { variant: "warning" },
                                  },
                                  [
                                    _vm._v(
                                      "\n              " +
                                        _vm._s(_vm.deSemnat.length) +
                                        "\n            "
                                    ),
                                  ]
                                )
                              : _vm._e(),
                          ],
                          1
                        ),
                        _vm._v(" "),
                        _vm.poateDepune
                          ? _c(
                              "div",
                              {
                                staticClass: "pentru-tiparire mt-50",
                                attrs: {
                                  title:
                                    "După semnare, trimite la ANAF declarațiile semnate în această sesiune, fără să mai fie nevoie de butonul „Depune”",
                                },
                              },
                              [
                                _c(
                                  "b-form-checkbox",
                                  {
                                    staticClass: "comutator-primar",
                                    attrs: { size: "sm" },
                                    model: {
                                      value: _vm.depuneDupaSemnare,
                                      callback: function ($$v) {
                                        _vm.depuneDupaSemnare = $$v
                                      },
                                      expression: "depuneDupaSemnare",
                                    },
                                  },
                                  [
                                    _c(
                                      "small",
                                      {
                                        staticClass: "text-nowrap",
                                        class: _vm.depuneDupaSemnare
                                          ? "text-danger"
                                          : "text-muted",
                                      },
                                      [
                                        _vm._v(
                                          "\n                Depune declarațiile semnate\n              "
                                        ),
                                      ]
                                    ),
                                  ]
                                ),
                              ],
                              1
                            )
                          : _vm._e(),
                        _vm._v(" "),
                        _c(
                          "div",
                          {
                            staticClass: "pentru-tiparire",
                            attrs: {
                              title:
                                "Trimite la imprimanta dumneavoastră toate declarațiile semnate în această sesiune",
                            },
                          },
                          [
                            _c(
                              "b-form-checkbox",
                              {
                                staticClass: "comutator-primar",
                                attrs: { size: "sm" },
                                model: {
                                  value: _vm.tiparire,
                                  callback: function ($$v) {
                                    _vm.tiparire = $$v
                                  },
                                  expression: "tiparire",
                                },
                              },
                              [
                                _c(
                                  "small",
                                  {
                                    staticClass: "text-nowrap",
                                    class: _vm.tiparire
                                      ? "text-primary"
                                      : "text-muted",
                                  },
                                  [
                                    _vm._v(
                                      "\n                Imprimă declarațiile semnate\n              "
                                    ),
                                  ]
                                ),
                              ]
                            ),
                          ],
                          1
                        ),
                      ],
                      1
                    ),
                  ])
                : _vm._e(),
              _vm._v(" "),
              _c("b-col", { staticClass: "d-flex align-items-stretch" }, [
                _c(
                  "div",
                  {
                    staticClass:
                      "d-flex align-items-center justify-content-center flex-grow-1",
                  },
                  [
                    _vm.poateDepune
                      ? _c(
                          "b-button",
                          {
                            attrs: {
                              variant: "outline-info",
                              disabled:
                                !_vm.deDepus.length || _vm.depunereInCurs,
                            },
                            on: { click: _vm.depuneSemnate },
                          },
                          [
                            _vm.depunereInCurs
                              ? _c("b-spinner", {
                                  staticClass: "mr-1",
                                  attrs: { small: "" },
                                })
                              : _c("feather-icon", {
                                  staticClass: "mr-1",
                                  attrs: { icon: "SendIcon", size: "15" },
                                }),
                            _vm._v(
                              "\n            Depune declarațiile semnate\n            "
                            ),
                            _vm.deDepus.length
                              ? _c(
                                  "b-badge",
                                  {
                                    staticClass: "ml-1",
                                    attrs: { variant: "warning" },
                                  },
                                  [
                                    _vm._v(
                                      "\n              " +
                                        _vm._s(_vm.deDepus.length) +
                                        "\n            "
                                    ),
                                  ]
                                )
                              : _vm._e(),
                          ],
                          1
                        )
                      : _vm._e(),
                  ],
                  1
                ),
                _vm._v(" "),
                _c(
                  "div",
                  {
                    staticClass:
                      "d-flex flex-column justify-content-center flex-shrink-0",
                  },
                  [
                    _c(
                      "div",
                      {
                        staticClass: "pentru-tiparire",
                        attrs: {
                          title:
                            "Trimite la imprimanta dumneavoastră toate recipisele aduse în această sesiune",
                        },
                      },
                      [
                        _c(
                          "b-form-checkbox",
                          {
                            staticClass: "comutator-verde",
                            attrs: { size: "sm" },
                            model: {
                              value: _vm.tiparireRecipise,
                              callback: function ($$v) {
                                _vm.tiparireRecipise = $$v
                              },
                              expression: "tiparireRecipise",
                            },
                          },
                          [
                            _c(
                              "small",
                              {
                                staticClass: "text-nowrap",
                                class: _vm.tiparireRecipise
                                  ? "text-success"
                                  : "text-muted",
                              },
                              [
                                _vm._v(
                                  "\n                Imprimare recipise descărcate\n              "
                                ),
                              ]
                            ),
                          ]
                        ),
                      ],
                      1
                    ),
                    _vm._v(" "),
                    _c(
                      "div",
                      {
                        staticClass: "pentru-tiparire",
                        attrs: {
                          title:
                            "Aplică watermark cu denumirea firmei pe recipisele de imprimat",
                        },
                      },
                      [
                        _c(
                          "b-form-checkbox",
                          {
                            staticClass: "comutator-verde",
                            attrs: {
                              size: "sm",
                              disabled: !_vm.tiparireRecipise,
                            },
                            model: {
                              value: _vm.filigran,
                              callback: function ($$v) {
                                _vm.filigran = $$v
                              },
                              expression: "filigran",
                            },
                          },
                          [
                            _c(
                              "small",
                              {
                                staticClass: "text-nowrap",
                                class:
                                  _vm.filigran && _vm.tiparireRecipise
                                    ? "text-success"
                                    : "text-muted",
                              },
                              [
                                _vm._v(
                                  "\n                Aplică watermark\n              "
                                ),
                              ]
                            ),
                          ]
                        ),
                      ],
                      1
                    ),
                    _vm._v(" "),
                    _c(
                      "b-dropdown",
                      {
                        attrs: {
                          split: "",
                          variant: "outline-success",
                          block: "",
                          disabled: _vm.recipiseInCurs,
                        },
                        on: {
                          click: function ($event) {
                            return _vm.verificaRecipise()
                          },
                        },
                        scopedSlots: _vm._u([
                          {
                            key: "button-content",
                            fn: function () {
                              return [
                                _vm.recipiseInCurs
                                  ? _c("b-spinner", {
                                      staticClass: "mr-1",
                                      attrs: { small: "" },
                                    })
                                  : _c("feather-icon", {
                                      staticClass: "mr-1",
                                      attrs: {
                                        icon: "DownloadIcon",
                                        size: "15",
                                      },
                                    }),
                                _vm._v(" "),
                                _vm._v(
                                  "\n              Descarcă recipise" +
                                    _vm._s(
                                      _vm.automat.activ ? " automat" : ""
                                    ) +
                                    "\n              "
                                ),
                                _vm.deDescarcat.length
                                  ? _c(
                                      "b-badge",
                                      {
                                        staticClass: "ml-1",
                                        attrs: { variant: "warning" },
                                      },
                                      [
                                        _vm._v(
                                          "\n                " +
                                            _vm._s(_vm.deDescarcat.length) +
                                            "\n              "
                                        ),
                                      ]
                                    )
                                  : _vm._e(),
                              ]
                            },
                            proxy: true,
                          },
                        ]),
                      },
                      [
                        _vm._v(" "),
                        _c(
                          "b-dropdown-item",
                          {
                            attrs: { active: !_vm.automat.activ },
                            on: {
                              click: function ($event) {
                                _vm.automat.activ = false
                              },
                            },
                          },
                          [
                            _vm._v(
                              "\n              Descarcă recipise\n            "
                            ),
                          ]
                        ),
                        _vm._v(" "),
                        _c(
                          "b-dropdown-item",
                          {
                            attrs: { active: _vm.automat.activ },
                            on: {
                              click: function ($event) {
                                _vm.automat.activ = true
                              },
                            },
                          },
                          [
                            _vm._v(
                              "\n              Descarcă recipise automat\n            "
                            ),
                          ]
                        ),
                      ],
                      1
                    ),
                    _vm._v(" "),
                    _c("div", { staticClass: "automat-recipise mt-1" }, [
                      _vm.automat.activ
                        ? _c(
                            "div",
                            {
                              staticClass:
                                "d-flex align-items-center justify-content-center",
                            },
                            [
                              _c(
                                "small",
                                { staticClass: "text-success mr-1" },
                                [_vm._v("la")]
                              ),
                              _vm._v(" "),
                              _c("b-form-select", {
                                staticClass: "lista-minute",
                                attrs: {
                                  size: "sm",
                                  options: _vm.optiuniMinute,
                                },
                                model: {
                                  value: _vm.automat.minute,
                                  callback: function ($$v) {
                                    _vm.$set(_vm.automat, "minute", _vm._n($$v))
                                  },
                                  expression: "automat.minute",
                                },
                              }),
                            ],
                            1
                          )
                        : _vm._e(),
                      _vm._v(" "),
                      _c("div", { staticClass: "text-center" }, [
                        _c("small", { staticClass: "text-muted" }, [
                          _vm.ultimaDescarcare
                            ? _c("span", [
                                _vm._v(
                                  "\n                  Ultima descărcare: " +
                                    _vm._s(_vm.ultimaDescarcare) +
                                    " · " +
                                    _vm._s(_vm.recipiseAduse) +
                                    "\n                "
                                ),
                              ])
                            : _c("span", [_vm._v("Nicio descărcare încă")]),
                        ]),
                      ]),
                    ]),
                  ],
                  1
                ),
              ]),
            ],
            1
          ),
        ],
        1
      ),
      _vm._v(" "),
      _c(
        "b-row",
        { staticClass: "mb-2" },
        [
          _c(
            "b-col",
            { attrs: { md: "2" } },
            [
              _c("label", { staticClass: "small mb-0" }, [
                _vm._v("Tip declarație"),
              ]),
              _vm._v(" "),
              _c("b-form-input", {
                attrs: { size: "sm", placeholder: "ex. D394" },
                on: { change: _vm.incarcaLista },
                model: {
                  value: _vm.filtre.tip,
                  callback: function ($$v) {
                    _vm.$set(_vm.filtre, "tip", $$v)
                  },
                  expression: "filtre.tip",
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
              _c("label", { staticClass: "small mb-0" }, [_vm._v("CUI")]),
              _vm._v(" "),
              _c("b-form-input", {
                attrs: { size: "sm", placeholder: "cod fiscal" },
                on: { change: _vm.incarcaLista },
                model: {
                  value: _vm.filtre.cui,
                  callback: function ($$v) {
                    _vm.$set(_vm.filtre, "cui", $$v)
                  },
                  expression: "filtre.cui",
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
              _c("label", { staticClass: "small mb-0" }, [
                _vm._v("Denumire firmă"),
              ]),
              _vm._v(" "),
              _c("b-form-input", {
                attrs: { size: "sm", placeholder: "parte din denumire" },
                on: { change: _vm.incarcaLista },
                model: {
                  value: _vm.filtre.den_firma,
                  callback: function ($$v) {
                    _vm.$set(_vm.filtre, "den_firma", $$v)
                  },
                  expression: "filtre.den_firma",
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
              _c("label", { staticClass: "small mb-0" }, [_vm._v("Perioada")]),
              _vm._v(" "),
              _c(
                "b-row",
                { attrs: { "no-gutters": "" } },
                [
                  _c(
                    "b-col",
                    { attrs: { cols: "5" } },
                    [
                      _c("b-form-input", {
                        attrs: {
                          size: "sm",
                          type: "number",
                          min: "1",
                          max: "12",
                          placeholder: "luna",
                        },
                        on: { change: _vm.incarcaLista },
                        model: {
                          value: _vm.filtre.luna,
                          callback: function ($$v) {
                            _vm.$set(_vm.filtre, "luna", $$v)
                          },
                          expression: "filtre.luna",
                        },
                      }),
                    ],
                    1
                  ),
                  _vm._v(" "),
                  _c(
                    "b-col",
                    { staticClass: "pl-1", attrs: { cols: "7" } },
                    [
                      _c("b-form-input", {
                        attrs: {
                          size: "sm",
                          type: "number",
                          placeholder: "anul",
                        },
                        on: { change: _vm.incarcaLista },
                        model: {
                          value: _vm.filtre.anul,
                          callback: function ($$v) {
                            _vm.$set(_vm.filtre, "anul", $$v)
                          },
                          expression: "filtre.anul",
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
          _c(
            "b-col",
            { attrs: { md: "2" } },
            [
              _c("label", { staticClass: "small mb-0" }, [
                _vm._v("Index încărcare"),
              ]),
              _vm._v(" "),
              _c("b-form-input", {
                attrs: { size: "sm", placeholder: "index ANAF" },
                on: { change: _vm.incarcaLista },
                model: {
                  value: _vm.filtre.index_recipisa,
                  callback: function ($$v) {
                    _vm.$set(_vm.filtre, "index_recipisa", $$v)
                  },
                  expression: "filtre.index_recipisa",
                },
              }),
            ],
            1
          ),
          _vm._v(" "),
          _c(
            "b-col",
            { attrs: { md: "1" } },
            [
              _c("label", { staticClass: "small mb-0" }, [_vm._v("Stare")]),
              _vm._v(" "),
              _c("b-form-select", {
                attrs: { size: "sm", options: _vm.optiuniPas },
                on: { change: _vm.incarcaLista },
                model: {
                  value: _vm.filtre.pas,
                  callback: function ($$v) {
                    _vm.$set(_vm.filtre, "pas", $$v)
                  },
                  expression: "filtre.pas",
                },
              }),
            ],
            1
          ),
        ],
        1
      ),
      _vm._v(" "),
      _vm.semnareInCurs || _vm.depunereMesaj
        ? _c("div", { staticClass: "mb-2" }, [
            _c("small", { staticClass: "text-muted" }, [
              _vm.semnareInCurs
                ? _c("span", [
                    _vm._v("Se semnează " + _vm._s(_vm.semnareInCurs) + "..."),
                  ])
                : _c("span", [
                    _vm._v("Se depune " + _vm._s(_vm.depunereMesaj) + "..."),
                  ]),
            ]),
          ])
        : _vm._e(),
      _vm._v(" "),
      _vm.recipiseInCurs && _vm.mersul
        ? _c(
            "div",
            { staticClass: "text-muted mb-2" },
            [
              _vm._v("\n    " + _vm._s(_vm.mersul) + "\n    "),
              _vm.deCercetat
                ? _c("b-progress", {
                    staticClass: "mt-1",
                    attrs: {
                      value: _vm.cercetate,
                      max: _vm.deCercetat,
                      variant: "success",
                      height: "6px",
                    },
                  })
                : _vm._e(),
            ],
            1
          )
        : _vm._e(),
      _vm._v(" "),
      _vm.eroare
        ? _c(
            "b-alert",
            { staticClass: "mb-2", attrs: { show: "", variant: "danger" } },
            [_vm._v("\n    " + _vm._s(_vm.eroare) + "\n  ")]
          )
        : _vm._e(),
      _vm._v(" "),
      _c(
        "b-card",
        { staticClass: "border mb-0", attrs: { "body-class": "p-1" } },
        [
          _c("b-table", {
            ref: "tabel",
            staticClass: "tabel-compact mb-0",
            attrs: {
              items: _vm.declaratii,
              fields: _vm.campuri,
              busy: _vm.listaInCurs,
              "per-page": _vm.pePagina,
              "current-page": _vm.pagina,
              responsive: "",
              striped: "",
              small: "",
              "show-empty": "",
              "empty-text": "Nu există declarații pentru filtrul selectat.",
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
                        _vm._v(
                          "\n          Se încarcă declarațiile...\n        "
                        ),
                      ],
                      1
                    ),
                  ]
                },
                proxy: true,
              },
              {
                key: "cell(den_firma)",
                fn: function (rand) {
                  return [
                    _c(
                      "div",
                      { staticClass: "d-flex align-items-center" },
                      [
                        _c("span", [
                          _vm._v(_vm._s(rand.item.den_firma || "-")),
                        ]),
                        _vm._v(" "),
                        !rand.item.inrolata
                          ? _c("feather-icon", {
                              directives: [
                                {
                                  name: "b-tooltip",
                                  rawName: "v-b-tooltip.hover.html.right",
                                  value: _vm.explicatieNeinrolata(rand.item),
                                  expression: "explicatieNeinrolata(rand.item)",
                                  modifiers: {
                                    hover: true,
                                    html: true,
                                    right: true,
                                  },
                                },
                              ],
                              staticClass: "text-warning ml-50 flex-shrink-0",
                              attrs: { icon: "AlertTriangleIcon", size: "15" },
                            })
                          : _vm._e(),
                      ],
                      1
                    ),
                  ]
                },
              },
              {
                key: "cell(perioada)",
                fn: function (rand) {
                  return [
                    _vm._v(
                      "\n        " +
                        _vm._s(
                          rand.item.luna
                            ? rand.item.luna + "/" + rand.item.anul
                            : rand.item.anul || "-"
                        ) +
                        "\n        "
                    ),
                    rand.item.rectificativa
                      ? _c(
                          "b-badge",
                          {
                            staticClass: "ml-1",
                            attrs: { variant: "warning" },
                          },
                          [_vm._v("\n          rectificativă\n        ")]
                        )
                      : _vm._e(),
                  ]
                },
              },
              {
                key: "cell(pas)",
                fn: function (rand) {
                  return [
                    _c(
                      "b-badge",
                      { attrs: { variant: _vm.variantaPas(rand.item.pas) } },
                      [
                        _vm._v(
                          "\n          " +
                            _vm._s(_vm.etichetaPas(rand.item.pas)) +
                            "\n        "
                        ),
                      ]
                    ),
                  ]
                },
              },
              {
                key: "cell(eroare)",
                fn: function (rand) {
                  return [
                    rand.item.eroare
                      ? _c(
                          "div",
                          { staticClass: "d-flex align-items-center" },
                          [
                            _c(
                              "div",
                              {
                                staticClass: "small text-danger",
                                class: _vm.desfasurata(rand.item)
                                  ? "coloana-eroare-desfasurata"
                                  : "coloana-eroare",
                                attrs: { title: rand.item.eroare },
                              },
                              [
                                _vm._v(
                                  "\n            " +
                                    _vm._s(_vm.peUnRand(rand.item.eroare)) +
                                    "\n          "
                                ),
                              ]
                            ),
                            _vm._v(" "),
                            _vm.eroareaEsteLunga(rand.item)
                              ? _c(
                                  "b-button",
                                  {
                                    staticClass:
                                      "btn-icon p-0 ml-1 flex-shrink-0",
                                    attrs: {
                                      size: "sm",
                                      variant: "flat-secondary",
                                      title: _vm.desfasurata(rand.item)
                                        ? "Restrânge eroarea"
                                        : "Arată eroarea întreagă",
                                    },
                                    on: {
                                      click: function ($event) {
                                        return _vm.comutaEroarea(rand.item)
                                      },
                                    },
                                  },
                                  [
                                    _c("feather-icon", {
                                      attrs: {
                                        icon: _vm.desfasurata(rand.item)
                                          ? "ChevronUpIcon"
                                          : "ChevronDownIcon",
                                        size: "16",
                                      },
                                    }),
                                  ],
                                  1
                                )
                              : _vm._e(),
                            _vm._v(" "),
                            rand.item.eroare_de_validare
                              ? _c(
                                  "b-button",
                                  {
                                    staticClass:
                                      "btn-icon p-0 ml-1 flex-shrink-0",
                                    attrs: {
                                      size: "sm",
                                      variant: "flat-primary",
                                      title:
                                        "SPV Wizard — explică eroarea pe înțelesul tuturor",
                                    },
                                    on: {
                                      click: function ($event) {
                                        return _vm.explicaEroarea(rand.item)
                                      },
                                    },
                                  },
                                  [
                                    _c("feather-icon", {
                                      attrs: { icon: "ZapIcon", size: "16" },
                                    }),
                                  ],
                                  1
                                )
                              : _vm._e(),
                          ],
                          1
                        )
                      : _c("span", { staticClass: "text-muted" }, [
                          _vm._v("-"),
                        ]),
                  ]
                },
              },
              {
                key: "cell(consistenta)",
                fn: function (rand) {
                  return [
                    !rand.item.are_consistenta
                      ? _c("span", { staticClass: "text-muted" }, [_vm._v("-")])
                      : rand.item.verificare_stare === "curata"
                      ? _c(
                          "b-badge",
                          {
                            staticClass: "badge-apasabil",
                            attrs: {
                              variant: "light-success",
                              title:
                                "Nicio neconcordanță; apasă pentru amănunte",
                            },
                            on: {
                              click: function ($event) {
                                return _vm.deschideConsistenta(rand.item)
                              },
                            },
                          },
                          [_vm._v("\n          curată\n        ")]
                        )
                      : rand.item.verificare_stare === "erori"
                      ? _c(
                          "b-badge",
                          {
                            staticClass: "badge-apasabil",
                            attrs: {
                              variant: "light-danger",
                              title: "Apasă pentru liniile de îndreptat",
                            },
                            on: {
                              click: function ($event) {
                                return _vm.deschideConsistenta(rand.item)
                              },
                            },
                          },
                          [
                            _vm._v(
                              "\n          " +
                                _vm._s(rand.item.verificare_numar) +
                                " " +
                                _vm._s(
                                  rand.item.verificare_numar === 1
                                    ? "linie"
                                    : "linii"
                                ) +
                                "\n        "
                            ),
                          ]
                        )
                      : rand.item.verificare_stare === "imposibil"
                      ? _c(
                          "b-badge",
                          {
                            staticClass: "badge-apasabil",
                            attrs: {
                              variant: "light-warning",
                              title:
                                "Verificarea nu a putut fi făcută; apasă pentru motiv",
                            },
                            on: {
                              click: function ($event) {
                                return _vm.deschideConsistenta(rand.item)
                              },
                            },
                          },
                          [_vm._v("\n          nu s-a putut\n        ")]
                        )
                      : _c(
                          "b-button",
                          {
                            attrs: {
                              size: "sm",
                              variant: "flat-primary",
                              disabled: _vm.ocupat === rand.item.id,
                            },
                            on: {
                              click: function ($event) {
                                return _vm.verificaConsistenta(rand.item)
                              },
                            },
                          },
                          [_vm._v("\n          Verifică\n        ")]
                        ),
                  ]
                },
              },
              {
                key: "cell(potrivire)",
                fn: function (rand) {
                  return [
                    !rand.item.are_potrivire
                      ? _c("span", { staticClass: "text-muted" }, [_vm._v("-")])
                      : rand.item.potrivire_stare === "potrivit"
                      ? _c(
                          "b-badge",
                          {
                            staticClass: "badge-apasabil",
                            attrs: {
                              variant: "light-success",
                              title:
                                "Decontul iese la fel din SAF-T; apasă pentru amănunte",
                            },
                            on: {
                              click: function ($event) {
                                return _vm.deschidePotrivirea(rand.item)
                              },
                            },
                          },
                          [_vm._v("\n          se potrivește\n        ")]
                        )
                      : rand.item.potrivire_stare === "diferente"
                      ? _c(
                          "b-badge",
                          {
                            staticClass: "badge-apasabil",
                            attrs: {
                              variant: "light-danger",
                              title:
                                "Apasă pentru rândurile care nu se potrivesc",
                            },
                            on: {
                              click: function ($event) {
                                return _vm.deschidePotrivirea(rand.item)
                              },
                            },
                          },
                          [
                            _vm._v(
                              "\n          " +
                                _vm._s(rand.item.potrivire_numar) +
                                "\n          " +
                                _vm._s(
                                  rand.item.potrivire_numar === 1
                                    ? "rând"
                                    : "rânduri"
                                ) +
                                "\n        "
                            ),
                          ]
                        )
                      : rand.item.potrivire_stare === "imposibil"
                      ? _c(
                          "b-badge",
                          {
                            staticClass: "badge-apasabil",
                            attrs: {
                              variant: "light-warning",
                              title:
                                "Comparația nu s-a putut face; apasă pentru motiv",
                            },
                            on: {
                              click: function ($event) {
                                return _vm.deschidePotrivirea(rand.item)
                              },
                            },
                          },
                          [_vm._v("\n          nu s-a putut\n        ")]
                        )
                      : _c(
                          "span",
                          {
                            staticClass: "text-muted",
                            attrs: {
                              title:
                                "Nu există declarația pereche a aceleiași luni",
                            },
                          },
                          [_vm._v("fără pereche")]
                        ),
                  ]
                },
              },
              {
                key: "cell(index_recipisa)",
                fn: function (rand) {
                  return [
                    rand.item.index_recipisa
                      ? _c("span", { staticClass: "text-nowrap" }, [
                          _vm._v(_vm._s(rand.item.index_recipisa)),
                        ])
                      : _c("span", { staticClass: "text-muted" }, [
                          _vm._v("-"),
                        ]),
                  ]
                },
              },
              {
                key: "cell(data_depunere)",
                fn: function (rand) {
                  return [
                    rand.item.data_depunere
                      ? _c("span", { staticClass: "text-nowrap" }, [
                          _vm._v(_vm._s(rand.item.data_depunere)),
                        ])
                      : _c("span", { staticClass: "text-muted" }, [
                          _vm._v("-"),
                        ]),
                  ]
                },
              },
              {
                key: "cell(stare_declaratie)",
                fn: function (rand) {
                  return [
                    rand.item.stare_declaratie
                      ? _c(
                          "div",
                          { class: _vm.clasaStare(rand.item.clasificare) },
                          [
                            _vm._v(
                              "\n          " +
                                _vm._s(rand.item.stare_declaratie) +
                                "\n        "
                            ),
                          ]
                        )
                      : rand.item.index_recipisa
                      ? _c("span", { staticClass: "text-muted" }, [
                          _vm._v("în așteptarea recipisei"),
                        ])
                      : _c("span", [_vm._v("-")]),
                  ]
                },
              },
              {
                key: "cell(certificat_nume)",
                fn: function (rand) {
                  return [
                    rand.item.certificat_nume
                      ? _c("span", [_vm._v(_vm._s(rand.item.certificat_nume))])
                      : rand.item.certificat_inrolare
                      ? _c(
                          "small",
                          {
                            staticClass: "text-muted",
                            attrs: {
                              title:
                                "Certificatul cu care este înrolată firma; cu el se va semna",
                            },
                          },
                          [
                            _vm._v(
                              "\n          " +
                                _vm._s(rand.item.certificat_inrolare) +
                                "\n        "
                            ),
                          ]
                        )
                      : _c("span", [_vm._v("-")]),
                  ]
                },
              },
              {
                key: "cell(actiuni)",
                fn: function (rand) {
                  return [
                    _vm.poateSemna && rand.item.pas === "eroare_semnare"
                      ? _c(
                          "b-button",
                          {
                            staticClass: "mr-1 mb-1",
                            attrs: {
                              size: "sm",
                              variant: "outline-primary",
                              disabled: _vm.ocupat === rand.item.id,
                            },
                            on: {
                              click: function ($event) {
                                return _vm.actiune(rand.item, "semneaza")
                              },
                            },
                          },
                          [_vm._v("\n          Reîncearcă semnarea\n        ")]
                        )
                      : _vm._e(),
                    _vm._v(" "),
                    _vm.poateDepune &&
                    rand.item.semnat &&
                    !rand.item.index_recipisa
                      ? _c(
                          "b-button",
                          {
                            staticClass: "mr-1 mb-1",
                            attrs: {
                              size: "sm",
                              variant: "outline-success",
                              disabled: _vm.ocupat === rand.item.id,
                            },
                            on: {
                              click: function ($event) {
                                return _vm.actiune(rand.item, "depune")
                              },
                            },
                          },
                          [_vm._v("\n          Depune\n        ")]
                        )
                      : _vm._e(),
                    _vm._v(" "),
                    rand.item.are_consistenta && rand.item.cale_xml
                      ? _c(
                          "b-button",
                          {
                            staticClass: "mr-1 mb-1",
                            attrs: {
                              size: "sm",
                              variant: "outline-primary",
                              disabled: _vm.ocupat === rand.item.id,
                            },
                            on: {
                              click: function ($event) {
                                return _vm.deschideDecontul(rand.item)
                              },
                            },
                          },
                          [_vm._v("\n          Decont TVA\n        ")]
                        )
                      : _vm._e(),
                    _vm._v(" "),
                    rand.item.cale_pdf_semnat ||
                    rand.item.arhiva_semnat ||
                    rand.item.cale_pdf
                      ? _c(
                          "b-button",
                          {
                            staticClass: "mr-1 mb-1",
                            attrs: { size: "sm", variant: "outline-secondary" },
                            on: {
                              click: function ($event) {
                                return _vm.deschide(
                                  rand.item,
                                  rand.item.cale_pdf_semnat ||
                                    rand.item.arhiva_semnat
                                    ? "semnat"
                                    : "pdf"
                                )
                              },
                            },
                          },
                          [_vm._v("\n          PDF\n        ")]
                        )
                      : _vm._e(),
                    _vm._v(" "),
                    rand.item.cale_recipisa || rand.item.arhiva_recipisa
                      ? _c(
                          "b-button",
                          {
                            staticClass: "mr-1 mb-1",
                            attrs: { size: "sm", variant: "outline-info" },
                            on: {
                              click: function ($event) {
                                return _vm.deschide(rand.item, "recipisa")
                              },
                            },
                          },
                          [_vm._v("\n          Recipisă\n        ")]
                        )
                      : _vm._e(),
                    _vm._v(" "),
                    !_vm.esteDepusa(rand.item)
                      ? _c(
                          "b-button",
                          {
                            staticClass: "mb-1",
                            attrs: { size: "sm", variant: "outline-danger" },
                            on: {
                              click: function ($event) {
                                return _vm.sterge(rand.item)
                              },
                            },
                          },
                          [_vm._v("\n          Șterge\n        ")]
                        )
                      : _vm._e(),
                  ]
                },
              },
            ]),
          }),
          _vm._v(" "),
          _vm.declaratii.length > _vm.pePagina
            ? _c(
                "div",
                {
                  staticClass:
                    "d-flex align-items-center justify-content-between mt-1",
                },
                [
                  _c("small", { staticClass: "text-muted text-nowrap" }, [
                    _vm._v(
                      "\n        " +
                        _vm._s(_vm.deLaRand) +
                        "–" +
                        _vm._s(_vm.panaLaRand) +
                        " din " +
                        _vm._s(_vm.declaratii.length) +
                        "\n      "
                    ),
                  ]),
                  _vm._v(" "),
                  _c("b-pagination", {
                    staticClass: "mb-0",
                    attrs: {
                      "total-rows": _vm.declaratii.length,
                      "per-page": _vm.pePagina,
                      size: "sm",
                      align: "center",
                    },
                    model: {
                      value: _vm.pagina,
                      callback: function ($$v) {
                        _vm.pagina = $$v
                      },
                      expression: "pagina",
                    },
                  }),
                  _vm._v(" "),
                  _c("b-form-select", {
                    staticClass: "selector-pagina",
                    attrs: { options: _vm.marimiPagina, size: "sm" },
                    model: {
                      value: _vm.pePaginaAles,
                      callback: function ($$v) {
                        _vm.pePaginaAles = $$v
                      },
                      expression: "pePaginaAles",
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
      _c(
        "b-modal",
        {
          attrs: {
            size: "lg",
            "ok-only": "",
            "ok-title": "Am înțeles",
            scrollable: "",
            "modal-class": "modul-spv",
          },
          scopedSlots: _vm._u([
            {
              key: "modal-title",
              fn: function () {
                return [
                  _c("feather-icon", {
                    staticClass: "text-primary mr-50",
                    attrs: { icon: "ZapIcon", size: "18" },
                  }),
                  _vm._v("\n      SPV Wizard\n      "),
                  _c("span", { staticClass: "small text-muted ml-50" }, [
                    _vm._v("ce înseamnă eroarea"),
                  ]),
                ]
              },
              proxy: true,
            },
          ]),
          model: {
            value: _vm.explicatieVizibila,
            callback: function ($$v) {
              _vm.explicatieVizibila = $$v
            },
            expression: "explicatieVizibila",
          },
        },
        [
          _vm._v(" "),
          _c(
            "b-alert",
            { attrs: { show: _vm.explicatieEroare !== "", variant: "danger" } },
            [
              _c("div", { staticClass: "alert-body" }, [
                _vm._v(
                  "\n        " + _vm._s(_vm.explicatieEroare) + "\n      "
                ),
              ]),
            ]
          ),
          _vm._v(" "),
          _vm.explicatie
            ? _c(
                "div",
                [
                  _vm.explicatie.rezumat
                    ? _c("p", { staticClass: "mb-2" }, [
                        _vm._v(
                          "\n        " +
                            _vm._s(_vm.explicatie.rezumat) +
                            "\n      "
                        ),
                      ])
                    : _vm.explicatieTotal
                    ? _c("p", { staticClass: "mb-2 text-muted" }, [
                        _vm._v(
                          "\n        Validatorul a raportat " +
                            _vm._s(_vm.explicatieTotal) +
                            "\n        " +
                            _vm._s(
                              _vm.explicatieTotal === 1
                                ? "problemă"
                                : "probleme"
                            ) +
                            ". Le iau pe rând.\n      "
                        ),
                      ])
                    : _vm._e(),
                  _vm._v(" "),
                  _vm._l(_vm.explicatie.probleme, function (problema, i) {
                    return _c(
                      "b-card",
                      {
                        key: i,
                        staticClass: "border mb-1",
                        attrs: { "body-class": "p-2" },
                      },
                      [
                        _c(
                          "div",
                          { staticClass: "d-flex align-items-center mb-1" },
                          [
                            _c(
                              "b-badge",
                              {
                                attrs: {
                                  variant: _vm.culoareSeveritate(
                                    problema.severitate
                                  ),
                                },
                              },
                              [
                                _vm._v(
                                  "\n            " +
                                    _vm._s(
                                      _vm.etichetaSeveritate(
                                        problema.severitate
                                      )
                                    ) +
                                    "\n          "
                                ),
                              ]
                            ),
                            _vm._v(" "),
                            problema.camp
                              ? _c(
                                  "span",
                                  { staticClass: "ml-1 font-weight-bold" },
                                  [_vm._v(_vm._s(problema.camp))]
                                )
                              : _vm._e(),
                            _vm._v(" "),
                            problema.regula
                              ? _c(
                                  "b-badge",
                                  {
                                    staticClass: "ml-1",
                                    attrs: { variant: "light-secondary" },
                                  },
                                  [
                                    _vm._v(
                                      "\n            regula " +
                                        _vm._s(problema.regula) +
                                        "\n          "
                                    ),
                                  ]
                                )
                              : _vm._e(),
                            _vm._v(" "),
                            problema.sectiune
                              ? _c(
                                  "span",
                                  { staticClass: "ml-1 text-muted small" },
                                  [
                                    _vm._v(
                                      "în secțiunea " +
                                        _vm._s(problema.sectiune)
                                    ),
                                  ]
                                )
                              : _vm._e(),
                          ],
                          1
                        ),
                        _vm._v(" "),
                        _c("div", { staticClass: "mesaj-original mb-1" }, [
                          _c("span", { staticClass: "small text-muted" }, [
                            _vm._v("Mesajul validatorului ANAF:"),
                          ]),
                          _vm._v(" "),
                          _c("code", { staticClass: "d-block" }, [
                            _vm._v(_vm._s(problema.mesaj)),
                          ]),
                        ]),
                        _vm._v(" "),
                        problema.explicatie
                          ? _c("p", { staticClass: "mb-1" }, [
                              _vm._v(
                                "\n          " +
                                  _vm._s(problema.explicatie) +
                                  "\n        "
                              ),
                            ])
                          : _c("p", { staticClass: "mb-1 text-muted" }, [
                              _vm._v(
                                "\n          Pentru acest mesaj nu am o explicație pregătită; mai sus este textul original al validatorului.\n        "
                              ),
                            ]),
                        _vm._v(" "),
                        problema.de_corectat || problema.locatie
                          ? _c("div", { staticClass: "mb-1" }, [
                              _c("strong", { staticClass: "small" }, [
                                _vm._v("De corectat:"),
                              ]),
                              _vm._v(" "),
                              problema.de_corectat
                                ? _c("div", { staticClass: "small" }, [
                                    _vm._v(
                                      "\n            " +
                                        _vm._s(problema.de_corectat) +
                                        "\n          "
                                    ),
                                  ])
                                : _vm._e(),
                              _vm._v(" "),
                              problema.locatie
                                ? _c("div", { staticClass: "small mt-1" }, [
                                    _c(
                                      "div",
                                      { staticClass: "indicatie-fisier" },
                                      [
                                        _vm._v(
                                          "\n              Deschide fișierul XML în Notepad++ și apasă "
                                        ),
                                        _c("kbd", [_vm._v("Ctrl+G")]),
                                        _vm._v(
                                          ", apoi mergi la\n              "
                                        ),
                                        _c("strong", [
                                          _vm._v(
                                            "linia " +
                                              _vm._s(problema.locatie.linie)
                                          ),
                                        ]),
                                        _vm._v(
                                          ",\n              coloana " +
                                            _vm._s(problema.locatie.coloana) +
                                            ".\n              "
                                        ),
                                        problema.locatie.aparitii > 1
                                          ? _c("span", [
                                              _vm._v(
                                                "\n                Valoarea apare de " +
                                                  _vm._s(
                                                    problema.locatie.aparitii
                                                  ) +
                                                  " ori în fișier; aceasta este cea reclamată.\n              "
                                              ),
                                            ])
                                          : _vm._e(),
                                      ]
                                    ),
                                    _vm._v(" "),
                                    _c(
                                      "div",
                                      { staticClass: "rand-xml mt-1" },
                                      [
                                        _c(
                                          "span",
                                          { staticClass: "text-muted mr-1" },
                                          [
                                            _vm._v(
                                              _vm._s(problema.locatie.linie)
                                            ),
                                          ]
                                        ),
                                        _c(
                                          "code",
                                          { staticClass: "xml-rand" },
                                          [
                                            _vm._v(
                                              _vm._s(problema.locatie.inainte)
                                            ),
                                            _c(
                                              "span",
                                              { staticClass: "xml-gresit" },
                                              [
                                                _vm._v(
                                                  _vm._s(
                                                    problema.locatie.potrivire
                                                  )
                                                ),
                                              ]
                                            ),
                                            _vm._v(
                                              _vm._s(problema.locatie.dupa)
                                            ),
                                          ]
                                        ),
                                      ]
                                    ),
                                    _vm._v(" "),
                                    problema.locatie.trunchiat
                                      ? _c(
                                          "div",
                                          { staticClass: "text-muted" },
                                          [
                                            _vm._v(
                                              "\n              Rândul este lung, așa că se vede doar bucata din jurul valorii.\n            "
                                            ),
                                          ]
                                        )
                                      : _vm._e(),
                                  ])
                                : _vm._e(),
                            ])
                          : _vm._e(),
                        _vm._v(" "),
                        problema.cauta
                          ? _c("div", { staticClass: "mb-1" }, [
                              _c("strong", { staticClass: "small" }, [
                                _vm._v("Caută în fișierul XML:"),
                              ]),
                              _vm._v(" "),
                              _c("code", { staticClass: "ml-1" }, [
                                _vm._v(_vm._s(problema.cauta)),
                              ]),
                            ])
                          : _vm._e(),
                      ]
                    )
                  }),
                  _vm._v(" "),
                  _vm.explicatieInCurs
                    ? _c(
                        "div",
                        {
                          staticClass:
                            "d-flex align-items-center text-muted my-2",
                        },
                        [
                          _c("b-spinner", {
                            staticClass: "mr-1",
                            attrs: { small: "" },
                          }),
                          _vm._v(" "),
                          _vm.explicatieTotal
                            ? _c("span", [
                                _vm._v(
                                  "\n          Lucrez la problema " +
                                    _vm._s(_vm.explicatie.probleme.length + 1) +
                                    " din " +
                                    _vm._s(_vm.explicatieTotal) +
                                    "...\n        "
                                ),
                              ])
                            : _c("span", [
                                _vm._v("Citesc erorile validatorului..."),
                              ]),
                        ],
                        1
                      )
                    : _vm._e(),
                ],
                2
              )
            : _vm._e(),
        ],
        1
      ),
      _vm._v(" "),
      _c(
        "b-modal",
        {
          attrs: {
            size: "xl",
            "ok-only": "",
            "ok-title": "Închide",
            scrollable: "",
            "modal-class": "modul-spv",
          },
          scopedSlots: _vm._u([
            {
              key: "modal-title",
              fn: function () {
                return [
                  _c("feather-icon", {
                    staticClass: "text-primary mr-50",
                    attrs: { icon: "CheckSquareIcon", size: "18" },
                  }),
                  _vm._v("\n      Consistența SAF-T\n      "),
                  _vm.consistentaDeclaratie
                    ? _c("span", { staticClass: "small text-muted ml-50" }, [
                        _vm._v(
                          "\n        " +
                            _vm._s(_vm.consistentaDeclaratie.cui) +
                            " — " +
                            _vm._s(_vm.consistentaDeclaratie.luna) +
                            "/" +
                            _vm._s(_vm.consistentaDeclaratie.anul) +
                            "\n      "
                        ),
                      ])
                    : _vm._e(),
                ]
              },
              proxy: true,
            },
          ]),
          model: {
            value: _vm.consistentaVizibila,
            callback: function ($$v) {
              _vm.consistentaVizibila = $$v
            },
            expression: "consistentaVizibila",
          },
        },
        [
          _vm._v(" "),
          _c(
            "b-alert",
            {
              attrs: { show: _vm.consistentaEroare !== "", variant: "danger" },
            },
            [
              _c("div", { staticClass: "alert-body" }, [
                _vm._v(
                  "\n        " + _vm._s(_vm.consistentaEroare) + "\n      "
                ),
              ]),
            ]
          ),
          _vm._v(" "),
          _vm.consistentaInCurs
            ? _c(
                "div",
                { staticClass: "d-flex align-items-center text-muted my-2" },
                [
                  _c("b-spinner", {
                    staticClass: "mr-1",
                    attrs: { small: "" },
                  }),
                  _vm._v("\n      Trec prin liniile declarației...\n    "),
                ],
                1
              )
            : _vm._e(),
          _vm._v(" "),
          _vm.consistentaDate
            ? _c(
                "div",
                [
                  _c(
                    "div",
                    {
                      staticClass:
                        "d-flex align-items-center justify-content-between mb-1",
                    },
                    [
                      _vm.consistentaDate.verificat_la
                        ? _c("small", { staticClass: "text-muted" }, [
                            _vm._v(
                              "\n          Verificată la " +
                                _vm._s(_vm.consistentaDate.verificat_la) +
                                "\n        "
                            ),
                          ])
                        : _c("span"),
                      _vm._v(" "),
                      _c(
                        "b-button",
                        {
                          attrs: {
                            size: "sm",
                            variant: "outline-primary",
                            disabled: _vm.consistentaInCurs,
                          },
                          on: {
                            click: function ($event) {
                              return _vm.verificaConsistenta(
                                _vm.consistentaDeclaratie
                              )
                            },
                          },
                        },
                        [_vm._v("\n          Verifică din nou\n        ")]
                      ),
                    ],
                    1
                  ),
                  _vm._v(" "),
                  _c(
                    "b-alert",
                    {
                      attrs: {
                        show: _vm.consistentaDate.stare === "imposibil",
                        variant: "warning",
                      },
                    },
                    [
                      _c("div", { staticClass: "alert-body" }, [
                        _vm._v(
                          "\n          " +
                            _vm._s(_vm.consistentaDate.mesaj) +
                            "\n        "
                        ),
                      ]),
                    ]
                  ),
                  _vm._v(" "),
                  _c(
                    "b-alert",
                    {
                      attrs: {
                        show: _vm.consistentaDate.stare === "curata",
                        variant: "success",
                      },
                    },
                    [
                      _c("div", { staticClass: "alert-body" }, [
                        _vm._v(
                          "\n          Nicio neconcordanță. Cifrele din jurnale se potrivesc între ele:\n          cont, cod TVA, cotă, bază și taxă.\n        "
                        ),
                      ]),
                    ]
                  ),
                  _vm._v(" "),
                  _vm.consistentaDate.antet &&
                  _vm.consistentaDate.antet.registrationnumber
                    ? _c("div", { staticClass: "small text-muted mb-1" }, [
                        _vm._v(
                          "\n        " +
                            _vm._s(_vm.consistentaDate.antet.name) +
                            " (" +
                            _vm._s(
                              _vm.consistentaDate.antet.registrationnumber
                            ) +
                            ") —\n        perioada " +
                            _vm._s(
                              _vm.consistentaDate.antet.selectionstartdate
                            ) +
                            " … " +
                            _vm._s(_vm.consistentaDate.antet.selectionenddate) +
                            ",\n        " +
                            _vm._s(_vm.consistentaDate.antet.numberofentries) +
                            " note contabile,\n        total debit " +
                            _vm._s(_vm.consistentaDate.antet.totaldebit) +
                            " / credit " +
                            _vm._s(_vm.consistentaDate.antet.totalcredit) +
                            ".\n      "
                        ),
                      ])
                    : _vm._e(),
                  _vm._v(" "),
                  _vm._l(_vm.consistentaDate.pe_teste, function (test) {
                    return _c(
                      "b-card",
                      {
                        key: test.cod,
                        staticClass: "border mb-1",
                        attrs: { "body-class": "p-1" },
                      },
                      [
                        _c(
                          "div",
                          { staticClass: "d-flex align-items-center mb-50" },
                          [
                            _c(
                              "b-badge",
                              { attrs: { variant: "light-danger" } },
                              [
                                _vm._v(
                                  "\n            " +
                                    _vm._s(test.numar) +
                                    " " +
                                    _vm._s(
                                      test.numar === 1 ? "linie" : "linii"
                                    ) +
                                    "\n          "
                                ),
                              ]
                            ),
                            _vm._v(" "),
                            _c("strong", { staticClass: "ml-1" }, [
                              _vm._v(_vm._s(test.titlu)),
                            ]),
                            _vm._v(" "),
                            _c(
                              "b-badge",
                              {
                                staticClass: "ml-1",
                                attrs: { variant: "light-secondary" },
                              },
                              [
                                _vm._v(
                                  "\n            " +
                                    _vm._s(test.cod) +
                                    "\n          "
                                ),
                              ]
                            ),
                          ],
                          1
                        ),
                        _vm._v(" "),
                        _c("div", { staticClass: "small mb-50" }, [
                          _vm._v(
                            "\n          " +
                              _vm._s(test.verifica) +
                              "\n        "
                          ),
                        ]),
                        _vm._v(" "),
                        _c("div", { staticClass: "small" }, [
                          _c("strong", [_vm._v("De făcut:")]),
                          _vm._v(" " + _vm._s(test.de_facut) + "\n        "),
                        ]),
                      ]
                    )
                  }),
                  _vm._v(" "),
                  _vm.consistentaDate.erori && _vm.consistentaDate.erori.length
                    ? _c("b-table", {
                        staticClass: "tabel-compact mt-1",
                        attrs: {
                          items: _vm.consistentaDate.erori,
                          fields: _vm.campuriConsistenta,
                          responsive: "",
                          small: "",
                          striped: "",
                        },
                        scopedSlots: _vm._u(
                          [
                            {
                              key: "cell(test)",
                              fn: function (rand) {
                                return [
                                  _c(
                                    "b-badge",
                                    {
                                      attrs: {
                                        variant: "light-danger",
                                        title: rand.item.test.titlu,
                                      },
                                    },
                                    [
                                      _vm._v(
                                        "\n            " +
                                          _vm._s(rand.item.test.cod) +
                                          "\n          "
                                      ),
                                    ]
                                  ),
                                ]
                              },
                            },
                          ],
                          null,
                          false,
                          2323123988
                        ),
                      })
                    : _vm._e(),
                  _vm._v(" "),
                  _vm.consistentaDate.trunchiat
                    ? _c("div", { staticClass: "small text-muted" }, [
                        _vm._v(
                          "\n        Se arată primele " +
                            _vm._s(_vm.consistentaDate.erori.length) +
                            " linii din " +
                            _vm._s(_vm.consistentaDate.numar) +
                            ".\n        Îndreaptă-le pe acestea și verifică din nou: de obicei aceeași greșeală le ține pe toate.\n      "
                        ),
                      ])
                    : _vm._e(),
                ],
                2
              )
            : _vm._e(),
        ],
        1
      ),
      _vm._v(" "),
      _c(
        "b-modal",
        {
          attrs: {
            size: "lg",
            "ok-only": "",
            "ok-title": "Închide",
            scrollable: "",
            "modal-class": "modul-spv",
          },
          scopedSlots: _vm._u([
            {
              key: "modal-title",
              fn: function () {
                return [
                  _c("feather-icon", {
                    staticClass: "text-primary mr-50",
                    attrs: { icon: "GitCompareIcon", size: "18" },
                  }),
                  _vm._v("\n      D300 față în față cu SAF-T\n      "),
                  _vm.potrivireDeclaratie
                    ? _c("span", { staticClass: "small text-muted ml-50" }, [
                        _vm._v(
                          "\n        " +
                            _vm._s(_vm.potrivireDeclaratie.cui) +
                            " — " +
                            _vm._s(_vm.potrivireDeclaratie.luna) +
                            "/" +
                            _vm._s(_vm.potrivireDeclaratie.anul) +
                            "\n      "
                        ),
                      ])
                    : _vm._e(),
                ]
              },
              proxy: true,
            },
          ]),
          model: {
            value: _vm.potrivireVizibila,
            callback: function ($$v) {
              _vm.potrivireVizibila = $$v
            },
            expression: "potrivireVizibila",
          },
        },
        [
          _vm._v(" "),
          _c(
            "b-alert",
            { attrs: { show: _vm.potrivireEroare !== "", variant: "danger" } },
            [
              _c("div", { staticClass: "alert-body" }, [
                _vm._v("\n        " + _vm._s(_vm.potrivireEroare) + "\n      "),
              ]),
            ]
          ),
          _vm._v(" "),
          _vm.potrivireInCurs
            ? _c(
                "div",
                { staticClass: "d-flex align-items-center text-muted my-2" },
                [
                  _c("b-spinner", {
                    staticClass: "mr-1",
                    attrs: { small: "" },
                  }),
                  _vm._v("\n      Citesc rezultatul...\n    "),
                ],
                1
              )
            : _vm._e(),
          _vm._v(" "),
          _vm.potrivireDate
            ? _c(
                "div",
                [
                  _c(
                    "b-alert",
                    {
                      attrs: {
                        show: "",
                        variant:
                          _vm.potrivireDate.stare === "potrivit"
                            ? "success"
                            : "warning",
                      },
                    },
                    [
                      _c("div", { staticClass: "alert-body" }, [
                        _c("strong", [_vm._v(_vm._s(_vm.potrivireDate.titlu))]),
                        _vm._v(" "),
                        _c("div", { staticClass: "small mt-25" }, [
                          _vm._v(
                            "\n            " +
                              _vm._s(_vm.potrivireDate.explicatie) +
                              "\n          "
                          ),
                        ]),
                        _vm._v(" "),
                        _vm.potrivireDate.perechea
                          ? _c(
                              "div",
                              { staticClass: "small mt-50 text-muted" },
                              [
                                _vm._v(
                                  "\n            Comparat cu " +
                                    _vm._s(_vm.potrivireDate.perechea.tip) +
                                    ":\n            " +
                                    _vm._s(
                                      _vm.potrivireDate.perechea.nume_fisier
                                    ) +
                                    "\n          "
                                ),
                              ]
                            )
                          : _vm._e(),
                      ]),
                    ]
                  ),
                  _vm._v(" "),
                  _vm.potrivireDate.diferente &&
                  _vm.potrivireDate.diferente.length
                    ? _c("b-table", {
                        staticClass: "tabel-compact",
                        attrs: {
                          items: _vm.potrivireDate.diferente,
                          fields: _vm.campuriPotrivire,
                          responsive: "",
                          small: "",
                          striped: "",
                        },
                        scopedSlots: _vm._u(
                          [
                            {
                              key: "cell(rand)",
                              fn: function (rand) {
                                return [
                                  _c(
                                    "div",
                                    [
                                      _c(
                                        "span",
                                        { staticClass: "font-weight-bold" },
                                        [
                                          _vm._v(
                                            "Rândul " + _vm._s(rand.item.rand)
                                          ),
                                        ]
                                      ),
                                      _vm._v(" "),
                                      _c(
                                        "b-badge",
                                        {
                                          staticClass: "ml-50",
                                          attrs: { variant: "light-secondary" },
                                        },
                                        [
                                          _vm._v(
                                            "\n              " +
                                              _vm._s(rand.item.fel) +
                                              "\n            "
                                          ),
                                        ]
                                      ),
                                      _vm._v(" "),
                                      _c(
                                        "small",
                                        { staticClass: "text-muted ml-50" },
                                        [_vm._v(_vm._s(rand.item.atribut))]
                                      ),
                                    ],
                                    1
                                  ),
                                  _vm._v(" "),
                                  _c(
                                    "div",
                                    { staticClass: "small text-muted" },
                                    [
                                      _vm._v(
                                        "\n            " +
                                          _vm._s(rand.item.denumire) +
                                          "\n          "
                                      ),
                                    ]
                                  ),
                                ]
                              },
                            },
                            {
                              key: "cell(din_saft)",
                              fn: function (rand) {
                                return [
                                  _c("span", { staticClass: "text-nowrap" }, [
                                    _vm._v(
                                      _vm._s(
                                        _vm.leiDeAfisat(rand.item.din_saft)
                                      )
                                    ),
                                  ]),
                                ]
                              },
                            },
                            {
                              key: "cell(din_d300)",
                              fn: function (rand) {
                                return [
                                  _c("span", { staticClass: "text-nowrap" }, [
                                    _vm._v(
                                      _vm._s(
                                        _vm.leiDeAfisat(rand.item.din_d300)
                                      )
                                    ),
                                  ]),
                                ]
                              },
                            },
                            {
                              key: "cell(diferenta)",
                              fn: function (rand) {
                                return [
                                  _c(
                                    "span",
                                    { staticClass: "text-nowrap text-danger" },
                                    [
                                      _vm._v(
                                        _vm._s(
                                          _vm.leiDeAfisat(rand.item.diferenta)
                                        )
                                      ),
                                    ]
                                  ),
                                ]
                              },
                            },
                          ],
                          null,
                          false,
                          817785726
                        ),
                      })
                    : _vm._e(),
                  _vm._v(" "),
                  _vm.potrivireDate.potrivit_la
                    ? _c("div", { staticClass: "small text-muted" }, [
                        _vm._v(
                          "\n        Comparat la " +
                            _vm._s(_vm.potrivireDate.potrivit_la) +
                            ". Se face din nou la fiecare validare\n        a oricăreia dintre cele două declarații.\n      "
                        ),
                      ])
                    : _vm._e(),
                ],
                1
              )
            : _vm._e(),
        ],
        1
      ),
      _vm._v(" "),
      _c(
        "b-modal",
        {
          attrs: { size: "lg", scrollable: "", "modal-class": "modul-spv" },
          scopedSlots: _vm._u([
            {
              key: "modal-title",
              fn: function () {
                return [
                  _c("feather-icon", {
                    staticClass: "text-primary mr-50",
                    attrs: { icon: "FileTextIcon", size: "18" },
                  }),
                  _vm._v("\n      Decont TVA din SAF-T\n      "),
                  _vm.decontDate
                    ? _c("span", { staticClass: "small text-muted ml-50" }, [
                        _vm._v(
                          "\n        " +
                            _vm._s(_vm.decontDate.denumire) +
                            " (" +
                            _vm._s(_vm.decontDate.cif) +
                            ") — " +
                            _vm._s(_vm.decontDate.luna) +
                            "/" +
                            _vm._s(_vm.decontDate.an) +
                            "\n      "
                        ),
                      ])
                    : _vm._e(),
                ]
              },
              proxy: true,
            },
            {
              key: "modal-footer",
              fn: function (ref) {
                var ok = ref.ok
                return [
                  _vm.decontDate && _vm.decontDate.randuri.length
                    ? _c(
                        "b-button",
                        {
                          attrs: {
                            variant: "outline-primary",
                            disabled: _vm.decontInCurs,
                            title:
                              "Scrie datele pentru PDF-ul inteligent al ANAF (soft A). Se încarcă în Acrobat Reader, din Import Data.",
                          },
                          on: { click: _vm.descarcaDecontFormular },
                        },
                        [
                          _c("feather-icon", {
                            staticClass: "mr-50",
                            attrs: { icon: "FileIcon", size: "14" },
                          }),
                          _vm._v(
                            "\n        Export XML D300 pentru PDF-ul ANAF\n      "
                          ),
                        ],
                        1
                      )
                    : _vm._e(),
                  _vm._v(" "),
                  _vm.decontDate && _vm.decontDate.randuri.length
                    ? _c(
                        "b-button",
                        {
                          attrs: {
                            variant: "primary",
                            disabled: _vm.decontInCurs,
                            title:
                              "Scrie declarația D300 într-un fișier XML, gata de validat și depus",
                          },
                          on: { click: _vm.descarcaDecontXml },
                        },
                        [
                          _c("feather-icon", {
                            staticClass: "mr-50",
                            attrs: { icon: "DownloadIcon", size: "14" },
                          }),
                          _vm._v(
                            "\n        Export XML D300 pentru DUKIntegrator\n      "
                          ),
                        ],
                        1
                      )
                    : _vm._e(),
                  _vm._v(" "),
                  _c(
                    "b-button",
                    {
                      attrs: { variant: "secondary" },
                      on: {
                        click: function ($event) {
                          return ok()
                        },
                      },
                    },
                    [_vm._v("\n        Închide\n      ")]
                  ),
                ]
              },
            },
          ]),
          model: {
            value: _vm.decontVizibil,
            callback: function ($$v) {
              _vm.decontVizibil = $$v
            },
            expression: "decontVizibil",
          },
        },
        [
          _vm._v(" "),
          _c(
            "b-alert",
            { attrs: { show: _vm.decontEroare !== "", variant: "danger" } },
            [
              _c("div", { staticClass: "alert-body" }, [
                _vm._v("\n        " + _vm._s(_vm.decontEroare) + "\n      "),
              ]),
            ]
          ),
          _vm._v(" "),
          _vm.decontInCurs
            ? _c(
                "div",
                { staticClass: "d-flex align-items-center text-muted my-2" },
                [
                  _c("b-spinner", {
                    staticClass: "mr-1",
                    attrs: { small: "" },
                  }),
                  _vm._v("\n      Trec prin jurnalele declarației...\n    "),
                ],
                1
              )
            : _vm._e(),
          _vm._v(" "),
          _vm.decontDate
            ? _c(
                "div",
                [
                  _c(
                    "b-alert",
                    {
                      attrs: {
                        show: "",
                        variant:
                          _vm.decontDate.lamurire.stare === "bun"
                            ? "success"
                            : "warning",
                      },
                    },
                    [
                      _c("div", { staticClass: "alert-body" }, [
                        _c("strong", [
                          _vm._v(_vm._s(_vm.decontDate.lamurire.titlu)),
                        ]),
                        _vm._v(" "),
                        _c("div", { staticClass: "small mt-25" }, [
                          _vm._v(
                            "\n            " +
                              _vm._s(_vm.decontDate.lamurire.explicatie) +
                              "\n          "
                          ),
                        ]),
                        _vm._v(" "),
                        _vm.decontDate.lamurire.de_facut
                          ? _c("div", { staticClass: "small mt-50" }, [
                              _c("strong", [_vm._v("De făcut:")]),
                              _vm._v(
                                " " +
                                  _vm._s(_vm.decontDate.lamurire.de_facut) +
                                  "\n          "
                              ),
                            ])
                          : _vm._e(),
                      ]),
                    ]
                  ),
                  _vm._v(" "),
                  _vm.decontDate.antet && !_vm.decontDate.antet.gata
                    ? _c(
                        "b-alert",
                        {
                          staticClass: "py-1 px-2",
                          attrs: { show: "", variant: "warning" },
                        },
                        [
                          _c("div", { staticClass: "alert-body" }, [
                            _c("strong", [
                              _vm._v("Antetul declarației e neîntregit."),
                            ]),
                            _vm._v(
                              "\n          Mai lipsesc: " +
                                _vm._s(
                                  _vm.decontDate.antet.lipsesc.join(", ")
                                ) +
                                ".\n          Se completează o dată, la "
                            ),
                            _c("em", [
                              _vm._v("Entități → Date pentru declarații"),
                            ]),
                            _vm._v(
                              ",\n          și se iau de acolo la fiecare declarație.\n        "
                            ),
                          ]),
                        ]
                      )
                    : _vm._e(),
                  _vm._v(" "),
                  _vm.decontDate.randuri.length
                    ? _c("b-table", {
                        staticClass: "tabel-compact",
                        attrs: {
                          items: _vm.decontDate.randuri,
                          fields: _vm.campuriDecont,
                          responsive: "",
                          small: "",
                          striped: "",
                        },
                        scopedSlots: _vm._u(
                          [
                            {
                              key: "cell(eticheta)",
                              fn: function (rand) {
                                return [
                                  rand.item.rand
                                    ? _c(
                                        "div",
                                        [
                                          _c(
                                            "span",
                                            { staticClass: "font-weight-bold" },
                                            [
                                              _vm._v(
                                                "Rândul " +
                                                  _vm._s(rand.item.rand)
                                              ),
                                            ]
                                          ),
                                          _vm._v(" "),
                                          _c(
                                            "b-badge",
                                            {
                                              staticClass: "ml-50",
                                              attrs: {
                                                variant: "light-secondary",
                                              },
                                            },
                                            [
                                              _vm._v(
                                                "\n              " +
                                                  _vm._s(rand.item.fel) +
                                                  "\n            "
                                              ),
                                            ]
                                          ),
                                          _vm._v(" "),
                                          rand.item.atribut
                                            ? _c(
                                                "small",
                                                {
                                                  staticClass:
                                                    "text-muted ml-50",
                                                },
                                                [
                                                  _vm._v(
                                                    _vm._s(rand.item.atribut)
                                                  ),
                                                ]
                                              )
                                            : _vm._e(),
                                          _vm._v(" "),
                                          _c(
                                            "div",
                                            { staticClass: "small text-muted" },
                                            [
                                              _vm._v(
                                                "\n              " +
                                                  _vm._s(rand.item.denumire) +
                                                  "\n            "
                                              ),
                                            ]
                                          ),
                                        ],
                                        1
                                      )
                                    : _c(
                                        "span",
                                        { staticClass: "text-muted" },
                                        [_vm._v(_vm._s(rand.item.camp))]
                                      ),
                                ]
                              },
                            },
                            {
                              key: "cell(valoare)",
                              fn: function (rand) {
                                return [
                                  _c("span", { staticClass: "text-nowrap" }, [
                                    _vm._v(
                                      _vm._s(_vm.leiDeAfisat(rand.item.valoare))
                                    ),
                                  ]),
                                ]
                              },
                            },
                          ],
                          null,
                          false,
                          68921302
                        ),
                      })
                    : _vm._e(),
                ],
                1
              )
            : _vm._e(),
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

/***/ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/app_pages/spv/Jurnal.vue?vue&type=template&id=54c7497b&":
/*!**********************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/src/views/app_pages/spv/Jurnal.vue?vue&type=template&id=54c7497b& ***!
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
        "b-card",
        { attrs: { title: "Jurnal de activitate" } },
        [
          _c("p", { staticClass: "text-muted" }, [
            _vm._v(
              "\n      Ce a făcut fiecare utilizator în modulul ANAF: citiri de mesaje, solicitări,\n      semnări și depuneri de declarații, modificări de date. Operațiile eșuate apar marcate.\n    "
            ),
          ]),
          _vm._v(" "),
          _c(
            "b-row",
            { staticClass: "mb-3" },
            [
              _c(
                "b-col",
                { attrs: { md: "2" } },
                [
                  _c("label", [_vm._v("Utilizator")]),
                  _vm._v(" "),
                  _c("b-form-select", {
                    attrs: { options: _vm.optiuniUtilizatori },
                    on: { change: _vm.incarcaLista },
                    model: {
                      value: _vm.filtre.user_id,
                      callback: function ($$v) {
                        _vm.$set(_vm.filtre, "user_id", $$v)
                      },
                      expression: "filtre.user_id",
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
                  _c("label", [_vm._v("Acțiune")]),
                  _vm._v(" "),
                  _c("b-form-select", {
                    attrs: { options: _vm.optiuniActiuni },
                    on: { change: _vm.incarcaLista },
                    model: {
                      value: _vm.filtre.actiune,
                      callback: function ($$v) {
                        _vm.$set(_vm.filtre, "actiune", $$v)
                      },
                      expression: "filtre.actiune",
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
                  _c("label", [_vm._v("De la")]),
                  _vm._v(" "),
                  _c("b-form-input", {
                    attrs: { type: "date" },
                    on: { change: _vm.incarcaLista },
                    model: {
                      value: _vm.filtre.de_la,
                      callback: function ($$v) {
                        _vm.$set(_vm.filtre, "de_la", $$v)
                      },
                      expression: "filtre.de_la",
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
                  _c("label", [_vm._v("Până la")]),
                  _vm._v(" "),
                  _c("b-form-input", {
                    attrs: { type: "date" },
                    on: { change: _vm.incarcaLista },
                    model: {
                      value: _vm.filtre.pana_la,
                      callback: function ($$v) {
                        _vm.$set(_vm.filtre, "pana_la", $$v)
                      },
                      expression: "filtre.pana_la",
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
                  _c("label", [_vm._v("Caută în descriere")]),
                  _vm._v(" "),
                  _c("b-form-input", {
                    attrs: { placeholder: "Ex: D300 sau 15208744" },
                    on: { change: _vm.incarcaLista },
                    model: {
                      value: _vm.filtre.cautare,
                      callback: function ($$v) {
                        _vm.$set(_vm.filtre, "cautare", $$v)
                      },
                      expression: "filtre.cautare",
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
            "b-row",
            { staticClass: "mb-3" },
            [
              _c(
                "b-col",
                {
                  staticClass: "d-flex align-items-center",
                  attrs: { md: "3" },
                },
                [
                  _c(
                    "b-form-checkbox",
                    {
                      on: { change: _vm.incarcaLista },
                      model: {
                        value: _vm.filtre.doar_esecuri,
                        callback: function ($$v) {
                          _vm.$set(_vm.filtre, "doar_esecuri", $$v)
                        },
                        expression: "filtre.doar_esecuri",
                      },
                    },
                    [_vm._v("\n          Doar operațiile eșuate\n        ")]
                  ),
                ],
                1
              ),
              _vm._v(" "),
              _c(
                "b-col",
                {
                  staticClass: "d-flex align-items-center",
                  attrs: { md: "3" },
                },
                [
                  _c(
                    "b-button",
                    {
                      staticClass: "mr-1",
                      attrs: { variant: "outline-secondary", size: "sm" },
                      on: { click: _vm.resetFiltre },
                    },
                    [_vm._v("\n          Șterge filtrele\n        ")]
                  ),
                  _vm._v(" "),
                  _c(
                    "b-button",
                    {
                      attrs: {
                        variant: "outline-success",
                        size: "sm",
                        disabled: !_vm.intrari.length || _vm.exportInCurs,
                      },
                      on: { click: _vm.exporta },
                    },
                    [
                      _vm.exportInCurs
                        ? _c("b-spinner", {
                            staticClass: "mr-50",
                            attrs: { small: "" },
                          })
                        : _c("feather-icon", {
                            staticClass: "mr-50",
                            attrs: { icon: "FileTextIcon", size: "14" },
                          }),
                      _vm._v("Export xls\n        "),
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
          _vm.eroare
            ? _c("b-alert", { attrs: { show: "", variant: "danger" } }, [
                _vm._v("\n      " + _vm._s(_vm.eroare) + "\n    "),
              ])
            : _vm._e(),
          _vm._v(" "),
          _c("b-table", {
            attrs: {
              items: _vm.intrari,
              "per-page": _vm.pePagina,
              "current-page": _vm.pagina,
              fields: _vm.campuri,
              busy: _vm.listaInCurs,
              responsive: "",
              striped: "",
              small: "",
              "show-empty": "",
              "empty-text":
                "Nicio activitate înregistrată pentru filtrele alese.",
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
                        _vm._v("\n          Se încarcă jurnalul...\n        "),
                      ],
                      1
                    ),
                  ]
                },
                proxy: true,
              },
              {
                key: "cell(descriere)",
                fn: function (rand) {
                  return [
                    _c(
                      "span",
                      { class: rand.item.reusit ? "" : "text-danger" },
                      [_vm._v(_vm._s(rand.item.descriere))]
                    ),
                    _vm._v(" "),
                    !rand.item.reusit
                      ? _c(
                          "b-badge",
                          { staticClass: "ml-1", attrs: { variant: "danger" } },
                          [_vm._v("\n          eșuat\n        ")]
                        )
                      : _vm._e(),
                  ]
                },
              },
              {
                key: "cell(email)",
                fn: function (rand) {
                  return [
                    _vm._v(
                      "\n        " + _vm._s(rand.item.email || "-") + "\n      "
                    ),
                  ]
                },
              },
              {
                key: "cell(cif)",
                fn: function (rand) {
                  return [
                    _vm._v(
                      "\n        " + _vm._s(rand.item.cif || "-") + "\n      "
                    ),
                  ]
                },
              },
            ]),
          }),
          _vm._v(" "),
          _c("paginare", {
            attrs: { "per-page": _vm.pePagina, total: _vm.intrari.length },
            on: {
              "update:perPage": function ($event) {
                _vm.pePagina = $event
              },
              "update:per-page": function ($event) {
                _vm.pePagina = $event
              },
            },
            model: {
              value: _vm.pagina,
              callback: function ($$v) {
                _vm.pagina = $$v
              },
              expression: "pagina",
            },
          }),
          _vm._v(" "),
          _c("p", { staticClass: "text-muted small" }, [
            _vm._v(
              "\n      Se afișează cele mai recente " +
                _vm._s(_vm.intrari.length) +
                " înregistrări.\n    "
            ),
          ]),
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

/***/ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/app_pages/spv/Mesaje.vue?vue&type=template&id=96f45f2c&":
/*!**********************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/src/views/app_pages/spv/Mesaje.vue?vue&type=template&id=96f45f2c& ***!
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
  return _c("div", { staticClass: "card" }, [
    _c(
      "div",
      { staticClass: "card-body" },
      [
        _c("div", { staticClass: "row mb-3" }, [
          _c(
            "div",
            { staticClass: "col-md-3" },
            [
              _c("label", { staticClass: "form-label" }, [
                _vm._v("Certificat (token)"),
              ]),
              _vm._v(" "),
              _c("b-form-select", {
                attrs: { options: _vm.optiuniCertificatToken },
                model: {
                  value: _vm.certificatAles,
                  callback: function ($$v) {
                    _vm.certificatAles = $$v
                  },
                  expression: "certificatAles",
                },
              }),
              _vm._v(" "),
              _c("small", { staticClass: "text-muted" }, [
                _vm._v(
                  "\n          " +
                    _vm._s(
                      _vm.certificatAles
                        ? "se descarcă doar de la acest token"
                        : "se descarcă de la toate tokenurile"
                    ) +
                    "\n        "
                ),
              ]),
            ],
            1
          ),
          _vm._v(" "),
          _c(
            "div",
            { staticClass: "col-md-3" },
            [
              _c(
                "div",
                {
                  staticClass:
                    "d-flex align-items-center justify-content-between",
                },
                [
                  _c("label", { staticClass: "form-label mb-0" }, [
                    _vm._v("\n            Firme\n            "),
                    _vm.firme.length
                      ? _c("span", { staticClass: "text-muted" }, [
                          _vm._v(
                            "· " +
                              _vm._s(_vm.firme.length) +
                              " " +
                              _vm._s(
                                _vm.firme.length === 1 ? "aleasă" : "alese"
                              )
                          ),
                        ])
                      : _vm._e(),
                  ]),
                  _vm._v(" "),
                  _c(
                    "b-button",
                    {
                      staticClass: "py-0 px-50",
                      attrs: {
                        size: "sm",
                        variant: "flat-primary",
                        disabled: !_vm.optiuniFirmeInrolate.length,
                      },
                      on: { click: _vm.alegeToateFirmele },
                    },
                    [
                      _c("small", [
                        _vm._v(
                          _vm._s(
                            _vm.firme.length === _vm.optiuniFirmeInrolate.length
                              ? "niciuna"
                              : "toate"
                          )
                        ),
                      ]),
                    ]
                  ),
                ],
                1
              ),
              _vm._v(" "),
              _c("v-select", {
                staticClass: "select-firme",
                attrs: {
                  multiple: "",
                  label: "eticheta",
                  options: _vm.optiuniFirmeInrolate,
                  placeholder: "Toate firmele înrolate",
                },
                scopedSlots: _vm._u([
                  {
                    key: "no-options",
                    fn: function () {
                      return [
                        _vm._v(
                          "\n            Nicio entitate înrolată. Sincronizați-le mai întâi.\n          "
                        ),
                      ]
                    },
                    proxy: true,
                  },
                ]),
                model: {
                  value: _vm.firme,
                  callback: function ($$v) {
                    _vm.firme = $$v
                  },
                  expression: "firme",
                },
              }),
            ],
            1
          ),
          _vm._v(" "),
          _c("div", { staticClass: "col-md-2" }, [
            _c("label", { staticClass: "form-label" }, [
              _vm._v("Zile de interogat la ANAF"),
            ]),
            _vm._v(" "),
            _c("input", {
              directives: [
                {
                  name: "model",
                  rawName: "v-model.number",
                  value: _vm.zile,
                  expression: "zile",
                  modifiers: { number: true },
                },
              ],
              staticClass: "form-control",
              attrs: { type: "number", min: "1", max: "60" },
              domProps: { value: _vm.zile },
              on: {
                input: function ($event) {
                  if ($event.target.composing) {
                    return
                  }
                  _vm.zile = _vm._n($event.target.value)
                },
                blur: function ($event) {
                  return _vm.$forceUpdate()
                },
              },
            }),
          ]),
          _vm._v(" "),
          _c(
            "div",
            {
              staticClass:
                "col-md-4 d-flex flex-column justify-content-end align-items-end",
            },
            [
              _c(
                "div",
                { staticClass: "d-flex align-items-end" },
                [
                  _c(
                    "b-button",
                    {
                      staticClass: "mr-1",
                      attrs: { variant: "outline-primary" },
                      on: { click: _vm.deschideAlerte },
                    },
                    [
                      _c("feather-icon", {
                        staticClass: "mr-50",
                        attrs: { icon: "BellIcon" },
                      }),
                      _vm._v("Configurare alerte\n          "),
                    ],
                    1
                  ),
                  _vm._v(" "),
                  _c(
                    "b-dropdown",
                    {
                      attrs: {
                        split: "",
                        variant: "outline-success",
                        disabled: _vm.loading,
                      },
                      on: {
                        click: function ($event) {
                          return _vm.loadMessages()
                        },
                      },
                      scopedSlots: _vm._u([
                        {
                          key: "button-content",
                          fn: function () {
                            return [
                              _vm.loading
                                ? _c("b-spinner", {
                                    staticClass: "mr-1",
                                    attrs: { small: "" },
                                  })
                                : _c("feather-icon", {
                                    staticClass: "mr-1",
                                    attrs: { icon: "DownloadIcon", size: "15" },
                                  }),
                              _vm._v(
                                "\n              Descarcă mesaje" +
                                  _vm._s(_vm.automat.activ ? " automat" : "") +
                                  "\n              "
                              ),
                              _vm.fisiereLipsa
                                ? _c(
                                    "b-badge",
                                    {
                                      staticClass: "ml-1",
                                      attrs: { variant: "warning" },
                                    },
                                    [
                                      _vm._v(
                                        "\n                " +
                                          _vm._s(_vm.fisiereLipsa) +
                                          "\n              "
                                      ),
                                    ]
                                  )
                                : _vm._e(),
                            ]
                          },
                          proxy: true,
                        },
                      ]),
                    },
                    [
                      _vm._v(" "),
                      _c(
                        "b-dropdown-item",
                        {
                          attrs: { active: !_vm.automat.activ },
                          on: {
                            click: function ($event) {
                              _vm.automat.activ = false
                            },
                          },
                        },
                        [
                          _vm._v(
                            "\n              Descarcă mesaje\n            "
                          ),
                        ]
                      ),
                      _vm._v(" "),
                      _c(
                        "b-dropdown-item",
                        {
                          attrs: { active: _vm.automat.activ },
                          on: {
                            click: function ($event) {
                              _vm.automat.activ = true
                            },
                          },
                        },
                        [
                          _vm._v(
                            "\n              Descarcă mesaje automat\n            "
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
              _c("div", { staticClass: "automat-mesaje mt-1 text-right" }, [
                _vm.automat.activ
                  ? _c(
                      "div",
                      {
                        staticClass:
                          "d-flex align-items-center justify-content-end",
                      },
                      [
                        _c("small", { staticClass: "text-success mr-1" }, [
                          _vm._v("la"),
                        ]),
                        _vm._v(" "),
                        _c("b-form-select", {
                          staticClass: "lista-minute",
                          attrs: { size: "sm", options: _vm.optiuniMinute },
                          model: {
                            value: _vm.automat.minute,
                            callback: function ($$v) {
                              _vm.$set(_vm.automat, "minute", _vm._n($$v))
                            },
                            expression: "automat.minute",
                          },
                        }),
                      ],
                      1
                    )
                  : _vm._e(),
                _vm._v(" "),
                _c("small", { staticClass: "text-muted" }, [
                  _vm.ultimaDescarcare
                    ? _c("span", [
                        _vm._v(
                          "\n              Ultima descărcare: " +
                            _vm._s(_vm.ultimaDescarcare) +
                            " · " +
                            _vm._s(_vm.mesajeAduse) +
                            "\n            "
                        ),
                      ])
                    : _c("span", [_vm._v("Nicio descărcare încă")]),
                ]),
              ]),
            ]
          ),
        ]),
        _vm._v(" "),
        _c(
          "b-modal",
          {
            attrs: {
              title: "Alerte pe email pentru documente noi în SPV",
              size: "lg",
              "ok-only": "",
              "ok-title": "Închide",
              "modal-class": "modul-spv",
            },
            model: {
              value: _vm.alerteVizibile,
              callback: function ($$v) {
                _vm.alerteVizibile = $$v
              },
              expression: "alerteVizibile",
            },
          },
          [
            _vm.eroareAlerte
              ? _c(
                  "b-alert",
                  {
                    staticClass: "py-1 px-2 small",
                    attrs: { show: "", variant: "danger" },
                  },
                  [_vm._v("\n        " + _vm._s(_vm.eroareAlerte) + "\n      ")]
                )
              : _vm._e(),
            _vm._v(" "),
            _c("p", { staticClass: "text-muted small" }, [
              _vm._v(
                "\n        Când ANAF pune în SPV un document de tipul ales, adresa primește o\n        înștiințare. Lăsate goale, certificatul și firma înseamnă „oricare”.\n      "
              ),
            ]),
            _vm._v(" "),
            _vm.alerte.length
              ? _c("b-table", {
                  staticClass: "mb-2",
                  attrs: {
                    items: _vm.alerte,
                    fields: _vm.campuriAlerte,
                    small: "",
                    responsive: "",
                  },
                  scopedSlots: _vm._u(
                    [
                      {
                        key: "cell(unde)",
                        fn: function (rand) {
                          return [
                            _c("div", [
                              _vm._v(
                                _vm._s(
                                  rand.item.certificat_nume ||
                                    "orice certificat"
                                )
                              ),
                            ]),
                            _vm._v(" "),
                            _c("div", { staticClass: "small text-muted" }, [
                              _vm._v(
                                "\n            " +
                                  _vm._s(_vm.numeFirma(rand.item.cif)) +
                                  "\n          "
                              ),
                            ]),
                          ]
                        },
                      },
                      {
                        key: "cell(tip_document)",
                        fn: function (rand) {
                          return [
                            rand.item.doar_cand_text
                              ? _c("span", { staticClass: "text-success" }, [
                                  _vm._v(_vm._s(rand.item.doar_cand_text)),
                                ])
                              : _c("span", [
                                  _vm._v(
                                    _vm._s(
                                      rand.item.tip_document || "orice document"
                                    )
                                  ),
                                ]),
                          ]
                        },
                      },
                      {
                        key: "cell(trimise)",
                        fn: function (rand) {
                          return [
                            rand.item.trimise
                              ? _c("span", [
                                  _vm._v(
                                    "\n            " +
                                      _vm._s(rand.item.trimise) +
                                      "\n            "
                                  ),
                                  _c(
                                    "div",
                                    { staticClass: "small text-muted" },
                                    [_vm._v(_vm._s(rand.item.ultima_alerta_la))]
                                  ),
                                ])
                              : _c("span", { staticClass: "text-muted" }, [
                                  _vm._v("—"),
                                ]),
                          ]
                        },
                      },
                      {
                        key: "cell(actiuni)",
                        fn: function (rand) {
                          return [
                            _c(
                              "b-button",
                              {
                                staticClass: "btn-icon",
                                attrs: {
                                  size: "sm",
                                  variant: "flat-secondary",
                                  title: "Modifică",
                                },
                                on: {
                                  click: function ($event) {
                                    return _vm.editeazaAlerta(rand.item)
                                  },
                                },
                              },
                              [
                                _c("feather-icon", {
                                  attrs: { icon: "Edit2Icon" },
                                }),
                              ],
                              1
                            ),
                            _vm._v(" "),
                            _c(
                              "b-button",
                              {
                                staticClass: "btn-icon",
                                attrs: {
                                  size: "sm",
                                  variant: "flat-danger",
                                  title: "Șterge",
                                },
                                on: {
                                  click: function ($event) {
                                    return _vm.stergeAlerta(rand.item)
                                  },
                                },
                              },
                              [
                                _c("feather-icon", {
                                  attrs: { icon: "Trash2Icon" },
                                }),
                              ],
                              1
                            ),
                          ]
                        },
                      },
                    ],
                    null,
                    false,
                    884567262
                  ),
                })
              : _vm._e(),
            _vm._v(" "),
            _c("hr"),
            _vm._v(" "),
            _c("h6", [
              _vm._v(_vm._s(_vm.alerta.id ? "Modifică alerta" : "Alertă nouă")),
            ]),
            _vm._v(" "),
            _c(
              "b-row",
              [
                _c(
                  "b-col",
                  { attrs: { md: "6" } },
                  [
                    _c("label", [_vm._v("Adresa de email")]),
                    _vm._v(" "),
                    _c("b-form-input", {
                      staticClass: "mb-2",
                      attrs: { type: "email", placeholder: "nume@firma.ro" },
                      model: {
                        value: _vm.alerta.email,
                        callback: function ($$v) {
                          _vm.$set(_vm.alerta, "email", $$v)
                        },
                        expression: "alerta.email",
                      },
                    }),
                  ],
                  1
                ),
                _vm._v(" "),
                _c(
                  "b-col",
                  { attrs: { md: "6" } },
                  [
                    _c("label", [_vm._v("Când se trimite")]),
                    _vm._v(" "),
                    _c("b-form-select", {
                      staticClass: "mb-1",
                      attrs: { options: _vm.optiuniConstatari },
                      model: {
                        value: _vm.alerta.doar_cand,
                        callback: function ($$v) {
                          _vm.$set(_vm.alerta, "doar_cand", $$v)
                        },
                        expression: "alerta.doar_cand",
                      },
                    }),
                    _vm._v(" "),
                    !_vm.alerta.doar_cand
                      ? [
                          _c("b-form-input", {
                            staticClass: "mb-1",
                            attrs: {
                              list: "tipuri-documente",
                              placeholder: "orice document",
                            },
                            model: {
                              value: _vm.alerta.tip_document,
                              callback: function ($$v) {
                                _vm.$set(_vm.alerta, "tip_document", $$v)
                              },
                              expression: "alerta.tip_document",
                            },
                          }),
                          _vm._v(" "),
                          _c("b-form-datalist", {
                            attrs: {
                              id: "tipuri-documente",
                              options: _vm.optiuniTipuri,
                            },
                          }),
                          _vm._v(" "),
                          _c(
                            "small",
                            { staticClass: "text-muted d-block mb-2" },
                            [
                              _vm.tipuriVazute
                                ? _c("span", [
                                    _vm._v(
                                      "\n                Primele " +
                                        _vm._s(_vm.tipuriVazute) +
                                        " din listă au apărut deja în mesajele\n                dumneavoastră; restul sunt feluri obișnuite.\n              "
                                    ),
                                  ])
                                : _vm._e(),
                              _vm._v(
                                "\n              Puteți scrie și altceva: se potrivește pe bucată de text, fără să\n              conteze literele mari sau mici.\n            "
                              ),
                            ]
                          ),
                        ]
                      : _c(
                          "small",
                          { staticClass: "text-success d-block mb-2" },
                          [
                            _vm._v(
                              "\n            Pleacă numai la firmele la care se constată acest lucru — nu la\n            fiecare document sosit.\n          "
                            ),
                          ]
                        ),
                  ],
                  2
                ),
              ],
              1
            ),
            _vm._v(" "),
            _c(
              "b-row",
              [
                _c(
                  "b-col",
                  { attrs: { md: "6" } },
                  [
                    _c("label", [_vm._v("Certificatul digital")]),
                    _vm._v(" "),
                    _c("b-form-select", {
                      staticClass: "mb-2",
                      attrs: { options: _vm.optiuniCertificate },
                      model: {
                        value: _vm.alerta.certificat_id,
                        callback: function ($$v) {
                          _vm.$set(_vm.alerta, "certificat_id", $$v)
                        },
                        expression: "alerta.certificat_id",
                      },
                    }),
                  ],
                  1
                ),
                _vm._v(" "),
                _c(
                  "b-col",
                  { attrs: { md: "6" } },
                  [
                    _c("label", [_vm._v("Firma")]),
                    _vm._v(" "),
                    _c("b-form-select", {
                      staticClass: "mb-2",
                      attrs: { options: _vm.optiuniFirme },
                      model: {
                        value: _vm.alerta.cif,
                        callback: function ($$v) {
                          _vm.$set(_vm.alerta, "cif", $$v)
                        },
                        expression: "alerta.cif",
                      },
                    }),
                    _vm._v(" "),
                    _c("small", { staticClass: "text-muted" }, [
                      _vm._v(
                        "\n            Nealeasă, alerta prinde toate firmele înrolate certificatului de alături.\n          "
                      ),
                    ]),
                  ],
                  1
                ),
              ],
              1
            ),
            _vm._v(" "),
            _c(
              "div",
              { staticClass: "d-flex align-items-center mt-1" },
              [
                _c(
                  "b-form-checkbox",
                  {
                    model: {
                      value: _vm.alerta.activ,
                      callback: function ($$v) {
                        _vm.$set(_vm.alerta, "activ", $$v)
                      },
                      expression: "alerta.activ",
                    },
                  },
                  [_vm._v("\n          Activă\n        ")]
                ),
                _vm._v(" "),
                _c(
                  "b-button",
                  {
                    staticClass: "ml-auto",
                    attrs: {
                      variant: "primary",
                      size: "sm",
                      disabled: !_vm.alerta.email,
                    },
                    on: { click: _vm.salveazaAlerta },
                  },
                  [
                    _vm._v(
                      "\n          " +
                        _vm._s(_vm.alerta.id ? "Salvează" : "Adaugă") +
                        "\n        "
                    ),
                  ]
                ),
                _vm._v(" "),
                _vm.alerta.id
                  ? _c(
                      "b-button",
                      {
                        staticClass: "ml-1",
                        attrs: { variant: "flat-secondary", size: "sm" },
                        on: { click: _vm.alertaNoua },
                      },
                      [_vm._v("\n          Renunță\n        ")]
                    )
                  : _vm._e(),
              ],
              1
            ),
          ],
          1
        ),
        _vm._v(" "),
        _vm.loading
          ? _c(
              "div",
              { staticClass: "text-muted" },
              [
                _vm._v(
                  "\n      " +
                    _vm._s(
                      _vm.mersul ||
                        "Se încarcă mesajele SPV și se descarcă fișierele..."
                    ) +
                    "\n      "
                ),
                _vm.deAdus
                  ? _c("b-progress", {
                      staticClass: "mt-1",
                      attrs: {
                        value: _vm.aduse,
                        max: _vm.deAdus,
                        variant: "success",
                        height: "6px",
                      },
                    })
                  : _vm._e(),
              ],
              1
            )
          : _vm.error
          ? _c("div", { staticClass: "alert alert-danger" }, [
              _vm._v("\n      " + _vm._s(_vm.error) + "\n    "),
            ])
          : !_vm.messages.length
          ? _c("div", { staticClass: "text-muted" }, [
              _vm._v("\n      Nu există mesaje pentru filtrul selectat.\n    "),
            ])
          : _c(
              "div",
              [
                _vm.infoDescarcare
                  ? _c("div", { staticClass: "alert alert-info py-2" }, [
                      _vm._v(
                        "\n        " + _vm._s(_vm.infoDescarcare) + "\n      "
                      ),
                    ])
                  : _vm._e(),
                _vm._v(" "),
                _c("p", { staticClass: "text-muted small mb-2" }, [
                  _vm.mesajeFiltrate.length !== _vm.messages.length
                    ? _c("span", [
                        _vm._v(
                          "\n          " +
                            _vm._s(_vm.mesajeFiltrate.length) +
                            " din " +
                            _vm._s(_vm.messages.length) +
                            " mesaje\n        "
                        ),
                      ])
                    : _c("span", [
                        _vm._v(
                          _vm._s(_vm.messages.length) + " mesaje în istoric"
                        ),
                      ]),
                  _vm._v(
                    ".\n        Tabelul arată ce este deja stocat; „Descarcă mesaje” aduce noutățile de\n        la ANAF, pe fereastra de zile de mai sus.\n      "
                  ),
                ]),
                _vm._v(" "),
                _c("table", { staticClass: "table table-sm table-bordered" }, [
                  _c("thead", [
                    _vm._m(0),
                    _vm._v(" "),
                    _c(
                      "tr",
                      { staticClass: "rand-filtre" },
                      [
                        _vm._l(_vm.campuriFiltrabile, function (camp) {
                          return _c(
                            "th",
                            { key: camp.cheie },
                            [
                              _c("b-form-input", {
                                attrs: {
                                  size: "sm",
                                  placeholder: camp.eticheta,
                                },
                                model: {
                                  value: _vm.filtre[camp.cheie],
                                  callback: function ($$v) {
                                    _vm.$set(_vm.filtre, camp.cheie, $$v)
                                  },
                                  expression: "filtre[camp.cheie]",
                                },
                              }),
                            ],
                            1
                          )
                        }),
                        _vm._v(" "),
                        _c(
                          "th",
                          [
                            _c("b-form-select", {
                              attrs: {
                                size: "sm",
                                options: [
                                  { value: "", text: "toate" },
                                  { value: "da", text: "descărcate" },
                                  { value: "nu", text: "nedescărcate" },
                                ],
                              },
                              model: {
                                value: _vm.filtre.descarcat,
                                callback: function ($$v) {
                                  _vm.$set(_vm.filtre, "descarcat", $$v)
                                },
                                expression: "filtre.descarcat",
                              },
                            }),
                          ],
                          1
                        ),
                      ],
                      2
                    ),
                  ]),
                  _vm._v(" "),
                  _c(
                    "tbody",
                    _vm._l(_vm.mesajePagina, function (item, index) {
                      return _c("tr", { key: index }, [
                        _c("td", [_vm._v(_vm._s(item.tip || "-"))]),
                        _vm._v(" "),
                        _c("td", [_vm._v(_vm._s(item.cif || "-"))]),
                        _vm._v(" "),
                        _c("td", [_vm._v(_vm._s(item.den_firma || "-"))]),
                        _vm._v(" "),
                        _c("td", [_vm._v(_vm._s(item.data_creare || "-"))]),
                        _vm._v(" "),
                        _c("td", [_vm._v(_vm._s(item.id_solicitare || "-"))]),
                        _vm._v(" "),
                        _c("td", [_vm._v(_vm._s(item.detalii || "-"))]),
                        _vm._v(" "),
                        _c("td", [_vm._v(_vm._s(item.certificat || "-"))]),
                        _vm._v(" "),
                        _c("td", [
                          item.descarcat
                            ? _c(
                                "button",
                                {
                                  staticClass:
                                    "btn btn-sm btn-outline-secondary",
                                  on: {
                                    click: function ($event) {
                                      return _vm.openFile(item.id)
                                    },
                                  },
                                },
                                [
                                  _vm._v(
                                    "\n                Deschide\n              "
                                  ),
                                ]
                              )
                            : _c(
                                "span",
                                {
                                  class: item.ultima_eroare
                                    ? "text-danger"
                                    : "text-muted",
                                },
                                [
                                  _vm._v(
                                    "\n                " +
                                      _vm._s(_vm.statusFisier(item)) +
                                      "\n              "
                                  ),
                                ]
                              ),
                        ]),
                      ])
                    }),
                    0
                  ),
                ]),
                _vm._v(" "),
                _c("paginare", {
                  attrs: {
                    "per-page": _vm.pePagina,
                    total: _vm.mesajeFiltrate.length,
                  },
                  on: {
                    "update:perPage": function ($event) {
                      _vm.pePagina = $event
                    },
                    "update:per-page": function ($event) {
                      _vm.pePagina = $event
                    },
                  },
                  model: {
                    value: _vm.pagina,
                    callback: function ($$v) {
                      _vm.pagina = $$v
                    },
                    expression: "pagina",
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
var staticRenderFns = [
  function () {
    var _vm = this
    var _h = _vm.$createElement
    var _c = _vm._self._c || _h
    return _c("tr", [
      _c("th", [_vm._v("Tip")]),
      _vm._v(" "),
      _c("th", [_vm._v("CIF")]),
      _vm._v(" "),
      _c("th", [_vm._v("Denumire")]),
      _vm._v(" "),
      _c("th", [_vm._v("Data creare")]),
      _vm._v(" "),
      _c("th", [_vm._v("ID solicitare")]),
      _vm._v(" "),
      _c("th", [_vm._v("Detalii")]),
      _vm._v(" "),
      _c("th", [_vm._v("Certificat")]),
      _vm._v(" "),
      _c("th", [_vm._v("Fișier")]),
    ])
  },
]
render._withStripped = true



/***/ }),

/***/ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/app_pages/spv/Societati.vue?vue&type=template&id=e2aaca68&scoped=true&":
/*!*************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/src/views/app_pages/spv/Societati.vue?vue&type=template&id=e2aaca68&scoped=true& ***!
  \*************************************************************************************************************************************************************************************************************************************/
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
        { attrs: { title: "Entități cu drept de semnătură" } },
        [
          _c("p", { staticClass: "text-muted" }, [
            _vm._v(
              "\n      Lista este preluată de la ANAF — sunt CIF-urile pentru care certificatul de pe token\n      are drepturi în SPV. Denumirile se completează din vectorul fiscal și din documentul\n      de date de identificare, apoi sunt folosite la mesajele și solicitările SPV.\n    "
            ),
          ]),
          _vm._v(" "),
          _c(
            "b-row",
            { staticClass: "mb-3" },
            [
              _c(
                "b-col",
                { attrs: { md: "3" } },
                [
                  _c(
                    "b-button",
                    {
                      attrs: {
                        variant: "primary",
                        disabled: _vm.sincronizareInCurs,
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
                      _vm._v(
                        "\n          Inițializează / actualizează lista\n        "
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
                { attrs: { md: "3" } },
                [
                  _c(
                    "b-button",
                    {
                      attrs: {
                        variant: "outline-primary",
                        disabled: _vm.solicitareInCurs,
                      },
                      on: { click: _vm.solicita },
                    },
                    [
                      _vm.solicitareInCurs
                        ? _c("b-spinner", {
                            staticClass: "mr-1",
                            attrs: { small: "" },
                          })
                        : _vm._e(),
                      _vm._v(
                        "\n          Solicită datele lipsă din SPV\n        "
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
                { attrs: { md: "6" } },
                [
                  _c(
                    "b-button",
                    {
                      staticClass: "mr-1",
                      attrs: { variant: "outline-primary" },
                      on: { click: _vm.deschideVector },
                    },
                    [
                      _c("feather-icon", {
                        staticClass: "mr-50",
                        attrs: { icon: "FileTextIcon", size: "14" },
                      }),
                      _vm._v(
                        "\n          Raport depunere declarații\n        "
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
            "b-row",
            { staticClass: "mb-2" },
            [
              _c(
                "b-col",
                { staticClass: "d-flex align-items-center" },
                [
                  _c(
                    "b-form-checkbox",
                    {
                      on: { change: _vm.incarcaLista },
                      model: {
                        value: _vm.arataToate,
                        callback: function ($$v) {
                          _vm.arataToate = $$v
                        },
                        expression: "arataToate",
                      },
                    },
                    [
                      _vm._v(
                        "\n          Arată și cele scoase din uz sau fără drepturi\n        "
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
          _vm.progres
            ? _c(
                "div",
                { staticClass: "mb-3" },
                [
                  _c(
                    "div",
                    {
                      staticClass:
                        "d-flex justify-content-between align-items-center mb-50",
                    },
                    [
                      _c("small", { staticClass: "text-muted" }, [
                        _vm._v(_vm._s(_vm.progres.text)),
                      ]),
                      _vm._v(" "),
                      _vm.progres.pas === "solicitari"
                        ? _c("small", { staticClass: "text-muted" }, [
                            _vm._v(
                              "\n          " +
                                _vm._s(
                                  Math.round(
                                    (_vm.progres.facut /
                                      Math.max(_vm.progres.total, 1)) *
                                      100
                                  )
                                ) +
                                "%\n        "
                            ),
                          ])
                        : _vm._e(),
                    ]
                  ),
                  _vm._v(" "),
                  _c(
                    "b-progress",
                    {
                      attrs: {
                        max: Math.max(_vm.progres.total, 1),
                        height: "6px",
                        animated: _vm.progres.pas !== "solicitari",
                      },
                    },
                    [
                      _c("b-progress-bar", {
                        attrs: {
                          value:
                            _vm.progres.pas === "solicitari"
                              ? _vm.progres.facut
                              : Math.max(_vm.progres.total, 1),
                          variant:
                            _vm.progres.pas === "date" ? "success" : "primary",
                        },
                      }),
                    ],
                    1
                  ),
                ],
                1
              )
            : _vm._e(),
          _vm._v(" "),
          _vm.eroare
            ? _c("b-alert", { attrs: { show: "", variant: "danger" } }, [
                _vm._v("\n      " + _vm._s(_vm.eroare) + "\n    "),
              ])
            : _vm._e(),
          _vm._v(" "),
          _vm.info
            ? _c(
                "b-alert",
                { staticClass: "py-2", attrs: { show: "", variant: "info" } },
                [_vm._v("\n      " + _vm._s(_vm.info) + "\n    ")]
              )
            : _vm._e(),
          _vm._v(" "),
          _c("b-table", {
            attrs: {
              items: _vm.societatiFiltrate,
              "per-page": _vm.pePagina,
              "current-page": _vm.pagina,
              fields: _vm.campuri,
              busy: _vm.listaInCurs,
              responsive: "",
              striped: "",
              small: "",
              "show-empty": "",
              "empty-text": "Nicio entitate pentru filtrul ales.",
            },
            scopedSlots: _vm._u([
              {
                key: "head(cif)",
                fn: function (date) {
                  return [
                    _c("div", [_vm._v(_vm._s(date.label))]),
                    _vm._v(" "),
                    _c("b-form-input", {
                      staticClass: "filtru-coloana",
                      attrs: { size: "sm", placeholder: "CIF" },
                      model: {
                        value: _vm.filtre.cif,
                        callback: function ($$v) {
                          _vm.$set(_vm.filtre, "cif", $$v)
                        },
                        expression: "filtre.cif",
                      },
                    }),
                  ]
                },
              },
              {
                key: "head(denumire)",
                fn: function (date) {
                  return [
                    _c("div", [_vm._v(_vm._s(date.label))]),
                    _vm._v(" "),
                    _c("b-form-input", {
                      staticClass: "filtru-coloana",
                      attrs: { size: "sm", placeholder: "Denumire" },
                      model: {
                        value: _vm.filtre.denumire,
                        callback: function ($$v) {
                          _vm.$set(_vm.filtre, "denumire", $$v)
                        },
                        expression: "filtre.denumire",
                      },
                    }),
                  ]
                },
              },
              {
                key: "head(tip)",
                fn: function (date) {
                  return [
                    _c("div", [_vm._v(_vm._s(date.label))]),
                    _vm._v(" "),
                    _c("b-form-select", {
                      staticClass: "filtru-coloana",
                      attrs: {
                        size: "sm",
                        options: [
                          { value: "", text: "toate" },
                          { value: "pj", text: "persoane juridice" },
                          { value: "pf", text: "persoane fizice" },
                        ],
                      },
                      model: {
                        value: _vm.filtre.tip,
                        callback: function ($$v) {
                          _vm.$set(_vm.filtre, "tip", $$v)
                        },
                        expression: "filtre.tip",
                      },
                    }),
                  ]
                },
              },
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
                key: "cell(denumire)",
                fn: function (rand) {
                  return [
                    rand.item.denumire
                      ? _c("span", [_vm._v(_vm._s(rand.item.denumire))])
                      : _c("span", { staticClass: "text-muted" }, [
                          _vm._v("necunoscută — solicitați datele din SPV"),
                        ]),
                    _vm._v(" "),
                    rand.item.denumire_sursa
                      ? _c(
                          "b-badge",
                          {
                            staticClass: "ml-1",
                            attrs: { variant: "primary" },
                          },
                          [
                            _vm._v(
                              "\n          " +
                                _vm._s(
                                  _vm.etichetaSursa(rand.item.denumire_sursa)
                                ) +
                                "\n        "
                            ),
                          ]
                        )
                      : _vm._e(),
                  ]
                },
              },
              {
                key: "cell(tip)",
                fn: function (rand) {
                  return [
                    _vm._v(
                      "\n        " +
                        _vm._s(
                          rand.item.tip === "pf"
                            ? "Persoană fizică"
                            : "Persoană juridică"
                        ) +
                        "\n      "
                    ),
                  ]
                },
              },
              {
                key: "cell(activ)",
                fn: function (rand) {
                  return [
                    rand.item.activ
                      ? _c(
                          "b-button",
                          {
                            attrs: {
                              size: "sm",
                              variant: rand.item.scos_din_uz
                                ? "outline-secondary"
                                : "success",
                              disabled: _vm.schimbaStarea === rand.item.id,
                              title: rand.item.scos_din_uz
                                ? "Este ignorată peste tot. Apăsați ca să fie iar luată în seamă."
                                : "Se lucrează cu ea. Apăsați ca să fie ignorată peste tot.",
                            },
                            on: {
                              click: function ($event) {
                                return _vm.schimbaUzul(rand.item)
                              },
                            },
                          },
                          [
                            _vm.schimbaStarea === rand.item.id
                              ? _c("b-spinner", {
                                  staticClass: "mr-50",
                                  attrs: { small: "" },
                                })
                              : _vm._e(),
                            _vm._v(
                              "\n          " +
                                _vm._s(
                                  rand.item.scos_din_uz
                                    ? "Scoasă din uz"
                                    : "În lucru"
                                ) +
                                "\n        "
                            ),
                          ],
                          1
                        )
                      : _c(
                          "b-badge",
                          {
                            attrs: {
                              variant: "secondary",
                              title:
                                "ANAF nu mai dă drepturi pe această entitate acestui certificat",
                            },
                          },
                          [_vm._v("\n          Fără drepturi\n        ")]
                        ),
                  ]
                },
              },
              {
                key: "cell(date)",
                fn: function (rand) {
                  return [
                    _c(
                      "div",
                      {
                        class: rand.item.vector_la
                          ? "text-success"
                          : "text-muted",
                      },
                      [
                        _vm._v(
                          "\n          Vector fiscal: " +
                            _vm._s(rand.item.vector_la || "lipsă") +
                            "\n        "
                        ),
                      ]
                    ),
                    _vm._v(" "),
                    _c(
                      "div",
                      {
                        class: rand.item.date_identificare_la
                          ? "text-success"
                          : "text-muted",
                      },
                      [
                        _vm._v(
                          "\n          Date identificare: " +
                            _vm._s(rand.item.date_identificare_la || "lipsă") +
                            "\n        "
                        ),
                      ]
                    ),
                  ]
                },
              },
              {
                key: "cell(certificat)",
                fn: function (rand) {
                  return [
                    rand.item.certificat
                      ? _c("div", [
                          _vm._v(
                            "\n          " +
                              _vm._s(rand.item.certificat) +
                              "\n          "
                          ),
                          _c("div", { staticClass: "small text-muted" }, [
                            _vm._v(
                              "\n            expiră " +
                                _vm._s(rand.item.certificat_expira) +
                                "\n          "
                            ),
                          ]),
                        ])
                      : _c("span", { staticClass: "text-muted" }, [
                          _vm._v("-"),
                        ]),
                  ]
                },
              },
              {
                key: "cell(actiuni)",
                fn: function (rand) {
                  return [
                    _c(
                      "b-button",
                      {
                        staticClass: "mr-50",
                        attrs: {
                          size: "sm",
                          variant: "outline-primary",
                          title:
                            "Redenumește — schimbă denumirea entității. Denumirea scrisă de mână are prioritate față de cea preluată din documentele SPV.",
                        },
                        on: {
                          click: function ($event) {
                            return _vm.editeaza(rand.item)
                          },
                        },
                      },
                      [
                        _c("feather-icon", {
                          attrs: { icon: "Edit2Icon", size: "14" },
                        }),
                      ],
                      1
                    ),
                    _vm._v(" "),
                    _c(
                      "b-button",
                      {
                        staticClass: "mr-50",
                        attrs: {
                          size: "sm",
                          variant: "outline-primary",
                          title:
                            "Vector fiscal — taxele în vigoare ale entității, așa cum le-a scris ANAF: cod, periodicitate și data intrării în vigoare. Doar de citit.",
                        },
                        on: {
                          click: function ($event) {
                            return _vm.deschideVectorFiscal(rand.item)
                          },
                        },
                      },
                      [
                        _c("feather-icon", {
                          attrs: { icon: "FileTextIcon", size: "14" },
                        }),
                      ],
                      1
                    ),
                    _vm._v(" "),
                    _c(
                      "b-button",
                      {
                        staticClass: "mr-50",
                        attrs: {
                          size: "sm",
                          variant: "outline-primary",
                          title:
                            "Actualizare frecvență declarații — declarațiile așteptate de la entitate: cele deduse din vectorul fiscal și din istoricul depunerilor, plus cele adăugate de dvs. Se pot adăuga, modifica și șterge — ele intră în raportul de depunere.",
                        },
                        on: {
                          click: function ($event) {
                            return _vm.deschideActualizare(rand.item)
                          },
                        },
                      },
                      [
                        _c("feather-icon", {
                          attrs: { icon: "CalendarIcon", size: "14" },
                        }),
                      ],
                      1
                    ),
                    _vm._v(" "),
                    _c(
                      "b-button",
                      {
                        attrs: {
                          size: "sm",
                          variant: "outline-primary",
                          title:
                            "Date pentru declarații — adresa, banca, contul, codul CAEN și cine semnează. Nu se află în fișierele de la ANAF, se scriu o dată aici și se iau de aici la fiecare declarație întocmită.",
                        },
                        on: {
                          click: function ($event) {
                            return _vm.deschideDateleDeclaratiilor(rand.item)
                          },
                        },
                      },
                      [
                        _c("feather-icon", {
                          attrs: { icon: "ClipboardIcon", size: "14" },
                        }),
                      ],
                      1
                    ),
                  ]
                },
              },
            ]),
          }),
          _vm._v(" "),
          _c("paginare", {
            attrs: {
              "per-page": _vm.pePagina,
              total: _vm.societatiFiltrate.length,
            },
            on: {
              "update:perPage": function ($event) {
                _vm.pePagina = $event
              },
              "update:per-page": function ($event) {
                _vm.pePagina = $event
              },
            },
            model: {
              value: _vm.pagina,
              callback: function ($$v) {
                _vm.pagina = $$v
              },
              expression: "pagina",
            },
          }),
        ],
        1
      ),
      _vm._v(" "),
      _c(
        "b-modal",
        {
          attrs: {
            title: "Raport depunere declarații",
            "modal-class": "modul-spv",
            "ok-title": _vm.vectorInCurs ? "Se întocmește..." : "Descarcă",
            "cancel-title": "Renunță",
            "ok-disabled": _vm.vectorInCurs,
          },
          on: {
            ok: function ($event) {
              $event.preventDefault()
              return _vm.descarcaVector.apply(null, arguments)
            },
          },
          model: {
            value: _vm.vectorVizibil,
            callback: function ($$v) {
              _vm.vectorVizibil = $$v
            },
            expression: "vectorVizibil",
          },
        },
        [
          _c("p", { staticClass: "text-muted small" }, [
            _vm._v(
              "\n      Din vectorul fiscal al fiecărei entități se deduc declarațiile lunii alese.\n      Pentru cele depuse se arată indexul recipisei cu data și ora depunerii;\n      pentru celelalte, periodicitatea obligației și atenționare dacă depunerea\n      era datorată chiar în luna aleasă.\n    "
            ),
          ]),
          _vm._v(" "),
          _c(
            "b-form-group",
            { attrs: { label: "Luna raportată" } },
            [
              _c(
                "b-row",
                [
                  _c(
                    "b-col",
                    { attrs: { cols: "7" } },
                    [
                      _c("b-form-select", {
                        attrs: { options: _vm.lunileAnului },
                        model: {
                          value: _vm.vector.luna,
                          callback: function ($$v) {
                            _vm.$set(_vm.vector, "luna", $$v)
                          },
                          expression: "vector.luna",
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
                      _c("b-form-select", {
                        attrs: { options: _vm.aniiDeAles },
                        model: {
                          value: _vm.vector.anul,
                          callback: function ($$v) {
                            _vm.$set(_vm.vector, "anul", $$v)
                          },
                          expression: "vector.anul",
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
          _c(
            "b-form-group",
            { attrs: { label: "Formatul dorit" } },
            [
              _c("b-form-radio-group", {
                attrs: {
                  options: [
                    { value: "pdf", text: "PDF" },
                    { value: "excel", text: "Excel" },
                  ],
                },
                model: {
                  value: _vm.vector.format,
                  callback: function ($$v) {
                    _vm.$set(_vm.vector, "format", $$v)
                  },
                  expression: "vector.format",
                },
              }),
            ],
            1
          ),
          _vm._v(" "),
          _vm.vectorEroare
            ? _c(
                "b-alert",
                {
                  staticClass: "py-1 px-2 mb-0",
                  attrs: { show: "", variant: "danger" },
                },
                [_vm._v("\n      " + _vm._s(_vm.vectorEroare) + "\n    ")]
              )
            : _vm._e(),
        ],
        1
      ),
      _vm._v(" "),
      _c(
        "b-modal",
        {
          attrs: {
            title: "Vector fiscal — " + _vm.etichetaEntitate(_vm.vfCui),
            "modal-class": "modul-spv",
            size: "lg",
            "ok-only": "",
            "ok-title": "Închide",
          },
          model: {
            value: _vm.vfVizibil,
            callback: function ($$v) {
              _vm.vfVizibil = $$v
            },
            expression: "vfVizibil",
          },
        },
        [
          _vm.vfEroare
            ? _c(
                "b-alert",
                {
                  staticClass: "py-1 px-2",
                  attrs: { show: "", variant: "danger" },
                },
                [_vm._v("\n      " + _vm._s(_vm.vfEroare) + "\n    ")]
              )
            : _vm._e(),
          _vm._v(" "),
          _c(
            "b-form-checkbox",
            {
              staticClass: "mb-1",
              model: {
                value: _vm.vfArataIstoricul,
                callback: function ($$v) {
                  _vm.vfArataIstoricul = $$v
                },
                expression: "vfArataIstoricul",
              },
            },
            [_vm._v("\n      Arată și obligațiile încheiate\n    ")]
          ),
          _vm._v(" "),
          _c("b-table", {
            attrs: {
              items: _vm.vfRanduriFiltrate,
              fields: [
                { key: "cod_imp", label: "Cod" },
                { key: "semnificatie", label: "Taxa" },
                { key: "perfisc", label: "Periodicitate" },
                { key: "data_inceput", label: "În vigoare de la" },
                { key: "data_sfarsit", label: "Până la" },
              ],
              busy: _vm.vfInCurs,
              responsive: "",
              striped: "",
              small: "",
              "show-empty": "",
              "empty-text":
                "Vectorul fiscal nu a fost încă preluat din SPV — apăsați „Solicită datele lipsă din SPV”.",
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
                key: "cell(data_sfarsit)",
                fn: function (rand) {
                  return [
                    rand.item.data_sfarsit
                      ? _c("span", [_vm._v(_vm._s(rand.item.data_sfarsit))])
                      : _c("b-badge", { attrs: { variant: "light-primary" } }, [
                          _vm._v("\n          în vigoare\n        "),
                        ]),
                  ]
                },
              },
            ]),
          }),
          _vm._v(" "),
          _vm.vfDataVector
            ? _c("p", { staticClass: "small text-muted mb-0" }, [
                _vm._v(
                  "\n      Vector fiscal extras din SPV la " +
                    _vm._s(_vm.vfDataVector) +
                    ".\n    "
                ),
              ])
            : _vm._e(),
        ],
        1
      ),
      _vm._v(" "),
      _c(
        "b-modal",
        {
          attrs: {
            title:
              "Actualizare frecvență declarații — " + _vm.actualizareDenumire,
            "modal-class": "modul-spv",
            size: "xl",
            "ok-only": "",
            "ok-title": "Închide",
          },
          model: {
            value: _vm.actualizareVizibila,
            callback: function ($$v) {
              _vm.actualizareVizibila = $$v
            },
            expression: "actualizareVizibila",
          },
        },
        [
          _c(
            "b-row",
            { staticClass: "mb-1 align-items-end" },
            [
              _c(
                "b-col",
                { attrs: { md: "3" } },
                [
                  _c("label", { staticClass: "small mb-0" }, [
                    _vm._v("Tip declarație"),
                  ]),
                  _vm._v(" "),
                  _c("b-form-input", {
                    attrs: {
                      size: "sm",
                      list: "tipuri-declaratii",
                      placeholder: "D300, BILANT...",
                    },
                    model: {
                      value: _vm.noua.tip,
                      callback: function ($$v) {
                        _vm.$set(_vm.noua, "tip", $$v)
                      },
                      expression: "noua.tip",
                    },
                  }),
                  _vm._v(" "),
                  _c(
                    "datalist",
                    { attrs: { id: "tipuri-declaratii" } },
                    _vm._l(_vm.tipuriCunoscute, function (tip) {
                      return _c("option", { key: tip }, [_vm._v(_vm._s(tip))])
                    }),
                    0
                  ),
                ],
                1
              ),
              _vm._v(" "),
              _c(
                "b-col",
                { attrs: { md: "3" } },
                [
                  _c("label", { staticClass: "small mb-0" }, [
                    _vm._v("Periodicitate"),
                  ]),
                  _vm._v(" "),
                  _c("b-form-select", {
                    attrs: { options: _vm.periodicitati, size: "sm" },
                    model: {
                      value: _vm.noua.perfisc,
                      callback: function ($$v) {
                        _vm.$set(_vm.noua, "perfisc", $$v)
                      },
                      expression: "noua.perfisc",
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
                  _c("label", { staticClass: "small mb-0" }, [
                    _vm._v("Valabilă de la"),
                  ]),
                  _vm._v(" "),
                  _c("b-form-input", {
                    attrs: { type: "date", size: "sm" },
                    model: {
                      value: _vm.noua.data_inceput,
                      callback: function ($$v) {
                        _vm.$set(_vm.noua, "data_inceput", $$v)
                      },
                      expression: "noua.data_inceput",
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
                  _c("label", { staticClass: "small mb-0" }, [
                    _vm._v("până la"),
                  ]),
                  _vm._v(" "),
                  _c("b-form-input", {
                    attrs: { type: "date", size: "sm" },
                    model: {
                      value: _vm.noua.data_sfarsit,
                      callback: function ($$v) {
                        _vm.$set(_vm.noua, "data_sfarsit", $$v)
                      },
                      expression: "noua.data_sfarsit",
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
                  _c(
                    "b-button",
                    {
                      attrs: {
                        variant: "primary",
                        size: "sm",
                        block: "",
                        disabled: !_vm.noua.tip || _vm.actualizareInCurs,
                      },
                      on: { click: _vm.adaugaDeclaratie },
                    },
                    [_vm._v("\n          Adaugă\n        ")]
                  ),
                ],
                1
              ),
            ],
            1
          ),
          _vm._v(" "),
          _vm.actualizareEroare
            ? _c(
                "b-alert",
                {
                  staticClass: "py-1 px-2",
                  attrs: { show: "", variant: "danger" },
                },
                [_vm._v("\n      " + _vm._s(_vm.actualizareEroare) + "\n    ")]
              )
            : _vm._e(),
          _vm._v(" "),
          _c("b-table", {
            attrs: {
              items: _vm.declaratiiFiltrate,
              fields: _vm.campuriDeclaratii,
              busy: _vm.actualizareInCurs,
              responsive: "",
              striped: "",
              small: "",
              "show-empty": "",
              "empty-text":
                "Nicio declarație — se completează la prima întocmire a raportului lunar sau manual, de mai sus.",
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
                key: "cell(perfisc)",
                fn: function (rand) {
                  return [
                    _vm.editataId === rand.item.id
                      ? _c("b-form-select", {
                          attrs: { options: _vm.periodicitati, size: "sm" },
                          model: {
                            value: _vm.editata.perfisc,
                            callback: function ($$v) {
                              _vm.$set(_vm.editata, "perfisc", $$v)
                            },
                            expression: "editata.perfisc",
                          },
                        })
                      : _c("span", [_vm._v(_vm._s(rand.item.perfisc))]),
                  ]
                },
              },
              {
                key: "cell(valabilitate)",
                fn: function (rand) {
                  return [
                    _vm.editataId === rand.item.id
                      ? _c(
                          "div",
                          { staticClass: "d-flex" },
                          [
                            _c("b-form-input", {
                              staticClass: "mr-50",
                              attrs: { type: "date", size: "sm" },
                              model: {
                                value: _vm.editata.data_inceput,
                                callback: function ($$v) {
                                  _vm.$set(_vm.editata, "data_inceput", $$v)
                                },
                                expression: "editata.data_inceput",
                              },
                            }),
                            _vm._v(" "),
                            _c("b-form-input", {
                              attrs: { type: "date", size: "sm" },
                              model: {
                                value: _vm.editata.data_sfarsit,
                                callback: function ($$v) {
                                  _vm.$set(_vm.editata, "data_sfarsit", $$v)
                                },
                                expression: "editata.data_sfarsit",
                              },
                            }),
                          ],
                          1
                        )
                      : _c("span", [
                          _vm._v(
                            "\n          " +
                              _vm._s(rand.item.data_inceput || "...") +
                              " → " +
                              _vm._s(rand.item.data_sfarsit || "în vigoare") +
                              "\n        "
                          ),
                        ]),
                  ]
                },
              },
              {
                key: "cell(sursa)",
                fn: function (rand) {
                  return [
                    _c(
                      "b-badge",
                      {
                        attrs: {
                          variant:
                            rand.item.sursa === "manuala"
                              ? "primary"
                              : "light-primary",
                        },
                      },
                      [
                        _vm._v(
                          "\n          " +
                            _vm._s(
                              rand.item.sursa === "manuala"
                                ? "manuală"
                                : "dedusă"
                            ) +
                            "\n        "
                        ),
                      ]
                    ),
                    _vm._v(" "),
                    rand.item.obligatii
                      ? _c("div", { staticClass: "small text-muted" }, [
                          _vm._v(
                            "\n          " +
                              _vm._s(rand.item.obligatii) +
                              "\n        "
                          ),
                        ])
                      : _vm._e(),
                  ]
                },
              },
              {
                key: "cell(actiuni)",
                fn: function (rand) {
                  return [
                    _vm.editataId === rand.item.id
                      ? [
                          _c(
                            "b-button",
                            {
                              staticClass: "mr-50",
                              attrs: {
                                size: "sm",
                                variant: "primary",
                                disabled: _vm.actualizareInCurs,
                              },
                              on: { click: _vm.salveazaModificarea },
                            },
                            [_vm._v("\n            Salvează\n          ")]
                          ),
                          _vm._v(" "),
                          _c(
                            "b-button",
                            {
                              attrs: {
                                size: "sm",
                                variant: "outline-secondary",
                              },
                              on: {
                                click: function ($event) {
                                  _vm.editataId = null
                                },
                              },
                            },
                            [_vm._v("\n            Renunță\n          ")]
                          ),
                        ]
                      : [
                          _c(
                            "b-button",
                            {
                              staticClass: "mr-50",
                              attrs: {
                                size: "sm",
                                variant: "outline-primary",
                                disabled: _vm.actualizareInCurs,
                              },
                              on: {
                                click: function ($event) {
                                  return _vm.incepeModificarea(rand.item)
                                },
                              },
                            },
                            [_vm._v("\n            Modifică\n          ")]
                          ),
                          _vm._v(" "),
                          _c(
                            "b-button",
                            {
                              attrs: {
                                size: "sm",
                                variant: "outline-danger",
                                disabled: _vm.actualizareInCurs,
                              },
                              on: {
                                click: function ($event) {
                                  return _vm.stergeDeclaratie(rand.item)
                                },
                              },
                            },
                            [_vm._v("\n            Șterge\n          ")]
                          ),
                        ],
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
            title: "Denumire entitate",
            "ok-title": "Salvează",
            "cancel-title": "Renunță",
            "modal-class": "modul-spv",
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
          _c("p", { staticClass: "text-muted small" }, [
            _vm._v(
              "\n      Denumirea introdusă manual are prioritate față de cea preluată din documentele SPV.\n    "
            ),
          ]),
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
      _vm._v(" "),
      _c(
        "b-modal",
        {
          attrs: {
            size: "lg",
            title: "Date pentru declarații",
            "ok-title": _vm.dateleInCurs ? "Se salvează..." : "Salvează",
            "cancel-title": "Renunță",
            "ok-disabled": _vm.dateleInCurs,
            "modal-class": "modul-spv",
            scrollable: "",
          },
          on: {
            ok: function ($event) {
              $event.preventDefault()
              return _vm.salveazaDatele.apply(null, arguments)
            },
          },
          model: {
            value: _vm.dateleVizibile,
            callback: function ($$v) {
              _vm.dateleVizibile = $$v
            },
            expression: "dateleVizibile",
          },
        },
        [
          _vm.dateleFirmei
            ? _c("p", { staticClass: "text-muted small mb-1" }, [
                _vm._v(
                  "\n      " +
                    _vm._s(
                      _vm.dateleFirmei.denumire || "Entitate fără denumire"
                    ) +
                    " (" +
                    _vm._s(_vm.dateleFirmei.cif) +
                    ").\n      Din ele se scrie antetul declarațiilor întocmite de aplicație — decontul de TVA\n      scos din SAF-T, de pildă. Cifrele declarației vin din fișierul ANAF; astea nu se\n      află nicăieri în el.\n    "
                ),
              ])
            : _vm._e(),
          _vm._v(" "),
          _vm.dateleFirmei && _vm.dateleFirmei.lipsesc.length
            ? _c(
                "b-alert",
                {
                  staticClass: "py-1 px-2",
                  attrs: { show: "", variant: "warning" },
                },
                [
                  _c("strong", [_vm._v("Mai lipsesc:")]),
                  _vm._v(
                    " " +
                      _vm._s(_vm.dateleFirmei.lipsesc.join(", ")) +
                      ".\n    "
                  ),
                ]
              )
            : _vm.dateleFirmei
            ? _c(
                "b-alert",
                {
                  staticClass: "py-1 px-2",
                  attrs: { show: "", variant: "success" },
                },
                [
                  _vm._v(
                    "\n      Datele sunt complete — declarațiile se pot întocmi.\n    "
                  ),
                ]
              )
            : _vm._e(),
          _vm._v(" "),
          _vm.dateleFirmei
            ? _c(
                "div",
                [
                  _c("h6", { staticClass: "mt-2" }, [
                    _vm._v("\n        Firma\n      "),
                  ]),
                  _vm._v(" "),
                  _c(
                    "b-row",
                    [
                      _c(
                        "b-col",
                        { attrs: { cols: "12" } },
                        [
                          _c(
                            "b-form-group",
                            { attrs: { label: "Adresa" } },
                            [
                              _c("b-form-input", {
                                model: {
                                  value: _vm.dateleScrise.adresa,
                                  callback: function ($$v) {
                                    _vm.$set(_vm.dateleScrise, "adresa", $$v)
                                  },
                                  expression: "dateleScrise.adresa",
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
                        "b-col",
                        { attrs: { md: "4" } },
                        [
                          _c(
                            "b-form-group",
                            { attrs: { label: "Telefon" } },
                            [
                              _c("b-form-input", {
                                model: {
                                  value: _vm.dateleScrise.telefon,
                                  callback: function ($$v) {
                                    _vm.$set(_vm.dateleScrise, "telefon", $$v)
                                  },
                                  expression: "dateleScrise.telefon",
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
                        "b-col",
                        { attrs: { md: "4" } },
                        [
                          _c(
                            "b-form-group",
                            { attrs: { label: "Fax" } },
                            [
                              _c("b-form-input", {
                                model: {
                                  value: _vm.dateleScrise.fax,
                                  callback: function ($$v) {
                                    _vm.$set(_vm.dateleScrise, "fax", $$v)
                                  },
                                  expression: "dateleScrise.fax",
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
                        "b-col",
                        { attrs: { md: "4" } },
                        [
                          _c(
                            "b-form-group",
                            { attrs: { label: "E-mail" } },
                            [
                              _c("b-form-input", {
                                attrs: { type: "email" },
                                model: {
                                  value: _vm.dateleScrise.email,
                                  callback: function ($$v) {
                                    _vm.$set(_vm.dateleScrise, "email", $$v)
                                  },
                                  expression: "dateleScrise.email",
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
                        "b-col",
                        { attrs: { md: "5" } },
                        [
                          _c(
                            "b-form-group",
                            { attrs: { label: "Banca" } },
                            [
                              _c("b-form-input", {
                                model: {
                                  value: _vm.dateleScrise.banca,
                                  callback: function ($$v) {
                                    _vm.$set(_vm.dateleScrise, "banca", $$v)
                                  },
                                  expression: "dateleScrise.banca",
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
                        "b-col",
                        { attrs: { md: "5" } },
                        [
                          _c(
                            "b-form-group",
                            { attrs: { label: "Contul (IBAN)" } },
                            [
                              _c("b-form-input", {
                                model: {
                                  value: _vm.dateleScrise.cont,
                                  callback: function ($$v) {
                                    _vm.$set(_vm.dateleScrise, "cont", $$v)
                                  },
                                  expression: "dateleScrise.cont",
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
                        "b-col",
                        { attrs: { md: "2" } },
                        [
                          _c(
                            "b-form-group",
                            { attrs: { label: "Cod CAEN" } },
                            [
                              _c("b-form-input", {
                                model: {
                                  value: _vm.dateleScrise.caen,
                                  callback: function ($$v) {
                                    _vm.$set(_vm.dateleScrise, "caen", $$v)
                                  },
                                  expression: "dateleScrise.caen",
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
                  _c("h6", { staticClass: "mt-1" }, [
                    _vm._v("\n        Cine semnează\n      "),
                  ]),
                  _vm._v(" "),
                  _c(
                    "b-row",
                    [
                      _c(
                        "b-col",
                        { attrs: { md: "4" } },
                        [
                          _c(
                            "b-form-group",
                            { attrs: { label: "Nume" } },
                            [
                              _c("b-form-input", {
                                model: {
                                  value: _vm.dateleScrise.nume_declarant,
                                  callback: function ($$v) {
                                    _vm.$set(
                                      _vm.dateleScrise,
                                      "nume_declarant",
                                      $$v
                                    )
                                  },
                                  expression: "dateleScrise.nume_declarant",
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
                        "b-col",
                        { attrs: { md: "4" } },
                        [
                          _c(
                            "b-form-group",
                            { attrs: { label: "Prenume" } },
                            [
                              _c("b-form-input", {
                                model: {
                                  value: _vm.dateleScrise.prenume_declarant,
                                  callback: function ($$v) {
                                    _vm.$set(
                                      _vm.dateleScrise,
                                      "prenume_declarant",
                                      $$v
                                    )
                                  },
                                  expression: "dateleScrise.prenume_declarant",
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
                        "b-col",
                        { attrs: { md: "4" } },
                        [
                          _c(
                            "b-form-group",
                            { attrs: { label: "Funcția" } },
                            [
                              _c("b-form-input", {
                                model: {
                                  value: _vm.dateleScrise.functie_declarant,
                                  callback: function ($$v) {
                                    _vm.$set(
                                      _vm.dateleScrise,
                                      "functie_declarant",
                                      $$v
                                    )
                                  },
                                  expression: "dateleScrise.functie_declarant",
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
                        "b-col",
                        { attrs: { cols: "12" } },
                        [
                          _c(
                            "b-form-checkbox",
                            {
                              model: {
                                value: _vm.dateleScrise.prin_reprezentant,
                                callback: function ($$v) {
                                  _vm.$set(
                                    _vm.dateleScrise,
                                    "prin_reprezentant",
                                    $$v
                                  )
                                },
                                expression: "dateleScrise.prin_reprezentant",
                              },
                            },
                            [
                              _vm._v(
                                "\n            Declarația se depune prin împuternicit\n          "
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
                  _c("h6", { staticClass: "mt-2" }, [
                    _vm._v("\n        Decontul de TVA (D300)\n      "),
                  ]),
                  _vm._v(" "),
                  _c(
                    "b-row",
                    [
                      _c(
                        "b-col",
                        { attrs: { md: "4" } },
                        [
                          _c(
                            "b-form-group",
                            { attrs: { label: "Felul decontului" } },
                            [
                              _c("b-form-select", {
                                attrs: { options: _vm.felurileDecontului },
                                model: {
                                  value: _vm.dateleScrise.d300_tip_decont,
                                  callback: function ($$v) {
                                    _vm.$set(
                                      _vm.dateleScrise,
                                      "d300_tip_decont",
                                      $$v
                                    )
                                  },
                                  expression: "dateleScrise.d300_tip_decont",
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
                        "b-col",
                        { attrs: { md: "4" } },
                        [
                          _c(
                            "b-form-group",
                            { attrs: { label: "Pro-rata (%)" } },
                            [
                              _c("b-form-input", {
                                attrs: {
                                  type: "number",
                                  min: "0",
                                  max: "100",
                                  step: "0.01",
                                  placeholder: "100",
                                },
                                model: {
                                  value: _vm.dateleScrise.d300_pro_rata,
                                  callback: function ($$v) {
                                    _vm.$set(
                                      _vm.dateleScrise,
                                      "d300_pro_rata",
                                      $$v
                                    )
                                  },
                                  expression: "dateleScrise.d300_pro_rata",
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
                  _c(
                    "b-row",
                    [
                      _c(
                        "b-col",
                        { attrs: { md: "6" } },
                        [
                          _c(
                            "b-form-checkbox",
                            {
                              staticClass: "mb-50",
                              model: {
                                value: _vm.dateleScrise.d300_bifa_interne,
                                callback: function ($$v) {
                                  _vm.$set(
                                    _vm.dateleScrise,
                                    "d300_bifa_interne",
                                    $$v
                                  )
                                },
                                expression: "dateleScrise.d300_bifa_interne",
                              },
                            },
                            [
                              _vm._v(
                                "\n            Operațiuni interne\n          "
                              ),
                            ]
                          ),
                          _vm._v(" "),
                          _c(
                            "b-form-checkbox",
                            {
                              staticClass: "mb-50",
                              model: {
                                value: _vm.dateleScrise.d300_bifa_cereale,
                                callback: function ($$v) {
                                  _vm.$set(
                                    _vm.dateleScrise,
                                    "d300_bifa_cereale",
                                    $$v
                                  )
                                },
                                expression: "dateleScrise.d300_bifa_cereale",
                              },
                            },
                            [
                              _vm._v(
                                "\n            Cereale și plante tehnice\n          "
                              ),
                            ]
                          ),
                          _vm._v(" "),
                          _c(
                            "b-form-checkbox",
                            {
                              model: {
                                value: _vm.dateleScrise.d300_bifa_mob,
                                callback: function ($$v) {
                                  _vm.$set(
                                    _vm.dateleScrise,
                                    "d300_bifa_mob",
                                    $$v
                                  )
                                },
                                expression: "dateleScrise.d300_bifa_mob",
                              },
                            },
                            [
                              _vm._v(
                                "\n            Telefoane mobile\n          "
                              ),
                            ]
                          ),
                        ],
                        1
                      ),
                      _vm._v(" "),
                      _c(
                        "b-col",
                        { attrs: { md: "6" } },
                        [
                          _c(
                            "b-form-checkbox",
                            {
                              staticClass: "mb-50",
                              model: {
                                value: _vm.dateleScrise.d300_bifa_disp,
                                callback: function ($$v) {
                                  _vm.$set(
                                    _vm.dateleScrise,
                                    "d300_bifa_disp",
                                    $$v
                                  )
                                },
                                expression: "dateleScrise.d300_bifa_disp",
                              },
                            },
                            [
                              _vm._v(
                                "\n            Dispozitive cu circuite integrate\n          "
                              ),
                            ]
                          ),
                          _vm._v(" "),
                          _c(
                            "b-form-checkbox",
                            {
                              staticClass: "mb-50",
                              model: {
                                value: _vm.dateleScrise.d300_bifa_cons,
                                callback: function ($$v) {
                                  _vm.$set(
                                    _vm.dateleScrise,
                                    "d300_bifa_cons",
                                    $$v
                                  )
                                },
                                expression: "dateleScrise.d300_bifa_cons",
                              },
                            },
                            [
                              _vm._v(
                                "\n            Console de jocuri, tablete, laptopuri\n          "
                              ),
                            ]
                          ),
                          _vm._v(" "),
                          _c(
                            "b-form-checkbox",
                            {
                              model: {
                                value: _vm.dateleScrise.d300_solicit_ramb,
                                callback: function ($$v) {
                                  _vm.$set(
                                    _vm.dateleScrise,
                                    "d300_solicit_ramb",
                                    $$v
                                  )
                                },
                                expression: "dateleScrise.d300_solicit_ramb",
                              },
                            },
                            [
                              _vm._v(
                                "\n            Se solicită rambursarea soldului sumei negative\n          "
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
              )
            : _vm._e(),
          _vm._v(" "),
          _vm.dateleEroare
            ? _c(
                "b-alert",
                {
                  staticClass: "py-1 px-2 mb-0 mt-1",
                  attrs: { show: "", variant: "danger" },
                },
                [_vm._v("\n      " + _vm._s(_vm.dateleEroare) + "\n    ")]
              )
            : _vm._e(),
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

/***/ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/app_pages/spv/Solicitari.vue?vue&type=template&id=1f7e2a2c&":
/*!**************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/src/views/app_pages/spv/Solicitari.vue?vue&type=template&id=1f7e2a2c& ***!
  \**************************************************************************************************************************************************************************************************************************/
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
        { attrs: { title: "Solicitări documente SPV" } },
        [
          _c(
            "b-row",
            { staticClass: "mb-3 align-items-end" },
            [
              _c(
                "b-col",
                { attrs: { md: "3" } },
                [
                  _c("label", [_vm._v("Certificat (token)")]),
                  _vm._v(" "),
                  _c("b-form-select", {
                    attrs: { options: _vm.optiuniCertificatToken },
                    model: {
                      value: _vm.certificatAles,
                      callback: function ($$v) {
                        _vm.certificatAles = $$v
                      },
                      expression: "certificatAles",
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
                    "div",
                    {
                      staticClass:
                        "d-flex align-items-center justify-content-between",
                    },
                    [
                      _c("label", { staticClass: "mb-0" }, [
                        _vm._v("\n            Firme\n            "),
                        _vm.firme.length
                          ? _c("span", { staticClass: "text-muted" }, [
                              _vm._v(
                                "· " +
                                  _vm._s(_vm.firme.length) +
                                  " " +
                                  _vm._s(
                                    _vm.firme.length === 1 ? "aleasă" : "alese"
                                  )
                              ),
                            ])
                          : _vm._e(),
                      ]),
                      _vm._v(" "),
                      _c(
                        "b-button",
                        {
                          staticClass: "py-0 px-50",
                          attrs: {
                            size: "sm",
                            variant: "flat-primary",
                            disabled: !_vm.firmeDisponibile.length,
                          },
                          on: { click: _vm.alegeToate },
                        },
                        [
                          _c("small", [
                            _vm._v(
                              _vm._s(
                                _vm.firme.length === _vm.firmeDisponibile.length
                                  ? "niciuna"
                                  : "toate"
                              )
                            ),
                          ]),
                        ]
                      ),
                    ],
                    1
                  ),
                  _vm._v(" "),
                  _c("v-select", {
                    staticClass: "select-firme",
                    attrs: {
                      multiple: "",
                      label: "eticheta",
                      options: _vm.firmeDisponibile,
                      loading: _vm.societatiInCurs,
                      placeholder: "Alegeți firmele...",
                    },
                    scopedSlots: _vm._u([
                      {
                        key: "no-options",
                        fn: function () {
                          return [
                            _vm._v(
                              "\n            " +
                                _vm._s(
                                  _vm.societatiInCurs
                                    ? "Se încarcă..."
                                    : "Nicio entitate înrolată. Sincronizați-le mai întâi."
                                ) +
                                "\n          "
                            ),
                          ]
                        },
                        proxy: true,
                      },
                    ]),
                    model: {
                      value: _vm.firme,
                      callback: function ($$v) {
                        _vm.firme = $$v
                      },
                      expression: "firme",
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
                  _c("label", [_vm._v("Tip document")]),
                  _vm._v(" "),
                  _c("b-form-select", {
                    attrs: { options: _vm.tipuri },
                    model: {
                      value: _vm.tipDocument,
                      callback: function ($$v) {
                        _vm.tipDocument = $$v
                      },
                      expression: "tipDocument",
                    },
                  }),
                ],
                1
              ),
              _vm._v(" "),
              _vm.cere("an")
                ? _c(
                    "b-col",
                    { attrs: { md: "2" } },
                    [
                      _c("label", [_vm._v("An")]),
                      _vm._v(" "),
                      _c("b-form-input", {
                        attrs: { type: "number", min: "2000", max: "2100" },
                        model: {
                          value: _vm.an,
                          callback: function ($$v) {
                            _vm.an = _vm._n($$v)
                          },
                          expression: "an",
                        },
                      }),
                    ],
                    1
                  )
                : _vm._e(),
              _vm._v(" "),
              _vm.cere("luna")
                ? _c(
                    "b-col",
                    { attrs: { md: "2" } },
                    [
                      _c("label", [_vm._v("Luna")]),
                      _vm._v(" "),
                      _c("b-form-input", {
                        attrs: { type: "number", min: "1", max: "12" },
                        model: {
                          value: _vm.luna,
                          callback: function ($$v) {
                            _vm.luna = _vm._n($$v)
                          },
                          expression: "luna",
                        },
                      }),
                    ],
                    1
                  )
                : _vm._e(),
              _vm._v(" "),
              _vm.cere("motiv")
                ? _c(
                    "b-col",
                    { attrs: { md: "3" } },
                    [
                      _c("label", [_vm._v("Motivul solicitării")]),
                      _vm._v(" "),
                      _c("b-form-input", {
                        attrs: { placeholder: "Ex: obținere credit" },
                        model: {
                          value: _vm.motiv,
                          callback: function ($$v) {
                            _vm.motiv = $$v
                          },
                          expression: "motiv",
                        },
                      }),
                    ],
                    1
                  )
                : _vm._e(),
              _vm._v(" "),
              _vm.cere("numar_inregistrare")
                ? _c(
                    "b-col",
                    { attrs: { md: "3" } },
                    [
                      _c("label", [_vm._v("Număr înregistrare declarație")]),
                      _vm._v(" "),
                      _c("b-form-input", {
                        attrs: { placeholder: "Ex: INTERNT-1160245317-2026" },
                        model: {
                          value: _vm.numarInregistrare,
                          callback: function ($$v) {
                            _vm.numarInregistrare = $$v
                          },
                          expression: "numarInregistrare",
                        },
                      }),
                    ],
                    1
                  )
                : _vm._e(),
              _vm._v(" "),
              _vm.cere("cui_pui")
                ? _c(
                    "b-col",
                    { attrs: { md: "2" } },
                    [
                      _c("label", [_vm._v("CUI punct de lucru (opțional)")]),
                      _vm._v(" "),
                      _c("b-form-input", {
                        model: {
                          value: _vm.cuiPui,
                          callback: function ($$v) {
                            _vm.cuiPui = $$v
                          },
                          expression: "cuiPui",
                        },
                      }),
                    ],
                    1
                  )
                : _vm._e(),
              _vm._v(" "),
              _c(
                "b-col",
                { staticClass: "d-flex align-items-end", attrs: { md: "2" } },
                [
                  _c(
                    "b-button",
                    {
                      attrs: {
                        variant: "primary",
                        disabled: !_vm.poateTrimite || _vm.trimiteInCurs,
                      },
                      on: { click: _vm.trimite },
                    },
                    [
                      _vm.trimiteInCurs
                        ? _c("b-spinner", {
                            staticClass: "mr-1",
                            attrs: { small: "" },
                          })
                        : _vm._e(),
                      _vm._v("\n          Solicită\n        "),
                    ],
                    1
                  ),
                ],
                1
              ),
              _vm._v(" "),
              _c(
                "b-col",
                {
                  staticClass:
                    "ml-auto d-flex justify-content-end align-items-end",
                  attrs: { md: "auto" },
                },
                [
                  _c(
                    "div",
                    { staticClass: "d-flex flex-column align-items-start" },
                    [
                      _c(
                        "div",
                        {
                          staticClass: "pentru-tiparire",
                          attrs: {
                            title:
                              "Trimite la imprimanta dumneavoastră răspunsurile aduse în această sesiune",
                          },
                        },
                        [
                          _c(
                            "b-form-checkbox",
                            {
                              staticClass: "comutator-primar",
                              attrs: { size: "sm" },
                              model: {
                                value: _vm.tiparire,
                                callback: function ($$v) {
                                  _vm.tiparire = $$v
                                },
                                expression: "tiparire",
                              },
                            },
                            [
                              _c(
                                "small",
                                {
                                  staticClass: "text-nowrap",
                                  class: _vm.tiparire
                                    ? "text-primary"
                                    : "text-muted",
                                },
                                [
                                  _vm._v(
                                    "\n                Imprimare răspunsuri preluate\n              "
                                  ),
                                ]
                              ),
                            ]
                          ),
                        ],
                        1
                      ),
                      _vm._v(" "),
                      _c(
                        "div",
                        {
                          staticClass: "pentru-tiparire",
                          attrs: {
                            title:
                              "Aplică watermark cu denumirea firmei pe documentele de imprimat",
                          },
                        },
                        [
                          _c(
                            "b-form-checkbox",
                            {
                              staticClass: "comutator-primar",
                              attrs: { size: "sm", disabled: !_vm.tiparire },
                              model: {
                                value: _vm.filigran,
                                callback: function ($$v) {
                                  _vm.filigran = $$v
                                },
                                expression: "filigran",
                              },
                            },
                            [
                              _c(
                                "small",
                                {
                                  staticClass: "text-nowrap",
                                  class:
                                    _vm.filigran && _vm.tiparire
                                      ? "text-primary"
                                      : "text-muted",
                                },
                                [
                                  _vm._v(
                                    "\n                Aplică watermark\n              "
                                  ),
                                ]
                              ),
                            ]
                          ),
                        ],
                        1
                      ),
                      _vm._v(" "),
                      _c(
                        "b-dropdown",
                        {
                          attrs: {
                            split: "",
                            variant: "outline-primary",
                            disabled: _vm.preiaInCurs,
                          },
                          on: {
                            click: function ($event) {
                              return _vm.preia()
                            },
                          },
                          scopedSlots: _vm._u([
                            {
                              key: "button-content",
                              fn: function () {
                                return [
                                  _vm.preiaInCurs
                                    ? _c("b-spinner", {
                                        staticClass: "mr-1",
                                        attrs: { small: "" },
                                      })
                                    : _c("feather-icon", {
                                        staticClass: "mr-1",
                                        attrs: {
                                          icon: "DownloadIcon",
                                          size: "15",
                                        },
                                      }),
                                  _vm._v(
                                    "\n              Preia răspunsurile" +
                                      _vm._s(
                                        _vm.automat.activ ? " automat" : ""
                                      ) +
                                      "\n              "
                                  ),
                                  _vm.inAsteptare
                                    ? _c(
                                        "b-badge",
                                        {
                                          staticClass: "ml-1",
                                          attrs: { variant: "warning" },
                                        },
                                        [
                                          _vm._v(
                                            "\n                " +
                                              _vm._s(_vm.inAsteptare) +
                                              "\n              "
                                          ),
                                        ]
                                      )
                                    : _vm._e(),
                                ]
                              },
                              proxy: true,
                            },
                          ]),
                        },
                        [
                          _vm._v(" "),
                          _c(
                            "b-dropdown-item",
                            {
                              attrs: { active: !_vm.automat.activ },
                              on: {
                                click: function ($event) {
                                  _vm.automat.activ = false
                                },
                              },
                            },
                            [
                              _vm._v(
                                "\n              Preia răspunsurile\n            "
                              ),
                            ]
                          ),
                          _vm._v(" "),
                          _c(
                            "b-dropdown-item",
                            {
                              attrs: { active: _vm.automat.activ },
                              on: {
                                click: function ($event) {
                                  _vm.automat.activ = true
                                },
                              },
                            },
                            [
                              _vm._v(
                                "\n              Preia răspunsurile automat\n            "
                              ),
                            ]
                          ),
                        ],
                        1
                      ),
                    ],
                    1
                  ),
                ]
              ),
            ],
            1
          ),
          _vm._v(" "),
          _c(
            "b-row",
            { staticClass: "mb-3 align-items-center" },
            [
              _c(
                "b-col",
                {
                  staticClass: "ml-auto automat-raspunsuri text-right",
                  attrs: { md: "auto" },
                },
                [
                  _vm.automat.activ
                    ? _c(
                        "div",
                        {
                          staticClass:
                            "d-flex align-items-center justify-content-end",
                        },
                        [
                          _c("small", { staticClass: "text-primary mr-1" }, [
                            _vm._v("la"),
                          ]),
                          _vm._v(" "),
                          _c("b-form-select", {
                            staticClass: "lista-minute",
                            attrs: { size: "sm", options: _vm.optiuniMinute },
                            model: {
                              value: _vm.automat.minute,
                              callback: function ($$v) {
                                _vm.$set(_vm.automat, "minute", _vm._n($$v))
                              },
                              expression: "automat.minute",
                            },
                          }),
                        ],
                        1
                      )
                    : _vm._e(),
                  _vm._v(" "),
                  _c("small", { staticClass: "text-muted" }, [
                    _vm.ultimaPreluare
                      ? _c("span", [
                          _vm._v(
                            "\n            Ultima preluare: " +
                              _vm._s(_vm.ultimaPreluare) +
                              " · " +
                              _vm._s(_vm.raspunsuriAduse) +
                              "\n          "
                          ),
                        ])
                      : _c("span", [_vm._v("Nicio preluare încă")]),
                  ]),
                ]
              ),
            ],
            1
          ),
          _vm._v(" "),
          _vm.preiaInCurs && _vm.mersul
            ? _c(
                "div",
                { staticClass: "text-muted mb-3" },
                [
                  _vm._v("\n      " + _vm._s(_vm.mersul) + "\n      "),
                  _vm.deAdus
                    ? _c("b-progress", {
                        staticClass: "mt-1",
                        attrs: {
                          value: _vm.aduse,
                          max: _vm.deAdus,
                          variant: "primary",
                          height: "6px",
                        },
                      })
                    : _vm._e(),
                ],
                1
              )
            : _vm._e(),
          _vm._v(" "),
          _vm.eroare
            ? _c(
                "b-alert",
                { staticClass: "mb-3", attrs: { show: "", variant: "danger" } },
                [_vm._v("\n      " + _vm._s(_vm.eroare) + "\n    ")]
              )
            : _vm._e(),
          _vm._v(" "),
          _c("b-table", {
            attrs: {
              items: _vm.solicitariFiltrate,
              "per-page": _vm.pePagina,
              "current-page": _vm.pagina,
              fields: _vm.campuri,
              busy: _vm.listaInCurs,
              responsive: "",
              striped: "",
              small: "",
              "show-empty": "",
              "empty-text": "Nu există solicitări pentru filtrul selectat.",
            },
            scopedSlots: _vm._u([
              {
                key: "head(cif)",
                fn: function (date) {
                  return [
                    _c("div", [_vm._v(_vm._s(date.label))]),
                    _vm._v(" "),
                    _c("b-form-input", {
                      staticClass: "filtru-coloana",
                      attrs: { size: "sm", placeholder: "CIF" },
                      model: {
                        value: _vm.filtre.cif,
                        callback: function ($$v) {
                          _vm.$set(_vm.filtre, "cif", $$v)
                        },
                        expression: "filtre.cif",
                      },
                    }),
                  ]
                },
              },
              {
                key: "head(den_firma)",
                fn: function (date) {
                  return [
                    _c("div", [_vm._v(_vm._s(date.label))]),
                    _vm._v(" "),
                    _c("b-form-input", {
                      staticClass: "filtru-coloana",
                      attrs: { size: "sm", placeholder: "Denumire" },
                      model: {
                        value: _vm.filtre.den_firma,
                        callback: function ($$v) {
                          _vm.$set(_vm.filtre, "den_firma", $$v)
                        },
                        expression: "filtre.den_firma",
                      },
                    }),
                  ]
                },
              },
              {
                key: "head(tip_document)",
                fn: function (date) {
                  return [
                    _c("div", [_vm._v(_vm._s(date.label))]),
                    _vm._v(" "),
                    _c("b-form-input", {
                      staticClass: "filtru-coloana",
                      attrs: { size: "sm", placeholder: "Tip" },
                      model: {
                        value: _vm.filtre.tip_document,
                        callback: function ($$v) {
                          _vm.$set(_vm.filtre, "tip_document", $$v)
                        },
                        expression: "filtre.tip_document",
                      },
                    }),
                  ]
                },
              },
              {
                key: "head(data_solicitarii)",
                fn: function (date) {
                  return [
                    _c("div", [_vm._v(_vm._s(date.label))]),
                    _vm._v(" "),
                    _c("b-form-input", {
                      staticClass: "filtru-coloana",
                      attrs: { size: "sm", placeholder: "Data" },
                      model: {
                        value: _vm.filtre.data_solicitarii,
                        callback: function ($$v) {
                          _vm.$set(_vm.filtre, "data_solicitarii", $$v)
                        },
                        expression: "filtre.data_solicitarii",
                      },
                    }),
                  ]
                },
              },
              {
                key: "head(data_afisare)",
                fn: function (date) {
                  return [
                    _c("div", [_vm._v(_vm._s(date.label))]),
                    _vm._v(" "),
                    _c("b-form-input", {
                      staticClass: "filtru-coloana",
                      attrs: { size: "sm", placeholder: "Data" },
                      model: {
                        value: _vm.filtre.data_afisare,
                        callback: function ($$v) {
                          _vm.$set(_vm.filtre, "data_afisare", $$v)
                        },
                        expression: "filtre.data_afisare",
                      },
                    }),
                  ]
                },
              },
              {
                key: "head(stare)",
                fn: function (date) {
                  return [
                    _c("div", [_vm._v(_vm._s(date.label))]),
                    _vm._v(" "),
                    _c("b-form-select", {
                      staticClass: "filtru-coloana",
                      attrs: {
                        size: "sm",
                        options: [
                          { value: "", text: "toate" },
                          { value: "preluata", text: "răspuns primit" },
                          { value: "trimisa", text: "în așteptare" },
                        ],
                      },
                      model: {
                        value: _vm.filtre.stare,
                        callback: function ($$v) {
                          _vm.$set(_vm.filtre, "stare", $$v)
                        },
                        expression: "filtre.stare",
                      },
                    }),
                  ]
                },
              },
              {
                key: "head(obs)",
                fn: function (date) {
                  return [
                    _c("div", [_vm._v(_vm._s(date.label))]),
                    _vm._v(" "),
                    _c("b-form-input", {
                      staticClass: "filtru-coloana",
                      attrs: { size: "sm", placeholder: "Observații" },
                      model: {
                        value: _vm.filtre.obs,
                        callback: function ($$v) {
                          _vm.$set(_vm.filtre, "obs", $$v)
                        },
                        expression: "filtre.obs",
                      },
                    }),
                  ]
                },
              },
              {
                key: "head(certificat_nume)",
                fn: function (date) {
                  return [
                    _c("div", [_vm._v(_vm._s(date.label))]),
                    _vm._v(" "),
                    _c("b-form-input", {
                      staticClass: "filtru-coloana",
                      attrs: { size: "sm", placeholder: "Certificat" },
                      model: {
                        value: _vm.filtre.certificat_nume,
                        callback: function ($$v) {
                          _vm.$set(_vm.filtre, "certificat_nume", $$v)
                        },
                        expression: "filtre.certificat_nume",
                      },
                    }),
                  ]
                },
              },
              {
                key: "table-busy",
                fn: function () {
                  return [
                    _c(
                      "div",
                      { staticClass: "text-center my-2" },
                      [
                        _c("b-spinner", { staticClass: "align-middle mr-1" }),
                        _vm._v(
                          "\n          Se încarcă solicitările...\n        "
                        ),
                      ],
                      1
                    ),
                  ]
                },
                proxy: true,
              },
              {
                key: "cell(den_firma)",
                fn: function (rand) {
                  return [
                    _c(
                      "div",
                      { staticClass: "d-flex align-items-center" },
                      [
                        _c("span", [
                          _vm._v(_vm._s(rand.item.den_firma || "-")),
                        ]),
                        _vm._v(" "),
                        !rand.item.inrolata
                          ? _c("feather-icon", {
                              staticClass: "text-warning ml-50 flex-shrink-0",
                              attrs: {
                                icon: "AlertTriangleIcon",
                                size: "14",
                                title:
                                  "CIF-ul " +
                                  rand.item.cif +
                                  " nu se află în Entități înrolate cu denumire. " +
                                  "Denumirea afișată este cea reținută la solicitare.",
                              },
                            })
                          : _vm._e(),
                      ],
                      1
                    ),
                  ]
                },
              },
              {
                key: "cell(stare)",
                fn: function (rand) {
                  return [
                    _c(
                      "b-badge",
                      {
                        attrs: {
                          variant:
                            rand.item.stare === "preluata"
                              ? "success"
                              : "secondary",
                        },
                      },
                      [
                        _vm._v(
                          "\n          " +
                            _vm._s(
                              rand.item.stare === "preluata"
                                ? "Răspuns primit"
                                : "În așteptare"
                            ) +
                            "\n        "
                        ),
                      ]
                    ),
                  ]
                },
              },
              {
                key: "cell(obs)",
                fn: function (rand) {
                  return [
                    _c("span", { class: _vm.clasaObs(rand.item.obs) }, [
                      _vm._v(_vm._s(rand.item.obs || "-")),
                    ]),
                  ]
                },
              },
              {
                key: "cell(actiuni)",
                fn: function (rand) {
                  return [
                    _vm.areDocument(rand.item)
                      ? _c(
                          "b-button",
                          {
                            staticClass: "mr-1",
                            attrs: { size: "sm", variant: "outline-secondary" },
                            on: {
                              click: function ($event) {
                                return _vm.deschide(rand.item)
                              },
                            },
                          },
                          [_vm._v("\n          Deschide\n        ")]
                        )
                      : _vm._e(),
                    _vm._v(" "),
                    _c(
                      "b-button",
                      {
                        attrs: { size: "sm", variant: "outline-danger" },
                        on: {
                          click: function ($event) {
                            return _vm.sterge(rand.item)
                          },
                        },
                      },
                      [_vm._v("\n          Șterge\n        ")]
                    ),
                  ]
                },
              },
            ]),
          }),
          _vm._v(" "),
          _c("paginare", {
            attrs: {
              "per-page": _vm.pePagina,
              total: _vm.solicitariFiltrate.length,
            },
            on: {
              "update:perPage": function ($event) {
                _vm.pePagina = $event
              },
              "update:per-page": function ($event) {
                _vm.pePagina = $event
              },
            },
            model: {
              value: _vm.pagina,
              callback: function ($$v) {
                _vm.pagina = $$v
              },
              expression: "pagina",
            },
          }),
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

/***/ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/app_pages/spv/Utilizatori.vue?vue&type=template&id=17852987&scoped=true&":
/*!***************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/src/views/app_pages/spv/Utilizatori.vue?vue&type=template&id=17852987&scoped=true& ***!
  \***************************************************************************************************************************************************************************************************************************************/
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
        { staticClass: "border mb-2", attrs: { "body-class": "p-2" } },
        [
          _c(
            "div",
            {
              staticClass: "d-flex align-items-center justify-content-between",
            },
            [
              _c("div", [
                _c("h6", { staticClass: "mb-25" }, [
                  _vm._v("\n          Utilizatorii firmei\n        "),
                ]),
                _vm._v(" "),
                _c("small", { staticClass: "text-muted" }, [
                  _vm._v(
                    "\n          Un utilizator obișnuit vede doar declarațiile și solicitările depuse de el,\n          plus mesajele din SPV ale certificatelor la care i-ați dat acces.\n        "
                  ),
                ]),
              ]),
              _vm._v(" "),
              _c(
                "b-button",
                {
                  staticClass: "flex-shrink-0",
                  attrs: { variant: "primary", size: "sm" },
                  on: {
                    click: function ($event) {
                      return _vm.deschide(null)
                    },
                  },
                },
                [
                  _c("feather-icon", {
                    staticClass: "mr-50",
                    attrs: { icon: "UserPlusIcon" },
                  }),
                  _vm._v("Utilizator nou\n      "),
                ],
                1
              ),
            ],
            1
          ),
        ]
      ),
      _vm._v(" "),
      _c(
        "b-card",
        { staticClass: "border mb-0", attrs: { "body-class": "p-1" } },
        [
          _c(
            "b-row",
            { staticClass: "mb-2" },
            [
              _c(
                "b-col",
                { attrs: { md: "4" } },
                [
                  _c("b-form-input", {
                    attrs: {
                      size: "sm",
                      placeholder: "Caută după nume, email sau telefon",
                    },
                    model: {
                      value: _vm.cautare,
                      callback: function ($$v) {
                        _vm.cautare = $$v
                      },
                      expression: "cautare",
                    },
                  }),
                ],
                1
              ),
              _vm._v(" "),
              _c(
                "b-col",
                {
                  staticClass: "d-flex align-items-center",
                  attrs: { md: "3" },
                },
                [
                  _vm.cautare
                    ? _c("small", { staticClass: "text-muted" }, [
                        _vm._v(
                          "\n          " +
                            _vm._s(_vm.utilizatoriFiltrati.length) +
                            " din " +
                            _vm._s(_vm.utilizatori.length) +
                            "\n        "
                        ),
                      ])
                    : _vm._e(),
                ]
              ),
            ],
            1
          ),
          _vm._v(" "),
          _c("b-table", {
            staticClass: "tabel-compact mb-0",
            attrs: {
              items: _vm.utilizatoriFiltrati,
              fields: _vm.campuri,
              busy: _vm.listaInCurs,
              responsive: "",
              striped: "",
              small: "",
              "show-empty": "",
              "empty-text": "Niciun utilizator.",
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
                key: "cell(persoana)",
                fn: function (rand) {
                  return [
                    _c(
                      "div",
                      { staticClass: "d-flex align-items-center" },
                      [
                        rand.item.administrator
                          ? _c("feather-icon", {
                              directives: [
                                {
                                  name: "b-tooltip",
                                  rawName: "v-b-tooltip.hover",
                                  modifiers: { hover: true },
                                },
                              ],
                              staticClass: "text-primary mr-50",
                              attrs: {
                                icon: "ShieldIcon",
                                size: "14",
                                title:
                                  "Administrator: vede tot ce s-a lucrat pentru firmă",
                              },
                            })
                          : _vm._e(),
                        _vm._v(" "),
                        !rand.item.administrator && rand.item.poate_semna
                          ? _c("feather-icon", {
                              directives: [
                                {
                                  name: "b-tooltip",
                                  rawName: "v-b-tooltip.hover",
                                  modifiers: { hover: true },
                                },
                              ],
                              staticClass: "text-success mr-50",
                              attrs: {
                                icon: "EditIcon",
                                size: "14",
                                title: "Poate semna declarațiile validate",
                              },
                            })
                          : _vm._e(),
                        _vm._v(" "),
                        !rand.item.administrator && rand.item.poate_depune
                          ? _c("feather-icon", {
                              directives: [
                                {
                                  name: "b-tooltip",
                                  rawName: "v-b-tooltip.hover",
                                  modifiers: { hover: true },
                                },
                              ],
                              staticClass: "text-warning mr-50",
                              attrs: {
                                icon: "SendIcon",
                                size: "14",
                                title: "Poate depune declarațiile semnate",
                              },
                            })
                          : _vm._e(),
                        _vm._v(" "),
                        _c("div", [
                          _c("div", [_vm._v(_vm._s(rand.item.nume))]),
                          _vm._v(" "),
                          _c("div", { staticClass: "small text-muted" }, [
                            _vm._v(
                              "\n              " +
                                _vm._s(rand.item.email) +
                                "\n            "
                            ),
                          ]),
                        ]),
                      ],
                      1
                    ),
                  ]
                },
              },
              {
                key: "cell(certificate)",
                fn: function (rand) {
                  return [
                    _vm._l(rand.item.certificate, function (certificat) {
                      return _c(
                        "b-badge",
                        {
                          key: certificat.id,
                          staticClass: "mr-25 mb-25",
                          attrs: { variant: "light-primary" },
                        },
                        [
                          _vm._v(
                            "\n          " +
                              _vm._s(certificat.cn) +
                              "\n        "
                          ),
                        ]
                      )
                    }),
                    _vm._v(" "),
                    !rand.item.certificate.length
                      ? _c("span", { staticClass: "small text-muted" }, [
                          _vm._v(
                            "\n          niciun certificat — nu vede mesaje SPV\n        "
                          ),
                        ])
                      : _vm._e(),
                  ]
                },
              },
              {
                key: "cell(imprimanta)",
                fn: function (rand) {
                  return [
                    rand.item.imprimanta
                      ? _c(
                          "span",
                          [
                            _c("feather-icon", {
                              staticClass: "text-muted mr-25",
                              attrs: { icon: "PrinterIcon", size: "13" },
                            }),
                            _vm._v(_vm._s(rand.item.imprimanta) + "\n        "),
                          ],
                          1
                        )
                      : _c("span", { staticClass: "small text-muted" }, [
                          _vm._v("nealeasă"),
                        ]),
                  ]
                },
              },
              {
                key: "cell(ip_permise)",
                fn: function (rand) {
                  return [
                    rand.item.ip_permise
                      ? _c("span", { staticClass: "small" }, [
                          _vm._v(_vm._s(rand.item.ip_permise)),
                        ])
                      : _c("span", { staticClass: "small text-muted" }, [
                          _vm._v("de oriunde"),
                        ]),
                  ]
                },
              },
              {
                key: "cell(stare)",
                fn: function (rand) {
                  return [
                    _c(
                      "b-badge",
                      {
                        attrs: {
                          variant: rand.item.blocat
                            ? "light-danger"
                            : "light-success",
                        },
                      },
                      [
                        _vm._v(
                          "\n          " +
                            _vm._s(rand.item.blocat ? "blocat" : "activ") +
                            "\n        "
                        ),
                      ]
                    ),
                  ]
                },
              },
              {
                key: "cell(actiuni)",
                fn: function (rand) {
                  return [
                    _c(
                      "b-button",
                      {
                        staticClass: "mr-1",
                        attrs: { size: "sm", variant: "outline-secondary" },
                        on: {
                          click: function ($event) {
                            return _vm.deschide(rand.item)
                          },
                        },
                      },
                      [_vm._v("\n          Modifică\n        ")]
                    ),
                    _vm._v(" "),
                    _c(
                      "b-button",
                      {
                        attrs: {
                          size: "sm",
                          variant: "outline-warning",
                          disabled: rand.item.eu,
                          title: rand.item.eu
                            ? "Nu vă puteți deconecta pe dumneavoastră de aici"
                            : "Închide sesiunile deschise",
                        },
                        on: {
                          click: function ($event) {
                            return _vm.deconecteaza(rand.item)
                          },
                        },
                      },
                      [_vm._v("\n          Deconectează\n        ")]
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
        "b-modal",
        {
          attrs: {
            title: _vm.formular.id
              ? "Utilizatorul " + _vm.formular.email
              : "Utilizator nou",
            "ok-title": "Salvează",
            "cancel-title": "Renunță",
            "modal-class": "modul-spv",
          },
          on: {
            ok: function ($event) {
              $event.preventDefault()
              return _vm.salveaza.apply(null, arguments)
            },
          },
          model: {
            value: _vm.formularVizibil,
            callback: function ($$v) {
              _vm.formularVizibil = $$v
            },
            expression: "formularVizibil",
          },
        },
        [
          _vm.eroareFormular
            ? _c(
                "b-alert",
                {
                  staticClass: "py-1 px-2 small",
                  attrs: { show: "", variant: "danger" },
                },
                [_vm._v("\n      " + _vm._s(_vm.eroareFormular) + "\n    ")]
              )
            : _vm._e(),
          _vm._v(" "),
          _c("label", [_vm._v("Nume")]),
          _vm._v(" "),
          _c("b-form-input", {
            staticClass: "mb-2",
            model: {
              value: _vm.formular.nume,
              callback: function ($$v) {
                _vm.$set(_vm.formular, "nume", $$v)
              },
              expression: "formular.nume",
            },
          }),
          _vm._v(" "),
          _c("label", [_vm._v("Email (cu el se autentifică)")]),
          _vm._v(" "),
          _c("b-form-input", {
            staticClass: "mb-2",
            attrs: { type: "email" },
            model: {
              value: _vm.formular.email,
              callback: function ($$v) {
                _vm.$set(_vm.formular, "email", $$v)
              },
              expression: "formular.email",
            },
          }),
          _vm._v(" "),
          _c("label", [_vm._v("Telefon")]),
          _vm._v(" "),
          _c("b-form-input", {
            staticClass: "mb-2",
            model: {
              value: _vm.formular.telefon,
              callback: function ($$v) {
                _vm.$set(_vm.formular, "telefon", $$v)
              },
              expression: "formular.telefon",
            },
          }),
          _vm._v(" "),
          _c("label", [
            _vm._v(
              _vm._s(
                _vm.formular.id
                  ? "Parolă nouă (gol = neschimbată)"
                  : "Parolă (minimum 8 caractere)"
              )
            ),
          ]),
          _vm._v(" "),
          _c("b-form-input", {
            staticClass: "mb-2",
            attrs: { type: "text", autocomplete: "new-password" },
            model: {
              value: _vm.formular.parola,
              callback: function ($$v) {
                _vm.$set(_vm.formular, "parola", $$v)
              },
              expression: "formular.parola",
            },
          }),
          _vm._v(" "),
          _c("label", [_vm._v("Certificate digitale la care are acces")]),
          _vm._v(" "),
          _c(
            "b-form-checkbox-group",
            {
              staticClass: "mb-1",
              attrs: { stacked: "" },
              model: {
                value: _vm.formular.certificate,
                callback: function ($$v) {
                  _vm.$set(_vm.formular, "certificate", $$v)
                },
                expression: "formular.certificate",
              },
            },
            _vm._l(_vm.certificate, function (certificat) {
              return _c(
                "b-form-checkbox",
                { key: certificat.id, attrs: { value: certificat.id } },
                [
                  _vm._v("\n        " + _vm._s(certificat.cn) + "\n        "),
                  _vm._v(" "),
                  certificat.activ === false
                    ? _c("span", { staticClass: "text-muted" }, [
                        _vm._v("— scos din uz"),
                      ])
                    : _vm._e(),
                ]
              )
            }),
            1
          ),
          _vm._v(" "),
          _c("small", { staticClass: "text-muted d-block mb-2" }, [
            _vm._v(
              "\n      Vede mesajele din SPV ale certificatelor bifate. Declarațiile și solicitările\n      le vede doar pe ale lui, indiferent de certificat.\n    "
            ),
          ]),
          _vm._v(" "),
          _c("hr"),
          _vm._v(" "),
          _c("label", [_vm._v("Imprimanta pe care iese hârtia")]),
          _vm._v(" "),
          _c(
            "b-row",
            { attrs: { "no-gutters": "" } },
            [
              _c(
                "b-col",
                { attrs: { cols: "5" } },
                [
                  _c("b-form-select", {
                    staticClass: "mb-1",
                    attrs: { options: _vm.optiuniCalculatoare },
                    on: { change: _vm.incarcaImprimante },
                    model: {
                      value: _vm.formular.imprimanta_certificat_id,
                      callback: function ($$v) {
                        _vm.$set(_vm.formular, "imprimanta_certificat_id", $$v)
                      },
                      expression: "formular.imprimanta_certificat_id",
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
                  _c("b-form-select", {
                    staticClass: "mb-1",
                    attrs: {
                      options: _vm.optiuniImprimante,
                      disabled:
                        !_vm.formular.imprimanta_certificat_id ||
                        _vm.imprimanteInCurs,
                    },
                    model: {
                      value: _vm.formular.imprimanta,
                      callback: function ($$v) {
                        _vm.$set(_vm.formular, "imprimanta", $$v)
                      },
                      expression: "formular.imprimanta",
                    },
                  }),
                ],
                1
              ),
            ],
            1
          ),
          _vm._v(" "),
          _vm.eroareImprimante
            ? _c("small", { staticClass: "text-danger d-block mb-2" }, [
                _vm._v(_vm._s(_vm.eroareImprimante)),
              ])
            : _c("small", { staticClass: "text-muted d-block mb-2" }, [
                _vm._v(
                  "\n      Declarațiile și recipisele bifate pentru imprimare se trimit direct aici,\n      pe calculatorul ales — nu se mai descarcă niciun fișier. Lista vine de la\n      calculatorul acela, deci trebuie să fie pornit.\n    "
                ),
              ]),
          _vm._v(" "),
          _c("hr"),
          _vm._v(" "),
          _c("label", [
            _vm._v("Adrese IP de la care are voie să se conecteze"),
          ]),
          _vm._v(" "),
          _c("b-form-textarea", {
            staticClass: "mb-1",
            attrs: {
              rows: "2",
              placeholder:
                "Lăsat gol: de oriunde. Ex: 86.120.4.15, 192.168.1.0/24, 79.112.*",
            },
            model: {
              value: _vm.formular.ip_permise,
              callback: function ($$v) {
                _vm.$set(_vm.formular, "ip_permise", $$v)
              },
              expression: "formular.ip_permise",
            },
          }),
          _vm._v(" "),
          _vm.ipCurent
            ? _c(
                "div",
                { staticClass: "mb-1" },
                [
                  _c("small", { staticClass: "text-muted" }, [
                    _vm._v("\n        Adresa de la care lucrați acum: "),
                    _c("code", [_vm._v(_vm._s(_vm.ipCurent))]),
                  ]),
                  _vm._v(" "),
                  _c(
                    "b-button",
                    {
                      staticClass: "py-0 px-50 ml-50",
                      attrs: { size: "sm", variant: "flat-primary" },
                      on: { click: _vm.adaugaIpCurent },
                    },
                    [_c("small", [_vm._v("adaugă")])]
                  ),
                ],
                1
              )
            : _vm._e(),
          _vm._v(" "),
          _c("small", { staticClass: "text-muted d-block mb-2" }, [
            _vm._v(
              "\n      Se scriu despărțite prin virgulă sau pe rânduri. Se acceptă o adresă\n      întreagă, un interval ("
            ),
            _c("code", [_vm._v("/24")]),
            _vm._v(") sau un început de adresă\n      ("),
            _c("code", [_vm._v("*")]),
            _vm._v("). "),
            _c("strong", [_vm._v("Gol înseamnă de oriunde.")]),
            _vm._v(
              "\n      La o încercare de la altă adresă, contul e respins, iar administratorul\n      aplicației primește un email cu cine și de unde a încercat.\n    "
            ),
          ]),
          _vm._v(" "),
          _c("hr"),
          _vm._v(" "),
          _c(
            "b-form-checkbox",
            {
              staticClass: "mb-1",
              model: {
                value: _vm.formular.administrator,
                callback: function ($$v) {
                  _vm.$set(_vm.formular, "administrator", $$v)
                },
                expression: "formular.administrator",
              },
            },
            [
              _vm._v(
                "\n      Administrator: gestionează utilizatorii și vede tot ce s-a lucrat pentru firmă\n    "
              ),
            ]
          ),
          _vm._v(" "),
          _c(
            "b-form-checkbox",
            {
              staticClass: "mb-1",
              attrs: { disabled: _vm.formular.administrator },
              model: {
                value: _vm.formular.poate_semna,
                callback: function ($$v) {
                  _vm.$set(_vm.formular, "poate_semna", $$v)
                },
                expression: "formular.poate_semna",
              },
            },
            [_vm._v("\n      Poate semna declarațiile validate\n    ")]
          ),
          _vm._v(" "),
          _c(
            "b-form-checkbox",
            {
              staticClass: "mb-1",
              attrs: { disabled: _vm.formular.administrator },
              model: {
                value: _vm.formular.poate_depune,
                callback: function ($$v) {
                  _vm.$set(_vm.formular, "poate_depune", $$v)
                },
                expression: "formular.poate_depune",
              },
            },
            [_vm._v("\n      Poate depune declarațiile semnate la ANAF\n    ")]
          ),
          _vm._v(" "),
          _c("small", { staticClass: "text-muted d-block mb-2" }, [
            _vm._v(
              "\n      Administratorul firmei are oricum ambele drepturi.\n      "
            ),
            _c("strong", [_vm._v("Depunerea nu se mai poate lua înapoi")]),
            _vm._v(", așa că se dă anume.\n    "),
          ]),
          _vm._v(" "),
          _vm.formular.id && !_vm.formular.eu
            ? _c(
                "b-form-checkbox",
                {
                  model: {
                    value: _vm.formular.blocat,
                    callback: function ($$v) {
                      _vm.$set(_vm.formular, "blocat", $$v)
                    },
                    expression: "formular.blocat",
                  },
                },
                [
                  _vm._v(
                    "\n      Blocat (nu se mai poate autentifica; sesiunile deschise se închid)\n    "
                  ),
                ]
              )
            : _vm._e(),
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

/***/ "./resources/js/src/libs/flux.js":
/*!***************************************!*\
  !*** ./resources/js/src/libs/flux.js ***!
  \***************************************/
/*! exports provided: default, areFlux */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return citesteFluxul; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "areFlux", function() { return areFlux; });
/* harmony import */ var C_dianasoft_serviciiweb_web_node_modules_babel_runtime_helpers_esm_objectSpread2_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./node_modules/@babel/runtime/helpers/esm/objectSpread2.js */ "./node_modules/@babel/runtime/helpers/esm/objectSpread2.js");
/* harmony import */ var core_js_modules_es_regexp_exec_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! core-js/modules/es.regexp.exec.js */ "./node_modules/core-js/modules/es.regexp.exec.js");
/* harmony import */ var core_js_modules_es_regexp_exec_js__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_es_regexp_exec_js__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var core_js_modules_es_string_replace_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! core-js/modules/es.string.replace.js */ "./node_modules/core-js/modules/es.string.replace.js");
/* harmony import */ var core_js_modules_es_string_replace_js__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_es_string_replace_js__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var core_js_modules_es_array_concat_js__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! core-js/modules/es.array.concat.js */ "./node_modules/core-js/modules/es.array.concat.js");
/* harmony import */ var core_js_modules_es_array_concat_js__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_es_array_concat_js__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var core_js_modules_es_object_to_string_js__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! core-js/modules/es.object.to-string.js */ "./node_modules/core-js/modules/es.object.to-string.js");
/* harmony import */ var core_js_modules_es_object_to_string_js__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_es_object_to_string_js__WEBPACK_IMPORTED_MODULE_4__);
/* harmony import */ var core_js_modules_es_json_stringify_js__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! core-js/modules/es.json.stringify.js */ "./node_modules/core-js/modules/es.json.stringify.js");
/* harmony import */ var core_js_modules_es_json_stringify_js__WEBPACK_IMPORTED_MODULE_5___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_es_json_stringify_js__WEBPACK_IMPORTED_MODULE_5__);
/* harmony import */ var core_js_modules_es_string_split_js__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! core-js/modules/es.string.split.js */ "./node_modules/core-js/modules/es.string.split.js");
/* harmony import */ var core_js_modules_es_string_split_js__WEBPACK_IMPORTED_MODULE_6___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_es_string_split_js__WEBPACK_IMPORTED_MODULE_6__);
/* harmony import */ var core_js_modules_web_dom_collections_for_each_js__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! core-js/modules/web.dom-collections.for-each.js */ "./node_modules/core-js/modules/web.dom-collections.for-each.js");
/* harmony import */ var core_js_modules_web_dom_collections_for_each_js__WEBPACK_IMPORTED_MODULE_7___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_web_dom_collections_for_each_js__WEBPACK_IMPORTED_MODULE_7__);
/* harmony import */ var core_js_modules_es_string_trim_js__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! core-js/modules/es.string.trim.js */ "./node_modules/core-js/modules/es.string.trim.js");
/* harmony import */ var core_js_modules_es_string_trim_js__WEBPACK_IMPORTED_MODULE_8___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_es_string_trim_js__WEBPACK_IMPORTED_MODULE_8__);
/* harmony import */ var _axios__WEBPACK_IMPORTED_MODULE_9__ = __webpack_require__(/*! @axios */ "./resources/js/src/libs/axios.js");










/**
 * Citirea unui răspuns care curge, rând cu rând.
 *
 * O descărcare de zeci de documente ține minute: fiecare are pauza cerută de
 * ANAF și drumul până la tokenul clientului. Într-un răspuns obișnuit, omul
 * vede o rotiță și atât — nu știe dacă merge, unde s-a ajuns, sau dacă s-a
 * împotmolit. Serverul trimite de aceea câte un obiect JSON pe rând (NDJSON),
 * iar de aici fiecare rând ajunge la filă de îndată ce sosește.
 *
 * Se folosește fetch, nu axios: numai el dă acces la conținut pe măsură ce vine.
 */

/** Adresa întreagă a unei rute din API. */

function adresa(cale) {
  // Adresa API vine din window.api_url (definit în pagină); instanța axios nu
  // are baseURL propriu, așa că nu se poate citi doar de acolo.
  var baza = (window.api_url || _axios__WEBPACK_IMPORTED_MODULE_9__["default"].defaults.baseURL || '').replace(/\/+$/, '');
  return "".concat(baza, "/").concat(String(cale).replace(/^\/+/, ''));
}
/**
 * Ce înseamnă, pe șleau, un răspuns care n-a mers.
 *
 * „HTTP 404" nu spune nimănui nimic — și cel mai adesea nu înseamnă că lipsește
 * ceva din datele omului, ci că serverul nu cunoaște ruta: ori are cod mai
 * vechi, ori i-au rămas rutele în cache de dinainte ca ea să fie adăugată.
 * Scris așa, se știe de la prima citire unde să se caute.
 */


function eroareaLegaturii(status) {
  var talcuri = {
    401: 'sesiunea a expirat — intrați din nou în aplicație',
    403: 'contul acesta nu are drept la operația cerută',
    404: 'serverul nu cunoaște această adresă — are cod mai vechi sau rutele rămase în cache' + ' (pe server: php artisan route:clear)',
    419: 'sesiunea a expirat — reîncărcați pagina',
    500: 'serverul a întâmpinat o eroare — vedeți jurnalul aplicației',
    502: 'serverul din față n-a primit răspuns',
    504: 'răspunsul a întârziat prea mult'
  };
  var eroare = new Error(talcuri[status] || "r\u0103spuns nea\u0219teptat de la server (HTTP ".concat(status, ")"));
  eroare.status = status;
  return eroare;
}
/**
 * Cere ruta și dă fiecare rând primit, pe măsură ce sosește.
 *
 * @param {string} cale ruta din API, fără adresa de bază
 * @param {function(object): void} laFiecarePas primește fiecare obiect citit
 * @param {object} [optiuni] „corp" pleacă drept JSON, cu POST
 * @returns {Promise<void>} se împlinește când fluxul s-a încheiat
 */


function citesteFluxul(cale, laFiecarePas) {
  var optiuni = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : {};
  var antete = _axios__WEBPACK_IMPORTED_MODULE_9__["default"].defaults.headers.common;
  var corp = optiuni.corp;
  return fetch(adresa(cale), {
    // Lucrările care primesc o listă — depunerea mai multor declarații, de
    // pildă — nu încap într-o adresă, deci pleacă cu POST.
    method: optiuni.metoda || (corp ? 'POST' : 'GET'),
    headers: Object(C_dianasoft_serviciiweb_web_node_modules_babel_runtime_helpers_esm_objectSpread2_js__WEBPACK_IMPORTED_MODULE_0__["default"])({
      // „application/json" trebuie să apară: fără el, la o sesiune expirată
      // Laravel redirecționează spre login în loc să răspundă 401, iar fluxul
      // ar părea gol în loc de eșuat.
      Accept: 'application/json, application/x-ndjson',
      Authorization: antete.Authorization,
      AuthorizationHeader: antete.AuthorizationHeader
    }, corp ? {
      'Content-Type': 'application/json'
    } : {}),
    body: corp ? JSON.stringify(corp) : undefined
  }).then(function (raspuns) {
    if (!raspuns.ok) throw eroareaLegaturii(raspuns.status);
    var cititor = raspuns.body.getReader();
    var decodor = new TextDecoder('utf-8');
    var ramas = '';

    var urmatorul = function urmatorul() {
      return cititor.read().then(function (_ref) {
        var done = _ref.done,
            value = _ref.value;
        if (done) return null;
        ramas += decodor.decode(value, {
          stream: true
        });
        var randuri = ramas.split('\n'); // Ultimul poate fi incomplet: rămâne pentru bucata următoare.

        ramas = randuri.pop();
        randuri.forEach(function (rand) {
          if (!rand.trim()) return;

          try {
            laFiecarePas(JSON.parse(rand));
          } catch (e) {// Un rând stricat nu are de ce să oprească restul lucrului.
          }
        });
        return urmatorul();
      });
    };

    return urmatorul();
  });
}
/** Browserele fără fetch cu flux (foarte vechi) trebuie să rămână pe calea obișnuită. */

function areFlux() {
  return typeof window.fetch === 'function' && typeof window.TextDecoder === 'function';
}

/***/ }),

/***/ "./resources/js/src/views/app_pages/Spv.vue":
/*!**************************************************!*\
  !*** ./resources/js/src/views/app_pages/Spv.vue ***!
  \**************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _Spv_vue_vue_type_template_id_50cb4596_scoped_true___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./Spv.vue?vue&type=template&id=50cb4596&scoped=true& */ "./resources/js/src/views/app_pages/Spv.vue?vue&type=template&id=50cb4596&scoped=true&");
/* harmony import */ var _Spv_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./Spv.vue?vue&type=script&lang=js& */ "./resources/js/src/views/app_pages/Spv.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport *//* harmony import */ var _Spv_vue_vue_type_style_index_0_id_50cb4596_lang_scss_scoped_true___WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./Spv.vue?vue&type=style&index=0&id=50cb4596&lang=scss&scoped=true& */ "./resources/js/src/views/app_pages/Spv.vue?vue&type=style&index=0&id=50cb4596&lang=scss&scoped=true&");
/* harmony import */ var _Spv_vue_vue_type_style_index_1_lang_scss___WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./Spv.vue?vue&type=style&index=1&lang=scss& */ "./resources/js/src/views/app_pages/Spv.vue?vue&type=style&index=1&lang=scss&");
/* harmony import */ var _node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ../../../../../node_modules/vue-loader/lib/runtime/componentNormalizer.js */ "./node_modules/vue-loader/lib/runtime/componentNormalizer.js");







/* normalize component */

var component = Object(_node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_4__["default"])(
  _Spv_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__["default"],
  _Spv_vue_vue_type_template_id_50cb4596_scoped_true___WEBPACK_IMPORTED_MODULE_0__["render"],
  _Spv_vue_vue_type_template_id_50cb4596_scoped_true___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"],
  false,
  null,
  "50cb4596",
  null
  
)

/* hot reload */
if (false) { var api; }
component.options.__file = "resources/js/src/views/app_pages/Spv.vue"
/* harmony default export */ __webpack_exports__["default"] = (component.exports);

/***/ }),

/***/ "./resources/js/src/views/app_pages/Spv.vue?vue&type=script&lang=js&":
/*!***************************************************************************!*\
  !*** ./resources/js/src/views/app_pages/Spv.vue?vue&type=script&lang=js& ***!
  \***************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_Spv_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../node_modules/babel-loader/lib??ref--4-0!../../../../../node_modules/vue-loader/lib??vue-loader-options!./Spv.vue?vue&type=script&lang=js& */ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/app_pages/Spv.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__["default"] = (_node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_Spv_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__["default"]); 

/***/ }),

/***/ "./resources/js/src/views/app_pages/Spv.vue?vue&type=style&index=0&id=50cb4596&lang=scss&scoped=true&":
/*!************************************************************************************************************!*\
  !*** ./resources/js/src/views/app_pages/Spv.vue?vue&type=style&index=0&id=50cb4596&lang=scss&scoped=true& ***!
  \************************************************************************************************************/
/*! no static exports found */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_style_loader_index_js_node_modules_css_loader_dist_cjs_js_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_7_2_node_modules_sass_loader_dist_cjs_js_ref_7_3_node_modules_sass_loader_dist_cjs_js_ref_11_0_node_modules_vue_loader_lib_index_js_vue_loader_options_Spv_vue_vue_type_style_index_0_id_50cb4596_lang_scss_scoped_true___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../node_modules/style-loader!../../../../../node_modules/css-loader/dist/cjs.js!../../../../../node_modules/vue-loader/lib/loaders/stylePostLoader.js!../../../../../node_modules/postcss-loader/src??ref--7-2!../../../../../node_modules/sass-loader/dist/cjs.js??ref--7-3!../../../../../node_modules/sass-loader/dist/cjs.js??ref--11-0!../../../../../node_modules/vue-loader/lib??vue-loader-options!./Spv.vue?vue&type=style&index=0&id=50cb4596&lang=scss&scoped=true& */ "./node_modules/style-loader/index.js!./node_modules/css-loader/dist/cjs.js!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src/index.js?!./node_modules/sass-loader/dist/cjs.js?!./node_modules/sass-loader/dist/cjs.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/app_pages/Spv.vue?vue&type=style&index=0&id=50cb4596&lang=scss&scoped=true&");
/* harmony import */ var _node_modules_style_loader_index_js_node_modules_css_loader_dist_cjs_js_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_7_2_node_modules_sass_loader_dist_cjs_js_ref_7_3_node_modules_sass_loader_dist_cjs_js_ref_11_0_node_modules_vue_loader_lib_index_js_vue_loader_options_Spv_vue_vue_type_style_index_0_id_50cb4596_lang_scss_scoped_true___WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_node_modules_style_loader_index_js_node_modules_css_loader_dist_cjs_js_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_7_2_node_modules_sass_loader_dist_cjs_js_ref_7_3_node_modules_sass_loader_dist_cjs_js_ref_11_0_node_modules_vue_loader_lib_index_js_vue_loader_options_Spv_vue_vue_type_style_index_0_id_50cb4596_lang_scss_scoped_true___WEBPACK_IMPORTED_MODULE_0__);
/* harmony reexport (unknown) */ for(var __WEBPACK_IMPORT_KEY__ in _node_modules_style_loader_index_js_node_modules_css_loader_dist_cjs_js_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_7_2_node_modules_sass_loader_dist_cjs_js_ref_7_3_node_modules_sass_loader_dist_cjs_js_ref_11_0_node_modules_vue_loader_lib_index_js_vue_loader_options_Spv_vue_vue_type_style_index_0_id_50cb4596_lang_scss_scoped_true___WEBPACK_IMPORTED_MODULE_0__) if(["default"].indexOf(__WEBPACK_IMPORT_KEY__) < 0) (function(key) { __webpack_require__.d(__webpack_exports__, key, function() { return _node_modules_style_loader_index_js_node_modules_css_loader_dist_cjs_js_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_7_2_node_modules_sass_loader_dist_cjs_js_ref_7_3_node_modules_sass_loader_dist_cjs_js_ref_11_0_node_modules_vue_loader_lib_index_js_vue_loader_options_Spv_vue_vue_type_style_index_0_id_50cb4596_lang_scss_scoped_true___WEBPACK_IMPORTED_MODULE_0__[key]; }) }(__WEBPACK_IMPORT_KEY__));


/***/ }),

/***/ "./resources/js/src/views/app_pages/Spv.vue?vue&type=style&index=1&lang=scss&":
/*!************************************************************************************!*\
  !*** ./resources/js/src/views/app_pages/Spv.vue?vue&type=style&index=1&lang=scss& ***!
  \************************************************************************************/
/*! no static exports found */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_style_loader_index_js_node_modules_css_loader_dist_cjs_js_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_7_2_node_modules_sass_loader_dist_cjs_js_ref_7_3_node_modules_sass_loader_dist_cjs_js_ref_11_0_node_modules_vue_loader_lib_index_js_vue_loader_options_Spv_vue_vue_type_style_index_1_lang_scss___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../node_modules/style-loader!../../../../../node_modules/css-loader/dist/cjs.js!../../../../../node_modules/vue-loader/lib/loaders/stylePostLoader.js!../../../../../node_modules/postcss-loader/src??ref--7-2!../../../../../node_modules/sass-loader/dist/cjs.js??ref--7-3!../../../../../node_modules/sass-loader/dist/cjs.js??ref--11-0!../../../../../node_modules/vue-loader/lib??vue-loader-options!./Spv.vue?vue&type=style&index=1&lang=scss& */ "./node_modules/style-loader/index.js!./node_modules/css-loader/dist/cjs.js!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src/index.js?!./node_modules/sass-loader/dist/cjs.js?!./node_modules/sass-loader/dist/cjs.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/app_pages/Spv.vue?vue&type=style&index=1&lang=scss&");
/* harmony import */ var _node_modules_style_loader_index_js_node_modules_css_loader_dist_cjs_js_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_7_2_node_modules_sass_loader_dist_cjs_js_ref_7_3_node_modules_sass_loader_dist_cjs_js_ref_11_0_node_modules_vue_loader_lib_index_js_vue_loader_options_Spv_vue_vue_type_style_index_1_lang_scss___WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_node_modules_style_loader_index_js_node_modules_css_loader_dist_cjs_js_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_7_2_node_modules_sass_loader_dist_cjs_js_ref_7_3_node_modules_sass_loader_dist_cjs_js_ref_11_0_node_modules_vue_loader_lib_index_js_vue_loader_options_Spv_vue_vue_type_style_index_1_lang_scss___WEBPACK_IMPORTED_MODULE_0__);
/* harmony reexport (unknown) */ for(var __WEBPACK_IMPORT_KEY__ in _node_modules_style_loader_index_js_node_modules_css_loader_dist_cjs_js_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_7_2_node_modules_sass_loader_dist_cjs_js_ref_7_3_node_modules_sass_loader_dist_cjs_js_ref_11_0_node_modules_vue_loader_lib_index_js_vue_loader_options_Spv_vue_vue_type_style_index_1_lang_scss___WEBPACK_IMPORTED_MODULE_0__) if(["default"].indexOf(__WEBPACK_IMPORT_KEY__) < 0) (function(key) { __webpack_require__.d(__webpack_exports__, key, function() { return _node_modules_style_loader_index_js_node_modules_css_loader_dist_cjs_js_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_7_2_node_modules_sass_loader_dist_cjs_js_ref_7_3_node_modules_sass_loader_dist_cjs_js_ref_11_0_node_modules_vue_loader_lib_index_js_vue_loader_options_Spv_vue_vue_type_style_index_1_lang_scss___WEBPACK_IMPORTED_MODULE_0__[key]; }) }(__WEBPACK_IMPORT_KEY__));


/***/ }),

/***/ "./resources/js/src/views/app_pages/Spv.vue?vue&type=template&id=50cb4596&scoped=true&":
/*!*********************************************************************************************!*\
  !*** ./resources/js/src/views/app_pages/Spv.vue?vue&type=template&id=50cb4596&scoped=true& ***!
  \*********************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_Spv_vue_vue_type_template_id_50cb4596_scoped_true___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!../../../../../node_modules/vue-loader/lib??vue-loader-options!./Spv.vue?vue&type=template&id=50cb4596&scoped=true& */ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/app_pages/Spv.vue?vue&type=template&id=50cb4596&scoped=true&");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "render", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_Spv_vue_vue_type_template_id_50cb4596_scoped_true___WEBPACK_IMPORTED_MODULE_0__["render"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "staticRenderFns", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_Spv_vue_vue_type_template_id_50cb4596_scoped_true___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"]; });



/***/ }),

/***/ "./resources/js/src/views/app_pages/spv/CereriDePin.vue":
/*!**************************************************************!*\
  !*** ./resources/js/src/views/app_pages/spv/CereriDePin.vue ***!
  \**************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _CereriDePin_vue_vue_type_template_id_6119d22f___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./CereriDePin.vue?vue&type=template&id=6119d22f& */ "./resources/js/src/views/app_pages/spv/CereriDePin.vue?vue&type=template&id=6119d22f&");
/* harmony import */ var _CereriDePin_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./CereriDePin.vue?vue&type=script&lang=js& */ "./resources/js/src/views/app_pages/spv/CereriDePin.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport *//* harmony import */ var _node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../../../../../node_modules/vue-loader/lib/runtime/componentNormalizer.js */ "./node_modules/vue-loader/lib/runtime/componentNormalizer.js");





/* normalize component */

var component = Object(_node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__["default"])(
  _CereriDePin_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__["default"],
  _CereriDePin_vue_vue_type_template_id_6119d22f___WEBPACK_IMPORTED_MODULE_0__["render"],
  _CereriDePin_vue_vue_type_template_id_6119d22f___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"],
  false,
  null,
  null,
  null
  
)

/* hot reload */
if (false) { var api; }
component.options.__file = "resources/js/src/views/app_pages/spv/CereriDePin.vue"
/* harmony default export */ __webpack_exports__["default"] = (component.exports);

/***/ }),

/***/ "./resources/js/src/views/app_pages/spv/CereriDePin.vue?vue&type=script&lang=js&":
/*!***************************************************************************************!*\
  !*** ./resources/js/src/views/app_pages/spv/CereriDePin.vue?vue&type=script&lang=js& ***!
  \***************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_CereriDePin_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../../node_modules/babel-loader/lib??ref--4-0!../../../../../../node_modules/vue-loader/lib??vue-loader-options!./CereriDePin.vue?vue&type=script&lang=js& */ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/app_pages/spv/CereriDePin.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__["default"] = (_node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_CereriDePin_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__["default"]); 

/***/ }),

/***/ "./resources/js/src/views/app_pages/spv/CereriDePin.vue?vue&type=template&id=6119d22f&":
/*!*********************************************************************************************!*\
  !*** ./resources/js/src/views/app_pages/spv/CereriDePin.vue?vue&type=template&id=6119d22f& ***!
  \*********************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_CereriDePin_vue_vue_type_template_id_6119d22f___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../../node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!../../../../../../node_modules/vue-loader/lib??vue-loader-options!./CereriDePin.vue?vue&type=template&id=6119d22f& */ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/app_pages/spv/CereriDePin.vue?vue&type=template&id=6119d22f&");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "render", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_CereriDePin_vue_vue_type_template_id_6119d22f___WEBPACK_IMPORTED_MODULE_0__["render"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "staticRenderFns", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_CereriDePin_vue_vue_type_template_id_6119d22f___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"]; });



/***/ }),

/***/ "./resources/js/src/views/app_pages/spv/Certificate.vue":
/*!**************************************************************!*\
  !*** ./resources/js/src/views/app_pages/spv/Certificate.vue ***!
  \**************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _Certificate_vue_vue_type_template_id_2163fac4_scoped_true___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./Certificate.vue?vue&type=template&id=2163fac4&scoped=true& */ "./resources/js/src/views/app_pages/spv/Certificate.vue?vue&type=template&id=2163fac4&scoped=true&");
/* harmony import */ var _Certificate_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./Certificate.vue?vue&type=script&lang=js& */ "./resources/js/src/views/app_pages/spv/Certificate.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport *//* harmony import */ var _Certificate_vue_vue_type_style_index_0_id_2163fac4_scoped_true_lang_css___WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./Certificate.vue?vue&type=style&index=0&id=2163fac4&scoped=true&lang=css& */ "./resources/js/src/views/app_pages/spv/Certificate.vue?vue&type=style&index=0&id=2163fac4&scoped=true&lang=css&");
/* harmony import */ var _node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../../../../../../node_modules/vue-loader/lib/runtime/componentNormalizer.js */ "./node_modules/vue-loader/lib/runtime/componentNormalizer.js");






/* normalize component */

var component = Object(_node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_3__["default"])(
  _Certificate_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__["default"],
  _Certificate_vue_vue_type_template_id_2163fac4_scoped_true___WEBPACK_IMPORTED_MODULE_0__["render"],
  _Certificate_vue_vue_type_template_id_2163fac4_scoped_true___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"],
  false,
  null,
  "2163fac4",
  null
  
)

/* hot reload */
if (false) { var api; }
component.options.__file = "resources/js/src/views/app_pages/spv/Certificate.vue"
/* harmony default export */ __webpack_exports__["default"] = (component.exports);

/***/ }),

/***/ "./resources/js/src/views/app_pages/spv/Certificate.vue?vue&type=script&lang=js&":
/*!***************************************************************************************!*\
  !*** ./resources/js/src/views/app_pages/spv/Certificate.vue?vue&type=script&lang=js& ***!
  \***************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_Certificate_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../../node_modules/babel-loader/lib??ref--4-0!../../../../../../node_modules/vue-loader/lib??vue-loader-options!./Certificate.vue?vue&type=script&lang=js& */ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/app_pages/spv/Certificate.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__["default"] = (_node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_Certificate_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__["default"]); 

/***/ }),

/***/ "./resources/js/src/views/app_pages/spv/Certificate.vue?vue&type=style&index=0&id=2163fac4&scoped=true&lang=css&":
/*!***********************************************************************************************************************!*\
  !*** ./resources/js/src/views/app_pages/spv/Certificate.vue?vue&type=style&index=0&id=2163fac4&scoped=true&lang=css& ***!
  \***********************************************************************************************************************/
/*! no static exports found */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_style_loader_index_js_node_modules_css_loader_dist_cjs_js_ref_6_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_6_2_node_modules_vue_loader_lib_index_js_vue_loader_options_Certificate_vue_vue_type_style_index_0_id_2163fac4_scoped_true_lang_css___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../../node_modules/style-loader!../../../../../../node_modules/css-loader/dist/cjs.js??ref--6-1!../../../../../../node_modules/vue-loader/lib/loaders/stylePostLoader.js!../../../../../../node_modules/postcss-loader/src??ref--6-2!../../../../../../node_modules/vue-loader/lib??vue-loader-options!./Certificate.vue?vue&type=style&index=0&id=2163fac4&scoped=true&lang=css& */ "./node_modules/style-loader/index.js!./node_modules/css-loader/dist/cjs.js?!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/app_pages/spv/Certificate.vue?vue&type=style&index=0&id=2163fac4&scoped=true&lang=css&");
/* harmony import */ var _node_modules_style_loader_index_js_node_modules_css_loader_dist_cjs_js_ref_6_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_6_2_node_modules_vue_loader_lib_index_js_vue_loader_options_Certificate_vue_vue_type_style_index_0_id_2163fac4_scoped_true_lang_css___WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_node_modules_style_loader_index_js_node_modules_css_loader_dist_cjs_js_ref_6_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_6_2_node_modules_vue_loader_lib_index_js_vue_loader_options_Certificate_vue_vue_type_style_index_0_id_2163fac4_scoped_true_lang_css___WEBPACK_IMPORTED_MODULE_0__);
/* harmony reexport (unknown) */ for(var __WEBPACK_IMPORT_KEY__ in _node_modules_style_loader_index_js_node_modules_css_loader_dist_cjs_js_ref_6_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_6_2_node_modules_vue_loader_lib_index_js_vue_loader_options_Certificate_vue_vue_type_style_index_0_id_2163fac4_scoped_true_lang_css___WEBPACK_IMPORTED_MODULE_0__) if(["default"].indexOf(__WEBPACK_IMPORT_KEY__) < 0) (function(key) { __webpack_require__.d(__webpack_exports__, key, function() { return _node_modules_style_loader_index_js_node_modules_css_loader_dist_cjs_js_ref_6_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_6_2_node_modules_vue_loader_lib_index_js_vue_loader_options_Certificate_vue_vue_type_style_index_0_id_2163fac4_scoped_true_lang_css___WEBPACK_IMPORTED_MODULE_0__[key]; }) }(__WEBPACK_IMPORT_KEY__));


/***/ }),

/***/ "./resources/js/src/views/app_pages/spv/Certificate.vue?vue&type=template&id=2163fac4&scoped=true&":
/*!*********************************************************************************************************!*\
  !*** ./resources/js/src/views/app_pages/spv/Certificate.vue?vue&type=template&id=2163fac4&scoped=true& ***!
  \*********************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_Certificate_vue_vue_type_template_id_2163fac4_scoped_true___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../../node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!../../../../../../node_modules/vue-loader/lib??vue-loader-options!./Certificate.vue?vue&type=template&id=2163fac4&scoped=true& */ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/app_pages/spv/Certificate.vue?vue&type=template&id=2163fac4&scoped=true&");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "render", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_Certificate_vue_vue_type_template_id_2163fac4_scoped_true___WEBPACK_IMPORTED_MODULE_0__["render"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "staticRenderFns", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_Certificate_vue_vue_type_template_id_2163fac4_scoped_true___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"]; });



/***/ }),

/***/ "./resources/js/src/views/app_pages/spv/Declaratii.vue":
/*!*************************************************************!*\
  !*** ./resources/js/src/views/app_pages/spv/Declaratii.vue ***!
  \*************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _Declaratii_vue_vue_type_template_id_fcac4d92_scoped_true___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./Declaratii.vue?vue&type=template&id=fcac4d92&scoped=true& */ "./resources/js/src/views/app_pages/spv/Declaratii.vue?vue&type=template&id=fcac4d92&scoped=true&");
/* harmony import */ var _Declaratii_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./Declaratii.vue?vue&type=script&lang=js& */ "./resources/js/src/views/app_pages/spv/Declaratii.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport *//* harmony import */ var _Declaratii_vue_vue_type_style_index_0_id_fcac4d92_scoped_true_lang_css___WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./Declaratii.vue?vue&type=style&index=0&id=fcac4d92&scoped=true&lang=css& */ "./resources/js/src/views/app_pages/spv/Declaratii.vue?vue&type=style&index=0&id=fcac4d92&scoped=true&lang=css&");
/* harmony import */ var _node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../../../../../../node_modules/vue-loader/lib/runtime/componentNormalizer.js */ "./node_modules/vue-loader/lib/runtime/componentNormalizer.js");






/* normalize component */

var component = Object(_node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_3__["default"])(
  _Declaratii_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__["default"],
  _Declaratii_vue_vue_type_template_id_fcac4d92_scoped_true___WEBPACK_IMPORTED_MODULE_0__["render"],
  _Declaratii_vue_vue_type_template_id_fcac4d92_scoped_true___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"],
  false,
  null,
  "fcac4d92",
  null
  
)

/* hot reload */
if (false) { var api; }
component.options.__file = "resources/js/src/views/app_pages/spv/Declaratii.vue"
/* harmony default export */ __webpack_exports__["default"] = (component.exports);

/***/ }),

/***/ "./resources/js/src/views/app_pages/spv/Declaratii.vue?vue&type=script&lang=js&":
/*!**************************************************************************************!*\
  !*** ./resources/js/src/views/app_pages/spv/Declaratii.vue?vue&type=script&lang=js& ***!
  \**************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_Declaratii_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../../node_modules/babel-loader/lib??ref--4-0!../../../../../../node_modules/vue-loader/lib??vue-loader-options!./Declaratii.vue?vue&type=script&lang=js& */ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/app_pages/spv/Declaratii.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__["default"] = (_node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_Declaratii_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__["default"]); 

/***/ }),

/***/ "./resources/js/src/views/app_pages/spv/Declaratii.vue?vue&type=style&index=0&id=fcac4d92&scoped=true&lang=css&":
/*!**********************************************************************************************************************!*\
  !*** ./resources/js/src/views/app_pages/spv/Declaratii.vue?vue&type=style&index=0&id=fcac4d92&scoped=true&lang=css& ***!
  \**********************************************************************************************************************/
/*! no static exports found */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_style_loader_index_js_node_modules_css_loader_dist_cjs_js_ref_6_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_6_2_node_modules_vue_loader_lib_index_js_vue_loader_options_Declaratii_vue_vue_type_style_index_0_id_fcac4d92_scoped_true_lang_css___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../../node_modules/style-loader!../../../../../../node_modules/css-loader/dist/cjs.js??ref--6-1!../../../../../../node_modules/vue-loader/lib/loaders/stylePostLoader.js!../../../../../../node_modules/postcss-loader/src??ref--6-2!../../../../../../node_modules/vue-loader/lib??vue-loader-options!./Declaratii.vue?vue&type=style&index=0&id=fcac4d92&scoped=true&lang=css& */ "./node_modules/style-loader/index.js!./node_modules/css-loader/dist/cjs.js?!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/app_pages/spv/Declaratii.vue?vue&type=style&index=0&id=fcac4d92&scoped=true&lang=css&");
/* harmony import */ var _node_modules_style_loader_index_js_node_modules_css_loader_dist_cjs_js_ref_6_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_6_2_node_modules_vue_loader_lib_index_js_vue_loader_options_Declaratii_vue_vue_type_style_index_0_id_fcac4d92_scoped_true_lang_css___WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_node_modules_style_loader_index_js_node_modules_css_loader_dist_cjs_js_ref_6_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_6_2_node_modules_vue_loader_lib_index_js_vue_loader_options_Declaratii_vue_vue_type_style_index_0_id_fcac4d92_scoped_true_lang_css___WEBPACK_IMPORTED_MODULE_0__);
/* harmony reexport (unknown) */ for(var __WEBPACK_IMPORT_KEY__ in _node_modules_style_loader_index_js_node_modules_css_loader_dist_cjs_js_ref_6_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_6_2_node_modules_vue_loader_lib_index_js_vue_loader_options_Declaratii_vue_vue_type_style_index_0_id_fcac4d92_scoped_true_lang_css___WEBPACK_IMPORTED_MODULE_0__) if(["default"].indexOf(__WEBPACK_IMPORT_KEY__) < 0) (function(key) { __webpack_require__.d(__webpack_exports__, key, function() { return _node_modules_style_loader_index_js_node_modules_css_loader_dist_cjs_js_ref_6_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_6_2_node_modules_vue_loader_lib_index_js_vue_loader_options_Declaratii_vue_vue_type_style_index_0_id_fcac4d92_scoped_true_lang_css___WEBPACK_IMPORTED_MODULE_0__[key]; }) }(__WEBPACK_IMPORT_KEY__));


/***/ }),

/***/ "./resources/js/src/views/app_pages/spv/Declaratii.vue?vue&type=template&id=fcac4d92&scoped=true&":
/*!********************************************************************************************************!*\
  !*** ./resources/js/src/views/app_pages/spv/Declaratii.vue?vue&type=template&id=fcac4d92&scoped=true& ***!
  \********************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_Declaratii_vue_vue_type_template_id_fcac4d92_scoped_true___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../../node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!../../../../../../node_modules/vue-loader/lib??vue-loader-options!./Declaratii.vue?vue&type=template&id=fcac4d92&scoped=true& */ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/app_pages/spv/Declaratii.vue?vue&type=template&id=fcac4d92&scoped=true&");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "render", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_Declaratii_vue_vue_type_template_id_fcac4d92_scoped_true___WEBPACK_IMPORTED_MODULE_0__["render"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "staticRenderFns", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_Declaratii_vue_vue_type_template_id_fcac4d92_scoped_true___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"]; });



/***/ }),

/***/ "./resources/js/src/views/app_pages/spv/Jurnal.vue":
/*!*********************************************************!*\
  !*** ./resources/js/src/views/app_pages/spv/Jurnal.vue ***!
  \*********************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _Jurnal_vue_vue_type_template_id_54c7497b___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./Jurnal.vue?vue&type=template&id=54c7497b& */ "./resources/js/src/views/app_pages/spv/Jurnal.vue?vue&type=template&id=54c7497b&");
/* harmony import */ var _Jurnal_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./Jurnal.vue?vue&type=script&lang=js& */ "./resources/js/src/views/app_pages/spv/Jurnal.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport *//* harmony import */ var _node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../../../../../node_modules/vue-loader/lib/runtime/componentNormalizer.js */ "./node_modules/vue-loader/lib/runtime/componentNormalizer.js");





/* normalize component */

var component = Object(_node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__["default"])(
  _Jurnal_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__["default"],
  _Jurnal_vue_vue_type_template_id_54c7497b___WEBPACK_IMPORTED_MODULE_0__["render"],
  _Jurnal_vue_vue_type_template_id_54c7497b___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"],
  false,
  null,
  null,
  null
  
)

/* hot reload */
if (false) { var api; }
component.options.__file = "resources/js/src/views/app_pages/spv/Jurnal.vue"
/* harmony default export */ __webpack_exports__["default"] = (component.exports);

/***/ }),

/***/ "./resources/js/src/views/app_pages/spv/Jurnal.vue?vue&type=script&lang=js&":
/*!**********************************************************************************!*\
  !*** ./resources/js/src/views/app_pages/spv/Jurnal.vue?vue&type=script&lang=js& ***!
  \**********************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_Jurnal_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../../node_modules/babel-loader/lib??ref--4-0!../../../../../../node_modules/vue-loader/lib??vue-loader-options!./Jurnal.vue?vue&type=script&lang=js& */ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/app_pages/spv/Jurnal.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__["default"] = (_node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_Jurnal_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__["default"]); 

/***/ }),

/***/ "./resources/js/src/views/app_pages/spv/Jurnal.vue?vue&type=template&id=54c7497b&":
/*!****************************************************************************************!*\
  !*** ./resources/js/src/views/app_pages/spv/Jurnal.vue?vue&type=template&id=54c7497b& ***!
  \****************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_Jurnal_vue_vue_type_template_id_54c7497b___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../../node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!../../../../../../node_modules/vue-loader/lib??vue-loader-options!./Jurnal.vue?vue&type=template&id=54c7497b& */ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/app_pages/spv/Jurnal.vue?vue&type=template&id=54c7497b&");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "render", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_Jurnal_vue_vue_type_template_id_54c7497b___WEBPACK_IMPORTED_MODULE_0__["render"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "staticRenderFns", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_Jurnal_vue_vue_type_template_id_54c7497b___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"]; });



/***/ }),

/***/ "./resources/js/src/views/app_pages/spv/Mesaje.vue":
/*!*********************************************************!*\
  !*** ./resources/js/src/views/app_pages/spv/Mesaje.vue ***!
  \*********************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _Mesaje_vue_vue_type_template_id_96f45f2c___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./Mesaje.vue?vue&type=template&id=96f45f2c& */ "./resources/js/src/views/app_pages/spv/Mesaje.vue?vue&type=template&id=96f45f2c&");
/* harmony import */ var _Mesaje_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./Mesaje.vue?vue&type=script&lang=js& */ "./resources/js/src/views/app_pages/spv/Mesaje.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport *//* harmony import */ var _Mesaje_vue_vue_type_style_index_0_lang_scss___WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./Mesaje.vue?vue&type=style&index=0&lang=scss& */ "./resources/js/src/views/app_pages/spv/Mesaje.vue?vue&type=style&index=0&lang=scss&");
/* harmony import */ var _node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../../../../../../node_modules/vue-loader/lib/runtime/componentNormalizer.js */ "./node_modules/vue-loader/lib/runtime/componentNormalizer.js");






/* normalize component */

var component = Object(_node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_3__["default"])(
  _Mesaje_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__["default"],
  _Mesaje_vue_vue_type_template_id_96f45f2c___WEBPACK_IMPORTED_MODULE_0__["render"],
  _Mesaje_vue_vue_type_template_id_96f45f2c___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"],
  false,
  null,
  null,
  null
  
)

/* hot reload */
if (false) { var api; }
component.options.__file = "resources/js/src/views/app_pages/spv/Mesaje.vue"
/* harmony default export */ __webpack_exports__["default"] = (component.exports);

/***/ }),

/***/ "./resources/js/src/views/app_pages/spv/Mesaje.vue?vue&type=script&lang=js&":
/*!**********************************************************************************!*\
  !*** ./resources/js/src/views/app_pages/spv/Mesaje.vue?vue&type=script&lang=js& ***!
  \**********************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_Mesaje_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../../node_modules/babel-loader/lib??ref--4-0!../../../../../../node_modules/vue-loader/lib??vue-loader-options!./Mesaje.vue?vue&type=script&lang=js& */ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/app_pages/spv/Mesaje.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__["default"] = (_node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_Mesaje_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__["default"]); 

/***/ }),

/***/ "./resources/js/src/views/app_pages/spv/Mesaje.vue?vue&type=style&index=0&lang=scss&":
/*!*******************************************************************************************!*\
  !*** ./resources/js/src/views/app_pages/spv/Mesaje.vue?vue&type=style&index=0&lang=scss& ***!
  \*******************************************************************************************/
/*! no static exports found */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_style_loader_index_js_node_modules_css_loader_dist_cjs_js_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_7_2_node_modules_sass_loader_dist_cjs_js_ref_7_3_node_modules_sass_loader_dist_cjs_js_ref_11_0_node_modules_vue_loader_lib_index_js_vue_loader_options_Mesaje_vue_vue_type_style_index_0_lang_scss___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../../node_modules/style-loader!../../../../../../node_modules/css-loader/dist/cjs.js!../../../../../../node_modules/vue-loader/lib/loaders/stylePostLoader.js!../../../../../../node_modules/postcss-loader/src??ref--7-2!../../../../../../node_modules/sass-loader/dist/cjs.js??ref--7-3!../../../../../../node_modules/sass-loader/dist/cjs.js??ref--11-0!../../../../../../node_modules/vue-loader/lib??vue-loader-options!./Mesaje.vue?vue&type=style&index=0&lang=scss& */ "./node_modules/style-loader/index.js!./node_modules/css-loader/dist/cjs.js!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src/index.js?!./node_modules/sass-loader/dist/cjs.js?!./node_modules/sass-loader/dist/cjs.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/app_pages/spv/Mesaje.vue?vue&type=style&index=0&lang=scss&");
/* harmony import */ var _node_modules_style_loader_index_js_node_modules_css_loader_dist_cjs_js_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_7_2_node_modules_sass_loader_dist_cjs_js_ref_7_3_node_modules_sass_loader_dist_cjs_js_ref_11_0_node_modules_vue_loader_lib_index_js_vue_loader_options_Mesaje_vue_vue_type_style_index_0_lang_scss___WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_node_modules_style_loader_index_js_node_modules_css_loader_dist_cjs_js_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_7_2_node_modules_sass_loader_dist_cjs_js_ref_7_3_node_modules_sass_loader_dist_cjs_js_ref_11_0_node_modules_vue_loader_lib_index_js_vue_loader_options_Mesaje_vue_vue_type_style_index_0_lang_scss___WEBPACK_IMPORTED_MODULE_0__);
/* harmony reexport (unknown) */ for(var __WEBPACK_IMPORT_KEY__ in _node_modules_style_loader_index_js_node_modules_css_loader_dist_cjs_js_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_7_2_node_modules_sass_loader_dist_cjs_js_ref_7_3_node_modules_sass_loader_dist_cjs_js_ref_11_0_node_modules_vue_loader_lib_index_js_vue_loader_options_Mesaje_vue_vue_type_style_index_0_lang_scss___WEBPACK_IMPORTED_MODULE_0__) if(["default"].indexOf(__WEBPACK_IMPORT_KEY__) < 0) (function(key) { __webpack_require__.d(__webpack_exports__, key, function() { return _node_modules_style_loader_index_js_node_modules_css_loader_dist_cjs_js_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_7_2_node_modules_sass_loader_dist_cjs_js_ref_7_3_node_modules_sass_loader_dist_cjs_js_ref_11_0_node_modules_vue_loader_lib_index_js_vue_loader_options_Mesaje_vue_vue_type_style_index_0_lang_scss___WEBPACK_IMPORTED_MODULE_0__[key]; }) }(__WEBPACK_IMPORT_KEY__));


/***/ }),

/***/ "./resources/js/src/views/app_pages/spv/Mesaje.vue?vue&type=template&id=96f45f2c&":
/*!****************************************************************************************!*\
  !*** ./resources/js/src/views/app_pages/spv/Mesaje.vue?vue&type=template&id=96f45f2c& ***!
  \****************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_Mesaje_vue_vue_type_template_id_96f45f2c___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../../node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!../../../../../../node_modules/vue-loader/lib??vue-loader-options!./Mesaje.vue?vue&type=template&id=96f45f2c& */ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/app_pages/spv/Mesaje.vue?vue&type=template&id=96f45f2c&");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "render", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_Mesaje_vue_vue_type_template_id_96f45f2c___WEBPACK_IMPORTED_MODULE_0__["render"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "staticRenderFns", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_Mesaje_vue_vue_type_template_id_96f45f2c___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"]; });



/***/ }),

/***/ "./resources/js/src/views/app_pages/spv/Societati.vue":
/*!************************************************************!*\
  !*** ./resources/js/src/views/app_pages/spv/Societati.vue ***!
  \************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _Societati_vue_vue_type_template_id_e2aaca68_scoped_true___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./Societati.vue?vue&type=template&id=e2aaca68&scoped=true& */ "./resources/js/src/views/app_pages/spv/Societati.vue?vue&type=template&id=e2aaca68&scoped=true&");
/* harmony import */ var _Societati_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./Societati.vue?vue&type=script&lang=js& */ "./resources/js/src/views/app_pages/spv/Societati.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport *//* harmony import */ var _Societati_vue_vue_type_style_index_0_id_e2aaca68_scoped_true_lang_css___WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./Societati.vue?vue&type=style&index=0&id=e2aaca68&scoped=true&lang=css& */ "./resources/js/src/views/app_pages/spv/Societati.vue?vue&type=style&index=0&id=e2aaca68&scoped=true&lang=css&");
/* harmony import */ var _node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../../../../../../node_modules/vue-loader/lib/runtime/componentNormalizer.js */ "./node_modules/vue-loader/lib/runtime/componentNormalizer.js");






/* normalize component */

var component = Object(_node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_3__["default"])(
  _Societati_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__["default"],
  _Societati_vue_vue_type_template_id_e2aaca68_scoped_true___WEBPACK_IMPORTED_MODULE_0__["render"],
  _Societati_vue_vue_type_template_id_e2aaca68_scoped_true___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"],
  false,
  null,
  "e2aaca68",
  null
  
)

/* hot reload */
if (false) { var api; }
component.options.__file = "resources/js/src/views/app_pages/spv/Societati.vue"
/* harmony default export */ __webpack_exports__["default"] = (component.exports);

/***/ }),

/***/ "./resources/js/src/views/app_pages/spv/Societati.vue?vue&type=script&lang=js&":
/*!*************************************************************************************!*\
  !*** ./resources/js/src/views/app_pages/spv/Societati.vue?vue&type=script&lang=js& ***!
  \*************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_Societati_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../../node_modules/babel-loader/lib??ref--4-0!../../../../../../node_modules/vue-loader/lib??vue-loader-options!./Societati.vue?vue&type=script&lang=js& */ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/app_pages/spv/Societati.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__["default"] = (_node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_Societati_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__["default"]); 

/***/ }),

/***/ "./resources/js/src/views/app_pages/spv/Societati.vue?vue&type=style&index=0&id=e2aaca68&scoped=true&lang=css&":
/*!*********************************************************************************************************************!*\
  !*** ./resources/js/src/views/app_pages/spv/Societati.vue?vue&type=style&index=0&id=e2aaca68&scoped=true&lang=css& ***!
  \*********************************************************************************************************************/
/*! no static exports found */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_style_loader_index_js_node_modules_css_loader_dist_cjs_js_ref_6_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_6_2_node_modules_vue_loader_lib_index_js_vue_loader_options_Societati_vue_vue_type_style_index_0_id_e2aaca68_scoped_true_lang_css___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../../node_modules/style-loader!../../../../../../node_modules/css-loader/dist/cjs.js??ref--6-1!../../../../../../node_modules/vue-loader/lib/loaders/stylePostLoader.js!../../../../../../node_modules/postcss-loader/src??ref--6-2!../../../../../../node_modules/vue-loader/lib??vue-loader-options!./Societati.vue?vue&type=style&index=0&id=e2aaca68&scoped=true&lang=css& */ "./node_modules/style-loader/index.js!./node_modules/css-loader/dist/cjs.js?!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/app_pages/spv/Societati.vue?vue&type=style&index=0&id=e2aaca68&scoped=true&lang=css&");
/* harmony import */ var _node_modules_style_loader_index_js_node_modules_css_loader_dist_cjs_js_ref_6_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_6_2_node_modules_vue_loader_lib_index_js_vue_loader_options_Societati_vue_vue_type_style_index_0_id_e2aaca68_scoped_true_lang_css___WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_node_modules_style_loader_index_js_node_modules_css_loader_dist_cjs_js_ref_6_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_6_2_node_modules_vue_loader_lib_index_js_vue_loader_options_Societati_vue_vue_type_style_index_0_id_e2aaca68_scoped_true_lang_css___WEBPACK_IMPORTED_MODULE_0__);
/* harmony reexport (unknown) */ for(var __WEBPACK_IMPORT_KEY__ in _node_modules_style_loader_index_js_node_modules_css_loader_dist_cjs_js_ref_6_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_6_2_node_modules_vue_loader_lib_index_js_vue_loader_options_Societati_vue_vue_type_style_index_0_id_e2aaca68_scoped_true_lang_css___WEBPACK_IMPORTED_MODULE_0__) if(["default"].indexOf(__WEBPACK_IMPORT_KEY__) < 0) (function(key) { __webpack_require__.d(__webpack_exports__, key, function() { return _node_modules_style_loader_index_js_node_modules_css_loader_dist_cjs_js_ref_6_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_6_2_node_modules_vue_loader_lib_index_js_vue_loader_options_Societati_vue_vue_type_style_index_0_id_e2aaca68_scoped_true_lang_css___WEBPACK_IMPORTED_MODULE_0__[key]; }) }(__WEBPACK_IMPORT_KEY__));


/***/ }),

/***/ "./resources/js/src/views/app_pages/spv/Societati.vue?vue&type=template&id=e2aaca68&scoped=true&":
/*!*******************************************************************************************************!*\
  !*** ./resources/js/src/views/app_pages/spv/Societati.vue?vue&type=template&id=e2aaca68&scoped=true& ***!
  \*******************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_Societati_vue_vue_type_template_id_e2aaca68_scoped_true___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../../node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!../../../../../../node_modules/vue-loader/lib??vue-loader-options!./Societati.vue?vue&type=template&id=e2aaca68&scoped=true& */ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/app_pages/spv/Societati.vue?vue&type=template&id=e2aaca68&scoped=true&");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "render", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_Societati_vue_vue_type_template_id_e2aaca68_scoped_true___WEBPACK_IMPORTED_MODULE_0__["render"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "staticRenderFns", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_Societati_vue_vue_type_template_id_e2aaca68_scoped_true___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"]; });



/***/ }),

/***/ "./resources/js/src/views/app_pages/spv/Solicitari.vue":
/*!*************************************************************!*\
  !*** ./resources/js/src/views/app_pages/spv/Solicitari.vue ***!
  \*************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _Solicitari_vue_vue_type_template_id_1f7e2a2c___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./Solicitari.vue?vue&type=template&id=1f7e2a2c& */ "./resources/js/src/views/app_pages/spv/Solicitari.vue?vue&type=template&id=1f7e2a2c&");
/* harmony import */ var _Solicitari_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./Solicitari.vue?vue&type=script&lang=js& */ "./resources/js/src/views/app_pages/spv/Solicitari.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport *//* harmony import */ var _Solicitari_vue_vue_type_style_index_0_lang_scss___WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./Solicitari.vue?vue&type=style&index=0&lang=scss& */ "./resources/js/src/views/app_pages/spv/Solicitari.vue?vue&type=style&index=0&lang=scss&");
/* harmony import */ var _node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../../../../../../node_modules/vue-loader/lib/runtime/componentNormalizer.js */ "./node_modules/vue-loader/lib/runtime/componentNormalizer.js");






/* normalize component */

var component = Object(_node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_3__["default"])(
  _Solicitari_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__["default"],
  _Solicitari_vue_vue_type_template_id_1f7e2a2c___WEBPACK_IMPORTED_MODULE_0__["render"],
  _Solicitari_vue_vue_type_template_id_1f7e2a2c___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"],
  false,
  null,
  null,
  null
  
)

/* hot reload */
if (false) { var api; }
component.options.__file = "resources/js/src/views/app_pages/spv/Solicitari.vue"
/* harmony default export */ __webpack_exports__["default"] = (component.exports);

/***/ }),

/***/ "./resources/js/src/views/app_pages/spv/Solicitari.vue?vue&type=script&lang=js&":
/*!**************************************************************************************!*\
  !*** ./resources/js/src/views/app_pages/spv/Solicitari.vue?vue&type=script&lang=js& ***!
  \**************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_Solicitari_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../../node_modules/babel-loader/lib??ref--4-0!../../../../../../node_modules/vue-loader/lib??vue-loader-options!./Solicitari.vue?vue&type=script&lang=js& */ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/app_pages/spv/Solicitari.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__["default"] = (_node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_Solicitari_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__["default"]); 

/***/ }),

/***/ "./resources/js/src/views/app_pages/spv/Solicitari.vue?vue&type=style&index=0&lang=scss&":
/*!***********************************************************************************************!*\
  !*** ./resources/js/src/views/app_pages/spv/Solicitari.vue?vue&type=style&index=0&lang=scss& ***!
  \***********************************************************************************************/
/*! no static exports found */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_style_loader_index_js_node_modules_css_loader_dist_cjs_js_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_7_2_node_modules_sass_loader_dist_cjs_js_ref_7_3_node_modules_sass_loader_dist_cjs_js_ref_11_0_node_modules_vue_loader_lib_index_js_vue_loader_options_Solicitari_vue_vue_type_style_index_0_lang_scss___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../../node_modules/style-loader!../../../../../../node_modules/css-loader/dist/cjs.js!../../../../../../node_modules/vue-loader/lib/loaders/stylePostLoader.js!../../../../../../node_modules/postcss-loader/src??ref--7-2!../../../../../../node_modules/sass-loader/dist/cjs.js??ref--7-3!../../../../../../node_modules/sass-loader/dist/cjs.js??ref--11-0!../../../../../../node_modules/vue-loader/lib??vue-loader-options!./Solicitari.vue?vue&type=style&index=0&lang=scss& */ "./node_modules/style-loader/index.js!./node_modules/css-loader/dist/cjs.js!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src/index.js?!./node_modules/sass-loader/dist/cjs.js?!./node_modules/sass-loader/dist/cjs.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/app_pages/spv/Solicitari.vue?vue&type=style&index=0&lang=scss&");
/* harmony import */ var _node_modules_style_loader_index_js_node_modules_css_loader_dist_cjs_js_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_7_2_node_modules_sass_loader_dist_cjs_js_ref_7_3_node_modules_sass_loader_dist_cjs_js_ref_11_0_node_modules_vue_loader_lib_index_js_vue_loader_options_Solicitari_vue_vue_type_style_index_0_lang_scss___WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_node_modules_style_loader_index_js_node_modules_css_loader_dist_cjs_js_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_7_2_node_modules_sass_loader_dist_cjs_js_ref_7_3_node_modules_sass_loader_dist_cjs_js_ref_11_0_node_modules_vue_loader_lib_index_js_vue_loader_options_Solicitari_vue_vue_type_style_index_0_lang_scss___WEBPACK_IMPORTED_MODULE_0__);
/* harmony reexport (unknown) */ for(var __WEBPACK_IMPORT_KEY__ in _node_modules_style_loader_index_js_node_modules_css_loader_dist_cjs_js_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_7_2_node_modules_sass_loader_dist_cjs_js_ref_7_3_node_modules_sass_loader_dist_cjs_js_ref_11_0_node_modules_vue_loader_lib_index_js_vue_loader_options_Solicitari_vue_vue_type_style_index_0_lang_scss___WEBPACK_IMPORTED_MODULE_0__) if(["default"].indexOf(__WEBPACK_IMPORT_KEY__) < 0) (function(key) { __webpack_require__.d(__webpack_exports__, key, function() { return _node_modules_style_loader_index_js_node_modules_css_loader_dist_cjs_js_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_7_2_node_modules_sass_loader_dist_cjs_js_ref_7_3_node_modules_sass_loader_dist_cjs_js_ref_11_0_node_modules_vue_loader_lib_index_js_vue_loader_options_Solicitari_vue_vue_type_style_index_0_lang_scss___WEBPACK_IMPORTED_MODULE_0__[key]; }) }(__WEBPACK_IMPORT_KEY__));


/***/ }),

/***/ "./resources/js/src/views/app_pages/spv/Solicitari.vue?vue&type=template&id=1f7e2a2c&":
/*!********************************************************************************************!*\
  !*** ./resources/js/src/views/app_pages/spv/Solicitari.vue?vue&type=template&id=1f7e2a2c& ***!
  \********************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_Solicitari_vue_vue_type_template_id_1f7e2a2c___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../../node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!../../../../../../node_modules/vue-loader/lib??vue-loader-options!./Solicitari.vue?vue&type=template&id=1f7e2a2c& */ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/app_pages/spv/Solicitari.vue?vue&type=template&id=1f7e2a2c&");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "render", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_Solicitari_vue_vue_type_template_id_1f7e2a2c___WEBPACK_IMPORTED_MODULE_0__["render"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "staticRenderFns", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_Solicitari_vue_vue_type_template_id_1f7e2a2c___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"]; });



/***/ }),

/***/ "./resources/js/src/views/app_pages/spv/Utilizatori.vue":
/*!**************************************************************!*\
  !*** ./resources/js/src/views/app_pages/spv/Utilizatori.vue ***!
  \**************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _Utilizatori_vue_vue_type_template_id_17852987_scoped_true___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./Utilizatori.vue?vue&type=template&id=17852987&scoped=true& */ "./resources/js/src/views/app_pages/spv/Utilizatori.vue?vue&type=template&id=17852987&scoped=true&");
/* harmony import */ var _Utilizatori_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./Utilizatori.vue?vue&type=script&lang=js& */ "./resources/js/src/views/app_pages/spv/Utilizatori.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport *//* harmony import */ var _Utilizatori_vue_vue_type_style_index_0_id_17852987_scoped_true_lang_css___WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./Utilizatori.vue?vue&type=style&index=0&id=17852987&scoped=true&lang=css& */ "./resources/js/src/views/app_pages/spv/Utilizatori.vue?vue&type=style&index=0&id=17852987&scoped=true&lang=css&");
/* harmony import */ var _node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../../../../../../node_modules/vue-loader/lib/runtime/componentNormalizer.js */ "./node_modules/vue-loader/lib/runtime/componentNormalizer.js");






/* normalize component */

var component = Object(_node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_3__["default"])(
  _Utilizatori_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__["default"],
  _Utilizatori_vue_vue_type_template_id_17852987_scoped_true___WEBPACK_IMPORTED_MODULE_0__["render"],
  _Utilizatori_vue_vue_type_template_id_17852987_scoped_true___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"],
  false,
  null,
  "17852987",
  null
  
)

/* hot reload */
if (false) { var api; }
component.options.__file = "resources/js/src/views/app_pages/spv/Utilizatori.vue"
/* harmony default export */ __webpack_exports__["default"] = (component.exports);

/***/ }),

/***/ "./resources/js/src/views/app_pages/spv/Utilizatori.vue?vue&type=script&lang=js&":
/*!***************************************************************************************!*\
  !*** ./resources/js/src/views/app_pages/spv/Utilizatori.vue?vue&type=script&lang=js& ***!
  \***************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_Utilizatori_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../../node_modules/babel-loader/lib??ref--4-0!../../../../../../node_modules/vue-loader/lib??vue-loader-options!./Utilizatori.vue?vue&type=script&lang=js& */ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/app_pages/spv/Utilizatori.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__["default"] = (_node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_Utilizatori_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__["default"]); 

/***/ }),

/***/ "./resources/js/src/views/app_pages/spv/Utilizatori.vue?vue&type=style&index=0&id=17852987&scoped=true&lang=css&":
/*!***********************************************************************************************************************!*\
  !*** ./resources/js/src/views/app_pages/spv/Utilizatori.vue?vue&type=style&index=0&id=17852987&scoped=true&lang=css& ***!
  \***********************************************************************************************************************/
/*! no static exports found */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_style_loader_index_js_node_modules_css_loader_dist_cjs_js_ref_6_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_6_2_node_modules_vue_loader_lib_index_js_vue_loader_options_Utilizatori_vue_vue_type_style_index_0_id_17852987_scoped_true_lang_css___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../../node_modules/style-loader!../../../../../../node_modules/css-loader/dist/cjs.js??ref--6-1!../../../../../../node_modules/vue-loader/lib/loaders/stylePostLoader.js!../../../../../../node_modules/postcss-loader/src??ref--6-2!../../../../../../node_modules/vue-loader/lib??vue-loader-options!./Utilizatori.vue?vue&type=style&index=0&id=17852987&scoped=true&lang=css& */ "./node_modules/style-loader/index.js!./node_modules/css-loader/dist/cjs.js?!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/app_pages/spv/Utilizatori.vue?vue&type=style&index=0&id=17852987&scoped=true&lang=css&");
/* harmony import */ var _node_modules_style_loader_index_js_node_modules_css_loader_dist_cjs_js_ref_6_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_6_2_node_modules_vue_loader_lib_index_js_vue_loader_options_Utilizatori_vue_vue_type_style_index_0_id_17852987_scoped_true_lang_css___WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_node_modules_style_loader_index_js_node_modules_css_loader_dist_cjs_js_ref_6_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_6_2_node_modules_vue_loader_lib_index_js_vue_loader_options_Utilizatori_vue_vue_type_style_index_0_id_17852987_scoped_true_lang_css___WEBPACK_IMPORTED_MODULE_0__);
/* harmony reexport (unknown) */ for(var __WEBPACK_IMPORT_KEY__ in _node_modules_style_loader_index_js_node_modules_css_loader_dist_cjs_js_ref_6_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_6_2_node_modules_vue_loader_lib_index_js_vue_loader_options_Utilizatori_vue_vue_type_style_index_0_id_17852987_scoped_true_lang_css___WEBPACK_IMPORTED_MODULE_0__) if(["default"].indexOf(__WEBPACK_IMPORT_KEY__) < 0) (function(key) { __webpack_require__.d(__webpack_exports__, key, function() { return _node_modules_style_loader_index_js_node_modules_css_loader_dist_cjs_js_ref_6_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_6_2_node_modules_vue_loader_lib_index_js_vue_loader_options_Utilizatori_vue_vue_type_style_index_0_id_17852987_scoped_true_lang_css___WEBPACK_IMPORTED_MODULE_0__[key]; }) }(__WEBPACK_IMPORT_KEY__));


/***/ }),

/***/ "./resources/js/src/views/app_pages/spv/Utilizatori.vue?vue&type=template&id=17852987&scoped=true&":
/*!*********************************************************************************************************!*\
  !*** ./resources/js/src/views/app_pages/spv/Utilizatori.vue?vue&type=template&id=17852987&scoped=true& ***!
  \*********************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_Utilizatori_vue_vue_type_template_id_17852987_scoped_true___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../../node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!../../../../../../node_modules/vue-loader/lib??vue-loader-options!./Utilizatori.vue?vue&type=template&id=17852987&scoped=true& */ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/app_pages/spv/Utilizatori.vue?vue&type=template&id=17852987&scoped=true&");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "render", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_Utilizatori_vue_vue_type_template_id_17852987_scoped_true___WEBPACK_IMPORTED_MODULE_0__["render"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "staticRenderFns", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_Utilizatori_vue_vue_type_template_id_17852987_scoped_true___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"]; });



/***/ })

}]);