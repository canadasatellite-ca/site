/**
 * Cart2Quote
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
        'Magento_Customer/js/model/customer'
    ],
    function (
        $,
        ko,
        Component,
        checkoutQuotationData,
        emailFormUsageObserver,
        $t,
        registry,
        customer
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

            /**
             * Init component
             */
            initialize: function () {
                this._super();

                this.initShowQuotationButton();

                this.allowToUseForm.extend({ notify: 'always' });

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

                return true;
            },

            /**
             * Trigger field validation for a fieldset
             *
             * @param fieldSet
             */
            triggerValidateFieldSet: function (fieldSet) {
                this.source.trigger(fieldSet+'.data.validate');
                if (typeof this.source.get('.'+fieldSet) !== 'undefined') {
                    this.source.trigger('.'+fieldSet+'.data.validate');
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
