<?php

namespace CanadaSatellite\DynamicsIntegration\DynamicsCrm;

class ProductModel {
	/**
	* Hidden field.
	*/
	private $iskit;

	/**
	* Hidden field.
	*/
	private $productstructure;

	/**
	 * Quantity On Hand.
	 */
	private $quantityonhand;

	/* General */

	/**
	* Name.
	*/
	private $name;

	/**
	* Code.
	*/
	private $productnumber;

	/**
	* List Price.
	*/
	private $price;

	/**
	* List Price (C$).
	*/
	private $price_base;

	/**
	* Unit Group.
	*/
	private $defaultuomscheduleid;

	/**
	* Default Unit.
	*/
	private $defaultuomid;

	/**
	* Volusion Id.
	*/
	private $cs_volusionid;

	/**
	* Currency.
	*/
	private $transactioncurrencyid;

	/**
	* Bundle?.
	*/
	private $new_bundle;

	/**
	* Stock Item.
	*/
	private $isstockitem;

	/**
	* Actual Inventory.
	*/
	private $new_actualinventory;

	/**
	* Last Inventory Day.
	*/
	private $new_lastinventoryday;

	/**
	* URL.
	*/
	private $producturl;

	/**
	* Is Synced.
	*/
	private $cs_issynced;

	/**
	* Last Updated.
	*/
	private $new_lastupdated;

	/**
	* Track Device.
	*/
	private $new_trackdevice;

	// Costs

	// Base Cost
	private $new_usdcost;

	// Shipping Cost
	private $new_shippingcost;

	// Currency Exchange 2%
	private $new_currencyexchange;

	// Processing Fees 3%
	private $new_processingfees;

	// Standard Cost
	private $standardcost;

	// Current Cost
	private $currentcost;

	// Profit ($)
	private $new_profit;

	// Exchange Rate
	private $exchangerate;

	// Margin (%)
	private $new_margin;

	// Cost Updated
	private $new_costupdated;

	// Vendor Description

	// Shipping Weight
	private $stockweight;

	// Product Description
	private $new_description;

	// Quote Description
	private $new_quotedescription;

	// Vendor Description
	private $description;

	// Vendor Name
	private $vendorpartnumber;

	// Network
	private $new_network;

	// Product Category
	private $new_productcategory;

	// Service Type
	private $new_service;

	// Meta Tag Title
	private $new_metatagtitle;

	// Meta Tag Description
	private $new_metatagdescription;

	// Meta Tag Keywords
	private $new_metatagkeywords10k;

	// Manufacturer
	private $new_manufacturerlookup;

	// Manufacturer Part #
	private $new_manufacturerpart;

	// Country Of Origin
	private $new_countryoforiginlookup;

	// Price List Pgae
	private $new_pricelistpage;

	// Vendor
	private $cs_vendorid;

	// Warranty
	private $new_warranty;

	// UPC
	private $new_upc;

	// Length (cm)
	private $new_lengthcm;

	// Width (cm)
	private $new_widthcm;

	// Height (cm)
	private $new_heightcm;
}