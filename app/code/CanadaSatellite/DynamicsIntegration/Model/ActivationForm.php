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

	public function __construct(
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

	public function getId()
	{
		return $this->id;
	}

	public function getEmail()
	{
		return $this->email;
	}

	public function getFirstName()
	{
		return $this->firstName;
	}

	public function getLastName()
	{
		return $this->lastName;
	}

	public function getCompanyName()
	{
		return $this->companyName;
	}

	public function getSimNumber()
	{
		return $this->simNumber;
	}

	public function getOrderNumber()
	{
		return $this->orderNumber;
	}

	public function getOrder()
	{
		return $this->order;
	}

	public function getCustomer()
	{
		return $this->customer;
	}

	public function getNotes()
	{
		return $this->notes;
	}

	public function getDesiredActivationDate()
	{
		return $this->desiredActivationDate;
	}

	public function getCompletedDate()
	{
		return $this->completedDate;
	}

	public function getPhoneNumber()
	{
		return $this->phoneNumber;
	}

	public function getDataNumber()
	{
		return $this->dataNumber;
	}

	public function getExpirationDate()
	{
		return $this->expirationDate;
	}

	public function getComments()
	{
		return $this->comments;
	}

	public function getStatus()
	{
		return $this->status;
	}
}