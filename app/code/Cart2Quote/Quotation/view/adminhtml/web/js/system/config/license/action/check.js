/*
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */
define([
    'jquery',
    'mage/storage',
    'mage/translate'
], function ($, storage, $t) {
    var callbacks = [],

        /**
         * @param {String} url
         * @param {Object} licenseData
         * @param {String} redirectUrl
         */
        action = function (url, licenseData, redirectUrl) {
            $("body").trigger('processStart');
            return $.post(url, licenseData).done(
                function (response) {
                    if (response.errors) {
                        callbacks.forEach(function (callback) {
                            callback(licenseData);
                        });
                    } else {
                        callbacks.forEach(function (callback) {
                            callback(licenseData);
                        });

                        if (response.redirectUrl) {
                            window.location.href = response.redirectUrl;
                        } else if (redirectUrl) {
                            window.location.href = redirectUrl;
                        } else {
                            location.reload();
                        }
                    }
                    $("body").trigger('processStop');
                }).fail(function () {
                callbacks.forEach(function (callback) {
                    callback(licenseData);
                });
            });
        };

    /**
     * @param {Function} callback
     */
    action.registerLoginCallback = function (callback) {
        callbacks.push(callback);
    };

    return action;
});