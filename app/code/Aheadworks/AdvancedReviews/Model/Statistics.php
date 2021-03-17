<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Model;

use Aheadworks\AdvancedReviews\Api\Data\StatisticsInterface;
use Magento\Framework\Model\AbstractModel;

/**
 * Class Statistics
 * @package Aheadworks\AdvancedReviews\Model
 */
class Statistics extends AbstractModel implements StatisticsInterface
{
    /**
     * {@inheritdoc}
     */
    public function getStoreId()
    {
        return $this->getData(self::STORE_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function getProductId()
    {
        return $this->getData(self::PRODUCT_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function getReviewsCount()
    {
        return $this->getData(self::REVIEWS_COUNT);
    }

    /**
     * {@inheritdoc}
     */
    public function getAggregatedRating()
    {
        return $this->getData(self::AGGREGATED_RATING);
    }
}
