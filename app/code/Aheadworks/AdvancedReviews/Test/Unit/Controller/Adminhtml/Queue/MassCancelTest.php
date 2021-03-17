<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Test\Unit\Controller\Adminhtml\Queue;

use Aheadworks\AdvancedReviews\Controller\Adminhtml\Queue\MassCancel;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\TestCase;
use Magento\Backend\App\Action\Context;
use Aheadworks\AdvancedReviews\Api\QueueManagementInterface;
use Aheadworks\AdvancedReviews\Api\Data\QueueItemInterface;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Backend\Model\View\Result\RedirectFactory as ResultRedirectFactory;
use Magento\Framework\Message\ManagerInterface as MessageManagerInterface;
use Magento\Ui\Component\MassAction\Filter;
use Aheadworks\AdvancedReviews\Model\ResourceModel\Queue\Collection as QueueCollection;
use Aheadworks\AdvancedReviews\Model\ResourceModel\Queue\CollectionFactory as QueueCollectionFactory;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SearchCriteria;
use Aheadworks\AdvancedReviews\Api\QueueRepositoryInterface;
use Aheadworks\AdvancedReviews\Api\Data\QueueItemSearchResultsInterface;

/**
 * Test for \Aheadworks\AdvancedReviews\Controller\Adminhtml\Queue\MassCancel
 */
class MassCancelTest extends TestCase
{
    /**
     * @var MassCancel
     */
    private $controller;

    /**
     * @var Context|\PHPUnit_Framework_MockObject_MockObject
     */
    private $contextMock;

    /**
     * @var QueueManagementInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $queueManagementMock;

    /**
     * @var ResultRedirectFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $resultRedirectFactoryMock;

    /**
     * @var MessageManagerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $messageManagerMock;

    /**
     * @var QueueCollectionFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $queueCollectionFactoryMock;

    /**
     * @var Filter|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $filterMock;

    /**
     * @var SearchCriteriaBuilder|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $searchCriteriaBuilderMock;

    /**
     * @var QueueRepositoryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $queueRepositoryMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->resultRedirectFactoryMock = $this->createMock(ResultRedirectFactory::class);
        $this->messageManagerMock = $this->createMock(MessageManagerInterface::class);

        $this->contextMock = $objectManager->getObject(
            Context::class,
            [
                'messageManager' => $this->messageManagerMock,
                'resultRedirectFactory' => $this->resultRedirectFactoryMock,
            ]
        );

        $this->queueCollectionFactoryMock = $this->createMock(QueueCollectionFactory::class);
        $this->filterMock = $this->createMock(Filter::class);
        $this->searchCriteriaBuilderMock = $this->createMock(SearchCriteriaBuilder::class);
        $this->queueRepositoryMock = $this->createMock(QueueRepositoryInterface::class);
        $this->queueManagementMock = $this->createMock(QueueManagementInterface::class);

        $this->controller = $objectManager->getObject(
            MassCancel::class,
            [
                'context' => $this->contextMock,
                'queueCollectionFactory' => $this->queueCollectionFactoryMock,
                'filter' => $this->filterMock,
                'searchCriteriaBuilder' => $this->searchCriteriaBuilderMock,
                'queueManagement' => $this->queueManagementMock,
                'queueRepository' => $this->queueRepositoryMock,
            ]
        );
    }

    /**
     * Test execute method with empty array of items
     */
    public function testExecuteEmptyArray()
    {
        $errorMessage = __('An item needs to be selected. Select and try again.');

        $queueCollection = $this->createMock(QueueCollection::class);
        $this->queueCollectionFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($queueCollection);

        $this->filterMock->expects($this->once())
            ->method('getCollection')
            ->with($queueCollection)
            ->willThrowException(new LocalizedException($errorMessage));

        $this->queueRepositoryMock->expects($this->never())
            ->method('getList');

        $this->queueManagementMock->expects($this->never())
            ->method('cancel');

        $this->messageManagerMock->expects($this->once())
            ->method('addErrorMessage')
            ->with($errorMessage);

        $resultRedirect = $this->createMock(Redirect::class);
        $resultRedirect->expects($this->once())
            ->method('setRefererOrBaseUrl')
            ->willReturnSelf();

        $this->resultRedirectFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($resultRedirect);

        $this->assertSame($resultRedirect, $this->controller->execute());
    }

