<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Model\Email\Subscriber;

use Aheadworks\AdvancedReviews\Api\EmailSubscriberRepositoryInterface;
use Aheadworks\AdvancedReviews\Api\Data\Email\SubscriberInterface;
use Aheadworks\AdvancedReviews\Api\Data\Email\SubscriberInterfaceFactory;
use Aheadworks\AdvancedReviews\Api\Data\Email\SubscriberSearchResultsInterface;
use Aheadworks\AdvancedReviews\Api\Data\Email\SubscriberSearchResultsInterfaceFactory;
use Aheadworks\AdvancedReviews\Model\Email\Subscriber;
use Aheadworks\AdvancedReviews\Model\ResourceModel\Email\Subscriber as SubscriberResourceModel;
use Aheadworks\AdvancedReviews\Model\ResourceModel\Email\Subscriber\Collection as SubscriberCollection;
use Aheadworks\AdvancedReviews\Model\ResourceModel\Email\Subscriber\CollectionFactory as SubscriberCollectionFactory;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\DataObjectHelper;

/**
 * Class Repository
 *
 * @package Aheadworks\AdvancedReviews\Model\Email\Subscriber
 */
class Repository implements EmailSubscriberRepositoryInterface
{
    /**
     * @var SubscriberInterfaceFactory
     */
    private $subscriberInterfaceFactory;

    /**
     * @var SubscriberResourceModel
     */
    private $subscriberResourceModel;

    /**
     * @var SubscriberSearchResultsInterfaceFactory
     */
    private $subscriberSearchResultsFactory;

    /**
     * @var SubscriberCollectionFactory
     */
    private $subscriberCollectionFactory;

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
     * @param SubscriberInterfaceFactory $subscriberInterfaceFactory
     * @param SubscriberResourceModel $subscriberResourceModel
     * @param SubscriberSearchResultsInterfaceFactory $subscriberSearchResultsFactory
     * @param SubscriberCollectionFactory $subscriberCollectionFactory
     * @param JoinProcessorInterface $extensionAttributesJoinProcessor
     * @param CollectionProcessorInterface $collectionProcessor
     * @param DataObjectHelper $dataObjectHelper
     */
    public function __construct(
        SubscriberInterfaceFactory $subscriberInterfaceFactory,
        SubscriberResourceModel $subscriberResourceModel,
        SubscriberSearchResultsInterfaceFactory $subscriberSearchResultsFactory,
        SubscriberCollectionFactory $subscriberCollectionFactory,
        JoinProcessorInterface $extensionAttributesJoinProcessor,
        CollectionProcessorInterface $collectionProcessor,
        DataObjectHelper $dataObjectHelper
    ) {
        $this->subscriberInterfaceFactory = $subscriberInterfaceFactory;
        $this->subscriberResourceModel = $subscriberResourceModel;
        $this->subscriberSearchResultsFactory = $subscriberSearchResultsFactory;
        $this->subscriberCollectionFactory = $subscriberCollectionFactory;
        $this->extensionAttributesJoinProcessor = $extensionAttributesJoinProcessor;
        $this->collectionProcessor = $collectionProcessor;
        $this->dataObjectHelper = $dataObjectHelper;
    }

    /**
     * {@inheritdoc}
     */
    public function save(SubscriberInterface $subscriber)
    {
        try {
            $this->subscriberResourceModel->save($subscriber);
            $this->instances[$subscriber->getId()] = $subscriber;
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__($exception->getMessage()));
        }
        return $subscriber;
    }

    /**
     * {@inheritdoc}
     */
    public function delete(SubscriberInterface $subscriber)
    {
        try {
            $this->subscriberResourceModel->delete($subscriber);
            unset($this->instances[$subscriber->getId()]);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__($exception->getMessage()));
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function getById($subscriberId)
    {
        if (!isset($this->instances[$subscriberId])) {
            /** @var SubscriberInterface $subscriber */
            $subscriber = $this->subscriberInterfaceFactory->create();
            $this->subscriberResourceModel->load($subscriber, $subscriberId);
            if (!$subscriber->getId()) {
                throw NoSuchEntityException::singleField('id', $subscriberId);
            }
            $this->instances[$subscriberId] = $subscriber;
        }
        return $this->instances[$subscriberId];
    }

    /**
     * {@inheritdoc}
     */
    public function getList(SearchCriteriaInterface $searchCriteria)
    {
        /** @var SubscriberCollection $collection */
        $collection = $this->subscriberCollectionFactory->create();

        $this->extensionAttributesJoinProcessor->process($collection, SubscriberInterface::class);
        $this->collectionProcessor->process($searchCriteria, $collection);

        /** @var SubscriberSearchResultsInterface $searchResults */
        $searchResults = $this->subscriberSearchResultsFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);
        $searchResults->setTotalCount($collection->getSize());

        $objects = [];
        /** @var Subscriber $item */
        foreach ($collection->getItems() as $item) {
            $objects[] = $this->getDataObject($item);
        }

        $searchResults->setItems($objects);

        return $searchResults;
    }

    /**
     * Retrieves data object using model
     *
     * @param Subscriber $model
     * @return SubscriberInterface
     */
    private function getDataObject($model)
    {
        /** @var SubscriberInterface $object */
        $object = $this->subscriberInterfaceFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $object,
            $model->getData(),
            SubscriberInterface::class
        );
        return $object;
    }
}
