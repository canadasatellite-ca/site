<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Test\Unit\Model\Service\Email;

use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

use Aheadworks\AdvancedReviews\Model\Service\Email\SubscriberService;
use Aheadworks\AdvancedReviews\Api\EmailSubscriberRepositoryInterface;
use Aheadworks\AdvancedReviews\Api\Data\Email\SubscriberInterface;
use Aheadworks\AdvancedReviews\Api\Data\Email\SubscriberInterfaceFactory;
use Aheadworks\AdvancedReviews\Model\Email\Subscriber\Finder as SubscriberFinder;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\Data\CustomerInterface;

/**
 * Test for \Aheadworks\AdvancedReviews\Model\Service\Email\SubscriberService
 */
class SubscriberServiceTest extends TestCase
{
    /**
     * @var SubscriberService
     */
    private $model;

    /**
     * @var EmailSubscriberRepositoryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $subscriberRepositoryMock;

    /**
     * @var SubscriberInterfaceFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $subscriberFactoryMock;

    /**
     * @var SubscriberFinder|\PHPUnit_Framework_MockObject_MockObject
     */
    private $subscriberFinderMock;

    /**
     * @var DataObjectHelper|\PHPUnit_Framework_MockObject_MockObject
     */
    private $dataObjectHelperMock;

    /**
     * @var CustomerRepositoryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $customerRepositoryMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->subscriberRepositoryMock = $this->createMock(EmailSubscriberRepositoryInterface::class);
        $this->subscriberFactoryMock = $this->createMock(SubscriberInterfaceFactory::class);
        $this->subscriberFinderMock = $this->createMock(SubscriberFinder::class);
        $this->dataObjectHelperMock = $this->createMock(DataObjectHelper::class);
        $this->customerRepositoryMock = $this->createMock(CustomerRepositoryInterface::class);

        $this->model = $objectManager->getObject(
            SubscriberService::class,
            [
                'subscriberRepository' => $this->subscriberRepositoryMock,
                'subscriberFactory' => $this->subscriberFactoryMock,
                'subscriberFinder' => $this->subscriberFinderMock,
                'dataObjectHelper' => $this->dataObjectHelperMock,
                'customerRepository' => $this->customerRepositoryMock,
            ]
        );
    }

    /**
     * Test createSubscriber method if subscriber already exists
     *
     * @expectedException \Magento\Framework\Exception\LocalizedException
     * @expectedExceptionMessage Subscriber already exists
     */
    public function testCreateSubscriberExisting()
    {
        $email = 'test@aw.com';
        $websiteId = 1;

        $subscriberMock = $this->createMock(SubscriberInterface::class);

        $this->subscriberFinderMock->expects($this->once())
            ->method('find')
            ->with($email, $websiteId)
            ->willReturn($subscriberMock);

        $this->subscriberFactoryMock->expects($this->never())
            ->method('create');

        $this->subscriberRepositoryMock->expects($this->never())
            ->method('save');

        $this->model->createSubscriber($email, $websiteId);
    }

    /**
     * Test createSubscriber method with error on save
     *
     * @expectedException \Magento\Framework\Exception\LocalizedException
     * @expectedExceptionMessage Error on save
     */
    public function testCreateSubscriberErrorOnSave()
    {
        $email = 'test@aw.com';
        $websiteId = 1;

        $this->subscriberFinderMock->expects($this->once())
            ->method('find')
            ->with($email, $websiteId)
            ->willReturn(null);

        $subscriberMock = $this->createMock(SubscriberInterface::class);
        $subscriberMock->expects($this->once())
            ->method('setEmail')
            ->with($email)
            ->willReturnSelf();
        $subscriberMock->expects($this->once())
            ->method('setWebsiteId')
            ->with($websiteId)
            ->willReturnSelf();
        $subscriberMock->expects($this->once())
            ->method('setIsReviewApprovedEmailEnabled')
            ->with(true)
            ->willReturnSelf();
        $subscriberMock->expects($this->once())
            ->method('setIsNewCommentEmailEnabled')
            ->with(true)
            ->willReturnSelf();
        $subscriberMock->expects($this->once())
            ->method('setIsReviewReminderEmailEnabled')
            ->with(true)
            ->willReturnSelf();

        $this->subscriberFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($subscriberMock);

        $this->subscriberRepositoryMock->expects($this->once())
            ->method('save')
            ->with($subscriberMock)
            ->willThrowException(new LocalizedException(__('Error on save')));

        $this->model->createSubscriber($email, $websiteId);
    }

