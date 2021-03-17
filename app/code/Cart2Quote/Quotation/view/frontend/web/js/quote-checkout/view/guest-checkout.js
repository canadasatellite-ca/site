/*
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

define(
    [
        'jquery',
        'Magento_Ui/js/form/form',
        'ko',
        'Magento_Customer/js/model/customer',
        'Magento_Checkout/js/model/shipping-service',
        'Cart2Quote_Quotation/js/quote-checkout/checkout-data-quotation',
        'Cart2Quote_Quotation/js/quote-checkout/model/quote-checkout-model-selector',
        'Cart2Quote_Quotation/js/quote-checkout/model/email-form-usage-observer',
        'uiRegistry'
    ],
    function (
        $,
        Component,
        ko,
        customer,
        shippingService,
        checkoutQuotationData,
        selector,
        emailFormUsage,
        registry
    ) {
        'use strict';

        return Component.extend({
            defaults: {
                template: 'Cart2Quote_Quotation/quote-checkout/view/guest-checkout'
            },

            loaded: false,
            loginSelector: '#customer-email-fieldset',
            isCustomerLoggedIn: customer.isLoggedIn(),

            showRequestShippingQuote: false,
            showRequestShippingQuoteTrigger: ko.observable(false),
            showCreateAccount: ko.observable(false),
            /** The toggle variable */
            requestAccount: ko.observable(customer.isLoggedIn()),

            /** Toggle action used in the template */
            toggleRequestAccount: function () {
                this.requestAccount(!this.requestAccount());
            },

            checkoutQuotationData: null,

            /**
             * Init component
             */
            initialize: function () {
                this._super();
                this.checkoutQuotationData = checkoutQuotationData;

                registry.async('checkoutProvider')(function (checkoutProvider) {
                    var quotationGuestFieldsData = checkoutQuotationData.getQuotationGuestFieldsFromData();

                    if (quotationGuestFieldsData) {
                        checkoutProvider.set(
                            'quotationGuestFieldData',
                            $.extend({}, checkoutProvider.get('quotationGuestFieldData'), quotationGuestFieldsData)
                        );
                    }

                    checkoutProvider.on('quotationGuestFieldData', function (quotationGuestFieldData) {
                        checkoutQuotationData.setQuotationGuestFieldsFromData(quotationGuestFieldData);
                    });
                });
            },

            /**
             * @return {exports.initObservable}
             */
            initObservable: function () {
                this._super();
                var self = this;

                this.initShowRequestShippingQuote();

                /** Subscribe only if the customer is logged in otherwise the checkbox is invisible */
                if (!this.isCustomerLoggedIn) {
                    /** Subscribe to the button switcher */
                    self.requestAccount.subscribe(function (requestAccount) {
                        var reg = requirejs('uiRegistry');
                        var fieldset = 'checkout.steps.shipping-step.quotation-fields.account-information-fieldsets';

                        if (requestAccount) {
                            emailFormUsage.updateFields();

                            //enable dob and gender fields
                            reg.get(fieldset + '.' + 'dob', function (item) {
                                item.enable();

                                var dobSelector = '[name="quotationFieldData.dob"] input';
                                if (typeof item.source.quotationFieldData !== 'undefined') {
                                    item.source.quotationFieldData.dob = $(dobSelector).val();
                                }
                            });
                            reg.get(fieldset + '.' + 'gender', function (item) {
                                item.enable();

                                var genderSelector = '[name="quotationFieldData.gender"] select';
                                if (typeof item.source.quotationFieldData !== 'undefined') {
                                    item.source.quotationFieldData.gender = $(genderSelector).val();
                                }
                            });
                        } else {
                            emailFormUsage.updateFields();

                            //disable dob and gender fields
                            reg.get(fieldset + '.' + 'dob', function (item) {
                                item.disable();

                                if (typeof item.source.quotationFieldData !== 'undefined') {
                                    if (typeof item.source.quotationFieldData.dob !== 'undefined') {
                                        delete item.source.quotationFieldData.dob;
                                    }
                                }
                            });
                            reg.get(fieldset + '.' + 'gender', function (item) {
                                item.disable();

                                if (typeof item.source.quotationFieldData !== 'undefined') {
                                    if (typeof item.source.quotationFieldData.gender !== 'undefined') {
                                        delete item.source.quotationFieldData.gender;
                                    }
                                }
                            });

                            self.showRequestShippingQuote.evaluateImmediate();
                        }
                    });
                }

                /** Hide or show the guest fields based on the customer login */
                shippingService.isLoading.subscribe(function (isLoading) {
                    if (!isLoading && !this.loaded) {
                        this.loaded = true;

                        if (this.isCustomerLoggedIn) {
                            emailFormUsage.updateFields();
                        } else {
                            emailFormUsage.updateFields();
                            this.showRequestShippingQuote.evaluateImmediate();
                        }
                    }
                }, this);

                /**
                 * Request account needs to be true when not using the guest functionality.
                 * You can change the in the Magento store configuration.
                 */
                if (!this.requestAccount()) {
                    this.requestAccount(this.allowToUseGuest() === 0);
                }

                emailFormUsage.showGuestField.subscribe(function () {
                    //emulate trigger
                    var quotationGuestFieldsData = self.checkoutQuotationData.getQuotationGuestFieldsFromData();
                    if (quotationGuestFieldsData) {
                        self.checkoutQuotationData.setQuotationGuestFieldsFromData(quotationGuestFieldsData);
                    }

                    self.showRequestShippingQuote.evaluateImmediate();
                });

                this.showRequestShippingQuote.subscribe(function (value) {
                    self.showCreateAccount(value && self.allowToUseGuest() === 1);
                });

                return this;
            },

            /**
             * Get allow to use guest mode
             * @returns Number
             */
            allowToUseGuest: function () {
                return Number(checkoutQuotationData.getQuotationConfigDataFromData().allowGuest);
            },
            /**
             * Init hide request shipping quote checkbox
             */
            initShowRequestShippingQuote: function () {
                var self = this;
                self.showRequestShippingQuote = ko.computed(function () {
                    var passwordVisible = false, email = false;

                    //just here so that changing this value triggers this computed
                    self.showRequestShippingQuoteTrigger();

                    if (selector.hasLoginModel()) {
                        passwordVisible = selector.getLoginModel().isPasswordVisible();
                        email = selector.getLoginModel().validateEmail();
                    }

                    return !self.isCustomerLoggedIn && self.allowToUseGuest() > 0 && !passwordVisible && email;
                });

                if (typeof self.showRequestShippingQuote.evaluateImmediate != 'function') {
                    self.showRequestShippingQuote.evaluateImmediate = function () {
                        //redundant triggers, the same as .extend({notify: 'always'}) but sometimes this reacts faster
                        if (typeof self.showRequestShippingQuoteTrigger.valueHasMutated === 'function') {
                            self.showRequestShippingQuoteTrigger.valueHasMutated();
                            if (typeof self.showRequestShippingQuote.valueHasMutated === 'function') {
                                self.showRequestShippingQuote.valueHasMutated();
                            }
                        } else {
                            //mutate value manually to trigger self.showRequestShippingQuote
                            var randomValue = Math.floor((Math.random() * 100) + 1);
                            self.showRequestShippingQuoteTrigger(randomValue);
                        }
                    };
                }

                self.showRequestShippingQuote.extend({notify: 'always'});
            }
        });
    }
);
