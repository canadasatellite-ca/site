<?php

namespace CanadaSatellite\DynamicsIntegration\Model;

class OrderAddressFactory
{
	function fromEnvelope($envelope)
	{
		if ($envelope === null) {
			return null;
		}
		
		return new OrderAddress(
			$envelope->firstName,
			$envelope->lastName,
			$envelope->company,
			$envelope->street,
			$envelope->city,
			$envelope->region,
			$envelope->postcode,
			$envelope->country,
			$envelope->phone,
			$envelope->fax
		);
	}
}
