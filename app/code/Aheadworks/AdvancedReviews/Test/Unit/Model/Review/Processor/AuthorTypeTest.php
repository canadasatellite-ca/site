<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Test\Unit\Model\Review\Processor;

use Aheadworks\AdvancedReviews\Model\Review\Processor\AuthorType;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Aheadworks\AdvancedReviews\Model\Review\Author\Type\Resolver as AuthorTypeResolver;
use Aheadworks\AdvancedReviews\Model\Source\Review\AuthorType as ReviewAuthorTypeSource;
use Magento\Store\Model\Store;
use Aheadworks\AdvancedReviews\Api\Data\ReviewInterface;

/**
 * Test for \Aheadworks\AdvancedReviews\Model\Review\Processor\AuthorType
 */
class AuthorTypeTest extends TestCase
{
    /**
     * @var AuthorType
     */
    private $processor;

    /**
     * @var AuthorTypeResolver|\PHPUnit_Framework_MockObject_MockObject
     */
    private $authorTypeResolverMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->authorTypeResolverMock = $this->createMock(AuthorTypeResolver::class);

        $this->processor = $objectManager->getObject(
            AuthorType::class,
            [
                'authorTypeResolver' => $this->authorTypeResolverMock,
            ]
        );
    }

    /**
     * Test process method when no store id is specified
     *
     * @param int|null|string $storeId
     * @dataProvider processNoStoreIdDataProvider
     */
    public function testProcessNoStoreId($storeId)
    {
        $review = $this->createMock(ReviewInterface::class);
        $review->expects($this->once())
            ->method('getStoreId')
            ->willReturn($storeId);
        $review->expects($this->never())
            ->method('getCustomerId');
        $review->expects($this->never())
            ->method('setAuthorType');

        $this->authorTypeResolverMock->expects($this->never())
            ->method('resolveAuthorType');

        $this->assertSame($review, $this->processor->process($review));
    }

    /**
     * @return array
     */
    public function processNoStoreIdDataProvider()
    {
        return [
            [
                'storeId' => null,
            ],
        ];
    }

    /**
     * Test process method
     *
     * @param int $storeId
     * @param int|null $customerId
     * @param int $authorType
     * @dataProvider processDataProvider
     */
    public function testProcess($storeId, $customerId, $authorType)
    {
        $this->authorTypeResolverMock->expects($this->any())
            ->method('resolveAuthorType')
            ->willReturnMap(
                [
                    [
                        1,
                        null,
                        ReviewAuthorTypeSource::GUEST
                    ],
                    [
                        1,
                        1,
                        ReviewAuthorTypeSource::CUSTOMER
                    ],
                    [
                        Store::DEFAULT_STORE_ID,
                        null,
                        ReviewAuthorTypeSource::ADMIN
                    ],
                ]
            );

        $review = $this->createMock(ReviewInterface::class);
        $review->expects($this->once())
            ->method('getStoreId')
            ->willReturn($storeId);
        $review->expects($this->once())
            ->method('getCustomerId')
            ->willReturn($customerId);
        $review->expects($this->once())
            ->method('setAuthorType')
            ->with($authorType)
            ->willReturnSelf();

        $this->authorTypeResolverMock->expects($this->once())
            ->method('resolveAuthorType')
            ->with($storeId, $customerId)
            ->willReturn($authorType);

        $this->assertSame($review, $this->processor->process($review));
    }

    /**
     * @return array
     */
    public function processDataProvider()
    {
        return [
            [
                'storeId' => 1,
                'customerId' => null,
                'authorType' => ReviewAuthorTypeSource::GUEST,
            ],
            [
                'storeId' => 1,
                'customerId' => 1,
                'authorType' => ReviewAuthorTypeSource::CUSTOMER,
            ],
            [
                'storeId' => Store::DEFAULT_STORE_ID,
                'customerId' => null,
                'authorType' => ReviewAuthorTypeSource::ADMIN,
            ],
        ];
    }
}
