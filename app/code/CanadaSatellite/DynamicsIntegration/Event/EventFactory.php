<?php

namespace CanadaSatellite\DynamicsIntegration\Event;

class EventFactory
{
	function createCustomerSavedEvent($customerId, $customerEnvelope)
	{
		return array(
			'kind' => 'CustomerSaved',
			'id' => $customerId,
			'data' => $customerEnvelope,
		);
	}

	function createCustomerDeletedEvent($customerId, $email)
	{
		return array(
			'kind' => 'CustomerDeleted',
			'id' => $customerId,
			'email' => $email,
		);
	}

	function createOrderSavedEvent($orderId, $orderEnvelope)
	{
		return array(
			'kind' => 'OrderSaved',
			'id' => $orderId,
			'data' => $orderEnvelope,
		);
	}

	function createOrderNoteCreatedEvent($orderId, $orderNote)
	{
		return array(
			'kind' => 'OrderNoteAdded',
			'id' => $orderId,
			'data' => $orderNote,
		);
	}

	function createProductSavedEvent($productId, $sku, $productEnvelope)
	{
		return array(
			'kind' => 'ProductSaved',
			'id' => $productId,
			'sku' => $sku,
			'data' => $productEnvelope,
		);
	}

	function createProductDeletedEvent($productId, $sku)
	{
		return array(
			'kind' => 'ProductDeleted',
			'id' => $productId,
			'sku' => $sku,
		);
	}

	function createActivationFormSavedEvent($activationFormId, $activationFormEnvelope)
	{
		return array(
			'kind' => 'ActivationFormSaved',
			'id' => $activationFormId,
			'data' => $activationFormEnvelope,
		);
	}

    function createAstQueuePushEvent($id, $data)
    {
        return array(
            'kind' => 'AstQueuePush',
            'id' => $id,
            'data' => $data,
        );
    }
}
