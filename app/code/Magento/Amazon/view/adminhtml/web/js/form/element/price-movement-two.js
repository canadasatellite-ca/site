/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
define([
    'jquery',
    'underscore',
    'uiRegistry',
    'Magento_Ui/js/form/element/select'
], function ($, _, uiRegistry, select) {
    'use strict';

    return select.extend({

        /**
         * On value change handler.
         *
         * @param {String} value
         */
        onUpdate: function (value) {

            // form field objects
            var simpleActionTwo = uiRegistry.get('index = simple_action_two');
            var discountAmountTwo = uiRegistry.get('index = discount_amount_two');

            // match type
            if (value == 0) {
                this.hideElement(simpleActionTwo);
                this.hideElement(discountAmountTwo);
            } else { // all others
                this.showElement(simpleActionTwo);
                this.showElement(discountAmountTwo);
            }

            return this;
        },

        /**
         * Get element value.
         *
         * @return boolean
         */
        getElementValue: function (element) {

            // if element exists
            if (typeof(element) != 'undefined' && element != null) {
                return element.value();
            }
            return false;
        },

        /**
         * Show element.
         */
        showElement: function (element) {

            // if element exists
            if (typeof(element) != 'undefined' && element != null) {
                element.show();
            }
        },

        /**
         * Hide element.
         */
        hideElement: function (element) {

            // if element exists
            if (typeof(element) != 'undefined' && element != null) {
                element.hide();
            }
        }
    });
});
