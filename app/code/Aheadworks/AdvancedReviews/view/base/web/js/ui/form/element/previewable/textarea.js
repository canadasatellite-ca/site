/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

define([
    'Magento_Ui/js/form/element/textarea'
], function (Component) {
    'use strict';

    return Component.extend({
        defaults: {
            previewSettings: {
                maxCountOfWords: 5,
                sentenceEndCharacters: '.!?',
                sentenceGapPlaceholder: '...'
            }
        },

        /**
         * {@inheritdoc}
         */
        getPreview: function () {
            var basicPreview = this._super(),
                previewFirstSentence,
                preparedPreview;

            basicPreview = this.trimTrailingWhitespaces(this.trimLeadingWhitespaces(basicPreview));
            preparedPreview = this.getTruncatedContent(basicPreview);
            previewFirstSentence = this.getFirstSentence(preparedPreview);
            if (previewFirstSentence) {
                preparedPreview = previewFirstSentence;
            } else if (basicPreview.length > preparedPreview.length) {
                preparedPreview += this.previewSettings.sentenceGapPlaceholder;
            }

            return preparedPreview;
        },

        /**
         * Remove leading whitespaces from the string
         *
         * @param {String} string
         * @returns {String}
         */
        trimLeadingWhitespaces: function(string) {
            return string.replace(/^\s\s*/, '');
        },

        /**
         * Remove trailing whitespaces from the string
         *
         * @param {String} string
         * @returns {String}
         */
        trimTrailingWhitespaces: function(string) {
            return string.replace(/\s\s*$/, '');
        },

        /**
         * Retrieve first words of the content string according to the preview settings
         *
         * @param {String} content
         * @returns {String}
         */
        getTruncatedContent: function(content) {
            var firstNWordsRegExp = new RegExp('^([^\\s]+\\s*){1,' + this.previewSettings.maxCountOfWords + '}'),
                truncatedContent = content.match(firstNWordsRegExp);
            if (truncatedContent) {
                return truncatedContent.shift();
            } else {
                return '';
            }
        },

        /**
         * Retrieve first sentence of the content
         *
         * @param {String} content
         * @returns {String}
         */
        getFirstSentence: function(content) {
            var firstSentenceRegExp = new RegExp(
                '^([^' + this.previewSettings.sentenceEndCharacters
                + ']+[' + this.previewSettings.sentenceEndCharacters + ']+)'),
                firstSentence = content.match(firstSentenceRegExp);
            if (firstSentence) {
                return firstSentence.shift();
            } else {
                return '';
            }
        }
    });
});
