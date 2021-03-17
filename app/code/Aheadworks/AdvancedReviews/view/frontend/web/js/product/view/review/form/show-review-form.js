/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

define([
    'jquery',
    'uiRegistry',
    'Aheadworks_AdvancedReviews/js/action/update-visibility'
], function ($, registry, updateVisiblityAction) {
    'use strict';

    $.widget('awar.awArShowReviewForm', {
        options: {
            reviewFormName: 'awArReviewContainer.awArReviewForm',
            animationSpeed: 500,
            animationType: 'show',
            defaultTargetToScrollSelector: '#reviews'
        },

        /**
         * Initialize widget
         */
        _create: function() {
            this._showReviewForm();
        },

        /**
         * Show form to submit review
         */
        _showReviewForm: function () {
            var reviewFormComponent = registry.get(this.options.reviewFormName),
                targetToScroll, reviewForm;

            if (reviewFormComponent.isNeedToRenderForm()) {
                reviewForm = $('#' + reviewFormComponent.getFormId());
                updateVisiblityAction(
                    reviewForm,
                    this.options.animationType,
                    this.options.animationSpeed,
                    this._getNicknameField()
                );
                targetToScroll = reviewForm;
            } else {
                targetToScroll = $(this.options.defaultTargetToScrollSelector);
            }
            this._scrollToTarget(targetToScroll);
        },

        /**
         * Retrieve nickname field of review form
         *
         * @returns {*|jQuery|HTMLElement}
         * @private
         */
        _getNicknameField: function() {
            var nicknameComponent = registry.get(this.options.reviewFormName + '.nickname'),
                nicknameField;

            if (nicknameComponent) {
                nicknameField = $('[name="' + nicknameComponent.inputName + '"]');
            }

            return nicknameField;
        },

        /**
         * Scroll to target
         *
         * @param {DOMElement} target
         * @private
         */
        _scrollToTarget: function (target) {
            if (target && target.offset()) {
                $('html, body').stop().animate({
                    scrollTop: target.offset().top
                }, this.options.animationSpeed);
            }
        }
    });

    return $.awar.awArShowReviewForm;
});
