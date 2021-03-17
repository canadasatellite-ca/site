<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Model\Review\Helpfulness\Vote\Processor;

use Aheadworks\AdvancedReviews\Model\Review\Helpfulness\Vote\ProcessorInterface;

/**
 * Class UnvoteDislike
 * @package Aheadworks\AdvancedReviews\Model\Review\Helpfulness\Vote\Processor
 */
class UnvoteDislike extends Base implements ProcessorInterface
{
    /**
     * {@inheritdoc}
     */
    protected function resolveVotesCount($review)
    {
        return [$review->getVotesPositive(), $review->getVotesNegative() - 1];
    }
}
