require([
    'jquery',
    'mage/apply/main',
    //'braintree',
    'Magento_Ui/js/modal/alert',
    'mage/template',
    'jquery/jquery-ui-timepicker-addon',
    'mage/translate',
    'mwOpcheckout',
    'mwHeadmain',
    'mwJqueryPlugins',
    'mwJqueryCorner'
], function($, mage, /*braintree,*/ alert, mageTemplate) {

window.gb_view_onestep_plugin_rewardpoints = null;

var view_onestep_init,
    view_onestep_billing,
    view_onestep_payment,
    view_onestep_gift,
    view_onestep_coupon,
    view_onestep_cart,
    view_onestep_loader,
    view_onestep_shipping_method,
    view_onestep_shipping,
    view_onestep_user,
    view_onestep_delivery_date,
    view_onestep_plugin_sagepay_server,
    view_onestep_plugin_braintree,
    view_onestep_load_session_form;

window.iChange = true;
window.changeLoad = false;
window.changeSelect = false;
window.colorLoading = onestepConfig.styleColor;
window.requestAjax = [];
window.updateOnestep = null;
window.redirectSuccess = null;

window.osc_block_loader = {
    updatecart              : 'checkout-review-load',
    updatebillingaddress    : 'co-billing-form',
    updatebillingform       : 'co-billing-form',
    updateshippingaddress   : 'co-shipping-form',
    updateshippingform      : 'co-shipping-form',
    updateshippingtype      : 'checkout-shipping-method-load',
    updateshippingmethod    : 'checkout-shipping-method-load',
    updatepaymenttype       : 'co-payment-form',
    updatepaymentmethod     : 'co-payment-form',
    updateddate     		: 'co-ddate',
    updatecoupon            : 'tab-coupon'
};

window.currentPaymentMethod = null;
window.verifiedVAT = false;

window.OneStep.$(document).ready(function($){
    OneStep.Views.Payment           = Backbone.View.extend({
        el: window.OneStep.$("#checkout-step-payment"),
        events: {
            "click .payment_method_handle"  :   "hdlChangeMethod"
        },
        initialize: function(){
            this.payment_method_changed = -1;
        },
        hdlChangeMethod: function(ev){
            var val = window.OneStep.$(ev.target).filter(':checked').val();
            if(val != this.payment_method_changed){
                window.OneStep.$('#co-payment-form dd fieldset').css('display', 'none');
                window.OneStep.$('#co-payment-form dd.'+val+' fieldset').css('display', 'block');
                currentPaymentMethod = window.OneStep.$(ev.target);
                var temp = window.OneStep.$('#id_gift_wrap').is(':checked');
                var params = {updategiftwrap: temp, updatepaymentmethod: false};
                if(onestepConfig.ajaxPayment){
                    view_onestep_init.update(params);
                    this.payment_method_changed = val;
                }
            }

            return true;
        }
    });

    OneStep.Views.ShippingMethod    = Backbone.View.extend({
        el: window.OneStep.$("#checkout-shipping-method-load"),
        events: {
            "click  .shipping_method_handle"                : "hdlChangeMethod"
        },
        initialize: function(){
            this.shipping_method_changed    = -1;
        },
        hdlChangeMethod: function(ev){
            var val = window.OneStep.$(ev.target).filter(":checked").val();
            if(onestepConfig.ajaxShipping){
                var params = {updateshippingmethod: false, updatepaymenttype: (onestepConfig.ajaxPaymentOnShipping ? true : false)}; /* false: mean dont show loading on block shipping method */
                view_onestep_init.update(params);
                this.shipping_method_changed = val;
            }
        }
    });

    OneStep.Views.Shipping          = Backbone.View.extend({
        el: window.OneStep.$("#co-shipping-form"),
        events: {
            "click  #shipping\\:save_in_address_book"       : "hdlCheckSaveAddress",
            "click  #shipping\\:same_as_billing"            : "hdlSameAsBilling",
            "click  .shipping_method_handle"                : "hdlChangeMethod",
            "change #shipping-address-select"               : "hdlChangeAddress",
            "change #shipping\\:country_id"                 : "hdlChangeCountry",
            "change #shipping\\:region_id"                  : "hdlChangeRegion",
            "blur   #shipping\\:region"                     : "hdlBlurRegion",
            "blur   #shipping\\:postcode"                   : "hdlBlurPostcode",
            "blur   #shipping\\:city"                       : "hdlBlurCity"
        },
        initialize: function(){
            this.val_regionship_before      = window.OneStep.$('#shipping\\:region').val();
            this.val_zipship_before         = window.OneStep.$('#shipping\\:postcode').val();
            this.val_cityship_before        = window.OneStep.$('#shipping\\:city').val();
            this.shipping_method_changed    = -1;
        },
        hdlSameAsBilling: function(ev){
            var isChecked = window.OneStep.$(ev.target).is(':checked');
            if (isChecked == true) {
                window.OneStep.$('#ship_to_same_address').trigger('click');
            }

            var address_shipping_id     = window.OneStep.$("#shipping-address-select").val();
            var address_billing_id      = window.OneStep.$("#billing\\:address-select").val();
            if (onestepConfig.hasAddress) {
                if (address_billing_id == "") {
                    var params = {updateshippingtype: (onestepConfig.ajaxShippingOnAddresss ? true : false)};
                    view_onestep_init.update(params);
                } else {
                    var params = {updateshippingaddress: true, updateshippingtype: (onestepConfig.ajaxShippingOnAddresss ? true : false)};
                    view_onestep_init.update(params);
                }
            } else {
                if (isChecked == false) {
                    if (!isLogged) {
                        window.OneStep.$('#shipping-new-address-form').clearForm();
                    }
                } else {
                    if (changeSelect == false) {
                        if (!address_shipping_id) {
                            var countryid = window.OneStep.$("#shipping\\:country_id option:selected").val();
                            if (countryid != ""){
                                var params = {updateshippingtype: (onestepConfig.ajaxShippingOnAddresss ? true : false)};
                                view_onestep_init.update(params);
                            }
                        }
                        changeSelect = true;
                    }
                }
            }
        },
        hdlCheckSaveAddress: function(ev){
            if(window.OneStep.$(ev.target).is(':checked')){
                window.OneStep.$(ev.target).val(1);
            }else{
                window.OneStep.$(ev.target).val(0);
            }
            return true;
        },
        hdlChangeAddress: function(ev){
            var val = window.OneStep.$(ev.target).find("option:selected").val();
            var changeSelect = true;
            if (val == "") {
                window.OneStep.$('#shipping-new-address-form').clearForm();
            }else {
                var params = {updateshippingform: true, updateshippingaddress: true, updateshippingtype: (onestepConfig.ajaxShippingOnAddresss ? true : false), updatepaymenttype: (onestepConfig.ajaxPaymentOnAddresss ? true : false)};
                view_onestep_init.update(params);
            }
        },
        hdlChangeCountry: function(ev){
            var value_id = window.OneStep.$('#shipping\\:country_id').val();
            view_onestep_load_session_form.replaceItem(osc_block_loader.updateshippingaddress, "shipping:country_id",value_id);
            view_onestep_load_session_form.replaceItem(osc_block_loader.updateshippingaddress, "shipping:same_as_billing",false);

            if(onestepConfig.ajaxCountry){
                var params = {changebycountry: true, updateshippingaddress: false, updateshippingtype: (onestepConfig.ajaxShippingOnAddresss ? true : false), updatepaymenttype: (onestepConfig.ajaxPaymentOnAddresss ? true : false)};
                params.addition_post = {withoutaddressselect: true};
                view_onestep_init.update(params);
            }
        },
        hdlChangeRegion: function(ev){
            var value_id = window.OneStep.$('#shipping\\:region_id').val();
            view_onestep_load_session_form.replaceItem(osc_block_loader.updateshippingaddress, "shipping:region_id",value_id);
            view_onestep_load_session_form.replaceItem(osc_block_loader.updateshippingaddress, "shipping:same_as_billing",false);
            if(onestepConfig.ajaxRegion){
                changeLoad = true;
                var params = {changebycountry: true, updateshippingaddress: false, updateshippingtype: (onestepConfig.ajaxShippingOnAddresss ? true : false)};
                view_onestep_init.update(params);
            }
        },
        hdlBlurRegion: function(ev){
            /** must check tagName is input */
            if(window.OneStep.$(ev.target).get(0).tagName == 'INPUT'){
                var val = window.OneStep.$(ev.target).val();
                if (val != "" && this.val_regionship_before != val) {
                    if (window.OneStep.$('#shipping\\:country_id').val()){
                        var params = {changebycountry: true, updateshippingaddress: true, updateshippingtype: (onestepConfig.ajaxShippingOnAddresss ? true : false)};
                        view_onestep_init.update(params);
                    }
                }
                this.val_regionship_before = val;
            }

        },
        hdlBlurPostcode: function(ev){
            if(onestepConfig.ajaxZipcode){
                var val = window.OneStep.$(ev.target).val();
                if (val != "" && this.val_zipship_before != val) {
                    if (window.OneStep.$('#shipping\\:country_id').val()){
                        var params = {updateshippingaddress: true, updateshippingtype: (onestepConfig.ajaxShippingOnAddresss ? true : false)};
                        view_onestep_init.update(params);
                    }
                }
                this.val_zipship_before = val;
            }
        },
        hdlBlurCity: function(ev){
            if(onestepConfig.ajaxCity){
                var val = window.OneStep.$(ev.target).val();
                if (val != "" && this.val_cityship_before != val) {
                    if (window.OneStep.$('#shipping\\:country_id').val()){
                        var params = {updateshippingtype: (onestepConfig.ajaxShippingOnAddresss ? true : false)};
                        view_onestep_init.update(params);
                    }
                }
                this.val_cityship_before = val;
            }
        }
    });

    OneStep.Views.Billing           = Backbone.View.extend({
        el: window.OneStep.$("#co-billing-form"),
        events: {
            "click  #billing\\:save_in_address_book"    : "hdlCheckSaveAddress",
            "click  #billing\\:save_into_account"       : "hdlSaveIntoAccount",
            "click  #ship_to_same_address"              : "hdlShiptoSameAddress",
            "click  #register_new_account"              : "hdlCreateNewAccount",
            "change #billing\\:address-select"          : "hdlChangeAddress",
            "change #billing\\:country_id"              : "hdlChangeCountry",
            "change #billing\\:region_id"               : "hdlChangeRegion",
            "blur   #billing\\:region"                  : "hdlBlurRegion",
            "blur   #billing\\:postcode"                : "hdlBlurPostcode",
            "blur   #billing\\:city"                    : "hdlBlurCity",
            "blur   #billing\\:email"                   : "hdlBlurEmail",
            "blur   #billing\\:taxvat"                  : "hdlBlurTax"
        },
        initialize: function(){
            this.val_regionbill_before = window.OneStep.$('#billing\\:region').val();
            this.val_zipbill_before = window.OneStep.$('#billing\\:postcode').val();
            this.val_citybill_before = window.OneStep.$('#billing\\:city').val();
            this.customer_email = window.OneStep.$('#billing\\:email').val();
            this.val_vat_before = window.OneStep.$('#billing\\:taxvat').val();
            this.billaddbook = 1;
        },
        hdlCreateNewAccount: function(ev){
            if (window.OneStep.$(ev.target).is(':checked')) {
                window.OneStep.$('#register-customer-password').css('display', 'block');
                window.OneStep.$(ev.target).val(1);
            }
            else {
                window.OneStep.$(ev.target).val(0);
                window.OneStep.$('#register-customer-password').css('display', 'none');
                window.OneStep.$('#register-customer-password').clearForm();
            }
        },
        hdlSaveIntoAccount: function(ev){
            window.OneStep.$(ev.target).val(window.OneStep.$(ev.target).is(':checked') ? 1 : 0);
        },
        hdlCheckSaveAddress: function(ev){
            if(window.OneStep.$(ev.target).is(':checked')){
                window.OneStep.$(ev.target).val(1);
            }else{
                window.OneStep.$(ev.target).val(0);
            }
            return true;
        },
        hdlShiptoSameAddress: function(ev){
            var isChecked = window.OneStep.$(ev.target).is(':checked');
            var shipAddSelect = window.OneStep.$("#shipping-address-select");
            var billAddSelect = window.OneStep.$("#billing:\\address-select");
            if (isChecked == false) {
                window.OneStep.$("#mw-osc-p2").removeClass('onestepcheckout-numbers onestepcheckout-numbers-2').addClass('onestepcheckout-numbers onestepcheckout-numbers-3');
                window.OneStep.$("#mw-osc-p3").removeClass('onestepcheckout-numbers onestepcheckout-numbers-3').addClass('onestepcheckout-numbers onestepcheckout-numbers-4');
                window.OneStep.$("#mw-osc-p4").removeClass('onestepcheckout-numbers onestepcheckout-numbers-4').addClass('onestepcheckout-numbers onestepcheckout-numbers-5');

                if (iChange) {
                    if (!isLogged) {
                        window.OneStep.$('#shipping-new-address-form').clearForm();
                    }
                    iChange = false;
                }

                var value_id = window.OneStep.$('#billing\\:country_id').val();
                view_onestep_load_session_form.replaceItem(osc_block_loader.updateshippingaddress, "shipping:country_id",value_id);

                var value_state = window.OneStep.$('#billing\\:region').val();
                view_onestep_load_session_form.replaceItem(osc_block_loader.updateshippingaddress, "shipping:region",value_state);

                window.OneStep.$("#shipping_show").css('display', 'block');
                window.OneStep.$(ev.target).val(0);
                var countryid = window.OneStep.$("#shipping\\:country_id option:selected").val();
                if (countryid) {
                    var params = {updateshippingaddress: false, updateshippingtype: (onestepConfig.ajaxShippingOnAddresss ? true : false), updatepaymenttype: (onestepConfig.ajaxPaymentOnAddresss ? true : false)};
                    view_onestep_init.update(params);
                }
            }
            else {
                // change step order
                window.OneStep.$("#mw-osc-p2").removeClass('onestepcheckout-numbers onestepcheckout-numbers-3').addClass('onestepcheckout-numbers onestepcheckout-numbers-2');
                window.OneStep.$("#mw-osc-p3").removeClass('onestepcheckout-numbers onestepcheckout-numbers-4').addClass('onestepcheckout-numbers onestepcheckout-numbers-3');
                window.OneStep.$("#mw-osc-p4").removeClass('onestepcheckout-numbers onestepcheckout-numbers-5').addClass('onestepcheckout-numbers onestepcheckout-numbers-4');

                window.OneStep.$("#shipping\\:same_as_billing").prop('checked', false);
                window.OneStep.$('#shipping_show').css('display', 'none');
                window.OneStep.$(ev.target).val(1);

                var countryid = window.OneStep.$("#billing\\:country_id option:selected").val();
                if (countryid) {
                    var params = {updatebillingaddress: false, updateshippingtype: (onestepConfig.ajaxShippingOnAddresss ? true : false), updatepaymenttype: (onestepConfig.ajaxPaymentOnAddresss ? true : false)};
                    view_onestep_init.update(params);
                }
            }
        },
        hdlChangeAddress: function(ev){
            var val = window.OneStep.$(ev.target).find("option:selected").val();
            if(val == ""){
                window.OneStep.$('#billing-new-address-form').clearForm();
            } else {
                var params = {updatebillingaddress: true, updateshippingtype: (onestepConfig.ajaxShippingOnAddresss ? true : false), updatepaymenttype: (onestepConfig.ajaxPaymentOnAddresss ? true : false)};
                view_onestep_init.update(params);
            }
        },
        hdlChangeCountry: function(ev){
            // change country in billing
            var value_id = window.OneStep.$('#billing\\:country_id').val();
            view_onestep_load_session_form.replaceItem(osc_block_loader.updatebillingaddress, "billing:country_id",value_id);
            if(onestepConfig.ajaxCountry){
                var params = {changebycountry: true, checkvat: (onestepConfig.validVAT ? true : false), updatebillingaddress: false, updateshippingtype: (onestepConfig.ajaxShippingOnAddresss ? true : false), updatepaymenttype: (onestepConfig.ajaxPaymentOnAddresss ? true : false)};
                params.addition_post = {withoutaddressselect: true};
                view_onestep_init.update(params);
            }
            return true;
        },
        hdlChangeRegion: function(ev){
            var value_id = window.OneStep.$('#billing\\:region_id').val();
            view_onestep_load_session_form.replaceItem(osc_block_loader.updatebillingaddress, "billing:region_id",value_id);
            if(onestepConfig.ajaxRegion){
                var params = {changebycountry: true, updatebillingaddress: false, updateshippingtype: (onestepConfig.ajaxShippingOnAddresss ? true : false), updatepaymenttype: (onestepConfig.ajaxPaymentOnAddresss ? true : false)};
                view_onestep_init.update(params);
            }
        },
        hdlBlurRegion: function(ev){
            if(onestepConfig.ajaxRegion){
                /** must check tagName is input */
                if(window.OneStep.$(ev.target).get(0).tagName == 'INPUT'){
                    var val = window.OneStep.$(ev.target).val();

                    if (val != "" && this.val_regionbill_before != val) {
                        if (window.OneStep.$('#billing\\:country_id').val()){
                            var params = {changebycountry: true, updatebillingaddress: true, updateshippingtype: (onestepConfig.ajaxShippingOnAddresss ? true : false), updateshippingmethod: true, updatepaymenttype: (onestepConfig.ajaxPaymentOnAddresss ? true : false)};
                            view_onestep_init.update(params);
                        }
                    }
                    this.val_regionbill_before = val;
                }
            }
        },
        hdlBlurPostcode: function(ev){
            if(onestepConfig.ajaxZipcode){
                var val = window.OneStep.$(ev.target).val();
                if (val != "" && this.val_zipbill_before != val) {
                    if (window.OneStep.$('#billing\\:country_id').val()){
                        var params = {updateshippingtype: (onestepConfig.ajaxShippingOnAddresss ? true : false), updateshippingmethod: true};
                        view_onestep_init.update(params);
                    }
                }
                this.val_zipbill_before = val;
            }
        },
        hdlBlurCity: function(ev){
            if(onestepConfig.ajaxCity){
                val = this.value;
                if (val != "" && this.val_citybill_before != val) {
                    if (window.OneStep.$('#billing\\:country_id').val()){
                        var params = {updateshippingtype: (onestepConfig.ajaxShippingOnAddresss ? true : false), updateshippingmethod: true};
                        view_onestep_init.update(params);
                    }
                }

                this.val_citybill_before = val;
            }
        },
        hdlBlurEmail: function(ev){
            var email = window.OneStep.$(ev.target).val();
            if (email != "" && this.customer_email != email) {
                var params = {updateshippingtype: false};
                view_onestep_init.update(params);
            }
            this.customer_email = email;
        },
        hdlBlurTax: function(ev){
            if(onestepConfig.validVAT){
                var val = window.OneStep.$(ev.target).val();
                if (this.val_vat_before != val) {
                    var countrycode = window.OneStep.$("#billing\\:country_id").find("option:selected").val();
                    view_onestep_init.checkVAT(countrycode, val);
                }

                this.val_vat_before = val;
            }
        },
        updateAbadonInfo: function(){
            if(!isLogged && onestepConfig.ajaxEmail){
                var session_id = Math.floor(new Date().getTime() / 1000) * Math.floor((Math.random()*100)+1);
                view_onestep_loader.insideBox({el: window.OneStep.$("#checkout-shipping-method-loadding"), session_id: session_id});
                window.OneStep.$.ajax({
                    type    :   "POST",
                    url     :   onestepConfig.url.updateShippingType,
                    data    :   window.OneStep.$("#onestep_form").serialize(),
                    success: function(msg){
                        view_onestep_loader.insideBox({el: "#loader_"+session_id, action: "hide"});
                        window.OneStep.$('#checkout-shipping-method-load').html(msg);
                    }
                });
            }
        }
    });

    OneStep.Views.Gift              = Backbone.View.extend({
        el: window.OneStep.$("#checkout-review-options"),
        events:{
            "click #allow_gift_options"    : "hdlBoxGiftMessages"
        },
        hdlBoxGiftMessages: function(ev){
            if (window.OneStep.$(ev.target).is(':checked')) {
                window.OneStep.$('#allow-gift-options-container').css('display', 'block');
                if (!isLogged) {
                    window.OneStep.$('input[id^="gift-message"]').val('');
                }
                else if (!onestepConfig.hasAddress) {
                    window.OneStep.$('input[id^="gift-message-whole-to"]').val('');
                    window.OneStep.$('input[id^="gift-message-"][id$="to"]').val('');
                }
            }
            else {
                window.OneStep.$('#allow-gift-options-container').css('display', 'none');
            }
        }
    });

    OneStep.Views.Coupon            = Backbone.View.extend({
        el: window.OneStep.$("#tab-coupon"),
        events: {
            "click .btn-coupon"         : "hdlSubmitCoupon",
            "click .btn-coupon-cancel"  : "hdlCancelCoupon"
        },
        hdlSubmitCoupon: function(ev){
            var val = this.$el.find("#coupon_code").val();
            if(val == ""){
                alert({
                    content: $.mage.__("Please enter coupon code.")
                });
                return false;
            }
            this.update(val, 0);
        },
        hdlCancelCoupon: function(ev){
            this.update(this.$el.find("#coupon_code").val(), 1);
        },
        update: function(code, isRemove){
            var params = {updatecouponcode: true, updatepaymenttype: true, updateshippingtype: true};

            params.addition_post = {coupon_code: code, remove: isRemove};
            view_onestep_init.update(params);
            return false;
        }
    });

    OneStep.Views.Referal           = Backbone.View.extend({
        el: window.OneStep.$("#referal_code"),
        events: {
            "click .btn-referal-submit" :   "hdlSubmitReferal",
            "click .btn-referal-cancel" :   "hdlCancelReferal"
        },
        hdlSubmitReferal: function(ev){
            var val = this.$el.find("#code_value").val();

            if(val == ""){
                alert({
                    content: $.mage.__("Please enter referral code.")
                });
                return false;
            }
            this.update(val, 0);
            return true;
        },
        hdlCancelReferal: function(ev){
            this.update(this.$el.find("#code_value").val(), 1);
            return true;
        },
        update: function(code, isRemove){

        }
    });

    OneStep.Views.Cart              = Backbone.View.extend({
        el: window.OneStep.$("#checkout-review-load"),
        events:{
            "click .btn-update-cart"        :   "hdlUpdate",
            "click .btn-checkout-remove2"   :   "hdlRemoveItem",
            "click #id_gift_wrap"           :   "hdlGifWrap"
        },
        hdlUpdate: function(){
            var params = {updatecart: true, updateshippingtype: (onestepConfig.ajaxShippingOnQty ? true : false), updatepaymenttype: true};
            view_onestep_init.update(params);
        },
        hdlRemoveItem: function(ev){
            var id = window.OneStep.$(ev.target).attr("data-item-id");
            window.OneStep.$(ev.target).closest('tr').css("opacity", "0.5");

            var params = {removeproduct: true, updateshippingtype: true, updatepaymenttype: true, updatepaymentmethod: true};
            params.addition_post = {id: id};

            view_onestep_init.update(params);
            /** update giftbox, link top cart */
            return true;
        },
        hdlGifWrap: function(ev){
            var temp = window.OneStep.$(ev.target).is(':checked');
            var params = {updategiftwrap: temp};
            view_onestep_init.update(params);
        }
    });

    OneStep.Views.Init              = Backbone.View.extend({
        el: window.OneStep.$("#checkout-review-submit"),
        events: {
            "click .btn-checkout"               : "hdlBtnCheckout",
            "click  #subscribe_newsletter"      : "hdlSubscribeNewletter"
        },

        initialize: function(){
            if(onestepConfig.isGepIp){
                window.OneStep.$('#billing\\:postcode').attr("value", window.geoip.postcode);
                window.OneStep.$('#billing\\:city').attr("value", window.geoip.city);
                window.OneStep.$('#billing\\:region').attr("value", window.geoip.region);
                if (window.OneStep.$("#billing\\:region_id option:selected").length){
                    window.OneStep.$("#billing\\:region_id option[value='"+window.geoip.regionid+"']").attr('selected', 'selected');
                }
            }
            window.OneStep.$(".cart .discount").css({'border':'none','background':'none'});

            this.initCountry();
            if(onestepConfig.addfieldZip != 2){
                //  remove required-entry field postcode when config field postcode is not required ,
                window.OneStep.$('#billing\\:postcode').removeClass('required-entry');
                window.OneStep.$('#shipping\\:postcode').removeClass('required-entry');
            }

            if(onestepConfig.addfieldState != 2){
                // field region state
                window.OneStep.$('#billing\\:region_id').removeClass('validate-select');
                window.OneStep.$('#shipping\\:region_id').removeClass('validate-select');
            }

            if(onestepConfig.defaultShippingmethod){
                var defaultShippingMethodId = "#s_method_" + onestepConfig.defaultShippingmethod;
                window.OneStep.$(defaultShippingMethodId).attr('checked','checked');
            }

            if(onestepConfig.addfieldCountry){
                window.OneStep.$("#billing-new-address-form").show();
            }

            window.OneStep.$("#shipping-new-address-form").show();

            //add fancy box to form click login, click forgot password
            var closeBtnTemplate = '<button id="fancybox-close" class="button" type="button"><span><span>'+$.mage.__('Close')+'</span></span></button>';
            window.OneStep.$("#loginbox").fancybox({
                width   : '600',
                height	: '400',
                padding : 0,
                tpl     : {
                    closeBtn: closeBtnTemplate
                },
                helpers : {
                    overlay: {
                        locked: false
                    }
                },
                afterShow: function(){
                    view_onestep_user = new OneStep.Views.User({el: window.OneStep.$("#mw-login-form"), form: 'login'});
                }
            });
            window.OneStep.$("#onestepcheckout-toc-link").fancybox({
                padding : 0,
                tpl     : {
                    closeBtn: closeBtnTemplate
                },
                helpers : {
                    overlay: {
                        locked: false
                    }
                },
            });
            window.OneStep.$("#forgotpassword").fancybox({
                padding : 0,
                tpl     : {
                    closeBtn: closeBtnTemplate
                },
                helpers : {
                    overlay: {
                        locked: false
                    }
                },
                afterShow   : function(){
                    view_onestep_user = new OneStep.Views.User({el: window.OneStep.$("#form-validate"), form: 'forgot'});
                }
            });
            window.OneStep.$("#backlogin").fancybox({
                padding : 0,
                tpl     : {
                    closeBtn: closeBtnTemplate
                },
                helpers : {
                    overlay: {
                        locked: false
                    }
                },
            });

            var initLoad = {
                both: false,
                ship: false,
                pay : false
            };
            /*if((window.OneStep.$('input[name=shipping_method]:checked').val()) && (window.OneStep.$('input[name="payment[method]"]:checked').val())){
                initLoad.both = true;
            }else*/ if(window.OneStep.$('input[name=shipping_method]:checked').val()
                || window.OneStep.$("#billing\\:country_id").find("option:selected").val()
                || window.OneStep.$("#shipping\\:country_id").find("option:selected").val()
            ){
                initLoad.ship = true;
            }else if(window.OneStep.$('input[name="payment[method]"]:checked').val()){
                initLoad.pay = true;
            }

            if(initLoad.both){
                this.update({updatepaymentmethod: true, updateshippingmethod: true, updateshippingtype: true});
            }else if(initLoad.ship){
                this.update({updatepaymentmethod: false, updateshippingmethod: true, updateshippingtype: true});
            }else if(initLoad.pay){
                this.update({updatepaymentmethod: true, updateshippingmethod: false});
            }

            // check validVAT onload
            if(onestepConfig.validVAT)
            {
                var countryCode = window.OneStep.$("#billing\\:country_id").find("option:selected").val();
                var vatNumber = window.OneStep.$("#billing\\:taxvat").val();
                this.checkVAT(countryCode, vatNumber);
            }
        },
        initCountry : function(){
            var value_id = window.onestepConfig.defaultCountry;
            var current_country = localStorage.getItem('co-billing-form_billing:country_id');

            if(!current_country){
                view_onestep_load_session_form.replaceItem(osc_block_loader.updatebillingaddress, "billing:country_id",value_id);
                view_onestep_load_session_form.replaceItem(osc_block_loader.updateshippingaddress, "shipping:country_id",value_id);
            }
        },
        hdlSubscribeNewletter: function(ev){
            window.OneStep.$(ev.target).val(window.OneStep.$(ev.target).is(':checked') ? 1 : 0);
        },
        initShipLoad: function(session_id){
            view_onestep_loader.insideBox({el: window.OneStep.$("#checkout-shipping-method-loadding"), session_id: session_id});
            return {
                type    :   "POST",
                url     :   onestepConfig.url.updShippingMethod,
                data    :   "shipping_method=" + window.OneStep.$('input[name=shipping_method]:checked').val(),
                success: function(msg){
                    view_onestep_loader.insideBox({el: "#loader_"+session_id, action: 'hide'});
                    msg = msg.replace("[]","");
                    msg = msg.replace("null","");
                    window.OneStep.$('#checkout-review-load').html(msg);
                }
            };
        },
        initPayLoad: function(session_id, value){
            view_onestep_loader.insideBox({el: window.OneStep.$("#checkout-payment-method-load"), session_id: session_id});
            return {
                type: "POST",
                url: onestepConfig.url.updPaymentMethod,
                data: "payment%5Bmethod%5D="+value+"&ajax=true",
                success: function(msg){
                    view_onestep_loader.insideBox({el: "#loader_"+session_id, action: 'hide'});
                    window.OneStep.$('#checkout-review-load').html(msg);
                }
            };
        },
        checkVAT: function(cc, vn){
            if(window.OneStep.$("#mw_osc_vlvat_text_error")){
                window.OneStep.$("#mw_osc_vlvat_text_error").remove();
            }
            if(window.OneStep.$("#mw_osc_vlvat_text")){
                window.OneStep.$("#mw_osc_vlvat_text").remove();
            }
            var flsg = 0;
            var european_union_countries = ['AT', 'BE', 'BG', 'CY', 'CZ', 'DE', 'DK', 'EE', 'ES', 'FI', 'FR', 'GB', 'GR', 'HU', 'IE', 'IT', 'LT', 'LU', 'LV', 'MT', 'NL', 'PL', 'PT', 'RO', 'SE', 'SI', 'SK'];
            for(var i=0; i <european_union_countries.length; i++)
            {
                if(european_union_countries[i] == cc){
                    flsg = 1;
                    break;
                }
            }
            if(flsg == 1){
                verifiedVAT = true;
                var params = {checkvat:true, updatebillingaddress: false};
                params.addition_post = {countrycode: cc, vatnumber: vn};
                this.update(params);
            }
        },
        afterValidatedVAT: function(data){
            var xml = data,
                xmlDoc = window.OneStep.$.parseXML(xml),
                $MW_Onestepcheckoutxml = window.OneStep.$(xmlDoc),
                $MW_Onestepcheckouttitle = $MW_Onestepcheckoutxml.find("valid");
            var result = $MW_Onestepcheckouttitle.text();
            if (window.OneStep.$("#billing:taxvat-error")) {
                window.OneStep.$("#billing:taxvat-error").remove();
            }

            if (result == "true") {
                window.OneStep.$('.taxvat input').after('<div for="billing\\:taxvat" generated="true" class="mage-error" id="billing:taxvat-error">'+$.mage.__("Verified")+'</div>');
            } else {
                window.OneStep.$('.taxvat input').after('<div for="billing\\:taxvat" generated="true" class="mage-error" id="billing:taxvat-error">'+$.mage.__("Not Verified")+'</div>');
            }
            window.OneStep.$('.btn-checkout').removeAttr('disabled');
        },
        validateVAT: function(cc, vn){
            var view = this;
            if(window.OneStep.$.trim(vn)!="")
            {
                var res = "";
                window.OneStep.$('.btn-checkout').attr('disabled','disabled');
                window.OneStep.$.ajax({
                    url: onestepConfig.url.mw_baseUrl + 'onestepcheckout/index/getvat/countrycode/' + cc + '/vatnumber/' + vn,
                    type: 'GET',
                    success: function(data){
                        view.afterValidatedVAT(data);
                    },
                    error: function()
                    {
                        if(window.OneStep.$("#mw_osc_vlvat_text_error")) {
                            window.OneStep.$("#mw_osc_vlvat_text_error").remove();
                        }
                        if(window.OneStep.$("#mw_osc_vlvat_text")) {
                            window.OneStep.$("#mw_osc_vlvat_text").remove();
                        }
                        window.OneStep.$("#osc_billing_vat_id label").append(" <b id='mw_osc_vlvat_text_error'> (<span style='color:red; font-weight:bold' > "+$.mage.__("Not Verified")+" </span>)</b>");
                        window.OneStep.$("#osc_shipping_vat_id label").append(" <b id='mw_osc_vlvat_text_error'> (<span style='color:red; font-weight:bold' > "+$.mage.__("Not Verified")+" </span>)</b>");
                        window.OneStep.$('#loading-mask').css('display','none');
                        window.OneStep.$('.btn-checkout').removeAttr('disabled');
                    }
                });
            }
        },
        /** All methods of click*/
        hdlBtnCheckout: function(ev){
            //	First validate the form
            window.OneStep.$('#co-payment-form').show();
            var form    = $('#onestep_form');
            var logic   = true;
            var countError = 0;
            if(onestepConfig.onlyProductDownloadable){
                var notshipmethod = 1 ;
            }

            //check DDate Info
            /*if (document.getElementById('ddate:date') != null) {
                if (document.getElementById('ddate:date').value=='') {
                    if (window.OneStep.$('#advice-required-entry_delivery').length == 0) {
                        window.OneStep.$('#co-ddate').append('<dt id="ddate-osc"><div class="validation-advice" id="advice-required-entry_delivery" style="">'+$.mage.__('Please select a delivery time!')+'</div></dt>');
                    }
                    countError++;
                } else {
                    if (window.OneStep.$('#ddate-osc').length != 0) {
                        document.getElementById('co-ddate').removeChild(document.getElementById('ddate-osc'));
                    }
                }
            }*/

            /*if (window.OneStep.$('input[name="payment[method]"]').filter(':checked').length == 0) {
                if (!window.OneStep.$('#advice-required-entry_payment').length) {
                    window.OneStep.$('#checkout-payment-method-load').append('<dt><div class="validation-advice" id="advice-required-entry_payment" style="">'+$.mage.__('Please select a payment method for your order!')+'</div></dt>');
                }
                countError++;
            } else {
                window.OneStep.$('#advice-required-entry_payment').remove();
            }
*/

            if (false && window.OneStep.$('input[name=shipping_method]').filter(':checked').length == 0) {
                if (!window.OneStep.$('#advice-required-entry_shipping').length) {
                    window.OneStep.$('#checkout-shipping-method-loadding').append('<dt><div class="validation-advice" id="advice-required-entry_shipping" style="">'+$.mage.__('Please select a shipping method for your order!')+'</div></dt>');
                    window.OneStep.$('#group-select').append('<dt><div class="validation-advice" id="advice-required-entry-delivery" style="">'+$.mage.__('Please select delivery time!')+'</div></dt>');
                }
                if (window.OneStep.$('input[name=shipping_method]').length == 0) {
                    if (!window.OneStep.$('#advice-required-entry_shipping').length) {
                        window.OneStep.$('#checkout-shipping-method-load').append('<dt><div class="validation-advice" id="advice-required-entry_shipping" style="">'+$.mage.__('Your order cannot be completed at this time as there is no shipping methods available for it. Please make necessary changes in your shipping address.')+'</div></dt>');
                        countError++;
                    }
                }
                countError++;
            } else {
                window.OneStep.$('#advice-required-entry_shipping').remove();
            }

            if ((form.validation() && !form.validation('isValid')) || countError > 0) {
                return false;
            } else {
                if (!isLogged) {
                    var msgerror = 1;
                    var val = window.OneStep.$('#billing\\:email').val();
                    var emailvalidated = /^([a-z0-9,!\#\$%&'\*\+\/=\?\^_`\{\|\}~-]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+(\.([a-z0-9,!\#\$%&'\*\+\/=\?\^_`\{\|\}~-]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+)*@([a-z0-9-]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+(\.([a-z0-9-]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+)*\.(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]){2,})$/i.test(val);
                    //validate email
                    /*if(val!="" && emailvalidated){
                        msgerror = this.updateEmailmsg(val);
                    }
                    if(msgerror == 0){
                        return false;
                    }*/
                }

                // check vat for eu
                var isvat = 1;
                if(onestepConfig.validVAT){
                    if(window.OneStep.$("#mw_osc_vlvat_text_error").length > 0){
                        isvat = 0;
                    }
                }
                if(isvat == 0){
                    alert({
                        content: $.mage.__("TaxVat Number is not verified")
                    });
                    return false;
                }

                if(logic){
                    this.updateOrder();
                    return false;
                }
                else {
                    return false;
                }
            }
            return false;
        },
        /** [END] methods */
        updateEmailmsg: function(val){
            var view = this;
            view.$el.find("#review-please-wait").show();
            window.OneStep.$('#message-box').html('');
            window.OneStep.$('.btn-checkout').attr('disabled','disabled');
            var asyncdata;
            window.OneStep.$.ajax({
                async   : false,
                type    : "POST",
                url     : onestepConfig.url.updateEmailMsg,
                data    : "email=" + val,
                success: function(msg){
                    view.$el.find("#review-please-wait").hide();

                    var error = '<div class="message-error error message"><div>'+$.mage.__("There is already a customer registered using this email address. Please login using this email address or enter a different email address.")+'</div></div>';
                    window.OneStep.$('.btn-checkout').removeAttr('disabled');
                    if(msg == 0){
                        window.OneStep.$('#message-box').html(error);
                        window.OneStep.$('html, body').animate({
                            scrollTop: (window.OneStep.$('#message-box').offset().top-100)
                        }, 500);
                        window.OneStep.$('#billing\\:email').addClass('validation-failed');

                        window.OneStep.$('.btn-checkout').removeAttr('disabled');
                        asyncdata = '0';
                    }
                    else{
                        if(!isLogged){
                            window.OneStep.$('#message-box').html('');
                        }
                        window.OneStep.$('#billing\\:email').removeClass('validation-failed');
                        asyncdata = '1';
                    }
                }
            });
            return asyncdata;
        },
        updateOrder: function(){
            var view = this;

            if(window.OneStep.$('input[name="payment[method]"]:checked').val() == "sagepaydirectpro" 
                || window.OneStep.$('input[name="payment[method]"]:checked').val() == "sagepayform" 
                || window.OneStep.$('input[name="payment[method]"]:checked').val() == 'sagepayserver'
            ) {
                view_onestep_plugin_sagepay_server.init(view);
                return false;
            } else if(window.OneStep.$('input[name="payment[method]"]:checked').val() == "braintree") {
                view_onestep_plugin_braintree.processPayment(view);
                return false;
            }

            this.placeOrder();
        },
        placeOrder: function() {
            var view = this;
            var urlPost = onestepConfig.url.updOrderMethod;
            this.$el.find('.btn-checkout').attr('disabled','disabled').css("opacity", 0.3);
            this.$el.find("#review-please-wait").show();
            window.OneStep.$.ajax({
                type    : "POST",
                url     : urlPost,
                data    : window.OneStep.$("#onestep_form").serialize(),
                dataType: "json",
                success: function(data){
                    if(!data || typeof data.error =='undefined'){
                        view.$el.find("#review-please-wait").hide();
                        view.$el.find('.btn-checkout').removeAttr('disabled').css("opacity", 1);
                        alert({
                            content: 'Incorrect response. Please try again.'
                        });
                        return;
                    }
                    if(data.error == 0){
                        if(data.msg.search("hosted_pro") != -1 || data.msg.search("payflow_link") != -1 || data.msg.search("payflow_advanced") != -1){
                            this.saveOrder();
                        }else{
                            if(window.OneStep.$.trim(data.msg) != ""){
                                alert({
                                    content: $.mage.__(data.msg)
                                });
                            }
                        }
                    }else{
                        view.$el.find("#review-please-wait").hide();
                        view.$el.find('.btn-checkout').removeAttr('disabled').css("opacity", 1);
                        alert({
                            content: $.mage.__(data.msg)
                        });
                    }
                    if(typeof data.redirect != 'undefined'){
                        if (typeof data.directpost != 'undefined') {
                            // Add card information for gateway
                            data.directpost.x_card_code = window.OneStep.$('#authorizenet_directpost_cc_cid').val();
                            data.directpost.x_card_num = window.OneStep.$('#authorizenet_directpost_cc_number').val();
                            exp_month = window.OneStep.$('#authorizenet_directpost_expiration').val();
                            exp_month = exp_month.trim();
                            if (exp_month < 10) {
                                exp_month = '0' + exp_month;
                            }
                            exp_year = window.OneStep.$('#authorizenet_directpost_expiration_yr').val();
                            exp_year = exp_year.trim();
                            exp_year = exp_year.substring(exp_year.length - 2);
                            data.directpost.x_exp_date = exp_month + '/' + exp_year;
                            hiddenFormTmpl =
                                '<form target="authorize-iframe" action="<%= data.action %>" method="POST" hidden enctype="application/x-www-form-urlencoded" class="no-display">' +
                                    '<% _.each(data.inputs, function(val, key){ %>' +
                                    '<input value="<%= val %>" name="<%= key %>" type="hidden">' +
                                    '<% }); %>' +
                                '</form>';
                            hiddenFormGateway = mageTemplate(hiddenFormTmpl);
                            tmpl = hiddenFormGateway({
                                data: {
                                    action: window.checkoutConfig.payment.iframe.cgiUrl.authorizenet_directpost,
                                    inputs: data.directpost
                                }
                            });
                            window.OneStep.$(tmpl).appendTo(window.OneStep.$('.mw-osc-checkoutcontainer')).submit();
                            $.initNamespaceStorage('mage-cache-storage').localStorage.removeAll();
                            window.redirectSuccess = data.redirect;
                        } else {
                            $.initNamespaceStorage('mage-cache-storage').localStorage.removeAll();
                            window.location = data.redirect;
                        }
                    }
                },
                error:function(data){
                    view.$el.find('.btn-checkout').attr('disabled','disabled').css("opacity", 0.3);
                    view.$el.find("#review-please-wait").show();
                    alert({
                        content: $.mage.__(data)
                    });
                }
            });
        },
        saveOrder: function(){
            window.OneStep.$('#message-box').html('');
            window.OneStep.$.ajax({
                type    : "POST",
                url     : onestepConfig.url.saveOrder,  /** ?!?!? Not exist method in controller, who support */
                data    : window.OneStep.$("#onestep_form").serialize(),
                success: function(msg){
                    var str = window.OneStep.$.parseJSON(msg);

                    if(str.update_section !== undefined){
                        window.OneStep.$("#checkout-paypaliframe-load").html(str.update_section.html);
                    }

                    if(str.error_messages !== undefined){
                        alert({
                            content: $.mage.__(str.error_messages)
                        });
                        window.OneStep.$('#checkout-review-submit').show();
                    }
                },
                error:function(msg){
                    alert({
                        content: $.mage.__("error")
                    });
                }
            });
        },
        ajax: function(){
            window.OneStep.$.when.apply(window.OneStep.$, window.requestAjax).done(function(){
                window.OneStep.$.each(arguments, function(k, v){
                    delete window.requestAjax[k];
                });
            });
        },
        update: function(params){
            var data = window.OneStep.$("#onestep_form").serializeArray();
            var updates = [];
            var view = this;
            params = this.filterParams(params);
            window.OneStep.$.each(params, function(k, v){
                updates.push({name: k, value: v});
            });
            data.push({name: 'updates', value: JSON.stringify(params)});
            if(typeof params.addition_post != 'undefined'){
                window.OneStep.$.each(params.addition_post, function(k, v){
                    data.push({name: k, value: v});
                });
            }
            data.push({name: 'isAjax', value: 1});

            view_onestep_loader.multiBox({boxes: params.loader});
            window.OneStep.$('.btn-checkout').attr('disabled','disabled');
            if(window.updateOnestep != null){
                /** Abort all requesting */
                window.updateOnestep.abort();
            }
            view_onestep_load_session_form.bindAll();
            window.updateOnestep = window.OneStep.$.ajax({
                type    :   "POST",
                url     :   onestepConfig.url.save,
                data    :   data,
                dataType:   "json",
                success :   function(data){
                    window.updateOnestep = null;
                    view_onestep_loader.multiBox({boxes: params.loader, action: "hide"});
                    if (typeof data.empty_cart != 'undefined') {
                        location.href = onestepConfig.url.mw_baseUrl + "checkout/cart";
                        return false;
                    }
                    if(typeof data.items != 'undefined'){
                        if(data.items <= 0){
                            location.href = onestepConfig.url.mw_baseUrl + "checkout/cart";
                            return false;
                        }
                    }
                    if(typeof data.totals != 'undefined'){
                        window.OneStep.$("#"+osc_block_loader.updatecart).find("tfoot").html(data.totals);
                        window.OneStep.$("#"+osc_block_loader.updatecart).find("tfoot").append(data.totals_footer);
                        window.OneStep.$("#"+osc_block_loader.updatecart).find("tfoot").find(".first").after(data.earn_points);
                    }
                    if(typeof data.billing != 'undefined' && data.billing != ""){
                        window.OneStep.$("#mw_onstepcheckout_billing_form").html(data.billing);
                        $(mage.apply);
                    }
                    if(typeof data.shipping != 'undefined' && data.shipping != ""){
                        window.OneStep.$("#mw_onstepcheckout_shipping_form").html(data.shipping);
                        $(mage.apply);
                    }
                    if(typeof data.shipping_method != 'undefined' && data.shipping_method != ""){
                        window.OneStep.$("#"+osc_block_loader.updateshippingmethod).html(data.shipping_method);
                        view.resetRadio({el: "#"+osc_block_loader.updateshippingmethod});
                    }
                    if(typeof data.payment_method != 'undefined' && data.payment_method != ""){
                        window.OneStep.$("#"+osc_block_loader.updatepaymentmethod).html(data.payment_method);
                        view.resetRadio({el: "#"+osc_block_loader.updatepaymentmethod});
                    }
                    if(typeof data.coupon != 'undefined' && data.coupon != ""){
                        window.OneStep.$("#"+osc_block_loader.updatecoupon).html(data.coupon.html);
                        view_onestep_load_session_form.restyleColor();
                        window.OneStep.$("#mw-coupon-message").html(data.coupon.msg);
                    }
                    if(typeof data.review_info != 'undefined' && data.review_info != ""){
                        window.OneStep.$("#"+osc_block_loader.updatecart).html(data.review_info);
                    }
                    if(typeof data.vat != 'undefined' && data.vat != ""){
                        view.afterValidatedVAT(data.vat);
                    }
                    if(typeof data.cart != 'undefined' && data.cart.error == 0) {
                        alert({
                            content: $.mage.__(data.cart.msg)
                        });
                        view_onestep_load_session_form.bindAll();
                        return false;
                    }
                    window.OneStep.$('.btn-checkout').removeAttr('disabled');
                    view_onestep_load_session_form.bindAll();
                },
                error   :   function(arg1,arg2,arg3){
                    window.updateOnestep = null;
                    if(arg2!=='abort'){
                        alert({
                            content: $.mage.__('Error while updating quote:') + arg3.message + ' Response:' + arg1.responseText
                        });
                        location.href = onestepConfig.url.mw_baseUrl + "checkout";
                    }
                    return false;
                }
            });
        },
        resetRadio: function(params){
            if(window.OneStep.$(params.el).find("input[type=radio]").length > 1){
                window.OneStep.$(params.el).find("input[type=radio]").each(function(){
                });
            }
        },
        filterParams: function(params){
            params.loader = [];
            window.OneStep.$.each(params, function(k, v){
                switch(k){
                    case 'updateshippingmethod':
                        if(v)
                            params.loader.push(osc_block_loader.updateshippingmethod);
                        else
                        /*delete params[k];*/
                            break;
                    case 'updatepaymentmethod':
                        if(v)
                            params.loader.push(osc_block_loader.updatepaymentmethod);
                        else
                            delete params[k];
                        break;
                    case 'updateshippingtype':
                        if(v)
                            params.loader.push(osc_block_loader.updateshippingtype);
                        else
                            delete params[k];
                        break;
                    case 'updatepaymenttype':
                        if(v)
                            params.loader.push(osc_block_loader.updatepaymenttype);
                        else
                            delete params[k];
                        break;
                    case 'updateshippingaddress':
                    case 'updateshippingform':
                        if(v)
                            params.loader.push(osc_block_loader.updateshippingform);
                        else
                        /*delete params[k];*/
                            break;
                    case 'updatebillingaddress':
                    case 'updatebillingform':
                        if(v)
                            params.loader.push(osc_block_loader.updatebillingform);
                        else
                        /*delete params[k];*/
                            break;
                    case 'checkvat':
                        if(v == false)
                            delete params[k];
                        break;
                };
            });
            params.loader.push(osc_block_loader.updatecart);
            return params;
        }
    });

    OneStep.Views.Loader            = Backbone.View.extend({
        initialize:function(params){

        },
        renderParam: function(params){
            params.el           = (typeof params.el == 'undefined') ? '' : params.el;
            params.boxes        = (typeof params.boxes == 'undefined') ? '' : params.boxes;
            params.session_id   = (typeof params.el == 'undefined') ? '' : params.session_id;
            params.show_text    = (typeof params.show_text == 'undefined') ? false : params.show_text
            params.size         = (typeof params.size == 'undefined') ? 20 : params.size
            params.color        = (typeof params.color == 'undefined') ? onestepConfig.styleColor : params.color;
            return params;
        },
        multiBox: function(params){
            var params = this.renderParam(params);
            this.hide(params);
            if(params.action == 'hide'){
                this.hide(params);
                return;
            }
            var view = this;
            window.OneStep.$.each(params.boxes, function(k, v){
                params.el = window.OneStep.$("#"+v);
                params.session_id = v;
                params.type = 'inside';
                view.show(params);
            });
        },
        insideBox: function(params){
            var params = this.renderParam(params);
            if(params.el == ""){
                console.log("No element to selector.");
                return false;
            }
            if(params.action == 'hide'){
                this.hide(params);
                return;
            }
            params.type = "inside";
            this.show(params);
        },
        show: function(params){
            var parent_box;
            switch(params.type){
                case 'inside':
                    parent_box = params.el.closest(".mw-osc-block-content");
                    parent_box.find(".loader").remove();
                    parent_box.prepend("<div class='loader' id='loader_"+params.session_id+"'></div>");
                    break;
            }
            var nodeLoader = parent_box.find("#loader_"+params.session_id);
            if (!nodeLoader.lenght){
                return;
            }
            var cl = new CanvasLoader('loader_'+params.session_id);
            cl.setColor('#'+params.color); // default is '#000000'
            cl.setDiameter(params.size); // default is 40 (size)
            cl.setDensity(55); // default is 40
            cl.setRange(0.9); // default is 1.3
            cl.setFPS(34); // default is 24
            cl.show(); // Hidden by default
            if(params.show_text){
                window.OneStep.$("#"+params.el).append("<div style='text-align: center; color: #757373;'>"+$.mage.__("Please wait..")+".</div>");
            }
        },
        hide: function(params){
            if(params.boxes != ""){
                var view = this;
                window.OneStep.$("div[id*=loader]").each(function(k, v){
                    window.OneStep.$(this).remove();
                });
            }else{
                window.OneStep.$(params.el).remove();
            }
        }
    });

    OneStep.Views.DeliveryDate      = Backbone.View.extend({
        el: window.OneStep.$("#delivery_date_load"),
        events: {
            "click input[name=deliverydate]"    :   "hdlType",
            "change #delivery-timerange"        :   "hdlTimerange",
            "change #onestepcheckout_time"      :   "hdlTime",
            "change #onestepcheckout_date"      :   "hdlDate"
        },
        initialize: function(){
            this.delivery_changed = 'now';
            var view = this;
            if(!onestepConfig.delivery.asaOption){
                if(window.OneStep.$("input[name=deliverydate]").val() != this.delivery_changed){
                    window.OneStep.$('#delivery-timerange').addClass('validate-select');
                    window.OneStep.$('#deliveryshow').css('display','block');
                }
            }

            var weekendDays = onestepConfig.delivery.weekendDays;
            this.weekendDays = weekendDays.split(","); //is weekend with 0=sunday,1=monday,....,6=saturday;
            this.weekendDays = window.OneStep.$.grep(this.weekendDays,function(n){ return(n) });

            var disabledDays = onestepConfig.delivery.disabledDays;
            this.disabledDays = disabledDays.split(",");
            this.disabledDays = window.OneStep.$.grep(this.disabledDays,function(n){ return(n) });

            var enableDays = onestepConfig.delivery.enableDays;
            this.enableDays = enableDays.split(",");
            this.enableDays = window.OneStep.$.grep(this.enableDays,function(n){ return(n) });

            this.formatDate = onestepConfig.delivery.formatDate;

            this.formatDatePicker = '';
            if(this.formatDate == 'd/m/Y'){
                this.formatDatePicker = 'dd/mm/yy';
            }else if(this.formatDate == 'm/d/Y'){
                this.formatDatePicker = 'mm/dd/yy';
            }
            this.isNowDay = onestepConfig.delivery.isNowDay;
            this.isNowTime = onestepConfig.delivery.isNowTime;
            this.timeRange = new Array();//save time range when select time ranger
            this.setTimer = new Array();//save time valid when select time ranger and ajax process

            this.option = {
                showAnim: 'fadeIn',
                duration:'fast',
                showOn: 'button',
                buttonImage: onestepConfig.delivery.buttonImage,
                minDate:'-0d',
                buttonImageOnly: true,
                dateFormat: this.formatDatePicker,
                beforeShowDay: function(date){
                    return view.noWeekendsOrHolidays(date);
                }
            };

            if(onestepConfig.delivery.rangeDay){
                this.option.maxDate = onestepConfig.delivery.rangeDay;
            }
            this.syncTime = new Date();
            var syncDate = new Date();
            this.daySelected = this.convertDate((syncDate.getMonth() + 1), syncDate.getDate(), syncDate.getFullYear());

            window.OneStep.$("#onestepcheckout_date").attr("value", this.daySelected);
            this.datePicker = window.OneStep.$('#onestepcheckout_date').datepicker(this.option);

            this.editTimepicker();
        },
        hdlTimerange: function(ev){
            var strtime = window.OneStep.$(ev.target).val();
            if(strtime)
                this.timeRange = strtime.split("-");
            else
                this.timeRange = new Array();
            this.updateTimepicker();
        },
        hdlType: function(ev){
            var val = window.OneStep.$(ev.target).filter(':checked').val();
            if(val != this.delivery_changed){
                window.OneStep.$('#delivery-timerange').addClass('validate-select');
                window.OneStep.$('#deliveryshow').css('display','block');
            } else{
                window.OneStep.$('#delivery-timerange').removeClass('validate-select');
                window.OneStep.$('#deliveryshow').css('display','none');
            }
        },
        notWeekends: function(date){		//true if not weekend
            var view = this;
            if(view.weekendDays.length > 0){
                var isWeekend = date.getDay();
                for (i = 0; i < view.weekendDays.length; i++) {
                    if(window.OneStep.$.inArray(isWeekend+"", view.weekendDays) !=-1){
                        return false;	//false if is weekend
                    }
                }
            }

            return true;	//true if not weekend
        },
        nationalDays: function(date){
            var view = this;
            if(view.disabledDays.length > 0){
                var m = date.getMonth(), d = date.getDate(), y = date.getFullYear();
                for (i = 0; i < view.disabledDays.length; i++) {
                    if(view.convertDate(m+1, d, y) == view.disabledDays[i]){
                        return [false];
                    }
                }
            }

            return [true];
        },
        additionalDays: function(date){
            var view = this;
            if(view.enableDays.length > 0){
                var m = date.getMonth(), d = date.getDate(), y = date.getFullYear();
                if(view.enableDays.length > 0){
                    for (i = 0; i < view.enableDays.length; i++) {
                        if(view.convertDate(m+1, d, y) == view.enableDays[i]){
                            return [true];
                        }
                    }
                }
            }

            return [false];
        },

        noWeekendsOrHolidays: function(date){
            var notWeekend = this.notWeekends(date);
            return notWeekend ? this.nationalDays(date) : this.additionalDays(date);
        },
        getAmPm: function(d){
            if ( d.getHours() > 11 ) {
                return "PM"
            } else {
                return "AM"
            }
        },
        convertDate: function(m, d, y){
            m = (m > 9)? m: '0' + m;
            d = (d > 9)? d: '0' + d;
            if(this.formatDate == 'm/d/Y'){
                return m + "/" + d + "/" + y;
            }
            else if(this.formatDate = 'd/m/Y'){
                return d + "/" + m + "/" + y;
            }
        },
        countHtom: function(chour){
            var splithour = chour.split(":");
            return parseFloat(splithour[0])*60 + parseFloat(splithour[1]);
        },
        cTime: function(formattime, time){
            time = window.OneStep.$.trim(time);
            if(formattime){
                var timer=time.split(":");
                return (parseFloat(timer[0]/12)<1)?timer[0]+":"+timer[1]+" am":((timer[0]%12)==0)?timer[0]+":"+timer[1]+" pm":(timer[0]%12)+":"+timer[1]+" pm";
            }
            else{
                var splitampm =time.split(" ");
                var splittimer = splitampm[0].split(":");
                if(splitampm[1]=="am" || splitampm[1]=="a" || splitampm[1]==null){
                    if(splittimer[0]=="12")
                        return "00:"+splittimer[1];
                    else
                        return splittimer[0]+":"+splittimer[1];
                }
                else{
                    if(splittimer[0]=="12")
                        return "12:"+splittimer[1];
                    else
                        return (parseFloat(splittimer[0])+12)+":"+splittimer[1];
                }
            }
        },
        hdlDate: function(ev){
            this.daySelected = window.OneStep.$(ev.target).val();
            window.OneStep.$('#date').html(this.daySelected);
            this.updateTimepicker();
        },
        hdlTime: function(){
            var view = this;
            if(window.OneStep.$("#onestepcheckout_date").val() != onestepConfig.delivery.isNowDay){
                if(view.setTimer.length){
                    var countmin = view.countHtom(view.cTime(false, window.OneStep.$('#onestepcheckout_time').val()));
                    var timestart = view.setTimer[0]+":"+view.setTimer[1];

                    if(view.countHtom(timestart)> countmin)
                        window.OneStep.$('#clock').html(view.cTime(true,timestart));
                    else {
                        if(view.countHtom(view.timeRange[1])< countmin)
                            window.OneStep.$('#clock').html(view.cTime(true,view.timeRange[1]));
                        else
                            window.OneStep.$('#clock').html(window.OneStep.$('#onestepcheckout_time').val());
                    }
                }
                else{
                    window.OneStep.$('#clock').html(window.OneStep.$('#onestepcheckout_time').val());
                }
            }
            else{
                var countmin  = view.countHtom(view.cTime(false, window.OneStep.$('#onestepcheckout_time').val()));
                var timestart = onestepConfig.delivery.isNowTime
                if(view.countHtom(timestart)> countmin)
                    window.OneStep.$('#clock').html(view.cTime(true, timestart));
                else
                    window.OneStep.$('#clock').html(window.OneStep.$('#onestepcheckout_time').val());
            }
        },
        editTimepicker: function(isTimeRange){
            var view = this;
            window.OneStep.$('#date').html(window.OneStep.$('#onestepcheckout_date').val());
            if(isTimeRange){
                var hourtime = (parseFloat(this.setTimer[0]/12) < 1)?this.setTimer[0] + ":" + this.setTimer[1] + " am":(this.setTimer[0]%12) + ":" + this.setTimer[1]+" pm";
                var htmlstr = "<div class='blockdate'><span>Time: </span><span id='clock'>"+hourtime+"</span></div>";
                htmlstr += "<input type='hidden' name='onestepcheckout_time' id='onestepcheckout_time' value='"+hourtime+"'/>";

            }
            else{
                var curTime = this.syncTime.getHours()+":"+this.syncTime.getMinutes()+" "+this.getAmPm(this.syncTime);
                var htmlstr = "<div class='blockdate'><span>Time: </span><span id='clock'>"+curTime+"</span></div>";
                htmlstr +="<input type='hidden' name='onestepcheckout_time' id='onestepcheckout_time' value='"+curTime+"'/>";

            }
            window.OneStep.$('#changedate').html(htmlstr);

            var path_clock = onestepConfig.delivery.clockImagePNG;
            // Check IE 6 - Fix for $.browser
            if((/MSIE (\d+\.\d+);/.test(navigator.userAgent)) ? (Number(RegExp.$1) == 6 ? true : false) : false){
                path_clock = onestepConfig.delivery.clockImageGIF;
            }

            if(window.OneStep.$("#onestepcheckout_date").val() == onestepConfig.delivery.isNowDay){
                window.OneStep.$('#onestepcheckout_time').timepicker({
                    showAnim: 'fadeIn',
                    duration:'fast',
                    showOn: 'button',
                    ampm: true,
                    minute: view.syncTime.getMinutes(),
                    hourMin: view.syncTime.getHours(),
                    buttonImage: path_clock,
                    buttonImageOnly: true
                });
            }
            else{
                window.OneStep.$('#onestepcheckout_time').timepicker({
                    showAnim: 'fadeIn',
                    duration:'fast',
                    showOn: 'button',
                    ampm: true,
                    minute: view.syncTime.getMinutes(),
                    hour: view.syncTime.getHours(),
                    buttonImage: path_clock,
                    buttonImageOnly: true
                });
            }
        },
        updateTimepicker: function(){
            var view = this;
            if(this.timeRange.length != 0){
                var isday = (window.OneStep.$('#onestepcheckout_date').val() == onestepConfig.delivery.isNowDay) ? 1 : 0;
                window.OneStep.$.ajax({
                    type    : "POST",
                    url     : onestepConfig.url.udpateTimepicker,
                    data    : "stime="+view.timeRange[0]+"&"+"etime="+view.timeRange[1]+"&"+"now="+isday,
                    success: function(msg){
                        if(msg){
                            view.setTimer = msg.split(":");
                            view.editTimepicker(true);
                        } else{
                            window.OneStep.$('#changedate').html("<span style='width:104px;margin-right:35px;'>"+$.mage.__("time off this day")+"</span>");
                        }
                    }
                });
            }
            else{
                view.editTimepicker(false);
            }
        }
    });

    OneStep.Views.User              = Backbone.View.extend({
        events: {
            "click .btn-login"      :   "hdlLogin",
            "click #btforgotpass"   :   "hdlForgotpass",
            "click #login_fb"       :   "loginViaFb"
        },
        initialize: function(params){
            this.initFB();
            if(params.form == 'login'){
                this.loginForm = $('#mw-login-form');
            }else if(params.form == 'forgot'){
                this.dataForm = $('#form-validate');
            }
            this.setElement(params.el);
        },
        loginViaFb : function(){
            var FbAppId = window.onestepConfig.FbAppId;
            var _self = this;
            (function(d, s, id) {
                var js, fjs = d.getElementsByTagName(s)[0];
                if (d.getElementById(id)) return;
                js = d.createElement(s); js.id = id;
                js.src = "//connect.facebook.net/en_US/sdk.js";
                fjs.parentNode.insertBefore(js, fjs);
            }(document, 'script', 'facebook-jssdk'));
            FB.init({
                appId      : FbAppId,
                cookie     : false,  // enable cookies to allow the server to access
                // the session
                xfbml      : true,  // parse social plugins on this page
                version    : 'v2.5' // use version 2.1
            });

            FB.login(function(response) {
                if (response.authResponse) {
                    FB.api('/me?fields=email,first_name,last_name', function(response) {
                        _self.successLogin(response);
                    });
                } else {
                    console.log('User cancelled login or did not fully authorize.');
                }
            }, {scope: 'email', return_scopes: true});
        },
        initFB : function(){
            (function(d, s, id) {
                var js, fjs = d.getElementsByTagName(s)[0];
                if (d.getElementById(id)) return;
                js = d.createElement(s); js.id = id;
                js.src = "//connect.facebook.net/en_US/sdk.js";
                fjs.parentNode.insertBefore(js, fjs);
            }(document, 'script', 'facebook-jssdk'));
        },
        successLogin :function(response) {
            // Fetch the data information
            window.OneStep.$.fancybox.close();
            window.OneStep.$('#billing\\:firstname').val(response.first_name);
            window.OneStep.$('#billing\\:lastname').val(response.last_name);
            window.OneStep.$('#billing\\:email').val(response.email);
            window.OneStep.$('#loginbox').css({'display':'none'});
        },
        statusChangeCallback :function (response) {
            // The response object is returned with a status field that lets the
            // app know the current login status of the person.
            // Full docs on the response object can be found in the documentation
            // for FB.getLoginStatus().
            if (response.status === 'connected') {
                // Logged into your app and Facebook.
                this.successLogin();
            } else if (response.status === 'not_authorized') {
                // The person is logged into Facebook, but not your app.
                console.log('Please log into this app');
                //document.getElementById('status').innerHTML = 'Please log ' +
                // 'into this app.';
            } else {
                // The person is not logged into Facebook, so we're not sure if
                // they are logged into this app or not.
                console.log('please log into facebook');
                //document.getElementById('status').innerHTML = 'Please log ' +
                //  'into Facebook.';
            }
        },
        checkLoginState : function() {
            var _self = this;
            FB.getLoginStatus(function(response) {
                _self.statusChangeCallback(response);
            });
        },
        hdlForgotpass: function(ev){
            if(this.dataForm.validation() && this.dataForm.validation('isValid')){
                this.submitForgot(this.$el.find('#email_address').val());
            }
            this.$el.find('#errorforgotpass').css('display','none');
        },
        hdlLogin: function(ev){
            if(this.loginForm.validation() && this.loginForm.validation('isValid')){
                this.submitLogin(this.$el.find('#mw-login-email').val(), this.$el.find('#mw-login-password').val());
            }
            this.$el.find('#errorlogin').css('display','none');
        },
        submitForgot: function(email){
            var view = this;
            this.$el.find('#btforgotpass').css('display','none');
            this.$el.find('#forgotpass-please-wait').css('display','block');
            window.OneStep.$.ajax({
                type    : "POST",
                url     : onestepConfig.url.forgotPass,
                data    : {email: email},
                success: function(msg){
                    if (msg == 1) {
                        window.OneStep.$('#inline3').html("<div class='mw-osc-title-login'><h1>"+$.mage.__("Password is sent successfully!")+'</h1></div><fieldset style="border:none"><p>'+$.mage.__('We have now sent you a new password to your email address. Click the link below to return to login.')+'</p><p class="back-link"><a id="backlogin1" href="#inline1" style="color:#1E7EC8;"><small>&laquo; </small>'+$.mage.__('Back to Login')+'</a></p></fieldset>');
                        var closeBtnTemplate = '<button id="fancybox-close" class="button" type="button"><span><span>'+$.mage.__('Close')+'</span></span></button>';
                        window.OneStep.$("#backlogin1").fancybox({
                            padding : 0,
                            tpl     : {
                                closeBtn: closeBtnTemplate
                            },
                            helpers : {
                                overlay: {
                                    locked: false
                                }
                            },
                        });
                    } else {
                        view.$el.find('#btforgotpass').css('display','block');
                        view.$el.find('#forgotpass-please-wait').css('display','none');
                        view.$el.find('#errorforgotpass').css('display','block');
                        view.$el.find('#email_address').addClass('validation-failed')
                    }
                }
            });
        },
        submitLogin: function(email, password){
            var view = this;
            this.$el.find('.buttons-set .button').css('display','none');
            this.$el.find('#mw-login-please-wait').css('display','block');
            window.OneStep.$.ajax({
                type    : "POST",
                url     : onestepConfig.url.updateLogin,
                data    : {
                    email: email,
                    password: password
                },
                success: function(msg){
                    if (msg == 0) {
                        view.$el.find('.buttons-set .button').css('display','block');
                        view.$el.find('#mw-login-please-wait').css('display','none');
                        view.$el.find('#errorlogin').css('display','block');
                        view.$el.find('#mw-login-email').addClass('validation-failed');
                        view.$el.find('#mw-login-password').addClass('validation-failed');
                    } else {
                        window.isCustomerLoggedIn = true;
                        window.location.reload();
                    }

                    return false;
                }
            });

            return false;
        }
    });

    OneStep.Views.LoadDataForm      = Backbone.View.extend({
        initialize: function(){
            this.checkIsWrap();
            this.reStylePayment();
            this.setPositionGiftWrap();
            this.restyleColor();
            this.reStyleField();
            this.reStyleCorner();
        },
        reStyleCorner : function(){
            var main_style = window.onestepConfig.styleLayout;
            var round_corner =  window.onestepConfig.round_corner;
            if(main_style == 1 || (main_style ==2 && round_corner ==1)){
                window.OneStep.$('#onestepcheckout_place_btn_id').corner('6px');
                window.OneStep.$('.mw-osc-block-title').each(function(ele){
                    window.OneStep.$(this).corner("top 6px");
                });
            }
        },
        reStyleField : function(){
            var height_field = window.OneStep.$('#billing\\:firstname').height() + 2;
            var main_style = window.onestepConfig.styleLayout;
            var my_package = window.OneStep.$('#my-package').val();
            if(main_style == 1 && my_package !='default' && my_package !='enterprise'  ){
                window.OneStep.$('#billing\\:gender').css({'height':height_field+'px','margin-top':'0px'});
                window.OneStep.$('#billing\\:country_id').css({'height':height_field+'px','margin-top':'0px'});
                window.OneStep.$('#billing\\:region_id').css({'height':height_field+'px','margin-top':'0px'});
            }

            var temp = window.OneStep.$('#edit-cart').height();
            if(!temp){
                window.OneStep.$('.mw-osc-giftmessagecontainer').css('margin','30px 0 12px');
                window.OneStep.$('#checkout-review-options').css('margin-top','30px');

            }
        },
        reStylePayment : function(){
            if(window.onestepConfig.pageLayout ==2){
                window.OneStep.$('.mw-osc-column-right').css('width','100%')
            }
        },
        restyleColor : function(){
            if(window.onestepConfig.styleColor ==''){
                var color = '#337BAA'
            }else{
                var color = '#'+window.onestepConfig.styleColor;
            }
            window.OneStep.$('.discount-input-text').css('color',color);
            window.OneStep.$('#button-btn-coupon-id').css('background',color);
            window.OneStep.$('#id-coupon-color').css('background',color);
            window.OneStep.$('#id-coupon-color-parent').css('background',color);
            window.OneStep.$('#button-btn-coupon-id').css('padding','6px 15px');
            var setting_style = window.onestepConfig.styleLayout;
            if(setting_style == 1){
                window.OneStep.$('#id_gift_wrap').css('left','0px');
            }else{
                window.OneStep.$('#id_gift_wrap').css('right','0px');
            }

            if (window.OneStep.$('.btn-coupon-cancel').length > 0) {
                window.OneStep.$('.btn-coupon-cancel').css('background',color);
                window.OneStep.$('.btn-coupon-cancel').css('padding','6px 15px');
            }
        },
        setPositionGiftWrap : function(){
            var height_coupon = window.OneStep.$('#tab-coupon').height();
            if(height_coupon ==0 || height_coupon == null){
                var temp = -27;
            }else{
                var temp = - 27 - height_coupon -20;
            }
            var bottom = temp+'px';
            window.OneStep.$('.onestepcheckout-gift-wrap').css('bottom',bottom);
        },
        checkIsWrap : function(){
            var temp = window.OneStep.$('#check_session').val();
            if(temp=='0'){
                window.OneStep.$('#id_gift_wrap').prop("checked",false);
            }else{
                window.OneStep.$('#id_gift_wrap').prop("checked",true);
            }
        },
        billing: function(){
            if(!isLogged){
                window.OneStep.$('#'+osc_block_loader.updatebillingaddress).autosave();
            }
        },
        shipping: function(){
            if(!isLogged){
                window.OneStep.$('#'+osc_block_loader.updateshippingaddress).autosave();
            }
        },
        payment: function(){
            window.OneStep.$('#'+osc_block_loader.updatepaymentmethod).autosave();
        },
        review: function(){
            window.OneStep.$('#checkout-review-options').autosave();
        },
        bindAll: function(){
            this.setPositionGiftWrap();
        },
        replaceItem: function(prefix, ele, value){
            var storage = window.localStorage,
                $this = this;
            prefix += "_";
            var prefix_key = ele;
            var key = prefix+ prefix_key;
            storage.removeItem(key);
            storage.setItem(key,value);
        }
    });

    OneStep.Plugins.SagepayServer           = Backbone.View.extend({
        init: function(parentView){

            this.parentView = parentView;
            window.SageServer = new MWSagePaySuite.Checkout({
                'checkout':  checkout,
                'review':    review,
                'payment':   payment,
                'billing':   billing,
                'accordion': accordion
            });
            OneStep.on("event:sagepay:server:before_post_to_gateway", this.beforePostToGateway);
            OneStep.on("event:sagepay:server:after_post_to_gateway", this.afterPostToGateway);
            SageServer.save();
        },
        beforePostToGateway: function(){
            view_onestep_plugin_sagepay_server.parentView.$el.find('.btn-checkout').attr('disabled','disabled').css("opacity", 0.3);
            view_onestep_plugin_sagepay_server.parentView.$el.find("#review-please-wait").show();
            OneStep.off("event:sagepay:server:before_post_to_gateway");
        },
        afterPostToGateway: function(){
            view_onestep_plugin_sagepay_server.parentView.$el.find('.btn-checkout').removeAttr('disabled').css("opacity", 1);
            view_onestep_plugin_sagepay_server.parentView.$el.find("#review-please-wait").hide();
            OneStep.off("event:sagepay:server:after_post_to_gateway");
        }
    });

    /** Payment method: Braintree */
    OneStep.Plugins.Braintree               = Backbone.View.extend({
        processPayment: function(parentView) {
            var ccNumber = window.OneStep.$("#braintree_cc_number").val();
            var ccExprYr = window.OneStep.$("#braintree_expiration_yr").val();
            var ccExprMo = window.OneStep.$("#braintree_expiration").val();
            var cvv = window.OneStep.$('#braintree_cc_cid').val();

            if (ccNumber) {
                var braintreeClient = new braintree.api.Client({
                    clientToken: window.checkoutConfig.payment.braintree.clientToken
                });
                braintreeClient.tokenizeCard(
                    {
                        number: ccNumber,
                        expirationMonth: ccExprMo,
                        expirationYear: ccExprYr,
                        cvv : cvv
                    },
                    function (err, nonce) {
                        if (!err) {
                            window.OneStep.$('#payment_form_braintree').find('#braintree_nonce').val(nonce);
                            window.OneStep.$("#braintree_cc_number").prop('disabled', true);
                            window.OneStep.$('#braintree_cc_cid').prop('disabled', true);
                            view_onestep_init.placeOrder();
                        } else {
                            return false;
                        }
                    }
                );
            }
        }
    });

    OneStep.Plugins.RewardPoint             = Backbone.View.extend({
        el: window.OneStep.$("#mw-checkout-payment-rewardpoints"),
        initialize: function(){
        },
        updateRewardPoints: function(mw_step_slider, mw_min_slider){
            var amount = this.$el.find("#mw_amount").val();
            amount = parseInt(amount/mw_step_slider) * mw_step_slider;
            if (amount < mw_min_slider) {
                amount = 0;
            }

            var params = {updaterewardpoints: true, updatepaymenttype: true};
            params.addition_post = {rewardpoints: amount};
            view_onestep_init.update(params);
        }
    });

    view_onestep_load_session_form      = new OneStep.Views.LoadDataForm();
    view_onestep_loader                 = new OneStep.Views.Loader();
    view_onestep_init                   = new OneStep.Views.Init();
    view_onestep_shipping               = new OneStep.Views.Shipping();
    view_onestep_shipping_method        = new OneStep.Views.ShippingMethod();
    view_onestep_payment                = new OneStep.Views.Payment();
    view_onestep_gift                   = new OneStep.Views.Gift();
    view_onestep_cart                   = new OneStep.Views.Cart();
    view_onestep_coupon                 = new OneStep.Views.Coupon();
    view_onestep_billing                = new OneStep.Views.Billing();
    view_onestep_delivery_date          = (onestepConfig.isDeliveryDate) ? new OneStep.Views.DeliveryDate() : null;

    /** Plugins payment methods */
    view_onestep_plugin_sagepay_server      = new OneStep.Plugins.SagepayServer();
    view_onestep_plugin_braintree           = new OneStep.Plugins.Braintree();
    gb_view_onestep_plugin_rewardpoints     = new OneStep.Plugins.RewardPoint();

    view_onestep_load_session_form.bindAll();

    window.OneStep.$('#authorize-iframe').on('load', function() {
        if (window.redirectSuccess != null) {
            window.location = window.redirectSuccess;
        }
    });
});

});