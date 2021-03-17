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
         * On value change handler
         *
         * @param {String} value
         */
        onUpdate: function (value) {

            var magentoStoreIdElement = uiRegistry.get('index = default_store');
            var customerCreationElement = uiRegistry.get('index = customer_is_active');
            var reserveElement = uiRegistry.get('index = reserve');
            var orderStatusElement = uiRegistry.get('index = custom_status_is_active');
            var customStatusElement = uiRegistry.get('index = custom_status');
            var isExternalOrderIdElement = uiRegistry.get('index = is_external_order_id');

            // import amazon orders enabled
            if (value == 1) {
                this.enableElement(magentoStoreIdElement);
                this.enableElement(customerCreationElement);
                this.enableElement(reserveElement);
                this.enableElement(orderStatusElement);
                this.enableElement(isExternalOrderIdElement);
                if (typeof(orderStatusElement) != 'undefined' && orderStatusElement != null) {
                    // if custom order status (show elements)
                    if (orderStatusElement.value() == 1) {
                        this.enableElement(customStatusElement);
                    } else {
                        this.disableElement(customStatusElement);
                    }
                }
            } else {
                this.disableElement(magentoStoreIdElement);
                this.disableElement(customerCreationElement);
                this.disableElement(customStatusElement);
                this.disableElement(reserveElement);
                this.disableElement(orderStatusElement);
                this.disableElement(isExternalOrderIdElement);
            }

            return this;
        },

        /**
         * Enable element.
         */
        enableElement: function (element) {

            // if element exists
            if (typeof(element) != 'undefined' && element != null) {
                element.enable();
            }
        },

        /**
         * Disable element.
         */
        disableElement: function (element) {

            // if element exists
            if (typeof(element) != 'undefined' && element != null) {
                element.disable();
            }
        }
    });
});