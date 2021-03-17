/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

define([
    'jquery',
    'uiComponent',
    'mage/translate',
    'Aheadworks_AdvancedReviews/js/product/view/review/form/show-review-form'
], function ($, Component, $t, showReviewForm) {
    'use strict';

    return Component.extend({

        defaults: {
            template: 'Aheadworks_AdvancedReviews/product/review-summary',
            summaryType: 'default',
            displayIfNoReviews: false,
            productData: {},
            isProductPage: false,
            statisticsData: {
                reviewsCount: 0,
                ratingValue: 0,
                ratingTitle: ''
            },
            ratingTmpl: 'Aheadworks_AdvancedReviews/review/rating/view',
            actionsTmpl: 'Aheadworks_AdvancedReviews/product/review_summary/actions'
        },
        reviewsIdSelector: '#reviews',
        reviewFormIdSelector: '#review-form',
        reviewActionsSelector: '.product-info-main .review-summary-actions a',
        reviewsTabId: 'product_aw_reviews_tab',
        scrollDelay: 500,

        /**
         * Get url to product reviews
         *
         * @returns {string}
         */
        getReviewsUrl: function() {
            var url = this.productData.url + this.reviewsIdSelector;

            if (!this. isProductPage) {
                url = this.getReviewsUrlOnProductPage()
            }

            return url;
        },

        /**
         * Get url to review form for submit review
         *
         * @returns {string}
         */
        getReviewFormUrl: function() {
            var url = this.productData.url + this.reviewFormIdSelector;

            if (!this. isProductPage) {
                url = this.getReviewsUrlOnProductPage()
            }

            return url;
        },

        /**
         * Get url to reviews on product page
         *
         * @returns {string}
         */
        getReviewsUrlOnProductPage: function () {
            return this.productData.url + '#' + this.reviewsTabId;
        },

        /**
         * Get correct reviews action label
         *
         * @returns {string}
         */
        getReviewsActionLabel: function() {
            var actionLabel = this.statisticsData.reviewsCount == 1 ? $t('Review') : $t('Reviews');

            return this.statisticsData.reviewsCount + ' ' + actionLabel;
        },

        /**
         * Check is short review summary
         *
         * @returns {boolean}
         */
        isShortView: function() {
            return this.summaryType == 'short';
        },

        /**
         * Check is need to display "Be the first..." action
         *
         * @returns {boolean}
         */
        isNeedToDisplayBeFirstAction: function () {
            return !this.statisticsData.reviewsCount && this.displayIfNoReviews;
        },

        /**
         * Bind summary actions
         */
        bindSummaryActions: function() {
            $(this.reviewActionsSelector).on('click', $.proxy(this.onActionClick, this));
        },

        /**
         * On action click handler
         *
         * @param {Event} event
         */
        onActionClick: function(event) {
            var targetToScroll = event.currentTarget.href.replace(/^.*?(?=#|$)/, '');

            event.preventDefault();
            this.activateReviewsTab();
            if (targetToScroll === this.reviewFormIdSelector) {
                showReviewForm();
            } else {
                this.scrollToTarget(targetToScroll);
            }
        },

        /**
         * Activate reviews tab
         */
        activateReviewsTab: function() {
            var me = this,
                reviewsTabTitleSelector = '#tab-label-' + me.reviewsTabId;

            $(reviewsTabTitleSelector).collapsible('forceActivate');
        },

        /**
         * Scroll to needed target
         *
         * @param {DOMElement} targetToScroll
         */
        scrollToTarget: function(targetToScroll) {
            $('html, body').animate({
                scrollTop: $(targetToScroll).offset().top
            }, this.scrollDelay);
        }
    });
});