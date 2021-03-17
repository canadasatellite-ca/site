<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Model\ResourceModel\Statistics;

use Aheadworks\AdvancedReviews\Model\ResourceModel\Review\Frontend\CollectionFactory as FrontendReviewCollectionFactory;
use Aheadworks\AdvancedReviews\Api\Data\ReviewInterface;

/**
 * Class ProductRecommended
 *
 * @package Aheadworks\AdvancedReviews\Model\ResourceModel\Statistics
 */
class ProductRecommended
{
    /**
     * @var FrontendReviewCollectionFactory
     */
    private $frontendReviewCollectionFactory;

    /**
     * @param FrontendReviewCollectionFactory $frontendReviewCollectionFactory
     */
    public function __construct(
        FrontendReviewCollectionFactory $frontendReviewCollectionFactory
    ) {
        $this->frontendReviewCollectionFactory = $frontendReviewCollectionFactory;
    }

    /**
     * Retrieve count of reviews with specified product recommended values
     *
     * @param int $productId
     * @param int $storeId
     * @param int $productRecommendedValue
     * @return int
     */
    public function getReviewsCount($productId, $storeId, $productRecommendedValue)
    {
        $reviewCollection = $this->frontendReviewCollectionFactory->create();
        $reviewCollection
            ->addFieldToFilter(ReviewInterface::PRODUCT_ID, $productId)
            ->addStoreFilter($storeId)
            ->addFieldToFilter(ReviewInterface::PRODUCT_RECOMMENDED, $productRecommendedValue);

        $reviewsCount = $reviewCollection->getSize();

        return $reviewsCount;
    }
}
