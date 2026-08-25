(window["webpackJsonp"] = window["webpackJsonp"] || []).push([[148],{

/***/ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/app_pages/Notificationlog.vue?vue&type=script&lang=js&":
/*!***********************************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib??ref--4-0!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/src/views/app_pages/Notificationlog.vue?vue&type=script&lang=js& ***!
  \***********************************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _Notificationlogaev_vue__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./Notificationlogaev.vue */ "./resources/js/src/views/app_pages/Notificationlogaev.vue");
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
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
    Notificationlogaev: _Notificationlogaev_vue__WEBPACK_IMPORTED_MODULE_0__["default"]
  },
  name: "notificationlog",
  data: function data() {
    return {
      refreshLocal: false,
      idselectat: null,
      campFiltruStart: "",
      modelName: "notificationlog",
      modelDisplayName: "Jurnal notificari",
      editVar: {
        notificationtype_id: "",
        from_id: "",
        user_id: "",
        channel: "",
        email: "",
        telefon: "",
        title: "",
        subtitle: "",
        type: "",
        icon: "",
        avatar: "",
        link: "",
        category: "",
        read_at: ""
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
        label: "Tip notificare",
        field: "notificationtype.denumire",
        width: "200px",
        type: "text",
        showSortAsc: true
      }, {
        label: "De la",
        field: "from.name",
        width: "200px",
        type: "text",
        showSortAsc: true
      }, {
        label: "Catre",
        field: "user.name",
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
        field: "subtitle",
        width: "300px",
        type: "text",
        showSortAsc: true
      }, {
        label: "Canal de comunicare",
        field: "channel",
        width: "200px",
        type: "text",
        showSortAsc: true
      }, {
        label: "Citit la",
        field: "read_at",
        width: "300px",
        type: "date",
        dateInputFormat: "yyyy-MM-dd'T'HH:mm:ss.SSSSSS'Z'",
        // expects 2018-03-16
        dateOutputFormat: "dd.MM.yyyy HH:mm:ss",
        // outputs Mar 16th 2018
        //dateInputFormat: "yyyy-MM-dd", // expects 2018-03-16
        //dateOutputFormat: "dd.MM.yyyy", // outputs Mar 16th 2018 
        showSortAsc: true
      }, {
        label: "Link",
        field: "link",
        width: "300px",
        type: "text",
        showSortAsc: true
      }, {
        label: "Email",
        field: "email",
        width: "300px",
        type: "text",
        showSortAsc: true
      }, {
        label: "Telefon",
        field: "telefon",
        width: "200px",
        type: "text",
        showSortAsc: true
      }, {
        label: "Tip",
        field: "type",
        width: "300px",
        type: "text",
        showSortAsc: true
      }, {
        label: "Icon",
        field: "icon",
        width: "300px",
        type: "text",
        showSortAsc: true
      }, {
        label: "Avatar",
        field: "avatar",
        width: "300px",
        type: "text",
        showSortAsc: true
      }, {
        label: "Categorie notificari",
        field: "category",
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
        notificationtype_id: "",
        from_id: "",
        user_id: "",
        channel: "",
        email: "",
        telefon: "",
        title: "",
        subtitle: "",
        type: "",
        icon: "",
        avatar: "",
        link: "",
        category: "",
        read_at: ""
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
        notificationtype_id: "",
        from_id: "",
        user_id: "",
        channel: "",
        email: "",
        telefon: "",
        title: "",
        subtitle: "",
        type: "",
        icon: "",
        avatar: "",
        link: "",
        category: "",
        read_at: ""
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

/***/ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/app_pages/Notificationlogaev.vue?vue&type=script&lang=js&":
/*!**************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib??ref--4-0!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/src/views/app_pages/Notificationlogaev.vue?vue&type=script&lang=js& ***!
  \**************************************************************************************************************************************************************************************/
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
  name: "notificationlogaev",
  data: function data() {
    return {
      rutainapoiLocal: this.rutainapoi,
      activeEditLocal: false,
      activeActionLocal: this.activeAction,
      editVarLocal: this.editVar,
      nextTodoId: 2,
      modelName: "notificationlog",
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
    modificaNotificationtype: function modificaNotificationtype() {
      if (this.editVarLocal.notificationtype) {
        this.editVarLocal.notificationtype_id = this.editVarLocal.notificationtype.id;
      } else {
        this.editVarLocal.notificationtype_id = null;
      }
    },
    modificaUser: function modificaUser() {
      if (this.editVarLocal.user) {
        this.editVarLocal.user_id = this.editVarLocal.user.id;
      } else {
        this.editVarLocal.user_id = null;
      }
    },
    modificaFrom: function modificaFrom() {
      if (this.editVarLocal.from) {
        this.editVarLocal.from_id = this.editVarLocal.from.id;
      } else {
        this.editVarLocal.from_id = null;
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
        // this.row=response  
        _this2.idselectat = response.id.toString();

        _this2.$emit("stored", response);

        _this2.editVarLocal = {
          notificationtype_id: '',
          from_id: '',
          user_id: '',
          channel: '',
          email: '',
          telefon: '',
          title: '',
          subtitle: '',
          type: '',
          icon: '',
          avatar: '',
          link: '',
          category: '',
          read_at: ''
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
        notificationtype_id: "",
        from_id: "",
        user_id: "",
        channel: "",
        email: "",
        telefon: "",
        title: "",
        subtitle: "",
        type: "",
        icon: "",
        avatar: "",
        link: "",
        category: "",
        read_at: ""
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
          notificationtype_id: "",
          from_id: "",
          user_id: "",
          channel: "",
          email: "",
          telefon: "",
          title: "",
          subtitle: "",
          type: "",
          icon: "",
          avatar: "",
          link: "",
          category: "",
          read_at: ""
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

/***/ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/app_pages/Notificationlog.vue?vue&type=template&id=c042d254&":
/*!***************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/src/views/app_pages/Notificationlog.vue?vue&type=template&id=c042d254& ***!
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
      _c("Notificationlogaev", {
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

/***/ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/app_pages/Notificationlogaev.vue?vue&type=template&id=c6758528&":
/*!******************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/src/views/app_pages/Notificationlogaev.vue?vue&type=template&id=c6758528& ***!
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
            "modal-class": "modal-success",
            title: _vm.activeActionLocal + " Jurnal notificari",
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
                  _c("dropdowncuruta", {
                    attrs: {
                      name: "tip_notificare",
                      readonly: _vm.activeAction == "Vizualizează",
                      campDisplay: "Tip notificare",
                      ruta: "notificationtype",
                      limitToList: "true",
                      colCaut: "denumire",
                    },
                    on: { change: _vm.modificaNotificationtype },
                    model: {
                      value: _vm.editVarLocal.notificationtype,
                      callback: function ($$v) {
                        _vm.$set(_vm.editVarLocal, "notificationtype", $$v)
                      },
                      expression: "editVarLocal.notificationtype",
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
                      labelDisplay: "De la",
                      readonly: _vm.activeActionLocal == "Vizualizează",
                    },
                    on: { change: _vm.modificaFrom },
                    model: {
                      value: _vm.editVarLocal.from,
                      callback: function ($$v) {
                        _vm.$set(_vm.editVarLocal, "from", $$v)
                      },
                      expression: "editVarLocal.from",
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
                      labelDisplay: "Catre",
                      readonly: _vm.activeActionLocal == "Vizualizează",
                    },
                    on: { change: _vm.modificaUser },
                    model: {
                      value: _vm.editVarLocal.user,
                      callback: function ($$v) {
                        _vm.$set(_vm.editVarLocal, "user", $$v)
                      },
                      expression: "editVarLocal.user",
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
                  _c("dropdowncuoptiuni", {
                    attrs: {
                      name: "channel",
                      readonly: _vm.activeAction == "Vizualizează",
                      campDisplay: "Canal de comunicare",
                      field_name: "Canal de comunicare notificari",
                      limitToList: "true",
                    },
                    model: {
                      value: _vm.editVarLocal.channel,
                      callback: function ($$v) {
                        _vm.$set(_vm.editVarLocal, "channel", $$v)
                      },
                      expression: "editVarLocal.channel",
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
                        id: "email",
                        placeholder: "Email",
                      },
                      model: {
                        value: _vm.editVarLocal.email,
                        callback: function ($$v) {
                          _vm.$set(_vm.editVarLocal, "email", $$v)
                        },
                        expression: "editVarLocal.email",
                      },
                    }),
                    _vm._v(" "),
                    _c("label", { attrs: { for: "email" } }, [_vm._v("Email")]),
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
                    _c("b-form-input", {
                      attrs: {
                        readonly: _vm.activeActionLocal == "Vizualizează",
                        id: "telefon",
                        placeholder: "Telefon",
                      },
                      model: {
                        value: _vm.editVarLocal.telefon,
                        callback: function ($$v) {
                          _vm.$set(_vm.editVarLocal, "telefon", $$v)
                        },
                        expression: "editVarLocal.telefon",
                      },
                    }),
                    _vm._v(" "),
                    _c("label", { attrs: { for: "telefon" } }, [
                      _vm._v("Telefon"),
                    ]),
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
                    _c("b-form-input", {
                      attrs: {
                        readonly: _vm.activeActionLocal == "Vizualizează",
                        id: "subtitle",
                        placeholder: "Descriere",
                      },
                      model: {
                        value: _vm.editVarLocal.subtitle,
                        callback: function ($$v) {
                          _vm.$set(_vm.editVarLocal, "subtitle", $$v)
                        },
                        expression: "editVarLocal.subtitle",
                      },
                    }),
                    _vm._v(" "),
                    _c("label", { attrs: { for: "subtitle" } }, [
                      _vm._v("Descriere"),
                    ]),
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
                    _c("b-form-input", {
                      attrs: {
                        readonly: _vm.activeActionLocal == "Vizualizează",
                        id: "type",
                        placeholder: "Tip",
                      },
                      model: {
                        value: _vm.editVarLocal.type,
                        callback: function ($$v) {
                          _vm.$set(_vm.editVarLocal, "type", $$v)
                        },
                        expression: "editVarLocal.type",
                      },
                    }),
                    _vm._v(" "),
                    _c("label", { attrs: { for: "type" } }, [_vm._v("Tip")]),
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
              _vm._v(" "),
              _c("b-col", { attrs: { cols: "2" } }, [
                _c(
                  "div",
                  { staticClass: "form-label-group" },
                  [
                    _c("b-form-input", {
                      attrs: {
                        readonly: _vm.activeActionLocal == "Vizualizează",
                        id: "avatar",
                        placeholder: "Avatar",
                      },
                      model: {
                        value: _vm.editVarLocal.avatar,
                        callback: function ($$v) {
                          _vm.$set(_vm.editVarLocal, "avatar", $$v)
                        },
                        expression: "editVarLocal.avatar",
                      },
                    }),
                    _vm._v(" "),
                    _c("label", { attrs: { for: "avatar" } }, [
                      _vm._v("Avatar"),
                    ]),
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
                    _c("b-form-input", {
                      attrs: {
                        readonly: _vm.activeActionLocal == "Vizualizează",
                        id: "link",
                        placeholder: "Link",
                      },
                      model: {
                        value: _vm.editVarLocal.link,
                        callback: function ($$v) {
                          _vm.$set(_vm.editVarLocal, "link", $$v)
                        },
                        expression: "editVarLocal.link",
                      },
                    }),
                    _vm._v(" "),
                    _c("label", { attrs: { for: "link" } }, [_vm._v("Link")]),
                  ],
                  1
                ),
              ]),
              _vm._v(" "),
              _c(
                "b-col",
                { attrs: { cols: "2" } },
                [
                  _c("dropdowncuoptiuni", {
                    attrs: {
                      name: "category",
                      readonly: _vm.activeAction == "Vizualizează",
                      campDisplay: "Categorie notificare",
                      field_name: "Categorie notificare",
                      limitToList: "true",
                    },
                    model: {
                      value: _vm.editVarLocal.category,
                      callback: function ($$v) {
                        _vm.$set(_vm.editVarLocal, "category", $$v)
                      },
                      expression: "editVarLocal.category",
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
                        id: "read_at",
                        placeholder: "Citit la",
                      },
                      model: {
                        value: _vm.editVarLocal.read_at,
                        callback: function ($$v) {
                          _vm.$set(_vm.editVarLocal, "read_at", $$v)
                        },
                        expression: "editVarLocal.read_at",
                      },
                    }),
                    _vm._v(" "),
                    _c("label", { attrs: { for: "read_at" } }, [
                      _vm._v("Citit la"),
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

/***/ "./resources/js/src/views/app_pages/Notificationlog.vue":
/*!**************************************************************!*\
  !*** ./resources/js/src/views/app_pages/Notificationlog.vue ***!
  \**************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _Notificationlog_vue_vue_type_template_id_c042d254___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./Notificationlog.vue?vue&type=template&id=c042d254& */ "./resources/js/src/views/app_pages/Notificationlog.vue?vue&type=template&id=c042d254&");
/* harmony import */ var _Notificationlog_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./Notificationlog.vue?vue&type=script&lang=js& */ "./resources/js/src/views/app_pages/Notificationlog.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport *//* harmony import */ var _node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../../../../node_modules/vue-loader/lib/runtime/componentNormalizer.js */ "./node_modules/vue-loader/lib/runtime/componentNormalizer.js");





/* normalize component */

var component = Object(_node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__["default"])(
  _Notificationlog_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__["default"],
  _Notificationlog_vue_vue_type_template_id_c042d254___WEBPACK_IMPORTED_MODULE_0__["render"],
  _Notificationlog_vue_vue_type_template_id_c042d254___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"],
  false,
  null,
  null,
  null
  
)

/* hot reload */
if (false) { var api; }
component.options.__file = "resources/js/src/views/app_pages/Notificationlog.vue"
/* harmony default export */ __webpack_exports__["default"] = (component.exports);

/***/ }),

/***/ "./resources/js/src/views/app_pages/Notificationlog.vue?vue&type=script&lang=js&":
/*!***************************************************************************************!*\
  !*** ./resources/js/src/views/app_pages/Notificationlog.vue?vue&type=script&lang=js& ***!
  \***************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_Notificationlog_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../node_modules/babel-loader/lib??ref--4-0!../../../../../node_modules/vue-loader/lib??vue-loader-options!./Notificationlog.vue?vue&type=script&lang=js& */ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/app_pages/Notificationlog.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__["default"] = (_node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_Notificationlog_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__["default"]); 

/***/ }),

/***/ "./resources/js/src/views/app_pages/Notificationlog.vue?vue&type=template&id=c042d254&":
/*!*********************************************************************************************!*\
  !*** ./resources/js/src/views/app_pages/Notificationlog.vue?vue&type=template&id=c042d254& ***!
  \*********************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_Notificationlog_vue_vue_type_template_id_c042d254___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!../../../../../node_modules/vue-loader/lib??vue-loader-options!./Notificationlog.vue?vue&type=template&id=c042d254& */ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/app_pages/Notificationlog.vue?vue&type=template&id=c042d254&");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "render", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_Notificationlog_vue_vue_type_template_id_c042d254___WEBPACK_IMPORTED_MODULE_0__["render"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "staticRenderFns", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_Notificationlog_vue_vue_type_template_id_c042d254___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"]; });



/***/ }),

