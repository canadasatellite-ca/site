<?php

namespace CanadaSatellite\DynamicsIntegration\Model;

class OrderFactory
{
	private $addressFactory;
	private $customerFactory;
	private $itemFactory;

	public function __construct(
		\CanadaSatellite\DynamicsIntegration\Model\OrderAddressFactory $addressFactory,
		\CanadaSatellite\DynamicsIntegration\Model\CustomerFactory $customerFactory,
		\CanadaSatellite\DynamicsIntegration\Model\OrderItemFactory $itemFactory
	) {
		$this->addressFactory = $addressFactory;
		$this->customerFactory = $customerFactory;
		$this->itemFactory = $itemFactory;
	}

	public function fromEnvelope($envelope)
	{
		if ($envelope === null) {
			return null;
		}

		$items = array();
		foreach ($envelope->items as $item) {
			$items []= $this->itemFactory->fromEnvelope($item);
		}

		return new Order(
			$envelope->id,
			$this->addressFactory->fromEnvelope($envelope->billingAddress),
			$this->addressFactory->fromEnvelope($envelope->shippingAddress),
			$this->customerFactory->fromEnvelope($envelope->customer),
			$envelope->createdAt,
			$envelope->incrementId,
			$envelope->currency,
			$envelope->status,
			$items,
			$envelope->email,
			property_exists($envelope, 'shippedAt') ? $envelope->shippedAt : null,
			property_exists($envelope, 'shippingAmount') ? $envelope->shippingAmount : null
		);
	}
}
