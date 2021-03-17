<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Ui\DataProvider\Review\Frontend\Customer\RecentReview;

use Aheadworks\AdvancedReviews\Ui\DataProvider\Review\Frontend\Customer\ListingDataProvider
    as ReviewFrontendCustomerListingDataProvider;
use Aheadworks\AdvancedReviews\Api\Data\ReviewInterface;

/**
 * Class ListingDataProvider
 *
 * @package Aheadworks\AdvancedReviews\Ui\DataProvider\Review\Frontend\Customer\RecentReview
 */
class ListingDataProvider extends ReviewFrontendCustomerListingDataProvider
{
    /**
     * Count of recent reviews in the customer's recent reviews listing
     */
    const REVIEWS_COUNT = 5;

    /**
     * Direction of column sorting in the customer's recent reviews list
     */
    const SORTING_DIRECTION = 'DESC';

    /**
     * {@inheritdoc}
     */
    protected function applyDefaultFilters()
    {
        parent::applyDefaultFilters();
        $this->getCollection()
            ->setPageSize(self::REVIEWS_COUNT)
            ->setOrder(ReviewInterface::CREATED_AT, self::SORTING_DIRECTION)
            ->setCurPage(1);
        return $this;
    }
}
