<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Amazon\Model\Amazon;

use Magento\Amazon\Api\Data\ListingInterface;
use Magento\Amazon\Api\ListingRepositoryInterface;
use Magento\Amazon\Model\ResourceModel\Amazon\Listing as ResourceModel;
use Magento\Amazon\Model\ResourceModel\Amazon\Listing\CollectionFactory;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Class ListingRepository
 */
class ListingRepository implements ListingRepositoryInterface
{
    /** @var ListingFactory $listingFactory */
    protected $listingFactory;
    /** @var ResourceModel $resourceModel */
    protected $resourceModel;
    /** @var CollectionFactory $collectionFactory */
    protected $collectionFactory;

    /**
     * @param ListingFactory $listingFactory
     * @param ResourceModel $resourceModel
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(
        ListingFactory $listingFactory,
        ResourceModel $resourceModel,
        CollectionFactory $collectionFactory
    ) {
        $this->listingFactory = $listingFactory;
        $this->resourceModel = $resourceModel;
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function save(ListingInterface $listing)
    {
        try {
            // save amazon order
            $this->resourceModel->save($listing);
        } catch (\Exception $e) {
            $phrase = __('Unable to save amazon listing. Please try again.');
            throw new CouldNotSaveException($phrase);
        }

        return $listing;
    }

    /**
     * {@inheritdoc}
     */
    public function getById($id, $empty = false)
    {
        /** @var Listing $listing */
        $listing = $this->listingFactory->create();

        $listing->load($id);

        if (!$listing->getId()) {
            // if return empty is not set
            if (!$empty) {
                $phrase = __('The requested listing does not exist.');
                throw new NoSuchEntityException($phrase);
            }
        }

        return $listing;
    }

    /**
     * {@inheritdoc}
     */
    public function getCatalogSkuBySellerSku($sellerSku, $merchantId)
    {
        /** @var Collection $collection */
        $collection = $this->collectionFactory->create();

        // add filters
        $collection->addFieldToFilter('seller_sku', $sellerSku);
        $collection->addFieldToFilter('merchant_id', $merchantId);
        $sku = $collection->getFirstItem()->getCatalogSku();

        return ($sku) ? $sku : $sellerSku;
    }

    /**
     * {@inheritdoc}
     */
    public function getBySellerSkuAndMerchantId(string $sellerSku, int $merchantId)
    {
        $collection = $this->collectionFactory->create();

        // add filters
        $collection->addFieldToFilter('seller_sku', $sellerSku);
        $collection->addFieldToFilter('merchant_id', $merchantId);
        return $collection->getFirstItem();
    }

    /**
     * {@inheritdoc}
     */
    public function delete(ListingInterface $listing)
    {
        try {
            $this->resourceModel->delete($listing);
        } catch (\Exception $e) {
            return false;
        }

        return true;
    }
}
