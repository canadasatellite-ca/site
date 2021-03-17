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

	public function __construct(
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

	public function getId()
	{
		return $this->id;
	}

	public function getPrefix()
	{
		return $this->prefix;
	}

	public function getFirstname()
	{
		return $this->firstname;
	}

	public function getMiddlename()
	{
		return $this->middlename;
	}

	public function getLastname()
	{
		return $this->lastname;
	}

	public function getEmail()
	{
		return $this->email;
	}

	public function getUrl()
	{
		return $this->url;
	}

	public function getGender()
	{
		return $this->gender;
	}

	public function getBirthDate()
	{
		return $this->birthDate;
	}

	public function getGroup()
	{
		return $this->group;
	}

	public function getSource()
	{
		return $this->source;
	}

	public function getCompany()
	{
		if ($this->billingAddress === null) {
			return null;
		}

		return $this->billingAddress->getCompany();
	}

	public function getPhone()
	{
		if ($this->billingAddress === null) {
			return null;
		}

		return $this->billingAddress->getPhone();
	}

	public function getFax() 
	{
		if ($this->billingAddress === null) {
			return null;
		}

		return $this->billingAddress->getFax();
	}

	public function getBillingAddress()
	{
		return $this->billingAddress;
	}
}
