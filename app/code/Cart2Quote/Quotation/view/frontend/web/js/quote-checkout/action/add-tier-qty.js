/*
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

var addTier;
var updateTier;
var removeTier;
var removeSavedTier;
define([
    "jquery",
    'mage/url'
], function ($, url) {
    'use strict';

    return function (config) {
        var index = 0;
        $('#add-tier-' + config.item_id).on('click', function () {
            index++;
            var itemId = config.item_id;
            var parentElement = document.getElementById('tiers-' + itemId);
            var tierElement = document.createElement('div');
            var tierElementDiv = 'tiers_' + itemId + '_' + index;
            var link = '<a href="#" class="action action-delete" title="Remove item" onclick="removeTier(this); return false"></a>';
            var customId = 'quote_' + itemId + '_' + index;
            tierElement.setAttribute("id", tierElementDiv);
            tierElement.className = "add-row-tier actions-toolbar";
            tierElement.innerHTML = '<input onchange="addTier(\'' + customId + '\', \'' + itemId + '\', \'' + tierElementDiv + '\')" type="number" id="' + customId + '"  class="input-text tierqty">' + link;
            parentElement.appendChild(tierElement);

            $('.add-row-tier:last .tierqty').rules('add', {
                required: true,
                'validate-greater-than-zero': true
            });
        });

        addTier = function (elementId, itemId, tierElementDiv) {
            var currentElement = document.getElementById(elementId);
            var qtyElement = $("#" + elementId);
            var qty = qtyElement.val();
            var dataForm = $('#form-validate');
            if (qtyElement.valid()) {
                $.ajax({
                    url: url.build("quotation/quote/addtieritems"),
                    type: "POST",
                    data: {"item_id": itemId, "qty": qty},
                    showLoader: true,
                    success: function (response) {
                        if (!response.error) {
                            var tierElement = 'tier-item-' + response.tier_id;
                            currentElement.setAttribute("id", tierElement);
                            currentElement.setAttribute("onchange", 'updateTier(\'' + tierElement + '\',\'' + itemId + '\', \'' + response.tier_id + '\')');
                            var removeAction = $(currentElement).parent().find(".action-delete");
                            removeAction.removeAttr("onclick");
                            removeAction.attr("onclick", 'removeSavedTier(\'' + tierElementDiv + '\', \'' + response.tier_id + '\'); return false');
                        }
                    }
                });
            }
        };

        updateTier = function (elementId, itemId, tierId) {
            var currentElement = $("#" + elementId);
            var qty = currentElement.val();
            var dataForm = $('#form-validate');
            if (dataForm.validation('isValid')) {
                $.ajax({
                    url: url.build("quotation/quote/addtieritems"),
                    type: "POST",
                    data: {"item_id": itemId, "qty": qty, 'tier_id': tierId},
                    showLoader: true
                });
            }
        };

        removeTier = function (element) {
            $(element.parentElement).hide();
        };

        removeSavedTier = function (elementId, tierId) {
            var currentElement = $("#" + elementId);
            $.ajax({
                url: url.build("quotation/quote/deletetieritem"),
                type: "POST",
                data: {"tier_id": tierId},
                showLoader: true,
                success: function () {
                    currentElement.hide();
                }
            });
        }
    };
});
