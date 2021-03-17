<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Amazon\Controller\Adminhtml\Amazon\Account\Listing\Thirdparty\Create;

use Magento\Amazon\Api\AccountRepositoryInterface;
use Magento\Amazon\Api\Data\ListingInterface;
use Magento\Amazon\Api\ListingManagementInterface;
use Magento\Amazon\Api\ProductManagementInterface;
use Magento\Amazon\Model\ResourceModel\Amazon\Listing\CollectionFactory;
use Magento\Backend\App\Action;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Api\ProductAttributeRepositoryInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\CatalogInventory\Api\StockConfigurationInterface;
use Magento\CatalogInventory\Api\StockRegistryInterface;
use Magento\Directory\Model\Currency;
use Magento\Framework\App\ProductMetadataInterface;
use Magento\Framework\EntityManager\MetadataPool;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Stdlib\DateTime;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class Save
 */
class Save extends Action
{
    const MAX_RECORDS = 5000;

    /** @var AccountRepositoryInterface $accountRepository */
    protected $accountRepository;
    /** @var ListingManagementInterface $listingManagement */
    protected $listingManagement;
    /** @var ProductAttributeRepositoryInterface $productAttributeRepository */
    protected $productAttributeRepository;
    /** @var ProductManagementInterface $productManagement */
    protected $productManagement;
    /** @var ProductRepositoryInterface $productRepository */
    protected $productRepository;
    /** @var MetadataPool $metadataPool */
    protected $metadataPool;
    /** @var StockRegistryInterface $stockRegistry */
    protected $stockRegistry;
    /** @var StockConfigurationInterface $stockConfiguration */
    protected $stockConfiguration;
    /** @var StoreManagerInterface $storeManager */
    protected $storeManager;
    /** @var int $storeId */
    protected $storeId;
    /** @var ProductMetadataInterface $productMetadata */
    protected $productMetadata;
    /** @var Currency $currency */
    protected $currency;
    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    /**
     * @param Action\Context $context
     * @param AccountRepositoryInterface $accountRepository
     * @param ListingManagementInterface $listingManagement
     * @param ProductAttributeRepositoryInterface $productAttributeRepository
     * @param ProductManagementInterface $productManagement
     * @param ProductRepositoryInterface $productRepository
     * @param MetadataPool $metadataPool
     * @param StockRegistryInterface $stockRegistry
     * @param StockConfigurationInterface $stockConfiguration
     * @param StoreManagerInterface $storeManager
     * @param CollectionFactory $collectionFactory
     * @param ProductMetadataInterface $productMetadata
     * @param Currency $currency
     */
    public function __construct(
        Action\Context $context,
        AccountRepositoryInterface $accountRepository,
        ListingManagementInterface $listingManagement,
        ProductAttributeRepositoryInterface $productAttributeRepository,
        ProductManagementInterface $productManagement,
        ProductRepositoryInterface $productRepository,
        MetadataPool $metadataPool,
        StockRegistryInterface $stockRegistry,
        StockConfigurationInterface $stockConfiguration,
        StoreManagerInterface $storeManager,
        CollectionFactory $collectionFactory,
        ProductMetadataInterface $productMetadata,
        Currency $currency
    ) {
        parent::__construct($context);
        $this->accountRepository = $accountRepository;
        $this->listingManagement = $listingManagement;
        $this->productAttributeRepository = $productAttributeRepository;
        $this->productManagement = $productManagement;
        $this->productRepository = $productRepository;
        $this->metadataPool = $metadataPool;
        $this->stockRegistry = $stockRegistry;
        $this->stockConfiguration = $stockConfiguration;
        $this->storeManager = $storeManager;
        $this->collectionFactory = $collectionFactory;
        $this->productMetadata = $productMetadata;
        $this->currency = $currency;
        $this->storeId = $this->storeManager->getDefaultStoreView()->getId();
    }

    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magento_SalesChannels::channel_amazon');
    }

    /**
     * Generates new Magento catalog products based on user selection
     *
     * Handles both the mass action and single select options
     *
     * @return Redirect
     * @throws \Exception
     */
    public function execute()
    {
        /** @var Redirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        /** @var int */
        $merchantId = $this->getRequest()->getParam('merchant_id');
        /** @var array */
        $entities = [];
        /** @var array */
        $websites = [];
        /** @var array */
        $attributes = [];
        /** @var array */
        $websiteIds = $this->getWebsites();
        /** @var array */
        $categoryIds = $this->getCategories();
        /** @var array */
        $categories = [];
        /** @var array */
        $stockData = [];
        /** @var int */
        $count = 0;
        /** @var array */
        $ids = [];

        // single select action
        if ($this->getRequest()->getParam('listing_id')) {
            $ids = [$this->getRequest()->getParam('listing_id')];
        }

        // mass action
        if ($this->getRequest()->getParam('selected_ids')) {
            $ids = json_decode($this->getRequest()->getParam('selected_ids'));
        }

        if (empty($ids)) {
            $this->messageManager
                ->addErrorMessage(__('Please select items.'));
            return $resultRedirect->setPath(
                'channel/amazon/account_listing_index',
                ['merchant_id' => $merchantId, 'active_tab' => "listing_view_thirdparty"]
            );
        }

        /** ListingCollection */
        $collection = $this->collectionFactory->create();
        $collection->addFieldToFilter('id', ['in' => $ids]);
        $collection->getSelect()->limit(self::MAX_RECORDS);

        foreach ($collection as $listing) {

            /** @var string */
            $sku = $listing->getSellerSku();

            try {
                $this->productRepository->get($sku);
                continue;
            } catch (NoSuchEntityException $e) {
                // proceed with catalog product build
            }

            // build import data
            $entities[$sku] = $this->getEntities($sku);
            $websites[$sku] = $websiteIds;
            $categories[$sku] = $categoryIds;
            $stockData[$sku] = $this->getStockData($listing->getQty());
            $attributes[$sku] = $this->getAttributes($listing, $sku);

            // increment counter
            $count++;
        }

        // if product exists
        if (!empty($entities)) {
            // build product entities
            $products = $this->productManagement->saveProductEntity($entities);

            // if successful product build(s)
            if (!empty($products)) {
                // build supporting product attributes
                $this->productManagement->saveWebsiteIds($websites, $products);
                $this->productManagement->saveProductCategories($categories, $products);
                $this->productManagement->saveAttributes($attributes);
                $this->productManagement->saveStockItems($stockData, $products);
                // process unmatched listing
                $this->listingManagement->insertUnmatchedListing($merchantId);
            }
        }

        // hit maximum number of records per attempt
        if ($count == self::MAX_RECORDS) {
            $this->messageManager
                ->addWarningMessage(__('A maximum of ' . self::MAX_RECORDS . ' products can be created per attempt.'));
        }

        $this->messageManager
            ->addSuccessMessage(__('Successfully built (' . $count . ') new Magento products.'));
        return $resultRedirect->setPath(
            'channel/amazon/account_listing_index',
            ['merchant_id' => $merchantId, 'active_tab' => "listing_view_thirdparty"]
        );
    }

    /**
     * Get entities data
     *
     * @param string $sku
     * @return array
     * @throws \Exception
     */
    private function getEntities($sku)
    {
        /** @var MetadataPool */
        $metadata = $this->getMetadata();
        /** @var int */
        $attributeSetId = $this->getRequest()->getParam('attribute_set_id');
        /** @var \DateTime */
        $timestamp = (new \DateTime())->format(DateTime::DATETIME_PHP_FORMAT);
        /** @var int */
        $id = $metadata->generateIdentifier();

        /** @var array */
        $entities = [
            'attribute_set_id' => $attributeSetId,
            'type_id' => 'simple',
            'sku' => $sku,
            'has_options' => 0,
            'created_at' => $timestamp,
            'updated_at' => $timestamp,
            $metadata->getIdentifierField() => $id
        ];

        // Enterprise only functionality
        if ($this->productMetadata->getEdition() === 'Enterprise'
            && class_exists(\Magento\Staging\Model\VersionManager::class)
        ) {
            $entities['created_in'] = 1;
            $entities['updated_in'] = \Magento\Staging\Model\VersionManager::MAX_VERSION;
        }

        return $entities;
    }

    /**
     * Get websites data
     *
     * @return array
     */
    private function getWebsites()
    {
        /** @var array */
        $websites = [];
        /** @var array */
        $websiteIds = $this->getRequest()->getParam('website_ids');
        $websiteIds = (is_array($websiteIds)) ? $websiteIds : [$websiteIds];

        foreach ($websiteIds as $id) {
            $websites[$id] = true;
        }

        return $websites;
    }

    /**
     * Get categories data
     *
     * @return array
     */
    private function getCategories()
    {
        /** @var array */
        $categories = [];
        /** @var array */
        $categoryIds = $this->getRequest()->getParam('category_ids');

        if (empty($categoryIds)) {
            return $categories;
        }

        $categoryIds = (is_array($categoryIds)) ? $categoryIds : [$categoryIds];

        if (empty($categoryIds)) {
            return $categories;
        }

        foreach ($categoryIds as $id) {
            $categories[$id] = true;
        }

        return $categories;
    }

    /**
     * Get product stock data
     *
     * @param int $qty
     * @return array
     */
    private function getStockData($qty)
    {
        /** @var array */
        return [
            'website_id' => $this->stockConfiguration->getDefaultScopeId(),
            'stock_id' => $this->stockRegistry->getStock($this->stockConfiguration->getDefaultScopeId())->getStockId(),
            'manage_stock' => 1,
            'qty' => $qty,
            'max_sale_qty' => 10000,
            'is_in_stock' => ($qty) ? 1 : 0
        ];
    }

    /**
     * Get user defined product attributes
     *
     * @param ListingInterface $listing
     * @param string $sku
     * @return array
     */
    private function getAttributes(ListingInterface $listing, $sku)
    {
        /** @var array */
        $attributes = [];
        /** @var string */
        $status = $this->getRequest()->getParam('product_status');
        $status = ($status) ? Status::STATUS_ENABLED : Status::STATUS_DISABLED;
        /** @var string */
        $visibility = $this->getRequest()->getParam('visibility');
        /** @var string */
        $name = ($name = $listing->getName()) ? $name : $sku;
        /** @var string $urlKey */
        $urlKey = preg_replace('#[^0-9a-z]+#i', '-', $name);
        /** @var float */
        $currencyRate = $this->getCurrencyRate();
        /** @var float */
        $price = $listing->getListPrice();
        $price = ($price) ? round(($price / $currencyRate), 2, PHP_ROUND_HALF_UP) : '99999.99';
        /** @var int */
        $taxClassId = $this->getRequest()->getParam('tax_class_id');

        $urlKey = (strlen($urlKey) > 241) ? substr($urlKey, 0, 241) : $urlKey;
        $urlKey = $urlKey . '-' . $listing->getId();

        $attributes = $this->getAttribute($attributes, 'status', $status, $sku);
        $attributes = $this->getAttribute($attributes, 'visibility', $visibility, $sku);
        $attributes = $this->getAttribute($attributes, 'name', $name, $sku);
        $attributes = $this->getAttribute($attributes, 'url_key', $urlKey, $sku);
        $attributes = $this->getAttribute($attributes, 'price', $price, $sku);
        $attributes = $this->getAttribute($attributes, 'tax_class_id', $taxClassId, $sku);

        return $attributes;
    }

    /**
     * Build individual user defined attribute values
     *
     * @param array $attributes
     * @param string $attributeCode
     * @param string $attributeValue
     * @param string $sku
     * @return array
     * @throws NoSuchEntityException
     */
    private function getAttribute(array $attributes, $attributeCode, $attributeValue, $sku)
    {
        // build attribute data
        $attribute = $this->productAttributeRepository->get($attributeCode);
        $attrId = $attribute->getId();
        $attrTable = $attribute->getBackend()->getTable();
        $attributes[$attrTable][$sku][$attrId][$this->storeId] = $attributeValue;
        $attributes[$attrTable][$sku][$attrId][0] = $attributeValue;

        return $attributes;
    }

    /**
     * Get currency conversion rate (if applicable)
     *
     * @return float
     * @throws NoSuchEntityException
     */
    private function getCurrencyRate(): float
    {
        /** @var int */
        $merchantId = $this->getRequest()->getParam('merchant_id');
        /** @var float */
        $currencyRate = 1.0000;

        try {
            $account = $this->accountRepository->getByMerchantId($merchantId);
        } catch (NoSuchEntityException $e) {
            // no conversion
            return $currencyRate;
        }

        // if conversion is active
        if (!$account->getCcIsActive()) {
            // no conversion
            return $currencyRate;
        }

        // get currency conversion options
        $store = $this->storeManager->getStore();
        $codes = $store->getAvailableCurrencyCodes(true);
        $base = $store->getBaseCurrency();
        $rates = $this->currency->getCurrencyRates($base, $codes);

        foreach ($rates as $symbol => $rate) {
            // if same as base skip
            if ($symbol == $account->getCcRate()) {
                // prevent division by 0
                if ($rate > 0) {
                    $currencyRate = round($rate, 6, PHP_ROUND_HALF_UP);
                    break;
                }
            }
        }

        // return base rate
        return $currencyRate;
    }

    /**
     * Get product metadata
     *
     * return MetadataPool
     */
    private function getMetadata()
    {
        return $this->metadataPool->getMetadata(ProductInterface::class);
    }
}
