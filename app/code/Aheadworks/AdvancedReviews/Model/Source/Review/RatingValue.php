<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Model\Source\Review;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class RatingValue
 * @package Aheadworks\AdvancedReviews\Model\Source\Review
 */
class RatingValue implements OptionSourceInterface
{
    /**#@+
     * Constants defined for rating values calculation
     */
    const VALUES_COUNT = 5;
    const MAX_VALUE = 100;
    const MIN_VALUE = 0;
    const VALUE_DELTA = (self::MAX_VALUE - self::MIN_VALUE) / self::VALUES_COUNT;
    /**#@-*/

    /**#@+
     * Review rating values
     */
    const ONE_STAR_VALUE = 1 * self::VALUE_DELTA;
    const TWO_STARS_VALUE = 2 * self::VALUE_DELTA;
    const THREE_STARS_VALUE = 3 * self::VALUE_DELTA;
    const FOUR_STARS_VALUE = 4 * self::VALUE_DELTA;
    const FIVE_STAR_VALUE = 5 * self::VALUE_DELTA;
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
                'value' => self::ONE_STAR_VALUE,
                'label' => __('1 star'),
                'title' => __("%1 out of %2 stars", 1, self::VALUES_COUNT)
            ],
            [
                'value' => self::TWO_STARS_VALUE,
                'label' => __('2 stars'),
                'title' => __("%1 out of %2 stars", 2, self::VALUES_COUNT)
            ],
            [
                'value' => self::THREE_STARS_VALUE,
                'label' => __('3 stars'),
                'title' => __("%1 out of %2 stars", 3, self::VALUES_COUNT)
            ],
            [
                'value' => self::FOUR_STARS_VALUE,
                'label' => __('4 stars'),
                'title' => __("%1 out of %2 stars", 4, self::VALUES_COUNT)
            ],
            [
                'value' => self::FIVE_STAR_VALUE,
                'label' => __('5 stars'),
                'title' => __("%1 out of %2 stars", 5, self::VALUES_COUNT)
            ],
        ];

        return $this->options;
    }
}
