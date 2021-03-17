<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Api\Data;

use Magento\Framework\Api\ExtensibleDataInterface;

/**
 * Interface CommentInterface
 * @package Aheadworks\AdvancedReviews\Api\Data
 */
interface CommentInterface extends ExtensibleDataInterface
{
    /**#@+
     * Constants defined for keys of the data array.
     * Identical to the name of the getter in snake case
     */
    const ID = 'id';
    const TYPE = 'type';
    const REVIEW_ID = 'review_id';
    const STATUS = 'status';
    const NICKNAME = 'nickname';
    const CONTENT = 'content';
    const CREATED_AT = 'created_at';
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
     * Get type
     *
     * @return string
     */
    public function getType();

    /**
     * Set type
     *
     * @param string $type
     * @return $this
     */
    public function setType($type);

    /**
     * Get review id
     *
     * @return int
     */
    public function getReviewId();

    /**
     * Set review id
     *
     * @param int $reviewId
     * @return $this
     */
    public function setReviewId($reviewId);

    /**
     * Get comment status
     *
     * @return int
     */
    public function getStatus();

    /**
     * Set comment status
     *
     * @param int $status
     * @return $this
     */
    public function setStatus($status);

    /**
     * Get nickname
     *
     * @return string
     */
    public function getNickname();

    /**
     * Set nickname
     *
     * @param string $nickname
     * @return $this
     */
    public function setNickname($nickname);

    /**
     * Get comment content
     *
     * @return string
     */
    public function getContent();

    /**
     * Set comment content
     *
     * @param string $content
     * @return $this
     */
    public function setContent($content);

    /**
     * Get comment created date
     *
     * @return string
     */
    public function getCreatedAt();

    /**
     * Set comment created date
     *
     * @param string $createdAt
     * @return $this
     */
    public function setCreatedAt($createdAt);

    /**
     * Retrieve existing extension attributes object or create a new one
     *
     * @return \Aheadworks\AdvancedReviews\Api\Data\CommentExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object
     *
     * @param \Aheadworks\AdvancedReviews\Api\Data\CommentExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Aheadworks\AdvancedReviews\Api\Data\CommentExtensionInterface $extensionAttributes
    );
}
