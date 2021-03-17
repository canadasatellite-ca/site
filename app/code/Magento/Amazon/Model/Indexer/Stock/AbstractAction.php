<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Amazon\Model\Indexer\Stock;

use Magento\Amazon\Api\AccountListingRepositoryInterface;
use Magento\Amazon\Api\Data\AccountInterface;
use Magento\Amazon\Api\Data\ListingInterface;
use Magento\Amazon\Api\ListingManagementInterface;
use Magento\Amazon\Domain\Command\CommandDispatcher;
use Magento\Amazon\Domain\Command\UpdateInventoryQtyFactory;
use Magento\Amazon\Logger\AscClientLogger;
use Magento\Amazon\Model\Amazon\Definitions;
use Magento\Amazon\Model\ResourceModel\Amazon\Account\CollectionFactory;
use Magento\Amazon\Model\ResourceModel\Amazon\Listing as ListingResourceModel;
use Magento\Amazon\Model\ResourceModel\Amazon\Listing\CollectionFactory as ListingCollectionFactory;
use Magento\Amazon\Model\ResourceModel\Amazon\Listing\Log as LogResourceModel;
use Magento\Amazon\Model\ResourceModel\Amazon\Listing\Rule\CollectionFactory as RuleCollectionFactory;
use Magento\Amazon\Model\Stock\StockInterface;
use Magento\Amazon\Model\Stock\StockResolver;
use Magento\CatalogRule\Api\Data\RuleInterface as CatalogRuleInterface;
use Magento\CatalogRule\Model\RuleFactory;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\Serialize\Serializer\Json;
use Zend_Db_Statement_Exception;

/**
 * Class AbstractAction
 */
abstract class AbstractAction
{
    /** @var int */
    const CHUNK_SIZE = 1000;

    /** @var ResourceConnection $resourceConnection */
    protected $resourceConnection;
    /** @var CollectionFactory $collectionFactory */
    protected $collectionFactory;
    /** @var ListingCollectionFactory $listingCollectionFactory */
    protected $listingCollectionFactory;
    /** @var RuleCollectionFactory $ruleCollectionFactory */
    protected $ruleCollectionFactory;
    /** @var ListingResourceModel $listingResourceModel */
    protected $listingResourceModel;
    /** @var LogResourceModel $logResourceModel */
    protected $logResourceModel;
    /** @var AccountListingRepositoryInterface $accountListingRepository */
    protected $accountListingRepository;
    /** @var ListingManagementInterface $listingManagement */
    protected $listingManagement;
    /** @var RuleFactory $ruleFactory */
    protected $ruleFactory;
    /** @var Json $serializer */
    protected $serializer;
    /** @var AscClientLogger $ascClientLogger */
    protected $ascClientLogger;

    /**
     * @var \Magento\Amazon\Domain\Command\CommandDispatcher
     */
    private $commandDispatcher;

    /**
     * @var \Magento\Amazon\Domain\Command\UpdateInventoryQtyFactory
     */
    private $updateInventoryQtyCommandFactory;

    /**
     * @var StockResolver
     */
    private $stockResolver;

    /**
     * @var AdapterInterface
     */
    private $connection;

