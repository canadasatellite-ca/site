/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

define([
    'underscore',
    'jquery',
    'uiComponent',
    'uiLayout',
    'uiRegistry',
    'mage/storage',
    'Aheadworks_AdvancedReviews/js/model/vote/resolver',
    'Aheadworks_AdvancedReviews/js/widget/message',
    'mage/cookies'
], function (_, $, Component, layout, registry, storage, resolver, message) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'Aheadworks_AdvancedReviews/product/view/review/grid/cells/helpfulness/voting',
            reviewId: null,
            imports: {
                likesCount: '${ $.ns }:votes_positive',
                dislikesCount: '${ $.ns }:votes_negative'
            }
        },
        votingUrl: 'aw_advanced_reviews/review/helpfulness',
        cookiesName: 'aw_advanced_review_voting',
        likesGreater: '',
        greaterClass: 'greater',
        votedClass: 'voted',

        /**
         * @inheritdoc
         */
        initialize: function () {
            this._super()
                .checkVoteStatus()
                .highlightGreaterValue();

            return this;
        },

        /**
         * @inheritdoc
         */
        initObservable: function () {
            this._super()
                .observe({
                    isVoted: false,
                    isLoading: false,
                    isVotedLike: false,
                    likesCount: 0,
                    dislikesCount: 0,
                    likesGreater: null
                });

            return this;
        },

        /**
         * Check is voted by current user
         */
        checkVoteStatus: function () {
            var values = this.getCookieValue(),
                isLike;

            if (values.hasOwnProperty(this.reviewId)) {
                this.isVoted(true);
                isLike = values[this.reviewId] == 'like' ? true : false;
                this.isVotedLike(isLike);
            }

            return this;
        },

        /**
         * Highlight greater votes value
         */
        highlightGreaterValue: function () {
            var likesCount = parseInt(this.likesCount()),
                dislikesCount = parseInt(this.dislikesCount());

            if (likesCount > dislikesCount) {
                this.likesGreater(true);
            } else if (likesCount < dislikesCount) {
                this.likesGreater(false);
            } else {
                this.likesGreater(null);
            }

            return this;
        },

        /**
         * On like click
         */
        onLikeClick: function () {
            var action = resolver.getAction(this.isVoted(), this.isVotedLike(), 'like');

            this.isVotedLike(true);
            this._sendVote(action);
        },

        /**
         * On dislike click
         */
        onDislikeClick: function () {
            var action = resolver.getAction(this.isVoted(), this.isVotedLike(), 'dislike');

            this.isVotedLike(false);
            this._sendVote(action);
        },

        /**
         * Send vote
         *
         * @param {string} voteAction
         * @private
         */
        _sendVote: function (voteAction) {
            var payload = {
                    action: voteAction,
                    reviewId: this.reviewId
                },
                serviceUrl = this.votingUrl,
                isVoted = resolver.getIsVoted(voteAction),
                voteStatus = resolver.getVoteStatus(voteAction),
                me = this;

            this.isLoading(true);

            return storage.post(
                serviceUrl,
                JSON.stringify(payload),
                true
            ).done(
                function (response) {
                    if (response.success) {
                        me.isVoted(isVoted);
                        me.likesCount(response.votes_positive);
                        me.dislikesCount(response.votes_negative);
                        me.setCookieValue(voteStatus);
                        me.highlightGreaterValue();
                    }
                }
            ).always(
                function (response) {
                    me.isLoading(false);
                    if (response) {
                        message({content: response.message});
                    }
                }
            );
        },

        /**
         * Set cookie values
         *
         * @param {string|boolean} voteStatus
         */
        setCookieValue: function (voteStatus) {
            var cookieValue = this.getCookieValue(),
                values = _.isEmpty(cookieValue) ? {} : cookieValue;

            if (voteStatus !== false) {
                values[this.reviewId] = voteStatus;
            } else {
                delete values[this.reviewId];
            }
            $.mage.cookies.set(this.cookiesName, JSON.stringify(values), {lifetime: 86400});
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
        }
    });
});
