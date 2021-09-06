<?php

namespace CanadaSatellite\DynamicsIntegration\Model;

class Product {
    private $id;
    private $name;
    private $sku;
    private $upc;
    private $price;
    private $priceUsd;
    private $cost;
    private $weight;

    private $qty;
    private $url;
    private $description;
    private $quoteDescription;
    private $network;
    private $category;
    private $service;

    private $brand;
    private $partNo;
    private $countryOfOrigin;
    private $priceListPage;
    private $warranty;
    private $vendor;
    private $vendorCurrency;
    private $vendorDescription;
    private $vendorPart;

    private $metaTitle;
    private $metaDescription;
    private $metaKeyword;

    private $shippingLength;
    private $shippingWidth;
    private $shippingHeight;

    private $specialPrice;
    private $specialPriceUsd;

    // Should be populated from product in CRM on product update round-back.
    private $shippingCost;
    private $currentCost;

    function __construct(
        $id, $name, $sku, $upc, $price, $priceUsd, $cost, $weight,

        $qty, $url, $description, $quoteDescription, $network, $category, $service,

        $brand, $partNo, $countryOfOrigin, $priceListPage, $warranty, $vendor, $vendorCurrency, $vendorDescription, $vendorPart,

        $metaTitle, $metaDescription, $metaKeyword,

        $shippingLength, $shippingWidth, $shippingHeight,

        $specialPrice, $specialPriceUsd
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->sku = $sku;
        $this->upc = $upc;
        $this->price = $price;
        $this->priceUsd = $priceUsd;
        $this->cost = $cost;
        $this->weight = $weight;

        $this->qty = $qty;
        $this->url = $url;
        $this->description = $description;
        $this->quoteDescription = $quoteDescription;
        $this->network = $network;
        $this->category = $category;
        $this->service = $service;

        $this->brand = $brand;
        $this->partNo = $partNo;
        $this->countryOfOrigin = $countryOfOrigin;
        $this->priceListPage = $priceListPage;
        $this->warranty = $warranty;
        $this->vendor = $vendor;
        $this->vendorCurrency = $vendorCurrency;
        $this->vendorDescription = $vendorDescription;
        $this->vendorPart = $vendorPart;

        $this->metaTitle = $metaTitle;
        $this->metaDescription = $metaDescription;
        $this->metaKeyword = $metaKeyword;

        $this->shippingLength = $shippingLength;
        $this->shippingWidth = $shippingWidth;
        $this->shippingHeight = $shippingHeight;

        $this->specialPrice = $specialPrice;
        $this->specialPriceUsd = $specialPriceUsd;
    }

    function getId() {
        return $this->id;
    }

    function getName() {
        return $this->name;
    }

    function getSku() {
        return $this->sku;
    }

    function getUpc() {
        return $this->upc;
    }

    function getPrice() {
        return $this->price;
    }

    function hasPriceUsd() {
        return $this->priceUsd !== null;
    }

    function getPriceUsd() {
        return $this->priceUsd;
    }

    function getCost() {
        return $this->cost;
    }

    function getWeight() {
        return $this->weight;
    }


    function getQty() {
        return $this->qty;
    }

    function getUrl() {
        return $this->url;
    }

    function getDescription() {
        return $this->description;
    }

    function getQuoteDescription() {
        return $this->quoteDescription;
    }

    function getNetwork() {
        return $this->network;
    }

    function getCategory() {
        return $this->category;
    }

    function getService() {
        return $this->service;
    }

    function getBrand() {
        return $this->brand;
    }

    function getPartNo() {
        return $this->partNo;
    }

    function getCountryOfOrigin() {
        return $this->countryOfOrigin;
    }

    function getPriceListPage() {
        return $this->priceListPage;
    }

    function getWarranty() {
        return $this->warranty;
    }

    function getVendor() {
        return $this->vendor;
    }

    function getVendorCurrency() {
        if ($this->vendorCurrency === null) {
            return 'CAD';
        }

        return $this->vendorCurrency;
    }

    function getVendorDescription() {
        return $this->vendorDescription;
    }

    function getVendorPart() {
        return $this->vendorPart;
    }


    function getMetaTitle() {
        return $this->metaTitle;
    }

    function getMetaDescription() {
        return $this->metaDescription;
    }

    function getMetaKeyword() {
        return $this->metaKeyword;
    }


    function getShippingLength() {
        return $this->shippingLength;
    }

    function getShippingWidth() {
        return $this->shippingWidth;
    }

    function getShippingHeight() {
        return $this->shippingHeight;
    }

    function getSpecialPrice() {
        return $this->specialPrice;
    }

    function getSpecialPriceUsd() {
        return $this->specialPriceUsd;
    }

    function hasSpecialPriceUsd() {
        return $this->specialPriceUsd !== null;
    }

    function getShippingCost() {
        // In base currency, CAD.
        return $this->shippingCost;
    }

    function setShippingCost($shippingCost) {
        // In base currency, CAD.
        $this->shippingCost = $shippingCost;
    }

    function getCurrentCost() {
        // In base currency, CAD.
        return $this->currentCost;
    }

    function setCurrentCost($currentCost) {
        // In base currency, CAD.
        $this->currentCost = $currentCost;
    }
}
