<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\Amazon\Model\ResourceModel\Amazon;

use Magento\Amazon\Api\Data\AccountListingInterface;
use Magento\Amazon\Api\Data\ListingInterface;
use Magento\Amazon\Domain\Command\CommandDispatcher;
use Magento\Amazon\Domain\Command\GetProductsByAttributesFactory;
use Magento\Amazon\Domain\Command\GetProductsByQueryFactory;
use Magento\Amazon\Domain\Command\RemoveProduct;
use Magento\Amazon\Domain\Command\RemoveProductFactory;
use Magento\Amazon\Domain\Command\UpdateInventoryQtyFactory;
use Magento\Amazon\Domain\Command\UpdateListing;
use Magento\Amazon\Domain\Command\UpdateListingFactory;
use Magento\Amazon\Logger\AscClientLogger;
use Magento\Amazon\Model\Amazon\Account\AccountConditionNotes;
use Magento\Amazon\Model\Amazon\Definitions;
use Magento\Amazon\Model\Amazon\ListingFactory;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\EntityManager\MetadataPool;
use Magento\Framework\Exception\AlreadyExistsException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Context;

/**
 * Class Listing
 */
class Listing extends AbstractDb
{
    /**
     * @var int
     */
    const CHUNK_SIZE = 1000;

    /**
     * @var int
     */
    const MAX_LOOKUP_QUOTA = 5;

    const HANDLING_TIME = 2;

    /**
     * @var ResourceConnection $resourceConnection
     */
    protected $resourceConnection;

    /**
     * @var MetadataPool $metadataPool
     */
    private $metadataPool;

    /**
     * @var AscClientLogger $ascClientLogger
     */
    private $ascClientLogger;

    /**
     * @var GetProductsByAttributesFactory
     */
    private $getProductsByAttributesFactory;

    /**
     * @var \Magento\Amazon\Domain\Command\CommandDispatcher
     */
    private $commandDispatcher;

    /**
     * @var UpdateInventoryQtyFactory
     */
    private $updateInventoryQtyCommandFactory;

    /**
     * @var ListingFactory
     */
    private $listingFactory;

    /**
     * @var UpdateListingFactory
     */
    private $updateListingFactory;

    /**
     * @var AccountConditionNotes
     */
    private $accountConditionNotes;

    /**
     * @var GetProductsByQueryFactory
     */
    private $getProductsByQueryCommandFactory;

    /**
     * @var RemoveProductFactory
     */
    private $removeProductFactory;

    /**
     * @param Context $context
     * @param MetadataPool $metadataPool
     * @param AscClientLogger $ascClientLogger
     * @param GetProductsByQueryFactory $getProductsByQueryCommandFactory
     * @param CommandDispatcher $commandDispatcher
     * @param GetProductsByAttributesFactory $getProductsByAttributesFactory
     * @param UpdateListingFactory $updateListingFactory
     * @param AccountConditionNotes $accountConditionNotes
     * @param UpdateInventoryQtyFactory $updateInventoryQtyFactory
     * @param ListingFactory $listingFactory
     * @param RemoveProductFactory $removeProductFactory
     * @param ResourceConnection $resourceConnection
     */
    public function __construct(
        Context $context,
        MetadataPool $metadataPool,
        AscClientLogger $ascClientLogger,
        GetProductsByQueryFactory $getProductsByQueryCommandFactory,
        CommandDispatcher $commandDispatcher,
        GetProductsByAttributesFactory $getProductsByAttributesFactory,
        UpdateListingFactory $updateListingFactory,
        AccountConditionNotes $accountConditionNotes,
        UpdateInventoryQtyFactory $updateInventoryQtyFactory,
        ListingFactory $listingFactory,
        RemoveProductFactory $removeProductFactory,
        ResourceConnection $resourceConnection
    ) {
        $this->metadataPool = $metadataPool;
        $this->ascClientLogger = $ascClientLogger;
        $this->getProductsByQueryCommandFactory = $getProductsByQueryCommandFactory;
        $this->commandDispatcher = $commandDispatcher;
        $this->getProductsByAttributesFactory = $getProductsByAttributesFactory;
        $this->updateListingFactory = $updateListingFactory;
        $this->accountConditionNotes = $accountConditionNotes;
        $this->updateInventoryQtyCommandFactory = $updateInventoryQtyFactory;
        $this->listingFactory = $listingFactory;
        $this->removeProductFactory = $removeProductFactory;
        $this->resourceConnection = $resourceConnection;
        parent::__construct($context);
    }

    /**
     * Constructor
     */
    protected function _construct()
    {
        $this->_init(
            'channel_amazon_listing',
            'id'
        );
    }

