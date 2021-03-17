/*
 *  Copyright © 2016 Magestore. All rights reserved.
 *  See COPYING.txt for license details.
 *
 */


define(
    [
        'jquery',
        'ko',
        'Magestore_Webpos/js/model/resource-model/magento-rest/checkout/checkout',
        'Magestore_Webpos/js/model/checkout/cart',
        'Magestore_Webpos/js/model/checkout/cart/discountpopup',
        'Magestore_Webpos/js/helper/datetime',
        'Magestore_Webpos/js/helper/price',
        'Magestore_Webpos/js/model/event-manager',
        'Magestore_Webpos/js/model/config/config',
        'Magestore_Webpos/js/helper/staff',
        'Magestore_Webpos/js/helper/order',
        'mage/translate',
        'Magestore_Webpos/js/action/notification/add-notification',
        'Magestore_Webpos/js/model/checkout/payment/authorizenet_directpost',
        'Magestore_Webpos/js/model/checkout/payment/cryozonic_stripe',
        'Magestore_Webpos/js/helper/general',
        'Magestore_Webpos/js/model/checkout/cart/totals-factory',
        'Magestore_Webpos/js/model/sales/order-factory',
        'Magestore_Webpos/js/model/checkout/payment/paypal_payflowpro',
        'Magestore_Webpos/js/model/checkout/payment/creditcard',
        'Magestore_Webpos/js/model/checkout/cart/data/cart'
    ],
    function ($, ko, CheckoutResource, CartModel, DiscountModel, DateTime, HelperPrice, Event,
              Config, Staff, OrderHelper, Translate, AddNoti, Directpost, Stripe, Helper, TotalsFactory, OrderFactory, PaypalPayflowpro, CreditCard, CartData) {
        "use strict";
        var storeAddress = {};
        var addressConfig = [
            {key:"country_id",path:"webpos/guest_checkout/country_id"},
            {key:"postcode",path:"webpos/guest_checkout/zip"},
            {key:"region_id",path:"webpos/guest_checkout/region_id"},
            {key:"customer_id",path:"webpos/guest_checkout/customer_id"},
            {key:"city",path:"webpos/guest_checkout/city"},
            {key:"email",path:"webpos/guest_checkout/email"},
            {key:"firstname",path:"webpos/guest_checkout/first_name"},
            {key:"lastname",path:"webpos/guest_checkout/last_name"},
            {key:"street",path:"webpos/guest_checkout/street"},
            {key:"telephone",path:"webpos/guest_checkout/telephone"}
        ];
        if(addressConfig && addressConfig.length > 0){
            $.each(addressConfig,function(){
                var value = window.webposConfig[this.path];
                if (!$.isNumeric(value)) {
                    var region_id_value = 0;
                } else {
                    var region_id_value = parseInt(value);
                }
                if(this.key != "customer_id" && value){
                    var value = (this.key == 'street')?[value]:value;
                    if(this.key == 'region_id'){
                        var region = {
                            region:window.webposConfig.defaultRegionLabel,
                            region_id:region_id_value,
                            region_code: window.webposConfig.defaultRegionCode
                        }
                        storeAddress.region = region;
                        storeAddress.region_id = region_id_value;
                    }else{
                        storeAddress[this.key] = value;
                    }
                }
            });
        }
        var CheckoutModel = {
            ccMethods: ko.observableArray(),
            selectedPayments: ko.observableArray(),
            selectedShippingTitle: ko.observable(),
            selectedShippingCode: ko.observable(),
            selectedShippingPrice: ko.observable(0),
            selectedShippingPriceType: ko.observable("O"),
            paymentCode: ko.observable(),
            orderComment: ko.observable(),
            createShipment: ko.observable(),
            createInvoice: ko.observable(),
            isOnhold: ko.observable(false),
            billingAddress: ko.observable(),
            shippingAddress: ko.observable(),
            storeAddress: ko.observable(storeAddress),
            createOrderResult: ko.observable({}),
            loading: ko.observable(),
            addedDefaultData: false,
            remainTotal: ko.observable(),
            baseRemainTotal: ko.observable(),
            placingOrder: ko.observable(false),
            autoCheckingPromotion: ko.observable(false),
            ADDRESS_TYPE: {
                BILLING:"billing",
                SHIPPING:"shipping"
            },
            couponCode: ko.pureComputed(function() {
                return DiscountModel.couponCode();
            }),

            cartDiscountAmount: ko.pureComputed(function() {
                return TotalsFactory.get().discountAmount();
            }),

            cartBaseDiscountAmount: ko.pureComputed(function() {
                return TotalsFactory.get().baseDiscountAmount();
            }),

            cartDiscountName: ko.pureComputed(function() {
                return DiscountModel.cartDiscountName();
            }),

            isCreatedOrder: function(){
                return (this.createOrderResult() && this.createOrderResult().increment_id)?true:false;
            },

            initDefaultData: function(){
                var self = this;
                if(self.addedDefaultData == true){
                    return;
                }
                self.isCreditCardPayment = ko.pureComputed(function(){
                    return (self.ccMethods().length > 0 && self.selectedPayments().length > 0)?true:false;
                });
                if(!self.selectedShippingTitle()){
                    self.selectedShippingTitle("");
                }
                if(!self.selectedShippingCode()){
                    self.selectedShippingCode("");
                }
                if(!self.selectedShippingPrice()){
                    self.selectedShippingPrice(0);
                }
                if(!self.orderComment()){
                    self.orderComment("");
                }
                if(!self.paymentCode()){
                    self.paymentCode("multipaymentforpos");
                }
                if(!self.createOrderResult()){
                    self.createOrderResult({});
                }

                if(!self.billingAddress()){
                    self.useDefaultAddress(self.ADDRESS_TYPE.BILLING);
                }
                if(!self.shippingAddress()){
                    self.useDefaultAddress(self.ADDRESS_TYPE.SHIPPING);
                }
                if(!CartModel.CheckoutModel()){
                    CartModel.CheckoutModel(self);
                }
                var enableShipment = Helper.getBrowserConfig('webpos/shipping/enable_mark_as_shipped_default');
                enableShipment = (enableShipment == '1')?true:false;
                self.createShipment(enableShipment);
                self.placingOrder(false);
                self.initObservable();
                self.addedDefaultData = true;

            },
            initObservable: function(){
                var self = this;
                Event.observer('cart_empty_after', function(){
                    self.resetCheckoutData();
                });
                Event.observer('update_shipping_price', function(event, data){
                    var price = (data && data.price)?data.price:0;
                    self.saveShipping({
                        price:price
                    });
                });
                Event.observer('load_shipping_online', function(){
                    if(Helper.isUseOnline('checkout')){
                        self.loadShippingOnline();
                    }
                });
                Event.observer('load_payment_online', function(){
                    if(Helper.isUseOnline('checkout')) {
                        self.loadPaymentOnline();
                    }
                });
                Event.observer('load_cart_data_online', function(event, data){
                    if(Helper.isUseOnline('checkout')) {
                        self.loadCartDataOnline(data.section?data.section:false);
                    }
                });
                Event.observer('checkout_select_customer_after', function(event, data){
                    if(Helper.isUseOnline('checkout') && CartModel.isOnCheckoutPage()) {
                        self.selectCustomerOnline();
                    }
                });
            },
            resetCheckoutData: function(){
                var self = this;
                self.selectedShippingPrice(0);
                DiscountModel.cartDiscountAmount(0);
                DiscountModel.cartBaseDiscountAmount(0);
                DiscountModel.cartDiscountName("");
                self.orderComment("");
                $("#form-note-order").find("textarea").val("");
                DiscountModel.couponCode();
                self.createOrderResult({});
                CartModel.removeCustomer();
                self.useDefaultAddress(self.ADDRESS_TYPE.BILLING);
                self.useDefaultAddress(self.ADDRESS_TYPE.SHIPPING);
                TotalsFactory.get().updateDiscountTotal();
                self.resetDeliveryTime();
                self.placingOrder(false);
            },
            useWebposShipping: function(){
                var self = this;
                self.selectedShippingCode("webpos_shipping_storepickup");
                self.selectedShippingPrice(0);
            },
            createOrder: function(){
                var self = this;
                self.isOnhold(false);
                self.placingOrder(true);
                var data = self.getOfflineOrderData();
                Event.dispatch('webpos_place_order_before',data);
                if(data.validate && data.validate == true){
                    delete data.validate;
                    OrderFactory.get().setData(data).setMode('offline').save().done(function (response) {
                        if(response){
                            self.placeOrder(response);
                            self.syncOrder(response,"checkout");
                        }
                    });
                }
            },
            placeOrder: function (response) {
                var self = this;
                Event.dispatch('webpos_place_order_after',response);
                self.createOrderResult(response);
                Event.dispatch('webpos_order_save_after',response,true);
            },
            checkPromotion: function(){
                var self = this;
                var deferred = $.Deferred();
                var params = this.getCheckPromotionParams();
                CheckoutResource().setPush(true).setLog(false).checkPromotion(params,deferred);

                DiscountModel.loading(true);
                deferred.done(function (response) {
                    var data =  JSON.parse(response);
                    if(data.items){
                        self.processCatalogRules(data.items);
                    }
                    if(data.discount_amount){
                        DiscountModel.promotionDiscountAmount(HelperPrice.toBasePrice(data.discount_amount));
                    }else{
                        DiscountModel.promotionDiscountAmount(0);
                    }
                    DiscountModel.loading(false);
                }).fail(function (response) {
                    DiscountModel.loading(false);
                }).always(function () {
                    DiscountModel.loading(false);
                });
            },
            getShippingRates: function(){
                var deferred = $.Deferred();
                var params = this.getCheckShippingParams();
                CheckoutResource().setPush(true).setLog(false).getShippingRates(params,deferred);
                deferred.done(function (response) {
                    var data = JSON.parse(response);
                });
            },
            sendEmail: function(email,increment_id){
                var deferred = $.Deferred();
                var params = {increment_id:increment_id,email:email};
                CheckoutResource().sendEmail(params,deferred);
            },
            syncOrder: function(orderData,page){
                if(orderData && orderData.sync_params){
                    var params = orderData.sync_params;
                    params.extension_data.push({
                        key:"webpos_order_id",
                        value:orderData.entity_id
                    });
                    params.extension_data.push({
                        key:"created_at",
                        value:orderData.created_at
                    });
                    params.extension_data.push({
                        key:"customer_fullname",
                        value:orderData.customer_fullname
                    });
                    if(page == "orderlist"){

                    }
                    CheckoutResource().setActionId(orderData.entity_id).setPush(true).createOrder(params,$.Deferred());
                }
            },
            saveShipping: function(data){
                if(typeof data.code != "undefined"){
                    this.selectedShippingCode(data.code);
                }
                if(typeof data.title != "undefined"){
                    this.selectedShippingTitle(data.title);
                }
                if(typeof data.price != "undefined"){
                    this.selectedShippingPrice(data.price);
                    var shippingData = {
                        price:data.price
                    }
                    if(typeof data.price_type != "undefined"){
                        this.selectedShippingPriceType(data.price_type);
                        shippingData.price_type = data.price_type;
                    }
                    TotalsFactory.get().shippingData(shippingData);
                    TotalsFactory.get().collectShippingTotal();
                }else{
                    this.selectedShippingPrice(0);
                    TotalsFactory.get().updateShippingAmount(0);
                }
                if(Helper.isUseOnline('checkout') && data.code){
                    this.saveShippingMethodOnline(data.code);
                }
            },
            saveBillingAddress: function(data){
                if(data.id == 0){
                    if (data.firstname && data.lastname) {
                        this.useDefaultAddress(this.ADDRESS_TYPE.BILLING, data.firstname, data.lastname);
                    } else {
                        this.useDefaultAddress(this.ADDRESS_TYPE.BILLING);
                    }
                    var newAddress = this.billingAddress();
                    newAddress.firstname = data.firstname;
                    newAddress.lastname = data.lastname;
                    this.billingAddress(newAddress);
                }else{
                    this.billingAddress(data);
                }
                CartModel.reCollectTaxRate();
            },
            updateBillingAddress: function(data){
                var address = this.billingAddress();
                if(typeof address == "undefined"){
                    address = {};
                }
                if(data && data.length > 0){
                    $.each(data,function(key, value){
                        address[key] = value;
                    });
                }
                this.billingAddress(address);
                var calculateTaxBaseOn = window.webposConfig["tax/calculation/based_on"];
                if(calculateTaxBaseOn == 'billing'){
                    CartModel.reCollectTaxRate();
                }
            },
            updateStoreAddress: function(key,value){
                var address = this.storeAddress();
                if(typeof address == "undefined"){
                    address = {};
                }
                address[key] = value;
                this.storeAddress(address);
            },
            updateShippingAddress: function(data){
                var address = this.shippingAddress();
                if(typeof address == "undefined"){
                    address = {};
                }
                if(data && data.length > 0){
                    $.each(data,function(key, value){
                        address[key] = value;
                    });
                }
                this.shippingAddress(address);
                var calculateTaxBaseOn = window.webposConfig["tax/calculation/based_on"];
                if(calculateTaxBaseOn == 'shipping'){
                    CartModel.reCollectTaxRate();
                }
            },
            saveShippingAddress: function(data){
                if(data.id == 0){
                    if (data.firstname && data.lastname) {
                        this.useDefaultAddress(this.ADDRESS_TYPE.SHIPPING, data.firstname, data.lastname);
                    } else {
                        this.useDefaultAddress(this.ADDRESS_TYPE.SHIPPING);
                    }
                    var newAddress = this.shippingAddress();
                    newAddress.firstname = data.firstname;
                    newAddress.lastname = data.lastname;
                    this.shippingAddress(newAddress);

                }else{
                    this.shippingAddress(data);
                }
                CartModel.reCollectTaxRate();
            },
            getHoldOrderParams: function(){
                var self = this;
                var orderParams = {
                    quote_init:CartModel.getQuoteInitParams(),
                    customer_id:CartModel.customerId(),
                    customer_group:CartModel.customerGroup(),
                    customerData: CartModel.customerData(),
                    items:CartModel.getItemsInitData(),
                    config:{
                        apply_promotion:DiscountModel.applyPromotion(),
                        cart_base_discount_amount:DiscountModel.cartBaseDiscountAmount(),
                        cart_discount_amount:DiscountModel.cartDiscountAmount(),
                        cart_discount_name:DiscountModel.cartDiscountName(),
                        cart_applied_discount:DiscountModel.appliedDiscount(),
                        cart_applied_promotion:DiscountModel.appliedPromotion(),
                        cart_discount_type:DiscountModel.cartDiscountType(),
                        cart_discount_percent:DiscountModel.cartDiscountPercent(),
                        currency_code:window.webposConfig.currentCurrencyCode
                    },
                    coupon_code: self.couponCode(),
                    billing_address: self.billingAddress(),
                    shipping_address: self.shippingAddress()
                };
                return orderParams;
            },
            getOrderParams: function(){
                var self = this;
                var TotalsModel = TotalsFactory.create();
                var billingAddress = self.billingAddress();
                var shippingAddress = self.shippingAddress();
                delete billingAddress['id'];
                delete shippingAddress['id'];

                var baseSubtotalInclTax = TotalsModel.getBaseTotalValue(TotalsModel.SUBTOTAL_TOTAL_CODE) + ((Helper.isShowTaxFinal())?TotalsModel.getBaseTotalFinalValue(TotalsModel.TAX_TOTAL_CODE):TotalsModel.getBaseTotalValue(TotalsModel.TAX_TOTAL_CODE));
                var subtotalInclTax = TotalsModel.getTotalValue(TotalsModel.SUBTOTAL_TOTAL_CODE) + ((Helper.isShowTaxFinal())?TotalsModel.getTotalFinalValue(TotalsModel.TAX_TOTAL_CODE):TotalsModel.getTotalValue(TotalsModel.TAX_TOTAL_CODE));

                if(Helper.isEnableCrossBorderTrade()){
                    baseSubtotalInclTax = TotalsModel.getBaseTotalValue(TotalsModel.SUBTOTAL_TOTAL_CODE);
                    subtotalInclTax = TotalsModel.getTotalValue(TotalsModel.SUBTOTAL_TOTAL_CODE);
                }

                var orderParams = {
                    //store:Helper.getOnlineConfig('default_store_code'),
                    customer_id:String(CartModel.customerId()),
                    items:CartModel.getItemsInfo(),
                    payment:{
                        method:self.paymentCode(),
                        method_data:self.getPaymentsData(),
                        address:self.billingAddress()
                    },
                    shipping:{
                        method:self.selectedShippingCode(),
                        tracks:[],
                        address:self.shippingAddress(),
                        datetime:self.getDeliverytime()
                    },
                    config:{
                        apply_promotion:DiscountModel.applyPromotion(),
                        note:self.orderComment(),
                        create_invoice:self.createInvoice(),
                        create_shipment:self.createShipment(),
                        is_onhold:self.isOnhold(),
                        init_data:JSON.stringify(self.getHoldOrderParams()),
                        cart_base_discount_amount:self.cartBaseDiscountAmount(),
                        cart_discount_amount:self.cartDiscountAmount(),
                        cart_discount_name:self.cartDiscountName(),
                        currency_code:window.webposConfig.currentCurrencyCode,
                        discount_apply: window.webposConfig.discountApply
                    },
                    coupon_code: self.couponCode(),
                    extension_data:[
                        {
                            key:"grand_total",
                            value: TotalsModel.getTotalValue('grand_total')
                        },
                        {
                            key:"base_grand_total",
                            value: TotalsModel.getBaseTotalValue('grand_total')
                        },
                        {
                            key:"tax_amount",
                            value: TotalsModel.tax()
                        },
                        {
                            key:"base_tax_amount",
                            value: TotalsModel.baseTax()
                        },
                        {
                            key:"shipping_amount",
                            value: HelperPrice.currencyConvert(TotalsModel.shippingFee())
                        },
                        {
                            key:"base_shipping_amount",
                            value: TotalsModel.shippingFee()
                        },
                        {
                            key:"subtotal",
                            value: TotalsModel.subtotal()
                        },
                        {
                            key:"base_subtotal",
                            value: TotalsModel.baseSubtotal()
                        },
                        {
                            key:"subtotal_incl_tax",
                            value: subtotalInclTax
                        },
                        {
                            key:"base_subtotal_incl_tax",
                            value: baseSubtotalInclTax
                        },
                    ],
                    session_data:[
                    ],
                    integration:[
                    ]
                };

                orderParams.extension_data.push({
                    key:"webpos_staff_id",
                    value: Staff.getStaffId()
                });
                orderParams.extension_data.push({
                    key:"webpos_staff_name",
                    value: Staff.getStaffName()
                });
                orderParams.extension_data.push({
                    key:"webpos_shift_id",
                    value: window.webposConfig.shiftId
                });
                orderParams.extension_data.push({
                    key:"location_id",
                    value: window.webposConfig.locationId
                });
                if(self.couponCode()){
                    orderParams.extension_data.push({
                        key:"discount_description",
                        value: self.couponCode()
                    });
                }
                if(self.remainTotal() && self.remainTotal() < 0){
                    orderParams.extension_data.push({
                        key:"webpos_change",
                        value: -self.remainTotal()
                    });
                    orderParams.extension_data.push({
                        key:"webpos_base_change",
                        value: -self.baseRemainTotal()
                    });
                }
                return orderParams;
            },
            submitOrderOnline: function(){
                var self = this;
                self.isOnhold(false);
                if(self.isAuthorizeNetDirectpost()){
                    self.createInvoice(false);
                }
                var params = self.getOrderParams();
                var createdTime = DateTime.getBaseSqlDatetime();
                params.extension_data.push({
                    key:"webpos_order_id",
                    value: OrderHelper.generateId()
                });
                params.extension_data.push({
                    key:"created_at",
                    value:createdTime
                });
                var deferred = $.Deferred();
                self.loading(true);
                var methodCode = params.payment.method_data[0];
                self.paymentCode(methodCode.code);
                params.payment.method = methodCode.code;
                CheckoutResource().setPush(true).setLog(false).submitOnlineOrder(params,deferred);

                deferred.done(function (response) {
                    if(response) {
                        var result = JSON.parse(response);
                        var orderId = result.order_id;
                        var paymentInfo = result.payment_infomation;
                        self.processOnlinePayment(result);
                        if (self.isAuthorizeNetDirectpost()) {
                            if (!Directpost().CheckoutModel()) {
                                Directpost().CheckoutModel(self);
                            }
                            if (orderId) {
                                Directpost().processPayment(orderId, paymentInfo);
                            } else {

                            }
                        }
                        if (self.isCryozonicStripe()) {
                            if (!Stripe().CheckoutModel()) {
                                Stripe().CheckoutModel(self);
                            }
                            if (orderId) {
                                Stripe().processPayment(orderId);
                            } else {

                            }
                        }
                    }else{
                        var error = Translate('Cannot connect to your server!');
                        AddNoti(error, true, 'danger', 'Error');
                        self.loading(false);
                    }
                });
                deferred.fail(function (response){
                    if(response != undefined && response.responseText) {
                        var error = JSON.parse(response.responseText);
                        if (error.message != undefined) {
                            AddNoti(error.message, true, 'danger', 'Error');
                        }
                    }else{
                        var error = Translate('Cannot connect to your server!');
                        AddNoti(error, true, 'danger', 'Error');
                    }
                    self.loading(false);
                });
            },
            getAllDiscountName: function () {
                var coupon = '';
                var self = this;
                if (self.couponCode())
                    coupon = self.couponCode();
                if (self.cartDiscountName())
                    coupon = self.cartDiscountName();
                if (self.couponCode() && self.cartDiscountName())
                    coupon = self.couponCode() + ',' + self.cartDiscountName();
                return coupon;
            },
            getOfflineOrderData: function(){
                var self = this;
                var customerFullname;
                var discountCode = self.getAllDiscountName();
                var createdTime = DateTime.getBaseSqlDatetime();
                var customerData = CartModel.customerData();
                var webpos_order_id = OrderHelper.generateId();
                var TotalsModel = TotalsFactory.create();
                var customerFirstname = (customerData.firstname)?(customerData.firstname):'';
                var customerLastname = (customerData.lastname)?(customerData.lastname):'';
                if (customerFirstname && customerLastname)
                    customerFullname = customerFirstname + customerLastname;

                var baseSubtotalInclTax = TotalsModel.getBaseTotalValue(TotalsModel.SUBTOTAL_TOTAL_CODE) + ((Helper.isShowTaxFinal())?TotalsModel.getBaseTotalFinalValue(TotalsModel.TAX_TOTAL_CODE):TotalsModel.getBaseTotalValue(TotalsModel.TAX_TOTAL_CODE));
                var subtotalInclTax = TotalsModel.getTotalValue(TotalsModel.SUBTOTAL_TOTAL_CODE) + ((Helper.isShowTaxFinal())?TotalsModel.getTotalFinalValue(TotalsModel.TAX_TOTAL_CODE):TotalsModel.getTotalValue(TotalsModel.TAX_TOTAL_CODE));

                if(Helper.isEnableCrossBorderTrade()){
                    baseSubtotalInclTax = TotalsModel.getBaseTotalValue(TotalsModel.SUBTOTAL_TOTAL_CODE);
                    subtotalInclTax = TotalsModel.getTotalValue(TotalsModel.SUBTOTAL_TOTAL_CODE);
                }

                var data = {
                    entity_id: webpos_order_id,
                    increment_id: webpos_order_id,
                    status: "notsync",
                    state: "notsync",
                    is_virtual: CartModel.isVirtual()?1:0,
                    created_at: createdTime,
                    updated_at: createdTime,
                    order_currency_code:window.webposConfig.currentCurrencyCode,
                    webpos_staff_id:Staff.getStaffId(),
                    webpos_shift_id:window.webposConfig.shiftId,
                    location_id:window.webposConfig.locationId,
                    webpos_staff_name:Staff.getStaffName(),
                    customer_email:customerData.email,
                    customer_firstname:customerData.firstname,
                    customer_id:CartModel.customerId(),
                    customer_lastname:customerData.lastname,
                    customer_telephone:customerData.telephone,
                    customer_fullname:customerFullname,
                    customer_note:self.orderComment(),
                    total_item_count:CartModel.totalItems(),
                    items:CartModel.getItemsDataForOrder(),
                    payment:{
                        method:self.paymentCode(),
                        additional_information:[]
                    },
                    extension_attributes:{
                        shipping_assignments:[
                            {
                                items:"",
                                shipping:{
                                    address:self.getShippingAddressForOrder(),
                                    method:self.selectedShippingCode(),
                                }
                            }
                        ]
                    },
                    shipping_description:self.selectedShippingTitle(),
                    billing_address:self.getBillingAddressForOrder(),
                    status_histories:[
                        {
                            comment:self.orderComment(),
                            created_at:createdTime,
                            status:"notsync",
                        }
                    ],
                    webpos_base_change:(self.baseRemainTotal() && self.baseRemainTotal() < 0)?-self.baseRemainTotal():0,
                    webpos_change:(self.remainTotal() && self.remainTotal() < 0)?-self.remainTotal():0,
                    base_subtotal:TotalsModel.getBaseTotalValue(TotalsModel.SUBTOTAL_TOTAL_CODE),
                    subtotal: TotalsModel.getTotalValue(TotalsModel.SUBTOTAL_TOTAL_CODE),
                    base_subtotal_incl_tax:baseSubtotalInclTax,
                    subtotal_incl_tax: subtotalInclTax,
                    base_tax_amount:TotalsModel.getBaseTotalValue(TotalsModel.TAX_TOTAL_CODE),
                    tax_amount:TotalsModel.getTotalValue(TotalsModel.TAX_TOTAL_CODE),
                    base_total_due:self.getBaseTotalDue(),
                    total_due:self.getTotalDue(),
                    base_total_paid:self.getBaseTotalPaid(),
                    total_paid:self.getTotalPaid(),
                    base_shipping_amount:TotalsModel.getBaseTotalValue(TotalsModel.SHIPPING_TOTAL_CODE),
                    shipping_amount:TotalsModel.getTotalValue(TotalsModel.SHIPPING_TOTAL_CODE),
                    base_discount_amount:TotalsModel.getBaseTotalValue(TotalsModel.DISCOUNT_TOTAL_CODE),
                    discount_amount:TotalsModel.getTotalValue(TotalsModel.DISCOUNT_TOTAL_CODE),
                    discount_description:discountCode,
                    base_grand_total:TotalsModel.getBaseTotalValue(TotalsModel.GRANDTOTAL_TOTAL_CODE),
                    grand_total:TotalsModel.getTotalValue(TotalsModel.GRANDTOTAL_TOTAL_CODE),
                    sync_params: self.getOrderParams(),
                    base_currency_code: window.webposConfig.baseCurrencyCode,
                    initData:self.getHoldOrderParams(),
                    webpos_order_payments:self.getPaymentsDataForOrder()
                };
                return data;
            },
            getHoldOrderData: function(){
                var currentTime = $.now();
                var self = this;
                self.isOnhold(true);
                if(!self.selectedShippingCode()){
                    self.useWebposShipping();
                }
                self.createInvoice(false);
                self.createShipment(false);
                var data = self.getOfflineOrderData();
                // delete data.sync_params;
                data.entity_id = currentTime.toString(),
                data.increment_id = currentTime.toString(),
                data.status = "onhold";
                data.state = "onhold";
                data.initData = self.getHoldOrderParams();
                return data;
            },
            getCheckPromotionParams: function(){
                var params = this.getOrderParams();
                return params;
            },
            getCheckShippingParams: function(){
                var params = this.getOrderParams();
                delete params.config;
                delete params.coupon_code;
                delete params.payment;
                delete params.shipping_method;
                params.zipcode = (this.shippingAddress() && this.shippingAddress().postcode)?this.shippingAddress().postcode:"";
                params.country = (this.shippingAddress() && this.shippingAddress().country_id)?this.shippingAddress().country_id:"";
                return params;
            },
            getDeliverytime: function () {
                if($('#delivery_date') && $('#delivery_date').val() != ''){
                    var date = '';
                    date = $('#delivery_date').val();
                    var datetime= DateTime.getBaseSqlDatetime(date);
                    return datetime;
                }
                return '';

            },
            resetDeliveryTime: function () {
                $('#delivery_date').val('');
            },
            getPaymentsData: function(){
                var self = this;
                var paymentsData = [];
                if(self.selectedPayments().length > 0){
                    $.each(self.selectedPayments(),function(){
                        var data = {};
                        data.code = this.code;
                        data.title = this.title;
                        data.base_amount = this.base_cart_total;
                        data.amount = this.cart_total;
                        data.base_real_amount = this.base_paid_amount;
                        data.real_amount = this.paid_amount;
                        data.reference_number = this.reference_number;
                        data.is_pay_later = this.is_pay_later;
                        data.shift_id = window.webposConfig.shiftId;
                        self.getCreditCardInfo(data);
                        paymentsData.push(data);
                    });
                }
                return paymentsData;
            },
            getCreditCardInfo: function (data) {
                var additionalData = {};
                additionalData.cc_owner = '';
                if($('#webpos_cc_owner') && $('#webpos_cc_owner').val() != ''){
                    additionalData.cc_owner = $('#webpos_cc_owner').val();
                }
                if($('#webpos_cc_type') && $('#webpos_cc_type').val() != ''){
                    additionalData.cc_type = $('#webpos_cc_type').val();
                }
                if($('#webpos_cc_number') && $('#webpos_cc_number').val() != ''){
                    additionalData.cc_number = $('#webpos_cc_number').val();
                }
                if($('#webpos_cc_exp_month') && $('#webpos_cc_exp_month').val() != ''){
                    additionalData.cc_exp_month = $('#webpos_cc_exp_month').val();
                }
                if($('#webpos_cc_exp_year') && $('#webpos_cc_exp_year').val() != ''){
                    additionalData.cc_exp_year = $('#webpos_cc_exp_year').val();
                }
                if($('#webpos_cc_cid') && $('#webpos_cc_cid').val() != ''){
                    additionalData.cc_cid = $('#webpos_cc_cid').val();
                }
                data.additional_data = additionalData;
                Event.dispatch('prepare_payment_additional_data',data);
            },
            getPaymentsDataForOrder: function(){
                var self = this;
                var payments = [];
                ko.utils.arrayForEach(self.getPaymentsData(), function(payment) {
                    var data = {
                        base_payment_amount:(payment.is_pay_later)?0:payment.base_amount,
                        payment_amount:(payment.is_pay_later)?0:payment.amount,
                        base_display_amount: (payment.is_pay_later)?0:payment.base_real_amount,
                        display_amount: (payment.is_pay_later)?0:payment.real_amount,
                        method:payment.code,
                        method_title:payment.title,
                    }
                    payments.push(data);
                });
                return payments;
            },
            getTotalPaid: function(){
                var self = this;
                var totalPaid = 0;
                if(self.getPaymentsData().length > 0){
                    $.each(self.getPaymentsData(),function(){
                        if(!this.is_pay_later){
                            totalPaid += HelperPrice.toNumber(this.amount);
                        }
                    });
                }
                return totalPaid;
            },
            getTotalDue: function(){
                var self = this;
                var totalPaid = 0;
                var baseGrandTotal = HelperPrice.toNumber(TotalsFactory.get().getTotalValue(TotalsFactory.get().GRANDTOTAL_TOTAL_CODE));
                if(self.getPaymentsData().length > 0){
                    $.each(self.getPaymentsData(),function(){
                        if(!this.is_pay_later){
                            totalPaid += HelperPrice.toNumber(this.amount);
                        }
                    });
                }
                var totalDue = HelperPrice.toNumber(baseGrandTotal - totalPaid);
                return (totalDue > 0)?totalDue:0;
            },
            getBaseTotalPaid: function(){
                var self = this;
                var totalPaid = 0;
                if(self.getPaymentsData().length > 0){
                    $.each(self.getPaymentsData(),function(){
                        if(!this.is_pay_later){
                            totalPaid += HelperPrice.toNumber(this.base_amount);
                        }
                    });
                }
                return totalPaid;
            },
            getBaseTotalDue: function(){
                var self = this;
                var totalPaid = 0;
                var baseGrandTotal = HelperPrice.toNumber(TotalsFactory.get().getBaseTotalValue(TotalsFactory.get().GRANDTOTAL_TOTAL_CODE));
                if(self.getPaymentsData().length > 0){
                    $.each(self.getPaymentsData(),function(){
                        if(!this.is_pay_later){
                            totalPaid += HelperPrice.toNumber(this.base_amount);
                        }
                    });
                }
                var totalDue = HelperPrice.toNumber(baseGrandTotal - totalPaid);
                return (totalDue > 0)?totalDue:0;
            },
            useDefaultAddress: function(type, firstname, lastname){
                var self = this;
                var address = self.storeAddress();
                if(address){
                    if(type == self.ADDRESS_TYPE.SHIPPING){
                        self.shippingAddress(address);
                        self.updateShippingAddress({
                            'firstname':firstname,
                            'lastname':lastname
                        });
                    }else{
                        self.billingAddress(address);
                        self.updateBillingAddress({
                            'firstname':firstname,
                            'lastname':lastname
                        });
                    }
                }
            },
            getBillingAddressForOrder: function(){
                var billing = this.billingAddress()?this.billingAddress():{};
                var data = {};
                data.address_type = this.ADDRESS_TYPE.BILLING;
                $.each(billing,function(key, value){
                    if(key == "region" && value.region){
                        var regions = JSON.parse(window.webposConfig.regionJson);
                        if(regions && billing.country_id && regions[billing.country_id]){
                            if(regions[billing.country_id][value.region_id]){
                                value.region = regions[billing.country_id][value.region_id].name;
                                value.region_code = regions[billing.country_id][value.region_id].code;
                            }
                        }
                        value = value.region;
                    }
                    data[key] = value;
                });
                return data;
            },
            getShippingAddressForOrder: function(){
                var shipping = this.shippingAddress()?this.shippingAddress():{};
                var data = {};
                data.address_type = this.ADDRESS_TYPE.SHIPPING;
                $.each(shipping,function(key, value){
                    if(key == "region" && value.region){
                        var regions = JSON.parse(window.webposConfig.regionJson);
                        if(regions && shipping.country_id && regions[shipping.country_id]){
                            if(regions[shipping.country_id][value.region_id]){
                                value.region = regions[shipping.country_id][value.region_id].name;
                                value.region_code = regions[shipping.country_id][value.region_id].code;
                            }
                        }
                        value = value.region;
                    }
                    data[key] = value;
                });
                return data;
            },
            autoCheckPromotion: function(){
                var self = this;
                if(self.autoCheckingPromotion() == false) {
                    var deferred = $.Deferred();
                    var params = this.getCheckPromotionParams();
                    CheckoutResource().setPush(true).setLog(false).checkPromotion(params, deferred);

                    self.autoCheckingPromotion(true);
                    deferred.done(function (response) {
                        var data = JSON.parse(response);
                        if(data.items){
                            self.processCatalogRules(data.items);
                        }
                        if (data.discount_amount) {
                            self.applyPromotionDiscount(HelperPrice.toBasePrice(data.discount_amount));
                        } else {
                            self.applyPromotionDiscount(0);
                        }
                    }).fail(function (response) {
                        self.autoCheckingPromotion(false);
                    }).always(function () {
                        self.autoCheckingPromotion(false);
                    });
                }
            },
            applyPromotionDiscount: function(amount){
                amount = (amount)?Helper.correctPrice(amount):0;
                if(amount > 0){
                    if(DiscountModel.cartBaseDiscountAmount() != amount){
                        var TotalsModel = TotalsFactory.get();
                        DiscountModel.appliedPromotion(true);
                        DiscountModel.appliedDiscount(false);
                        DiscountModel.cartDiscountType(DiscountModel.DISCOUNT_TYPES.FIXED);
                        DiscountModel.cartBaseDiscountAmount(amount);
                        DiscountModel.cartDiscountAmount(Helper.convertPrice(amount));
                        TotalsModel.updateDiscountTotal();
                        DiscountModel.process(TotalsModel.baseDiscountAmount());
                        Helper.dispatchEvent('reset_payments_data', '');
                    }
                }else{
                    if(DiscountModel.cartBaseDiscountAmount() != amount && DiscountModel.appliedPromotion()){
                        var TotalsModel = TotalsFactory.get();
                        DiscountModel.reset();
                        TotalsModel.updateDiscountTotal();
                        DiscountModel.process(TotalsModel.baseDiscountAmount());
                        Helper.dispatchEvent('reset_payments_data', '');
                    }
                }
            },
            unholdOrder: function(order_increment_id){
                var deferred = $.Deferred();
                var params = {order_increment_id:order_increment_id};
                CheckoutResource().unholdOrder(params,deferred);
                deferred.done(function (response) {
                    var data = JSON.parse(response);
                    var alertData = {priority:"",title: "",message: ""}
                    if(data.error){
                        alertData.priority = "warning";
                        alertData.title = Translate("Warning");
                    }else{
                        alertData.priority = "success";
                        alertData.title = Translate("Message");
                    }
                    if(data.message){
                        alertData.message = data.message;
                        Alert(alertData);
                    }
                });
            },
            isAuthorizeNetDirectpost: function(){
                var self = this;
                return (self.paymentCode() == 'authorizenet_directpost')?true:false;
            },
            isPaypalPro: function(){
                var self = this;
                return (self.paymentCode() == 'payflowpro')?true:false;
            },
            isAllowToCreateInvoice: function() {
                var self = this;
                return (
                    !self.isAuthorizeNetDirectpost() && !self.isPaypalPro() && !self.isAuthorizeNet()
                );
            },
            isAuthorizeNet: function(){
                var self = this;
                return (self.paymentCode() == 'authorizenet')?true:false;
            },
            isCryozonicStripe: function(){
                var self = this;
                return (self.paymentCode() == 'cryozonic_stripe')?true:false;
            },
            startAuthorizeNetPayment: function(){
                window.directPostModel.openAuthorizePopup();
            },
            processCatalogRules: function(itemsData){
                if(itemsData){
                    CartModel.applyCatalogRules(itemsData);
                }
            },
            startAuthorizePopup: function(){
                var self = this;
                var model = self.getPaymentJsModel();
                if(model){
                    model.openAuthorizePopup();
                    window.payflowproModel.openAuthorizePopup();
                }
            },
            getPaymentJsModel: function(){
                var self = this;
                var model = false;
                if(self.isAuthorizeNetDirectpost()){
                    model = window.directPostModel;
                }
                if(self.isPaypalPro()){
                    model = window.payflowproModel;
                }
                return model;
            },
            processOnlinePayment: function(response){
                var self = this;
                if (self.isPaypalPro()) {
                    if (!PaypalPayflowpro.CheckoutModel()) {
                        PaypalPayflowpro.CheckoutModel(self);
                    }
                    var quoteId = response.quote_id;
                    var paymentInfo = response.payment_infomation;
                    if (paymentInfo) {
                        PaypalPayflowpro.processPayment(quoteId, paymentInfo);
                    }
                }
            },
            submitQuoteOnline: function(quoteId){
                var self = this;
                if(quoteId){
                    self.isOnhold(false);
                    self.createInvoice(false);
                    var params = self.getOrderParams();
                    params.quote_id = quoteId;
                    delete params.customer_id;
                    delete params.items;
                    var createdTime = DateTime.getBaseSqlDatetime();
                    params.extension_data.push({
                        key:"webpos_order_id",
                        value: OrderHelper.generateId()
                    });
                    params.extension_data.push({
                        key:"created_at",
                        value:createdTime
                    });
                    var deferred = $.Deferred();
                    self.loading(true);
                    var methodCode = params.payment.method_data[0];
                    self.paymentCode(methodCode.code);
                    params.payment.method = methodCode.code;
                    CheckoutResource().setPush(true).setLog(false).submitQuoteOnline(params,deferred);
                    deferred.done(function(response){
                        if(response && response.increment_id){
                            self.placeOrder(response);
                        }
                    });
                    deferred.always(function(response){
                        self.loading(false);
                    });
                }
            },
            loadShippingOnline: function(){
                var self = this;
                var deferred = $.Deferred();
                var params = CartModel.getQuoteInitParams();
                self.loading(true);
                CheckoutResource().setPush(true).setLog(false).getShipping(params,deferred);
                deferred.always(function(){
                    self.loading(false);
                });
                return deferred;
            },
            loadPaymentOnline: function(){
                var self = this;
                var deferred = $.Deferred();
                var params = CartModel.getQuoteInitParams();
                self.loading(true);
                CheckoutResource().setPush(true).setLog(false).getPayment(params,deferred);
                deferred.always(function(){
                    self.loading(false);
                });
                return deferred;
            },
            loadCartDataOnline: function(section){
                var self = this;
                var deferred = $.Deferred();
                var params = CartModel.getQuoteInitParams();
                params.section = (section)?section:[];
                self.loading(true);
                CheckoutResource().setPush(true).setLog(false).getCartData(params,deferred);
                deferred.done(function(response){
                    // if(response.status == CartModel.DATA.STATUS.ERROR && response.messages){
                    //     CartData.hasErrors(true);
                    //     CartData.errorMessages(response.messages);
                    // }else{
                    //     CartData.hasErrors(false);
                    //     CartData.errorMessages('');
                    // }
                }).always(function(){
                    self.loading(false);
                });
                return deferred;
            },
            saveShippingMethodOnline: function(code){
                var deferred = $.Deferred();
                if(code){
                    var self = this;
                    var params = CartModel.getQuoteInitParams();
                    params.shipping_method = code;
                    self.loading(true);
                    CheckoutResource().setPush(true).setLog(false).saveShippingMethod(params,deferred);
                    deferred.always(function(){
                        self.loading(false);
                    });

                }
                return deferred;
            },
            savePaymentMethodOnline: function(code){
                var self = this;
                var deferred = $.Deferred();
                if(code && (self.loadingPayment != code)){
                    var initParams = CartModel.getQuoteInitParams();
                    var params = {quote_id: initParams.quote_id};
                    params.payment_method = code;
                    self.loading(true);
                    self.loadingPayment = code;
                    CheckoutResource().setPush(true).setLog(false).savePaymentMethod(params,deferred);
                    deferred.done(function(){
                        self.paymentCode(code);
                    }).always(function(){
                        self.loadingPayment = '';
                        self.loading(false);
                    });
                }
                return deferred;
            },
            placeOrderOnline: function(){
                var self = this;
                var deferred = $.Deferred();
                if(CartData.hasErrors() && CartData.errorMessages()){
                    CheckoutResource().processResponseMessages(CartModel.errorMessages(), 0);
                }else {
                    var initParams = CartModel.getQuoteInitParams();
                    var params = {quote_id: initParams.quote_id};
                    params.payment = self.getPaymentDataToPlaceOrder();
                    params.shipping_method = self.selectedShippingCode();
                    params.quote_data = DiscountModel.getApplyOnlineParams();
                    params.actions = {
                        create_invoice: self.createInvoice(),
                        create_shipment: self.createShipment(),
                        delivery_time: self.getDeliverytime()
                    };
                    params.integration = [];
                    params.extension_data = [];
                    Event.dispatch('webpos_place_order_online_before', params);
                    self.loading(true);
                    CheckoutResource().setPush(true).setLog(false).placeOrder(params, deferred);
                    deferred.done(function (response) {
                        if (response && response.increment_id) {
                            self.placeOrderSuccess(response);
                        }else{
                            response = JSON.parse(response);
                            var order = response.order;
                            var paymentInfo = response.payment_infomation;
                            if(order && order.entity_id){
                                var orderId = order.entity_id;
                                if (self.isAuthorizeNetDirectpost()) {
                                    if (!Directpost().CheckoutModel()) {
                                        Directpost().CheckoutModel(self);
                                    }
                                    if (orderId) {
                                        Directpost().processPayment(orderId, paymentInfo);
                                    }
                                }
                                if (self.isCryozonicStripe()) {
                                    if (!Stripe().CheckoutModel()) {
                                        Stripe().CheckoutModel(self);
                                    }
                                    if (orderId) {
                                        Stripe().processPayment(orderId);
                                    } else {

                                    }
                                }
                            }
                        }
                    }).always(function () {
                        if(!self.isAuthorizeNetDirectpost()){
                            self.loading(false);
                        }
                    });
                }
                return deferred;
            },
            placeOrderSuccess: function(orderData){
                if(orderData){
                    var self = this;
                    if (orderData.increment_id) {
                        var message = Helper.__('Order has been created successfully ') + "#" + orderData.increment_id;
                        AddNoti(message, true, "success", Helper.__('Message'));
                    }
                    self.createOrderResult(orderData);
                    Event.dispatch('webpos_place_order_online_after', {data: orderData});
                }

            },
            getSaveOnlineQuoteDataParams: function(comment){
                var self = this;
                var params = {
                    quote_id: Helper.getOnlineConfig(CartData.KEY.QUOTE_ID),
                    quote_data: DiscountModel.getApplyOnlineParams()
                };
                params.quote_data.customer_note = (comment)?comment:self.orderComment();
                return params;
            },
            applyCouponOnline: function(){
                var deferred = $.Deferred();
                var self = this;
                if(DiscountModel.couponCode()){
                    deferred = self._afterSaveCart($.proxy(self._applyCouponOnline, self));
                }else{
                    deferred = self.cancelCouponOnline();
                }
                return deferred;
            },
            _applyCouponOnline: function(){
                var self = this;
                var deferred = $.Deferred();
                var params = CartModel.getQuoteInitParams();
                params.coupon_code = DiscountModel.couponCode();
                CartModel.loading(true);
                CheckoutResource().setPush(true).setLog(false).applyCoupon(params,deferred);
                deferred.done(function(response){
                    if(DiscountModel.promotionDiscountAmount()){
                        DiscountModel.appliedPromotion(true);
                    }
                }).always(function(){
                    CartModel.loading(false);
                });
                return deferred;
            },
            cancelCouponOnline: function(){
                var self = this;
                var deferred = $.Deferred();
                var params = CartModel.getQuoteInitParams();
                params.coupon_code = '';
                self.loading(true);
                CheckoutResource().setPush(true).setLog(false).cancelCoupon(params,deferred);
                deferred.done(function(response){
                    DiscountModel.couponCode('');
                    DiscountModel.appliedPromotion(false);
                }).always(function(){
                    self.loading(false);
                });
                return deferred;
            },
            selectCustomerOnline: function(){
                var self = this;
                self.loading(true);
                var deferred = CartModel.saveCartOnline();
                deferred.always(function(){
                    self.loading(false);
                });
                return deferred;
            },
            /**
             * Check apply discount online
             * @returns {*}
             */
            applyCartDiscountOnline: function(){
                var self = this;
                var deferred = self._afterSaveCart($.proxy(self._applyCartDiscountOnline, self));
                return deferred;
            },
            /**
             * Process apply discount online
             * @returns {*}
             */
            _applyCartDiscountOnline: function(){
                var self = this;
                var deferred = $.Deferred();
                var params = self.getSaveOnlineQuoteDataParams()
                CartModel.loading(true);
                CheckoutResource().setPush(true).setLog(false).saveQuoteData(params,deferred);
                deferred.always(function(){
                    CartModel.loading(false);
                });
                return deferred;
            },
            /**
             * Call function after save cart
             * @param callback
             * @returns {*}
             * @private
             */
            _afterSaveCart: function(callback, args, saveBeforeRemove){
                var deferred = $.Deferred();
                var self = this;
                if(CartModel.currentPage() == CartData.PAGE.CHECKOUT){
                    deferred = callback(args);
                }else{
                    CartModel.saveCartOnline(saveBeforeRemove).done(function(){
                        deferred = callback(args);
                    });
                }
                return deferred;
            },
            /**
             * Save cart and save note to quote
             * @returns {*}
             */
            saveCustomerNote: function(){
                var self = this;
                var deferred = self._afterSaveCart($.proxy(self._saveCustomerNote, self), self.orderComment());
                return deferred;
            },
            /**
             * Call api to save
             * @returns {*}
             * @private
             */
            _saveCustomerNote: function(comment){
                var self = this;
                var deferred = $.Deferred();
                var params = self.getSaveOnlineQuoteDataParams(comment);
                CartModel.loading(true);
                CheckoutResource().setPush(true).setLog(false).saveQuoteData(params,deferred);
                deferred.always(function(){
                    CartModel.loading(false);
                });
                return deferred;
            },
            loadCurrentQuote: function(){
                var self = this;
                var params = CartModel.getQuoteInitParams();
                if(params.quote_id){
                    self.loadCartDataOnline();
                }
            },
            getPaymentDataToPlaceOrder: function(){
                var self = this;
                var payment = {
                    method:self.paymentCode(),
                    method_data:self.getPaymentsData()
                }
                if(self.isCreditCardPayment()){
                    payment = CreditCard.getData();
                    payment.method = self.paymentCode();
                }
                return payment;
            },
            getQuoteDataToPlaceOrder: function(){
                var self = this;
                var quote_data = {
                    webpos_delivery_date: self.getDeliverytime()
                };
                Event.dispatch('prepare_quote_data_to_place_order',quote_data);
                return quote_data;
            },
            isPrepaid: function(){
                var self = this;
                if(self.isPaypalPro()){
                    self.getPaymentRequestOnline();
                    return true;
                }
                return false;
            },
            getPaymentRequestOnline: function(){
                var self = this;
                var deferred = $.Deferred();
                var initParams = CartModel.getQuoteInitParams();
                var params = {quote_id: initParams.quote_id};
                params.payment = self.getPaymentDataToPlaceOrder();
                self.loading(true);
                CheckoutResource().setPush(true).setLog(false).getPaymentRequest(params, deferred);
                deferred.done(function (response) {
                    var result = JSON.parse(response);
                    self.processOnlinePayment(result);
                }).always(function () {
                    self.loading(false);
                });
            }
        };
        CheckoutModel.initDefaultData();
        CheckoutModel.loadCurrentQuote();
        return CheckoutModel;
    }
);