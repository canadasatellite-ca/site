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
            var autoSource = uiRegistry.get('index = auto_source');
            var autoMinimumFeedback = uiRegistry.get('index = auto_minimum_feedback');
            var autoFeedbackCount = uiRegistry.get('index = auto_feedback_count');
            var conditionInner = uiRegistry.get('index = condition_outer_fieldset');
            var priceActionOneFieldset = uiRegistry.get('index = price_action_one_fieldset');
            var priceActionTwoFieldset = uiRegistry.get('index = price_action_two_fieldset');
            var floorFieldset = uiRegistry.get('index = floor_fieldset');
            var floorDiscountAmount = uiRegistry.get('index = floor_discount_amount');
            var ceilingFieldset = uiRegistry.get('index = ceiling_fieldset');
            var ceilingDiscountAmount = uiRegistry.get('index = ceiling_discount_amount');
            var newVariance = uiRegistry.get('index = new_variance');
            var refurbishedVariance = uiRegistry.get('index = refurbished_variance');
            var usedlikenewVariance = uiRegistry.get('index = usedlikenew_variance');
            var usedverygoodVariance = uiRegistry.get('index = usedverygood_variance');
            var usedgoodVariance = uiRegistry.get('index = usedgood_variance');
            var usedacceptableVariance = uiRegistry.get('index = usedacceptable_variance');
            var collectiblelikenewVariance = uiRegistry.get('index = collectiblelikenew_variance');
            var collectibleverygoodVariance = uiRegistry.get('index = collectibleverygood_variance');
            var collectiblegoodVariance = uiRegistry.get('index = collectiblegood_variance');
            var collectibleacceptableVariance = uiRegistry.get('index = collectibleacceptable_variance');

            // if rule type is catalog price rule
            if (value == 0) {
                this.disableElement(autoSource);
                this.disableElement(autoMinimumFeedback);
                this.disableElement(autoFeedbackCount);
                this.hideFieldset(conditionInner);
                this.showFieldset(priceActionOneFieldset);
                this.hideFieldset(priceActionTwoFieldset);
                this.hideFieldset(floorFieldset);
                this.resetFormFieldValue(floorDiscountAmount);
                this.hideFieldset(ceilingFieldset);
                this.resetFormFieldValue(ceilingDiscountAmount);
                this.resetFormFieldValue(newVariance);
                this.resetFormFieldValue(refurbishedVariance);
                this.resetFormFieldValue(usedlikenewVariance);
                this.resetFormFieldValue(usedverygoodVariance);
                this.resetFormFieldValue(usedgoodVariance);
                this.resetFormFieldValue(usedacceptableVariance);
                this.resetFormFieldValue(collectiblelikenewVariance);
                this.resetFormFieldValue(collectibleverygoodVariance);
                this.resetFormFieldValue(collectiblegoodVariance);
                this.resetFormFieldValue(collectibleacceptableVariance);
            } else { // if rule type is intelligent repricer
                this.enableElement(autoSource);

                // if lowest price rule
                if (this.getElementValue(autoSource) == 1) {
                    this.enableElement(autoMinimumFeedback);
                    this.enableElement(autoFeedbackCount);
                    this.showFieldset(conditionInner);
                } else { // best buy box
                    this.disableElement(autoMinimumFeedback);
                    this.disableElement(autoFeedbackCount);
                    this.showFieldset(conditionInner);
                }
                this.hideFieldset(priceActionOneFieldset);
                this.showFieldset(priceActionTwoFieldset);
                this.showFieldset(floorFieldset);
                this.showFieldset(ceilingFieldset);
            }

            return this;
        },

        /**
         * Resets form field value
         */
        resetFormFieldValue: function (element) {

            // if element exists
            if (typeof(element) != 'undefined' && element != null) {
                element.value(element.initialValue);
            }
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
         * Enable element.
         */
        enableElement: function (element) {

            // if element exists
            if (typeof(element) != 'undefined' && element != null) {
                element.enable();
            }
        },

        /**
         * Show fieldset.
         */
        showFieldset: function (element) {

            // if element exists
            if (typeof(element) != 'undefined' && element != null) {
                element.visible(true);
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
        },
        
        /**
         * Disable element.
         */
        disableElement: function (element) {

            // if element exists
            if (typeof(element) != 'undefined' && element != null) {
                element.disable();
            }
        },

        /**
         * Hide fieldset.
         */
        hideFieldset: function (element) {

            // if element exists
            if (typeof(element) != 'undefined' && element != null) {
                element.visible(false);
            }
        }
    });
});