    /**
     * Inserts/updates listings, if flag is not active it does not import third party listings
     *
     * @param array $data
     * @param int $merchantId
     * @param bool $importThirdPartyListings
     * @return void
     * @throws LocalizedException
     */
    public function insert(array $data, $merchantId, bool $importThirdPartyListings = true)
    {
        $statuses = [
            Definitions::VALIDATE_ASIN_LIST_STATUS,
            Definitions::MISSING_CONDITION_LIST_STATUS,
            Definitions::READY_LIST_STATUS,
            Definitions::NOMATCH_LIST_STATUS,
            Definitions::MULTIPLE_LIST_STATUS,
            Definitions::VARIANTS_LIST_STATUS,
            Definitions::GENERAL_SEARCH_LIST_STATUS,
            Definitions::LIST_IN_PROGRESS_LIST_STATUS
        ];

        $publishedStatuses = [
            Definitions::ACTIVE_LIST_STATUS,
            Definitions::ERROR_LIST_STATUS
        ];

        /** @var AdapterInterface */
        $connection = $this->getConnection();
        /** @var string */
        $tableName = $this->getMainTable();

        if ($importThirdPartyListings) {
            $connection->insertOnDuplicate($tableName, $data, []);
        } else {
            foreach ($data as $row) {
                $bind = [
                    'is_listed' => (int)1,
                    'listing_id' => $row['listing_id'],
                    'product_id_type' => $row['product_id_type'],
                    'product_id' => $row['product_id'],
                    'product_type' => $row['product_type'],
                    'category_id' => $row['category_id'],
                    'asin' => $row['asin'],
                    'name' => $row['name'],
                    'qty' => $row['qty'],
                    'list_price' => $row['list_price'],
                    'shipping_price' => $row['shipping_price'],
                    'landed_price' => $row['landed_price'],
                    'msrp_price' => $row['msrp_price'],
                    'map_price' => $row['map_price'],
                    'condition' => $row['condition'],
                    'is_active' => $row['is_active'],
                    'is_ship' => $row['is_ship'],
                    'fulfilled_by' => $row['fulfilled_by']
                ];

                $where = [
                    'seller_sku = ?' => $row['seller_sku'],
                    'merchant_id = ?' => $row['merchant_id']
                ];

                $connection->update($tableName, $bind, $where);
            }
        }

        // record as active list status
        $bind = [
            'list_status' => Definitions::ACTIVE_LIST_STATUS
        ];

        $where = [
            'merchant_id = ?' => $merchantId,
            'is_listed = ?' => (int)1,
            'list_status IN (?)' => $statuses,
        ];

        $connection->update($tableName, $bind, $where);

        // record as ready to list (if active and not on all listings report)
        $bind = [
            'list_status' => Definitions::READY_LIST_STATUS
        ];

        $where = [
            'merchant_id = ?' => $merchantId,
            'is_listed = ?' => (int)0,
            'list_status IN (?)' => $publishedStatuses
        ];

        $connection->update($tableName, $bind, $where);
    }

    /**
     * Process condition override
     *
     * @param int[] $ids
     * @param int $conditionOverride
     * @return void
     * @throws LocalizedException
     */
    public function setConditionOverride(array $ids, $conditionOverride = 0)
    {
        /** @var AdapterInterface */
        $connection = $this->getConnection();
        /** @var string $tableName */
        $tableName = $this->getMainTable();

        foreach (array_chunk($ids, self::CHUNK_SIZE) as $chunkIds) {
            $bind = [
                'list_status' => (int)Definitions::CONDITION_OVERRIDE_LIST_STATUS,
                'condition_override' => $conditionOverride
            ];

            // if no condition overrides
            if (!$conditionOverride) {
                $where = [
                    'id IN (?)' => $chunkIds,
                    'eligible = ?' => (int)1,
                    'fulfilled_by NOT LIKE ?' => 'AMAZON%'
                ];
            } else {
                $where = [
                    'id IN (?)' => $chunkIds,
                    'condition_override = 0 OR condition_override != ?' => $conditionOverride,
                    'eligible = ?' => (int)1,
                    'fulfilled_by NOT LIKE ?' => 'AMAZON%'
                ];
            }

            try {
                $connection->beginTransaction();
                $connection->update($tableName, $bind, $where);
                $connection->commit();
            } catch (\Exception $e) {
                $connection->rollBack();
            }

            $bind = [
                'condition_override' => $conditionOverride
            ];

            // if no condition overrides
            if (!$conditionOverride) {
                $where = [
                    'id IN (?)' => $chunkIds,
                    'eligible = ?' => (int)0,
                    'fulfilled_by NOT LIKE ?' => 'AMAZON%'
                ];
            } else {
                $where = [
                    'id IN (?)' => $chunkIds,
                    'condition_override = 0 OR condition_override != ?' => $conditionOverride,
                    'eligible = ?' => (int)0,
                    'fulfilled_by NOT LIKE ?' => 'AMAZON%'
                ];
            }

            try {
                $connection->beginTransaction();
                $connection->update($tableName, $bind, $where);
                $connection->commit();
            } catch (\Exception $e) {
                $connection->rollBack();
            }
        }
    }

    /**
     * Process condition notes override
     *
     * @param int[] $ids
     * @param string $override
     * @return void
     * @throws LocalizedException
     */
    public function setConditionNotesOverride(array $ids, $override = null)
    {
        /** @var AdapterInterface */
        $connection = $this->getConnection();
        $tableName = $this->getMainTable();

        foreach (array_chunk($ids, self::CHUNK_SIZE) as $chunkIds) {
            $bind = [
                'condition_notes_override' => $override
            ];

            $where = [
                'id IN (?)' => $chunkIds,
                'condition_notes_override != ? OR condition_notes_override IS NULL' => $override
            ];

            try {
                $connection->beginTransaction();
                $connection->update($tableName, $bind, $where);
                $connection->commit();
            } catch (\Exception $e) {
                $connection->rollBack();
            }
        }
    }

    /**
     * Remove condition override
     *
     * @param int[] $ids
     * @return void
     * @throws LocalizedException
     */
    public function removeConditionOverride(array $ids)
    {
        /** @var AdapterInterface */
        $connection = $this->getConnection();

        /** @var array */
        $statuses = [
            Definitions::ERROR_LIST_STATUS,
            Definitions::ACTIVE_LIST_STATUS
        ];

        /** @var string */
        $tableName = $this->getMainTable();

        foreach (array_chunk($ids, self::CHUNK_SIZE) as $chunkIds) {
            $bind = [
                'list_status' => (int)Definitions::CONDITION_OVERRIDE_LIST_STATUS,
                'condition_override' => (int)0
            ];

            $where = [
                'id IN (?)' => $chunkIds,
                'condition_override != ?' => (int)0,
                'list_status IN (?)' => $statuses
            ];

            try {
                $connection->beginTransaction();
                $connection->update($tableName, $bind, $where);
                $connection->commit();
            } catch (\Exception $e) {
                $connection->rollBack();
            }

            $bind = [
                'condition_override' => (int)0
            ];

            $where = [
                'id IN (?)' => $chunkIds,
                'condition_override != ?' => (int)0,
                'list_status NOT IN (?)' => $statuses
            ];

            try {
                $connection->beginTransaction();
                $connection->update($tableName, $bind, $where);
                $connection->commit();
            } catch (\Exception $e) {
                $connection->rollBack();
            }
        }
    }

