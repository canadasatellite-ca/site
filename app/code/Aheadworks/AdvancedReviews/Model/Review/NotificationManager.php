<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Model\Review;

use Aheadworks\AdvancedReviews\Api\Data\ReviewInterface;
use Aheadworks\AdvancedReviews\Model\Source\Email\Type as NotificationType;
use Aheadworks\AdvancedReviews\Api\QueueManagementInterface;
use Aheadworks\AdvancedReviews\Model\Review\Author\Resolver as ReviewAuthorResolver;
use Aheadworks\AdvancedReviews\Model\Config;

/**
 * Class NotificationManager
 *
 * @package Aheadworks\AdvancedReviews\Model\Review
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
     * @var Config
     */
    private $config;

    /**
     * @param QueueManagementInterface $queueManagement
     * @param ReviewAuthorResolver $reviewAuthorResolver
     * @param Config $config
     */
    public function __construct(
        QueueManagementInterface $queueManagement,
        ReviewAuthorResolver $reviewAuthorResolver,
        Config $config
    ) {
        $this->queueManagement = $queueManagement;
        $this->reviewAuthorResolver = $reviewAuthorResolver;
        $this->config = $config;
    }

    /**
     * Notify review author about review approval
     *
     * @param ReviewInterface $review
     */
    public function notifyAuthorAboutReviewApproval(ReviewInterface $review)
    {
        $recipientName = $this->reviewAuthorResolver->getName($review);
        $recipientEmail = $this->reviewAuthorResolver->getEmail($review);
        if (!empty($recipientName) && !empty($recipientEmail)) {
            $this->queueManagement->add(
                NotificationType::SUBSCRIBER_REVIEW_APPROVED,
                $review->getId(),
                $review->getStoreId(),
                $recipientName,
                $recipientEmail
            );
        }
    }

    /**
     * Notify admin about new review
     *
     * @param ReviewInterface $review
     */
    public function notifyAdminAboutNewReview(ReviewInterface $review)
    {
        $recipientName = $this->config->getDefaultAdminRecipientName();
        $recipientEmail = $this->config->getAdminNotificationEmail($review->getStoreId());
        if (!empty($recipientName) && !empty($recipientEmail)) {
            $this->queueManagement->add(
                NotificationType::ADMIN_NEW_REVIEW,
                $review->getId(),
                $review->getStoreId(),
                $recipientName,
                $recipientEmail
            );
        }
    }

    /**
     * Notify admin about new critical review
     *
     * @param ReviewInterface $review
     */
    public function notifyAdminAboutNewCriticalReview(ReviewInterface $review)
    {
        $recipientName = $this->config->getDefaultAdminRecipientName();
        $recipientEmail = $this->config->getEmailAddressForCriticalReviewAlert($review->getStoreId());
        if (!empty($recipientName) && !empty($recipientEmail)) {
            $this->queueManagement->add(
                NotificationType::ADMIN_CRITICAL_REVIEW_ALERT,
                $review->getId(),
                $review->getStoreId(),
                $recipientName,
                $recipientEmail
            );
        }
    }
}
