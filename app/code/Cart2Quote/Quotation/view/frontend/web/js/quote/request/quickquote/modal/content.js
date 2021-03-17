/*
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

define(
    [
        'jquery',
        'uiComponent',
        'ko',
        'Magento_Customer/js/customer-data',
        'Magento_Customer/js/model/customer',
        'Magento_Customer/js/action/check-email-availability',
        'Magento_Customer/js/model/authentication-popup',
        'mage/url'
    ],
    function (
        $,
        Component,
        ko,
        customerData,
        customer,
        checkEmailAvailability,
        authenticationPopup,
        url
    ) {
        'use strict';

        return Component.extend({
            defaults: {
                formKey: $.cookie('form_key'),
                submitButtonId: '#submit-quickquote-btn',
                emailAvailable: true,
                quoteListUrl: url.build('quotation/quote'),
                submitQuoteUrl: url.build('quotation/quote_request/quickQuote'),
                email: null,
                emailFocused: false,
                isLoading: false,
                listens: {
                    email: 'emailHasChanged',
                    emailFocused: 'validateEmail'
                }
            },

            customer: customerData.get('customer'),
            quote: customerData.get('quote'),
            itemCount: ko.observable(0),
            showRemark: ko.observable(window.checkoutConfig.showRemark),
            checkDelay: 2000,
            emailCheckTimeout: 0,
            isEmailCheckComplete: null,

            /** @inheritdoc */
            initialize: function () {
                this._super();
                this.disableSubmitButton();
            },

            /**
             * Disable quick quote form submit button when a used email is provided or the customer is logged in
             */
            disableSubmitButton: function () {
                if (parseInt(window.checkoutConfig.registeredQuoteCheckout) === 1) {
                    $(this.submitButtonId).attr('disabled', false);
                } else {
                    $(this.submitButtonId).attr('disabled', !(this.emailAvailable() || this.customer().isLoggedIn));
                }
            },

            /**
             * Callback on changing email property
             */
            emailHasChanged: function () {
                var self = this;
                clearTimeout(this.emailCheckTimeout);

                this.emailCheckTimeout = setTimeout(function () {
                    if (!self.customer().isLoggedIn && self.validateEmail()) {
                        self.checkEmailAvailability();
                    }
                }, self.checkDelay);
            },

            /**
             * Local email validation.
             *
             * @param {Boolean} focused - input focus.
             * @returns {Boolean} - validation result.
             */
            validateEmail: function (focused) {
                var formSelector = 'form#quick-quote-form',
                    usernameSelector = formSelector + ' input[type=email]',
                    form = $(formSelector),
                    validator;

                form.validation();

                if (focused === false && !!this.email()) {
                    return !!$(usernameSelector).valid();
                }

                validator = form.validate();

                return validator.check(usernameSelector);
            },

            /**
             * Check email existing.
             */
            checkEmailAvailability: function () {
                this.validateRequest();
                this.isEmailCheckComplete = $.Deferred();
                this.isLoading(true);
                this.checkRequest = checkEmailAvailability(this.isEmailCheckComplete, this.email());

                $.when(this.isEmailCheckComplete).done(function () {
                    this.emailAvailable(true);
                }.bind(this)).fail(function () {
                    this.emailAvailable(false);
                }.bind(this)).always(function () {
                    this.isLoading(false);
                }.bind(this));
            },

            /**
             * If request has been sent -> abort it.
             * ReadyStates for request aborting:
             * 1 - The request has been set up
             * 2 - The request has been sent
             * 3 - The request is in process
             */
            validateRequest: function () {
                if (this.checkRequest != null && $.inArray(this.checkRequest.readyState, [1, 2, 3])) {
                    this.checkRequest.abort();
                    this.checkRequest = null;
                }
            },

            /**
             * Initializes observable properties of instance
             *
             * @returns {Object} Chainable.
             */
            initObservable: function () {
                this._super()
                    .observe(['emailAvailable', 'email', 'emailFocused', 'isLoading']);

                this.email(this.customer().email);
                this.emailAvailable.subscribe(this.disableSubmitButton, this);
                this.quote.subscribe(this.quoteChanged, this);

                return this;
            },

            quoteChanged: function (quote) {
                this.itemCount(quote.items.length);
                $('.quoted-items').trigger('contentUpdated');
            },

            /**
             * @param {String} productType
             * @return {*|String}
             */
            getItemRenderer: function (productType) {
                return this.itemRenderer[productType] || 'defaultRenderer';
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
