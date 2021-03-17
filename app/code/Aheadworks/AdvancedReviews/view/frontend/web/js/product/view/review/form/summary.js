/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

define([
    'Aheadworks_AdvancedReviews/js/ui/form/element/import-handler'
], function (Component) {
    'use strict';

    return Component.extend({

        /**
         * @inheritdoc
         * */
        initialize: function () {
            this._super();
            if (this.allowImport) {
                this.hide();
            }
            return this;
        },

        /**
         * @inheritdoc
         * */
        updateValue: function (placeholder, component) {
            this._super(placeholder, component);
            if (this.allowImport) {
                this.show();
            }
        }
    });
});
