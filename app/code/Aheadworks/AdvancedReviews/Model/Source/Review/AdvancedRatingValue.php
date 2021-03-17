<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Model\Source\Review;

/**
 * Class AdvancedRatingValue
 * @package Aheadworks\AdvancedReviews\Model\Source\Review
 */
class AdvancedRatingValue extends RatingValue
{
    /**#@+
     * Review rating values
     */
    const POSITIVE_REVIEWS = 'positive';
    const CRITICAL_REVIEWS = 'critical';
    /**#@-*/

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

        $ratings = parent::toOptionArray();
        krsort($ratings);

        $this->options = array_merge(
            [
                [
                    'value' => self::POSITIVE_REVIEWS,
                    'label' => __('Positive Reviews')
                ],
                [
                    'value' => self::CRITICAL_REVIEWS,
                    'label' => __('Critical Reviews')
                ]
            ],
            $ratings
        );

        return $this->options;
    }

    /**
     * Retrieve positive reviews
     *
     * @return array
     */
    public static function getPositiveReviews()
    {
        return [self::FOUR_STARS_VALUE, self::FIVE_STAR_VALUE];
    }

    /**
     * Retrieve critical reviews
     *
     * @return array
     */
    public static function getCriticalReviews()
    {
        return [self::ONE_STAR_VALUE, self::TWO_STARS_VALUE, self::THREE_STARS_VALUE];
    }
}
