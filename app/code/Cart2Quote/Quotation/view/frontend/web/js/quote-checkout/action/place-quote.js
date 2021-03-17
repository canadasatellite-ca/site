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
        'Magento_Customer/js/model/customer',
        'mage/url',
        'Magento_Customer/js/customer-data'
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
        customer,
        url,
        customerData
    ) {
        'use strict';

        /**
         * This action handles the quotation placement (saves the quote)
         */
        return function (shippingSameAsBilling, checkoutAsGuest) {

            /**
             * Handle undefined result
             *
             * @return void
             */
            function handleUndefined()
            {
                globalMessageList.addErrorMessage({
                    message: $t('Something went wrong while processing your quote. Please try again later.')
                });

                stopLoader();
                scrollToTop();
            }

            /**
             * Handle when success
             *
             * @return void
             */
            function handleSuccess(quoteId)
            {
                customerData.invalidate(['quote']);
                redirectOnSuccessAction.execute(quoteId);
            }

            /**
             * Handle when error
             *
             * @return void
             * @param result
             */
            function handleError(result)
            {
                if (result.status == 401) {
                    window.location.replace(url.build('customer/account/login/'));
                } else {
                    if (result.message != 'undefined') {
                        $.each(result.message.split('/n'), function (index, errorMessage) {
                            globalMessageList.addErrorMessage({message: errorMessage});
                        });
                    }

                    stopLoader();
                    scrollToTop();
                }
            }

            /**
             * Two times stop loader because placeOrderService creates an extra loader.
             *
             * @return void
             */
            function stopLoader()
            {
                fullScreenLoader.stopLoader(true);
                fullScreenLoader.stopLoader(true);
            }

            /**
             * Scroll the page to the error
             * @reutn void
             */
            function scrollToTop()
            {
                $('html, body').animate({scrollTop: $("#checkout").offset().top}, 500);
            }

            /**
             * Get an object of quote data
             *
             * @return object
             */
            function getQuoteData()
            {
                return {
                    cartId: quote.getQuoteId(),
                    form_key: $.mage.cookies.get('form_key'),
                    customer_email: quote.guestEmail == null ? customer.customerData.email : quote.guestEmail,
                    shipping_same_as_billing: shippingSameAsBilling,
                    checkout_as_guest: checkoutAsGuest,
                    clear_quote: true
                };
            }

            /**
             * Get the request URL
             *
             * @returns {*|string}
             */
            function getRequestUrl()
            {
                return resourceUrlManagerModel.getUrlForPlaceQuote(quote, getQuoteData());
            }

            /**
             * Place the quote
             *
             * @see Quotation/Controller/Quote/Ajax/CreateQuote.php
             */
            return placeOrderService(getRequestUrl(), getQuoteData(), globalMessageList).success(
                function (result) {
                    if (result.error) {
                        handleError(result);
                    } else if (result.success) {
                        handleSuccess(result.last_quote_id);
                    } else {
                        handleUndefined();
                    }
                }
            ).fail(
                function (response) {
                    if (response.status == 404 || response.status == 403) {
                        location.reload();
                    }
                }
            );
        };
    }
);
