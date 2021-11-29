"use strict";
(self["webpackChunk"] = self["webpackChunk"] || []).push([["resources_js_components_forms_RegistrationForm_js"],{

/***/ "./resources/js/components/forms/RegistrationForm.js":
/*!***********************************************************!*\
  !*** ./resources/js/components/forms/RegistrationForm.js ***!
  \***********************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = ({
  name: 'nzRegistrationForm',
  props: {
    initPasswordError: String
  },
  data: function data() {
    return {
      password: '',
      passwordWasOnceInvalid: !!this.initPasswordError,
      passwordIsInvalid: !!this.initPasswordError,
      passwordError: this.initPasswordError
    };
  },
  computed: {
    shouldBeDisabled: function shouldBeDisabled() {
      return this.passwordIsInvalid;
    }
  },
  methods: {
    validatePassword: function validatePassword() {
      return this.password.length < 8;
    },
    checkPassword: function checkPassword() {
      this.passwordIsInvalid = this.validatePassword();

      if (this.passwordIsInvalid) {
        this.passwordWasOnceInvalid = true;
      }

      this.passwordError = this.passwordIsInvalid ? this.trans('validation.min.string', {
        attribute: this.trans('labels.register.password'),
        min: 8
      }) : '';
    },
    checkIfFixedPassword: function checkIfFixedPassword() {
      if (this.passwordWasOnceInvalid) {
        this.checkPassword();
      }
    }
  }
});

/***/ })

}]);