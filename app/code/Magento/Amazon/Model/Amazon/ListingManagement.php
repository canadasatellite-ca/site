<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Amazon\Model\Amazon;

use Magento\Amazon\Api\AccountListingRepositoryInterface;
use Magento\Amazon\Api\AccountRepositoryInterface;
use Magento\Amazon\Api\Data\AccountInterface;
use Magento\Amazon\Api\Data\AccountListingInterface;
use Magento\Amazon\Api\Data\ListingInterface;
use Magento\Amazon\Api\ListingManagementInterface;
use Magento\Amazon\Api\ListingRepositoryInterface;
use Magento\Amazon\Domain\Command\CommandDispatcher;
use Magento\Amazon\Domain\Command\UpdateListingEligibility;
use Magento\Amazon\Domain\Command\UpdateListingEligibilityFactory;
use Magento\Amazon\Model\ResourceModel\Amazon\Listing as ResourceModel;
use Magento\Amazon\Model\ResourceModel\Amazon\Listing\CollectionFactory;
use Magento\Amazon\Model\ResourceModel\Amazon\Listing\Log as LogResourceModel;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductCollectionFactory;
use Magento\Eav\Api\AttributeRepositoryInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Model\ResourceModel\Iterator as CollectionIterator;

/**
 * Class ListingManagement
 */
class ListingManagement implements ListingManagementInterface
{
    /** form field flags for fulfillment type */
    const DEFAULT_SHIPPING = "1";
    const AMAZON_SHIPPING = "2";

    /** max rows to be processed per query */
    const CHUNK_SIZE = 2048;
    /** max string size for Amazon sku */
    const MAX_STRING_SIZE = 36;

    /** @var ListingRepositoryInterface $listingRepository */
    protected $listingRepository;
    /** @var ResourceModel $resourceModel */
    protected $resourceModel;
    /** @var CollectionFactory $collectionFactory */
    protected $collectionFactory;
    /** @var AccountRepositoryInterface $accountRepository */
    protected $accountRepository;
    /** @var AccountListingRepositoryInterface $accountListingRepository */
    protected $accountListingRepository;
    /** @var AttributeRepositoryInterface $attributeRepository */
    protected $attributeRepository;
    /** @var ProductRepositoryInterface $productRepository */
    protected $productRepository;
    /** @var LogResourceModel $logResourceModel */
    protected $logResourceModel;
    /** @var ProductCollectionFactory $productCollectionFactory */
    protected $productCollectionFactory;
    /**
     * @var CommandDispatcher
     */
    private $commandDispatcher;

    /**
     * @var UpdateListingEligibilityFactory
     */
    private $updateListingEligibilityFactory;

    /** @var CollectionIterator $collectionIterator */
    private $collectionIterator;

    /**
     * @param ListingRepositoryInterface $listingRepository
     * @param ResourceModel $resourceModel
     * @param CollectionFactory $collectionFactory
     * @param AccountRepositoryInterface $accountRepository
     * @param AccountListingRepositoryInterface $accountListingRepository
     * @param AttributeRepositoryInterface $attributeRepository
     * @param ProductRepositoryInterface $productRepository
     * @param LogResourceModel $logResourceModel
     * @param CommandDispatcher $commandDispatcher
     * @param UpdateListingEligibilityFactory $updateListingEligibilityFactory
     * @param ProductCollectionFactory $productCollectionFactory
     * @param CollectionIterator $collectionIterator
     */
    public function __construct(
        ListingRepositoryInterface $listingRepository,
        ResourceModel $resourceModel,
        CollectionFactory $collectionFactory,
        AccountRepositoryInterface $accountRepository,
        AccountListingRepositoryInterface $accountListingRepository,
        AttributeRepositoryInterface $attributeRepository,
        ProductRepositoryInterface $productRepository,
        LogResourceModel $logResourceModel,
        CommandDispatcher $commandDispatcher,
        UpdateListingEligibilityFactory $updateListingEligibilityFactory,
        ProductCollectionFactory $productCollectionFactory,
        CollectionIterator $collectionIterator
    ) {
        $this->listingRepository = $listingRepository;
        $this->resourceModel = $resourceModel;
        $this->collectionFactory = $collectionFactory;
        $this->accountRepository = $accountRepository;
        $this->accountListingRepository = $accountListingRepository;
        $this->attributeRepository = $attributeRepository;
        $this->productRepository = $productRepository;
        $this->attributeRepository = $attributeRepository;
        $this->logResourceModel = $logResourceModel;
        $this->commandDispatcher = $commandDispatcher;
        $this->updateListingEligibilityFactory = $updateListingEligibilityFactory;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->collectionIterator = $collectionIterator;
    }

