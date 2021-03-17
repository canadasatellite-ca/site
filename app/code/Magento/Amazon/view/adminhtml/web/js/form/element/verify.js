/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
define([
    'jquery',
    'uiElement',
    'uiRegistry',
    'uiLayout',
    'mageUtils',
    'Magento_Ui/js/modal/alert',
    'mage/translate'
], function ($, Element, registry, layout, utils, alertModal) {
    'use strict';

    return Element.extend({
        defaults: {
            buttonClasses: {},
            additionalClasses: {},
            displayArea: 'outsideGroup',
            displayAsLink: false,
            elementTmpl: 'ui/form/element/button',
            template: 'ui/form/components/button/simple',
            visible: true,
            disabled: false,
            title: ''
        },

        /**
         * Initializes component.
         *
         * @returns {Object} Chainable.
         */
        initialize: function () {
            return this._super()
                ._setClasses();
        },

        /** @inheritdoc */
        initObservable: function () {
            return this._super()
                .observe([
                    'visible',
                    'disabled',
                    'title'
                ]);
        },

        /**
         * Performs configured actions
         */
        action: function () {
            this.verifyAccountCredentials();
        },

        /**
         * Performs Ajax call to verify account credentials (opens alert w/ results)
         */
        verifyAccountCredentials: function () {

            var reloadurl = $('#cred-success').attr('data-storeurl');

            $.ajax({
                url: reloadurl,
                data: {
                    "seller_id":  registry.get('index = seller_id').value(),
                    "country_code":  registry.get('index = country_code').value(),
                    "aws_access_key":  registry.get('index = aws_access_key').value(),
                    "secret_key":  registry.get('index = secret_key').value()
                },
                cache: false,
                dataType: "text",
                showLoader: true,
                type: "GET",
                success: function (response) {

                    if (response == 1) {
                        alertModal({
                            title: $.mage.__('Amazon Credentials'),
                            content:  $('#cred-success').html(),
                            actions: {
                                always: function (){}
                            }
                        });
                    } else {
                        alertModal({
                            title: $.mage.__('Amazon Credentials'),
                            content: $('#cred-failure').html(),
                            actions: {
                                always: function (){}
                            }
                        });
                    }
                },
                error: function (response) {
                    alertModal({
                        title: $.mage.__('Amazon Credentials'),
                        content:  $('#cred-failure').html(),
                        actions: {
                            always: function (){}
                        }
                    });
                }
            });
        },

        /**
         * Extends 'additionalClasses' object.
         *
         * @returns {Object} Chainable.
         */
        _setClasses: function () {
            if (typeof this.additionalClasses == 'string') {
                this.additionalClasses = this.additionalClasses
                    .trim()
                    .split(' ')
                    .reduce(function (classes, name) {
                        classes[name] = true;

                        return classes;
                    }, {});
            }

            return this;
        }
    });
});