    /**
     * @param ResourceConnection $resourceConnection
     * @param CollectionFactory $collectionFactory
     * @param ListingCollectionFactory $listingCollectionFactory
     * @param RuleCollectionFactory $ruleCollectionFactory
     * @param ListingResourceModel $listingResourceModel
     * @param LogResourceModel $logResourceModel
     * @param AccountListingRepositoryInterface $accountListingRepository
     * @param ListingManagementInterface $listingManagement
     * @param RuleFactory $ruleFactory
     * @param Json $serializer
     * @param \Magento\Amazon\Domain\Command\CommandDispatcher $commandDispatcher
     * @param \Magento\Amazon\Domain\Command\UpdateInventoryQtyFactory $updateInventoryQtyFactory
     * @param StockResolver $stockResolver
     * @param AscClientLogger $ascClientLogger
     */
    public function __construct(
        ResourceConnection $resourceConnection,
        CollectionFactory $collectionFactory,
        ListingCollectionFactory $listingCollectionFactory,
        RuleCollectionFactory $ruleCollectionFactory,
        ListingResourceModel $listingResourceModel,
        LogResourceModel $logResourceModel,
        AccountListingRepositoryInterface $accountListingRepository,
        ListingManagementInterface $listingManagement,
        RuleFactory $ruleFactory,
        Json $serializer,
        CommandDispatcher $commandDispatcher,
        UpdateInventoryQtyFactory $updateInventoryQtyFactory,
        StockResolver $stockResolver,
        AscClientLogger $ascClientLogger
    ) {
        $this->connection = $resourceConnection->getConnection();
        $this->collectionFactory = $collectionFactory;
        $this->listingCollectionFactory = $listingCollectionFactory;
        $this->ruleCollectionFactory = $ruleCollectionFactory;
        $this->listingResourceModel = $listingResourceModel;
        $this->logResourceModel = $logResourceModel;
        $this->accountListingRepository = $accountListingRepository;
        $this->listingManagement = $listingManagement;
        $this->ruleFactory = $ruleFactory;
        $this->serializer = $serializer;
        $this->updateInventoryQtyCommandFactory = $updateInventoryQtyFactory;
        $this->commandDispatcher = $commandDispatcher;
        $this->stockResolver = $stockResolver;
        $this->ascClientLogger = $ascClientLogger;
        $this->resourceConnection = $resourceConnection;
    }

    /**
     * Reindex all marketplaces
     *
     * @return void
     */
    public function reindexAll()
    {
        /**
         * removes product association from ASC for products found with
         * listing status of ineligible or error, or if no core magento
         * catalog entry found. Also, if no ASIN found, set list status
         * as 'validate ASIN'.
         */
        $this->removeProductAssociation();

        /** @var AdapterInterface */
        $connection = $this->connection;
        $connection->beginTransaction();

        try {
            $this->synchronizeStockLevels();
            $this->synchronizeSkus([]);
            $connection->commit();
        } catch (\Exception $e) {
            $connection->rollBack();
            $this->ascClientLogger->critical(
                'Rolled back SQL transaction due to exception',
                [
                    'exception' => $e
                ]
            );
        }
    }

    /**
     * Reindex partial
     *
     * @param array
     * @return void
     */
    public function reindexPartial(array $ids = [])
    {
        /** @var array */
        $ids = array_unique($ids);

        /** @var AdapterInterface */
        $connection = $this->connection;
        $connection->beginTransaction();

        try {
            $this->synchronizeStockLevels($ids);
            $this->synchronizeSkus($ids);
            $connection->commit();
        } catch (\Exception $e) {
            $connection->rollBack();
            $this->ascClientLogger->critical(
                'Rolled back SQL transaction due to exception',
                [
                    'exception' => $e
                ]
            );
        }
    }

    /**
     * Updates product listing eligibility and stock levels
     *
     * @return void
     * @throws \Zend_Db_Select_Exception
     * @throws \Magento\Framework\Exception\LocalizedException
     * @var array $ids
     */
    private function synchronizeStockLevels(array $ids = [])
    {
        $processedIds = [];

        $collection = $this->collectionFactory->create();

        $collection->addFieldToFilter('is_active', 1);

        $collection->getSelect()->order(['is_active ASC', 'created_on ASC']);

        /** @var AccountInterface $account */
        foreach ($collection as $account) {
            $merchantId = (int)$account->getMerchantId();

            if (empty($ids)) {
                $this->setListingEligibility($account, $processedIds, $ids);
                $this->listingResourceModel->scheduleAsinLookups($merchantId, $ids);
                $this->listingResourceModel->scheduleGeneralSearchLookups($merchantId, $ids);
                $this->reindexStock($account, $ids);
                $processedIds = array_merge($processedIds, $this->getUnifiedListingIds($account));
                continue;
            }

            foreach (array_chunk($ids, self::CHUNK_SIZE) as $chunkIds) {
                $this->setListingEligibility($account, $processedIds, $chunkIds);
                $this->listingResourceModel->scheduleAsinLookups($merchantId, $chunkIds);
                $this->listingResourceModel->scheduleGeneralSearchLookups($merchantId, $chunkIds);
                $this->reindexStock($account, $chunkIds);
            }

            $processedIds = array_merge($processedIds, $this->getUnifiedListingIds($account));
        }

        // synchronize catalog product quantity with amazon marketplace quantity
        $this->setUpdateFlag();
        $this->syncAmazonListingQuantities();
        $this->scheduleStockUpdates();

        // clear index table
        $this->clearUpdateFlag();
        $this->clearIndexTable();
    }

