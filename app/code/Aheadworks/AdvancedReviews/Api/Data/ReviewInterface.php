<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Api\Data;

use Magento\Framework\Api\ExtensibleDataInterface;

/**
 * Interface ReviewInterface
 * @package Aheadworks\AdvancedReviews\Api\Data
 */
interface ReviewInterface extends ExtensibleDataInterface
{
    /**
     * Review cache tag
     */
    const CACHE_TAG = 'aw_ar_review';

    /**
     * Review product cache tag
     */
    const CACHE_PRODUCT_TAG = 'aw_ar_review_product';

    /**
     * Reviews list cache tag for 'All reviews' page
     */
    const CACHE_ALL_REVIEWS_PAGE_TAG = 'aw_ar_review_all_reviews_page';

    /**#@+
     * Constants defined for keys of the data array.
     * Identical to the name of the getter in snake case
     */
    const ID = 'id';
    const CREATED_AT = 'created_at';
    const RATING = 'rating';
    const SUMMARY = 'summary';
    const NICKNAME = 'nickname';
    const CONTENT = 'content';
    const PROS = 'pros';
    const CONS = 'cons';
    const STORE_ID = 'store_id';
    const PRODUCT_ID = 'product_id';
    const STATUS = 'status';
    const AUTHOR_TYPE = 'author_type';
    const CUSTOMER_ID = 'customer_id';
    const EMAIL = 'email';
    const IS_VERIFIED_BUYER = 'is_verified_buyer';
    const IS_FEATURED = 'is_featured';
    const VOTES_POSITIVE = 'votes_positive';
    const VOTES_NEGATIVE = 'votes_negative';
    const SHARED_STORE_IDS = 'shared_store_ids';
    const COMMENTS = 'comments';
    const ADMIN_COMMENT = 'admin_comment';
    const ORDER_ITEM_ID = 'order_item_id';
    const PRODUCT_RECOMMENDED = 'product_recommended';
    const ATTACHMENTS = 'attachments';
    const ARE_AGREEMENTS_CONFIRMED = 'are_agreements_confirmed';
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
     * Get review created date
     *
     * @return string
     */
    public function getCreatedAt();

    /**
     * Set review created date
     *
     * @param string $createdAt
     * @return $this
     */
    public function setCreatedAt($createdAt);

    /**
     * Get rating
     *
     * @return int
     */
    public function getRating();

    /**
     * Set rating
     *
     * @param int $rating
     * @return $this
     */
    public function setRating($rating);

    /**
     * Get review summary
     *
     * @return string
     */
    public function getSummary();

    /**
     * Set review summary
     *
     * @param string $summary
     * @return $this
     */
    public function setSummary($summary);

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
     * Get review content
     *
     * @return string
     */
    public function getContent();

    /**
     * Set review content
     *
     * @param string $content
     * @return $this
     */
    public function setContent($content);

    /**
     * Get product advantages
     *
     * @return string
     */
    public function getPros();

    /**
     * Set product advantages
     *
     * @param string $pros
     * @return $this
     */
    public function setPros($pros);

    /**
     * Get product disadvantages
     *
     * @return string
     */
    public function getCons();

    /**
     * Set product disadvantages
     *
     * @param string $cons
     * @return $this
     */
    public function setCons($cons);

    /**
     * Get review store id
     *
     * @return int
     */
    public function getStoreId();

    /**
     * Set review store id
     *
     * @param int $storeId
     * @return $this
     */
    public function setStoreId($storeId);

    /**
     * Get review product id
     *
     * @return int
     */
    public function getProductId();

    /**
     * Set review product id
     *
     * @param int $productId
     * @return $this
     */
    public function setProductId($productId);

    /**
     * Get review status
     *
     * @return int
     */
    public function getStatus();

    /**
     * Set review status
     *
     * @param int $status
     * @return $this
     */
    public function setStatus($status);

