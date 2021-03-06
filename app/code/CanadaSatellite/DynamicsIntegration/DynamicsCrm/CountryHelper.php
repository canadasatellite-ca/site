<?php

namespace CanadaSatellite\DynamicsIntegration\DynamicsCrm;

class CountryHelper
{
	private $restApi;
	private $logger;

	function __construct(
		\CanadaSatellite\DynamicsIntegration\Rest\RestApi $restApi,
		\CanadaSatellite\DynamicsIntegration\Logger\Logger $logger
	) {
		$this->restApi = $restApi;
		$this->logger = $logger;
	}

	/**
	 * @param string $country
	 * @return string|null Dynamics id
	 */
	function findCountryByName($country)
	{
		$id = $this->restApi->findCountryByName($country);
		if ($id === false) {
			return null;
		}

		return $id;
	}
}
