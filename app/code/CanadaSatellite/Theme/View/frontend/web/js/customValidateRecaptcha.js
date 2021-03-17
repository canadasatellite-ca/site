define([
    "jquery"
], function ($) {
    'use strict';

    function validateRecaptcha(e) {
        var responseField = $('.g-recaptcha-response', this);
        var requiredMessageElem = $('.captcha-required', this);
        if ('' !== responseField.val()) {
            requiredMessageElem.hide();
            return true;
        } else {
            requiredMessageElem.show();
            return false;
        }
    }

    return function () {
        $(document).ready( function() {
            $('.field-recaptcha').each(function(index, elem){
                var container = $(elem);
                container.closest('form').on('submit', validateRecaptcha);

                var html = '<span class="captcha-required" style="display: none; font-size: 1.2rem; color: rgb(255, 0, 0);">Please Fill Recaptcha To Continue</span>';
                container.after(html);
            });
        });
    };

});