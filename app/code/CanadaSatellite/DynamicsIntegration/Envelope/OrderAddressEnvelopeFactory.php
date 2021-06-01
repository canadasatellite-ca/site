<?php

namespace CanadaSatellite\DynamicsIntegration\Envelope;

class OrderAddressEnvelopeFactory
{
	private $addressUtils;

	function __construct(
		\CanadaSatellite\DynamicsIntegration\Utils\AddressUtils $addressUtils,
		\CanadaSatellite\DynamicsIntegration\Logger\Logger $logger
	) {
		$this->addressUtils = $addressUtils;
		$this->logger = $logger;
	}

	function create($address)
	{
		if ($address === null) {
			return null;
		}

		$data = array();

		$data['firstName'] = $address->getFirstname();
		$data['lastName'] = $address->getLastname();

		$company = $address->getCompany();
		$this->logger->info("Order company $company. Introspect: " . gettype($company));
		$data['company'] = is_string($company) ? $company : NULL;

		$data['street'] = $address->getStreet();
		$data['city'] = $address->getCity();

		$data['country'] = $this->addressUtils->getCountryName($address);

		$data['postcode'] = $address->getPostcode();
		$data['phone'] = $address->getTelephone();

		$fax = $address->getFax();
		$this->logger->info("Order fax $fax. Introspect: " . gettype($fax));
		$data['fax'] =  is_string($fax) ? $fax : NULL;

		$data['region'] = $address->getRegion();

		return $data;
	}
}
