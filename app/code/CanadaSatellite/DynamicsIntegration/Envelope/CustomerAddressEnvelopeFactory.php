<?php

namespace CanadaSatellite\DynamicsIntegration\Envelope;

class CustomerAddressEnvelopeFactory
{
	private $addressUtils;

	public function __construct(
		\CanadaSatellite\DynamicsIntegration\Utils\AddressUtils $addressUtils,
		\CanadaSatellite\DynamicsIntegration\Logger\Logger $logger
	) {
		$this->addressUtils = $addressUtils;
		$this->logger = $logger;
	}

	public function create($address)
	{
		$data = array();
		if ($address === null) {
			return null;
		}

		$company = $address->getCompany();
		$this->logger->info("Customer company $company. Introspect: " . gettype($company));
		$data['company'] = is_string($company) ? $company : NULL;

		$data['street'] = $address->getStreet();
		$data['city'] = $address->getCity();
		$data['postcode'] = $address->getPostcode();

		$data['country'] = $this->addressUtils->getCountryName($address);

		$data['phone'] = $address->getTelephone();

		$fax = $address->getFax();
		$this->logger->info("Customer fax $fax. Introspect: " . gettype($fax));
		$data['fax'] = is_string($fax) ? $fax : NULL;

		$region = $address->getRegion();
		if ($region !== null) {
			$data['region'] = $region->getRegion();
		}

		return $data;
	}
}