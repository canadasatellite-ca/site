<?php

namespace CanadaSatellite\DynamicsIntegration\DynamicsCrm;

class OrderModel {
	/** General */

	/**
	* Customer.
	*/
	private $customerid;

	/**
	* Account Number.
	*/
	private $cs_accountnumber;

	/**
	* Order #.
	*/
	private $name;

	/**
	* Price List.
	*/
	private $pricelevelid;

	/**
	* Order Status.
	*/
	private $new_orderstatus;

	/**
	* Order Date.
	*/
	private $cs_orderdate;

	/**
	* Shipping Date.
	*/
	private $new_shippingdate;

	/**
	* Currency.
	*/
	private $transactioncurrencyid;

	/**
	* Approval Code.
	*/
	private $new_approvalcode;

	// Bill To.

	/**
	* Name.
	*/
	private $billto_name;

	/**
	* Organization.
	*/
	private $billto_contactname;

	/**
	* Street 1.
	*/
	private $billto_line1;

	/**
	* Street 2.
	*/
	private $billto_line2;

	/**
	* Street 3.
	*/
	private $billto_line3;

	/**
	* City.
	*/
	private $billto_city;

	/**
	* State/Province.
	*/
	private $billto_stateorprovince;

	/**
	* ZIP/Postal Code.
	*/
	private $billto_postalcode;

	/**
	* Country/Region.
	*/
	private $billto_country;

	/**
	* Phone.
	*/
	private $billto_telephone;

	/**
	* Fax.
	*/
	private $billto_fax;

	// Shipping

	/**
	* Ship To.
	*/
	private $willcall;

	/**
	* Name.
	*/
	private $shipto_name;

	/**
	* Organization.
	*/
	private $shipto_contactname;

	/**
	* Street 1.
	*/
	private $shipto_line1;

	/**
	* Street 2.
	*/
	private $shipto_line2;

	/**
	* Street 3.
	*/
	private $shipto_line3;

	/**
	* City.
	*/
	private $shipto_city;

	/**
	* State/Province.
	*/
	private $shipto_stateorprovince;

	/**
	* ZIP/Postal Code.
	*/
	private $shipto_postalcode;

	/**
	* Country/Region.
	*/
	private $shipto_country;

	/**
	* Phone.
	*/
	private $shipto_telephone;

	/**
	* Fax.
	*/
	private $shipto_fax;
}