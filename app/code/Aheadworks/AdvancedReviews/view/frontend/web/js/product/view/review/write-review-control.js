/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

define([
    'jquery',
    'uiElement',
    'mage/translate',
    'Aheadworks_AdvancedReviews/js/product/view/review/form/show-review-form'
], function ($, Component, $t, showReviewForm) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'Aheadworks_AdvancedReviews/product/view/review/write-review-control',
            imports: {
                reviewsCount: '${ $.configProvider }:data.total_reviews_count',
                isAllowGuestSubmitReview: '${ $.configProvider }:data.review_form.is_allow_guest_submit_review',
                isCustomerLoggedIn: '${ $.configProvider }:data.is_customer_logged_in',
                loginUrl: '${ $.configProvider }:data.login_url',
                registerUrl: '${ $.configProvider }:data.register_url'
            }
        },

        /**
         * @inheritdoc
         * */
        initialize: function () {
            return this._super();
        },

        /**
         * Show form to submit review
         */
        showReviewForm: function () {
            showReviewForm({animationType: 'toggle-visibility'});
        },

        /**
         * Check if need to render current component
         *
         * @returns {boolean}
         */
        isNeedToRenderComponent: function () {
            return (this.reviewsCount > 0)
        },

        /**
         * Check if need to show write review button
         */
        isNeedToRenderWriteReviewButton: function () {
            return (this.isCustomerLoggedIn || this.isAllowGuestSubmitReview);
        },

        /**
         * Check if need to show message for guests
         */
        isNeedToRenderMessageForGuest: function () {
            return (!this.isCustomerLoggedIn && !this.isAllowGuestSubmitReview);
        },

        /**
         * Retrieve message for guests
         */
        getMessageForGuest: function () {
            var messageForGuest = $t('Please <a href="{login_url}">sign in</a> or ' +
                '<a href="{register_url}">create an account</a> to write a review.');

            return messageForGuest.replace('{login_url}', this.loginUrl).replace('{register_url}', this.registerUrl);
        }
    });
});
