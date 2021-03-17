<?php

namespace CanadaSatellite\DynamicsIntegration\Rest;

class DynamicsCredentials {
	private $clientId;
	private $clientSecret;
	private $resource;

	public function __construct($clientId, $clientSecret, $resource) {
		$this->clientId = $clientId;
		$this->clientSecret = $clientSecret; 
		$this->resource = $resource;
	}

	public function getClientId() {
		return $this->clientId;
	}

	public function getClientSecret() {
		return $this->clientSecret;
	}

	public function getResource() {
		return $this->resource;
	}
}