<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\Amazon\Model\Stock;

use Magento\Amazon\Api\ListingRuleRepositoryInterface;
use Magento\Amazon\Msi\MsiApi;
use Magento\Framework\App\ResourceConnection;

/**
 * Class MsiStock
 */
class MsiStock implements StockInterface
{
    /**
     * @var ResourceConnection
     */
    private $resourceConnection;

    /**
     * @var \Magento\Framework\DB\Adapter\AdapterInterface
     */
    private $connection;

    /**
     * @var ListingRuleRepositoryInterface
     */
    private $listingRuleRepository;

    /**
     * @var MsiApi
     */
    private $msiApi;

    /**
     * MsiStock constructor.
     * @param ResourceConnection $resourceConnection
     * @param ListingRuleRepositoryInterface $listingRuleRepository
     * @param MsiApi $msiApi
     */
    public function __construct(
        ResourceConnection $resourceConnection,
        ListingRuleRepositoryInterface $listingRuleRepository,
        MsiApi $msiApi
    ) {
        $this->resourceConnection = $resourceConnection;
        $this->connection = $resourceConnection->getConnection();
        $this->listingRuleRepository = $listingRuleRepository;
        $this->msiApi = $msiApi;
    }

    /**
     * @param int $merchantId
     */
    public function setAmazonListingQtyToStockQty(int $merchantId)
    {
        $connection = $this->connection;

        $listingRule = $this->listingRuleRepository->getByMerchantId($merchantId);
        $stock = $this->msiApi->getStockByWebsiteId((int)$listingRule->getWebsiteId());
        $stockId = $stock->getStockId();

        $indexTable = $this->resourceConnection->getTableName('channel_amazon_quantity_index');
        $listingTable = $this->resourceConnection->getTableName('channel_amazon_listing');
        $legacyStockTable = $this->resourceConnection->getTableName('cataloginventory_stock_item');
        $stockTable = $this->msiApi->getStockIndexTableName($stockId);
        $coreConfigTable = $this->resourceConnection->getTableName('core_config_data');

        $select = $connection->select()->from(
            false,
            [
                'quantity' => 'IF (
                    stock.is_salable,
                    FLOOR(IFNULL(stock.quantity, 0)),
                    0
                )'
            ]
        )->joinInner(
            ['listing' => $listingTable],
            'listing.id = index_table.parent_id',
            []
        )->joinInner(
            ['legacy_stock' => $legacyStockTable],
            'legacy_stock.product_id = listing.catalog_product_id',
            []
        )->joinLeft(
            ['stock' => $stockTable],
            'stock.sku = listing.catalog_sku',
            []
        )->joinLeft(
            ['core_config' => $coreConfigTable],
            'core_config.path = "cataloginventory/item_options/manage_stock"',
            []
        )->where(
            '(legacy_stock.manage_stock = 1 AND legacy_stock.use_config_manage_stock = 0) ' .
            ' OR (legacy_stock.use_config_manage_stock = 1 AND (core_config.value = 1 OR core_config.value IS NULL))'
        )->where(
            'index_table.merchant_id = ?',
            (int)$merchantId
        );

        $update = $connection->updateFromSelect($select, ['index_table' => $indexTable]);
        $connection->query($update);
    }
}
