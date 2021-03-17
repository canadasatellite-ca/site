<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Api;

/**
 * Interface StatisticsRepositoryInterface
 *
 * @package Aheadworks\AdvancedReviews\Api
 */
interface StatisticsRepositoryInterface
{
    /**
     * Retrieve statistics for specified product
     *
     * @param int $productId
     * @param int|null $storeId
     * @return \Aheadworks\AdvancedReviews\Api\Data\StatisticsInterface
     */
    public function getByProductId($productId, $storeId = null);
}
