<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Test\Unit\Model\Review\Validator\Agreements;

use Aheadworks\AdvancedReviews\Model\Review\Validator\Agreements\Pool;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\TestCase;
use Magento\Framework\Validator\AbstractValidator;
use Magento\Framework\DataObject;

/**
 * Test for \Aheadworks\AdvancedReviews\Model\Review\Validator\Agreements\Pool
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
     * @param AbstractValidator[] $validators
     * @param int $authorType
     * @param AbstractValidator|null $result
     * @throws \ReflectionException
     * @dataProvider getValidatorByAuthorTypeDataProvider
     */
    public function testGetValidatorByAuthorType($validators, $authorType, $result)
    {
        $this->setProperty('validators', $validators);

        $this->assertSame($result, $this->pool->getValidatorByAuthorType($authorType));
    }

    /**
     * @return array
     */
    public function getValidatorByAuthorTypeDataProvider()
    {
        $validatorMock = $this->createMock(AbstractValidator::class);
        $badValidator = $this->createMock(DataObject::class);
        $validators = [
            'validator_one' => $validatorMock,
            'validator_bad' => $badValidator
        ];
        return [
            [
                'validators' => $validators,
                'authorType' => 'validator_one',
                'result' => $validatorMock
            ],
            [
                'validators' => $validators,
                'authorType' => 'unknown_type',
                'result' => null
            ],
            [
                'validators' => $validators,
                'authorType' => 'validator_bad',
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
