/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

define([
    'jquery',
    'underscore',
    'mageUtils',
    'uiElement'
], function ($, _, utils, Component) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'Aheadworks_AdvancedReviews/product/view/review/summary/detailed-summary',
            imports: {
                detailedSummaryData: '${ $.provider }:data',
                filters: 'aw_advanced_reviews_product_review_listing.'
                    + 'aw_advanced_reviews_product_review_listing_data_source:params.filters',
                rating: 'aw_advanced_reviews_product_review_listing.'
                    + 'aw_advanced_reviews_product_review_listing.listing_top_toolbar.listing_filters.rating:value'
            },
            exports: {
                applied: 'aw_advanced_reviews_product_review_listing.'
                    + 'aw_advanced_reviews_product_review_listing_data_source:params.filters',
                rating: 'aw_advanced_reviews_product_review_listing.'
                    + 'aw_advanced_reviews_product_review_listing.listing_top_toolbar.listing_filters.rating:value'
            }
        },

        /**
         * Initializes observable properties
         *
         * @returns {DetailedSummary} Chainable
         */
        initObservable: function () {
            this._super()
                .track({
                    rating: []
                });

            return this;
        },

        /**
         * Check if selected value
         *
         * @param {Number} value
         * @returns {Boolean}
         */
        isSelected: function (value) {
            return parseInt(this.rating) === parseInt(value);
        },

        /**
         * Sets filters data to the applied state
         *
         * @param {Number} value
         * @returns {DetailedSummary} Chainable
         */
        apply: function (value) {
            var filters,
                addToFilter = {rating: String(value)};

            if (_.isMatch(this.filters, addToFilter)) {
                delete this.filters['rating'];
                addToFilter = {};
                value = '';
            }

            filters = utils.extend({}, this.filters, addToFilter);
            this.set('rating', value);
            this.set('applied', filters);

            return this;
        },

        /**
         * Retrieve detailed summary data
         *
         * @returns {Object}
         */
        getDetailedSummaryData: function () {
            return this.detailedSummaryData;
        },

        /**
         * Retrieve label from summary data for specific rating value
         *
         * @param {Object} dataRow
         * @returns {Object}
         */
        getRatingLabel: function (dataRow) {
            return dataRow.label;
        },

        /**
         * Retrieve reviews count from summary data for specific rating value
         *
         * @param {Object} dataRow
         * @returns {Object}
         */
        getRatingReviewsCount: function (dataRow) {
            return dataRow.reviews_count;
        },

        /**
         * Retrieve reviews percent from summary data for specific rating value
         *
         * @param {Object} dataRow
         * @returns {Object}
         */
        getRatingReviewsPercent: function (dataRow) {
            return dataRow.reviews_percent;
        }
    });
});
