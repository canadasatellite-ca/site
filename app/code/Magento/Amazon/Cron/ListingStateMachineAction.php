<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\Amazon\Cron;

use Magento\Amazon\Api\AccountManagementInterface;
use Magento\Amazon\Api\ConfigManagementInterface;
use Magento\Amazon\Api\Data\AccountInterface;
use Magento\Amazon\Api\ListingManagementInterface;
use Magento\Amazon\Cache\StoresWithOrdersThatCannotBeImported;
use Magento\Amazon\Comm\Amazon\Fetch;
use Magento\Amazon\Comm\Amazon\Push;
use Magento\Amazon\Logger\AscClientLogger;
use Magento\Amazon\Model\Order\OrderHandlerResolver;
use Magento\Amazon\Model\ResourceModel\Amazon\Account\CollectionFactory;
use Magento\Amazon\Model\ResourceModel\Amazon\Error\Log as ErrorResourceModel;
use Magento\Amazon\Model\ResourceModel\Amazon\Listing as ListingResourceModel;
use Magento\Amazon\Model\ResourceModel\Amazon\Listing\Log as LogResourceModel;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Lock\LockManagerInterface;

/**
 * Class ListingStateMachineAction
 */
class ListingStateMachineAction
{
    /** Category tree cache id */
    const CLEAR_LOGS_INTERVAL_DAYS_DEFAULT = 7;

    /** @var CollectionFactory $collectionFactory */
    private $collectionFactory;
    /** @var ListingResourceModel $listingResourceModel */
    private $listingResourceModel;
    /** @var LogResourceModel $logResourceModel */
    private $logResourceModel;
    /** @var ErrorResourceModel $errorResourceModel */
    private $errorResourceModel;
    /** @var ListingManagementInterface $listingManagement */
    private $listingManagement;
    /** @var ConfigManagementInterface $configManagement */
    private $configManagement;
    /** @var OrderHandlerResolver */
    private $orderHandlerResolver;
    /** @var AccountManagementInterface */
    private $accountManagement;
    /** @var Fetch $fetch */
    private $fetch;
    /** @var Push $push */
    private $push;
    /** @var AscClientLogger $ascClientLogger */
    protected $ascClientLogger;
    /**
     * @var StoresWithOrdersThatCannotBeImported
     */
    private $storesWithOrdersThatCannotBeImported;
    /**
     * @var LockManagerInterface
     */
    private $lockManager;

    /**
     * @param CollectionFactory $collectionFactory
     * @param ListingResourceModel $listingResourceModel
     * @param LogResourceModel $logResourceModel
     * @param ErrorResourceModel $errorResourceModel
     * @param ListingManagementInterface $listingManagement
     * @param ConfigManagementInterface $configManagement
     * @param OrderHandlerResolver $orderHandlerResolver
     * @param AccountManagementInterface $accountManagement
     * @param Fetch $fetch
     * @param Push $push
     * @param AscClientLogger $ascClientLogger
     * @param StoresWithOrdersThatCannotBeImported $storesWithOrdersThatCannotBeImported
     * @param LockManagerInterface $lockManager
     */
    public function __construct(
        CollectionFactory $collectionFactory,
        ListingResourceModel $listingResourceModel,
        LogResourceModel $logResourceModel,
        ErrorResourceModel $errorResourceModel,
        ListingManagementInterface $listingManagement,
        ConfigManagementInterface $configManagement,
        OrderHandlerResolver $orderHandlerResolver,
        AccountManagementInterface $accountManagement,
        Fetch $fetch,
        Push $push,
        AscClientLogger $ascClientLogger,
        StoresWithOrdersThatCannotBeImported $storesWithOrdersThatCannotBeImported,
        LockManagerInterface $lockManager
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->listingResourceModel = $listingResourceModel;
        $this->logResourceModel = $logResourceModel;
        $this->errorResourceModel = $errorResourceModel;
        $this->listingManagement = $listingManagement;
        $this->configManagement = $configManagement;
        $this->orderHandlerResolver = $orderHandlerResolver;
        $this->accountManagement = $accountManagement;
        $this->fetch = $fetch;
        $this->push = $push;
        $this->ascClientLogger = $ascClientLogger;
        $this->storesWithOrdersThatCannotBeImported = $storesWithOrdersThatCannotBeImported;
        $this->lockManager = $lockManager;
    }

