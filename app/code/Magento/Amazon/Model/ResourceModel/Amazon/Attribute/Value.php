<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\Amazon\Model\ResourceModel\Amazon\Attribute;

use Magento\Amazon\Api\AccountRepositoryInterface;
use Magento\Amazon\Api\Data\AttributeInterface;
use Magento\Amazon\Logger\AscClientLogger;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Model\Entity\Attribute;
use Magento\Catalog\Model\ProductFactory;
use Magento\Eav\Api\AttributeOptionManagementInterface;
use Magento\Eav\Api\Data\AttributeOptionInterfaceFactory;
use Magento\Eav\Model\Config;
use Magento\Eav\Model\Entity\Attribute\AbstractAttribute;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\EntityManager\MetadataPool;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\File\Uploader as FileUploader;
use Magento\Framework\Filesystem\Io\File;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Context;
use Magento\Store\Api\StoreRepositoryInterface;
use Magento\Store\Model\Store;

/**
 * Class Value
 */
class Value extends AbstractDb
{
    const CHUNK_SIZE = 1000;
    const DISABLED_STATUS = 0;
    const ENABLED_STATUS = 1;
    const DEFAULT_STORE_ID = 0;

    /**
     * @var ResourceConnection $resourceConnection
     */
    protected $resourceConnection;

    /** @var CollectionFactory */
    private $collectionFactory;

    /** @var AccountRepositoryInterface */
    private $accountRepository;

    /** @var StoreRepositoryInterface */
    private $storeRepository;

    /** @var AscClientLogger */
    private $ascClientLogger;

    /** @var ProductFactory */
    private $product;

    /** @var Config */
    private $eavConfig;

    /** @var DirectoryList */
    private $directoryList;

    /** @var File */
    private $file;

    /** @var AttributeOptionManagementInterface */
    private $attributeOptionManagement;

    /** @var AttributeOptionInterfaceFactory */
    private $attributeOptionFactory;

    /** @var array */
    private $attributeValues;

    /** @var MetadataPool */
    protected $metadataPool;

    /**
     * @param Context $context
     * @param CollectionFactory $collectionFactory
     * @param AccountRepositoryInterface $accountRepository
     * @param StoreRepositoryInterface $storeRepository
     * @param AscClientLogger $ascClientLogger
     * @param ProductFactory $product
     * @param Config $eavConfig
     * @param DirectoryList $directoryList
     * @param File $file
     * @param AttributeOptionManagementInterface $attributeOptionManagement
     * @param AttributeOptionInterfaceFactory $attributeOptionFactory
     * @param MetadataPool $metadataPool
     * @param ResourceConnection $resourceConnection
     */
    public function __construct(
        Context $context,
        CollectionFactory $collectionFactory,
        AccountRepositoryInterface $accountRepository,
        StoreRepositoryInterface $storeRepository,
        AscClientLogger $ascClientLogger,
        ProductFactory $product,
        Config $eavConfig,
        DirectoryList $directoryList,
        File $file,
        AttributeOptionManagementInterface $attributeOptionManagement,
        AttributeOptionInterfaceFactory $attributeOptionFactory,
        MetadataPool $metadataPool,
        ResourceConnection $resourceConnection
    ) {
        parent::__construct($context);
        $this->collectionFactory = $collectionFactory;
        $this->accountRepository = $accountRepository;
        $this->storeRepository = $storeRepository;
        $this->ascClientLogger = $ascClientLogger;
        $this->product = $product;
        $this->eavConfig = $eavConfig;
        $this->directoryList = $directoryList;
        $this->file = $file;
        $this->attributeOptionManagement = $attributeOptionManagement;
        $this->attributeOptionFactory = $attributeOptionFactory;
        $this->metadataPool = $metadataPool;
        $this->attributeValues = [];
        $this->resourceConnection = $resourceConnection;
    }

    /**
     * Constructor
     */
    protected function _construct()
    {
        $this->_init(
            'channel_amazon_attribute_value',
            'id'
        );
    }

    /**
     * Inserts Amazon attribute values
     *
     * @param array $data
     * @param int $merchantId
     * @return void
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function insert(array $data, $merchantId)
    {
        /** @var AdapterInterface */
        $connection = $this->getConnection();
        $tableName = $this->getMainTable();
        $keys = [];

        try {
            $account = $this->accountRepository->getByMerchantId($merchantId);
        } catch (NoSuchEntityException $e) {
            return;
        }

