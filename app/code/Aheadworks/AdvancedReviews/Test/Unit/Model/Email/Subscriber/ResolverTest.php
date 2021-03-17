<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Test\Unit\Model\Email\Subscriber;

use Aheadworks\AdvancedReviews\Model\Email\Subscriber\Resolver;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Aheadworks\AdvancedReviews\Api\EmailSubscriberManagementInterface;
use Magento\Framework\Exception\LocalizedException;
use Aheadworks\AdvancedReviews\Api\Data\Email\SubscriberInterface;
use Aheadworks\AdvancedReviews\Model\Store\Resolver as StoreResolver;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SearchCriteria;
use Aheadworks\AdvancedReviews\Api\Data\QueueItemInterface;
use Aheadworks\AdvancedReviews\Api\QueueRepositoryInterface;
use Aheadworks\AdvancedReviews\Api\Data\QueueItemSearchResultsInterface;

/**
 * Test for \Aheadworks\AdvancedReviews\Model\Email\Subscriber\Resolver
 */
class ResolverTest extends TestCase
{
    /**
     * @var Resolver
     */
    private $model;

    /**
     * @var EmailSubscriberManagementInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $emailSubscriberManagementMock;

    /**
     * @var StoreResolver|\PHPUnit_Framework_MockObject_MockObject
     */
    private $storeResolverMock;

    /**
     * @var QueueRepositoryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $queueRepositoryMock;

    /**
     * @var SearchCriteriaBuilder|\PHPUnit_Framework_MockObject_MockObject
     */
    private $searchCriteriaBuilderMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    protected function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->emailSubscriberManagementMock = $this->createMock(EmailSubscriberManagementInterface::class);
        $this->storeResolverMock = $this->createMock(StoreResolver::class);
        $this->queueRepositoryMock = $this->createMock(QueueRepositoryInterface::class);
        $this->searchCriteriaBuilderMock = $this->createMock(SearchCriteriaBuilder::class);