/***/ "./resources/js/src/views/app_pages/Notificationlogaev.vue":
/*!*****************************************************************!*\
  !*** ./resources/js/src/views/app_pages/Notificationlogaev.vue ***!
  \*****************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _Notificationlogaev_vue_vue_type_template_id_c6758528___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./Notificationlogaev.vue?vue&type=template&id=c6758528& */ "./resources/js/src/views/app_pages/Notificationlogaev.vue?vue&type=template&id=c6758528&");
/* harmony import */ var _Notificationlogaev_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./Notificationlogaev.vue?vue&type=script&lang=js& */ "./resources/js/src/views/app_pages/Notificationlogaev.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport *//* harmony import */ var _node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../../../../node_modules/vue-loader/lib/runtime/componentNormalizer.js */ "./node_modules/vue-loader/lib/runtime/componentNormalizer.js");





/* normalize component */

var component = Object(_node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__["default"])(
  _Notificationlogaev_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__["default"],
  _Notificationlogaev_vue_vue_type_template_id_c6758528___WEBPACK_IMPORTED_MODULE_0__["render"],
  _Notificationlogaev_vue_vue_type_template_id_c6758528___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"],
  false,
  null,
  null,
  null
  
)

/* hot reload */
if (false) { var api; }
component.options.__file = "resources/js/src/views/app_pages/Notificationlogaev.vue"
/* harmony default export */ __webpack_exports__["default"] = (component.exports);

