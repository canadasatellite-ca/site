<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Amazon\Ui\DataProvider\Form\Amazon\Account\Settings\Listings;

use Magento\Amazon\Model\ResourceModel\Amazon\Account\Listing\CollectionFactory;
use Magento\Catalog\Model\ProductFactory;
use Magento\Framework\App\Request\Http;
use Magento\Ui\DataProvider\AbstractDataProvider;
use Magento\Ui\DataProvider\Modifier\ModifierInterface;
use Magento\Ui\DataProvider\Modifier\PoolInterface;

/**
 * Class DataProvider
 */
class DataProvider extends AbstractDataProvider
{
    /** @var PoolInterface $pool */
    private $pool;
    /** @var CollectionFactory $collection */
    protected $collection;
    /** @var ProductFactory $product */
    protected $productFactory;
    /** @var Http $request */
    protected $request;

    /**
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param CollectionFactory $accountCollectionFactory
     * @param ProductFactory $productFactory
     * @param Http $request
     * @param PoolInterface $pool
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $collectionFactory,
        ProductFactory $productFactory,
        Http $request,
        PoolInterface $pool,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->pool = $pool;
        $this->collection = $collectionFactory->create();
        $this->productFactory = $productFactory;
        $this->request = $request;
    }

    /**
     * Get data
     *
     * @return array
     */
    public function getData()
    {
        /** @var array */
        $data = [];
        /** @var $id */
        $id = null;
        /** @var array */
        $items = $this->collection->getItems();

        foreach ($items as $item) {
            /** @var int */
            $merchantId = $item->getMerchantId();

            // if assigned by product attribute
            if (!$item->getFulfilledBy()) {
                // handles product attribute being no longer available
                if (!$this->attributeExist($item->getFulfilledByField())) {
                    $item->setFulfilledBy(1);
                    $item->setFulfilledByField('');
                    $item->setFulfilledBySeller('');
                    $item->setFulfilledByAmazon('');
                }
            }

            // if assigned by product attribute
            if (!$item->getListCondition()) {
                // handles product attribute being no longer available
                if (!$this->attributeExist($item->getListConditionField())) {
                    $item->setListConditionField('');
                }
            }

            $data[$merchantId] = $item->getData();
        }

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
     * Returns true/false on whether the Magento
     * product attribute exists
     *
     * @param string $code
     * @return bool
     */
    private function attributeExist($code)
    {
        /** @var ProductFactory */
        $product = $this->productFactory->create();

        // build attribute data
        return $product->getResource()->getAttribute($code);
    }
}
