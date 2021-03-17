/**
 * Copyright © Mageside. All rights reserved.
 * See MS-LICENSE.txt for license details.
 */
define([
    'jquery'
], function ($) {

    $.widget('mageside.register', {
        options: {
            url: null
        },

        _create: function () {
            var self = this;
            this.element.on("click", function () {
                window.location.href = self.options.url;
            });
        }
    });

    return $.mageside.register;
});
