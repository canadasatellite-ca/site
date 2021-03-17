<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Test\Unit\Model\Email\Processor;

use Aheadworks\AdvancedReviews\Api\Data\CommentInterface;
use Aheadworks\AdvancedReviews\Model\Email\EmailMetadataInterface;
use Aheadworks\AdvancedReviews\Model\Email\Processor\ReviewCommentProcessor;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Aheadworks\AdvancedReviews\Api\Data\QueueItemInterface;
use Aheadworks\AdvancedReviews\Model\Source\Email\Variables as EmailVariables;
use Aheadworks\AdvancedReviews\Api\CommentRepositoryInterface;
use Aheadworks\AdvancedReviews\Model\Config;
use Aheadworks\AdvancedReviews\Model\Email\EmailMetadataInterfaceFactory;
use Aheadworks\AdvancedReviews\Model\Email\QueueItem\SecurityCode\Generator as SecurityCodeGenerator;
use Aheadworks\AdvancedReviews\Model\Email\UrlBuilder;

/**
 * Test for \Aheadworks\AdvancedReviews\Model\Email\Processor\ReviewCommentProcessor
 */
class ReviewCommentProcessorTest extends TestCase
{
    /**
     * @var ReviewCommentProcessor
     */
    private $model;

    /**
     * @var Config|\PHPUnit_Framework_MockObject_MockObject
     */
    private $configMock;

    /**
     * @var EmailMetadataInterfaceFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $emailMetadataFactoryMock;

    /**
     * @var CommentRepositoryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $commentRepositoryMock;

    /**
     * @var CommentInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $commentMock;

    /**
     * @var SecurityCodeGenerator|\PHPUnit_Framework_MockObject_MockObject
     */
    private $securityCodeGeneratorMock;

    /**
     * @var UrlBuilder|\PHPUnit_Framework_MockObject_MockObject
     */
    private $urlBuilderMock;

    /**
     * @var array
     */
    private $emailData = [
        QueueItemInterface::STORE_ID => 1,
        QueueItemInterface::RECIPIENT_NAME => 'testCustomer',
        QueueItemInterface::RECIPIENT_EMAIL => 'testcustomer@test.com',
        QueueItemInterface::SECURITY_CODE => 'test_security_code',
        EmailMetadataInterface::SENDER_NAME => 'senderTest',
        EmailMetadataInterface::SENDER_EMAIL => 'sendertest@test.com',
        EmailMetadataInterface::TEMPLATE_ID => ReviewCommentProcessor::TEMPLATE,
        EmailVariables::UNSUBSCRIBE_URL => 'test unsubscribe url',
    ];

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->configMock = $this->createPartialMock(
            Config::class,
            [
                'getSenderName',
                'getSenderEmail'
            ]
        );
        $this->emailMetadataFactoryMock = $this->createPartialMock(EmailMetadataInterfaceFactory::class, ['create']);
        $this->commentRepositoryMock = $this->getMockForAbstractClass(CommentRepositoryInterface::class);
        $this->commentMock = $this->getMockForAbstractClass(CommentInterface::class);
        $this->securityCodeGeneratorMock = $this->createMock(SecurityCodeGenerator::class);
        $this->urlBuilderMock = $this->createMock(UrlBuilder::class);

