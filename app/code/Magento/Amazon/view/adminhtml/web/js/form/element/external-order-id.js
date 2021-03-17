/**
 * Copyright © 2017 Magento. All rights reserved.
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
                        
            // if vat is enabled
            if (value == 1) {
                this.showIsExternalOrderIdMessage();
            }

            return this;
        },
            
        /**
         * Popup modal for shipping notification
         */
        showIsExternalOrderIdMessage: function () {
            alert({
                title: $.mage.__('Important Note'),
                content: $.mage.__('If you are using an external system for order fulfillment, it is recommended to use Magento Order Number as the order source to prevent any incompatibilities with Amazon order numbers.'),
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