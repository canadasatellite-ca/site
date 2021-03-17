/*
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    "jquery",
    "jquery/ui"
], function ($) {
    'use strict';

    $.widget('mage.quotationTiercontrol', {
        options: {
            action: undefined,
            itemId: undefined,
            tierItemId: undefined,
            newRowCount: 0,
            increment: 5,
            highestQty: 1
        },

        _create: function () {
            var self = this;
            $(self.element).click(function (event) {
                switch (self.options.action) {
                    case 'addRow':
                        event.preventDefault();
                        self.addRow();
                        break;
                    case 'removeRow':
                        event.preventDefault();
                        self.removeRow();
                        break;
                    case 'selectRow':
                        self.selectRow();
                        break;
                    default:
                        break;
                }
            });
        },

        /**
         * Makes a copy of a quote tier row and shows the row.
         */
        addRow: function () {
            if (typeof(this.options.itemId) != 'undefined') {
                var emptyRow = $('#quote-item-tier-row-empty-' + this.options.itemId);
                var clonedRow = this.cloneRow(emptyRow);
                this.placeClonedRow(emptyRow, clonedRow);
                this.options.newRowCount++;
                this.updateRowSpan(1);
            }
        },

        /**
         * Clone the row from the empty row
         *
         * @param emptyRow
         * @return emptyRowClone
         */
        cloneRow: function (emptyRow) {
            var emptyRowClone = $(emptyRow.clone());
            emptyRowClone.prop(
                'id',
                'quote-item-tier-row-empty-' + this.options.itemId + '-' + this.options.newRowCount
            );
            emptyRowClone.show();
            emptyRowClone.html(emptyRowClone.html().replace(new RegExp("%template%", "g"), 'new'));
            emptyRowClone.html(emptyRowClone.html().replace(new RegExp("%increment%", "g"), this.options.newRowCount));
            emptyRowClone = this.incrementQty(emptyRowClone);

            var input = emptyRowClone.children('.col-price').find(':input').first();
            input.attr('data-mage-init', '{"priceQuoted":{}}');
            input.prop('id', input.attr('id') + '-' + this.options.newRowCount);
            return emptyRowClone;
        },

        /**
         * Place the cloned row
         *
         * @param emptyRow
         * @param emptyRowClone
         */
        placeClonedRow: function (emptyRow, emptyRowClone) {
            var rowCount = this.options.newRowCount - 1;
            var previousRow = $('#quote-item-tier-row-empty-' + this.options.itemId + '-' + rowCount);

            if (previousRow.length > 0) {
                previousRow.after(emptyRowClone);
                previousRow.trigger('contentUpdated');
            } else {
                emptyRow.after(emptyRowClone);
                emptyRow.trigger('contentUpdated');
            }
        },

        /**
         * Update the row span
         */
        updateRowSpan: function (add) {
            $('.selected-tier-row').find('[rowspan]').each(function () {
                var rowspanValue = (parseInt($(this).attr('rowspan')) + add);
                $(this).attr('rowspan', rowspanValue);
            });
        },

        /**
         * Increment the qty for the emptyRow
         *
         * @param row
         * @return row
         */
        incrementQty: function (row) {
            this.options.highestQty = this.options.highestQty + this.options.increment;
            row.find('.item-qty').val(this.options.highestQty);

            return row;
        },

        /**
         * Remove a row
         */
        removeRow: function () {
            $(this.element).closest('tr').remove();
            this.updateRowSpan(-1);
        },

        /**
         * Select a row
         */
        selectRow: function () {
            $(this.element).closest('tbody').find('.selected-tier-row').removeClass('selected-tier-row');
            $('#quote-item-tier-row-' + this.options.tierItemId).addClass('selected-tier-row');
        }
    });

    return $.mage.quotationTiercontrol;
});