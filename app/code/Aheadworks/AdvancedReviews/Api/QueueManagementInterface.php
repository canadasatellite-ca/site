<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Api;

use Aheadworks\AdvancedReviews\Api\Data\QueueItemInterface;
use Magento\Framework\Exception\LocalizedException;

/**
 * Interface QueueManagementInterface
 * @package Aheadworks\AdvancedReviews\Api
 */
interface QueueManagementInterface
{
    /**
     * Limit to send mails per iteration
     */
    const SEND_LIMIT = 100;

    /**
     * Add new queue item
     *
     * @param int $type
     * @param int $objectId
     * @param int $storeId
     * @param string $recipientName
     * @param string $recipientEmail
     * @return QueueItemInterface|null
     */
    public function add($type, $objectId, $storeId, $recipientName, $recipientEmail);

    /**
     * Cancel queue item
     *
     * @param QueueItemInterface $queueItem
     * @return QueueItemInterface
     * @throws LocalizedException
     */
    public function cancel(QueueItemInterface $queueItem);

    /**
     * Cancel queue item by id
     *
     * @param int $queueItemId
     * @return QueueItemInterface
     * @throws LocalizedException
     */
    public function cancelById($queueItemId);

    /**
     * Send queue item
     *
     * @param QueueItemInterface $queueItem
     * @return bool
     * @throws LocalizedException
     */
    public function send(QueueItemInterface $queueItem);

    /**
     * Send queue item by id
     *
     * @param int $queueItemId
     * @return bool
     * @throws LocalizedException
     */
    public function sendById($queueItemId);

    /**
     * Delete processed queue items
     */
    public function deleteProcessed();

    /**
     * Send scheduled
     */
    public function sendScheduled();
}
