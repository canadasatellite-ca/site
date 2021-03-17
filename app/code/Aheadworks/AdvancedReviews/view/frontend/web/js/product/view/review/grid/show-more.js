/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

define([
    'jquery',
    'mage/translate',
    'jquery/ui'
], function ($, $t) {
    'use strict';

    $.widget('awar.awArShowMore', {
        options: {
            reviewListSelector: '[data-role=aw-ar__review-items-list]',
            maxHght: 230,
            readModeStr: $t('Read more'),
            readLessStr: $t('Read less'),
            reviewItemSelector: '.review-item',
            contentSelector: '.content',
            buttonSelector: '.review-item .show-button'
        },

        /**
         * Initialize widget
         */
        _create: function() {
            this._initShowMore();
            this._initEventHandlers();
        },

        /**
         * Show more review
         */
        _initShowMore: function () {
            var me = this;

            $(this.options.reviewListSelector)
                .find(this.options.contentSelector)
                .removeClass('hide-more open-block');

            $(this.options.reviewListSelector)
                .find('.show-button')
                .parent()
                .remove();

            $(this.options.reviewListSelector).find(this.options.reviewItemSelector).each(function(){
                var $contentElement = $(this).find(me.options.contentSelector);

                if (parseInt($contentElement.height()) > me.options.maxHght) {
                    $contentElement
                        .addClass('hide-more')
                        .after('<div><span class="show-button" tabindex="0">'
                            + me.options.readModeStr + '</span></div>');
                } else {
                    $contentElement.removeClass('hide-more');
                    $(this)
                        .find('.show-button')
                        .parent()
                        .remove();
                }
            });
        },

        /**
         * Init event handlers
         *
         * @private
         */
        _initEventHandlers: function () {
            var me = this;

            $(this.options.buttonSelector).on(
                {
                    'click': this._toggleReviewContent.bind(this),
                    'keypress': function (event) {
                        if (event.keyCode === 13) {
                            me._toggleReviewContent(event);
                        }
                    }
                }
            );
        },

        /**
         * Toggle review content
         *
         * @param {Event} event
         * @private
         */
        _toggleReviewContent: function (event) {
            var button = event.currentTarget,
                buttonLabel;

            $(button)
                .toggleClass('active')
                .parents(this.options.reviewItemSelector)
                .find('.hide-more')
                .toggleClass('open-block');

            buttonLabel = $(button).hasClass('active') ? this.options.readLessStr : this.options.readModeStr;
            $(button).prop('innerText', buttonLabel);
        }
    });

    return $.awar.awArShowMore;
});
