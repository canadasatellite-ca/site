/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

define([
    'MSP_ReCaptcha/js/reCaptcha',
    'MSP_ReCaptcha/js/registry'
],
function (MspReCaptcha, MspReCaptchaRegistry) {
    'use strict';

    return MspReCaptcha.extend({

        /**
         * Reset captcha
         */
        reset: function () {
            var reCaptchaId = this.getReCaptchaId(),
                widgetId = _.findIndex(MspReCaptchaRegistry.ids(), function (captchaId) {
                return captchaId === reCaptchaId;
            });
            if (grecaptcha !== undefined && grecaptcha.reset && widgetId !== undefined && widgetId >= 0) {
                grecaptcha.reset(widgetId);
            }
        }
    });
});
