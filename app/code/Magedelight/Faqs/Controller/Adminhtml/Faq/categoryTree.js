/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'jquery',
    'uiRegistry',
    'Magento_Ui/js/form/element/ui-select',
], function (jQuery, uiRegistry, Select) {
    'use strict';
    return Select.extend({
        initialize: function () {
            this._super();
            var questionType = uiRegistry.get('index = question_type');
            if (questionType.initialValue === '1') {
                this.visible(false);
            } else {
                this.visible(true);
            }
            return this;
        },
    });
});
