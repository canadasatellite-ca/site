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

	public function __construct(
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

	public function getFirstname()
	{
		return $this->firstname;
	}

	public function getLastname()
	{
		return $this->lastname;
	}

	public function getCompany()
	{
		return $this->company;
	}

	public function getStreet()
	{
		return $this->street;
	}

	public function getCity()
	{
		return $this->city;
	}

	public function getRegion()
	{
		return $this->region;
	}

	public function getPostcode()
	{
		return $this->postcode;
	}

	public function getCountry()
	{
		return $this->country;
	}

	public function getPhone()
	{
		return $this->phone;
	}

	public function getFax()
	{
		return $this->fax;
	}
}
