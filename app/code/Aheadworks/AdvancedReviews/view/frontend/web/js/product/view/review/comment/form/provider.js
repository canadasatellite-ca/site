/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

define([
    'Magento_Ui/js/form/provider'
], function (Provider) {
    'use strict';

    return Provider.extend({

        defaults: {
            rowIndex: ''
        },

        /**
         * {@inheritdoc}
         */
        save: function (options) {
            var data = this.get('data'),
                dataToSave = data[this.rowIndex];

            dataToSave['row_index'] = this.rowIndex;
            this.client.save(dataToSave, options);

            return this;
        },

        /**
         * {@inheritdoc}
         */
        set: function (path, value) {
            path = path.indexOf('captcha') >= 0 ? path.replace('data.', 'data.' + this.rowIndex + '.') : path;

            this._super(path, value);

            return this;
        }
    });
});
