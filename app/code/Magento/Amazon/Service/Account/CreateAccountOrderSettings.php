<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\Amazon\Service\Account;

use Magento\Amazon\Api\AccountOrderRepositoryInterface;
use Magento\Amazon\Api\Data\AccountInterface;
use Magento\Amazon\Model\Amazon\Account\OrderFactory;
use Magento\Store\Api\Data\WebsiteInterface;

class CreateAccountOrderSettings
{
    /**
     * @var OrderFactory
     */
    private $accountOrderFactory;
    /**
     * @var AccountOrderRepositoryInterface
     */
    private $accountOrderRepository;
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    public function __construct(
        OrderFactory $accountOrderFactory,
        AccountOrderRepositoryInterface $accountOrderRepository,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->accountOrderFactory = $accountOrderFactory;
        $this->accountOrderRepository = $accountOrderRepository;
        $this->storeManager = $storeManager;
    }

    /**
     * @param AccountInterface $account
     * @param WebsiteInterface $website
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function createForAccount(AccountInterface $account, WebsiteInterface $website): void
    {
        /** @var \Magento\Amazon\Model\Amazon\Account\Order $orderSettings */
        $orderSettings = $this->accountOrderFactory->create();
        $orderSettings->setMerchantId($account->getMerchantId());
        $orderSettings->setDefaultStore($this->getDefaultStoreIdForWebsite($website));
        $orderSettings->setOrderIsActive(1);
        $orderSettings->setCustomerIsActive(0);
        $orderSettings->setIsExternalOrderId(0);
        $orderSettings->setReserve(1);
        $orderSettings->setCustomStatusIsActive(0);
        $this->accountOrderRepository->save($orderSettings);
    }

    private function getDefaultStoreIdForWebsite(WebsiteInterface $website): int
    {
        return (int)$this->storeManager->getGroup($website->getDefaultGroupId())->getDefaultStoreId();
    }
}
