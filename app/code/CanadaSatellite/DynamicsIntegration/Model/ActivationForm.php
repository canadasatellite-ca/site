<?php

namespace CanadaSatellite\DynamicsIntegration\Model;

class ActivationForm
{
	private $id;
	private $email;
	private $firstName;
	private $lastName;
	private $companyName;
	private $simNumber;
	private $orderNumber;
	private $order;
	private $customer;
	private $desiredActivationDate;
	private $notes;
	private $completedDate;
	private $phoneNumber;
	private $dataNumber;
	private $expirationDate;
	private $comments;
	private $status;

	function __construct(
		$id,
		$email,
		$firstName,
		$lastName,
		$companyName,
		$simNumber,
		$orderNumber,
		$order,
		$customer,
		$desiredActivationDate,
		$notes,
		$completedDate,
		$phoneNumber,
		$dataNumber,
		$expirationDate,
		$comments,
		$status
	) {
		$this->id = $id;
		$this->email = $email;
		$this->firstName = $firstName;
		$this->lastName = $lastName;
		$this->companyName = $companyName;
		$this->simNumber = $simNumber;
		$this->orderNumber = $orderNumber;
		$this->order = $order;
		$this->customer = $customer;
		$this->desiredActivationDate = $desiredActivationDate;
		$this->notes = $notes;
		$this->completedDate = $completedDate;
		$this->phoneNumber = $phoneNumber;
		$this->dataNumber = $dataNumber;
		$this->expirationDate = $expirationDate;
		$this->comments = $comments;
		$this->status = $status;
	}

	function getId()
	{
		return $this->id;
	}

	function getEmail()
	{
		return $this->email;
	}

	function getFirstName()
	{
		return $this->firstName;
	}

	function getLastName()
	{
		return $this->lastName;
	}

	function getCompanyName()
	{
		return $this->companyName;
	}

	function getSimNumber()
	{
		return $this->simNumber;
	}

	function getOrderNumber()
	{
		return $this->orderNumber;
	}

	function getOrder()
	{
		return $this->order;
	}

	function getCustomer()
	{
		return $this->customer;
	}

	function getNotes()
	{
		return $this->notes;
	}

	function getDesiredActivationDate()
	{
		return $this->desiredActivationDate;
	}

	function getCompletedDate()
	{
		return $this->completedDate;
	}

	function getPhoneNumber()
	{
		return $this->phoneNumber;
	}

	function getDataNumber()
	{
		return $this->dataNumber;
	}

	function getExpirationDate()
	{
		return $this->expirationDate;
	}

	function getComments()
	{
		return $this->comments;
	}

	function getStatus()
	{
		return $this->status;
	}
}