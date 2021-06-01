<?php

namespace CanadaSatellite\DynamicsIntegration\Model;

class CustomerAddressFactory
{
	function fromEnvelope($envelope)
	{
		if ($envelope === null) {
			return null;
		}
		
		return new CustomerAddress(
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
