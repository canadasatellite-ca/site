/*
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

define(
    [
        'jquery',
        'uiComponent',
        'Magento_Customer/js/customer-data'
    ],
    function (
        $,
        Component,
        customerData
    ) {
        'use strict';
        return function (widget) {
            var sections = ['quote'];

            return $.widget('mage.sidebar', $.mage.sidebar, {

                _showItemButton: function (elem) {
                    if (elem.data('cart-item')) {
                        return this._super(elem);
                    }
                    var itemId = elem.data('quote-item'),
                        itemQty = elem.data('item-qty');

                    if (this._isValidQty(itemQty, elem.val())) {
                        $('#update-quote-item-' + itemId).show('fade', 300);
                    } else if (elem.val() == 0) { //eslint-disable-line eqeqeq
                        this._hideItemButton(elem);
                    } else {
                        this._hideItemButton(elem);
                    }
                },

                _hideItemButton: function (elem) {
                    if (elem.data('cart-item')) {
                        return this._super(elem);
                    }
                    var itemId = elem.data('quote-item');

                    $('#update-quote-item-' + itemId).hide('fade', 300);
                },

                _removeItem: function (elem) {
                    if (elem.data('cart-item')) {
                        return this._super(elem);
                    }
                    var itemId = elem.data('quote-item');

                    this._ajax(this.options.url.remove, {
                        'item_id': itemId
                    }, elem, this._removeQuoteItemAfter);
                },

                _updateItemQty: function (elem) {
                    if (elem.data('cart-item')) {
                        return this._super(elem);
                    }
                    var itemId = elem.data('quote-item');

                    this._ajax(this.options.url.update, {
                        'item_id': itemId,
                        'item_qty': $('#quote-item-' + itemId + '-qty').val()
                    }, elem, this._updateQuoteItemQtyAfter);
                },

                _updateQuoteItemQtyAfter: function (elem) {
                    var productData = this._getQuoteProductById(Number(elem.data('quote-item')));

                    if (!_.isUndefined(productData)) {

                        if (window.location.href === this.quoteCartUrl()) {
                            window.location.reload(false);
                        }
                    }
                    this._hideItemButton(elem);

                    customerData.reload(sections, true);
                },

                _removeQuoteItemAfter: function (elem) {
                    var productData = this._getProductById(Number(elem.data('quote-item')));

                    if (!_.isUndefined(productData)) {
                        if (window.location.href === this.quoteCartUrl()) {
                            window.location.reload(false);
                        }
                    }
                    customerData.reload(sections, true);
                },

                _getQuoteProductById: function (productId) {
                    return _.find(customerData.get('quote')().items, function (item) {
                        return productId === Number(item['item_id']);
                    });
                },

                quoteCartUrl: function () {
                   if (window.quotation.checkoutUrl) {
                       return window.quotation.checkoutUrl;
                   }
                },
            });
        }
});