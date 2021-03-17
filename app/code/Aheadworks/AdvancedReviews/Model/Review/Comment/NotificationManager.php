<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Model\Review\Comment;

use Aheadworks\AdvancedReviews\Api\Data\ReviewInterface;
use Aheadworks\AdvancedReviews\Api\Data\CommentInterface;
use Aheadworks\AdvancedReviews\Model\Source\Email\Type as NotificationType;
use Aheadworks\AdvancedReviews\Api\QueueManagementInterface;
use Aheadworks\AdvancedReviews\Model\Review\Author\Resolver as ReviewAuthorResolver;
use Aheadworks\AdvancedReviews\Api\ReviewRepositoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Aheadworks\AdvancedReviews\Model\Config;

/**
 * Class NotificationManager
 *
 * @package Aheadworks\AdvancedReviews\Model\Review\Comment
 * TODO: refactoring within M2RV-368
 */
class NotificationManager
{
    /**
     * @var QueueManagementInterface
     */
    private $queueManagement;

    /**
     * @var ReviewAuthorResolver
     */
    private $reviewAuthorResolver;

    /**
     * @var ReviewRepositoryInterface
     */
    private $reviewRepository;

    /**
     * @var Config
     */
    private $config;

    /**
     * @param QueueManagementInterface $queueManagement
     * @param ReviewAuthorResolver $reviewAuthorResolver
     * @param ReviewRepositoryInterface $reviewRepository
     * @param Config $config
     */
    public function __construct(
        QueueManagementInterface $queueManagement,
        ReviewAuthorResolver $reviewAuthorResolver,
        ReviewRepositoryInterface $reviewRepository,
        Config $config
    ) {
        $this->queueManagement = $queueManagement;
        $this->reviewAuthorResolver = $reviewAuthorResolver;
        $this->reviewRepository = $reviewRepository;
        $this->config = $config;
    }

    /**
     * Notify review author about new comment
     *
     * @param CommentInterface $comment
     */
    public function notifyReviewAuthorAboutNewComment(CommentInterface $comment)
    {
        $review = $this->getReview($comment->getReviewId());
        if ($review) {
            $recipientName = $this->reviewAuthorResolver->getName($review);
            $recipientEmail = $this->reviewAuthorResolver->getEmail($review);
            if (!empty($recipientName) && !empty($recipientEmail)) {
                $this->queueManagement->add(
                    NotificationType::SUBSCRIBER_NEW_COMMENT,
                    $comment->getId(),
                    $review->getStoreId(),
                    $recipientName,
                    $recipientEmail
                );
            }
        }
    }

    /**
     * Notify admin about new comment from visitor
     *
     * @param CommentInterface $comment
     */
    public function notifyAdminAboutNewCommentFromVisitor(CommentInterface $comment)
    {
        $review = $this->getReview($comment->getReviewId());
        if ($review) {
            $recipientName = $this->config->getDefaultAdminRecipientName();
            $recipientEmail = $this->config->getAdminNotificationEmail($review->getStoreId());
            if (!empty($recipientName) && !empty($recipientEmail)) {
                $this->queueManagement->add(
                    NotificationType::ADMIN_NEW_COMMENT_FROM_VISITOR,
                    $comment->getId(),
                    $review->getStoreId(),
                    $recipientName,
                    $recipientEmail
                );
            }
        }
    }

    /**
     * Retrieve review by id
     *
     * @param int|null $reviewId
     * @return ReviewInterface|null
     */
    private function getReview($reviewId)
    {
        $review = null;
        if (!empty($reviewId)) {
            try {
                $review = $this->reviewRepository->getById($reviewId);
            } catch (NoSuchEntityException $e) {
                $review = null;
            }
        }
        return $review;
    }
}
