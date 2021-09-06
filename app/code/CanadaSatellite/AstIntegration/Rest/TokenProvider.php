<?php

namespace CanadaSatellite\AstIntegration\Rest;

class TokenProvider {
	const TOKEN_PART = "token";

	private $configProvider;
	private $zendClient;
	private $logger;

	private $accessToken = null;
	private $expiresAt = null;

	public function __construct(
		\CanadaSatellite\AstIntegration\Config\AstConfigProvider $configProvider,
		\CanadaSatellite\AstIntegration\Rest\ZendClient $zendClient,
		\CanadaSatellite\AstIntegration\Logger\Logger $logger
	) {
		$this->configProvider = $configProvider;
		$this->zendClient = $zendClient;
		$this->logger = $logger;
		$this->zendClient->setLogger($this->logger);
	}

    public function login() {
		if (isset($this->accessToken) && !$this->isTokenExpired()) {
			return;
		}

		$this->logger->info('Obtaining AST token...');

		$now = microtime(true);

		$headers = array(
			'Accept' => 'application/json',
			'Content-Type' => 'application/x-www-form-urlencoded',
		);

		$params = array(
			'grant_type' => 'password',
			'username' => $this->configProvider->getAstUserName(),
			'password' => $this->configProvider->getAstPassword()
		);
		$response = $this->zendClient->sendPostRequest($this->configProvider->getTokenUrl(), $headers, $params);
		$json = $this->zendClient->getResponseJsonIfSuccess($response);
		
		$this->accessToken = $json->access_token;
		$this->expiresAt = $json->expires_in + $now;
		$this->logger->info("AST token = $this->accessToken");
	}

	public function getToken() {
		if (!$this->isTokenExpired()) {
			return $this->accessToken;
		}
	}

	public function isTokenExpired() {
		if (!isset($this->expiresAt)) {
			return true;
		}
		
		$now = microtime(true);
		return $this->expiresAt < $now;
	}
}