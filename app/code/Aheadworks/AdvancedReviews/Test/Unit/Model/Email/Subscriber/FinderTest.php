<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Test\Unit\Model\Email\Subscriber;

use Aheadworks\AdvancedReviews\Model\Email\Subscriber\Finder;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Aheadworks\AdvancedReviews\Api\EmailSubscriberRepositoryInterface;
use Aheadworks\AdvancedReviews\Api\Data\Email\SubscriberSearchResultsInterface;
use Aheadworks\AdvancedReviews\Api\Data\Email\SubscriberInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SearchCriteria;

/**
 * Test for \Aheadworks\AdvancedReviews\Model\Email\Subscriber\Finder
 */
class FinderTest extends TestCase
{
    /**
     * @var Finder
     */
    private $model;

    /**
     * @var EmailSubscriberRepositoryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $subscriberRepositoryMock;

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

        $this->subscriberRepositoryMock = $this->createMock(EmailSubscriberRepositoryInterface::class);
        $this->searchCriteriaBuilderMock = $this->createMock(SearchCriteriaBuilder::class);

        $this->model = $objectManager->getObject(
            Finder::class,
            [
                'subscriberRepository' => $this->subscriberRepositoryMock,
                'searchCriteriaBuilder' => $this->searchCriteriaBuilderMock,
            ]
        );
    }

    /**
     * Test find method
     *
     * @param array $items
     * @param SubscriberInterface|null $result
     * @dataProvider findDataProvider
     */
    public function testFind($items, $result)
    {
        $email = 'test@test.com';
        $websiteId = 1;

        $searchCriteriaMock = $this->createMock(SearchCriteria::class);
        $this->searchCriteriaBuilderMock->expects($this->exactly(2))
            ->method('addFilter')
            ->withConsecutive(
                [SubscriberInterface::EMAIL, $email],
                [SubscriberInterface::WEBSITE_ID, $websiteId]
            )
            ->willReturnSelf();
        $this->searchCriteriaBuilderMock->expects($this->once())
            ->method('create')
            ->willReturn($searchCriteriaMock);

        $searchResultMock = $this->createMock(SubscriberSearchResultsInterface::class);
        $searchResultMock->expects($this->once())
            ->method('getItems')
            ->willReturn($items);
        $this->subscriberRepositoryMock->expects($this->once())
            ->method('getList')
            ->with($searchCriteriaMock)
            ->willReturn($searchResultMock);

        $this->assertSame($result, $this->model->find($email, $websiteId));
    }

    /**
     * @return array
     */
    public function findDataProvider()
    {
        $subscriberOneMock = $this->createMock(SubscriberInterface::class);
        $subscriberTwoMock = $this->createMock(SubscriberInterface::class);

        return [
            [
                'items' => [],
                'result' => null,
            ],
            [
                'items' => [$subscriberOneMock],
                'result' => $subscriberOneMock,
            ],
            [
                'items' => [$subscriberOneMock, $subscriberTwoMock],
                'result' => $subscriberOneMock,
            ],
        ];
    }
}
