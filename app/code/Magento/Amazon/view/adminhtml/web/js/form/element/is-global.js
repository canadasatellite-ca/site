/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
define([
    'underscore',
    'uiRegistry',
    'Magento_Ui/js/form/element/select'
], function (_, uiRegistry, select) {
    'use strict';

    return select.extend({

        /**
         * On value change handler.
         *
         * @param {String} value
         */
        onUpdate: function (value) {

            var isGlobal = 1;

            // form fieldsets
            var storeIds = uiRegistry.get('index = store_ids');

            // if select type
            if (value != isGlobal) {
                this.showElement(storeIds);
                return this;
            }

            this.hideElement(storeIds);

            return this;
        },

        /**
         * Show element.
         */
        showElement: function (element) {

            // if element exists
            if (typeof(element) != 'undefined' && element != null) {
                element.show();
            }
        },

        /**
         * Hide element.
         */
        hideElement: function (element) {

            // if element exists
            if (typeof(element) != 'undefined' && element != null) {
                element.hide();
            }
        },
    });
});
