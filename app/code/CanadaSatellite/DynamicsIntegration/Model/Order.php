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

	function __construct(
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

	function getId()
	{
		return $this->id;
	}

	function getBillingAddress()
	{
		return $this->billingAddress;
	}

	function getShippingAddress()
	{
		return $this->shippingAddress;
	}

	function getCustomer()
	{
		return $this->customer;
	}

	function getCustomerId()
	{
		if ($this->customer === null) {
			return null;
		}

		return $this->getCustomer()->getId();
	}

	function getCreatedAt()
	{
		return $this->createdAt;
	}

	function getIncrementId()
	{
		return $this->incrementId;
	}

	function getCurrency()
	{
		if (empty($this->currency)) {
			return 'CAD';
		}

		return $this->currency;
	}

	function getStatus()
	{
		return $this->status;
	}

    /**
     * @return \CanadaSatellite\DynamicsIntegration\Model\OrderItem[]
     */
    function getItems()
	{
		return $this->items;
	}

	function getCustomerEmail()
	{
		return $this->email;
	}

	function getShippedAt()
	{
		return $this->shippedAt;
	}

	function getShippingAmount()
	{
		return $this->shippingAmount;
	}
}
