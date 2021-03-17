<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Test\Unit\Model\Email;

use Aheadworks\AdvancedReviews\Model\Email\Sender;
use Aheadworks\AdvancedReviews\Model\Email\EmailMetadataInterface;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Framework\Mail\TransportInterface;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\Exception\MailException;

/**
 * Test for \Aheadworks\AdvancedReviews\Model\Email\Sender
 */
class SenderTest extends TestCase
{
    /**
     * @var Sender
     */
    private $model;

    /**
     * @var TransportBuilder|\PHPUnit_Framework_MockObject_MockObject
     */
    private $transportBuilderMock;

    /**
     * @var TransportInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $transportMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->transportBuilderMock = $this->createPartialMock(
            TransportBuilder::class,
            [
                'setTemplateIdentifier',
                'setTemplateOptions',
                'setTemplateVars',
                'setFrom',
                'addTo',
                'addAttachment',
                'getTransport'
            ]
        );
        $this->transportMock = $this->getMockForAbstractClass(TransportInterface::class);

        $this->model = $objectManager->getObject(
            Sender::class,
            [
                'transportBuilder' => $this->transportBuilderMock
            ]
        );
    }

    /**
     * Test send method
     */
    public function testSend()
    {
        $emailMetadata = [
            EmailMetadataInterface::TEMPLATE_ID => 'template_id',
            EmailMetadataInterface::TEMPLATE_OPTIONS => ['opt1', ['opt2']],
            EmailMetadataInterface::TEMPLATE_VARIABLES => ['var1', 'var2'],
            EmailMetadataInterface::SENDER_NAME => 'sender_name',
            EmailMetadataInterface::SENDER_EMAIL => 'sender_email',
            EmailMetadataInterface::RECIPIENT_NAME => 'recipient_name',
            EmailMetadataInterface::RECIPIENT_EMAIL => 'recipient_email',
        ];
        $expectedValue = true;

        $emailMetadataMock = $this->getEmailMetadataMock($emailMetadata);
        $this->initTransportBuilder($emailMetadata);

        $this->transportMock->expects($this->once())
            ->method('sendMessage');

        $this->assertEquals($expectedValue, $this->model->send($emailMetadataMock));
    }

    /**
     * Test send method if an exception occurs
     *
     * @expectedException \Magento\Framework\Exception\MailException
     * @expectedExceptionMessage Error !!!
     */
    public function testSendOnException()
    {
        $emailMetadata = [
            EmailMetadataInterface::TEMPLATE_ID => 'template_id',
            EmailMetadataInterface::TEMPLATE_OPTIONS => ['opt1', ['opt2']],
            EmailMetadataInterface::TEMPLATE_VARIABLES => ['var1', 'var2'],
            EmailMetadataInterface::SENDER_NAME => 'sender_name',
            EmailMetadataInterface::SENDER_EMAIL => 'sender_email',
            EmailMetadataInterface::RECIPIENT_NAME => 'recipient_name',
            EmailMetadataInterface::RECIPIENT_EMAIL => 'recipient_email',
        ];
        $exception = new MailException(__('Error !!!'));

        $emailMetadataMock = $this->getEmailMetadataMock($emailMetadata);
        $this->initTransportBuilder($emailMetadata);

        $this->transportMock->expects($this->once())
            ->method('sendMessage')
            ->willThrowException($exception);

        $this->model->send($emailMetadataMock);
    }

    /**
     * Get email metadata mock
     *
     * @param array $emailMetadata
     * @return EmailMetadataInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private function getEmailMetadataMock($emailMetadata)
    {
        $emailMetadataMock = $this->getMockForAbstractClass(EmailMetadataInterface::class);
        $emailMetadataMock->expects($this->once())
            ->method('getTemplateId')
            ->willReturn($emailMetadata[EmailMetadataInterface::TEMPLATE_ID]);
        $emailMetadataMock->expects($this->once())
            ->method('getTemplateOptions')
            ->willReturn($emailMetadata[EmailMetadataInterface::TEMPLATE_OPTIONS]);
        $emailMetadataMock->expects($this->once())
            ->method('getTemplateVariables')
            ->willReturn($emailMetadata[EmailMetadataInterface::TEMPLATE_VARIABLES]);
        $emailMetadataMock->expects($this->once())
            ->method('getSenderName')
            ->willReturn($emailMetadata[EmailMetadataInterface::SENDER_NAME]);
        $emailMetadataMock->expects($this->once())
            ->method('getSenderEmail')
            ->willReturn($emailMetadata[EmailMetadataInterface::SENDER_EMAIL]);
        $emailMetadataMock->expects($this->once())
            ->method('getRecipientName')
            ->willReturn($emailMetadata[EmailMetadataInterface::RECIPIENT_NAME]);
        $emailMetadataMock->expects($this->once())
            ->method('getRecipientEmail')
            ->willReturn($emailMetadata[EmailMetadataInterface::RECIPIENT_EMAIL]);

        return $emailMetadataMock;
    }

    /**
     * Init transport builder
     *
     * @param array $emailMetadata
     */
    private function initTransportBuilder($emailMetadata)
    {
        $this->transportBuilderMock->expects($this->once())
            ->method('setTemplateIdentifier')
            ->with($emailMetadata[EmailMetadataInterface::TEMPLATE_ID])
            ->willReturnSelf();
        $this->transportBuilderMock->expects($this->once())
            ->method('setTemplateOptions')
            ->with($emailMetadata[EmailMetadataInterface::TEMPLATE_OPTIONS])
            ->willReturnSelf();
        $this->transportBuilderMock->expects($this->once())
            ->method('setTemplateVars')
            ->with($emailMetadata[EmailMetadataInterface::TEMPLATE_VARIABLES])
            ->willReturnSelf();
        $this->transportBuilderMock->expects($this->once())
            ->method('setFrom')
            ->with([
                'name' => $emailMetadata[EmailMetadataInterface::SENDER_NAME],
                'email' => $emailMetadata[EmailMetadataInterface::SENDER_EMAIL]
            ])->willReturnSelf();
        $this->transportBuilderMock->expects($this->once())
            ->method('addTo')
            ->with(
                $emailMetadata[EmailMetadataInterface::RECIPIENT_EMAIL],
                $emailMetadata[EmailMetadataInterface::RECIPIENT_NAME]
            )->willReturnSelf();
        $this->transportBuilderMock->expects($this->once())
            ->method('getTransport')
            ->willReturn($this->transportMock);
    }
}
