/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
define(
    [
        'jquery',
        'Magento_Ui/js/modal/modal'
    ],
    function ($) {
        "use strict";
        $.widget('Magento.modalComplete', {
            _create: function () {
                this.options.modalOption = this._getModalOptions();
                this._bind();
            },
            _getModalOptions: function () {
                var options;
                var reloadUrlYes = this.options.successUrl,
                    reloadUrlNo = this.options.failureUrl;

                options = {
                    type: 'popup',
                    responsive: true,
                    innerScroll: true,
                    title: this.options.modalTitle,
                    buttons: [{
                        text: "Do Not Activate Account",
                        "class": "cancel-changes",
                        click: function () {
                            window.location.href = reloadUrlNo;
                        }
                    }, {
                        text: "Activate Account",
                        "class": "primary accept-changes",
                        click: function () {
                            window.location.href = reloadUrlYes;
                        }
                    }]
                };
                return options;
            },
            _bind: function () {
                var modalOption = this.options.modalOption;
                var modalForm = this.options.modalId;

                $(document).on('click', this.options.modalTarget,  function () {
                    $(modalForm).modal(modalOption);
                    $(modalForm).trigger('openModal');
                });
            },
            /**
             * Cancels category update action.
             */
            cancelCategoryChanges: function (url) {
                window.location.replace(url);
            }
        });

        return $.Magento.modalComplete;
    }
);
