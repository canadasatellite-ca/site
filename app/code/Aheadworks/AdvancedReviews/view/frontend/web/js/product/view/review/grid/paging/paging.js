/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

define([
    'jquery',
    'Aheadworks_AdvancedReviews/js/ui/grid/paging/paging'
], function ($, Component) {
    'use strict';

    return Component.extend({
        scrollDelay: 300,
        reviewContainerIdentifierClass: 'container-standard-review',
        targetPadding: 30,

        /**
         * @inheritdoc
         * */
        next: function () {
            this.scrollToTarget();
            return this._super();
        },

        /**
         * @inheritdoc
         * */
        prev: function () {
            this.scrollToTarget();
            return this._super();
        },

        /**
         * Scroll to needed target
         *
         * @returns {exports}
         */
        scrollToTarget: function() {
            var target = $('.'+this.reviewContainerIdentifierClass+' .block-content .review-header');
            $('html, body').animate({
                scrollTop: target.offset().top - this.targetPadding
            }, this.scrollDelay);
            return this;
        }
    });
});