    /**
     * Test execute method
     */
    public function testExecute()
    {
        $ids = [1, 3, 6];
        $firstQueueItem = $this->createMock(QueueItemInterface::class);
        $secondQueueItem = $this->createMock(QueueItemInterface::class);
        $thirdQueueItem = $this->createMock(QueueItemInterface::class);
        $queueItems = [$firstQueueItem, $secondQueueItem, $thirdQueueItem];
        $successMessage = 'A total of 3 email(s) have been updated';

        $queueCollection = $this->createMock(QueueCollection::class);
        $queueCollection->expects($this->once())
            ->method('getAllIds')
            ->willReturn($ids);

        $this->queueCollectionFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($queueCollection);

        $this->filterMock->expects($this->once())
            ->method('getCollection')
            ->with($queueCollection)
            ->willReturn($queueCollection);

        $searchCriteria = $this->createMock(SearchCriteria::class);
        $this->searchCriteriaBuilderMock->expects($this->once())
            ->method('addFilter')
            ->with(QueueItemInterface::ID, $ids, 'in')
            ->willReturnSelf();
        $this->searchCriteriaBuilderMock->expects($this->once())
            ->method('create')
            ->willReturn($searchCriteria);

        $searchResults = $this->createMock(QueueItemSearchResultsInterface::class);
        $searchResults->expects($this->once())
            ->method('getItems')
            ->willReturn($queueItems);

        $this->queueRepositoryMock->expects($this->once())
            ->method('getList')
            ->with($searchCriteria)
            ->willReturn($searchResults);

        $this->queueManagementMock->expects($this->exactly(3))
            ->method('cancel')
            ->withConsecutive($firstQueueItem, $secondQueueItem, $thirdQueueItem)
            ->willReturnOnConsecutiveCalls($firstQueueItem, $secondQueueItem, $thirdQueueItem);

        $this->messageManagerMock->expects($this->once())
            ->method('addSuccessMessage')
            ->with($successMessage);

        $this->messageManagerMock->expects($this->never())
            ->method('addErrorMessage');

        $resultRedirect = $this->createMock(Redirect::class);
        $resultRedirect->expects($this->once())
            ->method('setRefererOrBaseUrl')
            ->willReturnSelf();

        $this->resultRedirectFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($resultRedirect);

        $this->assertSame($resultRedirect, $this->controller->execute());
    }

    /**
     * Test execute method with error
     */
    public function testExecuteError()
    {
        $ids = [1, 3, 6];
        $firstQueueItem = $this->createMock(QueueItemInterface::class);
        $secondQueueItem = $this->createMock(QueueItemInterface::class);
        $thirdQueueItem = $this->createMock(QueueItemInterface::class);
        $queueItems = [$firstQueueItem, $secondQueueItem, $thirdQueueItem];
        $errorMessage = __('Error!');

        $queueCollection = $this->createMock(QueueCollection::class);
        $queueCollection->expects($this->once())
            ->method('getAllIds')
            ->willReturn($ids);

        $this->queueCollectionFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($queueCollection);

        $this->filterMock->expects($this->once())
            ->method('getCollection')
            ->with($queueCollection)
            ->willReturn($queueCollection);

        $searchCriteria = $this->createMock(SearchCriteria::class);
        $this->searchCriteriaBuilderMock->expects($this->once())
            ->method('addFilter')
            ->with(QueueItemInterface::ID, $ids, 'in')
            ->willReturnSelf();
        $this->searchCriteriaBuilderMock->expects($this->once())
            ->method('create')
            ->willReturn($searchCriteria);

        $searchResults = $this->createMock(QueueItemSearchResultsInterface::class);
        $searchResults->expects($this->once())
            ->method('getItems')
            ->willReturn($queueItems);

        $this->queueRepositoryMock->expects($this->once())
            ->method('getList')
            ->with($searchCriteria)
            ->willReturn($searchResults);

        $this->queueManagementMock->expects($this->at(0))
            ->method('cancel')
            ->with($firstQueueItem)
            ->willReturn($firstQueueItem);
        $this->queueManagementMock->expects($this->at(1))
            ->method('cancel')
            ->with($secondQueueItem)
            ->willThrowException(new LocalizedException($errorMessage));

        $this->messageManagerMock->expects($this->never())
            ->method('addSuccessMessage');

        $this->messageManagerMock->expects($this->once())
            ->method('addErrorMessage')
            ->with($errorMessage);

        $resultRedirect = $this->createMock(Redirect::class);
        $resultRedirect->expects($this->once())
            ->method('setRefererOrBaseUrl')
            ->willReturnSelf();

        $this->resultRedirectFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($resultRedirect);

        $this->assertSame($resultRedirect, $this->controller->execute());
    }
}