    /**
     * Clears ASC product association by moving to third party listing
     * all products no longer found in Magento core catalog, ineligible,
     * or having listing error status.
     *
     * Also, sets listing status to 'validating asin' for all products
     * found to be missing an ASIN value0
     * .
     * @return void
     */
    private function removeProductAssociation()
    {
        /** @var array */
        $results = $this->collectDisassociatedListings();

        if ($results && !empty($results)) {
            $this->setThirdpartyListings($results);
        }

        /** @var array */
        $results = $this->collectMissingProductIds();

        if ($results && !empty($results)) {
            $this->setThirdpartyListings($results);
        }

        /** @var array */
        $results = $this->collectMissingAsins();

        if ($results && !empty($results)) {
            $this->setValidateAsin($results);
        }
    }

    /**
     * Collects and returns listing IDs (id) of ASC listings no longer associated
     * with a Magento core catalog product, or no longer eligible for ASC listing.
     *
     * @return array
     */
    private function collectDisassociatedListings(): array
    {
        /** @var AdapterInterface */
        $connection = $this->connection;

        /** @var string */
        $listingTable = $this->resourceConnection->getTableName('channel_amazon_listing');
        /** @var string */
        $productTable = $this->resourceConnection->getTableName('catalog_product_entity');
        /** @var array */
        $statuses = [
            Definitions::ERROR_LIST_STATUS,
            Definitions::ACTIVE_LIST_STATUS,
            Definitions::NO_LONGER_ELIGIBLE_STATUS

        ];// query
        $result = $connection->select()->from(
            ['listing' => $listingTable],
            []
        )->joinLeft(
            ['cpe' => $productTable],
            'cpe.entity_id = listing.catalog_product_id',
            []
        )->where(
            'listing.catalog_product_id IS NOT NULL'
        )->where(
            'listing.list_status IN (?)',
            $statuses
        )->where(
            'cpe.sku IS NULL'
        )->columns(
            [
                'id' => 'listing.id'
            ]
        );

        return $connection->fetchAll($result);
    }

    /**
     * Collects and returns listing IDs (id) of ASC listings that
     * have a missing Magento core catalog product association
     *
     * @return array
     */
    private function collectMissingProductIds(): array
    {
        /** @var AdapterInterface */
        $connection = $this->connection;

        /** @var string */
        $listingTable = $this->resourceConnection->getTableName('channel_amazon_listing');
        /** @var array */
        $statuses = [
            Definitions::ERROR_LIST_STATUS,
            Definitions::ACTIVE_LIST_STATUS

        ];// query
        $result = $connection->select()->from(
            ['listing' => $listingTable],
            []
        )->where(
            'listing.catalog_product_id IS NULL'
        )->where(
            'listing.list_status IN (?)',
            $statuses
        )->columns(
            [
                'id' => 'listing.id'
            ]
        );

        return $connection->fetchAll($result);
    }

    /**
     * Collects and returns listing IDs (id) of ASC listings that have a missing amazon asin
     *
     * @return array
     */
    private function collectMissingAsins(): array
    {
        /** @var AdapterInterface */
        $connection = $this->connection;

        /** @var string */
        $listingTable = $this->resourceConnection->getTableName('channel_amazon_listing');
        /** @var array */
        $statuses = [
            Definitions::ERROR_LIST_STATUS,
            Definitions::ACTIVE_LIST_STATUS

        ];// query
        $result = $connection->select()->from(
            ['listing' => $listingTable],
            []
        )->where(
            'listing.asin IS NULL'
        )->where(
            'listing.list_status IN (?)',
            $statuses
        )->columns(
            [
                'id' => 'listing.id'
            ]
        );

        return $connection->fetchAll($result);
    }