        $this->model = $objectManager->getObject(
            Resolver::class,
            [
                'emailSubscriberManagement' => $this->emailSubscriberManagementMock,
                'storeResolver' => $this->storeResolverMock,
                'queueRepository' => $this->queueRepositoryMock,
                'searchCriteriaBuilder' => $this->searchCriteriaBuilderMock,
            ]
        );
    }

    /**
     * Test getByEmailQueueItem method
     */
    public function testGetByEmailQueueItem()
    {
        $email = 'test@aw.com';
        $storeId = 1;
        $websiteId = 1;

        $this->storeResolverMock->expects($this->any())
            ->method('getWebsiteIdByStoreId')
            ->with($storeId)
            ->willReturn($websiteId);

        $subscriber = $this->createMock(SubscriberInterface::class);

        $this->emailSubscriberManagementMock->expects($this->once())
            ->method('getSubscriber')
            ->with($email, $websiteId)
            ->willReturn($subscriber);

        $queueItem = $this->createQueueItemMock($storeId, $email);

        $this->assertSame($subscriber, $this->model->getByEmailQueueItem($queueItem));
    }

    /**
     * Test getByEmailQueueItem method with error
     */
    public function testGetByEmailQueueItemWithError()
    {
        $email = 'test@aw.com';
        $storeId = 1;
        $websiteId = 1;

        $this->storeResolverMock->expects($this->any())
            ->method('getWebsiteIdByStoreId')
            ->with($storeId)
            ->willReturn($websiteId);

        $this->emailSubscriberManagementMock->expects($this->once())
            ->method('getSubscriber')
            ->with($email, $websiteId)
            ->willThrowException(new LocalizedException(__('Error!')));

        $queueItem = $this->createQueueItemMock($storeId, $email);

        $this->assertNull($this->model->getByEmailQueueItem($queueItem));
    }

    /**
     * Test getBySecurityCode method with empty code
     */
    public function testGetByEmptySecurityCode()
    {
        $securityCode = '';

        $this->assertNull($this->model->getBySecurityCode($securityCode));
    }

    /**
     * Test getBySecurityCode method
     *
     * @param string $securityCode
     * @param array $queueItems
     * @param int $storeId
     * @param string $email
     * @param SubscriberInterface|null $result
     * @dataProvider getBySecurityCodeDataProvider
     */
    public function testGetBySecurityCode($securityCode, $queueItems, $storeId, $email, $result)
    {
        $websiteId = 1;

        $this->storeResolverMock->expects($this->any())
            ->method('getWebsiteIdByStoreId')
            ->with($storeId)
            ->willReturn($websiteId);

        $searchCriteria = $this->createMock(SearchCriteria::class);
        $this->searchCriteriaBuilderMock->expects($this->once())
            ->method('addFilter')
            ->with(QueueItemInterface::SECURITY_CODE, $securityCode, 'eq')
            ->willReturnSelf();
        $this->searchCriteriaBuilderMock->expects($this->once())
            ->method('create')
            ->willReturn($searchCriteria);

        $searchResults = $this->createMock(QueueItemSearchResultsInterface::class);
        $searchResults->expects($this->once())
            ->method('getItems')
            ->willReturn($queueItems);

        $this->queueRepositoryMock->expects($this->any())
            ->method('getList')
            ->with($searchCriteria)
            ->willReturn($searchResults);

        $this->emailSubscriberManagementMock->expects($this->any())
            ->method('getSubscriber')
            ->with($email, $websiteId)
            ->willReturn($result);

        $this->assertSame($result, $this->model->getBySecurityCode($securityCode));
    }

    /**
     * @return array
     */
    public function getBySecurityCodeDataProvider()
    {
        $queueItemOneMock = $this->createQueueItemMock(1, 'test1@aw.com');
        $queueItemTwoMock = $this->createQueueItemMock(2, 'test2@aw.com');
        $subscriberMock = $this->createMock(SubscriberInterface::class);

        return [
            [
                'securityCode' => 'test1',
                'queueItems' => [],
                'storeId' => 1,
                'email' => 'test1@aw.com',
                'result' => null,
            ],
            [
                'securityCode' => 'test2',
                'queueItems' => [$queueItemOneMock],
                'storeId' => 1,
                'email' => 'test1@aw.com',
                'result' => $subscriberMock,
            ],
            [
                'securityCode' => 'test3',
                'queueItems' => [$queueItemTwoMock],
                'storeId' => 2,
                'email' => 'test2@aw.com',
                'result' => $subscriberMock,
            ],
            [
                'securityCode' => 'test4',
                'queueItems' => [$queueItemOneMock, $queueItemTwoMock],
                'storeId' => 1,
                'email' => 'test1@aw.com',
                'result' => $subscriberMock,
            ],
        ];
    }

    /**
     * Test getBySecurityCode method with error
     *
     * @param string $securityCode
     * @param array $queueItems
     * @param int $storeId
     * @param string $email
     * @dataProvider getBySecurityCodeDataProvider
     */
    public function testGetBySecurityCodeWithError($securityCode, $queueItems, $storeId, $email)
    {
        $websiteId = 1;

        $this->storeResolverMock->expects($this->any())
            ->method('getWebsiteIdByStoreId')
            ->with($storeId)
            ->willReturn($websiteId);

        $searchCriteria = $this->createMock(SearchCriteria::class);
        $this->searchCriteriaBuilderMock->expects($this->once())
            ->method('addFilter')
            ->with(QueueItemInterface::SECURITY_CODE, $securityCode, 'eq')
            ->willReturnSelf();
        $this->searchCriteriaBuilderMock->expects($this->once())
            ->method('create')
            ->willReturn($searchCriteria);

        $searchResults = $this->createMock(QueueItemSearchResultsInterface::class);
        $searchResults->expects($this->once())
            ->method('getItems')
            ->willReturn($queueItems);

        $this->queueRepositoryMock->expects($this->any())
            ->method('getList')
            ->with($searchCriteria)
            ->willReturn($searchResults);

        $this->emailSubscriberManagementMock->expects($this->any())
            ->method('getSubscriber')
            ->with($email, $websiteId)
            ->willThrowException(new LocalizedException(__('Error!')));

        $this->assertNull($this->model->getBySecurityCode($securityCode));
    }

    /**
     * Retrieve queue item mock
     *
     * @param int $storeId
     * @param string $email
     * @return QueueItemInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    private function createQueueItemMock($storeId, $email)
    {
        $queueItemMock = $this->createMock(QueueItemInterface::class);
        $queueItemMock->expects($this->any())
            ->method('getStoreId')
            ->willReturn($storeId);
        $queueItemMock->expects($this->any())
            ->method('getRecipientEmail')
            ->willReturn($email);
        return $queueItemMock;
    }
}
