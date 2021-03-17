<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Test\Unit\Model;

use Aheadworks\AdvancedReviews\Model\StatisticsRepository;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\TestCase;
use Aheadworks\AdvancedReviews\Model\ResourceModel\Statistics as StatisticsResourceModel;
use Aheadworks\AdvancedReviews\Model\Statistics as StatisticsModel;
use Aheadworks\AdvancedReviews\Api\Data\StatisticsInterfaceFactory;

/**
 * Class StatisticsRepositoryTest
 *
 * @package Aheadworks\AdvancedReviews\Test\Unit\Model
 */
class StatisticsRepositoryTest extends TestCase
{
    /**
     * @var StatisticsRepository
     */
    private $statisticsRepository;

    /**
     * @var StatisticsResourceModel|\PHPUnit_Framework_MockObject_MockObject
     */
    private $resourceMock;

    /**
     * @var StatisticsInterfaceFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $statisticsInterfaceFactoryMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    protected function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->resourceMock = $this->createPartialMock(
            StatisticsResourceModel::class,
            [
                'load',
            ]
        );
        $this->statisticsInterfaceFactoryMock = $this->createPartialMock(
            StatisticsInterfaceFactory::class,
            [
                'create'
            ]
        );

        $this->statisticsRepository = $objectManager->getObject(
            StatisticsRepository::class,
            [
                'resource' => $this->resourceMock,
                'statisticsInterfaceFactory' => $this->statisticsInterfaceFactoryMock,
            ]
        );
    }

    /**
     * Testing of getByProductId method
     *
     * @param int $productId
     * @param int|null $storeId
     * @dataProvider testGetByProductIdDataProvider
     */
    public function testGetByProductId($productId, $storeId)
    {
        /** @var StatisticsModel|\PHPUnit_Framework_MockObject_MockObject $statisticsInstanceMock */
        $statisticsInstanceMock = $this->createPartialMock(
            StatisticsModel::class,
            []
        );
        $this->statisticsInterfaceFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($statisticsInstanceMock);
        $this->resourceMock->expects($this->once())
            ->method('load')
            ->with($statisticsInstanceMock, $productId, $storeId)
            ->willReturnSelf();

        $this->assertSame($statisticsInstanceMock, $this->statisticsRepository->getByProductId($productId, $storeId));
    }

    /**
     * Data provider for getByProductId
     *
     * @return array
     */
    public function testGetByProductIdDataProvider()
    {
        return [
            [
                1,
                null
            ],
            [
                1,
                1
            ],
            [
                2,
                3
            ],
        ];
    }
}
