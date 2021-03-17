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
 * Class MsiStockWithReservations
 */
class MsiStockWithReservations implements StockInterface
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
        $reservationTable = $this->resourceConnection->getTableName('inventory_reservation');

        $select = $connection->select()->from(
            ['listing' => $listingTable],
            [
                'quantity' => 'IF (
                        stock.is_salable,
                        FLOOR(GREATEST( IFNULL(stock.quantity, 0) + SUM(IFNULL(reservations.quantity, 0)), 0)),
                        0
                    )',
                'parent_id' => 'listing.id',
                'merchant_id' => new \Zend_Db_Expr($merchantId)
            ]
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
            'listing.merchant_id = ?',
            (int)$merchantId
        )->joinLeft(
            ['reservations' => $reservationTable],
            'reservations.sku = listing.catalog_sku AND reservations.stock_id = ' . $stockId,
            []
        )->group('listing.catalog_sku');

        $tempIndexTable = $indexTable . '_temp_' . $merchantId;
        $connection->dropTemporaryTable($tempIndexTable);
        $connection->createTemporaryTableLike($tempIndexTable, $indexTable);
        $insert = $connection->insertFromSelect($select, $tempIndexTable, ['quantity', 'parent_id', 'merchant_id']);
        $connection->query($insert);

        $selectForUpdate = $connection->select()->from(
            false,
            [
                'quantity' => 'temp.quantity'
            ]
        )->joinInner(
            ['temp' => $tempIndexTable],
            'temp.parent_id = index_table.parent_id',
            []
        );

        $update = $connection->updateFromSelect($selectForUpdate, ['index_table' => $indexTable]);

        $connection->query($update);
    }
}
