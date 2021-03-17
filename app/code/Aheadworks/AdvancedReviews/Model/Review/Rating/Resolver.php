<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Model\Review\Rating;

use Aheadworks\AdvancedReviews\Model\Source\Review\RatingValue;

/**
 * Class Resolver
 * @package Aheadworks\AdvancedReviews\Model\Review\Rating
 */
class Resolver
{
    /**
     * Calculate absolute value for the rating percentage value
     *
     * @param int $ratingPercent
     * @param int $precision
     * @return float
     */
    public function getRatingAbsoluteValue($ratingPercent, $precision = 1)
    {
        return round($ratingPercent / RatingValue::VALUE_DELTA, $precision);
    }

    /**
     * Retrieve rating title
     *
     * @param int $ratingPercent
     * @return string
     */
    public function getRatingTitle($ratingPercent)
    {
        $ratingAbsoluteValue = $this->getRatingAbsoluteValue($ratingPercent);
        return __("%1 out of %2 stars", $ratingAbsoluteValue, RatingValue::VALUES_COUNT);
    }

    /**
     * Retrieve rating maximum absolute value
     *
     * @return int
     */
    public function getRatingMaximumAbsoluteValue()
    {
        return RatingValue::VALUES_COUNT;
    }

    /**
     * Retrieve rating minimum absolute value
     *
     * @return int
     */
    public function getRatingMinimumAbsoluteValue()
    {
        return (int)(RatingValue::ONE_STAR_VALUE / RatingValue::VALUE_DELTA);
    }
}
