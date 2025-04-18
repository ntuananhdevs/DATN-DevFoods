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

/***/ "./resources/js/core/app-menu.js":
/*!***************************************!*\
  !*** ./resources/js/core/app-menu.js ***!
  \***************************************/
/*! no static exports found */
/***/ (function(module, exports) {

/*=========================================================================================
  File Name: app-menu.js
  Description: Menu navigation, custom scrollbar, hover scroll bar, multilevel menu
  initialization and manipulations
  ----------------------------------------------------------------------------------------
  Item Name:  Vusax - Vuejs, HTML & Laravel Admin Dashboard Template
  Author: Pixinvent
  Author URL: hhttp://www.themeforest.net/user/pixinvent
==========================================================================================*/
(function (window, document, $) {
  'use strict';

  var vh = window.innerHeight * 0.01;
  document.documentElement.style.setProperty('--vh', "".concat(vh, "px"));
  $.app = $.app || {};
  var $body = $('body');
  var $window = $(window);
  var menuWrapper_el = $('div[data-menu="menu-wrapper"]').html();
  var menuWrapperClasses = $('div[data-menu="menu-wrapper"]').attr('class');

  // Main menu
  $.app.menu = {
    expanded: null,
    collapsed: null,
    hidden: null,
    container: null,
    horizontalMenu: false,
    is_touch_device: function is_touch_device() {
      var prefixes = ' -webkit- -moz- -o- -ms- '.split(' ');
      var mq = function mq(query) {
        return window.matchMedia(query).matches;
      };
      if ('ontouchstart' in window || window.DocumentTouch && document instanceof DocumentTouch) {
        return true;
      }
      // include the 'heartz' as a way to have a non matching MQ to help terminate the join
      // https://git.io/vznFH
      var query = ['(', prefixes.join('touch-enabled),('), 'heartz', ')'].join('');
      return mq(query);
    },
    manualScroller: {
      obj: null,
      init: function init() {
        var scroll_theme = $('.main-menu').hasClass('menu-dark') ? 'light' : 'dark';
        if (!$.app.menu.is_touch_device()) {
          this.obj = new PerfectScrollbar(".main-menu-content", {
            suppressScrollX: true,
            wheelPropagation: false
          });
        } else {
          $(".main-menu").addClass("menu-native-scroll");
        }
      },
      update: function update() {
        // if (this.obj) {
        // Scroll to currently active menu on page load if data-scroll-to-active is true
        if ($('.main-menu').data('scroll-to-active') === true) {
          var activeEl, menu, activeElHeight;
          activeEl = document.querySelector('.main-menu-content li.active');
          if ($body.hasClass('menu-collapsed')) {
            if ($('.main-menu-content li.sidebar-group-active').length) {
              activeEl = document.querySelector('.main-menu-content li.sidebar-group-active');
            }
          } else {
            menu = document.querySelector('.main-menu-content');
            if (activeEl) {
              activeElHeight = activeEl.getBoundingClientRect().top + menu.scrollTop;
            }
            // If active element's top position is less than 2/3 (66%) of menu height than do not scroll
            if (activeElHeight > parseInt(menu.clientHeight * 2 / 3)) {
              var start = menu.scrollTop,
                change = activeElHeight - start - parseInt(menu.clientHeight / 2);
            }
          }
          setTimeout(function () {
            $.app.menu.container.stop().animate({
              scrollTop: change
            }, 300);
            $('.main-menu').data('scroll-to-active', 'false');
          }, 300);
        }
        // this.obj.update();
        // }
      },
      enable: function enable() {
        if (!$('.main-menu-content').hasClass('ps')) {
          this.init();
        }
      },
      disable: function disable() {
        if (this.obj) {
          this.obj.destroy();
        }
      },
      updateHeight: function updateHeight() {
        if (($body.data('menu') == 'vertical-menu' || $body.data('menu') == 'vertical-menu-modern' || $body.data('menu') == 'vertical-overlay-menu') && $('.main-menu').hasClass('menu-fixed')) {
          $('.main-menu-content').css('height', $(window).height() - $('.header-navbar').height() - $('.main-menu-header').outerHeight() - $('.main-menu-footer').outerHeight());
          this.update();
        }
      }
    },
    init: function init(compactMenu) {
      if ($('.main-menu-content').length > 0) {
        this.container = $('.main-menu-content');
        var menuObj = this;
        var defMenu = '';
        if (compactMenu === true) {
          defMenu = 'collapsed';
        }
        if ($body.data('menu') == 'vertical-menu-modern') {
          var menuToggle = '';
          if (menuToggle === "false") {
            this.change('collapsed');
          } else {
            this.change(defMenu);
          }
        } else {
          this.change(defMenu);
        }
      }
    },
    drillDownMenu: function drillDownMenu(screenSize) {
      if ($('.drilldown-menu').length) {
        if (screenSize == 'sm' || screenSize == 'xs') {
          if ($('#navbar-mobile').attr('aria-expanded') == 'true') {
            $('.drilldown-menu').slidingMenu({
              backLabel: true
            });
          }
        } else {
          $('.drilldown-menu').slidingMenu({
            backLabel: true
          });
        }
      }
    },
    change: function change(defMenu) {
      var currentBreakpoint = Unison.fetch.now(); // Current Breakpoint
      this.reset();
      var menuType = $body.data('menu');
      if (currentBreakpoint) {
        switch (currentBreakpoint.name) {
          case 'xl':
            if (menuType === 'vertical-overlay-menu') {
              this.hide();
            } else {
              if (defMenu === 'collapsed') this.collapse(defMenu);else this.expand();
            }
            break;
          case 'lg':
            if (menuType === 'vertical-overlay-menu' || menuType === 'vertical-menu-modern' || menuType === 'horizontal-menu') {
              this.hide();
            } else {
              this.collapse();
            }
            break;
          case 'md':
          case 'sm':
            this.hide();
            break;
          case 'xs':
            this.hide();
            break;
        }
      }

      // On the small and extra small screen make them overlay menu
      if (menuType === 'vertical-menu' || menuType === 'vertical-menu-modern') {
        this.toOverlayMenu(currentBreakpoint.name, menuType);
      }
      if ($body.is('.horizontal-layout') && !$body.hasClass('.horizontal-menu-demo')) {
        this.changeMenu(currentBreakpoint.name);
        $('.menu-toggle').removeClass('is-active');
      }

      // Initialize drill down menu for vertical layouts, for horizontal layouts drilldown menu is intitialized in changemenu function
      if (menuType != 'horizontal-menu') {
        // Drill down menu
        // ------------------------------
        this.drillDownMenu(currentBreakpoint.name);
      }

      // Dropdown submenu on large screen on hover For Large screen only
      // ---------------------------------------------------------------
      if (currentBreakpoint.name == 'xl') {
        $('body[data-open="hover"] .header-navbar .dropdown').on('mouseenter', function () {
          if (!$(this).hasClass('show')) {
            $(this).addClass('show');
          } else {
            $(this).removeClass('show');
          }
        }).on('mouseleave', function (event) {
          $(this).removeClass('show');
        });
        $('body[data-open="hover"] .dropdown a').on('click', function (e) {
          if (menuType == 'horizontal-menu') {
            var $this = $(this);
            if ($this.hasClass('dropdown-toggle')) {
              return false;
            }
          }
        });
      }

      // Added data attribute brand-center for navbar-brand-center
      // TODO:AJ: Shift this feature in JADE.
      if ($('.header-navbar').hasClass('navbar-brand-center')) {
        $('.header-navbar').attr('data-nav', 'brand-center');
      }
      if (currentBreakpoint.name == 'sm' || currentBreakpoint.name == 'xs') {
        $('.header-navbar[data-nav=brand-center]').removeClass('navbar-brand-center');
      } else {
        $('.header-navbar[data-nav=brand-center]').addClass('navbar-brand-center');
      }

      // On screen width change, current active menu in horizontal
      if (currentBreakpoint.name == 'xl' && menuType == 'horizontal-menu') {
        $(".main-menu-content").find('li.active').parents('li').addClass('sidebar-group-active active');
      }
      if (currentBreakpoint.name !== 'xl' && menuType == 'horizontal-menu') {
        $("#navbar-type").toggleClass('d-none d-xl-block');
      }

      // Dropdown submenu on small screen on click
      // --------------------------------------------------
      $('ul.dropdown-menu [data-toggle=dropdown]').on('click', function (event) {
        if ($(this).siblings('ul.dropdown-menu').length > 0) {
          event.preventDefault();
        }
        event.stopPropagation();
        $(this).parent().siblings().removeClass('show');
        $(this).parent().toggleClass('show');
      });

      // Horizontal layout submenu drawer scrollbar
      if (menuType == 'horizontal-menu') {
        $('li.dropdown-submenu').on('mouseenter', function () {
          if (!$(this).parent('.dropdown').hasClass('show')) {
            $(this).removeClass('openLeft');
          }
          var dd = $(this).find('.dropdown-menu');
          if (dd.length > 0) {
            var pageHeight = $(window).height(),
              ddTop = $(this).position().top,
              ddLeft = dd.offset().left,
              ddWidth = dd.width(),
              ddHeight = dd.height();
            if (pageHeight - ddTop - ddHeight - 28 < 1) {
              var maxHeight = pageHeight - ddTop - 170;
              $(this).find('.dropdown-menu').css({
                'max-height': maxHeight + 'px',
                'overflow-y': 'auto',
                'overflow-x': 'hidden'
              });
              var menu_content = new PerfectScrollbar('li.dropdown-submenu.show .dropdown-menu', {
                wheelPropagation: false
              });
            }
            // Add class to horizontal sub menu if screen width is small
            if (ddLeft + ddWidth - (window.innerWidth - 16) >= 0) {
              $(this).addClass('openLeft');
            }
          }
        });
        $('.theme-layouts').find('.semi-dark').hide();
        $('#customizer-navbar-colors').hide();
      }

      /********************************************
      *             Searchable Menu               *
      ********************************************/

      function searchMenu(list) {
        var input = $(".menu-search");
        $(input).change(function () {
          var filter = $(this).val();
          if (filter) {
            // Hide Main Navigation Headers
            $('.navigation-header').hide();
            // this finds all links in a list that contain the input,
            // and hide the ones not containing the input while showing the ones that do
            $(list).find("li a:not(:Contains(" + filter + "))").hide().parent().hide();
            // $(list).find("li a:Contains(" + filter + ")").show().parents('li').show().addClass('open').closest('li').children('a').show();
            var searchFilter = $(list).find("li a:Contains(" + filter + ")");
            if (searchFilter.parent().hasClass('has-sub')) {
              searchFilter.show().parents('li').show().addClass('open').closest('li').children('a').show().children('li').show();

              // searchFilter.parents('li').find('li').show().children('a').show();
              if (searchFilter.siblings('ul').length > 0) {
                searchFilter.siblings('ul').children('li').show().children('a').show();
              }
            } else {
              searchFilter.show().parents('li').show().addClass('open').closest('li').children('a').show();
            }
          } else {
            // return to default
            $('.navigation-header').show();
            $(list).find("li a").show().parent().show().removeClass('open');
          }
          $.app.menu.manualScroller.update();
          return false;
        }).keyup(function () {
          // fire the above change event after every letter
          $(this).change();
        });
      }
      if (menuType === 'vertical-menu' || menuType === 'vertical-overlay-menu') {
        // custom css expression for a case-insensitive contains()
        jQuery.expr[':'].Contains = function (a, i, m) {
          return (a.textContent || a.innerText || "").toUpperCase().indexOf(m[3].toUpperCase()) >= 0;
        };
        searchMenu($("#main-menu-navigation"));
      }
    },
    transit: function transit(callback1, callback2) {
      var menuObj = this;
      $body.addClass('changing-menu');
      callback1.call(menuObj);
      if ($body.hasClass('vertical-layout')) {
        if ($body.hasClass('menu-open') || $body.hasClass('menu-expanded')) {
          $('.menu-toggle').addClass('is-active');

          // Show menu header search when menu is normally visible
          if ($body.data('menu') === 'vertical-menu') {
            if ($('.main-menu-header')) {
              $('.main-menu-header').show();
            }
          }
        } else {
          $('.menu-toggle').removeClass('is-active');

          // Hide menu header search when only menu icons are visible
          if ($body.data('menu') === 'vertical-menu') {
            if ($('.main-menu-header')) {
              $('.main-menu-header').hide();
            }
          }
        }
      }
      setTimeout(function () {
        callback2.call(menuObj);
        $body.removeClass('changing-menu');
        menuObj.update();
      }, 500);
    },
    open: function open() {
      this.transit(function () {
        $body.removeClass('menu-hide menu-collapsed').addClass('menu-open');
        this.hidden = false;
        this.expanded = true;
        if ($body.hasClass('vertical-overlay-menu')) {
          $('.sidenav-overlay').removeClass('d-none').addClass('d-block');
          $('body').css('overflow', 'hidden');
        }
      }, function () {
        if (!$('.main-menu').hasClass('menu-native-scroll') && $('.main-menu').hasClass('menu-fixed')) {
          this.manualScroller.enable();
          $('.main-menu-content').css('height', $(window).height() - $('.header-navbar').height() - $('.main-menu-header').outerHeight() - $('.main-menu-footer').outerHeight());
          // this.manualScroller.update();
        }
        if (!$body.hasClass('vertical-overlay-menu')) {
          $('.sidenav-overlay').removeClass('d-block d-none');
          $('body').css('overflow', 'auto');
        }
      });
    },
    hide: function hide() {
      this.transit(function () {
        $body.removeClass('menu-open menu-expanded').addClass('menu-hide');
        this.hidden = true;
        this.expanded = false;
        if ($body.hasClass('vertical-overlay-menu')) {
          $('.sidenav-overlay').removeClass('d-block').addClass('d-none');
          $('body').css('overflow', 'auto');
        }
      }, function () {
        if (!$('.main-menu').hasClass('menu-native-scroll') && $('.main-menu').hasClass('menu-fixed')) {
          this.manualScroller.enable();
        }
        if (!$body.hasClass('vertical-overlay-menu')) {
          $('.sidenav-overlay').removeClass('d-block d-none');
          $('body').css('overflow', 'auto');
        }
      });
    },
    expand: function expand() {
      if (this.expanded === false) {
        if ($body.data('menu') == 'vertical-menu-modern') {
          $('.modern-nav-toggle').find('.toggle-icon').removeClass('feather icon-circle').addClass('feather icon-disc');
        }
        this.transit(function () {
          $body.removeClass('menu-collapsed').addClass('menu-expanded');
          this.collapsed = false;
          this.expanded = true;
          $('.sidenav-overlay').removeClass('d-block d-none');
        }, function () {
          if ($('.main-menu').hasClass('menu-native-scroll') || $body.data('menu') == 'horizontal-menu') {
            this.manualScroller.disable();
          } else {
            if ($('.main-menu').hasClass('menu-fixed')) this.manualScroller.enable();
          }
          if (($body.data('menu') == 'vertical-menu' || $body.data('menu') == 'vertical-menu-modern') && $('.main-menu').hasClass('menu-fixed')) {
            $('.main-menu-content').css('height', $(window).height() - $('.header-navbar').height() - $('.main-menu-header').outerHeight() - $('.main-menu-footer').outerHeight());
            // this.manualScroller.update();
          }
        });
      }
    },
    collapse: function collapse(defMenu) {
      if (this.collapsed === false) {
        if ($body.data('menu') == 'vertical-menu-modern') {
          $('.modern-nav-toggle').find('.toggle-icon').removeClass('feather icon-disc').addClass('feather icon-circle');
        }
        this.transit(function () {
          $body.removeClass('menu-expanded').addClass('menu-collapsed');
          this.collapsed = true;
          this.expanded = false;
          $('.content-overlay').removeClass('d-block d-none');
        }, function () {
          if ($body.data('menu') == 'horizontal-menu' && $body.hasClass('vertical-overlay-menu')) {
            if ($('.main-menu').hasClass('menu-fixed')) this.manualScroller.enable();
          }
          if (($body.data('menu') == 'vertical-menu' || $body.data('menu') == 'vertical-menu-modern') && $('.main-menu').hasClass('menu-fixed')) {
            $('.main-menu-content').css('height', $(window).height() - $('.header-navbar').height());
            // this.manualScroller.update();
          }
          if ($body.data('menu') == 'vertical-menu-modern') {
            if ($('.main-menu').hasClass('menu-fixed')) this.manualScroller.enable();
          }
        });
      }
    },
    toOverlayMenu: function toOverlayMenu(screen, menuType) {
      var menu = $body.data('menu');
      if (menuType == 'vertical-menu-modern') {
        if (screen == 'lg' || screen == 'md' || screen == 'sm' || screen == 'xs') {
          if ($body.hasClass(menu)) {
            $body.removeClass(menu).addClass('vertical-overlay-menu');
          }
        } else {
          if ($body.hasClass('vertical-overlay-menu')) {
            $body.removeClass('vertical-overlay-menu').addClass(menu);
          }
        }
      } else {
        if (screen == 'sm' || screen == 'xs') {
          if ($body.hasClass(menu)) {
            $body.removeClass(menu).addClass('vertical-overlay-menu');
          }
        } else {
          if ($body.hasClass('vertical-overlay-menu')) {
            $body.removeClass('vertical-overlay-menu').addClass(menu);
          }
        }
      }
    },
    changeMenu: function changeMenu(screen) {
      // Replace menu html
      $('div[data-menu="menu-wrapper"]').html('');
      $('div[data-menu="menu-wrapper"]').html(menuWrapper_el);
      var menuWrapper = $('div[data-menu="menu-wrapper"]'),
        menuContainer = $('div[data-menu="menu-container"]'),
        menuNavigation = $('ul[data-menu="menu-navigation"]'),
        /*megaMenu           = $('li[data-menu="megamenu"]'),
        megaMenuCol        = $('li[data-mega-col]'),*/
        dropdownMenu = $('li[data-menu="dropdown"]'),
        dropdownSubMenu = $('li[data-menu="dropdown-submenu"]');
      if (screen === 'xl') {
        // Change body classes
        $body.removeClass('vertical-layout vertical-overlay-menu fixed-navbar').addClass($body.data('menu'));

        // Remove navbar-fix-top class on large screens
        $('nav.header-navbar').removeClass('fixed-top');

        // Change menu wrapper, menu container, menu navigation classes
        menuWrapper.removeClass().addClass(menuWrapperClasses);

        // Intitialize drill down menu for horizontal layouts
        // --------------------------------------------------
        this.drillDownMenu(screen);
        $('a.dropdown-item.nav-has-children').on('click', function () {
          event.preventDefault();
          event.stopPropagation();
        });
        $('a.dropdown-item.nav-has-parent').on('click', function () {
          event.preventDefault();
          event.stopPropagation();
        });
      } else {
        // Change body classes
        $body.removeClass($body.data('menu')).addClass('vertical-layout vertical-overlay-menu fixed-navbar');

        // Add navbar-fix-top class on small screens
        $('nav.header-navbar').addClass('fixed-top');

        // Change menu wrapper, menu container, menu navigation classes
        menuWrapper.removeClass().addClass('main-menu menu-light menu-fixed menu-shadow');
        // menuContainer.removeClass().addClass('main-menu-content');
        menuNavigation.removeClass().addClass('navigation navigation-main');

        // If Dropdown Menu
        dropdownMenu.removeClass('dropdown').addClass('has-sub');
        dropdownMenu.find('a').removeClass('dropdown-toggle nav-link');
        dropdownMenu.children('ul').find('a').removeClass('dropdown-item');
        dropdownMenu.find('ul').removeClass('dropdown-menu');
        dropdownSubMenu.removeClass().addClass('has-sub');
        $.app.nav.init();

        // Dropdown submenu on small screen on click
        // --------------------------------------------------
        $('ul.dropdown-menu [data-toggle=dropdown]').on('click', function (event) {
          event.preventDefault();
          event.stopPropagation();
          $(this).parent().siblings().removeClass('open');
          $(this).parent().toggleClass('open');
        });
        $(".main-menu-content").find('li.active').parents('li').addClass('sidebar-group-active');
        $(".main-menu-content").find("li.active").closest("li.nav-item").addClass("open");
      }
    },
    toggle: function toggle() {
      var currentBreakpoint = Unison.fetch.now(); // Current Breakpoint
      var collapsed = this.collapsed;
      var expanded = this.expanded;
      var hidden = this.hidden;
      var menu = $body.data('menu');
      switch (currentBreakpoint.name) {
        case 'xl':
          if (expanded === true) {
            if (menu == 'vertical-overlay-menu') {
              this.hide();
            } else {
              this.collapse();
            }
          } else {
            if (menu == 'vertical-overlay-menu') {
              this.open();
            } else {
              this.expand();
            }
          }
          break;
        case 'lg':
          if (expanded === true) {
            if (menu == 'vertical-overlay-menu' || menu == 'vertical-menu-modern' || menu == 'horizontal-menu') {
              this.hide();
            } else {
              this.collapse();
            }
          } else {
            if (menu == 'vertical-overlay-menu' || menu == 'vertical-menu-modern' || menu == 'horizontal-menu') {
              this.open();
            } else {
              this.expand();
            }
          }
          break;
        case 'md':
        case 'sm':
          if (hidden === true) {
            this.open();
          } else {
            this.hide();
          }
          break;
        case 'xs':
          if (hidden === true) {
            this.open();
          } else {
            this.hide();
          }
          break;
      }

      // Re-init sliding menu to update width
      this.drillDownMenu(currentBreakpoint.name);
    },
    update: function update() {
      this.manualScroller.update();
    },
    reset: function reset() {
      this.expanded = false;
      this.collapsed = false;
      this.hidden = false;
      $body.removeClass('menu-hide menu-open menu-collapsed menu-expanded');
    }
  };

  // Navigation Menu
  $.app.nav = {
    container: $('.navigation-main'),
    initialized: false,
    navItem: $('.navigation-main').find('li').not('.navigation-category'),
    config: {
      speed: 300
    },
    init: function init(config) {
      this.initialized = true; // Set to true when initialized
      $.extend(this.config, config);
      this.bind_events();
    },
    bind_events: function bind_events() {
      var menuObj = this;
      $('.navigation-main').on('mouseenter.app.menu', 'li', function () {
        var $this = $(this);
        $('.hover', '.navigation-main').removeClass('hover');
        if ($body.hasClass('menu-collapsed') && $body.data('menu') != 'vertical-menu-modern') {
          $('.main-menu-content').children('span.menu-title').remove();
          $('.main-menu-content').children('a.menu-title').remove();
          $('.main-menu-content').children('ul.menu-content').remove();

          // Title
          var menuTitle = $this.find('span.menu-title').clone(),
            tempTitle,
            tempLink;
          if (!$this.hasClass('has-sub')) {
            tempTitle = $this.find('span.menu-title').text();
            tempLink = $this.children('a').attr('href');
            if (tempTitle !== '') {
              menuTitle = $("<a>");
              menuTitle.attr("href", tempLink);
              menuTitle.attr("title", tempTitle);
              menuTitle.text(tempTitle);
              menuTitle.addClass("menu-title");
            }
          }
          // menu_header_height = ($('.main-menu-header').length) ? $('.main-menu-header').height() : 0,
          // fromTop = menu_header_height + $this.position().top + parseInt($this.css( "border-top" ),10);
          var fromTop;
          if ($this.css("border-top")) {
            fromTop = $this.position().top + parseInt($this.css("border-top"), 10);
          } else {
            fromTop = $this.position().top;
          }
          if ($body.data('menu') !== 'vertical-compact-menu') {
            menuTitle.appendTo('.main-menu-content').css({
              position: 'fixed',
              top: fromTop
            });
          }

          // Content
          if ($this.hasClass('has-sub') && $this.hasClass('nav-item')) {
            var menuContent = $this.children('ul:first');
            menuObj.adjustSubmenu($this);
          }
        }
        $this.addClass('hover');
      }).on('mouseleave.app.menu', 'li', function () {
        // $(this).removeClass('hover');
      }).on('active.app.menu', 'li', function (e) {
        $(this).addClass('active');
        e.stopPropagation();
      }).on('deactive.app.menu', 'li.active', function (e) {
        $(this).removeClass('active');
        e.stopPropagation();
      }).on('open.app.menu', 'li', function (e) {
        var $listItem = $(this);
        $listItem.addClass('open');
        menuObj.expand($listItem);

        // If menu collapsible then do not take any action
        if ($('.main-menu').hasClass('menu-collapsible')) {
          return false;
        }
        // If menu accordion then close all except clicked once
        else {
          $listItem.siblings('.open').find('li.open').trigger('close.app.menu');
          $listItem.siblings('.open').trigger('close.app.menu');
        }
        e.stopPropagation();
      }).on('close.app.menu', 'li.open', function (e) {
        var $listItem = $(this);
        $listItem.removeClass('open');
        menuObj.collapse($listItem);
        e.stopPropagation();
      }).on('click.app.menu', 'li', function (e) {
        var $listItem = $(this);
        if ($listItem.is('.disabled')) {
          e.preventDefault();
        } else {
          if ($body.hasClass('menu-collapsed') && $body.data('menu') != 'vertical-menu-modern') {
            e.preventDefault();
          } else {
            if ($listItem.has('ul').length) {
              if ($listItem.is('.open')) {
                $listItem.trigger('close.app.menu');
              } else {
                $listItem.trigger('open.app.menu');
              }
            } else {
              if (!$listItem.is('.active')) {
                $listItem.siblings('.active').trigger('deactive.app.menu');
                $listItem.trigger('active.app.menu');
              }
            }
          }
        }
        e.stopPropagation();
      });
      $('.navbar-header, .main-menu').on('mouseenter', modernMenuExpand).on('mouseleave', modernMenuCollapse);
      function modernMenuExpand() {
        if ($body.data('menu') == 'vertical-menu-modern') {
          $('.main-menu, .navbar-header').addClass('expanded');
          if ($body.hasClass('menu-collapsed')) {
            if ($('.main-menu li.open').length === 0) {
              $(".main-menu-content").find('li.active').parents('li').addClass('open');
            }
            var $listItem = $('.main-menu li.menu-collapsed-open'),
              $subList = $listItem.children('ul');
            $subList.hide().slideDown(200, function () {
              $(this).css('display', '');
            });
            $listItem.addClass('open').removeClass('menu-collapsed-open');
            // $.app.menu.changeLogo('expand');
          }
        }
      }
      function modernMenuCollapse() {
        if ($body.hasClass('menu-collapsed') && $body.data('menu') == 'vertical-menu-modern') {
          setTimeout(function () {
            if ($('.main-menu:hover').length === 0 && $('.navbar-header:hover').length === 0) {
              $('.main-menu, .navbar-header').removeClass('expanded');
              if ($body.hasClass('menu-collapsed')) {
                var $listItem = $('.main-menu li.open'),
                  $subList = $listItem.children('ul');
                $listItem.addClass('menu-collapsed-open');
                $subList.show().slideUp(200, function () {
                  $(this).css('display', '');
                });
                $listItem.removeClass('open');
                // $.app.menu.changeLogo();
              }
            }
          }, 1);
        }
      }
      $('.main-menu-content').on('mouseleave', function () {
        if ($body.hasClass('menu-collapsed')) {
          $('.main-menu-content').children('span.menu-title').remove();
          $('.main-menu-content').children('a.menu-title').remove();
          $('.main-menu-content').children('ul.menu-content').remove();
        }
        $('.hover', '.navigation-main').removeClass('hover');
      });

      // If list item has sub menu items then prevent redirection.
      $('.navigation-main li.has-sub > a').on('click', function (e) {
        e.preventDefault();
      });
      $('ul.menu-content').on('click', 'li', function (e) {
        var $listItem = $(this);
        if ($listItem.is('.disabled')) {
          e.preventDefault();
        } else {
          if ($listItem.has('ul')) {
            if ($listItem.is('.open')) {
              $listItem.removeClass('open');
              menuObj.collapse($listItem);
            } else {
              $listItem.addClass('open');
              menuObj.expand($listItem);

              // If menu collapsible then do not take any action
              if ($('.main-menu').hasClass('menu-collapsible')) {
                return false;
              }
              // If menu accordion then close all except clicked once
              else {
                $listItem.siblings('.open').find('li.open').trigger('close.app.menu');
                $listItem.siblings('.open').trigger('close.app.menu');
              }
              e.stopPropagation();
            }
          } else {
            if (!$listItem.is('.active')) {
              $listItem.siblings('.active').trigger('deactive.app.menu');
              $listItem.trigger('active.app.menu');
            }
          }
        }
        e.stopPropagation();
      });
    },
    /**
     * Ensure an admin submenu is within the visual viewport.
     * @param {jQuery} $menuItem The parent menu item containing the submenu.
     */
    adjustSubmenu: function adjustSubmenu($menuItem) {
      var menuHeaderHeight,
        menutop,
        topPos,
        winHeight,
        bottomOffset,
        subMenuHeight,
        popOutMenuHeight,
        borderWidth,
        scroll_theme,
        $submenu = $menuItem.children('ul:first'),
        ul = $submenu.clone(true);
      menuHeaderHeight = $('.main-menu-header').height();
      menutop = $menuItem.position().top;
      winHeight = $window.height() - $('.header-navbar').height();
      borderWidth = 0;
      subMenuHeight = $submenu.height();
      if (parseInt($menuItem.css("border-top"), 10) > 0) {
        borderWidth = parseInt($menuItem.css("border-top"), 10);
      }
      popOutMenuHeight = winHeight - menutop - $menuItem.height() - 30;
      scroll_theme = $('.main-menu').hasClass('menu-dark') ? 'light' : 'dark';
      topPos = menutop + $menuItem.height() + borderWidth;
      ul.addClass('menu-popout').appendTo('.main-menu-content').css({
        'top': topPos,
        'position': 'fixed',
        'max-height': popOutMenuHeight
      });
      var menu_content = new PerfectScrollbar('.main-menu-content > ul.menu-content', {
        wheelPropagation: false
      });
    },
    collapse: function collapse($listItem, callback) {
      var $subList = $listItem.children('ul');
      $subList.show().slideUp($.app.nav.config.speed, function () {
        $(this).css('display', '');
        $(this).find('> li').removeClass('is-shown');
        if (callback) {
          callback();
        }
        $.app.nav.container.trigger('collapsed.app.menu');
      });
    },
    expand: function expand($listItem, callback) {
      var $subList = $listItem.children('ul');
      var $children = $subList.children('li').addClass('is-hidden');
      $subList.hide().slideDown($.app.nav.config.speed, function () {
        $(this).css('display', '');
        if (callback) {
          callback();
        }
        $.app.nav.container.trigger('expanded.app.menu');
      });
      setTimeout(function () {
        $children.addClass('is-shown');
        $children.removeClass('is-hidden');
      }, 0);
    },
    refresh: function refresh() {
      $.app.nav.container.find('.open').removeClass('open');
    }
  };
})(window, document, jQuery);

// We listen to the resize event
window.addEventListener('resize', function () {
  // We execute the same script as before
  var vh = window.innerHeight * 0.01;
  document.documentElement.style.setProperty('--vh', "".concat(vh, "px"));
});

/***/ }),

