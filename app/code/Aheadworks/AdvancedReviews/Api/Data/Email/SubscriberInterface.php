<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Api\Data\Email;

use Magento\Framework\Api\ExtensibleDataInterface;

/**
 * Interface SubscriberInterface
 *
 * @package Aheadworks\AdvancedReviews\Api\Data\Email
 */
interface SubscriberInterface extends ExtensibleDataInterface
{
    /**#@+
     * Constants defined for keys of the data array.
     * Identical to the name of the getter in snake case
     */
    const ID                                = 'id';
    const EMAIL                             = 'email';
    const WEBSITE_ID                        = 'website_id';
    const IS_REVIEW_APPROVED_EMAIL_ENABLED  = 'is_review_approved_email_enabled';
    const IS_NEW_COMMENT_EMAIL_ENABLED      = 'is_new_comment_email_enabled';
    const IS_REVIEW_REMINDER_EMAIL_ENABLED  = 'is_review_reminder_email_enabled';
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
     * Get email
     *
     * @return string
     */
    public function getEmail();

    /**
     * Set email
     *
     * @param string $email
     * @return $this
     */
    public function setEmail($email);

    /**
     * Get website id
     *
     * @return int
     */
    public function getWebsiteId();

    /**
     * Set website id
     *
     * @param int $websiteId
     * @return $this
     */
    public function setWebsiteId($websiteId);

    /**
     * Get is review approved email enabled
     *
     * @return bool
     */
    public function getIsReviewApprovedEmailEnabled();

    /**
     * Set is review approved email enabled
     *
     * @param bool $flag
     * @return $this
     */
    public function setIsReviewApprovedEmailEnabled($flag);

    /**
     * Get is new comment email enabled
     *
     * @return bool
     */
    public function getIsNewCommentEmailEnabled();

    /**
     * Set is new comment email enabled
     *
     * @param bool $flag
     * @return $this
     */
    public function setIsNewCommentEmailEnabled($flag);

    /**
     * Get is review reminder email enabled
     *
     * @return bool
     */
    public function getIsReviewReminderEmailEnabled();

    /**
     * Set is review reminder email enabled
     *
     * @param bool $flag
     * @return $this
     */
    public function setIsReviewReminderEmailEnabled($flag);

    /**
     * Retrieve existing extension attributes object or create a new one
     *
     * @return \Aheadworks\AdvancedReviews\Api\Data\Email\SubscriberExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object
     *
     * @param \Aheadworks\AdvancedReviews\Api\Data\Email\SubscriberExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Aheadworks\AdvancedReviews\Api\Data\Email\SubscriberExtensionInterface $extensionAttributes
    );
}
