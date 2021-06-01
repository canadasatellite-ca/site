<?php

namespace CanadaSatellite\DynamicsIntegration\Config;

use Magento\Framework\App\DeploymentConfig;

class Config
{
	const DYNAMICS_INTEGRATION_CONFIG = 'dynamics_integration';
	const QUEUE = 'queue';

	function __construct(DeploymentConfig $config)
	{
		$this->config = $config;
	}

	function getIntegrationQueue()
	{
		$config = $this->config->getConfigData(self::DYNAMICS_INTEGRATION_CONFIG) ?: array();
		if (!array_key_exists(self::QUEUE, $config)) {
			throw new \Exception("No queue configured for integration.");
		}

		return $config[self::QUEUE];
	}
}
