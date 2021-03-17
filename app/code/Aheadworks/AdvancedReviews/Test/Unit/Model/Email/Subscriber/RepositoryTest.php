<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Test\Unit\Model\Email\Subscriber;

use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Aheadworks\AdvancedReviews\Model\Email\Subscriber\Repository;
use Aheadworks\AdvancedReviews\Api\Data\Email\SubscriberInterface;
use Aheadworks\AdvancedReviews\Api\Data\Email\SubscriberInterfaceFactory;
use Aheadworks\AdvancedReviews\Api\Data\Email\SubscriberSearchResultsInterface;
use Aheadworks\AdvancedReviews\Api\Data\Email\SubscriberSearchResultsInterfaceFactory;
use Aheadworks\AdvancedReviews\Model\Email\Subscriber;
use Aheadworks\AdvancedReviews\Model\ResourceModel\Email\Subscriber as SubscriberResourceModel;
use Aheadworks\AdvancedReviews\Model\ResourceModel\Email\Subscriber\Collection as SubscriberCollection;
use Aheadworks\AdvancedReviews\Model\ResourceModel\Email\Subscriber\CollectionFactory as SubscriberCollectionFactory;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\DataObjectHelper;

/**
 * Test for \Aheadworks\AdvancedReviews\Model\Email\Subscriber\Repository
 */
class RepositoryTest extends TestCase
{
    /**
     * @var Repository
     */
    private $repository;

    /**
     * @var SubscriberInterfaceFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $subscriberInterfaceFactoryMock;

    /**
     * @var SubscriberResourceModel|\PHPUnit_Framework_MockObject_MockObject
     */
    private $subscriberResourceModelMock;

    /**
     * @var SubscriberSearchResultsInterfaceFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $subscriberSearchResultsFactoryMock;

    /**
     * @var SubscriberCollectionFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $subscriberCollectionFactoryMock;

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
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->subscriberInterfaceFactoryMock = $this->createMock(SubscriberInterfaceFactory::class);
        $this->subscriberResourceModelMock = $this->createMock(SubscriberResourceModel::class);
        $this->subscriberSearchResultsFactoryMock = $this->createMock(SubscriberSearchResultsInterfaceFactory::class);
        $this->subscriberCollectionFactoryMock = $this->createMock(SubscriberCollectionFactory::class);
        $this->extensionAttributesJoinProcessorMock = $this->createMock(JoinProcessorInterface::class);
        $this->collectionProcessorMock = $this->createMock(CollectionProcessorInterface::class);
        $this->dataObjectHelperMock = $this->createMock(DataObjectHelper::class);

        $this->repository = $objectManager->getObject(
            Repository::class,
            [
                'subscriberInterfaceFactory' => $this->subscriberInterfaceFactoryMock,
                'subscriberResourceModel' => $this->subscriberResourceModelMock,
                'subscriberSearchResultsFactory' => $this->subscriberSearchResultsFactoryMock,
                'subscriberCollectionFactory' => $this->subscriberCollectionFactoryMock,
                'extensionAttributesJoinProcessor' => $this->extensionAttributesJoinProcessorMock,
                'collectionProcessor' => $this->collectionProcessorMock,
                'dataObjectHelper' => $this->dataObjectHelperMock,
            ]
        );
    }

    /**
     * Test save method
     */
    public function testSave()
    {
        $subscriberId = 10;
        /** @var Subscriber|\PHPUnit_Framework_MockObject_MockObject $subscriberMock */
        $subscriberToSaveMock = $this->createMock(Subscriber::class);
        $subscriberToSaveMock->expects($this->atLeastOnce())
            ->method('getId')
            ->willReturn($subscriberId);

        $this->subscriberResourceModelMock->expects($this->once())
            ->method('save')
            ->with($subscriberToSaveMock)
            ->willReturnSelf();

        $this->assertSame($subscriberToSaveMock, $this->repository->save($subscriberToSaveMock));
    }

    /**
     * Test save method if save error occurs
     *
     * @expectedException \Magento\Framework\Exception\CouldNotSaveException
     * @expectedExceptionMessage Error!
     */
    public function testSaveCouldNotSaveError()
    {
        /** @var Subscriber|\PHPUnit_Framework_MockObject_MockObject $subscriberMock */
        $subscriberToSaveMock = $this->createMock(Subscriber::class);

        $this->subscriberResourceModelMock->expects($this->once())
            ->method('save')
            ->with($subscriberToSaveMock)
            ->willThrowException(new \Exception('Error!'));

        $this->repository->save($subscriberToSaveMock);
    }

    /**
     * Test getById method
     */
    public function testGetById()
    {
        $subscriberId = 1;

        /** @var Subscriber|\PHPUnit_Framework_MockObject_MockObject $subscriberMock */
        $subscriberMock = $this->createMock(Subscriber::class);
        $subscriberMock->expects($this->once())
            ->method('getId')
            ->willReturn($subscriberId);

        $this->subscriberInterfaceFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($subscriberMock);

        $this->subscriberResourceModelMock->expects($this->once())
            ->method('load')
            ->with($subscriberMock, $subscriberId)
            ->willReturnSelf();

        $this->assertSame($subscriberMock, $this->repository->getById($subscriberId));
    }

