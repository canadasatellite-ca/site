<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\Amazon\Model;

use GuzzleHttp\Exception\GuzzleException;
use Magento\Amazon\Api\Data\AccountInterface;
use Magento\Amazon\Configuration\ServerEndpointConfiguration;
use Magento\Amazon\Domain\Command\AmazonCommandInterface;
use Magento\Amazon\Logger\AscClientLogger;
use Magento\Amazon\Model\ApiClient\ApiException;
use Magento\Amazon\Model\ApiClient\ResponseFormatValidationException;
use Magento\Amazon\Model\ApiClient\ResponseValidationException;
use Psr\Http\Message\ResponseInterface;

class ApiClient
{
    /**
     * @var \GuzzleHttp\Client|null
     */
    private $client;
    /**
     * @var ServerEndpointConfiguration
     */
    private $serverEndpointConfiguration;
    /**
     * @var ServicesClientFactory
     */
    private $servicesClientFactory;
    /**
     * @var AscClientLogger
     */
    private $logger;
    /**
     * @var ModuleVersionResolver
     */
    private $moduleVersionResolver;

    /**
     * @param ServerEndpointConfiguration $serverEndpointConfiguration
     * @param ServicesClientFactory $servicesClientFactory
     * @param AscClientLogger $ascClientLogger
     * @param ModuleVersionResolver $moduleVersionResolver
     */
    public function __construct(
        ServerEndpointConfiguration $serverEndpointConfiguration,
        ServicesClientFactory $servicesClientFactory,
        AscClientLogger $ascClientLogger,
        ModuleVersionResolver $moduleVersionResolver
    ) {
        $this->serverEndpointConfiguration = $serverEndpointConfiguration;
        $this->servicesClientFactory = $servicesClientFactory;
        $this->logger = $ascClientLogger;
        $this->moduleVersionResolver = $moduleVersionResolver;
    }

    private function getClient(): \GuzzleHttp\Client
    {
        if (null === $this->client) {
            $this->client = $this->servicesClientFactory->create();
        }
        return $this->client;
    }

    /**
     * @param array $merchantProfileData
     * @return array
     * @throws ApiException
     */
    public function createMerchant(array $merchantProfileData): array
    {
        try {
            $this->logger->info('Sending request to create a merchant.');
            $url = $this->serverEndpointConfiguration->merchantCreate($merchantProfileData['country_code']);

            $response = $this->call(
                'POST',
                $url,
                [
                    'body' => json_encode(['merchantProfile' => $merchantProfileData])
                ]
            );
            $responseData = $this->decodeResponse($response);
            if (isset($responseData['message'])) {
                throw new ResponseValidationException(__($responseData['message']), $response);
            }
            if (!isset($responseData['uuid'])) {
                $this->logger->error('UUID is missing in the response');
                throw new ResponseFormatValidationException(
                    __('Failed to save the Amazon store. Please try again.'),
                    $response
                );
            }
            $this->logger->info('Merchant created.', ['debug' => ['merchant_uuid' => $responseData['uuid']]]);
        } catch (GuzzleException $e) {
            $this->logger->error('Cannot create a merchant. Exception occurred.', ['exception' => $e]);
            throw new ApiException(
                __('An error occurred while setting up the Amazon store. Please try again.'),
                $e
            );
        }
        return $responseData;
    }

    /**
     * @param AccountInterface $account
     * @param array $merchantProfileData
     * @return array
     * @throws ApiException
     * @throws ResponseFormatValidationException
     * @throws ResponseValidationException
     */
    public function updateMerchant(AccountInterface $account, array $merchantProfileData): array
    {
        try {
            $this->logger->info(
                'Sending request for merchant update.',
                [
                    'uuid' => $account->getUuid(),
                    'marketplace' => $account->getCountryCode()
                ]
            );
            $url = $this->serverEndpointConfiguration->merchant($account->getCountryCode(), $account->getUuid());
            $response = $this->call(
                'PATCH',
                $url,
                [
                    'body' => json_encode(['merchantProfile' => $merchantProfileData])
                ]
            );
            $responseData = $this->decodeResponse($response);
            if (isset($responseData['message'])) {
                throw new ResponseValidationException(__($responseData['message']), $response);
            }
            $this->logger->info(
                'Merchant successfully updated. Merchant UUID: {uuid}',
                ['uuid' => $account->getUuid()]
            );
        } catch (GuzzleException $e) {
            $this->logger->error(
                'Cannot update merchant. Exception occurred.',
                [
                    'uuid' => $account->getUuid(),
                    'exception' => $e,
                ]
            );
            throw new ApiException(__('Unable to build the Amazon store. Please try again.'), $e);
        }
        return $responseData;
    }