    /**
     * {@inheritdoc}
     */
    public function getCountByListStatus(array $statuses, $merchantId)
    {
        /** @var CollectionFactory */
        $collection = $this->collectionFactory->create()->addFieldToFilter('merchant_id', $merchantId);
        $collection->addFieldToFilter('list_status', ['in' => $statuses]);

        return ($records = $collection->getSize()) ? $records : 0;
    }

    /**
     * {@inheritdoc}
     */
    public function isSubNewConditionListing($merchantId)
    {
        /** @var AccountInterface */
        $account = $this->accountListingRepository->getByMerchantId($merchantId);

        // if listings condition setting is non new
        if ($account->getListingCondition() != Definitions::NEW_CONDITION_CODE) {
            return true;
        }

        /** @var CollectionFactory */
        $collection = $this->collectionFactory->create()->addFieldToFilter(
            ['condition'],
            [
                ['neq' => Definitions::NEW_CONDITION_CODE]
            ]
        );

        $collection->addFieldToFilter('merchant_id', $merchantId);

        return ($records = $collection->getSize());
    }

    /**
     * {@inheritdoc}
     */
    public function getCountByOverrides($merchantId)
    {
        /** @var CollectionFactory */
        $collection = $this->collectionFactory->create()->addFieldToFilter(
            [
                'condition_override',
                'list_price_override',
                'handling_override',
                'condition_notes_override'
            ],
            [
                ['neq' => '0'],
                ['neq' => 'NULL'],
                ['neq' => 'NULL'],
                ['neq' => 'NULL']
            ]
        );

        $collection->addFieldToFilter('merchant_id', $merchantId);

        return ($records = $collection->getSize()) ? $records : 0;
    }

    /**
     * {@inheritdoc}
     */
    public function setPriceOverride($id, $override = null)
    {
        try {
            /** @var ListingInterface */
            $listing = $this->listingRepository->getById($id);
        } catch (NoSuchEntityException $e) {
            return;
        }

        $listing->setListPriceOverride($override);
        $this->resourceModel->save($listing);

        // reindex row
        //$this->pricingIndexer->executeRow($listing->getCatalogProductId());
    }

