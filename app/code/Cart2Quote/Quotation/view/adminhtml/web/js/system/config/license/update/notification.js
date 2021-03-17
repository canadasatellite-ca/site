/*
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'ko',
    'jquery',
    'Cart2Quote_Quotation/js/system/config/license/update/check'
], function (ko, $, Component) {

    return Component.extend({
        defaults: {
            "template": "Cart2Quote_Quotation/system/config/license/update/notification.html",
        },
        releaseNotesLinkTextFormat: $.mage.__('View Release Notes version %1'),
        releaseNotesLinkText: ko.observable(false),
        versionChanged: function (value) {
            this._super();
            this.releaseNotesLinkText(
                this.releaseNotesLinkTextFormat.replace('%1', value)
            );
        },
    });
});