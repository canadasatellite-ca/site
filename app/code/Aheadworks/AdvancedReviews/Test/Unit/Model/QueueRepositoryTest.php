<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Test\Unit\Model;

use Aheadworks\AdvancedReviews\Model\QueueRepository;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\TestCase;
use Aheadworks\AdvancedReviews\Model\QueueItem as QueueItemModel;
use Aheadworks\AdvancedReviews\Api\Data\QueueItemInterface;
use Aheadworks\AdvancedReviews\Api\Data\QueueItemInterfaceFactory;
use Aheadworks\AdvancedReviews\Api\Data\QueueItemSearchResultsInterface;
use Aheadworks\AdvancedReviews\Api\Data\QueueItemSearchResultsInterfaceFactory;
use Aheadworks\AdvancedReviews\Api\OrderHistoryRepositoryInterface;
use Aheadworks\AdvancedReviews\Model\Queue;
use Aheadworks\AdvancedReviews\Model\ResourceModel\Queue as QueueResource;
use Aheadworks\AdvancedReviews\Model\ResourceModel\Queue\Collection as QueueCollection;
use Aheadworks\AdvancedReviews\Model\ResourceModel\Queue\CollectionFactory as QueueCollectionFactory;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\DataObjectHelper;

/**
 * Class QueueRepositoryTest
 *
 * @package Aheadworks\AdvancedReviews\Test\Unit\Model
 */
class QueueRepositoryTest extends TestCase
{
    /**
     * @var QueueRepository
     */
    private $queueRepository;

    /**
     * @var QueueItemInterfaceFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $queueItemFactoryMock;

    /**
     * @var QueueResource|\PHPUnit_Framework_MockObject_MockObject
     */
    private $queueResourceMock;

    /**
     * @var QueueItemSearchResultsInterfaceFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $queueItemsSearchResultsFactoryMock;

    /**
     * @var QueueCollectionFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $queueCollectionFactoryMock;

    /**
     * @var JoinProcessorInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $extensionAttributesJoinProcessorMock;

    /**
     * @var CollectionProcessorInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $collectionProcessorMock;

    /**
     * @var DataObjectHelper|\PHPUnit_Framework_MockObject_MockObject
     */
    private $dataObjectHelperMock;

    /**
     * @var array
     */
    private $queueItemData = [
        QueueItemInterface::ID => 1,
    ];

    /**
     * Init mocks for tests
     *
     * @return void
     */
    protected function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->queueItemFactoryMock = $this->createPartialMock(
            QueueItemInterfaceFactory::class,
            [
                'create',
            ]
        );
        $this->queueResourceMock = $this->createPartialMock(
            QueueResource::class,
            [
                'save',
                'load',
                'delete',
            ]
        );
        $this->queueItemsSearchResultsFactoryMock = $this->createPartialMock(
            QueueItemSearchResultsInterfaceFactory::class,
            [
                'create'
            ]
        );
        $this->queueCollectionFactoryMock = $this->createPartialMock(
            QueueCollectionFactory::class,
            [
                'create'
            ]
        );

        $this->extensionAttributesJoinProcessorMock = $this->getMockForAbstractClass(
            JoinProcessorInterface::class
        );
        $this->collectionProcessorMock = $this->getMockForAbstractClass(
            CollectionProcessorInterface::class
        );
        $this->dataObjectHelperMock = $this->createPartialMock(
            DataObjectHelper::class,
            [
                'populateWithArray'
            ]
        );

