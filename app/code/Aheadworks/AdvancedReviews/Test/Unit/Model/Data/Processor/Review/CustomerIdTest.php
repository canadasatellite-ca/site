<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Test\Unit\Model\Data\Processor\Review;

use Aheadworks\AdvancedReviews\Model\Data\Processor\Review\CustomerId;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Aheadworks\AdvancedReviews\Api\Data\ReviewInterface;
use Magento\Customer\Model\Session as CustomerSession;

/**
 * Test for \Aheadworks\AdvancedReviews\Model\Data\Processor\Review\CustomerId
 */
class CustomerIdTest extends TestCase
{
    /**
     * @var CustomerId
     */
    private $processor;

    /**
     * @var CustomerSession|\PHPUnit_Framework_MockObject_MockObject
     */
    private $customerSessionMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->customerSessionMock = $this->createMock(CustomerSession::class);

        $this->processor = $objectManager->getObject(
            CustomerId::class,
            [
                'customerSession' => $this->customerSessionMock
            ]
        );
    }

    /**
     * Test for process method
     *
     * @param array $data
     * @param int|null $customerId
     * @param array $result
     * @dataProvider processDataProvider
     */
    public function testProcess($data, $customerId, $result)
    {
        $this->customerSessionMock->expects($this->any())
            ->method('getCustomerId')
            ->willReturn($customerId);

        $this->assertEquals($result, $this->processor->process($data));
    }

    /**
     * @return array
     */
    public function processDataProvider()
    {
        return [
            [
                'data' => [],
                'customerId' => 1,
                'result' => [
                    ReviewInterface::CUSTOMER_ID => 1,
                ],
            ],
            [
                'data' => [
                    ReviewInterface::ID => 1,
                ],
                'customerId' => 1,
                'result' => [
                    ReviewInterface::ID => 1,
                    ReviewInterface::CUSTOMER_ID => 1,
                ],
            ],
            [
                'data' => [
                    ReviewInterface::ID => 1,
                    ReviewInterface::CUSTOMER_ID => 1,
                ],
                'customerId' => 1,
                'result' => [
                    ReviewInterface::ID => 1,
                    ReviewInterface::CUSTOMER_ID => 1,
                ],
            ],
            [
                'data' => [
                    ReviewInterface::ID => 1,
                    ReviewInterface::CUSTOMER_ID => 10,
                ],
                'customerId' => 1,
                'result' => [
                    ReviewInterface::ID => 1,
                    ReviewInterface::CUSTOMER_ID => 10,
                ],
            ],
            [
                'data' => [
                    ReviewInterface::ID => 1,
                    ReviewInterface::CUSTOMER_ID => "",
                ],
                'customerId' => 1,
                'result' => [
                    ReviewInterface::ID => 1,
                    ReviewInterface::CUSTOMER_ID => "",
                ],
            ],
            [
                'data' => [
                    ReviewInterface::ID => 1,
                    ReviewInterface::CUSTOMER_ID => 0,
                ],
                'customerId' => 1,
                'result' => [
                    ReviewInterface::ID => 1,
                    ReviewInterface::CUSTOMER_ID => 0,
                ],
            ],
            [
                'data' => [],
                'customerId' => null,
                'result' => [
                    ReviewInterface::CUSTOMER_ID => null,
                ],
            ],
            [
                'data' => [
                    ReviewInterface::ID => 1,
                ],
                'customerId' => null,
                'result' => [
                    ReviewInterface::ID => 1,
                    ReviewInterface::CUSTOMER_ID => null,
                ],
            ],
            [
                'data' => [
                    ReviewInterface::ID => 1,
                    ReviewInterface::CUSTOMER_ID => 1,
                ],
                'customerId' => null,
                'result' => [
                    ReviewInterface::ID => 1,
                    ReviewInterface::CUSTOMER_ID => 1,
                ],
            ],
            [
                'data' => [
                    ReviewInterface::ID => 1,
                    ReviewInterface::CUSTOMER_ID => 10,
                ],
                'customerId' => null,
                'result' => [
                    ReviewInterface::ID => 1,
                    ReviewInterface::CUSTOMER_ID => 10,
                ],
            ],
            [
                'data' => [
                    ReviewInterface::ID => 1,
                    ReviewInterface::CUSTOMER_ID => "",
                ],
                'customerId' => null,
                'result' => [
                    ReviewInterface::ID => 1,
                    ReviewInterface::CUSTOMER_ID => "",
                ],
            ],
            [
                'data' => [
                    ReviewInterface::ID => 1,
                    ReviewInterface::CUSTOMER_ID => 0,
                ],
                'customerId' => null,
                'result' => [
                    ReviewInterface::ID => 1,
                    ReviewInterface::CUSTOMER_ID => 0,
                ],
            ],
        ];
    }
}
