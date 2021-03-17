<?php

/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Amazon\Test\Integration\Model\Indexer;

use Magento\Amazon\Model\Amazon\Account;
use Magento\Amazon\Model\Indexer\PricingIndexer;
use Magento\Amazon\Model\ResourceModel\Amazon\Listing\Collection as ListingCollection;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

abstract class PricingTestCase extends TestCase
{
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * @var \Magento\Amazon\Model\Indexer\PricingIndexer
     */
    protected $indexer;

    protected function setUp(): void
    {
        $this->objectManager = Bootstrap::getObjectManager();
        $this->indexer = $this->objectManager->create(
            PricingIndexer::class
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

        /** @var \Magento\Amazon\Model\Amazon\Account $account */
        $account = $accountCollectionFactory->create()
            ->addFieldToFilter('name', $name)
            ->getFirstItem();

        return $account;
    }

    /**
     * @param string $fileName
     * @return string
     */
    protected function getTestFileContents(string $fileName)
    {
        /** @var string $content */
        $content = file_get_contents(__DIR__ . '/../../../_files/' . $fileName);

        return $content;
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
    protected function getListingPriceBySku(ListingCollection $listings, string $sku)
    {
        /** @var \Magento\Amazon\Model\Amazon\Listing $listing */
        $listing = $listings->getItemByColumnValue('catalog_sku', $sku);

        $this->assertNotNull($listing, 'Listing does not exist for sku ' . $sku);

        return $listing->getListPrice();
    }

    /**
     * @param ListingCollection $listings
     * @param string $sku
     * @return float|null
     * @throws \Exception
     */
    protected function getLandedPriceBySku(ListingCollection $listings, string $sku)
    {
        /** @var \Magento\Amazon\Model\Amazon\Listing $listing */
        $listing = $listings->getItemByColumnValue('catalog_sku', $sku);

        $this->assertNotNull($listing, 'Listing does not exist for sku ' . $sku);

        return $listing->getLandedPrice();
    }
}