    /**
     * @param AccountInterface $account
     * @return array
     * @throws ApiException
     * @throws ResponseFormatValidationException
     * @throws ResponseValidationException
     */
    public function getMerchantSellerId(AccountInterface $account): ?string
    {
        $result = null;
        try {
            $this->logger->info(
                'Sending request to get merchant\'s seller id.',
                [
                    'uuid' => $account->getUuid(),
                    'marketplace' => $account->getCountryCode()
                ]
            );
            $url = $this->serverEndpointConfiguration->merchantSellerId(
                $account->getCountryCode(),
                $account->getUuid()
            );
            $response = $this->call('GET', $url);
            $responseData = $this->decodeResponse($response);
            if (isset($responseData['message'])) {
                throw new ResponseValidationException(__($responseData['message']), $response);
            }
            $result = $responseData['seller_id'] ?? null;
            $this->logger->info(
                'Merchant seller id received. Merchant UUID: {uuid}',
                ['uuid' => $account->getUuid()]
            );
        } catch (GuzzleException $e) {
            $this->logger->error(
                'Cannot get seller id. Exception occurred.',
                [
                    'uuid' => $account->getUuid(),
                    'exception' => $e,
                ]
            );
            throw new ApiException(__('Unable to receive seller id. Please try again.'), $e);
        }
        return $result;
    }

    /**
     * @param AccountInterface $account
     * @return string|null
     * @throws ApiException
     * @throws ResponseValidationException
     */
    public function getMerchantAuthenticationStatus(AccountInterface $account): ?string
    {
        $authenticationStatus = null;
        try {
            $this->logger->info(
                'Sending request to get merchant authentication status.',
                [
                    'uuid' => $account->getUuid(),
                    'marketplace' => $account->getCountryCode()
                ]
            );
            $url = $this->serverEndpointConfiguration->merchantAuthenticationStatus(
                $account->getCountryCode(),
                $account->getUuid()
            );
            $response = $this->call('GET', $url);
            $responseData = $this->decodeResponse($response);
            if (isset($responseData['message'])) {
                throw new ResponseValidationException(__($responseData['message']), $response);
            }
            if (!isset($responseData['status'])) {
                throw new ResponseValidationException(
                    __('Authentication status is missing in the API response'),
                    $response
                );
            }
            $authenticationStatus = $responseData['status'] ?? null;
            $this->logger->info(
                'Merchant authentication status. Merchant UUID: {uuid}',
                ['uuid' => $account->getUuid(), 'authentication_status' => $authenticationStatus]
            );
        } catch (GuzzleException $e) {
            $this->logger->error(
                'Cannot get authentication status. Exception occurred.',
                [
                    'uuid' => $account->getUuid(),
                    'exception' => $e,
                ]
            );
            throw new ApiException(__('Unable to receive authentication status. Please try again.'), $e);
        }
        return $authenticationStatus;
    }

    /**
     * @param AccountInterface $account
     * @param int $isActive
     * @return void
     * @throws ApiException
     * @throws ResponseValidationException
     */
    public function updateMerchantStatus(AccountInterface $account, int $isActive)
    {
        try {
            $this->logger->info(
                'Sending request to update merchant status.',
                ['uuid' => $account->getUuid(), 'status' => $isActive]
            );
            $url = $this->serverEndpointConfiguration->merchant($account->getCountryCode(), $account->getUuid());
            $response = $this->call(
                'PATCH',
                $url,
                [
                    'body' => json_encode(['merchantProfile' => ['status' => $isActive]])
                ]
            );
            if ($response->getStatusCode() !== 200) {
                $responseData = $this->decodeResponse($response);
                $error = $responseData['message'] ?? '';
                $this->logger->error('Unable to update account status on the service layer. Error: ' . $error);
                throw new ResponseValidationException(
                    __('The account status could not be toggled at this time. Please try again.'),
                    $response
                );
            }

            $this->logger->info(
                'Request for merchant status update completed.',
                ['uuid' => $account->getUuid(), 'status' => $isActive]
            );
        } catch (GuzzleException $e) {
            $this->logger->error(
                'Cannot update merchant status. Exception occurred.',
                [
                    'uuid' => $account->getUuid(),
                    'exception' => $e,
                ]
            );
            throw new ApiException(__('The account status could not be toggled at this time. Please try again.'), $e);
        }
    }

