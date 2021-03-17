<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Model\Product\Layout\Processor\Review\DetailedSummary;

use Aheadworks\AdvancedReviews\Model\Source\Review\RatingValue as RatingValueSource;

/**
 * Class RatingOptionProvider
 *
 * @package Aheadworks\AdvancedReviews\Model\Product\Layout\Processor\Review\DetailedSummary
 */
class RatingOptionProvider
{
    /**
     * @var RatingValueSource
     */
    private $ratingValueSource;

    /**
     * @param RatingValueSource $ratingValueSource
     */
    public function __construct(
        RatingValueSource $ratingValueSource
    ) {
        $this->ratingValueSource = $ratingValueSource;
    }

    /**
     * Retrieve prepared and sorted rating options array
     *
     * @return array an array with rating 'value' and 'label' key/value pairs
     */
    public function getRatingOptions()
    {
        $ratingValueSourceOptionArray = $this->ratingValueSource->toOptionArray();
        $ratingOptions = [];
        foreach ($ratingValueSourceOptionArray as $ratingValueSourceOption) {
            if (isset($ratingValueSourceOption['value']) && isset($ratingValueSourceOption['label'])) {
                $ratingOptions[$ratingValueSourceOption['value']] = $ratingValueSourceOption['label'];
            }
        }
        krsort($ratingOptions);
        return $ratingOptions;
    }
}
