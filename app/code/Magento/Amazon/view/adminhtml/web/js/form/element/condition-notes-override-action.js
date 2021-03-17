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

            // if removing seller notes
            if (value == 2) {
                this.showMessage();
                return this;
            }

            return this._super();
        },

        /**
         * Popup modal for shipping notification
         */
        showMessage: function () {
            alert({
                title: $.mage.__('Important Note'),
                content: $.mage.__('Removing seller notes will only remove the seller notes override locally.  Amazon does not support removal of the seller notes once assigned.  To complete the seller note removal, please login to your Amazon Seller account and remove the seller notes from your listing.'),
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
