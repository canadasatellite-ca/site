/*
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

define(
    [
        'jquery',
        'Magento_Customer/js/model/authentication-popup',
        'mage/url',
        'mage/cookies'
    ],
    function ($, authenticationPopup, url) {
        'use strict';

        return function (config, element) {
            $(element).click(function (event) {
                var date = new Date();
                date.setTime(date.getTime() + 420000);
                event.preventDefault();
                $.cookie('login_redirect', url.build('quotation/quote'), {expires: date});
                authenticationPopup.showModal();
                return false;
            });
        };
    }
);
