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
    'jquery',
    'mage/translate',
    'Magento_Customer/js/customer-data',
    'jquery/ui',
    'mage/validation'
], function ($, $t, customerData) {
    'use strict';

    $.widget('desk.createMessage', {

        options: {
            processStart: null,
            processStop: null,
            bindSubmit: true,
            messagesSelector: '[data-placeholder="messages"]',
            messageBoxSelector: '#ticket-message-field',
            viewYourTicketSelector: '#view-ticket-button',
            nonLoggedInUserFieldsSelector: '.ticket-non-registered-users',
            formInputSelector: '.ticket-form input',
            sendMessageButtonSelector: '#send-ticket-button',
            sendMessageButtonDisabledClass: 'disabled',
            sendMessageButtonTextWhileAdding: $t("Sending..."),
            sendMessageButtonTextAdded: $t("Question Send"),
            sendMessageButtonTextDefault: $t("Ask a Question"),
            ticketFormSuccessSelector: '.ticket-form-success-content',
            ticketFormSelector: '.ticket-form-content'
        },

        /**
         * Binds the submit on create
         *
         * @private
         */
        _create: function() {
            var fullname = customerData.get('customer')().fullname;

            if(fullname){
                $(this.options.nonLoggedInUserFieldsSelector).hide();
            }

            if (this.options.bindSubmit) {
                this._bindSubmit();
            }
        },

        /**
         * If the form is valid, bind the submit to an alternative submit
         *
         * @private
         */
        _bindSubmit: function() {
            var self = this;
            this.element.on('submit', function(e) {
                e.preventDefault();

                if($(this).validation('isValid')){
                    self.submitForm($(this));
                }
            });
        },

        /**
         * Check if the loader is enabled
         *
         * @returns {null}
         */
        isLoaderEnabled: function() {
            return this.options.processStart && this.options.processStop;
        },

        /**
         * Bind alternative submit action
         *
         * @param form
         */
        submitForm: function(form) {
            this.ajaxSubmit(form);
        },

        /**
         * Submits the form via AJAX
         * Before sending disabling the buttons and fields
         * On success enabled the buttons and fields again
         *
         * @param form
         */
        ajaxSubmit: function(form) {
            var self = this;
            $(self.options.minicartSelector).trigger('contentLoading');
            self.disableSendMessageButton(form);

            $.ajax({
                url: form.attr('action'),
                data: form.serialize(),
                type: 'post',
                dataType: 'json',
                beforeSend: function() {
                    $(self.options.messageBoxSelector).prop("disabled", true);
                    $(self.options.formInputSelector).prop("disabled", true);
                    if (self.isLoaderEnabled()) {
                        $('body').trigger(self.options.processStart);
                    }
                },
                success: function(res) {
                    if (self.isLoaderEnabled()) {
                        $('body').trigger(self.options.processStop);
                    }

                    if (res.messages) {
                        $(self.options.messagesSelector).html(res.messages);
                    }

                    if (res.backUrl) {
                        window.location = res.backUrl;
                        return;
                    }

                    if(res.ticket_id){
                        $(self.options.viewYourTicketSelector).attr('data-ticket-id', res.ticket_id);

                        $(self.options.ticketFormSuccessSelector).show();
                        $(self.options.ticketFormSelector).hide();
                    }
                    self.enableSendMessageButton(form);
                    $(self.options.messageBoxSelector).val('');
                    $(self.options.formInputSelector).val('');
                    $(self.options.messageBoxSelector).prop("disabled", false);
                    $(self.options.formInputSelector).prop("disabled", false);
                }
            });
        },

        /**
         * Disable the send message button
         *
         * @param form
         */
        disableSendMessageButton: function(form) {
            var sendMessageButton = $(form).find(this.options.sendMessageButtonSelector);
            sendMessageButton.addClass(this.options.sendMessageButtonDisabledClass);
            sendMessageButton.attr('title', this.options.sendMessageButtonTextWhileAdding);
            sendMessageButton.find('span').text(this.options.sendMessageButtonTextWhileAdding);
        },

        /**
         * Enable the send message button
         *
         * @param form
         */
        enableSendMessageButton: function(form) {
            var self = this,
                sendMessageButton = $(form).find(this.options.sendMessageButtonSelector);

            sendMessageButton.find('span').text(this.options.sendMessageButtonTextAdded);
            sendMessageButton.attr('title', this.options.sendMessageButtonTextAdded);

            setTimeout(function() {
                sendMessageButton.removeClass(self.options.sendMessageButtonDisabledClass);
                sendMessageButton.find('span').text(self.options.sendMessageButtonTextDefault);
                sendMessageButton.attr('title', self.options.sendMessageButtonTextDefault);
            }, 1000);
        }
    });

    return $.desk.createMessage
});