    /**
     * Test createSubscriber method
     */
    public function testCreateSubscriber()
    {
        $email = 'test@aw.com';
        $websiteId = 1;

        $this->subscriberFinderMock->expects($this->once())
            ->method('find')
            ->with($email, $websiteId)
            ->willReturn(null);

        $subscriberMock = $this->createMock(SubscriberInterface::class);
        $subscriberMock->expects($this->once())
            ->method('setEmail')
            ->with($email)
            ->willReturnSelf();
        $subscriberMock->expects($this->once())
            ->method('setWebsiteId')
            ->with($websiteId)
            ->willReturnSelf();
        $subscriberMock->expects($this->once())
            ->method('setIsReviewApprovedEmailEnabled')
            ->with(true)
            ->willReturnSelf();
        $subscriberMock->expects($this->once())
            ->method('setIsNewCommentEmailEnabled')
            ->with(true)
            ->willReturnSelf();
        $subscriberMock->expects($this->once())
            ->method('setIsReviewReminderEmailEnabled')
            ->with(true)
            ->willReturnSelf();

        $this->subscriberFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($subscriberMock);

        $this->subscriberRepositoryMock->expects($this->once())
            ->method('save')
            ->with($subscriberMock)
            ->willReturn($subscriberMock);

        $this->assertSame($subscriberMock, $this->model->createSubscriber($email, $websiteId));
    }

    /**
     * Test deleteSubscriber method with error
     *
     * @expectedException \Magento\Framework\Exception\LocalizedException
     * @expectedExceptionMessage Error on delete
     */
    public function testDeleteSubscriberError()
    {
        $subscriberMock = $this->createMock(SubscriberInterface::class);

        $this->subscriberRepositoryMock->expects($this->once())
            ->method('delete')
            ->with($subscriberMock)
            ->willThrowException(new LocalizedException(__('Error on delete')));

        $this->model->deleteSubscriber($subscriberMock);
    }

    /**
     * Test deleteSubscriber method
     */
    public function testDeleteSubscriber()
    {
        $subscriberMock = $this->createMock(SubscriberInterface::class);

        $this->subscriberRepositoryMock->expects($this->once())
            ->method('delete')
            ->with($subscriberMock)
            ->willReturn(true);

        $this->assertTrue($this->model->deleteSubscriber($subscriberMock));
    }

    /**
     * Test deleteSubscriberById method when no subscriber exists
     *
     * @expectedException \Magento\Framework\Exception\LocalizedException
     * @expectedExceptionMessage No subscriber
     */
    public function testDeleteSubscriberByIdNoSubscriber()
    {
        $subscriberId = 3;

        $this->subscriberRepositoryMock->expects($this->once())
            ->method('getById')
            ->with($subscriberId)
            ->willThrowException(new LocalizedException(__('No subscriber')));

        $this->subscriberRepositoryMock->expects($this->never())
            ->method('delete');

        $this->model->deleteSubscriberById($subscriberId);
    }

    /**
     * Test deleteSubscriberById method with error
     *
     * @expectedException \Magento\Framework\Exception\LocalizedException
     * @expectedExceptionMessage Error on delete
     */
    public function testDeleteSubscriberByIdError()
    {
        $subscriberId = 3;
        $subscriberMock = $this->createMock(SubscriberInterface::class);

        $this->subscriberRepositoryMock->expects($this->once())
            ->method('getById')
            ->with($subscriberId)
            ->willReturn($subscriberMock);

        $this->subscriberRepositoryMock->expects($this->once())
            ->method('delete')
            ->with($subscriberMock)
            ->willThrowException(new LocalizedException(__('Error on delete')));

        $this->model->deleteSubscriberById($subscriberId);
    }

    /**
     * Test deleteSubscriberById method
     */
    public function testDeleteSubscriberById()
    {
        $subscriberId = 3;
        $subscriberMock = $this->createMock(SubscriberInterface::class);

        $this->subscriberRepositoryMock->expects($this->once())
            ->method('getById')
            ->with($subscriberId)
            ->willReturn($subscriberMock);

        $this->subscriberRepositoryMock->expects($this->once())
            ->method('delete')
            ->with($subscriberMock)
            ->willReturn(true);

        $this->assertTrue($this->model->deleteSubscriberById($subscriberId));
    }

