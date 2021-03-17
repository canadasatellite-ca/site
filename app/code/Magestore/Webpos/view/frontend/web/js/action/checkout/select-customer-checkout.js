/*
 *  Copyright © 2016 Magestore. All rights reserved.
 *  See COPYING.txt for license details.
 *
 */

/*global define*/
define(
    [
        'require',
        'ko',
        'Magestore_Webpos/js/model/customer/current-customer',
        'Magestore_Webpos/js/model/checkout/cart',
        'Magestore_Webpos/js/model/event-manager',
        'Magestore_Webpos/js/view/layout',
        'Magestore_Webpos/js/model/customer/customer-factory'
    ],
    function(
        require,
        ko,
        currentCustomer,
        CartModel,
        eventManager,
        ViewManager,
        CustomerFactory
    ) {
        'use strict';
        var SelectCustomer = function (data) {
            var viewManager = require('Magestore_Webpos/js/view/layout');
            var editCustomer = viewManager.getSingleton('view/checkout/customer/edit-customer');
            currentCustomer.setCustomerId(data.id);
            currentCustomer.setCustomerEmail(data.email);
            currentCustomer.setFullName(data.full_name);
            editCustomer.loadData(data);
            currentCustomer.setData(data);
            editCustomer.showBillingPreview();
            editCustomer.showShippingPreview();
            CartModel.addCustomer(getCustomerData(data));
            /* fire select_customer_after event*/
            var eventData = {'customer' : data};
            eventManager.dispatch('checkout_select_customer_after', eventData);
        }
        
        function getCustomerData(object){
            var keys = ["id","email","firstname","lastname","full_name","group_id", "telephone"];
            var data = {};
            ko.utils.arrayForEach(keys, function(key) {
                data[key] = (typeof object[key] != "undefined")?object[key]:"";
            });
            return data;
        }

        var selectedCustomer = false;
        var customerData = CartModel.getCustomerInitParams();
        if (customerData) {
            if (customerData.customer_id) {
                CustomerFactory.get().load(customerData.customer_id).done(function(data){
                    if(data && data.id){
                        SelectCustomer(data);
                        selectedCustomer = true;
                    }
                });
            }
        }
        return SelectCustomer;
    }
);