    /**
     * Moves listing status to validating asin
     * when a missing asin is discovered
     *
     * @param array $results
     * @return void
     */
    private function setValidateAsin(array $results)
    {
        $connection = $this->connection;
        $listingTable = $this->resourceConnection->getTableName('channel_amazon_listing');
        $ids = array_column($results, 'id');

        if (!empty($ids)) {
            $bind = [
                'list_status' => Definitions::VALIDATE_ASIN_LIST_STATUS
            ];

            $where = [
                'id IN (?)' => $ids
            ];
            $connection->update($listingTable, $bind, $where);
        }
    }

    /**
     * Moves listing status to third party import awaiting
     * new catalog product association. These include listings
     * that are found to be effectively 'lost/disassociated'
     * from ASC and/or core catalog.
     *
     * @param array $results
     * @return void
     */
    private function setThirdpartyListings(array $results)
    {
        /** @var AdapterInterface $adapter */
        $connection = $this->connection;
        /** @var array */
        $ids = [];
        /** @var string */
        $listingTable = $this->resourceConnection->getTableName('channel_amazon_listing');

        foreach ($results as $row) {
            $ids[] = $row['id'];
        }

        foreach (array_chunk($ids, self::CHUNK_SIZE) as $chunkIds) {
            $bind = [
                'list_status' => Definitions::THIRDPARTY_LIST_STATUS,
                'catalog_sku' => null,
                'catalog_product_id' => null
            ];

            $where = [
                'id IN (?)' => $chunkIds
            ];

            $connection->update($listingTable, $bind, $where);
        }
    }

    /**
     * Updates product listing eligibility
     *
     * @param AccountInterface $account
     * @param array $processedIds
     * @param array $ids
     * @return void
     */
    private function setListingEligibility(AccountInterface $account, array $processedIds, array $ids = [])
    {
        $merchantId = (int)$account->getMerchantId();
        $listedIds = [];
        $listedEligibleIds = [];
        $listedIneligibleIds = [];

        /** @var ListingInterface[] */
        $collection = $this->listingCollectionFactory->create();
        $collection->addFieldToFilter('merchant_id', $merchantId);
        $collection->addFieldToFilter('catalog_product_id', ['notnull' => true]);

        // filter by ids
        if (!empty($ids)) {
            $collection->addFieldToFilter('catalog_product_id', ['in' => $ids]);
        }

        /** @var ListingInterface $listing */
        foreach ($collection as $listing) {
            $sellerSku = $listing->getSellerSku();
            $productId = $listing->getCatalogProductId();
            $listStatus = $listing->getListStatus();
            $listedIds[] = $productId;

            if (in_array($sellerSku, $processedIds)) {
                continue;
            }

            if ($listStatus == Definitions::NO_LONGER_ELIGIBLE_STATUS) {
                $listedIneligibleIds[] = $productId;
                continue;
            }
            $listedEligibleIds[] = $productId;
        }

        // all eligible ids
        $eligibleIds = $this->getEligibleProducts($merchantId, $ids);

        // new listings
        if ($toListIds = array_diff($eligibleIds, $listedIds)) {
            $this->listingManagement->insertByProductIds($toListIds, $merchantId);
        }

        // new eligible listings
        if ($newEligibleIds = array_intersect($listedIneligibleIds, $eligibleIds)) {
            $this->listingManagement->setEligibilityByProductIds($newEligibleIds, $merchantId);
        }

        // new ineligible listings
        if ($newIneligibleIds = array_diff($listedEligibleIds, $eligibleIds)) {
            $this->listingManagement->setEligibilityByProductIds($newIneligibleIds, $merchantId, false);
        }
    }

