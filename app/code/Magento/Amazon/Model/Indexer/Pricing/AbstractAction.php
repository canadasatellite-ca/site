<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\Amazon\Model\Indexer\Pricing;

use Magento\Amazon\Api\AccountListingRepositoryInterface;
use Magento\Amazon\Api\Data\AccountInterface;
use Magento\Amazon\Api\Data\AccountListingInterface;
use Magento\Amazon\Api\Data\PricingRuleInterface;
use Magento\Amazon\Api\ListingRepositoryInterface;
use Magento\Amazon\Api\ListingRuleRepositoryInterface;
use Magento\Amazon\Domain\Command\CommandDispatcher;
use Magento\Amazon\Domain\Command\UpdateProductPrice;
use Magento\Amazon\Domain\Command\UpdateProductPriceFactory;
use Magento\Amazon\Logger\AscClientLogger;
use Magento\Amazon\Model\Amazon\Definitions;
use Magento\Amazon\Model\ResourceModel\Amazon\Account\CollectionFactory;
use Magento\Amazon\Model\ResourceModel\Amazon\Listing\Log as LogResourceModel;
use Magento\Amazon\Model\ResourceModel\Amazon\Pricing\Rule\CollectionFactory as PricingRuleCollectionFactory;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\CatalogRule\Model\RuleFactory;
use Magento\Directory\Model\Currency;
use Magento\Eav\Api\AttributeRepositoryInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\DB\Select;
use Magento\Framework\EntityManager\MetadataPool;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class AbstractAction
 */
abstract class AbstractAction
{
    /** @var int */
    const CHUNK_SIZE = 1000;

    /** @var int */
    const MIN_VALUE = 1;

    /** @var MetadataPool $metadataPool */
    protected $metadataPool;
    /** @var AscClientLogger $ascClientLogger */
    protected $ascClientLogger;
    /** @var ResourceConnection $resourceConnection */
    protected $resourceConnection;
    /** @var CollectionFactory $collectionFactory */
    protected $collectionFactory;
    /** @var AccountListingRepositoryInterface $accountListingRepository */
    protected $accountListingRepository;
    /** @var StoreManagerInterface $storeManager */
    protected $storeManager;
    /** @var Currency $currency */
    protected $currency;
    /** @var ScopeConfigInterface $scopeConfig */
    protected $scopeConfig;
    /** @var AttributeRepositoryInterface $attributeRepository */
    protected $attributeRepository;
    /** @var PricingRuleCollectionFactory $pricingRuleCollectionFactory */
    protected $pricingRuleCollectionFactory;
    /** @var TimezoneInterface $timezone */
    protected $timezone;
    /** @var Json $serializer */
    protected $serializer;
    /** @var RuleFactory $ruleFactory */
    protected $ruleFactory;
    /** @var LogResourceModel $logResourceModel */
    protected $logResourceModel;
    /** @var array $ids */
    protected $ids;

    /**
     * @var AdapterInterface
     */
    private $connection;

    /**
     * @var CommandDispatcher
     */
    private $commandDispatcher;

    /**
     * @var UpdateProductPriceFactory
     */
    private $updateProductPriceFactory;

    /**
     * @var ListingRepositoryInterface
     */
    private $listingRepository;

    /**
     * @var ListingRuleRepositoryInterface
     */
    private $listingRuleRepository;

    /**
     * @param ResourceConnection $resourceConnection
     * @param MetadataPool $metadataPool
     * @param AscClientLogger $ascClientLogger
     * @param CollectionFactory $collectionFactory
     * @param AccountListingRepositoryInterface $accountListingRepository
     * @param StoreManagerInterface $storeManager
     * @param Currency $currency
     * @param ScopeConfigInterface $scopeConfig
     * @param AttributeRepositoryInterface $attributeRepository
     * @param PricingRuleCollectionFactory $pricingRuleCollectionFactory
     * @param TimezoneInterface $timezone
     * @param Json $serializer
     * @param RuleFactory $ruleFactory
     * @param LogResourceModel $logResourceModel
     * @param CommandDispatcher $commandDispatcher
     * @param UpdateProductPriceFactory $updateProductPriceFactory
     * @param ListingRepositoryInterface $listingRepository
     * @param ListingRuleRepositoryInterface $listingRuleRepository
     */
    public function __construct(
        ResourceConnection $resourceConnection,
        MetadataPool $metadataPool,
        AscClientLogger $ascClientLogger,
        CollectionFactory $collectionFactory,
        AccountListingRepositoryInterface $accountListingRepository,
        StoreManagerInterface $storeManager,
        Currency $currency,
        ScopeConfigInterface $scopeConfig,
        AttributeRepositoryInterface $attributeRepository,
        PricingRuleCollectionFactory $pricingRuleCollectionFactory,
        TimezoneInterface $timezone,
        Json $serializer,
        RuleFactory $ruleFactory,
        LogResourceModel $logResourceModel,
        CommandDispatcher $commandDispatcher,
        UpdateProductPriceFactory $updateProductPriceFactory,
        ListingRepositoryInterface $listingRepository,
        ListingRuleRepositoryInterface $listingRuleRepository
    ) {
        $this->resourceConnection = $resourceConnection;
        $this->connection = $this->resourceConnection->getConnection();
        $this->metadataPool = $metadataPool;
        $this->ascClientLogger = $ascClientLogger;
        $this->collectionFactory = $collectionFactory;
        $this->accountListingRepository = $accountListingRepository;
        $this->storeManager = $storeManager;
        $this->currency = $currency;
        $this->scopeConfig = $scopeConfig;
        $this->attributeRepository = $attributeRepository;
        $this->pricingRuleCollectionFactory = $pricingRuleCollectionFactory;
        $this->timezone = $timezone;
        $this->serializer = $serializer;
        $this->ruleFactory = $ruleFactory;
        $this->logResourceModel = $logResourceModel;
        $this->commandDispatcher = $commandDispatcher;
        $this->updateProductPriceFactory = $updateProductPriceFactory;
        $this->listingRepository = $listingRepository;
        $this->listingRuleRepository = $listingRuleRepository;
    }

    /**
     * Reindex all
     *
     * @return void
     */
    public function reindexAll()
    {
        /** @var AdapterInterface */
        $connection = $this->connection;
        $connection->beginTransaction();

        try {
            $this->synchronizeListing();
            $connection->commit();
        } catch (\Exception $e) {
            $this->ascClientLogger->critical($e);
            $connection->rollBack();
        }
    }

    /**
     * Reindex partial by ids
     *
     * @param array $ids
     * @return void
     */
    public function reindexPartial(array $ids = [])
    {
        /** @var AdapterInterface */
        $connection = $this->connection;
        $connection->beginTransaction();

        // set ids
        $this->ids = array_unique($ids);

        try {
            $this->synchronizeListing();
            $connection->commit();
        } catch (\Exception $e) {
            $this->ascClientLogger->critical($e);
            $connection->rollBack();
        }
    }

