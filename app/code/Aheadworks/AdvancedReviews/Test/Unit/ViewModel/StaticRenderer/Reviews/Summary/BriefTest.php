<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Test\Unit\ViewModel\StaticRenderer\Reviews\Summary;

use Aheadworks\AdvancedReviews\ViewModel\StaticRenderer\Reviews\Summary\Brief;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Aheadworks\AdvancedReviews\Model\Product\Layout\Processor\Review\BriefSummaryDataProvider;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Api\Data\StoreInterface;

/**
 * Test for \Aheadworks\AdvancedReviews\ViewModel\StaticRenderer\Reviews\Summary\Brief
 */
class BriefTest extends TestCase
{
    /**
     * @var Brief
     */
    private $viewModel;

    /**
     * @var BriefSummaryDataProvider|\PHPUnit_Framework_MockObject_MockObject
     */
    private $briefSummaryDataProviderMock;

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

        $this->briefSummaryDataProviderMock = $this->createMock(BriefSummaryDataProvider::class);
        $this->storeManagerMock = $this->createMock(StoreManagerInterface::class);

        $this->viewModel = $objectManager->getObject(
            Brief::class,
            [
                'briefSummaryDataProvider' => $this->briefSummaryDataProviderMock,
                'storeManager' => $this->storeManagerMock,
            ]
        );
    }

    /**
     * Test for getAggregatedRatingAbsoluteValue method
     *
     * @param array $briefSummaryData
     * @param string $result
     * @dataProvider getAggregatedRatingAbsoluteValueDataProvider
     */
    public function testGetAggregatedRatingAbsoluteValue($briefSummaryData, $result)
    {
        $currentStoreId = 1;

        $storeMock = $this->createMock(StoreInterface::class);
        $storeMock->expects($this->once())
            ->method('getId')
            ->willReturn($currentStoreId);
        $this->storeManagerMock->expects($this->once())
            ->method('getStore')
            ->with(true)
            ->willReturn($storeMock);

        $this->briefSummaryDataProviderMock->expects($this->once())
            ->method('getBriefSummaryData')
            ->with(null, $currentStoreId)
            ->willReturn($briefSummaryData);

        $this->assertEquals($result, $this->viewModel->getAggregatedRatingAbsoluteValue());
    }

    /**
     * Test for getAggregatedRatingAbsoluteValue method when no store detected
     *
     * @param array $briefSummaryData
     * @param string $result
     * @dataProvider getAggregatedRatingAbsoluteValueDataProvider
     */
    public function testGetAggregatedRatingAbsoluteValueNoStore($briefSummaryData, $result)
    {
        $currentStoreId = null;

        $this->storeManagerMock->expects($this->once())
            ->method('getStore')
            ->with(true)
            ->willThrowException(new NoSuchEntityException());

        $this->briefSummaryDataProviderMock->expects($this->once())
            ->method('getBriefSummaryData')
            ->with(null, $currentStoreId)
            ->willReturn($briefSummaryData);

        $this->assertEquals($result, $this->viewModel->getAggregatedRatingAbsoluteValue());
    }

    /**
     * Test for getAggregatedRatingAbsoluteValue method when brief data is already fetched
     *
     * @param array $briefSummaryData
     * @param string $result
     * @dataProvider getAggregatedRatingAbsoluteValueDataProviderLoadedData
     */
    public function testGetAggregatedRatingAbsoluteValueFromLoadedData($briefSummaryData, $result)
    {
        $this->storeManagerMock->expects($this->never())
            ->method('getStore');

        $this->briefSummaryDataProviderMock->expects($this->never())
            ->method('getBriefSummaryData');

        $this->setProperty('briefSummaryData', $briefSummaryData);

        $this->assertEquals($result, $this->viewModel->getAggregatedRatingAbsoluteValue());
    }

    /**
     * @return array
     */
    public function getAggregatedRatingAbsoluteValueDataProvider()
    {
        $data = $this->getAggregatedRatingAbsoluteValueDataProviderLoadedData();
        $data[] = [
            'briefSummaryData' => [],
            'result' => '',
        ];
        return $data;
    }

    /**
     * @return array
     */
    public function getAggregatedRatingAbsoluteValueDataProviderLoadedData()
    {
        return [
            [
                'briefSummaryData' => [
                    'aggregated_rating_absolute' => null,
                ],
                'result' => '',
            ],
            [
                'briefSummaryData' => [
                    'aggregated_rating_absolute' => '',
                ],
                'result' => '',
            ],
            [
                'briefSummaryData' => [
                    'aggregated_rating_absolute' => '4.0',
                ],
                'result' => '4.0',
            ],
        ];
    }

    /**
     * Test for getReviewsCount method
     *
     * @param array $briefSummaryData
     * @param string $result
     * @dataProvider getReviewsCountDataProvider
     */
    public function testGetReviewsCount($briefSummaryData, $result)
    {
        $currentStoreId = 1;

        $storeMock = $this->createMock(StoreInterface::class);
        $storeMock->expects($this->once())
            ->method('getId')
            ->willReturn($currentStoreId);
        $this->storeManagerMock->expects($this->once())
            ->method('getStore')
            ->with(true)
            ->willReturn($storeMock);

        $this->briefSummaryDataProviderMock->expects($this->once())
            ->method('getBriefSummaryData')
            ->with(null, $currentStoreId)
            ->willReturn($briefSummaryData);

        $this->assertEquals($result, $this->viewModel->getReviewsCount());
    }

    /**
     * Test for getReviewsCount method when no store detected
     *
     * @param array $briefSummaryData
     * @param string $result
     * @dataProvider getReviewsCountDataProvider
     */
    public function testGetReviewsCountNoStore($briefSummaryData, $result)
    {
        $currentStoreId = null;

        $this->storeManagerMock->expects($this->once())
            ->method('getStore')
            ->with(true)
            ->willThrowException(new NoSuchEntityException());

        $this->briefSummaryDataProviderMock->expects($this->once())
            ->method('getBriefSummaryData')
            ->with(null, $currentStoreId)
            ->willReturn($briefSummaryData);

        $this->assertEquals($result, $this->viewModel->getReviewsCount());
    }

    /**
     * Test for getReviewsCount method when brief data is already fetched
     *
     * @param array $briefSummaryData
     * @param string $result
     * @dataProvider getReviewsCountDataProviderLoadedData
     */
    public function testGetReviewsCountFromLoadedData($briefSummaryData, $result)
    {
        $this->storeManagerMock->expects($this->never())
            ->method('getStore');

        $this->briefSummaryDataProviderMock->expects($this->never())
            ->method('getBriefSummaryData');

        $this->setProperty('briefSummaryData', $briefSummaryData);

        $this->assertEquals($result, $this->viewModel->getReviewsCount());
    }

    /**
     * @return array
     */
    public function getReviewsCountDataProvider()
    {
        $data = $this->getReviewsCountDataProviderLoadedData();
        $data[] = [
            'briefSummaryData' => [],
            'result' => '',
        ];
        return $data;
    }

    /**
     * @return array
     */
    public function getReviewsCountDataProviderLoadedData()
    {
        return [
            [
                'briefSummaryData' => [
                    'reviews_count' => null,
                ],
                'result' => '',
            ],
            [
                'briefSummaryData' => [
                    'reviews_count' => '',
                ],
                'result' => '',
            ],
            [
                'briefSummaryData' => [
                    'reviews_count' => '350',
                ],
                'result' => '350',
            ],
        ];
    }

    /**
     * Test for getAggregatedRatingPercentValue method
     *
     * @param array $briefSummaryData
     * @param string $result
     * @dataProvider getAggregatedRatingPercentValueDataProvider
     */
    public function testGetAggregatedRatingPercentValue($briefSummaryData, $result)
    {
        $currentStoreId = 1;

        $storeMock = $this->createMock(StoreInterface::class);
        $storeMock->expects($this->once())
            ->method('getId')
            ->willReturn($currentStoreId);
        $this->storeManagerMock->expects($this->once())
            ->method('getStore')
            ->with(true)
            ->willReturn($storeMock);

        $this->briefSummaryDataProviderMock->expects($this->once())
            ->method('getBriefSummaryData')
            ->with(null, $currentStoreId)
            ->willReturn($briefSummaryData);

        $this->assertEquals($result, $this->viewModel->getAggregatedRatingPercentValue());
    }

    /**
     * Test for getAggregatedRatingPercentValue method when no store detected
     *
     * @param array $briefSummaryData
     * @param string $result
     * @dataProvider getAggregatedRatingPercentValueDataProvider
     */
    public function testGetAggregatedRatingPercentValueNoStore($briefSummaryData, $result)
    {
        $currentStoreId = null;

        $this->storeManagerMock->expects($this->once())
            ->method('getStore')
            ->with(true)
            ->willThrowException(new NoSuchEntityException());

        $this->briefSummaryDataProviderMock->expects($this->once())
            ->method('getBriefSummaryData')
            ->with(null, $currentStoreId)
            ->willReturn($briefSummaryData);

        $this->assertEquals($result, $this->viewModel->getAggregatedRatingPercentValue());
    }

    /**
     * Test for getAggregatedRatingPercentValue method when brief data is already fetched
     *
     * @param array $briefSummaryData
     * @param string $result
     * @dataProvider getAggregatedRatingPercentValueLoadedData
     */
    public function testGetAggregatedRatingPercentValueFromLoadedData($briefSummaryData, $result)
    {
        $this->storeManagerMock->expects($this->never())
            ->method('getStore');

        $this->briefSummaryDataProviderMock->expects($this->never())
            ->method('getBriefSummaryData');

        $this->setProperty('briefSummaryData', $briefSummaryData);

        $this->assertEquals($result, $this->viewModel->getAggregatedRatingPercentValue());
    }

    /**
     * @return array
     */
    public function getAggregatedRatingPercentValueDataProvider()
    {
        $data = $this->getAggregatedRatingPercentValueLoadedData();
        $data[] = [
            'briefSummaryData' => [],
            'result' => '',
        ];
        return $data;
    }

    /**
     * @return array
     */
    public function getAggregatedRatingPercentValueLoadedData()
    {
        return [
            [
                'briefSummaryData' => [
                    'aggregated_rating_percent' => null,
                ],
                'result' => '',
            ],
            [
                'briefSummaryData' => [
                    'aggregated_rating_percent' => '',
                ],
                'result' => '',
            ],
            [
                'briefSummaryData' => [
                    'aggregated_rating_percent' => '80',
                ],
                'result' => '80',
            ],
        ];
    }

    /**
     * Test for getAggregatedRatingTitle method
     *
     * @param array $briefSummaryData
     * @param string $result
     * @dataProvider getAggregatedRatingTitleDataProvider
     */
    public function testGetAggregatedRatingTitle($briefSummaryData, $result)
    {
        $currentStoreId = 1;

        $storeMock = $this->createMock(StoreInterface::class);
        $storeMock->expects($this->once())
            ->method('getId')
            ->willReturn($currentStoreId);
        $this->storeManagerMock->expects($this->once())
            ->method('getStore')
            ->with(true)
            ->willReturn($storeMock);

        $this->briefSummaryDataProviderMock->expects($this->once())
            ->method('getBriefSummaryData')
            ->with(null, $currentStoreId)
            ->willReturn($briefSummaryData);

        $this->assertEquals($result, $this->viewModel->getAggregatedRatingTitle());
    }

    /**
     * Test for getAggregatedRatingPercentValue method when no store detected
     *
     * @param array $briefSummaryData
     * @param string $result
     * @dataProvider getAggregatedRatingTitleDataProvider
     */
    public function testGetAggregatedRatingTitleNoStore($briefSummaryData, $result)
    {
        $currentStoreId = null;

        $this->storeManagerMock->expects($this->once())
            ->method('getStore')
            ->with(true)
            ->willThrowException(new NoSuchEntityException());

        $this->briefSummaryDataProviderMock->expects($this->once())
            ->method('getBriefSummaryData')
            ->with(null, $currentStoreId)
            ->willReturn($briefSummaryData);

        $this->assertEquals($result, $this->viewModel->getAggregatedRatingTitle());
    }

    /**
     * Test for getAggregatedRatingTitle method when brief data is already fetched
     *
     * @param array $briefSummaryData
     * @param string $result
     * @dataProvider getAggregatedRatingTitleLoadedData
     */
    public function testGetAggregatedRatingTitleFromLoadedData($briefSummaryData, $result)
    {
        $this->storeManagerMock->expects($this->never())
            ->method('getStore');

        $this->briefSummaryDataProviderMock->expects($this->never())
            ->method('getBriefSummaryData');

        $this->setProperty('briefSummaryData', $briefSummaryData);

        $this->assertEquals($result, $this->viewModel->getAggregatedRatingTitle());
    }

    /**
     * @return array
     */
    public function getAggregatedRatingTitleDataProvider()
    {
        $data = $this->getAggregatedRatingTitleLoadedData();
        $data[] = [
            'briefSummaryData' => [
                'aggregated_rating_percent' => '80',
            ],
            'result' => '80%',
        ];
        return $data;
    }

    /**
     * @return array
     */
    public function getAggregatedRatingTitleLoadedData()
    {
        return [
            [
                'briefSummaryData' => [
                    'aggregated_rating_title' => null,
                    'aggregated_rating_percent' => '80',
                ],
                'result' => '80%',
            ],
            [
                'briefSummaryData' => [
                    'aggregated_rating_title' => '',
                    'aggregated_rating_percent' => '80',
                ],
                'result' => '',
            ],
            [
                'briefSummaryData' => [
                    'aggregated_rating_title' => '4 out of 5 stars',
                    'aggregated_rating_percent' => '80',
                ],
                'result' => '4 out of 5 stars',
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
        $class = new \ReflectionClass($this->viewModel);
        $property = $class->getProperty($propertyName);
        $property->setAccessible(true);
        $property->setValue($this->viewModel, $value);

        return $this;
    }
}
