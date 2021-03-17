<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\Amazon\Model\Stock;

use Magento\Framework\App\ResourceConnection;

/**
 * Class LegacyStock
 */
class LegacyStock implements StockInterface
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
     * LegacyStock constructor.
     * @param ResourceConnection $resourceConnection
     */
    public function __construct(
        ResourceConnection $resourceConnection
    ) {
        $this->resourceConnection = $resourceConnection;
        $this->connection = $resourceConnection->getConnection();
    }

    /**
     * @param int $merchantId
     */
    public function setAmazonListingQtyToStockQty(int $merchantId)
    {
        $connection = $this->connection;

        $listingTable = $this->resourceConnection->getTableName('channel_amazon_listing');
        $indexTable = $this->resourceConnection->getTableName('channel_amazon_quantity_index');
        $stockTable = $this->resourceConnection->getTableName('cataloginventory_stock_item');
        $coreConfigTable = $this->resourceConnection->getTableName('core_config_data');

        $select = $connection->select()->from(
            false,
            [
                'quantity' => 'FLOOR(stock.qty)'
            ]
        )->joinInner(
            ['listing' => $listingTable],
            'listing.id = index_table.parent_id',
            []
        )->joinInner(
            ['stock' => $stockTable],
            'stock.product_id = listing.catalog_product_id',
            []
        )->joinLeft(
            ['core_config' => $coreConfigTable],
            'core_config.path = "cataloginventory/item_options/manage_stock"',
            []
        )->where(
            'index_table.merchant_id = ?',
            (int)$merchantId
        )->where(
            '(stock.manage_stock = 1 AND stock.use_config_manage_stock = 0) ' .
            ' OR (stock.use_config_manage_stock = 1 AND (core_config.value = 1 OR core_config.value IS NULL))'
        );

        $update = $connection->updateFromSelect($select, ['index_table' => $indexTable]);
        $connection->query($update);
    }
}
