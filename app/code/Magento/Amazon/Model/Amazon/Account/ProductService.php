<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\Amazon\Model\Amazon\Account;

use Magento\Amazon\Api\AccountListingRepositoryInterface;
use Magento\Amazon\Api\Data\AccountOrderInterface;
use Magento\Amazon\Api\Data\OrderInterface;
use Magento\Amazon\Api\ListingRepositoryInterface;
use Magento\Amazon\Api\ProductManagementInterface;
use Magento\Amazon\Model\CurrencyConversion;
use Magento\Amazon\Model\ResourceModel\Amazon\Order\Item\CollectionFactory;
use Magento\Amazon\Model\Stock\ConfigureStock;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\ProductFactory;
use Magento\CatalogInventory\Model\ResourceModel\Stock\Item as StockItem;
use Magento\CatalogInventory\Model\ResourceModel\Stock\ItemFactory;
use Magento\Framework\App\ProductMetadataInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\DataObject\Factory as ObjectFactory;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\EntityManager\MetadataPool;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Registry;
use Magento\Quote\Api\Data\CartInterface;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class ProductService
 */
class ProductService implements ProductManagementInterface
{
    /** @const string */
    const TYPE_ID = 'simple';
    /** @const float */
    const PRICE = 0.00;
    /** @const bool */
    const USE_CONFIG_MANAGE_STOCK = false;
    /** @const bool */
    const MANAGE_STOCK = false;
    /** @const bool */
    const IS_IN_STOCK = true;
    /** @const int */
    const QTY = 0;

    /** @var ListingRepositoryInterface $listingRepository */
    protected $listingRepository;
    /** @var CollectionFactory $orderItemCollectionFactory */
    protected $orderItemCollectionFactory;
    /** @var ProductRepositoryInterface $productRepository */
    protected $productRepository;
    /** @var ProductFactory $productFactory */
    protected $productFactory;
    /** @var AccountListingRepositoryInterface $accountListingRepository */
    protected $accountListingRepository;
    /** @var StoreManagerInterface $storeManager */
    protected $storeManager;
    /** @var CurrencyConversion $currency */
    protected $currency;
    /** @var Registry $registry */
    protected $registry;
    /** @var ResourceConnection $resourceConnection */
    protected $resourceConnection;
    /** @var ItemFactory $itemFactory */
    protected $itemFactory;
    /** @var MetadataPool $metadataPool */
    protected $metadataPool;
    /** @var ObjectFactory */
    protected $objectFactory;
    /** @var ProductMetadataInterface */
    protected $productMetadata;
    /** @var array */
    protected $newProducts;
    /** @var ConfigureStock */
    private $configureStock;

    /**
     * Constructor
     *
     * @param ListingRepositoryInterface $listingRepository
     * @param CollectionFactory $orderItemCollectionFactory
     * @param ProductRepositoryInterface $productRepository
     * @param ProductFactory $productFactory
     * @param AccountListingRepositoryInterface $accountListingRepository
     * @param StoreManagerInterface $storeManager
     * @param Registry $registry
     * @param ResourceConnection $resourceConnection
     * @param ItemFactory $itemFactory
     * @param MetadataPool $metadataPool
     * @param CurrencyConversion $currency
     * @param ObjectFactory $objectFactory
     * @param ProductMetadataInterface $productMetadataInteface
     * @param ConfigureStock $configureStock
     */
    public function __construct(
        ListingRepositoryInterface $listingRepository,
        CollectionFactory $orderItemCollectionFactory,
        ProductRepositoryInterface $productRepository,
        ProductFactory $productFactory,
        AccountListingRepositoryInterface $accountListingRepository,
        StoreManagerInterface $storeManager,
        Registry $registry,
        ResourceConnection $resourceConnection,
        ItemFactory $itemFactory,
        MetadataPool $metadataPool,
        CurrencyConversion $currency,
        ObjectFactory $objectFactory,
        ProductMetadataInterface $productMetadataInteface,
        ConfigureStock $configureStock
    ) {
        $this->listingRepository = $listingRepository;
        $this->orderItemCollectionFactory = $orderItemCollectionFactory;
        $this->productRepository = $productRepository;
        $this->productFactory = $productFactory;
        $this->accountListingRepository = $accountListingRepository;
        $this->storeManager = $storeManager;
        $this->registry = $registry;
        $this->resourceConnection = $resourceConnection;
        $this->itemFactory = $itemFactory;
        $this->metadataPool = $metadataPool;
        $this->currency = $currency;
        $this->objectFactory = $objectFactory;
        $this->productMetadata = $productMetadataInteface;
        $this->configureStock = $configureStock;
        $this->newProducts = [];
    }

