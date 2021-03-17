<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Test\Unit\Model\Agreements;

use Aheadworks\AdvancedReviews\Model\Agreements\Checker;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\TestCase;
use Aheadworks\AdvancedReviews\Model\Config;
use Aheadworks\AdvancedReviews\Model\Source\Review\Agreements\DisplayMode as AgreementsDisplayMode;

/**
 * Test for \Aheadworks\AdvancedReviews\Model\Agreements\Checker
 */
class CheckerTest extends TestCase
{
    /**
     * @var Checker
     */
    private $model;

    /**
     * @var Config|\PHPUnit_Framework_MockObject_MockObject
     */
    private $configMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    protected function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->configMock = $this->createMock(Config::class);
        $this->configMock->expects($this->any())
            ->method('areAgreementsEnabled')
            ->willReturnMap(
                [
                    [
                        null,
                        true,
                    ],
                    [
                        1,
                        false,
                    ],
                    [
                        2,
                        true,
                    ],
                ]
            );

        $this->configMock->expects($this->any())
            ->method('getAgreementsDisplayMode')
            ->willReturnMap(
                [
                    [
                        null,
                        AgreementsDisplayMode::EVERYONE,
                    ],
                    [
                        1,
                        AgreementsDisplayMode::GUESTS_ONLY,
                    ],
                    [
                        2,
                        AgreementsDisplayMode::EVERYONE,
                    ],
                ]
            );

        $this->model = $objectManager->getObject(
            Checker::class,
            [
                'config' => $this->configMock,
            ]
        );
    }

    /**
     * Test for areAgreementsEnabled method
     *
     * @param int|null $storeId
     * @dataProvider areAgreementsEnabledDataProvider
     */
    public function testAreAgreementsEnabled($storeId)
    {
        $result = $this->model->areAgreementsEnabled($storeId);
        $this->assertTrue(is_bool($result));
    }

    /**
     * @return array
     */
    public function areAgreementsEnabledDataProvider()
    {
        return [
            [

                'storeId' => null,
            ],
            [
                'storeId' => 1,
            ],
            [
                'storeId' => 2,
            ],
        ];
    }

    /**
     * Test for isNeedToShowForGuests method
     *
     * @param int|null $storeId
     * @param bool $result
     * @dataProvider isNeedToShowForGuestsDataProvider
     */
    public function testIsNeedToShowForGuests($storeId, $result)
    {
        $this->assertEquals($result, $this->model->isNeedToShowForGuests($storeId));
    }

    /**
     * @return array
     */
    public function isNeedToShowForGuestsDataProvider()
    {
        return [
            [

                'storeId' => null,
                'result' => true,
            ],
            [
                'storeId' => 1,
                'result' => true,
            ],
            [
                'storeId' => 2,
                'result' => true
                ,
            ],
        ];
    }

    /**
     * Test for isNeedToShowForCustomers method
     *
     * @param int|null $storeId
     * @param bool $result
     * @dataProvider isNeedToShowForCustomersDataProvider
     */
    public function testIsNeedToShowForCustomers($storeId, $result)
    {
        $this->assertEquals($result, $this->model->isNeedToShowForCustomers($storeId));
    }

    /**
     * @return array
     */
    public function isNeedToShowForCustomersDataProvider()
    {
        return [
            [

                'storeId' => null,
                'result' => true,
            ],
            [
                'storeId' => 1,
                'result' => false,
            ],
            [
                'storeId' => 2,
                'result' => true
                ,
            ],
        ];
    }
}
