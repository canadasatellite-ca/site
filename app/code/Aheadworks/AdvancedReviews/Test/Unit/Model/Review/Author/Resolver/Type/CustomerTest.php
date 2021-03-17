<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Test\Unit\Model\Review\Author\Resolver\Type;

use Aheadworks\AdvancedReviews\Model\Review\Author\Resolver\Type\Customer;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\TestCase;
use Aheadworks\AdvancedReviews\Api\Data\ReviewInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\UrlInterface;
use Magento\Customer\Api\Data\CustomerInterface;

/**
 * Test for \Aheadworks\AdvancedReviews\Model\Review\Author\Resolver\Type\Customer
 */
class CustomerTest extends TestCase
{
    /**
     * @var Customer
     */
    private $resolver;

    /**
     * @var CustomerRepositoryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $customerRepositoryMock;

    /**
     * @var UrlInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $urlBuilderMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->customerRepositoryMock = $this->createMock(CustomerRepositoryInterface::class);
        $this->urlBuilderMock = $this->createMock(UrlInterface::class);

        $this->resolver = $objectManager->getObject(
            Customer::class,
            [
                'customerRepository' => $this->customerRepositoryMock,
                'urlBuilder' => $this->urlBuilderMock,
            ]
        );
    }

    /**
     * Test getBackendLabel method when no customer retrieved
     *
     * @param int|null $customerId
     * @dataProvider getDataNoCustomerDataProvider
     */
    public function testGetBackendLabelNoCustomer($customerId)
    {
        $backendLabel = __('Not specified');
        $this->customerRepositoryMock->expects($this->any())
            ->method('getById')
            ->with($customerId)
            ->willThrowException(new LocalizedException(__('No customer')));
        $this->assertEquals((string)$backendLabel, (string)$this->resolver->getBackendLabel($customerId));
    }

    /**
     * @return array
     */
    public function getDataNoCustomerDataProvider()
    {
        return [
            [
                'customerId' => null
            ],
            [
                'customerId' => 1
            ],
        ];
    }

    /**
     * Test getBackendLabel method
     */
    public function testGetBackendLabel()
    {
        $customerId = 2;
        $customer = $this->getCustomerMock($customerId);

        $backendLabel = "Test Firstname Test Lastname";
        $this->customerRepositoryMock->expects($this->once())
            ->method('getById')
            ->with($customerId)
            ->willReturn($customer);
        $this->assertEquals((string)$backendLabel, (string)$this->resolver->getBackendLabel($customerId));
    }

    /**
     * Test getBackendUrl method when no customer retrieved
     *
     * @param int|null $customerId
     * @dataProvider getDataNoCustomerDataProvider
     */
    public function testGetBackendUrlNoCustomer($customerId)
    {
        $authorUrl = '';
        $this->customerRepositoryMock->expects($this->any())
            ->method('getById')
            ->with($customerId)
            ->willThrowException(new LocalizedException(__('No customer')));
        $this->urlBuilderMock->expects($this->never())
            ->method('getUrl');
        $this->assertEquals((string)$authorUrl, (string)$this->resolver->getBackendUrl($customerId));
    }

    /**
     * Test getBackendUrl method
     */
    public function testGetBackendUrl()
    {
        $authorUrl = "www.store.com/admin/customer/index/edit/2";
        $customerId = 2;
        $customer = $this->getCustomerMock($customerId);

        $this->customerRepositoryMock->expects($this->once())
            ->method('getById')
            ->with($customerId)
            ->willReturn($customer);
        $this->urlBuilderMock->expects($this->once())
            ->method('getUrl')
            ->with(
                'customer/index/edit',
                ['id' => $customerId]
            )->willReturn($authorUrl);

        $this->assertEquals((string)$authorUrl, (string)$this->resolver->getBackendUrl($customerId));
    }

    /**
     * Retrieves customer object mock
     *
     * @param int $customerId
     * @return \PHPUnit\Framework\MockObject\MockObject|CustomerInterface
     */
    private function getCustomerMock($customerId)
    {
        $customerMock = $this->createMock(CustomerInterface::class);
        $customerMock->expects($this->any())
            ->method('getId')
            ->willReturn($customerId);
        $customerMock->expects($this->any())
            ->method('getFirstname')
            ->willReturn('Test Firstname');
        $customerMock->expects($this->any())
            ->method('getLastname')
            ->willReturn('Test Lastname');
        $customerMock->expects($this->any())
            ->method('getEmail')
            ->willReturn('testemail@gmail.com');
        return $customerMock;
    }

    /**
     * Test getName method when no customer retrieved
     *
     * @param int|null $customerId
     * @dataProvider getDataNoCustomerDataProvider
     */
    public function testGetNameNoCustomer($customerId)
    {
        $name = null;

        $reviewMock = $this->createMock(ReviewInterface::class);
        $reviewMock->expects($this->any())
            ->method('getCustomerId')
            ->WillReturn($customerId);

        $this->customerRepositoryMock->expects($this->any())
            ->method('getById')
            ->with($customerId)
            ->willThrowException(new LocalizedException(__('No customer')));

        $this->assertEquals($name, (string)$this->resolver->getName($reviewMock));
    }

    /**
     * Test getName method
     */
    public function testGetName()
    {
        $name = 'Test Firstname';
        $customerId = 2;
        $customer = $this->getCustomerMock($customerId);

        $reviewMock = $this->createMock(ReviewInterface::class);
        $reviewMock->expects($this->any())
            ->method('getCustomerId')
            ->WillReturn($customerId);

        $this->customerRepositoryMock->expects($this->once())
            ->method('getById')
            ->with($customerId)
            ->willReturn($customer);
        $this->assertEquals($name, $this->resolver->getName($reviewMock));
    }

    /**
     * Test getEmail method when no customer retrieved
     *
     * @param int|null $customerId
     * @dataProvider getDataNoCustomerDataProvider
     */
    public function testGetEmailNoCustomer($customerId)
    {
        $email = null;

        $reviewMock = $this->createMock(ReviewInterface::class);
        $reviewMock->expects($this->any())
            ->method('getCustomerId')
            ->WillReturn($customerId);

        $this->customerRepositoryMock->expects($this->any())
            ->method('getById')
            ->with($customerId)
            ->willThrowException(new LocalizedException(__('No customer')));

        $this->assertEquals($email, $this->resolver->getEmail($reviewMock));
    }

    /**
     * Test getEmail method
     */
    public function testGetEmail()
    {
        $email = 'testemail@gmail.com';
        $customerId = 2;
        $customer = $this->getCustomerMock($customerId);

        $reviewMock = $this->createMock(ReviewInterface::class);
        $reviewMock->expects($this->any())
            ->method('getCustomerId')
            ->WillReturn($customerId);

        $this->customerRepositoryMock->expects($this->once())
            ->method('getById')
            ->with($customerId)
            ->willReturn($customer);
        $this->assertEquals($email, $this->resolver->getEmail($reviewMock));
    }
}