    /**
     * Gets eligible products by account
     *
     * @param int $merchantId
     * @param array $filterIds
     * @return array
     */
    private function getEligibleProducts($merchantId, $filterIds = []): array
    {
        $ids = [];

        $collection = $this->ruleCollectionFactory->create();
        $collection->addFieldToFilter('merchant_id', $merchantId);

        foreach ($collection as $listingRule) {
            $conditionsSerialized = $listingRule->getConditionsSerialized();
            $rule = [];

            $rule['conditions'][1] = $this->serializer->unserialize($conditionsSerialized);
            if (!$rule['conditions'][1]) {
                return $ids;
            }

            /** @var CatalogRuleInterface */
            $ruleFactory = $this->ruleFactory->create();

            // add product filter
            if (!empty($filterIds)) {
                $ruleFactory->setProductsFilter($filterIds);
            }

            $websiteId = $listingRule->getWebsiteId();
            $ruleFactory->setWebsiteIds($websiteId);

            // set rule
            $ruleFactory->loadPost($rule);

            // get matching product ids
            /**
             * Inject alternate method via object manager preferences pattern. IDE typically cannot understand this.
             * @see di.xml
             * <preference for="Magento\CatalogRule\Model\Rule" type="Magento\Amazon\Model\Rule\RuleOverride"/>
             */
            $eligibleIds = $ruleFactory->getMatchingProductIds(true);

            // format results
            if (is_array($eligibleIds)) {
                foreach ($eligibleIds as $productId => $product) {
                    foreach ($product as $value) {
                        if ($value) {
                            $ids[] = $productId;
                            break;
                        }
                    }
                }
            }
        }

        return array_unique($ids);
    }

    /**
     * Reindex available quantities
     *
     * @param AccountInterface $account
     * @param array $ids
     * @return void
     */
    private function reindexStock(AccountInterface $account, array $ids = [])
    {
        /** @var int */
        $records = $this->buildIndexTable($account, $ids);
        /** @var int */
        $merchantId = $account->getMerchantId();

        $accountListing = $this->accountListingRepository->getByMerchantId($merchantId);

        if ($records) {
            /** @var int */
            $customQty = ($accountListing->getCustomQty()) ? $accountListing->getCustomQty() : 100;

            // set starting quantities
            $this->setCustomQty($customQty, $merchantId);
            /** @var StockInterface $stockModel */
            $stockModel = $this->stockResolver->resolve();
            $stockModel->setAmazonListingQtyToStockQty($merchantId);

            // calculate minimum quantity
            if ($minQty = $accountListing->getMinQty()) {
                $this->setMinQty($minQty, $merchantId);
            }

            // calculated maximum quantity
            if ($maxQty = $accountListing->getMaxQty()) {
                $this->setMaxQty($maxQty, $merchantId);
            }
        }
    }

    /**
     * Build index table with id filter
     *
     * @param AccountInterface $account
     * @param array $ids
     * @return int
     * @throws Zend_Db_Statement_Exception
     */
    private function buildIndexTable(AccountInterface $account, array $ids = []): int
    {
        /** @var AdapterInterface */
        $connection = $this->connection;

        /** @var int */
        $merchantId = (int)$account->getMerchantId();
        /** @var int */
        $rowCount = 0;
        /** @var array */
        $fields = [
            'merchant_id',
            'seller_id',
            'parent_id',
            'quantity',
            'seller_sku'
        ];

        /** @var array */
        $statuses = [
            Definitions::ERROR_LIST_STATUS,
            Definitions::ACTIVE_LIST_STATUS
        ];

        /** @var string */
        $listingTable = $this->resourceConnection->getTableName('channel_amazon_listing');
        /** @var string */
        $indexTable = $this->resourceConnection->getTableName('channel_amazon_quantity_index');
        /** @var string */
        $accountTable = $this->resourceConnection->getTableName('channel_amazon_account');

        if (!empty($ids)) {
            // chunk data
            foreach (array_chunk($ids, self::CHUNK_SIZE) as $chunkIds) {
                $select = $connection->select()->from(
                    ['listing' => $listingTable],
                    []
                )->joinInner(
                    ['account' => $accountTable],
                    'account.merchant_id = listing.merchant_id',
                    []
                )->where(
                    'listing.merchant_id = ?',
                    $merchantId
                )->where(
                    'listing.catalog_product_id IN (?)',
                    $chunkIds
                )->where(
                    'listing.fulfilled_by = "DEFAULT" OR listing.fulfilled_by_update = 1'
                )->where(
                    'listing.list_status IN (?)',
                    $statuses
                )->columns(
                    [
                        'merchant_id' => new \Zend_Db_Expr($connection->quote($merchantId)),
                        'seller_id' => 'account.seller_id',
                        'parent_id' => 'listing.id',
                        'quantity' => 'listing.qty',
                        'seller_sku' => 'listing.seller_sku'
                    ]
                );

                $sql = $connection->insertFromSelect($select, $indexTable, $fields, AdapterInterface::INSERT_IGNORE);
                $connection->beginTransaction();
                $rowCount = $connection->query($sql)->rowCount();
                $connection->commit();
            }
        } else {
            $select = $connection->select()->from(
                ['listing' => $listingTable],
                []
            )->joinInner(
                ['account' => $accountTable],
                'account.merchant_id = listing.merchant_id',
                []
            )->where(
                'listing.merchant_id = ?',
                $merchantId
            )->where(
                'listing.fulfilled_by = "DEFAULT" OR listing.fulfilled_by_update = 1'
            )->where(
                'listing.eligible = ?',
                (int)1
            )->where(
                'listing.list_status IN (?)',
                $statuses
            )->columns(
                [
                    'merchant_id' => new \Zend_Db_Expr($connection->quote($merchantId)),
                    'seller_id' => 'account.seller_id',
                    'parent_id' => 'listing.id',
                    'quantity' => 'listing.qty',
                    'seller_sku' => 'listing.seller_sku'
                ]
            );

            $sql = $connection->insertFromSelect($select, $indexTable, $fields, AdapterInterface::INSERT_IGNORE);
            $rowCount = $connection->query($sql)->rowCount();
        }

        return $rowCount;
    }

