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
            return this;
        },
        /**
         * On value change handler.
         *
         * @param {String} value
         */
        onUpdate: function (value) {
            var category_tree =  uiRegistry.get('index = category_id');
            if (value === '1' ) {
                category_tree.visible(false);
            } else {
                category_tree.visible(true);
            }
            return this._super();
        },
    });
});