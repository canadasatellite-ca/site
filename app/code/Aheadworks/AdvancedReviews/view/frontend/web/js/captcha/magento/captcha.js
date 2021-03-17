/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

define([
    'Magento_Captcha/js/view/checkout/defaultCaptcha',
    'Magento_Captcha/js/model/captchaList'
],
function (defaultCaptcha, captchaList) {
    'use strict';

    return defaultCaptcha.extend({

        /**
         * {@inheritdoc}
         */
        initialize: function () {
            var currentCaptcha;

            this._super();
            currentCaptcha = captchaList.getCaptchaByFormId(this.formId);

            if (currentCaptcha != null) {
                currentCaptcha.setIsVisible(true);
                this.setCurrentCaptcha(currentCaptcha);
                this.refresh();
            }
        },

        /**
         * Reset captcha
         */
        reset: function () {
            this.currentCaptcha.setCaptchaValue('');
            this.refresh();
        }
    });
});
