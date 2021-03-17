<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Test\Unit\ViewModel\Customer\Account\Dashboard;

use Aheadworks\AdvancedReviews\ViewModel\Customer\Account\Dashboard\Info;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Customer\Model\Session as CustomerSession;
use Aheadworks\AdvancedReviews\Api\EmailSubscriberManagementInterface;
use Aheadworks\AdvancedReviews\Api\Data\Email\SubscriberInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\UrlInterface;

/**
 * Test for \Aheadworks\AdvancedReviews\ViewModel\Customer\Account\Dashboard\Info
 */
class InfoTest extends TestCase
{
    /**
     * @var Info
     */
    private $viewModel;

    /**
     * @var UrlInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $urlBuilderMock;

    /**
     * @var CustomerSession|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $customerSessionMock;

    /**
     * @var EmailSubscriberManagementInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $emailSubscriberManagementMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->urlBuilderMock = $this->createMock(UrlInterface::class);
        $this->customerSessionMock = $this->createMock(CustomerSession::class);
        $this->emailSubscriberManagementMock = $this->createMock(EmailSubscriberManagementInterface::class);

        $this->viewModel = $objectManager->getObject(
            Info::class,
            [
                'urlBuilder' => $this->urlBuilderMock,
                'customerSession' => $this->customerSessionMock,
                'emailSubscriberManagement' => $this->emailSubscriberManagementMock,
            ]
        );
    }

    /**
     * Test for getEditNotificationsUrl method
     */
    public function testGetEditNotificationsUrl()
    {
        $editNotificationsUrl = 'www.store.com/aw_advanced_reviews/customer';
        $this->urlBuilderMock->expects($this->once())
            ->method('getUrl')
            ->with('aw_advanced_reviews/customer')
            ->willReturn($editNotificationsUrl);
        $this->assertEquals($editNotificationsUrl, $this->viewModel->getEditNotificationsUrl());
    }

    /**
     * Test for getCurrentSubscriber method when there is no current customer
     */
    public function testGetCurrentSubscriberNoCustomer()
    {
        $customerId = null;

        $this->customerSessionMock->expects($this->once())
            ->method('getCustomerId')
            ->willReturn($customerId);

        $this->emailSubscriberManagementMock->expects($this->once())
            ->method('getSubscriberByCustomerId')
            ->with($customerId)
            ->willThrowException(new LocalizedException(__('Error!')));

        $this->assertNull($this->viewModel->getCurrentSubscriber());
    }

    /**
     * Test for getCurrentSubscriber method
     */
    public function testGetCurrentSubscriber()
    {
        $customerId = 3;

        $this->customerSessionMock->expects($this->once())
            ->method('getCustomerId')
            ->willReturn($customerId);

        $subscriberMock = $this->getSubscriberMock(false, false, false);

        $this->emailSubscriberManagementMock->expects($this->once())
            ->method('getSubscriberByCustomerId')
            ->with($customerId)
            ->willReturn($subscriberMock);

        $this->assertSame($subscriberMock, $this->viewModel->getCurrentSubscriber());
    }

    /**
     * Test for getIsSubscriberReceiveNotifications method
     *
     * @param $subscriber
     * @param $result
     * @dataProvider getIsSubscriberReceiveNotificationsDataProvider
     */
    public function testGetIsSubscriberReceiveNotifications($subscriber, $result)
    {
        $this->assertEquals($result, $this->viewModel->getIsSubscriberReceiveNotifications($subscriber));
    }

    /**
     * @return array
     */
    public function getIsSubscriberReceiveNotificationsDataProvider()
    {
        return [
            [
                'subscriber' => null,
                'result' => false,
            ],
            [
                'subscriber' => $this->getSubscriberMock(
                    false,
                    false,
                    false
                ),
                'result' => false,
            ],
            [
                'subscriber' => $this->getSubscriberMock(
                    true,
                    false,
                    false
                ),
                'result' => true,
            ],
            [
                'subscriber' => $this->getSubscriberMock(
                    false,
                    true,
                    false
                ),
                'result' => true,
            ],
            [
                'subscriber' => $this->getSubscriberMock(
                    false,
                    false,
                    true
                ),
                'result' => true,
            ],
            [
                'subscriber' => $this->getSubscriberMock(
                    true,
                    true,
                    false
                ),
                'result' => true,
            ],
            [
                'subscriber' => $this->getSubscriberMock(
                    false,
                    true,
                    true
                ),
                'result' => true,
            ],
            [
                'subscriber' => $this->getSubscriberMock(
                    true,
                    false,
                    true
                ),
                'result' => true,
            ],
            [
                'subscriber' => $this->getSubscriberMock(
                    true,
                    true,
                    true
                ),
                'result' => true,
            ],
        ];
    }

    /**
     * Retrieve subscriber mock
     *
     * @param bool $isReviewReminderEmailEnabled
     * @param bool $isNewCommentEmailEnabled
     * @param bool $isReviewApprovedEmailEnabled
     * @return \PHPUnit\Framework\MockObject\MockObject|SubscriberInterface
     */
    private function getSubscriberMock(
        $isReviewReminderEmailEnabled,
        $isNewCommentEmailEnabled,
        $isReviewApprovedEmailEnabled
    ) {
        $subscriberMock = $this->createMock(SubscriberInterface::class);
        $subscriberMock->expects($this->any())
            ->method('getIsReviewReminderEmailEnabled')
            ->willReturn($isReviewReminderEmailEnabled);
        $subscriberMock->expects($this->any())
            ->method('getIsNewCommentEmailEnabled')
            ->willReturn($isNewCommentEmailEnabled);
        $subscriberMock->expects($this->any())
            ->method('getIsReviewApprovedEmailEnabled')
            ->willReturn($isReviewApprovedEmailEnabled);
        return $subscriberMock;
    }
}
