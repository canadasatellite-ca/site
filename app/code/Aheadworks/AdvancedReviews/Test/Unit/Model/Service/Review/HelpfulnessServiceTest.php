<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Test\Unit\Model\Service;

use Aheadworks\AdvancedReviews\Model\Review\Helpfulness\Vote\ProcessorInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Aheadworks\AdvancedReviews\Model\Service\Review\HelpfulnessService;
use Aheadworks\AdvancedReviews\Api\ReviewRepositoryInterface;
use Aheadworks\AdvancedReviews\Api\Data\ReviewInterface;
use Aheadworks\AdvancedReviews\Api\Data\VoteResultInterfaceFactory;
use Aheadworks\AdvancedReviews\Api\Data\VoteResultInterface;
use Aheadworks\AdvancedReviews\Model\Review\Helpfulness\Vote\ProcessorPool;

/**
 * Class HelpfulnessServiceTest
 * @package Aheadworks\AdvancedReviews\Test\Unit\Model\Service
 */
class HelpfulnessServiceTest extends TestCase
{
    /**
     * @var HelpfulnessService
     */
    private $service;

    /**
     * @var VoteResultInterfaceFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $voteResultFactoryMock;

    /**
     * @var ReviewRepositoryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $reviewRepositoryMock;

    /**
     * @var ProcessorPool|\PHPUnit_Framework_MockObject_MockObject
     */
    private $processorPoolMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp()
    {
        $objectManager = new ObjectManager($this);
        $this->voteResultFactoryMock = $this->createMock(VoteResultInterfaceFactory::class);
        $this->reviewRepositoryMock = $this->createMock(ReviewRepositoryInterface::class);
        $this->processorPoolMock = $this->createMock(ProcessorPool::class);

        $this->service = $objectManager->getObject(
            HelpfulnessService::class,
            [
                'voteResultFactory' => $this->voteResultFactoryMock,
                'reviewRepository' => $this->reviewRepositoryMock,
                'processorPool' => $this->processorPoolMock
            ]
        );
    }

    /**
     * Test vote method
     */
    public function testVote()
    {
        $reviewMock = $this->createMock(ReviewInterface::class);
        $processorMock = $this->createMock(ProcessorInterface::class);
        $voteResultMock = $this->createMock(VoteResultInterface::class);
        $action = 'vote_like';
        $reviewId = 1;
        $likesCount = 1;
        $dislikesCount = 0;

        $this->reviewRepositoryMock->expects($this->once())
            ->method('getById')
            ->with($reviewId)
            ->willReturn($reviewMock);
        $this->processorPoolMock->expects($this->once())
            ->method('getByAction')
            ->with($action)
            ->willReturn($processorMock);
        $processorMock->expects($this->once())
            ->method('process')
            ->with($reviewMock)
            ->willReturn($reviewMock);
        $this->reviewRepositoryMock->expects($this->once())
            ->method('save')
            ->with($reviewMock)
            ->willReturn($reviewMock);
        $this->voteResultFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($voteResultMock);
        $reviewMock->expects($this->once())
            ->method('getVotesPositive')
            ->willReturn($likesCount);
        $reviewMock->expects($this->once())
            ->method('getVotesNegative')
            ->willReturn($dislikesCount);
        $voteResultMock->expects($this->once())
            ->method('setLikeCount')
            ->with($likesCount)
            ->willReturnSelf();
        $voteResultMock->expects($this->once())
            ->method('setDislikeCount')
            ->with($dislikesCount)
            ->willReturnSelf();

        $this->assertSame($voteResultMock, $this->service->vote($reviewId, $action));
    }

    /**
     * Test vote method with exception
     *
     * @param NoSuchEntityException|CouldNotSaveException $exception
     * @param string $excClass
     * @param string $excMessage
     * @dataProvider testVoteWithExceptionProvider
     */
    public function testVoteWithException($exception, $excClass, $excMessage)
    {
        $reviewMock = $this->createMock(ReviewInterface::class);
        $processorMock = $this->createMock(ProcessorInterface::class);
        $action = 'vote_like';
        $reviewId = 1;

        if ($exception instanceof NoSuchEntityException) {
            $this->reviewRepositoryMock->expects($this->once())
                ->method('getById')
                ->with($reviewId)
                ->willThrowException($exception);
        } else {
            $this->reviewRepositoryMock->expects($this->once())
                ->method('getById')
                ->with($reviewId)
                ->willReturn($reviewMock);
            $this->processorPoolMock->expects($this->once())
                ->method('getByAction')
                ->with($action)
                ->willReturn($processorMock);
            $processorMock->expects($this->once())
                ->method('process')
                ->with($reviewMock)
                ->willReturn($reviewMock);
            $this->reviewRepositoryMock->expects($this->once())
                ->method('save')
                ->with($reviewMock)
                ->willThrowException($exception);
        }

        $this->expectException($excClass);
        $this->expectExceptionMessage($excMessage);

        $this->service->vote($reviewId, $action);
    }

    /**
     * @return array
     */
    public function testVoteWithExceptionProvider()
    {
        return [
            [
                new NoSuchEntityException(__('No such entity with id = 1')),
                NoSuchEntityException::class,
                'No such entity with id = 1'
            ],
            [
                new CouldNotSaveException(__('Cannot save review!')),
                CouldNotSaveException::class,
                'Cannot save review!'
            ]
        ];
    }
}
