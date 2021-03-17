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

            var thirdpartySku = uiRegistry.get('index = thirdparty_sku_field');
            var thirdpartyAsin = uiRegistry.get('index = thirdparty_asin_field');

            if (value == 1) {
                this.enableElement(thirdpartySku);
                this.enableElement(thirdpartyAsin);
            } else {
                this.disableElement(thirdpartySku);
                this.disableElement(thirdpartyAsin);
            }

            return this;
        },

        /**
         * Enable element.
         */
        enableElement: function (element) {

            // if element exists
            if (typeof(element) != 'undefined' && element != null) {
                element.enable();
            }
        },

        /**
         * Disable element.
         */
        disableElement: function (element) {

            // if element exists
            if (typeof(element) != 'undefined' && element != null) {
                element.disable();
            }
        }
    });
});