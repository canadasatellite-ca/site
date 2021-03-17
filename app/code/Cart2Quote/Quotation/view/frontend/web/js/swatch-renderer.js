/*
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'jquery'
], function ($) {
    'use strict';

    return function (widget) {
        $.widget('mage.SwatchRenderer', widget, {
            options: {
                selectorAddToCart: '[data-role=addToCartButton]',
                selectorAddToCartInstant: '#instant-purchase',
                selectorAddToCartInstantExtra: '[data-action=checkout-form-submit]',
                selectorAddToQuote: '[data-role=addToQuoteButton]',
                selectorPrintQuote: '#product-printquote-button',
                dynamicAddButtons: false
            },

            _init: function () {
                this._super();
                if (typeof this.options.jsonConfig.dynamic_add_buttons !== "undefined") {
                    this.dynamicAddButtons = this.options.jsonConfig.dynamic_add_buttons;
                }

                this._UpdateButtons();
            },

            _OnClick: function ($this, widget, eventName) {
                this._super($this, widget, eventName);
                widget._UpdateButtons();
            },

            _UpdateButtons: function () {
                if (this.dynamicAddButtons) {
                    var widget = this,
                        saleable = widget.options.jsonConfig.is_saleable,
                        quotable = widget.options.jsonConfig.is_quotable,

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

                        selectedProduct = this.getProduct(),

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

            //This override is to fix the default selection not being selected,
            // because the gallery is not loaded when called from the file:
            // magento/module-swatches/view/frontend/web/js/configurable-customer-data.js
            _EmulateSelectedByAttributeId: function (selectedAttributes) {
                selectedAttributes = $.isEmptyObject(selectedAttributes) ?
                    this.options.jsonConfig.defaultValues :
                    selectedAttributes;
                var context = this._determineProductData().isInProductView ?
                    this.element.parents('.column.main') :
                    this.element.parents('.product-item-info');
                var gallery = context.find(this.options.mediaGallerySelector).data('gallery');

                if (gallery !== undefined) {
                    this._super(selectedAttributes);
                } else {
                    context.find(this.options.mediaGallerySelector).on('gallery:loaded', function (loadedGallery) {
                        this._EmulateSelectedByAttributeId(selectedAttributes);
                    }.bind(this));
                }
            }
        });

        return $.mage.SwatchRenderer;
    }
});