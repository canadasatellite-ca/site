<?php

namespace CanadaSatellite\DynamicsIntegration\DynamicsCrm;

class PriceListHelper {
	private $restApi;

	private static $defaultPriceListId = null;

	function __construct(
		\CanadaSatellite\DynamicsIntegration\Rest\RestApi $restApi
	) {
		$this->restApi = $restApi;
	}

	function getDefaultPriceListId() {
		if (self::$defaultPriceListId === null) {
			self::$defaultPriceListId = $this->restApi->getDefaultPriceListId();
		}

		return self::$defaultPriceListId;
	}
}