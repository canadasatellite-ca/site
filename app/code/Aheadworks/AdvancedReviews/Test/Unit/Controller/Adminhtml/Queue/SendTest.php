<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Test\Unit\Controller\Adminhtml\Queue;

use Aheadworks\AdvancedReviews\Controller\Adminhtml\Queue\Send;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\TestCase;
use Magento\Backend\App\Action\Context;
use Aheadworks\AdvancedReviews\Api\QueueManagementInterface;
use Aheadworks\AdvancedReviews\Api\Data\QueueItemInterface;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Backend\Model\View\Result\RedirectFactory as ResultRedirectFactory;
use Magento\Framework\Message\ManagerInterface as MessageManagerInterface;
use Magento\Framework\App\RequestInterface;

/**
 * Test for \Aheadworks\AdvancedReviews\Controller\Adminhtml\Queue\Send
 */
class SendTest extends TestCase
{
    /**
     * @var Send
     */
    private $controller;

    /**
     * @var Context|\PHPUnit_Framework_MockObject_MockObject
     */
    private $contextMock;

    /**
     * @var QueueManagementInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $queueManagementMock;

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

        $this->queueManagementMock = $this->createMock(QueueManagementInterface::class);

        $this->controller = $objectManager->getObject(
            Send::class,
            [
                'context' => $this->contextMock,
                'queueManagement' => $this->queueManagementMock,
            ]
        );
    }

    /**
     * Test execute method - no id in the request
     */
    public function testExecuteNoId()
    {
        $queueItemId = false;

        $resultRedirect = $this->createMock(Redirect::class);
        $resultRedirect->expects($this->once())
            ->method('setRefererUrl')
            ->willReturnSelf();

        $this->resultRedirectFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($resultRedirect);

        $this->requestMock->expects($this->once())
            ->method('getParam')
            ->with(QueueItemInterface::ID, false)
            ->willReturn($queueItemId);

        $this->queueManagementMock->expects($this->never())
            ->method('sendById');

        $this->assertSame($resultRedirect, $this->controller->execute());
    }

    /**
     * Test execute method with error
     */
    public function testExecuteError()
    {
        $queueItemId = 12;
        $exception = new LocalizedException(__('Error!'));

        $resultRedirect = $this->createMock(Redirect::class);
        $resultRedirect->expects($this->once())
            ->method('setRefererUrl')
            ->willReturnSelf();

        $this->resultRedirectFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($resultRedirect);

        $this->requestMock->expects($this->once())
            ->method('getParam')
            ->with(QueueItemInterface::ID, false)
            ->willReturn($queueItemId);

        $this->queueManagementMock->expects($this->once())
            ->method('sendById')
            ->with($queueItemId)
            ->willThrowException($exception);

        $this->messageManagerMock->expects($this->never())
            ->method('addSuccessMessage');

        $this->messageManagerMock->expects($this->never())
            ->method('addErrorMessage');

        $this->messageManagerMock->expects($this->once())
            ->method('addExceptionMessage')
            ->with($exception, __('Something went wrong while sending the email.'));

        $this->assertSame($resultRedirect, $this->controller->execute());
    }

    /**
     * Test execute method, email was not sent
     */
    public function testExecuteNotSent()
    {
        $queueItemId = 12;
        $executionResult = false;

        $resultRedirect = $this->createMock(Redirect::class);
        $resultRedirect->expects($this->once())
            ->method('setRefererUrl')
            ->willReturnSelf();

        $this->resultRedirectFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($resultRedirect);

        $this->requestMock->expects($this->once())
            ->method('getParam')
            ->with(QueueItemInterface::ID, false)
            ->willReturn($queueItemId);

        $this->queueManagementMock->expects($this->once())
            ->method('sendById')
            ->with($queueItemId)
            ->willReturn($executionResult);

        $this->messageManagerMock->expects($this->never())
            ->method('addSuccessMessage');

        $this->messageManagerMock->expects($this->once())
            ->method('addErrorMessage')
            ->with(__('This email can not be sent.'));

        $this->messageManagerMock->expects($this->never())
            ->method('addExceptionMessage');

        $this->assertSame($resultRedirect, $this->controller->execute());
    }

    /**
     * Test execute method
     */
    public function testExecute()
    {
        $queueItemId = 12;
        $executionResult = true;

        $resultRedirect = $this->createMock(Redirect::class);
        $resultRedirect->expects($this->once())
            ->method('setRefererUrl')
            ->willReturnSelf();

        $this->resultRedirectFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($resultRedirect);

        $this->requestMock->expects($this->once())
            ->method('getParam')
            ->with(QueueItemInterface::ID, false)
            ->willReturn($queueItemId);

        $this->queueManagementMock->expects($this->once())
            ->method('sendById')
            ->with($queueItemId)
            ->willReturn($executionResult);

        $this->messageManagerMock->expects($this->once())
            ->method('addSuccessMessage')
            ->with(__('Email was successfully sent.'));

        $this->messageManagerMock->expects($this->never())
            ->method('addErrorMessage');

        $this->messageManagerMock->expects($this->never())
            ->method('addExceptionMessage');

        $this->assertSame($resultRedirect, $this->controller->execute());
    }
}
