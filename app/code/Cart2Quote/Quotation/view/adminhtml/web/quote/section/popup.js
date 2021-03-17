/*
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

define(
    [
        'jquery',
        'Magento_Ui/js/modal/modal',
        'mage/translate'
    ],
    function ($, modal, $t) {
        "use strict";
        //creating jquery widget
        $.widget('mage.quotationPopup', $.mage.modal, {
            options: {
                elementId: null,
                title: $t('Sections'),
                buttons: [{
                    text:
                        $t('Cancel'),
                    class: 'action-default scalable action-secondary',
                    click: function () {
                        this.closeModal();
                    }
                }, {
                    text: $t('Save'),
                    class: 'action-default scalable action-primary',
                    click: function () {
                        var self = this;
                        $.ajax({
                            url: $('#section-form').attr('action'),
                            data: $('#section-form').serialize(),
                            type: 'post',
                            dataType: 'json',
                            showLoader: true,
                            /** @inheritdoc */
                            success: function (res) {
                                location.reload();
                                self.closeModal();
                            }
                        });
                    }
                }]
            },
            /**
             * @protected
             */
            _create: function () {
                this._prepareDialog();
            },

            /**
             * Show modal
             */
            showDialog: function () {
                this.dialog.modal('openModal');
                $("#section-form").submit(function (e) {
                    e.preventDefault();
                    return false;
                });
            },
            /**
             * Prepare modal
             * @protected
             */
            _prepareDialog: function () {
                this.dialog = $(this.options.elementId).modal(this.options);
            }
        });

        return $.mage.quotationPopup;
    }
);