<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Model\Status\Resolver;

use Aheadworks\AdvancedReviews\Model\Source\Review\Comment\Status;
use Aheadworks\AdvancedReviews\Model\Status\AbstractResolver;

/**
 * Class Comment
 * @package Aheadworks\AdvancedReviews\Model\Status\Resolver
 */
class Comment extends AbstractResolver
{
    /**
     * {@inheritdoc}
     */
    public function getNewInstanceStatus($storeId)
    {
        return $this->config->isAutoApproveCommentsEnabled($storeId)
            ? Status::APPROVED
            : Status::getDefaultStatus();
    }
}
