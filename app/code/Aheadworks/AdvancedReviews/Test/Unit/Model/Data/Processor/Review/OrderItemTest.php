<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Test\Unit\Model\Data\Processor\Review;

use Aheadworks\AdvancedReviews\Model\Data\Processor\Review\OrderItem;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Aheadworks\AdvancedReviews\Api\Data\ReviewInterface;
use Magento\Sales\Api\Data\OrderItemInterface;
use Magento\Sales\Api\OrderItemRepositoryInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Aheadworks\AdvancedReviews\Model\Source\Review\IsVerifiedBuyer as ReviewIsVerifiedBuyerSource;
use Aheadworks\AdvancedReviews\Model\Agreements\Checker as AgreementsChecker;

/**
 * Test for \Aheadworks\AdvancedReviews\Model\Data\Processor\Review\OrderItem
 */
class OrderItemTest extends TestCase
{
    /**
     * @var OrderItem
     */
    private $processor;

    /**
     * @var OrderItemRepositoryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $orderItemRepositoryMock;

    /**
     * @var OrderRepositoryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $orderRepositoryMock;

    /**
     * @var AgreementsChecker|\PHPUnit_Framework_MockObject_MockObject
     */
    private $agreementsCheckerMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->orderItemRepositoryMock = $this->createMock(OrderItemRepositoryInterface::class);
        $this->orderRepositoryMock = $this->createMock(OrderRepositoryInterface::class);
        $this->agreementsCheckerMock = $this->createMock(AgreementsChecker::class);

        $this->orderItemRepositoryMock->expects($this->any())
            ->method('get')
            ->willReturnMap(
                [
                    [
                        1,
                        $this->getOrderItemMock(1, 1, 1),
                    ],
                    [
                        2,
                        $this->getOrderItemMock(2, 16, 2),
                    ]
                ]
            );

        $this->orderRepositoryMock->expects($this->any())
            ->method('get')
            ->willReturnMap(
                [
                    [
                        1,
                        $this->getOrderMock(1, 'customer1@gmail.com'),
                    ],
                    [
                        2,
                        $this->getOrderMock(null, 'guest@gmail.com'),
                    ]
                ]
            );

        $this->agreementsCheckerMock->expects($this->any())
            ->method('areAgreementsEnabled')
            ->willReturnMap(
                [
                    [
                        1,
                        true,
                    ],
                    [
                        2,
                        false,
                    ],
                ]
            );

