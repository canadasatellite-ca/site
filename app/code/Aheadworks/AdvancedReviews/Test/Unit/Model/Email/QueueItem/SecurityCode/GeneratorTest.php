<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Test\Unit\Model\Email\QueueItem\SecurityCode;

use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Aheadworks\AdvancedReviews\Model\Email\QueueItem\SecurityCode\Generator;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SearchCriteria;
use Magento\Framework\Math\Random;
use Aheadworks\AdvancedReviews\Api\Data\QueueItemInterface;
use Aheadworks\AdvancedReviews\Api\Data\QueueItemSearchResultsInterface;
use Aheadworks\AdvancedReviews\Api\QueueRepositoryInterface;
use Magento\Framework\Exception\LocalizedException;

/**
 * Test for \Aheadworks\AdvancedReviews\Model\Email\QueueItem\SecurityCode\Generator
 */
class GeneratorTest extends TestCase
{
    /**
     * @var Generator
     */
    private $model;

    /**
     * @var QueueRepositoryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $eventRepositoryMock;

    /**
     * @var SearchCriteriaBuilder|\PHPUnit_Framework_MockObject_MockObject
     */
    private $searchCriteriaBuilderMock;

    /**
     * @var Random|\PHPUnit_Framework_MockObject_MockObject
     */
    private $randomMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->eventRepositoryMock = $this->createMock(QueueRepositoryInterface::class);
        $this->searchCriteriaBuilderMock = $this->createMock(SearchCriteriaBuilder::class);
        $this->randomMock = $this->createMock(Random::class);

        $this->model = $objectManager->getObject(
            Generator::class,
            [
                'queueRepository' => $this->eventRepositoryMock,
                'searchCriteriaBuilder' => $this->searchCriteriaBuilderMock,
                'random' => $this->randomMock,
            ]
        );
    }

    /**
     * Test getCode method
     */
    public function testGetCode()
    {
        $randomCodeOne = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabc012';
        $randomCodeTwo = '1234567890abcdefghijklmnopqrtsuv';

        $this->randomMock->expects($this->exactly(2))
            ->method('getRandomString')
            ->with(Generator::CODE_LENGTH)
            ->willReturnOnConsecutiveCalls($randomCodeOne, $randomCodeTwo);

        $searchResultsOneMock = $this->getSearchResultsMock(1);
        $searchResultsTwoMock = $this->getSearchResultsMock(0);

        $searchCriteriaOneMock = $this->createMock(SearchCriteria::class);
        $searchCriteriaTwoMock = $this->createMock(SearchCriteria::class);
        $this->searchCriteriaBuilderMock->expects($this->exactly(2))
            ->method('addFilter')
            ->withConsecutive(
                [QueueItemInterface::SECURITY_CODE, $randomCodeOne, 'eq'],
                [QueueItemInterface::SECURITY_CODE, $randomCodeTwo, 'eq']
            )
            ->willReturnSelf();
        $this->searchCriteriaBuilderMock->expects($this->exactly(2))
            ->method('create')
            ->willReturnOnConsecutiveCalls($searchCriteriaOneMock, $searchCriteriaTwoMock);

        $this->eventRepositoryMock->expects($this->exactly(2))
            ->method('getList')
            ->withConsecutive([$searchCriteriaOneMock], [$searchCriteriaTwoMock])
            ->willReturnOnConsecutiveCalls($searchResultsOneMock, $searchResultsTwoMock);

        $this->assertEquals($randomCodeTwo, $this->model->getCode());
    }

    /**
     * Test getCode method if an error occurs
     *
     * @expectedException \Magento\Framework\Exception\LocalizedException
     * @expectedExceptionMessage Error!
     */
    public function testGetCodeError()
    {
        $this->randomMock->expects($this->once())
            ->method('getRandomString')
            ->with(Generator::CODE_LENGTH)
            ->willThrowException(new LocalizedException(__('Error!')));

        $this->searchCriteriaBuilderMock->expects($this->never())
            ->method('create');

        $this->eventRepositoryMock->expects($this->never())
            ->method('getList');

        $this->model->getCode();
    }

    /**
     * Get search results mock
     *
     * @param int $resultsCount
     * @return QueueItemSearchResultsInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private function getSearchResultsMock($resultsCount)
    {
        $searchResultsOneMock = $this->createMock(QueueItemSearchResultsInterface::class);
        $searchResultsOneMock->expects($this->once())
            ->method('getTotalCount')
            ->willReturn($resultsCount);

        return $searchResultsOneMock;
    }
}
