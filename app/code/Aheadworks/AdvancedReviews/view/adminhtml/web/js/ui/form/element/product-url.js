/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

define([
    './label-url',
    'uiRegistry'
], function (LabelUrl, registry) {
    'use strict';

    return LabelUrl.extend({
        defaults: {
            imports: {
                selectedProductData: '${ $.provider }:data.product_selected'
            },
            exports: {
                selectedProductData: '${ $.provider }:data.product_selected'
            },
            listens: {
                selectedProductData: 'processSelectedProductData'
            },
            productIdFormFieldQuery: '${ $.ns }.${ $.ns }.review_details.product_id',
            selectProductFormFieldQuery: '${ $.ns }.${ $.ns }.review_details.select_product'
        },

        /**
         * Process selected product data
         */
        processSelectedProductData: function() {
            this.updateProductIdValue();
            this.hideSelectProductButton();
            this.updateProductName();
            this.visible(true);
        },

        /**
         * Update product name
         */
        updateProductName: function() {
            this.source.data[this.index] = this.getCurrentSelectedProductData().name;
        },

        /**
         * Set product_id value to hidden field
         */
        updateProductIdValue: function() {
            var productIdField = registry.get(this.productIdFormFieldQuery);
            
            productIdField.value(this.getCurrentSelectedProductData().entity_id);
        },

        /**
         * Hide "Select Product" button
         */
        hideSelectProductButton: function() {
            var selectProductField = registry.get(this.selectProductFormFieldQuery);
            
            selectProductField.hide();
        },

        /**
         * Get current selected product data
         * @returns {Array}
         */
        getCurrentSelectedProductData: function() {
            var preparedData = [];

            if (this.selectedProductData.length) {
                preparedData = this.selectedProductData[0];
            }
            return preparedData;
        }
    });
});
