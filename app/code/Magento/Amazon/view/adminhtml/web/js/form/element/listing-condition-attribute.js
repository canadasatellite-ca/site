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

            if (! value) {
                this.hideAllGroups();
            } else {
                this.showAllGroups();
                this.obtainElementValue(value);
            }

            return this;
        },

        /**
         * Makes AJAX call to identify attribute type (select vs. input) and populates options if applicable
         */
        obtainElementValue: function (value, flag = false) {

            var hideElement = this.hideElement;
            var showElement = this.showElement;
            var updateValues = this.updateValues;

            // form field objects
            var attributeNewSelect = uiRegistry.get('index = list_condition_new_select');
            var attributeNewText = uiRegistry.get('index = list_condition_new_text');
            var attributeRefurbishedSelect = uiRegistry.get('index = list_condition_refurbished_select');
            var attributeRefurbishedText = uiRegistry.get('index = list_condition_refurbished_text');
            var attributeLikeNewSelect = uiRegistry.get('index = list_condition_likenew_select');
            var attributeLikeNewText = uiRegistry.get('index = list_condition_likenew_text');
            var attributeVeryGoodSelect = uiRegistry.get('index = list_condition_verygood_select');
            var attributeVeryGoodText = uiRegistry.get('index = list_condition_verygood_text');
            var attributeGoodSelect = uiRegistry.get('index = list_condition_good_select');
            var attributeGoodText = uiRegistry.get('index = list_condition_good_text');
            var attributeAcceptableSelect = uiRegistry.get('index = list_condition_acceptable_select');
            var attributeAcceptableText = uiRegistry.get('index = list_condition_acceptable_text');
            var collectibleLikeNewSelect = uiRegistry.get('index = list_condition_collectible_likenew_select');
            var collectibleLikeNewText = uiRegistry.get('index = list_condition_collectible_likenew_text');
            var collectibleVeryGoodSelect = uiRegistry.get('index = list_condition_collectible_verygood_select');
            var collectibleVeryGoodText = uiRegistry.get('index = list_condition_collectible_verygood_text');
            var collectibleGoodSelect = uiRegistry.get('index = list_condition_collectible_good_select');
            var collectibleGoodText = uiRegistry.get('index = list_condition_collectible_good_text');
            var collectibleAcceptableSelect = uiRegistry.get('index = list_condition_collectible_acceptable_select');
            var collectibleAcceptableText = uiRegistry.get('index = list_condition_collectible_acceptable_text');

            var reloadurl = $('#listing-condition-attribute-url').attr('data-storeurl') + 'selectedValue/' + value + '/required/' + 'false';

            $.ajax({
                url: reloadurl,
                cache: false,
                dataType: "json",
                showLoader: true,
                type: "GET",
                success: function (response) {
                    // attribute of type select
                    if (response) {
                        showElement(attributeNewSelect);
                        attributeNewSelect.setOptions(response);
                        hideElement(attributeNewText);
                        showElement(attributeRefurbishedSelect);
                        attributeRefurbishedSelect.setOptions(response);
                        hideElement(attributeRefurbishedText);
                        showElement(attributeLikeNewSelect);
                        attributeLikeNewSelect.setOptions(response);
                        hideElement(attributeLikeNewText);
                        showElement(attributeVeryGoodSelect);
                        attributeVeryGoodSelect.setOptions(response);
                        hideElement(attributeVeryGoodText);
                        showElement(attributeGoodSelect);
                        attributeGoodSelect.setOptions(response);
                        hideElement(attributeGoodText);
                        showElement(attributeAcceptableSelect);
                        attributeAcceptableSelect.setOptions(response);
                        hideElement(attributeAcceptableText);
                        showElement(collectibleLikeNewSelect);
                        collectibleLikeNewSelect.setOptions(response);
                        hideElement(collectibleLikeNewText);
                        showElement(collectibleVeryGoodSelect);
                        collectibleVeryGoodSelect.setOptions(response);
                        hideElement(collectibleVeryGoodText);
                        showElement(collectibleGoodSelect);
                        collectibleGoodSelect.setOptions(response);
                        hideElement(collectibleGoodText);
                        showElement(collectibleAcceptableSelect);
                        collectibleAcceptableSelect.setOptions(response);
                        hideElement(collectibleAcceptableText);
                        // if being initialized, assign text elements to null
                        if (flag) {
                            updateValues(attributeNewSelect, attributeNewText);
                            updateValues(attributeRefurbishedSelect, attributeRefurbishedText);
                            updateValues(attributeLikeNewSelect, attributeLikeNewText);
                            updateValues(attributeVeryGoodSelect, attributeVeryGoodText);
                            updateValues(attributeGoodSelect, attributeGoodText);
                            updateValues(attributeAcceptableSelect, attributeAcceptableText);
                            updateValues(collectibleLikeNewSelect, collectibleLikeNewText);
                            updateValues(collectibleVeryGoodSelect, collectibleVeryGoodText);
                            updateValues(collectibleGoodSelect, collectibleGoodText);
                            updateValues(collectibleAcceptableSelect, collectibleAcceptableText);
                        }
                    } else { // attribute of type text
                        hideElement(attributeNewSelect);
                        showElement(attributeNewText);
                        attributeNewText.value('');
                        hideElement(attributeRefurbishedSelect);
                        showElement(attributeRefurbishedText);
                        attributeRefurbishedText.value('');
                        hideElement(attributeLikeNewSelect);
                        showElement(attributeLikeNewText);
                        attributeLikeNewText.value('');
                        hideElement(attributeVeryGoodSelect);
                        showElement(attributeVeryGoodText);
                        attributeVeryGoodText.value('');
                        hideElement(attributeGoodSelect);
                        showElement(attributeGoodText);
                        attributeGoodText.value('');
                        hideElement(attributeAcceptableSelect);
                        showElement(attributeAcceptableText);
                        attributeAcceptableText.value('');
                        hideElement(collectibleLikeNewSelect);
                        showElement(collectibleLikeNewText);
                        collectibleLikeNewText.value('');
                        hideElement(collectibleVeryGoodSelect);
                        showElement(collectibleVeryGoodText);
                        collectibleVeryGoodText.value('');
                        hideElement(collectibleGoodSelect);
                        showElement(collectibleGoodText);
                        collectibleGoodText.value('');
                        hideElement(collectibleAcceptableSelect);
                        showElement(collectibleAcceptableText);
                        collectibleAcceptableText.value('');
                    }
                }
            });
        },

        /**
         * Get element value.
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
        },

        /**
         * Initializes select type current and initial values and empties text type value.
         */
        updateValues: function (targetElement, sourceElement) {

            // if element exists
            if (typeof(targetElement) != 'undefined' && targetElement != null) {
                // if element exists
                if (typeof(sourceElement) != 'undefined' && sourceElement != null) {
                    targetElement.initialValue = sourceElement.value();
                    targetElement.value(sourceElement.value());
                    sourceElement.initialValue = '';
                    sourceElement.value('');
                }
            }
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
    });
});
