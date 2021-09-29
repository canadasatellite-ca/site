<?php

namespace CanadaSatellite\AstIntegration\Rest;

class RestApi {
    private $zendClient;
    private $configProvider;
    private $tokenProvider;
    private $logger;

    public function __construct(
        \CanadaSatellite\AstIntegration\Rest\ZendClient          $zendClient,
        \CanadaSatellite\AstIntegration\Config\AstConfigProvider $configProvider,
        \CanadaSatellite\AstIntegration\Rest\TokenProvider       $tokenProvider,
        \CanadaSatellite\AstIntegration\Logger\Logger            $logger
    ) {
        $this->zendClient = $zendClient;
        $this->configProvider = $configProvider;
        $this->tokenProvider = $tokenProvider;
        $this->logger = $logger;
    }

    public function iridiumActivate($sim) {
        $response = $this->zendClient->sendPostRequestJson(
            $this->configProvider->getIridiumActivationUrl(),
            $this->getHeaders(),
            json_encode($sim)
        );
        return $this->zendClient->getResponseJsonIfSuccess($response);
    }

    public function iridiumTopUp($payload) {
        $response = $this->zendClient->sendPostRequestJson(
            $this->configProvider->getIridiumTopupUrl(),
            $this->getHeaders(),
            json_encode($payload)
        );
        return $this->zendClient->getResponseJsonIfSuccess($response);
    }

    public function inmarsatActivate($sim) {
        $response = $this->zendClient->sendPostRequestJson(
            $this->configProvider->getInmarsatActivationUrl(),
            $this->getHeaders(),
            json_encode($sim)
        );
        return $this->zendClient->getResponseJsonIfSuccess($response);
    }

    public function inmarsatTopup($sim) {
        $response = $this->zendClient->sendPostRequestJson(
            $this->configProvider->getInmarsatTopupUrl(),
            $this->getHeaders(),
            json_encode($sim)
        );
        return $this->zendClient->getResponseJsonIfSuccess($response);
    }

    /**
     * @param integer $dataId
     * @return object
     */
    public function getActionStatus($dataId) {
        $response = $this->zendClient->sendPostRequestJson(
            $this->configProvider->getActionStatusUrl(),
            $this->getHeaders(),
            json_encode(['DataId' => $dataId])
        );

        return $this->zendClient->getResponseJsonIfSuccess($response);
    }

    private function getHeaders() {
        return array(
            'Authorization' => 'Bearer ' . $this->getToken(),
            'Content-Type' => 'application/json; charset=utf-8',
            'Accept' => 'application/json'
        );
    }

    private function getToken() {
        $expired = $this->tokenProvider->isTokenExpired();
        if ($expired) {
            $this->logger->info("AST token: Expired");
            $this->tokenProvider->login();
        }

        return $this->tokenProvider->getToken();
    }
}
