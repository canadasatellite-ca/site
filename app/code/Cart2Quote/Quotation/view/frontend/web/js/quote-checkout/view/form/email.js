/*
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

define(
    [
        'jquery',
        'ko',
        'Magento_Checkout/js/view/form/element/email',
        'Cart2Quote_Quotation/js/quote-checkout/model/email-form-usage-observer',
        'Magento_Customer/js/model/authentication-popup',
        'Cart2Quote_Quotation/js/quote-checkout/view/request-switcher'
    ],
    function (
        $,
        ko,
        Component,
        emailFormUsageObserver,
        authenticationPopup,
        switcher
    ) {
        'use strict';

        return Component.extend({
            defaults: {
                listens: {
                    isPasswordVisible: 'passwordVisibleChanged',
                }
            },

            showGuestText: ko.observable(false),
            showPasswordField: ko.observable(false),
            showEmailField: ko.observable(false),

            /**
             * Initializes observable properties of instance
             *
             * @returns {Object} Chainable.
             */
            initObservable: function () {
                this._super();
                this.passwordVisibleChanged();
                this.getShowEmailField();

                return this;
            },

            /**
             * Update showGuestText and showPasswordField each time isPasswordVisible is changed
             */
            passwordVisibleChanged: function () {
                var requireLogin = emailFormUsageObserver.registeredQuoteCheckoutMode() == "0";
                this.showGuestText(this.isPasswordVisible() && !requireLogin);
                this.showPasswordField(this.isPasswordVisible() && requireLogin);
            },

            getShowEmailField: function () {
              this.showEmailField = switcher().getShowEmailField();
            },
            /**
             * Open authentication popup
             */
            login: function () {
                authenticationPopup.showModal();
            }
        });
    }
);
