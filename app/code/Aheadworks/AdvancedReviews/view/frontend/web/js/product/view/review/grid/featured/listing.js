/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

define([
    'Aheadworks_AdvancedReviews/js/product/view/review/grid/listing'
], function (Listing) {
    'use strict';

    return Listing.extend({
        defaults: {
            template: 'Aheadworks_AdvancedReviews/product/view/review/grid/featured/listing',
            featuredReviewLabel: 'Featured review(s)',
            attachmentIdentifierClass: 'featured-preview-link'
        },

        /**
         * Returns label for featured review block
         */
        getFeaturedReviewLabel: function() {
            return this.featuredReviewLabel;
        }
    });
});
