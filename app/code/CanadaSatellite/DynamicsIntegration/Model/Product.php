<?php

namespace CanadaSatellite\DynamicsIntegration\Model;

class Product
{
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

	public function __construct(
		$id,
		$name,
		$sku,
		$upc,
		$price,
		$priceUsd,
		$cost,
		$weight,

		$qty,
		$url,
		$description,
		$quoteDescription,
		$network,
		$category,
		$service,

		$brand,
		$partNo,
		$countryOfOrigin,
		$priceListPage,
		$warranty,
		$vendor,
		$vendorCurrency,
		$vendorDescription,
		$vendorPart,

		$metaTitle,
		$metaDescription,
		$metaKeyword,

		$shippingLength,
		$shippingWidth,
		$shippingHeight,

		$specialPrice,
		$specialPriceUsd
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

	public function getId()
	{
		return $this->id;
	}

	public function getName()
	{
		return $this->name;
	}

	public function getSku()
	{
		return $this->sku;
	}

	public function getUpc()
	{
		return $this->upc;
	}

	public function getPrice()
	{
		return $this->price;
	}

	public function hasPriceUsd()
	{
		return $this->priceUsd !== null;
	}

	public function getPriceUsd()
	{
		return $this->priceUsd;
	}

	public function getCost()
	{
		return $this->cost;
	}

	public function getWeight()
	{
		return $this->weight;
	}


	public function getQty()
	{
		return $this->qty;
	}

	public function getUrl()
	{
		return $this->url;
	}

	public function getDescription()
	{
		return $this->description;
	}

	public function getQuoteDescription()
	{
		return $this->quoteDescription;
	}

	public function getNetwork()
	{
		return $this->network;
	}

	public function getCategory()
	{
		return $this->category;
	}

	public function getService()
	{
		return $this->service;
	}

	public function getBrand()
	{
		return $this->brand;
	}

	public function getPartNo()
	{
		return $this->partNo;
	}

	public function getCountryOfOrigin()
	{
		return $this->countryOfOrigin;
	}

	public function getPriceListPage()
	{
		return $this->priceListPage;
	}

	public function getWarranty()
	{
		return $this->warranty;
	}

	public function getVendor()
	{
		return $this->vendor;
	}

	public function getVendorCurrency()
	{
		if ($this->vendorCurrency === null) {
			return 'CAD';
		}
		
		return $this->vendorCurrency;
	}

	public function getVendorDescription()
	{
		return $this->vendorDescription;
	}

	public function getVendorPart()
	{
		return $this->vendorPart;
	}


	public function getMetaTitle()
	{
		return $this->metaTitle;
	}

	public function getMetaDescription()
	{
		return $this->metaDescription;
	}

	public function getMetaKeyword()
	{
		return $this->metaKeyword;
	}


	public function getShippingLength()
	{
		return $this->shippingLength;
	}

	public function getShippingWidth()
	{
		return $this->shippingWidth;
	}

	public function getShippingHeight()
	{
		return $this->shippingHeight;
	}

	public function getSpecialPrice()
	{
		return $this->specialPrice;
	}

	public function getSpecialPriceUsd()
	{
		return $this->specialPriceUsd;
	}

	public function hasSpecialPriceUsd()
	{
		return $this->specialPriceUsd !== null;
	}

	public function getShippingCost()
	{
		// In base currency, CAD.
		return $this->shippingCost;
	}

	public function setShippingCost($shippingCost)
	{
		// In base currency, CAD.
		$this->shippingCost = $shippingCost;
	}

	public function getCurrentCost()
	{
		// In base currency, CAD.
		return $this->currentCost;
	}

	public function setCurrentCost($currentCost)
	{
		// In base currency, CAD.
		$this->currentCost = $currentCost;
	}
}
