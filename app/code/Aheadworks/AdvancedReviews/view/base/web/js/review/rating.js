/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

define([
    'jquery',
    'Magento_Ui/js/form/element/abstract'
], function ($, Abstract) {
    'use strict';

    return Abstract.extend({
        defaults: {
            isEditable: false,
            checkedLabelSelector: '.aw-ar-rating .ratings-controls-container label._checked',
            checkedLabelClass: '_checked',
            hoveredLabelClass: '_hovered',
            viewTmpl: 'Aheadworks_AdvancedReviews/review/rating/view',
            tooltipSelector: '.ratings-controls-container .rating-tooltip',
            ratingContainerSelector: '.aw-ar-rating .ratings-controls-container'
        },

        /**
         * On click label event handler
         *
         * @param {Array} itemData
         * @param {Event} event
         */
        onClick: function(itemData, event) {
            this.value(itemData.value);
            this.checkCurrentLabel(event.currentTarget);
            this.updateRating();
            this.updateTooltip(itemData.title, itemData.title);
            this.updateStyleContainer();
        },

        /**
         * On mouse enter label event handler
         *
         * @param {Array} itemData
         * @param {Event} event
         */
        onMouseEnter: function(itemData, event) {
            this.markAsCheckedHoveredLabels(event.currentTarget);
            if (!$(this.ratingContainerSelector).hasClass(this.checkedLabelClass)) {
                this.updateTooltip(itemData.title);
            }
        },

        /**
         * On mouse leave label event handler
         *
         * @param {Array} itemData
         * @param {Event} event
         */
        onMouseLeave: function(itemData, event) {
            this.markAsUncheckedHoveredLabels(event.currentTarget);

            this.updateTooltip(this.getTooltipTextOnMouseLeave());
        },

        /**
         * Update tooltip text
         *
         * @param {String} tooltipText
         * @param {String} tooltipTitle
         */
        updateTooltip: function( tooltipText, tooltipTitle ) {
            if (tooltipTitle != '' && typeof tooltipTitle !== typeof undefined && tooltipTitle !== false) {
                $(this.tooltipSelector).text(tooltipText).attr('data-title',tooltipTitle);
            }
            else {
                $(this.tooltipSelector).text(tooltipText);
            }
        },

        /**
         * Get tooltip title
         */
        getTooltipTitle: function() {
            return $(this.tooltipSelector).attr('data-title');
        },

        /**
         * Add class to parent star element
         */
        updateStyleContainer: function() {
            $(this.ratingContainerSelector).addClass(this.checkedLabelClass);
        },

        /**
         * Get tooltip text on mouse leave
         *
         * @returns {string}
         */
        getTooltipTextOnMouseLeave: function() {
            var attrTitle = this.getTooltipTitle(), tooltipText = '';
            if ( attrTitle != '' && typeof attrTitle !== typeof undefined && attrTitle !== false) {
                tooltipText = attrTitle;
            }
            return tooltipText;
        },

        /**
         * Update label rating by current rating value
         */
        updateRating: function() {
            var lastCheckedLabel = $(this.checkedLabelSelector).last(),
                labelsToCheck = lastCheckedLabel.prevAll('label').addBack(),
                labelsToUncheck = lastCheckedLabel.nextAll('label');

            labelsToCheck.addClass(this.checkedLabelClass);
            labelsToUncheck.removeClass(this.checkedLabelClass);
        },

        /**
         * Check current label
         *
         * @param {DOMElement} currentLabel
         */
        checkCurrentLabel: function(currentLabel) {
            $(currentLabel).addClass(this.checkedLabelClass);
        },

        /**
         * Mark as checked hovered labels
         * 
         * @param {DOMElement} currentLabel
         */
        markAsCheckedHoveredLabels: function(currentLabel) {
            var labelsToCheck = this.getActiveForHoverLabels(currentLabel);

            labelsToCheck.addClass(this.hoveredLabelClass);
        },
        
        /**
         * Mark as unchecked hovered labels
         * 
         * @param {DOMElement} currentLabel
         */
        markAsUncheckedHoveredLabels: function(currentLabel) {
            var labelsToUncheck = this.getActiveForHoverLabels(currentLabel);

            labelsToUncheck.removeClass(this.hoveredLabelClass);
        },

        /**
         * Get active for hover labels
         * 
         * @param currentLabel
         * @returns {*|jQuery}
         */
        getActiveForHoverLabels: function(currentLabel) {
            return $(currentLabel)
                .prevAll('label')
                .addBack();
        }
    });
});
