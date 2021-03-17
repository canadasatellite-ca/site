/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

define([
    'Magento_Ui/js/form/element/multiselect'
], function (Multiselect) {
    'use strict';

    return Multiselect.extend({

        /**
         * @inheritDoc
         */
        initialize: function () {
            this._super()
                .markAsRequiredIfNeeded();

            return this;
        },

        /**
         * Mark field as required if needed
         *
         * @returns {exports}
         */
        markAsRequiredIfNeeded: function () {
            if (this.source.data['newReview']) {
                this.required(true);
                this.validation = {'required-entry': true};
            }

            return this;
        }
    });
});
