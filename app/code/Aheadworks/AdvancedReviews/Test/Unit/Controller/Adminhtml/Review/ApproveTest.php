<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Test\Unit\Controller\Adminhtml\Review;

use Aheadworks\AdvancedReviews\Controller\Adminhtml\Review\Approve;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\TestCase;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\RequestInterface;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Backend\Model\View\Result\RedirectFactory as ResultRedirectFactory;
use Magento\Framework\Message\ManagerInterface as MessageManagerInterface;
use Aheadworks\AdvancedReviews\Api\Data\ReviewInterface;
use Aheadworks\AdvancedReviews\Api\ReviewRepositoryInterface;
use Magento\Framework\Exception\LocalizedException;
use Aheadworks\AdvancedReviews\Api\ReviewManagementInterface;
use Aheadworks\AdvancedReviews\Model\Source\Review\Status as ReviewStatusSource;

/**
 * Test for \Aheadworks\AdvancedReviews\Controller\Adminhtml\Review\Approve
 */
class ApproveTest extends TestCase
{
    /**
     * @var Approve
     */
    private $controller;

    /**
     * @var Context|\PHPUnit_Framework_MockObject_MockObject
     */
    private $contextMock;

    /**
     * @var ReviewManagementInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $reviewManagementMock;

    /**
     * @var ReviewRepositoryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $reviewRepositoryMock;

    /**
     * @var ResultRedirectFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $resultRedirectFactoryMock;

    /**
     * @var MessageManagerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $messageManagerMock;

    /**
     * @var RequestInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $requestMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->resultRedirectFactoryMock = $this->createMock(ResultRedirectFactory::class);
        $this->messageManagerMock = $this->createMock(MessageManagerInterface::class);
        $this->requestMock = $this->createMock(RequestInterface::class);

        $this->contextMock = $objectManager->getObject(
            Context::class,
            [
                'request' => $this->requestMock,
                'messageManager' => $this->messageManagerMock,
                'resultRedirectFactory' => $this->resultRedirectFactoryMock,
            ]
        );

        $this->reviewManagementMock = $this->createMock(ReviewManagementInterface::class);
        $this->reviewRepositoryMock = $this->createMock(ReviewRepositoryInterface::class);

        $this->controller = $objectManager->getObject(
            Approve::class,
            [
                'context' => $this->contextMock,
                'reviewManagement' => $this->reviewManagementMock,
                'reviewRepository' => $this->reviewRepositoryMock,
            ]
        );
    }

    /**
     * Test execute method when no review id is specified
     */
    public function testExecuteNoReviewParam()
    {
        $this->requestMock->expects($this->any())
            ->method('getParam')
            ->with(ReviewInterface::ID, false)
            ->willReturn(false);

        $this->reviewRepositoryMock->expects($this->never())
            ->method('getById');

        $this->reviewManagementMock->expects($this->never())
            ->method('updateReview');

        $resultRedirect = $this->createMock(Redirect::class);
        $resultRedirect->expects($this->once())
            ->method('setRefererUrl')
            ->willReturnSelf();

        $this->resultRedirectFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($resultRedirect);

        $this->assertSame($resultRedirect, $this->controller->execute());
    }

    /**
     * Test execute method when repository throws an exception
     */
    public function testExecuteRepositoryError()
    {
        $reviewId = 3;
        $errorMessage = __('Error!');

        $this->requestMock->expects($this->any())
            ->method('getParam')
            ->with(ReviewInterface::ID, false)
            ->willReturn($reviewId);

        $this->reviewRepositoryMock->expects($this->once())
            ->method('getById')
            ->with($reviewId)
            ->willThrowException(new LocalizedException($errorMessage));

        $this->reviewManagementMock->expects($this->never())
            ->method('updateReview');

        $this->messageManagerMock->expects($this->once())
            ->method('addErrorMessage')
            ->with($errorMessage);

        $resultRedirect = $this->createMock(Redirect::class);
        $resultRedirect->expects($this->once())
            ->method('setRefererUrl')
            ->willReturnSelf();

        $this->resultRedirectFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($resultRedirect);

        $this->assertSame($resultRedirect, $this->controller->execute());
    }

    /**
     * Test execute method when service throws an exception
     */
    public function testExecuteServiceError()
    {
        $reviewId = 3;
        $errorMessage = __('Error!');

        $this->requestMock->expects($this->any())
            ->method('getParam')
            ->with(ReviewInterface::ID, false)
            ->willReturn($reviewId);

        $review = $this->createMock(ReviewInterface::class);
        $review->expects($this->once())
            ->method('setStatus')
            ->with(ReviewStatusSource::APPROVED)
            ->willReturnSelf();

        $this->reviewRepositoryMock->expects($this->once())
            ->method('getById')
            ->with($reviewId)
            ->willReturn($review);

        $this->reviewManagementMock->expects($this->once())
            ->method('updateReview')
            ->with($review)
            ->willThrowException(new LocalizedException($errorMessage));

        $this->messageManagerMock->expects($this->once())
            ->method('addErrorMessage')
            ->with($errorMessage);

        $resultRedirect = $this->createMock(Redirect::class);
        $resultRedirect->expects($this->once())
            ->method('setRefererUrl')
            ->willReturnSelf();

        $this->resultRedirectFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($resultRedirect);

        $this->assertSame($resultRedirect, $this->controller->execute());
    }

    /**
     * Test execute method
     */
    public function testExecute()
    {
        $reviewId = 3;

        $this->requestMock->expects($this->any())
            ->method('getParam')
            ->with(ReviewInterface::ID, false)
            ->willReturn($reviewId);

        $review = $this->createMock(ReviewInterface::class);
        $review->expects($this->once())
            ->method('setStatus')
            ->with(ReviewStatusSource::APPROVED)
            ->willReturnSelf();

        $this->reviewRepositoryMock->expects($this->once())
            ->method('getById')
            ->with($reviewId)
            ->willReturn($review);

        $this->reviewManagementMock->expects($this->once())
            ->method('updateReview')
            ->with($review)
            ->willReturn($review);

        $this->messageManagerMock->expects($this->once())
            ->method('addSuccessMessage')
            ->with(__('The review was successfully approved.'));

        $resultRedirect = $this->createMock(Redirect::class);
        $resultRedirect->expects($this->once())
            ->method('setRefererUrl')
            ->willReturnSelf();

        $this->resultRedirectFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($resultRedirect);

        $this->assertSame($resultRedirect, $this->controller->execute());
    }
}
