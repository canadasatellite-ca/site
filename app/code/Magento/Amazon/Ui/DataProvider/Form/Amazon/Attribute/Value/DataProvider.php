<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Amazon\Ui\DataProvider\Form\Amazon\Attribute\Value;

use Magento\Amazon\Model\ResourceModel\Amazon\Attribute\CollectionFactory;
use Magento\Catalog\Model\ProductFactory;
use Magento\Ui\DataProvider\AbstractDataProvider;
use Magento\Ui\DataProvider\Modifier\ModifierInterface;
use Magento\Ui\DataProvider\Modifier\PoolInterface;

/**
 * Class DataProvider
 */
class DataProvider extends AbstractDataProvider
{
    /** @var CollectionFactory $collection */
    protected $collection;
    /** @var ProductFactory $productFactory */
    protected $productFactory;
    /** @var PoolInterface $pool */
    protected $pool;

    /**
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param array $meta
     * @param array $data
     * @param CollectionFactory $attributeCollectionFactory
     * @param ProductFactory $product
     * @param PoolInterface $pool
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $attributeCollectionFactory,
        ProductFactory $productFactory,
        PoolInterface $pool,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->collection = $attributeCollectionFactory->create();
        $this->productFactory = $productFactory;
        $this->pool = $pool;
    }

    /**
     * Get data for grid
     *
     * @return array
     */
    public function getData()
    {
        /** @var array */
        $data = [];
        /** @var int */
        $id = null;
        /** @var array */
        $items = $this->collection->getItems();

        foreach ($items as $attribute) {
            $data[$attribute->getId()] = $attribute->getData();
            $id = $attribute->getId();
        }

        /** @var string */
        $amazonAttribute = $data[$id]['amazon_attribute'] ?? 'NotAvailable';
        /** @var string */
        $newName = $amazonAttribute;
        /** @var string */
        $newCode = strtolower($amazonAttribute);
        /** @var string */
        $attributeSetIds = isset($data[$id]['attribute_set_ids'])
            ? explode(',', $data[$id]['attribute_set_ids'])
            : '';
        /** @var int */
        $type = $data[$id]['type'] ?? 1;
        /** @var string */
        $attributeCode = $data[$id]['catalog_attribute'];
        /** @var bool */
        $isGlobal = ($attributeCode) ? $this->isAttributeGlobal($attributeCode) : 1;

        // set default value for new_name form field
        $data[$id]['new_name'] = $newName;
        $data[$id]['new_code'] = substr($newCode, 0, 30);
        $data[$id]['attribute_set_ids'] = $attributeSetIds;
        $data[$id]['type'] = $type;
        $data[$id]['is_global'] = $isGlobal;

        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function getMeta()
    {
        $meta = parent::getMeta();

        /** @var ModifierInterface $modifier */
        foreach ($this->pool->getModifiersInstances() as $modifier) {
            $meta = $modifier->modifyMeta($meta);
        }

        return $meta;
    }

    /**
     * Returns the is_global value by attribute code
     *
     * @param string $attributeCode
     * @return bool
     */
    private function isAttributeGlobal($attributeCode)
    {
        /** @var ProductFactory */
        $product = $this->productFactory->create();

        if (!$attribute = $product->getResource()->getAttribute($attributeCode)) {
            return true;
        }

        return $attribute->getIsGlobal();
    }
}
