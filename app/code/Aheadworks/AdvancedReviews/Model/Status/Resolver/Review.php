<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Model\Status\Resolver;

use Aheadworks\AdvancedReviews\Model\Source\Review\Status;
use Aheadworks\AdvancedReviews\Model\Status\AbstractResolver;

/**
 * Class Review
 * @package Aheadworks\AdvancedReviews\Model\Status\Resolver
 */
class Review extends AbstractResolver
{
    /**
     * {@inheritdoc}
     */
    public function getNewInstanceStatus($storeId)
    {
        return $this->config->isAutoApproveReviewsEnabled($storeId)
            ? Status::APPROVED
            : Status::getDefaultStatus();
    }
}