    /**
     * {@inheritdoc}
     */
    public function isCatalogMatch(AccountListingInterface $account, ProductInterface $product)
    {
        /** @var string */
        $asinField = $account->getAsinMappingField();

        try {
            $this->attributeRepository->get('catalog_product', $asinField);
        } catch (NoSuchEntityException $e) {
            $this->clearAccountField($account, 'asin_mapping_field');
            $asinField = false;
        }

        /** @var string */
        $upcField = $account->getUpcMappingField();

        try {
            $this->attributeRepository->get('catalog_product', $upcField);
        } catch (NoSuchEntityException $e) {
            $this->clearAccountField($account, 'upc_mapping_field');
            $upcField = false;
        }

        /** @var string */
        $isbnField = $account->getIsbnMappingField();

        try {
            $this->attributeRepository->get('catalog_product', $isbnField);
        } catch (NoSuchEntityException $e) {
            $this->clearAccountField($account, 'isbn_mapping_field');
            $isbnField = false;
        }

        /** @var string */
        $gcidField = $account->getGcidMappingField();

        try {
            $this->attributeRepository->get('catalog_product', $gcidField);
        } catch (NoSuchEntityException $e) {
            $this->clearAccountField($account, 'gcid_mapping_field');
            $gcidField = false;
        }

        /** @var string */
        $eanField = $account->getEanMappingField();

        try {
            $this->attributeRepository->get('catalog_product', $eanField);
        } catch (NoSuchEntityException $e) {
            $this->clearAccountField($account, 'ean_mapping_field');
            $eanField = false;
        }

        /** @var string */
        $generalField = $account->getGeneralMappingField();

        try {
            $this->attributeRepository->get('catalog_product', $generalField);
        } catch (NoSuchEntityException $e) {
            $this->clearAccountField($account, 'general_mapping_field', $generalField);
            $generalField = 'name';
        }

        // lookup by asin
        if ($asinField && $asinField != 'not_selected') {
            if ($value = $product->getData($asinField)) {
                /** @var array */
                return [
                    'product_id' => $value,
                    'product_type' => Definitions::ASIN_ATTRIBUTE
                ];
            }
        }

        // lookup by upc
        if ($upcField && $upcField != 'not_selected') {
            if ($value = $product->getData($upcField)) {
                /** @var array */
                return [
                    'product_id' => $value,
                    'product_type' => Definitions::UPC_ATTRIBUTE
                ];
            }
        }

        // lookup by isbn
        if ($isbnField && $isbnField != 'not_selected') {
            if ($value = $product->getData($isbnField)) {
                /** @var array */
                return [
                    'product_id' => $value,
                    'product_type' => Definitions::ISBN_ATTRIBUTE
                ];
            }
        }

        // lookup by gcid
        if ($gcidField && $gcidField != 'not_selected') {
            if ($value = $product->getData($gcidField)) {
                /** @var array */
                return [
                    'product_id' => $value,
                    'product_type' => Definitions::GCID_ATTRIBUTE
                ];
            }
        }

        // lookup by ean
        if ($eanField && $eanField != 'not_selected') {
            if ($value = $product->getData($eanField)) {
                /** @var array */
                return [
                    'product_id' => $value,
                    'product_type' => Definitions::EAN_ATTRIBUTE
                ];
            }
        }

        // lookup by general search
        if ($generalField && $generalField != 'not_selected') {
            if ($value = $product->getData($generalField)) {
                /** @var array */
                return [
                    'product_id' => $value,
                    'product_type' => Definitions::GENERAL_ATTRIBUTE
                ];
            }
        }

        return false;
    }

