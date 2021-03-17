define([
    'jquery',
    'Magento_Ui/js/modal/modal',
    'mage/mage',
    'jquery/ui'
], function ($, modal) {
    'use strict';

    $.widget('brsw.salesOrderCustomerEmailEdit', {

        _create: function () {
            var options = this.options;
            var orderId = options.orderId;
            var editForm = this.element.find('form');
            var self = this,
                popup_newsletter_options = {
                    type: 'popup',
                    responsive: true,
                    innerScroll: true,
                    buttons: false,
                    modalClass : 'sales-order-email-edit'
                };

            modal(popup_newsletter_options, this.element);
            self.prepare(self.element);

            $('#email-edit').on('click', function(){
                self.element.modal('openModal');
            });

            $('button#email-edit-submit').on('click', function(){

                if ($(editForm).validation('isValid')) {
                    var email = $('#popup-email-field').val();
                    $.ajax({
                        url: $(editForm).attr('action'),
                        cache: true,
                        data: {email:email,orderId:orderId},
                        dataType: 'json',
                        type: 'POST',
                        showLoader: true
                    }).done(function (data) {
                        self.element.find('.messages .message div').html(data.message);
                        if (data.error) {
                            self.element.find('.messages .message').addClass('message-error error');
                        }else{
                            self.element.find('.messages .message')
                                .removeClass('message-error error')
                                .addClass('message-success success');
                            $('.order-account-information-table').find('a[href^=mailto]').text(email);
                        }
                        self.element.find('.messages').show();
                        setTimeout(function() {
                            self.element.find('.messages').hide();
                        }, 5000);
                    });
                }
                return false;
            });
        },

        prepare: function(form){
            var mailToEl = $('.order-account-information-table').find('a[href^=mailto]');

            if (typeof(mailToEl) !== 'undefined'){
                var content = '<span class="admin__page-nav-item-message" id="email-edit">' +
                    '<span class="admin__page-nav-item-message-icon"></span></span>' +
                    '</span>';

                mailToEl.after(content);
                $(form).find('#popup-email-field').val(mailToEl.text());
            }
        }
    });

    return $.brsw.salesOrderCustomerEmailEdit;
});