<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Test\Unit\Model\Email\Subscriber;

use Aheadworks\AdvancedReviews\Model\Email\Subscriber\Updater;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Aheadworks\AdvancedReviews\Api\EmailSubscriberManagementInterface;
use Aheadworks\AdvancedReviews\Api\Data\Email\SubscriberInterface;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Framework\Exception\LocalizedException;
use Psr\Log\LoggerInterface;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Reflection\DataObjectProcessor;
use Aheadworks\AdvancedReviews\Model\Data\Extractor as DataExtractor;

/**
 * Test for \Aheadworks\AdvancedReviews\Model\Email\Subscriber\Updater
 */
class UpdaterTest extends TestCase
{
    /**
     * @var Updater
     */
    private $model;

    /**
     * @var EmailSubscriberManagementInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $subscriberManagementMock;

    /**
     * @var LoggerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $loggerMock;

    /**
     * @var DataObjectHelper|\PHPUnit_Framework_MockObject_MockObject
     */
    private $dataObjectHelperMock;

    /**
     * @var DataObjectProcessor|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $dataObjectProcessorMock;

    /**
     * @var DataExtractor|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $dataExtractorMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    protected function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->subscriberManagementMock = $this->createMock(EmailSubscriberManagementInterface::class);
        $this->loggerMock = $this->createMock(LoggerInterface::class);
        $this->dataObjectHelperMock = $this->createMock(DataObjectHelper::class);
        $this->dataObjectProcessorMock = $this->createMock(DataObjectProcessor::class);
        $this->dataExtractorMock = $this->createMock(DataExtractor::class);

        $this->model = $objectManager->getObject(
            Updater::class,
            [
                'subscriberManagement' => $this->subscriberManagementMock,
                'logger' => $this->loggerMock,
                'dataObjectHelper' => $this->dataObjectHelperMock,
                'dataObjectProcessor' => $this->dataObjectProcessorMock,
                'dataExtractor' => $this->dataExtractorMock,
            ]
        );
    }

    /**
     * Test processCustomerModifications method
     *
     * @param $savedCustomerEmail
     * @param $savedCustomerWebsiteId
     * @param $originalCustomerEmail
     * @param $originalCustomerWebsiteId
     * @dataProvider processCustomerModificationsDataProvider
     */
    public function testProcessCustomerModifications(
        $savedCustomerEmail,
        $savedCustomerWebsiteId,
        $originalCustomerEmail,
        $originalCustomerWebsiteId
    ) {
        $originalCustomerSubscriberData = [
            SubscriberInterface::ID => 1,
            SubscriberInterface::IS_NEW_COMMENT_EMAIL_ENABLED => true,
        ];
        $originalCustomerSubscriberNotificationFlags = [
            SubscriberInterface::IS_NEW_COMMENT_EMAIL_ENABLED => true,
        ];

        $savedCustomer = $this->createMock(CustomerInterface::class);
        $savedCustomer->expects($this->any())
            ->method('getEmail')
            ->willReturn($savedCustomerEmail);
        $savedCustomer->expects($this->any())
            ->method('getWebsiteId')
            ->willReturn($savedCustomerWebsiteId);

        $originalCustomer = $this->createMock(CustomerInterface::class);
        $originalCustomer->expects($this->any())
            ->method('getEmail')
            ->willReturn($originalCustomerEmail);
        $originalCustomer->expects($this->any())
            ->method('getWebsiteId')
            ->willReturn($originalCustomerWebsiteId);

        $originalCustomerSubscriber = $this->createMock(SubscriberInterface::class);
        $savedCustomerSubscriber = $this->createMock(SubscriberInterface::class);

        $this->subscriberManagementMock->expects($this->any())
            ->method('getSubscriber')
            ->willReturnMap(
                [
                    [
                        $savedCustomerEmail,
                        $savedCustomerWebsiteId,
                        $savedCustomerSubscriber,
                    ],
                    [
                        $originalCustomerEmail,
                        $originalCustomerWebsiteId,
                        $originalCustomerSubscriber,
                    ],
                ]
            );

        $this->dataObjectProcessorMock->expects($this->any())
            ->method('buildOutputDataArray')
            ->with($originalCustomerSubscriber, SubscriberInterface::class)
            ->willReturn($originalCustomerSubscriberData);
        $this->dataExtractorMock->expects($this->any())
            ->method('extractFields')
            ->with($originalCustomerSubscriberData)
            ->willReturn($originalCustomerSubscriberNotificationFlags);

        $this->dataObjectHelperMock->expects($this->any())
            ->method('populateWithArray')
            ->with($savedCustomerSubscriber, $originalCustomerSubscriberNotificationFlags, SubscriberInterface::class);

        $this->subscriberManagementMock->expects($this->any())
            ->method('updateSubscriber')
            ->with($savedCustomerSubscriber);

        $this->loggerMock->expects($this->never())
            ->method('warning');

        $this->model->processCustomerModifications($savedCustomer, $originalCustomer);
    }

