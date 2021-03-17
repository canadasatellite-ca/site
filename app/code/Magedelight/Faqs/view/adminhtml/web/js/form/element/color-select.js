/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

/* global $break $ FORM_KEY */

define([
    'underscore',
    'Magento_Ui/js/lib/view/utils/async',
    'mage/template',
    'uiRegistry',
    'prototype',
    'Magento_Ui/js/form/element/abstract',
    'jquery/colorpicker/js/colorpicker',
    'jquery/ui'
], function (_, jQuery, mageTemplate, rg, prototype, Abstract) {
    'use strict';

    /**
     * Former implementation.
     *
     * @param {*} value
     * @param {Object} container
     * @param {String} elementName
     */
    function oldCode(value, container, elementName) {
        var swatchVisualOption = {
            itemCount: 0,
            totalItems: 0,
            rendered: 0,
            isReadOnly: false,

            /**
             * Initialize.
             */
            initialize: function () {
                if (_.isEmpty(value)) {
                    container.addClassName('unavailable');
                } else {
                    jQuery(container).find('.question_window').css('background', value);
                }
                    
                jQuery(container).on(
                    'click',
                    '.colorpicker_handler',
                    this.initColorPicker
                );
            },

            /**
             * ColorPicker initialization process
             */
            initColorPicker: function () {
                var element = this,
                    hiddenColorPicker = !jQuery(element).data('colorpickerId');
                
                    
                jQuery(this).ColorPicker({

                    /**
                     * ColorPicker onShow action
                     */
                    onShow: function () {
                        var color = jQuery(element).parent().parent().prev().prev('input').val(),
                            menu = jQuery(this).parents('.swatch_sub-menu_container');

                        menu.show();
                        jQuery(element).ColorPickerSetColor(color);
                    },
                    
                    /**
                     * ColorPicker onSubmit action
                     *
                     * @param {String} hsb
                     * @param {String} hex
                     * @param {String} rgb
                     * @param {String} el
                     */
                    onSubmit: function (hsb, hex, rgb, el) {
                        var localContainer = jQuery(el).parent().parent().prev();
                        jQuery(el).ColorPickerHide();
                        localContainer.parent().removeClass('unavailable');
                        localContainer.prev('input').val('#' + hex).trigger('change');
                        localContainer.css('background', '#' + hex);
                    }
                });

                if (hiddenColorPicker) {
                    jQuery(this).ColorPickerShow();
                }
               
            }
        };

        //swatchVisualOption.initColorPicker();

        jQuery('body').on('click', function (event) {
            var element = jQuery(event.target);

            if (
                element.parents('.swatch_sub-menu_container').length === 1 ||
                element.next('div.swatch_sub-menu_container').length === 1
            ) {
                return true;
            }
            jQuery('.swatch_sub-menu_container').show();
        });

        jQuery(function ($) {

            var swatchComponents = {

                /**
                 * div wrapper for to hide all evement
                 */
                wrapper: null,

                /**
                 * form component for upload image
                 */
                form: null,

                /**
                 * Input file component for upload image
                 */
                inputFile: null,

                /**
                 * Create swatch component for upload files
                 *
                 * @this {swatchComponents}
                 * @public
                 */
                create: function () {
                    this.wrapper = $('<div>').css({
                        display: 'none'
                    }).appendTo($('body'));
                    $('<input />', {
                        type: 'hidden',
                        name: 'form_key',
                        value: FORM_KEY
                    }).appendTo(this.form);
                }
            };

            swatchVisualOption.initialize();

            /**
             * Create swatch components
             */
            swatchComponents.create();

            
        });
    }

    return Abstract.extend({
        defaults: {
            elementId: 0,
            elementName: '',
        },

        /**
         * Parses options and merges the result with instance
         *
         * @returns {Object} Chainable.
         */
        initConfig: function () {
            this._super();
            this.configureDataScope();

            return this;
        },

        /**
         * Initialize.
         *
         * @returns {Object} Chainable.
         */
        initialize: function () {
            this._super()
                    .initOldCode();

            return this;
        },

        /**
         * Initialize wrapped former implementation.
         *
         * @returns {Object} Chainable.
         */
        initOldCode: function () {
            jQuery.async('.' + this.elementName, function (elem) {
                oldCode(this.value(), elem.parentElement, this.elementName);
            }.bind(this));
            
            return this;
        },

        /**
         * Configure data scope.
         */
        configureDataScope: function () {
           this.elementName = this.index;
        }
    });
});
