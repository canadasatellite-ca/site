<?php

namespace CanadaSatellite\DynamicsIntegration\DynamicsCrm;

class CurrencyHelper {
	private $restApi;
	private $currencies;

	public function __construct(\CanadaSatellite\DynamicsIntegration\Rest\RestApi $restApi) {
		$this->restApi = $restApi;
		$this->currencies = array();
	}

	public function getCurrencyIdByCode($code) {
		if (!array_key_exists($code, $this->currencies)) {
			try {
				$this->currencies[$code] = $this->restApi->findCurrencyIdByIsoCode($code);
			}
			catch (DynamicsException $e) {
				return "00000000-0000-0000-0000-000000000000";
			}
		}

		return $this->currencies[$code];
	}
}