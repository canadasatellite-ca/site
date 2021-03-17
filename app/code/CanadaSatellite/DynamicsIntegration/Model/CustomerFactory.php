<?php

namespace CanadaSatellite\DynamicsIntegration\Model;

class CustomerFactory
{
	private $addressFactory;

	public function __construct(
		\CanadaSatellite\DynamicsIntegration\Model\CustomerAddressFactory $addressFactory
	) {
		$this->addressFactory = $addressFactory;
	}

	public function fromEnvelope($envelope)
	{
		if ($envelope === null) {
			return null;
		}
		
		return new Customer(
			$envelope->id,
			$envelope->prefix,
			$envelope->firstName,
			$envelope->middleName,
			$envelope->lastName,
			$envelope->email,
			$envelope->url,
			$envelope->gender,
			$envelope->birthDate,
			$envelope->group,
			$envelope->source,
			$this->addressFactory->fromEnvelope($envelope->billingAddress)
		);
	}

	public function create($customerId, $email, $firstName, $lastName) 
	{
		return new Customer(
			$customerId,
			null,
			$firstName,
			null,
			$lastName,
			$email,
			null,
			null,
			null,
			null,
			null,
			null
		);
	}
}