    /**
     * Executes the custom CRON activities associated
     * with the listing state machine of Amazon Sales Channel
     *
     * If Magento CRON is disabled in settings, it suppresses
     * execution.
     *
     * @return void
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Throwable
     */
    public function execute()
    {
        $this->ascClientLogger->debug('Attempt to run ASC listing state machine.');
        $isAmazonCron = $this->configManagement->getCronSourceSetting();
        if ($isAmazonCron && !$this->lockManager->isLocked(\Magento\Amazon\Console\Cron\Amazon\Run::LOCK_NAME)) {
            $this->runTasks();
        }
    }

    /**
     * Returns user set interval to clear logs (in days)
     *
     * @return int
     * @throws LocalizedException
     */
    private function getLogHistoryDays(): int
    {
        $this->ascClientLogger->debug('Returns user set interval to clear logs.');
        $logHistoryDays = (int)$this->configManagement->getLogHistorySetting();
        if (1 <= $logHistoryDays) {
            return $logHistoryDays;
        }

        return self::CLEAR_LOGS_INTERVAL_DAYS_DEFAULT;
    }

    /**
     * Executes the listing state machine of Amazon Sales Channel.
     *
     * @return void
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Throwable
     */
    public function runTasks()
    {
        $this->storesWithOrdersThatCannotBeImported->clean();

        $this->ascClientLogger->debug('Executes ASC listing state machine.');
        $collection = $this->collectionFactory->create();
        $collection->addFieldToFilter('is_active', 1);

        /** @var AccountInterface[] $accounts */
        $accounts = $collection->getItems();
        foreach ($accounts as $account) {
            try {
                $this->fetch->fetchLogsForMerchant($account);
            } catch (\Exception $e) {
                $this->ascClientLogger->critical(
                    'Cannot fetch logs for merchant',
                    ['exception' => $e, 'account' => $account]
                );
            }
            try {
                $this->scheduleActionsForMerchant($account);
            } catch (\Exception $e) {
                $this->ascClientLogger->critical(
                    'Cannot schedule actions for merchant',
                    ['exception' => $e, 'account' => $account]
                );
            }
            try {
                $this->push->pushCommandsForMerchant($account);
            } catch (\Exception $e) {
                $this->ascClientLogger->critical(
                    'Cannot push updates for merchant',
                    ['exception' => $e, 'account' => $account]
                );
            }
        }

        $this->storesWithOrdersThatCannotBeImported->persist();
    }

    /**
     * @param $account
     * @throws LocalizedException
     * @throws \Zend_Db_Select_Exception
     */
    private function scheduleActionsForMerchant(AccountInterface $account): void
    {
        $accountReadyToPushCommands = $this->accountManagement->isAccountReadyToPushCommands($account);

        if ($accountReadyToPushCommands) {
            $merchantId = (int)$account->getMerchantId();
            $orderHandler = $this->orderHandlerResolver->resolve();

            // process scheduled listing additions (if set to automatically list) and removals
            $this->listingManagement->scheduleListingInsertions($merchantId);
            $this->listingResourceModel->scheduleListingRemovals($merchantId);
            $this->listingResourceModel->scheduleConditionOverrides($merchantId);
            $orderHandler->synchronizeOrders($merchantId);
            $logHistoryDays = $this->getLogHistoryDays();
            $this->logResourceModel->clearLogs($logHistoryDays);
            $this->errorResourceModel->clearLogs($logHistoryDays);
        }
    }
}
