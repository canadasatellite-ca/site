<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Model\Service;

use Aheadworks\AdvancedReviews\Model\Source\Review\AdvancedRatingValue;
use Aheadworks\AdvancedReviews\Model\Source\Review\AuthorType;
use Magento\Framework\Api\DataObjectHelper;
use Aheadworks\AdvancedReviews\Api\ReviewManagementInterface;
use Aheadworks\AdvancedReviews\Api\ReviewRepositoryInterface;
use Aheadworks\AdvancedReviews\Api\Data\ReviewInterface;
use Aheadworks\AdvancedReviews\Model\Source\Review\Status;
use Aheadworks\AdvancedReviews\Model\Review\Comment\Processor as CommentProcessor;
use Aheadworks\AdvancedReviews\Model\Review\NotificationManager as ReviewNotificationManager;
use Aheadworks\AdvancedReviews\Model\Review\ProcessorInterface;
use Magento\Framework\Exception\LocalizedException;
use Zend_Validate_Interface;
use Zend_Validate_Exception;
use Magento\Framework\Phrase;

/**
 * Class ReviewService
 *
 * @package Aheadworks\AdvancedReviews\Model\Service
 */
class ReviewService implements ReviewManagementInterface
{
    /**
     * @var ReviewRepositoryInterface
     */
    private $reviewRepository;

    /**
     * @var DataObjectHelper
     */
    private $dataObjectHelper;

    /**
     * @var CommentProcessor
     */
    private $commentProcessor;

    /**
     * @var ReviewNotificationManager
     */
    private $reviewNotificationManager;

    /**
     * @var ProcessorInterface
     */
    private $creationProcessor;

    /**
     * @var Zend_Validate_Interface
     */
    private $creationValidator;

    /**
     * @param ReviewRepositoryInterface $reviewRepository
     * @param DataObjectHelper $dataObjectHelper
     * @param CommentProcessor $commentProcessor
     * @param ReviewNotificationManager $reviewNotificationManager
     * @param ProcessorInterface $creationProcessor
     * @param Zend_Validate_Interface $creationValidator
     */
    public function __construct(
        ReviewRepositoryInterface $reviewRepository,
        DataObjectHelper $dataObjectHelper,
        CommentProcessor $commentProcessor,
        ReviewNotificationManager $reviewNotificationManager,
        ProcessorInterface $creationProcessor,
        Zend_Validate_Interface $creationValidator
    ) {
        $this->reviewRepository = $reviewRepository;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->commentProcessor = $commentProcessor;
        $this->reviewNotificationManager = $reviewNotificationManager;
        $this->creationProcessor = $creationProcessor;
        $this->creationValidator = $creationValidator;
    }

    /**
     * {@inheritdoc}
     */
    public function createReview(ReviewInterface $review)
    {
        $this->creationProcessor->process($review);
        $this->validateNewReview($review);
        $createdReview = $this->reviewRepository->save($review);
        $this->commentProcessor->processAdminComment($createdReview);
        $this->processAdminNotificationsForNewReview($createdReview);

        return $createdReview;
    }

    /**
     * {@inheritdoc}
     */
    public function updateReview(ReviewInterface $review)
    {
        $reviewToMerge = $this->reviewRepository->getById($review->getId(), true);
        $isReviewApproved = $this->isReviewApproved($review, $reviewToMerge);
        $updatedReview = $this->prepareUpdatedReview($review, $reviewToMerge);
        $savedReview = $this->reviewRepository->save($updatedReview);
        $this->commentProcessor->processAdminComment($savedReview);
        if ($isReviewApproved) {
            $this->reviewNotificationManager->notifyAuthorAboutReviewApproval($savedReview);
        }
        return $savedReview;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteReview(ReviewInterface $review)
    {
        $result = $this->reviewRepository->delete($review);
        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteReviewById($reviewId)
    {
        $review = $this->reviewRepository->getById($reviewId);
        $result = $this->deleteReview($review);
        return $result;
    }

    /**
     * Validate review before creation
     *
     * @param ReviewInterface $review
     * @throws LocalizedException
     */
    private function validateNewReview($review)
    {
        try {
            if (!$this->creationValidator->isValid($review)) {
                throw new LocalizedException(new Phrase(implode(PHP_EOL, $this->creationValidator->getMessages())));
            }
        } catch (Zend_Validate_Exception $exception) {
            throw new LocalizedException(__('Review validation error occurred'));
        }
    }

    /**
     * Merges edited review object onto the existing one
     *
     * @param ReviewInterface $editedReview
     * @param ReviewInterface $reviewToMerge
     * @return ReviewInterface
     */
    private function prepareUpdatedReview($editedReview, $reviewToMerge)
    {
        $this->dataObjectHelper->mergeDataObjects(
            ReviewInterface::class,
            $reviewToMerge,
            $editedReview
        );
        return $reviewToMerge;
    }

    /**
     * Process necessary admin notifications for new review
     *
     * @param ReviewInterface $review
     */
    private function processAdminNotificationsForNewReview(ReviewInterface $review)
    {
        if ($this->isNeedToNotifyAdmin($review)) {
            $this->reviewNotificationManager->notifyAdminAboutNewReview($review);
            if ($this->isReviewCritical($review)) {
                $this->reviewNotificationManager->notifyAdminAboutNewCriticalReview($review);
            }
        }
    }

    /**
     * Check if need to notify admin
     *
     * @param ReviewInterface $review
     * @return bool
     */
    private function isNeedToNotifyAdmin(ReviewInterface $review)
    {
        return $review->getAuthorType() != AuthorType::ADMIN;
    }

    /**
     * Check if review is critical
     *
     * @param ReviewInterface $review
     * @return bool
     */
    private function isReviewCritical(ReviewInterface $review)
    {
        return in_array($review->getRating(), AdvancedRatingValue::getCriticalReviews());
    }

    /**
     * Check if current review is approved
     *
     * @param ReviewInterface $editedReview
     * @param ReviewInterface $origReview
     * @return bool
     */
    private function isReviewApproved($editedReview, $origReview)
    {
        return $editedReview->getStatus() == Status::APPROVED
            && $editedReview->getStatus() != $origReview->getStatus()
        ;
    }
}
