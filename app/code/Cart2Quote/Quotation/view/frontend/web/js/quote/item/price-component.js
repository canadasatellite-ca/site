/*
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

define(
    [
        'jquery',
        'uiComponent',
        'ko',
        'Magento_Customer/js/customer-data'
    ],
    function (
        $,
        Component,
        ko,
        customerData
    ) {
        'use strict';
        return Component.extend({
            defaults: {
                price: null,
                itemId: null,
                template: 'Cart2Quote_Quotation/quote/item/price'
            },
            initialize: function () {
                this._super();
                customerData.get('quote').subscribe(this.updateItemPrice, this);
            },
            initObservable: function () {
                this.price = ko.observable(this.price);
                return this;
            },
            updateItemPrice: function (quote) {
                var self = this;
                quote.items.filter(function (item) {
                    if (item.item_id == self.itemId) {
                        self.price(item.product_price);
                    }
                });
            }
        });
    }
);