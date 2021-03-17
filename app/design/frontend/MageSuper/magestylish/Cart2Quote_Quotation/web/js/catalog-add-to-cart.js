/**
 * Cart2Quote
 */
define([
    'jquery',
    'mage/translate',
    'jquery/ui'
], function ($, $t) {
    "use strict";

    $.widget('quotation.quotationAddToCart', {

        options: {
            quoteFormUrl: false,
            processStart: null,
            processStop: null,
            bindSubmit: true,
            noAjax: true,
            minicartSelector: '[data-block="minicart"]',
            miniquoteSelector: '[data-block="miniquote"]',
            messagesSelector: '[data-placeholder="messages"]',
            productStatusSelector: '.stock.available',

            addToCartButton: {
                selector: '#product-addtocart-button',
                disabledClass: 'disabled',
                textWhileAdding: $t('Adding...'),
                textAdded: $t('Added'),
                textDefault: $t('Add to Cart'),
            },

            addToQuoteButton: {
                selector: '#product-addtoquote-button',
                disabledClass: 'disabled',
                textWhileAdding: $t('Adding to Quote...'),
                textAdded: $t('Added'),
                textDefault: $t('Add to Quote')
            },
            namespace: 'ajaxcartpro'
        },

        _create: function () {
            if (this.options.bindSubmit) {
                this._bindSubmit();
            }
        },

        _bindSubmit: function () {
            var self = this;
            this.element.on('submit', function (e) {
                e.preventDefault();
                self.submitForm($(this));
            });
        },

        isLoaderEnabled: function () {
            return this.options.processStart && this.options.processStop;
        },

        submitForm: function (form) {
            var self = this;
            if (form.has('input[type="file"]').length && form.find('input[type="file"]').val() !== '' && self.options.noAjax) {
                self.element.off('submit');

                // Check if quote is being requested.
                if (self.usingQuote()) {
                    form.attr('action', self.options.quoteFormUrl);
                }

                form.submit();
            } else {
                self.ajaxSubmit(form);
            }
        },

        modifyUrl: function(url) {
            var self = this;
            var terms = ['checkout'];
            $.each(terms,function(key,val){
                url = url.replace(val,self.options.namespace);
            });
            return url;
        },

        imageAnimation: function(form){
            $('#footer-mini-cart').slideDown(300,'linear',function(){

                $('#footer-cart-trigger').addClass('active');
                if( form.parents('.product-item').length > 0 ){
                    var $parent = form.parents('.product-item').first();
                    if($parent.find('.product-image-photo').length > 0){
                        var src = $parent.find('.product-image-photo').attr('src');
                        var width = $parent.find('.product-image-photo').width();
                    }else{
                        var src = $parent.find('.product-image').attr('src');
                        var width = $parent.find('.product-image').width();
                    }
                }else{
                    var $parent = $('.fotorama__stage__shaft').first();
                    var src = $parent.find('.fotorama__img').attr('src');
                    var width = $parent.find('.fotorama__img').width();
                }


                var $img = $('<img class="adding-product-img" style="display:none;" />'); //$('#adding-product-img');
                $('body').append($img);
                var imgTop = $parent.offset().top;
                var imgLeft = $parent.offset().left;
                $img.attr('src',src);
                $img.css({
                    'opacity': 1,
                    'width': width,
                    'max-width':'300px',
                    'position': 'absolute',
                    'top': imgTop +'px',
                    'left': imgLeft +'px',
                    'z-index': 1000,
                });
                var $cart = $('.footer-mini-cart .cart-icon').first();
                $cart.removeClass('tada');
                var productId = form.find('input[name="product"]').val();
                imgTop = $cart.offset().top;
                imgLeft = $cart.offset().left + ($cart.width() - 20)/2 ;
                $img.animate({
                    'opacity': 0,
                    'top': imgTop +'px',
                    'left': imgLeft +'px',
                    'width': '20px',
                },1500,'linear',function(){
                    $img.replaceWith('');
                    $cart.addClass('animated tada');
                });
            });
        },

        imageAnimationQuote: function(form){
            if( form.parents('.product-item').length > 0 ){
                var $parent = form.parents('.product-item').first();
                if($parent.find('.product-image-photo').length > 0){
                    var src = $parent.find('.product-image-photo').attr('src');
                    var width = $parent.find('.product-image-photo').width();
                }else{
                    var src = $parent.find('.product-image').attr('src');
                    var width = $parent.find('.product-image').width();
                }
            }else{
                var $parent = $('.fotorama__stage__shaft').first();
                var src = $parent.find('.fotorama__img').attr('src');
                var width = $parent.find('.fotorama__img').width();
            }
            var $p = $('#product-addtoquote-button');
            if ($p.length){
                $parent = $p;
            }

            var $img = $('<img class="adding-product-img" style="display:none;" />'); //$('#adding-product-img');
            $('body').append($img);
            var imgTop = $parent.offset().top;
            var imgLeft = $parent.offset().left;
            $img.attr('src',src);
            $img.css({
                'opacity': 1,
                'width': width,
                'max-width':'300px',
                'position': 'absolute',
                'top': imgTop +'px',
                'left': imgLeft +'px',
                'z-index': 1000,
            });
            var $quote = $('.miniquote-wrapper').first();
            var productId = form.find('input[name="product"]').val();
            imgTop = $quote.offset().top;
            imgLeft = $quote.offset().left + ($quote.width() - 20)/2 ;
            $img.animate({
                'opacity': 0,
                'top': imgTop +'px',
                'left': imgLeft +'px',
                'width': '20px',
            },1000,'linear',function(){
                $img.replaceWith('');
            });
        },
        ajaxSubmit: function (form) {

            var self = this;
            var ajaxUrl = form.attr('action');

            // Check if quote is being requested.

            if (self.usingQuote()) {
                self.disableButton(form, self.options.addToCartButton, false);
                self.disableButton(form, self.options.addToQuoteButton, true);
                $(self.options.miniquoteSelector).trigger('contentLoading');
                ajaxUrl = self.options.quoteFormUrl;
                self.imageAnimationQuote(form);
            } else {
                self.disableButton(form, self.options.addToCartButton, true);
                self.disableButton(form, self.options.addToQuoteButton, false);
                ajaxUrl = self.modifyUrl(ajaxUrl);
                $(self.options.minicartSelector).trigger('contentLoading');
                self.imageAnimation(form);
            }
            $.ajax({
                url: ajaxUrl,
                data: form.serialize(),
                type: 'post',
                dataType: 'json',
                beforeSend: function () {
                    if (self.isLoaderEnabled()) {
                        $('body').trigger(self.options.processStart);
                    }
                },
                success: function (res) {
                    if (self.isLoaderEnabled()) {
                        $('body').trigger(self.options.processStop);
                    }

                    if (res.backUrl) {
                        window.location = res.backUrl;
                        return;
                    }
                    if (res.messages) {
                        $(self.options.messagesSelector).html(res.messages);
                    }
                    if (res.minicart) {
                        if (self.usingQuote()) {
                            $(self.options.miniquoteSelector).replaceWith(res.minicart);
                            $(self.options.miniquoteSelector).trigger('contentUpdated');
                        } else {
                            $(self.options.minicartSelector).replaceWith(res.minicart);
                            $(self.options.minicartSelector).trigger('contentUpdated');
                        }

                    }
                    if (res.product && res.product.statusText) {
                        $(self.options.productStatusSelector)
                            .removeClass('available')
                            .addClass('unavailable')
                            .find('span')
                            .html(res.product.statusText);
                    }
                    if(res.product){
                        window.addedItem(res.product);
                        window.crosssell(res.crosssell);
                    }
                    if(res.cart){
                        window.ajaxcart(res.cart);
                        res.cart.trigger = true;
                        window.cartSidebar(res.cart);
                    }

                    if (self.usingQuote()) {
                        self.enableButton(form, self.options.addToCartButton, false);
                        self.enableButton(form, self.options.addToQuoteButton, true);
                    } else {
                        self.enableButton(form, self.options.addToCartButton, true);
                        self.enableButton(form, self.options.addToQuoteButton, false);
                    }

                }
            });
        },

        disableButton: function (form, buttonType, useTextWhileAdding) {
            var button = $(form).find(buttonType.selector);
            button.addClass(buttonType.disabledClass);
            if (useTextWhileAdding) {
                button.attr('title', buttonType.textWhileAdding);
                button.find('span').text(buttonType.textWhileAdding);
            }
        },

        enableButton: function (form, buttonType, useTextAdded) {
            var self = this,
                button = $(form).find(buttonType.selector);

            if (useTextAdded) {
                button.find('span').text(buttonType.textAdded);
                button.attr('title', buttonType.textAdded);
            }

            setTimeout(function () {
                button.removeClass(buttonType.disabledClass);
                button.find('span').text(buttonType.textDefault);
                button.attr('title', buttonType.textDefault);
            }, 1000);
        },

        /**
         * Checks if requesting a quote.
         * @returns {boolean}
         */
        usingQuote: function () {
            if (this.options.quoteFormUrl !== false) {
                return true;
            } else {
                return false;
            }
        }
    });
    
    return $.quotation.quotationAddToCart;
});
