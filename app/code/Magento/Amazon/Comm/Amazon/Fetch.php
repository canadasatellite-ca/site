<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\Amazon\Comm\Amazon;

use Magento\Amazon\Api\Data\AccountInterface;
use Magento\Amazon\Logger\AscClientLogger;
use Magento\Amazon\Model\ApiClient;
use Magento\Amazon\Model\ApiClient\ResponseValidationException;
use Magento\Amazon\Model\LogStateManagement;
use Magento\Amazon\Model\ResourceModel\Amazon\Account\CollectionFactory as AccountCollectionFactory;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class Fetch
 */
class Fetch
{
    /** @var AccountCollectionFactory $accountCollectionFactory */
    private $accountCollectionFactory;

    /**
     * @var LogStateManagement
     */
    private $logStateManagement;
    /**
     * @var AscClientLogger
     */
    private $logger;
    /**
     * @var ApiClient
     */
    private $apiClient;
    /**
     * @var UpdateHandler
     */
    private $updateHandler;

    /**
     * @param AccountCollectionFactory $accountCollectionFactory
     * @param LogStateManagement $logStateManagement
     * @param AscClientLogger $logger
     * @param ApiClient $apiClient
     * @param UpdateHandler $updateHandler
     */
    public function __construct(
        AccountCollectionFactory $accountCollectionFactory,
        LogStateManagement $logStateManagement,
        AscClientLogger $logger,
        ApiClient $apiClient,
        UpdateHandler $updateHandler
    ) {
        $this->accountCollectionFactory = $accountCollectionFactory;
        $this->logStateManagement = $logStateManagement;
        $this->logger = $logger;
        $this->apiClient = $apiClient;
        $this->updateHandler = $updateHandler;
    }

    /**
     * Fetches Amazon API responses and processes results
     *
     * @return void
     */
    public function execute()
    {
        $collection = $this->accountCollectionFactory->create();
        $collection->addFieldToFilter('is_active', 1);

        /** @var AccountInterface $account */
        foreach ($collection as $account) {
            try {
                $this->fetchLogsForMerchant($account);
            } catch (ResponseValidationException $exception) {
                $this->logger->critical(
                    sprintf(
                        'Cannot fetch logs for account %s: %s',
                        $account->getUuid(),
                        $exception->getResponse()->getBody()
                    ),
                    [
                        'account' => $account->getUuid(),
                        'exception' => $exception,
                    ]
                );
            } catch (\Throwable $exception) {
                $this->logger->critical(
                    'Unexpected exception occurred during fetching updates for the account',
                    ['exception' => $exception, 'account' => $account]
                );
            }
        }
    }

    public function fetchLogsForMerchant(AccountInterface $account): void
    {
        $rows = $this->apiClient->fetchLogs($account);

        while (!empty($rows['logs'])) {
            $items = $this->getLogsAvailableForProcessing($rows['logs']);
            $this->logger->debug('Filtered In Progress logs', [
                'logs_fetched' => count($rows['logs']),
                'logs_left' => count($items),
            ]);
            if ($items) {
                $processedLogs = [];
                try {
                    $this->logStateManagement->processing(array_keys($items));
                    $processedLogs = $this->updateHandler->handle($items, $account);
                    if ($processedLogs) {
                        $this->logger->info(
                            'Deleting processed logs',
                            ['count' => count($processedLogs), 'debug' => ['logIds' => $processedLogs]]
                        );
                        $this->apiClient->deleteLogs($account, $processedLogs);
                    }
                } catch (\Exception $e) {
                    $this->logger->critical('Exception occurred during logs processing', ['exception' => $e]);
                } finally {
                    $this->logStateManagement->complete($processedLogs);
                }
            }
            $rows = $this->apiClient->fetchLogs($account, $rows['lastLogToken']);
        }
    }

    /**
     * @param $logs
     * @return array
     * @throws LocalizedException
     */
    private function getLogsAvailableForProcessing($logs): array
    {
        $logIds = array_column($logs, 'id');
        $items = array_combine($logIds, $logs);
        $processableLogs = array_flip($this->logStateManagement->filterProcessableLogs($logIds));
        $items = array_intersect_key($items, $processableLogs);
        return $items;
    }
}
