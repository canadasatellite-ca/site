<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Test\Unit\Model\Data\Processor\Review;

use Aheadworks\AdvancedReviews\Model\Data\Processor\Review\StoreId;
use Magento\Framework\Exception\LocalizedException;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Aheadworks\AdvancedReviews\Api\Data\ReviewInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Api\Data\StoreInterface;

/**
 * Test for \Aheadworks\AdvancedReviews\Model\Data\Processor\Review\StoreId
 */
class StoreIdTest extends TestCase
{
    const CURRENT_STORE_ID = 3;

    /**
     * @var StoreId
     */
    private $processor;

    /**
     * @var StoreManagerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $storeManagerMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->storeManagerMock = $this->createMock(StoreManagerInterface::class);

        $this->processor = $objectManager->getObject(
            StoreId::class,
            [
                'storeManager' => $this->storeManagerMock
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
        $storeMock = $this->createMock(StoreInterface::class);
        $storeMock->expects($this->any())
            ->method('getId')
            ->willReturn(self::CURRENT_STORE_ID);

        $this->storeManagerMock->expects($this->any())
            ->method('getStore')
            ->with(true)
            ->willReturn($storeMock);

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
                'result' => [
                    ReviewInterface::STORE_ID => self::CURRENT_STORE_ID,
                ],
            ],
            [
                'data' => [
                    ReviewInterface::ID => 1,
                ],
                'result' => [
                    ReviewInterface::ID => 1,
                    ReviewInterface::STORE_ID => self::CURRENT_STORE_ID,
                ],
            ],
            [
                'data' => [
                    ReviewInterface::ID => 1,
                    ReviewInterface::STORE_ID => self::CURRENT_STORE_ID,
                ],
                'result' => [
                    ReviewInterface::ID => 1,
                    ReviewInterface::STORE_ID => self::CURRENT_STORE_ID,
                ],
            ],
            [
                'data' => [
                    ReviewInterface::ID => 1,
                    ReviewInterface::STORE_ID => 1,
                ],
                'result' => [
                    ReviewInterface::ID => 1,
                    ReviewInterface::STORE_ID => 1,
                ],
            ],
            [
                'data' => [
                    ReviewInterface::ID => 1,
                    ReviewInterface::STORE_ID => "",
                ],
                'result' => [
                    ReviewInterface::ID => 1,
                    ReviewInterface::STORE_ID => "",
                ],
            ],
            [
                'data' => [
                    ReviewInterface::ID => 1,
                    ReviewInterface::STORE_ID => 0,
                ],
                'result' => [
                    ReviewInterface::ID => 1,
                    ReviewInterface::STORE_ID => 0,
                ],
            ],
        ];
    }

    /**
     * Test for process method with exception
     *
     * @param array $data
     * @param array $result
     * @param bool $isExceptionThrown
     * @dataProvider processWithExceptionDataProvider
     */
    public function testProcessWithException($data, $result, $isExceptionThrown)
    {
        $exception = new NoSuchEntityException();
        $this->storeManagerMock->expects($this->any())
            ->method('getStore')
            ->with(true)
            ->willThrowException($exception);

        if ($isExceptionThrown) {
            try {
                $this->processor->process($data);
            } catch (LocalizedException $exceptionThrown) {
                $this->assertSame($exception, $exceptionThrown);
            }
        } else {
            $this->assertEquals($result, $this->processor->process($data));
        }
    }

    /**
     * @return array
     */
    public function processWithExceptionDataProvider()
    {
        return [
            [
                'data' => [],
                'result' => [
                    ReviewInterface::STORE_ID => self::CURRENT_STORE_ID,
                ],
                'isExceptionThrown' => true,
            ],
            [
                'data' => [
                    ReviewInterface::ID => 1,
                ],
                'result' => [
                    ReviewInterface::ID => 1,
                    ReviewInterface::STORE_ID => self::CURRENT_STORE_ID,
                ],
                'isExceptionThrown' => true,
            ],
            [
                'data' => [
                    ReviewInterface::ID => 1,
                    ReviewInterface::STORE_ID => self::CURRENT_STORE_ID,
                ],
                'result' => [
                    ReviewInterface::ID => 1,
                    ReviewInterface::STORE_ID => self::CURRENT_STORE_ID,
                ],
                'isExceptionThrown' => false,
            ],
            [
                'data' => [
                    ReviewInterface::ID => 1,
                    ReviewInterface::STORE_ID => 1,
                ],
                'result' => [
                    ReviewInterface::ID => 1,
                    ReviewInterface::STORE_ID => 1,
                ],
                'isExceptionThrown' => false,
            ],
            [
                'data' => [
                    ReviewInterface::ID => 1,
                    ReviewInterface::STORE_ID => "",
                ],
                'result' => [
                    ReviewInterface::ID => 1,
                    ReviewInterface::STORE_ID => "",
                ],
                'isExceptionThrown' => false,
            ],
            [
                'data' => [
                    ReviewInterface::ID => 1,
                    ReviewInterface::STORE_ID => 0,
                ],
                'result' => [
                    ReviewInterface::ID => 1,
                    ReviewInterface::STORE_ID => 0,
                ],
                'isExceptionThrown' => false,
            ],
        ];
    }
}