    /**
     * Runs pricing synchronization by account
     *
     * @return void
     * @throws NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function synchronizeListing()
    {
        $collection = $this->collectionFactory->create();

        $collection->addFieldToFilter('is_active', 1);

        foreach ($collection as $account) {
            $merchantId = $account->getMerchantId();

            try {
                /** @var AccountListingInterface */
                $accountListing = $this->accountListingRepository->getByMerchantId($merchantId);
            } catch (\Exception $e) {
                // skip
                continue;
            }

            // reindex pricing
            if (!empty($accountListing->getId())) {
                $this->reindexPricing($accountListing, $account);
            }
        }
    }

    /**
     * Reindex Amazon list price
     *
     * @param AccountListingInterface $accountListing
     * @param AccountInterface $account
     * @return void
     * @throws NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function reindexPricing(AccountListingInterface $accountListing, AccountInterface $account)
    {
        /** @var int $merchantId */
        $merchantId = (int)$account->getMerchantId();
        /** @var string $countryCode */
        $countryCode = $account->getCountryCode();
        /** @var float $currencyRate */
        $currencyRate = $this->getCurrencyRate($accountListing);
        /** @var string */
        $priceField = $accountListing->getPriceField();
        /** @var array */
        $storeIds = $this->getStoreIds($merchantId);

        // clean index table
        $this->cleanIndexTable();
        // set map pricing
        $this->setMap($accountListing);
        // set msrp pricing
        $this->setMsrp($accountListing);

        // build partial index table
        if (!empty($this->ids)) {
            // process $ids as $chunkIds
            foreach (array_chunk($this->ids, self::CHUNK_SIZE) as $chunkIds) {
                $this->buildIndexTable($merchantId, $chunkIds);
            }
        } else { // build full index table
            $this->buildIndexTable($merchantId);
        }

        foreach ($storeIds as $storeId) {
            $this->setCatalogPrice($accountListing, $storeId, $priceField);
        }

        if ($priceField != 'price') {
            foreach ($storeIds as $storeId) {
                $this->setCatalogPrice($accountListing, $storeId, 'price');
            }
        }

        /** @var CollectionFactory $collection */
        $collection = $this->pricingRuleCollectionFactory->create()
            ->addFieldToFilter('merchant_id', $merchantId)
            ->addFieldToFilter('is_active', '1')
            ->setOrder('sort_order', 'asc');

        // loop over pricing rules
        foreach ($collection as $rule) {
            // check for valid date range
            if (!$this->checkDateRange($rule->getFromDate(), $rule->getToDate())) {
                continue;
            }

            // get eligible product ids
            $ruleIds = $this->getEligibleProducts($rule);

            // continue if no eligible ids
            if (empty($ruleIds)) {
                continue;
            }

            // update index table with eligible ids
            $this->updateRuleIds($ruleIds, 2, 0);

            // competitor pricing rule
            if ($rule->getAuto()) {
                $this->processAutomatedRule($accountListing, $countryCode, $rule, (float)$currencyRate);
            } else { // manual pricing rule
                $this->processManualRule($rule);
            }

            // stop further rules processing
            if ($rule->getStopRulesProcessing()) {
                $this->updateRuleIds($ruleIds, 1, 2);
            } else { // continue further rules processing (if applicable)
                $this->updateRuleIds($ruleIds, 0, 2);
            }
        }

        // add vat percentage (if applicable)
        if ($accountListing->getVatIsActive()) {
            if ($vatPercentage = $accountListing->getVatPercentage()) {
                $this->applyVatTax($vatPercentage);
            }
        }

        // apply pricing overrides (if applicable)
        $this->applyPricingOverrides($merchantId);
        // apply landed price
        $this->applyLandedPrice($merchantId);
        // update listing table
        $this->updateListingTable($merchantId);
        // update actions table
        $this->updateActionsTable($merchantId);
        // clear listing table update flags
        $this->clearUpdatesFlag($merchantId);
    }

    /**
     * Get currency conversion rate (if applicable)
     *
     * @param AccountListingInterface $account
     * @return float
     * @throws NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function getCurrencyRate(AccountListingInterface $account): float
    {
        // if conversion is active
        if ($account->getCcIsActive()) {
            // get currency conversion options
            $store = $this->storeManager->getStore();
            $codes = $store->getAvailableCurrencyCodes(true);
            $base = $store->getBaseCurrency();
            $rates = $this->currency->getCurrencyRates($base, $codes);

            foreach ($rates as $symbol => $rate) {
                // if same as base skip
                if ($symbol == $account->getCcRate()) {
                    return round($rate, 6, PHP_ROUND_HALF_UP);
                }
            }
        }

        // return default
        return 1.00;
    }

    /**
     * Get eligible store ids
     *
     * @param int $merchantId
     * @return array
     * @throws \Zend_Db_Select_Exception
     */
    private function getStoreIds($merchantId): array
    {
        /** @var AdapterInterface */
        $connection = $this->connection;
        $storeIds = [];

        $query = $connection->select()->from(
            ['rule' => $this->resourceConnection->getTableName('channel_amazon_listing_rule')],
            []
        )->joinInner(
            ['store' => $this->resourceConnection->getTableName('store')],
            'store.website_id = rule.website_id',
            []
        )->where(
            'rule.merchant_id = ?',
            $merchantId
        )->columns(
            [
                'store_id' => 'store.store_id'
            ]
        );

        if ($rows = $connection->fetchAll($query)) {
            foreach ($rows as $row) {
                $storeIds[] = $row['store_id'];
            }
        }

        $storeIds = array_unique($storeIds);

        // website scope enabled
        if ($this->scopeConfig->getValue('catalog/price/scope', ScopeInterface::SCOPE_STORE)) {
            $storeIds[] = 0;
        } else {
            array_unshift($storeIds, 0);
        }

        return $storeIds;
    }

    /**
     * Cleans pricing index table
     *
     * @return void
     */
    protected function cleanIndexTable()
    {
        /** @var AdapterInterface */
        $connection = $this->connection;
        $connection->delete($this->resourceConnection->getTableName('channel_amazon_pricing_index'));
    }

    /**
     * Set MAP pricing
     *
     * @param AccountListingInterface $account
     * @param array $ids
     * @return void
     * @throws NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Zend_Db_Select_Exception
     */
    private function setMap(AccountListingInterface $account, $ids = null)
    {
        /** @var AdapterInterface */
        $connection = $this->connection;
        /** @var float */
        $currencyRate = $this->getCurrencyRate($account);
        /** @var int */
        $merchantId = $account->getMerchantId();
        /** @var string */
        $linkId = $this->getProductEntityLinkField();

        // clear existing floor pricing
        $bind = [
            'map_price' => new \Zend_Db_Expr('0.00')
        ];

        // if $ids
        if ($ids) {
            $where = [
                'merchant_id = ?' => (int)$merchantId,
                'catalog_product_id IN (?)',
                $ids
            ];
        } else {
            $where = [
                'merchant_id = ?' => (int)$merchantId
            ];
        }

        $connection->update($this->resourceConnection->getTableName('channel_amazon_listing'), $bind, $where);

        if (!$mapField = $account->getMapPriceField()) {
            return;
        }

        /** @var array */
        $storeIds = $this->getStoreIds($merchantId);

        // apply floor pricing
        foreach ($storeIds as $storeId) {
            // updates listing table
            $select = $connection->select()->from(
                false,
                [
                    'map_price' => new \Zend_Db_Expr(
                        $connection->quoteInto('decimalTable.value * ?', $currencyRate)
                    )
                ]
            )->joinInner(
                ['attribute' => $this->resourceConnection->getTableName('eav_attribute')],
                $connection->quoteInto(
                    'attribute.attribute_code = ?',
                    $mapField
                ),
                []
            );

            $select->where(
                'decimalTable.store_id = ?',
                (int)$storeId
            )->where(
                'decimalTable.value > ?',
                (int)0
            )->where(
                'listingTable.map_price < ?',
                floatval(0.001)
            )->where(
                'listingTable.merchant_id = ?',
                (int)$merchantId
            );

            // if $ids
            if ($ids) {
                $select->where(
                    'listingTable.catalog_product_id IN (?)',
                    $ids
                );
            }

            if ($linkId != 'entity_id') {
                $maxId = ($this->getMaxIdByTime()) ? $this->getMaxIdByTime() : 1;

                $subquery = new \Zend_Db_Expr(
                    '(SELECT entity_id, MAX(row_id) as row_id
                   FROM ' . $this->resourceConnection->getTableName("catalog_product_entity") . '
                   WHERE created_in <= ' . $maxId . '
                   GROUP BY entity_id)'
                );

                $select->joinInner(
                    ['cpe' => $subquery],
                    'cpe.entity_id = listingTable.catalog_product_id',
                    []
                )->joinInner(
                    [
                        'decimalTable' => $this->resourceConnection->getTableName('catalog_product_entity_decimal')
                    ],
                    'decimalTable.attribute_id = attribute.attribute_id AND decimalTable.'
                    . $linkId . ' = cpe.' . $linkId,
                    []
                );
            } else {
                $select->joinInner(
                    ['cpe' => $this->resourceConnection->getTableName('catalog_product_entity')],
                    'cpe.' . $linkId . ' = listingTable.catalog_product_id',
                    []
                )->joinInner(
                    ['decimalTable' => $this->resourceConnection->getTableName('catalog_product_entity_decimal')],
                    'decimalTable.attribute_id = attribute.attribute_id AND decimalTable.'
                    . $linkId . ' = cpe.' . $linkId,
                    []
                );
            }

            $select->group('cpe.entity_id');

            $update = $connection->updateFromSelect(
                $select,
                ['listingTable' => $this->resourceConnection->getTableName('channel_amazon_listing')]
            );
            $connection->query($update);
        }
    }

    /**
     * Set MSRP and MAP pricing
     *
     * @param AccountListingInterface $account
     * @param array $ids
     * @return void
     * @throws NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Zend_Db_Select_Exception
     */
    private function setMsrp(AccountListingInterface $account, $ids = null)
    {
        /** @var AdapterInterface */
        $connection = $this->connection;
        /** @var float */
        $currencyRate = $this->getCurrencyRate($account);
        /** @var int */
        $merchantId = $account->getMerchantId();
        /** @var string */
        $linkId = $this->getProductEntityLinkField();

        // clear existing floor pricing
        $bind = [
            'msrp_price' => new \Zend_Db_Expr('0.00')
        ];

        // if $ids
        if ($ids) {
            $where = [
                'merchant_id = ?' => (int)$merchantId,
                'catalog_product_id IN (?)' => $ids
            ];
        } else {
            $where = [
                'merchant_id = ?' => (int)$merchantId
            ];
        }

        $connection->update($this->resourceConnection->getTableName('channel_amazon_listing'), $bind, $where);

        if (!$msrpField = $account->getStrikePriceField()) {
            return;
        }

        /** @var array */
        $storeIds = $this->getStoreIds($merchantId);

        // apply floor pricing
        foreach ($storeIds as $storeId) {
            // updates listing table
            $select = $connection->select()->from(
                false,
                [
                    'msrp_price' => new \Zend_Db_Expr(
                        $connection->quoteInto('decimalTable.value * ?', $currencyRate)
                    )
                ]
            )->joinInner(
                ['attribute' => $this->resourceConnection->getTableName('eav_attribute')],
                $connection->quoteInto(
                    'attribute.attribute_code = ?',
                    $msrpField
                ),
                []
            );

            $select->where(
                'decimalTable.store_id = ?',
                (int)$storeId
            )->where(
                'decimalTable.value > ?',
                (int)0
            )->where(
                'listingTable.msrp_price < ?',
                floatval(0.001)
            )->where(
                'listingTable.merchant_id = ?',
                (int)$merchantId
            );

            // if $ids
            if ($ids) {
                $select->where(
                    'listingTable.catalog_product_id IN (?)',
                    $ids
                );
            }

            if ($linkId != 'entity_id') {
                $maxId = ($this->getMaxIdByTime()) ? $this->getMaxIdByTime() : 1;

                $subquery = new \Zend_Db_Expr(
                    '(SELECT entity_id, MAX(row_id) as row_id '
                    . 'FROM ' . $this->resourceConnection->getTableName("catalog_product_entity") . '
                    WHERE created_in <= ' . $maxId . '
                    GROUP BY entity_id)'
                );

                $select->joinInner(
                    ['cpe' => $subquery],
                    'cpe.entity_id = listingTable.catalog_product_id',
                    []
                )->joinInner(
                    ['decimalTable' => $this->resourceConnection->getTableName('catalog_product_entity_decimal')],
                    'decimalTable.attribute_id = attribute.attribute_id AND decimalTable.'
                    . $linkId . ' = cpe.' . $linkId,
                    []
                );
            } else {
                $select->joinInner(
                    ['cpe' => $this->resourceConnection->getTableName('catalog_product_entity')],
                    'cpe.' . $linkId . ' = listingTable.catalog_product_id',
                    []
                )->joinInner(
                    ['decimalTable' => $this->resourceConnection->getTableName('catalog_product_entity_decimal')],
                    'decimalTable.attribute_id = attribute.attribute_id AND decimalTable.'
                    . $linkId . ' = cpe.' . $linkId,
                    []
                );
            }

            $select->group('cpe.entity_id');

            $update = $connection->updateFromSelect(
                $select,
                ['listingTable' => $this->resourceConnection->getTableName('channel_amazon_listing')]
            );
            $connection->query($update);
        }
    }

    /**
     * Retrieve max version id for requested datetime
     *
     * @return string
     * @throws \Zend_Db_Select_Exception
     */
    private function getMaxIdByTime()
    {
        /** @var AdapterInterface */
        $connection = $this->connection;

        if ($updateTable = $this->resourceConnection->getTableName('staging_update')) {
            $timestamp = date('Y-m-d H:i:s');
            $select = $connection->select()
                ->from($updateTable)
                ->where('start_time <= ?', $timestamp)
                ->order(['id ' . Select::SQL_DESC])
                ->limit(1);
            return $connection->fetchOne($select);
        }
        return 1;
    }

    /**
     * Builds Amazon channel pricing index table
     *
     * @param int $merchantId
     * @param array $ids
     * @return void
     * @throws \Zend_Db_Select_Exception
     */
    private function buildIndexTable($merchantId, $ids = null)
    {
        /** @var AdapterInterface */
        $connection = $this->connection;
        /** @var array */
        $statuses = [
            Definitions::ERROR_LIST_STATUS,
            Definitions::ACTIVE_LIST_STATUS
        ];
        // query insert fields
        $fields = [
            'merchant_id',
            'parent_id',
            'product_id',
            'asin',
            'shipping_price',
            'condition',
            'shipping_calculated'
        ];

        // select query
        $select = $connection->select()->from(
            ['listing' => $this->resourceConnection->getTableName('channel_amazon_listing')],
            []
        )->where(
            'listing.merchant_id = ?',
            (int)$merchantId
        )->where(
            'listing.list_status IN (?)',
            $statuses
        );

        // if $ids
        if ($ids) {
            $select->where(
                'listing.catalog_product_id IN (?)',
                $ids
            );
        }

        $select->columns(
            [
                'merchant_id' => new \Zend_Db_Expr($connection->quote($merchantId)),
                'parent_id' => 'listing.id',
                'product_id' => 'listing.catalog_product_id',
                'asin' => 'listing.asin',
                'shipping_price' => 'listing.shipping_price',
                'condition' => new \Zend_Db_Expr(
                    'IF(listing.condition_override < 1, listing.condition, listing.condition_override)'
                ),
                'shipping_calculated' => 'listing.is_ship'
            ]
        );

        // insert query
        $sql = $connection->insertFromSelect(
            $select,
            $this->resourceConnection->getTableName('channel_amazon_pricing_index'),
            $fields,
            AdapterInterface::INSERT_IGNORE
        );
        $connection->query($sql);
    }

    /**
     * Catalog price sync query with scope consideration
     *
     * @param AccountListingInterface $account
     * @param int $storeId
     * @param string $priceField
     * @return void
     * @throws NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Zend_Db_Select_Exception
     */
    private function setCatalogPrice(AccountListingInterface $account, $storeId, $priceField)
    {
        /** @var AdapterInterface */
        $connection = $this->connection;
        /** @var float */
        $currencyRate = $this->getCurrencyRate($account);
        /** @var int */
        $merchantId = $account->getMerchantId();
        /** @var string */
        $linkId = $this->getProductEntityLinkField();

        try {
            $this->attributeRepository->get('catalog_product', $priceField);
        } catch (NoSuchEntityException $e) {
            $this->clearAccountField($account, 'price_field', 'price');
            return;
        }

        $sql = new \Zend_Db_Expr('SELECT NOW()');
        $now = $connection->fetchOne($sql);

        // updates listing table
        $select = $connection->select()->from(
            false,
            [
                'catalog_price' => new \Zend_Db_Expr(
                    $connection->quoteInto('decimalTable.value * ?', $currencyRate)
                ),
                'listing_price' => new \Zend_Db_Expr(
                    $connection->quoteInto('decimalTable.value * ?', $currencyRate)
                ),
                'landed_price' => new \Zend_Db_Expr(
                    $connection->quoteInto('(decimalTable.value * ?) + indexTable.shipping_price', $currencyRate)
                )
            ]
        )->joinInner(
            ['attribute' => $this->resourceConnection->getTableName('eav_attribute')],
            $connection->quoteInto(
                'attribute.attribute_code = ?',
                $priceField
            ),
            []
        );

        $select->where(
            'decimalTable.store_id = ?',
            (int)$storeId
        )->where(
            'decimalTable.value > ?',
            (int)0
        )->where(
            'indexTable.listing_price < ?',
            floatval(0.001)
        )->where(
            'indexTable.merchant_id = ?',
            (int)$merchantId
        );

        if ($linkId != 'entity_id') {
            $maxId = ($this->getMaxIdByTime()) ? $this->getMaxIdByTime() : 1;

            $subquery = new \Zend_Db_Expr(
                '(SELECT entity_id, MAX(row_id) as row_id
	            FROM ' . $this->resourceConnection->getTableName("catalog_product_entity") . '
	            WHERE created_in <= ' . $maxId . '
	            GROUP BY entity_id)'
            );

            $select->joinInner(
                ['cpe' => $subquery],
                'cpe.entity_id = indexTable.product_id',
                []
            )->joinInner(
                ['decimalTable' => $this->resourceConnection->getTableName('catalog_product_entity_decimal')],
                'decimalTable.attribute_id = attribute.attribute_id AND decimalTable.' . $linkId . ' = cpe.' . $linkId,
                []
            );
        } else {
            $select->joinInner(
                ['cpe' => $this->resourceConnection->getTableName('catalog_product_entity')],
                'cpe.' . $linkId . ' = indexTable.product_id',
                []
            )->joinInner(
                ['decimalTable' => $this->resourceConnection->getTableName('catalog_product_entity_decimal')],
                'decimalTable.attribute_id = attribute.attribute_id AND decimalTable.' . $linkId . ' = cpe.' . $linkId,
                []
            );
        }

        // special handling for special price
        if ($priceField == 'special_price') {
            $select->joinLeft(
                ['attributeFrom' => $this->resourceConnection->getTableName('eav_attribute')],
                'attributeFrom.attribute_code = "special_from_date"',
                []
            )->joinLeft(
                ['attributeTo' => $this->resourceConnection->getTableName('eav_attribute')],
                'attributeTo.attribute_code = "special_to_date"',
                []
            )->joinLeft(
                ['datetimeFrom' => $this->resourceConnection->getTableName('catalog_product_entity_datetime')],
                'datetimeFrom.attribute_id = attributeFrom.attribute_id AND datetimeFrom.' .
                $linkId .
                ' = cpe.' .
                $linkId,
                []
            )->joinLeft(
                ['datetimeTo' => $this->resourceConnection->getTableName('catalog_product_entity_datetime')],
                'datetimeTo.attribute_id = attributeTo.attribute_id AND datetimeTo.' . $linkId . ' = cpe.' . $linkId,
                []
            );
        }

        // special handling for special price
        if ($priceField == 'special_price') {
            $select->where(
                $connection->quoteInto(
                    'datetimeFrom.value IS NULL OR datetimeFrom.value < ?',
                    $now
                )
            )->where(
                $connection->quoteInto(
                    'datetimeTo.value IS NULL OR datetimeTo.value > ?',
                    $now
                )
            );
        }

        $select->group('cpe.entity_id');

        $update = $connection->updateFromSelect(
            $select,
            ['indexTable' => $this->resourceConnection->getTableName('channel_amazon_pricing_index')]
        );
        $connection->query($update);
    }

    /**
     * Clears an attribute mapping if not longer exists
     *
     * @param AccountListingInterface $account
     * @param string $field
     * @param string|null $value
     * @return void
     */
    public function clearAccountField(AccountListingInterface $account, $field, $value = null)
    {
        $account->setData($field, $value);

        try {
            $this->accountListingRepository->save($account);
        } catch (CouldNotSaveException $e) {
            // already deleted (continue)
            return;
        }
    }

    /**
     * Check for valid date range
     *
     * Returns false if out of date range or true if
     * in date range or no date range set
     *
     * @param \DateTime $fromDate
     * @param \DateTime $toDate
     * @return boolean
     * @throws \Exception
     */
    private function checkDateRange($fromDate, $toDate): bool
    {
        /** @var \DateTime */
        $date = $this->timezone->date()->format('Y-m-d H:i:s');
        $fromDate =
            ($fromDate) ? $this->timezone->date(new \DateTime($fromDate))->format('Y-m-d H:i:s') : false;
        $toDate = ($toDate) ? $this->timezone->date(new \DateTime($toDate))->format('Y-m-d H:i:s') : false;

        if ($fromDate) {
            if ($fromDate > $date) {
                return false;
            }
        }

        if ($toDate) {
            if ($toDate < $date) {
                return false;
            }
        }

        return true;
    }

    /**
     * Gets eligible products.
     *
     * @param PricingRuleInterface $priceRule
     * @param int|array $filterIds
     * @return array|bool
     */
    protected function getEligibleProducts(PricingRuleInterface $priceRule, $filterIds = null)
    {
        $ids = [];

        if (!$conditionsSerialized = $priceRule->getConditionsSerialized()) {
            return $ids;
        }

        // process rule conditions
        if (!$rule['conditions'][1] = $this->serializer->unserialize($conditionsSerialized)) {
            return false;
        }

        /** @var RuleFactory $ruleFactory */
        $ruleFactory = $this->ruleFactory->create();

        // add product filter
        if ($filterIds) {
            $ruleFactory->setProductsFilter($filterIds);
        }

        // set website data
        $listingRule = $this->listingRuleRepository->getByMerchantId($priceRule->getMerchantId());
        $websiteId = $listingRule->getWebsiteId();
        $ruleFactory->setWebsiteIds($websiteId);

        // set rule rules
        $ruleFactory->loadPost($rule);

        // get matching product ids
        $eligibleIds = $ruleFactory->getMatchingProductIds(true);

        // format results
        foreach ($eligibleIds as $productId => $product) {
            foreach ($product as $value) {
                if ($value) {
                    $ids[] = $productId;
                    break;
                }
            }
        }

        return array_unique($ids);
    }

    /**
     * Updates rule based eligible ids
     *
     * @param array $ids
     * @param int $stopTo
     * @param int $stopFrom
     * @return void
     */
    private function updateRuleIds(array $ids, $stopTo, $stopFrom)
    {
        /** @var AdapterInterface */
        $connection = $this->connection;

        // chunk data (if applicable)
        foreach (array_chunk($ids, self::CHUNK_SIZE) as $chunkIds) {
            // update index table with eligible ids
            $bind = [
                'stop_rules' => (int)$stopTo,
            ];

            $where = [
                'product_id IN (?)' => $chunkIds,
                'stop_rules = ?' => (int)$stopFrom
            ];

            $connection->update($this->resourceConnection->getTableName('channel_amazon_pricing_index'), $bind, $where);
        }
    }

    /**
     * Processes automated rule
     *
     * @param AccountListingInterface $account
     * @param string $countryCode
     * @param PricingRuleInterface $rule
     * @param float $currencyRate
     * @return void
     * @throws \Zend_Db_Select_Exception
     */
    private function processAutomatedRule(
        AccountListingInterface $account,
        string $countryCode,
        PricingRuleInterface $rule,
        float $currencyRate
    ) {
        // lowest price rule
        if ($rule->getAutoSource()) {
            $this->applyLowestPriceRule($countryCode, $rule);
        } else {// best buy box price
            $this->applyBestBuyBoxRule($countryCode, $rule);
        }

        // pricing adjustment settings
        $priceMovement = $rule->getPriceMovement();
        $discountType = $rule->getSimpleAction();
        $discountAmount = $rule->getDiscountAmount();

        // check for pricing adjustments
        if ($priceMovement) {
            // by fixed amount
            if ($discountType == 'by_fixed') {
                if ($priceMovement == '2') {
                    $discountAmount = -($discountAmount);
                }
            } elseif ($discountType == 'by_percent') {// by percentage
                // reduce by
                if ($priceMovement != 1) {
                    $discountAmount = (1 - ($discountAmount / 100));
                } else {// add by
                    $discountAmount = (1 + ($discountAmount / 100));
                }
            }

            // apply pricing adjustments
            $this->applyPricingAdjustments($discountAmount, $discountType);
        } else {
            $this->applyPricingAdjustments('1', 'by_percent');
        }

        // apply listing price (landed price - shipping price)
        $this->applyListingPrice();

        // ceiling price settings
        $ceilingAttribute = $rule->getCeiling();
        $ceilingPriceMovement = $rule->getCeilingPriceMovement();
        $discountAmount = 1;

        // ceiling increase / decrease by
        if ($ceilingPriceMovement) {

            /** @var float */
            $discountAmount = $rule->getCeilingDiscountAmount();

            // reduce by
            if ($ceilingPriceMovement != 1) {
                $discountAmount = number_format((1 - ($discountAmount / 100)), 4, '.', '');
            } else { // add by
                $discountAmount = number_format((1 + ($discountAmount / 100)), 4, '.', '');
            }
        }

        // apply ceiling pricing
        if ($ceilingAttribute) {
            $this->applyCeilingPricing($ceilingAttribute, $currencyRate, $discountAmount);
        }

        // floor price settings
        $floorAttribute = $rule->getFloor();
        $floorPriceMovement = $rule->getFloorPriceMovement();
        $discountAmount = 1;

        // floor increase / decrease by
        if ($floorPriceMovement) {

            /** @var float */
            $discountAmount = $rule->getFloorDiscountAmount();

            // reduce by
            if ($floorPriceMovement != 1) {
                $discountAmount = number_format((1 - ($discountAmount / 100)), 4, '.', '');
            } else {// add by
                $discountAmount = number_format((1 + ($discountAmount / 100)), 4, '.', '');
            }
        }

        // apply floor pricing
        $this->applyFloorPricing($account, $floorAttribute, $currencyRate, $discountAmount);
    }

    /**
     * Apply lowest price rule
     *
     * @param string $countryCode
     * @param PricingRuleInterface $rule
     * @return void
     * @throws \Zend_Db_Select_Exception
     */
    private function applyLowestPriceRule(string $countryCode, PricingRuleInterface $rule)
    {
        /** @var AdapterInterface */
        $connection = $this->connection;
        /** @var string */
        $orderByClause = ('
            (lowest.condition_code = index_table.condition) DESC,
            (CASE
                WHEN index_table.condition = 1 THEN FIELD(lowest.condition_code,1,11,10,4,3,2)
                WHEN index_table.condition = 2 THEN FIELD(lowest.condition_code,2,11,10,4,3,1)
                WHEN index_table.condition = 3 THEN FIELD(lowest.condition_code,3,11,10,1,4,2)
                WHEN index_table.condition = 4 THEN FIELD(lowest.condition_code,4,11,10,1,2,3)
                WHEN index_table.condition = 5 THEN FIELD(lowest.condition_code,5,11,8,7,6)
                WHEN index_table.condition = 6 THEN FIELD(lowest.condition_code,6,11,8,7,5)
                WHEN index_table.condition = 7 THEN FIELD(lowest.condition_code,7,11,5,8,6)
                WHEN index_table.condition = 8 THEN FIELD(lowest.condition_code,8,11,5,6,7)
                WHEN index_table.condition = 10 THEN FIELD(lowest.condition_code,10,4,3,2,1,11)
                WHEN index_table.condition = 11 THEN FIELD(lowest.condition_code,11,4,3,2,1,10)
            END) DESC,
            (lowest.landed_price * 1) ASC
        ');

        // apply condition variances (if exists)
        if ($rule->getAutoCondition() == 2) {
            $subSelect = $connection->select()->from(
                ['lowest' => $this->resourceConnection->getTableName('channel_amazon_pricing_lowest')],
                []
            )->where(
                'lowest.asin = index_table.asin'
            )->where(
                'lowest.country_code = ?',
                $countryCode
            )->where(
                'lowest.feedback_rating > ?',
                ($rule->getAutoMinimumFeedback() - 1)
            )->where(
                'lowest.feedback_count > ?',
                ($rule->getAutoFeedbackCount() - 1)
            )->columns(
                [
                    'landed_price' => new \Zend_Db_Expr('
                        ((100 -
                            ((CASE ' .
                        $connection->quoteInto(
                            'WHEN lowest.condition_code=1 THEN ? ',
                            $rule->getUsedlikenewVariance()
                        )
                        . $connection->quoteInto(
                            'WHEN lowest.condition_code=2 THEN ? ',
                            $rule->getUsedverygoodVariance()
                        )
                        . $connection->quoteInto(
                            'WHEN lowest.condition_code=3 THEN ? ',
                            $rule->getUsedgoodVariance()
                        )
                        . $connection->quoteInto(
                            'WHEN lowest.condition_code=4 THEN ? ',
                            $rule->getUsedAcceptableVariance()
                        )
                        . $connection->quoteInto(
                            'WHEN lowest.condition_code=5 THEN ? ',
                            $rule->getCollectiblelikenewVariance()
                        )
                        . $connection->quoteInto(
                            'WHEN lowest.condition_code=6 THEN ? ',
                            $rule->getCollectibleverygoodVariance()
                        )
                        . $connection->quoteInto(
                            'WHEN lowest.condition_code=7 THEN ? ',
                            $rule->getCollectiblegoodVariance()
                        )
                        . $connection->quoteInto(
                            'WHEN lowest.condition_code=8 THEN ? ',
                            $rule->getCollectibleacceptableVariance()
                        )
                        . $connection->quoteInto(
                            'WHEN lowest.condition_code=10 THEN ? ',
                            $rule->getRefurbishedVariance()
                        )
                        . $connection->quoteInto(
                            'WHEN lowest.condition_code=11 THEN ? ',
                            $rule->getNewVariance()
                        )
                        . ' END) - (CASE ' .
                        $connection->quoteInto(
                            'WHEN index_table.condition=1 THEN ? ',
                            $rule->getUsedlikenewVariance()
                        )
                        . $connection->quoteInto(
                            'WHEN index_table.condition=2 THEN ? ',
                            $rule->getUsedverygoodVariance()
                        )
                        . $connection->quoteInto(
                            'WHEN index_table.condition=3 THEN ? ',
                            $rule->getUsedgoodVariance()
                        )
                        . $connection->quoteInto(
                            'WHEN index_table.condition=4 THEN ? ',
                            $rule->getUsedAcceptableVariance()
                        )
                        . $connection->quoteInto(
                            'WHEN index_table.condition=5 THEN ? ',
                            $rule->getCollectiblelikenewVariance()
                        )
                        . $connection->quoteInto(
                            'WHEN index_table.condition=6 THEN ? ',
                            $rule->getCollectibleverygoodVariance()
                        )
                        . $connection->quoteInto(
                            'WHEN index_table.condition=7 THEN ? ',
                            $rule->getCollectiblegoodVariance()
                        )
                        . $connection->quoteInto(
                            'WHEN index_table.condition=8 THEN ? ',
                            $rule->getCollectibleacceptableVariance()
                        )
                        . $connection->quoteInto(
                            'WHEN index_table.condition=10 THEN ? ',
                            $rule->getRefurbishedVariance()
                        )
                        . $connection->quoteInto(
                            'WHEN index_table.condition=11 THEN ? ',
                            $rule->getNewVariance()
                        )
                        . ' END))
                        ) / 100) * lowest.landed_price
                    ')
                ]
            )->order(new \Zend_Db_Expr($orderByClause))->limit(1);
        } elseif ($rule->getAutoCondition() == 1) {// only matching condition
            $subSelect = $connection->select()->from(
                ['lowest' => $this->resourceConnection->getTableName('channel_amazon_pricing_lowest')],
                []
            )->where(
                'lowest.asin = index_table.asin'
            )->where(
                'lowest.country_code = ?',
                $countryCode
            )->where(
                'lowest.feedback_rating > ?',
                ($rule->getAutoMinimumFeedback() - 1)
            )->where(
                'lowest.feedback_count > ?',
                ($rule->getAutoFeedbackCount() - 1)
            )->where(
                'lowest.condition_code = index_table.condition'
            )->columns(
                [
                    'landed_price' => 'lowest.landed_price'
                ]
            )->order(new \Zend_Db_Expr('(lowest.landed_price * 1) ASC'))->limit(1);
        } else {// use all conditions
            $subSelect = $connection->select()->from(
                ['lowest' => $this->resourceConnection->getTableName('channel_amazon_pricing_lowest')],
                []
            )->where(
                'lowest.asin = index_table.asin'
            )->where(
                'lowest.country_code = ?',
                $countryCode
            )->where(
                'lowest.feedback_rating > ?',
                ($rule->getAutoMinimumFeedback() - 1)
            )->where(
                'lowest.feedback_count > ?',
                ($rule->getAutoFeedbackCount() - 1)
            )->columns(
                [
                    'landed_price' => 'lowest.landed_price'
                ]
            )->order(new \Zend_Db_Expr($orderByClause))->limit(1);
        }

        // query to update landed price based on competitors pricing
        $select = $connection->select()->from(
            false,
            [
                'landed_price' => new \Zend_Db_Expr('IFNULL((' . $subSelect . '), index_table.landed_price)'),
                'stop_rules' => new \Zend_Db_Expr('IF((' . $subSelect . '), 2, 0)')
            ]
        )->where(
            'index_table.stop_rules = ?',
            (int)2
        )->where(
            'index_table.asin IS NOT NULL'
        )->where(
            'index_table.shipping_calculated = ?',
            (int)1
        );

        $update = $connection->updateFromSelect(
            $select,
            ['index_table' => $this->resourceConnection->getTableName('channel_amazon_pricing_index')]
        );
        $connection->query($update);
    }

    /**
     * Apply best buy box rule
     *
     * @param string $countryCode
     * @param PricingRuleInterface $rule
     * @return void
     * @throws \Zend_Db_Select_Exception
     */
    private function applyBestBuyBoxRule(string $countryCode, PricingRuleInterface $rule)
    {
        /** @var AdapterInterface */
        $connection = $this->connection;
        /** @var string */
        $indexTable = $this->resourceConnection->getTableName('channel_amazon_pricing_index');
        /** @var string */
        $bbbTable = $this->resourceConnection->getTableName('channel_amazon_pricing_bestbuybox');

        $orderByClause = ('
            (bbb.condition_code = index_table.condition) DESC,
            (CASE
                WHEN index_table.condition = 1 THEN FIELD(bbb.condition_code,1,11,10,4,3,2)
                WHEN index_table.condition = 2 THEN FIELD(bbb.condition_code,2,11,10,4,3,1)
                WHEN index_table.condition = 3 THEN FIELD(bbb.condition_code,3,11,10,1,4,2)
                WHEN index_table.condition = 4 THEN FIELD(bbb.condition_code,4,11,10,1,2,3)
                WHEN index_table.condition = 5 THEN FIELD(bbb.condition_code,5,11,8,7,6)
                WHEN index_table.condition = 6 THEN FIELD(bbb.condition_code,6,11,8,7,5)
                WHEN index_table.condition = 7 THEN FIELD(bbb.condition_code,7,11,5,8,6)
                WHEN index_table.condition = 8 THEN FIELD(bbb.condition_code,8,11,5,6,7)
                WHEN index_table.condition = 10 THEN FIELD(bbb.condition_code,10,4,3,2,1,11)
                WHEN index_table.condition = 11 THEN FIELD(bbb.condition_code,11,4,3,2,1,10)
            END) DESC,
            (bbb.landed_price * 1) ASC
        ');

        // select query
        $select = $connection->select()->from(
            false,
            [
                'is_seller' => (int)1,
                'listing_price' => 'bbb.list_price',
                'shipping_price' => 'bbb.shipping_price',
                'landed_price' => 'bbb.landed_price'
            ]
        )->joinInner(
            ['bbb' => $bbbTable],
            'bbb.asin = indexTable.asin AND bbb.condition_code = indexTable.condition',
            []
        )->where(
            'bbb.is_seller = ?',
            (int)1
        )->where(
            'bbb.country_code = ?',
            $countryCode
        )->where(
            'indexTable.stop_rules = ?',
            (int)2
        )->where(
            'indexTable.asin IS NOT NULL'
        )->where(
            'indexTable.shipping_calculated = ?',
            (int)1
        );

        $query = $connection->updateFromSelect($select, ['indexTable' => $indexTable]);
        $connection->query($query);

        // apply condition variances (if exists)
        if ($rule->getData('auto_condition') == 2) {
            $subSelect = $connection->select()->from(
                ['bbb' => $bbbTable],
                []
            )->where(
                'bbb.asin = index_table.asin'
            )->where(
                'bbb.country_code = ?',
                $countryCode
            )->columns(
                [
                    'landed_price' => new \Zend_Db_Expr('
                            ((100 -
                                ((CASE ' .
                        $connection->quoteInto(
                            'WHEN bbb.condition_code=1 THEN ? ',
                            $rule->getUsedlikenewVariance()
                        )
                        . $connection->quoteInto(
                            'WHEN bbb.condition_code=2 THEN ? ',
                            $rule->getUsedverygoodVariance()
                        )
                        . $connection->quoteInto(
                            'WHEN bbb.condition_code=3 THEN ? ',
                            $rule->getUsedgoodVariance()
                        )
                        . $connection->quoteInto(
                            'WHEN bbb.condition_code=4 THEN ? ',
                            $rule->getUsedAcceptableVariance()
                        )
                        . $connection->quoteInto(
                            'WHEN bbb.condition_code=5 THEN ? ',
                            $rule->getCollectiblelikenewVariance()
                        )
                        . $connection->quoteInto(
                            'WHEN bbb.condition_code=6 THEN ? ',
                            $rule->getCollectibleverygoodVariance()
                        )
                        . $connection->quoteInto(
                            'WHEN bbb.condition_code=7 THEN ? ',
                            $rule->getCollectiblegoodVariance()
                        )
                        . $connection->quoteInto(
                            'WHEN bbb.condition_code=8 THEN ? ',
                            $rule->getCollectibleacceptableVariance()
                        )
                        . $connection->quoteInto(
                            'WHEN bbb.condition_code=10 THEN ? ',
                            $rule->getRefurbishedVariance()
                        )
                        . $connection->quoteInto(
                            'WHEN bbb.condition_code=11 THEN ? ',
                            $rule->getNewVariance()
                        )
                        . ' END
                                ) - (
                                CASE ' .
                        $connection->quoteInto(
                            'WHEN index_table.condition=1 THEN ? ',
                            $rule->getUsedlikenewVariance()
                        )
                        . $connection->quoteInto(
                            'WHEN index_table.condition=2 THEN ? ',
                            $rule->getUsedverygoodVariance()
                        )
                        . $connection->quoteInto(
                            'WHEN index_table.condition=3 THEN ? ',
                            $rule->getUsedgoodVariance()
                        )
                        . $connection->quoteInto(
                            'WHEN index_table.condition=4 THEN ? ',
                            $rule->getUsedAcceptableVariance()
                        )
                        . $connection->quoteInto(
                            'WHEN index_table.condition=5 THEN ? ',
                            $rule->getCollectiblelikenewVariance()
                        )
                        . $connection->quoteInto(
                            'WHEN index_table.condition=6 THEN ? ',
                            $rule->getCollectibleverygoodVariance()
                        )
                        . $connection->quoteInto(
                            'WHEN index_table.condition=7 THEN ? ',
                            $rule->getCollectiblegoodVariance()
                        )
                        . $connection->quoteInto(
                            'WHEN index_table.condition=8 THEN ? ',
                            $rule->getCollectibleacceptableVariance()
                        )
                        . $connection->quoteInto(
                            'WHEN index_table.condition=10 THEN ? ',
                            $rule->getRefurbishedVariance()
                        )
                        . $connection->quoteInto(
                            'WHEN index_table.condition=11 THEN ? ',
                            $rule->getNewVariance()
                        )
                        . ' END))
                            ) / 100) * bbb.landed_price
                        ')
                ]
            )->order(new \Zend_Db_Expr($orderByClause))->limit(1);
        } elseif ($rule->getData('auto_condition') == 1) {// only matching condition
            $subSelect = $connection->select()->from(
                ['bbb' => $bbbTable],
                []
            )->where(
                'bbb.asin = index_table.asin'
            )->where(
                'bbb.condition_code = index_table.condition'
            )->where(
                'bbb.country_code = ?',
                $countryCode
            )->columns(
                [
                    'landed_price' => 'bbb.landed_price'
                ]
            )->order(new \Zend_Db_Expr('(bbb.landed_price * 1) ASC'))->limit(1);
        } else {// use any condition for comparison
            $subSelect = $connection->select()->from(
                ['bbb' => $bbbTable],
                []
            )->where(
                'bbb.asin = index_table.asin'
            )->where(
                'bbb.country_code = ?',
                $countryCode
            )->columns(
                [
                    'landed_price' => 'bbb.landed_price'
                ]
            )->order(new \Zend_Db_Expr($orderByClause))->limit(1);
        }

        // query to update landed price based on competitors pricing
        $select = $connection->select()->from(
            false,
            [
                'landed_price' => new \Zend_Db_Expr('IFNULL((' . $subSelect . '), index_table.landed_price)'),
                'stop_rules' => new \Zend_Db_Expr('IF((' . $subSelect . '), 2, 0)')
            ]
        )->where(
            'index_table.stop_rules = ?',
            (int)2
        )->where(
            'index_table.is_seller = ?',
            (int)0
        )->where(
            'index_table.asin IS NOT NULL'
        )->where(
            'index_table.shipping_calculated = ?',
            (int)1
        );

        $update = $connection->updateFromSelect(
            $select,
            ['index_table' => $this->resourceConnection->getTableName('channel_amazon_pricing_index')]
        );
        $connection->query($update);
    }

    /**
     * Apply pricing adjustments
     *
     * @param float $discountAmount
     * @param string $discountType
     * @return void
     */
    private function applyPricingAdjustments($discountAmount, $discountType)
    {
        /** @var AdapterInterface */
        $connection = $this->connection;

        if ($discountType == 'by_percent') {
            $bind = [
                'landed_price' => new \Zend_Db_Expr($connection->quoteInto('landed_price * ?', $discountAmount)),
                'apply_vat' => 0
            ];
        } else {
            $bind = [
                'landed_price' => new \Zend_Db_Expr($connection->quoteInto('landed_price + ?', $discountAmount)),
                'apply_vat' => 0
            ];
        }

        $where = [
            'shipping_calculated = ?' => (int)1,
            'is_seller = ?' => (int)0,
            'asin IS NOT NULL',
            'stop_rules = ?' => (int)2
        ];

        $connection->update($this->resourceConnection->getTableName('channel_amazon_pricing_index'), $bind, $where);
    }

    /**
     * Apply listing price (competitor pricing rule only)
     *
     * @return void
     */
    private function applyListingPrice()
    {
        /** @var AdapterInterface */
        $connection = $this->connection;

        $bind = [
            'listing_price' => new \Zend_Db_Expr('landed_price - shipping_price')
        ];

        $where = [
            'shipping_calculated = ?' => (int)1,
            'asin IS NOT NULL',
            'stop_rules = ?' => (int)2
        ];

        $connection->update($this->resourceConnection->getTableName('channel_amazon_pricing_index'), $bind, $where);
    }

    /**
     * Apply floor pricing
     *
     * @param AccountListingInterface $account
     * @param string $floorAttribute
     * @param float $currencyRate
     * @param float $discountAmount
     * @return void
     * @throws \Zend_Db_Select_Exception
     */
    private function applyFloorPricing(
        AccountListingInterface $account,
        $floorAttribute,
        $currencyRate,
        $discountAmount
    ) {
        /** @var AdapterInterface */
        $connection = $this->connection;
        /** @var float */
        $discountAmount = ($discountAmount) ? $discountAmount : 1;
        /** @var array */
        $storeIds = $this->getStoreIds($account->getMerchantId());

        $linkId = $this->getProductEntityLinkField();

        // clear existing floor pricing
        $bind = [
            'floor_price' => new \Zend_Db_Expr('0.00')
        ];

        $where = [
            'shipping_calculated = ?' => (int)1,
            'stop_rules = ?' => (int)2,
            'ASIN IS NOT NULL'
        ];

        $connection->update($this->resourceConnection->getTableName('channel_amazon_pricing_index'), $bind, $where);

        // apply floor pricing
        foreach ($storeIds as $storeId) {
            // query for user specified floor price source (per rule)
            $select = $connection->select()->from(
                false,
                [
                    'floor_price' => new \Zend_Db_Expr(
                        '(decimal_table.value * ' . floatval($currencyRate) . ') * ' . floatval($discountAmount)
                    )
                ]
            )->joinInner(
                ['attribute' => $this->resourceConnection->getTableName('eav_attribute')],
                $connection->quoteInto('attribute.attribute_code = ?', $floorAttribute),
                []
            )->where(
                'index_table.shipping_calculated = ?',
                (int)1
            )->where(
                'index_table.asin IS NOT NULL'
            )->where(
                'decimal_table.store_id = ?',
                (int)$storeId
            )->where(
                'index_table.floor_price < ?',
                floatval(0.001)
            )->where(
                'decimal_table.value > ?',
                (int)0
            )->where(
                'index_table.stop_rules = ?',
                (int)2
            );

            if ($linkId != 'entity_id') {
                $maxId = ($this->getMaxIdByTime()) ? $this->getMaxIdByTime() : 1;

                $subquery = new \Zend_Db_Expr(
                    '(SELECT entity_id, MAX(row_id) as row_id
                    FROM ' . $this->resourceConnection->getTableName("catalog_product_entity") . '
                    WHERE created_in <= ' . $maxId . '
                    GROUP BY entity_id)'
                );

                $select->joinInner(
                    ['cpe' => $subquery],
                    'cpe.entity_id = index_table.product_id',
                    []
                )->joinInner(
                    ['decimal_table' => $this->resourceConnection->getTableName('catalog_product_entity_decimal')],
                    'decimal_table.attribute_id = attribute.attribute_id AND decimal_table.'
                    . $linkId . ' = cpe.' . $linkId,
                    []
                );
            } else {
                $select->joinInner(
                    ['cpe' => $this->resourceConnection->getTableName('catalog_product_entity')],
                    'cpe.' . $linkId . ' = index_table.product_id',
                    []
                )->joinInner(
                    ['decimal_table' => $this->resourceConnection->getTableName('catalog_product_entity_decimal')],
                    'decimal_table.attribute_id = attribute.attribute_id AND decimal_table.'
                    . $linkId . ' = cpe.' . $linkId,
                    []
                );
            }

            $update = $connection->updateFromSelect(
                $select,
                ['index_table' => $this->resourceConnection->getTableName('channel_amazon_pricing_index')]
            );
            $connection->query($update);
        }

        // query for handling no floor price source (defaults to catalog price)
        $select = $connection->select()->from(
            false,
            [
                'floor_price' => 'index_table.catalog_price'
            ]
        )->where(
            'index_table.floor_price < .001'
        )->where(
            'index_table.shipping_calculated = ?',
            (int)1
        )->where(
            'index_table.asin IS NOT NULL'
        )->where(
            'index_table.stop_rules = ?',
            (int)2
        );

        $update = $connection->updateFromSelect(
            $select,
            ['index_table' => $this->resourceConnection->getTableName('channel_amazon_pricing_index')]
        );
        $connection->query($update);

        // update listing price per floor price (if applicable)
        $select = $connection->select()->from(
            false,
            [
                'listing_price' => 'index_table.floor_price',
                'apply_vat' => (int)1
            ]
        )->where(
            'index_table.floor_price > index_table.listing_price'
        )->where(
            'index_table.shipping_calculated = ?',
            (int)1
        )->where(
            'index_table.asin IS NOT NULL'
        )->where(
            'index_table.stop_rules = ?',
            (int)2
        );

        $update = $connection->updateFromSelect(
            $select,
            ['index_table' => $this->resourceConnection->getTableName('channel_amazon_pricing_index')]
        );
        $connection->query($update);
    }

    /**
     * Apply ceiling pricing
     *
     * @param string $ceilingAttribute
     * @param float $currencyRate
     * @param float $discountAmount
     * @return void
     * @throws \Zend_Db_Select_Exception
     */
    private function applyCeilingPricing($ceilingAttribute, $currencyRate, $discountAmount)
    {
        /** @var AdapterInterface */
        $connection = $this->connection;
        /** @var float */
        $discountAmount = ($discountAmount) ? $discountAmount : 1;

        $linkId = $this->getProductEntityLinkField();

        // main query
        $select = $connection->select()->from(
            false,
            [
                'listing_price' => new \Zend_Db_Expr(
                    $connection->quoteInto('(decimal_table.value * ?) * ', floatval($currencyRate)) .
                    $connection->quote(floatval($discountAmount))
                ),
                'apply_vat' => (int)1
            ]
        )->joinInner(
            ['attribute' => $this->resourceConnection->getTableName('eav_attribute')],
            $connection->quoteInto('attribute.attribute_code = ?', $ceilingAttribute),
            []
        )->joinInner(
            ['cpe' => $this->resourceConnection->getTableName('catalog_product_entity')],
            'cpe.entity_id = index_table.product_id',
            []
        )->joinInner(
            ['decimal_table' => $this->resourceConnection->getTableName('catalog_product_entity_decimal')],
            'decimal_table.attribute_id = attribute.attribute_id AND decimal_table.' . $linkId . ' = cpe.' . $linkId,
            []
        )->where(
            'index_table.shipping_calculated = ?',
            (int)1
        )->where(
            'index_table.asin IS NOT NULL'
        )->where(
            $connection->quoteInto(
                'index_table.listing_price > (decimal_table.value * ?) * ',
                $currencyRate
            ) . $connection->quote(floatval($discountAmount))
        )->where(
            'index_table.stop_rules = ?',
            (int)2
        );

        $update = $connection->updateFromSelect(
            $select,
            ['index_table' => $this->resourceConnection->getTableName('channel_amazon_pricing_index')]
        );
        $connection->query($update);
    }

    /**
     * Processes manual rule
     *
     * @param PricingRuleInterface $priceRule
     * @return void
     * @throws \Zend_Db_Select_Exception
     */
    private function processManualRule(PricingRuleInterface $priceRule)
    {
        /** @var AdapterInterface */
        $connection = $this->connection;
        /** @var int */
        $discountType = $priceRule->getSimpleAction();
        /** @var float */
        $discountAmount = $priceRule->getDiscountAmount();

        // by fixed
        if ($discountType == 'by_fixed') {
            // discount amount
            if ($priceRule->getPriceMovement() != 1) {
                // convert to negative
                $discountAmount = -($discountAmount);
            }

            // generate query
            $select = $connection->select()->from(
                false,
                [
                    'listing_price' => new \Zend_Db_Expr(
                        $connection->quoteInto('index_table.listing_price + ?', $discountAmount)
                    ),
                    'apply_vat' => (int)1
                ]
            )->where(
                'index_table.stop_rules = ?',
                (int)2
            )->where(
                new \Zend_Db_Expr($connection->quoteInto('index_table.listing_price + ? > 0', $discountAmount))
            );

            $update = $connection->updateFromSelect(
                $select,
                ['index_table' => $this->resourceConnection->getTableName('channel_amazon_pricing_index')]
            );
            $connection->query($update);
        } else { // by percent
            // reduce by
            if ($priceRule->getPriceMovement() != 1) {
                $discountAmount = (1 - ($discountAmount / 100));
            } else {// add by
                $discountAmount = (1 + ($discountAmount / 100));
            }

            // generate query
            $select = $connection->select()->from(
                false,
                [
                    'listing_price' => new \Zend_Db_Expr(
                        $connection->quoteInto('listing_price * ?', $discountAmount)
                    ),
                    'apply_vat' => (int)1
                ]
            )->where(
                'index_table.stop_rules = ?',
                (int)2
            );

            $update = $connection->updateFromSelect(
                $select,
                ['index_table' => $this->resourceConnection->getTableName('channel_amazon_pricing_index')]
            );
            $connection->query($update);
        }
    }

    /**
     * Applies VAT Tax
     *
     * @param float $vatPercentage
     * @return void
     */
    private function applyVatTax($vatPercentage)
    {
        /** @var AdapterInterface */
        $connection = $this->connection;

        $bind = [
            'listing_price' => new \Zend_Db_Expr('listing_price + (listing_price * ' . ($vatPercentage / 100) . ')')
        ];

        $where = [
            'apply_vat = ?' => (int)1
        ];

        $connection->update($this->resourceConnection->getTableName('channel_amazon_pricing_index'), $bind, $where);
    }

    /**
     * Applies pricing overrides
     *
     * @param int $merchantId
     * @return void
     * @throws \Zend_Db_Select_Exception
     */
    private function applyPricingOverrides(int $merchantId)
    {
        /** @var AdapterInterface */
        $connection = $this->connection;

        // generate query
        $select = $connection->select()->from(
            false,
            [
                'listing_price' => 'listing.list_price_override'
            ]
        )->joinInner(
            ['listing' => $this->resourceConnection->getTableName('channel_amazon_listing')],
            'listing.id = index_table.parent_id',
            []
        )->where(
            'listing.merchant_id = ?',
            (int)$merchantId
        )->where(
            'listing.list_price_override > 0 AND listing.list_price_override IS NOT NULL'
        );

        $update = $connection->updateFromSelect(
            $select,
            ['index_table' => $this->resourceConnection->getTableName('channel_amazon_pricing_index')]
        );
        $connection->query($update);
    }

    /**
     * Applies landed price (listing price + shipping price)
     *
     * @param int $merchantId
     * @return void
     */
    private function applyLandedPrice(int $merchantId)
    {
        /** @var AdapterInterface */
        $connection = $this->connection;

        // verify landed price is greater than 0
        $bind = [
            'listing_price' => new \Zend_Db_Expr('catalog_price')
        ];

        $where = [
            'listing_price < ?' => 0.001
        ];

        $connection->update($this->resourceConnection->getTableName('channel_amazon_pricing_index'), $bind, $where);

        // update landed price
        $bind = [
            'landed_price' => new \Zend_Db_Expr('listing_price + shipping_price')
        ];

        $where = [
            'merchant_id = ?' => (int)$merchantId
        ];

        $connection->update($this->resourceConnection->getTableName('channel_amazon_pricing_index'), $bind, $where);
    }

    /**
     * Update listing table (with pricing and pricing update flag)
     *
     * @param int $merchantId
     * @return void
     * @throws \Zend_Db_Select_Exception
     */
    private function updateListingTable(int $merchantId)
    {
        /** @var AdapterInterface */
        $connection = $this->connection;

        // updates listing table
        $select = $connection->select()->from(
            false,
            [
                'list_price' => 'index_table.listing_price',
                'shipping_price' => 'index_table.shipping_price',
                'landed_price' => 'index_table.landed_price',
                'pricing_update' => new \Zend_Db_Expr($connection->quote('1'))
            ]
        )->joinInner(
            ['index_table' => $this->resourceConnection->getTableName('channel_amazon_pricing_index')],
            'listing.id = index_table.parent_id',
            []
        )->where(
            'listing.merchant_id = ?',
            $merchantId
        )->where(
            'index_table.listing_price != listing.list_price ' .
            'OR index_table.landed_price != listing.landed_price'
        )->where(
            'index_table.listing_price > 0'
        );

        $update = $connection->updateFromSelect(
            $select,
            ['listing' => $this->resourceConnection->getTableName('channel_amazon_listing')]
        );
        $connection->query($update);
    }

    /**
     * Update actions table (with pricing changes)
     *
     * @param int $merchantId
     * @return void
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Zend_Db_Select_Exception
     */
    private function updateActionsTable(int $merchantId)
    {
        /** @var AdapterInterface */
        $connection = $this->connection;
        $log = [];

        // update Amazon pricing
        $select = $connection->select()->from(
            ['listing' => $this->resourceConnection->getTableName('channel_amazon_listing')],
            []
        )->where(
            'listing.merchant_id = ?',
            $merchantId
        )->where(
            'listing.pricing_update = 1'
        )->columns(
            [
                'merchant_id' => new \Zend_Db_Expr($connection->quote($merchantId)),
                'id' => 'listing.id',
                'sku' => 'listing.seller_sku',
                'type' => new \Zend_Db_Expr($connection->quote('Pricing')),
                'action_type' => new \Zend_Db_Expr($connection->quote('Feeds')),
                'list_price' => 'listing.list_price'
            ]
        );

        $data = $connection->fetchAll($select);

        foreach ($data as $row) {
            $commandData = $this->prepareCommandData($row);
            /** @var UpdateProductPrice $command */
            $command = $this->updateProductPriceFactory->create(
                [
                    'body' => $commandData,
                    'identifier' => $row['id']
                ]
            );
            $this->commandDispatcher->dispatch($merchantId, $command);
            $log[] = [
                'merchant_id' => $row['merchant_id'],
                'seller_sku' => $row['sku'],
                'action' => __('Pricing'),
                'notes' => __('Updated to ' . $row['list_price'])
            ];
        }

        if (!empty($log)) {
            $this->logResourceModel->insert($log);
        }
    }

    /**
     * Clear updates flag
     *
     * @param int $merchantId
     * @return void
     */
    private function clearUpdatesFlag(int $merchantId)
    {
        /** @var AdapterInterface */
        $connection = $this->connection;

        // clear updates flag
        $bind = [
            'pricing_update' => (int)0
        ];

        $where = [
            'pricing_update = 1',
            $connection->quoteInto('merchant_id = ?', $merchantId)
        ];

        $connection->update($this->resourceConnection->getTableName('channel_amazon_listing'), $bind, $where);
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
     * Prepare data for command
     *
     * @param array $row
     * @return array
     */
    private function prepareCommandData(array $row): array
    {
        try {
            /** @var \Magento\Amazon\Api\Data\ListingInterface $listing */
            $listing = $this->listingRepository->getById($row['id']);
        } catch (NoSuchEntityException $e) {
            return [];
        }

        /** @var \Magento\Amazon\Api\Data\AccountListingInterface $accountListing */
        $accountListing = $this->accountListingRepository->getByMerchantId($row['merchant_id']);

        $price = $row['list_price'];
        $qtyPriceType = '';
        $businessPrice = '';
        $tierPrices = [];

        if ($accountListing->getBusinessIsActive() && ($price > self::MIN_VALUE)) {
            $businessPrice = $price;

            if ($accountListing->getTierIsActive()
                && $accountListing->getQtyPriceOne()
                && $accountListing->getLowerBoundOne()
            ) {
                $qtyPriceType = 'percent';
                $tierPrices = $this->getTierPrices($accountListing);
            }
        }

        return [
            'sku' => $listing->getSellerSku(),
            'standard_price' => $price,
            'map_price' => $listing->getMapPrice(),
            'business_price' => $businessPrice,
            'price_type' => $qtyPriceType,
            'tier_prices' => $tierPrices,
        ];
    }

    /**
     * Get list of tier prices for account listing
     *
     * @param AccountListingInterface $accountListing
     * @return array
     */
    private function getTierPrices(AccountListingInterface $accountListing): array
    {
        $tierPrices = [
            'qty_price_1' => $accountListing->getQtyPriceOne(),
            'qty_lower_bound_1' => $accountListing->getLowerBoundOne(),
        ];

        if ($accountListing->getQtyPriceTwo() && $accountListing->getLowerBoundTwo()) {
            $tierPrices['qty_price_2'] = $accountListing->getQtyPriceTwo();
            $tierPrices['qty_lower_bound_2'] = $accountListing->getLowerBoundTwo();
        }

        if ($accountListing->getQtyPriceThree() && $accountListing->getLowerBoundThree()) {
            $tierPrices['qty_price_3'] = $accountListing->getQtyPriceThree();
            $tierPrices['qty_lower_bound_3'] = $accountListing->getLowerBoundThree();
        }

        if ($accountListing->getQtyPriceFour() && $accountListing->getLowerBoundFour()) {
            $tierPrices['qty_price_4'] = $accountListing->getQtyPriceFour();
            $tierPrices['qty_lower_bound_4'] = $accountListing->getLowerBoundFour();
        }

        if ($accountListing->getQtyPriceFive() && $accountListing->getLowerBoundFive()) {
            $tierPrices['qty_price_5'] = $accountListing->getQtyPriceFive();
            $tierPrices['qty_lower_bound_5'] = $accountListing->getLowerBoundFive();
        }

        return $tierPrices;
    }
}