    /**
     * Test getById method if no subscriber found
     *
     * @expectedException \Magento\Framework\Exception\NoSuchEntityException
     * @expectedExceptionMessage No such entity with id = 10
     */
    public function testGetByIdNoSubscriberFound()
    {
        $subscriberId = 10;

        /** @var Subscriber|\PHPUnit_Framework_MockObject_MockObject $subscriberMock */
        $subscriberMock = $this->createMock(Subscriber::class);
        $this->subscriberInterfaceFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($subscriberMock);

        $this->subscriberResourceModelMock->expects($this->once())
            ->method('load')
            ->with($subscriberMock, $subscriberId)
            ->willReturnSelf();

        $this->repository->getById($subscriberId);
    }

    /**
     * Test delete method
     */
    public function testDelete()
    {
        $subscriberId = '123';

        $subscriberMock = $this->createMock(Subscriber::class);
        $subscriberMock->expects($this->atLeastOnce())
            ->method('getId')
            ->willReturn($subscriberId);

        $this->subscriberResourceModelMock->expects($this->once())
            ->method('delete')
            ->with($subscriberMock)
            ->willReturn(true);

        $this->assertTrue($this->repository->delete($subscriberMock));
    }

    /**
     * Test delete method if an error occurs
     *
     * @expectedException \Magento\Framework\Exception\CouldNotDeleteException
     * @expectedExceptionMessage Error!
     */
    public function testDeleteException()
    {
        $subscriberId = '123';

        $subscriberMock = $this->createMock(Subscriber::class);
        $subscriberMock->expects($this->never())
            ->method('getId')
            ->willReturn($subscriberId);

        $this->subscriberResourceModelMock->expects($this->once())
            ->method('delete')
            ->with($subscriberMock)
            ->willThrowException(new \Exception('Error!'));

        $this->repository->delete($subscriberMock);
    }

    /**
     * Testing of getList method
     */
    public function testGetList()
    {
        $subscriberData = [
            SubscriberInterface::ID => 1,
        ];
        /** @var Subscriber|\PHPUnit_Framework_MockObject_MockObject $subscriberModelMock */
        $subscriberModelMock = $this->createPartialMock(
            Subscriber::class,
            [
                'getData'
            ]
        );
        $subscriberModelMock->expects($this->once())
            ->method('getData')
            ->willReturn($subscriberData);
        /** @var SubscriberInterface|\PHPUnit_Framework_MockObject_MockObject $subscriberItemMock */
        $subscriberMock = $this->createMock(SubscriberInterface::class);
        $searchResultItems = [
            $subscriberMock
        ];
        $collectionItems = [
            $subscriberModelMock
        ];
        $collectionSize = count($collectionItems);

        /** @var SubscriberCollection|\PHPUnit_Framework_MockObject_MockObject $subscriberCollectionMock */
        $subscriberCollectionMock = $this->createMock(SubscriberCollection::class);
        $subscriberCollectionMock->expects($this->once())
            ->method('getSize')
            ->willReturn($collectionSize);
        $subscriberCollectionMock->expects($this->once())
            ->method('getItems')
            ->willReturn($collectionItems);

        /** @var SearchCriteriaInterface|\PHPUnit_Framework_MockObject_MockObject $searchCriteriaMock */
        $searchCriteriaMock = $this->createMock(SearchCriteriaInterface::class);
        $searchResultsMock = $this->createMock(SubscriberSearchResultsInterface::class);

        $this->subscriberCollectionFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($subscriberCollectionMock);

        $this->extensionAttributesJoinProcessorMock->expects($this->once())
            ->method('process')
            ->with($subscriberCollectionMock, SubscriberInterface::class);
        $this->collectionProcessorMock->expects($this->once())
            ->method('process')
            ->with($searchCriteriaMock, $subscriberCollectionMock);

        $this->subscriberSearchResultsFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($searchResultsMock);
        $searchResultsMock->expects($this->once())
            ->method('setSearchCriteria')
            ->with($searchCriteriaMock);
        $searchResultsMock->expects($this->once())
            ->method('setTotalCount')
            ->with($collectionSize);

        $this->subscriberInterfaceFactoryMock->expects($this->exactly($collectionSize))
            ->method('create')
            ->willReturn($subscriberMock);
        $this->dataObjectHelperMock->expects($this->exactly($collectionSize))
            ->method('populateWithArray')
            ->with($subscriberMock, $subscriberData, SubscriberInterface::class);

        $searchResultsMock->expects($this->once())
            ->method('setItems')
            ->with($searchResultItems)
            ->willReturnSelf();

        $this->assertSame($searchResultsMock, $this->repository->getList($searchCriteriaMock));
    }
}