        $this->processor = $objectManager->getObject(
            OrderItem::class,
            [
                'orderItemRepository' => $this->orderItemRepositoryMock,
                'orderRepository' => $this->orderRepositoryMock,
                'agreementsChecker' => $this->agreementsCheckerMock,
            ]
        );
    }

    /**
     * Test for process method
     *
     * @param array $data
     * @param array $result
     * @dataProvider processDataProvider
     */
    public function testProcess($data, $result)
    {
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
                'result' => [],
            ],
            [
                'data' => [
                    ReviewInterface::ID => 1,
                ],
                'result' => [
                    ReviewInterface::ID => 1,
                ],
            ],
            [
                'data' => [
                    ReviewInterface::ID => 1,
                    ReviewInterface::ORDER_ITEM_ID => 1,
                ],
                'result' => [
                    ReviewInterface::ID => 1,
                    ReviewInterface::ORDER_ITEM_ID => 1,
                    ReviewInterface::CUSTOMER_ID => 1,
                    ReviewInterface::PRODUCT_ID => 1,
                    ReviewInterface::STORE_ID => 1,
                    ReviewInterface::IS_VERIFIED_BUYER => ReviewIsVerifiedBuyerSource::YES,
                    ReviewInterface::ARE_AGREEMENTS_CONFIRMED => true,
                ],
            ],
            [
                'data' => [
                    ReviewInterface::ID => 1,
                    ReviewInterface::ORDER_ITEM_ID => 1,
                    ReviewInterface::CUSTOMER_ID => null,
                    ReviewInterface::PRODUCT_ID => null,
                    ReviewInterface::STORE_ID => null,
                    ReviewInterface::IS_VERIFIED_BUYER => null,
                ],
                'result' => [
                    ReviewInterface::ID => 1,
                    ReviewInterface::ORDER_ITEM_ID => 1,
                    ReviewInterface::CUSTOMER_ID => 1,
                    ReviewInterface::PRODUCT_ID => 1,
                    ReviewInterface::STORE_ID => 1,
                    ReviewInterface::IS_VERIFIED_BUYER => ReviewIsVerifiedBuyerSource::YES,
                    ReviewInterface::ARE_AGREEMENTS_CONFIRMED => true,
                ],
            ],
            [
                'data' => [
                    ReviewInterface::ID => 1,
                    ReviewInterface::ORDER_ITEM_ID => 1,
                    ReviewInterface::CUSTOMER_ID => 12,
                    ReviewInterface::PRODUCT_ID => 14,
                    ReviewInterface::STORE_ID => 12,
                    ReviewInterface::IS_VERIFIED_BUYER => 1,
                ],
                'result' => [
                    ReviewInterface::ID => 1,
                    ReviewInterface::ORDER_ITEM_ID => 1,
                    ReviewInterface::CUSTOMER_ID => 1,
                    ReviewInterface::PRODUCT_ID => 1,
                    ReviewInterface::STORE_ID => 1,
                    ReviewInterface::IS_VERIFIED_BUYER => ReviewIsVerifiedBuyerSource::YES,
                    ReviewInterface::ARE_AGREEMENTS_CONFIRMED => true,
                ],
            ],
            [
                'data' => [
                    ReviewInterface::ID => 1,
                    ReviewInterface::ORDER_ITEM_ID => 2,
                ],
                'result' => [
                    ReviewInterface::ID => 1,
                    ReviewInterface::ORDER_ITEM_ID => 2,
                    ReviewInterface::CUSTOMER_ID => null,
                    ReviewInterface::EMAIL => 'guest@gmail.com',
                    ReviewInterface::PRODUCT_ID => 16,
                    ReviewInterface::STORE_ID => 2,
                    ReviewInterface::IS_VERIFIED_BUYER => ReviewIsVerifiedBuyerSource::YES,
                ],
            ],
            [
                'data' => [
                    ReviewInterface::ID => 1,
                    ReviewInterface::ORDER_ITEM_ID => 2,
                    ReviewInterface::CUSTOMER_ID => null,
                    ReviewInterface::EMAIL => null,
                    ReviewInterface::PRODUCT_ID => null,
                    ReviewInterface::STORE_ID => null,
                    ReviewInterface::IS_VERIFIED_BUYER => null,
                ],
                'result' => [
                    ReviewInterface::ID => 1,
                    ReviewInterface::ORDER_ITEM_ID => 2,
                    ReviewInterface::CUSTOMER_ID => null,
                    ReviewInterface::EMAIL => 'guest@gmail.com',
                    ReviewInterface::PRODUCT_ID => 16,
                    ReviewInterface::STORE_ID => 2,
                    ReviewInterface::IS_VERIFIED_BUYER => ReviewIsVerifiedBuyerSource::YES,
                ],
            ],
            [
                'data' => [
                    ReviewInterface::ID => 1,
                    ReviewInterface::ORDER_ITEM_ID => 2,
                    ReviewInterface::CUSTOMER_ID => 12,
                    ReviewInterface::EMAIL => 'temporary value',
                    ReviewInterface::PRODUCT_ID => 14,
                    ReviewInterface::STORE_ID => 12,
                    ReviewInterface::IS_VERIFIED_BUYER => 1,
                ],
                'result' => [
                    ReviewInterface::ID => 1,
                    ReviewInterface::ORDER_ITEM_ID => 2,
                    ReviewInterface::CUSTOMER_ID => null,
                    ReviewInterface::EMAIL => 'guest@gmail.com',
                    ReviewInterface::PRODUCT_ID => 16,
                    ReviewInterface::STORE_ID => 2,
                    ReviewInterface::IS_VERIFIED_BUYER => ReviewIsVerifiedBuyerSource::YES,
                ],
            ],
        ];
    }

    /**
     * Retrieve mock for order item
     *
     * @param int $orderId
     * @param int $productId
     * @param int $storeId
     * @return \PHPUnit\Framework\MockObject\MockObject|OrderItemInterface
     */
    private function getOrderItemMock($orderId, $productId, $storeId)
    {
        $orderItemMock = $this->createMock(OrderItemInterface::class);
        $orderItemMock->expects($this->any())
            ->method('getOrderId')
            ->willReturn($orderId);
        $orderItemMock->expects($this->any())
            ->method('getProductId')
            ->willReturn($productId);
        $orderItemMock->expects($this->any())
            ->method('getStoreId')
            ->willReturn($storeId);
        return $orderItemMock;
    }

    /**
     * Retrieve mock for order
     *
     * @param int|null $customerId
     * @param int $customerEmail
     * @return \PHPUnit\Framework\MockObject\MockObject|OrderInterface
     */
    private function getOrderMock($customerId, $customerEmail)
    {
        $orderMock = $this->createMock(OrderInterface::class);
        $orderMock->expects($this->any())
            ->method('getCustomerId')
            ->willReturn($customerId);
        $orderMock->expects($this->any())
            ->method('getCustomerEmail')
            ->willReturn($customerEmail);
        return $orderMock;
    }
}
