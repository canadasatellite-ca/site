/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

define([
    'jquery',
    'Aheadworks_AdvancedReviews/js/product/view/review/grid/show-more'
], function ($, initShowMore) {
    'use strict';

    return function (widget) {
        $.widget('mage.collapsible', widget, {
            awReviewsTabId: 'product_aw_reviews_tab',

            /**
             * @inheritdoc
             * */
            activate: function () {
                this._super();
                if (this.isWidgetContainsReviewsGrid()) {
                    initShowMore();
                }
            },

            /**
             * @inheritdoc
             * */
            forceActivate: function () {
                this._super();
                if (this.isWidgetContainsReviewsGrid()) {
                    initShowMore();
                }
            },

            /**
             * Check if current collapsible widget contains reviews grid
             *
             * @returns {boolean}
             */
            isWidgetContainsReviewsGrid: function () {
                var isWidgetContainsReviewsGrid = false;
                if (this.content.length > 0) {
                    var widgetContent = this.content[0];
                    if (widgetContent.id === this.awReviewsTabId) {
                        isWidgetContainsReviewsGrid = true;
                    }
                }
                return isWidgetContainsReviewsGrid;
            }
        });

        return $.mage.collapsible;
    }
});
