<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Ui\DataProvider\Review\Frontend\Product;

use Aheadworks\AdvancedReviews\Ui\DataProvider\Review\Frontend\ListingDataProvider
    as ReviewFrontendListingDataProvider;
use Aheadworks\AdvancedReviews\Api\Data\ReviewInterface;

/**
 * Class ListingDataProvider
 *
 * @package Aheadworks\AdvancedReviews\Ui\DataProvider\Review\Frontend\Product
 */
class ListingDataProvider extends ReviewFrontendListingDataProvider
{
    /**
     * {@inheritdoc}
     */
    protected function applyFiltersFromRequest()
    {
        if ($productId = $this->request->getParam(ReviewInterface::PRODUCT_ID, 0)) {
            $this->getCollection()->addFieldToFilter(ReviewInterface::PRODUCT_ID, $productId);
        }
        return $this;
    }
}
