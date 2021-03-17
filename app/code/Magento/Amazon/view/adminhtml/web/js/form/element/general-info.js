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
    'uiRegistry',
    'mage/translate'
], function ($, Element, registry, layout, utils, alert, uiRegistry) {
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
         * Sends general store info to host
         */
        action: function () {
            this.setupStore();
        },

        /**
         * Performs Ajax call to setup Amazon store
         */
        setupStore: function () {

            var ajaxurl = $('#marketplace-save-link').attr('data-storeurl');
            var reloadurl = $('#marketplace-reload').attr('data-storeurl');

            $.ajax({
                url: ajaxurl,
                data: {
                    "email":  registry.get('index = email').value(),
                    "name":  registry.get('index = name').value(),
                    "base_url":  encodeURIComponent(registry.get('index = base_url').value()),
                    "country_code":  registry.get('index = country_code').value()
                },
                cache: false,
                dataType: "text",
                showLoader: true,
                type: "GET",
                success: function (response) {
                    if (response) {
                        window.location.href = reloadurl.concat("merchant_id/", response, "/general/true/");
                    } else {
                        window.location.href = reloadurl;
                    }
                },
                error: function (response) {
                    window.location.href = reloadurl;
                }
            });
        },
        
        /**
         * Get element value.
         *
         * @return boolean
         */
        getElementValue: function (element) {

            // if element exists
            if (typeof(element) != 'undefined' && element != null) {
                return element.value();
            }
            return false;
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