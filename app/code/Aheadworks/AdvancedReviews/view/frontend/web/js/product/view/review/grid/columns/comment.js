/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

define([
    'jquery',
    'underscore',
    'Aheadworks_AdvancedReviews/js/product/view/review/grid/columns/abusive-column',
    'uiLayout',
    'uiRegistry',
    'mageUtils'
], function ($, _, Column, layout, registry, utils) {
    'use strict';

    return Column.extend({
        defaults: {
            bodyTmpl: 'Aheadworks_AdvancedReviews/product/view/review/grid/cells/comment',
            commentFormConfig: {},
            commentForms: [],
            isGuestAllowed: false,
            isCustomerLoggedIn: false,
            listens: {
                '${ $.provider }:reloaded': '_onGridReloaded'
            }
        },

        /**
         * Returns comment form
         *
         * @returns {String}
         */
        getCommentForm: function (record) {
            var rowIndex = record._rowIndex.toString(),
                commentForm = this.commentForms[rowIndex],
                config = this._generateCommentFormConfig(rowIndex),
                self = this;

            if (!commentForm) {
                layout([config]);
                registry.get(config.name, function (commentForm) {
                    self._updateReviewId(commentForm, record.id);
                    self.commentForms[rowIndex] = commentForm;
                });
                this.commentForms[rowIndex] = config.name;
                commentForm = config.name;
            }

            if (_.isObject(commentForm)) {
                this._updateReviewId(commentForm, record.id);
                commentForm = $('#' + commentForm.getFormId()).length
                    ? commentForm
                    : commentForm.name;
            }

            return commentForm;
        },

        /**
         * Generate comment form config
         *
         * @param {String} rowIndex
         * @returns {Object}
         * @private
         */
        _generateCommentFormConfig: function (rowIndex) {
            var formId = 'comment-form-' + rowIndex,
                args = {
                    name: formId,
                    namespace: 'aw_ar_comment_form_' + rowIndex,
                    dataScope: 'data.' + rowIndex,
                    rowIndex: rowIndex,
                    deps: _.union([formId + '.review_id'], this.commentFormConfig.deps),
                    parentProvider: this.provider
                };
            this._prepareChildrenConfig(rowIndex, formId);

            return _.extend({}, this.commentFormConfig, args);
        },

        /**
         * Prepare children config
         *
         * @param {String} rowIndex
         * @param {String} formId
         * @private
         */
        _prepareChildrenConfig: function (rowIndex, formId) {
            _.each(this.commentFormConfig.children, function (childConfig, childName) {
                childConfig.customScope = rowIndex.toString();
                if (childName === 'captcha') {
                    childConfig.formId = formId;
                    childConfig.reCaptchaId = 'comment-form-' + utils.uniqueid();
                }
            });
        },

        /**
         * Update review id form value
         *
         * @param {Object} commentForm
         * @param {String} reviewId
         * @private
         */
        _updateReviewId: function (commentForm, reviewId) {
            commentForm.getChild('review_id').value(reviewId);
        },

        /**
         * Retrieve comments
         *
         * @param {Object} record
         * @return {*}
         */
        getComments: function (record) {
            return record.comments ? record.comments : [];
        },

        /**
         * Open comment form
         *
         * @param {Integer} rowIndex
         */
        openCommentForm: function (rowIndex) {
            var commentForm = this.commentForms[rowIndex];

            if (_.isObject(commentForm)) {
                commentForm.toggleVisible();
            }
        },

        /**
         * On grid data reloaded handler
         *
         * @private
         */
        _onGridReloaded: function () {
            _.each(this.commentForms, function (form) {
                if (_.isObject(form)) {
                    form.toggleVisible(true);
                    form.reset();
                }
            });
        },

        /**
         * Check is comments allowed
         */
        isCommentsAllowed: function () {
            return (this.isCustomerLoggedIn || this.isGuestAllowed);
        }
    });
});
