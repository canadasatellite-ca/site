/*
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */
define([
    'jquery'
], function ($) {
    var callbacks = [],
        /**
         * @param {String} url
         */
        action = function (url) {
            return $.get(url).done(
                function (response) {
                    callbacks.forEach(function (callback) {
                        callback(response);
                    });
                }).fail(function () {
                callbacks.forEach(function (callback) {
                    callback();
                });
            });
        };

    /**
     * @param {Function} callback
     */
    action.registerCallback = function (callback) {
        callbacks.push(callback);
    };

    return action;
});