/***/ "./resources/sass/bootstrap-extended.scss":
/*!************************************************!*\
  !*** ./resources/sass/bootstrap-extended.scss ***!
  \************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

// removed by extract-text-webpack-plugin

/***/ }),

/***/ "./resources/sass/bootstrap.scss":
/*!***************************************!*\
  !*** ./resources/sass/bootstrap.scss ***!
  \***************************************/
/*! no static exports found */
/***/ (function(module, exports) {

// removed by extract-text-webpack-plugin

/***/ }),

/***/ "./resources/sass/colors.scss":
/*!************************************!*\
  !*** ./resources/sass/colors.scss ***!
  \************************************/
/*! no static exports found */
/***/ (function(module, exports) {

// removed by extract-text-webpack-plugin

/***/ }),

/***/ "./resources/sass/components.scss":
/*!****************************************!*\
  !*** ./resources/sass/components.scss ***!
  \****************************************/
/*! no static exports found */
/***/ (function(module, exports) {

// removed by extract-text-webpack-plugin

/***/ }),

/***/ "./resources/sass/core/colors/palette-gradient.scss":
/*!**********************************************************!*\
  !*** ./resources/sass/core/colors/palette-gradient.scss ***!
  \**********************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

// removed by extract-text-webpack-plugin

/***/ }),

