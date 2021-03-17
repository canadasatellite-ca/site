/*
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

var newUploadRow;
define([
    'jquery'
], function ($) {
    'use strict';

    return function () {
        var fileRowNumber = 1;

        /**
         * New file upload row
         *
         */
        newUploadRow = function () {
            var div = document.getElementById("fileUpload");
            var divTitle = document.createElement("div");
            var divContentText = document.createElement("div");
            var divContentFile = document.createElement("div");
            var text = document.createElement("input");
            var file = document.createElement("input");

            divTitle.setAttribute("class", "upload-row clearfix");
            text.setAttribute("type", "text");
            text.setAttribute("name", "title_" + fileRowNumber);
            file.setAttribute("type", "file");
            file.setAttribute("name", "fileupload_" + fileRowNumber);
            file.setAttribute("class", "fileupload-file required-entry");
            divContentText.setAttribute("class", "upload-row-title");
            divContentText.appendChild(text);
            divContentFile.setAttribute("class", "upload-row-file");
            divContentFile.appendChild(file);
            divTitle.appendChild(divContentText);
            divTitle.appendChild(divContentFile);
            div.appendChild(divTitle);

            fileRowNumber = (fileRowNumber + 1);
        };

        /**
         * Wrap Quote content
         */
        var uploadedFiles = $('.uploaded-files');
        $('.cart-summary, .file-upload-container').wrapAll("<div class='quote-details-container'></div>");
        uploadedFiles.addClass('empty');

        if ($('.uploaded-files li').length > 0) {
            uploadedFiles.removeClass('empty');
        }

        /**
         * Show message when file is selected
         */
        $('input[type=file]').change(function () {
            $('.upload-message').show();
        });
    };
});
