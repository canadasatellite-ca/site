/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
define([
    'underscore',
    'uiRegistry',
    'Magento_Ui/js/form/element/select'
], function (_, uiRegistry, select) {
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
            var selectType = 2;

            // form fieldsets
            var newAttributeSelectFieldset = uiRegistry.get('index = new_attribute_select_fieldset');

            // if select type
            if (value == selectType) {
                this.showFieldset(newAttributeSelectFieldset);
                return this._super();
            }

            this.hideFieldset(newAttributeSelectFieldset);

            return this._super();
        },

        /**
         * On value change handler.
         *
         * @param {String} value
         */
        onUpdate: function (value) {

            var selectType = 2;

            // form fieldsets
            var newAttributeSelectFieldset = uiRegistry.get('index = new_attribute_select_fieldset');

            // if select type
            if (value == selectType) {
                this.showFieldset(newAttributeSelectFieldset);
                return this;
            }

            this.hideFieldset(newAttributeSelectFieldset);

            return this;
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
