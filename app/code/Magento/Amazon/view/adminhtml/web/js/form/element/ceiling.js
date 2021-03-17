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
            var ceilingPriceMovement = uiRegistry.get('index = ceiling_price_movement');
            var ceilingSimpleAction = uiRegistry.get('index = ceiling_simple_action');
            var ceilingDiscountAmount = uiRegistry.get('index = ceiling_discount_amount');

            // if ceiling is active
            if (value) {
                this.enableElement(ceilingPriceMovement);
                var ceilingPriceMovementValue = this.getElementValue(ceilingPriceMovement);

                if (ceilingPriceMovementValue && ceilingPriceMovementValue > 0) {
                    this.enableElement(ceilingSimpleAction);
                    this.enableElement(ceilingDiscountAmount);

                    return this;
                }
            } else {
                this.disableElement(ceilingPriceMovement);
            }
            // if ceiling is not active
            this.disableElement(ceilingSimpleAction);
            this.disableElement(ceilingDiscountAmount);
            this.resetFormFieldValue(ceilingDiscountAmount);

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