<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Model;

use Aheadworks\AdvancedReviews\Api\Data\QueueItemInterface;
use Magento\Framework\Model\AbstractModel;
use Aheadworks\AdvancedReviews\Model\ResourceModel\Queue as QueueResource;

/**
 * Class QueueItem
 * @package Aheadworks\AdvancedReviews\Model
 */
class QueueItem extends AbstractModel implements QueueItemInterface
{
    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init(QueueResource::class);
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->getData(self::ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setId($id)
    {
        return $this->setData(self::ID, $id);
    }

    /**
     * {@inheritdoc}
     */
    public function getStoreId()
    {
        return $this->getData(self::STORE_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setStoreId($storeId)
    {
        return $this->setData(self::STORE_ID, $storeId);
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return $this->getData(self::TYPE);
    }

    /**
     * {@inheritdoc}
     */
    public function setType($type)
    {
        return $this->setData(self::TYPE, $type);
    }

    /**
     * {@inheritdoc}
     */
    public function getStatus()
    {
        return $this->getData(self::STATUS);
    }

    /**
     * {@inheritdoc}
     */
    public function setStatus($status)
    {
        return $this->setData(self::STATUS, $status);
    }

    /**
     * {@inheritdoc}
     */
    public function getCreatedAt()
    {
        return $this->getData(self::CREATED_AT);
    }

    /**
     * {@inheritdoc}
     */
    public function setCreatedAt($createdAt)
    {
        return $this->setData(self::CREATED_AT, $createdAt);
    }

    /**
     * {@inheritdoc}
     */
    public function getScheduledAt()
    {
        return $this->getData(self::SCHEDULED_AT);
    }

    /**
     * {@inheritdoc}
     */
    public function setScheduledAt($scheduledAt)
    {
        return $this->setData(self::SCHEDULED_AT, $scheduledAt);
    }

    /**
     * {@inheritdoc}
     */
    public function getSentAt()
    {
        return $this->getData(self::SENT_AT);
    }

    /**
     * {@inheritdoc}
     */
    public function setSentAt($sentAt)
    {
        return $this->setData(self::SENT_AT, $sentAt);
    }

    /**
     * {@inheritdoc}
     */
    public function getRecipientName()
    {
        return $this->getData(self::RECIPIENT_NAME);
    }

    /**
     * {@inheritdoc}
     */
    public function setRecipientName($recipientName)
    {
        return $this->setData(self::RECIPIENT_NAME, $recipientName);
    }

    /**
     * {@inheritdoc}
     */
    public function getRecipientEmail()
    {
        return $this->getData(self::RECIPIENT_EMAIL);
    }

    /**
     * {@inheritdoc}
     */
    public function setRecipientEmail($recipientEmail)
    {
        return $this->setData(self::RECIPIENT_EMAIL, $recipientEmail);
    }

    /**
     * {@inheritdoc}
     */
    public function getObjectId()
    {
        return $this->getData(self::OBJECT_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setObjectId($objectId)
    {
        return $this->setData(self::OBJECT_ID, $objectId);
    }

    /**
     * {@inheritdoc}
     */
    public function getSecurityCode()
    {
        return $this->getData(self::SECURITY_CODE);
    }

    /**
     * {@inheritdoc}
     */
    public function setSecurityCode($securityCode)
    {
        return $this->setData(self::SECURITY_CODE, $securityCode);
    }

    /**
     * {@inheritdoc}
     */
    public function getExtensionAttributes()
    {
        return $this->getData(self::EXTENSION_ATTRIBUTES_KEY);
    }

    /**
     * {@inheritdoc}
     */
    public function setExtensionAttributes(
        \Aheadworks\AdvancedReviews\Api\Data\QueueItemExtensionInterface $extensionAttributes
    ) {
        return $this->setData(self::EXTENSION_ATTRIBUTES_KEY, $extensionAttributes);
    }
}
