<?php

namespace CanadaSatellite\DynamicsIntegration\Model;

class Order
{
	private $id;
	private $billingAddress;
	private $shippingAddress;
	private $customer;
	private $createdAt;
	private $incrementId;
	private $currency;
	private $status;
	private $items;
	private $email;
	private $shippedAt;
	private $shippingAmount;

	public function __construct(
		$id,
		$billingAddress,
		$shippingAddress,
		$customer,
		$createdAt,
		$incrementId,
		$currency,
		$status,
		$items,
		$email,
		$shippedAt,
		$shippingAmount
	) {
		$this->id = $id;
		$this->billingAddress = $billingAddress;
		$this->shippingAddress = $shippingAddress;
		$this->customer = $customer;
		$this->createdAt = $createdAt;
		$this->incrementId = $incrementId;
		$this->currency = $currency;
		$this->status = $status;
		$this->items = $items;
		$this->email = $email;
		$this->shippedAt = $shippedAt;
		$this->shippingAmount = $shippingAmount;
	}

	public function getId()
	{
		return $this->id;
	}

	public function getBillingAddress()
	{
		return $this->billingAddress;
	}

	public function getShippingAddress()
	{
		return $this->shippingAddress;
	}

	public function getCustomer()
	{
		return $this->customer;
	}

	public function getCustomerId()
	{
		if ($this->customer === null) {
			return null;
		}

		return $this->getCustomer()->getId();
	}

	public function getCreatedAt()
	{
		return $this->createdAt;
	}

	public function getIncrementId()
	{
		return $this->incrementId;
	}

	public function getCurrency()
	{
		if (empty($this->currency)) {
			return 'CAD';
		}

		return $this->currency;
	}

	public function getStatus()
	{
		return $this->status;
	}

	public function getItems()
	{
		return $this->items;
	}

	public function getCustomerEmail()
	{
		return $this->email;
	}

	public function getShippedAt()
	{
		return $this->shippedAt;
	}

	public function getShippingAmount()
	{
		return $this->shippingAmount;
	}
}
