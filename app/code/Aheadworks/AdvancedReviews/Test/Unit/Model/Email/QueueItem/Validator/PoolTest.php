<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Test\Unit\Model\Email\QueueItem\Validator;

use Aheadworks\AdvancedReviews\Model\Email\QueueItem\Validator\Pool;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\TestCase;
use Aheadworks\AdvancedReviews\Model\Email\QueueItem\ValidatorInterface;
use Magento\Framework\DataObject;

/**
 * Test for \Aheadworks\AdvancedReviews\Model\Email\QueueItem\Validator\Pool
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
     * Test getValidatorByType method
     *
     * @param ValidatorInterface[] $validators
     * @param int $type
     * @param ValidatorInterface|null $result
     * @throws \ReflectionException
     * @dataProvider getValidatorByTypeDataProvider
     */
    public function testGetValidatorByType($validators, $type, $result)
    {
        $this->setProperty('validators', $validators);

        $this->assertSame($result, $this->pool->getValidatorByType($type));
    }

    /**
     * @return array
     */
    public function getValidatorByTypeDataProvider()
    {
        $validatorMock = $this->createMock(ValidatorInterface::class);
        $badValidator = $this->createMock(DataObject::class);
        $validators = [
            'validator_one' => $validatorMock,
            'validator_bad' => $badValidator
        ];
        return [
            [
                'validators' => $validators,
                'emailType' => 'validator_one',
                'result' => $validatorMock
            ],
            [
                'validators' => $validators,
                'emailType' => 'unknown_type',
                'result' => null
            ],
            [
                'validators' => $validators,
                'emailType' => 'validator_bad',
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