/***/ "./resources/sass/core/colors/palette-noui.scss":
/*!******************************************************!*\
  !*** ./resources/sass/core/colors/palette-noui.scss ***!
  \******************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

// removed by extract-text-webpack-plugin

/***/ }),

/***/ "./resources/sass/core/colors/palette-variables.scss":
/*!***********************************************************!*\
  !*** ./resources/sass/core/colors/palette-variables.scss ***!
  \***********************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

// removed by extract-text-webpack-plugin

/***/ }),

/***/ "./resources/sass/core/menu/menu-types/horizontal-menu.scss":
/*!******************************************************************!*\
  !*** ./resources/sass/core/menu/menu-types/horizontal-menu.scss ***!
  \******************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

// removed by extract-text-webpack-plugin

/***/ }),

/***/ "./resources/sass/core/menu/menu-types/vertical-menu.scss":
/*!****************************************************************!*\
  !*** ./resources/sass/core/menu/menu-types/vertical-menu.scss ***!
  \****************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

// removed by extract-text-webpack-plugin

/***/ }),

/***/ "./resources/sass/core/menu/menu-types/vertical-overlay-menu.scss":
/*!************************************************************************!*\
  !*** ./resources/sass/core/menu/menu-types/vertical-overlay-menu.scss ***!
  \************************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

// removed by extract-text-webpack-plugin

/***/ }),

