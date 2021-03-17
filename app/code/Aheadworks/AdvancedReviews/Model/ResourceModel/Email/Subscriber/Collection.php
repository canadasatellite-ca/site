<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Model\ResourceModel\Email\Subscriber;

use Aheadworks\AdvancedReviews\Model\ResourceModel\AbstractCollection;
use Aheadworks\AdvancedReviews\Api\Data\Email\SubscriberInterface;
use Aheadworks\AdvancedReviews\Model\ResourceModel\Email\Subscriber as EmailSubscriberResource;
use Aheadworks\AdvancedReviews\Model\Email\Subscriber as EmailSubscriber;

/**
 * Class Collection
 *
 * @package Aheadworks\AdvancedReviews\Model\ResourceModel\Email\Subscriber
 */
class Collection extends AbstractCollection
{
    /**
     * {@inheritdoc}
     */
    protected $_idFieldName = SubscriberInterface::ID;

    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init(EmailSubscriber::class, EmailSubscriberResource::class);
    }
}
