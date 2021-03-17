/*
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

define(
    [
        'ko',
        'jquery',
        'uiComponent',
        'Magento_Checkout/js/model/shipping-service',
        'Cart2Quote_Quotation/js/quote-checkout/model/quote-checkout-model-selector',
        'Cart2Quote_Quotation/js/quote-checkout/checkout-data-quotation'
    ],
    function (ko, $, Component, shippingService, selector, checkoutQuotationData) {
        "use strict";

        /**
         * This is the model that handles the form behavior
         */
        return Component.extend({

            /**
             * Flag for the variables that are fired by the loaded shipping service.
             */
            loaded: false,

            /**
             * Flag for the guests fields need to be shown
             */
            showGuestField: ko.observable(false),

            /**
             * Flag for the non-guest fields need to be shown
             */
            showNonGuestField: ko.observable(false),

            /**
             * Flag for all the fields if they are allowed to be shown
             */
            showFields: ko.observable(false),

            /**
             * Init component
             */
            initialize: function () {
                var self = this;
                this._super();

                this.showGuestField.extend({notify: 'always'});
                this.showNonGuestField.extend({notify: 'always'});
                this.showFields.extend({notify: 'always'});

                if (!this.allowToUseForm()) {
                    this.initAllowToUseForm();
                } else {
                    /** Check the remove fields value of the email */
                    shippingService.isLoading.subscribe(function (isLoading) {
                        if (!isLoading && !this.loaded) {
                            this.initAllowToUseForm();

                            setTimeout(function () {
                                self.initCopyNameService();
                            }, 1500);
                            this.loaded = true;

                            if (selector.hasLoginModel()) {
                                selector.getLoginModel().emailHasChanged();
                            }

                            if (selector.hasGuestCheckoutFields()) {
                                selector.getGuestCheckoutModel().requestAccount.subscribe(
                                    function () {
                                        self.updateFields()
                                    }
                                );
                            }
                        }
                    }, this);
                }
            },

            /**
             * Init the allow to use form.
             */
            initAllowToUseForm: function () {
                var self = this;

                /** Init fields */
                self.updateFields();

                if (selector.hasLoginModel()) {
                    /** Subscribe to email change */
                    selector.getLoginModel().email.subscribe(function () {
                        self.updateFields()
                    });

                    /** Subscribe to password field */
                    selector.getLoginModel().isPasswordVisible.subscribe(function () {
                        self.updateFields()
                    });
                }
            },

            /**
             * Update the showGuestField, showNonGuestField and showFields
             * variable by the validForShow and requestAccount
             */
            updateFields: function () {
                var validForShow = true, requestAccount = true, configAllowForm = this.allowToUseForm(),
                    existAccount = true;

                if (selector.hasLoginModel()) {
                    validForShow = this.isLoginValid();
                    if (this.registeredQuoteCheckoutMode() == "0") {
                        validForShow = !selector.getLoginModel().isPasswordVisible();
                    } else if (this.registeredQuoteCheckoutMode() == "1") {
                        requestAccount = !selector.getLoginModel().isPasswordVisible();
                    } else if (this.registeredQuoteCheckoutMode() == "2") {
                        existAccount = !selector.getLoginModel().isPasswordVisible();
                    }
                }

                if (this.allowToUseGuest() === 1 && existAccount) {
                    if (selector.hasGuestCheckoutFields()) {
                        requestAccount = selector.getGuestCheckoutModel().requestAccount();
                    }
                }
                var guestAddress = this.allowToUseGuest() === 2;
                if (!existAccount && guestAddress) {
                    this.showGuestField(false);
                    this.showNonGuestField(true);
                    this.showFields(true);
                } else {
                    this.showGuestField(validForShow && !requestAccount && configAllowForm);
                    this.showNonGuestField(validForShow && requestAccount && configAllowForm);
                    this.showFields(validForShow && configAllowForm);
                }
            },

            /**
             * Checks if the email is valid
             *
             * @returns {boolean}
             */
            isLoginValid: function () {
                if (selector.getLoginFormField().first().val()) {
                    selector.getLoginForm().validation();
                    return Boolean(selector.getLoginFormField().valid());
                }

                return false;
            },

            /**
             * Update the firstname and lastname in the quotation form each time requestAccount is changed.
             */
            initCopyNameService: function () {
                if (selector.hasGuestCheckoutFields()) {
                    selector.getGuestCheckoutModel().requestAccount.subscribe(function () {
                        if (selector.getGuestCheckoutModel().requestAccount()) {
                            //copy guest to shipping and billing forms
                            this.copyNamesFromGuest();
                        } else {
                            //copy shipping and billing forms to quest
                            this.copyNamesToGuest();
                        }
                    }, this);
                }
            },

            /**
             * Copy the first name and last name to the guest quotation fields.
             */
            copyNamesToGuest: function () {
                this.updateQuotationFieldToGuest('firstname');
                this.updateQuotationFieldToGuest('lastname');
            },

            /**
             * Copy the first name and last name from the guest quotation fields.
             */
            copyNamesFromGuest: function () {
                this.updateQuotationFieldFromGuest('firstname');
                this.updateQuotationFieldFromGuest('lastname');
            },

            /**
             * Update a guest data filed with quotation data
             * KO will be notified by the change function on the input.
             *
             * @param name
             */
            updateQuotationFieldToGuest: function (name) {
                var field = this.getField(name, 'quotationGuestFieldData');
                field.val(this.getAddressAttribute(name));
                field.change();
            },

            /**
             * Update a quotation field with guest data
             * KO will be notified by the change function on the input.
             *
             * @param name
             */
            updateQuotationFieldFromGuest: function (name) {
                var billingAddressField = this.getField(name, 'billingAddress');
                billingAddressField.val(this.getAddressAttribute(name));
                billingAddressField.change();

                var shippingAddressField = this.getField(name, 'shippingAddress');
                shippingAddressField.val(this.getAddressAttribute(name));
                shippingAddressField.change();
            },

            /**
             * Get an address attribute.
             * Fallback:
             *      1)  field on billing
             *      2)  field on shipping
             *      3)  field on quotation
             *
             * @param name
             * @returns string
             */
            getAddressAttribute: function (name) {
                var value = '';

                if (this.getField(name, 'billingAddress').val() !== '') {
                    value = this.getField(name, 'billingAddress').val();
                } else if (this.getField(name, 'shippingAddress').val() !== '') {
                    value = this.getField(name, 'shippingAddress').val();
                } else {
                    value = this.getField(name, 'quotationGuestFieldData').val()
                }

                return value;
            },

            /**
             * Get a field
             *
             * @param fieldName
             * @param type
             * @returns {*|jQuery|HTMLElement}
             */
            getField: function (fieldName, type) {
                return $('[name="' + type + '.' + fieldName + '"] input');
            },

            /**
             * Get allow to use guest mode
             * @returns Number
             */
            allowToUseGuest: function () {
                return Number(checkoutQuotationData.getQuotationConfigDataFromData().allowGuest);
            },

            /**
             * Get allow to use form
             * @returns boolean
             */
            allowToUseForm: function () {
                return Boolean(checkoutQuotationData.getQuotationConfigDataFromData().allowForm);
            },

            /**
             * Get show shipping methods
             * @returns boolean
             */
            displayShippingMethods: function () {
                return Boolean(checkoutQuotationData.getQuotationConfigDataFromData().displayShipping);
            },

            /**
             * Get registered user quote checkout configuration
             * @returns integer
             */
            registeredQuoteCheckoutMode: function () {
                return checkoutQuotationData.getQuotationConfigDataFromData().registeredQuoteCheckout;
            },

            displayMethodSwitcher: function () {
                return checkoutQuotationData.getQuotationConfigDataFromData().displaySwitcher;
            }
        })();
    }
);