/***/ "./resources/sass/core/mixins/alert.scss":
/*!***********************************************!*\
  !*** ./resources/sass/core/mixins/alert.scss ***!
  \***********************************************/
/*! no static exports found */
/***/ (function(module, exports) {

// removed by extract-text-webpack-plugin

/***/ }),

/***/ "./resources/sass/core/mixins/hex2rgb.scss":
/*!*************************************************!*\
  !*** ./resources/sass/core/mixins/hex2rgb.scss ***!
  \*************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

// removed by extract-text-webpack-plugin

/***/ }),

/***/ "./resources/sass/core/mixins/main-menu-mixin.scss":
/*!*********************************************************!*\
  !*** ./resources/sass/core/mixins/main-menu-mixin.scss ***!
  \*********************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

// removed by extract-text-webpack-plugin

/***/ }),

/***/ "./resources/sass/core/mixins/transitions.scss":
/*!*****************************************************!*\
  !*** ./resources/sass/core/mixins/transitions.scss ***!
  \*****************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

// removed by extract-text-webpack-plugin

/***/ }),

/***/ "./resources/sass/custom-laravel.scss":
/*!********************************************!*\
  !*** ./resources/sass/custom-laravel.scss ***!
  \********************************************/
/*! no static exports found */
/***/ (function(module, exports) {

// removed by extract-text-webpack-plugin

/***/ }),

/***/ "./resources/sass/custom-rtl.scss":
/*!****************************************!*\
  !*** ./resources/sass/custom-rtl.scss ***!
  \****************************************/
/*! no static exports found */
/***/ (function(module, exports) {

// removed by extract-text-webpack-plugin

/***/ }),

/***/ "./resources/sass/pages/aggrid.scss":
/*!******************************************!*\
  !*** ./resources/sass/pages/aggrid.scss ***!
  \******************************************/
/*! no static exports found */
/***/ (function(module, exports) {

// removed by extract-text-webpack-plugin

/***/ }),

/***/ "./resources/sass/pages/app-chat.scss":
/*!********************************************!*\
  !*** ./resources/sass/pages/app-chat.scss ***!
  \********************************************/
/*! no static exports found */
/***/ (function(module, exports) {

// removed by extract-text-webpack-plugin

/***/ }),

/***/ "./resources/sass/pages/app-ecommerce-details.scss":
/*!*********************************************************!*\
  !*** ./resources/sass/pages/app-ecommerce-details.scss ***!
  \*********************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

// removed by extract-text-webpack-plugin

/***/ }),

/***/ "./resources/sass/pages/app-ecommerce-shop.scss":
/*!******************************************************!*\
  !*** ./resources/sass/pages/app-ecommerce-shop.scss ***!
  \******************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

// removed by extract-text-webpack-plugin

/***/ }),

/***/ "./resources/sass/pages/app-email.scss":
/*!*********************************************!*\
  !*** ./resources/sass/pages/app-email.scss ***!
  \*********************************************/
/*! no static exports found */
/***/ (function(module, exports) {

// removed by extract-text-webpack-plugin

/***/ }),

/***/ "./resources/sass/pages/app-file-manager.scss":
/*!****************************************************!*\
  !*** ./resources/sass/pages/app-file-manager.scss ***!
  \****************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

// removed by extract-text-webpack-plugin

/***/ }),

/***/ "./resources/sass/pages/app-todo.scss":
/*!********************************************!*\
  !*** ./resources/sass/pages/app-todo.scss ***!
  \********************************************/
/*! no static exports found */
/***/ (function(module, exports) {

// removed by extract-text-webpack-plugin

/***/ }),

/***/ "./resources/sass/pages/app-user.scss":
/*!********************************************!*\
  !*** ./resources/sass/pages/app-user.scss ***!
  \********************************************/
/*! no static exports found */
/***/ (function(module, exports) {

// removed by extract-text-webpack-plugin

/***/ }),

/***/ "./resources/sass/pages/authentication.scss":
/*!**************************************************!*\
  !*** ./resources/sass/pages/authentication.scss ***!
  \**************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

// removed by extract-text-webpack-plugin

/***/ }),

/***/ "./resources/sass/pages/card-analytics.scss":
/*!**************************************************!*\
  !*** ./resources/sass/pages/card-analytics.scss ***!
  \**************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

// removed by extract-text-webpack-plugin

/***/ }),

/***/ "./resources/sass/pages/colors.scss":
/*!******************************************!*\
  !*** ./resources/sass/pages/colors.scss ***!
  \******************************************/
/*! no static exports found */
/***/ (function(module, exports) {

// removed by extract-text-webpack-plugin

/***/ }),

/***/ "./resources/sass/pages/coming-soon.scss":
/*!***********************************************!*\
  !*** ./resources/sass/pages/coming-soon.scss ***!
  \***********************************************/
/*! no static exports found */
/***/ (function(module, exports) {

// removed by extract-text-webpack-plugin

/***/ }),

/***/ "./resources/sass/pages/dashboard-analytics.scss":
/*!*******************************************************!*\
  !*** ./resources/sass/pages/dashboard-analytics.scss ***!
  \*******************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

// removed by extract-text-webpack-plugin

/***/ }),

/***/ "./resources/sass/pages/data-list-view.scss":
/*!**************************************************!*\
  !*** ./resources/sass/pages/data-list-view.scss ***!
  \**************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

// removed by extract-text-webpack-plugin

/***/ }),

/***/ "./resources/sass/pages/error.scss":
/*!*****************************************!*\
  !*** ./resources/sass/pages/error.scss ***!
  \*****************************************/
/*! no static exports found */
/***/ (function(module, exports) {

// removed by extract-text-webpack-plugin

/***/ }),

/***/ "./resources/sass/pages/faq.scss":
/*!***************************************!*\
  !*** ./resources/sass/pages/faq.scss ***!
  \***************************************/
/*! no static exports found */
/***/ (function(module, exports) {

// removed by extract-text-webpack-plugin

/***/ }),

/***/ "./resources/sass/pages/invoice.scss":
/*!*******************************************!*\
  !*** ./resources/sass/pages/invoice.scss ***!
  \*******************************************/
/*! no static exports found */
/***/ (function(module, exports) {

// removed by extract-text-webpack-plugin

/***/ }),

/***/ "./resources/sass/pages/knowledge-base.scss":
/*!**************************************************!*\
  !*** ./resources/sass/pages/knowledge-base.scss ***!
  \**************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

// removed by extract-text-webpack-plugin

/***/ }),

/***/ "./resources/sass/pages/pricing.scss":
/*!*******************************************!*\
  !*** ./resources/sass/pages/pricing.scss ***!
  \*******************************************/
/*! no static exports found */
/***/ (function(module, exports) {

// removed by extract-text-webpack-plugin

/***/ }),

/***/ "./resources/sass/pages/register.scss":
/*!********************************************!*\
  !*** ./resources/sass/pages/register.scss ***!
  \********************************************/
/*! no static exports found */
/***/ (function(module, exports) {

// removed by extract-text-webpack-plugin

/***/ }),

/***/ "./resources/sass/pages/search.scss":
/*!******************************************!*\
  !*** ./resources/sass/pages/search.scss ***!
  \******************************************/
/*! no static exports found */
/***/ (function(module, exports) {

// removed by extract-text-webpack-plugin

/***/ }),

/***/ "./resources/sass/pages/timeline.scss":
/*!********************************************!*\
  !*** ./resources/sass/pages/timeline.scss ***!
  \********************************************/
/*! no static exports found */
/***/ (function(module, exports) {

// removed by extract-text-webpack-plugin

/***/ }),

/***/ "./resources/sass/pages/users.scss":
/*!*****************************************!*\
  !*** ./resources/sass/pages/users.scss ***!
  \*****************************************/
/*! no static exports found */
/***/ (function(module, exports) {

// removed by extract-text-webpack-plugin

/***/ }),

/***/ "./resources/sass/plugins/animate/animate.scss":
/*!*****************************************************!*\
  !*** ./resources/sass/plugins/animate/animate.scss ***!
  \*****************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

// removed by extract-text-webpack-plugin

/***/ }),

/***/ "./resources/sass/plugins/calendars/fullcalendar.scss":
/*!************************************************************!*\
  !*** ./resources/sass/plugins/calendars/fullcalendar.scss ***!
  \************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

// removed by extract-text-webpack-plugin

/***/ }),

/***/ "./resources/sass/plugins/extensions/context-menu.scss":
/*!*************************************************************!*\
  !*** ./resources/sass/plugins/extensions/context-menu.scss ***!
  \*************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

// removed by extract-text-webpack-plugin

/***/ }),

/***/ "./resources/sass/plugins/extensions/drag-and-drop.scss":
/*!**************************************************************!*\
  !*** ./resources/sass/plugins/extensions/drag-and-drop.scss ***!
  \**************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

// removed by extract-text-webpack-plugin

/***/ }),

/***/ "./resources/sass/plugins/extensions/media-plyr.scss":
/*!***********************************************************!*\
  !*** ./resources/sass/plugins/extensions/media-plyr.scss ***!
  \***********************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

// removed by extract-text-webpack-plugin

/***/ }),

/***/ "./resources/sass/plugins/extensions/noui-slider.scss":
/*!************************************************************!*\
  !*** ./resources/sass/plugins/extensions/noui-slider.scss ***!
  \************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

// removed by extract-text-webpack-plugin

/***/ }),

/***/ "./resources/sass/plugins/extensions/swiper.scss":
/*!*******************************************************!*\
  !*** ./resources/sass/plugins/extensions/swiper.scss ***!
  \*******************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

// removed by extract-text-webpack-plugin

/***/ }),

