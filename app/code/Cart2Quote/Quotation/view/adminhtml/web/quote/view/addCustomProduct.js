/*
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

define(
    [
        'jquery',
        'Magento_Ui/js/modal/modal',
        'mage/translate',
        'Magento_Ui/js/modal/alert'
    ],
    function ($, modal, $t, alert) {
        "use strict";
        //creating jquery widget
        $.widget('mage.addCustomProductModal', $.mage.modal, {
            options: {
                title: $t('Add Custom Product'),
                type: 'slide',
                submitForm: '#custom_product_form',
                optionsForm: 'custom_product_options',
                configureForm: '#product_composite_configure_form',
                buttons: [{
                    text: $t('Cancel'),
                    class: 'action-default scalable action-secondary',
                    click: function () {
                        this.closeModal();
                    }
                }, {
                    text: $t('Add Custom Product'),
                    class: 'action-default scalable action-primary',
                    attr: {
                        id: "button-id"
                    },
                    click: function () {
                    }
                }]
            },
            existingProductRequest: function (productId, productOptions, productQty) {
                var self = this;
                $(self.options.configureForm).append($.map(productOptions, function (param) {
                    if (param.option_id) {
                        return $('<input>', {
                            type: 'hidden',
                            class: self.options.optionsForm,
                            name: "item[" + param.product_id + "][options][" + param.option_id + "]",
                            value: param.option_value
                        })
                    }
                }));
                quote.gridProducts.set(productId, {
                    qty: productQty,
                });
            },
            newProductRequest: function (productId, productQty) {
                quote.gridProducts.set(productId, {
                    qty: productQty,
                });
            },
            closeAndResetForm: function () {
                var self = this;
                self.closeDialog();
                quote.productGridAddSelected();
                $(self.dialog).find('input:text').val('');
                $('.' + self.options.optionsForm).remove();
            },
            _create: function () {
                var self = this;
                this._prepareDialog();
                this.element.on('click', function () {
                    self.showDialog();
                });
                $('#button-id').one('click', this.submitFormAjax.bind(this));
            },
            submitFormAjax: function () {
                var self = this;
                var form = $(self.options.submitForm);
                if (form.valid()) {
                    var productData = quote.serializeData(self.dialog.attr('id')).toJSON();
                    $.ajax({
                        type: 'POST',
                        url: this.options.url,
                        dataType: 'json',
                        data: productData
                    }).success(function (resultJson) {
                        if (typeof resultJson.errorMsg === 'undefined') {
                            var productQty = resultJson.qty;
                            var productId = resultJson.product_id;
                            if (typeof resultJson.productOptions === 'undefined') {
                                self.newProductRequest(productId, productQty);
                            } else {
                                var productOptions = resultJson.productOptions;
                                self.existingProductRequest(productId, productOptions, productQty);
                            }
                            self.closeAndResetForm();
                        } else {
                            quote.productGridAddSelected();
                            alert({
                                title:'Custom product not available!',
                                content: resultJson.errorMsg
                            });
                        }
                    });
                }
            },
            /**
             * Show modal
             */
            showDialog: function () {
                this.dialog.modal('openModal');
            },
            /**
             * Close modal
             */
            closeDialog: function () {
                this.dialog.modal('closeModal');
            },
            /**
             * Prepare modal
             */
            _prepareDialog: function () {
                this.dialog = $(this.options.elementId).modal(this.options);
            },
        });

        return $.mage.addCustomProductModal;
    },
);
