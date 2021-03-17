/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

define([
    'underscore',
    'jquery',
    'Aheadworks_AdvancedReviews/js/product/view/review/abstract-form',
    'Aheadworks_AdvancedReviews/js/widget/message'
], function (_, $, Component, message) {
    'use strict';

    return Component.extend({
        defaults: {
            formId: 'comment-form',
            formCss: 'aw-ar-comment-form',
            rowIndex: '',
            dataFormPartSelectors: [],
            isDisplayCancel: true,
            listens: {
                responseData: 'onResponseData'
            },
            exports: {
                applied: '${ $.parentProvider }:params.time',
                rowIndex: '${ $.provider }:rowIndex'
            }
        },

        /**
         * {@inheritdoc}
         */
        initialize: function () {
            this._super()
                .triggerValidateStr = this.rowIndex + '.data.validate';

            return this;
        },

        /**
         * {@inheritDoc}
         */
        save: function (redirect, data) {
            this.setLinks(this.exports, 'exports')
                ._super();
        },

        /**
         * {@inheritDoc}
         */
        getFormId: function() {
            return this.formId + '-' + this.rowIndex;
        },

        /**
         * {@inheritDoc}
         */
        isVisible: function () {
            return false;
        },

        /**
         * Toggle visible form
         *
         * @param {Boolean|Null} isVisible
         */
        toggleVisible: function (isVisible) {
            var target = $('#' + this.getFormId());

            if (target) {
                isVisible = isVisible || target.is(':visible');
                isVisible ? target.hide() : target.show();
            }
        },

        /**
         * Reset captcha
         */
        resetCaptcha: function () {
            var captcha = this.getChild('captcha');

            if (captcha && captcha.reset) {
                captcha.reset();
            }
        },

        /**
         * {@inheritDoc}
         */
        onResponseData: function (response) {
            this.resetCaptcha();

            if (response.success) {
                this.toggleVisible(true);
                this.reset();
            }
            if (response.message) {
                message({content: response.message});
            }
            if (response.refresh) {
                this.set('applied', new Date().getTime());
            }

            return this;
        },

        /**
         * {@inheritDoc}
         */
        onCancel: function () {
            this.reset();
            this.toggleVisible(false);
        }
    });
});
