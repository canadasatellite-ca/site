<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Model\Source\Review;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class ProductRecommended
 *
 * @package Aheadworks\AdvancedReviews\Model\Source\Review
 */
class ProductRecommended implements OptionSourceInterface
{
    /**#@+
     * Product recommended by review author values
     */
    const NOT_SPECIFIED = 0;
    const NO = 1;
    const YES = 2;
    /**#@-*/

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
                'value' => self::NOT_SPECIFIED,
                'label' => __('Not specified')
            ],
            [
                'value' => self::NO,
                'label' => __('No')
            ],
            [
                'value' => self::YES,
                'label' => __('Yes')
            ]
        ];

        return $this->options;
    }
}
