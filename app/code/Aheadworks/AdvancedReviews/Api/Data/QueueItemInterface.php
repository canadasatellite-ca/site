<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Api\Data;

use Magento\Framework\Api\ExtensibleDataInterface;

/**
 * Interface QueueItemInterface
 * @package Aheadworks\AdvancedReviews\Api\Data
 */
interface QueueItemInterface extends ExtensibleDataInterface
{
    /**#@+
     * Constants defined for keys of the data array.
     * Identical to the name of the getter in snake case
     */
    const ID = 'id';
    const STORE_ID = 'store_id';
    const TYPE = 'type';
    const STATUS = 'status';
    const CREATED_AT = 'created_at';
    const SCHEDULED_AT = 'scheduled_at';
    const SENT_AT = 'sent_at';
    const RECIPIENT_NAME = 'recipient_name';
    const RECIPIENT_EMAIL = 'recipient_email';
    const OBJECT_ID = 'object_id';
    const SECURITY_CODE = 'security_code';
    /**#@-*/

    /**
     * Get id
     *
     * @return int
     */
    public function getId();

    /**
     * Set id
     *
     * @param int $id
     * @return $this
     */
    public function setId($id);

    /**
     * Get queue item store id
     *
     * @return int
     */
    public function getStoreId();

    /**
     * Set queue item store id
     *
     * @param int $storeId
     * @return $this
     */
    public function setStoreId($storeId);

    /**
     * Get email queue type
     *
     * @return int
     */
    public function getType();

    /**
     * Set email queue type
     *
     * @param int $type
     * @return $this
     */
    public function setType($type);

    /**
     * Get queue item status
     *
     * @return int
     */
    public function getStatus();

    /**
     * Set queue item status
     *
     * @param int $status
     * @return $this
     */
    public function setStatus($status);

    /**
     * Get queue item created date
     *
     * @return string
     */
    public function getCreatedAt();

    /**
     * Set queue item created date
     *
     * @param string $createdAt
     * @return $this
     */
    public function setCreatedAt($createdAt);

    /**
     * Get queue item scheduled date
     *
     * @return string
     */
    public function getScheduledAt();

    /**
     * Set queue item scheduled date
     *
     * @param string $scheduledAt
     * @return $this
     */
    public function setScheduledAt($scheduledAt);

    /**
     * Get queue item sent date
     *
     * @return string
     */
    public function getSentAt();

    /**
     * Set queue item sent date
     *
     * @param string $sentAt
     * @return $this
     */
    public function setSentAt($sentAt);

    /**
     * Get recipient name
     *
     * @return string
     */
    public function getRecipientName();

    /**
     * Set recipient name
     *
     * @param string $recipientName
     * @return $this
     */
    public function setRecipientName($recipientName);

    /**
     * Get recipient email
     *
     * @return string
     */
    public function getRecipientEmail();

    /**
     * Set recipient email
     *
     * @param string $recipientEmail
     * @return $this
     */
    public function setRecipientEmail($recipientEmail);

    /**
     * Get related object id
     *
     * @return int
     */
    public function getObjectId();

    /**
     * Set related object id
     *
     * @param int $objectId
     * @return $this
     */
    public function setObjectId($objectId);

    /**
     * Get security code
     *
     * @return string
     */
    public function getSecurityCode();

    /**
     * Set security code
     *
     * @param string $securityCode
     * @return $this
     */
    public function setSecurityCode($securityCode);

    /**
     * Retrieve existing extension attributes object or create a new one
     *
     * @return \Aheadworks\AdvancedReviews\Api\Data\QueueItemExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object
     *
     * @param \Aheadworks\AdvancedReviews\Api\Data\QueueItemExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Aheadworks\AdvancedReviews\Api\Data\QueueItemExtensionInterface $extensionAttributes
    );
}
