<?php

namespace CanadaSatellite\DynamicsIntegration\DynamicsCrm;

class PriceListHelper {
	private $restApi;

	private static $defaultPriceListId = null;

	public function __construct(
		\CanadaSatellite\DynamicsIntegration\Rest\RestApi $restApi
	) {
		$this->restApi = $restApi;
	}

	public function getDefaultPriceListId() {
		if (self::$defaultPriceListId === null) {
			self::$defaultPriceListId = $this->restApi->getDefaultPriceListId();
		}

		return self::$defaultPriceListId;
	}
}