    /**
     * Get author type
     *
     * @return int
     */
    public function getAuthorType();

    /**
     * Set author type
     *
     * @param int $type
     * @return $this
     */
    public function setAuthorType($type);

    /**
     * Get customer id
     *
     * @return int|null
     */
    public function getCustomerId();

    /**
     * Set customer id
     *
     * @param int $customerId
     * @return $this
     */
    public function setCustomerId($customerId);

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
     * Get is featured review
     *
     * @return bool
     */
    public function getIsFeatured();

    /**
     * Set is featured review
     *
     * @param bool $value
     * @return $this
     */
    public function setIsFeatured($value);

    /**
     * Get is verified buyer
     *
     * @return bool
     */
    public function getIsVerifiedBuyer();

    /**
     * Set is verified buyer
     *
     * @param bool $value
     * @return $this
     */
    public function setIsVerifiedBuyer($value);

    /**
     * Get votes positive for review
     *
     * @return int
     */
    public function getVotesPositive();

    /**
     * Set votes positive for review
     *
     * @param int $votesCount
     * @return $this
     */
    public function setVotesPositive($votesCount);

    /**
     * Get votes negative for review
     *
     * @return int
     */
    public function getVotesNegative();

    /**
     * Set votes negative for review
     *
     * @param int $votesCount
     * @return $this
     */
    public function setVotesNegative($votesCount);

    /**
     * Get shared store ids
     *
     * @return int[]
     */
    public function getSharedStoreIds();

    /**
     * Set shared store ids
     *
     * @param int[] $storeIds
     * @return $this
     */
    public function setSharedStoreIds($storeIds);

    /**
     * Get comments
     *
     * @return \Aheadworks\AdvancedReviews\Api\Data\CommentInterface[]
     */
    public function getComments();

    /**
     * Set comments
     *
     * @param \Aheadworks\AdvancedReviews\Api\Data\CommentInterface[] $comments
     * @return $this
     */
    public function setComments($comments);

    /**
     * Get admin comment
     *
     * @return \Aheadworks\AdvancedReviews\Api\Data\CommentInterface
     */
    public function getAdminComment();

    /**
     * Set admin comment
     *
     * @param \Aheadworks\AdvancedReviews\Api\Data\CommentInterface $comment
     * @return $this
     */
    public function setAdminComment($comment);

    /**
     * Get product recommended by review author
     *
     * @return int
     */
    public function getProductRecommended();

    /**
     * Set product recommended by review author
     *
     * @param int $productRecommended
     * @return $this
     */
    public function setProductRecommended($productRecommended);

    /**
     * Get order item id
     *
     * @return int|null
     */
    public function getOrderItemId();

    /**
     * Set order item id
     *
     * @param int $orderItemId
     * @return $this
     */
    public function setOrderItemId($orderItemId);

    /**
     * Get attachments
     *
     * @return \Aheadworks\AdvancedReviews\Api\Data\ReviewAttachmentInterface[]
     */
    public function getAttachments();

    /**
     * Set attachments
     *
     * @param \Aheadworks\AdvancedReviews\Api\Data\ReviewAttachmentInterface[] $attachments
     * @return $this
     */
    public function setAttachments($attachments);

    /**
     * Get are terms and conditions confirmed
     *
     * @return bool|null
     */
    public function getAreAgreementsConfirmed();

    /**
     * Set are terms and conditions confirmed
     *
     * @param bool $areAgreementsConfirmed
     * @return $this
     */
    public function setAreAgreementsConfirmed($areAgreementsConfirmed);

    /**
     * Retrieve existing extension attributes object or create a new one
     *
     * @return \Aheadworks\AdvancedReviews\Api\Data\ReviewExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object
     *
     * @param \Aheadworks\AdvancedReviews\Api\Data\ReviewExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Aheadworks\AdvancedReviews\Api\Data\ReviewExtensionInterface $extensionAttributes
    );
}