    /**
     * Process handling override
     *
     * @param int[] $ids
     * @param int $handlingOverride
     * @return void
     * @throws LocalizedException
     * @throws \Zend_Db_Select_Exception
     */
    public function setHandlingOverride(array $ids, $handlingOverride)
    {
        /** @var AdapterInterface */
        $connection = $this->getConnection();
        /** @var string */
        $listingTable = $this->getMainTable();
        /** @var string */
        $accountTable = $this->resourceConnection->getTableName('channel_amazon_account');

        foreach (array_chunk($ids, self::CHUNK_SIZE) as $chunkIds) {
            // select query
            $select = $connection->select()->from(
                false,
                [
                    'handling_override' => $handlingOverride
                ]
            )->joinLeft(
                ['childListing' => $listingTable],
                'childListing.seller_sku = listing.seller_sku',
                []
            )->joinInner(
                ['account' => $accountTable],
                'account.merchant_id = listing.merchant_id',
                []
            )->joinInner(
                ['childAccount' => $accountTable],
                'childAccount.merchant_id = childListing.merchant_id',
                []
            )->where(
                'account.seller_id = childAccount.seller_id'
            )->where(
                'childListing.id IN (?)',
                $chunkIds
            );

            $query = $connection->updateFromSelect($select, ['listing' => $listingTable]);

            try {
                $connection->beginTransaction();
                $connection->query($query);
                $connection->commit();
            } catch (\Exception $e) {
                $connection->rollBack();
            }
        }
    }

    /**
     * Remove handling override
     *
     * @param int[] $ids
     * @return void
     * @throws LocalizedException
     * @throws \Zend_Db_Select_Exception
     */
    public function removeHandlingOverride(array $ids)
    {
        /** @var AdapterInterface */
        $connection = $this->getConnection();
        /** @var string */
        $listingTable = $this->getMainTable();
        /** @var string */
        $accountTable = $this->resourceConnection->getTableName('channel_amazon_account');

        foreach (array_chunk($ids, self::CHUNK_SIZE) as $chunkIds) {
            // select query
            $select = $connection->select()->from(
                false,
                [
                    'handling_override' => new \Zend_Db_Expr('IF(1 > 2, 1, NULL)')
                ]
            )->joinLeft(
                ['childListing' => $listingTable],
                'childListing.seller_sku = listing.seller_sku',
                []
            )->joinInner(
                ['account' => $accountTable],
                'account.merchant_id = listing.merchant_id',
                []
            )->joinInner(
                ['childAccount' => $accountTable],
                'childAccount.merchant_id = childListing.merchant_id',
                []
            )->where(
                'account.seller_id = childAccount.seller_id'
            )->where(
                'childListing.id IN (?)',
                $chunkIds
            );

            $query = $connection->updateFromSelect($select, ['listing' => $listingTable]);

            try {
                $connection->beginTransaction();
                $connection->query($query);
                $connection->commit();
            } catch (\Exception $e) {
                $connection->rollBack();
            }
        }
    }

    /**
     * Update list status action
     *
     * @param array $ids
     * @param int $listStatus
     * @param array $exclude
     * @return void
     * @throws LocalizedException
     */
    public function scheduleListStatusUpdate(array $ids, $listStatus, $exclude = [])
    {
        /** @var AdapterInterface */
        $connection = $this->getConnection();
        /** @var string */
        $tableName = $this->getMainTable();
        /** @var string */
        $condition = $connection->quoteIdentifier('condition');

        foreach (array_chunk($ids, self::CHUNK_SIZE) as $idList) {
            // remove existing actions
            if ($listStatus == Definitions::TOBEENDED_LIST_STATUS) {
                $this->removeActionsByListingIds($idList, 'remove_product');
            }

            // if setting to "ready to list" check for missing condition
            if ($listStatus == Definitions::READY_LIST_STATUS) {
                $bind = [
                    'list_status' => Definitions::MISSING_CONDITION_LIST_STATUS
                ];

                $where = [
                    'id IN (?)' => $idList,
                    $condition . ' = ?' => (int)0
                ];

                try {
                    $connection->beginTransaction();
                    $connection->update($tableName, $bind, $where);
                    $connection->commit();
                } catch (\Exception $e) {
                    $connection->rollBack();
                }
            }

            // if no match
            if ($listStatus == Definitions::GENERAL_SEARCH_LIST_STATUS) {
                $bind = [
                    'list_status' => $listStatus,
                    'asin' => null
                ];
            } else {
                $bind = [
                    'list_status' => $listStatus,
                ];
            }

            // if exclude
            if ($exclude) {
                $where = [
                    'id IN (?)' => $idList,
                    'list_status IN (?)' => $exclude
                ];
            } else {// no exclude
                $where = [
                    'id IN (?)' => $idList
                ];
            }

            try {
                $connection->beginTransaction();
                $connection->update($tableName, $bind, $where);
                $connection->commit();
            } catch (\Exception $e) {
                $connection->rollBack();
            }
        }
    }

