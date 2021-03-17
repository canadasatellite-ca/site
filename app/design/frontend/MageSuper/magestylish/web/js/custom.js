document.addEventListener("DOMContentLoaded", function (event) {

    window.addEventListener("scroll", function (event) {

        var height = this.scrollY;
        var width = this.innerWidth;

        if ((height > 150)) {
            if (document.getElementsByTagName('header')[0]) {
                document.getElementsByTagName('header')[0].className = 'page-header fixed-menu';
            }
            if (document.getElementsByClassName('sections nav-sections')[0] && (width > 768)) {
                document.getElementsByClassName('sections nav-sections')[0].className = 'sections nav-sections fixed-menu';
            }
        } else {
            if (document.getElementsByTagName('header')[0]) {
                document.getElementsByTagName('header')[0].className = 'page-header';
            }
            if (document.getElementsByClassName('sections nav-sections')[0]) {
                document.getElementsByClassName('sections nav-sections')[0].className = 'sections nav-sections';
            }
        }
    });
});

require(['jquery', 'tabs'], function ($) {
    $(function () {

        var heightMax = 0;
        var heightMaxSlick = 0;
        var isCalculated = false;
        var isCalculatedSlick = false;
        var windowWidth = 0;
        var windowWidthSlick = 0;

        window.onresize = function(event) {
            setTimeout(function () {
                if (windowWidth != window.innerWidth) {
                    equalHeight();
                }
                if (windowWidthSlick != window.innerWidth) {
                    equalHeightSlick();
                }
            }, 50)
        };

        window.onscroll = function(event) {
            if (heightMax === 0) {
                equalHeight();
            }

            if (isCalculated === false) {
                equalHeight();
            }

            if (heightMaxSlick === 0) {
                equalHeightSlick();
            }

            if (isCalculatedSlick === false) {
                equalHeightSlick();
            }
        };

        function equalHeight() {
            $('.products-grid').each(function () {
                var items = $(this).find('.product-items .product-item .product-item-info');
                equalHeightItems(items);
            });
        }

        function equalHeightSlick() {
            $('.products-grid').each(function () {
                var itemsSlick = $(this).find('.product-items .product-item .product-item-info-slick');
                equalHeightItemsSlick(itemsSlick);
            });
        }

        function equalHeightItems(items) {
            var group = [],
                offset = 0,
                maxHeight = 0,
                coeff = 1;
            items.first().closest('.product-items').removeClass('equalheight');
            items.css('height', 'auto');

            if (!items.length) {
                return;
            }

            var elHeight = 0;
            items.each(function () {
                var height = $(this).innerHeight();

                if (height > elHeight) {
                    elHeight = height;
                    maxHeight = height;
                }
            });

            if ($(window).width() <= 411) {
                coeff = 0.92;
            }
            items.css('height', maxHeight * coeff);
            isCalculated = true;
            heightMax = maxHeight;
            windowWidth = $(window).width();
            if (maxHeight > 0) {
                $(group).height(maxHeight);
                items.first().closest('.product-items').addClass('equalheight');
            }
        }

        function equalHeightItemsSlick(items) {
            var groupSlick = [],
                offset = 0,
                maxHeightSlick = 0,
                coeffSlick = 1.1;
            items.first().closest('.product-items').removeClass('equalheight');
            items.css('height', 'auto');

            if (!items.length) {
                return;
            }

            var elHeightSlick = 0;
            items.each(function () {
                var heightSlick = $(this).innerHeight();

                if (heightSlick > elHeightSlick) {
                    elHeightSlick = heightSlick;
                    maxHeightSlick = heightSlick;
                }
            });

            items.css('height', maxHeightSlick * coeffSlick);
            isCalculatedSlick = true;
            heightMaxSlick = maxHeightSlick;
            windowWidthSlick = $(window).width();
            if (maxHeightSlick > 0) {
                $(groupSlick).height(maxHeightSlick);
                items.first().closest('.product-items').addClass('equalheight');
            }
        }

        setTimeout(function () {
            $(window).trigger('resize');
        }, 2000);
        /*setTimeout(function(){
            $(window).trigger('resize');
        },3000);*/

        var $carousels = $('.owl-carousel');
        $carousels.on('refreshed.owl.carousel initialized.owl.carousel', function () {
            var $info = $(this).find('.product-item .product-item-info');
            equalHeightItems($info);
        });

        $('.footer-inner-right .box').click(function () {
            $(this).siblings().removeClass('active');
            $(this).toggleClass('active');
        });
        $(".btn-qty").click(function (event) {
            var $button = $(this);
            var oldValue = $button.closest('.control').find("input#qty").val();
            var defaultValue = 1;
            if ($button.hasClass('plus')) {
                var newVal = parseFloat(oldValue) + 1;
            } else {
                if (oldValue > defaultValue) {
                    var newVal = parseFloat(oldValue) - 1;
                } else {
                    newVal = defaultValue;
                }
            }
            $button.closest('.control').find("input#qty").val(newVal);
            event.preventDefault();
        });
        if ($('body').hasClass('cms-index-index')) {
            var fp = $('.block-featured-products').detach();
            $('.main .welcome-text').before(fp);
        }

        var anchor = window.location.hash.substr(1);
        $(".product.data.items [data-role='content']").each(function (index) {
            $('.product-info-main a[href$="#' + this.id + '"]').click(function (event) {
                var anchor = $(this).attr('href').replace(/^.*?(#|$)/, '');
                $('.product.data.items').tabs('activate', index);
                var scroll = parseInt($('#' + anchor).offset().top) - 150;
                $('html, body').animate({
                    scrollTop: scroll
                }, 300);
            });
            if (anchor == this.id) {
                setTimeout(function () {
                    $('.product.data.items').tabs('activate', index);
                    var scroll = parseInt($('#' + anchor).offset().top) - 150;
                    $('html, body').animate({
                        scrollTop: scroll
                    }, 300);
                }, 2000);
            }
        });
    });

    /*$('#mw_onstepcheckout_billing_form input[id="billing\:email"]').on('change',function(){
        $.ajax({
            'url':'/casat/index/emailcheck',
            'data':{
                'email':$(this).val()
            },
            'success':function(result){
                debugger;
            }
        });
    });*/
});

//document.getElementsByClassName("close-btn")[0].onclick = function(event) {
//    document.getElementsByClassName("sections nav-sections")[0].style.display = 'none';
//}

requirejs(['jquery'], function ($) {

    var lazyloadThrottleTimeout;

    lazyload = function () {
        var lazyloadImages = jQuery(".clazyload");
        if (lazyloadThrottleTimeout) {
            clearTimeout(lazyloadThrottleTimeout);
        }
        lazyloadThrottleTimeout = setTimeout(function () {
            var scrollTop = jQuery(window).scrollTop();
            lazyloadImages.each(function () {
                var el = jQuery(this);
                if (el.offset().top !== 0 && el.offset().top < window.innerHeight + scrollTop + 500) {
                    var url = el.attr("data-img");
                    el.attr("src", url);
                    el.removeClass("clazyload");
                    lazyloadImages = jQuery(".clazyload");
                    $(this).closest('.trigger-equal-height').trigger('contentUpdated');
                }
            });
            if (lazyloadImages.length == 0) {
                jQuery(document).off("scroll");
                jQuery(window).off("resize");
            }
        }, 20);
    }

    jQuery(document).ready(function () {
        if (document.documentElement.scrollTop > 100) {
            lazyload();
        }
        jQuery(document).on("scroll", lazyload);
        jQuery(window).on("resize", lazyload);
        $(document).bind('ajaxComplete', function () {
            lazyload();
        });

    });
});
