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
            var sellerNotesElement = uiRegistry.get('index = seller_notes');
            var listConditionElement = uiRegistry.get('index = list_condition_field');
            // if condition is new or assigned by attribute
            if (value == 0) {
                this.hideElement(sellerNotesElement);
                this.showElement(listConditionElement);

                if (! listConditionElement.value()) {
                    this.hideAllGroups();
                } else {
                    this.showAllGroups();
                }
            } else if (value == 11) {
                this.hideElement(sellerNotesElement);
                this.hideElement(listConditionElement);
                this.hideAllGroups();
                this.resetSellerNotesValidation();
            } else {
                this.showElement(sellerNotesElement);
                this.hideElement(listConditionElement);
                this.hideAllGroups();
                this.resetSellerNotesValidation();
            }

            return this;
        },

        /**
         * Show all groups.
         */
        showAllGroups: function () {

            // form field groups
            var attributeNewGroup = uiRegistry.get('index = attribute_new_group');
            var attributeRefurbishedGroup = uiRegistry.get('index = attribute_refurbished_group');
            var attributeLikeNewGroup = uiRegistry.get('index = attribute_likenew_group');
            var attributeVeryGoodGroup = uiRegistry.get('index = attribute_verygood_group');
            var attributeGoodGroup = uiRegistry.get('index = attribute_good_group');
            var attributeAcceptableGroup = uiRegistry.get('index = attribute_acceptable_group');
            var collectibleLikeNewGroup = uiRegistry.get('index = collectible_likenew_group');
            var collectibleVeryGoodGroup = uiRegistry.get('index = collectible_verygood_group');
            var collectibleGoodGroup = uiRegistry.get('index = collectible_good_group');
            var collectibleAcceptableGroup = uiRegistry.get('index = collectible_acceptable_group');

            attributeNewGroup.visible(true);
            attributeRefurbishedGroup.visible(true);
            attributeLikeNewGroup.visible(true);
            attributeVeryGoodGroup.visible(true);
            attributeGoodGroup.visible(true);
            attributeAcceptableGroup.visible(true);
            collectibleLikeNewGroup.visible(true);
            collectibleVeryGoodGroup.visible(true);
            collectibleGoodGroup.visible(true);
            collectibleAcceptableGroup.visible(true);
        },

        /**
         * Resets seller notes validation
         */
        resetSellerNotesValidation: function () {

            // form field groups
            this.resetFormFieldValue(uiRegistry.get('index = seller_notes'));
            this.resetFormFieldValue(uiRegistry.get('index = seller_notes_refurbished'));
            this.resetFormFieldValue(uiRegistry.get('index = seller_notes_likenew'));
            this.resetFormFieldValue(uiRegistry.get('index = seller_notes_verygood'));
            this.resetFormFieldValue(uiRegistry.get('index = seller_notes_good'));
            this.resetFormFieldValue(uiRegistry.get('index = seller_notes_acceptable'));
            this.resetFormFieldValue(uiRegistry.get('index = seller_notes_collectible_likenew'));
            this.resetFormFieldValue(uiRegistry.get('index = seller_notes_collectible_verygood'));
            this.resetFormFieldValue(uiRegistry.get('index = seller_notes_collectible_good'));
            this.resetFormFieldValue(uiRegistry.get('index = seller_notes_collectible_acceptable'));
        },

        /**
         * Hide all groups.
         */
        hideAllGroups: function () {

            // form field groups
            var attributeNewGroup = uiRegistry.get('index = attribute_new_group');
            var attributeRefurbishedGroup = uiRegistry.get('index = attribute_refurbished_group');
            var attributeLikeNewGroup = uiRegistry.get('index = attribute_likenew_group');
            var attributeVeryGoodGroup = uiRegistry.get('index = attribute_verygood_group');
            var attributeGoodGroup = uiRegistry.get('index = attribute_good_group');
            var attributeAcceptableGroup = uiRegistry.get('index = attribute_acceptable_group');
            var collectibleLikeNewGroup = uiRegistry.get('index = collectible_likenew_group');
            var collectibleVeryGoodGroup = uiRegistry.get('index = collectible_verygood_group');
            var collectibleGoodGroup = uiRegistry.get('index = collectible_good_group');
            var collectibleAcceptableGroup = uiRegistry.get('index = collectible_acceptable_group');

            attributeNewGroup.visible(false);
            attributeRefurbishedGroup.visible(false);
            attributeLikeNewGroup.visible(false);
            attributeVeryGoodGroup.visible(false);
            attributeGoodGroup.visible(false);
            attributeAcceptableGroup.visible(false);
            collectibleLikeNewGroup.visible(false);
            collectibleVeryGoodGroup.visible(false);
            collectibleGoodGroup.visible(false);
            collectibleAcceptableGroup.visible(false);
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
