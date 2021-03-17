/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

define([
    'jquery',
    'underscore',
    'Magento_Ui/js/form/form',
    'mage/translate'
], function ($, _, Component, $t) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'Aheadworks_AdvancedReviews/ui/form',
            formId: '',
            formCss: '',
            buttonLabel: $t('Submit'),
            isDisplayCancel: false
        },

        /**
         * {@inheritdoc}
         */
        initialize: function () {
            this._super()
                ._addFormKeyIfNotSet();

            return this;
        },

        /**
         * Add form key to window object if form key is not added earlier
         * Used for submit request validation
         *
         * @returns {Form} Chainable
         */
        _addFormKeyIfNotSet: function () {
            if (!window.FORM_KEY) {
                window.FORM_KEY = $.mage.cookies.get('form_key');
            }
            return this;
        },

        /**
         * Retrieve form id
         *
         * @returns {String}
         */
        getFormId: function() {
            return this.formId;
        },

        /**
         * Retrieve form css
         *
         * @returns {String}
         */
        getFormCss: function() {
            return this.formCss;
        },

        /**
         * Check if form visible by default
         *
         * @returns {boolean}
         */
        isVisible: function () {
            return true;
        },

        /**
         * Cancel event
         */
        onCancel: function () {
        },

        /**
         * Check if need to render form
         */
        isNeedToRenderForm: function () {
            return true;
        }
    });
});
