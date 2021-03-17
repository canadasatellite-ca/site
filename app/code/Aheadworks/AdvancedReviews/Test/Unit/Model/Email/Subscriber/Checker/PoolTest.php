<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Test\Unit\Model\Email\Subscriber\Checker;

use Aheadworks\AdvancedReviews\Model\Email\Subscriber\Checker\Pool;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\TestCase;
use Aheadworks\AdvancedReviews\Model\Email\Subscriber\Checker\CheckerInterface;
use Magento\Framework\DataObject;

/**
 * Test for \Aheadworks\AdvancedReviews\Model\Email\Subscriber\Checker\Pool
 */
class PoolTest extends TestCase
{
    /**
     * @var Pool
     */
    private $pool;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->pool = $objectManager->getObject(Pool::class, []);
    }

    /**
     * Test getCheckerByEmailType method
     *
     * @param CheckerInterface[] $checkers
     * @param int $emailType
     * @param CheckerInterface|null $result
     * @throws \ReflectionException
     * @dataProvider getCheckerByEmailTypeDataProvider
     */
    public function testGetCheckerByEmailType($checkers, $emailType, $result)
    {
        $this->setProperty('checkers', $checkers);

        $this->assertSame($result, $this->pool->getCheckerByEmailType($emailType));
    }

    /**
     * @return array
     */
    public function getCheckerByEmailTypeDataProvider()
    {
        $checkerMock = $this->createMock(CheckerInterface::class);
        $badChecker = $this->createMock(DataObject::class);
        $checkers = [
            'checker_one' => $checkerMock,
            'checker_bad' => $badChecker
        ];
        return [
            [
                'checkers' => $checkers,
                'emailType' => 'checker_one',
                'result' => $checkerMock
            ],
            [
                'checkers' => $checkers,
                'emailType' => 'unknown_type',
                'result' => null
            ],
            [
                'checkers' => $checkers,
                'emailType' => 'checker_bad',
                'result' => null
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
        $class = new \ReflectionClass($this->pool);
        $property = $class->getProperty($propertyName);
        $property->setAccessible(true);
        $property->setValue($this->pool, $value);

        return $this;
    }
}
