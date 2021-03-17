<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Test\Unit\Model\Data\Processor\Review;

use Aheadworks\AdvancedReviews\Model\Data\Processor\Review\Agreements;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Aheadworks\AdvancedReviews\Api\Data\ReviewInterface;
use Aheadworks\AdvancedReviews\Model\Agreements\Checker as AgreementsChecker;
use Aheadworks\AdvancedReviews\Model\Agreements\Resolver as AgreementsResolver;
use Magento\CheckoutAgreements\Api\Data\AgreementInterface;

/**
 * Test for \Aheadworks\AdvancedReviews\Model\Data\Processor\Review\Agreements
 */
class AgreementsTest extends TestCase
{
    /**
     * @var Agreements
     */
    private $processor;

    /**
     * @var AgreementsChecker|\PHPUnit_Framework_MockObject_MockObject
     */
    private $agreementsCheckerMock;

    /**
     * @var AgreementsResolver|\PHPUnit_Framework_MockObject_MockObject
     */
    private $agreementsResolverMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->agreementsCheckerMock = $this->createMock(AgreementsChecker::class);
        $this->agreementsResolverMock = $this->createMock(AgreementsResolver::class);

        $this->processor = $objectManager->getObject(
            Agreements::class,
            [
                'agreementsChecker' => $this->agreementsCheckerMock,
                'agreementsResolver' => $this->agreementsResolverMock,
            ]
        );
    }

    /**
     * Test process method when no store id specified
     *
     * @param array $data
     * @param array $result
     * @dataProvider processNoStoreSpecifiedDataProvider
     */
    public function testProcessNoStoreSpecified($data, $result)
    {
        $this->agreementsCheckerMock->expects($this->never())
            ->method('areAgreementsEnabled');

        $this->agreementsResolverMock->expects($this->never())
            ->method('getAgreementsForReviews');

        $this->assertSame($result, $this->processor->process($data));
    }

    /**
     * @return array
     */
    public function processNoStoreSpecifiedDataProvider()
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
                    ReviewInterface::ARE_AGREEMENTS_CONFIRMED => false,
                ],
                'result' => [
                    ReviewInterface::ID => 1,
                    ReviewInterface::ARE_AGREEMENTS_CONFIRMED => false,
                ],
            ],
        ];
    }

    /**
     * Test process method when agreements are disabled
     *
     * @param array $data
     * @param array $result
     * @dataProvider processAgreementsDisabledDataProvider
     */
    public function testProcessAgreementsDisabled($data, $result)
    {
        $storeId = 1;

        $this->agreementsCheckerMock->expects($this->any())
            ->method('areAgreementsEnabled')
            ->with($storeId)
            ->willReturn(false);

        $this->agreementsResolverMock->expects($this->never())
            ->method('getAgreementsForReviews');

        $this->assertSame($result, $this->processor->process($data));
    }

    /**
     * @return array
     */
    public function processAgreementsDisabledDataProvider()
    {
        $noStoreSpecifiedData = $this->processNoStoreSpecifiedDataProvider();
        return array_merge(
            $noStoreSpecifiedData,
            [
                [
                    'data' => [
                        ReviewInterface::ID => 1,
                        ReviewInterface::STORE_ID => 1,
                    ],
                    'result' => [
                        ReviewInterface::ID => 1,
                        ReviewInterface::STORE_ID => 1,
                    ],
                ],
                [
                    'data' => [
                        ReviewInterface::ID => 1,
                        ReviewInterface::ARE_AGREEMENTS_CONFIRMED => true,
                        ReviewInterface::STORE_ID => 1,
                    ],
                    'result' => [
                        ReviewInterface::ID => 1,
                        ReviewInterface::ARE_AGREEMENTS_CONFIRMED => true,
                        ReviewInterface::STORE_ID => 1,
                    ],
                ],
                [
                    'data' => [
                        ReviewInterface::ID => 1,
                        ReviewInterface::ARE_AGREEMENTS_CONFIRMED => false,
                        ReviewInterface::STORE_ID => 1,
                    ],
                    'result' => [
                        ReviewInterface::ID => 1,
                        ReviewInterface::ARE_AGREEMENTS_CONFIRMED => false,
                        ReviewInterface::STORE_ID => 1,
                    ],
                ],
            ]
        );
    }

    /**
     * Test process method when agreements are enabled, but no required agreements specified
     *
     * @param array $data
     * @param array $result
     * @dataProvider processAgreementsEnabledNoRequiredDataProvider
     */
    public function testProcessAgreementsEnabledNoRequired($data, $result)
    {
        $storeIdNoRequiredAgreements = 1;
        $storeIdWithRequiredAgreements = 2;

        $this->agreementsCheckerMock->expects($this->any())
            ->method('areAgreementsEnabled')
            ->willReturnMap(
                [
                    [
                        $storeIdNoRequiredAgreements,
                        true,
                    ],
                    [
                        $storeIdWithRequiredAgreements,
                        false,
                    ],
                ]
            );

        $this->agreementsResolverMock->expects($this->any())
            ->method('getAgreementsForReviews')
            ->willReturnMap(
                [
                    [
                        $storeIdNoRequiredAgreements,
                        true,
                        [],
                    ]
                ]
            );

        $this->assertSame($result, $this->processor->process($data));
    }

    /**
     * @return array
     */
    public function processAgreementsEnabledNoRequiredDataProvider()
    {
        $noStoreSpecifiedData = $this->processNoStoreSpecifiedDataProvider();
        return array_merge(
            $noStoreSpecifiedData,
            [
                [
                    'data' => [
                        ReviewInterface::ID => 1,
                        ReviewInterface::STORE_ID => 1,
                    ],
                    'result' => [
                        ReviewInterface::ID => 1,
                        ReviewInterface::STORE_ID => 1,
                        ReviewInterface::ARE_AGREEMENTS_CONFIRMED => true,
                    ],
                ],
                [
                    'data' => [
                        ReviewInterface::ID => 1,
                        ReviewInterface::ARE_AGREEMENTS_CONFIRMED => true,
                        ReviewInterface::STORE_ID => 1,
                    ],
                    'result' => [
                        ReviewInterface::ID => 1,
                        ReviewInterface::ARE_AGREEMENTS_CONFIRMED => true,
                        ReviewInterface::STORE_ID => 1,
                    ],
                ],
                [
                    'data' => [
                        ReviewInterface::ID => 1,
                        ReviewInterface::ARE_AGREEMENTS_CONFIRMED => false,
                        ReviewInterface::STORE_ID => 1,
                    ],
                    'result' => [
                        ReviewInterface::ID => 1,
                        ReviewInterface::ARE_AGREEMENTS_CONFIRMED => true,
                        ReviewInterface::STORE_ID => 1,
                    ],
                ],
            ]
        );
    }

    /**
     * Test process method when agreements are enabled and store is specified
     *
     * @param array $data
     * @param array $result
     * @param array $requiredAgreements
     * @dataProvider processAgreementsEnabledStoreSpecifiedDataProvider
     */
    public function testProcessAgreementsEnabledStoreSpecified($data, $result, $requiredAgreements)
    {
        $storeId = 1;

        $this->agreementsCheckerMock->expects($this->any())
            ->method('areAgreementsEnabled')
            ->with($storeId)
            ->willReturn(true);

        $this->agreementsResolverMock->expects($this->any())
            ->method('getAgreementsForReviews')
            ->with($storeId, true)
            ->willReturn($requiredAgreements);

        $this->assertSame($result, $this->processor->process($data));
    }

    /**
     * @return array
     */
    public function processAgreementsEnabledStoreSpecifiedDataProvider()
    {
        $firstRequiredAgreement = $this->createMock(AgreementInterface::class);
        $firstRequiredAgreement->expects($this->any())
            ->method('getAgreementId')
            ->willReturn(1);
        $secondRequiredAgreement = $this->createMock(AgreementInterface::class);
        $secondRequiredAgreement->expects($this->any())
            ->method('getAgreementId')
            ->willReturn(2);
        return [
            [
                'data' => [
                    ReviewInterface::ID => 1,
                    ReviewInterface::STORE_ID => 1,
                ],
                'result' => [
                    ReviewInterface::ID => 1,
                    ReviewInterface::STORE_ID => 1,
                    ReviewInterface::ARE_AGREEMENTS_CONFIRMED => false,
                ],
                'requiredAgreements' => [
                    $firstRequiredAgreement,
                    $secondRequiredAgreement
                ],
            ],
            [
                'data' => [
                    ReviewInterface::ID => 1,
                    ReviewInterface::STORE_ID => 1,
                    Agreements::AGREEMENTS_DATA_KEY => [],
                ],
                'result' => [
                    ReviewInterface::ID => 1,
                    ReviewInterface::STORE_ID => 1,
                    Agreements::AGREEMENTS_DATA_KEY => [],
                    ReviewInterface::ARE_AGREEMENTS_CONFIRMED => false,
                ],
                'requiredAgreements' => [
                    $firstRequiredAgreement,
                    $secondRequiredAgreement
                ],
            ],
            [
                'data' => [
                    ReviewInterface::ID => 1,
                    ReviewInterface::STORE_ID => 1,
                    Agreements::AGREEMENTS_DATA_KEY => [
                        1 => true,
                        2 => true,
                    ],
                ],
                'result' => [
                    ReviewInterface::ID => 1,
                    ReviewInterface::STORE_ID => 1,
                    Agreements::AGREEMENTS_DATA_KEY => [
                        1 => true,
                        2 => true,
                    ],
                    ReviewInterface::ARE_AGREEMENTS_CONFIRMED => true,
                ],
                'requiredAgreements' => [
                    $firstRequiredAgreement,
                    $secondRequiredAgreement
                ],
            ],
            [
                'data' => [
                    ReviewInterface::ID => 1,
                    ReviewInterface::STORE_ID => 1,
                    Agreements::AGREEMENTS_DATA_KEY => [
                        1 => true,
                    ],
                ],
                'result' => [
                    ReviewInterface::ID => 1,
                    ReviewInterface::STORE_ID => 1,
                    Agreements::AGREEMENTS_DATA_KEY => [
                        1 => true,
                    ],
                    ReviewInterface::ARE_AGREEMENTS_CONFIRMED => false,
                ],
                'requiredAgreements' => [
                    $firstRequiredAgreement,
                    $secondRequiredAgreement
                ],
            ],
            [
                'data' => [
                    ReviewInterface::ID => 1,
                    ReviewInterface::STORE_ID => 1,
                    Agreements::AGREEMENTS_DATA_KEY => [
                        2 => true,
                    ],
                ],
                'result' => [
                    ReviewInterface::ID => 1,
                    ReviewInterface::STORE_ID => 1,
                    Agreements::AGREEMENTS_DATA_KEY => [
                        2 => true,
                    ],
                    ReviewInterface::ARE_AGREEMENTS_CONFIRMED => false,
                ],
                'requiredAgreements' => [
                    $firstRequiredAgreement,
                    $secondRequiredAgreement
                ],
            ],
            [
                'data' => [
                    ReviewInterface::ID => 1,
                    ReviewInterface::STORE_ID => 1,
                    Agreements::AGREEMENTS_DATA_KEY => [
                        1 => true,
                        2 => false,
                    ],
                ],
                'result' => [
                    ReviewInterface::ID => 1,
                    ReviewInterface::STORE_ID => 1,
                    Agreements::AGREEMENTS_DATA_KEY => [
                        1 => true,
                        2 => false,
                    ],
                    ReviewInterface::ARE_AGREEMENTS_CONFIRMED => false,
                ],
                'requiredAgreements' => [
                    $firstRequiredAgreement,
                    $secondRequiredAgreement
                ],
            ],
            [
                'data' => [
                    ReviewInterface::ID => 1,
                    ReviewInterface::STORE_ID => 1,
                    Agreements::AGREEMENTS_DATA_KEY => [
                        1 => true,
                        2 => '',
                    ],
                ],
                'result' => [
                    ReviewInterface::ID => 1,
                    ReviewInterface::STORE_ID => 1,
                    Agreements::AGREEMENTS_DATA_KEY => [
                        1 => true,
                        2 => '',
                    ],
                    ReviewInterface::ARE_AGREEMENTS_CONFIRMED => false,
                ],
                'requiredAgreements' => [
                    $firstRequiredAgreement,
                    $secondRequiredAgreement
                ],
            ],
            [
                'data' => [
                    ReviewInterface::ID => 1,
                    ReviewInterface::STORE_ID => 1,
                    Agreements::AGREEMENTS_DATA_KEY => [
                        1 => true,
                        2 => null,
                    ],
                ],
                'result' => [
                    ReviewInterface::ID => 1,
                    ReviewInterface::STORE_ID => 1,
                    Agreements::AGREEMENTS_DATA_KEY => [
                        1 => true,
                        2 => null,
                    ],
                    ReviewInterface::ARE_AGREEMENTS_CONFIRMED => false,
                ],
                'requiredAgreements' => [
                    $firstRequiredAgreement,
                    $secondRequiredAgreement
                ],
            ],
            [
                'data' => [
                    ReviewInterface::ID => 1,
                    ReviewInterface::STORE_ID => 1,
                    Agreements::AGREEMENTS_DATA_KEY => [
                        1 => 'true',
                        2 => 'true',
                    ],
                ],
                'result' => [
                    ReviewInterface::ID => 1,
                    ReviewInterface::STORE_ID => 1,
                    Agreements::AGREEMENTS_DATA_KEY => [
                        1 => 'true',
                        2 => 'true',
                    ],
                    ReviewInterface::ARE_AGREEMENTS_CONFIRMED => true,
                ],
                'requiredAgreements' => [
                    $firstRequiredAgreement,
                    $secondRequiredAgreement
                ],
            ],
            [
                'data' => [
                    ReviewInterface::ID => 1,
                    ReviewInterface::STORE_ID => 1,
                    Agreements::AGREEMENTS_DATA_KEY => [
                        1 => 1,
                        2 => 1,
                    ],
                ],
                'result' => [
                    ReviewInterface::ID => 1,
                    ReviewInterface::STORE_ID => 1,
                    Agreements::AGREEMENTS_DATA_KEY => [
                        1 => 1,
                        2 => 1,
                    ],
                    ReviewInterface::ARE_AGREEMENTS_CONFIRMED => true,
                ],
                'requiredAgreements' => [
                    $firstRequiredAgreement,
                    $secondRequiredAgreement
                ],
            ],
            [
                'data' => [
                    ReviewInterface::ID => 1,
                    ReviewInterface::STORE_ID => 1,
                    Agreements::AGREEMENTS_DATA_KEY => [
                        1 => 0,
                        2 => 0,
                    ],
                ],
                'result' => [
                    ReviewInterface::ID => 1,
                    ReviewInterface::STORE_ID => 1,
                    Agreements::AGREEMENTS_DATA_KEY => [
                        1 => 0,
                        2 => 0,
                    ],
                    ReviewInterface::ARE_AGREEMENTS_CONFIRMED => false,
                ],
                'requiredAgreements' => [
                    $firstRequiredAgreement,
                    $secondRequiredAgreement
                ],
            ],
        ];
    }
}
