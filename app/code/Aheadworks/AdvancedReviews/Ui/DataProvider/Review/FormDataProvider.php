<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Ui\DataProvider\Review;

use Magento\Ui\DataProvider\AbstractDataProvider;
use Aheadworks\AdvancedReviews\Model\ResourceModel\Review\CollectionFactory;
use Aheadworks\AdvancedReviews\Model\ResourceModel\Review\Collection;
use Aheadworks\AdvancedReviews\Model\Review;
use Aheadworks\AdvancedReviews\Api\Data\ReviewInterface;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Ui\DataProvider\Modifier\PoolInterface;
use Magento\Ui\DataProvider\Modifier\ModifierInterface;

/**
 * Class FormDataProvider
 * @package Aheadworks\AdvancedReviews\Ui\DataProvider\Review
 */
class FormDataProvider extends AbstractDataProvider
{
    /**
     * @var Collection
     */
    protected $collection;

    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @var DataPersistorInterface
     */
    private $dataPersistor;

    /**
     * @var PoolInterface
     */
    private $pool;

    /**
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param CollectionFactory $collectionFactory
     * @param RequestInterface $request
     * @param DataPersistorInterface $dataPersistor
     * @param PoolInterface $pool
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $collectionFactory,
        RequestInterface $request,
        DataPersistorInterface $dataPersistor,
        PoolInterface $pool,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->collection = $collectionFactory->create();
        $this->request = $request;
        $this->dataPersistor = $dataPersistor;
        $this->pool = $pool;
    }

    /**
     * Get data
     *
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getData()
    {
        $data = [];
        $dataFromForm = $this->dataPersistor->get('aw_adv_rev_review_form');
        $reviewId = $this->request->getParam($this->getRequestFieldName());
        if (!empty($dataFromForm)) {
            $data = $dataFromForm;
            $this->dataPersistor->clear('aw_adv_rev_review_form');
        } else {
            $reviews = $this->getCollection()
                ->addFieldToFilter(ReviewInterface::ID, $reviewId)
                ->setNeedToAttachProductData(true)
                ->getItems();
            /** @var Review $review */
            foreach ($reviews as $review) {
                if ($reviewId == $review->getId()) {
                    $data = $review->getData();
                }
            }
        }
        $preparedData[$reviewId] = $this->prepareData($data);

        return $preparedData;
    }

    /**
     * Prepare data
     *
     * @param array $data
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function prepareData($data)
    {
        /** @var ModifierInterface $modifier */
        foreach ($this->pool->getModifiersInstances() as $modifier) {
            $data = $modifier->modifyData($data);
        }

        return $data;
    }

    /**
     * {@inheritdoc}
     * @throws \Magento\Framework\Exception\LocalizedException
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
}