        $this->queueRepository = $objectManager->getObject(
            QueueRepository::class,
            [
                'queueItemFactory' => $this->queueItemFactoryMock,
                'queueResource' => $this->queueResourceMock,
                'queueItemsSearchResultsFactory' => $this->queueItemsSearchResultsFactoryMock,
                'queueCollectionFactory' => $this->queueCollectionFactoryMock,
                'extensionAttributesJoinProcessor' => $this->extensionAttributesJoinProcessorMock,
                'collectionProcessor' => $this->collectionProcessorMock,
                'dataObjectHelper' => $this->dataObjectHelperMock,
            ]
        );
    }

    /**
     * Testing of save method
     */
    public function testSave()
    {
        /** @var QueueItemInterface|\PHPUnit_Framework_MockObject_MockObject $queueItemMock */
        $queueItemMock = $this->createPartialMock(
            QueueItemModel::class,
            [
                'getId',
            ]
        );

        $queueItemMock->expects($this->once())
            ->method('getId')
            ->willReturn($this->queueItemData[QueueItemInterface::ID]);
        $this->queueResourceMock->expects($this->once())
            ->method('save')
            ->with($queueItemMock)
            ->willReturnSelf();

        $this->assertSame($queueItemMock, $this->queueRepository->save($queueItemMock));
    }

    /**
     * Testing of save method on exception
     *
     * @expectedException \Magento\Framework\Exception\CouldNotSaveException
     * @expectedExceptionMessage Test message
     */
    public function testSaveWithException()
    {
        $exception = new \Exception('Test message');
        /** @var QueueItemInterface|\PHPUnit_Framework_MockObject_MockObject $queueItemMock */
        $queueItemMock = $this->createPartialMock(
            QueueItemModel::class,
            [
                'getId',
            ]
        );

        $queueItemMock->expects($this->never())
            ->method('getId');
        $this->queueResourceMock->expects($this->once())
            ->method('save')
            ->with($queueItemMock)
            ->willThrowException($exception);

        $this->queueRepository->save($queueItemMock);
    }

    /**
     * Testing of delete method
     */
    public function testDelete()
    {
        /** @var QueueItemInterface|\PHPUnit_Framework_MockObject_MockObject $queueItemMock */
        $queueItemMock = $this->createPartialMock(
            QueueItemModel::class,
            [
                'getId',
            ]
        );

        $queueItemMock->expects($this->once())
            ->method('getId')
            ->willReturn($this->queueItemData[QueueItemInterface::ID]);
        $this->queueResourceMock->expects($this->once())
            ->method('delete')
            ->with($queueItemMock)
            ->willReturnSelf();

        $this->assertTrue($this->queueRepository->delete($queueItemMock));
    }

    /**
     * Testing of delete method on exception
     *
     * @expectedException \Magento\Framework\Exception\CouldNotDeleteException
     * @expectedExceptionMessage Test message
     */
    public function testDeleteWithException()
    {
        $exception = new \Exception('Test message');
        /** @var QueueItemInterface|\PHPUnit_Framework_MockObject_MockObject $queueItemMock */
        $queueItemMock = $this->createPartialMock(
            QueueItemModel::class,
            [
                'getId',
            ]
        );

        $queueItemMock->expects($this->never())
            ->method('getId');
        $this->queueResourceMock->expects($this->once())
            ->method('delete')
            ->with($queueItemMock)
            ->willThrowException($exception);

        $this->queueRepository->delete($queueItemMock);
    }

    /**
     * Testing of getById method
     */
    public function testGetById()
    {
        $queueItemId = 1;

        /** @var QueueItemInterface|\PHPUnit_Framework_MockObject_MockObject $queueItemMock */
        $queueItemMock = $this->createPartialMock(
            QueueItemModel::class,
            [
                'getId',
            ]
        );
        $this->queueItemFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($queueItemMock);

        $this->queueResourceMock->expects($this->once())
            ->method('load')
            ->with($queueItemMock, $queueItemId)
            ->willReturnSelf();
        $queueItemMock->expects($this->once())
            ->method('getId')
            ->willReturn($queueItemId);

        $this->assertSame($queueItemMock, $this->queueRepository->getById($queueItemId));
    }

    /**
     * Testing of getById method on exception
     *
     * @expectedException \Magento\Framework\Exception\NoSuchEntityException
     * @expectedExceptionMessage No such entity with id = 1
     */
    public function testGetByIdOnException()
    {
        $queueItemId = 1;

        /** @var QueueItemInterface|\PHPUnit_Framework_MockObject_MockObject $queueItemMock */
        $queueItemMock = $this->createPartialMock(
            QueueItemModel::class,
            [
                'getId',
            ]
        );
        $this->queueItemFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($queueItemMock);

        $this->queueResourceMock->expects($this->once())
            ->method('load')
            ->with($queueItemMock, $queueItemId)
            ->willReturnSelf();
        $queueItemMock->expects($this->once())
            ->method('getId')
            ->willReturn(null);

        $this->assertSame($queueItemMock, $this->queueRepository->getById($queueItemId));
    }

    /**
     * Testing of deleteById method
     */
    public function testDeleteById()
    {
        $queueItemId = 1;

        /** @var QueueItemInterface|\PHPUnit_Framework_MockObject_MockObject $queueItemMock */
        $queueItemMock = $this->createPartialMock(
            QueueItemModel::class,
            [
                'getId',
            ]
        );
        $this->queueItemFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($queueItemMock);

        $this->queueResourceMock->expects($this->once())
            ->method('load')
            ->with($queueItemMock, $queueItemId)
            ->willReturnSelf();
        $queueItemMock->expects($this->exactly(2))
            ->method('getId')
            ->willReturn($queueItemId);

        $this->queueResourceMock->expects($this->once())
            ->method('delete')
            ->with($queueItemMock)
            ->willReturnSelf();

        $this->assertTrue($this->queueRepository->deleteById($queueItemId));
    }

    /**
     * Testing of deleteById method on NoSuchEntityException
     *
     * @expectedException \Magento\Framework\Exception\NoSuchEntityException
     * @expectedExceptionMessage No such entity with id = 1
     */
    public function testDeleteByIdOnNoSuchEntityException()
    {
        $queueItemId = 1;

        /** @var QueueItemInterface|\PHPUnit_Framework_MockObject_MockObject $queueItemMock */
        $queueItemMock = $this->createPartialMock(
            QueueItemModel::class,
            [
                'getId',
            ]
        );
        $this->queueItemFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($queueItemMock);

        $this->queueResourceMock->expects($this->once())
            ->method('load')
            ->with($queueItemMock, $queueItemId)
            ->willReturnSelf();
        $queueItemMock->expects($this->once())
            ->method('getId')
            ->willReturn(null);

        $this->queueResourceMock->expects($this->never())
            ->method('delete')
            ->with($queueItemMock);

        $this->queueRepository->deleteById($queueItemId);
    }

    /**
     * Testing of deleteById method on CouldNotDeleteException
     *
     * @expectedException \Magento\Framework\Exception\CouldNotDeleteException
     * @expectedExceptionMessage Test message
     */
    public function testDeleteByIdOnCouldNotDeleteException()
    {
        $queueItemId = 1;
        $exception = new \Exception('Test message');

        /** @var QueueItemInterface|\PHPUnit_Framework_MockObject_MockObject $queueItemMock */
        $queueItemMock = $this->createPartialMock(
            QueueItemModel::class,
            [
                'getId',
            ]
        );
        $this->queueItemFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($queueItemMock);

        $this->queueResourceMock->expects($this->once())
            ->method('load')
            ->with($queueItemMock, $queueItemId)
            ->willReturnSelf();
        $queueItemMock->expects($this->once())
            ->method('getId')
            ->willReturn($queueItemId);

        $this->queueResourceMock->expects($this->once())
            ->method('delete')
            ->with($queueItemMock)
            ->willThrowException($exception);

        $this->queueRepository->deleteById($queueItemId);
    }

    /**
     * Testing of getList method
     */
    public function testGetList()
    {
        /** @var QueueItemModel|\PHPUnit_Framework_MockObject_MockObject $reviewModelMock */
        $queueItemModelMock = $this->createPartialMock(
            QueueItemModel::class,
            [
                'getData'
            ]
        );
        $queueItemModelMock->expects($this->once())
            ->method('getData')
            ->willReturn($this->queueItemData);
        /** @var QueueItemInterface|\PHPUnit_Framework_MockObject_MockObject $queueItemMock */
        $queueItemMock = $this->getMockForAbstractClass(QueueItemInterface::class);
        $searchResultItems = [
            $queueItemMock
        ];
        $collectionItems = [
            $queueItemModelMock
        ];
        $collectionSize = count($collectionItems);

        /** @var QueueCollection|\PHPUnit_Framework_MockObject_MockObject $queueCollectionMock */
        $queueCollectionMock = $this->createPartialMock(
            QueueCollection::class,
            [
                'getSize',
                'getItems'
            ]
        );
        $queueCollectionMock->expects($this->once())
            ->method('getSize')
            ->willReturn($collectionSize);
        $queueCollectionMock->expects($this->once())
            ->method('getItems')
            ->willReturn($collectionItems);

        /** @var SearchCriteriaInterface|\PHPUnit_Framework_MockObject_MockObject $searchCriteriaMock */
        $searchCriteriaMock = $this->getMockForAbstractClass(
            SearchCriteriaInterface::class
        );
        $searchResultsMock = $this->getMockForAbstractClass(
            QueueItemSearchResultsInterface::class
        );

        $this->queueCollectionFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($queueCollectionMock);

        $this->extensionAttributesJoinProcessorMock->expects($this->once())
            ->method('process')
            ->with($queueCollectionMock, QueueItemInterface::class);
        $this->collectionProcessorMock->expects($this->once())
            ->method('process')
            ->with($searchCriteriaMock, $queueCollectionMock);

        $this->queueItemsSearchResultsFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($searchResultsMock);
        $searchResultsMock->expects($this->once())
            ->method('setSearchCriteria')
            ->with($searchCriteriaMock);
        $searchResultsMock->expects($this->once())
            ->method('setTotalCount')
            ->with($collectionSize);

        $this->queueItemFactoryMock->expects($this->exactly($collectionSize))
            ->method('create')
            ->willReturn($queueItemMock);
        $this->dataObjectHelperMock->expects($this->exactly($collectionSize))
            ->method('populateWithArray')
            ->with($queueItemMock, $this->queueItemData, QueueItemInterface::class);

        $searchResultsMock->expects($this->once())
            ->method('setItems')
            ->with($searchResultItems)
            ->willReturnSelf();

        $this->assertSame($searchResultsMock, $this->queueRepository->getList($searchCriteriaMock));
    }
}
