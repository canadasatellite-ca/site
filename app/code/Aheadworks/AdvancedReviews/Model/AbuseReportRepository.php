<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Model;

use Aheadworks\AdvancedReviews\Api\AbuseReportRepositoryInterface;
use Aheadworks\AdvancedReviews\Api\Data\AbuseReportInterface;
use Aheadworks\AdvancedReviews\Api\Data\AbuseReportInterfaceFactory;
use Aheadworks\AdvancedReviews\Model\ResourceModel\AbuseReport as AbuseReportResourceModel;
use Aheadworks\AdvancedReviews\Api\Data\AbuseReportSearchResultsInterface;
use Aheadworks\AdvancedReviews\Api\Data\AbuseReportSearchResultsInterfaceFactory;
use Aheadworks\AdvancedReviews\Model\AbuseReport as AbuseReportModel;
use Aheadworks\AdvancedReviews\Model\ResourceModel\AbuseReport\Collection as AbuseReportCollection;
use Aheadworks\AdvancedReviews\Model\ResourceModel\AbuseReport\CollectionFactory as AbuseReportCollectionFactory;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;

/**
 * Class AbuseReportRepository
 * @package Aheadworks\AdvancedReviews\Model
 */
class AbuseReportRepository implements AbuseReportRepositoryInterface
{
    /**
     * @var AbuseReportResourceModel
     */
    private $resource;

    /**
     * @var AbuseReportInterfaceFactory
     */
    private $abuseReportInterfaceFactory;

    /**
     * @var AbuseReportCollectionFactory
     */
    private $abuseReportCollectionFactory;

    /**
     * @var AbuseReportSearchResultsInterfaceFactory
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
    private $reportInstances = [];

    public function __construct(
        AbuseReportResourceModel $resource,
        AbuseReportInterfaceFactory $abuseReportInterfaceFactory,
        AbuseReportCollectionFactory $abuseReportCollectionFactory,
        AbuseReportSearchResultsInterfaceFactory $searchResultsFactory,
        JoinProcessorInterface $extensionAttributesJoinProcessor,
        CollectionProcessorInterface $collectionProcessor,
        DataObjectHelper $dataObjectHelper
    ) {
        $this->resource = $resource;
        $this->abuseReportInterfaceFactory = $abuseReportInterfaceFactory;
        $this->abuseReportCollectionFactory = $abuseReportCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->extensionAttributesJoinProcessor = $extensionAttributesJoinProcessor;
        $this->collectionProcessor = $collectionProcessor;
        $this->dataObjectHelper = $dataObjectHelper;
    }

    /**
     * {@inheritdoc}
     */
    public function save(AbuseReportInterface $report)
    {
        try {
            $this->resource->save($report);
            $this->reportInstances[$report->getId()] = $report;
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__($exception->getMessage()));
        }

        return $report;
    }

    /**
     * {@inheritdoc}
     */
    public function delete(AbuseReportInterface $report)
    {
        try {
            $this->resource->delete($report);
            unset($this->reportInstances[$report->getId()]);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__($exception->getMessage()));
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function getById($reportId)
    {
        if (!isset($this->reportInstances[$reportId])) {
            /** @var AbuseReportInterface $report */
            $report = $this->abuseReportInterfaceFactory->create();
            $this->resource->load($report, $reportId);
            if (!$report->getId()) {
                throw NoSuchEntityException::singleField('id', $reportId);
            }
            $this->reportInstances[$reportId] = $report;
        }
        return $this->reportInstances[$reportId];
    }

    /**
     * {@inheritdoc}
     */
    public function getByEntity($entityType, $entityId)
    {
        $reportId = $this->resource->getReportIdByEntity($entityType, $entityId);

        return $this->getById($reportId);
    }

    /**
     * {@inheritdoc}
     */
    public function ignoreForEntity($entityType, $ids)
    {
        if (!empty($ids)) {
            $this->resource->ignoreForEntity($entityType, $ids);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getList(SearchCriteriaInterface $searchCriteria)
    {
        /** @var AbuseReportCollection $collection */
        $collection = $this->abuseReportCollectionFactory->create();

        $this->extensionAttributesJoinProcessor->process($collection, AbuseReportInterface::class);
        $this->collectionProcessor->process($searchCriteria, $collection);

        /** @var AbuseReportSearchResultsInterface $searchResults */
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);
        $searchResults->setTotalCount($collection->getSize());

        $objects = [];
        /** @var AbuseReportModel $item */
        foreach ($collection->getItems() as $item) {
            $objects[] = $this->getDataObject($item);
        }
        $searchResults->setItems($objects);

        return $searchResults;
    }

    /**
     * Retrieves data object using model
     *
     * @param AbuseReportModel $model
     * @return AbuseReportInterface
     */
    private function getDataObject($model)
    {
        /** @var AbuseReportInterface $object */
        $object = $this->abuseReportInterfaceFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $object,
            $model->getData(),
            AbuseReportInterface::class
        );
        return $object;
    }
}
