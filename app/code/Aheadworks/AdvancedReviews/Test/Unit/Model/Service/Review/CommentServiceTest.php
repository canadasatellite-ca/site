<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Test\Unit\Model\Service;

use Aheadworks\AdvancedReviews\Model\Source\Review\Comment\Status;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Aheadworks\AdvancedReviews\Model\Service\Review\CommentService;
use Magento\Framework\Api\DataObjectHelper;
use Aheadworks\AdvancedReviews\Api\CommentRepositoryInterface;
use Aheadworks\AdvancedReviews\Api\Data\CommentInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Aheadworks\AdvancedReviews\Model\Status\Resolver\Comment as StatusResolver;
use Aheadworks\AdvancedReviews\Model\Review\Comment\NotificationManager as CommentNotificationManager;
use Aheadworks\AdvancedReviews\Model\Review\Comment\Resolver as CommentResolver;

/**
 * Test for \Aheadworks\AdvancedReviews\Model\Service\Review\CommentService
 */
class CommentServiceTest extends TestCase
{
    /**
     * @var CommentService
     */
    private $service;

    /**
     * @var CommentRepositoryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $commentRepositoryMock;

    /**
     * @var DataObjectHelper|\PHPUnit_Framework_MockObject_MockObject
     */
    private $dataObjectHelperMock;

    /**
     * @var StatusResolver|\PHPUnit_Framework_MockObject_MockObject
     */
    private $statusResolverMock;

    /**
     * @var CommentNotificationManager|\PHPUnit_Framework_MockObject_MockObject
     */
    private $commentNotificationManagerMock;

    /**
     * @var CommentResolver|\PHPUnit_Framework_MockObject_MockObject
     */
    private $commentResolverMock;

    /**
     * @var array
     */
    private $commentData = [
        CommentInterface::ID => 1,
        CommentInterface::REVIEW_ID => 1,
        CommentInterface::CONTENT => 'Test comment content'
    ];

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->dataObjectHelperMock = $this->createPartialMock(DataObjectHelper::class, ['mergeDataObjects']);
        $this->commentRepositoryMock = $this->getMockForAbstractClass(CommentRepositoryInterface::class);
        $this->statusResolverMock = $this->createPartialMock(StatusResolver::class, ['getNewInstanceStatus']);
        $this->commentNotificationManagerMock = $this->createMock(CommentNotificationManager::class);
        $this->commentResolverMock = $this->createMock(CommentResolver::class);

        $this->commentResolverMock->expects($this->any())
            ->method('isNeedToShowOnFrontend')
            ->willReturnMap(
                [
                    [
                        Status::PENDING,
                        false
                    ],
                    [
                        Status::APPROVED,
                        true
                    ]
                ]
            );

