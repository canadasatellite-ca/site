<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Model\Email\QueueItem\Validator\Type;

use Aheadworks\AdvancedReviews\Model\Email\QueueItem\ValidatorInterface;
use Aheadworks\AdvancedReviews\Api\Data\QueueItemInterface;
use Aheadworks\AdvancedReviews\Model\Email\Subscriber\Resolver as SubscriberResolver;
use Aheadworks\AdvancedReviews\Model\Email\Subscriber\Checker as SubscriberChecker;

/**
 * Class Subscriber
 *
 * @package Aheadworks\AdvancedReviews\Model\Email\QueueItem\Validator\Type
 */
class Subscriber implements ValidatorInterface
{
    /**
     * @var SubscriberResolver
     */
    private $subscriberResolver;

    /**
     * @var SubscriberChecker
     */
    private $subscriberChecker;

    /**
     * @param SubscriberResolver $subscriberResolver
     * @param SubscriberChecker $subscriberChecker
     */
    public function __construct(
        SubscriberResolver $subscriberResolver,
        SubscriberChecker $subscriberChecker
    ) {
        $this->subscriberResolver = $subscriberResolver;
        $this->subscriberChecker = $subscriberChecker;
    }

    /**
     * {@inheritdoc}
     */
    public function isValid(QueueItemInterface $queueItem)
    {
        $isValid = false;

        $subscriber = $this->subscriberResolver->getByEmailQueueItem($queueItem);
        if ($subscriber) {
            $isValid = $this->subscriberChecker->isNeedToSendEmail($subscriber, $queueItem->getType());
        }

        return $isValid;
    }
}
