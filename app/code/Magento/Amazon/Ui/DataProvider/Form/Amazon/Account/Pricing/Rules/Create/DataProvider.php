<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Amazon\Ui\DataProvider\Form\Amazon\Account\Pricing\Rules\Create;

use Magento\Amazon\Model\ResourceModel\Amazon\Pricing\Rule\Collection as RuleCollection;
use Magento\Amazon\Model\ResourceModel\Amazon\Pricing\Rule\CollectionFactory as RuleCollectionFactory;
use Magento\Catalog\Model\ProductFactory;
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
    /** @var ProductFactory $productFactory */
    private $productFactory;

    /** @var RuleCollection */
    protected $collection;

    /**
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param RuleCollectionFactory $ruleCollectionFactory
     * @param ProductFactory $productFactory
     * @param PoolInterface $pool
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        RuleCollectionFactory $ruleCollectionFactory,
        ProductFactory $productFactory,
        PoolInterface $pool,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->pool = $pool;
        $this->collection = $ruleCollectionFactory->create();
        $this->productFactory = $productFactory;
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
        /** @var array */
        $items = $this->collection->getItems();

        foreach ($items as $item) {
            // if assigned by product attribute
            if ($floor = $item->getFloor()) {
                // handles product attribute being no longer available
                if (!$this->attributeExist($floor)) {
                    $item->setFloor('price');
                    $item->setFloorPriceMovement('0');
                }
            }

            /** @var int */
            $id = $item->getId();
            $data[$id] = $item->getData();
            // update for data fork
            $data[$id]['price_movement_one'] = $data[$id]['price_movement'];
            $data[$id]['price_movement_two'] = $data[$id]['price_movement'];
            $data[$id]['simple_action_one'] = $data[$id]['simple_action'];
            $data[$id]['simple_action_two'] = $data[$id]['simple_action'];
            $data[$id]['discount_amount_one'] = $data[$id]['discount_amount'];
            $data[$id]['discount_amount_two'] = $data[$id]['discount_amount'];
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