    /**
     * Removed product from catalog
     *
     * @return void
     */
    public function removeProduct()
    {
        if (empty($this->newProducts)) {
            return;
        }

        $this->registry->unregister('isSecureArea');
        $this->registry->register('isSecureArea', true);

        foreach ($this->newProducts as $product) {
            try {
                $this->productRepository->delete($product);
            } catch (\Exception $e) {
                // already deleted, no action needed
                return;
            }
        }

        $this->newProducts = [];
    }

    /**
     * Check that url_keys are not assigned to other products in DB
     *
     *
     * @return string
     * @throws \Zend_Db_Select_Exception
     * @var string $urlKey
     */
    public function getProductUrlDuplicate(string $urlKey): string
    {
        /** @var AdapterInterface */
        $connection = $this->resourceConnection->getConnection();
        /** @var string */
        $tableName = $this->resourceConnection->getTableName('url_rewrite');

        // build query
        $select = $connection->select()->from(
            ['url' => $tableName],
            []
        )->where(
            'url.request_path = ?',
            $urlKey
        )->columns(
            [
                'entity_id' => 'url.entity_id'
            ]
        );

        try {
            /** @var array */
            $rows = $connection->fetchAssoc($select);
        } catch (\Exception $e) {
            return '_' . rand(1, 99);
        }

        if ($rows) {
            return $urlKey;
        }

        return '_' . rand(1, 99);
    }

    /**
     * Update and insert data in entity table.
     *
     * @param array $entities
     * @return array
     * @throws \Zend_Db_Select_Exception
     */
    public function saveProductEntity(array $entities): array
    {
        /** @var array */
        $products = [];
        /** @var AdapterInterface */
        $connection = $this->resourceConnection->getConnection();
        /** @var string */
        $tableName = $this->resourceConnection->getTableName('catalog_product_entity');

        if (!$entities) {
            return $products;
        }

        try {
            $connection->beginTransaction();
            $connection->insertMultiple($tableName, array_values($entities));
            $connection->commit();
        } catch (\Exception $e) {
            $connection->rollBack();
        }

        // build query
        $select = $connection->select()->from(
            ['cpe' => $tableName],
            []
        )->where(
            'cpe.sku IN (?)',
            array_keys($entities)
        )->columns(
            [
                'entity_id' => 'cpe.entity_id',
                'sku' => 'cpe.sku'
            ]
        )->group('cpe.entity_id');

        if ($rows = $connection->fetchAssoc($select)) {
            foreach ($rows as $row) {
                $products[$row['sku']] = $row['entity_id'];
            }
        }

        return $products;
    }

    /**
     * Update and insert website data.
     *
     * @param array $websiteData
     * @param array $products
     * @return void
     */
    public function saveWebsiteIds(array $websiteData, array $products)
    {
        /** @var AdapterInterface */
        $connection = $this->resourceConnection->getConnection();
        /** @var string $tableName */
        $tableName = $this->resourceConnection->getTableName('catalog_product_website');
        /** @var array */
        $websitesData = [];

        foreach ($websiteData as $sku => $websites) {
            if (isset($products[$sku])) {
                foreach (array_keys($websites) as $websiteId) {
                    $websitesData[] = ['product_id' => $products[$sku], 'website_id' => $websiteId];
                }
            }
        }

        if (empty($websitesData)) {
            return;
        }

        try {
            $connection->beginTransaction();
            $connection->insertOnDuplicate($tableName, $websitesData);
            $connection->commit();
        } catch (\Exception $e) {
            $connection->rollBack();
        }
    }

    /**
     * Update and insert category data.
     *
     * @param array $categories
     * @param array $products
     * @return void
     */
    public function saveProductCategories(array $categories, array $products)
    {
        /** @var AdapterInterface */
        $connection = $this->resourceConnection->getConnection();
        /** @var string */
        $tableName = $this->resourceConnection->getTableName('catalog_category_product');
        /** @var array */
        $categoriesData = [];

        foreach ($categories as $sku => $category) {
            if (!isset($products[$sku])) {
                continue;
            }

            foreach (array_keys($category) as $categoryId) {
                if ($categoryId) {
                    $categoriesData[] = [
                        'product_id' => $products[$sku],
                        'category_id' => $categoryId,
                        'position' => 1
                    ];
                }
            }
        }

        try {
            $connection->beginTransaction();
            $connection->insertOnDuplicate($tableName, $categoriesData);
            $connection->commit();
        } catch (\Exception $e) {
            $connection->rollBack();
        }
    }

