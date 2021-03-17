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
         * On value change handler
         *
         * @param {String} value
         */
        onUpdate: function (value) {

            var tierIsActive = uiRegistry.get('index = tier_is_active');
            var qtyPriceOne = uiRegistry.get('index = qty_price_one');
            var lowerBoundOne = uiRegistry.get('index = lower_bound_one');
            var qtyPriceTwo = uiRegistry.get('index = qty_price_two');
            var lowerBoundTwo = uiRegistry.get('index = lower_bound_two');
            var qtyPriceThree = uiRegistry.get('index = qty_price_three');
            var lowerBoundThree = uiRegistry.get('index = lower_bound_three');
            var qtyPriceFour = uiRegistry.get('index = qty_price_four');
            var lowerBoundFour = uiRegistry.get('index = lower_bound_four');
            var qtyPriceFive = uiRegistry.get('index = qty_price_five');
            var lowerBoundFive = uiRegistry.get('index = lower_bound_five');

            // import amazon orders enabled
            if (value == 1) {
                this.enableElement(qtyPriceOne);
                this.enableElement(lowerBoundOne);
                this.enableElement(qtyPriceTwo);
                this.enableElement(lowerBoundTwo);
                this.enableElement(qtyPriceThree);
                this.enableElement(lowerBoundThree);
                this.enableElement(qtyPriceFour);
                this.enableElement(lowerBoundFour);
                this.enableElement(qtyPriceFive);
                this.enableElement(lowerBoundFive);
            } else {
                this.disableElement(qtyPriceOne);
                this.disableElement(lowerBoundOne);
                this.disableElement(qtyPriceTwo);
                this.disableElement(lowerBoundTwo);
                this.disableElement(qtyPriceThree);
                this.disableElement(lowerBoundThree);
                this.disableElement(qtyPriceFour);
                this.disableElement(lowerBoundFour);
                this.disableElement(qtyPriceFive);
                this.disableElement(lowerBoundFive);
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