    /**
     * @return array
     */
    public function processCustomerModificationsDataProvider()
    {
        return [
            [
                'savedCustomerEmail' => 'test1@aw.com',
                'savedCustomerWebsiteId' => 1,
                'originalCustomerEmail' => 'test1@aw.com',
                'originalCustomerWebsiteId' => 1,
            ],
            [
                'savedCustomerEmail' => 'test1@aw.com',
                'savedCustomerWebsiteId' => 2,
                'originalCustomerEmail' => 'test1@aw.com',
                'originalCustomerWebsiteId' => 1,
            ],
            [
                'savedCustomerEmail' => 'test1new@aw.com',
                'savedCustomerWebsiteId' => 1,
                'originalCustomerEmail' => 'test1@aw.com',
                'originalCustomerWebsiteId' => 1,
            ],
            [
                'savedCustomerEmail' => 'test1new@aw.com',
                'savedCustomerWebsiteId' => 2,
                'originalCustomerEmail' => 'test1@aw.com',
                'originalCustomerWebsiteId' => 1,
            ],
        ];
    }

    /**
     * Test processCustomerModifications method with error on fetching customer subscriber
     *
     * @param $savedCustomerEmail
     * @param $savedCustomerWebsiteId
     * @param $originalCustomerEmail
     * @param $originalCustomerWebsiteId
     * @dataProvider processCustomerModificationsDataProvider
     */
    public function testProcessCustomerModificationsSubscriberError(
        $savedCustomerEmail,
        $savedCustomerWebsiteId,
        $originalCustomerEmail,
        $originalCustomerWebsiteId
    ) {
        $errorMessage = __('Error!');
        $originalCustomerSubscriberData = [
            SubscriberInterface::ID => 1,
            SubscriberInterface::IS_NEW_COMMENT_EMAIL_ENABLED => true,
        ];
        $originalCustomerSubscriberNotificationFlags = [
            SubscriberInterface::IS_NEW_COMMENT_EMAIL_ENABLED => true,
        ];

        $savedCustomer = $this->createMock(CustomerInterface::class);
        $savedCustomer->expects($this->any())
            ->method('getEmail')
            ->willReturn($savedCustomerEmail);
        $savedCustomer->expects($this->any())
            ->method('getWebsiteId')
            ->willReturn($savedCustomerWebsiteId);

        $originalCustomer = $this->createMock(CustomerInterface::class);
        $originalCustomer->expects($this->any())
            ->method('getEmail')
            ->willReturn($originalCustomerEmail);
        $originalCustomer->expects($this->any())
            ->method('getWebsiteId')
            ->willReturn($originalCustomerWebsiteId);

        $originalCustomerSubscriber = $this->createMock(SubscriberInterface::class);
        $savedCustomerSubscriber = $this->createMock(SubscriberInterface::class);

        $this->subscriberManagementMock->expects($this->any())
            ->method('getSubscriber')
            ->willThrowException(new LocalizedException($errorMessage));

        $this->dataObjectProcessorMock->expects($this->never())
            ->method('buildOutputDataArray')
            ->with($originalCustomerSubscriber, SubscriberInterface::class)
            ->willReturn($originalCustomerSubscriberData);
        $this->dataExtractorMock->expects($this->never())
            ->method('extractFields')
            ->with($originalCustomerSubscriberData)
            ->willReturn($originalCustomerSubscriberNotificationFlags);

        $this->dataObjectHelperMock->expects($this->never())
            ->method('populateWithArray')
            ->with($savedCustomerSubscriber, $originalCustomerSubscriberNotificationFlags, SubscriberInterface::class);

        $this->subscriberManagementMock->expects($this->never())
            ->method('updateSubscriber')
            ->with($savedCustomerSubscriber);

        $this->loggerMock->expects($this->any())
            ->method('warning')
            ->with($errorMessage);

        $this->model->processCustomerModifications($savedCustomer, $originalCustomer);
    }

