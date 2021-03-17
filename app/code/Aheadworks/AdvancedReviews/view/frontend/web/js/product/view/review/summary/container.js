/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

define([
    'jquery',
    'underscore',
    'uiComponent',
    'mage/translate'
], function ($, _, Component, $t) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'Aheadworks_AdvancedReviews/product/view/review/summary/container',
            imports: {
                reviewsCount: '${ $.configProvider }:data.total_reviews_count',
                isAllowGuestSubmitReview: '${ $.configProvider }:data.review_form.is_allow_guest_submit_review',
                isCustomerLoggedIn: '${ $.configProvider }:data.is_customer_logged_in',
                loginUrl: '${ $.configProvider }:data.login_url',
                registerUrl: '${ $.configProvider }:data.register_url'
            }
        },

        /**
         * Check if need to render summary block
         *
         * @returns {boolean}
         */
        isNeedToRenderSummaryBlock: function () {
            return (this.reviewsCount > 0);
        },

        /**
         * Check if need to render message when there are no reviews to display
         *
         * @returns {boolean}
         */
        isNeedToRenderNoReviewsMessage: function () {
            return (this.reviewsCount === 0);
        },

        /**
         * Retrieve message for empty summary block
         *
         * @returns {string}
         */
        getNoReviewsMessage: function () {
            var noReviewsMessage = '';
            if (!this.isCustomerLoggedIn && !this.isAllowGuestSubmitReview) {
                noReviewsMessage = this._getNoReviewsMessageForGuest();
            } else {
                noReviewsMessage = $t('No reviews here yet. Be the first to write one!');
            }
            return noReviewsMessage;
        },

        /**
         * Retrieve no reviews message for guests
         * @private
         *
         * @returns {string}
         */
        _getNoReviewsMessageForGuest: function () {
            var messageForGuest = $t('No reviews here yet. Please <a href="{login_url}">sign in</a> or ' +
                '<a href="{register_url}">create an account</a> to write the first one!');
            return messageForGuest.replace('{login_url}', this.loginUrl).replace('{register_url}', this.registerUrl);
        }
    });
});
