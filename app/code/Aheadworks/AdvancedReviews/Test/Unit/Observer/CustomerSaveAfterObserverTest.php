<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Test\Unit\Observer;

use Aheadworks\AdvancedReviews\Observer\CustomerSaveAfterObserver;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\TestCase;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event;
use Magento\Customer\Api\Data\CustomerInterface;
use Aheadworks\AdvancedReviews\Model\Email\Subscriber\Updater as EmailSubscriberUpdater;

/**
 * Test for \Aheadworks\AdvancedReviews\Observer\CustomerSaveAfterObserver
 */
class CustomerSaveAfterObserverTest extends TestCase
{
    /**
     * @var CustomerSaveAfterObserver
     */
    private $observer;

    /**
     * @var EmailSubscriberUpdater|\PHPUnit_Framework_MockObject_MockObject
     */
    private $emailSubscriberUpdaterMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->emailSubscriberUpdaterMock = $this->createMock(EmailSubscriberUpdater::class);

        $this->observer = $objectManager->getObject(
            CustomerSaveAfterObserver::class,
            [
                'subscriberUpdater' => $this->emailSubscriberUpdaterMock,
            ]
        );
    }

    /**
     * Test execute method
     *
     * @param CustomerInterface|null $savedCustomer
     * @param CustomerInterface|null $originalCustomer
     * @param bool $isUpdatedCalled
     * @dataProvider executeDataProvider
     */
    public function testExecute($savedCustomer, $originalCustomer, $isUpdatedCalled)
    {
        $eventMock = $this->createMock(Event::class);
        $eventMock->expects($this->exactly(2))
            ->method('getData')
            ->willReturnMap(
                [
                    [
                        'customer_data_object',
                        null,
                        $savedCustomer
                    ],
                    [
                        'orig_customer_data_object',
                        null,
                        $originalCustomer
                    ],
                ]
            );

        $observerMock = $this->createMock(Observer::class);
        $observerMock->expects($this->once())
            ->method('getEvent')
            ->willReturn($eventMock);

        if ($isUpdatedCalled) {
            $this->emailSubscriberUpdaterMock->expects($this->once())
                ->method('processCustomerModifications')
                ->with($savedCustomer, $originalCustomer);
        } else {
            $this->emailSubscriberUpdaterMock->expects($this->never())
                ->method('processCustomerModifications');
        }

        $this->observer->execute($observerMock);
    }

    /**
     * @return array
     */
    public function executeDataProvider()
    {
        return [
            [
                'savedCustomer' => null,
                'originalCustomer' => null,
                'isUpdatedCalled' => false,
            ],
            [
                'savedCustomer' => $this->createMock(CustomerInterface::class),
                'originalCustomer' => null,
                'isUpdatedCalled' => false,
            ],
            [
                'savedCustomer' => null,
                'originalCustomer' => $this->createMock(CustomerInterface::class),
                'isUpdatedCalled' => false,
            ],
            [
                'savedCustomer' => $this->createMock(CustomerInterface::class),
                'originalCustomer' => $this->createMock(CustomerInterface::class),
                'isUpdatedCalled' => true,
            ],
        ];
    }
}
