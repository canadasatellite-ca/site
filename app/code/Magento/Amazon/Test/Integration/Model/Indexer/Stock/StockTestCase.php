<?php

/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Amazon\Test\Integration\Model\Indexer;

use Magento\Amazon\Model\Amazon\Account;
use Magento\Amazon\Model\Indexer\StockIndexer;
use Magento\Amazon\Model\ResourceModel\Amazon\Listing\Collection as ListingCollection;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

abstract class StockTestCase extends TestCase
{
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * @var \Magento\Amazon\Model\Indexer\StockIndexer
     */
    protected $indexer;

    protected function setUp(): void
    {
        $this->objectManager = Bootstrap::getObjectManager();
        $this->indexer = $this->objectManager->create(
            StockIndexer::class
        );
    }

    /**
     * @param string $name
     * @return Account
     */
    protected function getAccountByName(string $name)
    {
        $accountCollectionFactory = $this->objectManager->create(
            \Magento\Amazon\Model\ResourceModel\Amazon\Account\CollectionFactory::class
        );
        return $accountCollectionFactory->create()
            ->addFieldToFilter('name', $name)
            ->getFirstItem();
    }

    /**
     * @param Account $account
     * @return ListingCollection
     */
    protected function getListingsByAccount(Account $account)
    {
        $listingCollectionFactory = $this->objectManager->create(
            \Magento\Amazon\Model\ResourceModel\Amazon\Listing\CollectionFactory::class
        );
        return $listingCollectionFactory->create()
            ->addFieldToFilter('merchant_id', $account->getMerchantId());
    }

    /**
     * @param ListingCollection $listings
     * @param string $sku
     * @return float|null
     * @throws \Exception
     */
    protected function getQuantityBySku(ListingCollection $listings, string $sku)
    {
        /** @var \Magento\Amazon\Model\Amazon\Listing $listing */
        $listing = $listings->getItemByColumnValue('catalog_sku', $sku);

        $this->assertNotNull($listing, 'Listing does not exist for sku ' . $sku);

        return $listing->getQty();
    }
}