    /**
     * Test updateSubscriber method when subscriber already deleted
     *
     * @expectedException \Magento\Framework\Exception\LocalizedException
     * @expectedExceptionMessage No subscriber
     */
    public function testUpdateSubscriberAlreadyDeleted()
    {
        $subscriberId = 4;
        $subscriberMock = $this->createMock(SubscriberInterface::class);
        $subscriberMock->expects($this->any())
            ->method('getId')
            ->willReturn($subscriberId);

        $this->subscriberRepositoryMock->expects($this->once())
            ->method('getById')
            ->with($subscriberId)
            ->willThrowException(new LocalizedException(__('No subscriber')));

        $this->dataObjectHelperMock->expects($this->never())
            ->method('mergeDataObjects');

        $this->subscriberRepositoryMock->expects($this->never())
            ->method('save');

        $this->model->updateSubscriber($subscriberMock);
    }

    /**
     * Test updateSubscriber method with error on save
     *
     * @expectedException \Magento\Framework\Exception\LocalizedException
     * @expectedExceptionMessage Error on save
     */
    public function testUpdateSubscriberErrorOnSave()
    {
        $subscriberId = 4;
        $subscriberMock = $this->createMock(SubscriberInterface::class);
        $subscriberMock->expects($this->any())
            ->method('getId')
            ->willReturn($subscriberId);

        $subscriberToMerge = $this->createMock(SubscriberInterface::class);

        $this->subscriberRepositoryMock->expects($this->once())
            ->method('getById')
            ->with($subscriberId)
            ->willReturn($subscriberToMerge);

        $this->dataObjectHelperMock->expects($this->once())
            ->method('mergeDataObjects')
            ->with(SubscriberInterface::class, $subscriberToMerge, $subscriberMock);

        $this->subscriberRepositoryMock->expects($this->once())
            ->method('save')
            ->with($subscriberToMerge)
            ->willThrowException(new LocalizedException(__('Error on save')));

        $this->model->updateSubscriber($subscriberMock);
    }

    /**
     * Test updateSubscriber method
     */
    public function testUpdateSubscriber()
    {
        $subscriberId = 4;
        $subscriberMock = $this->createMock(SubscriberInterface::class);
        $subscriberMock->expects($this->any())
            ->method('getId')
            ->willReturn($subscriberId);

        $subscriberToMerge = $this->createMock(SubscriberInterface::class);

        $this->subscriberRepositoryMock->expects($this->once())
            ->method('getById')
            ->with($subscriberId)
            ->willReturn($subscriberToMerge);

        $this->dataObjectHelperMock->expects($this->once())
            ->method('mergeDataObjects')
            ->with(SubscriberInterface::class, $subscriberToMerge, $subscriberMock);

        $this->subscriberRepositoryMock->expects($this->once())
            ->method('save')
            ->with($subscriberToMerge)
            ->willReturn($subscriberToMerge);

        $this->assertSame($subscriberToMerge, $this->model->updateSubscriber($subscriberMock));
    }

    /**
     * Test getSubscriberByCustomerId method when customer does not exist
     *
     * @expectedException \Magento\Framework\Exception\LocalizedException
     * @expectedExceptionMessage No customer found
     */
    public function testGetSubscriberByCustomerIdNoCustomer()
    {
        $customerId = 1;

        $this->customerRepositoryMock->expects($this->once())
            ->method('getById')
            ->with($customerId)
            ->willThrowException(new LocalizedException(__('No customer found')));

        $this->model->getSubscriberByCustomerId($customerId);
    }

