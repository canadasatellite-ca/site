<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Model\Email\QueueItem\Validator\Type;

use Aheadworks\AdvancedReviews\Model\Email\QueueItem\ValidatorInterface;
use Aheadworks\AdvancedReviews\Api\Data\QueueItemInterface;

/**
 * Class Admin
 *
 * @package Aheadworks\AdvancedReviews\Model\Email\QueueItem\Validator\Type
 */
class Admin implements ValidatorInterface
{
    /**
     * {@inheritdoc}
     */
    public function isValid(QueueItemInterface $queueItem)
    {
        return true;
    }
}
