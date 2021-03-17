<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Amazon\Comm\Amazon;

use Magento\Amazon\Api\AccountManagementInterface;
use Magento\Amazon\Api\Data\AccountInterface;
use Magento\Amazon\Model\ApiClient;
use Magento\Amazon\Model\ResourceModel\Amazon\Account\CollectionFactory;
use Magento\Amazon\Model\ResourceModel\Amazon\Action as ActionResourceModel;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class for pushing commands to host
 */
class Push
{
    /** Max commands to be processed per push */
    const CHUNK_SIZE = 1000;

    /**
     * @var CollectionFactory $collectionFactory
     */
    private $collectionFactory;

    /**
     * @var ActionResourceModel $actionResourceModel
     */
    private $actionResourceModel;

    /**
     * @var AccountManagementInterface
     */
    private $accountManagement;

    /**
     * @var ApiClient
     */
    private $apiClient;

    /**
     * @param CollectionFactory $collectionFactory
     * @param ActionResourceModel $actionResourceModel
     * @param AccountManagementInterface $accountManagement
     * @param ApiClient $apiClient
     */
    public function __construct(
        CollectionFactory $collectionFactory,
        ActionResourceModel $actionResourceModel,
        AccountManagementInterface $accountManagement,
        ApiClient $apiClient
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->actionResourceModel = $actionResourceModel;
        $this->accountManagement = $accountManagement;
        $this->apiClient = $apiClient;
    }

    /**
     * Fetches Amazon API responses and processes results
     *
     * @return void
     * @throws LocalizedException
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Throwable
     */
    public function execute()
    {
        /** @var \Magento\Amazon\Model\ResourceModel\Amazon\Account\Collection $collection */
        $collection = $this->collectionFactory->create();
        $collection->addFieldToFilter('is_active', 1);

        foreach ($collection as $account) {
            $this->pushCommandsForMerchant($account);
        }
    }

    /**
     * Prepare commands and push them to the server.
     *
     * @param AccountInterface $account
     * @param int $merchantId
     * @throws LocalizedException
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Throwable
     */
    private function processCommands(AccountInterface $account, int $merchantId)
    {
        $commandList = $this->actionResourceModel->getByMerchantId($merchantId);
        $commandListChunks = array_chunk($commandList, self::CHUNK_SIZE);

        foreach ($commandListChunks as $commandListChunk) {
            $commands = [];
            $commandIds = [];
            foreach ($commandListChunk as $commandData) {
                $commands[] = [
                    'name' => $commandData['command'],
                    'body' => $commandData['command_body'],
                ];
                $commandIds[] = $commandData['id'];
            }

            if (!empty($commands)) {
                $this->apiClient->pushCommands($account, $commands);
                $this->actionResourceModel->deleteByIds($commandIds);
            }
        }
    }

    /**
     * @param AccountInterface $account
     * @throws LocalizedException
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Throwable
     */
    public function pushCommandsForMerchant(AccountInterface $account): void
    {
        $merchantId = $account->getMerchantId();
        $accountReadyToPushCommands = $this->accountManagement->isAccountReadyToPushCommands($account);

        if ($accountReadyToPushCommands) {
            $this->processCommands($account, $merchantId);
        }
    }
}
