/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

define([
    'jquery',
    'underscore',
    'Magento_Ui/js/modal/alert'
], function ($, _) {
    'use strict';

    $.widget('awAR.message', $.mage.alert, {
        options: {
            title: '',
            delay: 5000,
            buttons: []
        },

        /**
         * inheritdoc
         */
        openModal: function () {
            this._super();

            _.delay(this.closeModal, this.options.delay);
        }
    });

    return function (config) {
        return $('<div class="popup-message"></div>').html(config.content).message(config);
    };
});
