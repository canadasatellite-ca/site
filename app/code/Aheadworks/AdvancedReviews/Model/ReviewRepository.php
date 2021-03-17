<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Model;

use Aheadworks\AdvancedReviews\Api\ReviewRepositoryInterface;
use Aheadworks\AdvancedReviews\Api\Data\ReviewInterface;
use Aheadworks\AdvancedReviews\Api\Data\ReviewInterfaceFactory;
use Aheadworks\AdvancedReviews\Api\Data\ReviewSearchResultsInterface;
use Aheadworks\AdvancedReviews\Api\Data\ReviewSearchResultsInterfaceFactory;
use Aheadworks\AdvancedReviews\Model\Review as ReviewModel;
use Aheadworks\AdvancedReviews\Model\ResourceModel\Review as ReviewResourceModel;
use Aheadworks\AdvancedReviews\Model\ResourceModel\Review\Collection as ReviewCollection;
use Aheadworks\AdvancedReviews\Model\ResourceModel\Review\CollectionFactory as ReviewCollectionFactory;
use Aheadworks\AdvancedReviews\Model\Indexer\Statistics\Processor;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\CouldNotDeleteException;

/**
 * Class ReviewRepository
 * @package Aheadworks\AdvancedReviews\Model
 */
class ReviewRepository implements ReviewRepositoryInterface
{
    /**
     * @var ReviewResourceModel
     */
    private $resource;

    /**
     * @var ReviewInterfaceFactory
     */
    private $reviewInterfaceFactory;

    /**
     * @var ReviewCollectionFactory
     */
    private $reviewCollectionFactory;

    /**
     * @var ReviewSearchResultsInterfaceFactory
     */
    private $searchResultsFactory;

    /**
     * @var JoinProcessorInterface
     */
    private $extensionAttributesJoinProcessor;

    /**
     * @var CollectionProcessorInterface
     */
    private $collectionProcessor;

    /**
     * @var DataObjectHelper
     */
    private $dataObjectHelper;

    /**
     * @var Processor
     */
    private $indexProcessor;

    /**
     * @var array
     */
    private $reviewInstances = [];

    /**
     * @param ReviewResourceModel $resource
     * @param ReviewInterfaceFactory $reviewInterfaceFactory
     * @param ReviewCollectionFactory $reviewCollectionFactory
     * @param ReviewSearchResultsInterfaceFactory $searchResultsFactory
     * @param JoinProcessorInterface $extensionAttributesJoinProcessor
     * @param CollectionProcessorInterface $collectionProcessor
     * @param DataObjectHelper $dataObjectHelper
     * @param Processor $indexProcessor
     */
    public function __construct(
        ReviewResourceModel $resource,
        ReviewInterfaceFactory $reviewInterfaceFactory,
        ReviewCollectionFactory $reviewCollectionFactory,
        ReviewSearchResultsInterfaceFactory $searchResultsFactory,
        JoinProcessorInterface $extensionAttributesJoinProcessor,
        CollectionProcessorInterface $collectionProcessor,
        DataObjectHelper $dataObjectHelper,
        Processor $indexProcessor
    ) {
        $this->resource = $resource;
        $this->reviewInterfaceFactory = $reviewInterfaceFactory;
        $this->reviewCollectionFactory = $reviewCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->extensionAttributesJoinProcessor = $extensionAttributesJoinProcessor;
        $this->collectionProcessor = $collectionProcessor;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->indexProcessor = $indexProcessor;
    }

    /**
     * {@inheritdoc}
     */
    public function save(ReviewInterface $review)
    {
        try {
            $this->resource->save($review);
            $this->reviewInstances[$review->getId()] = $review;
            $this->indexProcessor->reindexRow($review->getProductId(), true);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__($exception->getMessage()));
        }

        return $review;
    }

    /**
     * {@inheritdoc}
     */
    public function delete(ReviewInterface $review)
    {
        try {
            $productId = $review->getProductId();
            $this->resource->delete($review);
            $this->indexProcessor->reindexRow($productId, true);
            unset($this->reviewInstances[$review->getId()]);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__($exception->getMessage()));
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function getById($reviewId, $isForceLoadEnabled = false)
    {
        if ($isForceLoadEnabled || !isset($this->reviewInstances[$reviewId])) {
            /** @var ReviewInterface $review */
            $review = $this->reviewInterfaceFactory->create();
            $this->resource->load($review, $reviewId);
            if (!$review->getId()) {
                throw NoSuchEntityException::singleField('id', $reviewId);
            }
            $this->reviewInstances[$reviewId] = $review;
        }
        return $this->reviewInstances[$reviewId];
    }

    /**
     * {@inheritdoc}
     */
    public function getList(SearchCriteriaInterface $searchCriteria)
    {
        /** @var ReviewCollection $collection */
        $collection = $this->reviewCollectionFactory->create();

        $this->extensionAttributesJoinProcessor->process($collection, ReviewInterface::class);
        $this->collectionProcessor->process($searchCriteria, $collection);

        /** @var ReviewSearchResultsInterface $searchResults */
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);
        $searchResults->setTotalCount($collection->getSize());

        $objects = [];
        /** @var ReviewModel $item */
        foreach ($collection->getItems() as $item) {
            $objects[] = $this->getDataObject($item);
        }
        $searchResults->setItems($objects);

        return $searchResults;
    }

    /**
     * Retrieves data object using model
     *
     * @param ReviewModel $model
     * @return ReviewInterface
     */
    private function getDataObject($model)
    {
        /** @var ReviewInterface $object */
        $object = $this->reviewInterfaceFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $object,
            $model->getData(),
            ReviewInterface::class
        );
        return $object;
    }
}
