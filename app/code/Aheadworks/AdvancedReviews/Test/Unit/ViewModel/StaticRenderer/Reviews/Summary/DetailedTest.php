<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Test\Unit\ViewModel\StaticRenderer\Reviews\Summary;

use Aheadworks\AdvancedReviews\ViewModel\StaticRenderer\Reviews\Summary\Detailed;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Aheadworks\AdvancedReviews\Model\Product\Layout\Processor\Review\DetailedSummaryDataProvider;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Api\Data\StoreInterface;

/**
 * Test for \Aheadworks\AdvancedReviews\ViewModel\StaticRenderer\Reviews\Summary\Detailed
 */
class DetailedTest extends TestCase
{
    /**
     * @var Detailed
     */
    private $viewModel;

    /**
     * @var DetailedSummaryDataProvider|\PHPUnit_Framework_MockObject_MockObject
     */
    private $detailedSummaryDataProviderMock;

    /**
     * @var StoreManagerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $storeManagerMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->detailedSummaryDataProviderMock = $this->createMock(DetailedSummaryDataProvider::class);
        $this->storeManagerMock = $this->createMock(StoreManagerInterface::class);

        $this->viewModel = $objectManager->getObject(
            Detailed::class,
            [
                'detailedSummaryDataProvider' => $this->detailedSummaryDataProviderMock,
                'storeManager' => $this->storeManagerMock,
            ]
        );
    }

    /**
     * Test for getDetailedSummaryData method
     */
    public function testGetDetailedSummaryData()
    {
        $currentStoreId = 1;
        $detailedSummaryData = [];

        $storeMock = $this->createMock(StoreInterface::class);
        $storeMock->expects($this->once())
            ->method('getId')
            ->willReturn($currentStoreId);
        $this->storeManagerMock->expects($this->once())
            ->method('getStore')
            ->with(true)
            ->willReturn($storeMock);

        $this->detailedSummaryDataProviderMock->expects($this->once())
            ->method('getDetailedSummaryData')
            ->with(null, $currentStoreId)
            ->willReturn($detailedSummaryData);

        $this->assertTrue(is_array($this->viewModel->getDetailedSummaryData()));
    }

    /**
     * Test for getDetailedSummaryData method when no store detected
     */
    public function testGetDetailedSummaryDataNoStore()
    {
        $detailedSummaryData = [];
        $currentStoreId = null;

        $this->storeManagerMock->expects($this->once())
            ->method('getStore')
            ->with(true)
            ->willThrowException(new NoSuchEntityException());

        $this->detailedSummaryDataProviderMock->expects($this->once())
            ->method('getDetailedSummaryData')
            ->with(null, $currentStoreId)
            ->willReturn($detailedSummaryData);

        $this->assertTrue(is_array($this->viewModel->getDetailedSummaryData()));
    }

    /**
     * Test for getRatingLabel method
     *
     * @param array $detailedSummaryDataRow
     * @param string $result
     * @dataProvider getRatingLabelDataProvider
     */
    public function testGetRatingLabel($detailedSummaryDataRow, $result)
    {
        $this->assertEquals($result, $this->viewModel->getRatingLabel($detailedSummaryDataRow));
    }

    /**
     * @return array
     */
    public function getRatingLabelDataProvider()
    {
        return [
            [
                'detailedSummaryDataRow' => [],
                'result' => '',
            ],
            [
                'detailedSummaryDataRow' => [
                    'label' => null,
                ],
                'result' => '',
            ],
            [
                'detailedSummaryDataRow' => [
                    'label' => '',
                ],
                'result' => '',
            ],
            [
                'detailedSummaryDataRow' => [
                    'label' => 'test label',
                ],
                'result' => 'test label',
            ],
        ];
    }

    /**
     * Test for getRatingReviewsCount method
     *
     * @param array $detailedSummaryDataRow
     * @param string $result
     * @dataProvider getRatingReviewsCountDataProvider
     */
    public function testGetRatingReviewsCount($detailedSummaryDataRow, $result)
    {
        $this->assertEquals($result, $this->viewModel->getRatingReviewsCount($detailedSummaryDataRow));
    }

    /**
     * @return array
     */
    public function getRatingReviewsCountDataProvider()
    {
        return [
            [
                'detailedSummaryDataRow' => [],
                'result' => '',
            ],
            [
                'detailedSummaryDataRow' => [
                    'reviews_count' => null,
                ],
                'result' => '',
            ],
            [
                'detailedSummaryDataRow' => [
                    'reviews_count' => '',
                ],
                'result' => '',
            ],
            [
                'detailedSummaryDataRow' => [
                    'reviews_count' => '45',
                ],
                'result' => '45',
            ],
        ];
    }

    /**
     * Test for getRatingReviewsPercent method
     *
     * @param array $detailedSummaryDataRow
     * @param string $result
     * @dataProvider getRatingReviewsPercentDataProvider
     */
    public function testGetRatingReviewsPercent($detailedSummaryDataRow, $result)
    {
        $this->assertEquals($result, $this->viewModel->getRatingReviewsPercent($detailedSummaryDataRow));
    }

    /**
     * @return array
     */
    public function getRatingReviewsPercentDataProvider()
    {
        return [
            [
                'detailedSummaryDataRow' => [],
                'result' => '',
            ],
            [
                'detailedSummaryDataRow' => [
                    'reviews_percent' => null,
                ],
                'result' => '',
            ],
            [
                'detailedSummaryDataRow' => [
                    'reviews_percent' => '',
                ],
                'result' => '',
            ],
            [
                'detailedSummaryDataRow' => [
                    'reviews_percent' => '18%',
                ],
                'result' => '18%',
            ],
        ];
    }
}
