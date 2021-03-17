/*
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'jquery'
], function ($) {
    'use strict';

    return function (widget) {
        $.widget('mage.productValidate', widget, {
            options: {
                addToCartButtonSelector: '.action.tocart'
            },
            /**
             * Uses Magento's validation widget for the form object.
             * @private
             */
            _create: function () {
                var self = this;
                var bindSubmit = this.options.bindSubmit;
                var jqForm = $(this.element).catalogAddToCart(
                    $.extend(
                        {bindSubmit: bindSubmit},
                        self.options.catalogAddToCart
                    )
                );

                this.element.validation({
                    radioCheckboxClosest: this.options.radioCheckboxClosest,

                    /**
                     * Uses catalogAddToCart widget as submit handler.
                     * @param {Object} form
                     * @returns {Boolean}
                     */
                    submitHandler: function (form) {
                        jqForm.catalogAddToCart('submitForm', $(form));

                        return false;
                    }
                });
                $(this.options.addToCartButtonSelector).attr('disabled', false);
            }
        });
        return $.mage.productValidate;
    }
});