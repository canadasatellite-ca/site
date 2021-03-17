/*
 *  Copyright © 2016 Magestore. All rights reserved.
 *  See COPYING.txt for license details.
 *
 */

define(
    [
        'Magestore_Webpos/js/model/resource-model/indexed-db/abstract'
    ],
    function (Abstract) {
        "use strict";
        return Abstract.extend({
            mainTable: 'stock_item',
            keyPath: 'item_id',
            indexes: {
                item_id: {unique: true},
                sku: {unique: true},
                name: {},
            },
        });
    }
);