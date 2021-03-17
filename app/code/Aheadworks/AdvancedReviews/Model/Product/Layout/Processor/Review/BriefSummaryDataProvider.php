<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Model\Product\Layout\Processor\Review;

use Aheadworks\AdvancedReviews\Model\Layout\LayoutProcessorInterface;
use Magento\Framework\Stdlib\ArrayManager;
use Aheadworks\AdvancedReviews\Api\StatisticsRepositoryInterface;
use Aheadworks\AdvancedReviews\Model\Review\Rating\Resolver as RatingResolver;
use Aheadworks\AdvancedReviews\Model\Source\Review\ProductRecommended as ProductRecommendedSource;
use Aheadworks\AdvancedReviews\Model\ResourceModel\Statistics\ProductRecommended as ProductRecommendedStatistics;

/**
 * Class BriefSummaryDataProvider
 *
 * @package Aheadworks\AdvancedReviews\Model\Product\Layout\Processor\Review
 */
class BriefSummaryDataProvider implements LayoutProcessorInterface
{
    /**
     * @var ArrayManager
     */
    private $arrayManager;

    /**
     * @var StatisticsRepositoryInterface
     */
    private $statisticsRepository;

    /**
     * @var RatingResolver
     */
    private $ratingResolver;

    /**
     * @var ProductRecommendedStatistics
     */
    private $productRecommendedStatistics;

    /**
     * @param ArrayManager $arrayManager
     * @param StatisticsRepositoryInterface $statisticsRepository
     * @param RatingResolver $ratingResolver
     * @param ProductRecommendedStatistics $productRecommendedStatistics
     */
    public function __construct(
        ArrayManager $arrayManager,
        StatisticsRepositoryInterface $statisticsRepository,
        RatingResolver $ratingResolver,
        ProductRecommendedStatistics $productRecommendedStatistics
    ) {
        $this->arrayManager = $arrayManager;
        $this->statisticsRepository = $statisticsRepository;
        $this->ratingResolver = $ratingResolver;
        $this->productRecommendedStatistics = $productRecommendedStatistics;
    }

    /**
     * {@inheritdoc}
     */
    public function process($jsLayout, $productId = null, $storeId = null)
    {
        $reviewBriefSummaryProviderPath = 'components/awArReviewBriefSummaryProvider';
        $jsLayout = $this->arrayManager->merge(
            $reviewBriefSummaryProviderPath,
            $jsLayout,
            [
                'data' => $this->getBriefSummaryData($productId, $storeId)
            ]
        );

        return $jsLayout;
    }

    /**
     * Retrieve brief summary data for specified product
     *
     * @param int|null $productId
     * @param int|null $storeId
     * @return array
     */
    public function getBriefSummaryData($productId = null, $storeId = null)
    {
        $statisticsInstance = $this->statisticsRepository->getByProductId($productId, $storeId);
        $briefSummaryData = [
            'reviews_count' => $statisticsInstance->getReviewsCount(),
            'aggregated_rating_percent' => $statisticsInstance->getAggregatedRating(),
            'aggregated_rating_absolute' => $this->ratingResolver->getRatingAbsoluteValue(
                $statisticsInstance->getAggregatedRating()
            ),
            'aggregated_rating_title' => $this->ratingResolver->getRatingTitle(
                $statisticsInstance->getAggregatedRating()
            ),
            'customer_recommended_percent' => $this->getCustomersRecommendedPercent(
                $statisticsInstance->getReviewsCount(),
                $productId,
                $storeId
            ),
        ];

        return $briefSummaryData;
    }

    /**
     * Calculate percent of customers who recommend current product
     *
     * @param int $reviewsCount
     * @param int|null $productId
     * @param int|null $storeId
     * @return int
     */
    private function getCustomersRecommendedPercent($reviewsCount, $productId = null, $storeId = null)
    {
        $customersRecommendedPercent = 0;
        if ($reviewsCount && $productId) {
            $countOfReviewsProductRecommended = $this->productRecommendedStatistics->getReviewsCount(
                $productId,
                $storeId,
                ProductRecommendedSource::YES
            );
            $customersRecommendedPercent = round($countOfReviewsProductRecommended / $reviewsCount * 100);
        }
        return $customersRecommendedPercent;
    }
}
