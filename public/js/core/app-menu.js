/******/ (function(modules) { // webpackBootstrap
/******/ 	// The module cache
/******/ 	var installedModules = {};
/******/
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/
/******/ 		// Check if module is in cache
/******/ 		if(installedModules[moduleId]) {
/******/ 			return installedModules[moduleId].exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = installedModules[moduleId] = {
/******/ 			i: moduleId,
/******/ 			l: false,
/******/ 			exports: {}
/******/ 		};
/******/
/******/ 		// Execute the module function
/******/ 		modules[moduleId].call(module.exports, module, module.exports, __webpack_require__);
/******/
/******/ 		// Flag the module as loaded
/******/ 		module.l = true;
/******/
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/
/******/
/******/ 	// expose the modules object (__webpack_modules__)
/******/ 	__webpack_require__.m = modules;
/******/
/******/ 	// expose the module cache
/******/ 	__webpack_require__.c = installedModules;
/******/
/******/ 	// define getter function for harmony exports
/******/ 	__webpack_require__.d = function(exports, name, getter) {
/******/ 		if(!__webpack_require__.o(exports, name)) {
/******/ 			Object.defineProperty(exports, name, { enumerable: true, get: getter });
/******/ 		}
/******/ 	};
/******/
/******/ 	// define __esModule on exports
/******/ 	__webpack_require__.r = function(exports) {
/******/ 		if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 			Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 		}
/******/ 		Object.defineProperty(exports, '__esModule', { value: true });
/******/ 	};
/******/
/******/ 	// create a fake namespace object
/******/ 	// mode & 1: value is a module id, require it
/******/ 	// mode & 2: merge all properties of value into the ns
/******/ 	// mode & 4: return value when already ns object
/******/ 	// mode & 8|1: behave like require
/******/ 	__webpack_require__.t = function(value, mode) {
/******/ 		if(mode & 1) value = __webpack_require__(value);
/******/ 		if(mode & 8) return value;
/******/ 		if((mode & 4) && typeof value === 'object' && value && value.__esModule) return value;
/******/ 		var ns = Object.create(null);
/******/ 		__webpack_require__.r(ns);
/******/ 		Object.defineProperty(ns, 'default', { enumerable: true, value: value });
/******/ 		if(mode & 2 && typeof value != 'string') for(var key in value) __webpack_require__.d(ns, key, function(key) { return value[key]; }.bind(null, key));
/******/ 		return ns;
/******/ 	};
/******/
/******/ 	// getDefaultExport function for compatibility with non-harmony modules
/******/ 	__webpack_require__.n = function(module) {
/******/ 		var getter = module && module.__esModule ?
/******/ 			function getDefault() { return module['default']; } :
/******/ 			function getModuleExports() { return module; };
/******/ 		__webpack_require__.d(getter, 'a', getter);
/******/ 		return getter;
/******/ 	};
/******/
/******/ 	// Object.prototype.hasOwnProperty.call
/******/ 	__webpack_require__.o = function(object, property) { return Object.prototype.hasOwnProperty.call(object, property); };
/******/
/******/ 	// __webpack_public_path__
/******/ 	__webpack_require__.p = "/";
/******/
/******/
/******/ 	// Load entry module and return exports
/******/ 	return __webpack_require__(__webpack_require__.s = 0);
/******/ })
/************************************************************************/
/******/ ({

/***/ "./resources/css/app.css":
/*!*******************************!*\
  !*** ./resources/css/app.css ***!
  \*******************************/
/*! no static exports found */
/***/ (function(module, exports) {

throw new Error("Module build failed (from ./node_modules/css-loader/index.js):\nModuleBuildError: Module build failed (from ./node_modules/postcss-loader/src/index.js):\nError: It looks like you're trying to use `tailwindcss` directly as a PostCSS plugin. The PostCSS plugin has moved to a separate package, so to continue using Tailwind CSS with PostCSS you'll need to install `@tailwindcss/postcss` and update your PostCSS configuration.\n    at We (C:\\laragon\\www\\DATN-DevFoods\\node_modules\\tailwindcss\\dist\\lib.js:35:2121)\n    at LazyResult.run (C:\\laragon\\www\\DATN-DevFoods\\node_modules\\postcss-loader\\node_modules\\postcss\\lib\\lazy-result.js:288:14)\n    at LazyResult.asyncTick (C:\\laragon\\www\\DATN-DevFoods\\node_modules\\postcss-loader\\node_modules\\postcss\\lib\\lazy-result.js:212:26)\n    at C:\\laragon\\www\\DATN-DevFoods\\node_modules\\postcss-loader\\node_modules\\postcss\\lib\\lazy-result.js:254:14\n    at new Promise (<anonymous>)\n    at LazyResult.async (C:\\laragon\\www\\DATN-DevFoods\\node_modules\\postcss-loader\\node_modules\\postcss\\lib\\lazy-result.js:250:23)\n    at LazyResult.then (C:\\laragon\\www\\DATN-DevFoods\\node_modules\\postcss-loader\\node_modules\\postcss\\lib\\lazy-result.js:131:17)\n    at C:\\laragon\\www\\DATN-DevFoods\\node_modules\\postcss-loader\\src\\index.js:142:8\n    at process.processTicksAndRejections (node:internal/process/task_queues:105:5)\n    at C:\\laragon\\www\\DATN-DevFoods\\node_modules\\webpack\\lib\\NormalModule.js:316:20\n    at C:\\laragon\\www\\DATN-DevFoods\\node_modules\\loader-runner\\lib\\LoaderRunner.js:367:11\n    at C:\\laragon\\www\\DATN-DevFoods\\node_modules\\loader-runner\\lib\\LoaderRunner.js:233:18\n    at context.callback (C:\\laragon\\www\\DATN-DevFoods\\node_modules\\loader-runner\\lib\\LoaderRunner.js:111:13)\n    at C:\\laragon\\www\\DATN-DevFoods\\node_modules\\postcss-loader\\src\\index.js:208:9\n    at process.processTicksAndRejections (node:internal/process/task_queues:105:5)");

/***/ }),

/***/ 0:
/*!**********************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** multi ./resources/js/core/app-menu.js ./resources/sass/bootstrap.scss ./resources/sass/bootstrap-extended.scss ./resources/sass/colors.scss ./resources/sass/components.scss ./resources/sass/custom-rtl.scss ./resources/sass/custom-laravel.scss ./resources/css/app.css ***!
  \**********************************************************************************************************************************************************************************************************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

!(function webpackMissingModule() { var e = new Error("Cannot find module 'C:\\laragon\\www\\DATN-DevFoods\\resources\\js\\core\\app-menu.js'"); e.code = 'MODULE_NOT_FOUND'; throw e; }());
!(function webpackMissingModule() { var e = new Error("Cannot find module 'C:\\laragon\\www\\DATN-DevFoods\\resources\\sass\\bootstrap.scss'"); e.code = 'MODULE_NOT_FOUND'; throw e; }());
!(function webpackMissingModule() { var e = new Error("Cannot find module 'C:\\laragon\\www\\DATN-DevFoods\\resources\\sass\\bootstrap-extended.scss'"); e.code = 'MODULE_NOT_FOUND'; throw e; }());
!(function webpackMissingModule() { var e = new Error("Cannot find module 'C:\\laragon\\www\\DATN-DevFoods\\resources\\sass\\colors.scss'"); e.code = 'MODULE_NOT_FOUND'; throw e; }());
!(function webpackMissingModule() { var e = new Error("Cannot find module 'C:\\laragon\\www\\DATN-DevFoods\\resources\\sass\\components.scss'"); e.code = 'MODULE_NOT_FOUND'; throw e; }());
!(function webpackMissingModule() { var e = new Error("Cannot find module 'C:\\laragon\\www\\DATN-DevFoods\\resources\\sass\\custom-rtl.scss'"); e.code = 'MODULE_NOT_FOUND'; throw e; }());
!(function webpackMissingModule() { var e = new Error("Cannot find module 'C:\\laragon\\www\\DATN-DevFoods\\resources\\sass\\custom-laravel.scss'"); e.code = 'MODULE_NOT_FOUND'; throw e; }());
module.exports = __webpack_require__(/*! C:\laragon\www\DATN-DevFoods\resources\css\app.css */"./resources/css/app.css");


/***/ })

/******/ });
