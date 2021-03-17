<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Amazon\Ui\DataProvider\Listing\Amazon\Account\Listing\Thirdparty\Manual;

use Magento\Amazon\Model\Amazon\Definitions;
use Magento\Amazon\Model\ResourceModel\Amazon\Listing\CollectionFactory as ListingCollectionFactory;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Framework\Api\Filter;
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Ui\DataProvider\AbstractDataProvider;

/**
 * Class DataProvider
 */
class DataProvider extends AbstractDataProvider
{
    /**
     * @var \Magento\Amazon\Model\ResourceModel\Amazon\Listing\Collection
     */
    protected $collection;
    /** @var ListingCollectionFactory $listingCollectionFactory */
    protected $listingCollectionFactory;
    /** @var RequestInterface */
    protected $request;
    /** @var FilterBuilder $filterBuilder */
    private $filterBuilder;
    /** @var \Magento\Ui\DataProvider\AddFieldToCollectionInterface[] */
    protected $addFieldStrategies;
    /** @var \Magento\Ui\DataProvider\AddFilterToCollectionInterface[] */
    protected $addFilterStrategies;
    /** @var Filter $collectionFilter */
    protected $collectionFilter;
    /** @var DataPersistorInterface $dataPersistor */
    protected $dataPersistor;

    /**
     * Construct
     *
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param CollectionFactory $collectionFactory
     * @param ListingCollectionFactory $listingCollectionFactory
     * @param RequestInterface $request
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
        ListingCollectionFactory $listingCollectionFactory,
        RequestInterface $request,
        FilterBuilder $filterBuilder,
        DataPersistorInterface $dataPersistor,
        array $addFieldStrategies = [],
        array $addFilterStrategies = [],
        array $meta = [],
        array $data = []
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->collection = $collectionFactory->create();
        $this->listingCollectionFactory = $listingCollectionFactory;
        $this->request = $request;
        $this->filterBuilder = $filterBuilder;
        $this->dataPersistor = $dataPersistor;
        $this->addFieldStrategies = $addFieldStrategies;
        $this->addFilterStrategies = $addFilterStrategies;
    }

    /**
     * Get data
     *
     * @return array
     */
    public function getData()
    {
        // add required fields to collection
        $this->addField('name');
        $this->addField('attribute_set_id');
        $this->addField('type_id');
        $this->addField('has_options');
        $this->addField('qty');
        $this->addField('price');

        /** @var array */
        $allowedProductTypes = [
            'simple',
            'virtual'
        ];

        // filter by product type
        $filter = $this->filterBuilder->setField('type_id')
            ->setValue($allowedProductTypes)->setConditionType('in')->create();
        $this->addFilter($filter);
        // filter by has options
        $filter = $this->filterBuilder->setField('required_options')->setValue('0')->setConditionType('eq')->create();
        $this->addFilter($filter);

        /** @var array */
        $listingIds = [];
        /** @var array */
        $statuses = [
            Definitions::LIST_IN_PROGRESS_LIST_STATUS,
            Definitions::CONDITION_OVERRIDE_LIST_STATUS,
            Definitions::ERROR_LIST_STATUS,
            Definitions::ACTIVE_LIST_STATUS,
            Definitions::REMOVE_IN_PROGRESS_LIST_STATUS,
            Definitions::ENDED_LIST_STATUS,
            Definitions::TOBEENDED_LIST_STATUS
        ];

        /** @var CollectionFactory */
        $listingCollection = $this->listingCollectionFactory->create();

        if ($merchantId = $this->request->getParam('merchant_id')) {
            $this->dataPersistor->set('current_merchant_id', $merchantId);
        } else {
            $merchantId = $this->dataPersistor->get('current_merchant_id');
            $this->dataPersistor->clear('current_merchant_id');
        }

        $listingCollection->addFieldToFilter('merchant_id', $merchantId);
        $listingCollection->addFieldToFilter('catalog_product_id', ['notnull' => true]);
        $listingCollection->addFieldToFilter('list_status', ['in' => $statuses]);

        foreach ($listingCollection as $listing) {
            array_push($listingIds, $listing->getData('catalog_product_id'));
        }

        if (!empty($listingIds)) {
            $filter = $this->filterBuilder->setField('entity_id')
                ->setValue($listingIds)->setConditionType('nin')->create();
            $this->addFilter($filter);
        }

        $this->getCollection()->load();
        $items = $this->getCollection()->toArray();

        return [
            'totalRecords' => $this->getCollection()->getSize(),
            'items' => array_values($items),
        ];
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
            $this->addFieldStrategies[$field]->addField($this->getCollection(), $field, $alias);
        } else {
            parent::addField($field, $alias);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function addFilter(Filter $filter)
    {
        if (isset($this->addFilterStrategies[$filter->getField()])) {
            $this->addFilterStrategies[$filter->getField()]
                ->addFilter(
                    $this->getCollection(),
                    $filter->getField(),
                    [$filter->getConditionType() => $filter->getValue()]
                );
        } else {
            parent::addFilter($filter);
        }
    }
}
