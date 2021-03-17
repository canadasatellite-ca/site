/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

define([
    'Magento_Ui/js/form/element/single-checkbox'
], function (Component) {
    'use strict';

    return Component.extend({
        defaults: {
            featuredReviewsCount: 0,
            featuredReviewsLimit: 0,
            additionalInfoTemplate: ''
        },

        /**
         * @inheritDoc
         */
        initialize: function () {
            this._super()
                .updateAdditionalInfo();

            return this;
        },

        /**
         * Update additional info
         *
         * @returns {exports}
         */
        updateAdditionalInfo: function () {
            if (this.featuredReviewsCount > 0) {
                this.additionalInfo = this.additionalInfoTemplate
                    .replace('{count}', this.featuredReviewsCount)
                    .replace('{limit}', this.featuredReviewsLimit)
            }
            return this;
        }
    });
});
