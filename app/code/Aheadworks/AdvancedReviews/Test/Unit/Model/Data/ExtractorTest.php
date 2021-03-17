<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Test\Unit\Model\Data;

use Aheadworks\AdvancedReviews\Model\Data\Extractor;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Framework\Stdlib\ArrayManager;

/**
 * Test for \Aheadworks\AdvancedReviews\Model\Email\DateTime\Resolver
 */
class ExtractorTest extends TestCase
{
    /**
     * @var Extractor
     */
    private $model;

    /**
     * @var ArrayManager|\PHPUnit_Framework_MockObject_MockObject
     */
    private $arrayManagerMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->arrayManagerMock = $this->createMock(ArrayManager::class);

        $this->model = $objectManager->getObject(
            Extractor::class,
            [
                'arrayManager' => $this->arrayManagerMock,
            ]
        );
    }

    /**
     * Test extractFields method when no field to extract specified
     */
    public function testExtractFieldsNoFields()
    {
        $result = [];
        $data = [
            'test' => 'value',
        ];

        $this->assertSame($result, $this->model->extractFields($data));
    }

    /**
     * Test extractFields method
     */
    public function testExtractFields()
    {
        $data = [
            'test1' => 'value1',
            'test2' => 'value2',
        ];
        $fields = [
            'test2',
            'test3',
        ];
        $result = [
            'test2' => 'value2',
            'test3' => null,
        ];

        $this->setProperty('fieldNames', $fields);

        $this->arrayManagerMock->expects($this->exactly(2))
            ->method('get')
            ->willReturnMap(
                [
                    [
                        'test2',
                        $data,
                        null,
                        ArrayManager::DEFAULT_PATH_DELIMITER,
                        'value2',
                    ],
                    [
                        'test3',
                        $data,
                        null,
                        ArrayManager::DEFAULT_PATH_DELIMITER,
                        null,
                    ],
                ]
            );

        $this->arrayManagerMock->expects($this->exactly(2))
            ->method('set')
            ->withConsecutive(
                [
                    'test2',
                    [],
                    'value2',
                    ArrayManager::DEFAULT_PATH_DELIMITER,
                ],
                [
                    'test3',
                    ['test2' => 'value2',],
                    null,
                    ArrayManager::DEFAULT_PATH_DELIMITER,
                ]
            )->willReturnOnConsecutiveCalls(
                ['test2' => 'value2',],
                ['test2' => 'value2', 'test3' => null,]
            );

        $this->assertSame($result, $this->model->extractFields($data));
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
        $class = new \ReflectionClass($this->model);
        $property = $class->getProperty($propertyName);
        $property->setAccessible(true);
        $property->setValue($this->model, $value);

        return $this;
    }
}
