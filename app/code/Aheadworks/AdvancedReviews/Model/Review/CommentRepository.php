<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Model\Review;

use Aheadworks\AdvancedReviews\Api\CommentRepositoryInterface;
use Aheadworks\AdvancedReviews\Api\Data\CommentInterface;
use Aheadworks\AdvancedReviews\Api\Data\CommentInterfaceFactory;
use Aheadworks\AdvancedReviews\Model\ResourceModel\Review\Comment as CommentResourceModel;
use Aheadworks\AdvancedReviews\Api\Data\CommentSearchResultsInterface;
use Aheadworks\AdvancedReviews\Api\Data\CommentSearchResultsInterfaceFactory;
use Aheadworks\AdvancedReviews\Model\Review\Comment as CommentModel;
use Aheadworks\AdvancedReviews\Model\ResourceModel\Review\Comment\Collection as CommentCollection;
use Aheadworks\AdvancedReviews\Model\ResourceModel\Review\Comment\CollectionFactory as CommentCollectionFactory;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;

/**
 * Class CommentRepository
 * @package Aheadworks\AdvancedReviews\Model\Review
 */
class CommentRepository implements CommentRepositoryInterface
{
    /**
     * @var CommentResourceModel
     */
    private $resource;

    /**
     * @var CommentInterfaceFactory
     */
    private $commentInterfaceFactory;

    /**
     * @var CommentCollectionFactory
     */
    private $commentCollectionFactory;

    /**
     * @var CommentSearchResultsInterfaceFactory
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
     * @var array
     */
    private $commentInstances = [];

    public function __construct(
        CommentResourceModel $resource,
        CommentInterfaceFactory $commentInterfaceFactory,
        CommentCollectionFactory $commentCollectionFactory,
        CommentSearchResultsInterfaceFactory $searchResultsFactory,
        JoinProcessorInterface $extensionAttributesJoinProcessor,
        CollectionProcessorInterface $collectionProcessor,
        DataObjectHelper $dataObjectHelper
    ) {
        $this->resource = $resource;
        $this->commentInterfaceFactory = $commentInterfaceFactory;
        $this->commentCollectionFactory = $commentCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->extensionAttributesJoinProcessor = $extensionAttributesJoinProcessor;
        $this->collectionProcessor = $collectionProcessor;
        $this->dataObjectHelper = $dataObjectHelper;
    }

    /**
     * {@inheritdoc}
     */
    public function save(CommentInterface $comment)
    {
        try {
            $this->resource->save($comment);
            $this->commentInstances[$comment->getId()] = $comment;
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__($exception->getMessage()));
        }

        return $comment;
    }

    /**
     * {@inheritdoc}
     */
    public function delete(CommentInterface $comment)
    {
        try {
            $this->resource->delete($comment);
            unset($this->commentInstances[$comment->getId()]);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__($exception->getMessage()));
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function getById($commentId)
    {
        if (!isset($this->commentInstances[$commentId])) {
            /** @var CommentInterface $comment */
            $comment = $this->commentInterfaceFactory->create();
            $this->resource->load($comment, $commentId);
            if (!$comment->getId()) {
                throw NoSuchEntityException::singleField('id', $commentId);
            }
            $this->commentInstances[$commentId] = $comment;
        }
        return $this->commentInstances[$commentId];
    }

    /**
     * {@inheritdoc}
     */
    public function getIdsByReviewIds($reviewIds)
    {
        if ($reviewIds) {
            /** @var CommentCollection $collection */
            $collection = $this->commentCollectionFactory->create();
            $collection->addFieldToFilter(CommentInterface::REVIEW_ID, ['in' => $reviewIds]);

            return $collection->getColumnValues(CommentInterface::ID);
        }

        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function getList(SearchCriteriaInterface $searchCriteria)
    {
        /** @var CommentCollection $collection */
        $collection = $this->commentCollectionFactory->create();

        $this->extensionAttributesJoinProcessor->process($collection, CommentInterface::class);
        $this->collectionProcessor->process($searchCriteria, $collection);

        /** @var CommentSearchResultsInterface $searchResults */
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);
        $searchResults->setTotalCount($collection->getSize());

        $objects = [];
        /** @var CommentModel $item */
        foreach ($collection->getItems() as $item) {
            $objects[] = $this->getDataObject($item);
        }
        $searchResults->setItems($objects);

        return $searchResults;
    }

    /**
     * Retrieves data object using model
     *
     * @param CommentModel $model
     * @return CommentInterface
     */
    private function getDataObject($model)
    {
        /** @var CommentInterface $object */
        $object = $this->commentInterfaceFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $object,
            $model->getData(),
            CommentInterface::class
        );
        return $object;
    }
}