/***/ "./resources/sass/plugins/extensions/toastr.scss":
/*!*******************************************************!*\
  !*** ./resources/sass/plugins/extensions/toastr.scss ***!
  \*******************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

// removed by extract-text-webpack-plugin

/***/ }),

/***/ "./resources/sass/plugins/file-uploaders/dropzone.scss":
/*!*************************************************************!*\
  !*** ./resources/sass/plugins/file-uploaders/dropzone.scss ***!
  \*************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

// removed by extract-text-webpack-plugin

/***/ }),

/***/ "./resources/sass/plugins/forms/extended/typeahed.scss":
/*!*************************************************************!*\
  !*** ./resources/sass/plugins/forms/extended/typeahed.scss ***!
  \*************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

// removed by extract-text-webpack-plugin

/***/ }),

/***/ "./resources/sass/plugins/forms/form-inputs-groups.scss":
/*!**************************************************************!*\
  !*** ./resources/sass/plugins/forms/form-inputs-groups.scss ***!
  \**************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

throw new Error("Module build failed (from ./node_modules/css-loader/index.js):\nModuleBuildError: Module build failed (from ./node_modules/sass-loader/dist/cjs.js):\n\r\n          height: calc(#{$input-height-inner - 0.05rem} + #{$input-height-border + 0.2px});\r\n                        ^\r\n      Undefined operation \"calc(1.25em + 1.4rem) - 0.05rem\".\n   ╷\n48 │           height: calc(#{$input-height-inner - 0.05rem} + #{$input-height-border + 0.2px});\n   │                          ^^^^^^^^^^^^^^^^^^^^^^^^^^^^^\n   ╵\n  stdin 48:26  root stylesheet\r\n      in C:\\xampp\\htdocs\\fe-datn\\resources\\sass\\plugins\\forms\\form-inputs-groups.scss (line 48, column 26)\n    at C:\\xampp\\htdocs\\fe-datn\\node_modules\\webpack\\lib\\NormalModule.js:316:20\n    at C:\\xampp\\htdocs\\fe-datn\\node_modules\\loader-runner\\lib\\LoaderRunner.js:367:11\n    at C:\\xampp\\htdocs\\fe-datn\\node_modules\\loader-runner\\lib\\LoaderRunner.js:233:18\n    at context.callback (C:\\xampp\\htdocs\\fe-datn\\node_modules\\loader-runner\\lib\\LoaderRunner.js:111:13)\n    at C:\\xampp\\htdocs\\fe-datn\\node_modules\\sass-loader\\dist\\index.js:89:7\n    at Function.call$2 (C:\\xampp\\htdocs\\fe-datn\\node_modules\\sass\\sass.dart.js:127213:16)\n    at render_closure1.call$2 (C:\\xampp\\htdocs\\fe-datn\\node_modules\\sass\\sass.dart.js:108357:12)\n    at _RootZone.runBinary$3$3 (C:\\xampp\\htdocs\\fe-datn\\node_modules\\sass\\sass.dart.js:39825:18)\n    at _FutureListener.handleError$1 (C:\\xampp\\htdocs\\fe-datn\\node_modules\\sass\\sass.dart.js:38313:21)\n    at _Future__propagateToListeners_handleError.call$0 (C:\\xampp\\htdocs\\fe-datn\\node_modules\\sass\\sass.dart.js:38656:49)\n    at Object._Future__propagateToListeners (C:\\xampp\\htdocs\\fe-datn\\node_modules\\sass\\sass.dart.js:5273:77)\n    at _Future._completeError$2 (C:\\xampp\\htdocs\\fe-datn\\node_modules\\sass\\sass.dart.js:38489:9)\n    at _AsyncAwaitCompleter.completeError$2 (C:\\xampp\\htdocs\\fe-datn\\node_modules\\sass\\sass.dart.js:38083:12)\n    at Object._asyncRethrow (C:\\xampp\\htdocs\\fe-datn\\node_modules\\sass\\sass.dart.js:5040:17)\n    at C:\\xampp\\htdocs\\fe-datn\\node_modules\\sass\\sass.dart.js:28659:20\n    at _wrapJsFunctionForAsync_closure.$protected (C:\\xampp\\htdocs\\fe-datn\\node_modules\\sass\\sass.dart.js:5065:15)\n    at _wrapJsFunctionForAsync_closure.call$2 (C:\\xampp\\htdocs\\fe-datn\\node_modules\\sass\\sass.dart.js:38102:12)\n    at _awaitOnObject_closure0.call$2 (C:\\xampp\\htdocs\\fe-datn\\node_modules\\sass\\sass.dart.js:38096:25)\n    at _RootZone.runBinary$3$3 (C:\\xampp\\htdocs\\fe-datn\\node_modules\\sass\\sass.dart.js:39825:18)\n    at _FutureListener.handleError$1 (C:\\xampp\\htdocs\\fe-datn\\node_modules\\sass\\sass.dart.js:38313:21)\n    at _Future__propagateToListeners_handleError.call$0 (C:\\xampp\\htdocs\\fe-datn\\node_modules\\sass\\sass.dart.js:38656:49)\n    at Object._Future__propagateToListeners (C:\\xampp\\htdocs\\fe-datn\\node_modules\\sass\\sass.dart.js:5273:77)\n    at _Future._completeError$2 (C:\\xampp\\htdocs\\fe-datn\\node_modules\\sass\\sass.dart.js:38489:9)\n    at _AsyncAwaitCompleter.completeError$2 (C:\\xampp\\htdocs\\fe-datn\\node_modules\\sass\\sass.dart.js:38083:12)\n    at Object._asyncRethrow (C:\\xampp\\htdocs\\fe-datn\\node_modules\\sass\\sass.dart.js:5040:17)\n    at C:\\xampp\\htdocs\\fe-datn\\node_modules\\sass\\sass.dart.js:20720:20\n    at _wrapJsFunctionForAsync_closure.$protected (C:\\xampp\\htdocs\\fe-datn\\node_modules\\sass\\sass.dart.js:5065:15)\n    at _wrapJsFunctionForAsync_closure.call$2 (C:\\xampp\\htdocs\\fe-datn\\node_modules\\sass\\sass.dart.js:38102:12)\n    at _awaitOnObject_closure0.call$2 (C:\\xampp\\htdocs\\fe-datn\\node_modules\\sass\\sass.dart.js:38096:25)\n    at _RootZone.runBinary$3$3 (C:\\xampp\\htdocs\\fe-datn\\node_modules\\sass\\sass.dart.js:39825:18)\n    at _FutureListener.handleError$1 (C:\\xampp\\htdocs\\fe-datn\\node_modules\\sass\\sass.dart.js:38313:21)\n    at _Future__propagateToListeners_handleError.call$0 (C:\\xampp\\htdocs\\fe-datn\\node_modules\\sass\\sass.dart.js:38656:49)\n    at Object._Future__propagateToListeners (C:\\xampp\\htdocs\\fe-datn\\node_modules\\sass\\sass.dart.js:5273:77)\n    at _Future._completeError$2 (C:\\xampp\\htdocs\\fe-datn\\node_modules\\sass\\sass.dart.js:38489:9)\n    at _AsyncAwaitCompleter.completeError$2 (C:\\xampp\\htdocs\\fe-datn\\node_modules\\sass\\sass.dart.js:38083:12)\n    at Object._asyncRethrow (C:\\xampp\\htdocs\\fe-datn\\node_modules\\sass\\sass.dart.js:5040:17)\n    at C:\\xampp\\htdocs\\fe-datn\\node_modules\\sass\\sass.dart.js:20765:20\n    at _wrapJsFunctionForAsync_closure.$protected (C:\\xampp\\htdocs\\fe-datn\\node_modules\\sass\\sass.dart.js:5065:15)\n    at _wrapJsFunctionForAsync_closure.call$2 (C:\\xampp\\htdocs\\fe-datn\\node_modules\\sass\\sass.dart.js:38102:12)\n    at _awaitOnObject_closure0.call$2 (C:\\xampp\\htdocs\\fe-datn\\node_modules\\sass\\sass.dart.js:38096:25)\n    at _RootZone.runBinary$3$3 (C:\\xampp\\htdocs\\fe-datn\\node_modules\\sass\\sass.dart.js:39825:18)\n    at _FutureListener.handleError$1 (C:\\xampp\\htdocs\\fe-datn\\node_modules\\sass\\sass.dart.js:38313:21)\n    at _Future__propagateToListeners_handleError.call$0 (C:\\xampp\\htdocs\\fe-datn\\node_modules\\sass\\sass.dart.js:38656:49)\n    at Object._Future__propagateToListeners (C:\\xampp\\htdocs\\fe-datn\\node_modules\\sass\\sass.dart.js:5273:77)\n    at _Future._completeError$2 (C:\\xampp\\htdocs\\fe-datn\\node_modules\\sass\\sass.dart.js:38489:9)\n    at _AsyncAwaitCompleter.completeError$2 (C:\\xampp\\htdocs\\fe-datn\\node_modules\\sass\\sass.dart.js:38083:12)\n    at Object._asyncRethrow (C:\\xampp\\htdocs\\fe-datn\\node_modules\\sass\\sass.dart.js:5040:17)\n    at C:\\xampp\\htdocs\\fe-datn\\node_modules\\sass\\sass.dart.js:86194:24\n    at _wrapJsFunctionForAsync_closure.$protected (C:\\xampp\\htdocs\\fe-datn\\node_modules\\sass\\sass.dart.js:5065:15)\n    at _wrapJsFunctionForAsync_closure.call$2 (C:\\xampp\\htdocs\\fe-datn\\node_modules\\sass\\sass.dart.js:38102:12)");

/***/ }),

/***/ "./resources/sass/plugins/forms/validation/form-validation.scss":
/*!**********************************************************************!*\
  !*** ./resources/sass/plugins/forms/validation/form-validation.scss ***!
  \**********************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

// removed by extract-text-webpack-plugin

/***/ }),

/***/ "./resources/sass/plugins/forms/wizard.scss":
/*!**************************************************!*\
  !*** ./resources/sass/plugins/forms/wizard.scss ***!
  \**************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

// removed by extract-text-webpack-plugin

/***/ }),

/***/ "./resources/sass/plugins/loaders/animations/ball-beat.scss":
/*!******************************************************************!*\
  !*** ./resources/sass/plugins/loaders/animations/ball-beat.scss ***!
  \******************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

// removed by extract-text-webpack-plugin

/***/ }),

/***/ "./resources/sass/plugins/loaders/animations/ball-clip-rotate-multiple.scss":
/*!**********************************************************************************!*\
  !*** ./resources/sass/plugins/loaders/animations/ball-clip-rotate-multiple.scss ***!
  \**********************************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

// removed by extract-text-webpack-plugin

/***/ }),

