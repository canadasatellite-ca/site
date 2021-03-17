<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Amazon\Model\Amazon;

use Magento\Amazon\Api\AccountRepositoryInterface;
use Magento\Amazon\Api\Data\AccountInterface;
use Magento\Amazon\Model\ResourceModel\Amazon\Account as AccountResourceModel;
use Magento\Amazon\Model\ResourceModel\Amazon\Account\CollectionFactory;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Psr\Log\LoggerInterface;

/**
 * Class AccountRepository
 */
class AccountRepository implements AccountRepositoryInterface
{
    /** @var AccountFactory $accountFactory */
    protected $accountFactory;
    /** @var AccountResourceModel $resourceModel */
    protected $resourceModel;
    /** @var CollectionFactory $collectionFactory */
    protected $collectionFactory;

    /**
     * @param AccountFactory $accountFactory
     * @param AccountResourceModel $resourceModel
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(
        AccountFactory $accountFactory,
        AccountResourceModel $resourceModel,
        CollectionFactory $collectionFactory
    ) {
        $this->accountFactory = $accountFactory;
        $this->resourceModel = $resourceModel;
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function save(AccountInterface $account)
    {
        try {
            $this->resourceModel->save($account);
        } catch (\Exception $e) {
            /** @var LoggerInterface $logger */
            $logger = ObjectManager::getInstance()->get(LoggerInterface::class);
            $logger->critical($e);
            $phrase = __('Unable to save account settings. Please try again.');
            throw new CouldNotSaveException($phrase, $e);
        }

        return $account;
    }

    /**
     * {@inheritdoc}
     */
    public function delete(AccountInterface $account)
    {
        try {
            $this->resourceModel->delete($account);
        } catch (\Exception $e) {
            // already deleted
            return;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getByMerchantId($merchantId, $empty = false)
    {
        /** @var Account $account */
        $account = $this->accountFactory->create();

        $account->load($merchantId);

        if (!$account->getMerchantId()) {
            // if return empty is not set
            if (!$empty) {
                $phrase = __('The requested account does not exist.');
                throw new NoSuchEntityException($phrase);
            }
        }

        return $account;
    }

    /**
     * {@inheritdoc}
     */
    public function getByUuid(string $uuid)
    {
        /** @var Account $account */
        $account = $this->accountFactory->create();

        $account->load($uuid, 'uuid');

        if (!$account->getMerchantId()) {
            $phrase = __('The requested account does not exist.');
            throw new NoSuchEntityException($phrase);
        }

        return $account;
    }

    /**
     * {@inheritdoc}
     */
    public function isAccount()
    {
        return $this->collectionFactory
            ->create()
            ->getFirstItem()
            ->getMerchantId();
    }
}
