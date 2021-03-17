/**
 * Copyright © Mageside. All rights reserved.
 * See MS-LICENSE.txt for license details.
 */
/*browser:true*/
/*global define*/
define(
    [
        'uiComponent',
        'Magento_Checkout/js/model/shipping-rates-validator',
        'Magento_Checkout/js/model/shipping-rates-validation-rules',
        'Mageside_CanadaPostShipping/js/model/shipping-rates-validator',
        'Mageside_CanadaPostShipping/js/model/shipping-rates-validation-rules'
    ],
    function (
        Component,
        defaultShippingRatesValidator,
        defaultShippingRatesValidationRules,
        canadapostShippingRatesValidator,
        canadapostShippingRatesValidationRules
    ) {
        'use strict';
        defaultShippingRatesValidator.registerValidator('canadapost', canadapostShippingRatesValidator);
        defaultShippingRatesValidationRules.registerRules('canadapost', canadapostShippingRatesValidationRules);
        return Component;
    }
);
