<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Test\Unit\Model\Review\Processor;

use Aheadworks\AdvancedReviews\Model\Review\Processor\Composite;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Aheadworks\AdvancedReviews\Model\Review\ProcessorInterface;
use Magento\Framework\DataObject;
use Aheadworks\AdvancedReviews\Api\Data\ReviewInterface;

/**
 * Test for \Aheadworks\AdvancedReviews\Model\Review\Processor\Composite
 */
class CompositeTest extends TestCase
{
    /**
     * @var Composite
     */
    private $processor;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->processor = $objectManager->getObject(
            Composite::class,
            []
        );
    }

    /**
     * Test process method if no processors used
     */
    public function testProcessNoProcessors()
    {
        $review = $this->createMock(ReviewInterface::class);

        $processors = [];

        $this->setProperty('processors', $processors);

        $this->assertSame($review, $this->processor->process($review));
    }

    /**
     * Test process method if bad processor used
     *
     * @expectedException \Exception
     */
    public function testProcessBadProcessor()
    {
        $review = $this->createMock(ReviewInterface::class);

        $goodProcessorMock = $this->getProcessorMock($review);
        $badProcessorMock = $this->createMock(DataObject::class);

        $processors = [
            'p1' => $goodProcessorMock,
            'p2' => $badProcessorMock,
        ];

        $this->setProperty('processors', $processors);

        $this->processor->process($review);
    }

    /**
     * Test process method
     */
    public function testProcess()
    {
        $review = $this->createMock(ReviewInterface::class);

        $firstProcessorMock = $this->getProcessorMock($review);
        $secondProcessorMock = $this->getProcessorMock($review);

        $processors = [
            'p1' => $firstProcessorMock,
            'p2' => $secondProcessorMock,
        ];

        $this->setProperty('processors', $processors);

        $this->assertSame($review, $this->processor->process($review));
    }

    /**
     * Get processor mock
     *
     * @param ReviewInterface $review
     * @return ProcessorInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private function getProcessorMock($review)
    {
        $processorMock = $this->createMock(ProcessorInterface::class);
        $processorMock->expects($this->any())
            ->method('process')
            ->with($review)
            ->willReturn($review);

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
        $class = new \ReflectionClass($this->processor);
        $property = $class->getProperty($propertyName);
        $property->setAccessible(true);
        $property->setValue($this->processor, $value);

        return $this;
    }
}
