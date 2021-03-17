<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Model\Product\Layout\Processor\Review;

use Aheadworks\AdvancedReviews\Model\Layout\LayoutProcessorInterface;
use Magento\Framework\Stdlib\ArrayManager;
use Aheadworks\AdvancedReviews\Model\ResourceModel\Statistics\Rating as RatingStatistics;
use Aheadworks\AdvancedReviews\Model\Product\Layout\Processor\Review\DetailedSummary\RatingOptionProvider;

/**
 * Class DetailedSummaryDataProvider
 *
 * @package Aheadworks\AdvancedReviews\Model\Product\Layout\Processor\Review
 */
class DetailedSummaryDataProvider implements LayoutProcessorInterface
{
    /**
     * @var ArrayManager
     */
    private $arrayManager;

    /**
     * @var RatingStatistics
     */
    private $ratingStatistics;

    /**
     * @var RatingOptionProvider
     */
    private $ratingOptionProvider;

    /**
     * @param ArrayManager $arrayManager
     * @param RatingStatistics $ratingStatistics
     * @param RatingOptionProvider $ratingOptionProvider
     */
    public function __construct(
        ArrayManager $arrayManager,
        RatingStatistics $ratingStatistics,
        RatingOptionProvider $ratingOptionProvider
    ) {
        $this->arrayManager = $arrayManager;
        $this->ratingStatistics = $ratingStatistics;
        $this->ratingOptionProvider = $ratingOptionProvider;
    }

    /**
     * {@inheritdoc}
     */
    public function process($jsLayout, $productId = null, $storeId = null)
    {
        $reviewDetailedSummaryProviderPath = 'components/awArReviewDetailedSummaryProvider';
        $jsLayout = $this->arrayManager->merge(
            $reviewDetailedSummaryProviderPath,
            $jsLayout,
            [
                'data' => $this->getDetailedSummaryData($productId, $storeId)
            ]
        );

        return $jsLayout;
    }

    /**
     * Retrieve detailed summary data for specified product
     *
     * @param int|null $productId
     * @param int|null $storeId
     * @return array
     */
    public function getDetailedSummaryData($productId = null, $storeId = null)
    {
        $detailedSummaryData = [];
        $ratingOptions = $this->ratingOptionProvider->getRatingOptions();
        $totalReviewsCount = $this->ratingStatistics->getReviewsCount(
            $productId,
            $storeId
        );
        if ($totalReviewsCount) {
            foreach ($ratingOptions as $ratingValue => $ratingLabel) {
                $reviewsCountForRatingValue = $this->ratingStatistics->getReviewsCount(
                    $productId,
                    $storeId,
                    $ratingValue
                );
                $detailedSummaryData[] = [
                    'value' => $ratingValue,
                    'label' => $ratingLabel,
                    'reviews_count' => $reviewsCountForRatingValue,
                    'reviews_percent' => round($reviewsCountForRatingValue / $totalReviewsCount * 100.0),
                ];
            }
        }
        return $detailedSummaryData;
    }
}
