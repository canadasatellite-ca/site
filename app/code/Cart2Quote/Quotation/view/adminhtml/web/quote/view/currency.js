/*
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */


define([
    "jquery",
    'Magento_Catalog/js/price-utils'
], function ($, priceUtils) {
    'use strict';

    $.widget('mage.quotationCurrency', {
        options: {
            inputSelector: null
        },

        _create: function () {
            var self = this;
            $('#' + this.options.inputSelector).change(function (event) {
                var inputData = event.target.value;
                self.innerHtml = self.getFormattedPrice(inputData);
            });
        },

        getFormattedPrice: function (price) {
            return priceUtils.formatPrice(price, 2, true);
        }
    });

    return $.mage.quotationCurrency;
});