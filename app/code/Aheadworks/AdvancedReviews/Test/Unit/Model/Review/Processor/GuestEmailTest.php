<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Test\Unit\Model\Review\Processor;

use Aheadworks\AdvancedReviews\Model\Review\Processor\GuestEmail;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Aheadworks\AdvancedReviews\Model\Store\Resolver as StoreResolver;
use Magento\Framework\Exception\LocalizedException;
use Aheadworks\AdvancedReviews\Model\Source\Review\AuthorType as ReviewAuthorType;
use Aheadworks\AdvancedReviews\Api\Data\ReviewInterface;
use Magento\Customer\Api\Data\CustomerInterface;

/**
 * Test for \Aheadworks\AdvancedReviews\Model\Review\Processor\GuestEmail
 */
class GuestEmailTest extends TestCase
{
    /**
     * @var GuestEmail
     */
    private $processor;

    /**
     * @var CustomerRepositoryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $customerRepositoryMock;

    /**
     * @var StoreResolver|\PHPUnit_Framework_MockObject_MockObject
     */
    private $storeResolverMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->customerRepositoryMock = $this->createMock(CustomerRepositoryInterface::class);
        $this->storeResolverMock = $this->createMock(StoreResolver::class);

        $this->processor = $objectManager->getObject(
            GuestEmail::class,
            [
                'customerRepository' => $this->customerRepositoryMock,
                'storeResolver' => $this->storeResolverMock,
            ]
        );
    }

    /**
     * Test process method when reviews isn't changed
     *
     * @param int|null $authorType
     * @param int|null $storeId
     * @param string|null $email
     * @dataProvider processNoModificationsDataProvider
     */
    public function testProcessNoModifications($authorType, $storeId, $email)
    {
        $websiteId = null;

        $review = $this->createMock(ReviewInterface::class);
        $review->expects($this->any())
            ->method('getAuthorType')
            ->willReturn($authorType);
        $review->expects($this->any())
            ->method('getStoreId')
            ->willReturn($storeId);
        $review->expects($this->any())
            ->method('getEmail')
            ->willReturn($email);
        $review->expects($this->never())
            ->method('setCustomerId');
        $review->expects($this->never())
            ->method('setEmail');

        $this->storeResolverMock->expects($this->any())
            ->method('getWebsiteIdByStoreId')
            ->with($storeId)
            ->willReturn($websiteId);

        $this->customerRepositoryMock->expects($this->never())
            ->method('get');

        $this->assertSame($review, $this->processor->process($review));
    }

    public function processNoModificationsDataProvider()
    {
        return [
            [
                'authorType' => null,
                'storeId' => null,
                'email' => null,
            ],
            [
                'authorType' => null,
                'storeId' => null,
                'email' => 'test@aw.com',
            ],
            [
                'authorType' => null,
                'storeId' => 1,
                'email' => null,
            ],
            [
                'authorType' => null,
                'storeId' => 1,
                'email' => 'test@aw.com',
            ],
            [
                'authorType' => ReviewAuthorType::ADMIN,
                'storeId' => null,
                'email' => null,
            ],
            [
                'authorType' => ReviewAuthorType::ADMIN,
                'storeId' => null,
                'email' => 'test@aw.com',
            ],
            [
                'authorType' => ReviewAuthorType::ADMIN,
                'storeId' => 1,
                'email' => null,
            ],
            [
                'authorType' => ReviewAuthorType::ADMIN,
                'storeId' => 1,
                'email' => 'test@aw.com',
            ],
            [
                'authorType' => ReviewAuthorType::CUSTOMER,
                'storeId' => null,
                'email' => null,
            ],
            [
                'authorType' => ReviewAuthorType::CUSTOMER,
                'storeId' => null,
                'email' => 'test@aw.com',
            ],
            [
                'authorType' => ReviewAuthorType::CUSTOMER,
                'storeId' => 1,
                'email' => null,
            ],
            [
                'authorType' => ReviewAuthorType::CUSTOMER,
                'storeId' => 1,
                'email' => 'test@aw.com',
            ],
            [
                'authorType' => ReviewAuthorType::GUEST,
                'storeId' => null,
                'email' => null,
            ],
            [
                'authorType' => ReviewAuthorType::GUEST,
                'storeId' => null,
                'email' => 'test@aw.com',
            ],
            [
                'authorType' => ReviewAuthorType::GUEST,
                'storeId' => 1,
                'email' => null,
            ],
            [
                'authorType' => ReviewAuthorType::GUEST,
                'storeId' => 1,
                'email' => 'test@aw.com',
            ],
        ];
    }

    /**
     * Test process method when no customer found
     */
    public function testProcessNoCustomer()
    {
        $authorType = ReviewAuthorType::GUEST;
        $storeId = 2;
        $email = 'test@aw.com';
        $websiteId = 2;

        $review = $this->createMock(ReviewInterface::class);
        $review->expects($this->any())
            ->method('getAuthorType')
            ->willReturn($authorType);
        $review->expects($this->any())
            ->method('getStoreId')
            ->willReturn($storeId);
        $review->expects($this->any())
            ->method('getEmail')
            ->willReturn($email);

        $review->expects($this->once())
            ->method('setCustomerId')
            ->with(null)
            ->willReturnSelf();
        $review->expects($this->once())
            ->method('setEmail')
            ->with($email)
            ->willReturnSelf();

        $this->storeResolverMock->expects($this->any())
            ->method('getWebsiteIdByStoreId')
            ->with($storeId)
            ->willReturn($websiteId);

        $this->customerRepositoryMock->expects($this->once())
            ->method('get')
            ->with($email, $websiteId)
            ->willThrowException(new LocalizedException(__('No customer found')));

        $this->assertSame($review, $this->processor->process($review));
    }

    /**
     * Test process method
     */
    public function testProcess()
    {
        $authorType = ReviewAuthorType::GUEST;
        $storeId = 2;
        $email = 'test@aw.com';
        $websiteId = 2;
        $customerId = 10;

        $customer = $this->createMock(CustomerInterface::class);
        $customer->expects($this->any())
            ->method('getId')
            ->willReturn($customerId);

        $review = $this->createMock(ReviewInterface::class);
        $review->expects($this->any())
            ->method('getAuthorType')
            ->willReturn($authorType);
        $review->expects($this->any())
            ->method('getStoreId')
            ->willReturn($storeId);
        $review->expects($this->any())
            ->method('getEmail')
            ->willReturn($email);

        $review->expects($this->once())
            ->method('setCustomerId')
            ->with($customerId)
            ->willReturnSelf();
        $review->expects($this->once())
            ->method('setEmail')
            ->with('')
            ->willReturnSelf();

        $this->storeResolverMock->expects($this->any())
            ->method('getWebsiteIdByStoreId')
            ->with($storeId)
            ->willReturn($websiteId);

        $this->customerRepositoryMock->expects($this->once())
            ->method('get')
            ->with($email, $websiteId)
            ->willReturn($customer);

        $this->assertSame($review, $this->processor->process($review));
    }
}