        $this->service = $objectManager->getObject(
            CommentService::class,
            [
                'commentRepository' => $this->commentRepositoryMock,
                'dataObjectHelper' => $this->dataObjectHelperMock,
                'statusResolver' => $this->statusResolverMock,
                'commentNotificationManager' => $this->commentNotificationManagerMock,
                'commentResolver' => $this->commentResolverMock,
            ]
        );
    }

    /**
     * Test add customer comment
     *
     * @param int $status
     * @param bool $isReviewAuthorNotified
     * @param int|null $storeId
     * @dataProvider addCustomerCommentDataProvider
     */
    public function testAddCustomerComment($status, $isReviewAuthorNotified, $storeId)
    {
        $this->statusResolverMock->expects($this->any())
            ->method('getNewInstanceStatus')
            ->with($storeId)
            ->willReturn($status);

        $commentMock = $this->getCommentMock();
        $commentMock->expects($this->once())
            ->method('setStatus')
            ->with($status)
            ->willReturnSelf();
        $commentMock->expects($this->any())
            ->method('getStatus')
            ->willReturn($status);

        $this->commentRepositoryMock->expects($this->once())
            ->method('save')
            ->with($commentMock)
            ->willReturn($commentMock);

        $this->commentNotificationManagerMock->expects($this->once())
            ->method('notifyAdminAboutNewCommentFromVisitor')
            ->with($commentMock);

        if ($isReviewAuthorNotified) {
            $this->commentNotificationManagerMock->expects($this->once())
                ->method('notifyReviewAuthorAboutNewComment')
                ->with($commentMock);
        } else {
            $this->commentNotificationManagerMock->expects($this->never())
                ->method('notifyReviewAuthorAboutNewComment');
        }

        $this->assertSame(
            $commentMock,
            $this->service->addCustomerComment(
                $commentMock,
                $storeId
            )
        );
    }

    /**
     * @return array
     */
    public function addCustomerCommentDataProvider()
    {
        return [
            [
                'status' => Status::PENDING,
                'isReviewAuthorNotified' => false,
                'storeId' => null,
            ],
            [
                'status' => Status::APPROVED,
                'isReviewAuthorNotified' => true,
                'storeId' => null,
            ],
            [
                'status' => Status::PENDING,
                'isReviewAuthorNotified' => false,
                'storeId' => 1,
            ],
            [
                'status' => Status::APPROVED,
                'isReviewAuthorNotified' => true,
                'storeId' => 1,
            ],
        ];
    }

    /**
     * Test add customer comment with exception
     *
     * @param int $status
     * @param int|null $storeId
     * @dataProvider addCustomerCommentExpDataProvider
     * @expectedException \Magento\Framework\Exception\CouldNotSaveException
     * @expectedExceptionMessage Error!
     */
    public function testAddCustomerCommentExp($status, $storeId)
    {
        $this->statusResolverMock->expects($this->any())
            ->method('getNewInstanceStatus')
            ->with($storeId)
            ->willReturn($status);

        $commentMock = $this->getCommentMock();
        $commentMock->expects($this->once())
            ->method('setStatus')
            ->with($status)
            ->willReturnSelf();
        $commentMock->expects($this->any())
            ->method('getStatus')
            ->willReturn($status);
        $exception = new CouldNotSaveException(__('Error!'));

        $this->commentRepositoryMock->expects($this->once())
            ->method('save')
            ->with($commentMock)
            ->willThrowException($exception);

        $this->commentNotificationManagerMock->expects($this->never())
            ->method('notifyAdminAboutNewCommentFromVisitor');
        $this->commentNotificationManagerMock->expects($this->never())
            ->method('notifyReviewAuthorAboutNewComment');

        $this->service->addCustomerComment($commentMock, $storeId);
    }

    /**
     * @return array
     */
    public function addCustomerCommentExpDataProvider()
    {
        return [
            [
                'status' => Status::PENDING,
                'storeId' => null,
            ],
            [
                'status' => Status::APPROVED,
                'storeId' => null,
            ],
            [
                'status' => Status::PENDING,
                'storeId' => 1,
            ],
            [
                'status' => Status::APPROVED,
                'storeId' => 1,
            ],
        ];
    }

    /**
     * Test add admin comment
     */
    public function testAddAdminComment()
    {
        $commentMock = $this->getCommentMock();
        $commentMock->expects($this->once())
            ->method('setStatus')
            ->with(Status::APPROVED)
            ->willReturnSelf();
        $commentMock->expects($this->any())
            ->method('getStatus')
            ->willReturn(Status::APPROVED);

        $this->commentRepositoryMock->expects($this->once())
            ->method('save')
            ->with($commentMock)
            ->willReturn($commentMock);

        $this->commentNotificationManagerMock->expects($this->once())
            ->method('notifyReviewAuthorAboutNewComment')
            ->with($commentMock);

        $this->assertSame($commentMock, $this->service->addAdminComment($commentMock));
    }

    /**
     * Test add admin comment with exception
     *
     * @expectedException \Magento\Framework\Exception\CouldNotSaveException
     * @expectedExceptionMessage Error!
     */
    public function testAddAdminCommentExp()
    {
        $commentMock = $this->getCommentMock();
        $commentMock->expects($this->once())
            ->method('setStatus')
            ->with(Status::APPROVED)
            ->willReturnSelf();
        $commentMock->expects($this->any())
            ->method('getStatus')
            ->willReturn(Status::APPROVED);
        $exception = new CouldNotSaveException(__('Error!'));

        $this->commentRepositoryMock->expects($this->once())
            ->method('save')
            ->with($commentMock)
            ->willThrowException($exception);
        $this->commentNotificationManagerMock->expects($this->never())
            ->method('notifyReviewAuthorAboutNewComment');

        $this->service->addAdminComment($commentMock);
    }

    /**
     * Test update comment
     *
     * @param int $status
     * @param bool $isReviewAuthorNotified
     * @dataProvider updateCommentDataProvider
     */
    public function testUpdateComment($status, $isReviewAuthorNotified)
    {
        $commentMock = $this->getCommentMock();

        $commentToMerge = $this->getCommentMock();

        $commentToSave = $this->getCommentMock();
        $commentToSave->expects($this->any())
            ->method('getStatus')
            ->willReturn($status);

        $this->commentRepositoryMock->expects($this->once())
            ->method('getById')
            ->with($this->commentData[CommentInterface::ID])
            ->willReturn($commentToMerge);
        $this->dataObjectHelperMock->expects($this->once())
            ->method('mergeDataObjects')
            ->with(CommentInterface::class, $commentMock, $commentToMerge)
            ->willReturn($commentToSave);
        $this->commentRepositoryMock->expects($this->once())
            ->method('save')
            ->with($commentToSave)
            ->willReturn($commentToSave);

        if ($isReviewAuthorNotified) {
            $this->commentNotificationManagerMock->expects($this->once())
                ->method('notifyReviewAuthorAboutNewComment')
                ->with($commentMock);
        } else {
            $this->commentNotificationManagerMock->expects($this->never())
                ->method('notifyReviewAuthorAboutNewComment');
        }

        $this->assertSame($commentToSave, $this->service->updateComment($commentMock));
    }

    /**
     * @return array
     */
    public function updateCommentDataProvider()
    {
        return [
            [
                'status' => Status::PENDING,
                'isReviewAuthorNotified' => false,
            ],
            [
                'status' => Status::APPROVED,
                'isReviewAuthorNotified' => true,
            ],
            [
                'status' => Status::NOT_APPROVED,
                'isReviewAuthorNotified' => false,
            ],
        ];
    }

    /**
     * Test update comment
     *
     * @expectedException \Magento\Framework\Exception\NoSuchEntityException
     * @expectedExceptionMessage Error!
     */
    public function testUpdateCommentExp()
    {
        $comment = $this->getCommentMock();
        $exception = new NoSuchEntityException(__('Error!'));

        $this->commentRepositoryMock->expects($this->once())
            ->method('getById')
            ->with($this->commentData[CommentInterface::ID])
            ->willThrowException($exception);
        $this->commentRepositoryMock->expects($this->never())
            ->method('save');
        $this->dataObjectHelperMock->expects($this->never())
            ->method('mergeDataObjects');

        $this->service->updateComment($comment);
    }

    /**
     * Test delete comment
     */
    public function testDeleteComment()
    {
        $comment = $this->getCommentMock();

        $this->commentRepositoryMock->expects($this->once())
            ->method('delete')
            ->with($comment)
            ->willReturn(true);

        $this->assertTrue($this->service->deleteComment($comment));
    }

    /**
     * Test delete comment with exception
     *
     * @expectedException \Magento\Framework\Exception\CouldNotDeleteException
     * @expectedExceptionMessage Error!
     */
    public function testDeleteCommentExp()
    {
        $comment = $this->getCommentMock();
        $exception = new CouldNotDeleteException(__('Error!'));

        $this->commentRepositoryMock->expects($this->once())
            ->method('delete')
            ->with($comment)
            ->willThrowException($exception);

        $this->service->deleteComment($comment);
    }

    /**
     * Test delete comment by id
     */
    public function testDeleteCommentById()
    {
        $comment = $this->getCommentMock();

        $this->commentRepositoryMock->expects($this->once())
            ->method('getById')
            ->with($this->commentData[CommentInterface::ID])
            ->willReturn($comment);
        $this->commentRepositoryMock->expects($this->once())
            ->method('delete')
            ->with($comment)
            ->willReturn(true);

        $this->assertTrue($this->service->deleteCommentById($this->commentData[CommentInterface::ID]));
    }

    /**
     * Test delete comment by id with exception
     *
     * @param NoSuchEntityException|CouldNotDeleteException $exception
     * @param string $excClass
     * @param string $excMsg
     * @dataProvider testDeleteCommentByIdExpProvider
     */
    public function testDeleteCommentByIdExp($exception, $excClass, $excMsg)
    {
        $comment = $this->getCommentMock();
        $isNoSuchEntity = $exception instanceof NoSuchEntityException;
        $callsCount = $isNoSuchEntity ? 0 : 1;

        if ($isNoSuchEntity) {
            $this->commentRepositoryMock->expects($this->once())
                ->method('getById')
                ->with($this->commentData[CommentInterface::ID])
                ->willThrowException($exception);
        } else {
            $this->commentRepositoryMock->expects($this->once())
                ->method('getById')
                ->with($this->commentData[CommentInterface::ID])
                ->willReturn($comment);
        }
        $this->commentRepositoryMock->expects($this->exactly($callsCount))
            ->method('delete')
            ->with($comment)
            ->willThrowException($exception);

        $this->expectException($excClass);
        $this->expectExceptionMessage($excMsg);

        $this->service->deleteCommentById($this->commentData[CommentInterface::ID]);
    }

    /**
     * @return array
     */
    public function testDeleteCommentByIdExpProvider()
    {
        return [
            [
                new CouldNotDeleteException(__('Cannot delete comment message.')),
                CouldNotDeleteException::class,
                'Cannot delete comment message.'
            ],
            [
                new NoSuchEntityException(__('No such comment message.')),
                NoSuchEntityException::class,
                'No such comment message.'
            ]
        ];
    }

    /**
     * Get comment mock
     *
     * @return CommentInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private function getCommentMock()
    {
        $commentMock = $this->getMockForAbstractClass(CommentInterface::class);

        $commentMock->expects($this->atMost(2))
            ->method('getId')
            ->willReturn($this->commentData[CommentInterface::ID]);
        $commentMock->expects($this->atMost(2))
            ->method('getReviewId')
            ->willReturn($this->commentData[CommentInterface::REVIEW_ID]);

        return $commentMock;
    }
}