    /**
     * @param string $uuid
     * @param string $countryCode
     * @return void
     * @throws ApiException
     * @throws ResponseValidationException
     */
    public function deleteMerchant(string $uuid, string $countryCode): void
    {
        try {
            $this->logger->info(
                'Sending request to delete merchant account.',
                ['uuid' => $uuid]
            );
            $url = $this->serverEndpointConfiguration->merchant($countryCode, $uuid);
            $response = $this->call(
                'DELETE',
                $url,
                []
            );

            if ($response->getStatusCode() !== 200) {
                $responseData = $this->decodeResponse($response);
                $error = $responseData['message'] ?? '';
                $this->logger->error('Unable to delete Amazon store. Error: ' . $error);
                throw new ResponseValidationException(
                    __('Unable to delete the Amazon store. Please try again.'),
                    $response
                );
            }

            $this->logger->info(
                'Request for merchant delete completed.',
                ['uuid' => $uuid]
            );
        } catch (GuzzleException $e) {
            $this->logger->error(
                'Cannot delete merchant. Exception occurred.',
                [
                    'uuid' => $uuid,
                    'exception' => $e,
                ]
            );
            $this->logger->critical($e);
            throw new ApiException(__('Unable to delete the Amazon store. Please try again.'), $e);
        }
    }

    /**
     * @param AccountInterface $account
     * @param string|null $after last log token
     * @return array
     * @throws ApiException
     * @throws ResponseFormatValidationException
     */
    public function fetchLogs(AccountInterface $account, ?string $after = null): array
    {
        $this->logger->info(
            'Sending request to fetch logs.',
            ['account' => $account]
        );
        $url = $this->serverEndpointConfiguration->logs($account->getCountryCode(), $account->getUuid());

        try {
            $response = $this->call(
                'GET',
                $url,
                ['query' => ['after' => $after]]
            );
            $responseData = $this->decodeResponse($response);
        } catch (GuzzleException $e) {
            $this->logger->critical($e);
            throw new ApiException(__('Unable to fetch logs. Please try again.'), $e);
        }

        if ($response->getStatusCode() !== 200) {
            $error = $responseData['message'] ?? '';
            $this->logger->error('Unable to fetch logs. Error: ' . $error);
            throw new ResponseFormatValidationException(__('Unable to fetch logs. Please try again.'), $response);
        }

        $this->logger->info(
            'Request for logs fetching completed.',
            [
                'uuid' => $account->getUuid(),
                'logs_count' => isset($responseData['logs']) ? count($responseData['logs']) : 0,
                'after' => $after ?: 'empty',
                'lastToken' => $rows['lastLogToken'] ?? 'empty',
            ]
        );

        return $responseData;
    }

    /**
     * @param AccountInterface $account
     * @param array $logIds
     * @return void
     * @throws ApiException
     * @throws ResponseFormatValidationException
     */
    public function deleteLogs(AccountInterface $account, array $logIds)
    {
        try {
            $this->logger->info(
                'Sending request to delete logs.',
                ['uuid' => $account->getUuid()]
            );
            $url = $this->serverEndpointConfiguration->logsBulkDelete($account->getCountryCode(), $account->getUuid());
            $response = $this->call(
                'POST',
                $url,
                ['body' => json_encode(['ids' => array_values($logIds)])]
            );

            if ($response->getStatusCode() !== 200) {
                $responseData = $this->decodeResponse($response);
                $error = $responseData['message'] ?? '';
                $this->logger->error('Unable to delete logs. Error: ' . $error);
                throw new ResponseFormatValidationException(__('Unable to delete logs. Please try again.'), $response);
            }

            $this->logger->info(
                'Logs delete request completed.',
                [
                    'uuid' => $account->getUuid(),
                    'logs_count' => count($logIds)
                ]
            );
        } catch (GuzzleException $e) {
            $this->logger->critical($e);
            throw new ApiException(__('Unable to delete logs. Please try again'), $e);
        }
    }

