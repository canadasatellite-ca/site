/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

define([
    'Magento_Ui/js/grid/columns/column'
], function (Component) {
    'use strict';

    return Component.extend({

        /**
         * Toggles sorting direction.
         *
         * @returns {Column} Chainable.
         */
        toggleSorting: function () {
            this.sorting === 'desc' ?
                this.sortAscending() :
                this.sortDescending();

            return this;
        }
    });
});
