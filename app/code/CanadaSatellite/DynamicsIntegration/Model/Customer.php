<?php

namespace CanadaSatellite\DynamicsIntegration\Model;

class Customer
{
	private $id;
	private $prefix;
	private $firstname;
	private $middlename;
	private $lastname;
	private $email;
	private $url;
	private $gender;
	private $birthDate;
	private $group;
	private $source;
	private $billingAddress;

	function __construct(
		$id,
		$prefix,
		$firstname,
		$middlename,
		$lastname,
		$email,
		$url,
		$gender,
		$birthDate,
		$group,
		$source,
		$billingAddress
	) {
		$this->id = $id;
		$this->prefix = $prefix;
		$this->firstname = $firstname;
		$this->middlename = $middlename;
		$this->lastname = $lastname;
		$this->email = $email;
		$this->url = $url;
		$this->gender = $gender;
		$this->birthDate = $birthDate;
		$this->group = $group;
		$this->source = $source;
		$this->billingAddress = $billingAddress;
	}

	function getId()
	{
		return $this->id;
	}

	function getPrefix()
	{
		return $this->prefix;
	}

	function getFirstname()
	{
		return $this->firstname;
	}

	function getMiddlename()
	{
		return $this->middlename;
	}

	function getLastname()
	{
		return $this->lastname;
	}

	function getEmail()
	{
		return $this->email;
	}

	function getUrl()
	{
		return $this->url;
	}

	function getGender()
	{
		return $this->gender;
	}

	function getBirthDate()
	{
		return $this->birthDate;
	}

	function getGroup()
	{
		return $this->group;
	}

	function getSource()
	{
		return $this->source;
	}

	function getCompany()
	{
		if ($this->billingAddress === null) {
			return null;
		}

		return $this->billingAddress->getCompany();
	}

	function getPhone()
	{
		if ($this->billingAddress === null) {
			return null;
		}

		return $this->billingAddress->getPhone();
	}

	function getFax() 
	{
		if ($this->billingAddress === null) {
			return null;
		}

		return $this->billingAddress->getFax();
	}

	function getBillingAddress()
	{
		return $this->billingAddress;
	}
}
