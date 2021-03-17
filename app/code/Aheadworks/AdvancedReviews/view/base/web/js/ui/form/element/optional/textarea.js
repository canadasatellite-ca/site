/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

define([
    'Magento_Ui/js/form/element/textarea'
], function (Component) {
    'use strict';

    return Component.extend({

        /**
         * {@inheritdoc}
         */
        initialize: function () {
            this._super()
                .updateVisibility();
            
            return this;
        },

        /**
         * Set optional field visible if some value has been set earlier
         */
        updateVisibility: function () {
            if (this.hasData()) {
                this.show();
            }
        }
    });
});
