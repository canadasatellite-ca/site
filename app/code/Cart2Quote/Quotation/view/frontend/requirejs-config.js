/*
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

var config = {
    config: {
        mixins: {
            'Magento_Swatches/js/swatch-renderer': {
                'Cart2Quote_Quotation/js/swatch-renderer': true
            },
            'Magento_Catalog/js/validate-product': {
                'Cart2Quote_Quotation/js/validate-product': true
            },
            'Magento_ConfigurableProduct/js/configurable': {
                'Cart2Quote_Quotation/js/configurable': true
            },
            'Magento_Checkout/js/sidebar': {
                'Cart2Quote_Quotation/js/view/sidebar': true
            },
            'Magento_SalesRule/js/view/payment/discount': {
                'Cart2Quote_Quotation/js/view/payment/discount': true
            }
        }
    },

    map: {
        "*": {
            catalogAddToCart: 'Cart2Quote_Quotation/js/catalog-add-to-cart',
            productUpdater: 'Cart2Quote_Quotation/js/quote-checkout/view/product-updater',
            loginPopup: 'Cart2Quote_Quotation/js/login-popup',
            movetoquote: 'Cart2Quote_Quotation/js/checkout/cart/movetoquote',
            quoteawish: 'Cart2Quote_Quotation/js/wishlist/quoteawish',
            cartToQuoteActions: 'Cart2Quote_Quotation/js/checkout/cart/cart-to-quote-actions',
            quickQuoteModal: 'Cart2Quote_Quotation/js/quote/request/quickquote/modal',
            addTierQty: 'Cart2Quote_Quotation/js/quote-checkout/action/add-tier-qty',
            quoteToCartActions: 'Cart2Quote_Quotation/js/checkout/quote/quote-to-cart-actions',
            fileUploader: 'Cart2Quote_Quotation/js/quote-checkout/view/file-upload',
            miniQuoteReload: 'Cart2Quote_Quotation/js/quote-checkout/miniquote-reload'
        }
    }
};