    /** Update listing product type, asin, and name (on newly matched listing)
     *
     * /**
     * Update listing product type, asin, and name (on newly matched listing)
     *
     * @param array $data
     * @return void
     * @throws LocalizedException
     */
    public function updateListingInfo(array $data)
    {
        /** @var AdapterInterface */
        $connection = $this->getConnection();
        /** @var string $listingTable */
        $listingTable = $this->getMainTable();

        foreach ($data as $id => $row) {
            $bind = [
                'asin' => $row['asin'] ?? $row['ASIN'],
                'product_type' => $row['product_type'] ?? $row['ProductType'],
                'name' => $row['title'] ?? $row['Title']
            ];

            $where = [
                'id = ?' => $id
            ];

            try {
                $connection->beginTransaction();
                $connection->update($listingTable, $bind, $where);
                $connection->commit();
            } catch (\Exception $e) {
                $connection->rollBack();
            }
        }
    }

    /**
     * Remove all scheduled actions by listing id
     * (excludes $type)
     *
     * @param array $ids
     * @param string $commandName
     * @return void
     */
    private function removeActionsByListingIds(array $ids, string $commandName)
    {
        /** @var AdapterInterface */
        $connection = $this->getConnection();
        /** @var string */
        $actionsTable = $this->resourceConnection->getTableName('channel_amazon_action');

        $where = [
            'identifier IN (?)' => $ids,
            'command = ?' => $commandName
        ];

        try {
            $connection->beginTransaction();
            $connection->delete($actionsTable, $where);
            $connection->commit();
        } catch (\Exception $e) {
            $connection->rollBack();
        }
    }

    /**
     * Toggles fulfillment type between Amazon and merchant
     *
     * The fulfillment type set is the $fulfillmentCode argument
     * and $sellerId and $sellerSku are used to propagate the
     * change across all existing matching seller skus in support
     * of unified seller accounts
     *
     * @param string $fulfillmentCode
     * @param string $sellerId
     * @param string $sellerSku
     * @return int
     * @throws LocalizedException
     * @throws \Zend_Db_Statement_Exception
     * @throws \Zend_Db_Select_Exception
     */
    public function toggleFulfillmentType($fulfillmentCode, $sellerId, $sellerSku): int
    {
        /** @var AdapterInterface */
        $connection = $this->getConnection();
        /** @var string */
        $listingTable = $this->getMainTable();
        /** @var string */
        $accountTable = $this->resourceConnection->getTableName('channel_amazon_account');

        // select query
        $select = $connection->select()->from(
            false,
            [
                'fulfilled_by' => new \Zend_Db_Expr($connection->quote($fulfillmentCode)),
                'fulfilled_by_update' => (int)1,
                'quantity_update' => (int)1
            ]
        )->joinInner(
            ['account' => $accountTable],
            'account.merchant_id = listing.merchant_id',
            []
        )->where(
            'account.seller_id = ?',
            $sellerId
        )->where(
            'listing.seller_sku = ?',
            $sellerSku
        );

        $query = $connection->updateFromSelect($select, ['listing' => $listingTable]);

        try {
            $connection->beginTransaction();
            $rows = $connection->query($query);
            $connection->commit();
        } catch (\Exception $e) {
            $connection->rollBack();
            return 0;
        }

        return $rows->rowCount();
    }

    /**
     * Attempts to locate Magento product by Amazon SKU
     *
     * @param AccountListingInterface $account
     * @param array $ids
     * @return array
     * @throws LocalizedException
     * @throws \Zend_Db_Select_Exception
     */
    public function findProductByAmazonSku(AccountListingInterface $account, array $ids = []): array
    {
        /** @var AdapterInterface */
        $connection = $this->getConnection();
        /** @var int */
        $merchantId = $account->getMerchantId();

        // query
        $result = $connection->select()->from(
            ['listing' => $this->getMainTable()],
            []
        )->joinInner(
            ['cpe' => $this->resourceConnection->getTableName('catalog_product_entity')],
            'cpe.sku = listing.seller_sku',
            []
        )->where(
            'listing.merchant_id = ?',
            $merchantId
        )->where(
            'listing.list_status = ?',
            Definitions::THIRDPARTY_LIST_STATUS
        )->columns(
            [
                'id' => 'listing.id',
                'asin' => 'listing.asin',
                'condition' => 'listing.condition',
                'seller_sku' => 'listing.seller_sku',
                'list_price' => 'listing.list_price',
                'qty' => 'listing.qty',
                'catalog_sku' => 'cpe.sku',
                'listing_id' => 'listing.listing_id',
                'catalog_product_id' => 'cpe.entity_id'
            ]
        )->group(
            'listing.id'
        )->order(['cpe.sku DESC']);

        if (!empty($ids)) {
            $result->where(
                'listing.id IN (?)',
                $ids
            );
        }

        try {
            $connection->beginTransaction();
            $results = $connection->fetchAll($result);
            $connection->commit();
        } catch (\Exception $e) {
            $connection->rollBack();
            $results = [];
        }

        return $results;
    }

