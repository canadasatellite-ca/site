<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Test\Unit\Model\Email\Processor;

use Aheadworks\AdvancedReviews\Model\Email\EmailMetadataInterface;
use Aheadworks\AdvancedReviews\Model\Review;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Framework\Exception\MailException;
use Magento\Framework\Exception\NoSuchEntityException;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Aheadworks\AdvancedReviews\Api\Data\QueueItemInterface;
use Aheadworks\AdvancedReviews\Model\Source\Email\Variables as EmailVariables;
use Aheadworks\AdvancedReviews\Api\Data\ReviewInterface;
use Aheadworks\AdvancedReviews\Api\ReviewRepositoryInterface;
use Aheadworks\AdvancedReviews\Model\Config;
use Aheadworks\AdvancedReviews\Model\Email\EmailMetadataInterfaceFactory;
use Aheadworks\AdvancedReviews\Model\Email\Processor\NewReviewProcessor;
use Aheadworks\AdvancedReviews\Model\Review\Rating\Resolver;
use Aheadworks\AdvancedReviews\Model\Email\UrlBuilder;
use Aheadworks\AdvancedReviews\Model\Product\Resolver as ProductResolver;

/**
 * Test for \Aheadworks\AdvancedReviews\Model\Email\Processor\NewReviewProcessor
 */
class NewReviewProcessorTest extends TestCase
{
    /**
     * @var NewReviewProcessor
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
     * @var UrlBuilder|\PHPUnit_Framework_MockObject_MockObject
     */
    private $urlBuilderMock;

    /**
     * @var Resolver|\PHPUnit_Framework_MockObject_MockObject
     */
    private $ratingResolverMock;

    /**
     * @var ReviewRepositoryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $reviewRepositoryMock;

    /**
     * @var Review|\PHPUnit_Framework_MockObject_MockObject
     */
    private $reviewMock;

    /**
     * @var ProductResolver|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $productResolverMock;

    /**
     * @var array
     */
    private $emailData = [
        QueueItemInterface::STORE_ID => 1,
        QueueItemInterface::RECIPIENT_NAME => 'testCustomer',
        QueueItemInterface::RECIPIENT_EMAIL => 'testcustomer@test.com',
        EmailMetadataInterface::SENDER_NAME => 'senderTest',
        EmailMetadataInterface::SENDER_EMAIL => 'sendertest@test.com',
        EmailMetadataInterface::TEMPLATE_ID => 'new_review_template',
        ProductInterface::NAME => 'Test product name'
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
                'getAdminNotificationTemplate',
                'getSenderName',
                'getSenderEmail'
            ]
        );
        $this->emailMetadataFactoryMock = $this->createPartialMock(EmailMetadataInterfaceFactory::class, ['create']);
        $this->urlBuilderMock = $this->createPartialMock(UrlBuilder::class, ['getBackendUrl']);
        $this->ratingResolverMock = $this->createPartialMock(Resolver::class, ['getRatingAbsoluteValue']);
        $this->reviewRepositoryMock = $this->getMockForAbstractClass(ReviewRepositoryInterface::class);
        $this->productResolverMock = $this->createMock(ProductResolver::class);
        $this->reviewMock = $this->createPartialMock(
            Review::class,
            [
                'getId',
                'getStoreId',
                'getRating',
                'setData',
                'getProductId'
            ]
        );

        $this->model = $objectManager->getObject(
            NewReviewProcessor::class,
            [
                'config' => $this->configMock,
                'emailMetadataFactory' => $this->emailMetadataFactoryMock,
                'urlBuilder' => $this->urlBuilderMock,
                'resolver' => $this->ratingResolverMock,
                'reviewRepository' => $this->reviewRepositoryMock,
                'productResolver' => $this->productResolverMock
            ]
        );
        $this->reviewMock->method('getProductId')->willReturn(1);
    }

    /**
     * Test process method
     *
     * @throws MailException
     * @throws NoSuchEntityException
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
        $reviewId = 1;
        $reviewRating = 60;
        $absoluteReviewRating = 3;
        $params = [ReviewInterface::ID => $reviewId];
        $routePath = 'aw_advanced_reviews/review/edit';
        $url = 'http://store.com/aw_advancedreviews/review/edit/id/1';

        $this->configMock->expects($this->once())
            ->method('getSenderName')
            ->willReturn($this->emailData[EmailMetadataInterface::SENDER_NAME]);
        $this->configMock->expects($this->once())
            ->method('getSenderEmail')
            ->willReturn($this->emailData[EmailMetadataInterface::SENDER_EMAIL]);
        $this->configMock->expects($this->once())
            ->method('getAdminNotificationTemplate')
            ->with($this->emailData[QueueItemInterface::STORE_ID])
            ->willReturn($this->emailData[EmailMetadataInterface::TEMPLATE_ID]);
        $this->reviewMock->expects($this->once())
            ->method('getId')
            ->willReturn($reviewId);
        $this->reviewMock->expects($this->once())
            ->method('getRating')
            ->willReturn($reviewRating);
        $this->reviewMock->expects($this->once())
            ->method('getStoreId')
            ->willReturn($this->emailData[QueueItemInterface::STORE_ID]);
        $this->reviewMock->expects($this->once())
            ->method('setData')
            ->with(NewReviewProcessor::ABSOLUTE_RATING, $absoluteReviewRating);
        $this->urlBuilderMock->expects($this->once())
            ->method('getBackendUrl')
            ->with($routePath, $this->emailData[QueueItemInterface::STORE_ID], $params)
            ->willReturn($url);
        $this->productResolverMock->expects($this->once())
            ->method('getPreparedProductName')
            ->with($this->reviewMock->getProductId())
            ->willReturn($this->emailData[ProductInterface::NAME]);
        $this->reviewRepositoryMock->expects($this->once())
            ->method('getById')
            ->with($reviewId)
            ->willReturn($this->reviewMock);
        $this->ratingResolverMock->expects($this->once())
            ->method('getRatingAbsoluteValue')
            ->with($reviewRating, 0)
            ->willReturn($absoluteReviewRating);
    }

    /**
     * Get queue item
     *
     * @return QueueItemInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private function getQueueItemMock()
    {
        $reviewId = 1;
        $queueItemMock = $this->getMockForAbstractClass(QueueItemInterface::class);

        $queueItemMock->expects($this->once())
            ->method('getStoreId')
            ->willReturn($this->emailData[QueueItemInterface::STORE_ID]);
        $queueItemMock->expects($this->once())
            ->method('getRecipientName')
            ->willReturn($this->emailData[QueueItemInterface::RECIPIENT_NAME]);
        $queueItemMock->expects($this->once())
            ->method('getRecipientEmail')
            ->willReturn($this->emailData[QueueItemInterface::RECIPIENT_EMAIL]);
        $queueItemMock->expects($this->once())
            ->method('getObjectId')
            ->willReturn($reviewId);

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
            EmailVariables::PRODUCT_NAME => $this->emailData[ProductInterface::NAME],
            EmailVariables::REVIEW => $this->reviewMock,
            EmailVariables::REVIEW_URL => 'http://store.com/aw_advancedreviews/review/edit/id/1'
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