    /**
     * Clears an attribute mapping if not longer exists
     *
     * @param AccountListingInterface $account
     * @param string $field
     * @return void
     */
    private function clearAccountField(AccountListingInterface $account, $field, $value = null)
    {
        $account->setData($field, $value);

        try {
            $this->accountListingRepository->save($account);
        } catch (CouldNotSaveException $e) {
            // ignore
            return;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function insertUnmatchedListing($merchantId, array $ids = [])
    {
        /** @var int */
        $count = 0;

        try {
            /** @var AccountInterface */
            $account = $this->accountListingRepository->getByMerchantId($merchantId);
        } catch (NoSuchEntityException $e) {
            return $count;
        }

        /** @var string */
        $sku = $account->getThirdpartySkuField();

        try {
            $this->attributeRepository->get('catalog_product', $sku);
        } catch (NoSuchEntityException $e) {
            $this->clearAccountField($account, 'thirdparty_sku_field');
            $sku = false;
        }

        /** @var string */
        $asin = $account->getThirdpartyAsinField();

        try {
            $this->attributeRepository->get('catalog_product', $asin);
        } catch (NoSuchEntityException $e) {
            $this->clearAccountField($account, 'thirdparty_asin_field');
            $asin = false;
        }

        // match by sku
        if ($sku === 'sku') {
            if ($data = $this->resourceModel->findProductByAmazonSku($account, $ids)) {
                $count += $this->processMatch($data);
            }
        } elseif ($sku) {
            // match by seller sku
            if ($data = $this->resourceModel->findProductByAmazonAttribute($account, $sku, 'seller_sku', $ids)) {
                $count += $this->processMatch($data);
            }
        }

        // match by asin
        if ($asin) {
            if ($data = $this->resourceModel->findProductByAmazonAttribute($account, $asin, 'asin', $ids)) {
                $count += $this->processMatch($data);
            }
        }

        return $count;
    }

    /**
     * Handles newly matched Amazon listing => Magento product
     *
     * @param array $data
     *
     * @return int
     */
    private function processMatch(array $data)
    {
        /** @var array $parsedData */
        $this->parseMatchData($data);

        return count($data);
    }

    /**
     * Parses listing match data
     *
     * @param array $data
     * @return void
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function parseMatchData(array $data)
    {
        foreach ($data as $row) {
            // format data
            $id = (isset($row['id'])) ? $row['id'] : '';
            $asin = (isset($row['asin'])) ? $row['asin'] : '';
            $listingId = (isset($row['listing_id'])) ? $row['listing_id'] : '';
            $condition = (isset($row['condition'])) ? $row['condition'] : '';
            $existingId = (isset($row['existing_listing_id'])) ? $row['existing_listing_id'] : '';
            $listPrice = (isset($row['list_price'])) ? $row['list_price'] : '';
            $qty = (isset($row['qty'])) ? $row['qty'] : '';
            $sellerSku = (isset($row['seller_sku'])) ? $row['seller_sku'] : '';
            $sku = (isset($row['catalog_sku'])) ? $row['catalog_sku'] : '';
            $catalogProductId = (isset($row['catalog_product_id'])) ? $row['catalog_product_id'] : '';

            try {
                /** @var ListingInterface */
                $listing = $this->listingRepository->getById($id);
            } catch (NoSuchEntityException $e) {
                continue;
            }

            if ($existingId) {
                $this->resourceModel->deleteListings([$id]);

                // update listing data
                $listing->setId($existingId);
                $listing->setAsin($asin);
                $listing->setCondition($condition);
                $listing->setListingId($listingId);
                $listing->setSellerSku($sellerSku);
                $listing->setListPrice($listPrice);
                $listing->setQty($qty);
                $listing->setListStatus(Definitions::ACTIVE_LIST_STATUS);

                $this->resourceModel->assignThirdparty($listing);
                continue;
            }

            // update listing data
            $listing->setId($id);
            $listing->setCatalogSku($sku);
            $listing->setCatalogProductId($catalogProductId);
            $listing->setListStatus(Definitions::ACTIVE_LIST_STATUS);

            $this->resourceModel->assign($listing);
        }
    }

    /**
     * @param AccountListingInterface $accountListing
     * @return string
     */
    private function getProductTaxCode(AccountListingInterface $accountListing): string
    {
        $productTaxCode = '';

        if ($accountListing->getManagePtc()) {
            $productTaxCode = $accountListing->getDefaultPtc() ?: 'A_GEN_NOTAX';
        }

        return $productTaxCode;
    }

    /**
     * {@inheritdoc}
     */
    public function insertByProductIds(array $ids, $merchantId)
    {
        try {
            /** @var AccountInterface */
            $account = $this->accountRepository->getByMerchantId($merchantId);
        } catch (NoSuchEntityException $e) {
            return;
        }

        try {
            /** @var AccountListingInterface */
            $accountListing = $this->accountListingRepository->getByMerchantId($merchantId);
        } catch (NoSuchEntityException $e) {
            return;
        }

        $listings = [];

        $attributesAddToSelect = [];

        $countryCode = $account->getCountryCode();
        $productTaxCode = $this->getProductTaxCode($accountListing);

        //get listing settings for fulfillment
        $userDefinedFBAAttributeVal = $accountListing->getFulfilledByAmazon();
        $amazonFulfillmentCode = Definitions::getFulfillmentCode($countryCode, 'AMAZON_NA');
        $fulfilledByDd = $accountListing->getFulfilledBy();

        $fulfilledByCode = 'DEFAULT';
        $userDefinedFulfilledByAttribute = null;
        $isUsingAttributeForFulfillment = false;
        if ($fulfilledByDd === self::AMAZON_SHIPPING) {
            $fulfilledByCode = $amazonFulfillmentCode;
        } elseif ($fulfilledByDd === self::DEFAULT_SHIPPING) {
            $fulfilledByCode = 'DEFAULT';
        } else { //using user defined attribute to represent fulfillment
            $userDefinedFulfilledByAttribute = $accountListing->getFulfilledByField();
            try { //handle if attribute no longer exists
                $this->attributeRepository->get('catalog_product', $userDefinedFulfilledByAttribute);
                $attributesAddToSelect[] = $userDefinedFulfilledByAttribute;
                $isUsingAttributeForFulfillment = true;
            } catch (NoSuchEntityException $e) {
                $this->clearAccountField($accountListing, 'fulfilled_by_field');
                $fulfilledByCode = 'DEFAULT';
            }
        }

        //get listing settings for condition
        $stdDefaultConditionCode = $accountListing->getListCondition();
        $userDefinedListConditionAttribute = null;
        $isUsingAttributeForCondition = false;
        if (!$stdDefaultConditionCode) {
            $userDefinedListConditionAttribute = $accountListing->getListConditionField();
            try {
                $this->attributeRepository->get('catalog_product', $userDefinedListConditionAttribute);
                $attributesAddToSelect[] = $userDefinedListConditionAttribute;
                $isUsingAttributeForCondition = true;
            } catch (NoSuchEntityException $e) {
                $this->clearAccountField($accountListing, 'list_condition_field');
                $stdDefaultConditionCode = false;
            }
        }

        //get attr cfg to represent 'asin search', set $attributeRepresentingAsin = false if ndef
        $attributeRepresentingAsin = $accountListing->getAsinMappingField();
        try {
            $this->attributeRepository->get('catalog_product', $attributeRepresentingAsin);
            $attributesAddToSelect[] = $attributeRepresentingAsin;
        } catch (NoSuchEntityException $e) {
            $this->clearAccountField($accountListing, 'asin_mapping_field');
            $attributeRepresentingAsin = false;
        }

        //get attr cfg to represent 'upc search', set $attributeRepresentingUpc = false if ndef
        $attributeRepresentingUpc = $accountListing->getUpcMappingField();
        try {
            $this->attributeRepository->get('catalog_product', $attributeRepresentingUpc);
            $attributesAddToSelect[] = $attributeRepresentingUpc;
        } catch (NoSuchEntityException $e) {
            $this->clearAccountField($accountListing, 'upc_mapping_field');
            $attributeRepresentingUpc = false;
        }

        //get attr cfg to represent 'isbn search', set $attributeRepresentingIsbn = false if ndef
        $attributeRepresentingIsbn = $accountListing->getIsbnMappingField();
        try {
            $this->attributeRepository->get('catalog_product', $attributeRepresentingIsbn);
            $attributesAddToSelect[] = $attributeRepresentingIsbn;
        } catch (NoSuchEntityException $e) {
            $this->clearAccountField($accountListing, 'isbn_mapping_field');
            $attributeRepresentingIsbn = false;
        }

        //get attr cfg to represent 'gcid search', set $attributeRepresentingGcid = false if ndef
        $attributeRepresentingGcid = $accountListing->getGcidMappingField();
        try {
            $this->attributeRepository->get('catalog_product', $attributeRepresentingGcid);
            $attributesAddToSelect[] = $attributeRepresentingGcid;
        } catch (NoSuchEntityException $e) {
            $this->clearAccountField($accountListing, 'gcid_mapping_field');
            $attributeRepresentingGcid = false;
        }

        //get attr cfg to represent 'ean search', set $attributeRepresentingEan = false if ndef
        $attributeRepresentingEan = $accountListing->getEanMappingField();
        try {
            $this->attributeRepository->get('catalog_product', $attributeRepresentingEan);
            $attributesAddToSelect[] = $attributeRepresentingEan;
        } catch (NoSuchEntityException $e) {
            $this->clearAccountField($accountListing, 'ean_mapping_field');
            $attributeRepresentingEan = false;
        }

        //get attr cfg to represent 'general search', set $attributeRepresentingGeneral = false if ndef
        $attributeRepresentingGeneral = $accountListing->getGeneralMappingField();
        try {
            $this->attributeRepository->get('catalog_product', $attributeRepresentingGeneral);
            $attributesAddToSelect[] = $attributeRepresentingGeneral;
        } catch (NoSuchEntityException $e) {
            $this->clearAccountField($accountListing, 'general_mapping_field', $attributeRepresentingGeneral);
            $attributeRepresentingGeneral = 'name';
        }

        //iterator callback fcn
        /**
         * @param $args
         */
        $callback = function ($args) use (&$listings) {
            $id = $args['row']['entity_id'];
            $listings[$id] = $this->callbackPerListingHelper($args, $id);
        };
        //operate on product ids in chunks
        $productIdChunks = array_chunk($ids, self::CHUNK_SIZE);

        foreach ($productIdChunks as $productIdChunk) {
            $listings = [];

            $productCollection = $this->productCollectionFactory->create()
                ->addFieldToFilter('entity_id', ['in' => $productIdChunk])
                ->addFieldToFilter('required_options', ['eq' => 0])//ASC doesn't support bundled products
                ->addFieldToFilter('type_id', [
                    ['eq' => \Magento\Catalog\Model\Product\Type::TYPE_SIMPLE],
                    ['eq' => \Magento\Catalog\Model\Product\Type::TYPE_VIRTUAL]
                ])
                ->addFieldToFilter('name', ['like' => '%']);

            if (!empty($attributesAddToSelect)) {
                $productCollection->addAttributeToSelect($attributesAddToSelect, 'left'); //left outer
            }

            //use Magento iterator for speed
            $this->collectionIterator->walk(
                $productCollection->getSelect(),
                [$callback],
                [
                    'account' => $account,
                    'merchantId' => $merchantId,
                    'userDefinedFulfilledByAttribute' => $userDefinedFulfilledByAttribute,
                    'fulfilledByCode' => $fulfilledByCode,
                    'userDefinedFBAAttributeVal' => $userDefinedFBAAttributeVal,
                    'amazonFulfillmentCode' => $amazonFulfillmentCode,
                    'isUsingAttributeForFulfillment' => $isUsingAttributeForFulfillment,
                    'userDefinedListConditionAttribute' => $userDefinedListConditionAttribute,
                    'stdDefaultConditionCode' => $stdDefaultConditionCode,
                    'accountListing' => $accountListing,
                    'countryCode' => $countryCode,
                    'productTaxCode' => $productTaxCode,
                    'attributeRepresentingAsin' => $attributeRepresentingAsin,
                    'attributeRepresentingUpc' => $attributeRepresentingUpc,
                    'attributeRepresentingIsbn' => $attributeRepresentingIsbn,
                    'attributeRepresentingGcid' => $attributeRepresentingGcid,
                    'attributeRepresentingEan' => $attributeRepresentingEan,
                    'attributeRepresentingGeneral' => $attributeRepresentingGeneral,
                    'isUsingAttributeForCondition' => $isUsingAttributeForCondition
                ]
            );

            // insert new listings for this chunk
            if (!empty($listings)) {
                $this->resourceModel->insertByProductIds($listings);
            }
        }
    }

    private function callbackPerListingHelper($args, $id): array
    {
        $productData = $args['row'];

        //merchant_id, catalog_product_id, name
        $listing['catalog_product_id'] = $id;
        $listing['merchant_id'] = $args['merchantId'];
        $listing['name'] = $productData['name'];
        $listing['product_tax_code'] = $args['productTaxCode'];

        //sku, catalog_sku, seller_sku
        $sku = $productData['sku'];
        $listing['catalog_sku'] = $sku;
        $sellerSku = $this->buildSellerSku($sku, (int)$id);
        $listing['seller_sku'] = $sellerSku;

        //fullfilled by
        if (!$args['isUsingAttributeForFulfillment']) {
            $listing['fulfilled_by'] = $args['fulfilledByCode'];
        } elseif ($args['userDefinedFBAAttributeVal'] === $productData[$args['userDefinedFulfilledByAttribute']]) {
            $listing['fulfilled_by'] = $args['amazonFulfillmentCode']; //use the cc-specific amazon code
        } else {
            $listing['fulfilled_by'] = 'DEFAULT';
        }

        //condition
        if (!$args['isUsingAttributeForCondition']) {
            $listing['condition'] = $args['stdDefaultConditionCode'];
        } elseif ($productData[$args['userDefinedListConditionAttribute']]) {
            // match val found in user defined attribute
            $listing['condition'] = $this->mapConditionToKnownValue(
                $args['accountListing'],
                $productData[$args['userDefinedListConditionAttribute']]
            );
        } else { // if no match, set to 'empty condition'
            $listing['condition'] = Definitions::EMPTY_CONDITION_CODE;
        }

        // add product id, type, listStatus - if couldn't find match for condition it has val false
        $listing['list_status'] =
            $listing['condition'] ? Definitions::VALIDATE_ASIN_LIST_STATUS : Definitions::MISSING_CONDITION_LIST_STATUS;
        // first-found match lookup in following order: by asin, upc, isbn, gcid, ean, general
        $isMatchFound = false;
        if ($args['attributeRepresentingAsin'] && $args['attributeRepresentingAsin'] != 'not_selected') {
            if ($value = $productData[$args['attributeRepresentingAsin']]) {
                $listing['product_id'] = $value;
                $listing['product_id_type'] = Definitions::ASIN_ATTRIBUTE;
                $isMatchFound = true;
            }
        }
        if (!$isMatchFound &&
            $args['attributeRepresentingUpc'] &&
            $args['attributeRepresentingUpc'] != 'not_selected'
        ) {
            if ($value = $productData[$args['attributeRepresentingUpc']]) {
                $listing['product_id'] = $value;
                $listing['product_id_type'] = Definitions::UPC_ATTRIBUTE;
                $isMatchFound = true;
            }
        }
        if (!$isMatchFound &&
            $args['attributeRepresentingIsbn'] &&
            $args['attributeRepresentingIsbn'] != 'not_selected'
        ) {
            if ($value = $productData[$args['attributeRepresentingIsbn']]) {
                $listing['product_id'] = $value;
                $listing['product_id_type'] = Definitions::ISBN_ATTRIBUTE;
                $isMatchFound = true;
            }
        }
        if (!$isMatchFound &&
            $args['attributeRepresentingGcid'] &&
            $args['attributeRepresentingGcid'] != 'not_selected'
        ) {
            if ($value = $productData[$args['attributeRepresentingGcid']]) {
                $listing['product_id'] = $value;
                $listing['product_id_type'] = Definitions::GCID_ATTRIBUTE;
                $isMatchFound = true;
            }
        }
        if (!$isMatchFound &&
            $args['attributeRepresentingEan'] &&
            $args['attributeRepresentingEan'] != 'not_selected'
        ) {
            if ($value = $productData[$args['attributeRepresentingEan']]) {
                $listing['product_id'] = $value;
                $listing['product_id_type'] = Definitions::EAN_ATTRIBUTE;
                $isMatchFound = true;
            }
        }
        if (!$isMatchFound &&
            $args['attributeRepresentingGeneral'] &&
            $args['attributeRepresentingGeneral'] != 'not_selected'
        ) {
            if ($value = $productData[$args['attributeRepresentingGeneral']]) {
                $listing['product_id'] = $value;
                $listing['product_id_type'] = Definitions::GENERAL_ATTRIBUTE;
                $isMatchFound = true;
            }
        }

        if (!$isMatchFound) {
            $listing['list_status'] =
                ($listing['condition']) ? Definitions::NOMATCH_LIST_STATUS : Definitions::MISSING_CONDITION_LIST_STATUS;
            $listing['product_id'] = null;
            $listing['product_id_type'] = Definitions::EMPTY_ATTRIBUTE;
        }

        return $listing;
    }

    private function mapConditionToKnownValue(AccountListingInterface $accountListing, $attributeCondition)
    {
        // returns listing condition
        switch ($attributeCondition) {
            case $accountListing->getListConditionNew():
                return Definitions::NEW_CONDITION_CODE;
            case $accountListing->getListConditionRefurbished():
                return Definitions::REFURBISHED_CONDITION_CODE;
            case $accountListing->getListConditionLikenew():
                return Definitions::USEDLIKENEW_CONDITION_CODE;
            case $accountListing->getListConditionVerygood():
                return Definitions::USEDVERYGOOD_CONDITION_CODE;
            case $accountListing->getListConditionGood():
                return Definitions::USEDGOOD_CONDITION_CODE;
            case $accountListing->getListConditionAcceptable():
                return Definitions::USEDACCEPTABLE_CONDITION_CODE;
            case $accountListing->getListConditionCollectibleLikenew():
                return Definitions::COLLECTIBLELIKENEW_CONDITION_CODE;
            case $accountListing->getListConditionCollectibleVerygood():
                return Definitions::COLLECTIBLEVERYGOOD_CONDITION_CODE;
            case $accountListing->getListConditionCollectibleGood():
                return Definitions::COLLECTIBLEGOOD_CONDITION_CODE;
            case $accountListing->getListConditionCollectibleAcceptable():
                return Definitions::COLLECTIBLEACCEPTABLE_CONDITION_CODE;
            default:
                return false;
        }
    }

    /**
     * Builds the Amazon seller sku from catalog sku
     *
     * Appends a country code to the end of the sku and checks
     * for character limitations
     *
     * @param string $sku
     * @param string $countryCode
     * @param int $productId
     * @return string
     */
    private function buildSellerSku(string $sku, int $productId): string
    {
        if (strlen($sku) > self::MAX_STRING_SIZE) {
            $sku = substr($sku, 0, 26);
            $sku .= '-' . $productId;
        }

        return $sku;
    }

    /**
     * {@inheritdoc}
     */
    public function setEligibilityByProductIds(array $ids, int $merchantId, $eligible = true)
    {
        if (empty($ids)) {
            return;
        }

        // edit list status
        if (!$this->resourceModel->setEligibilityByProductIds($ids, $merchantId, $eligible)) {
            return;
        }

        /** @var array */
        $eligibleStatuses = [
            Definitions::ERROR_LIST_STATUS,
            Definitions::ACTIVE_LIST_STATUS
        ];

        /** @var array */
        $ineligibleStatuses = [
            Definitions::NO_LONGER_ELIGIBLE_STATUS
        ];

        /** @var array */
        $log = [];
        /** @var string */
        $notes = ($eligible) ? 'eligible' : 'ineligible';

        /** @var ListingInterface[] */
        $listings = $this->collectionFactory->create()
            ->addFieldToFilter('merchant_id', $merchantId)
            ->addFieldToFilter('catalog_product_id', ['in' => $ids]);

        if ($eligible) {
            $listings->addFieldToFilter('list_status', ['in' => $eligibleStatuses]);
        } else {
            $listings->addFieldToFilter('list_status', ['in' => $ineligibleStatuses]);
        }

        foreach ($listings as $listing) {
            // build insert data
            if (!$sku = $listing->getData('seller_sku')) {
                continue;
            }

            $commandData = [
                'body' => [
                    'eligible' => (int)$eligible,
                    'sku' => $sku,
                ],
                'identifier' => $sku,
            ];

            /** @var UpdateListingEligibility $command */
            $command = $this->updateListingEligibilityFactory->create($commandData);
            $this->commandDispatcher->dispatch($merchantId, $command);

            $log[] = [
                'merchant_id' => $merchantId,
                'seller_sku' => $sku,
                'action' => __('Eligibility'),
                'notes' => __('Updated to ' . $notes)
            ];
        }

        if (!empty($log)) {
            $this->logResourceModel->insert($log);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function removeListing(string $sellerSku, int $merchantId)
    {
        $listing = $this->listingRepository->getBySellerSkuAndMerchantId($sellerSku, $merchantId);
        $listStatus = $listing->getListStatus();
        if ($listStatus == Definitions::TOBEENDED_LIST_STATUS) {
            $this->resourceModel->scheduleListStatusUpdate([$listing->getId()], Definitions::ENDED_LIST_STATUS);
        } elseif ($listStatus == Definitions::REMOVE_IN_PROGRESS_LIST_STATUS) {
            $this->listingRepository->delete($listing);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function scheduleListingInsertions(int $merchantId)
    {
        $accountListing = $this->accountListingRepository->getByMerchantId($merchantId);
        $this->resourceModel->scheduleListingInsertions($merchantId, (bool)$accountListing->getAutoList());
    }
}
