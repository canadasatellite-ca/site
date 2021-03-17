<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Test\Unit\Model\Service;

use Aheadworks\AdvancedReviews\Api\CommentManagementInterface;
use Aheadworks\AdvancedReviews\Api\Data\ReviewInterface;
use Aheadworks\AdvancedReviews\Model\Source\Review\Comment\Type;
use Magento\Framework\Exception\LocalizedException;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Aheadworks\AdvancedReviews\Model\Review\Comment\Processor as CommentProcessor;
use Aheadworks\AdvancedReviews\Api\Data\CommentInterface;

/**
 * Class CommentProcessorTest
 * @package Aheadworks\AdvancedReviews\Test\Unit\Model\Service
 */
class CommentProcessorTest extends TestCase
{
    /**
     * @var CommentProcessor
     */
    private $processor;

    /**
     * @var CommentManagementInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $commentManagementMock;

    /**
     * @var array
     */
    private $commentData = [
        CommentInterface::ID => 1,
        CommentInterface::REVIEW_ID => 1,
        CommentInterface::TYPE => Type::ADMIN,
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
        $this->commentManagementMock = $this->createMock(CommentManagementInterface::class);

        $this->processor = $objectManager->getObject(
            CommentProcessor::class,
            [
                'commentManagement' => $this->commentManagementMock
            ]
        );
    }

    /**
     * Test add comment
     *
     * @param CommentInterface|\PHPUnit_Framework_MockObject_MockObject $comment
     * @param ReviewInterface|\PHPUnit_Framework_MockObject_MockObject $review
     * @param bool $isContentEmpty
     * @throws LocalizedException
     * @dataProvider processCommentProvider
     */
    public function testProcessAdminComment($comment, $review, $isContentEmpty)
    {
        $callsCount = $isContentEmpty ? 0 : 1;

        $this->commentManagementMock->expects($this->exactly($callsCount))
            ->method('addAdminComment')
            ->with($comment)
            ->willReturn($comment);

        $this->processor->processAdminComment($review);
    }

    /**
     * Test add comment with exception
     *
     * @expectedException \Magento\Framework\Exception\LocalizedException
     * @expectedExceptionMessage Test message!
     * @throws LocalizedException
     */
    public function testProcessAdminCommentWithException()
    {
        $exception = new LocalizedException(__('Test message!'));
        $comment = $this->getCommentMock(null, $this->commentData[CommentInterface::CONTENT]);
        $review = $this->getReviewMock($comment);

        $this->commentManagementMock->expects($this->once())
            ->method('addAdminComment')
            ->with($comment)
            ->willThrowException($exception);

        $this->processor->processAdminComment($review);
    }

    /**
     * @return array
     */
    public function processCommentProvider()
    {
        $comment = $this->getCommentMock(null, $this->commentData[CommentInterface::CONTENT]);
        $emptyComment = $this->getCommentMock(null);

        return [
            [
                null,
                $review = $this->getReviewMock(null),
                true
            ],
            [
                $comment,
                $review = $this->getReviewMock($comment),
                false
            ],
            [
                $emptyComment,
                $review = $this->getReviewMock($emptyComment),
                true
            ]
        ];
    }

    /**
     * Get comment mock
     *
     * @param int $id
     * @param string $content
     * @return CommentInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private function getCommentMock($id, $content = '')
    {
        $commentMock = $this->getMockForAbstractClass(CommentInterface::class);

        $commentMock->expects($this->atMost(2))
            ->method('getId')
            ->willReturn($id);
        $commentMock->expects($this->atMost(2))
            ->method('getContent')
            ->willReturn($content);

        $commentMock->expects($this->once())
            ->method('setReviewId')
            ->with($this->commentData[CommentInterface::REVIEW_ID])
            ->willReturnSelf();
        $commentMock->expects($this->once())
            ->method('setType')
            ->with($this->commentData[CommentInterface::TYPE])
            ->willReturnSelf();

        return $commentMock;
    }

    /**
     * Get review mock
     *
     * @param CommentInterface|\PHPUnit_Framework_MockObject_MockObject $comment
     * @return ReviewInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private function getReviewMock($comment)
    {
        $reviewMock = $this->getMockForAbstractClass(ReviewInterface::class);

        $reviewMock->expects($this->atMost(1))
            ->method('getAdminComment')
            ->willReturn($comment);
        $reviewMock->expects($this->atMost(1))
            ->method('getId')
            ->willReturn($this->commentData[CommentInterface::REVIEW_ID]);

        return $reviewMock;
    }
}
