<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Test\Unit\Model\Review\Comment;

use Aheadworks\AdvancedReviews\Model\Review\Comment\NotificationManager;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Aheadworks\AdvancedReviews\Api\Data\ReviewInterface;
use Aheadworks\AdvancedReviews\Api\Data\CommentInterface;
use Aheadworks\AdvancedReviews\Model\Source\Email\Type as NotificationType;
use Aheadworks\AdvancedReviews\Api\QueueManagementInterface;
use Aheadworks\AdvancedReviews\Model\Review\Author\Resolver as ReviewAuthorResolver;
use Aheadworks\AdvancedReviews\Api\ReviewRepositoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Aheadworks\AdvancedReviews\Model\Config;
use Aheadworks\AdvancedReviews\Api\Data\QueueItemInterface;

/**
 * Test for \Aheadworks\AdvancedReviews\Model\Review\Comment\NotificationManager
 */
class NotificationManagerTest extends TestCase
{
    /**
     * @var NotificationManager
     */
    private $model;

    /**
     * @var QueueManagementInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $queueManagementMock;

    /**
     * @var ReviewAuthorResolver|\PHPUnit_Framework_MockObject_MockObject
     */
    private $reviewAuthorResolverMock;

    /**
     * @var Config|\PHPUnit_Framework_MockObject_MockObject
     */
    private $configMock;

    /**
     * @var ReviewRepositoryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $reviewRepositoryMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->queueManagementMock = $this->createMock(QueueManagementInterface::class);
        $this->reviewAuthorResolverMock = $this->createMock(ReviewAuthorResolver::class);
        $this->configMock = $this->createMock(Config::class);
        $this->reviewRepositoryMock = $this->createMock(ReviewRepositoryInterface::class);

        $this->model = $objectManager->getObject(
            NotificationManager::class,
            [
                'queueManagement' => $this->queueManagementMock,
                'reviewAuthorResolver' => $this->reviewAuthorResolverMock,
                'config' => $this->configMock,
                'reviewRepository' => $this->reviewRepositoryMock,
            ]
        );
    }

    /**
     * Test notifyReviewAuthorAboutNewComment method when no related review is specified
     *
     * @param int|null $reviewId
     * @dataProvider notifyReviewAuthorAboutNewCommentNoReviewDataProvider
     */
    public function testNotifyReviewAuthorAboutNewCommentNoReview($reviewId)
    {
        $comment = $this->createMock(CommentInterface::class);
        $comment->expects($this->once())
            ->method('getReviewId')
            ->willReturn($reviewId);

        $this->reviewRepositoryMock->expects($this->any())
            ->method('getById')
            ->with($reviewId)
            ->willThrowException(new NoSuchEntityException());

        $this->reviewAuthorResolverMock->expects($this->never())
            ->method('getName');
        $this->reviewAuthorResolverMock->expects($this->never())
            ->method('getEmail');

        $this->queueManagementMock->expects($this->never())
            ->method('add');

        $this->model->notifyReviewAuthorAboutNewComment($comment);
    }

    /**
     * @return array
     */
    public function notifyReviewAuthorAboutNewCommentNoReviewDataProvider()
    {
        return [
            [
                null,
            ],
            [
                '',
            ],
            [
                1,
            ],
        ];
    }

    /**
     * Test notifyReviewAuthorAboutNewComment method
     *
     * @param int|null $reviewId
     * @param string|null $recipientName
     * @param string|null $recipientEmail
     * @param bool $isQueueItemAdded
     * @dataProvider notifyReviewAuthorAboutNewCommentDataProvider
     */
    public function testNotifyReviewAuthorAboutNewComment(
        $reviewId,
        $recipientName,
        $recipientEmail,
        $isQueueItemAdded
    ) {
        $commentId = 12;
        $storeId = 1;
        $comment = $this->createMock(CommentInterface::class);
        $comment->expects($this->any())
            ->method('getReviewId')
            ->willReturn($reviewId);
        $comment->expects($this->any())
            ->method('getId')
            ->willReturn($commentId);

        $review = $this->createMock(ReviewInterface::class);
        $review->expects($this->any())
            ->method('getStoreId')
            ->willReturn($storeId);

        $this->reviewRepositoryMock->expects($this->any())
            ->method('getById')
            ->with($reviewId)
            ->willReturn($review);

        $this->reviewAuthorResolverMock->expects($this->once())
            ->method('getName')
            ->with($review)
            ->willReturn($recipientName);
        $this->reviewAuthorResolverMock->expects($this->once())
            ->method('getEmail')
            ->with($review)
            ->willReturn($recipientEmail);

        if ($isQueueItemAdded) {
            $queueItem = $this->createMock(QueueItemInterface::class);
            $this->queueManagementMock->expects($this->once())
                ->method('add')
                ->with(
                    NotificationType::SUBSCRIBER_NEW_COMMENT,
                    $commentId,
                    $storeId,
                    $recipientName,
                    $recipientEmail
                )->willReturn($queueItem);
        } else {
            $this->queueManagementMock->expects($this->never())
                ->method('add');
        }

        $this->model->notifyReviewAuthorAboutNewComment($comment);
    }

