<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Test\Unit\Model\Review;

use Aheadworks\AdvancedReviews\Model\Review\NotificationManager;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Aheadworks\AdvancedReviews\Api\Data\ReviewInterface;
use Aheadworks\AdvancedReviews\Model\Source\Email\Type as NotificationType;
use Aheadworks\AdvancedReviews\Api\QueueManagementInterface;
use Aheadworks\AdvancedReviews\Model\Review\Author\Resolver as ReviewAuthorResolver;
use Aheadworks\AdvancedReviews\Model\Config;
use Aheadworks\AdvancedReviews\Api\Data\QueueItemInterface;

/**
 * Test for \Aheadworks\AdvancedReviews\Model\Review\NotificationManager
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

        $this->model = $objectManager->getObject(
            NotificationManager::class,
            [
                'queueManagement' => $this->queueManagementMock,
                'reviewAuthorResolver' => $this->reviewAuthorResolverMock,
                'config' => $this->configMock,
            ]
        );
    }

    /**
     * Test notifyAuthorAboutReviewApproval method
     *
     * @param int $reviewId
     * @param string|null $recipientName
     * @param string|null $recipientEmail
     * @param bool $isQueueItemAdded
     * @dataProvider notifyAuthorAboutReviewApprovalDataProvider
     */
    public function testNotifyAuthorAboutReviewApproval(
        $reviewId,
        $recipientName,
        $recipientEmail,
        $isQueueItemAdded
    ) {
        $reviewStoreId = 1;
        $review = $this->getReviewMock($reviewId, $reviewStoreId);

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
                    NotificationType::SUBSCRIBER_REVIEW_APPROVED,
                    $reviewId,
                    $reviewStoreId,
                    $recipientName,
                    $recipientEmail
                )->willReturn($queueItem);
        } else {
            $this->queueManagementMock->expects($this->never())
                ->method('add');
        }

        $this->model->notifyAuthorAboutReviewApproval($review);
    }

    /**
     * @return array
     */
    public function notifyAuthorAboutReviewApprovalDataProvider()
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
     * Test notifyAdminAboutNewReview method
     *
     * @param int $reviewId
     * @param string|null $recipientName
     * @param string|null $recipientEmail
     * @param bool $isQueueItemAdded
     * @dataProvider notifyAdminAboutNewReviewDataProvider
     */
    public function testNotifyAdminAboutNewReview(
        $reviewId,
        $recipientName,
        $recipientEmail,
        $isQueueItemAdded
    ) {
        $reviewStoreId = 1;
        $review = $this->getReviewMock($reviewId, $reviewStoreId);

        $this->configMock->expects($this->once())
            ->method('getDefaultAdminRecipientName')
            ->willReturn($recipientName);
        $this->configMock->expects($this->once())
            ->method('getAdminNotificationEmail')
            ->with($reviewStoreId)
            ->willReturn($recipientEmail);

        if ($isQueueItemAdded) {
            $queueItem = $this->createMock(QueueItemInterface::class);
            $this->queueManagementMock->expects($this->once())
                ->method('add')
                ->with(
                    NotificationType::ADMIN_NEW_REVIEW,
                    $reviewId,
                    $reviewStoreId,
                    $recipientName,
                    $recipientEmail
                )->willReturn($queueItem);
        } else {
            $this->queueManagementMock->expects($this->never())
                ->method('add');
        }

        $this->model->notifyAdminAboutNewReview($review);
    }

    /**
     * @return array
     */
    public function notifyAdminAboutNewReviewDataProvider()
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

    /**
     * Test notifyAdminAboutNewCriticalReview method
     *
     * @param int $reviewId
     * @param string|null $recipientName
     * @param string|null $recipientEmail
     * @param bool $isQueueItemAdded
     * @dataProvider notifyAdminAboutNewCriticalReviewDataProvider
     */
    public function testNotifyAdminAboutNewCriticalReview(
        $reviewId,
        $recipientName,
        $recipientEmail,
        $isQueueItemAdded
    ) {
        $reviewStoreId = 1;
        $review = $this->getReviewMock($reviewId, $reviewStoreId);

        $this->configMock->expects($this->once())
            ->method('getDefaultAdminRecipientName')
            ->willReturn($recipientName);
        $this->configMock->expects($this->once())
            ->method('getEmailAddressForCriticalReviewAlert')
            ->with($reviewStoreId)
            ->willReturn($recipientEmail);

        if ($isQueueItemAdded) {
            $queueItem = $this->createMock(QueueItemInterface::class);
            $this->queueManagementMock->expects($this->once())
                ->method('add')
                ->with(
                    NotificationType::ADMIN_CRITICAL_REVIEW_ALERT,
                    $reviewId,
                    $reviewStoreId,
                    $recipientName,
                    $recipientEmail
                )->willReturn($queueItem);
        } else {
            $this->queueManagementMock->expects($this->never())
                ->method('add');
        }

        $this->model->notifyAdminAboutNewCriticalReview($review);
    }

    /**
     * @return array
     */
    public function notifyAdminAboutNewCriticalReviewDataProvider()
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

    /**
     * @param int $id
     * @param int $storeId
     * @return \PHPUnit\Framework\MockObject\MockObject|ReviewInterface
     */
    private function getReviewMock($id, $storeId)
    {
        $review = $this->createMock(ReviewInterface::class);
        $review->expects($this->any())
            ->method('getId')
            ->willReturn($id);
        $review->expects($this->any())
            ->method('getStoreId')
            ->willReturn($storeId);
        return $review;
    }
}
