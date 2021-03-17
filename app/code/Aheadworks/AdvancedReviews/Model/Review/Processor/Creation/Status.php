<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Model\Review\Processor\Creation;

use Aheadworks\AdvancedReviews\Model\Review\ProcessorInterface;
use Aheadworks\AdvancedReviews\Model\Status\Resolver\Review as StatusResolver;

/**
 * Class Status
 *
 * @package Aheadworks\AdvancedReviews\Model\Review\Processor\Creation
 */
class Status implements ProcessorInterface
{
    /**
     * @var StatusResolver
     */
    private $statusResolver;

    /**
     * @param StatusResolver $statusResolver
     */
    public function __construct(
        StatusResolver $statusResolver
    ) {
        $this->statusResolver = $statusResolver;
    }

    /**
     * {@inheritdoc}
     */
    public function process($review)
    {
        if (empty($review->getStatus())) {
            $review->setStatus($this->statusResolver->getNewInstanceStatus($review->getStoreId()));
        }
        return $review;
    }
}
