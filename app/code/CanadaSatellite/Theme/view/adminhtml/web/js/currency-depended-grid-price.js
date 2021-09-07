require([
    'jquery'
], function($) {
    "use strict";

    $('body').on('click', 'div[data-index="custom_options"]', function () {

        var intervalCurrencyDropdown = setInterval(function() {
            if (jQuery('div[data-index="container_option"] label.admin__addon-prefix').length) {
                console.log('aaa');
                clearInterval(intervalCurrencyDropdown);

                $('div.admin__field[data-index="currency_code"] div[data-role="selected-option"]').each(function(i,v){
                    var textVal = $(this)[0].innerHTML;
                    var parent = $(this).closest('div.fieldset-wrapper.admin__collapsible-block-wrapper._show');
                    parent.find('table.admin__dynamic-rows td div[data-index="price"] span[data-bind="text: addBefore()"]').text(textVal);
                });
            }
        }, 100);
    });


    $(document).on('DOMSubtreeModified', 'div.admin__field[data-index="currency_code"] div[data-role="selected-option"]', function() {
        var textVal = $(this)[0].innerHTML;
        var parent = $(this).closest('div.fieldset-wrapper.admin__collapsible-block-wrapper._show');
        parent.find('table.admin__dynamic-rows td div[data-index="price"] span[data-bind="text: addBefore()"]').text(textVal);
    });
});