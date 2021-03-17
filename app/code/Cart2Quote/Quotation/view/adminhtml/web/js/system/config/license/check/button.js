/*
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'jquery',
    'Cart2Quote_Quotation/js/system/config/license/action/check',
    'jquery/ui',
    'mage/backend/button'
], function ($, licenseCheckAction) {

    $.widget('cart2quote.licenseCheckButton', $.ui.button, {
        options: {
            url: '',
        },
        _create: function () {
            this._super();
            if (this.options.url) {
                this._bind();
            }
            licenseCheckAction.registerLoginCallback(
                function () {

                }
            );
        },
        /**
         * Button click handler.
         * @protected
         */
        _click: function () {
            var licenceData = {
                orderId: $('#quotation_general_general_order_id').val()
            };
            $('input#quotation_general_general_order_id').validation();
            if ($('input#quotation_general_general_order_id').validation('isValid')) {
                licenseCheckAction(this.options.url, licenceData);
            }
        }
    });

    return $.cart2quote.licenseCheckButton;
});