    /**
     * Match by user mapped attribute
     *
     * @param AccountListingInterface $account
     * @param string $attributeCode
     * @param string $listingValue
     * @param array $ids
     * @return array
     * @throws LocalizedException
     * @throws \Zend_Db_Select_Exception
     */
    public function findProductByAmazonAttribute(
        AccountListingInterface $account,
        $attributeCode,
        $listingValue,
        array $ids = []
    ): array {
        /** @var string */
        $linkId = $this->getProductEntityLinkField();
        /** @var AdapterInterface */
        $connection = $this->getConnection();
        /** @var int */
        $merchantId = $account->getMerchantId();

        $result = $connection->select()->from(
            ['listing' => $this->getMainTable()],
            []
        )->joinInner(
            ['eet' => $this->resourceConnection->getTableName('eav_entity_type')],
            'eet.entity_type_code = \'catalog_product\'',
            []
        )->joinInner(
            ['ea' => $this->resourceConnection->getTableName('eav_attribute')],
            'ea.attribute_code = \'' . $attributeCode . '\' AND ea.entity_type_id = eet.entity_type_id',
            []
        )->joinInner(
            ['cpev' => $this->resourceConnection->getTableName('catalog_product_entity_varchar')],
            'cpev.attribute_id = ea.attribute_id AND cpev.value = listing.' . $listingValue,
            []
        )->joinInner(
            ['cpe' => $this->resourceConnection->getTableName('catalog_product_entity')],
            'cpe.' . $linkId . ' = cpev.' . $linkId,
            []
        )->joinLeft(
            ['existing_listing' => $this->resourceConnection->getTableName('channel_amazon_listing')],
            'existing_listing.id != listing.id AND existing_listing.is_listed = 0 AND existing_listing.catalog_product_id = cpe.entity_id AND existing_listing.merchant_id = ' .
            (int)$merchantId,
            []
        )->where(
            'listing.merchant_id = ?',
            $merchantId
        )->where(
            'listing.list_status = ?',
            Definitions::THIRDPARTY_LIST_STATUS
        )->columns(
            [
                'id' => 'listing.id',
                'asin' => 'listing.asin',
                'listing_id' => 'listing.listing_id',
                'seller_sku' => 'listing.seller_sku',
                'list_price' => 'listing.list_price',
                'qty' => 'listing.qty',
                'condition' => 'listing.condition',
                'catalog_product_id' => 'cpe.entity_id',
                'existing_listing_id' => 'existing_listing.id'
            ]
        )->group('listing.id');

        if (!empty($ids)) {
            $result->where(
                'listing.id IN (?)',
                $ids
            );
        }

        try {
            $connection->beginTransaction();
            $results = $connection->fetchAll($result);
            $connection->commit();
        } catch (\Exception $e) {
            $connection->rollBack();
            $results = [];
        }

        return $results;
    }

    /** Deleted listing by id
     *
     * @param int[] $ids
     * @return void
     * @throws LocalizedException
     */
    public function deleteListings(array $ids)
    {
        /** @var AdapterInterface */
        $connection = $this->getConnection();
        /** @var string */
        $listingTable = $this->getMainTable();

        // chunk deletion
        foreach (array_chunk($ids, self::CHUNK_SIZE) as $idList) {
            $where = [
                'id IN (?)' => $idList
            ];

            try {
                $connection->beginTransaction();
                $connection->delete($listingTable, $where);
                $connection->commit();
            } catch (\Exception $e) {
                $connection->rollBack();
            }
        }
    }

    /**
     * Assign third party listing to active status w/ catalog product data
     *
     * @param ListingInterface $listing
     * @return void
     * @throws LocalizedException
     */
    public function assignThirdparty(ListingInterface $listing)
    {
        /** @var AdapterInterface */
        $connection = $this->getConnection();
        /** @var string */
        $listingTable = $this->getMainTable();
        /** @var int */
        $id = $listing->getId();

        $bind = [
            'listing_id' => $listing->getListingId(),
            'asin' => $listing->getAsin(),
            'list_status' => $listing->getListStatus(),
            'seller_sku' => $listing->getSellerSku(),
            'list_price' => $listing->getListPrice(),
            'qty' => $listing->getQty(),
            'condition' => $listing->getCondition()
        ];

        $where = [
            'id = ?' => $id
        ];

        try {
            $connection->beginTransaction();
            $connection->update($listingTable, $bind, $where);
            $connection->commit();
        } catch (\Exception $e) {
            $connection->rollBack();
            return;
        }
    }

    /**
     * Assign third party listing to pre-existing listing
     *
     * @param ListingInterface $listing
     * @return void
     * @throws LocalizedException
     */
    public function assign(ListingInterface $listing)
    {
        /** @var AdapterInterface */
        $connection = $this->getConnection();
        /** @var string $listingTable */
        $listingTable = $this->getMainTable();
        /** @var int */
        $id = $listing->getId();

        $bind = [
            'catalog_sku' => $listing->getCatalogSku(),
            'catalog_product_id' => $listing->getCatalogProductId(),
            'list_status' => $listing->getListStatus()
        ];

        $where = [
            'id = ' . $id
        ];

        try {
            $connection->beginTransaction();
            $connection->update($listingTable, $bind, $where);
            $connection->commit();
        } catch (\Exception $e) {
            $connection->rollBack();
            return;
        }
    }

    /**
     * Adds eligible catalog products to listing
     *
     * @param array $data
     * @return void
     * @throws LocalizedException
     */
    public function insertByProductIds($data)
    {
        /** @var AdapterInterface */
        $connection = $this->getConnection();
        /** @var string */
        $listingTable = $this->getMainTable();

        try {
            $connection->beginTransaction();
            $connection->insertOnDuplicate($listingTable, $data, ['catalog_product_id', 'catalog_sku']);
            $connection->commit();
        } catch (\Exception $e) {
            $connection->rollBack();
        }

        // move third party listings to active status
        $bind = [
            'list_status' => Definitions::ACTIVE_LIST_STATUS,
            'eligible' => (int)1
        ];

        $where = [
            'list_status = ?' => Definitions::THIRDPARTY_LIST_STATUS,
            'catalog_product_id IS NOT NULL'
        ];

        try {
            $connection->beginTransaction();
            $connection->update($listingTable, $bind, $where);
            $connection->commit();
        } catch (\Exception $e) {
            $connection->rollBack();
        }
    }

