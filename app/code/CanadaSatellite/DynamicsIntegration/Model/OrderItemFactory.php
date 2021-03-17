<?php

namespace CanadaSatellite\DynamicsIntegration\Model;

class OrderItemFactory
{
	private $productFactory;

	public function __construct(
		\CanadaSatellite\DynamicsIntegration\Model\ProductFactory $productFactory
	) {
		$this->productFactory = $productFactory;
	}

	public function fromEnvelope($envelope)
	{
		if ($envelope === null) {
			return null;
		}
		
		return new OrderItem(
			$envelope->sku,
			$this->productFactory->fromEnvelope($envelope->product),
			$envelope->price,
			$envelope->qty,
			$envelope->tax,
			$envelope->discount,
			$envelope->total,

			property_exists($envelope, 'optionsPrice') ? $envelope->optionsPrice : null,
			property_exists($envelope, 'optionsCost') ? $envelope->optionsCost : null,

			property_exists($envelope, 'cost') ? $envelope->cost : null
		);
	}
}