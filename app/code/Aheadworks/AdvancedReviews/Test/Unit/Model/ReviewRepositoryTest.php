<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Test\Unit\Model;

use Aheadworks\AdvancedReviews\Api\Data\ReviewInterface;
use Aheadworks\AdvancedReviews\Api\Data\ReviewInterfaceFactory;
use Aheadworks\AdvancedReviews\Api\Data\ReviewSearchResultsInterface;
use Aheadworks\AdvancedReviews\Api\Data\ReviewSearchResultsInterfaceFactory;
use Aheadworks\AdvancedReviews\Model\Review as ReviewModel;
use Aheadworks\AdvancedReviews\Model\ResourceModel\Review as ReviewResourceModel;
use Aheadworks\AdvancedReviews\Model\ResourceModel\Review\Collection as ReviewCollection;
use Aheadworks\AdvancedReviews\Model\ResourceModel\Review\CollectionFactory as ReviewCollectionFactory;
use Aheadworks\AdvancedReviews\Model\Indexer\Statistics\Processor;
use Aheadworks\AdvancedReviews\Model\Review;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;
use Aheadworks\AdvancedReviews\Model\ReviewRepository;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\TestCase;

/**
 * Class ReviewRepositoryTest
 *
 * @package Aheadworks\AdvancedReviews\Test\Unit\Model
 */
class ReviewRepositoryTest extends TestCase
{
    /**
     * @var ReviewResourceModel|\PHPUnit_Framework_MockObject_MockObject
     */
    private $resourceMock;

    /**
     * @var ReviewInterfaceFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $reviewInterfaceFactoryMock;

    /**
     * @var ReviewCollectionFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $reviewCollectionFactoryMock;

    /**
     * @var ReviewSearchResultsInterfaceFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $searchResultsFactoryMock;

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
     * @var ReviewRepository
     */
    private $reviewRepository;

    /**
     * @var Processor|\PHPUnit_Framework_MockObject_MockObject
     */
    private $indexProcessorMock;

    /**
     * @var array
     */
    private $reviewData = [
        ReviewInterface::ID => 1,
        ReviewInterface::PRODUCT_ID => 10,
    ];

