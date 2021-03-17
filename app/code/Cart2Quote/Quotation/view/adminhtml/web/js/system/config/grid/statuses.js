/*
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

define(
    [
        'jquery',
        'jquery/ui',
        'uiComponent',
        'ko'
    ],
    function ($, ui, Component, ko) {
        'use strict';

        /**
         * This is the js model that is used for the grid in the Cart2Quote settings.
         */
        return Component.extend({
            defaults: {
                template: 'Cart2Quote_Quotation/system/config/grid/statuses'
            },

            /**
             * Address data as array of JSON objects
             */
            statusesFields: ko.observableArray(),

            /**
             * Complete parsed data
             */
            statusesData: null,

            /**
             * Config field name
             */
            name: null,

            /**
             * Config field label
             */
            label: null,

            /**
             * Config field Html ID
             */
            htmlId: null,

            /**
             * Config field saved data
             */
            initFieldValue: null,

            /**
             * Observable field container: mandatory for a observable array with observable vars
             *
             * @param label
             * @param name
             * @param limitation
             * @param locked
             * @param finalized
             */
            observableField: function (label, name, limitation, locked, finalized) {
                this.label = ko.observable(label);
                this.name = ko.observable(name);
                this.limitation = ko.observable(limitation);
                this.locked = ko.observable(locked);
                this.finalized = ko.observable(finalized);
            },

            /**
             * @return {exports.initObservable}
             */
            initObservable: function () {
                var self = this;
                this.initFields(this.data);
                var initialData = this.initFieldValue;

                if (initialData === null) {
                    initialData = this.getDefaultValues();
                } else {
                    //merge new elements in existing settings
                    if (initialData.length !== this.getDefaultValues().length) {
                        $.each(this.getDefaultValues(), function (defaultIndex, defaultValue) {
                            var found = false;
                            var defaultName = defaultValue.name;
                            $.each(initialData, function (index, value) {
                                var name = value.name;
                                if (name === defaultName) {
                                    found = true;
                                    return false; //break
                                }
                            });

                            //add new element to current settings
                            if (found === false) {
                                initialData.push(defaultValue);
                            }
                        });
                    }
                }

                this.statusesFields = ko.observableArray(
                    ko.utils.arrayMap(initialData, function (field) {
                        return new self.observableField(
                            field.label,
                            field.name,
                            field.limitation,
                            field.locked,
                            field.finalized,
                        );
                    })
                );

                return this;
            },

            /**
             * Get default field values
             *
             * @returns {*[]}
             */
            getDefaultValues: function () {
                var statuses = this.statuses;
                var defaultValues = [];
                for (var i = 0; i < statuses.length; ++i) {
                    defaultValues.push(
                    {
                        label: statuses[i].label,
                        name: statuses[i].value,
                        limitation: true,
                        locked: false,
                        finalized: false
                    });
                }

                return defaultValues;
            },

            /**
             * Initialize the fields
             *
             * @param data
             */
            initFields: function (data) {
                this.statusesData = ko.utils.parseJson(data);
                this.name = this.statusesData.field.name;
                this.label = this.statusesData.field.label;
                this.statuses = this.statusesData.field.statuses;
                this.htmlId = this.statusesData.field.htmlId;
                this.initFieldValue = ko.utils.parseJson(this.statusesData.field.fieldValue);
            },

            /**
             * Get the status fields
             * @returns {*}
             */
            getstatusesFields: function () {
                var self = this;
                return self.statusesFields();
            }
        });
    }
);
