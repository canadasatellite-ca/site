/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

define([
    'underscore',
    'Aheadworks_AdvancedReviews/js/ui/grid/columns/column'
], function (_, Column) {
    'use strict';

    return Column.extend({
        defaults: {
            bodyTmpl: 'Aheadworks_AdvancedReviews/product/view/review/grid/cells/product-recommended',
            view_config: []
        },

        /**
         * {@inheritdoc}
         */
        getLabel: function (record) {
            return record[this.index + '_label'];
        },

        /**
         * Returns list of classes that should be applied to a field.
         *
         * @param {Object} record
         * @returns {Object}
         */
        getFieldClass: function (record) {
            var currentFieldClass = {},
                productRecommendedValue = record[this.index],
                additionalClassesArray = {};
            if (!_.isUndefined(this.view_config[productRecommendedValue])) {
                additionalClassesArray = this.view_config[productRecommendedValue].additionalClasses;
            }
            _.extend(currentFieldClass, this._super(), additionalClassesArray);
            return currentFieldClass;
        }
    });
});
