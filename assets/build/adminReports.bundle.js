/******/ (() => { // webpackBootstrap
/******/ 	"use strict";
/******/ 	var __webpack_modules__ = ({

/***/ "./node_modules/jquery-ui/dist/themes/base/jquery-ui.min.css":
/*!*******************************************************************!*\
  !*** ./node_modules/jquery-ui/dist/themes/base/jquery-ui.min.css ***!
  \*******************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
// extracted by mini-css-extract-plugin


/***/ }),

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
/*!**********************************************!*\
  !*** ./assets/src/js/admin/admin-reports.js ***!
  \**********************************************/
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var jquery__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! jquery */ "jquery");
/* harmony import */ var jquery__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(jquery__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _node_modules_jquery_ui_dist_themes_base_jquery_ui_min_css__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../../../../../../node_modules/jquery-ui/dist/themes/base/jquery-ui.min.css */ "./node_modules/jquery-ui/dist/themes/base/jquery-ui.min.css");


(function ($) {
  const WOOTAdminReports = {
    init: () => {
      const startYear = wootipar.fod ? wootipar.fod : wootipar.cuy;
      jQuery('#wot-reports-date-from, #wot-reports-date-to').datepicker({
        dateFormat: 'yy-mm-dd',
        changeMonth: true,
        changeYear: true,
        yearRange: startYear + ':' + wootipar.cuy
      });
      jQuery('p.submit').remove();
    },
    getRowData: row => {
      const lastRow = jQuery('#woo-order-tip-reports-table tbody tr:last-child').index();
      return `<tr data-orderid="${row['orderId']}">
                    <td style="width: 30px;">
                        <input class="select-order" type="checkbox" />
                    </td>
                    <td class="row-count" data-value="${lastRow + 1}">
                        ${lastRow + 1}
                    </td>
                    <td class="order-id" data-value="${row['orderId']}">
                        <a href="${row['orderLink']}" target="_blank" rel="noopener noreferrer">${row['orderId']}</a>
                    </td>
                    <td class="order-status-col" data-value="${row['orderStatus']}">
                        ${row['orderStatus']}
                    </td>
                    <td class="customer-name" data-value="${row['customer']}">
                        ${row['customer']}
                    </td>
                    <td class="fee-type" data-value="${row['feeType']}">
                        ${row['feeType']}
                    </td>
                    <td class="order-value" data-value="${row['feeValue']}">
                        ${row['feePrice']}
                    </td>
                    <td class="order-date" data-value="${row['orderDate']}">
                        ${row['orderDate']}
                    </td>
                </tr>`;
    },
    getFilteredTipOrders: paged => {
      const dateFrom = jQuery('#wot-reports-date-from'),
        dateTo = jQuery('#wot-reports-date-to'),
        status = jQuery('#wot-reports-order-status'),
        // feeNames = jQuery('#wot-reports-order-fees input:checked'),
        feeNames = jQuery('#wot-reports-order-fees'),
        errorMsg = jQuery('#woo-order-tip-reports-errors'),
        containerRes = jQuery('#woo-order-tip-reports-table tbody'),
        totalRes = jQuery('#woo-order-tip-reports-table tfoot #woo-order-tip-reports-total'),
        fromRes = jQuery('#displaying-from-to #displaying-from'),
        toRes = jQuery('#displaying-from-to #displaying-to'),
        preloader = jQuery('#woo-order-tip-loading'),
        errors = WOOTAdminReports.validateDates();
      if (!errors) {
        preloader.show();
        errorMsg.empty();
        jQuery.ajax({
          type: "POST",
          url: wootipar.aju,
          dataType: 'json',
          data: {
            action: 'display_orders_list_reports_ajax',
            from: dateFrom.val(),
            to: dateTo.val(),
            feeNames: feeNames.val(),
            status: status.val(),
            paged: paged,
            security: wootipar.ajn
          },
          success: function (data) {
            if ('error' === data.status) {
              jQuery.each(data.errors, function (i, err) {
                errormsg.append('<p>' + err + '</p>');
              });
            } else {
              fromRes.text(data.after_date);
              toRes.text(data.before_date);
              totalRes.empty().text(data.total);
              if (1 == paged) {
                containerRes.empty().html(data.result);
              }
              if (paged >= 1) {
                for (const key in data.result) {
                  if (!jQuery(`#woo-order-tip-reports-table tbody tr[data-orderid="${data.result[key]['orderId']}"]`).length) {
                    const rowData = WOOTAdminReports.getRowData(data.result[key]);
                    containerRes.append(rowData);
                  }
                }
              }
              WOOTAdminReports.resetRowsData();
              if (100 === data.order_ids_count) {
                WOOTAdminReports.getFilteredTipOrders(paged + 1);
              } else {
                preloader.hide();
              }
            }
          },
          error: function (xhr, status, error) {
            errormsg.append('<p>' + error + '</p>');
            WOOTAdminReports.resetRowsData();
            preloader.hide();
          }
        });
      }
    },
    resetRowsData: () => {
      const table = jQuery('#woo-order-tip-reports-table');
      if (table.find('tbody tr').length) {
        let total = 0;
        table.find('tbody tr').each(function (idx, row) {
          jQuery(row).find('td.row-count').text(idx + 1);
          const value = jQuery(row).find('td.order-value').attr('data-value');
          if (value) {
            total += parseFloat(value);
          }
        });
        jQuery('#woo-order-tip-reports-total').text(total.toFixed(2));
      }
    },
    onFiltersChange: () => {
      const dateFrom = jQuery('#wot-reports-date-from'),
        dateTo = jQuery('#wot-reports-date-to'),
        // feeNames = jQuery('#wot-reports-order-fees input:checked'),
        feeNames = jQuery('#wot-reports-order-fees'),
        expButton = jQuery('#wot-export-csv'),
        errors = WOOTAdminReports.validateDates();
      if (!errors) {
        let url = wootipar.exn;
        url = url.replace('fromDate', dateFrom.val());
        url = url.replace('toDate', dateTo.val());
        url = url.replace('Fees', feeNames.val().join(','));
        expButton.removeAttr('disabled').attr('href', url);
      } else {
        expButton.attr('disabled', 'disabled').attr('href', '#!');
      }
    },
    validateDates: () => {
      const dateFrom = jQuery('#wot-reports-date-from'),
        dateTo = jQuery('#wot-reports-date-to');
      let errors = 0;
      if (!dateFrom.val()) {
        dateFrom.css('border', '1px solid red').trigger('focus');
        errors = 1;
        return errors;
      } else {
        dateFrom.css('border', '1px solid #7e8993');
        errors = 0;
      }
      if (!dateTo.val()) {
        dateTo.css('border', '1px solid red').trigger('focus');
        errors = 1;
        return errors;
      } else {
        dateTo.css('border', '1px solid #7e8993');
        errors = 0;
      }
      return errors;
    },
    selectAllOrders: trigger => {
      jQuery('#woo-order-tip-reports-table input.select-order, #woo-order-tip-reports-table input.select-all').prop('checked', trigger.prop('checked'));
    },
    onExportCsvClick: trigger => {
      if (jQuery('#woo-order-tip-reports-table input.select-order:checked').length <= 0) {
        alert('Please select at least one order to export');
        return false;
      }
      const errorMsg = jQuery('#woo-order-tip-reports-errors'),
        preloader = jQuery('#woo-order-tip-loading');
      let orders = [];
      jQuery('#woo-order-tip-reports-table input.select-order:checked').each(function (idx, order) {
        let orderRow = jQuery(order).parents('tr');
        orders.push({
          orderId: orderRow.find('td.order-id').attr('data-value'),
          feeName: orderRow.find('td.fee-type').attr('data-value'),
          feeValue: orderRow.find('td.order-value').attr('data-value'),
          orderDate: orderRow.find('td.order-date').attr('data-value')
        });
      });
      preloader.show();
      errorMsg.empty();
      jQuery.ajax({
        type: "POST",
        url: wootipar.aju,
        dataType: 'json',
        data: {
          action: 'export_tips_to_csv_ajax',
          data: orders,
          security: wootipar.erc
        },
        success: function (data) {
          preloader.hide();
          if (data.errors.length) {
            jQuery.each(data.errors, function (i, err) {
              errorMsg.append('<p>' + err + '</p>');
            });
            return false;
          }
          if (data.fileUrl) {
            window.open(data.fileUrl);
          }
          if (data.filePath) {
            setTimeout(() => {
              jQuery.ajax({
                type: "POST",
                url: wootipar.aju,
                dataType: 'json',
                data: {
                  action: 'delete_exported_csv_file_ajax',
                  filePath: data.filePath,
                  security: wootipar.def
                },
                success: function (data) {}
              });
            }, 500);
          }
        },
        error: function (xhr, status, error) {
          errormsg.append('<p>' + error + '</p>');
          preloader.hide();
        }
      });
    }
  };
  jQuery(function () {
    WOOTAdminReports.init();
    jQuery('body').on('click', '#wot-set-filters', function (evt) {
      evt.preventDefault();
      WOOTAdminReports.getFilteredTipOrders(1);
    });
    jQuery('body').on('change', '#wot-reports-date-from, #wot-reports-date-to, #wot-reports-order-fees', function (evt) {
      WOOTAdminReports.onFiltersChange();
    });
    jQuery('body').on('click', '#woo-order-tip-reports-table input.select-all', function (evt) {
      WOOTAdminReports.selectAllOrders(jQuery(this));
    });
    jQuery('body').on('click', '#wot-export-csv', function (evt) {
      evt.preventDefault();
      WOOTAdminReports.onExportCsvClick(jQuery(this));
    });
  });
})(jQuery);
})();

// This entry need to be wrapped in an IIFE because it need to be isolated against other entry modules.
(() => {
/*!**************************************************!*\
  !*** ./assets/src/scss/admin/admin-reports.scss ***!
  \**************************************************/
__webpack_require__.r(__webpack_exports__);
// extracted by mini-css-extract-plugin

})();

/******/ })()
;
//# sourceMappingURL=adminReports.bundle.js.map