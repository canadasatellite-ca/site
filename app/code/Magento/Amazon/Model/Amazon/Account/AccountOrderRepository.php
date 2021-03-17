<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Amazon\Model\Amazon\Account;

use Magento\Amazon\Api\AccountOrderRepositoryInterface;
use Magento\Amazon\Api\Data\AccountOrderInterface;
use Magento\Amazon\Model\ResourceModel\Amazon\Account\Order as ResourceModel;
use Magento\Amazon\Model\ResourceModel\Amazon\Account\Order\CollectionFactory;
use Magento\Framework\Exception\CouldNotSaveException;

/**
 * Class AccountOrderRepository
 */
class AccountOrderRepository implements AccountOrderRepositoryInterface
{
    /** @var ResourceModel $resourceModel */
    protected $resourceModel;
    /** @var CollectionFactory $collectionFactory */
    protected $collectionFactory;

    /**
     * @param ResourceModel $resourceModel
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(
        ResourceModel $resourceModel,
        CollectionFactory $collectionFactory
    ) {
        $this->resourceModel = $resourceModel;
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function save(AccountOrderInterface $account)
    {
        try {
            $this->resourceModel->save($account);
        } catch (\Exception $e) {
            $phrase = __('Unable to save account settings. Please try again.');
            throw new CouldNotSaveException($phrase);
        }

        return $account;
    }

    /**
     * {@inheritdoc}
     */
    public function getByMerchantId($merchantId)
    {
        /** @var CollectionFactory */
        $collection = $this->collectionFactory->create();
        $collection->addFieldToFilter('merchant_id', $merchantId);

        return $collection->getFirstItem();
    }
}
