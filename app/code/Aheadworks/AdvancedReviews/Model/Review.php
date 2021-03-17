<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Model;

use Aheadworks\AdvancedReviews\Api\Data\ReviewInterface;
use Magento\Framework\Validator\AbstractValidator;
use Aheadworks\AdvancedReviews\Model\ResourceModel\Review as ReviewResource;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\Context;
use Magento\Framework\Registry;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\DataObject\IdentityInterface;

/**
 * Class Review
 * @package Aheadworks\AdvancedReviews\Model
 */
class Review extends AbstractModel implements ReviewInterface, IdentityInterface
{
    /**
     * @var AbstractValidator
     */
    private $validator;

    /**
     * @param Context $context
     * @param Registry $registry
     * @param AbstractValidator $validator
     * @param AbstractResource|null $resource
     * @param AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        AbstractValidator $validator,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $registry,
            $resource,
            $resourceCollection,
            $data
        );
        $this->validator = $validator;
    }

    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init(ReviewResource::class);
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
    public function getRating()
    {
        return $this->getData(self::RATING);
    }

    /**
     * {@inheritdoc}
     */
    public function setRating($rating)
    {
        return $this->setData(self::RATING, $rating);
    }

    /**
     * {@inheritdoc}
     */
    public function getSummary()
    {
        return $this->getData(self::SUMMARY);
    }

    /**
     * {@inheritdoc}
     */
    public function setSummary($summary)
    {
        return $this->setData(self::SUMMARY, $summary);
    }

    /**
     * {@inheritdoc}
     */
    public function getNickname()
    {
        return $this->getData(self::NICKNAME);
    }

    /**
     * {@inheritdoc}
     */
    public function setNickname($nickname)
    {
        return $this->setData(self::NICKNAME, $nickname);
    }

    /**
     * {@inheritdoc}
     */
    public function getContent()
    {
        return $this->getData(self::CONTENT);
    }

    /**
     * {@inheritdoc}
     */
    public function setContent($content)
    {
        return $this->setData(self::CONTENT, $content);
    }

    /**
     * {@inheritdoc}
     */
    public function getPros()
    {
        return $this->getData(self::PROS);
    }

    /**
     * {@inheritdoc}
     */
    public function setPros($pros)
    {
        return $this->setData(self::PROS, $pros);
    }

    /**
     * {@inheritdoc}
     */
    public function getCons()
    {
        return $this->getData(self::CONS);
    }

    /**
     * {@inheritdoc}
     */
    public function setCons($cons)
    {
        return $this->setData(self::CONS, $cons);
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
    public function getProductId()
    {
        return $this->getData(self::PRODUCT_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setProductId($productId)
    {
        return $this->setData(self::PRODUCT_ID, $productId);
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
    public function getAuthorType()
    {
        return $this->getData(self::AUTHOR_TYPE);
    }

    /**
     * {@inheritdoc}
     */
    public function setAuthorType($type)
    {
        return $this->setData(self::AUTHOR_TYPE, $type);
    }

    /**
     * {@inheritdoc}
     */
    public function getCustomerId()
    {
        return $this->getData(self::CUSTOMER_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setCustomerId($customerId)
    {
        return $this->setData(self::CUSTOMER_ID, $customerId);
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
    public function getIsVerifiedBuyer()
    {
        return $this->getData(self::IS_VERIFIED_BUYER);
    }

    /**
     * {@inheritdoc}
     */
    public function setIsVerifiedBuyer($value)
    {
        return $this->setData(self::IS_VERIFIED_BUYER, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getIsFeatured()
    {
        return $this->getData(self::IS_FEATURED);
    }

    /**
     * {@inheritdoc}
     */
    public function setIsFeatured($value)
    {
        return $this->setData(self::IS_FEATURED, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getVotesPositive()
    {
        return $this->getData(self::VOTES_POSITIVE);
    }

    /**
     * {@inheritdoc}
     */
    public function setVotesPositive($votesCount)
    {
        return $this->setData(self::VOTES_POSITIVE, $votesCount);
    }

    /**
     * {@inheritdoc}
     */
    public function getVotesNegative()
    {
        return $this->getData(self::VOTES_NEGATIVE);
    }

    /**
     * {@inheritdoc}
     */
    public function setVotesNegative($votesCount)
    {
        return $this->setData(self::VOTES_NEGATIVE, $votesCount);
    }

    /**
     * {@inheritdoc}
     */
    public function getSharedStoreIds()
    {
        return $this->getData(self::SHARED_STORE_IDS);
    }

    /**
     * {@inheritdoc}
     */
    public function setSharedStoreIds($storeIds)
    {
        return $this->setData(self::SHARED_STORE_IDS, $storeIds);
    }

    /**
     * {@inheritdoc}
     */
    public function getComments()
    {
        return $this->getData(self::COMMENTS);
    }

    /**
     * {@inheritdoc}
     */
    public function setComments($comments)
    {
        return $this->setData(self::COMMENTS, $comments);
    }

    /**
     * {@inheritdoc}
     */
    public function getAdminComment()
    {
        return $this->getData(self::ADMIN_COMMENT);
    }

    /**
     * {@inheritdoc}
     */
    public function setAdminComment($comment)
    {
        return $this->setData(self::ADMIN_COMMENT, $comment);
    }

    /**
     * {@inheritdoc}
     */
    public function getProductRecommended()
    {
        return $this->getData(self::PRODUCT_RECOMMENDED);
    }

    /**
     * {@inheritdoc}
     */
    public function setProductRecommended($productRecommended)
    {
        return $this->setData(self::PRODUCT_RECOMMENDED, $productRecommended);
    }

    /**
     * {@inheritdoc}
     */
    public function getOrderItemId()
    {
        return $this->getData(self::ORDER_ITEM_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setOrderItemId($orderItemId)
    {
        return $this->setData(self::ORDER_ITEM_ID, $orderItemId);
    }

    /**
     * {@inheritdoc}
     */
    public function getAttachments()
    {
        return $this->getData(self::ATTACHMENTS);
    }

    /**
     * {@inheritdoc}
     */
    public function setAttachments($attachments)
    {
        return $this->setData(self::ATTACHMENTS, $attachments);
    }

    /**
     * {@inheritdoc}
     */
    public function getAreAgreementsConfirmed()
    {
        return $this->getData(self::ARE_AGREEMENTS_CONFIRMED);
    }

    /**
     * {@inheritdoc}
     */
    public function setAreAgreementsConfirmed($areAgreementsConfirmed)
    {
        return $this->setData(self::ARE_AGREEMENTS_CONFIRMED, $areAgreementsConfirmed);
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
        \Aheadworks\AdvancedReviews\Api\Data\ReviewExtensionInterface $extensionAttributes
    ) {
        return $this->setData(self::EXTENSION_ATTRIBUTES_KEY, $extensionAttributes);
    }

    /**
     * {@inheritdoc}
     */
    protected function _getValidationRulesBeforeSave()
    {
        return $this->validator;
    }

    /**
     * {@inheritdoc}
     */
    public function getIdentities()
    {
        $identities = [];
        $identities = array_merge($identities, $this->getReviewIdentities());
        return $identities;
    }

    /**
     * Retrieve current review identities
     *
     * @return array
     */
    protected function getReviewIdentities()
    {
        return [
            self::CACHE_ALL_REVIEWS_PAGE_TAG,
            self::CACHE_TAG . '_' . $this->getId(),
            self::CACHE_PRODUCT_TAG . '_' . $this->getProductId(),
        ];
    }
}