    /**
     * Init mocks for tests
     *
     * @return void
     */
    protected function setUp()
    {
        $objectManager = new ObjectManager($this);
        $this->resourceMock = $this->createPartialMock(
            ReviewResourceModel::class,
            [
                'save',
                'load',
                'delete'
            ]
        );
        $this->reviewInterfaceFactoryMock = $this->createPartialMock(
            ReviewInterfaceFactory::class,
            [
                'create'
            ]
        );
        $this->reviewCollectionFactoryMock = $this->createPartialMock(
            ReviewCollectionFactory::class,
            [
                'create'
            ]
        );
        $this->searchResultsFactoryMock = $this->createPartialMock(
            ReviewSearchResultsInterfaceFactory::class,
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
        $this->indexProcessorMock = $this->createPartialMock(
            Processor::class,
            [
                'reindexRow'
            ]
        );
        $this->reviewRepository = $objectManager->getObject(
            ReviewRepository::class,
            [
                'resource' => $this->resourceMock,
                'reviewInterfaceFactory' => $this->reviewInterfaceFactoryMock,
                'reviewCollectionFactory' => $this->reviewCollectionFactoryMock,
                'searchResultsFactory' => $this->searchResultsFactoryMock,
                'extensionAttributesJoinProcessor' => $this->extensionAttributesJoinProcessorMock,
                'collectionProcessor' => $this->collectionProcessorMock,
                'dataObjectHelper' => $this->dataObjectHelperMock,
                'indexProcessor' => $this->indexProcessorMock
            ]
        );
    }

    /**
     * Testing of save method
     */
    public function testSave()
    {
        /** @var ReviewInterface|\PHPUnit_Framework_MockObject_MockObject $reviewMock */
        $reviewMock = $this->createPartialMock(
            ReviewModel::class,
            [
                'getId',
                'getProductId'
            ]
        );

        $reviewMock->expects($this->once())
            ->method('getId')
            ->willReturn($this->reviewData[ReviewInterface::ID]);
        $reviewMock->expects($this->once())
            ->method('getProductId')
            ->willReturn($this->reviewData[ReviewInterface::PRODUCT_ID]);
        $this->resourceMock->expects($this->once())
            ->method('save')
            ->willReturnSelf();
        $this->indexProcessorMock->expects($this->once())
            ->method('reindexRow')
            ->with($this->reviewData[ReviewInterface::PRODUCT_ID]);

        $this->assertSame($reviewMock, $this->reviewRepository->save($reviewMock));
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
        /** @var ReviewInterface|\PHPUnit_Framework_MockObject_MockObject $reviewMock */
        $reviewMock = $this->createPartialMock(
            ReviewModel::class,
            [
                'getId',
                'getProductId'
            ]
        );

        $reviewMock->expects($this->never())
            ->method('getId');
        $reviewMock->expects($this->never())
            ->method('getProductId');
        $this->resourceMock->expects($this->once())
            ->method('save')
            ->with($reviewMock)
            ->willThrowException($exception);
        $this->indexProcessorMock->expects($this->never())
            ->method('reindexRow');

        $this->reviewRepository->save($reviewMock);
    }

    /**
     * Testing of delete method
     */
    public function testDelete()
    {
        /** @var ReviewInterface|\PHPUnit_Framework_MockObject_MockObject $reviewMock */
        $reviewMock = $this->createPartialMock(
            ReviewModel::class,
            [
                'getId',
                'getProductId'
            ]
        );

        $reviewMock->expects($this->once())
            ->method('getId')
            ->willReturn($this->reviewData[ReviewInterface::ID]);
        $reviewMock->expects($this->once())
            ->method('getProductId')
            ->willReturn($this->reviewData[ReviewInterface::PRODUCT_ID]);
        $this->resourceMock->expects($this->once())
            ->method('delete')
            ->with($reviewMock)
            ->willReturnSelf();
        $this->indexProcessorMock->expects($this->once())
            ->method('reindexRow')
            ->with($this->reviewData[ReviewInterface::PRODUCT_ID]);

        $this->assertTrue($this->reviewRepository->delete($reviewMock));
    }

    /**
     * Testing of delete method
     *
     * @expectedException \Magento\Framework\Exception\CouldNotDeleteException
     * @expectedExceptionMessage Test message
     */
    public function testDeleteWithException()
    {
        $exception = new \Exception('Test message');
        /** @var ReviewInterface|\PHPUnit_Framework_MockObject_MockObject $reviewMock */
        $reviewMock = $this->createPartialMock(
            ReviewModel::class,
            [
                'getId',
                'getProductId'
            ]
        );

        $reviewMock->expects($this->never())
            ->method('getId');
        $reviewMock->expects($this->once())
            ->method('getProductId')
            ->willReturn($this->reviewData[ReviewInterface::PRODUCT_ID]);
        $this->resourceMock->expects($this->once())
            ->method('delete')
            ->with($reviewMock)
            ->willThrowException($exception);
        $this->indexProcessorMock->expects($this->never())
            ->method('reindexRow');

        $this->reviewRepository->delete($reviewMock);
    }

    /**
     * Testing of getById method
     */
    public function testGetById()
    {
        $reviewId = 1;

        /** @var ReviewInterface|\PHPUnit_Framework_MockObject_MockObject $reviewMock */
        $reviewMock = $this->createPartialMock(
            ReviewModel::class,
            [
                'getId',
            ]
        );
        $this->reviewInterfaceFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($reviewMock);

        $this->resourceMock->expects($this->once())
            ->method('load')
            ->with($reviewMock, $reviewId)
            ->willReturnSelf();
        $reviewMock->expects($this->once())
            ->method('getId')
            ->willReturn($reviewId);

        $this->assertSame($reviewMock, $this->reviewRepository->getById($reviewId));
    }

    /**
     * Testing of getById method on exception
     *
     * @expectedException \Magento\Framework\Exception\NoSuchEntityException
     * @expectedExceptionMessage No such entity with id = 1
     */
    public function testGetByIdOnException()
    {
        $reviewId = 1;

        /** @var ReviewInterface|\PHPUnit_Framework_MockObject_MockObject $reviewMock */
        $reviewMock = $this->createPartialMock(
            ReviewModel::class,
            [
                'getId',
            ]
        );
        $this->reviewInterfaceFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($reviewMock);

        $this->resourceMock->expects($this->once())
            ->method('load')
            ->with($reviewMock, $reviewId)
            ->willReturnSelf();
        $reviewMock->expects($this->once())
            ->method('getId')
            ->willReturn(null);

        $this->assertSame($reviewMock, $this->reviewRepository->getById($reviewId));
    }

    /**
     * Testing of getList method
     */
    public function testGetList()
    {
        /** @var ReviewModel|\PHPUnit_Framework_MockObject_MockObject $reviewModelMock */
        $reviewModelMock = $this->createPartialMock(
            ReviewModel::class,
            [
                'getData'
            ]
        );
        $reviewModelMock->expects($this->once())
            ->method('getData')
            ->willReturn($this->reviewData);
        /** @var ReviewInterface|\PHPUnit_Framework_MockObject_MockObject $reviewMock */
        $reviewMock = $this->getMockForAbstractClass(ReviewInterface::class);
        $searchResultItems = [
            $reviewMock
        ];
        $collectionItems = [
            $reviewModelMock
        ];
        $collectionSize = count($collectionItems);

        /** @var ReviewCollection|\PHPUnit_Framework_MockObject_MockObject $ticketCollectionMock */
        $reviewCollectionMock = $this->createPartialMock(
            ReviewCollection::class,
            [
                'getSize',
                'getItems'
            ]
        );
        $reviewCollectionMock->expects($this->once())
            ->method('getSize')
            ->willReturn($collectionSize);
        $reviewCollectionMock->expects($this->once())
            ->method('getItems')
            ->willReturn($collectionItems);

        /** @var SearchCriteriaInterface|\PHPUnit_Framework_MockObject_MockObject $searchCriteriaMock */
        $searchCriteriaMock = $this->getMockForAbstractClass(
            SearchCriteriaInterface::class
        );
        $searchResultsMock = $this->getMockForAbstractClass(
            ReviewSearchResultsInterface::class
        );

        $this->reviewCollectionFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($reviewCollectionMock);

        $this->extensionAttributesJoinProcessorMock->expects($this->once())
            ->method('process')
            ->with($reviewCollectionMock, ReviewInterface::class);
        $this->collectionProcessorMock->expects($this->once())
            ->method('process')
            ->with($searchCriteriaMock, $reviewCollectionMock);

        $this->searchResultsFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($searchResultsMock);
        $searchResultsMock->expects($this->once())
            ->method('setSearchCriteria')
            ->with($searchCriteriaMock);
        $searchResultsMock->expects($this->once())
            ->method('setTotalCount')
            ->with($collectionSize);

        $this->reviewInterfaceFactoryMock->expects($this->exactly($collectionSize))
            ->method('create')
            ->willReturn($reviewMock);
        $this->dataObjectHelperMock->expects($this->exactly($collectionSize))
            ->method('populateWithArray')
            ->with($reviewMock, $this->reviewData, ReviewInterface::class);

        $searchResultsMock->expects($this->once())
            ->method('setItems')
            ->with($searchResultItems)
            ->willReturnSelf();

        $this->assertSame($searchResultsMock, $this->reviewRepository->getList($searchCriteriaMock));
    }
}
