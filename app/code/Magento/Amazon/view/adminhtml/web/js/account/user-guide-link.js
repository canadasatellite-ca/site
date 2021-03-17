/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'Magento_Ui/js/form/components/button',
    'uiRegistry'
], function (Button, registry) {
    'use strict';

    return Button.extend({
        /**
         * Opens the onboarding guide in a new tab.
         */
        viewUserGuide: function () {
            window.open("https://docs.magento.com/m2/ee/user_guide/sales-channel-amazon/before-you-begin.html");
        }
    });
});
