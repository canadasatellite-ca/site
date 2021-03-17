<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Model\Service\Review;

use Aheadworks\AdvancedReviews\Model\Source\Review\Comment\Status;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Api\DataObjectHelper;
use Aheadworks\AdvancedReviews\Api\CommentManagementInterface;
use Aheadworks\AdvancedReviews\Api\CommentRepositoryInterface;
use Aheadworks\AdvancedReviews\Api\Data\CommentInterface;
use Aheadworks\AdvancedReviews\Model\Status\Resolver\Comment as StatusResolver;
use Aheadworks\AdvancedReviews\Model\Review\Comment\NotificationManager as CommentNotificationManager;
use Aheadworks\AdvancedReviews\Model\Review\Comment\Resolver as CommentResolver;

/**
 * Class CommentService
 * @package Aheadworks\AdvancedReviews\Model\Service\Review
 */
class CommentService implements CommentManagementInterface
{
    /**
     * @var CommentRepositoryInterface
     */
    private $commentRepository;

    /**
     * @var DataObjectHelper
     */
    private $dataObjectHelper;

    /**
     * @var StatusResolver
     */
    private $statusResolver;

    /**
     * @var CommentNotificationManager
     */
    private $commentNotificationManager;

    /**
     * @var CommentResolver
     */
    private $commentResolver;

    /**
     * @param CommentRepositoryInterface $commentRepository
     * @param DataObjectHelper $dataObjectHelper
     * @param StatusResolver $statusResolver
     * @param CommentNotificationManager $commentNotificationManager
     * @param CommentResolver $commentResolver
     */
    public function __construct(
        CommentRepositoryInterface $commentRepository,
        DataObjectHelper $dataObjectHelper,
        StatusResolver $statusResolver,
        CommentNotificationManager $commentNotificationManager,
        CommentResolver $commentResolver
    ) {
        $this->commentRepository = $commentRepository;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->statusResolver = $statusResolver;
        $this->commentNotificationManager = $commentNotificationManager;
        $this->commentResolver = $commentResolver;
    }

    /**
     * {@inheritdoc}
     */
    public function addCustomerComment(CommentInterface $comment, $storeId = null)
    {
        $comment->setStatus($this->statusResolver->getNewInstanceStatus($storeId));
        $createdComment = $this->commentRepository->save($comment);
        $this->commentNotificationManager->notifyAdminAboutNewCommentFromVisitor($createdComment);
        $this->processReviewAuthorNotificationForNewComment($createdComment);
        return $createdComment;
    }

    /**
     * {@inheritdoc}
     */
    public function addAdminComment(CommentInterface $comment)
    {
        $comment->setStatus(Status::APPROVED);
        $createdComment = $this->commentRepository->save($comment);
        $this->processReviewAuthorNotificationForNewComment($createdComment);
        return $createdComment;
    }

    /**
     * {@inheritdoc}
     */
    public function updateComment(CommentInterface $comment)
    {
        $updatedComment = $this->prepareUpdatedComment($comment);
        $savedComment = $this->commentRepository->save($updatedComment);
        $this->processReviewAuthorNotificationForNewComment($savedComment);
        return $savedComment;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteComment(CommentInterface $comment)
    {
        $result = $this->commentRepository->delete($comment);
        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteCommentById($commentId)
    {
        $comment = $this->commentRepository->getById($commentId);
        return $this->deleteComment($comment);
    }

    /**
     * Merges edited comment object with existing
     *
     * @param CommentInterface $editedComment
     * @return CommentInterface
     * @throws LocalizedException
     */
    private function prepareUpdatedComment($editedComment)
    {
        $commentToMerge = $this->commentRepository->getById($editedComment->getId());
        $this->dataObjectHelper->mergeDataObjects(
            CommentInterface::class,
            $commentToMerge,
            $editedComment
        );
        return $commentToMerge;
    }

    /**
     * Process review author notification for new comment
     *
     * @param CommentInterface $comment
     */
    private function processReviewAuthorNotificationForNewComment($comment)
    {
        if ($this->isCommentVisibleForReviewAuthor($comment)) {
            $this->commentNotificationManager->notifyReviewAuthorAboutNewComment($comment);
        }
    }

    /**
     * Check if comment visible for review author
     *
     * @param CommentInterface $comment
     * @return bool
     */
    private function isCommentVisibleForReviewAuthor($comment)
    {
        return $this->commentResolver->isNeedToShowOnFrontend($comment->getStatus());
    }
}
