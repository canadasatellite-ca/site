<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Test\Unit\Model\Data;

use Aheadworks\AdvancedReviews\Model\Data\CompositeProcessor;
use Aheadworks\AdvancedReviews\Model\Data\ProcessorInterface;
use Magento\Framework\DataObject;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

/**
 * Test for \Aheadworks\AdvancedReviews\Model\Data\CompositeProcessor
 */
class CompositeProcessorTest extends TestCase
{
    /**
     * @var CompositeProcessor
     */
    private $compositeProcessor;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->compositeProcessor = $objectManager->getObject(CompositeProcessor::class, []);
    }

    /**
     * Test process method
     */
    public function testProcess()
    {
        $data = [
            'entity_id' => 10,
            'name' => 'Sample Name',
        ];
        $dataProcessedByFirst = [
            'entity_id' => 10,
            'name' => 'Sample Name Processed First',
        ];
        $dataProcessedBySecond = [
            'entity_id' => 10,
            'name' => 'Sample Name Processed Second',
        ];

        $processorOneMock = $this->getProcessorMock($data, $dataProcessedByFirst);
        $processorTwoMock = $this->getProcessorMock(
            $dataProcessedByFirst,
            $dataProcessedBySecond
        );

        $processors = [
            'p1' => $processorOneMock,
            'p2' => $processorTwoMock,
        ];

        $this->setProperty('processors', $processors);

        $this->assertEquals($dataProcessedBySecond, $this->compositeProcessor->process($data));
    }

    /**
     * Test process method if no processors used
     */
    public function testProcessNoProcessors()
    {
        $data = [
            'entity_id' => 10,
            'name' => 'Sample Name',
        ];

        $processors = [];

        $this->setProperty('processors', $processors);

        $this->assertEquals($data, $this->compositeProcessor->process($data));
    }

    /**
     * Test process method if bad processor used
     *
     * @expectedException \Exception
     */
    public function testProcessBadProcessor()
    {
        $data = [
            'entity_id' => 10,
            'name' => 'Sample Name',
        ];
        $dataProcessed = [
            'entity_id' => 10,
            'name' => 'Sample Name Processed First',
        ];

        $goodProcessorMock = $this->getProcessorMock($data, $dataProcessed);
        $badProcessorMock = $this->createMock(DataObject::class);

        $processors = [
            'p1' => $goodProcessorMock,
            'p2' => $badProcessorMock,
        ];

        $this->setProperty('processors', $processors);

        $this->compositeProcessor->process($data);
    }

    /**
     * Get processor mock
     *
     * @param array $data
     * @param array $processedData
     * @return ProcessorInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private function getProcessorMock($data, $processedData)
    {
        $processorMock = $this->createMock(ProcessorInterface::class);
        $processorMock->expects($this->once())
            ->method('process')
            ->with($data)
            ->willReturn($processedData);

        return $processorMock;
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
        $class = new \ReflectionClass($this->compositeProcessor);
        $property = $class->getProperty($propertyName);
        $property->setAccessible(true);
        $property->setValue($this->compositeProcessor, $value);

        return $this;
    }
}
