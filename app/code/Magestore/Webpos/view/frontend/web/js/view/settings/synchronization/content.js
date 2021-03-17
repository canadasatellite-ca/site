/*
 *  Copyright © 2016 Magestore. All rights reserved.
 *  See COPYING.txt for license details.
 *
 */

define(
    [
        'jquery',
        'ko',
        'uiComponent'
    ],
    function ($, ko, Component) {
        "use strict";

        return Component.extend({
            headerName: ko.observable('Sync Data'),
            initialize: function () {
                this._super();
            },
            changeMenu: function (self, data) {
                self.headerName(data.label);
            }
        });
    }
);