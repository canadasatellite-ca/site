/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
define([
    'underscore',
    'uiRegistry',
    'Magento_Ui/js/form/element/select'
], function (_, uiRegistry, select) {
    'use strict';

    return select.extend({

        /**
         * On value change handler.
         *
         * @param {String} value
         */
        onUpdate: function (value) {

            var priceConversionRateElement = uiRegistry.get('index = cc_rate');

            // if vat is enabled
            if (value === '1') {
                this.showElement(priceConversionRateElement);
            } else {
                this.hideElement(priceConversionRateElement);
            }

            return this;
        },

        /**
         * Show element.
         */
        showElement: function (element) {

            // if element exists
            if (typeof(element) !== 'undefined' && element !== null) {
                element.show();
            }
        },

        /**
         * Hide element.
         */
        hideElement: function (element) {

            // if element exists
            if (typeof(element) !== 'undefined' && element !== null) {
                element.hide();
            }
        }
    });
});
