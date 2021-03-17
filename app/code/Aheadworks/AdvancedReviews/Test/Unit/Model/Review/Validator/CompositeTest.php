<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Test\Unit\Model\Review\Validator;

use Aheadworks\AdvancedReviews\Model\Review\Validator\Composite;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Framework\Validator\AbstractValidator;
use Magento\Framework\DataObject;
use Aheadworks\AdvancedReviews\Api\Data\ReviewInterface;

/**
 * Test for \Aheadworks\AdvancedReviews\Model\Review\Validator\Composite
 */
class CompositeTest extends TestCase
{
    /**
     * @var Composite
     */
    private $validator;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->validator = $objectManager->getObject(
            Composite::class,
            []
        );
    }

    /**
     * Test isValid method if no validators used
     */
    public function testIsValidNoValidators()
    {
        $review = $this->createMock(ReviewInterface::class);

        $validators = [];

        $this->setProperty('validators', $validators);

        $this->assertTrue($this->validator->isValid($review));
    }

    /**
     * Test isValid method if bad validator used
     *
     * @expectedException \Exception
     */
    public function testIsValidBadValidator()
    {
        $review = $this->createMock(ReviewInterface::class);

        $goodValidatorMock = $this->getValidatorMock($review, true);
        $badValidatorMock = $this->createMock(DataObject::class);

        $validators = [
            'v1' => $goodValidatorMock,
            'v2' => $badValidatorMock,
        ];

        $this->setProperty('validators', $validators);

        $this->validator->isValid($review);
    }

    /**
     * Test isValid method
     *
     * @param bool $firstValidatorFlag
     * @param bool $secondValidatorFlag
     * @param bool $result
     * @dataProvider isValidDataProvider
     */
    public function testIsValid($firstValidatorFlag, $secondValidatorFlag, $result)
    {
        $review = $this->createMock(ReviewInterface::class);

        $validatorOneMock = $this->getValidatorMock($review, $firstValidatorFlag);
        $validatorTwoMock = $this->getValidatorMock($review, $secondValidatorFlag);

        $validators = [
            'v1' => $validatorOneMock,
            'v2' => $validatorTwoMock,
        ];

        $this->setProperty('validators', $validators);

        $this->assertEquals($result, $this->validator->isValid($review));
    }

    /**
     * @return array
     */
    public function isValidDataProvider()
    {
        return [
            [
                'firstValidatorFlag' => true,
                'secondValidatorFlag' => true,
                'result' => true,
            ],
            [
                'firstValidatorFlag' => true,
                'secondValidatorFlag' => false,
                'result' => false,
            ],
            [
                'firstValidatorFlag' => false,
                'secondValidatorFlag' => true,
                'result' => false,
            ],
            [
                'firstValidatorFlag' => false,
                'secondValidatorFlag' => false,
                'result' => false,
            ],
        ];
    }

    /**
     * Get validator mock
     *
     * @param ReviewInterface $review
     * @param bool $isReviewValid
     * @return AbstractValidator|\PHPUnit_Framework_MockObject_MockObject
     */
    private function getValidatorMock($review, $isReviewValid)
    {
        $validatorMock = $this->createMock(AbstractValidator::class);
        $validatorMock->expects($this->any())
            ->method('isValid')
            ->with($review)
            ->willReturn($isReviewValid);

        if ($isReviewValid) {
            $validatorMock->expects($this->never())
                ->method('getMessages');
        } else {
            $validatorMock->expects($this->atLeastOnce())
                ->method('getMessages')
                ->willReturn(['error message']);
        }

        return $validatorMock;
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
        $class = new \ReflectionClass($this->validator);
        $property = $class->getProperty($propertyName);
        $property->setAccessible(true);
        $property->setValue($this->validator, $value);

        return $this;
    }
}
