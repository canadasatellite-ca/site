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
                template: 'Cart2Quote_Quotation/system/config/grid/address'
            },

            /**
             * Address data as array of JSON objects
             */
            addressFields: ko.observableArray(),

            /**
             * Complete parsed data
             */
            addressData: null,

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
             * Make the element sortable
             *
             * @param e
             */
            makeSortable: function (e) {
                var self = this;
                $(e).sortable(
                    {
                        update: function (event) {
                            $('#' + self.id + ' .sort-order').each(function (index, element) {
                                $(element).val(index + 1);
                                $(element).trigger('change');
                            });
                        }
                    }
                );
            },

            /**
             * Observable field container: mandatory for a observable array with observable vars
             *
             * @param label
             * @param name
             * @param required
             * @param additionalCss
             * @param enabled
             * @param locked
             * @param sortOrder
             */
            observableField: function (label, name, required, additionalCss, enabled, locked, sortOrder) {
                this.label = ko.observable(label);
                this.name = ko.observable(name);
                this.required = ko.observable(required);
                this.additionalCss = ko.observable(additionalCss);
                this.enabled = ko.observable(enabled);
                this.locked = ko.observable(locked);
                this.sortOrder = ko.observable(sortOrder);
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

                initialData.sort(self.sortBySortOrder);

                this.addressFields()[this.id] = ko.observableArray(
                    ko.utils.arrayMap(initialData, function (field) {
                        return new self.observableField(
                            field.label,
                            field.name,
                            field.required,
                            field.additionalCss,
                            field.enabled,
                            field.locked,
                            field.sortOrder
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
                return [{
                    label: 'Prefix',
                    name: 'prefix',
                    required: false,
                    additionalCss: '',
                    enabled: false,
                    locked: false,
                    sortOrder: 5
                }, {
                    label: 'First Name',
                    name: 'firstname',
                    required: true,
                    additionalCss: '',
                    enabled: true,
                    locked: true,
                    sortOrder: 10
                }, {
                    label: 'Middlename',
                    name: 'middlename',
                    required: false,
                    additionalCss: '',
                    enabled: false,
                    locked: false,
                    sortOrder: 15
                }, {
                    label: 'Last Name',
                    name: 'lastname',
                    required: true,
                    additionalCss: '',
                    enabled: true,
                    locked: true,
                    sortOrder: 20
                }, {
                    label: 'Suffix',
                    name: 'suffix',
                    required: false,
                    additionalCss: '',
                    enabled: false,
                    locked: false,
                    sortOrder: 25
                }, {
                    label: 'Company',
                    name: 'company',
                    required: true,
                    additionalCss: '',
                    enabled: true,
                    locked: false,
                    sortOrder: 30
                }, {
                    label: 'Street Address',
                    name: 'street',
                    required: true,
                    additionalCss: '',
                    enabled: true,
                    locked: true,
                    sortOrder: 40
                }, {
                    label: 'City',
                    name: 'city',
                    required: true,
                    additionalCss: '',
                    enabled: true,
                    locked: true,
                    sortOrder: 50
                }, {
                    label: 'State/Province',
                    name: 'region_id',
                    required: true,
                    additionalCss: '',
                    enabled: true,
                    locked: true,
                    sortOrder: 60
                }, {
                    label: 'Zip/Postal Code',
                    name: 'postcode',
                    required: true,
                    additionalCss: '',
                    enabled: true,
                    locked: true,
                    sortOrder: 70
                }, {
                    label: 'Country',
                    name: 'country_id',
                    required: true,
                    additionalCss: '',
                    enabled: true,
                    locked: true,
                    sortOrder: 80
                }, {
                    label: 'Phone Number',
                    name: 'telephone',
                    required: true,
                    additionalCss: '',
                    enabled: true,
                    locked: false,
                    sortOrder: 90
                }, {
                    label: 'Tax/VAT Number',
                    name: 'vat_id',
                    required: false,
                    additionalCss: '',
                    enabled: false,
                    locked: false,
                    sortOrder: 100
                }, {
                    label: 'Fax Number',
                    name: 'fax',
                    required: false,
                    additionalCss: '',
                    enabled: false,
                    locked: false,
                    sortOrder: 110
                }];
            },

            /**
             * Initialize the fields
             *
             * @param data
             */
            initFields: function (data) {
                this.addressData = ko.utils.parseJson(data);
                this.name = this.addressData.field.name;
                this.label = this.addressData.field.label;
                this.htmlId = this.addressData.field.htmlId;
                this.initFieldValue = ko.utils.parseJson(this.addressData.field.fieldValue);
            },

            /**
             * Sort array
             *
             * @param value1
             * @param value2
             * @returns {number}
             */
            sortBySortOrder: function (value1, value2) {
                var sortOrder1 = parseInt(value1.sortOrder);
                var sortOrder2 = parseInt(value2.sortOrder);

                return ((sortOrder1 < sortOrder2) ? -1 : ((sortOrder1 > sortOrder2) ? 1 : 0));
            },

            /**
             * Get the address fields
             * @returns {*}
             */
            getAddressFields: function () {
                var self = this;
                return self.addressFields()[self.id];
            }
        });
    }
);