    /**
     * Sets product eligibility according to $eligible flag by catalog product ids
     *
     * @param array $ids
     * @param int $merchantId
     * @param bool $eligible
     * @return bool
     * @throws LocalizedException
     * @throws \Zend_Db_Select_Exception
     */
    public function setEligibilityByProductIds(array $ids, $merchantId, $eligible): bool
    {
        /** @var AdapterInterface */
        $connection = $this->getConnection();
        /** @var string */
        $listingTable = $this->getMainTable();
        /** @var string */
        $accountTable = $this->resourceConnection->getTableName('channel_amazon_account');
        /** @var int */
        $listStatus = ($eligible) ? Definitions::ACTIVE_LIST_STATUS : Definitions::NO_LONGER_ELIGIBLE_STATUS;

        /** @var array */
        $statuses = [
            Definitions::ERROR_LIST_STATUS,
            Definitions::ACTIVE_LIST_STATUS,
            Definitions::NO_LONGER_ELIGIBLE_STATUS
        ];

        foreach (array_chunk($ids, self::CHUNK_SIZE) as $chunkIds) {
            // select query
            $select = $connection->select()->from(
                false,
                [
                    'list_status' => (int)$listStatus,
                    'eligible' => new \Zend_Db_Expr((int)$eligible)
                ]
            )->joinLeft(
                ['childListing' => $listingTable],
                'childListing.seller_sku = listing.seller_sku',
                []
            )->joinInner(
                ['account' => $accountTable],
                'account.merchant_id = listing.merchant_id',
                []
            )->joinInner(
                ['childAccount' => $accountTable],
                'childAccount.merchant_id = childListing.merchant_id',
                []
            )->where(
                'account.seller_id = childAccount.seller_id'
            )->where(
                'childListing.catalog_product_id IN (?)',
                $chunkIds
            )->where(
                'childAccount.merchant_id = ?',
                $merchantId
            )->where(
                'childListing.list_status IN (?)',
                $statuses
            );

            $updateQuery = $connection->updateFromSelect($select, ['listing' => $listingTable]);

            /** @var array */
            $removeStatuses = [
                Definitions::MISSING_CONDITION_LIST_STATUS,
                Definitions::NOMATCH_LIST_STATUS,
                Definitions::MULTIPLE_LIST_STATUS,
                Definitions::VARIANTS_LIST_STATUS
            ];

            $deleteClause = [
                'list_status IN (?)' => $removeStatuses,
                'id IN (?)' => $chunkIds
            ];

            $bind = [
                'qty' => (int)0,
            ];

            $where = [
                'list_status = ?' => Definitions::NO_LONGER_ELIGIBLE_STATUS
            ];

            try {
                $connection->beginTransaction();
                $connection->query($updateQuery);
                if (!$eligible) {
                    $connection->delete($listingTable, $deleteClause);
                }
                $connection->update($listingTable, $bind, $where);
                $connection->commit();
            } catch (\Exception $e) {
                $connection->rollBack();
                return false;
            }
        }

        return true;
    }

    /**
     * Schedules ASIN lookups
     *
     * @param int $merchantId
     * @param int[] $ids
     * @return void
     * @throws LocalizedException
     * @throws \Zend_Db_Select_Exception
     */
    public function scheduleAsinLookups($merchantId, array $ids = [])
    {
        /** @var AdapterInterface */
        $connection = $this->getConnection();
        /** @var string */
        $listingTable = $this->getMainTable();

        // build query
        $select = $connection->select()->from(
            ['listing' => $listingTable],
            []
        )->where(
            'listing.list_status = ?',
            Definitions::VALIDATE_ASIN_LIST_STATUS
        )->where(
            'listing.merchant_id = ?',
            new \Zend_Db_Expr($connection->quote($merchantId))
        )->where(
            'listing.product_id IS NOT NULL'
        )->columns(
            [
                'listing_id' => 'listing.id',
                'identifier' => 'listing.product_id',
                'product_id_type' => 'listing.product_id_type'
            ]
        );

        if (!empty($ids)) {
            $select->where(
                'listing.catalog_product_id IN (?)',
                $ids
            );
        }

        if (!$data = $connection->fetchAll($select)) {
            return;
        }

        $insertions = [];
        $productIdTypes = Definitions::PRODUCT_ID_TYPES;

        foreach ($data as $row) {
            $productIdType = $row['product_id_type'];
            $productIdType = isset($productIdTypes[$productIdType]) ? $productIdTypes[$productIdType] : 'ASIN';
            $insertions[$productIdType][] = [
                $row['listing_id'] => preg_replace("/[^a-zA-Z0-9]+/", "", trim($row['identifier']))
            ];
        }

        foreach ($insertions as $type => $lookups) {
            $counter = 1;
            foreach (array_chunk($lookups, self::MAX_LOOKUP_QUOTA) as $chunkLookups) {
                $commandData = [
                    'body' => [
                        'attribute_type' => $type,
                        'attributes' => $chunkLookups
                    ],
                    'identifier' => $type . $counter
                ];
                $command = $this->getProductsByAttributesFactory->create($commandData);
                $this->commandDispatcher->dispatch($merchantId, $command);
                $counter++;
            }
        }
    }

