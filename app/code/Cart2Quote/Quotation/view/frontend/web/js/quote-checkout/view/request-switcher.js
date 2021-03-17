/*
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

define(
    [
        'jquery',
        'uiComponent',
        'ko',
        'Cart2Quote_Quotation/js/quote-checkout/model/email-form-usage-observer'
    ],
    function (
        $,
        Component,
        ko,
        emailFormUsageObserver
    ) {
        'use strict';

        return Component.extend({
            defaults: {
                listens: {
                    radioSelection: 'toggleFormRequestMethod'
                }
            },

            showEmailField: ko.observable(false),
            radioSelection: ko.observable('email'),
            showSwitcher: emailFormUsageObserver.displayMethodSwitcher,

            /**
             * Initializes observable properties of instance
             *
             * @returns {Object} Chainable.
             */
            initObservable: function () {
                this._super();
                this.toggleFormRequestMethod();

                return this;
            },

            toggleFormRequestMethod: function () {
                var phoneSelector = document.getElementsByName("quotationGuestFieldData.guest_telephone");
                var phoneFieldSelector = document.getElementsByName("guest_telephone");
                if (this.radioSelection() == 'phone') {
                    this.showEmailField(false);
                    $(phoneSelector).show();
                    $(phoneFieldSelector).val('');
                } else {
                    this.showEmailField(true);
                    $(phoneSelector).hide();
                    $(phoneSelector).removeClass('_required');
                    $(phoneFieldSelector).removeClass('_required');
                    $(phoneFieldSelector).val('-');
                }
            },

            getShowEmailField: function () {
                return this.showEmailField;
            }
        });
    }
);
