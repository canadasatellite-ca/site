/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

define([
    'Magento_Ui/js/form/element/abstract'
], function (Component) {
    'use strict';

    return Component.extend({

        /**
         * {@inheritdoc}
         */
        reset: function () {
            return this;
        }
    });
});
