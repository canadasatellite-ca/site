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
        'mage/url',
        'mage/translate'
    ],
    function ($, Component, confirmation, customerData, url, $t) {
        'use strict';

        return Component.extend({
            quoteable: null,
            notquoteable: null,

            initialize: function () {
                this._super();
                var self = this;
                var wishlist = customerData.get('wishlist');
                $(self.quoteable).toggle(wishlist().summary_count > 0);
                $(self.notquoteable).toggle(wishlist().summary_count > 0);
                wishlist.subscribe(
                    function (value) {
                        $(self.quoteable).toggle(value.summary_count > 0);
                        $(self.notquoteable).toggle(value.summary_count > 0);
                    }
                );

                $(this.quoteable).click(function () {
                    confirmation({
                        title: $t('Move items to Quote Request'),
                        content: $t('Are you sure you want to move your Wish List to the Quote? Your Wish List will be cleared.'),
                        actions: {
                            confirm: function () {
                                window.location.href = url.build('quotation/quoteawish/update');
                            }
                        }
                    })
                });

                $(this.notquoteable).click(function () {
                    confirmation({
                        title: $t('There are product(s) in your Wish List which are not quotable'),
                        content: $t('Products which have not yet been configured, and products which are not quotable may not be moved to quotation.'),
                    })
                });
            },
        });
    }
);
