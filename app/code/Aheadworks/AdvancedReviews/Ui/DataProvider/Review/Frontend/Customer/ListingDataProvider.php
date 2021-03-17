<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Ui\DataProvider\Review\Frontend\Customer;

use Aheadworks\AdvancedReviews\Ui\DataProvider\Review\Frontend\ListingDataProvider
    as ReviewFrontendListingDataProvider;
use Aheadworks\AdvancedReviews\Api\Data\ReviewInterface;

/**
 * Class ListingDataProvider
 *
 * @package Aheadworks\AdvancedReviews\Ui\DataProvider\Review\Frontend\Customer
 */
class ListingDataProvider extends ReviewFrontendListingDataProvider
{
    /**
     * {@inheritdoc}
     */
    protected function createReviewCollection($frontendReviewCollectionFactory)
    {
        return $frontendReviewCollectionFactory->create([], false);
    }

    /**
     * {@inheritdoc}
     */
    protected function applyFiltersFromRequest()
    {
        if ($customerId = $this->request->getParam(ReviewInterface::CUSTOMER_ID, 0)) {
            $this->getCollection()->addFieldToFilter(ReviewInterface::CUSTOMER_ID, $customerId);
        }
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    protected function applyDefaultFilters()
    {
        parent::applyDefaultFilters();
        $this->getCollection()->setNeedTruncateReviewSummary(true);
    }
}