    /**
     * @param AccountInterface $account
     * @param AmazonCommandInterface[] $commands
     * @return void
     * @throws GuzzleException
     * @throws \Throwable
     */
    public function pushCommands(AccountInterface $account, array $commands)
    {
        try {
            $this->logger->info('Sending request to push updates.');
            $url = $this->serverEndpointConfiguration->commands($account->getCountryCode(), $account->getUuid());
            $response = $this->call(
                'POST',
                $url,
                ['body' => json_encode(['commands' => $commands])]
            );

            if ($response->getStatusCode() !== 200) {
                $responseData = $this->decodeResponse($response);
                $error = $responseData['message'] ?? '';
                $this->logger->error(
                    'An error occurred while pushing commands. Error: ' . $error
                );
                throw new ResponseFormatValidationException(
                    __('Unable to push commands. Please try again.'),
                    $response
                );
            }

            $this->logger->info(
                'Pushing updates request completed.',
                [
                    'uuid' => $account->getUuid(),
                    'commands_count' => count($commands)
                ]
            );
        } catch (GuzzleException $e) {
            $this->logger->critical($e);
            throw new ApiException(__('Unable to push Commands. Please try again.'), $e);
        }
    }

    /**
     * @param AccountInterface $account
     * @return string
     * @throws ApiException
     * @throws ResponseFormatValidationException
     */
    public function getIrpUrl(AccountInterface $account): string
    {
        try {
            $this->logger->info(
                'Sending request to get IRP url.',
                ['uuid' => $account->getUuid()]
            );
            $url = $this->serverEndpointConfiguration->merchantIrpUrl($account->getCountryCode(), $account->getUuid());
            $response = $this->call(
                'GET',
                $url,
                [
                    'query' => [
                        'merchant_uuid' => $account->getUuid(),
                    ]
                ]
            );

            if ($response->getStatusCode() !== 200) {
                $irpUrl = $this->decodeResponse($response);
                $error = $irpUrl['message'] ?? '';
                $this->logger->error(
                    'An error occurred while retrieving IRP URL. Error: ' . $error
                );
                throw new ResponseFormatValidationException(
                    __('Unable to retrieve IRP URL. Please try again.'),
                    $response
                );
            }

            $response = json_decode((string)$response->getBody(), true);
            if ($response === null || empty($response['irpUrl'])) {
                $this->logger->critical(
                    'Cannot decode response body',
                    ['body_base64' => base64_encode($response->getBody())]
                );
                throw new ResponseFormatValidationException(__('Cannot decode response body'), $response);
            }
        } catch (GuzzleException $e) {
            $this->logger->critical($e);
            throw new ApiException(__('Unable to retrieve IRP URL. Please try again.'));
        }
        return $response['irpUrl'];
    }

    /**
     * @param string $method
     * @param string $url
     * @param array $options
     * @return ResponseInterface
     * @throws GuzzleException
     */
    private function call(
        string $method,
        string $url,
        array $options = []
    ): ResponseInterface {
        $options['headers']['X-AMAZON-CLIENT-VERSION'] = $this->moduleVersionResolver->getVersion();
        if (!isset($options['headers']['Content-Type'])) {
            $options['headers']['Content-Type'] = 'application/json';
        }
        return $this->getClient()->request($method, $url, $options);
    }

    /**
     * @param ResponseInterface $response
     * @return array
     * @throws ApiException
     */
    private function decodeResponse(ResponseInterface $response): array
    {
        $result = json_decode((string)$response->getBody(), true);
        if (!is_array($result)) {
            $this->logger->critical(
                'Cannot decode response body',
                ['body_base64' => base64_encode((string)$response->getBody())]
            );
            throw new ResponseFormatValidationException(
                __("An error occurred when communicating to the server. Please try again later."),
                $response
            );
        }
        return $result;
    }
}
