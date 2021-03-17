<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Test\Unit\Model\Review\Validator\Agreements\AuthorType;

use Aheadworks\AdvancedReviews\Model\Review\Validator\Agreements\AuthorType\Customer;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Aheadworks\AdvancedReviews\Model\Review;
use Aheadworks\AdvancedReviews\Model\Agreements\Checker as AgreementsChecker;

/**
 * Test for \Aheadworks\AdvancedReviews\Model\Review\Validator\Agreements\AuthorType\Admin
 */
class CustomerTest extends TestCase
{
    /**
     * @var Customer
     */
    private $validator;

    /**
     * @var AgreementsChecker|\PHPUnit_Framework_MockObject_MockObject
     */
    private $agreementsCheckerMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->agreementsCheckerMock = $this->createMock(AgreementsChecker::class);

        $this->agreementsCheckerMock->expects($this->any())
            ->method('areAgreementsEnabled')
            ->willReturnMap(
                [
                    [
                        1,
                        true,
                    ],
                    [
                        2,
                        true,
                    ],
                    [
                        3,
                        false,
                    ],
                    [
                        4,
                        false,
                    ],
                ]
            );
        $this->agreementsCheckerMock->expects($this->any())
            ->method('isNeedToShowForCustomers')
            ->willReturnMap(
                [
                    [
                        1,
                        true,
                    ],
                    [
                        2,
                        false,
                    ],
                    [
                        3,
                        true,
                    ],
                    [
                        4,
                        false,
                    ],
                ]
            );

        $this->validator = $objectManager->getObject(
            Customer::class,
            [
                'agreementsChecker' => $this->agreementsCheckerMock,
            ]
        );
    }

    /**
     * Test for isValid method
     *
     * @param \PHPUnit\Framework\MockObject\MockObject|Review $validator
     * @param bool $result
     * @dataProvider isValidDataProvider
     */
    public function testIsValid($review, $result)
    {
        $this->assertEquals($result, $this->validator->isValid($review));
    }

    /**
     * @return array
     */
    public function isValidDataProvider()
    {
        return [
            [
                'review' => $this->getReviewMock(1, true),
                'result' => true,
            ],
            [
                'review' => $this->getReviewMock(1, false),
                'result' => false,
            ],
            [
                'review' => $this->getReviewMock(2, true),
                'result' => true,
            ],
            [
                'review' => $this->getReviewMock(2, false),
                'result' => true,
            ],
            [
                'review' => $this->getReviewMock(3, true),
                'result' => true,
            ],
            [
                'review' => $this->getReviewMock(3, false),
                'result' => true,
            ],
            [
                'review' => $this->getReviewMock(4, true),
                'result' => true,
            ],
            [
                'review' => $this->getReviewMock(4, false),
                'result' => true,
            ],
        ];
    }

    /**
     * Retrieve review mock
     *
     * @param int $storeId
     * @param bool|null $areAgreementsConfirmed
     * @return \PHPUnit\Framework\MockObject\MockObject|Review
     */
    private function getReviewMock($storeId, $areAgreementsConfirmed)
    {
        $review = $this->createMock(Review::class);
        $review->expects($this->any())
            ->method('getStoreId')
            ->willReturn($storeId);
        $review->expects($this->any())
            ->method('getAreAgreementsConfirmed')
            ->willReturn($areAgreementsConfirmed);
        return $review;
    }
}
