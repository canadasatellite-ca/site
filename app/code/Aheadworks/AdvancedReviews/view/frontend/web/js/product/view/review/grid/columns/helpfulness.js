/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

define([
    'underscore',
    'Aheadworks_AdvancedReviews/js/ui/grid/columns/column',
    './helpfulness/voting'
], function (_ ,Column, Voting) {
    'use strict';

    return Column.extend({
        defaults: {
            bodyTmpl: 'Aheadworks_AdvancedReviews/product/view/review/grid/cells/helpfulness',

            votingConfig: {
                component: 'Aheadworks_AdvancedReviews/js/product/view/review/grid/columns/helpfulness/voting',
                template: 'Aheadworks_AdvancedReviews/product/view/review/grid/cells/helpfulness/voting'
            }
        },
        votingComponents: {},

        /**
         * Initialize voting component
         *
         * @returns {exports}
         */
        getVotingComponent: function (record) {
            if (!this.votingComponents[record.id]) {
                this.votingComponents[record.id] = new Voting(this.generateVotingConfig(record));
            }

            return this.votingComponents[record.id];
        },

        /**
         * Generate voting config
         *
         * @param record
         * @returns {Object}
         */
        generateVotingConfig: function (record) {
            var componentArgs = {
                name: 'voting_' + record.id,
                reviewId: record.id,
                votes_positive: record.votes_positive,
                votes_negative: record.votes_negative
            };

            return _.extend({}, this.votingConfig, componentArgs);
        }
    });
});
