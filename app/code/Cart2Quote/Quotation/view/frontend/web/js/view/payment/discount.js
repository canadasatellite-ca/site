/*
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'jquery',
    'Magento_Checkout/js/model/quote',
    'Magento_Ui/js/modal/modal'
],function ($, quote, modal) {
    'use strict';

    var mixin = {
        defaults: {
            template: 'Cart2Quote_Quotation/payment/discount'
        },

        isQuotationQuote: function () {
            let linkedId = window.checkoutConfig.quoteData.linked_quotation_id;
            var couponConfigDisabled = $.parseJSON(window.checkoutConfig.quotationQuoteCouponDisabled);
            if (linkedId > 0 && couponConfigDisabled) {
                return false;
            }

            return true;
        }
    };

    return function (target) {
        return target.extend(mixin);
    };
});