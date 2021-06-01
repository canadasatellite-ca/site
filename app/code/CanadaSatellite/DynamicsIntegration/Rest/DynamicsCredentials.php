<?php

namespace CanadaSatellite\DynamicsIntegration\Rest;

class DynamicsCredentials {
	private $clientId;
	private $clientSecret;
	private $resource;

	function __construct($clientId, $clientSecret, $resource) {
		$this->clientId = $clientId;
		$this->clientSecret = $clientSecret; 
		$this->resource = $resource;
	}

	function getClientId() {
		return $this->clientId;
	}

	function getClientSecret() {
		return $this->clientSecret;
	}

	function getResource() {
		return $this->resource;
	}
}