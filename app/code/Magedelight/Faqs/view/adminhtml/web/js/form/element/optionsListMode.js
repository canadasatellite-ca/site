define([
    'underscore',
    'uiRegistry',
    'Magento_Ui/js/form/element/select',
    'Magento_Ui/js/modal/modal'
], function (_, uiRegistry, select, modal) {
    'use strict';

    return select.extend({
        
        initialize: function () {
            this._super();
            this.onUpdate(this.initialValue);
            return this;
        },
        /**
         * On value change handler.
         *
         * @param {String} value
         */
        onUpdate: function (value) {
            var grid_column =  uiRegistry.get('index = grid_column');
            if (value == 2 ) {
                grid_column.visible(true);
            } else {
                grid_column.visible(false);
            }
            return this._super();
        },
    });
});