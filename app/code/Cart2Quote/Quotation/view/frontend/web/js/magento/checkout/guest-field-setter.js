/*
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

define(
    [
        'jquery',
        'ko',
        'uiComponent',
        'Magento_Checkout/js/model/shipping-service'
    ],
    function ($, ko, Component, shippingService) {
        'use strict';

        /**
         * A model to set the email, first name and last name
         * when a guest uses Guests checkout and Cart2Quote.
         */
        return Component.extend({

            /**
             * Checks if the data is already set
             */
            loaded: false,

            /**
             * @return {exports.initObservable}
             */
            initObservable: function () {
                this._super();
                var self = this;

                /** Fire code when shipping service is done loading */
                shippingService.isLoading.subscribe(function (loading) {
                    if (!self.loaded && !loading && checkoutConfig.quotationGuestCheckout) {
                        /** Quote data */
                        var quotationCustomerData = checkoutConfig.quotationCustomerData;

                        /**
                         * An array with an object containing a selector and value.
                         * These are the fields that need to be set on the checkout
                         *
                         * @type {*[]}
                         */
                        var fields = [{
                            selector: 'form[data-role=email-with-possible-login] [name="username"]',
                            value: quotationCustomerData.email
                        }, {
                            selector: '#shipping-new-address-form [name="firstname"]',
                            value: quotationCustomerData.firstname
                        }, {
                            selector: '#shipping-new-address-form [name="lastname"]',
                            value: quotationCustomerData.lastname
                        }];

                        /** Process the array */
                        $.each(fields, function (id, field) {
                            $(field.selector).val(field.value);
                            $(field.selector).change();
                        });

                        self.loaded = true;
                    }
                });
                return this;
            }
        });
    }
);
