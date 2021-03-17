/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

define([
    'jquery',
    'underscore',
    'Aheadworks_AdvancedReviews/js/ui/grid/columns/column',
    'Magento_Ui/js/lib/spinner',
    'Magento_Ui/js/modal/confirm',
    'Aheadworks_AdvancedReviews/js/widget/message',
    'mage/storage',
    'mage/translate'
], function ($, _, Column, loader, confirm, message, storage, $t) {
    'use strict';

    return Column.extend({
        defaults: {
            reportUrl: 'aw_advanced_reviews/abuse/report',
            confirmText: $t('Please confirm that you want to report abuse.'),
            exports: {
                applied: '${ $.provider }:params.time'
            }
        },
        cookiesName: 'aw_advanced_review_report_abuse',
        cookieValue: {},

        /**
         * @inheritdoc
         */
        initialize: function () {
            this._super()
                .cookieValue = this.getCookieValue();

            return this;
        },

        /**
         * Check is allow for comment
         *
         * @param {number} id
         * @return {boolean}
         */
        isAllowForComment: function (id) {
            return _.isEmpty(this.cookieValue.comment) || _.indexOf(this.cookieValue.comment, id) === -1;
        },

        /**
         * Check is allow for review
         *
         * @param {number} id
         * @return {boolean}
         */
        isAllowForReview: function (id) {
            return _.isEmpty(this.cookieValue.review) || _.indexOf(this.cookieValue.review, id) === -1;
        },

        /**
         * Send report fo review
         *
         * @param {number} id
         */
        reportReview: function (id) {
            this._confirmBeforeSend('review', id);
        },

        /**
         * Send report fo comment
         *
         * @param {number} id
         * @return {Column}
         */
        reportComment: function (id) {
            this._confirmBeforeSend('comment', id);
        },

        _confirmBeforeSend: function (entityType, entityId) {
            var self = this;

            confirm({
                content: this.confirmText,
                actions: {
                    cancel: function (event) {
                    },
                    confirm: function () {
                        self._sendReport(entityType, entityId);
                    }
                },
                buttons: [
                    {
                        text: $t('I Confirm'),
                        class: 'action-primary action-accept',
                        click: function (event) {
                            this.closeModal(event, true);
                        }
                    },
                    {
                        text: $t('Cancel'),
                        click: function (event) {
                            this.closeModal(event);
                        }
                    }
                ]
            });
        },

        /**
         * Send report
         *
         * @param {string} entityType
         * @param {number} entityId
         * @private
         */
        _sendReport: function (entityType, entityId) {
            var payload = {
                    entityType: entityType,
                    entityId: entityId,
                    form_key: this.getFormKey()
                },
                serviceUrl = this.reportUrl,
                me = this;

            this.showLoader();
            storage.post(
                serviceUrl,
                payload,
                true,
                'application/x-www-form-urlencoded; charset=UTF-8'
            ).done(
                function (response) {
                    if (response.success) {
                        me.setCookieValue(entityType, entityId);
                        me.set('applied', new Date().getTime());
                    }
                }
            ).fail(
                function () {
                    me.getMessagesContainer().addErrorMessage(response);
                }
            ).always(
                function (response) {
                    me.hideLoader();
                    if (response.message) {
                        message({content: response.message});
                    }
                }
            );

            return this;
        },

        /**
         * Retrieve form key
         *
         * @returns {String}
         */
        getFormKey: function () {
            if (!window.FORM_KEY) {
                window.FORM_KEY = $.mage.cookies.get('form_key');
            }
            return window.FORM_KEY;
        },

        /**
         * Set cookie values
         *
         * @param {string} entityType
         * @param {number} entityId
         */
        setCookieValue: function (entityType, entityId) {
            var cookieValue = this.getCookieValue(),
                values = _.isEmpty(cookieValue[entityType]) ? [] : cookieValue[entityType];

            cookieValue[entityType] = _.union(values, [entityId]);
            this.cookieValue = cookieValue;
            $.mage.cookies.set(this.cookiesName, JSON.stringify(cookieValue), {lifetime: 86400});
        },

        /**
         * Get cookie values
         *
         * @returns {Object}
         */
        getCookieValue: function () {
            var cookieValue = $.mage.cookies.get(this.cookiesName)
                ? $.mage.cookies.get(this.cookiesName)
                : '{}';

            return JSON.parse(cookieValue);
        },

        /**
         * Hides loader.
         */
        hideLoader: function () {
            loader.get(this.parentName).hide();
        },

        /**
         * Shows loader.
         */
        showLoader: function () {
            loader.get(this.parentName).show();
        }
    });
});
