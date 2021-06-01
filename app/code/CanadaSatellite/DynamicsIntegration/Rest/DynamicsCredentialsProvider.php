<?php

namespace CanadaSatellite\DynamicsIntegration\Rest;

class DynamicsCredentialsProvider {
	private $scopeConfig;

	const CLIENT_ID_PATH = 'dynamics/credentials/client_id';
	const CLIENT_SECRET_PATH = 'dynamics/credentials/client_secret';
	const RESOURCE_PATH_CONFIG_PATH = 'dynamics/credentials/resource';

	function __construct(\Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig) {
		$this->scopeConfig = $scopeConfig;
	}

	function getCredentials() {
		$clientId = $this->scopeConfig->getValue(self::CLIENT_ID_PATH);
		$clientSecret = $this->scopeConfig->getValue(self::CLIENT_SECRET_PATH);
		$resource = $this->scopeConfig->getValue(self::RESOURCE_PATH_CONFIG_PATH);

		return new DynamicsCredentials($clientId, $clientSecret, $resource);
	}
}