<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Test\Unit\Model\Data\Processor;

use Aheadworks\AdvancedReviews\Model\Data\Processor\Boolean;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Framework\Stdlib\BooleanUtils;
use Aheadworks\AdvancedReviews\Api\Data\ReviewInterface;

/**
 * Test for \Aheadworks\AdvancedReviews\Model\Data\Processor\Boolean
 */
class BooleanTest extends TestCase
{
    /**
     * @var Boolean
     */
    private $processor;

    /**
     * @var BooleanUtils|\PHPUnit_Framework_MockObject_MockObject
     */
    private $booleanUtilsMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->booleanUtilsMock = $this->createMock(BooleanUtils::class);

        $this->processor = $objectManager->getObject(
            Boolean::class,
            [
                'booleanUtils' => $this->booleanUtilsMock,
            ]
        );
    }

    /**
     * Test process method when field name is not set
     *
     * @param array $data
     * @param array $result
     * @dataProvider processFieldNameIsNotSetDataProvider
     */
    public function testProcessFieldNameIsNotSet($data, $result)
    {
        $this->booleanUtilsMock->expects($this->never())
            ->method('toBoolean');

        $this->assertSame($result, $this->processor->process($data));
    }

    /**
     * @return array
     */
    public function processFieldNameIsNotSetDataProvider()
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
        ];
    }

    /**
     * Test process method
     *
     * @param array $data
     * @param array $result
     * @dataProvider processDataProvider
     */
    public function testProcess($data, $result)
    {
        $this->setProperty('fieldName', ReviewInterface::ARE_AGREEMENTS_CONFIRMED);

        $this->booleanUtilsMock->expects($this->any())
            ->method('toBoolean')
            ->willReturnMap(
                [
                    [
                        true,
                        true,
                    ],
                    [
                        1,
                        true,
                    ],
                    [
                        'true',
                        true,
                    ],
                    [
                        '1',
                        true,
                    ],
                    [
                        false,
                        false,
                    ],
                    [
                        0,
                        false,
                    ],
                    [
                        'false',
                        false,
                    ],
                    [
                        '0',
                        false,
                    ],
                ]
            );

        $this->assertSame($result, $this->processor->process($data));
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
                    ReviewInterface::ARE_AGREEMENTS_CONFIRMED => null,
                ],
                'result' => [
                    ReviewInterface::ID => 1,
                    ReviewInterface::ARE_AGREEMENTS_CONFIRMED => null,
                ],
            ],
            [
                'data' => [
                    ReviewInterface::ID => 1,
                    ReviewInterface::ARE_AGREEMENTS_CONFIRMED => 1,
                ],
                'result' => [
                    ReviewInterface::ID => 1,
                    ReviewInterface::ARE_AGREEMENTS_CONFIRMED => true,
                ],
            ],
            [
                'data' => [
                    ReviewInterface::ID => 1,
                    ReviewInterface::ARE_AGREEMENTS_CONFIRMED => '1',
                ],
                'result' => [
                    ReviewInterface::ID => 1,
                    ReviewInterface::ARE_AGREEMENTS_CONFIRMED => true,
                ],
            ],
            [
                'data' => [
                    ReviewInterface::ID => 1,
                    ReviewInterface::ARE_AGREEMENTS_CONFIRMED => true,
                ],
                'result' => [
                    ReviewInterface::ID => 1,
                    ReviewInterface::ARE_AGREEMENTS_CONFIRMED => true,
                ],
            ],
            [
                'data' => [
                    ReviewInterface::ID => 1,
                    ReviewInterface::ARE_AGREEMENTS_CONFIRMED => 'true',
                ],
                'result' => [
                    ReviewInterface::ID => 1,
                    ReviewInterface::ARE_AGREEMENTS_CONFIRMED => true,
                ],
            ],
            [
                'data' => [
                    ReviewInterface::ID => 1,
                    ReviewInterface::ARE_AGREEMENTS_CONFIRMED => 0,
                ],
                'result' => [
                    ReviewInterface::ID => 1,
                    ReviewInterface::ARE_AGREEMENTS_CONFIRMED => false,
                ],
            ],
            [
                'data' => [
                    ReviewInterface::ID => 1,
                    ReviewInterface::ARE_AGREEMENTS_CONFIRMED => '0',
                ],
                'result' => [
                    ReviewInterface::ID => 1,
                    ReviewInterface::ARE_AGREEMENTS_CONFIRMED => false,
                ],
            ],
            [
                'data' => [
                    ReviewInterface::ID => 1,
                    ReviewInterface::ARE_AGREEMENTS_CONFIRMED => false,
                ],
                'result' => [
                    ReviewInterface::ID => 1,
                    ReviewInterface::ARE_AGREEMENTS_CONFIRMED => false,
                ],
            ],
            [
                'data' => [
                    ReviewInterface::ID => 1,
                    ReviewInterface::ARE_AGREEMENTS_CONFIRMED => 'false',
                ],
                'result' => [
                    ReviewInterface::ID => 1,
                    ReviewInterface::ARE_AGREEMENTS_CONFIRMED => false,
                ],
            ],
        ];
    }

    /**
     * Set property
     *
     * @param string $propertyName
     * @param mixed $value
     * @return mixed
     * @throws \ReflectionException
     */
    private function setProperty($propertyName, $value)
    {
        $class = new \ReflectionClass($this->processor);
        $property = $class->getProperty($propertyName);
        $property->setAccessible(true);
        $property->setValue($this->processor, $value);

        return $this;
    }
}
