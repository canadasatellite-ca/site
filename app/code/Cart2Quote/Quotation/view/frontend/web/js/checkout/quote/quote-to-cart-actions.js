/*
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */
define(
    [
        'jquery',
        'uiComponent',
        'Magento_Ui/js/modal/confirm',
        'Magento_Customer/js/customer-data',
        'Magento_Customer/js/section-config',
        'mage/url',
        'mage/translate'
    ],
    function ($, Component, confirmation, customerData, sectionConfig, url, $t) {
        'use strict';

        return Component.extend({
            buttonSelector: null,
            directQuoteSelector: null,

            initialize: function () {
                this._super();
                var self = this;
                var quote = customerData.get('quote');
                $(self.buttonSelector).toggle(quote().summary_count > 0);
                $(self.directQuoteSelector).toggle(quote().summary_count > 0);
                quote.subscribe(
                    function (value) {
                        $(self.buttonSelector).toggle(value.summary_count > 0);
                        $(self.directQuoteSelector).toggle(value.summary_count > 0);
                    }
                );
                $(this.buttonSelector).click(function () {
                    confirmation({
                        title: $t('Move items to shopping Cart'),
                        content: $t('Are you sure you want to move the Quote Cart to the shopping Cart? Your Quote Cart will be cleared.'),
                        actions: {
                            confirm: function () {
                                var action = 'quotation/movetocart';
                                var sections = sectionConfig.getAffectedSections(action);

                                if (sections) {
                                    customerData.invalidate(sections);
                                }
                                window.location.href = url.build(action);
                            }
                        }
                    })
                });
                $(this.directQuoteSelector).click(function () {
                    confirmation({
                        title: $t('Submit Quote Request'),
                        content: $t('Are you sure you want to submit quote request? Your shopping Cart will be cleared.'),
                        actions: {
                            confirm: function () {
                                window.location.href = url.build('quotation/quote/directquote');
                            }
                        }
                    })
                });
            },
        });
    }
);