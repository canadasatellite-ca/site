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

            // if conditional variances are enabled
            if (value == 2) {
                this.showElement(uiRegistry.get('index = new_variance'));
                this.showElement(uiRegistry.get('index = refurbished_variance'));
                this.showElement(uiRegistry.get('index = usedlikenew_variance'));
                this.showElement(uiRegistry.get('index = usedverygood_variance'));
                this.showElement(uiRegistry.get('index = usedgood_variance'));
                this.showElement(uiRegistry.get('index = usedacceptable_variance'));
                this.showElement(uiRegistry.get('index = collectiblelikenew_variance'));
                this.showElement(uiRegistry.get('index = collectibleverygood_variance'));
                this.showElement(uiRegistry.get('index = collectiblegood_variance'));
                this.showElement(uiRegistry.get('index = collectibleacceptable_variance'));
            } else { // if rule type is catalog price rule
                this.hideElement(uiRegistry.get('index = new_variance'));
                this.hideElement(uiRegistry.get('index = refurbished_variance'));
                this.hideElement(uiRegistry.get('index = usedlikenew_variance'));
                this.hideElement(uiRegistry.get('index = usedverygood_variance'));
                this.hideElement(uiRegistry.get('index = usedgood_variance'));
                this.hideElement(uiRegistry.get('index = usedacceptable_variance'));
                this.hideElement(uiRegistry.get('index = collectiblelikenew_variance'));
                this.hideElement(uiRegistry.get('index = collectibleverygood_variance'));
                this.hideElement(uiRegistry.get('index = collectiblegood_variance'));
                this.hideElement(uiRegistry.get('index = collectibleacceptable_variance'));

                this.verifyValues(uiRegistry.get('index = refurbished_variance'));
                this.verifyValues(uiRegistry.get('index = usedlikenew_variance'));
                this.verifyValues(uiRegistry.get('index = usedverygood_variance'));
                this.verifyValues(uiRegistry.get('index = usedgood_variance'));
                this.verifyValues(uiRegistry.get('index = usedacceptable_variance'));
                this.verifyValues(uiRegistry.get('index = collectiblelikenew_variance'), true);
                this.verifyValues(uiRegistry.get('index = collectibleverygood_variance'), true);
                this.verifyValues(uiRegistry.get('index = collectiblegood_variance'), true);
                this.verifyValues(uiRegistry.get('index = collectibleacceptable_variance'), true);
            }

            return this;
        },

        /**
         * Verifies value is within the allowed range
         */
        verifyValues: function (element, collectible = false) {

              // if element exists
            if (typeof(element) != 'undefined' && element != null) {
                element.value(element.initialValue);
            }
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