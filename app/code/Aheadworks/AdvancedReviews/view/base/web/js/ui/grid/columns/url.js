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
            bodyTmpl: 'Aheadworks_AdvancedReviews/ui/grid/cells/url',
            textBeforeUrl: ''
        },

        /**
         * Retrieve label for column
         *
         * @returns {String}
         */
        getLabel: function(row) {
            return row[this.index + '_label'];
        },

        /**
         * Retrieve url for column
         *
         * @returns {String}
         */
        getUrl: function(row) {
            return row[this.index + '_url'];
        }
    });
});