    /**
     * @return array
     */
    public function notifyReviewAuthorAboutNewCommentDataProvider()
    {
        return [
            [
                1,
                null,
                null,
                false
            ],
            [
                1,
                'Recipient name',
                null,
                false
            ],
            [
                1,
                null,
                'recipient@mail.com',
                false
            ],
            [
                1,
                '',
                '',
                false
            ],
            [
                1,
                'Recipient name',
                '',
                false
            ],
            [
                1,
                '',
                'recipient@mail.com',
                false
            ],
            [
                1,
                'Recipient name',
                'recipient@mail.com',
                true
            ],
        ];
    }

    /**
     * Test notifyAdminAboutNewCommentFromVisitor method when no related review is specified
     *
     * @param int|null $reviewId
     * @dataProvider notifyAdminAboutNewCommentFromVisitorNoReviewDataProvider
     */
    public function testNotifyAdminAboutNewCommentFromVisitorNoReview($reviewId)
    {
        $comment = $this->createMock(CommentInterface::class);
        $comment->expects($this->once())
            ->method('getReviewId')
            ->willReturn($reviewId);

        $this->reviewRepositoryMock->expects($this->any())
            ->method('getById')
            ->with($reviewId)
            ->willThrowException(new NoSuchEntityException());

        $this->reviewAuthorResolverMock->expects($this->never())
            ->method('getName');
        $this->reviewAuthorResolverMock->expects($this->never())
            ->method('getEmail');

        $this->queueManagementMock->expects($this->never())
            ->method('add');

        $this->model->notifyAdminAboutNewCommentFromVisitor($comment);
    }

    /**
     * @return array
     */
    public function notifyAdminAboutNewCommentFromVisitorNoReviewDataProvider()
    {
        return [
            [
                null,
            ],
            [
                '',
            ],
            [
                1,
            ],
        ];
    }

    /**
     * Test notifyAdminAboutNewCommentFromVisitor method
     *
     * @param int|null $reviewId
     * @param string|null $recipientName
     * @param string|null $recipientEmail
     * @param bool $isQueueItemAdded
     * @dataProvider notifyAdminAboutNewCommentFromVisitorDataProvider
     */
    public function testNotifyAdminAboutNewCommentFromVisitor(
        $reviewId,
        $recipientName,
        $recipientEmail,
        $isQueueItemAdded
    ) {
        $commentId = 12;
        $storeId = 1;
        $comment = $this->createMock(CommentInterface::class);
        $comment->expects($this->any())
            ->method('getReviewId')
            ->willReturn($reviewId);
        $comment->expects($this->any())
            ->method('getId')
            ->willReturn($commentId);

        $review = $this->createMock(ReviewInterface::class);
        $review->expects($this->any())
            ->method('getStoreId')
            ->willReturn($storeId);

        $this->reviewRepositoryMock->expects($this->any())
            ->method('getById')
            ->with($reviewId)
            ->willReturn($review);

        $this->configMock->expects($this->once())
            ->method('getDefaultAdminRecipientName')
            ->willReturn($recipientName);
        $this->configMock->expects($this->once())
            ->method('getAdminNotificationEmail')
            ->with($storeId)
            ->willReturn($recipientEmail);

        if ($isQueueItemAdded) {
            $queueItem = $this->createMock(QueueItemInterface::class);
            $this->queueManagementMock->expects($this->once())
                ->method('add')
                ->with(
                    NotificationType::ADMIN_NEW_COMMENT_FROM_VISITOR,
                    $commentId,
                    $storeId,
                    $recipientName,
                    $recipientEmail
                )->willReturn($queueItem);
        } else {
            $this->queueManagementMock->expects($this->never())
                ->method('add');
        }

        $this->model->notifyAdminAboutNewCommentFromVisitor($comment);
    }

    /**
     * @return array
     */
    public function notifyAdminAboutNewCommentFromVisitorDataProvider()
    {
        return [
            [
                1,
                'Recipient name',
                '',
                false
            ],
            [
                1,
                '',
                'recipient@mail.com',
                false
            ],
            [
                1,
                'Recipient name',
                'recipient@mail.com',
                true
            ],
        ];
    }
}
