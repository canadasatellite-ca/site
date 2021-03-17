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
            var floorSimpleAction = uiRegistry.get('index = floor_simple_action');
            var floorDiscountAmount = uiRegistry.get('index = floor_discount_amount');

            // if floor is active
            if (value == 0) {
                this.disableElement(floorSimpleAction);
                this.disableElement(floorDiscountAmount);
            } else { // if floor is not active
                this.enableElement(floorSimpleAction);
                this.enableElement(floorDiscountAmount);
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
            if (typeof(element) != 'undefined' && element !=null) {
                return element.value();
            }
            return false;
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