/***/ }),

/***/ "./resources/js/src/views/app_pages/Notificationlogaev.vue?vue&type=script&lang=js&":
/*!******************************************************************************************!*\
  !*** ./resources/js/src/views/app_pages/Notificationlogaev.vue?vue&type=script&lang=js& ***!
  \******************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_Notificationlogaev_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../node_modules/babel-loader/lib??ref--4-0!../../../../../node_modules/vue-loader/lib??vue-loader-options!./Notificationlogaev.vue?vue&type=script&lang=js& */ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/app_pages/Notificationlogaev.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__["default"] = (_node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_Notificationlogaev_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__["default"]); 

/***/ }),

/***/ "./resources/js/src/views/app_pages/Notificationlogaev.vue?vue&type=template&id=c6758528&":
/*!************************************************************************************************!*\
  !*** ./resources/js/src/views/app_pages/Notificationlogaev.vue?vue&type=template&id=c6758528& ***!
  \************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_Notificationlogaev_vue_vue_type_template_id_c6758528___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!../../../../../node_modules/vue-loader/lib??vue-loader-options!./Notificationlogaev.vue?vue&type=template&id=c6758528& */ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/app_pages/Notificationlogaev.vue?vue&type=template&id=c6758528&");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "render", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_Notificationlogaev_vue_vue_type_template_id_c6758528___WEBPACK_IMPORTED_MODULE_0__["render"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "staticRenderFns", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_Notificationlogaev_vue_vue_type_template_id_c6758528___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"]; });



/***/ })

}]);