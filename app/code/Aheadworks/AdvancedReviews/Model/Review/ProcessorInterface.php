<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Model\Review;

use Aheadworks\AdvancedReviews\Api\Data\ReviewInterface;

/**
 * Interface ProcessorInterface
 *
 * @package Aheadworks\AdvancedReviews\Model\Review
 */
interface ProcessorInterface
{
    /**
     * Process review instance
     *
     * @param ReviewInterface $review
     * @return ReviewInterface
     */
    public function process($review);
}
