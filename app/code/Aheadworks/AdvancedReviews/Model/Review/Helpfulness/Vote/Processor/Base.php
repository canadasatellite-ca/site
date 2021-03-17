<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Model\Review\Helpfulness\Vote\Processor;

use Aheadworks\AdvancedReviews\Api\Data\ReviewInterface;
use Aheadworks\AdvancedReviews\Model\Review\Helpfulness\Vote\ProcessorInterface;

/**
 * Class Base
 * @package Aheadworks\AdvancedReviews\Model\Review\Helpfulness\Vote\Processor
 */
class Base implements ProcessorInterface
{
    /**
     * {@inheritdoc}
     */
    public function process($review)
    {
        list($positiveVotes, $negativeVotes) = $this->resolveVotesCount($review);

        $review
            ->setVotesPositive($positiveVotes)
            ->setVotesNegative($negativeVotes);

        return $review;
    }

    /**
     * Resolve votes count
     *
     * @param ReviewInterface $review
     * @return array
     */
    protected function resolveVotesCount($review)
    {
        return [$review->getVotesPositive(), $review->getVotesNegative()];
    }
}
