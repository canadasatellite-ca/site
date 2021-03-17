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
    'jquery/ui'
], function ($, $t) {
    'use strict';

    $.widget('desk.createMessage', {

        options: {
            processStart: null,
            processStop: null,
            bindSubmit: true,
            renderMessage: true,
            messageBoxSelector: '#comment',
            subjectBoxSelector: '#ticket-subject',
            messagesSelector: '[data-placeholder="messages"]',
            sendMessageButtonSelector: '#send-message-button',
            sendMessageButtonDisabledClass: 'disabled',
            sendMessageButtonTextWhileAdding: $t("Sending..."),
            sendMessageButtonTextAdded: $t("Message Send"),
            sendMessageButtonTextDefault: $t("Send"),
            updateMessageBox: '#ticket_messages_details',
            pageTitleSelector: '.page-title-wrapper .page-title span'
        },

        /**
         * Binds the submit on create
         *
         * @private
         */
        _create: function() {
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
         * Checks if the load is enabled
         *
         * @returns {null}
         */
        isLoaderEnabled: function() {
            return this.options.processStart && this.options.processStop;
        },

        /**
         * Alternative submit form
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

            var params = {ticket_id: form.data('ticketId'), render_message: self.options.renderMessage};

            $.ajax({
                url: form.attr('action') + self.prepareParams(params),
                data: form.serialize(),
                type: 'post',
                dataType: 'json',
                beforeSend: function() {
                    $(self.options.messageBoxSelector).prop("disabled", true);
                    $(self.options.subjectBoxSelector).prop("disabled", true);
                    if (self.isLoaderEnabled()) {
                        $('body').trigger(self.options.processStart);
                    }
                },
                success: function(res) {
                    if (self.isLoaderEnabled()) {
                        $('body').trigger(self.options.processStop);
                    }

                    if (res.backUrl) {
                        window.location = res.backUrl;
                        return;
                    }

                    if (res.messages) {
                        $(self.options.messagesSelector).html(res.messages);
                    }

                    if(res.ticket_id){
                        form.data('ticketId', res.ticket_id);
                        self.updateTicketId(res.ticket_id);
                        self.updateHeader(form, res.ticket_id);

                        $(self.options.messageBoxSelector).val('');
                    }

                    if(res.message_id){
                        self.updateLastMessageId(res.message_id);
                    }

                    if (res.message_html) {
                        self.updateMessageHtml(res.message_html);
                    }

                    self.enableSendMessageButton(form);
                    $(self.options.messageBoxSelector).prop("disabled", false);
                    $(self.options.subjectBoxSelector).prop("disabled", false);
                },
                error: function(res){
                    if (self.isLoaderEnabled()) {
                        $('body').trigger(self.options.processStop);
                    }

                    if (res.backUrl) {
                        window.location = res.backUrl;
                        return;
                    }

                    if (res.messages) {
                        $(self.options.messagesSelector).html(res.messages);
                    }

                    self.enableSendMessageButton(form);
                    $(self.options.messageBoxSelector).prop("disabled", false);
                    $(self.options.subjectBoxSelector).prop("disabled", false);
                }
            });
        },

        /**
         * Format params
         *
         * @param {Object} params
         * @returns {Array}
         */
        prepareParams: function (params){
            var result = '?';

            _.each(params, function (value, key) {
                result += key + '=' + value + '&';
            });

            return result.slice(0, -1);
        },

        /**
         * Updates the ticket ID in the element
         *
         * @param ticketId
         */
        updateTicketId: function(ticketId){
            $(this.options.updateMessageBox).data('ticketId', ticketId);
        },

        /**
         * Updates the last message ID in the element
         *
         * @param ticketId
         */
        updateLastMessageId: function(messageId){
            $(this.options.updateMessageBox).data('lastId', messageId);
        },

        /**
         * Updates the messages HTML
         *
         * @param newHtml
         */
        updateMessageHtml: function(newHtml){
            $(this.options.updateMessageBox)
                .children()
                .removeClass('message-details-first')
                .addClass('message-details');
            $(this.options.updateMessageBox).prepend(newHtml);
        },

        /**
         * Updates the header with the new ticket ID
         *
         * @param ticketId
         */
        updateHeader: function(form, ticketId){
            $(this.options.pageTitleSelector).html(form.data('headerTranslation').replace('%1', ticketId));
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
