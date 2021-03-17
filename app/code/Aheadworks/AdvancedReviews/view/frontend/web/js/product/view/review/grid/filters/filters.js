/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

define([
    'underscore',
    'Magento_Ui/js/grid/filters/filters'
], function (_, Filters) {
    'use strict';

    return Filters.extend({
        defaults: {
            template: 'Aheadworks_AdvancedReviews/product/view/review/grid/filters/filters',
            statefull: {},
            imports: {
                rows: '${ $.provider }:data.items'
            },
            listens: {
                filters: 'apply'
            },
        },

        /**
         * Initializes observable properties
         *
         * @returns {Filters} Chainable
         */
        initObservable: function () {
            this._super()
                .track({
                    rows: []
                });

            return this;
        },

        /**
         * {@inheritdoc}
         */
        initChips: function () {
            return this;
        },

        /**
         * Check if display filter
         *
         * @returns {Boolean}
         */
        isDisplay: function () {
            return !(_.size(this.applied) === 1 && this.rows.length === 0);
        }
    });
});
