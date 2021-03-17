<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */


namespace Aheadworks\AdvancedReviews\Test\Unit\Model\Store;

use Aheadworks\AdvancedReviews\Model\Store\Resolver;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Api\Data\StoreInterface;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Test for \Aheadworks\AdvancedReviews\Model\Store\Resolver
 */
class ResolverTest extends TestCase
{
    /**
     * @var Resolver
     */
    private $model;

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

        $this->storeManagerMock = $this->createMock(StoreManagerInterface::class);

        $this->model = $objectManager->getObject(
            Resolver::class,
            [
                'storeManager' => $this->storeManagerMock,
            ]
        );
    }

    /**
     * Test getWebsiteIdByStoreId method
     */
    public function testGetWebsiteIdByStoreId()
    {
        $websiteId = 2;
        $storeId = 3;

        $storeMock = $this->createMock(StoreInterface::class);
        $this->storeManagerMock->expects($this->once())
            ->method('getStore')
            ->with($storeId)
            ->willReturn($storeMock);

        $storeMock->expects($this->once())
            ->method('getWebsiteId')
            ->willReturn($websiteId);

        $this->assertEquals($websiteId, $this->model->getWebsiteIdByStoreId($storeId));
    }

    /**
     * Test getWebsiteIdByStoreId method if no store found
     */
    public function testGetWebsiteIdByStoreIdNoStore()
    {
        $storeId = 3;

        $this->storeManagerMock->expects($this->once())
            ->method('getStore')
            ->with($storeId)
            ->willThrowException(new NoSuchEntityException(__('No such entity!')));

        $this->assertNull($this->model->getWebsiteIdByStoreId($storeId));
    }
}
