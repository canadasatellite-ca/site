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
            "template": "Cart2Quote_Quotation/system/config/license/update/version.html",
            "isUpdateAllowed": false,
            "expiredUrl": "",
            "updateUrl": "",
        },
        updateAvailableTextFormat: $.mage.__('Update %1 available'),
        updateAvailableText: ko.observable(false),
        updateSubscriptionStatusText: ko.observable(false),
        updateSubscriptionStatusLinkUrl: ko.observable(false),
        updateSubscriptionStatusLinkText: ko.observable(false),
        initialize: function () {
            this._super();
            this.setUpdateAllowed(this.isUpdateAllowed);
        },
        versionChanged: function (value) {
            this._super();
            this.updateAvailableText(
                this.updateAvailableTextFormat.replace('%1', value)
            );
        },
        setUpdateAllowed: function (isAllowed) {
            if (!isAllowed) {
                this.updateSubscriptionStatusText($.mage.__('Your update subscription has expired'));
                this.updateSubscriptionStatusLinkUrl(this.expiredUrl);
                this.updateSubscriptionStatusLinkText($.mage.__('Renew update subscription'));
            } else {
                this.updateSubscriptionStatusText($.mage.__('Your update subscription is valid'));
                this.updateSubscriptionStatusLinkUrl(this.updateUrl);
                this.updateSubscriptionStatusLinkText($.mage.__('Update Cart2Quote'));
            }
        }
    });
});