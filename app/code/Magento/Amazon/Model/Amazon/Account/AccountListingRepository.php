<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Amazon\Model\Amazon\Account;

use Magento\Amazon\Api\AccountListingRepositoryInterface;
use Magento\Amazon\Api\Data\AccountListingInterface;
use Magento\Amazon\Model\ResourceModel\Amazon\Account\Listing as ResourceModel;
use Magento\Amazon\Model\ResourceModel\Amazon\Account\Listing\CollectionFactory;
use Magento\Framework\Exception\CouldNotSaveException;

/**
 * Class AccountListingRepository
 */
class AccountListingRepository implements AccountListingRepositoryInterface
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
    public function save(AccountListingInterface $account)
    {
        try {
            $this->resourceModel->save($account);
        } catch (\Exception $e) {
            $phrase = __('Unable to save account settings. Please try again.');
            throw new CouldNotSaveException($phrase, $e);
        }

        return $account;
    }

    /**
     * {@inheritdoc}
     */
    public function getByMerchantId($merchantId)
    {
        /** @var \Magento\Amazon\Model\ResourceModel\Amazon\Account\Listing\Collection $collection */
        $collection = $this->collectionFactory->create();
        $collection->addFieldToFilter('merchant_id', $merchantId);

        return $collection->getFirstItem();
    }
}
