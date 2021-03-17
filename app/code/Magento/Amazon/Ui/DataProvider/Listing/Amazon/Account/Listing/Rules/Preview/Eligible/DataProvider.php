<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Amazon\Ui\DataProvider\Listing\Amazon\Account\Listing\Rules\Preview\Eligible;

use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Framework\Api\Filter;
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Ui\DataProvider\AbstractDataProvider;

/**
 * Class DataProvider
 */
class DataProvider extends AbstractDataProvider
{
    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\Collection
     */
    protected $collection;
    /** @var FilterBuilder $filterBuilder */
    protected $filterBuilder;
    /** @var DataPersistorInterface $dataPersistor */
    protected $dataPersistor;
    /** @var \Magento\Ui\DataProvider\AddFieldToCollectionInterface[] */
    protected $addFieldStrategies;
    /** @var \Magento\Ui\DataProvider\AddFilterToCollectionInterface[] */
    protected $addFilterStrategies;

    /**
     * Construct
     *
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param CollectionFactory $collectionFactory
     * @param FilterBuilder $filterBuilder
     * @param DataPersistorInterface $dataPersistor
     * @param \Magento\Ui\DataProvider\AddFieldToCollectionInterface[] $addFieldStrategies
     * @param \Magento\Ui\DataProvider\AddFilterToCollectionInterface[] $addFilterStrategies
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $collectionFactory,
        FilterBuilder $filterBuilder,
        DataPersistorInterface $dataPersistor,
        array $addFieldStrategies = [],
        array $addFilterStrategies = [],
        array $meta = [],
        array $data = []
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->filterBuilder = $filterBuilder;
        $this->dataPersistor = $dataPersistor;
        $this->addFieldStrategies = $addFieldStrategies;
        $this->addFilterStrategies = $addFilterStrategies;
        $this->collection = $collectionFactory->create();
    }

    /**
     * Add field to select
     *
     * @param string|array $field
     * @param string|null $alias
     * @return void
     */
    public function addField($field, $alias = null)
    {
        if (isset($this->addFieldStrategies[$field])) {
            $this->addFieldStrategies[$field]->addField($this->collection, $field, $alias);
        } else {
            parent::addField($field, $alias);
        }
    }

    /**
     * Get data
     *
     * @return array
     */
    public function getData()
    {
        /** @var array */
        $ids = $this->dataPersistor->get('listing_eligible');

        // add required fields to collection
        $this->addField('name');
        $this->addField('attribute_set_id');
        $this->addField('type_id');
        $this->addField('has_options');
        $this->addField('qty');
        $this->addField('price');

        // filter by to list ids
        $filter = $this->filterBuilder->setField('entity_id')->setValue($ids)->setConditionType('in')->create();
        $this->addFilter($filter);

        if (!$this->collection->isLoaded()) {
            $this->collection->load();
        }

        $items = $this->collection->toArray();

        return [
            'totalRecords' => $this->collection->getSize(),
            'items' => array_values($items),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function addFilter(Filter $filter)
    {
        if (isset($this->addFilterStrategies[$filter->getField()])) {
            $this->addFilterStrategies[$filter->getField()]
                ->addFilter(
                    $this->collection,
                    $filter->getField(),
                    [$filter->getConditionType() => $filter->getValue()]
                );
        } else {
            parent::addFilter($filter);
        }
    }
}