    /**
     * Schedules General search lookups
     *
     * @param int $merchantId
     * @param int[] $ids
     * @return void
     * @throws LocalizedException
     * @throws \Zend_Db_Select_Exception
     */
    public function scheduleGeneralSearchLookups($merchantId, array $ids = [])
    {
        /** @var AdapterInterface */
        $connection = $this->getConnection();
        $listingTable = $this->getMainTable();

        // build query
        $select = $connection->select()->from(
            ['listing' => $listingTable],
            []
        )->where(
            'listing.list_status = ?',
            Definitions::GENERAL_SEARCH_LIST_STATUS
        )->where(
            'listing.merchant_id = ?',
            new \Zend_Db_Expr($connection->quote($merchantId))
        )->columns(
            [
                'listing_id' => 'listing.id',
                'identifier' => 'listing.product_id',
                'query' => 'listing.name'
            ]
        );

        if (!empty($ids)) {
            $select->where(
                'listing.catalog_product_id IN (?)',
                $ids
            );
        }

        if (!$data = $connection->fetchAll($select)) {
            return;
        }

        foreach ($data as $row) {
            $commandData = [
                'body' => [
                    'listing_id' => $row['listing_id'],
                    'query' => $row['query']
                ],
                'identifier' => $row['query'],
            ];
            /** @var \Magento\Amazon\Domain\Command\GetProductsByQuery $command */
            $command = $this->getProductsByQueryCommandFactory->create($commandData);
            $this->commandDispatcher->dispatch($merchantId, $command);
        }
    }

    /**
     * Clear reindex.
     *
     * Resets both quantity and price after initial list
     * to force a re-index and update of values on Amazon
     *
     * @param array $ids
     * @param string $type
     * @return void
     * @throws LocalizedException
     */
    public function clearReindexValues(array $ids, $type)
    {
        /** @var AdapterInterface */
        $connection = $this->getConnection();
        /** @var string $tableName */
        $tableName = $this->getMainTable();

        $bind = [
            $type => (int)0,
        ];

        // chunk deletion
        foreach (array_chunk($ids, self::CHUNK_SIZE) as $idList) {
            $where = [
                'id IN (?)' => $idList
            ];

            try {
                $connection->beginTransaction();
                $connection->update($tableName, $bind, $where);
                $connection->commit();
            } catch (\Exception $e) {
                $connection->rollBack();
            }
        }
    }

    /**
     * Schedules new listings for insertion on Amazon marketplace
     *
     * In the event a listing has not assigned listing condition, it
     * is excluded for insertion and moved to "missing condition"
     * listing status
     *
     * @param int $merchantId
     * @param bool $isAutoInsert
     * @return void
     * @throws LocalizedException
     * @throws \Zend_Db_Select_Exception
     */
    public function scheduleListingInsertions(int $merchantId, bool $isAutoInsert)
    {
        /** @var AdapterInterface */
        $connection = $this->getConnection();
        /** @var string */
        $listingTable = $this->getMainTable();
        /** @var string */
        $condition = $connection->quoteIdentifier('condition');

        // query to check for missing listing condition and edits listing status if applicable
        $bind = [
            'list_status' => (int)Definitions::MISSING_CONDITION_LIST_STATUS
        ];

        $where = [
            'list_status = ?' => (int)Definitions::READY_LIST_STATUS,
            'merchant_id = ?' => (int)$merchantId,
            $condition . ' < ?' => (int)1
        ];

        $listingStatusWhiteList = $isAutoInsert ?
            [Definitions::READY_LIST_STATUS, Definitions::LIST_IN_PROGRESS_LIST_STATUS] :
            [Definitions::LIST_IN_PROGRESS_LIST_STATUS];

        try {
            $connection->beginTransaction();
            $connection->update($listingTable, $bind, $where);
            $connection->commit();
        } catch (\Exception $e) {
            $connection->rollBack();
        }

        // main query to pull all listings scheduled (auto or manually) for Amazon insertion
        $select = $connection->select()->from(
            ['listing' => $listingTable],
            []
        )->where(
            'listing.merchant_id = ?',
            $merchantId
        )->where(
            'listing.list_status in (?)',
            $listingStatusWhiteList
        )->columns(
            [
                'id' => 'listing.id',
                'seller_sku' => 'listing.seller_sku',
                'asin' => 'listing.asin',
                'condition' => 'listing.condition',
                'condition_override' => 'listing.condition_override',
                'condition_notes_override' => 'listing.condition_notes_override',
                'catalog_product_id' => 'listing.catalog_product_id',
                'list_price' => 'listing.list_price',
                'msrp_price' => 'listing.msrp_price',
                'name' => 'listing.name',
                'product_tax_code' => 'listing.product_tax_code'
            ]
        );

        if (!$data = $connection->fetchAll($select)) {
            return;
        }

        foreach ($data as $row) {
            $this->dispatchUpdateListingCommand($merchantId, $row);
        }

        if ($isAutoInsert) {
            // query to move listings in "ready to list" status into "list in progress" status
            $bind = [
                'list_status' => (int)Definitions::LIST_IN_PROGRESS_LIST_STATUS
            ];

            $where = [
                'merchant_id = ?' => (int)$merchantId,
                'list_status = ?' => Definitions::READY_LIST_STATUS
            ];

            try {
                $connection->beginTransaction();
                $connection->update($listingTable, $bind, $where);
                $connection->commit();
            } catch (\Exception $e) {
                $connection->rollBack();
            }
        }
    }

    /**
     * Schedules listings for a condition override on Amazon marketplace
     *
     * Listings with a status of "condition override" have the removal
     * request submitted to Amazon
     *
     * @param int $merchantId
     * @return void
     * @throws LocalizedException
     * @throws \Zend_Db_Select_Exception
     */
    public function scheduleConditionOverrides($merchantId)
    {
        /** @var array */
        $statuses = [
            Definitions::CONDITION_OVERRIDE_LIST_STATUS
        ];

        /** @var AdapterInterface */
        $connection = $this->getConnection();
        /** @var string */
        $listingTable = $this->getMainTable();
        /** @var string */
        $accountTable = $this->resourceConnection->getTableName('channel_amazon_account_listing');

        // main query to select all listings scheduled for removal from Amazon
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
            'listing.list_status IN (?)',
            $statuses
        )->where(
            'listing.fulfilled_by = "DEFAULT" OR listing.fulfilled_by_update = 0'
        )->columns(
            [
                'merchant_id' => new \Zend_Db_Expr($connection->quoteInto('?', $merchantId)),
                'id' => 'listing.id',
                'sku' => 'listing.seller_sku',
                'list_status' => 'listing.list_status',
                'handling_time' => 'account.handling_time',
                'handling_override' => 'listing.handling_override',
                'fulfilled_by' => 'listing.fulfilled_by',
                'fulfilled_by_update' => 'listing.fulfilled_by_update',
                'qty' => new \Zend_Db_Expr($connection->quote('0'))
            ]
        );

