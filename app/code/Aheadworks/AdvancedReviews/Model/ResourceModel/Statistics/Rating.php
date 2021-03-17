<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Model\ResourceModel\Statistics;

use Aheadworks\AdvancedReviews\Model\ResourceModel\Review\Frontend\CollectionFactory as FrontendReviewCollectionFactory;
use Aheadworks\AdvancedReviews\Api\Data\ReviewInterface;

/**
 * Class Rating
 *
 * @package Aheadworks\AdvancedReviews\Model\ResourceModel\Statistics
 */
class Rating
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
     * Retrieve count of reviews with specified rating for the product on the specific store
     *
     * @param int|null $productId
     * @param int|null $storeId
     * @param int|null $ratingValue
     * @return int
     */
    public function getReviewsCount($productId = null, $storeId = null, $ratingValue = null)
    {
        $reviewCollection = $this->frontendReviewCollectionFactory->create();
        if (isset($productId)) {
            $reviewCollection->addFieldToFilter(ReviewInterface::PRODUCT_ID, $productId);
        }
        if (isset($storeId)) {
            $reviewCollection->addStoreFilter($storeId);
        }
        if (isset($ratingValue)) {
            $reviewCollection->addFieldToFilter(ReviewInterface::RATING, $ratingValue);
        }

        $reviewsCount = $reviewCollection->getSize();

        return $reviewsCount;
    }
}