    /**
     * Test processCustomerModifications method with error on subscriber update
     *
     * @param $savedCustomerEmail
     * @param $savedCustomerWebsiteId
     * @param $originalCustomerEmail
     * @param $originalCustomerWebsiteId
     * @dataProvider processCustomerModificationsDataProvider
     */
    public function testProcessCustomerModificationsSavedSubscriberError(
        $savedCustomerEmail,
        $savedCustomerWebsiteId,
        $originalCustomerEmail,
        $originalCustomerWebsiteId
    ) {
        $errorMessage = __('Error!');
        $originalCustomerSubscriberData = [
            SubscriberInterface::ID => 1,
            SubscriberInterface::IS_NEW_COMMENT_EMAIL_ENABLED => true,
        ];
        $originalCustomerSubscriberNotificationFlags = [
            SubscriberInterface::IS_NEW_COMMENT_EMAIL_ENABLED => true,
        ];

        $savedCustomer = $this->createMock(CustomerInterface::class);
        $savedCustomer->expects($this->any())
            ->method('getEmail')
            ->willReturn($savedCustomerEmail);
        $savedCustomer->expects($this->any())
            ->method('getWebsiteId')
            ->willReturn($savedCustomerWebsiteId);

        $originalCustomer = $this->createMock(CustomerInterface::class);
        $originalCustomer->expects($this->any())
            ->method('getEmail')
            ->willReturn($originalCustomerEmail);
        $originalCustomer->expects($this->any())
            ->method('getWebsiteId')
            ->willReturn($originalCustomerWebsiteId);

        $originalCustomerSubscriber = $this->createMock(SubscriberInterface::class);
        $savedCustomerSubscriber = $this->createMock(SubscriberInterface::class);

        $this->subscriberManagementMock->expects($this->any())
            ->method('getSubscriber')
            ->willReturnMap(
                [
                    [
                        $savedCustomerEmail,
                        $savedCustomerWebsiteId,
                        $savedCustomerSubscriber,
                    ],
                    [
                        $originalCustomerEmail,
                        $originalCustomerWebsiteId,
                        $originalCustomerSubscriber,
                    ],
                ]
            );

        $this->dataObjectProcessorMock->expects($this->any())
            ->method('buildOutputDataArray')
            ->with($originalCustomerSubscriber, SubscriberInterface::class)
            ->willReturn($originalCustomerSubscriberData);
        $this->dataExtractorMock->expects($this->any())
            ->method('extractFields')
            ->with($originalCustomerSubscriberData)
            ->willReturn($originalCustomerSubscriberNotificationFlags);

        $this->dataObjectHelperMock->expects($this->any())
            ->method('populateWithArray')
            ->with($savedCustomerSubscriber, $originalCustomerSubscriberNotificationFlags, SubscriberInterface::class);

        $this->subscriberManagementMock->expects($this->any())
            ->method('updateSubscriber')
            ->with($savedCustomerSubscriber)
            ->willThrowException(new LocalizedException($errorMessage));

        $this->loggerMock->expects($this->any())
            ->method('warning')
            ->with($errorMessage);

        $this->model->processCustomerModifications($savedCustomer, $originalCustomer);
    }
}