    /**
     * Test getSubscriberByCustomerId method when subscriber already exists
     */
    public function testGetSubscriberByCustomerIdSubscriberExists()
    {
        $customerId = 1;
        $email = 'test@aw.com';
        $websiteId = 1;

        $customer = $this->createMock(CustomerInterface::class);
        $customer->expects($this->any())
            ->method('getEmail')
            ->willReturn($email);
        $customer->expects($this->any())
            ->method('getWebsiteId')
            ->willReturn($websiteId);

        $this->customerRepositoryMock->expects($this->once())
            ->method('getById')
            ->with($customerId)
            ->willReturn($customer);

        $subscriber = $this->createMock(SubscriberInterface::class);
        $this->subscriberFinderMock->expects($this->once())
            ->method('find')
            ->with($email, $websiteId)
            ->willReturn($subscriber);

        $this->assertSame($subscriber, $this->model->getSubscriberByCustomerId($customerId));
    }

    /**
     * Test getSubscriberByCustomerId method when new subscriber is created
     */
    public function testGetSubscriberByCustomerIdNewSubscriber()
    {
        $customerId = 1;
        $email = 'test@aw.com';
        $websiteId = 1;

        $customer = $this->createMock(CustomerInterface::class);
        $customer->expects($this->any())
            ->method('getEmail')
            ->willReturn($email);
        $customer->expects($this->any())
            ->method('getWebsiteId')
            ->willReturn($websiteId);

        $this->customerRepositoryMock->expects($this->once())
            ->method('getById')
            ->with($customerId)
            ->willReturn($customer);

        $this->subscriberFinderMock->expects($this->exactly(2))
            ->method('find')
            ->with($email, $websiteId)
            ->willReturn(null);

        $subscriber = $this->createMock(SubscriberInterface::class);
        $subscriber->expects($this->once())
            ->method('setEmail')
            ->with($email)
            ->willReturnSelf();
        $subscriber->expects($this->once())
            ->method('setWebsiteId')
            ->with($websiteId)
            ->willReturnSelf();
        $subscriber->expects($this->once())
            ->method('setIsReviewApprovedEmailEnabled')
            ->with(true)
            ->willReturnSelf();
        $subscriber->expects($this->once())
            ->method('setIsNewCommentEmailEnabled')
            ->with(true)
            ->willReturnSelf();
        $subscriber->expects($this->once())
            ->method('setIsReviewReminderEmailEnabled')
            ->with(true)
            ->willReturnSelf();

        $this->subscriberFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($subscriber);

        $this->subscriberRepositoryMock->expects($this->once())
            ->method('save')
            ->with($subscriber)
            ->willReturn($subscriber);

        $this->assertSame($subscriber, $this->model->getSubscriberByCustomerId($customerId));
    }

    /**
     * Test getSubscriberByCustomerId method when new subscriber is created with error
     *
     * @expectedException \Magento\Framework\Exception\LocalizedException
     * @expectedExceptionMessage Error on save
     */
    public function testGetSubscriberByCustomerIdNewSubscriberError()
    {
        $customerId = 1;
        $email = 'test@aw.com';
        $websiteId = 1;

        $customer = $this->createMock(CustomerInterface::class);
        $customer->expects($this->any())
            ->method('getEmail')
            ->willReturn($email);
        $customer->expects($this->any())
            ->method('getWebsiteId')
            ->willReturn($websiteId);

        $this->customerRepositoryMock->expects($this->once())
            ->method('getById')
            ->with($customerId)
            ->willReturn($customer);

        $this->subscriberFinderMock->expects($this->exactly(2))
            ->method('find')
            ->with($email, $websiteId)
            ->willReturn(null);

        $subscriber = $this->createMock(SubscriberInterface::class);
        $subscriber->expects($this->once())
            ->method('setEmail')
            ->with($email)
            ->willReturnSelf();
        $subscriber->expects($this->once())
            ->method('setWebsiteId')
            ->with($websiteId)
            ->willReturnSelf();
        $subscriber->expects($this->once())
            ->method('setIsReviewApprovedEmailEnabled')
            ->with(true)
            ->willReturnSelf();
        $subscriber->expects($this->once())
            ->method('setIsNewCommentEmailEnabled')
            ->with(true)
            ->willReturnSelf();
        $subscriber->expects($this->once())
            ->method('setIsReviewReminderEmailEnabled')
            ->with(true)
            ->willReturnSelf();

        $this->subscriberFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($subscriber);

        $this->subscriberRepositoryMock->expects($this->once())
            ->method('save')
            ->with($subscriber)
            ->willThrowException(new LocalizedException(__('Error on save')));

        $this->model->getSubscriberByCustomerId($customerId);
    }

