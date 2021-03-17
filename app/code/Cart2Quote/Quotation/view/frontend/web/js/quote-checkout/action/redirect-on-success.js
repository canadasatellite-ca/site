/*
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

define(
    [
        'Cart2Quote_Quotation/js/quote-checkout/model/resource-url-manager'
    ],
    function (resourceUrlManager) {
        'use strict';

        return {
            /**
             * Provide redirect to page
             */
            execute: function (quoteId) {
                var url = resourceUrlManager.getUrlForRedirectOnSuccess(quoteId, {id: quoteId});
                window.location.replace(url);
            }
        };
    }
);