/***/ "./resources/sass/plugins/loaders/animations/ball-clip-rotate-pulse.scss":
/*!*******************************************************************************!*\
  !*** ./resources/sass/plugins/loaders/animations/ball-clip-rotate-pulse.scss ***!
  \*******************************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

// removed by extract-text-webpack-plugin

/***/ }),

/***/ "./resources/sass/plugins/loaders/animations/ball-clip-rotate.scss":
/*!*************************************************************************!*\
  !*** ./resources/sass/plugins/loaders/animations/ball-clip-rotate.scss ***!
  \*************************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

// removed by extract-text-webpack-plugin

/***/ }),

/***/ "./resources/sass/plugins/loaders/animations/ball-grid-beat.scss":
/*!***********************************************************************!*\
  !*** ./resources/sass/plugins/loaders/animations/ball-grid-beat.scss ***!
  \***********************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

// removed by extract-text-webpack-plugin

/***/ }),

/***/ "./resources/sass/plugins/loaders/animations/ball-grid-pulse.scss":
/*!************************************************************************!*\
  !*** ./resources/sass/plugins/loaders/animations/ball-grid-pulse.scss ***!
  \************************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

// removed by extract-text-webpack-plugin

/***/ }),

/***/ "./resources/sass/plugins/loaders/animations/ball-pulse-rise.scss":
/*!************************************************************************!*\
  !*** ./resources/sass/plugins/loaders/animations/ball-pulse-rise.scss ***!
  \************************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

// removed by extract-text-webpack-plugin

/***/ }),

/***/ "./resources/sass/plugins/loaders/animations/ball-pulse-round.scss":
/*!*************************************************************************!*\
  !*** ./resources/sass/plugins/loaders/animations/ball-pulse-round.scss ***!
  \*************************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

// removed by extract-text-webpack-plugin

/***/ }),

/***/ "./resources/sass/plugins/loaders/animations/ball-pulse-sync.scss":
/*!************************************************************************!*\
  !*** ./resources/sass/plugins/loaders/animations/ball-pulse-sync.scss ***!
  \************************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

// removed by extract-text-webpack-plugin

/***/ }),

/***/ "./resources/sass/plugins/loaders/animations/ball-pulse.scss":
/*!*******************************************************************!*\
  !*** ./resources/sass/plugins/loaders/animations/ball-pulse.scss ***!
  \*******************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

// removed by extract-text-webpack-plugin

/***/ }),

/***/ "./resources/sass/plugins/loaders/animations/ball-rotate.scss":
/*!********************************************************************!*\
  !*** ./resources/sass/plugins/loaders/animations/ball-rotate.scss ***!
  \********************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

// removed by extract-text-webpack-plugin

/***/ }),

/***/ "./resources/sass/plugins/loaders/animations/ball-scale-multiple.scss":
/*!****************************************************************************!*\
  !*** ./resources/sass/plugins/loaders/animations/ball-scale-multiple.scss ***!
  \****************************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

// removed by extract-text-webpack-plugin

/***/ }),

/***/ "./resources/sass/plugins/loaders/animations/ball-scale-random.scss":
/*!**************************************************************************!*\
  !*** ./resources/sass/plugins/loaders/animations/ball-scale-random.scss ***!
  \**************************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

// removed by extract-text-webpack-plugin

/***/ }),

/***/ "./resources/sass/plugins/loaders/animations/ball-scale-ripple-multiple.scss":
/*!***********************************************************************************!*\
  !*** ./resources/sass/plugins/loaders/animations/ball-scale-ripple-multiple.scss ***!
  \***********************************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

// removed by extract-text-webpack-plugin

/***/ }),

/***/ "./resources/sass/plugins/loaders/animations/ball-scale-ripple.scss":
/*!**************************************************************************!*\
  !*** ./resources/sass/plugins/loaders/animations/ball-scale-ripple.scss ***!
  \**************************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

// removed by extract-text-webpack-plugin

/***/ }),

/***/ "./resources/sass/plugins/loaders/animations/ball-scale.scss":
/*!*******************************************************************!*\
  !*** ./resources/sass/plugins/loaders/animations/ball-scale.scss ***!
  \*******************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

// removed by extract-text-webpack-plugin

/***/ }),

/***/ "./resources/sass/plugins/loaders/animations/ball-spin-fade-loader.scss":
/*!******************************************************************************!*\
  !*** ./resources/sass/plugins/loaders/animations/ball-spin-fade-loader.scss ***!
  \******************************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

// removed by extract-text-webpack-plugin

/***/ }),

/***/ "./resources/sass/plugins/loaders/animations/ball-spin-loader.scss":
/*!*************************************************************************!*\
  !*** ./resources/sass/plugins/loaders/animations/ball-spin-loader.scss ***!
  \*************************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

// removed by extract-text-webpack-plugin

/***/ }),

/***/ "./resources/sass/plugins/loaders/animations/ball-triangle-trace.scss":
/*!****************************************************************************!*\
  !*** ./resources/sass/plugins/loaders/animations/ball-triangle-trace.scss ***!
  \****************************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

// removed by extract-text-webpack-plugin

/***/ }),

/***/ "./resources/sass/plugins/loaders/animations/ball-zig-zag-deflect.scss":
/*!*****************************************************************************!*\
  !*** ./resources/sass/plugins/loaders/animations/ball-zig-zag-deflect.scss ***!
  \*****************************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

// removed by extract-text-webpack-plugin

/***/ }),

/***/ "./resources/sass/plugins/loaders/animations/ball-zig-zag.scss":
/*!*********************************************************************!*\
  !*** ./resources/sass/plugins/loaders/animations/ball-zig-zag.scss ***!
  \*********************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

// removed by extract-text-webpack-plugin

/***/ }),

/***/ "./resources/sass/plugins/loaders/animations/cube-transition.scss":
/*!************************************************************************!*\
  !*** ./resources/sass/plugins/loaders/animations/cube-transition.scss ***!
  \************************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

// removed by extract-text-webpack-plugin

/***/ }),

/***/ "./resources/sass/plugins/loaders/animations/line-scale-pulse-out-rapid.scss":
/*!***********************************************************************************!*\
  !*** ./resources/sass/plugins/loaders/animations/line-scale-pulse-out-rapid.scss ***!
  \***********************************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

// removed by extract-text-webpack-plugin

/***/ }),

/***/ "./resources/sass/plugins/loaders/animations/line-scale-pulse-out.scss":
/*!*****************************************************************************!*\
  !*** ./resources/sass/plugins/loaders/animations/line-scale-pulse-out.scss ***!
  \*****************************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

// removed by extract-text-webpack-plugin

/***/ }),

/***/ "./resources/sass/plugins/loaders/animations/line-scale-random.scss":
/*!**************************************************************************!*\
  !*** ./resources/sass/plugins/loaders/animations/line-scale-random.scss ***!
  \**************************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

// removed by extract-text-webpack-plugin

/***/ }),

/***/ "./resources/sass/plugins/loaders/animations/line-scale.scss":
/*!*******************************************************************!*\
  !*** ./resources/sass/plugins/loaders/animations/line-scale.scss ***!
  \*******************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

// removed by extract-text-webpack-plugin

/***/ }),

/***/ "./resources/sass/plugins/loaders/animations/line-spin-fade-loader.scss":
/*!******************************************************************************!*\
  !*** ./resources/sass/plugins/loaders/animations/line-spin-fade-loader.scss ***!
  \******************************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

// removed by extract-text-webpack-plugin

/***/ }),

/***/ "./resources/sass/plugins/loaders/animations/pacman.scss":
/*!***************************************************************!*\
  !*** ./resources/sass/plugins/loaders/animations/pacman.scss ***!
  \***************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

// removed by extract-text-webpack-plugin

/***/ }),

/***/ "./resources/sass/plugins/loaders/animations/semi-circle-spin.scss":
/*!*************************************************************************!*\
  !*** ./resources/sass/plugins/loaders/animations/semi-circle-spin.scss ***!
  \*************************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

// removed by extract-text-webpack-plugin

/***/ }),

/***/ "./resources/sass/plugins/loaders/animations/square-spin.scss":
/*!********************************************************************!*\
  !*** ./resources/sass/plugins/loaders/animations/square-spin.scss ***!
  \********************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

// removed by extract-text-webpack-plugin

/***/ }),

/***/ "./resources/sass/plugins/loaders/animations/triangle-skew-spin.scss":
/*!***************************************************************************!*\
  !*** ./resources/sass/plugins/loaders/animations/triangle-skew-spin.scss ***!
  \***************************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

// removed by extract-text-webpack-plugin

/***/ }),

/***/ "./resources/sass/plugins/loaders/loaders.scss":
/*!*****************************************************!*\
  !*** ./resources/sass/plugins/loaders/loaders.scss ***!
  \*****************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

// removed by extract-text-webpack-plugin

/***/ }),

/***/ "./resources/sass/plugins/pickers/bootstrap-datetimepicker-build.scss":
/*!****************************************************************************!*\
  !*** ./resources/sass/plugins/pickers/bootstrap-datetimepicker-build.scss ***!
  \****************************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

// removed by extract-text-webpack-plugin

/***/ }),

/***/ "./resources/sass/plugins/tour/tour.scss":
/*!***********************************************!*\
  !*** ./resources/sass/plugins/tour/tour.scss ***!
  \***********************************************/
/*! no static exports found */
/***/ (function(module, exports) {

// removed by extract-text-webpack-plugin

/***/ }),

/***/ "./resources/sass/plugins/ui/coming-soon.scss":
/*!****************************************************!*\
  !*** ./resources/sass/plugins/ui/coming-soon.scss ***!
  \****************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

// removed by extract-text-webpack-plugin

/***/ }),

/***/ "./resources/sass/themes/dark-layout.scss":
/*!************************************************!*\
  !*** ./resources/sass/themes/dark-layout.scss ***!
  \************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

// removed by extract-text-webpack-plugin

/***/ }),

/***/ "./resources/sass/themes/semi-dark-layout.scss":
/*!*****************************************************!*\
  !*** ./resources/sass/themes/semi-dark-layout.scss ***!
  \*****************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

// removed by extract-text-webpack-plugin

/***/ }),

