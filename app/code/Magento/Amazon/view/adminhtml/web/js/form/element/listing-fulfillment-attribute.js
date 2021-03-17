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

            var fulfilledBySellerSelect = uiRegistry.get('index = fulfilled_by_seller_select');
            var fulfilledByAmazonSelect = uiRegistry.get('index = fulfilled_by_amazon_select');
            var fulfilledBySellerText = uiRegistry.get('index = fulfilled_by_seller_text');
            var fulfilledByAmazonText = uiRegistry.get('index = fulfilled_by_amazon_text');

            this.hideElement(fulfilledBySellerSelect);
            this.hideElement(fulfilledByAmazonSelect);
            this.hideElement(fulfilledBySellerText);
            this.hideElement(fulfilledByAmazonText);
            
            if (value) {
                this.obtainElementValue(value);
            }

            return this;
        },

        /**
         * Makes AJAX call to identify attribute type (select vs. input) and populates options if applicable
         */
        obtainElementValue: function (value) {

            var hideElement = this.hideElement;
            var showElement = this.showElement;

            // get form field elements
            var fulfilledBySellerText = uiRegistry.get('index = fulfilled_by_seller_text');
            var fulfilledByAmazonText = uiRegistry.get('index = fulfilled_by_amazon_text');
            var fulfilledBySellerSelect = uiRegistry.get('index = fulfilled_by_seller_select');
            var fulfilledByAmazonSelect = uiRegistry.get('index = fulfilled_by_amazon_select');

            var reloadurl = $('#listing-condition-attribute-url').attr('data-storeurl') + 'selectedValue/' + value + '/required/' + 'true';

            $.ajax({
                url: reloadurl,
                cache: false,
                dataType: "json",
                showLoader: true,
                type: "GET",
                success: function (response) {
                    // attribute of type select
                    if (response) {
                        // hide text form fields
                        hideElement(fulfilledBySellerText);
                        hideElement(fulfilledByAmazonText);
                        // show select form fields and apply options
                        showElement(fulfilledBySellerSelect);
                        fulfilledBySellerSelect.setOptions(response);
                        fulfilledBySellerSelect.value(fulfilledBySellerSelect.initialValue);
                        showElement(fulfilledByAmazonSelect);
                        fulfilledByAmazonSelect.setOptions(response);
                        fulfilledByAmazonSelect.value(fulfilledByAmazonSelect.initialValue);
                    } else { // attribute of type text
                        // show text form fields
                        showElement(fulfilledBySellerText);
                        fulfilledBySellerText.value('');
                        showElement(fulfilledByAmazonText);
                        fulfilledByAmazonText.value('');
                        // hide select form fields
                        hideElement(fulfilledBySellerSelect);
                        hideElement(fulfilledByAmazonSelect);
                    }
                }
            });
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