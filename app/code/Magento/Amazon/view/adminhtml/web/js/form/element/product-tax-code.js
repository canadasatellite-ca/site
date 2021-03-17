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

            var defaultPtcElement = uiRegistry.get('index = default_ptc');

            if (value == 1) {
                this.showElement(defaultPtcElement);
                return this;
            } else {
                this.hideElement(defaultPtcElement);
            }

            return this;
        },

        showElement: function (element) {
            if (typeof(element) != 'undefined' && element != null) {
                element.show();
            }
        },

        hideElement: function (element) {
            if (typeof(element) != 'undefined' && element != null) {
                element.hide();
            }
        }
    });
});