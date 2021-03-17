/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
define(
    [
        'jquery'
    ],
    function ($) {
        return function (config) {
            var menuUrl = config.menuUrl;
            var credsUrl = config.credsUrl;
            var listingUrl = config.listingUrl;
            var ordersUrl = config.ordersUrl;
            var listingRulesUrl = config.listingRulesUrl;
            var pricingRulesUrl = config.pricingRulesUrl;
            var current = config.current;
            var backTarget = config.backTarget;

            $(backTarget).on('click', function () {

                switch (current) {
                    case '1':
                        window.location.href = menuUrl;
                        break;
                    case '2':
                        window.location.href = credsUrl;
                        break;
                    case '3':
                        window.location.href = listingUrl;
                        break;
                    case '4':
                        window.location.href = listingRulesUrl;
                        break;
                    case '5':
                        window.location.href = listingRulesUrl;
                        break;
                    case '6':
                        window.location.href  = ordersUrl;
                        break;
                    case '7':
                        window.location.href  = pricingRulesUrl;
                        break;
                }
            });
        }
    }
);