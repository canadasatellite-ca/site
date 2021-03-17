<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Ui\DataProvider\Review\Modifier\Frontend\StaticRenderer;

use Aheadworks\AdvancedReviews\Ui\DataProvider\Review\Modifier\Frontend\ProductRecommended
    as FrontendProductRecommendedModifier;
use Aheadworks\AdvancedReviews\Model\Source\Review\ProductRecommended as ProductRecommendedSource;

/**
 * Class ProductRecommended
 *
 * @package Aheadworks\AdvancedReviews\Ui\DataProvider\Review\Modifier\Frontend\StaticRenderer
 */
class ProductRecommended extends FrontendProductRecommendedModifier
{
    /**
     * {@inheritdoc}
     */
    protected function getLabelsConfig()
    {
        return [
            ProductRecommendedSource::NO => __("I don't recommend this product"),
            ProductRecommendedSource::YES => __("I recommend this product"),
        ];
    }
}