        $countryCode = $account->getCountryCode();
        $collection = $this->collectionFactory->create();
        $collection->addFieldToFilter('country_code', $countryCode);

        foreach ($collection as $attribute) {
            $keys[$attribute->getAmazonAttribute()] = $attribute->getId();
        }

        foreach ($data as $key => $attributeValue) {
            /** @var string */
            $amazonAttribute = (isset($attributeValue['amazon_attribute'])) ? $attributeValue['amazon_attribute'] : '';
            if (!isset($keys[$amazonAttribute])) {
                $this->ascClientLogger->warning('Cannot insert attribute value. Attribute not found.', ['attribute' => $amazonAttribute]);
                continue;
            }
            $data[$key]['parent_id'] = $keys[$amazonAttribute];
        }

        $connection->insertOnDuplicate($tableName, $data, []);
    }

    /**
     * Clears imported attribute values by attribute id
     * where $attributeId represents the amazon attribute id
     * from channel_amazon_attribute table
     *
     * @param array $attributeIds
     * @return void
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function clearAttributeValuesByAttributeIds(array $attributeIds): void
    {
        $connection = $this->getConnection();
        $tableName = $this->getMainTable();
        $bind = [
            'status' => (int)self::DISABLED_STATUS
        ];
        $where = [
            'parent_id IN (?)' => $attributeIds
        ];

        try {
            $connection->beginTransaction();
            $connection->update($tableName, $bind, $where);
            $connection->commit();
        } catch (\Exception $e) {
            $this->ascClientLogger->error('Cannot clear attribute values', ['exception' => $e]);
            $connection->rollBack();
        }
    }

    /**
     * Imports all amazon attribute values to the associated Magento attribute
     *
     * where $attributeId represents the amazon attribute id
     * from channel_amazon_attribute table
     *
     * @param AttributeInterface $attribute
     * @return void
     */
    public function importAmazonAttributeValues(AttributeInterface $attribute)
    {
        try {
            $downloadFlag = false;
            $importedIdSets = [];
            $overwrite = $attribute->getData('overwrite');
            /** @var ProductFactory */
            $product = $this->product->create();

            foreach (explode(',', $attribute->getCatalogAttribute()) as $attributeCode) {
                // build attribute data
                if (!$catalogAttribute = $product->getResource()->getAttribute($attributeCode)) {
                    $this->clearAttributeValue($attribute->getId());
                    continue;
                }

                // if global
                if ($catalogAttribute->getIsGlobal()) {
                    $storeList[] = self::DEFAULT_STORE_ID;
                } else {
                    $storeIds = $attribute->getStoreIds();
                    if (empty($storeIds)) {
                        continue;
                    }
                    $storeIds = explode(',', $storeIds);
                    $stores = $this->storeRepository->getList();
                    $storeList = [];
                    foreach ($stores as $store) {
                        if (in_array($store->getStoreId(), $storeIds)) {
                            $storeList[] = $store->getStoreId();
                        }
                    }
                }

                // get attribute value by id
                if ($rows = $this->getAttributeValueById($attribute->getId())) {
                    /** @var array */
                    $importedIdSets[] = $this->importAttributeValues(
                        $catalogAttribute,
                        $attributeCode,
                        $overwrite,
                        $rows,
                        $storeList,
                        $downloadFlag
                    );
                }

                // mark download complete
                if ($catalogAttribute->getData('frontend_input') == 'media_image') {
                    $downloadFlag = true;
                }
            }

            // set as imported into Magento
            if (!empty($importedIdSets)) {
                $importedIds = array_merge(...$importedIdSets);
                foreach (array_chunk($importedIds, self::CHUNK_SIZE) as $chunkedIds) {
                    $this->setAsImported($chunkedIds);
                }
            }
        } catch (\Exception $e) {
            $this->ascClientLogger->error('Cannot import Amazon attribute values', ['exception' => $e]);
        }
    }

    /**
     * Clears attribute value in the event the attribute is no
     * longer available (i.e. deleted)
     *
     * @param int $id
     * @return void
     */
    private function clearAttributeValue($id)
    {
        /** @var AdapterInterface */
        $connection = $this->getConnection();
        $attributeTable = $this->resourceConnection->getTableName('channel_amazon_attribute');

        // reset active listing flag
        $bind = [
            'is_active' => (int)0,
            'catalog_attribute' => null,
            'overwrite' => (int)0,
            'type' => (int)0,
            'attribute_set_ids' => null,
            'in_search' => (int)0,
            'comparable' => (int)0,
            'in_navigation' => (int)0,
            'in_search_navigation' => (int)0,
            'position' => (int)0,
            'in_promo' => (int)0
        ];

        $where = [
            'id = ?' => $id
        ];

        try {
            $connection->beginTransaction();
            $connection->update($attributeTable, $bind, $where);
            $connection->commit();
        } catch (\Exception $e) {
            $connection->rollBack();
        }
    }

    /**
     * Get attribute value by id
     *
     * @param int $id
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Zend_Db_Select_Exception
     */
    private function getAttributeValueById($id): array
    {
        $connection = $this->getConnection();
        /** @var string */
        $attributeValues = $this->getMainTable();
        /** @var string */
        $amazonAttributes = $this->resourceConnection->getTableName('channel_amazon_attribute');
        /** @var string */
        $accountTable = $this->resourceConnection->getTableName('channel_amazon_account');
        /** @var string */
        $accountListing = $this->resourceConnection->getTableName('channel_amazon_listing');
        /** @var string */
        $catalogEntityProduct = $this->resourceConnection->getTableName('catalog_product_entity');
        /** @var array */
        $values = [];
        $linkId = $this->getProductEntityLinkField();

        if (!$id) {
            return [];
        }

        $select = $connection->select()->from(
            ['attribute_values' => $attributeValues],
            []
        )->joinInner(
            ['amazon_attributes' => $amazonAttributes],
            'amazon_attributes.id = attribute_values.parent_id',
            []
        )->joinInner(
            ['account' => $accountTable],
            'account.country_code = attribute_values.country_code',
            []
        )->joinInner(
            ['listing' => $accountListing],
            'listing.asin = attribute_values.asin and listing.merchant_id = account.merchant_id',
            []
        )->joinInner(
            ['cpe' => $catalogEntityProduct],
            'cpe.entity_id = listing.catalog_product_id',
            []
        )->where(
            'amazon_attributes.id = ?',
            $id
        )->where(
            'attribute_values.status = ?',
            self::DISABLED_STATUS
        )->columns(
            [
                'id' => 'attribute_values.id',
                'product_id' => 'cpe.' . $linkId,
                'value' => 'attribute_values.value'
            ]
        )->group('cpe.' . $linkId);

        try {
            $connection->beginTransaction();
            $values = $connection->fetchAll($select);
            $connection->commit();
        } catch (\Exception $e) {
            $this->ascClientLogger->error('Cannot get attribute values', ['exception' => $e]);
            $connection->rollBack();
        }

        return $values;
    }

    /**
     * Import attribute value(s) into Magento product attribute
     *
     * @param Attribute $attribute
     * @param string $attributeCode
     * @param boolean $overwrite
     * @param array $rows
     * @param array $stores
     * @param boolean $downloadFlag
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function importAttributeValues(
        $attribute,
        $attributeCode,
        $overwrite,
        array $rows,
        array $stores,
        $downloadFlag = false
    ): array {
        /** @var ProductFactory */
        $product = $this->product->create();
        $mediaGalleryAttributeId = $product->getResource()->getAttribute('media_gallery')->getId();
        $eavAttribute = $this->eavConfig->getAttribute('catalog_product', $attributeCode);
        $attrId = $attribute->getId();
        $tableData = [];
        $imageData = [];
        $importedIds = [];

        foreach ($rows as $row) {
            $productId = $row['product_id'];
            $value = $row['value'];

            // special handling for images
            if ($attribute->getFrontendInput() == 'media_image') {
                // if image is not downloaded
                if (!$downloadFlag) {
                    if ($image = $this->downloadImage($value)) {
                        $imageData[$productId][] = [
                            'attribute_id' => $mediaGalleryAttributeId,
                            'label' => '',
                            'position' => '99',
                            'disabled' => '0',
                            'value' => $image,
                        ];
                    }
                }
                $value = $this->getImageRelativePathWithFileNameByUrl($value);
            }

            $value = $eavAttribute->usesSource() ? $this->saveOptionId($eavAttribute, $attributeCode, $value) : $value;

            // determines select vs input type
            if ($value) {
                // build database insert data by store
                foreach ($stores as $storeId) {
                    $importedIds[] = $row['id'];
                    $tableData[] = [
                        $this->getProductEntityLinkField() => $productId,
                        'attribute_id' => $attrId,
                        'store_id' => $storeId,
                        'value' => $value
                    ];
                }
            }
        }

        // import images
        foreach (array_chunk($imageData, self::CHUNK_SIZE, true) as $insertList) {
            $this->saveMediaGallery($insertList);
        }

        foreach (array_chunk($tableData, self::CHUNK_SIZE, true) as $insertList) {
            // insert in db
            $this->insertAttributeValue($attribute, $overwrite, $insertList);
        }

        return $importedIds;
    }

    /**
     * Downloads Amazon image to pub/media/
     *
     * @param string $imageUrl
     * @return bool|string
     */
    private function downloadImage($imageUrl)
    {
        try {
            /** @var string */
            $importDir = $this->directoryList->getPath(DirectoryList::MEDIA) . '/catalog/product/';
            /** create folder if it is not exists */
            $this->file->checkAndCreateFolder($importDir);
            /** @var string */
            $relativeFilePath = $this->getImageRelativePathWithFileNameByUrl($imageUrl);

            $fileDirectory = $importDir . dirname($relativeFilePath);
            $filePath = $importDir . $relativeFilePath;
            $this->file->mkdir($fileDirectory, 0775);

            /** read file from URL and copy it to the import folder */
            $this->file->read($imageUrl, $filePath);
            return $relativeFilePath;
        } catch (\Exception $e) {
            $this->ascClientLogger->critical(
                'Exception occurred during during image downloading',
                ['exception' => $e]
            );
        }
        return false;
    }

    /**
     * Checks for existing attribute option id, create one if not
     *
     * @param AbstractAttribute $attr
     * @param string $attributeCode
     * @param string $value
     * @return int
     */
    public function saveOptionId($attr, $attributeCode, $value): int
    {
        $optionId = 0;
        try {
            $value = ucwords(strtolower(trim($value)));
            // check for duplicates
            if (isset($this->attributeValues[$value])) {
                return $this->attributeValues[$value];
            }

            $optionId = (int)$attr->getSource()->getOptionId($value);
            // if does not exist
            if (!$optionId) {
                // get attribute option interface
                $attributeOption = $this->attributeOptionFactory->create();
                // set option values
                $attributeOption->setLabel($value);
                $attributeOption->setSortOrder(99);
                $attributeOption->setIsDefault(false);
                $attributeOptionManagement = $this->attributeOptionManagement;
                $attributeOptionManagement->add(4, $attributeCode, $attributeOption);
                $items = $attributeOptionManagement->getItems(4, $attributeCode);

                foreach ($items as $item) {
                    if ($item->getLabel() == $value) {
                        $optionId = (int)$item->getValue();
                        // add to attribute values
                        $this->attributeValues[$value] = $optionId;
                    }
                }
            }
        } catch (\Exception $e) {
            $this->ascClientLogger->warning(
                'Exception occurred during option saving',
                ['exception' => $e]
            );
        }
        return $optionId;
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
     * Save product media gallery.
     *
     * @param array $mediaGalleryData
     * @return void
     */
    private function saveMediaGallery(array $mediaGalleryData)
    {
        try {
            /** @var AdapterInterface */
            $connection = $this->getConnection();
            /** @var string */
            $mediaGalleryTableName = $this->resourceConnection->getTableName('catalog_product_entity_media_gallery');
            $mediaGalleryValueTableName = $this->resourceConnection->getTableName('catalog_product_entity_media_gallery_value');
            $mediaGalleryEntityToValueTableName =
                $this->resourceConnection->getTableName('catalog_product_entity_media_gallery_value_to_entity');
            $productIds = [];
            $imageNames = [];
            $multiInsertData = [];
            $valueToProductId = [];

            foreach ($mediaGalleryData as $productId => $mediaGalleryRows) {
                $productIds[] = $productId;
                $insertedGalleryImgs = [];

                foreach ($mediaGalleryRows as $insertValue) {
                    if (!in_array($insertValue['value'], $insertedGalleryImgs)) {
                        $valueArr = [
                            'attribute_id' => $insertValue['attribute_id'],
                            'value' => $insertValue['value'],
                        ];
                        $valueToProductId[$insertValue['value']][] = $productId;
                        $imageNames[] = $insertValue['value'];
                        $multiInsertData[] = $valueArr;
                        $insertedGalleryImgs[] = $insertValue['value'];
                    }
                }
            }

            $oldMediaValues = $connection->fetchAssoc(
                $connection->select()->from($mediaGalleryTableName, ['value_id', 'value'])
                    ->where('value IN (?)', $imageNames)
            );

            $connection->insertOnDuplicate($mediaGalleryTableName, $multiInsertData, []);

            $multiInsertData = [];
            $newMediaSelect = $connection->select()->from($mediaGalleryTableName, ['value_id', 'value'])
                ->where('value IN (?)', $imageNames);
            if (array_keys($oldMediaValues)) {
                $newMediaSelect->where('value_id NOT IN (?)', array_keys($oldMediaValues));
            }
            $dataForSkinnyTable = [];
            $newMediaValues = $connection->fetchAssoc($newMediaSelect);

            foreach ($mediaGalleryData as $productId => $mediaGalleryRows) {
                foreach ($mediaGalleryRows as $insertValue) {
                    foreach ($newMediaValues as $valueId => $values) {
                        if ($values['value'] == $insertValue['value']) {
                            $insertValue['value_id'] = $valueId;
                            $insertValue[$this->getProductEntityLinkField()]
                                = array_shift($valueToProductId[$values['value']]);
                            unset($newMediaValues[$valueId]);
                            break;
                        }
                    }
                    if (isset($insertValue['value_id'])) {
                        $valueArr = [
                            'value_id' => $insertValue['value_id'],
                            'store_id' => Store::DEFAULT_STORE_ID,
                            $this->getProductEntityLinkField() => $insertValue[$this->getProductEntityLinkField()],
                            'label' => $insertValue['label'],
                            'position' => $insertValue['position'],
                            'disabled' => $insertValue['disabled'],
                        ];
                        $multiInsertData[] = $valueArr;
                        $dataForSkinnyTable[] = [
                            'value_id' => $insertValue['value_id'],
                            $this->getProductEntityLinkField() => $insertValue[$this->getProductEntityLinkField()],
                        ];
                    }
                }
            }

            $connection->insertOnDuplicate(
                $mediaGalleryValueTableName,
                $multiInsertData,
                ['value_id', 'store_id', $this->getProductEntityLinkField(), 'label', 'position', 'disabled']
            );

            $connection->insertOnDuplicate(
                $mediaGalleryEntityToValueTableName,
                $dataForSkinnyTable,
                ['value_id']
            );
        } catch (\Exception $e) {
            $this->ascClientLogger->error('Cannot save media gallery', ['exception' => $e]);
        }
    }

    /**
     * Runs DB insertion of attribute values
     *
     * @param Attribute $attribute
     * @param boolean $overwrite
     * @param array $insertList
     * @return void
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function insertAttributeValue($attribute, $overwrite, array $insertList)
    {
        $connection = $this->getConnection();
        /** @var string */
        $attrTable = $attribute->getBackend()->getTable();
        $linkId = $this->getProductEntityLinkField();
        // if set for no overwrite of existing values
        $values = ($overwrite) ? [$linkId, 'attribute_id', 'store_id', 'value'] : [$linkId, 'attribute_id', 'store_id'];

        try {
            $connection->beginTransaction();
            $connection->insertOnDuplicate($attrTable, $insertList, $values);
            $connection->commit();
        } catch (\Exception $e) {
            $this->ascClientLogger->error('Cannot insert attribute value', ['exception' => $e]);
            $connection->rollBack();
        }
    }

    /**
     * Updates status to complete
     *
     * @param array $ids
     * @return void
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function setAsImported($ids)
    {
        $connection = $this->getConnection();
        /** @var string */
        $tableName = $this->getMainTable();
        // chunk data
        foreach (array_chunk($ids, self::CHUNK_SIZE) as $chunkIds) {
            $bind = [
                'status' => (int)self::ENABLED_STATUS
            ];
            $where = [
                'id IN (?)' => $chunkIds
            ];
            try {
                $connection->beginTransaction();
                $connection->update($tableName, $bind, $where);
                $connection->commit();
            } catch (\Exception $e) {
                $this->ascClientLogger->error('Cannot mark attributes as imported', ['exception' => $e]);
                $connection->rollBack();
            }
        }
    }

    /**
     * @param $imageUrl
     * @return string
     */
    private function getImageRelativePathWithFileNameByUrl($imageUrl): string
    {
        $filteredFileName = str_replace('%', '', baseName($imageUrl));

        /*
         * doing this to add some level of uniqueness to the file name
         * and avoid conflicts with other magento images
         */
        $fileName = hash('sha256', 'amazon_' . $filteredFileName)
            . '.' . pathinfo($filteredFileName, PATHINFO_EXTENSION);

        $dispersionPath = FileUploader::getDispersionPath($fileName);
        return '/' . ltrim($dispersionPath, '/') . '/' . $fileName;
    }
}