    /**
     * Test getSubscriber method when subscriber is already created for guest
     */
    public function testGetSubscriberByEmailExists()
    {
        $email = 'test@aw.com';
        $websiteId = 1;

        $this->customerRepositoryMock->expects($this->once())
            ->method('get')
            ->with($email, $websiteId)
            ->willThrowException(new LocalizedException(__('No customer found')));

        $subscriber = $this->createMock(SubscriberInterface::class);
        $this->subscriberFinderMock->expects($this->once())
            ->method('find')
            ->with($email, $websiteId)
            ->willReturn($subscriber);

        $this->assertSame($subscriber, $this->model->getSubscriber($email, $websiteId));
    }

    /**
     * Test getSubscriber method when new subscriber is created for guest
     */
    public function testGetSubscriberByEmailCreateSubscriber()
    {
        $email = 'test@aw.com';
        $websiteId = 1;

        $this->customerRepositoryMock->expects($this->once())
            ->method('get')
            ->with($email, $websiteId)
            ->willThrowException(new LocalizedException(__('No customer found')));

        $this->subscriberFinderMock->expects($this->exactly(2))
            ->method('find')
            ->with($email, $websiteId)
            ->willReturn(null);

        $subscriber = $this->createMock(SubscriberInterface::class);
        $subscriber->expects($this->once())
            ->method('setEmail')
            ->with($email)
            ->willReturnSelf();
        $subscriber->expects($this->once())
            ->method('setWebsiteId')
            ->with($websiteId)
            ->willReturnSelf();
        $subscriber->expects($this->once())
            ->method('setIsReviewApprovedEmailEnabled')
            ->with(true)
            ->willReturnSelf();
        $subscriber->expects($this->once())
            ->method('setIsNewCommentEmailEnabled')
            ->with(true)
            ->willReturnSelf();
        $subscriber->expects($this->once())
            ->method('setIsReviewReminderEmailEnabled')
            ->with(true)
            ->willReturnSelf();

        $this->subscriberFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($subscriber);
        $this->subscriberRepositoryMock->expects($this->once())
            ->method('save')
            ->with($subscriber)
            ->willReturn($subscriber);

        $this->assertSame($subscriber, $this->model->getSubscriber($email, $websiteId));
    }

    /**
     * Test getSubscriber method when new subscriber is created for guest with error
     *
     * @expectedException \Magento\Framework\Exception\LocalizedException
     * @expectedExceptionMessage Error on save
     */
    public function testGetSubscriberByEmailCreateSubscriberWithError()
    {
        $email = 'test@aw.com';
        $websiteId = 1;

        $this->customerRepositoryMock->expects($this->once())
            ->method('get')
            ->with($email, $websiteId)
            ->willThrowException(new LocalizedException(__('No customer found')));

        $this->subscriberFinderMock->expects($this->exactly(2))
            ->method('find')
            ->with($email, $websiteId)
            ->willReturn(null);

        $subscriber = $this->createMock(SubscriberInterface::class);
        $subscriber->expects($this->once())
            ->method('setEmail')
            ->with($email)
            ->willReturnSelf();
        $subscriber->expects($this->once())
            ->method('setWebsiteId')
            ->with($websiteId)
            ->willReturnSelf();
        $subscriber->expects($this->once())
            ->method('setIsReviewApprovedEmailEnabled')
            ->with(true)
            ->willReturnSelf();
        $subscriber->expects($this->once())
            ->method('setIsNewCommentEmailEnabled')
            ->with(true)
            ->willReturnSelf();
        $subscriber->expects($this->once())
            ->method('setIsReviewReminderEmailEnabled')
            ->with(true)
            ->willReturnSelf();

        $this->subscriberFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($subscriber);
        $this->subscriberRepositoryMock->expects($this->once())
            ->method('save')
            ->with($subscriber)
            ->willThrowException(new LocalizedException(__('Error on save')));

        $this->model->getSubscriber($email, $websiteId);
    }

