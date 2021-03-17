/*
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'ko',
    'jquery',
    'uiComponent',
    'Cart2Quote_Quotation/js/system/config/license/update/action',
    'mage/translate',
    'jquery/ui'
], function (ko, $, Class, versionCheckAction) {

    return Class.extend({
        defaults: {
            "url": "",
            "releaseNotesUrlFormat": "",
            "template": "",
            "currentVersion": "0.0.0"
        },
        isVisible: ko.observable(false),
        version: ko.observable('0.0.0'),
        releaseNotesUrl: ko.observable(false),
        initialize: function () {
            this._super();
            this.version.subscribe(this.versionChanged.bind(this));
            versionCheckAction.registerCallback(this.versionCheckCallback.bind(this));
            versionCheckAction(this.url);
        },
        versionCheckCallback: function (data) {
            if (Array.isArray(data.values) && data.values[0].name) {
                this.version(data.values[0].name);
                if (this.isNewerVersion(this.currentVersion, this.version())) {
                    this.isVisible(true);
                } else {
                    this.isVisible(false);
                }
            }
        },
        versionChanged: function (value) {
            this.releaseNotesUrl(
                this.releaseNotesUrlFormat.replace('%1', value.replace(/\./g, ''))
            );
        },
        isNewerVersion: function (currentVersion, compareVersion) {
            currentVersion = this.parseVersionString(currentVersion);
            compareVersion = this.parseVersionString(compareVersion);
            if (currentVersion.major < compareVersion.major) {
                return true;
            } else if (currentVersion.minor < compareVersion.minor) {
                return true;
            } else if (currentVersion.patch < compareVersion.patch) {
                return true;
            }

            return false;
        },
        parseVersionString: function (version) {
            if (typeof (version) != 'string') {
                return false;
            }
            var x = version.split('.');
            // parse from string or default to 0 if can't parse
            var maj = parseInt(x[0]) || 0;
            var min = parseInt(x[1]) || 0;
            var pat = parseInt(x[2]) || 0;
            return {
                major: maj,
                minor: min,
                patch: pat
            }
        }
    });
});