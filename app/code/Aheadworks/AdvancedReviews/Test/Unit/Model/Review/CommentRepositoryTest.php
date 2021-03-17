<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Test\Unit\Model\Review;

use Aheadworks\AdvancedReviews\Model\Review\CommentRepository;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\TestCase;
use Aheadworks\AdvancedReviews\Api\Data\CommentInterface;
use Aheadworks\AdvancedReviews\Model\Review\Comment as CommentModel;
use Aheadworks\AdvancedReviews\Api\Data\CommentInterfaceFactory;
use Aheadworks\AdvancedReviews\Model\ResourceModel\Review\Comment as CommentResourceModel;

/**
 * Class CommentRepositoryTest
 *
 * @package Aheadworks\AdvancedReviews\Test\Unit\Model\Review
 */
class CommentRepositoryTest extends TestCase
{
    /**
     * @var CommentRepository
     */
    private $model;

    /**
     * @var CommentResourceModel|\PHPUnit_Framework_MockObject_MockObject
     */
    private $resourceMock;

    /**
     * @var CommentInterfaceFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $commentInterfaceFactoryMock;

    /**
     * @var array
     */
    private $commentData = [
        CommentInterface::ID => 1,
    ];

    /**
     * Init mocks for tests
     *
     * @return void
     */
    protected function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->resourceMock = $this->createPartialMock(
            CommentResourceModel::class,
            [
                'save',
                'load',
                'delete',
                'getCommentIdByReviewId'
            ]
        );
        $this->commentInterfaceFactoryMock = $this->createPartialMock(
            CommentInterfaceFactory::class,
            [
                'create'
            ]
        );

        $this->model = $objectManager->getObject(
            CommentRepository::class,
            [
                'resource' => $this->resourceMock,
                'commentInterfaceFactory' => $this->commentInterfaceFactoryMock,
            ]
        );
    }

    /**
     * Testing of save method
     */
    public function testSave()
    {
        /** @var CommentInterface|\PHPUnit_Framework_MockObject_MockObject $commentMock */
        $commentMock = $this->createPartialMock(
            CommentModel::class,
            [
                'getId',
            ]
        );

        $commentMock->expects($this->once())
            ->method('getId')
            ->willReturn($this->commentData[CommentInterface::ID]);
        $this->resourceMock->expects($this->once())
            ->method('save')
            ->willReturnSelf();

        $this->assertSame($commentMock, $this->model->save($commentMock));
    }

    /**
     * Testing of save method on exception
     *
     * @expectedException \Magento\Framework\Exception\CouldNotSaveException
     * @expectedExceptionMessage Test message
     */
    public function testSaveWithException()
    {
        $exception = new \Exception('Test message');
        /** @var CommentInterface|\PHPUnit_Framework_MockObject_MockObject $commentMock */
        $commentMock = $this->createPartialMock(
            CommentModel::class,
            [
                'getId',
            ]
        );

        $commentMock->expects($this->never())
            ->method('getId');
        $this->resourceMock->expects($this->once())
            ->method('save')
            ->willThrowException($exception);

        $this->model->save($commentMock);
    }

    /**
     * Testing of delete method
     */
    public function testDelete()
    {
        /** @var CommentInterface|\PHPUnit_Framework_MockObject_MockObject $commentMock */
        $commentMock = $this->createPartialMock(
            CommentModel::class,
            [
                'getId',
            ]
        );

        $commentMock->expects($this->once())
            ->method('getId')
            ->willReturn($this->commentData[CommentInterface::ID]);
        $this->resourceMock->expects($this->once())
            ->method('delete')
            ->willReturnSelf();

        $this->assertTrue($this->model->delete($commentMock));
    }

    /**
     * Testing of delete method on exception
     *
     * @expectedException \Magento\Framework\Exception\CouldNotDeleteException
     * @expectedExceptionMessage Test message
     */
    public function testDeleteWithException()
    {
        $exception = new \Exception('Test message');
        /** @var CommentInterface|\PHPUnit_Framework_MockObject_MockObject $commentMock */
        $commentMock = $this->createPartialMock(
            CommentModel::class,
            [
                'getId',
            ]
        );

        $commentMock->expects($this->never())
            ->method('getId');
        $this->resourceMock->expects($this->once())
            ->method('delete')
            ->willThrowException($exception);

        $this->model->delete($commentMock);
    }

    /**
     * Testing of getById method
     */
    public function testGetById()
    {
        $commentId = 1;

        /** @var CommentInterface|\PHPUnit_Framework_MockObject_MockObject $commentMock */
        $commentMock = $this->createPartialMock(
            CommentModel::class,
            [
                'getId',
            ]
        );
        $this->commentInterfaceFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($commentMock);

        $this->resourceMock->expects($this->once())
            ->method('load')
            ->with($commentMock, $commentId)
            ->willReturnSelf();
        $commentMock->expects($this->once())
            ->method('getId')
            ->willReturn($commentId);

        $this->assertSame($commentMock, $this->model->getById($commentId));
    }

    /**
     * Testing of getById method on exception
     *
     * @expectedException \Magento\Framework\Exception\NoSuchEntityException
     * @expectedExceptionMessage No such entity with id = 1
     */
    public function testGetByIdOnException()
    {
        $commentId = 1;

        /** @var CommentInterface|\PHPUnit_Framework_MockObject_MockObject $commentMock */
        $commentMock = $this->createPartialMock(
            CommentModel::class,
            [
                'getId',
            ]
        );
        $this->commentInterfaceFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($commentMock);

        $this->resourceMock->expects($this->once())
            ->method('load')
            ->with($commentMock, $commentId)
            ->willReturnSelf();
        $commentMock->expects($this->once())
            ->method('getId')
            ->willReturn(null);

        $this->model->getById($commentId);
    }
}
