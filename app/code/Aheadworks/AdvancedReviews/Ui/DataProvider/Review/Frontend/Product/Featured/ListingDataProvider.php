<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Ui\DataProvider\Review\Frontend\Product\Featured;

use Aheadworks\AdvancedReviews\Ui\DataProvider\Review\Frontend\Product\ListingDataProvider
    as AWListingDataProvider;
use Aheadworks\AdvancedReviews\Api\Data\ReviewInterface;

/**
 * Class ListingDataProvider
 *
 * @package Aheadworks\AdvancedReviews\Ui\DataProvider\Review\Frontend\Product\Featured
 */
class ListingDataProvider extends AWListingDataProvider
{
    /**
     * Direction of column sorting featured reviews list
     */
    const SORTING_DIRECTION = 'DESC';

    /**
     * Value for is_featured field if review is marked as featured
     */
    const REVIEW_MARKED_AS_FEATURED = 1;

    /**
     * Count of featured reviews in the listing
     */
    const REVIEWS_COUNT = 5;

    /**
     * {@inheritdoc}
     */
    protected function applyDefaultFilters()
    {
        $this->getCollection()
            ->addFieldToFilter(ReviewInterface::IS_FEATURED, self::REVIEW_MARKED_AS_FEATURED)
            ->setOrder(ReviewInterface::VOTES_POSITIVE, self::SORTING_DIRECTION)
            ->setOrder(ReviewInterface::CREATED_AT, self::SORTING_DIRECTION)
            ->setPageSize(self::REVIEWS_COUNT)
            ->setCurPage(1);
        parent::applyDefaultFilters();
    }
}
