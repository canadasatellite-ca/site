/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
define([
    'jquery',
    'underscore',
    'uiRegistry',
    'Magento_Ui/js/form/element/select',
    'Magento_Ui/js/modal/alert',
    'mage/translate'
], function ($, _, uiRegistry, select, alert) {
    'use strict';

    return select.extend({

        /**
         * On value change handler.
         *
         * @param {String} value
         */
        onUpdate: function (value) {

            // show popup message if disabling
            if (value == 0) {
                this.showWarningMessage();
            }

            return this._super();
        },

        /**
         * Popup modal for listing action
         */
        showWarningMessage: function () {
            alert({
                title: $.mage.__('Important Note'),
                content: $.mage.__('By selecting \"Do Not Automatically List Eligible Products\", all eligible products will simply queue up and await a user to publish on Amazon.'),
                autoOpen: true,
                clickableOverlay: false,
                focus: "",
                actions: {
                    always: function () {}
                }
            });
        }
    });
});
