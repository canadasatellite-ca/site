/*
 *  Copyright © 2016 Magestore. All rights reserved.
 *  See COPYING.txt for license details.
 *
 */

define(
    [
        'jquery',
        'ko',
        'Magestore_Webpos/js/model/checkout/cart/items',
        'Magestore_Webpos/js/model/checkout/taxcalculator',
        'mage/translate',
        'Magestore_Webpos/js/model/event-manager',
        'Magestore_Webpos/js/model/checkout/cart/totals-factory',
        'Magestore_Webpos/js/model/catalog/product-factory',
        'Magestore_Webpos/js/helper/pole',
        'Magestore_Webpos/js/helper/price',
        'Magestore_Webpos/js/helper/general',
        'Magestore_Webpos/js/model/checkout/cart/data/cart',
        'Magestore_Webpos/js/model/resource-model/magento-rest/checkout/cart'
    ],
    function ($, ko, Items, TaxCalculator, __, Event, TotalsFactory, ProductFactory, poleHelper, priceHelper, Helper, CartData, CartResource) {
        "use strict";
        var CartModel = {
            loading: ko.observable(),
            customerId: ko.observable(''),
            customerGroup: ko.observable(''),
            customerData: ko.observable({}),
            currentPage: ko.observable(),
            GUEST_CUSTOMER_NAME: __("Guest"),
            BACK_CART_BUTTON_CODE: "back_to_cart",
            CHECKOUT_BUTTON_CODE: "checkout",
            HOLD_BUTTON_CODE: "hold",
            CheckoutModel:ko.observable(),
            CartCustomerModel:ko.observable(),
            initialize: function(){
                var self = this;
                self.initObserver();
                return self;
            },
            initObserver: function(){
                var self = this;
                self.isOnCheckoutPage = ko.pureComputed(function(){
                    return (self.currentPage() == CartData.PAGE.CHECKOUT)?true:false;
                });
                Event.observer('init_quote_online_after', function(event, response){
                    if(response && response.data){
                        self.saveQuoteData(response.data);
                    }
                });
            },
            emptyCart: function(){
                Items.items.removeAll();
                this.removeCustomer();
                this.CheckoutModel().useDefaultAddress(this.CheckoutModel().ADDRESS_TYPE.BILLING);
                this.CheckoutModel().useDefaultAddress(this.CheckoutModel().ADDRESS_TYPE.SHIPPING);
                this.CheckoutModel().selectedShippingCode("");
                this.CheckoutModel().selectedShippingPrice(0);
                TotalsFactory.get().shippingData("");
                TotalsFactory.get().shippingFee(0);
                TotalsFactory.get().updateShippingAmount(0);
                Event.dispatch('cart_empty_after','');
                poleHelper('', 'Total: ' +
                    priceHelper.convertAndFormat(TotalsFactory.get().getTotalValue('grand_total')));
            },
            addCustomer: function(data){
                this.customerData(data);
                this.customerId(data.id);
                this.customerGroup(data.group_id);
                this.collectTierPrice();
                poleHelper('', 'Total: ' +
                    priceHelper.convertAndFormat(TotalsFactory.get().getTotalValue('grand_total')));
            },
            removeCustomer: function(){
                this.CartCustomerModel().setCustomerId("");
                this.CartCustomerModel().setFullName(this.GUEST_CUSTOMER_NAME);
                this.customerId("");
                this.customerGroup("");
                this.customerData({});
                this.collectTierPrice();
                Event.dispatch('cart_remove_customer_after',{guest_customer_name:this.GUEST_CUSTOMER_NAME});
                poleHelper('', 'Total: ' +
                    priceHelper.convertAndFormat(TotalsFactory.get().getTotalValue('grand_total')));
            },
            removeItem: function(itemId){
                Items.removeItem(itemId);
                if(Items.items().length == 0){
                    TotalsFactory.get().updateShippingAmount(0);
                }
                TotalsFactory.get().collectShippingTotal();
                TotalsFactory.get().collectTaxTotal();
                Event.dispatch('cart_item_remove_after',Items.items());
                poleHelper('', 'Total: ' +
                    priceHelper.convertAndFormat(TotalsFactory.get().getTotalValue('grand_total')));
            },
            addProduct: function(data){
                var validate = true;
                var item = Items.getAddedItem(data);
                if(item !== false){
                    var dataToValidate = item.getData();
                    if(dataToValidate.product_id != "customsale" && data.product_type != "bundle"){
                        dataToValidate.qty += data.qty;
                        dataToValidate.customer_group = this.customerGroup();
                        validate = ProductFactory.get().validateQtyInCart(dataToValidate);
                    }
                }else{
                    if(data.product_id != "customsale" && data.product_type != "bundle"){
                        data.customer_group = this.customerGroup();
                        if(data.minimum_qty && data.qty < data.minimum_qty){
                            data.qty = data.minimum_qty;
                        }
                        if(data.maximum_qty && data.maximum_qty > 0 && data.qty > data.maximum_qty){
                            data.qty = data.maximum_qty;
                        }
                        validate = ProductFactory.get().validateQtyInCart(data);
                    }
                }
                if(validate){
                    data = this.collectTaxRate(data);
                    Items.addItem(data);
                    TotalsFactory.get().collectShippingTotal();
                    TotalsFactory.get().collectTaxTotal();
                    this.collectTierPrice();
                }
                poleHelper(data.sku + ' +' + priceHelper.convertAndFormat(parseFloat(data.unit_price) * parseFloat(data.qty)), 'Total: ' +
                    priceHelper.convertAndFormat(TotalsFactory.get().getTotalValue('grand_total')));
            },
            updateItem: function(itemId, key, value){
                var validate = true;
                var item = Items.getItem(itemId);
                if(item){
                    if(key == "qty"){
                        var data = item.getData();
                        data.qty = value;
                        if(data.product_id != "customsale" && data.product_type != "bundle"){
                            data.customer_group = this.customerGroup();
                            validate = ProductFactory.get().validateQtyInCart(data);
                        }
                        if(data.product_id == "customsale"){
                            value = (value > 0)?value:1;
                        }
                    }
                    if(validate){
                        Items.setItemData(itemId, key, value);
                        TotalsFactory.get().collectShippingTotal();
                        TotalsFactory.get().collectTaxTotal();
                    }
                }
            },
            getItemData: function(itemId, key){
                return Items.getItemData(itemId, key);
            },
            getItemsInfo: function(){
                var itemsInfo = [];
                if(Items.items().length > 0){
                    ko.utils.arrayForEach(Items.items(), function(item) {
                        itemsInfo.push(item.getInfoBuyRequest());
                    });
                }
                return itemsInfo;
            },
            getItemsDataForOrder: function(){
                var itemsData = [];
                if(Items.items().length > 0){
                    ko.utils.arrayForEach(Items.items(), function(item) {
                        itemsData.push(item.getDataForOrder());
                    });
                }
                return itemsData;
            },
            getItemsInitData: function(){
                var itemsData = [];
                if(Items.items().length > 0){
                    ko.utils.arrayForEach(Items.items(), function(item) {
                        itemsData.push(item.getData());
                    });
                }
                return itemsData;
            },
            isVirtual: function(){
                var isVirtual = true;
                if(Items.items().length > 0){
                    var notVirtualItem = ko.utils.arrayFilter(Items.items(), function(item) {
                        return item.is_virtual() == false;
                    });
                    isVirtual = (notVirtualItem.length > 0)?false:true;
                }
                return isVirtual;
            },
            totalItems: function(){
                return Items.totalItems();
            },
            totalShipableItems: function(){
                return Items.totalShipableItems();
            },
            collectTaxRate: function(data){
                var self = this;
                var calculateTaxBaseOn = window.webposConfig["tax/calculation/based_on"];
                var address = (calculateTaxBaseOn == 'shipping')?self.CheckoutModel().shippingAddress():self.CheckoutModel().billingAddress();
                data.tax_rates = TaxCalculator().getProductTaxRate(data.tax_class_id, this.customerGroup(), address);
                data.tax_origin_rates = TaxCalculator().getOriginRate(data.tax_class_id, this.customerGroup());
                return data;
            },
            reCollectTaxRate: function(){
                var self = this;
                if(Items.items().length > 0){
                    var calculateTaxBaseOn = window.webposConfig["tax/calculation/based_on"];
                    var address = (calculateTaxBaseOn == 'shipping')?self.CheckoutModel().shippingAddress():self.CheckoutModel().billingAddress();
                    ko.utils.arrayForEach(Items.items(), function(item) {
                        var taxrate = TaxCalculator().getProductTaxRate(item.tax_class_id(), self.customerGroup(), address);
                        var taxOriginRate = TaxCalculator().getOriginRate(item.tax_class_id(), self.customerGroup());
                        self.updateItem(item.item_id(),'tax_rates',taxrate);
                        self.updateItem(item.item_id(),'tax_origin_rates',taxOriginRate);
                    });
                }
            },
            collectTierPrice: function(){
                var self = this;
                if(Items.items().length > 0){
                    var hasTierPriceItems = ko.utils.arrayFilter(Items.items(), function(item) {
                        return (item.tier_prices() && item.tier_prices().length > 0)?true:false;
                    });
                    ko.utils.arrayForEach(hasTierPriceItems, function(item) {
                        var tier_prices = item.tier_prices();
                        var itemQty = item.qty();
                        var tier_price = false;
                        if(tier_prices){
                            var validTierPrice = ko.utils.arrayFirst(tier_prices, function(data) {
                                return (((self.customerGroup() == data.customer_group_id ) || (data.customer_group_id  == 32000))  && itemQty >= data.qty);
                            });
                            if(validTierPrice){
                                tier_price = validTierPrice.value;
                            }
                        }
                        self.updateItem(item.item_id(),'tier_price',tier_price);
                    });

                    var hasNoTierPriceItems = ko.utils.arrayFilter(Items.items(), function(item) {
                        return (item.tier_prices() && item.tier_prices().length > 0)?false:true;
                    });
                    ko.utils.arrayForEach(hasNoTierPriceItems, function(item) {
                        var child_id = item.child_id();
                        var itemQty = item.qty();
                        var collection = ProductFactory.get().getCollection();
                        collection.reset();
                        collection.addFieldToFilter('id', child_id, 'eq');
                        var cdeferred = collection.load();
                        cdeferred.done(function (data) {
                            if (data.items[0]) {
                                var child = data.items[0];
                                var tier_price = false;
                                if(child && child.tier_prices){
                                    item.tier_prices(child.tier_prices);
                                    var validTierPrice = ko.utils.arrayFirst(child.tier_prices, function(data) {
                                        return (((self.customerGroup() == data.customer_group_id ) || (data.customer_group_id  == 32000))  && itemQty >= data.qty);
                                    });
                                    if(validTierPrice){
                                        tier_price = validTierPrice.value;
                                    }
                                }
                                self.updateItem(item.item_id(),'tier_price',tier_price);
                            }
                        });
                    });
                }
            },
            validateItemsQty: function(){
                var self = this;
                var error = [];
                if(Items.items().length > 0){
                    ko.utils.arrayForEach(Items.items(), function(item) {
                        var data = item.getData();
                        if(data.product_id != "customsale" && data.product_type != "bundle"){
                            data.customer_group = self.customerGroup();
                            var validate = ProductFactory.get().checkStockItemsInCart(data);
                            if(validate !== true){
                                error.push(validate);
                            }
                        }
                    });
                }
                return (error.length > 0)?error:true;
            },
            getItemChildsQty: function(){
                var qtys = [];
                if(Items.items().length > 0){
                    ko.utils.arrayForEach(Items.items(), function(item) {
                        var data = item.getData();
                        if(data.product_id != "customsale"){
                            if(data.product_type == "bundle"){
                                if(data.bundle_childs_qty){
                                    ko.utils.arrayForEach(data.bundle_childs_qty, function(option) {
                                        qtys.push({id:option.code,qty:option.value});
                                    });
                                }
                            }else{
                                if(data.child_id){
                                    qtys.push({id:data.child_id,qty:data.qty});
                                }else{
                                    qtys.push({id:data.product_id,qty:data.qty});
                                }
                            }
                        }
                    });
                }
                return qtys;
            },
            getQtyInCart: function(productId){
                var qty = 0;
                if(productId && Items.items().length > 0){
                    ko.utils.arrayForEach(Items.items(), function(item) {
                        if(item.getData('product_id') == productId){
                            qty += item.getData('qty');
                        }
                    });
                }
                return qty;
            },
            hasStorecredit: function(){
                if(Items.items().length > 0){
                    var storecreditItem = ko.utils.arrayFirst(Items.items(), function(item) {
                        return (item.product_type() == "customercredit");
                    });
                    if(storecreditItem){
                        return true;
                    }
                }
                return false;
            },
            canCheckoutStorecredit: function(){
                var hasStorecredit = this.hasStorecredit();
                if(hasStorecredit && this.customerId() == ''){
                    return false;
                }
                return true;
            },
            applyCatalogRules: function(itemsData){
                var self = this;
                if(itemsData && Items.items().length > 0){
                    $.each(itemsData, function(itemId, itemData) {
                        var item = ko.utils.arrayFirst(Items.items(), function(item) {
                            return (item.item_id() == itemId);
                        });
                        if(item){
                            if(itemData.applied_rule_ids && (typeof itemData.base_original_price != 'undefined')){
                                item.applied_catalog_rules(true);
                                item.base_original_price(itemData.base_original_price);
                                item.unit_price(itemData.base_price);
                            }
                            item.qty(itemData.qty);
                        }
                    });
                }
            },
            getQuoteCustomerParams: function(){
                var self = this;
                return {
                    customer_id: self.customerId(),
                    billing_address: self.CheckoutModel().billingAddress(),
                    shipping_address: self.CheckoutModel().shippingAddress()
                };
            },
            resetQuoteInitData: function(){
                var self = this;
                var data = {
                    quote_id: '',
                    customer_id: self.customerId()
                };
                self.saveQuoteData(data);
            },
            getCustomerInitParams: function(){
                var self = this;
                return {
                    customer_id: Helper.getOnlineConfig(CartData.KEY.CUSTOMER_ID),
                    billing_address: Helper.getOnlineConfig(CartData.KEY.BILLING_ADDRESS),
                    shipping_address: Helper.getOnlineConfig(CartData.KEY.SHIPPING_ADDRESS),
                    data: Helper.getOnlineConfig(CartData.KEY.CUSTOMER_DATA)
                };
            },
            getQuoteInitParams: function(){
                var self = this;
                return {
                    quote_id: Helper.getOnlineConfig(CartData.KEY.QUOTE_ID),
                    store_id: Helper.getOnlineConfig(CartData.KEY.STORE_ID),
                    customer_id: Helper.getOnlineConfig(CartData.KEY.CUSTOMER_ID),
                    currency_id: Helper.getOnlineConfig(CartData.KEY.CURRENCY_ID),
                    till_id: Helper.getOnlineConfig(CartData.KEY.TILL_ID)
                };
            },
            /**
             * Save cart only - not distch events
             * @returns {*}
             */
            saveCartOnline: function(){
                var self = this;
                var params = self.getQuoteInitParams();
                params.items = self.getItemsInfo();
                params.customer = self.getQuoteCustomerParams();
                params.section = CartData.KEY.QUOTE_INIT;
                self.loading(true);
                var apiRequest = $.Deferred();
                CartResource().setPush(true).setLog(false).saveCart(params, apiRequest);
                apiRequest.done(function(response){
                    // if(response.status == CartData.DATA.STATUS.ERROR && response.messages){
                    //     CartData.errorMessages(response.messages);
                    // }else{
                    //     CartData.hasErrors(false);
                    //     CartData.errorMessages('');
                    // }
                }).always(function(){
                    self.loading(false);

                });
                return apiRequest;
            },
            /**
             * Save cart and dispatch events
             * @param saveBeforeRemove
             * @returns {*}
             */
            saveCartBeforeCheckoutOnline: function(saveBeforeRemove){
                var self = this;
                var params = self.getQuoteInitParams();
                params.items = self.getItemsInfo();
                params.customer = self.getQuoteCustomerParams();
                if(saveBeforeRemove == true){
                    params.section = CartData.KEY.QUOTE_INIT;
                }
                self.loading(true);
                var apiRequest = $.Deferred();
                CartResource().setPush(true).setLog(false).saveCartBeforeCheckout(params, apiRequest);

                apiRequest.done(function(response){
                    // if(response.status == CartData.DATA.STATUS.ERROR && response.messages && saveBeforeRemove != true){
                    //     CartData.errorMessages(response.messages);
                    // }else{
                    //     CartData.hasErrors(false);
                    //     CartData.errorMessages('');
                    // }
                }).always(function(){
                    self.loading(false);
                    poleHelper('', 'Total: ' + Helper.convertAndFormatPrice(TotalsFactory.get().grandTotal()));
                });
                return apiRequest;
            },
            /**
             * Call API to empty cart - remove quote
             * @returns {*}
             */
            removeCartOnline: function(){
                var self = this;
                var initParams = self.getQuoteInitParams();
                var params = {quote_id: initParams.quote_id};
                self.loading(true);
                var apiRequest = $.Deferred();
                CartResource().setPush(true).setLog(false).removeCart(params, apiRequest);

                apiRequest.done(
                    function (response) {
                        if(typeof response.quote_init != undefined){
                            self.emptyCart();
                        }
                    }
                ).always(function(){
                    self.loading(false);
                    poleHelper('', 'Total: ' + Helper.convertAndFormatPrice(TotalsFactory.get().grandTotal()));
                });
                return apiRequest;
            },
            /**
             * Call API to remove cart item online
             * @param itemId
             * @returns {*}
             */
            removeItemOnline: function(itemId){
                var self = this;
                if(Items.items().length == 1){
                    return self.removeCartOnline();
                }

                var params = self.getQuoteInitParams();
                params.item_id = itemId;

                self.loading(true);
                var apiRequest = $.Deferred();
                CartResource().setPush(true).setLog(false).removeItem(params, apiRequest);

                apiRequest.done(
                    function (response) {
                        if(typeof response.quote_init != undefined){
                            self.removeItem(itemId);
                        }
                    }
                ).always(function(){
                    self.loading(false);
                    poleHelper('', 'Total: ' +
                    Helper.convertAndFormatPrice(TotalsFactory.get().grandTotal()));
                });
                return apiRequest;
            },
            /**
             * Check if cart has been saved online or not
             * @returns {boolean}
             */
            hasOnlineQuote: function(){
                var self = this;
                return (Helper.getOnlineConfig(CartData.KEY.QUOTE_ID))?true:false;
            },
            /**
             * Save quote init data to data manager
             * @param quoteData
             */
            saveQuoteData: function(quoteData){
                if(quoteData){
                    $.each(quoteData, function(key, value){
                        value = (value)?value:'';
                        Helper.saveOnlineConfig(key, value);
                    })
                }
            }
        };
        return CartModel.initialize();
    }
);