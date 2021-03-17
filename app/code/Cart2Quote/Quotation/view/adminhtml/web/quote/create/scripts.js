/**
 * Cart2Quote
 */

requirejs(["Magento_Sales/order/create/scripts"], function () {
    window.AdminOrder.prototype.dataShow = function () {
        if ($('submit_order_top_button')) {
            $('submit_order_top_button').show();

            //show header create quote button
            jQuery('#quote').show();
            showBottomCreateQuoteButton();
        }
        this.showArea('data');
    };

    window.AdminOrder.prototype.getAreaId = function (area) {
        if (area == 'totals') {
            showBottomCreateQuoteButton();
        }
        return 'order-' + area;
    };

    window.AdminOrder.prototype.areasLoaded = function () {
        if (jQuery('#submit_order_top_button').is(":visible")) {
            //show header create quote button
            jQuery('#quote').show();
            showBottomCreateQuoteButton();
        }
    };

    function showBottomCreateQuoteButton(){
        //show bottom create quote button
        if (jQuery('.quote-create-button-bottom').length < 2) {
            var bottomBtn = jQuery('.quote-create-button-bottom').clone(true);
            bottomBtn.appendTo('#order-totals > div > div.actions');
            bottomBtn.show();
        }
    }
});
