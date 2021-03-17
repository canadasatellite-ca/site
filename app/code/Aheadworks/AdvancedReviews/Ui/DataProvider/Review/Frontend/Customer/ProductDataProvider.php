<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Ui\DataProvider\Review\Frontend\Customer;

use Aheadworks\AdvancedReviews\Ui\DataProvider\Review\Frontend\ProductDataProvider as BaseProductDataProvider;

/**
 * Class ProductDataProvider
 *
 * @package Aheadworks\AdvancedReviews\Ui\DataProvider\Review\Frontend\Customer
 */
class ProductDataProvider extends BaseProductDataProvider
{
    /**
     * {@inheritdoc}
     */
    protected function getProductUrl($product)
    {
        return $this->productResolver->getProductReviewUrlByObject($product);
    }
}