    /**
     * Test getSubscriber method for customer when subscriber already exists
     */
    public function testGetSubscriberByCustomerSubscriberExists()
    {
        $email = 'test@aw.com';
        $websiteId = 1;

        $customer = $this->createMock(CustomerInterface::class);
        $customer->expects($this->any())
            ->method('getEmail')
            ->willReturn($email);
        $customer->expects($this->any())
            ->method('getWebsiteId')
            ->willReturn($websiteId);

        $this->customerRepositoryMock->expects($this->once())
            ->method('get')
            ->with($email, $websiteId)
            ->willReturn($customer);

        $subscriber = $this->createMock(SubscriberInterface::class);
        $this->subscriberFinderMock->expects($this->once())
            ->method('find')
            ->with($email, $websiteId)
            ->willReturn($subscriber);

        $this->assertSame($subscriber, $this->model->getSubscriber($email, $websiteId));
    }

    /**
     * Test getSubscriber method for customer when new subscriber is created
     */
    public function testGetSubscriberByCustomerNewSubscriber()
    {
        $email = 'test@aw.com';
        $websiteId = 1;

        $customer = $this->createMock(CustomerInterface::class);
        $customer->expects($this->any())
            ->method('getEmail')
            ->willReturn($email);
        $customer->expects($this->any())
            ->method('getWebsiteId')
            ->willReturn($websiteId);

        $this->customerRepositoryMock->expects($this->once())
            ->method('get')
            ->with($email, $websiteId)
            ->willReturn($customer);

        $this->subscriberFinderMock->expects($this->exactly(2))
            ->method('find')
            ->with($email, $websiteId)
            ->willReturn(null);

        $subscriber = $this->createMock(SubscriberInterface::class);
        $subscriber->expects($this->once())
            ->method('setEmail')
            ->with($email)
            ->willReturnSelf();
        $subscriber->expects($this->once())
            ->method('setWebsiteId')
            ->with($websiteId)
            ->willReturnSelf();
        $subscriber->expects($this->once())
            ->method('setIsReviewApprovedEmailEnabled')
            ->with(true)
            ->willReturnSelf();
        $subscriber->expects($this->once())
            ->method('setIsNewCommentEmailEnabled')
            ->with(true)
            ->willReturnSelf();
        $subscriber->expects($this->once())
            ->method('setIsReviewReminderEmailEnabled')
            ->with(true)
            ->willReturnSelf();

        $this->subscriberFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($subscriber);

        $this->subscriberRepositoryMock->expects($this->once())
            ->method('save')
            ->with($subscriber)
            ->willReturn($subscriber);

        $this->assertSame($subscriber, $this->model->getSubscriber($email, $websiteId));
    }

    /**
     * Test getSubscriber method for customer when new subscriber is created with error
     *
     * @expectedException \Magento\Framework\Exception\LocalizedException
     * @expectedExceptionMessage Error on save
     */
    public function testGetSubscriberByCustomerNewSubscriberError()
    {
        $email = 'test@aw.com';
        $websiteId = 1;

        $customer = $this->createMock(CustomerInterface::class);
        $customer->expects($this->any())
            ->method('getEmail')
            ->willReturn($email);
        $customer->expects($this->any())
            ->method('getWebsiteId')
            ->willReturn($websiteId);

        $this->customerRepositoryMock->expects($this->once())
            ->method('get')
            ->with($email, $websiteId)
            ->willReturn($customer);

        $this->subscriberFinderMock->expects($this->exactly(2))
            ->method('find')
            ->with($email, $websiteId)
            ->willReturn(null);

        $subscriber = $this->createMock(SubscriberInterface::class);
        $subscriber->expects($this->once())
            ->method('setEmail')
            ->with($email)
            ->willReturnSelf();
        $subscriber->expects($this->once())
            ->method('setWebsiteId')
            ->with($websiteId)
            ->willReturnSelf();
        $subscriber->expects($this->once())
            ->method('setIsReviewApprovedEmailEnabled')
            ->with(true)
            ->willReturnSelf();
        $subscriber->expects($this->once())
            ->method('setIsNewCommentEmailEnabled')
            ->with(true)
            ->willReturnSelf();
        $subscriber->expects($this->once())
            ->method('setIsReviewReminderEmailEnabled')
            ->with(true)
            ->willReturnSelf();

        $this->subscriberFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($subscriber);

        $this->subscriberRepositoryMock->expects($this->once())
            ->method('save')
            ->with($subscriber)
            ->willThrowException(new LocalizedException(__('Error on save')));

        $this->model->getSubscriber($email, $websiteId);
    }
}
