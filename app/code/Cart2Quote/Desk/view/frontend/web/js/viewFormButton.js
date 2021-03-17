/**
 *
 * CART2QUOTE CONFIDENTIAL
 * __________________
 *
 *  [2009] - [2016] Cart2Quote B.V.
 *  All Rights Reserved.
 *
 * NOTICE OF LICENSE
 *
 * All information contained herein is, and remains
 * the property of Cart2Quote B.V. and its suppliers,
 * if any.  The intellectual and technical concepts contained
 * herein are proprietary to Cart2Quote B.V.
 * and its suppliers and may be covered by European and Foreign Patents,
 * patents in process, and are protected by trade secret or copyright law.
 * Dissemination of this information or reproduction of this material
 * is strictly forbidden unless prior written permission is obtained
 * from Cart2Quote B.V.
 *
 * @category    Cart2Quote
 * @package     Desk
 * @copyright   Copyright (c) 2016 Cart2Quote B.V. (https://www.cart2quote.com)
 * @license     https://www.cart2quote.com/ordering-licenses(https://www.cart2quote.com)
 */

define([
    'jquery'
], function ($) {
    'use strict';

    $.widget('desk.viewFormButton', {

        options: {
            bindOnClick: true,
            ticketFormSuccessSelector: '.ticket-form-success-content',
            ticketFormSelector: '.ticket-form-content'
        },

        /**
         * Bind the onclick event
         *
         * @private
         */
        _create: function () {
            if (this.options.bindOnClick) {
                this._bindOnClick();
            }
        },

        /**
         * Show/Hide the ticket fields on click
         *
         * @private
         */
        _bindOnClick: function () {
            var self = this;
            this.element.on('click', function (e) {
                e.preventDefault();
                $(self.options.ticketFormSuccessSelector).hide();
                $(self.options.ticketFormSelector).show();
            });
        },

        /**
         * Format params
         *
         * @param {Object} params
         * @returns {Array}
         */
        prepareParams: function (params) {
            var result = '?';

            _.each(params, function (value, key) {
                result += key + '=' + value + '&';
            });

            return result.slice(0, -1);
        }
    });

    return $.desk.viewFormButton
});
