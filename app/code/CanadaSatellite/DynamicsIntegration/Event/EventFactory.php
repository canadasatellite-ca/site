<?php

namespace CanadaSatellite\DynamicsIntegration\Event;

class EventFactory
{
	public function createCustomerSavedEvent($customerId, $customerEnvelope)
	{
		return array(
			'kind' => 'CustomerSaved',
			'id' => $customerId,
			'data' => $customerEnvelope,
		);
	}

	public function createCustomerDeletedEvent($customerId, $email)
	{
		return array(
			'kind' => 'CustomerDeleted',
			'id' => $customerId,
			'email' => $email,
		);
	}

	public function createOrderSavedEvent($orderId, $orderEnvelope)
	{
		return array(
			'kind' => 'OrderSaved',
			'id' => $orderId,
			'data' => $orderEnvelope,
		);
	}

	public function createProductSavedEvent($productId, $sku, $productEnvelope)
	{
		return array(
			'kind' => 'ProductSaved',
			'id' => $productId,
			'sku' => $sku,
			'data' => $productEnvelope,
		);
	}

	public function createProductDeletedEvent($productId, $sku)
	{
		return array(
			'kind' => 'ProductDeleted',
			'id' => $productId,
			'sku' => $sku,
		);
	}

	public function createActivationFormSavedEvent($activationFormId, $activationFormEnvelope)
	{
		return array(
			'kind' => 'ActivationFormSaved',
			'id' => $activationFormId,
			'data' => $activationFormEnvelope,
		);
	}
}
