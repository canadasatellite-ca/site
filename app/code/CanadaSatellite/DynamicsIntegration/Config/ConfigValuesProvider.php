<?php

namespace CanadaSatellite\DynamicsIntegration\Config;

class ConfigValuesProvider {
	private $scopeConfig;

	const SIM_ORDER_NOTE_REGEX_PATH = 'magento/regulars/get_sim_from_order_note';

	public function __construct(\Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig) {
		$this->scopeConfig = $scopeConfig;
	}

	public function getSimOrderNoteRegex() {
		$simOrderNoteRegex = $this->scopeConfig->getValue(self::SIM_ORDER_NOTE_REGEX_PATH);

		return $simOrderNoteRegex;
	}
}