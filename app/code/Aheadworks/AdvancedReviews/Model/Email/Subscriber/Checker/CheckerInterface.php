<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Model\Email\Subscriber\Checker;

use Aheadworks\AdvancedReviews\Api\Data\Email\SubscriberInterface;

/**
 * Interface CheckerInterface
 *
 * @package Aheadworks\AdvancedReviews\Model\Email\Subscriber\Checker
 */
interface CheckerInterface
{
    /**
     * Check if subscriber should receive email
     *
     * @param SubscriberInterface $subscriber
     * @return bool
     */
    public function isNeedToSendEmail($subscriber);
}
