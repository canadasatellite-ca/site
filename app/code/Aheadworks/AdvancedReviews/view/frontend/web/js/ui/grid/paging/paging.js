/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

define([
    'Magento_Ui/js/grid/paging/paging',
    'mageUtils',
    'uiLayout'
], function (Component, utils, layout) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'Aheadworks_AdvancedReviews/ui/grid/paging/paging',
            defaultPageSize: 20
        },

        /**
         * Checks if need to render paging component
         *
         * @returns {Boolean}
         */
        isNeedToRenderPager: function () {
            return this.pages > 1;
        },

        /**
         * Initializes sizes component with default page size
         *
         * @returns {exports}
         */
        initSizes: function () {
            var sizesComponentConfig = {
                value: this.defaultPageSize
            };
            sizesComponentConfig = utils.extend({}, this.sizesConfig, sizesComponentConfig);
            layout([sizesComponentConfig]);
            return this;
        }
    });
});
