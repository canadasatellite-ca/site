<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Model\Source\Review;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class IsVerifiedBuyer
 *
 * @package Aheadworks\AdvancedReviews\Model\Source\Review
 */
class IsVerifiedBuyer implements OptionSourceInterface
{
    /**#@+
     * Verified buyer values
     */
    const YES = 1;
    const NO = 0;
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
                'value' => self::YES,
                'label' => __('Yes')
            ],
            [
                'value' => self::NO,
                'label' => __('No')
            ]
        ];

        return $this->options;
    }
}
