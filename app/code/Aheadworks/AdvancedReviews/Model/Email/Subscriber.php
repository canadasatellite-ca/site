<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Model\Email;

use Aheadworks\AdvancedReviews\Api\Data\Email\SubscriberInterface;
use Aheadworks\AdvancedReviews\Model\ResourceModel\Email\Subscriber as EmailSubscriberResource;
use Magento\Framework\Model\AbstractModel;

/**
 * Class Subscriber
 *
 * @package Aheadworks\AdvancedReviews\Model\Email
 */
class Subscriber extends AbstractModel implements SubscriberInterface
{
    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init(EmailSubscriberResource::class);
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
    public function getEmail()
    {
        return $this->getData(self::EMAIL);
    }

    /**
     * {@inheritdoc}
     */
    public function setEmail($email)
    {
        return $this->setData(self::EMAIL, $email);
    }

    /**
     * {@inheritdoc}
     */
    public function getWebsiteId()
    {
        return $this->getData(self::WEBSITE_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setWebsiteId($websiteId)
    {
        return $this->setData(self::WEBSITE_ID, $websiteId);
    }

    /**
     * {@inheritdoc}
     */
    public function getIsReviewApprovedEmailEnabled()
    {
        return $this->getData(self::IS_REVIEW_APPROVED_EMAIL_ENABLED);
    }

    /**
     * {@inheritdoc}
     */
    public function setIsReviewApprovedEmailEnabled($flag)
    {
        return $this->setData(self::IS_REVIEW_APPROVED_EMAIL_ENABLED, $flag);
    }

    /**
     * {@inheritdoc}
     */
    public function getIsNewCommentEmailEnabled()
    {
        return $this->getData(self::IS_NEW_COMMENT_EMAIL_ENABLED);
    }

    /**
     * {@inheritdoc}
     */
    public function setIsNewCommentEmailEnabled($flag)
    {
        return $this->setData(self::IS_NEW_COMMENT_EMAIL_ENABLED, $flag);
    }

    /**
     * {@inheritdoc}
     */
    public function getIsReviewReminderEmailEnabled()
    {
        return $this->getData(self::IS_REVIEW_REMINDER_EMAIL_ENABLED);
    }

    /**
     * {@inheritdoc}
     */
    public function setIsReviewReminderEmailEnabled($flag)
    {
        return $this->setData(self::IS_REVIEW_REMINDER_EMAIL_ENABLED, $flag);
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
        \Aheadworks\AdvancedReviews\Api\Data\Email\SubscriberExtensionInterface $extensionAttributes
    ) {
        return $this->setData(self::EXTENSION_ATTRIBUTES_KEY, $extensionAttributes);
    }
}
