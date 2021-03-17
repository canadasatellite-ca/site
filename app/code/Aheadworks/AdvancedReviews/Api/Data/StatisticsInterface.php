<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Api\Data;

/**
 * Interface StatisticsInterface
 * @package Aheadworks\AdvancedReviews\Api\Data
 */
interface StatisticsInterface
{
    /**#@+
     * Constants defined for keys of the data array.
     * Identical to the name of the getter in snake case
     */
    const STORE_ID = 'store_id';
    const PRODUCT_ID = 'product_id';
    const REVIEWS_COUNT = 'reviews_count';
    const AGGREGATED_RATING = 'aggregated_rating';
    /**#@-*/

    /**
     * Get product id
     *
     * @return int|null
     */
    public function getProductId();

    /**
     * Get store id
     *
     * @return int
     */
    public function getStoreId();

    /**
     * Get reviews count
     *
     * @return int
     */
    public function getReviewsCount();

    /**
     * Get aggregated rating
     *
     * @return int
     */
    public function getAggregatedRating();
}
