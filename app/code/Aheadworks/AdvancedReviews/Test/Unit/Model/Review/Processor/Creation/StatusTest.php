<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Test\Unit\Model\Review\Processor\Creation;

use Aheadworks\AdvancedReviews\Model\Review\Processor\Creation\Status;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Aheadworks\AdvancedReviews\Api\Data\ReviewInterface;
use Aheadworks\AdvancedReviews\Model\Status\Resolver\Review as StatusResolver;
use Aheadworks\AdvancedReviews\Model\Source\Review\Status as ReviewStatusSource;

/**
 * Test for \Aheadworks\AdvancedReviews\Model\Review\Processor\Creation\Status
 */
class StatusTest extends TestCase
{
    /**
     * @var Status
     */
    private $processor;

    /**
     * @var StatusResolver|\PHPUnit_Framework_MockObject_MockObject
     */
    private $statusResolverMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->statusResolverMock = $this->createMock(StatusResolver::class);

        $this->processor = $objectManager->getObject(
            Status::class,
            [
                'statusResolver' => $this->statusResolverMock,
            ]
        );
    }

    /**
     * Test process method when no modifications have been done
     *
     * @param int|null $status
     * @dataProvider processNoModificationsDataProvider
     */
    public function testProcessNoModifications($status)
    {
        $review = $this->createMock(ReviewInterface::class);
        $review->expects($this->once())
            ->method('getStatus')
            ->willReturn($status);
        $review->expects($this->never())
            ->method('setStatus');

        $this->statusResolverMock->expects($this->never())
            ->method('getNewInstanceStatus');

        $this->assertSame($review, $this->processor->process($review));
    }

    /**
     * @return array
     */
    public function processNoModificationsDataProvider()
    {
        return [
            [
                'status' => ReviewStatusSource::APPROVED,
            ],
            [
                'status' => ReviewStatusSource::PENDING,
            ],
            [
                'status' => ReviewStatusSource::NOT_APPROVED,
            ],
        ];
    }

    /**
     * Test process method
     *
     * @param int|null $status
     * @param int $storeId
     * @dataProvider processDataProvider
     */
    public function testProcess($status, $storeId)
    {
        $statusToSet = ReviewStatusSource::PENDING;

        $review = $this->createMock(ReviewInterface::class);
        $review->expects($this->once())
            ->method('getStatus')
            ->willReturn($status);
        $review->expects($this->once())
            ->method('getStoreId')
            ->willReturn($storeId);
        $review->expects($this->once())
            ->method('setStatus')
            ->with($statusToSet)
            ->willReturnSelf();

        $this->statusResolverMock->expects($this->once())
            ->method('getNewInstanceStatus')
            ->with($storeId)
            ->willReturn($statusToSet);

        $this->assertSame($review, $this->processor->process($review));
    }

    /**
     * @return array
     */
    public function processDataProvider()
    {
        return [
            [
                'status' => null,
                'storeId' => 1,
            ],
            [
                'status' => '',
                'storeId' => 1,
            ],
            [
                'status' => 0,
                'storeId' => 1,
            ],
        ];
    }
}