        if (!$data = $connection->fetchAll($select)) {
            return;
        }

        foreach ($data as $row) {
            $commandData = [
                'body' => $this->prepareCommandBody($row, true),
                'identifier' => (string)$row['id']
            ];
            /** @var \Magento\Amazon\Domain\Command\UpdateInventoryQty $command */
            $command = $this->updateInventoryQtyCommandFactory->create($commandData);
            $this->commandDispatcher->dispatch((int)$row['merchant_id'], $command);
            $this->handleFulfillment($command);
        }
    }

    /**
     * Prepare command body
     *
     * @param array $listingData
     * @param bool $conditionOverrideListing
     * @return array
     */
    public function prepareCommandBody(array $listingData, bool $conditionOverrideListing): array
    {
        $handling = $listingData['handling_override'];
        $handling = $handling ?: $listingData['handling_time'];
        $handling = $handling ?: self::HANDLING_TIME;
        return [
            'id' => $listingData['id'],
            'sku' => $listingData['sku'],
            'qty' => $listingData['qty'],
            'fulfilled_by' => $listingData['fulfilled_by'],
            'fulfilled_by_update' => $listingData['fulfilled_by_update'],
            'handling' => $handling,
            'condition_override_listing' => $conditionOverrideListing,
        ];
    }

    /**
     * Handle fulfillment
     *
     * @param \Magento\Amazon\Domain\Command\UpdateInventoryQty $command
     * @return void
     * @throws \Exception
     */
    public function handleFulfillment(\Magento\Amazon\Domain\Command\UpdateInventoryQty $command)
    {
        try {
            $commandData = $command->getBody();
            $listing = $this->listingFactory->create();
            $listing->load($commandData['id']);
            if ($listing->getId() && $listing->getFulfilledByUpdate()) {
                $listing->setFulfilledByUpdate(false);
                $this->save($listing);
            }
        } catch (AlreadyExistsException $e) {
            $this->ascClientLogger->critical($e);
        }
    }

    /**
     * Schedules listings for removal from Amazon marketplace
     *
     * Listings with a status of "remove in progress" and "to be ended"
     * have the removal request submitted to Amazon
     *
     * @param int $merchantId
     * @return void
     * @throws LocalizedException
     * @throws \Zend_Db_Select_Exception
     */
    public function scheduleListingRemovals($merchantId)
    {
        /** @var array */
        $statuses = [
            Definitions::REMOVE_IN_PROGRESS_LIST_STATUS,
            Definitions::TOBEENDED_LIST_STATUS
        ];

        /** @var AdapterInterface */
        $connection = $this->getConnection();
        /** @var string */
        $mainTable = $this->getMainTable();

        // main query to select all listings scheduled for removal from Amazon
        $select = $connection->select()->from(
            ['listing' => $mainTable],
            []
        )->where(
            'listing.merchant_id = ?',
            $merchantId
        )->where(
            'listing.list_status IN (?)',
            $statuses
        )->columns(
            [
                'sku' => 'listing.seller_sku',
            ]
        );

        if (!$data = $connection->fetchAll($select)) {
            return;
        }

        foreach ($data as $row) {
            $commandData = [
                'body' => [
                    'sku' => $row['sku'],
                ],
                'identifier' => $row['sku'],
            ];

            /** @var RemoveProduct $command */
            $command = $this->removeProductFactory->create($commandData);
            $this->commandDispatcher->dispatch($merchantId, $command);
        }
    }

    /**
     * Get product entity link field
     *
     * @return string
     * @throws \Exception
     */
    private function getProductEntityLinkField(): string
    {
        return $this->metadataPool
            ->getMetadata(ProductInterface::class)
            ->getLinkField();
    }

    /**
     * Dispatch UpdateListing Command
     *
     * @param int $merchantId
     * @param array $listingData
     * @return void
     */
    public function dispatchUpdateListingCommand(int $merchantId, array $listingData)
    {
        $commandData = $this->prepareUpdateListingCommandData($merchantId, $listingData);
        /** @var UpdateListing $command */
        $command = $this->updateListingFactory->create(
            [
                'body' => $commandData,
                'identifier' => $listingData['id']
            ]
        );
        $this->commandDispatcher->dispatch($merchantId, $command);
    }

    /**
     * Prepare Command Data
     *
     * @param int $merchantId
     * @param array $listingData
     * @return array
     */
    private function prepareUpdateListingCommandData(
        int $merchantId,
        array $listingData
    ): array {
        $condition = (int)$listingData['condition_override'] ?: (int)$listingData['condition'];
        $conditionNotes = $this->accountConditionNotes->getNotes($merchantId, $condition);
        $conditionNotes = $listingData['condition_notes_override'] ?: $conditionNotes;

        return [
            'id' => $listingData['id'],
            'sku' => $listingData['seller_sku'],
            'asin' => $listingData['asin'],
            'condition' => $condition,
            'condition_notes' => $conditionNotes,
            'list_price' => $listingData['list_price'],
            'msrp_price' => $listingData['msrp_price'],
            'name' => $listingData['name'],
            'product_tax_code' => $listingData['product_tax_code']
        ];
    }
}
