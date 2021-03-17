/*
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

define(
    [
        'jquery',
        'Magento_Ui/js/modal/modal',
        'mage/translate',
        'Magento_Customer/js/action/login',
    ],
    function ($, modal, $t, loginAction) {
        "use strict";
        $.widget('mage.quickQuoteModal', $.mage.modal, {
            storageKey: 'open-modal-on-reload',
            storage: $.localStorage,
            options: {
                formSelector: '#quick-quote-form',
                wrapperClass: "quick-quote-wrapper",
                title: $t('Quick Quote'),
                buttons: [{
                    text: $t('Request Quote'),
                    class: "action primary requestquote",
                    attr: {
                        id: 'submit-quickquote-btn'
                    },
                    click: function () {
                        var form = this.element.find(this.options.formSelector);
                        if (form.valid()) {
                            $(this.element).trigger('processStart');
                            form.submit();
                        }
                    }
                }, {
                    text: $t('Continue shopping'),
                    class: "action secondary continueshopping",
                    click: function () {
                        this.closeModal();
                    }
                }]
            },
            _create: function () {
                this._super();
                var self = this;

                $(document).on('startProductAddToQuote', function () {
                    $(self.element).trigger('processStart');
                });

                $(document).on('successProductAddToQuote', function () {
                    self.openModal();
                    $(self.element).trigger('processStop');
                });

                if (self.storage.get(self.storageKey)) {
                    self.storage.set(self.storageKey, false);
                    self.openModal();
                }

                loginAction.registerLoginCallback(function () {
                    self.storage.set(self.storageKey, true);
                });
            },
        });
        return $.mage.quickQuoteModal;
    }
);