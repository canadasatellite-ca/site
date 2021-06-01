<?php

namespace CanadaSatellite\DynamicsIntegration\Model;

class ActivationFormFactory
{
	private $orderFactory;
	private $customerFactory;

	function __construct(
		\CanadaSatellite\DynamicsIntegration\Model\OrderFactory $orderFactory,
		\CanadaSatellite\DynamicsIntegration\Model\CustomerFactory $customerFactory
	) {
		$this->orderFactory = $orderFactory;
		$this->customerFactory = $customerFactory;
	}


	function fromEnvelope($envelope)
	{
		if ($envelope === null) {
			return null;
		}

		return new ActivationForm(
			$envelope->id,
			$envelope->email,
			$envelope->firstName,
			$envelope->lastName,
			$envelope->companyName,
			$envelope->simNumber,
			$envelope->orderNumber,
			$this->orderFactory->fromEnvelope($envelope->order),
			$this->customerFactory->fromEnvelope($envelope->customer),
			property_exists($envelope, 'desiredActivationDate') ? $envelope->desiredActivationDate : null,
			$envelope->notes,
			$envelope->completedDate,
			$envelope->phoneNumber,
			property_exists($envelope, 'dataNumber') ? $envelope->dataNumber : null,
			$envelope->expirationDate,
			$envelope->comments,
			$envelope->status);
	}
}
