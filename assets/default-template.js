/*
 * ATTENTION: The "eval" devtool has been used (maybe by default in mode: "development").
 * This devtool is neither made for production nor for readable output files.
 * It uses "eval()" calls to create a separate source file in the browser devtools.
 * If you are trying to read the output file, select a different devtool (https://webpack.js.org/configuration/devtool/)
 * or disable the default devtool with "devtool: false".
 * If you are looking for production-ready output files, see mode: "production" (https://webpack.js.org/configuration/mode/).
 */
/******/ (() => { // webpackBootstrap
/******/ 	var __webpack_modules__ = ({

/***/ "./assets/js/default-template.js":
/*!***************************************!*\
  !*** ./assets/js/default-template.js ***!
  \***************************************/
/***/ (() => {

eval("let page = 1;\n\nasync function getJSON( url, callback ) {\n\tvar xhr = new XMLHttpRequest();\n\n\tif ( is_subscribed && -1 === url.indexOf( 'user_is_subscribed=true' ) ) {\n\t\turl += '&user_is_subscribed=true';\n\t}\n\n\txhr.open( 'GET', url, true );\n\n\tif ( is_subscribed ) {\n\t\txhr.setRequestHeader( 'x-ml-is-user-subscribed', 'true' );\n\t}\n\n\txhr.responseType = 'text';\n\txhr.onload       = function() {\n\t\tvar status = xhr.status;\n\t\tif ( status == 200 ) {\n\t\t\tcallback( null, xhr.response );\n\t\t} else {\n\t\t\tcallback( status );\n\t\t}\n\t};\n\n\txhr.send();\n\treturn 1;\n};\n\nvar nextOffset = pppage;\nvar shouldLoadMore = true;\n\nasync function getNewlyPublishedArticles( url, offset, params ) {\n\treturn new Promise(\n\t\tfunction ( resolve, reject ) {\n\t\t\tgetJSON(\n\t\t\t\turl + '/ml-api/v2/posts' + ( params || '?' ) + '&dt=true&offset=' + offset, // without \"?\" after \"v2/posts\".\n\t\t\t\tfunction( x, response ) {\n\t\t\t\t\tif ( 0 === response.length ) {\n\t\t\t\t\t\tshouldLoadMore = false;\n\t\t\t\t\t}\n\t\t\t\t\tdocument.querySelector( '.page__content > .dt-list' ).insertAdjacentHTML( 'beforeend', response );\n\t\t\t\t\tnextOffset += pppage;\n\t\t\t\t\tresolve();\n\t\t\t\t}\n\t\t\t)\n\t\t}\n\t);\n}\n\ndocument.querySelector('ons-page').onInfiniteScroll = function( done ) {\n\tif ( ! shouldLoadMore ) {\n\t\treturn;\n\t}\n\n\tgetNewlyPublishedArticles( ml_site_url, nextOffset, window.location.search ).then( function() {\n\t\tdone();\n\t} );\n}\n\n//# sourceURL=webpack://mobiloud-mobile-app-plugin/./assets/js/default-template.js?");

/***/ }),

/***/ "./assets/scss/index.scss":
/*!********************************!*\
  !*** ./assets/scss/index.scss ***!
  \********************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n// extracted by mini-css-extract-plugin\n\n\n//# sourceURL=webpack://mobiloud-mobile-app-plugin/./assets/scss/index.scss?");

/***/ })

/******/ 	});
/************************************************************************/
/******/ 	// The require scope
/******/ 	var __webpack_require__ = {};
/******/ 	
/************************************************************************/
/******/ 	/* webpack/runtime/make namespace object */
/******/ 	(() => {
/******/ 		// define __esModule on exports
/******/ 		__webpack_require__.r = (exports) => {
/******/ 			if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 				Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 			}
/******/ 			Object.defineProperty(exports, '__esModule', { value: true });
/******/ 		};
/******/ 	})();
/******/ 	
/************************************************************************/
/******/ 	
/******/ 	// startup
/******/ 	// Load entry module and return exports
/******/ 	// This entry module can't be inlined because the eval devtool is used.
/******/ 	__webpack_modules__["./assets/js/default-template.js"](0, {}, __webpack_require__);
/******/ 	var __webpack_exports__ = {};
/******/ 	__webpack_modules__["./assets/scss/index.scss"](0, __webpack_exports__, __webpack_require__);
/******/ 	
/******/ })()
;