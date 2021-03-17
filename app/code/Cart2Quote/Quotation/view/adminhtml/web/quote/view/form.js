/*
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

/* global AdminQuote */
define([
    "jquery",
    "Cart2Quote_Quotation/quote/view/scripts"
], function (jQuery) {
    'use strict';

    var $el = jQuery('#edit_form'),
        config,
        baseUrl,
        quote,
        payment;

    if (!$el.length || !$el.data('quote-config')) {
        return;
    }

    config = $el.data('quote-config');
    baseUrl = $el.data('load-base-url');

    quote = new AdminQuote(config);
    quote.setLoadBaseUrl(baseUrl);

    payment = {
        switchMethod: quote.switchPaymentMethod.bind(quote)
    };

    window.quote = quote;
    window.payment = payment;
});