/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

define([
    'Magento_Ui/js/grid/columns/column'
], function (Column) {
    'use strict';

    return Column.extend({

        defaults:{
            attachmentIdentifierClass:'preview-link'
        },

        /**
         * Returns path to the files' preview image.
         *
         * @param {Object} file
         * @returns {String}
         */
        getFilePreview: function (file) {
            return file.url;
        },

        /**
         * Handler of the preview image load event
         *
         * @param {Object} file - File associated with an image
         * @param {Event} e
         */
        onPreviewLoad: function (file, e) {
            var img = e.currentTarget;

            file.previewWidth = img.naturalWidth;
            file.previewHeight = img.naturalHeight;
        },

        /**
         * Get simplified file type
         *
         * @param {Object} file - File to be checked
         * @returns {String}
         */
        getFilePreviewType: function (file) {
            var type;

            if (file.previewType) {
                return file.previewType;
            }
            if (!file.type) {
                return 'document';
            }

            type = file.type.split('/')[0];
            file.previewType = type !== 'image' && type !== 'video' ? 'document' : type;

            return file.previewType;
        },

        /**
         * Get file title
         *
         * @param {Object} file - File to process
         * @returns {String}
         */
        getFileTitle: function (file) {
            var fileTitle = '';

            if (this.getFilePreviewType(file) === 'image') {
                fileTitle = file.image_title;
            }
            return fileTitle;
        },

        /**
         * Get classes for anchor tag over review image
         *
         * @param {Object} file - File to process
         * @returns {String}
         */
        getImageAnchorClasses: function(file){
            var imageAnchorClasses='preview-' + this.getFilePreviewType(file);
            imageAnchorClasses+= ' '+this.attachmentIdentifierClass;
            return imageAnchorClasses;
        }
    });
});