    /**
     * Sets custom quantity to Amazon listings
     *
     * @param int $qty
     * @param int $merchantId
     * @return void
     */
    private function setCustomQty($qty, $merchantId)
    {
        /** @var AdapterInterface */
        $connection = $this->connection;

        /** @var string */
        $listingTable = $this->resourceConnection->getTableName('channel_amazon_listing');
        /** @var string */
        $indexTable = $this->resourceConnection->getTableName('channel_amazon_quantity_index');
        /** @var string */
        $stockTable = $this->resourceConnection->getTableName('cataloginventory_stock_item');
        /** @var string */
        $coreConfigTable = $this->resourceConnection->getTableName('core_config_data');

        $select = $connection->select()->from(
            false,
            [
                'quantity' => $qty
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
            'stock.use_config_manage_stock = ?',
            (int)1
        )->where(
            'core_config.value = ?',
            (int)0
        )->where(
            'index_table.merchant_id = ?',
            (int)$merchantId
        );

        $update = $connection->updateFromSelect($select, ['index_table' => $indexTable]);
        $connection->query($update);

        $select = $connection->select()->from(
            false,
            [
                'quantity' => $qty
            ]
        )->joinInner(
            ['listing' => $listingTable],
            'listing.id = index_table.parent_id',
            []
        )->joinInner(
            ['stock' => $stockTable],
            'stock.product_id = listing.catalog_product_id',
            []
        )->where(
            'stock.manage_stock = ?',
            (int)0
        )->where(
            'stock.use_config_manage_stock = ?',
            (int)0
        )->where(
            'index_table.merchant_id = ?',
            (int)$merchantId
        );

        $update = $connection->updateFromSelect($select, ['index_table' => $indexTable]);
        $connection->query($update);
    }

    /**
     * Edits all Amazon listings to minimum qty setting
     *
     * @param int $minQty
     * @param int $merchantId
     * @return void
     */
    private function setMinQty($minQty, $merchantId)
    {
        /** @var AdapterInterface */
        $connection = $this->connection;
        /** @var string */
        $indexTable = $this->resourceConnection->getTableName('channel_amazon_quantity_index');

        $bind = [
            'quantity' => (int)0
        ];

        $where = [
            $connection->quoteInto('quantity < ?', $minQty),
            $connection->quoteInto('merchant_id = ?', $merchantId)
        ];

        $connection->update($indexTable, $bind, $where);
    }

    /**
     * Edits all Amazon listings to maximum qty setting
     *
     * @param int $maxQty
     * @param int $merchantId
     * @return void
     */
    private function setMaxQty($maxQty, $merchantId)
    {
        /** @var AdapterInterface */
        $connection = $this->connection;
        /** @var string */
        $indexTable = $this->resourceConnection->getTableName('channel_amazon_quantity_index');

        $bind = [
            'quantity' => $maxQty
        ];

        $where = [
            $connection->quoteInto('quantity > ?', $maxQty),
            $connection->quoteInto('merchant_id = ?', $merchantId)
        ];

        $connection->update($indexTable, $bind, $where);
    }

    /**
     * Checks for unified listings that are controlled by another
     * marketplaces, and if so, returns an array of matches
     *
     * @param AccountInterface $account
     * @return array
     */
    private function getUnifiedListingIds(AccountInterface $account): array
    {
        /** @var array */
        $ids = [];
        /** @var array */
        $excludedStatuses = [
            Definitions::REMOVE_IN_PROGRESS_LIST_STATUS,
            Definitions::ENDED_LIST_STATUS,
            Definitions::TOBEENDED_LIST_STATUS
        ];

        if (!$account->getIsActive()) {
            return $ids;
        }

        /** @var int */
        $merchantId = $account->getMerchantId();

        /** @var ListingInterface[] */
        $listingCollection = $this->listingCollectionFactory->create();

        $listingCollection->addFieldToFilter('merchant_id', $merchantId);
        $listingCollection->addFieldToFilter('catalog_product_id', ['notnull' => true]);

        /** @var ListingInterface */
        foreach ($listingCollection as $listing) {
            /** @var string */
            $listStatus = $listing->getListStatus();

            if (in_array($listStatus, $excludedStatuses)) {
                continue;
            }

            $ids[$listing->getId()] = $listing->getSellerSku();
        }

        return $ids;
    }

    /**
     * Sets listing update flag when qty change is detected
     *
     * @return void
     */
    private function setUpdateFlag()
    {
        /** @var AdapterInterface */
        $connection = $this->connection;
        /** @var array */
        $statuses = [
            Definitions::ERROR_LIST_STATUS,
            Definitions::ACTIVE_LIST_STATUS
        ];

        /** @var string */
        $indexTable = $this->resourceConnection->getTableName('channel_amazon_quantity_index');
        /** @var string */
        $listingTable = $this->resourceConnection->getTableName('channel_amazon_listing');

        $select = $connection->select()->from(
            false,
            [
                'quantity_update' => (int)1
            ]
        )->joinInner(
            ['index_table' => $indexTable],
            'listing.id = index_table.parent_id',
            []
        )->where(
            'listing.list_status IN (?)',
            $statuses
        )->where(
            'listing.qty != index_table.quantity OR index_table.quantity IS NULL'
        );

        $update = $connection->updateFromSelect($select, ['listing' => $listingTable]);

        $connection->query($update);
    }

    /**
     * Synchronizes Amazon listing quantity with changes
     * after stock reindex process
     *
     * @return void
     */
    private function syncAmazonListingQuantities()
    {
        /** @var AdapterInterface */
        $connection = $this->connection;

        /** @var string */
        $indexTable = $this->resourceConnection->getTableName('channel_amazon_quantity_index');
        /** @var string */
        $listingTable = $this->resourceConnection->getTableName('channel_amazon_listing');
        /** @var string */
        $accountTable = $this->resourceConnection->getTableName('channel_amazon_account');

        $select = $connection->select()->from(
            false,
            [
                'qty' => 'index_table.quantity'
            ]
        )->joinInner(
            ['index_table' => $indexTable],
            'listing.seller_sku = index_table.seller_sku',
            []
        )->joinInner(
            ['index_account' => $accountTable],
            'index_account.merchant_id = index_table.merchant_id',
            []
        )->joinInner(
            ['listing_account' => $accountTable],
            'listing_account.merchant_id = listing.merchant_id',
            []
        )->where(
            'listing.qty != index_table.quantity OR index_table.quantity IS NULL'
        )->where(
            'index_account.seller_id = listing_account.seller_id'
        );

        $update = $connection->updateFromSelect($select, ['listing' => $listingTable]);

        $connection->query($update);
    }

    /**
     * Schedules Amazon quantity updates via API feed
     * for all stock changes
     *
     * @return void
     */
    private function scheduleStockUpdates()
    {
        /** @var AdapterInterface */
        $connection = $this->connection;

        /** @var string */
        $accountListingTable = $this->resourceConnection->getTableName('channel_amazon_account_listing');
        /** @var string */
        $listingTable = $this->resourceConnection->getTableName('channel_amazon_listing');
        /** @var array */
        $log = [];

        $select = $connection->select()->from(
            ['listing' => $listingTable],
            []
        )->joinInner(
            ['account_listing' => $accountListingTable],
            'account_listing.merchant_id = listing.merchant_id',
            []
        )->where(
            'listing.quantity_update = 1'
        )->where(
            'listing.fulfilled_by = "DEFAULT" OR listing.fulfilled_by_update = 0'
        )->columns(
            [
                'id' => 'listing.id',
                'merchant_id' => 'listing.merchant_id',
                'sku' => 'listing.seller_sku',
                'list_status' => 'listing.list_status',
                'handling_override' => 'listing.handling_override',
                'handling_time' => 'account_listing.handling_time',
                'fulfilled_by' => 'listing.fulfilled_by',
                'fulfilled_by_update' => 'listing.fulfilled_by_update',
                'qty' => 'listing.qty'
            ]
        );

        $data = $connection->fetchAll($select);

        foreach ($data as $row) {
            $commandData = [
                'body' => $this->listingResourceModel->prepareCommandBody($row, false),
                'identifier' => (string)$row['id']
            ];
            /** @var \Magento\Amazon\Domain\Command\UpdateInventoryQty $command */
            $command = $this->updateInventoryQtyCommandFactory->create($commandData);
            $this->commandDispatcher->dispatch($row['merchant_id'], $command);
            $this->listingResourceModel->handleFulfillment($command);

            $log[] = [
                'merchant_id' => $row['merchant_id'],
                'seller_sku' => $row['sku'],
                'action' => __('Quantity'),
                'notes' => __('Updated to ' . $row['qty'])
            ];
        }

        if (!empty($log)) {
            $this->logResourceModel->insert($log);
        }
    }

    /**
     * Clears the qty update flag on listings
     *
     * @return void
     */
    private function clearUpdateFlag()
    {
        /** @var AdapterInterface */
        $connection = $this->connection;

        /** @var string */
        $listingTable = $this->resourceConnection->getTableName('channel_amazon_listing');

        $bind = [
            'quantity_update' => new \Zend_Db_Expr('0')
        ];

        $connection->update($listingTable, $bind);
    }

    /**
     * Empties index table
     *
     * @return void
     */
    private function clearIndexTable()
    {
        /** @var AdapterInterface */
        $connection = $this->connection;
        $indexTable = $this->resourceConnection->getTableName('channel_amazon_quantity_index');
        $connection->delete($indexTable);
    }

    /**
     * Synchronizes Amazon listing SKU with product SKU.
     *
     * If $idArr empty, synchronise all 'inner joined' rows, otherwise
     * synchronize for supplied ids only.
     *
     * @param array $ids
     * @return void
     * @throws \Zend_Db_Select_Exception
     */
    private function synchronizeSkus(array $ids)
    {
        /** @var AdapterInterface */
        $connection = $this->connection;
        /** @var string */
        $listingTable = $this->resourceConnection->getTableName('channel_amazon_listing');
        /** @var string */
        $productTable = $this->resourceConnection->getTableName('catalog_product_entity');

        // query
        $select = $connection->select()->from(
            false,
            []
        );
        $select->join(
            ['cpe' => $productTable],
            'ascListingTable.catalog_product_id = cpe.entity_id',
            ['catalog_sku' => 'sku']
        );
        if (!empty($ids)) {
            $select->where('ascListingTable.catalog_product_id IN (?)', $ids);
        }
        $query = $connection->updateFromSelect(
            $select,
            ['ascListingTable' => $listingTable]
        );

        $connection->query($query);
    }
}
