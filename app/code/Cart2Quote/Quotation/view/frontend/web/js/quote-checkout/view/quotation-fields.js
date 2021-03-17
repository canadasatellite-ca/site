/*
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

define(
    [
        'jquery',
        'ko',
        'Magento_Ui/js/form/form',
        'Cart2Quote_Quotation/js/quote-checkout/checkout-data-quotation',
        'Cart2Quote_Quotation/js/quote-checkout/model/email-form-usage-observer',
        'mage/translate',
        'uiRegistry',
        'Magento_Customer/js/model/customer',
        'Cart2Quote_Quotation/js/quote-checkout/view/request-switcher'
    ],
    function (
        $,
        ko,
        Component,
        checkoutQuotationData,
        emailFormUsageObserver,
        $t,
        registry,
        customer,
        switcher
    ) {
        'use strict';

        return Component.extend({
            defaults: {
                template: 'Cart2Quote_Quotation/quote-checkout/view/fields'
            },

            formSelector: '#quotation-fields',
            allowToUseForm: emailFormUsageObserver.showFields,
            showGuestField: emailFormUsageObserver.showGuestField,
            isCustomerLoggedIn: customer.isLoggedIn,
            loginEnabled: emailFormUsageObserver.allowToUseForm(),

            showQuotationFields: null,
            showNonGuestField: emailFormUsageObserver.showNonGuestField,
            emailRequest: switcher().getShowEmailField(),

            /**
             * Init component
             */
            initialize: function () {
                this._super();
                this.initShowQuotationButton();
                this.allowToUseForm.extend({notify: 'always'});
                emailFormUsageObserver.updateFields();

                registry.async('checkoutProvider')(function (checkoutProvider) {
                    var quotationFieldsData = checkoutQuotationData.getQuotationFieldsFromData();

                    if (quotationFieldsData) {
                        checkoutProvider.set(
                            'quotationFieldData',
                            $.extend({}, checkoutProvider.get('quotationFieldData'), quotationFieldsData)
                        );
                    }
                    checkoutProvider.on('quotationFieldData', function (quotationFieldData) {
                        checkoutQuotationData.setQuotationFieldsFromData(quotationFieldData);
                    });
                });
            },

            /**
             * Validate the fields
             * @return boolean
             */
            validateFields: function () {
                var emailValidationResult = false,
                    loginFormSelector = 'form[data-role=email-with-possible-login]',
                    firstNameSelector = '[name="quotationGuestFieldData.firstname"]',
                    lastNameSelector = '[name="quotationGuestFieldData.lastname"]',
                    telephoneSelector = '[name="quotationGuestFieldData.guest_telephone"]',
                    dobSelector = '[name="quotationFieldData.dob"]',
                    genderSelector = '[name="quotationFieldData.gender"]';

                this.source.set('params.invalid', false);

                if (customer.isLoggedIn()) {
                    emailValidationResult = true;
                } else {
                    $(loginFormSelector).validation();
                    emailValidationResult = Boolean($(loginFormSelector + ' input[name=username]').valid());
                }

                var reg = requirejs('uiRegistry');
                var fieldset = 'checkout.steps.shipping-step.quotation-fields.account-information-fieldsets';

                if (!this.showGuestField() || customer.isLoggedIn()) {
                    $(firstNameSelector).removeClass('_required');
                    $(lastNameSelector).removeClass('_required');

                    $(dobSelector).addClass('_required');
                    reg.get(fieldset + '.' + 'dob', function (item) {
                        item.enable();

                        if (typeof item.source.quotationFieldData !== 'undefined') {
                            if (typeof item.source.quotationFieldData.dob === 'undefined') {
                                item.source.quotationFieldData.dob = $(dobSelector + 'input').val();
                            }
                        }
                    });

                    $(genderSelector).addClass('_required');
                    reg.get(fieldset + '.' + 'gender', function (item) {
                        item.enable();

                        if (typeof item.source.quotationFieldData !== 'undefined') {
                            if (typeof item.source.quotationFieldData.gender === 'undefined') {
                                item.source.quotationFieldData.gender = $(genderSelector + 'select').val();
                            }
                        }
                    });
                } else {
                    $(firstNameSelector).addClass('_required');
                    $(lastNameSelector).addClass('_required');

                    $(dobSelector).removeClass('_required');
                    reg.get(fieldset + '.' + 'dob', function (item) {
                        item.disable();

                        if (typeof item.source.quotationFieldData !== 'undefined') {
                            if (typeof item.source.quotationFieldData.dob !== 'undefined') {
                                delete item.source.quotationFieldData.dob;
                            }
                        }
                    });

                    $(genderSelector).removeClass('_required');
                    reg.get(fieldset + '.' + 'gender', function (item) {
                        item.disable();

                        if (typeof item.source.quotationFieldData !== 'undefined') {
                            if (typeof item.source.quotationFieldData.gender !== 'undefined') {
                                delete item.source.quotationFieldData.gender;
                            }
                        }
                    });

                    if (this.emailRequest()) {
                        delete this.source.quotationGuestFieldData.telephone;
                        $(telephoneSelector).val('-');
                    } else {
                        emailValidationResult = true;
                    }
                    //validate guest fields
                    this.triggerValidateFieldSet('quotationGuestFieldData');
                }

                //validate global fields
                this.triggerValidateFieldSet('quotationFieldData');

                return !(this.source.get('params.invalid')) && emailValidationResult;
            },

            /**
             * Trigger field validation for a fieldset
             *
             * @param fieldSet
             */
            triggerValidateFieldSet: function (fieldSet) {
                this.source.trigger(fieldSet + '.data.validate');
                if (typeof this.source.get('.' + fieldSet) !== 'undefined') {
                    this.source.trigger('.' + fieldSet + '.data.validate');
                }
            },

            /**
             * Init the login button
             */
            initShowQuotationButton: function () {
                var self = this;

                self.showQuotationFields = ko.computed(function () {
                    return self.allowToUseForm() || (self.isCustomerLoggedIn() && !self.allowToUseForm())
                });
            }
        });
    }
);
