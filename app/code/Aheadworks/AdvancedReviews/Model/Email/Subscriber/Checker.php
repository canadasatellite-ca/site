<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Model\Email\Subscriber;

use Aheadworks\AdvancedReviews\Api\Data\Email\SubscriberInterface;
use Aheadworks\AdvancedReviews\Model\Email\Subscriber\Checker\Pool as CheckerPool;

/**
 * Class Checker
 *
 * @package Aheadworks\AdvancedReviews\Model\Email\Subscriber
 */
class Checker
{
    /**
     * @var CheckerPool
     */
    private $checkerPool;

    /**
     * @param CheckerPool $checkerPool
     */
    public function __construct(
        CheckerPool $checkerPool
    ) {
        $this->checkerPool = $checkerPool;
    }

    /**
     * Check if subscriber should receive email of specific type
     *
     * @param SubscriberInterface $subscriber
     * @param int $emailType
     * @return bool
     */
    public function isNeedToSendEmail($subscriber, $emailType)
    {
        $isNeedToSendEmail = false;

        $checker = $this->checkerPool->getCheckerByEmailType($emailType);
        if ($checker) {
            $isNeedToSendEmail = $checker->isNeedToSendEmail($subscriber);
        }

        return $isNeedToSendEmail;
    }
}
