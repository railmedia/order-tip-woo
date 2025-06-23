/******/ (() => { // webpackBootstrap
/******/ 	"use strict";
/******/ 	var __webpack_modules__ = ({

/***/ "jquery":
/*!*************************!*\
  !*** external "jQuery" ***!
  \*************************/
/***/ ((module) => {

module.exports = jQuery;

/***/ })

/******/ 	});
/************************************************************************/
/******/ 	// The module cache
/******/ 	var __webpack_module_cache__ = {};
/******/ 	
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/ 		// Check if module is in cache
/******/ 		var cachedModule = __webpack_module_cache__[moduleId];
/******/ 		if (cachedModule !== undefined) {
/******/ 			return cachedModule.exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = __webpack_module_cache__[moduleId] = {
/******/ 			// no module.id needed
/******/ 			// no module.loaded needed
/******/ 			exports: {}
/******/ 		};
/******/ 	
/******/ 		// Execute the module function
/******/ 		__webpack_modules__[moduleId](module, module.exports, __webpack_require__);
/******/ 	
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/ 	
/************************************************************************/
/******/ 	/* webpack/runtime/compat get default export */
/******/ 	(() => {
/******/ 		// getDefaultExport function for compatibility with non-harmony modules
/******/ 		__webpack_require__.n = (module) => {
/******/ 			var getter = module && module.__esModule ?
/******/ 				() => (module['default']) :
/******/ 				() => (module);
/******/ 			__webpack_require__.d(getter, { a: getter });
/******/ 			return getter;
/******/ 		};
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/define property getters */
/******/ 	(() => {
/******/ 		// define getter functions for harmony exports
/******/ 		__webpack_require__.d = (exports, definition) => {
/******/ 			for(var key in definition) {
/******/ 				if(__webpack_require__.o(definition, key) && !__webpack_require__.o(exports, key)) {
/******/ 					Object.defineProperty(exports, key, { enumerable: true, get: definition[key] });
/******/ 				}
/******/ 			}
/******/ 		};
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/hasOwnProperty shorthand */
/******/ 	(() => {
/******/ 		__webpack_require__.o = (obj, prop) => (Object.prototype.hasOwnProperty.call(obj, prop))
/******/ 	})();
/******/ 	
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
var __webpack_exports__ = {};
// This entry need to be wrapped in an IIFE because it need to be isolated against other entry modules.
(() => {
var __webpack_exports__ = {};
/*!**************************************!*\
  !*** ./assets/src/js/front/front.js ***!
  \**************************************/
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var jquery__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! jquery */ "jquery");
/* harmony import */ var jquery__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(jquery__WEBPACK_IMPORTED_MODULE_0__);

(function ($) {
  const orderTipPlacedEvent = new CustomEvent('wootipplaced');
  const orderTipRemove = new CustomEvent('wootipremove');
  const WooOrderTip = {
    selectTip: trigger => {
      const applyTip = trigger.parent().find('.woo_order_tip_custom_text_field');
      jQuery('.woo_order_tip').removeClass('active');
      trigger.addClass('active');
      const tip = trigger.data('tip');
      if (tip == 'custom') {
        applyTip.toggle();
        jQuery('.woo_order_tip_apply').show();
      } else {
        WooOrderTip.applyTip(trigger);
      }
    },
    applyTip: trigger => {
      const container = trigger.parents('#wooot_order_tip_form'),
        tip_type = container.find('.woo_order_tip.active').data('tip-type'),
        tip_type_symbol = tip_type == '1' ? '%' : wootip.cs,
        tip_custom = container.find('.woo_order_tip.active').data('tip-custom'),
        tip_cash = container.find('.woo_order_tip.active').data('tip-cash'),
        tip_recurring = container.find('#woo_recurring_tip').is(':checked');
      let errors = 0,
        tip = container.find('.woo_order_tip.active').data('tip');
      tip = tip ? Math.abs(tip) : 0;
      console.log(tip);
      const tip_label = tip + tip_type_symbol;
      if (tip == 'custom') {
        tip = container.find('.woo_order_tip_custom_text').val();
        if (!tip || parseInt(tip) == 0) {
          container.find('.woo_order_tip_custom_text').css('border', '1px solid red').focus();
          errors = 1;
          return false;
        } else {
          container.find('.woo_order_tip_custom_text').css('border', 'initial');
          errors = 0;
        }
      }
      if (!errors) {
        jQuery('.woocommerce').block({
          message: ''
        });
        jQuery.ajax({
          type: "POST",
          url: wootip.au,
          dataType: 'json',
          data: {
            action: 'apply_tip',
            tip: tip,
            tip_type: tip_type,
            tip_label: tip_label,
            tip_custom: tip_custom,
            tip_cash: tip_cash,
            tip_recurring: tip_recurring,
            security: wootip.n
          },
          success: function (tipApplied) {
            if (tipApplied.status && 'success' === tipApplied.status) {
              if (tip_custom) {
                jQuery('.woo_order_tip[data-tip="custom"]').text(wootip.s.cut + ' (' + wootip.cs + tip.replace(',', wootip.ds).replace('.', wootip.ds) + ')');
              }
              jQuery('body').trigger('update_checkout');
              if (jQuery('button[name="update_cart"]').length) {
                jQuery('button[name="update_cart"]').attr('aria-disabled', false).removeAttr('disabled').trigger('click');
              }
              jQuery('.woo_order_tip_remove').show();
              jQuery('.woo_order_tip_apply').hide();
              jQuery('.woo_order_tip_custom_text_field').hide();
              document.dispatchEvent(orderTipPlacedEvent);
              jQuery('.woocommerce').unblock();
            }
          }
        });
      }
    },
    removeTip: () => {
      if (wootip.eart == '1') {
        if (confirm(wootip.s.rtc) === true) {
          WooOrderTip.doRemoveTip();
        }
      } else {
        WooOrderTip.doRemoveTip();
      }
    },
    doRemoveTip: () => {
      jQuery('.woocommerce').block({
        message: ''
      });
      jQuery.ajax({
        type: "POST",
        url: wootip.au,
        dataType: 'html',
        data: {
          action: 'remove_tip',
          security: wootip.n2
        },
        success: function (tipRemoved) {
          if (tipRemoved == 'success') {
            document.dispatchEvent(orderTipRemove);
            jQuery('.woo_order_tip[data-tip="custom"]').text(wootip.s.cut);
            jQuery('body').trigger('update_checkout');
            jQuery('[name="update_cart"]').attr('aria-disabled', false).removeAttr('disabled').trigger('click');
            jQuery('.woocommerce').unblock();
            jQuery('.woo_order_tip_remove').hide();
            jQuery('.woo_order_tip').removeClass('active');
          }
        }
      });
    }
  };
  jQuery(function () {
    jQuery('body').on('click', '.woo_order_tip', function (evt) {
      evt.preventDefault();
      WooOrderTip.selectTip(jQuery(this));
    });
    jQuery('.woo_order_tip_custom_text').on('keypress', function (evt) {
      if (evt.which == 13) {
        evt.preventDefault();
        return false;
      }
    });
    jQuery('body').on('change', '.woo_order_tip_custom_text', function (evt) {
      jQuery(this).val(jQuery(this).val().replace(/[^0-9.,]/g, ''));
    });
    jQuery('body').on('click', '.woo_order_tip_apply', function (evt) {
      evt.preventDefault();
      WooOrderTip.applyTip(jQuery(this));
    });
    jQuery('body').on('change', '#woo_recurring_tip', function (evt) {
      evt.preventDefault();
      WooOrderTip.applyTip(jQuery(this));
    });
    jQuery('body').on('click', '.woo_order_tip_remove', function (evt) {
      evt.preventDefault();
      WooOrderTip.removeTip();
    });
  });
})(jQuery);
})();

// This entry need to be wrapped in an IIFE because it need to be isolated against other entry modules.
(() => {
/*!******************************************!*\
  !*** ./assets/src/scss/front/front.scss ***!
  \******************************************/
__webpack_require__.r(__webpack_exports__);
// extracted by mini-css-extract-plugin

})();

/******/ })()
;
//# sourceMappingURL=front.bundle.js.map