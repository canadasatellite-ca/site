/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

define([
    'Aheadworks_AdvancedReviews/js/product/view/review/abstract-form'
], function (Component) {
    'use strict';

    return Component.extend({
        defaults: {
            formId: 'review-form',
            formCss: 'aw-ar-review-form',
            imports: {
                reviewsCount: '${ $.configProvider }:data.total_reviews_count',
                isAllowGuestSubmitReview: '${ $.configProvider }:data.review_form.is_allow_guest_submit_review',
                isCustomerLoggedIn: '${ $.configProvider }:data.is_customer_logged_in'
            }
        },

        /**
         * Check if need to render review form
         */
        isNeedToRenderForm: function () {
            return (this.isCustomerLoggedIn || this.isAllowGuestSubmitReview);
        },

        /**
         * Check if review form visible by default
         *
         * @returns {boolean}
         */
        isVisible: function () {
            return (this.reviewsCount === 0);
        }
    });
});
