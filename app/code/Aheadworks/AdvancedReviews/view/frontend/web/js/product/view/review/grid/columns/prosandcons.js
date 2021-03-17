/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

define([
    'Aheadworks_AdvancedReviews/js/ui/grid/columns/column'
], function (Column) {
    'use strict';

    return Column.extend({

        /**
         * Returns true or false if field has any data or field is empty.
         *
         *@param {Object} record.
         * @returns {Boolean}
         */
        isFieldHasData: function (record) {
            return (this.getLabel(record) && this.getLabel(record).replace(/\s/g,"") !== "");
        }
    });
});
