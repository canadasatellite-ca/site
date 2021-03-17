/*
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

define(
    [
        'ko',
        'jquery'
    ],
    function (ko, $) {
        "use strict";

        /**
         * A model that helps with selecting KO models
         */
        return {
            /** Selectors: BEGIN */
            loginSelector: 'form[data-role=email-with-possible-login]',
            guestSelector: '#co-guest-form',
            shippingSelector: '.checkout-shipping-address',
            billingSelector: '.billing-address-form',
            quotationSelector: '#quotation-fields',
            shippingMethodSelector: '#opc-shipping_method',
            /** Selectors: END */

            /**
             * Get the shipping KO model
             */
            getShippingModel: function () {
                return ko.dataFor($(this.shippingSelector)[0]);
            },

            /**
             * Get the billing KO model
             */
            getBillingModel: function () {
                return ko.dataFor($(this.billingSelector)[0]);
            },

            /**
             * Get the quotation KO model
             */
            getQuotationModel: function () {
                return ko.dataFor($(this.quotationSelector)[0]);
            },

            /**
             * Get the guest checkout KO model
             */
            getGuestCheckoutModel: function () {
                return ko.dataFor($(this.guestSelector)[0]);
            },

            /**
             * Get the login KO model
             */
            getLoginModel: function () {
                return ko.dataFor($(this.loginSelector)[0]);
            },

            /**
             * Get the login form
             */
            getLoginForm: function () {
                return $(this.loginSelector);
            },

            /**
             * Get the login form field
             */
            getLoginFormField: function () {
                return $(this.loginSelector + ' input[name=username]');
            },

            /**
             * Check if shipping address is available
             * @returns {boolean}
             */
            hasShippingAddress: function () {
                return $(this.shippingSelector).is(":visible");
            },

            /**
             * Check if billing address is available
             * @returns {boolean}
             */
            hasBillingAddress: function () {
                return $(this.billingSelector).is(":visible");
            },

            hasShippingMethod: function () {
                return $(this.shippingMethodSelector).length > 0;
            },

            /**
             * Check if quotation fields are available
             * @returns {boolean}
             */
            hasQuotationFields: function () {
                return $(this.quotationSelector).length > 0;
            },

            /**
             * Check if guest checkout fields are available
             * @returns {boolean}
             */
            hasGuestCheckoutFields: function () {
                return $(this.guestSelector).length > 0;
            },

            /**
             * Check if the login model is available
             * @returns {boolean}
             */
            hasLoginModel: function () {
                return $(this.loginSelector).length > 0;
            }
        };
    }
);
