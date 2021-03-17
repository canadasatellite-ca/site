<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Ui\Component\Review\Listing\MassAction\Source;

use Magento\Framework\Data\OptionSourceInterface;
use Aheadworks\AdvancedReviews\Model\Source\Review\ProductRecommended as ProductRecommendedSource;

/**
 * Class ProductRecommended
 *
 * @package Aheadworks\AdvancedReviews\Ui\Component\Review\Listing\MassAction\Source
 */
class ProductRecommended implements OptionSourceInterface
{
    /**
     * @var array
     */
    protected $options;

    /**
     * Get options
     *
     * @return array
     */
    public function toOptionArray()
    {
        if ($this->options !== null) {
            return $this->options;
        }

        $this->options = [
            [
                'value' => ProductRecommendedSource::NO,
                'label' => __("I don't recommend this product")
            ],
            [
                'value' => ProductRecommendedSource::YES,
                'label' => __("I recommend this product")
            ]
        ];

        return $this->options;
    }
}
