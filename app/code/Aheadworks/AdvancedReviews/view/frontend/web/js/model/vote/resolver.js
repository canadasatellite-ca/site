/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

define([], function () {
    'use strict';

    return {
        isVotedMap: {
            vote_like: true,
            vote_dislike: true,
            from_like_to_dislike: true,
            from_dislike_to_like: true,
            unvote_like: false,
            unvote_dislike: false
        },
        voteStatusesMap: {
            vote_like: 'like',
            vote_dislike: 'dislike',
            from_like_to_dislike: 'dislike',
            from_dislike_to_like: 'like',
            unvote_like: false,
            unvote_dislike: false
        },

        /**
         * Resolve action
         *
         * @param {boolean} isVoted
         * @param {boolean} isVoteLike
         * @param {string} voteToSet
         * @return {string}
         */
        getAction: function (isVoted, isVoteLike, voteToSet) {
            var action = '';

            if (!isVoted && voteToSet === 'like') {
                action = 'vote_like';
            } else if (!isVoted && voteToSet === 'dislike') {
                action = 'vote_dislike';
            } else if (isVoted && voteToSet === 'like' && isVoteLike) {
                action = 'unvote_like';
            } else if (isVoted && voteToSet === 'dislike' && !isVoteLike) {
                action = 'unvote_dislike';
            } else if (isVoted && voteToSet === 'dislike' && isVoteLike) {
                action = 'from_like_to_dislike';
            } else if (isVoted && voteToSet === 'like' && !isVoteLike) {
                action = 'from_dislike_to_like';
            }

            return action;
        },

        /**
         * Retrieve is voted value
         *
         * @param {string} action
         * @return {boolean}|undefined
         */
        getIsVoted: function (action) {
            return this.isVotedMap[action];
        },

        /**
         * Retrieve vote status
         *
         * @param {string} action
         * @return {boolean|string}
         */
        getVoteStatus: function (action) {
            return this.voteStatusesMap[action];
        }
    }
});
