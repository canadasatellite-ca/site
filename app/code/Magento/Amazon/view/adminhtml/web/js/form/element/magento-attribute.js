/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
define([
    'jquery',
    'underscore',
    'uiRegistry',
    'Magento_Ui/js/form/element/select'
], function ($, _, uiRegistry, select) {
    'use strict';

    return select.extend({

        /**
         * Initializes UI component dependencies
         *
         * Sets initial hide/show values for
         * dependent form fields.
         *
         * @returns {UISelect} Chainable.
         */
        initialize: function () {

            this._super();

            var value = this.initialValue;

            // new attribute flag
            var newAttributeFlag = 0;

            // dependent form fieldsets
            var newAttributeFieldset = uiRegistry.get('index = new_attribute_fieldset');
            // dependent form fields
            var overwrite = uiRegistry.get('index = overwrite');
            var attributeSetIds = uiRegistry.get('index = attribute_set_ids');

            // new attribute - show fieldsets and hide overwrite
            if (value == newAttributeFlag) {
                this.showFieldset(newAttributeFieldset);
                this.showElement(attributeSetIds, true);
                this.hideElement(overwrite);
                return this._super();
            }

            // existing attribute - hide fieldsets and show overwrite
            this.showFieldset(overwrite);
            this.hideFieldset(newAttributeFieldset);
            this.hideElement(attributeSetIds);

            return this._super();
        },

        /**
         * On value change handler
         *
         * @param {String} value
         */
        onUpdate: function (value) {

            var hideFieldset = this.hideFieldset;
            var showFieldset = this.showFieldset;
            var disableIsGlobal = this.disableIsGlobal;

            // new attribute flag
            var newAttributeFlag = 0;

            // dependent form fieldsets
            var newAttributeFieldset = uiRegistry.get('index = new_attribute_fieldset');
            var scopeFieldset = uiRegistry.get('index = scope_fieldset');
            // dependent form fields
            var overwrite = uiRegistry.get('index = overwrite');
            var attributeSetIds = uiRegistry.get('index = attribute_set_ids');
            var newName = uiRegistry.get('index = new_name');
            var newCode = uiRegistry.get('index = new_code');
            var isGlobal = uiRegistry.get('index = is_global');

            // new attribute - show fieldsets and hide overwrite
            if (value == newAttributeFlag) {
                this.showFieldset(scopeFieldset);
                this.enableIsGlobal(isGlobal);

                this.showFieldset(newAttributeFieldset);
                this.showElement(attributeSetIds,true);
                this.hideElement(overwrite);
                return this;
            }

            // existing attribute - hide fieldsets and show overwrite
            this.showFieldset(overwrite);
            this.hideFieldset(newAttributeFieldset);
            this.hideElement(attributeSetIds);
            // reset form field values
            this.resetFormFieldValue(newName);
            this.resetFormFieldValue(newCode);

            var reloadurl = $('#is_global').attr('data-storeurl');

            $.ajax({
                url: reloadurl,
                data: {
                    "catalog_attribute":  uiRegistry.get('index = catalog_attribute').value()
                },
                cache: false,
                dataType: "text",
                showLoader: true,
                type: "GET",
                success: function (response) {

                    if (response == 1) {
                        hideFieldset(scopeFieldset);
                    } else {
                        disableIsGlobal(isGlobal);
                        showFieldset(scopeFieldset);
                    }
                },
                error: function (response) {
                    hideFieldset(scopeFieldset);
                }
            });
        },

        /**
         * Resets form field value
         */
        enableIsGlobal: function (element) {

            // if element exists
            if (typeof(element) != 'undefined' && element != null) {
                element.disabled(false);
                element.value(1);
            }
        },

        /**
         * Resets form field value
         */
        disableIsGlobal: function (element) {

            // if element exists
            if (typeof(element) != 'undefined' && element != null) {
                element.disabled(true);
                element.value(0);
            }
        },

        /**
         * Resets form field value
         */
        resetFormFieldValue: function (element) {

            // if element exists
            if (typeof(element) != 'undefined' && element != null) {
                element.value(element.initialValue);
            }
        },

        /**
         * Show element.
         */
        showElement: function (element, flag = false) {

            // if element exists
            if (typeof(element) != 'undefined' && element != null) {
                element.show();

                if (flag) {
                    element.required(true);
                }
            }
        },

        /**
         * Hide element.
         */
        hideElement: function (element, flag = false) {

            // if element exists
            if (typeof(element) != 'undefined' && element != null) {
                element.hide();

                if (flag) {
                    element.required(false);
                }
            }
        },

        /**
         * Show fieldset.
         */
        showFieldset: function (element) {

            // if element exists
            if (typeof(element) != 'undefined' && element != null) {
                element.visible(true);
            }
        },

        /**
         * Hide fieldset.
         */
        hideFieldset: function (element) {

            // if element exists
            if (typeof(element) != 'undefined' && element != null) {
                element.visible(false);
            }
        }
    });
});
