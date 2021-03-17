/*
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

require(['jquery'], function ($) {
    $(function () {
        //hide left menu when there is only one menu item
        if (jQuery('.admin__page-nav-items').first().find('.admin__page-nav-item').length < 2) {
            $('.side-col').hide();
            $('.main-col').width('calc(100% - 30px)');
        }
    });
});