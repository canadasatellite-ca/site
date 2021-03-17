/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'jquery',
    'underscore',
    'uiComponent',
    'mageUtils',
    'mage/template',
    'mage/translate',
    'Magento_Checkout/js/checkout-data',
    'uiRegistry',
    'Magento_Checkout/js/model/quote',
    'Magento_Customer/js/model/address-list',
    'Magento_Checkout/js/model/address-converter',
    'Magento_Checkout/js/model/shipping-address/form-popup-state',
    'Magento_Ui/js/modal/modal',
    'mage/cookies'
], function (
    $,
    _,
    Component,
    utils,
    mageTemplate,
    $t,
    checkoutData,
    uiRegistry,
    quote,
    addressList,
    addressConverter,
    formPopUpState
) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'Mageside_CanadaPostShipping/checkout/delivery',
            isD2poSelected: false,
            isD2poDisabled: true,
            canDeliveryToPostOffice: false,
            listens: {
                isD2poSelected: 'toggleD2po'
            },
            map: null,
            geocoder: null,
            infoWindow: null,
            myLocation: null,
            markers: {},
            markerIds: [],
            selectedMarker: null,
            addressCached: {},
            selectedOffice: false,
            officesCached: {},
            canadaD2poId: null,
            isFormPopUpVisible: formPopUpState.isVisible,
            isFormInline: addressList().length === 0,
            addressList: addressList(),
            quote: quote,
            options: {
                width: 800,
                getOfficesUrl: '',
                getOfficeDetailUrl: '',
                mapSelector: '#canadapost-map',
                modalSelector: '#canadapost-find-offices-map',
                availableMethods: [],
                officeTemplate:  '<li id="<%= id %>" style="cursor:pointer; padding:5px; margin:0">' +
                        '<strong><%= name %></strong><br>' +
                        '<text><%= address %></text><br>' +
                        '<text><%= city %> <%= region %> <%= postcode %></text>' +
                    '</li>',
                map: {
                    center: {lat: 61.8302323, lng: -101.1052586},
                    zoom: 14,
                    infoTemplate: '<div>' +
                            '<strong><%= name %></strong><br>' +
                            '<text><%= address %></text><br>' +
                            '<text><%= city %> <%= region %> <%= postcode %></text><br><br>' +
                            '<% if (operation && operation.length > 0) { %>' +
                            '<table cellspacing="0" cellpadding="0" width="100%">' +
                                '<tbody>' +
                                '<% _.each(operation, function(item) { %>' +
                                    '<tr>' +
                                        '<td style="padding:0;width:25%;"><%= item.day %></td>' +
                                        '<td style="padding:0;width:25%;text-align:center;"><%= item.time.from %></td>' +
                                        '<td style="padding:0;width:25%;text-align:center;"> - </td>' +
                                        '<td style="padding:0;width:25%;text-align:center;"><%= item.time.to %></td></tr>' +
                                '<% }); %>' +
                                '</tbody>' +
                            '</table><br>' +
                            '<% } %>' +
                            '<a href="https://www.canadapost.ca/cpotools/apps/fpo/personal/findPostOfficeDetail?outletId=<%= id %>" target="_blank">' +
                            '<%= $t("View details and directions") %>' +
                            '</a>' +
                        '</div>',
                    myLocationTemplate: '<div>' +
                            '<strong><%= $t("My Location") %></strong><br>' +
                            '<text><%= address %></text>' +
                        '</div>',
                    markerIcon: {
                        office: 'https://www.canadapost.ca/cpo/mc/assets/images/app/fpo/pin-legend-cpc.png',
                        my: 'https://www.canadapost.ca//cpo/mc/assets/images/app/fpo/pin-legend-search.png'
                    }
                }
            }
        },

        initialize: function () {
            this._super();
            var self = this;

            uiRegistry.async('checkoutProvider')(function (checkoutProvider) {
                checkoutProvider.on('shippingAddress', function (shippingAddressData) {
                    var canD2po = shippingAddressData.country_id === 'CA';
                    self.canDeliveryToPostOffice(canD2po);
                    if (shippingAddressData.d2po) {
                        self.addressCached = shippingAddressData.d2po.addressCached;
                        self.isD2poDisabled(shippingAddressData.d2po.isD2poDisabled);
                        self.selectedOffice(shippingAddressData.d2po.selectedOffice);
                        self.isD2poSelected(shippingAddressData.d2po.isD2poSelected);
                    }
                });
            });

            quote.shippingMethod.subscribe(function (shippingMethod) {
                var canadaD2poId = self.getCanadaD2poId(),
                    isEnabled = false,
                    isSelectedCache = self.isD2poSelected();
                if (shippingMethod) {
                    isEnabled = shippingMethod['carrier_code'] === "canadapost"
                        && _.contains(self.options.availableMethods, shippingMethod['method_code']);
                }

                self.isD2poDisabled(!isEnabled);
                if (!isEnabled || (isEnabled && canadaD2poId)) {
                    self.isD2poSelected(isEnabled);
                }

                if (isEnabled && isSelectedCache && canadaD2poId && self.canadaD2poId !== canadaD2poId) {
                    self.getSelectedOfficeById(canadaD2poId);
                }
                if (!canadaD2poId) {
                    self.isD2poSelected(false);
                    self.selectedOffice(false);
                }
                self.canadaD2poId = canadaD2poId;
            });
        },

        initObservable: function () {
            this._super().observe(['isD2poSelected', 'isD2poDisabled', 'selectedOffice', 'canDeliveryToPostOffice']);
            return this;
        },

        initConfig: function () {
            var uid = utils.uniqueid();
            this._super();
            _.extend(this, {uid: uid});

            return this;
        },

        toggleD2po: function () {
            if (this.isD2poSelected()) {
                this.findOffice();
            } else {
                if (this.isFormInline) {
                    var address = $.extend(true, {}, this.addressCached, {
                        'custom_attributes': {
                            'canada_dpo_id': {
                                'attribute_code': 'canada_dpo_id',
                                'value': ''
                            }
                        }
                    });
                    this.setShippingAddress(address);
                }
            }
        },

        findOffice: function (canadaD2poId) {
            var office = this.selectedOffice();
                canadaD2poId = canadaD2poId || this.getCanadaD2poId();
            if (office && !canadaD2poId) {
                var postcode = office.postcode.replace(/([\w]{3})([\w]{3})/, '$1 $2');
                var address = {
                    'city': office.city,
                    'postcode': postcode,
                    'street': {0: office.address},
                    'region_id': office.region_id,
                    'country_id': 'CA',
                    'custom_attributes': {
                        'canada_dpo_id': {
                            'attribute_code': 'canada_dpo_id',
                            'value': office.id
                        }
                    }
                };
                this.setShippingAddress(address);
            } else if (office && office.id === canadaD2poId) {
                return null;
            } else if (canadaD2poId) {
                this.getSelectedOfficeById(canadaD2poId);
            } else {
                this.changeOffice();
            }
        },

        changeOffice: function () {
            var shippingAddress = this.getShippingAddress();
            this.addressCached = shippingAddress;
            _.each(this.markers, function(marker) {
                marker.setMap(null);
            });
            this.markers = [];
            this.loadMarkersData({post_code: shippingAddress.postcode});
        },

        confirmOffice: function () {
            if (this.selectedMarker) {
                var postcode = this.selectedMarker.postcode.replace(/([\w]{3})([\w]{3})/, '$1 $2');
                var address = {
                    'city': this.selectedMarker.city,
                    'postcode': postcode,
                    'street': {0: this.selectedMarker.address},
                    'region_id': this.selectedMarker.region_id,
                    'country_id': 'CA',
                    'custom_attributes': {
                        'canada_dpo_id': {
                            'attribute_code': 'canada_dpo_id',
                            'value': this.selectedMarker.id
                        }
                    }
                };
                this.selectedOffice(this.selectedMarker);
                this.setShippingAddress(address);
                this.closeModal();
            } else {
                this.showMessages(
                    '<div class="message message-error error">' +
                        '<div data-ui-id="messages-message-error">' + $t('Please select an office.') + '</div>' +
                    '</div>'
                );
            }
        },

        getShippingAddress: function () {
            var shippingAddress;
            if (!this.isFormInline) {
                shippingAddress = addressConverter.quoteAddressToFormAddressData(
                    this.quote.shippingAddress()
                );
            }
            if (!shippingAddress) {
                shippingAddress = uiRegistry.get('checkoutProvider').get('shippingAddress');
            }

            if (shippingAddress.postcode) {
                shippingAddress.postcode = shippingAddress.postcode.toUpperCase();
            }

            return shippingAddress;
        },

        setShippingAddress: function (shippingAddressData) {
            var self = this;
            if (!self.selectedOffice()) {
                return;
            }

            var checkoutProvider = uiRegistry.get('checkoutProvider');
            var shippingAddress = this.getShippingAddress();
            var newAddress = $.extend(true,
                {
                    company: '',
                    street: {1: ''}
                },
                shippingAddress,
                shippingAddressData,
                {
                    d2po: {
                        isD2poSelected: self.isD2poSelected(),
                        isD2poDisabled: self.isD2poDisabled(),
                        selectedOffice: self.selectedOffice(),
                        addressCached: self.addressCached
                    }
                });
            checkoutProvider.set('shippingAddress', newAddress);

            if (!this.isFormInline) {
                this.isFormPopUpVisible(true);
            }
        },

        getCanadaD2poId: function () {
            var shippingAddress = this.getShippingAddress();
            if (shippingAddress['custom_attributes']
                && shippingAddress['custom_attributes']['canada_dpo_id']['value']
            ) {
                return shippingAddress['custom_attributes']['canada_dpo_id']['value'];
            }
            return false;
        },

        openModal: function () {
            this.getModal().modal('openModal');
            this.renderMap();

            var office = this.selectedOffice();
            if (office && office.hasOwnProperty('id')) {
                this.triggerMarkerClick({currentTarget: {id: office.id}});
            }
        },

        closeModal: function () {
            this.getModal().modal('closeModal');
            if (!this.selectedOffice()) {
                this.isD2poSelected(false);
            }
        },

        getModal: function () {
            if (!this.modal) {
                var self = this;
                this.modal = $(this.options.modalSelector).modal({
                    "responsive": true,
                    "innerScroll": true,
                    "modalCloseBtnHandler": function () {
                        self.closeModal();
                    },
                    "buttons": [
                        {
                            text: $t('Confirm'),
                            class: "action primary",
                            click: function () {
                                self.confirmOffice();
                            }
                        },
                        {
                            text: $t('Cancel'),
                            class: "action secondary",
                            click: function () {
                                self.closeModal();
                            }
                        }
                    ]
                });
            }

            return this.modal;
        },

        renderMap: function () {
            if (window.google && window.google.maps) {
                this.initMap();
                this.addMarkers();
                this.getMyLocation();
            }
        },

        initMap:function () {
            if (!this.map) {
                var $mapBlock = $(this.options.mapSelector);
                if ($mapBlock.length) {
                    this.map = new google.maps.Map($mapBlock[0], {});
                    this.geocoder = new google.maps.Geocoder();
                    this.setCenter(this.options.map.center.lat, this.options.map.center.lng)
                        .setZoom(this.options.map.zoom);
                    this.infoWindow = new google.maps.InfoWindow;
                }
            }
        },

        getMyLocation: function () {
            var self = this;

            if (self.myLocation) {
                self.myLocation.setMap(null);
                self.myLocation = null;
            }

            var postCode = this.getShippingAddress().postcode;

            if (this.map && postCode) {
                if (self.markers.length > 0) {
                    self.map.setCenter(self.markers[0].position);
                }

                this.geocoder.geocode( {'address': postCode}, function(results, status) {
                    if (status === "OK") {
                        self.map.setCenter(results[0].geometry.location);
                        self.myLocation = new google.maps.Marker({
                            map: self.map,
                            position: results[0].geometry.location,
                            icon: self.options.map.markerIcon.my
                        });
                        self.myLocation.addListener('click', $.proxy(function () {
                            self.infoWindow.setContent(
                                mageTemplate(
                                    self.options.map.myLocationTemplate,
                                    {
                                        $t: $t,
                                        address: results[0].formatted_address
                                    }
                                )
                            );
                            self.infoWindow.open(self.map, self.myLocation);
                        }, this));
                    } else {
                        self.myLocation = null;
                    }
                });
            }
        },

        setCenter:function (lat, lng) {
            this.options.map.center = {lat: lat, lng: lng};
            if (this.map) {
                var center = new google.maps.LatLng(
                    parseFloat(lat),
                    parseFloat(lng)
                );
                this.map.setCenter(center);
            }
            return this;
        },

        setZoom:function (zoom) {
            this.options.map.zoom = zoom;
            if (this.map) {
                this.map.setZoom(parseInt(zoom));
            }
            return this;
        },

        addMarkers: function () {
            var template = this.options.officeTemplate;
            this.getModal().find('.offices-list ul').html('');
            this.markers = [];
            _.each(this.markerIds, function (id) {
                var office = this.officesCached[id];
                var marker = this.createMarker(office);
                if (marker) {
                    this.markers.push(marker);
                }
                this.getModal().find('.offices-list ul').append(mageTemplate(template, office));
                $('#' + office.id).on('click', $.proxy(this.triggerMarkerClick, this));
            }, this);
        },

        triggerMarkerClick: function (event) {
            var id = event.currentTarget.id;
            var marker = _.find(this.markers, function(item) {
                return item.officeId === id;
            });
            if (marker) {
                window.google.maps.event.trigger(marker, 'click');
            }
        },

        createMarker: function (markerData) {
            var self = this;
            var marker = false;
            var point = new google.maps.LatLng(
                parseFloat(markerData.position.lat),
                parseFloat(markerData.position.lng)
            );
            marker = new google.maps.Marker({
                map: this.map,
                position: point,
                label: markerData.label,
                officeId: markerData.id,
                icon: self.options.map.markerIcon.office
            });
            marker.addListener('click', $.proxy(function () {
                self.getMarkerInfoContent(marker);
                self.setSelectedMarker(marker.officeId);
            }, this));

            return marker;
        },

        loadMarkersData: function (data) {
            var self = this;
            data = data || {};
            $.extend(data, {
                'form_key': $.mage.cookies.get('form_key')
            });

            $.ajax({
                url: this.options.getOfficesUrl,
                data: data,
                type: 'post',
                dataType: 'json',
                context: this,
                beforeSend: function() {
                    $('body').trigger('processStart');
                },
                success: function(response) {
                    $('body').trigger('processStop');
                    if (!response.error && response.offices && response.offices.length > 0) {
                        self.markerIds = [];
                        _.each(response.offices, function (office) {
                            if (!this.officesCached[office.id]) {
                                this.officesCached[office.id] = office;
                            }
                            this.markerIds.push(office.id);
                        }, self);
                    }
                    if (response.messages) {
                        self.showMessages(response.messages);
                    }
                    self.openModal();
                }
            });
        },

        getMarkerInfoContent: function (marker) {
            var self = this,
                officeId = marker.officeId;

            if (self.officesCached[officeId] && self.officesCached[officeId]['operation']) {
                $.extend(self.officesCached[officeId], {$t: $t});
                self.infoWindow.setContent(mageTemplate(self.options.map.infoTemplate, self.officesCached[officeId]));
                self.infoWindow.open(this.map, marker);
            } else {
                var data = {
                    'form_key': $.mage.cookies.get('form_key'),
                    'office_id': officeId
                };
                $.ajax({
                    url: this.options.getOfficeDetailUrl,
                    data: data,
                    type: 'post',
                    dataType: 'json',
                    context: this,
                    beforeSend: function () {
                        $('body').trigger('processStart');
                    },
                    success: function (response) {
                        $('body').trigger('processStop');
                        if (!response.error && response.office) {
                            self.officesCached[officeId] = response.office;
                            marker.officeDetail = response.office;
                            $.extend(response.office, {$t: $t});
                            self.infoWindow.setContent(mageTemplate(self.options.map.infoTemplate, response.office));
                            self.infoWindow.open(self.map, marker);
                        }
                    }
                });
            }
        },

        getSelectedOfficeById: function (id) {
            if (this.officesCached[id]) {
                this.selectedOffice(this.officesCached[id]);
            } else {
                var self = this;
                var data = {
                    'form_key': $.mage.cookies.get('form_key'),
                    'office_id': id
                };
                $.ajax({
                    url: this.options.getOfficeDetailUrl,
                    data: data,
                    type: 'post',
                    dataType: 'json',
                    context: this,
                    beforeSend: function () {
                        $('body').trigger('processStart');
                    },
                    success: function (response) {
                        $('body').trigger('processStop');
                        if (!response.error && response.office) {
                            self.selectedOffice(response.office);
                        }
                        self.officesCached[id] = response.office;
                    }
                });
            }
        },

        setSelectedMarker: function (markerId) {
            if (this.officesCached[markerId]) {
                this.selectedMarker = this.officesCached[markerId];
                this.showMessages();
            }

            this.getModal().find('.offices-list ul li').css('background-color', '');
            $('#' + markerId).css('background-color', 'lightgray');
        },

        showMessages: function (messages) {
            if (messages) {
                this.getModal().find('.messages').html(messages);
            } else {
                this.getModal().find('.messages').html('');
            }
        }
    });
});
