<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\ViewModel\StaticRenderer\Reviews\Summary;

use Magento\Framework\View\Element\Block\ArgumentInterface;
use Aheadworks\AdvancedReviews\Model\Product\Layout\Processor\Review\DetailedSummaryDataProvider;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Class Detailed
 *
 * @package Aheadworks\AdvancedReviews\ViewModel\StaticRenderer\Reviews\Summary
 */
class Detailed implements ArgumentInterface
{
    /**
     * @var DetailedSummaryDataProvider
     */
    private $detailedSummaryDataProvider;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @param DetailedSummaryDataProvider $detailedSummaryDataProvider
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        DetailedSummaryDataProvider $detailedSummaryDataProvider,
        StoreManagerInterface $storeManager
    ) {
        $this->detailedSummaryDataProvider = $detailedSummaryDataProvider;
        $this->storeManager = $storeManager;
    }

    /**
     * Retrieve detailed summary data array
     *
     * @return array
     */
    public function getDetailedSummaryData()
    {
        return $this->detailedSummaryDataProvider->getDetailedSummaryData(null, $this->getCurrentStoreId());
    }

    /**
     * Retrieve rating label from summary data row
     *
     * @param array $detailedSummaryDataRow
     * @return string
     */
    public function getRatingLabel($detailedSummaryDataRow)
    {
        return isset($detailedSummaryDataRow['label'])
            ? $detailedSummaryDataRow['label']
            : '';
    }

    /**
     * Retrieve rating reviews count from summary data row
     *
     * @param array $detailedSummaryDataRow
     * @return string
     */
    public function getRatingReviewsCount($detailedSummaryDataRow)
    {
        return isset($detailedSummaryDataRow['reviews_count'])
            ? $detailedSummaryDataRow['reviews_count']
            : '';
    }

    /**
     * Retrieve rating reviews percent from summary data row
     *
     * @param array $detailedSummaryDataRow
     * @return string
     */
    public function getRatingReviewsPercent($detailedSummaryDataRow)
    {
        return isset($detailedSummaryDataRow['reviews_percent'])
            ? $detailedSummaryDataRow['reviews_percent']
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
