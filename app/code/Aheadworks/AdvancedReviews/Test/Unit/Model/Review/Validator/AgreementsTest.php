<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Test\Unit\Model\Review\Validator;

use Aheadworks\AdvancedReviews\Model\Review\Validator\Agreements;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Framework\Validator\AbstractValidator;
use Aheadworks\AdvancedReviews\Model\Review;
use Aheadworks\AdvancedReviews\Model\Review\Validator\Agreements\Pool as AgreementsValidatorPool;
use Aheadworks\AdvancedReviews\Model\Source\Review\AuthorType as ReviewAuthorTypeSource;

/**
 * Test for \Aheadworks\AdvancedReviews\Model\Review\Validator\Agreements
 */
class AgreementsTest extends TestCase
{
    /**
     * @var Agreements
     */
    private $validator;

    /**
     * @var AgreementsValidatorPool|\PHPUnit_Framework_MockObject_MockObject
     */
    private $agreementsValidatorPoolMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->agreementsValidatorPoolMock = $this->createMock(AgreementsValidatorPool::class);

        $this->validator = $objectManager->getObject(
            Agreements::class,
            [
                'agreementsValidatorPool' => $this->agreementsValidatorPoolMock,
            ]
        );
    }

    /**
     * Test isValid method
     *
     * @param AbstractValidator|null $validator
     * @param bool $result
     * @dataProvider isValidDataProvider
     */
    public function testIsValid($validator, $result)
    {
        $reviewAuthorType = ReviewAuthorTypeSource::CUSTOMER;
        $review = $this->createMock(Review::class);
        $review->expects($this->any())
            ->method('getAuthorType')
            ->willReturn($reviewAuthorType);

        $this->agreementsValidatorPoolMock->expects($this->any())
            ->method('getValidatorByAuthorType')
            ->with($reviewAuthorType)
            ->willReturn($validator);

        $this->assertEquals($result, $this->validator->isValid($review));
    }

    /**
     * @return array
     */
    public function isValidDataProvider()
    {
        return [
            [
                'validator' => null,
                'result' => true,
            ],
            [
                'validator' => $this->getValidatorMock(true),
                'result' => true,
            ],
            [
                'validator' => $this->getValidatorMock(false),
                'result' => false,
            ],
        ];
    }

    /**
     * Get validator mock
     *
     * @param bool $isReviewValid
     * @return AbstractValidator|\PHPUnit_Framework_MockObject_MockObject
     */
    private function getValidatorMock($isReviewValid)
    {
        $validatorMock = $this->createMock(AbstractValidator::class);
        $validatorMock->expects($this->any())
            ->method('isValid')
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
}
