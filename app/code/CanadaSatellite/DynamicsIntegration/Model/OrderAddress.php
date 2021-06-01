<?php

namespace CanadaSatellite\DynamicsIntegration\Model;

class OrderAddress
{
	private $firstname;
	private $lastname;
	private $company;
	private $street;
	private $city;
	private $region;
	private $postcode;
	private $country;
	private $phone;
	private $fax;

	function __construct(
		$firstname,
		$lastname,
		$company,
		$street,
		$city,
		$region,
		$postcode,
		$country,
		$phone,
		$fax
	) {
		$this->firstname = $firstname;
		$this->lastname = $lastname;
		$this->company = $company;
		$this->street = $street;
		$this->city = $city;
		$this->region = $region;
		$this->postcode = $postcode;
		$this->country = $country;
		$this->phone = $phone;
		$this->fax = $fax;
	}

	function getFirstname()
	{
		return $this->firstname;
	}

	function getLastname()
	{
		return $this->lastname;
	}

	function getCompany()
	{
		return $this->company;
	}

	function getStreet()
	{
		return $this->street;
	}

	function getCity()
	{
		return $this->city;
	}

	function getRegion()
	{
		return $this->region;
	}

	function getPostcode()
	{
		return $this->postcode;
	}

	function getCountry()
	{
		return $this->country;
	}

	function getPhone()
	{
		return $this->phone;
	}

	function getFax()
	{
		return $this->fax;
	}
}
