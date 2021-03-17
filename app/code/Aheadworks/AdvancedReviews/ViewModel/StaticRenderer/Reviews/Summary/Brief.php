<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\ViewModel\StaticRenderer\Reviews\Summary;

use Magento\Framework\View\Element\Block\ArgumentInterface;
use Aheadworks\AdvancedReviews\Model\Product\Layout\Processor\Review\BriefSummaryDataProvider;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Class Brief
 *
 * @package Aheadworks\AdvancedReviews\ViewModel\StaticRenderer\Reviews\Summary
 */
class Brief implements ArgumentInterface
{
    /**
     * @var BriefSummaryDataProvider
     */
    private $briefSummaryDataProvider;

    /**
     * @var array
     */
    private $briefSummaryData = [];

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @param BriefSummaryDataProvider $briefSummaryDataProvider
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        BriefSummaryDataProvider $briefSummaryDataProvider,
        StoreManagerInterface $storeManager
    ) {
        $this->briefSummaryDataProvider = $briefSummaryDataProvider;
        $this->storeManager = $storeManager;
    }

    /**
     * Retrieve aggregated rating absolute value
     *
     * @return string
     */
    public function getAggregatedRatingAbsoluteValue()
    {
        if (empty($this->briefSummaryData)) {
            $this->briefSummaryData = $this->briefSummaryDataProvider->getBriefSummaryData(
                null,
                $this->getCurrentStoreId()
            );
        }
        return isset($this->briefSummaryData['aggregated_rating_absolute'])
            ? $this->briefSummaryData['aggregated_rating_absolute']
            : '';
    }

    /**
     * Retrieve reviews count
     *
     * @return string
     */
    public function getReviewsCount()
    {
        if (empty($this->briefSummaryData)) {
            $this->briefSummaryData = $this->briefSummaryDataProvider->getBriefSummaryData(
                null,
                $this->getCurrentStoreId()
            );
        }
        return isset($this->briefSummaryData['reviews_count'])
            ? $this->briefSummaryData['reviews_count']
            : '';
    }

    /**
     * Retrieve aggregated rating title
     *
     * @return string
     */
    public function getAggregatedRatingTitle()
    {
        if (empty($this->briefSummaryData)) {
            $this->briefSummaryData = $this->briefSummaryDataProvider->getBriefSummaryData(
                null,
                $this->getCurrentStoreId()
            );
        }
        return isset($this->briefSummaryData['aggregated_rating_title'])
            ? $this->briefSummaryData['aggregated_rating_title']
            : $this->getAggregatedRatingPercentValue() . '%';
    }

    /**
     * Retrieve aggregated rating percent value
     *
     * @return string
     */
    public function getAggregatedRatingPercentValue()
    {
        if (empty($this->briefSummaryData)) {
            $this->briefSummaryData = $this->briefSummaryDataProvider->getBriefSummaryData(
                null,
                $this->getCurrentStoreId()
            );
        }
        return isset($this->briefSummaryData['aggregated_rating_percent'])
            ? $this->briefSummaryData['aggregated_rating_percent']
            : '';
    }

    /**
     * Retrieve current store id
     *
     * @return int|null
     */
    private function getCurrentStoreId()
    {
        try {
            $currentStoreId = $this->storeManager->getStore(true)->getId();
        } catch (NoSuchEntityException $exception) {
            $currentStoreId = null;
        }
        return $currentStoreId;
    }
}
