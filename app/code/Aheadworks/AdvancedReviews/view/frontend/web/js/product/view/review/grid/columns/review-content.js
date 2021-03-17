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
            fieldsProsAndCons: {
                reviewAdvantagesField: 'pros',
                reviewDisadvantagesField: 'cons'
            }
        },

        /**
         * Returns true if review has additional description otherwise return false.
         *
         *@param {Object} record.
         * @returns {Boolean}
         */
        isReviewHasAdditionalDescription: function (record) {
            var flag = false;
            Object.values(this.fieldsProsAndCons).forEach(function(fieldName) {
                if (record[fieldName] && record[fieldName].replace(/\s/g,"") !== ""){
                    flag = true;
                }
            });
            return flag;
        }
    });
});