/***/ 0:
/*!*************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** multi ./resources/js/core/app-menu.js ./resources/sass/plugins/animate/animate.scss ./resources/sass/plugins/calendars/fullcalendar.scss ./resources/sass/plugins/extensions/context-menu.scss ./resources/sass/plugins/extensions/drag-and-drop.scss ./resources/sass/plugins/extensions/media-plyr.scss ./resources/sass/plugins/extensions/noui-slider.scss ./resources/sass/plugins/extensions/swiper.scss ./resources/sass/plugins/extensions/toastr.scss ./resources/sass/plugins/file-uploaders/dropzone.scss ./resources/sass/plugins/forms/extended/typeahed.scss ./resources/sass/plugins/forms/form-inputs-groups.scss ./resources/sass/plugins/forms/validation/form-validation.scss ./resources/sass/plugins/forms/wizard.scss ./resources/sass/plugins/loaders/animations/ball-beat.scss ./resources/sass/plugins/loaders/animations/ball-clip-rotate-multiple.scss ./resources/sass/plugins/loaders/animations/ball-clip-rotate-pulse.scss ./resources/sass/plugins/loaders/animations/ball-clip-rotate.scss ./resources/sass/plugins/loaders/animations/ball-grid-beat.scss ./resources/sass/plugins/loaders/animations/ball-grid-pulse.scss ./resources/sass/plugins/loaders/animations/ball-pulse-rise.scss ./resources/sass/plugins/loaders/animations/ball-pulse-round.scss ./resources/sass/plugins/loaders/animations/ball-pulse-sync.scss ./resources/sass/plugins/loaders/animations/ball-pulse.scss ./resources/sass/plugins/loaders/animations/ball-rotate.scss ./resources/sass/plugins/loaders/animations/ball-scale-multiple.scss ./resources/sass/plugins/loaders/animations/ball-scale-random.scss ./resources/sass/plugins/loaders/animations/ball-scale-ripple-multiple.scss ./resources/sass/plugins/loaders/animations/ball-scale-ripple.scss ./resources/sass/plugins/loaders/animations/ball-scale.scss ./resources/sass/plugins/loaders/animations/ball-spin-fade-loader.scss ./resources/sass/plugins/loaders/animations/ball-spin-loader.scss ./resources/sass/plugins/loaders/animations/ball-triangle-trace.scss ./resources/sass/plugins/loaders/animations/ball-zig-zag-deflect.scss ./resources/sass/plugins/loaders/animations/ball-zig-zag.scss ./resources/sass/plugins/loaders/animations/cube-transition.scss ./resources/sass/plugins/loaders/animations/line-scale-pulse-out-rapid.scss ./resources/sass/plugins/loaders/animations/line-scale-pulse-out.scss ./resources/sass/plugins/loaders/animations/line-scale-random.scss ./resources/sass/plugins/loaders/animations/line-scale.scss ./resources/sass/plugins/loaders/animations/line-spin-fade-loader.scss ./resources/sass/plugins/loaders/animations/pacman.scss ./resources/sass/plugins/loaders/animations/semi-circle-spin.scss ./resources/sass/plugins/loaders/animations/square-spin.scss ./resources/sass/plugins/loaders/animations/triangle-skew-spin.scss ./resources/sass/plugins/loaders/loaders.scss ./resources/sass/plugins/pickers/bootstrap-datetimepicker-build.scss ./resources/sass/plugins/tour/tour.scss ./resources/sass/plugins/ui/coming-soon.scss ./resources/sass/themes/dark-layout.scss ./resources/sass/themes/semi-dark-layout.scss ./resources/sass/pages/aggrid.scss ./resources/sass/pages/app-chat.scss ./resources/sass/pages/app-ecommerce-details.scss ./resources/sass/pages/app-ecommerce-shop.scss ./resources/sass/pages/app-email.scss ./resources/sass/pages/app-file-manager.scss ./resources/sass/pages/app-todo.scss ./resources/sass/pages/app-user.scss ./resources/sass/pages/authentication.scss ./resources/sass/pages/card-analytics.scss ./resources/sass/pages/colors.scss ./resources/sass/pages/coming-soon.scss ./resources/sass/pages/dashboard-analytics.scss ./resources/sass/pages/data-list-view.scss ./resources/sass/pages/error.scss ./resources/sass/pages/faq.scss ./resources/sass/pages/invoice.scss ./resources/sass/pages/knowledge-base.scss ./resources/sass/pages/pricing.scss ./resources/sass/pages/register.scss ./resources/sass/pages/search.scss ./resources/sass/pages/timeline.scss ./resources/sass/pages/users.scss ./resources/sass/core/colors/palette-gradient.scss ./resources/sass/core/colors/palette-noui.scss ./resources/sass/core/colors/palette-variables.scss ./resources/sass/core/menu/menu-types/horizontal-menu.scss ./resources/sass/core/menu/menu-types/vertical-menu.scss ./resources/sass/core/menu/menu-types/vertical-overlay-menu.scss ./resources/sass/core/mixins/alert.scss ./resources/sass/core/mixins/hex2rgb.scss ./resources/sass/core/mixins/main-menu-mixin.scss ./resources/sass/core/mixins/transitions.scss ./resources/sass/bootstrap.scss ./resources/sass/bootstrap-extended.scss ./resources/sass/colors.scss ./resources/sass/components.scss ./resources/sass/custom-rtl.scss ./resources/sass/custom-laravel.scss ***!
  \*************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

__webpack_require__(/*! C:\xampp\htdocs\fe-datn\resources\js\core\app-menu.js */"./resources/js/core/app-menu.js");
__webpack_require__(/*! C:\xampp\htdocs\fe-datn\resources\sass\plugins\animate\animate.scss */"./resources/sass/plugins/animate/animate.scss");
__webpack_require__(/*! C:\xampp\htdocs\fe-datn\resources\sass\plugins\calendars\fullcalendar.scss */"./resources/sass/plugins/calendars/fullcalendar.scss");
__webpack_require__(/*! C:\xampp\htdocs\fe-datn\resources\sass\plugins\extensions\context-menu.scss */"./resources/sass/plugins/extensions/context-menu.scss");
__webpack_require__(/*! C:\xampp\htdocs\fe-datn\resources\sass\plugins\extensions\drag-and-drop.scss */"./resources/sass/plugins/extensions/drag-and-drop.scss");
__webpack_require__(/*! C:\xampp\htdocs\fe-datn\resources\sass\plugins\extensions\media-plyr.scss */"./resources/sass/plugins/extensions/media-plyr.scss");
__webpack_require__(/*! C:\xampp\htdocs\fe-datn\resources\sass\plugins\extensions\noui-slider.scss */"./resources/sass/plugins/extensions/noui-slider.scss");
__webpack_require__(/*! C:\xampp\htdocs\fe-datn\resources\sass\plugins\extensions\swiper.scss */"./resources/sass/plugins/extensions/swiper.scss");
__webpack_require__(/*! C:\xampp\htdocs\fe-datn\resources\sass\plugins\extensions\toastr.scss */"./resources/sass/plugins/extensions/toastr.scss");
__webpack_require__(/*! C:\xampp\htdocs\fe-datn\resources\sass\plugins\file-uploaders\dropzone.scss */"./resources/sass/plugins/file-uploaders/dropzone.scss");
__webpack_require__(/*! C:\xampp\htdocs\fe-datn\resources\sass\plugins\forms\extended\typeahed.scss */"./resources/sass/plugins/forms/extended/typeahed.scss");
__webpack_require__(/*! C:\xampp\htdocs\fe-datn\resources\sass\plugins\forms\form-inputs-groups.scss */"./resources/sass/plugins/forms/form-inputs-groups.scss");
__webpack_require__(/*! C:\xampp\htdocs\fe-datn\resources\sass\plugins\forms\validation\form-validation.scss */"./resources/sass/plugins/forms/validation/form-validation.scss");
__webpack_require__(/*! C:\xampp\htdocs\fe-datn\resources\sass\plugins\forms\wizard.scss */"./resources/sass/plugins/forms/wizard.scss");
__webpack_require__(/*! C:\xampp\htdocs\fe-datn\resources\sass\plugins\loaders\animations\ball-beat.scss */"./resources/sass/plugins/loaders/animations/ball-beat.scss");
__webpack_require__(/*! C:\xampp\htdocs\fe-datn\resources\sass\plugins\loaders\animations\ball-clip-rotate-multiple.scss */"./resources/sass/plugins/loaders/animations/ball-clip-rotate-multiple.scss");
__webpack_require__(/*! C:\xampp\htdocs\fe-datn\resources\sass\plugins\loaders\animations\ball-clip-rotate-pulse.scss */"./resources/sass/plugins/loaders/animations/ball-clip-rotate-pulse.scss");
__webpack_require__(/*! C:\xampp\htdocs\fe-datn\resources\sass\plugins\loaders\animations\ball-clip-rotate.scss */"./resources/sass/plugins/loaders/animations/ball-clip-rotate.scss");
__webpack_require__(/*! C:\xampp\htdocs\fe-datn\resources\sass\plugins\loaders\animations\ball-grid-beat.scss */"./resources/sass/plugins/loaders/animations/ball-grid-beat.scss");
__webpack_require__(/*! C:\xampp\htdocs\fe-datn\resources\sass\plugins\loaders\animations\ball-grid-pulse.scss */"./resources/sass/plugins/loaders/animations/ball-grid-pulse.scss");
__webpack_require__(/*! C:\xampp\htdocs\fe-datn\resources\sass\plugins\loaders\animations\ball-pulse-rise.scss */"./resources/sass/plugins/loaders/animations/ball-pulse-rise.scss");
__webpack_require__(/*! C:\xampp\htdocs\fe-datn\resources\sass\plugins\loaders\animations\ball-pulse-round.scss */"./resources/sass/plugins/loaders/animations/ball-pulse-round.scss");
__webpack_require__(/*! C:\xampp\htdocs\fe-datn\resources\sass\plugins\loaders\animations\ball-pulse-sync.scss */"./resources/sass/plugins/loaders/animations/ball-pulse-sync.scss");
__webpack_require__(/*! C:\xampp\htdocs\fe-datn\resources\sass\plugins\loaders\animations\ball-pulse.scss */"./resources/sass/plugins/loaders/animations/ball-pulse.scss");
__webpack_require__(/*! C:\xampp\htdocs\fe-datn\resources\sass\plugins\loaders\animations\ball-rotate.scss */"./resources/sass/plugins/loaders/animations/ball-rotate.scss");
__webpack_require__(/*! C:\xampp\htdocs\fe-datn\resources\sass\plugins\loaders\animations\ball-scale-multiple.scss */"./resources/sass/plugins/loaders/animations/ball-scale-multiple.scss");
__webpack_require__(/*! C:\xampp\htdocs\fe-datn\resources\sass\plugins\loaders\animations\ball-scale-random.scss */"./resources/sass/plugins/loaders/animations/ball-scale-random.scss");
__webpack_require__(/*! C:\xampp\htdocs\fe-datn\resources\sass\plugins\loaders\animations\ball-scale-ripple-multiple.scss */"./resources/sass/plugins/loaders/animations/ball-scale-ripple-multiple.scss");
__webpack_require__(/*! C:\xampp\htdocs\fe-datn\resources\sass\plugins\loaders\animations\ball-scale-ripple.scss */"./resources/sass/plugins/loaders/animations/ball-scale-ripple.scss");
__webpack_require__(/*! C:\xampp\htdocs\fe-datn\resources\sass\plugins\loaders\animations\ball-scale.scss */"./resources/sass/plugins/loaders/animations/ball-scale.scss");
__webpack_require__(/*! C:\xampp\htdocs\fe-datn\resources\sass\plugins\loaders\animations\ball-spin-fade-loader.scss */"./resources/sass/plugins/loaders/animations/ball-spin-fade-loader.scss");
__webpack_require__(/*! C:\xampp\htdocs\fe-datn\resources\sass\plugins\loaders\animations\ball-spin-loader.scss */"./resources/sass/plugins/loaders/animations/ball-spin-loader.scss");
__webpack_require__(/*! C:\xampp\htdocs\fe-datn\resources\sass\plugins\loaders\animations\ball-triangle-trace.scss */"./resources/sass/plugins/loaders/animations/ball-triangle-trace.scss");
__webpack_require__(/*! C:\xampp\htdocs\fe-datn\resources\sass\plugins\loaders\animations\ball-zig-zag-deflect.scss */"./resources/sass/plugins/loaders/animations/ball-zig-zag-deflect.scss");
__webpack_require__(/*! C:\xampp\htdocs\fe-datn\resources\sass\plugins\loaders\animations\ball-zig-zag.scss */"./resources/sass/plugins/loaders/animations/ball-zig-zag.scss");
__webpack_require__(/*! C:\xampp\htdocs\fe-datn\resources\sass\plugins\loaders\animations\cube-transition.scss */"./resources/sass/plugins/loaders/animations/cube-transition.scss");
__webpack_require__(/*! C:\xampp\htdocs\fe-datn\resources\sass\plugins\loaders\animations\line-scale-pulse-out-rapid.scss */"./resources/sass/plugins/loaders/animations/line-scale-pulse-out-rapid.scss");
__webpack_require__(/*! C:\xampp\htdocs\fe-datn\resources\sass\plugins\loaders\animations\line-scale-pulse-out.scss */"./resources/sass/plugins/loaders/animations/line-scale-pulse-out.scss");
__webpack_require__(/*! C:\xampp\htdocs\fe-datn\resources\sass\plugins\loaders\animations\line-scale-random.scss */"./resources/sass/plugins/loaders/animations/line-scale-random.scss");
__webpack_require__(/*! C:\xampp\htdocs\fe-datn\resources\sass\plugins\loaders\animations\line-scale.scss */"./resources/sass/plugins/loaders/animations/line-scale.scss");
__webpack_require__(/*! C:\xampp\htdocs\fe-datn\resources\sass\plugins\loaders\animations\line-spin-fade-loader.scss */"./resources/sass/plugins/loaders/animations/line-spin-fade-loader.scss");
__webpack_require__(/*! C:\xampp\htdocs\fe-datn\resources\sass\plugins\loaders\animations\pacman.scss */"./resources/sass/plugins/loaders/animations/pacman.scss");
__webpack_require__(/*! C:\xampp\htdocs\fe-datn\resources\sass\plugins\loaders\animations\semi-circle-spin.scss */"./resources/sass/plugins/loaders/animations/semi-circle-spin.scss");
__webpack_require__(/*! C:\xampp\htdocs\fe-datn\resources\sass\plugins\loaders\animations\square-spin.scss */"./resources/sass/plugins/loaders/animations/square-spin.scss");
__webpack_require__(/*! C:\xampp\htdocs\fe-datn\resources\sass\plugins\loaders\animations\triangle-skew-spin.scss */"./resources/sass/plugins/loaders/animations/triangle-skew-spin.scss");
__webpack_require__(/*! C:\xampp\htdocs\fe-datn\resources\sass\plugins\loaders\loaders.scss */"./resources/sass/plugins/loaders/loaders.scss");
__webpack_require__(/*! C:\xampp\htdocs\fe-datn\resources\sass\plugins\pickers\bootstrap-datetimepicker-build.scss */"./resources/sass/plugins/pickers/bootstrap-datetimepicker-build.scss");
__webpack_require__(/*! C:\xampp\htdocs\fe-datn\resources\sass\plugins\tour\tour.scss */"./resources/sass/plugins/tour/tour.scss");
__webpack_require__(/*! C:\xampp\htdocs\fe-datn\resources\sass\plugins\ui\coming-soon.scss */"./resources/sass/plugins/ui/coming-soon.scss");
__webpack_require__(/*! C:\xampp\htdocs\fe-datn\resources\sass\themes\dark-layout.scss */"./resources/sass/themes/dark-layout.scss");
__webpack_require__(/*! C:\xampp\htdocs\fe-datn\resources\sass\themes\semi-dark-layout.scss */"./resources/sass/themes/semi-dark-layout.scss");
__webpack_require__(/*! C:\xampp\htdocs\fe-datn\resources\sass\pages\aggrid.scss */"./resources/sass/pages/aggrid.scss");
__webpack_require__(/*! C:\xampp\htdocs\fe-datn\resources\sass\pages\app-chat.scss */"./resources/sass/pages/app-chat.scss");
__webpack_require__(/*! C:\xampp\htdocs\fe-datn\resources\sass\pages\app-ecommerce-details.scss */"./resources/sass/pages/app-ecommerce-details.scss");
__webpack_require__(/*! C:\xampp\htdocs\fe-datn\resources\sass\pages\app-ecommerce-shop.scss */"./resources/sass/pages/app-ecommerce-shop.scss");
__webpack_require__(/*! C:\xampp\htdocs\fe-datn\resources\sass\pages\app-email.scss */"./resources/sass/pages/app-email.scss");
__webpack_require__(/*! C:\xampp\htdocs\fe-datn\resources\sass\pages\app-file-manager.scss */"./resources/sass/pages/app-file-manager.scss");
__webpack_require__(/*! C:\xampp\htdocs\fe-datn\resources\sass\pages\app-todo.scss */"./resources/sass/pages/app-todo.scss");
__webpack_require__(/*! C:\xampp\htdocs\fe-datn\resources\sass\pages\app-user.scss */"./resources/sass/pages/app-user.scss");
__webpack_require__(/*! C:\xampp\htdocs\fe-datn\resources\sass\pages\authentication.scss */"./resources/sass/pages/authentication.scss");
__webpack_require__(/*! C:\xampp\htdocs\fe-datn\resources\sass\pages\card-analytics.scss */"./resources/sass/pages/card-analytics.scss");
__webpack_require__(/*! C:\xampp\htdocs\fe-datn\resources\sass\pages\colors.scss */"./resources/sass/pages/colors.scss");
__webpack_require__(/*! C:\xampp\htdocs\fe-datn\resources\sass\pages\coming-soon.scss */"./resources/sass/pages/coming-soon.scss");
__webpack_require__(/*! C:\xampp\htdocs\fe-datn\resources\sass\pages\dashboard-analytics.scss */"./resources/sass/pages/dashboard-analytics.scss");
__webpack_require__(/*! C:\xampp\htdocs\fe-datn\resources\sass\pages\data-list-view.scss */"./resources/sass/pages/data-list-view.scss");
__webpack_require__(/*! C:\xampp\htdocs\fe-datn\resources\sass\pages\error.scss */"./resources/sass/pages/error.scss");
__webpack_require__(/*! C:\xampp\htdocs\fe-datn\resources\sass\pages\faq.scss */"./resources/sass/pages/faq.scss");
__webpack_require__(/*! C:\xampp\htdocs\fe-datn\resources\sass\pages\invoice.scss */"./resources/sass/pages/invoice.scss");
__webpack_require__(/*! C:\xampp\htdocs\fe-datn\resources\sass\pages\knowledge-base.scss */"./resources/sass/pages/knowledge-base.scss");
__webpack_require__(/*! C:\xampp\htdocs\fe-datn\resources\sass\pages\pricing.scss */"./resources/sass/pages/pricing.scss");
__webpack_require__(/*! C:\xampp\htdocs\fe-datn\resources\sass\pages\register.scss */"./resources/sass/pages/register.scss");
__webpack_require__(/*! C:\xampp\htdocs\fe-datn\resources\sass\pages\search.scss */"./resources/sass/pages/search.scss");
__webpack_require__(/*! C:\xampp\htdocs\fe-datn\resources\sass\pages\timeline.scss */"./resources/sass/pages/timeline.scss");
__webpack_require__(/*! C:\xampp\htdocs\fe-datn\resources\sass\pages\users.scss */"./resources/sass/pages/users.scss");
__webpack_require__(/*! C:\xampp\htdocs\fe-datn\resources\sass\core\colors\palette-gradient.scss */"./resources/sass/core/colors/palette-gradient.scss");
__webpack_require__(/*! C:\xampp\htdocs\fe-datn\resources\sass\core\colors\palette-noui.scss */"./resources/sass/core/colors/palette-noui.scss");
__webpack_require__(/*! C:\xampp\htdocs\fe-datn\resources\sass\core\colors\palette-variables.scss */"./resources/sass/core/colors/palette-variables.scss");
__webpack_require__(/*! C:\xampp\htdocs\fe-datn\resources\sass\core\menu\menu-types\horizontal-menu.scss */"./resources/sass/core/menu/menu-types/horizontal-menu.scss");
__webpack_require__(/*! C:\xampp\htdocs\fe-datn\resources\sass\core\menu\menu-types\vertical-menu.scss */"./resources/sass/core/menu/menu-types/vertical-menu.scss");
__webpack_require__(/*! C:\xampp\htdocs\fe-datn\resources\sass\core\menu\menu-types\vertical-overlay-menu.scss */"./resources/sass/core/menu/menu-types/vertical-overlay-menu.scss");
__webpack_require__(/*! C:\xampp\htdocs\fe-datn\resources\sass\core\mixins\alert.scss */"./resources/sass/core/mixins/alert.scss");
__webpack_require__(/*! C:\xampp\htdocs\fe-datn\resources\sass\core\mixins\hex2rgb.scss */"./resources/sass/core/mixins/hex2rgb.scss");
__webpack_require__(/*! C:\xampp\htdocs\fe-datn\resources\sass\core\mixins\main-menu-mixin.scss */"./resources/sass/core/mixins/main-menu-mixin.scss");
__webpack_require__(/*! C:\xampp\htdocs\fe-datn\resources\sass\core\mixins\transitions.scss */"./resources/sass/core/mixins/transitions.scss");
__webpack_require__(/*! C:\xampp\htdocs\fe-datn\resources\sass\bootstrap.scss */"./resources/sass/bootstrap.scss");
__webpack_require__(/*! C:\xampp\htdocs\fe-datn\resources\sass\bootstrap-extended.scss */"./resources/sass/bootstrap-extended.scss");
__webpack_require__(/*! C:\xampp\htdocs\fe-datn\resources\sass\colors.scss */"./resources/sass/colors.scss");
__webpack_require__(/*! C:\xampp\htdocs\fe-datn\resources\sass\components.scss */"./resources/sass/components.scss");
__webpack_require__(/*! C:\xampp\htdocs\fe-datn\resources\sass\custom-rtl.scss */"./resources/sass/custom-rtl.scss");
module.exports = __webpack_require__(/*! C:\xampp\htdocs\fe-datn\resources\sass\custom-laravel.scss */"./resources/sass/custom-laravel.scss");


/***/ })

/******/ });