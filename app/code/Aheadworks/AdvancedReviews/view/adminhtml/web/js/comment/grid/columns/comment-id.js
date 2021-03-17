/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

define([
    'underscore',
    'Magento_Ui/js/grid/columns/multiselect'
], function (_, MultiSelect) {
    'use strict';

    return MultiSelect.extend({

        /**
         * {@inheritDoc}
         */
        getFiltering: function () {
            var source = this.source(),
                keys = ['filters', 'search', 'namespace', 'current_review_id'];

            if (!source) {
                return {};
            }

            return _.pick(source.get('params'), keys);
        }
    });
});
