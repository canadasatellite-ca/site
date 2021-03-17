/*
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

define(
    [
        'jquery',
        'Magento_Checkout/js/view/shipping',
        'ko',
        'Cart2Quote_Quotation/js/quote-checkout/action/place-quote',
        'Cart2Quote_Quotation/js/quote-checkout/action/update-quote',
        'Magento_Checkout/js/action/set-shipping-information',
        'Magento_Checkout/js/model/shipping-service',
        'Magento_Checkout/js/model/quote',
        'Cart2Quote_Quotation/js/quote-checkout/model/email-form-usage-observer'
    ],
    function (
        $,
        Component,
        ko,
        placeQuoteAction,
        updateQuoteAction,
        setShippingInformationAction,
        shippingService,
        quote,
        emailFormUsageObserver
    ) {
        'use strict';

        return Component.extend({
            allowToUseForm: emailFormUsageObserver.showNonGuestField,
            displayShippingMethods: emailFormUsageObserver.displayShippingMethods
        });
    }
);
