/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

define([
    'jquery',
    'Magento_Ui/js/grid/data-storage'
], function ($, Storage) {
    'use strict';

    return Storage.extend({

        /**
         * {@inheritdoc}
         */
        getRequestData: function (request) {
            var defer = $.Deferred(),
                resolve = defer.resolve.bind(defer),
                delay = this.cachedRequestDelay,
                result;

            result = {
                items: this.getByIds(request.ids),
                imageByPages: request.imageByPages,
                totalRecords: request.totalRecords
            };

            delay ?
                _.delay(resolve, delay, result) :
                resolve(result);

            return defer.promise();
        },

        /**
         * {@inheritdoc}
         */
        cacheRequest: function (data, params) {
            var cached = this.getRequest(params);

            if (cached) {
                this.removeRequest(cached);
            }

            this._requests.push({
                ids: this.getIds(data.items),
                params: params,
                imageByPages: data.imageByPages,
                totalRecords: data.totalRecords
            });

            return this;
        }
    });
});
