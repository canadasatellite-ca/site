<?php

namespace CanadaSatellite\AstIntegration\Rest;

use CanadaSatellite\AstIntegration\Exceptions\HttpException;

class ZendClient {
	private $httpClient;
	private $logger;

	public function __construct() {
		$this->httpClient = $this->createHttpClient();
	}

	public function setLogger($logger) {
		$this->logger = $logger;
	}

	public function sendGetRequest($uri, $headers, $query) {
		$request = new \Zend\Http\Request();
		$request->setMethod(\Zend\Http\Request::METHOD_GET);
		$request->setUri($uri);

		$request->getHeaders()->addHeaders($headers);
		$request->getQuery()->fromArray($query);

		return $this->sendRequest($request);
	}

	public function sendPostRequest($uri, $headers, $params) {
		$request = new \Zend\Http\Request();
		$request->setMethod(\Zend\Http\Request::METHOD_POST);
		$request->setUri($uri);

		$request->getHeaders()->addHeaders($headers);
		$request->getPost()->fromArray($params);
		
		return $this->sendRequest($request);
	}

	public function sendPostRequestJson($uri, $headers, $json) {
		$request = new \Zend\Http\Request();
		$request->setMethod(\Zend\Http\Request::METHOD_POST);
		$request->setUri($uri);

		$request->getHeaders()->addHeaders($headers);
		$request->setContent($json);

		return $this->sendRequest($request);
	}

	public function sendPatchRequestJson($uri, $headers, $json) {
		$request = new \Zend\Http\Request();
		$request->setMethod(\Zend\Http\Request::METHOD_PATCH);
		$request->setUri($uri);

		$request->getHeaders()->addHeaders($headers);
		$request->setContent($json);

		return $this->sendRequest($request);
	}

	public function sendDeleteRequest($uri, $headers) {
		$request = new \Zend\Http\Request();
		$request->setMethod(\Zend\Http\Request::METHOD_DELETE);
		$request->setUri($uri);

		$request->getHeaders()->addHeaders($headers);

		return $this->sendRequest($request);
	}

	public function getResponseJsonIfSuccess($response, $asArray = false) {
		$this->checkResponseIsSuccess($response);

		$body = $response->getBody();
		return json_decode($body, $asArray);
	}

	private function createHttpClient() {
		$client = new \Zend\Http\Client();
		$options = array(
			'adapter' => 'Zend\Http\Client\Adapter\Curl',
			'curloptions' => [CURLOPT_FOLLOWLOCATION => true],
			'maxredirects' => 0,
			'timeout' => 30
		);
		$client->setOptions($options);
		return $client;
	}

	private function sendRequest($request) {
		try {
			$response = $this->httpClient->send($request);
			$request = $this->httpClient->getLastRawRequest();
			$this->logger->info("Request: $request");
			$this->logger->info("Response: ". $response->getBody());
			return $response;
		}
		catch (\Zend\Http\Client\Exception\RuntimeException $e) {
			$this->logger->error("Failed Zend request: " . $e->getMessage());
			throw new HttpException('Request to ' . $request->getUri());
		}
		catch (\Zend\Http\Exception\RuntimeException $e) {
			$this->logger->error("Failed Zend request: " . $e->getMessage());
			throw new HttpException('Request to ' . $request->getUri());
		}
	}

	private function checkResponseIsSuccess($response) {
		$this->logger->info('Response ' . $this->httpClient->getLastRawResponse());
		if (!$response->isSuccess())
			throw new HttpException('Recipient returned failed response');
	}
}