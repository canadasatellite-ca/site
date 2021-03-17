/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'Magento_Ui/js/form/element/select',
    'uiRegistry',
    'jquery'
], function (Select, registry, $) {
    'use strict';

    return Select.extend({
        defaults: {
            previousType: '',
            parentContainer: '',
            selections: '',
            targetIndex: '',
            typeMap: {}
        },

        /**
         * @inheritdoc
         */
        onUpdate: function () {
            var data = JSON.parse(this.currency_data);
            var e = this.value();
            var newData = data[e];

            var curency = registry.get(this.parentName + '.currency_code');
            var rate = registry.get(this.parentName + '.currency_rate');
            curency.value(newData['currency']);
            rate.value(newData['rate']);
            this._super();
        }
    });
});
