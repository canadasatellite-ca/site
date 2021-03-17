<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Model\Review\Helpfulness\Vote;

use Aheadworks\AdvancedReviews\Api\Data\ReviewInterface;

/**
 * Interface ProcessorInterface
 * @package Aheadworks\AdvancedReviews\Model\Review\Helpfulness\Vote
 */
interface ProcessorInterface
{
    /**
     * Process review votes
     *
     * @param ReviewInterface $review
     * @return ReviewInterface $review
     */
    public function process($review);
}
