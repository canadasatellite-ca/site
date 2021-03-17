define([
    'jquery'
], function($) {
    'use strict';
    $.widget('crypto.coins',{
        firstSend: false,
        isSend: false,
        _create: function() {
            var self = this;
            setInterval(function() {
                if (self.firstSend && !self.isSend) {
                    self._getResponseData();
                }
            }, self.options.interval)

            if (!this.firstSend && !this.isSend) {
                this._getResponseData();
                this.firstSend = true;
            }
        },
        _getResponseData: function () {
            var self = this;
            $.ajax({
                url: self.options.url,
                type: 'post',
                dataType: 'json',
                beforeSend: function () {
                    self.isSend = true;
                    if (!self.firstSend) {
                        $("body").trigger('processStart');
                    }
                },
                success: function (response, textStatus, jqXHR) {
                    if (jqXHR.status == 200) {
                        if (typeof response.result == 'undefined') {
                            return false;
                        }
                        if (response.result.length > 0) {
                            var limit = self.options.limit;
                            var html = '';
                            html = html + '<div class="crypto-coins-item-head">' +
                                '<strong>#</strong>' +
                                '<strong>Name</strong>' +
                                '<strong>Last Price</strong>';
                            if (self.options.priceChange) {
                                html = html + '<strong>24h Change ($)</strong>';
                            }

                            if (self.options.highPriceChange) {
                                html = html + '<strong>24h High</strong>';
                            }

                            if (self.options.lowPriceChange) {
                                html = html + '<strong>24h Low</strong>';
                            }

                            if (self.options.priceChangePercent) {
                                html = html + '<strong>24h Change (%)</strong>';
                            }

                            html = html + '</div>';
                            $.each(response.result, function (key, value) {
                                if (limit && (limit == key)) {
                                    return false;
                                }
                                html = html + '<div class="crypto-coins-item-number">' + key +
                                    '</div>';

                                html = html + '<div class="crypto-coins-item">' +
                                    '<div class="pair">';

                                html = html + '<div>' + this.symbol + '</div>' +
                                    '</div>' +
                                    '<div class="price">$' + this.lastPrice + '</div>';

                                if (self.options.priceChange) {
                                    html = html + '<div class="price-change">' + this.priceChange + '</div>';
                                }

                                if (self.options.highPriceChange) {
                                    html = html + '<div class="high-price-change">' + this.highPrice + '</div>';
                                }

                                if (self.options.lowPriceChange) {
                                    html = html + '<div class="low-price-change">' + this.lowPrice + '</div>';
                                }


                                if (self.options.priceChangePercent) {
                                    var changeColor = ' class="price-change-percent more"';
                                    if (this.priceChangePercent.substring(0, 1) == '-') {
                                        changeColor = ' class="price-change-percent less"';
                                    }
                                    html = html + '<div' + changeColor + '>' + this.priceChangePercent + '%</div>';
                                }

                                html = html + '</div>';
                            })
                            $(self.element).html(html);
                        }
                    }
                }
            }).done(function () {
                self.isSend = false;
                $("body").trigger('processStop');
            })
        }
    });

    return $.crypto.coins;
});
