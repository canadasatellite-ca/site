define(
    [
        'uiComponent',
        'Magento_Checkout/js/model/payment/additional-validators',
        'BroSolutions_CheckoutReCapcha/js/model/google-recaptcha'
    ],
    function (Component, additionalValidators, recaptcha) {
        'use strict';
        additionalValidators.registerValidator(recaptcha);
        return Component.extend({});
    }
);
