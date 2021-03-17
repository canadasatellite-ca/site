/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
define([
    'jquery',
    'Magento_Catalog/js/price-utils',
    'underscore',
    'Magento_Catalog/js/price-box',
    'qTip',
    'jquery/ui'
], function ($, utils, _) {
    'use strict';

    $.widget('mageworx.optionFeatures', {

        options: {
            absolutePriceOptionTemplate: '<%= data.label %>' +
            '<% if (data.finalPrice.value) { %>' +
            ' <%- data.finalPrice.formatted %>' +
            '<% } %>'
        },

        /**
         * Triggers one time at first run (from base.js)
         * @param optionConfig
         * @param productConfig
         * @param base
         * @param self
         */
        firstRun: function firstRun(optionConfig, productConfig, base, self) {
            setTimeout(function () {

                // Qty input
                $('.mageworx-option-qty').each(function () {

                    $(this).on('change', function () {

                        var optionInput = $("[data-selector='" + $(this).attr('data-parent-selector') + "']");
                        optionInput.trigger('change');
                    });
                });
            }, 500);

            // Option\Value Description & tooltip
            var extendedOptionsConfig = typeof base.options.extendedOptionsConfig != 'undefined' ?
                base.options.extendedOptionsConfig : {};

            for (var option_id in optionConfig) {
                if (!optionConfig.hasOwnProperty(option_id)) {
                    continue;
                }

                var description = extendedOptionsConfig[option_id]['description'],
                    $option = base.getOptionHtmlById(option_id);
                if (1 > $option.length) {
                    console.log('Empty option container for option with id: ' + option_id);
                    continue;
                }

                if (this.options.option_description_enabled && !_.isEmpty(extendedOptionsConfig[option_id]['description'])) {
                    if (this.options.option_description_mode == this.options.option_description_modes.tooltip) {
                        var $element = $option.find('label span')
                            .first();
                        if ($element.length == 0) {
                            $element = $option.find('fieldset legend span')
                                .first();
                        }
                        $element.css('border-bottom', '1px dotted black');
                        $element.append(' <i class="fa fa-question-circle" style="font-family: fontawesome;font-style: normal;"></i>');
                        $element.qtip({
                            content: {
                                text: description
                            },
                            style: {
                                classes: 'qtip-light'
                            },
                            position: {
                                target: false
                            }
                        });
                    } else if (this.options.option_description_mode == this.options.option_description_modes.text) {
                        var $label = $option.find('label');
                        if ($label.length > 0) {
                            $label
                                .first()
                                .after($('<p class="option-description-text">' + description + '</p>'));
                        } else {
                            $label = $option.find('span');
                            $label
                                .first()
                                .parent()
                                .after($('<p class="option-description-text">' + description + '</p>'));
                        }
                    } else {
                        console.log('Unknown option mode');
                    }
                }

                if (this.options.value_description_enabled) {
                    this._addValueDescription($option, optionConfig, extendedOptionsConfig);
                }
            }
        },

        /**
         * Triggers each time when option is updated\changed (from the base.js)
         * @param option
         * @param optionConfig
         * @param productConfig
         * @param base
         */
        update: function update(option, optionConfig, productConfig, base) {
            var $option = $(option),
                $optionQtyInput = $("[data-parent-selector='" + $option.attr('data-selector') + "']"),
                optionQty = 1,
                values = $option.val(),
                optionId = base.getOptionId($option);

            if ($optionQtyInput.length) {
                if (($option.is(':checked') || $('option:selected', $option).val())) {
                    if ($optionQtyInput.val() == 0) {
                        $optionQtyInput.val(1);
                    }
                    $optionQtyInput.attr('disabled', false);
                } else if (!$option.is(':checked') && !$('option:selected', $option).val()) {
                    if ($optionQtyInput.attr('type') != 'hidden' && $option.attr('type') != 'radio') {
                        $optionQtyInput.val(0);
                        $optionQtyInput.attr('disabled', true);
                    }
                }

                if (parseFloat($optionQtyInput.val())) {
                    optionQty = parseFloat($optionQtyInput.val());
                }

                if (values) {
                    if (!Array.isArray(values)) {
                        values = [values];
                    }

                    $(values).each(function (i, e) {
                        optionConfig[optionId][e]['qty'] = optionQty;
                    });
                }
            }
        },

        /**
         * Triggers each time after the all updates when option was changed (from the base.js)
         * @param base
         * @param productConfig
         */
        applyChanges: function (base, productConfig)
        {
            this.base = base;

            var isAbsolutePriceUsed = true;
            if (_.isUndefined(productConfig.absolute_price) || productConfig.absolute_price == "0") {
                isAbsolutePriceUsed = false;
            }

            if (productConfig.type_id == 'configurable' && !isAbsolutePriceUsed) {
                return;
            }

            this.initProductPrice(productConfig);
            this.calculateSelectedOptionsPrice();
            this.applyProductPriceDisplayMode();

            if (!isAbsolutePriceUsed || (isAbsolutePriceUsed && this.optionBasePrice <= 0)) {
                this.productRegularPriceExclTax += parseFloat(this.optionOldPriceExclTax);
                this.productRegularPriceInclTax += parseFloat(this.optionOldPriceInclTax);
                this.productFinalPriceExclTax += parseFloat(this.optionBasePrice);
                this.productFinalPriceInclTax += parseFloat(this.optionFinalPrice);
            } else {
                this.productRegularPriceExclTax = parseFloat(this.optionOldPriceExclTax);
                this.productRegularPriceInclTax = parseFloat(this.optionOldPriceInclTax);
                this.productFinalPriceExclTax = parseFloat(this.optionBasePrice);
                this.productFinalPriceInclTax = parseFloat(this.optionFinalPrice);
            }

            // Set product prices according to price's display mode on the product view page
            // 1 - without tax
            // 2 - with tax
            // 3 - both (with and without tax)
            if (base.getPriceDisplayMode() == 1) {
                base.setProductRegularPrice(this.productRegularPriceExclTax);
                base.setProductFinalPrice(this.productFinalPriceExclTax);
            } else {
                base.setProductRegularPrice(this.productRegularPriceInclTax);
                base.setProductFinalPrice(this.productFinalPriceInclTax);
            }
            base.setProductPriceExclTax(this.productFinalPriceExclTax);
        },

        /**
         * Get summary price from all selected options
         */
        calculateSelectedOptionsPrice: function ()
        {
            var self = this,
                form = this.base.getFormElement(),
                config = this.base.options,
                options = $(config.optionsSelector, form),
                processedDatetimeOptions = [];

            this.optionFinalPrice = 0;
            this.optionBasePrice = 0;
            this.optionOldPriceInclTax = 0;
            this.optionOldPriceExclTax = 0;

            options.filter('select').each(function (index, element) {
                var $element = $(element),
                    optionId = utils.findOptionId($element),
                    optionConfig = config.optionConfig && config.optionConfig[optionId],
                    values = $element.val();

                if (_.isUndefined(values) || !values) {
                    return;
                }

                if (!Array.isArray(values)) {
                    values = [values];
                }

                $(values).each(function (i, valueId) {
                    if (_.isUndefined(optionConfig[valueId])) {
                        if (_.isUndefined(optionConfig.prices)) {
                            return;
                        }

                        var dateDropdowns = $element.parent().find(config.dateDropdownsSelector);
                        if (_.isUndefined(dateDropdowns)) {
                            return;
                        }

                        if ($element.closest('.field').css('display') == 'none') {
                            $element.val('');
                            return;
                        }

                        var optionConfigCurrent = self.base.getDateDropdownConfig(optionConfig, dateDropdowns);
                        if (_.isUndefined(optionConfigCurrent.prices) ||
                            $.inArray(optionId, processedDatetimeOptions) != -1) {
                            return;
                        }
                        processedDatetimeOptions.push(optionId);
                    } else {
                        var optionConfigCurrent = optionConfig[valueId];
                    }

                    self.collectOptionPriceAndQty(optionConfigCurrent, optionId, valueId);
                });
            });

            options.filter('input[type="radio"], input[type="checkbox"]').each(function (index, element) {
                var $element = $(element),
                    optionId = utils.findOptionId($element),
                    optionConfig = config.optionConfig && config.optionConfig[optionId],
                    valueId = $element.val();

                if (!$element.is(':checked')) {
                    return;
                }

                if (typeof valueId == 'undefined' || !valueId) {
                    return;
                }

                var optionConfigCurrent = optionConfig[valueId];

                self.collectOptionPriceAndQty(optionConfigCurrent, optionId, valueId);
            });

            options.filter('input[type="text"], textarea, input[type="file"]').each(function (index, element) {
                var $element = $(element),
                    optionId = utils.findOptionId($element),
                    optionConfig = config.optionConfig && config.optionConfig[optionId],
                    value = $element.val();

                if (typeof value == 'undefined' || !value) {
                    if ($('#delete-options_' + optionId + '_file').length < 1) {
                        return;
                    }
                }

                if ($element.closest('.field').css('display') == 'none') {
                    $element.val('');
                    return;
                }

                var qty = typeof optionConfig['qty'] != 'undefined' ? optionConfig['qty'] : 1;
                self.optionFinalPrice += parseFloat(optionConfig.prices.finalPrice.amount) * qty;
                self.optionOldPriceInclTax += parseFloat(optionConfig.prices.oldPrice.amount_incl_tax) * qty;
                self.optionBasePrice += parseFloat(optionConfig.prices.basePrice.amount) * qty;
                self.optionOldPriceExclTax += parseFloat(optionConfig.prices.oldPrice.amount_excl_tax) * qty;
            });
        },

        /**
         * Collect Option's Price
         *
         * @param {array} optionConfigCurrent
         * @param {number} optionId
         * @param {number} valueId
         * @private
         */
        collectOptionPriceAndQty: function calculateOptionsPrice(optionConfigCurrent, optionId, valueId)
        {
            this.actualPriceInclTax = 0;
            this.actualPriceExclTax = 0;

            var config = this.base.options,
                isOneTime = this.base.isOneTimeOption(optionId),
                productQty = $(config.productQtySelector).val(),
                qty = !_.isUndefined(optionConfigCurrent['qty']) ? optionConfigCurrent['qty'] : 1;
            this.getActualPrice(optionId, valueId, qty);

            var actualFinalPrice = this.actualPriceInclTax
                ? this.actualPriceInclTax
                : parseFloat(optionConfigCurrent.prices.finalPrice.amount),
                actualBasePrice = this.actualPriceExclTax
                    ? this.actualPriceExclTax
                    : parseFloat(optionConfigCurrent.prices.basePrice.amount),
                oldPriceInclTax = parseFloat(optionConfigCurrent.prices.oldPrice.amount_incl_tax),
                oldPriceExclTax = parseFloat(optionConfigCurrent.prices.oldPrice.amount_excl_tax);

            if (!isOneTime && this.options.product_price_display_mode === 2) {
                actualFinalPrice *= productQty;
                actualBasePrice *= productQty;
                oldPriceInclTax *= productQty;
                oldPriceExclTax *= productQty;
            }

            this.optionFinalPrice += actualFinalPrice * qty;
            this.optionBasePrice += actualBasePrice * qty;
            this.optionOldPriceInclTax += oldPriceInclTax * qty;
            this.optionOldPriceExclTax += oldPriceExclTax * qty;
        },

        /**
         * Get actual price of option considering special/tier prices
         *
         * @param {number} optionId
         * @param {number} valueId
         * @param {number} qty
         * @returns {void}
         */
        getActualPrice: function (optionId, valueId, qty)
        {
            var config = this.base.options,
                specialPrice = null,
                tierPrices = null,
                price = null,
                totalQty = 0,
                suitableTierPrice = null,
                suitableTierPriceQty = null,
                isOneTime = this.base.isOneTimeOption(optionId),
                productQty = $(config.productQtySelector).val();
            if (_.isUndefined(config.extendedOptionsConfig[optionId].values)) {
                return;
            }

            if (isOneTime) {
                totalQty = parseFloat(qty);
            } else {
                totalQty = parseFloat(productQty) * parseFloat(qty);
            }

            if (!_.isUndefined(config.optionConfig[optionId][valueId].prices.basePrice.amount)) {
                specialPrice = config.optionConfig[optionId][valueId].prices.basePrice.amount;
            }

            if (!_.isUndefined(config.extendedOptionsConfig[optionId].values[valueId].tier_price)) {
                tierPrices = $.parseJSON(config.extendedOptionsConfig[optionId].values[valueId].tier_price);
                if (_.isUndefined(tierPrices[totalQty])) {
                    $.each(tierPrices, function(index, tierPrice) {
                        if (suitableTierPriceQty < index && totalQty >= index) {
                            suitableTierPrice = tierPrice;
                            suitableTierPriceQty = index;
                        }
                    });
                } else {
                    suitableTierPrice = tierPrices[totalQty];
                    suitableTierPriceQty = totalQty;
                }
            }

            if (suitableTierPrice && (suitableTierPrice.price < specialPrice || specialPrice === null)) {
                this.actualPriceExclTax = suitableTierPrice.price;
                this.actualPriceInclTax = suitableTierPrice.price_incl_tax;
            } else {
                this.actualPriceExclTax = specialPrice;
                this.actualPriceInclTax = config.optionConfig[optionId][valueId].prices.finalPrice.amount;
            }
        },

        /**
         * Initialize Product Price
         *
         * @param productConfig
         * @private
         */
        initProductPrice: function (productConfig)
        {
            this.productRegularPriceExclTax = productConfig.regular_price_excl_tax;
            this.productRegularPriceInclTax = productConfig.regular_price_incl_tax;
            this.productFinalPriceExclTax = productConfig.final_price_excl_tax;
            this.productFinalPriceInclTax = productConfig.final_price_incl_tax;
        },

        /**
         * Apply Product Price Display Mode
         *
         * @private
         */
        applyProductPriceDisplayMode: function ()
        {
            var productPriceDisplayMode = this.options.product_price_display_mode,
                productQty = parseFloat($(this.base.options.productQtySelector).val()),
                actualTierPrice = null;

            if (productPriceDisplayMode === 'per_item') {
                actualTierPrice = this.getProductActualTierPrice();
                if (actualTierPrice !== null) {
                    this.productFinalPriceExclTax = actualTierPrice;
                    this.productFinalPriceInclTax = this.getProductActualTierPrice(true);
                }
            } else if (productPriceDisplayMode === 'final_price') {
                actualTierPrice = this.getProductActualTierPrice();
                if (actualTierPrice !== null) {
                    this.productFinalPriceExclTax = actualTierPrice * productQty;
                    this.productFinalPriceInclTax = this.getProductActualTierPrice(true) * productQty;
                } else {
                    this.productFinalPriceExclTax *= productQty;
                    this.productFinalPriceInclTax *= productQty;
                }
                this.productRegularPriceExclTax *= productQty;
                this.productRegularPriceInclTax *= productQty;
            }
        },

        /**
         * Get product's actual price considering its qty
         *
         * @param {boolean} includeTax
         * @returns {number}
         */
        getProductActualTierPrice: function (includeTax)
        {
            var config = this.base.options,
                productConfig = config.productConfig,
                price = null,
                productQty = $(config.productQtySelector).val(),
                key = includeTax ? 'price_incl_tax' : 'price_excl_tax';

            if (_.isUndefined(productConfig.extended_tier_prices) || productConfig.extended_tier_prices.length < 1) {
                return price;
            }

            var tierPrices = productConfig.extended_tier_prices;
            tierPrices.sort(function (a, b) {
                return a['qty'] - b['qty'];
            });

            _.each(tierPrices, function (tier, index) {
                if (parseFloat(tier['qty']) > parseFloat(productQty)) {
                    return;
                }

                if (price === null || parseFloat(tier[key]) < parseFloat(price)) {
                    price = tier[key];
                }
            });

            return price;
        },

        /**
         * Add description to the values
         * @param $option
         * @param optionConfig
         * @param extendedOptionsConfig
         * @private
         */
        _addValueDescription: function _addValueDescription($option, optionConfig, extendedOptionsConfig) {
            var self = this,
                $options = $option.find('.product-custom-option');

            $options.filter('select').each(function (index, element) {
                var $element = $(element),
                    optionId = utils.findOptionId($element),
                    value = extendedOptionsConfig[optionId]['values'];

                if ($element.attr('multiple') && !$element.hasClass('mageworx-swatch')) {
                    return;
                }

                if (typeof value == 'undefined' || _.isEmpty(value)) {
                    return;
                }

                if ($element.hasClass('mageworx-swatch')) {
                    var $swatches = $element.parent().find('.mageworx-swatch-option');

                    $swatches.each(function (swatchKey, swatchValue) {
                        var valueId = $(swatchValue).attr('data-option-type-id');
                        if (!_.isUndefined(value[valueId]) &&
                            (!_.isEmpty(value[valueId]['description']) ||
                                !_.isEmpty(value[valueId]['images_data']['tooltip_image']))
                        ) {
                            var tooltipImage = self.getTooltipImageHtml(value[valueId]);
                            var title = '<div class="title">' + value[valueId]['title'] + '</div>';
                            var description = '';
                            if (!_.isEmpty(value[valueId]['description'])) {
                                description = value[valueId]['description'];
                            }
                            var stockMessage = '';
                            if (!_.isEmpty(optionConfig[optionId][valueId]['stockMessage'])) {
                                stockMessage = '<div class="info">'
                                    + optionConfig[optionId][valueId]['stockMessage']
                                    + '</div>';
                            }
                            $(swatchValue).qtip({
                                content: {
                                    text: tooltipImage + title + stockMessage + description
                                },
                                style: {
                                    classes: 'qtip-light'
                                },
                                position: {
                                    target: false
                                }
                            });
                        }
                    });
                } else {
                    var $image = $('<img>', {
                        src: self.options.question_image,
                        alt: 'tooltip',
                        "class": 'option-select-tooltip-' + optionId,
                        width: '16px',
                        height: '16px',
                        style: 'display: none'
                    });

                    $element.parent().prepend($image);
                    $element.on('change', function (e) {
                        var valueId = $element.val();
                        if (!_.isUndefined(value[valueId]) &&
                            !_.isEmpty(value[valueId]['description'])
                        ) {
                            var tooltipImage = self.getTooltipImageHtml(value[valueId]);
                            $image.qtip({
                                content: {
                                    text: tooltipImage + value[valueId]['description']
                                },
                                style: {
                                    classes: 'qtip-light'
                                },
                                position: {
                                    target: false
                                }
                            });
                            $image.show();
                        } else {
                            $image.hide();
                        }
                    });
                }

                if ($element.val()) {
                    $element.trigger('change');
                }
            });

            $options.filter('input[type="radio"], input[type="checkbox"]').each(function (index, element) {
                var $element = $(element),
                    optionId = utils.findOptionId($element),
                    optionConfig = extendedOptionsConfig[optionId],
                    value = extendedOptionsConfig[optionId]['values'];

                if (typeof value == 'undefined' || !value) {
                    return;
                }

                var valueId = $element.val();
                if (_.isUndefined(value[valueId]) ||
                    _.isEmpty(value[valueId]['description'])
                ) {
                    return;
                }

                var description = value[valueId]['description'],
                    tooltipImage = self.getTooltipImageHtml(value[valueId]),
                    $image = self.getTooltipImageForOptionValue(valueId);
                $element.parent().append($image);
                $image.qtip({
                    content: {
                        text: tooltipImage + description
                    },
                    style: {
                        classes: 'qtip-light'
                    },
                    position: {
                        target: false
                    }
                });
            });
        },

        /**
         * Create image with "?" mark
         * @param valueId
         * @returns {*|jQuery|HTMLElement}
         */
        getTooltipImageForOptionValue: function getTooltipImageForOptionValue(valueId) {
            return $('<img>', {
                src: this.options.question_image,
                alt: 'tooltip',
                "class": 'option-value-tooltip-' + valueId,
                width: '16px',
                height: '16px'
            });
        },

        /**
         * Get image html, if it exists, for tooltip
         * @param value
         * @returns {string}
         */
        getTooltipImageHtml: function getTooltipImageHtml(value) {
            if (value['images_data']['tooltip_image']) {
                return '<div class="image" style="width:auto; height:auto"><img src="' +
                    value['images_data']['tooltip_image'] +
                    '" /></div>';
            } else {
                return '';
            }
        }
    });

    return $.mageworx.optionFeatures;
});
