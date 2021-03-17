/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

define([
    'Aheadworks_AdvancedReviews/js/ui/grid/columns/column'
], function (Column) {
    'use strict';

    return Column.extend({
        defaults: {
            bodyTmpl: 'Aheadworks_AdvancedReviews/ui/grid/cells/rating',
            viewTmpl: 'Aheadworks_AdvancedReviews/review/rating/view'
        },

        /**
         * Retrieve rating value
         *
         * @param {Object} record
         * @returns {String}
         */
        getRatingValue: function (record) {
            return this.getLabel(record);
        },

        /**
         * Retrieve rating title
         *
         * @param {Object} record
         * @returns {String}
         */
        getRatingTitle: function(record) {
            return record[this.index + '_title'];
        }
    });
});
