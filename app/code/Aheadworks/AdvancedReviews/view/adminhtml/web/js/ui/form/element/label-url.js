/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

define([
    'Magento_Ui/js/form/element/abstract'
], function (Abstract) {
    'use strict';

    return Abstract.extend({
        defaults: {
            elementTmpl: 'Aheadworks_AdvancedReviews/ui/form/element/label-url'
        },

        /**
         * Retrieve label for field
         *
         * @returns {String}
         */
        getLabel: function() {
            return this.source.data[this.index];
        },

        /**
         * Retrieve url for field
         *
         * @returns {String}
         */
        getUrl: function() {
            return this.source.data[this.index + '_url'];
        }
    });
});