        $this->model = $objectManager->getObject(
            ReviewCommentProcessor::class,
            [
                'config' => $this->configMock,
                'emailMetadataFactory' => $this->emailMetadataFactoryMock,
                'commentRepository' => $this->commentRepositoryMock,
                'securityCodeGenerator' => $this->securityCodeGeneratorMock,
                'urlBuilder' => $this->urlBuilderMock,
            ]
        );
    }

    /**
     * Test process method
     */
    public function testProcess()
    {
        $this->prepareMocks();
        $queueItemMock = $this->getQueueItemMock();
        $emailMetadataMock = $this->getEmailMetadataMock();

        $this->assertSame($emailMetadataMock, $this->model->process($queueItemMock));
    }

    /**
     * Prepare mocks for tests
     */
    private function prepareMocks()
    {
        $commentId = 1;

        $this->configMock->expects($this->once())
            ->method('getSenderName')
            ->willReturn($this->emailData[EmailMetadataInterface::SENDER_NAME]);
        $this->configMock->expects($this->once())
            ->method('getSenderEmail')
            ->willReturn($this->emailData[EmailMetadataInterface::SENDER_EMAIL]);
        $this->commentRepositoryMock->expects($this->once())
            ->method('getById')
            ->with($commentId)
            ->willReturn($this->commentMock);

        $this->securityCodeGeneratorMock->expects($this->once())
            ->method('getCode')
            ->willReturn($this->emailData[QueueItemInterface::SECURITY_CODE]);

        $this->urlBuilderMock->expects($this->once())
            ->method('getFrontendUrl')
            ->willReturn($this->emailData[EmailVariables::UNSUBSCRIBE_URL]);
    }

    /**
     * Get queue item
     *
     * @return QueueItemInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private function getQueueItemMock()
    {
        $commentId = 1;
        $queueItemMock = $this->getMockForAbstractClass(QueueItemInterface::class);

        $queueItemMock->expects($this->atLeastOnce())
            ->method('getStoreId')
            ->willReturn($this->emailData[QueueItemInterface::STORE_ID]);
        $queueItemMock->expects($this->atLeastOnce())
            ->method('getRecipientName')
            ->willReturn($this->emailData[QueueItemInterface::RECIPIENT_NAME]);
        $queueItemMock->expects($this->once())
            ->method('getRecipientEmail')
            ->willReturn($this->emailData[QueueItemInterface::RECIPIENT_EMAIL]);
        $queueItemMock->expects($this->once())
            ->method('getObjectId')
            ->willReturn($commentId);
        $queueItemMock->expects($this->once())
            ->method('setSecurityCode')
            ->with($this->emailData[QueueItemInterface::SECURITY_CODE])
            ->willReturnSelf();

        return $queueItemMock;
    }

    /**
     * Get email meta data
     *
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function getEmailMetadataMock()
    {
        $templateOptions = [
            'area' => 'frontend',
            'store' => $this->emailData[QueueItemInterface::STORE_ID]
        ];
        $templateVariables = [
            EmailVariables::COMMENT => $this->commentMock,
            EmailVariables::CUSTOMER_NAME => $this->emailData[QueueItemInterface::RECIPIENT_NAME],
            EmailVariables::UNSUBSCRIBE_URL => $this->emailData[EmailVariables::UNSUBSCRIBE_URL]
        ];
        $emailMetadataMock = $this->getMockForAbstractClass(EmailMetadataInterface::class);

        $emailMetadataMock->expects($this->once())
            ->method('setTemplateId')
            ->with($this->emailData[EmailMetadataInterface::TEMPLATE_ID])
            ->willReturnSelf();
        $emailMetadataMock->expects($this->once())
            ->method('setTemplateOptions')
            ->with($templateOptions)
            ->willReturnSelf();
        $emailMetadataMock->expects($this->once())
            ->method('setTemplateVariables')
            ->with($templateVariables)
            ->willReturnSelf();
        $emailMetadataMock->expects($this->once())
            ->method('setSenderName')
            ->with($this->emailData[EmailMetadataInterface::SENDER_NAME])
            ->willReturnSelf();
        $emailMetadataMock->expects($this->once())
            ->method('setSenderEmail')
            ->with($this->emailData[EmailMetadataInterface::SENDER_EMAIL])
            ->willReturnSelf();
        $emailMetadataMock->expects($this->once())
            ->method('setRecipientName')
            ->with($this->emailData[QueueItemInterface::RECIPIENT_NAME])
            ->willReturnSelf();
        $emailMetadataMock->expects($this->once())
            ->method('setRecipientEmail')
            ->with($this->emailData[QueueItemInterface::RECIPIENT_EMAIL])
            ->willReturnSelf();
        $this->emailMetadataFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($emailMetadataMock);

        return $emailMetadataMock;
    }
}
