<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Model\ResourceModel\Queue;

use Aheadworks\AdvancedReviews\Api\Data\QueueItemInterface;
use Aheadworks\AdvancedReviews\Model\ResourceModel\AbstractCollection;
use Aheadworks\AdvancedReviews\Model\ResourceModel\Queue as QueueResource;
use Aheadworks\AdvancedReviews\Model\QueueItem;

/**
 * Class Collection
 * @package Aheadworks\AdvancedReviews\Model\ResourceModel\Queue
 */
class Collection extends AbstractCollection
{
    /**
     * {@inheritdoc}
     */
    protected $_idFieldName = QueueItemInterface::ID;

    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init(QueueItem::class, QueueResource::class);
    }
}
