/*
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

define(
    [
        'jquery',
        'jquery/ui'
    ],
    function ($) {
        'use strict';

        $.widget(
            'mage.quotationPriceQuoted',
            {
                _create: function () {
                    this.init();
                },

                init: function () {
                    var self = this;

                    $(self.element).change(function (event) {
                        self.priceListener(event);
                    });
                },

                priceListener: function (event) {
                    var minPrice = parseFloat($(event.target).data("minprice"));

                    if (minPrice > 0) {
                        var origPrice = parseFloat(event.target.defaultValue);
                        var newPrice = parseFloat(event.target.value);
                        this.checkCostPrice(newPrice, minPrice, origPrice, event.target);
                    }
                },

                checkCostPrice: function (newPrice, minPrice, origPrice, target) {
                    if (newPrice < minPrice) {
                        alert($.mage.__('Entered value lower than cost price'));
                        target.value = origPrice;
                    }
                }
            }
        );

        return $.mage.quotationPriceQuoted;
    }
);