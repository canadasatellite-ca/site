<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Model\Email\QueueItem;

use Aheadworks\AdvancedReviews\Api\Data\QueueItemInterface;

/**
 * Interface ValidatorInterface
 *
 * @package Aheadworks\AdvancedReviews\Model\Email\QueueItem
 */
interface ValidatorInterface
{
    /**
     * Check if queue item is valid
     *
     * @param QueueItemInterface $queueItem
     * @return bool
     */
    public function isValid(QueueItemInterface $queueItem);
}
