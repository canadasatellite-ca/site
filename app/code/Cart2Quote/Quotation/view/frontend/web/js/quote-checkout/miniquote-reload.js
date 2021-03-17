/*
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

require([
    'jquery',
    'Magento_Customer/js/customer-data'
], function ($, customerData) {
    var sections = ['quote'];
    customerData.invalidate(sections);
    customerData.reload(sections, true);
});