    /**
     * Update and insert attribute data.
     *
     * @param array $attributesData
     * @return void
     * @throws \Zend_Db_Select_Exception
     * @throws \Exception
     */
    public function saveAttributes(array $attributesData)
    {
        /** @var AdapterInterface */
        $connection = $this->resourceConnection->getConnection();
        /** @var string */
        $entityTable = $this->resourceConnection->getTableName('catalog_product_entity');

        foreach ($attributesData as $data) {
            foreach ($data as $tableName => $skuData) {

                /** @var array */
                $tableData = [];

                foreach ($skuData as $sku => $attributes) {
                    $linkId = $connection->fetchOne(
                        $connection->select()
                            ->from($entityTable, [])
                            ->where('sku = ?', $sku)
                            ->columns($this->getProductEntityLinkField())
                    );

                    foreach ($attributes as $attributeId => $storeValues) {
                        foreach ($storeValues as $storeId => $storeValue) {
                            $tableData[] = [
                                $this->getProductEntityLinkField() => $linkId,
                                'attribute_id' => $attributeId,
                                'store_id' => $storeId,
                                'value' => $storeValue,
                            ];
                        }
                    }
                }

                try {
                    $connection->beginTransaction();
                    $connection->insertOnDuplicate($tableName, $tableData, ['value']);
                    $connection->commit();
                } catch (\Exception $e) {
                    $connection->rollBack();
                }
            }
        }
    }

    /**
     * Saves product stock data
     *
     * @param array $stockData
     * @param array $products
     * @return void
     * @throws LocalizedException
     */
    public function saveStockItems(array $stockData, array $products)
    {
        /** @var AdapterInterface */
        $connection = $this->resourceConnection->getConnection();
        /** @var StockItem */
        $stockResource = $this->itemFactory->create();
        /** @var string */
        $entityTable = $stockResource->getMainTable();

        foreach (array_keys($stockData) as $sku) {
            if (isset($products[$sku])) {
                $stockData[$sku]['product_id'] = $products[$sku];
            }
        }

        if (empty($stockData)) {
            return;
        }

        try {
            $connection->beginTransaction();
            $connection->insertOnDuplicate($entityTable, $stockData, []);
            $connection->commit();
        } catch (\Exception $e) {
            $connection->rollBack();
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
     * Get product for order
     * Creates new catalog product if not exists
     *
     * @param string $sku
     * @param int $websiteId
     * @return ProductInterface
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    private function getProduct(
        string $sku,
        int $websiteId
    ) {
        $product = $this->productRepository->get($sku);
        // play around MSI bug
        $this->configureStock->createSourceItemForProductNotManagingStock($sku, $websiteId);
        return $product;
    }

    /**
     * Add product to cart
     *
     * @param AccountOrderInterface $account
     * @param OrderInterface $order
     * @param CartInterface $cart
     * @return array | bool
     * @throws LocalizedException
     * @throws NoSuchEntityException
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Zend_Db_Select_Exception
     */
    public function addProductToCart(
        AccountOrderInterface $account,
        OrderInterface $order,
        CartInterface $cart
    ) {
        $orderId = (string)$order->getOrderId();
        $storeId = (int)$account->getDefaultStore();
        /** @var $items OrderItemInterface[] */
        $items = $this->orderItemCollectionFactory->create()->addFieldToFilter('order_id', $orderId);
        /** @var StoreManagerInterface */
        $store = $this->storeManager->getStore($storeId);
        $websiteId = (int)$store->getWebsiteId();
        $merchantId = (int)$account->getMerchantId();

        $orderItems = [];

        $accountListing = $this->accountListingRepository->getByMerchantId($merchantId);

        foreach ($items as $item) {
            /** @var int */
            $itemId = $item->getData('id');
            /** @var string */
            $orderItemId = $item->getOrderItemId();

            try {
                $sku = $this->listingRepository->getCatalogSkuBySellerSku($item->getSku(), $item->getMerchantId());
                $product = $this->getProduct($sku, $websiteId);
            } catch (NoSuchEntityException $exception) {
                throw new LocalizedException(__('Product "%1" does not exist', [$item->getSku()]), $exception);
            }

            /** @var int */
            $qtyOrdered = $item->getQtyOrdered() ?: 1;

            /** @var float */
            $unitPrice = $item->getItemPrice();
            $unitPrice = round((float)($unitPrice / $qtyOrdered), 2, PHP_ROUND_HALF_UP);

            /** @var string */
            $conversionIsActive = $accountListing->getCcIsActive();
            /** @var string */
            $conversionRate = $accountListing->getCcRate();
            /** @var float */
            $basePrice = ($conversionIsActive) ? $this->currency->convert($unitPrice, $conversionRate) : $unitPrice;

            /** @var string */
            $storeCurrencyCode = $store->getDefaultCurrencyCode();
            /** @var float */
            $unitPrice = $this->currency->convert($basePrice, null, $storeCurrencyCode);

            $product->setPrice($basePrice);
            $request = $this->objectFactory->create(['qty' => $qtyOrdered, 'custom_price' => $unitPrice]);

            try {
                $orderItems[$itemId] = $cart->addProduct($product, $request);
            } catch (\Exception $e) {
                throw new LocalizedException(__('Product "%1" in order is not salable', [$product->getSku()]), $e);
            }

            // configure order item data
            $orderItems[$itemId]->setData('description', $orderItemId);
        }

        return $orderItems;
    }
}
