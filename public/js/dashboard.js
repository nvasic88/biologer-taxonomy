"use strict";
(self["webpackChunk"] = self["webpackChunk"] || []).push([["dashboard"],{

/***/ "./resources/js/components/DashboardNavbar.js":
/*!****************************************************!*\
  !*** ./resources/js/components/DashboardNavbar.js ***!
  \****************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
Object(function webpackMissingModule() { var e = new Error("Cannot find module '@/components/Sidebar'"); e.code = 'MODULE_NOT_FOUND'; throw e; }());

/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = ({
  name: 'nzDashboardNavbar',
  components: {
    NzSidebar: Object(function webpackMissingModule() { var e = new Error("Cannot find module '@/components/Sidebar'"); e.code = 'MODULE_NOT_FOUND'; throw e; }())
  },
  props: {
    hasUnread: Boolean
  },
  data: function data() {
    return {
      active: false,
      showSidebar: false,
      hasUnreadNotifications: this.hasUnread
    };
  },
  methods: {
    toggle: function toggle() {
      this.active = !this.active;
    },
    toggleSidebar: function toggleSidebar() {
      this.showSidebar = !this.showSidebar;
    }
  }
});

/***/ })

}]);