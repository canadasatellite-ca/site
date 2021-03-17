<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Model;

use Aheadworks\AdvancedReviews\Api\Data\QueueItemInterface;
use Aheadworks\AdvancedReviews\Api\Data\QueueItemInterfaceFactory;
use Aheadworks\AdvancedReviews\Api\Data\QueueItemSearchResultsInterface;
use Aheadworks\AdvancedReviews\Api\Data\QueueItemSearchResultsInterfaceFactory;
use Aheadworks\AdvancedReviews\Api\QueueRepositoryInterface;
use Aheadworks\AdvancedReviews\Api\OrderHistoryRepositoryInterface;
use Aheadworks\AdvancedReviews\Model\QueueItem;
use Aheadworks\AdvancedReviews\Model\ResourceModel\Queue as QueueResource;
use Aheadworks\AdvancedReviews\Model\ResourceModel\Queue\Collection as QueueCollection;
use Aheadworks\AdvancedReviews\Model\ResourceModel\Queue\CollectionFactory as QueueCollectionFactory;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\DataObjectHelper;

/**
 * Class QueueRepository
 * @package Aheadworks\AdvancedReviews\Model
 */
class QueueRepository implements QueueRepositoryInterface
{
    /**
     * @var QueueItemInterfaceFactory
     */
    private $queueItemFactory;

    /**
     * @var QueueResource
     */
    private $queueResource;

    /**
     * @var QueueItemSearchResultsInterfaceFactory
     */
    private $queueItemsSearchResultsFactory;

    /**
     * @var QueueCollectionFactory
     */
    private $queueCollectionFactory;

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
    private $instances = [];

    /**
     * QueueRepository constructor.
     * @param QueueItemInterfaceFactory $queueItemFactory
     * @param QueueResource $queueResource
     * @param QueueItemSearchResultsInterfaceFactory $queueItemsSearchResultsFactory
     * @param QueueCollectionFactory $queueCollectionFactory
     * @param JoinProcessorInterface $extensionAttributesJoinProcessor
     * @param CollectionProcessorInterface $collectionProcessor
     * @param DataObjectHelper $dataObjectHelper
     */
    public function __construct(
        QueueItemInterfaceFactory $queueItemFactory,
        QueueResource $queueResource,
        QueueItemSearchResultsInterfaceFactory $queueItemsSearchResultsFactory,
        QueueCollectionFactory $queueCollectionFactory,
        JoinProcessorInterface $extensionAttributesJoinProcessor,
        CollectionProcessorInterface $collectionProcessor,
        DataObjectHelper $dataObjectHelper
    ) {
        $this->queueItemFactory = $queueItemFactory;
        $this->queueResource = $queueResource;
        $this->queueItemsSearchResultsFactory = $queueItemsSearchResultsFactory;
        $this->queueCollectionFactory = $queueCollectionFactory;
        $this->extensionAttributesJoinProcessor = $extensionAttributesJoinProcessor;
        $this->collectionProcessor = $collectionProcessor;
        $this->dataObjectHelper = $dataObjectHelper;
    }

    /**
     * {@inheritdoc}
     */
    public function save(QueueItemInterface $queue)
    {
        try {
            $this->queueResource->save($queue);
            $this->instances[$queue->getId()] = $queue;
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__($exception->getMessage()));
        }
        return $queue;
    }

    /**
     * {@inheritdoc}
     */
    public function delete(QueueItemInterface $queue)
    {
        try {
            $this->queueResource->delete($queue);
            unset($this->instances[$queue->getId()]);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__($exception->getMessage()));
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById($queueId)
    {
        $queue = $this->getById($queueId);
        $result = $this->delete($queue);
        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function getById($queueId)
    {
        if (!isset($this->instances[$queueId])) {
            /** @var QueueItemInterface $queue */
            $queue = $this->queueItemFactory->create();
            $this->queueResource->load($queue, $queueId);
            if (!$queue->getId()) {
                throw NoSuchEntityException::singleField('id', $queueId);
            }
            $this->instances[$queueId] = $queue;
        }
        return $this->instances[$queueId];
    }

    /**
     * {@inheritdoc}
     */
    public function getList(SearchCriteriaInterface $searchCriteria)
    {
        /** @var QueueCollection $collection */
        $collection = $this->queueCollectionFactory->create();

        $this->extensionAttributesJoinProcessor->process($collection, QueueItemInterface::class);
        $this->collectionProcessor->process($searchCriteria, $collection);

        /** @var QueueItemSearchResultsInterface $searchResults */
        $searchResults = $this->queueItemsSearchResultsFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);
        $searchResults->setTotalCount($collection->getSize());

        $objects = [];
        /** @var QueueItem $item */
        foreach ($collection->getItems() as $item) {
            $objects[] = $this->getDataObject($item);
        }

        $searchResults->setItems($objects);

        return $searchResults;
    }

    /**
     * Retrieves data object using model
     *
     * @param QueueItem $model
     * @return QueueItemInterface
     */
    private function getDataObject($model)
    {
        /** @var QueueItemInterface $object */
        $object = $this->queueItemFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $object,
            $model->getData(),
            QueueItemInterface::class
        );
        return $object;
    }
}
