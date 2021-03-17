/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

define([
    'jquery',
    'underscore',
    'Aheadworks_AdvancedReviews/js/ui/form',
    'mage/translate'
], function ($, _, Component, $t) {
    'use strict';

    /**
     * Check if fields is valid
     *
     * @param {Array} items
     * @returns {Boolean}
     */
    function isValidFields(items) {
        var result = true,
            config = {errorElement: 'div'};

        _.each(items, function (item) {
            if (!$.validator.validateSingleElement(item, config)) {
                result = false;
            }
        });

        return result;
    }

    return Component.extend({
        defaults: {
            dataFormPartSelectors: [],
            triggerValidateStr: 'data.validate'
        },

        /**
         * {@inheritdoc}
         */
        initialize: function () {
            this._super()
                ._addDataFormPart();

            return this;
        },

        /**
         * {@inheritdoc}
         */
        validate: function () {
            this.additionalFields = document.querySelectorAll(this.selector);
            this.source.set('params.invalid', false);
            this.source.trigger(this.triggerValidateStr);
            this.set('additionalInvalid', !isValidFields(this.additionalFields));
        },

        /**
         * Add data-form-part attribute to form elements
         *
         * @returns {Form} Chainable
         */
        _addDataFormPart: function () {
            var self = this;

            _.each(this.dataFormPartSelectors, function (field) {
                $.async('#' + self.getFormId() + ' ' + field, function (node) {
                    $(node).attr('data-form-part', self.namespace);
                });
            });
            return this;
        }
    });
});
