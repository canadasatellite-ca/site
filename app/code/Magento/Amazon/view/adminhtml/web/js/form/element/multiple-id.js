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

            // form field objects
            var value = this.initialValue;
            var multipleAsin = uiRegistry.get('index = multiple_asin');

            // import amazon orders enabled
            if (value == 0) {
                this.showElement(multipleAsin);
            } else {
                this.hideElement(multipleAsin);
            }

            return this._super();
        },

        /**
         * On value change handler
         *
         * @param {String} value
         */
        onUpdate: function (value) {

            var multipleAsin = uiRegistry.get('index = multiple_asin');

            // import amazon orders enabled
            if (value == 0) {
                this.showElement(multipleAsin);
            } else {
                this.hideElement(multipleAsin);
            }

            return this;
        },

        /**
         * Show element.
         */
        showElement: function (element) {

            // if element exists
            if (typeof(element) != 'undefined' && element != null) {
                element.show();
            }
        },

        /**
         * Hide element.
         */
        hideElement: function (element) {

            // if element exists
            if (typeof(element) != 'undefined' && element != null) {
                element.hide();
            }
        }
    });
});