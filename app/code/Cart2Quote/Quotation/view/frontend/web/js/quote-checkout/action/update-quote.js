/*
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

define(
    [
        'jquery',
        'Magento_Checkout/js/model/quote',
        'Magento_Checkout/js/model/place-order',
        'Cart2Quote_Quotation/js/quote-checkout/action/redirect-on-success',
        'Cart2Quote_Quotation/js/quote-checkout/model/resource-url-manager',
        'mage/translate',
        'Magento_Ui/js/model/messageList',
        'Magento_Checkout/js/model/full-screen-loader',
        'Cart2Quote_Quotation/js/quote-checkout/checkout-data-quotation',
        'Cart2Quote_Quotation/js/quote-checkout/action/update-quote-silent',
    ],
    function (
        $,
        quote,
        placeOrderService,
        redirectOnSuccessAction,
        resourceUrlManagerModel,
        $t,
        globalMessageList,
        fullScreenLoader,
        checkoutDataQuotation,
        updateQuoteSilentService
    ) {
        'use strict';

        /**
         * This action handles the update quotation action
         */
        return function (silent) {
            var quoteRequestUrl, quoteRequestParams, quoteData;

            /**
             * The quote request params that are being send with the request
             *
             * @type {object}
             */
            quoteRequestParams = {
                cartId: quote.getQuoteId(),
                form_key: $.mage.cookies.get('form_key')
            };

            /**
             * The quote data that is being send with the request
             *
             * @type {object}
             */
            quoteData = {
                quotation_guest_field_data: JSON.stringify(checkoutDataQuotation.getQuotationGuestFieldsFromData()),
                quotation_field_data: JSON.stringify(checkoutDataQuotation.getQuotationFieldsFromData()),
                quotation_product_data: JSON.stringify(checkoutDataQuotation.getQuotationProductsFromData()),
                quotation_store_config_data: JSON.stringify(checkoutDataQuotation.getQuotationConfigDataFromData())
            };

            /**
             * Get request URL
             *
             * @type {string}
             */
            quoteRequestUrl = resourceUrlManagerModel.getUrlForUpdateQuote(quote, quoteRequestParams);

            /**
             * Request quotation update
             *
             * @see Quotation/Controller/Quote/Ajax/UpdateQuote.php
             */
            if (silent) {
                return updateQuoteSilentService(quoteRequestUrl, quoteData, globalMessageList);
            }

            return placeOrderService(quoteRequestUrl, quoteData, globalMessageList);
        };
    }
);
