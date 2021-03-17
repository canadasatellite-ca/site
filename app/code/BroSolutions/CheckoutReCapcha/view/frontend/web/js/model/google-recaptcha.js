define(
    [
        'mage/translate',
        'Magento_Ui/js/model/messageList',
        'jquery',
        'mage/url'
    ],
    function ($t, messageList, $, urlBuilder) {
        'use strict';
        return {
            validate: function () {
                if (window.checkout.kjip && window.checkout.adhs == grecaptcha.getResponse()) {
                    return true;
                }

                messageList.addErrorMessage({ message: $t('Incorrect reCaptcha') });
                return false;
            }
        }
    }
);
