/*
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'jquery'
], function ($) {
    'use strict';

    return function (widget) {
        $.widget('mage.configurable', widget, {
            options: {
                selectorAddToCart: '#product-addtocart-button',
                selectorAddToCartInstant: '#instant-purchase',
                selectorAddToCartInstantExtra: '[data-action=checkout-form-submit]',
                selectorAddToQuote: '#product-addtoquote-button',
                selectorPrintQuote: '#product-printquote-button',
                dynamicAddButtons: false
            },

            _init: function () {
                this._super();
                if (typeof this.options.spConfig.dynamic_add_buttons !== "undefined") {
                    this.dynamicAddButtons = this.options.spConfig.dynamic_add_buttons;
                }

                this._UpdateButtons();
            },

            _configureElement: function (element) {
                this._super(element);
                this._UpdateButtons();
            },

            _UpdateButtons: function () {
                if (this.dynamicAddButtons) {
                    var widget = this,
                        saleable = widget.options.spConfig.is_saleable,
                        quotable = widget.options.spConfig.is_quotable,

                        cartButton = widget.element.parents(widget.options.selectorProduct)
                            .find(widget.options.selectorAddToCart),
                        cartInstant = widget.element.parents(widget.options.selectorProduct)
                            .find(widget.options.selectorAddToCartInstant),
                        cartInstantExtra = widget.element.parents(widget.options.selectorProduct)
                            .find(widget.options.selectorAddToCartInstantExtra),

                        quoteButton = widget.element.parents(widget.options.selectorProduct)
                            .find(widget.options.selectorAddToQuote),
                        printQuoteButton = widget.element.parents(widget.options.selectorProduct)
                            .find(widget.options.selectorPrintQuote),

                        selectedProduct = this.simpleProduct,

                        //default cart button is true
                        showCartButton = true,

                        //default quote button is true
                        showQuoteButton = true;

                    if (typeof selectedProduct !== "undefined") {
                        showCartButton = saleable[selectedProduct] == 'undefined' ? false : saleable[selectedProduct];
                        showQuoteButton = quotable[selectedProduct] == 'undefined' ? false : quotable[selectedProduct];
                    }

                    //show or hide cart buttons
                    cartButton.toggle(showCartButton);
                    cartInstant.toggle(showCartButton);
                    cartInstantExtra.toggle(showCartButton);

                    //show or hide quote button
                    quoteButton.toggle(showQuoteButton);
                    printQuoteButton.toggle(showQuoteButton);
                }
            },
        });

        return $.mage.configurable;
    }
});