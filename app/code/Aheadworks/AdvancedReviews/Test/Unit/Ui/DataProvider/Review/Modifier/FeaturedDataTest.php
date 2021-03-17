<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Test\Unit\Ui\DataProvider\Review\Modifier;

use Aheadworks\AdvancedReviews\Ui\DataProvider\Review\Modifier\FeaturedData;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Aheadworks\AdvancedReviews\Api\Data\ReviewInterface;
use Aheadworks\AdvancedReviews\Api\ReviewRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SearchCriteria;
use Aheadworks\AdvancedReviews\Model\Source\Review\Status as ReviewStatusSource;
use Aheadworks\AdvancedReviews\Ui\DataProvider\Review\Frontend\Product\Featured\ListingDataProvider
    as FeaturedReviewListingDataProvider;
use Aheadworks\AdvancedReviews\Api\Data\ReviewSearchResultsInterface;

/**
 * Test for \Aheadworks\AdvancedReviews\Ui\DataProvider\Review\Modifier\FeaturedData
 */
class FeaturedDataTest extends TestCase
{
    /**
     * Featured reviews total count
     */
    const TOTAL_COUNT = 4;

    /**
     * @var FeaturedData
     */
    private $modifier;

    /**
     * @var ReviewRepositoryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $reviewRepositoryMock;

    /**
     * @var SearchCriteriaBuilder|\PHPUnit_Framework_MockObject_MockObject
     */
    private $searchCriteriaBuilderMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->reviewRepositoryMock = $this->createMock(ReviewRepositoryInterface::class);
        $this->searchCriteriaBuilderMock = $this->createMock(SearchCriteriaBuilder::class);

        $this->modifier = $objectManager->getObject(
            FeaturedData::class,
            [
                'reviewRepository' => $this->reviewRepositoryMock,
                'searchCriteriaBuilder' => $this->searchCriteriaBuilderMock,
            ]
        );
    }

    /**
     * Test modifyMeta method
     *
     * @param array $meta
     * @param array $result
     * @dataProvider modifyMetaDataProvider
     */
    public function testModifyMeta($meta, $result)
    {
        $this->assertSame($result, $this->modifier->modifyMeta($meta));
    }

    /**
     * @return array
     */
    public function modifyMetaDataProvider()
    {
        return [
            [
                [],
                []
            ],
            [
                ['some meta data'],
                ['some meta data'],
            ],
        ];
    }

    /**
     * Test modifyData method
     *
     * @param array $data
     * @param array $result
     * @dataProvider modifyDataDataProvider
     */
    public function testModifyData($data, $result)
    {
        $productId = isset($data[ReviewInterface::PRODUCT_ID]) ? $data[ReviewInterface::PRODUCT_ID] : null;

        $searchCriteria = $this->createMock(SearchCriteria::class);

        $this->searchCriteriaBuilderMock->expects($this->any())
            ->method('addFilter')
            ->withConsecutive(
                [ReviewInterface::PRODUCT_ID, $productId],
                [ReviewInterface::IS_FEATURED, true],
                [ReviewInterface::STATUS, ReviewStatusSource::getDisplayStatuses()]
            )->willReturnSelf();
        $this->searchCriteriaBuilderMock->expects($this->any())
            ->method('create')
            ->willReturn($searchCriteria);

        $searchResults = $this->createMock(ReviewSearchResultsInterface::class);
        $searchResults->expects($this->any())
            ->method('getTotalCount')
            ->willReturn(self::TOTAL_COUNT);

        $this->reviewRepositoryMock->expects($this->any())
            ->method('getList')
            ->with($searchCriteria)
            ->willReturn($searchResults);

        $this->assertEquals($result, $this->modifier->modifyData($data));
    }

    /**
     * @return array
     */
    public function modifyDataDataProvider()
    {
        return [
            [
                [],
                [
                    'featuredReviewsCount' => 0,
                    'featuredReviewsLimit' => FeaturedReviewListingDataProvider::REVIEWS_COUNT,
                ],
            ],
            [
                [
                    ReviewInterface::ID => null,
                ],
                [
                    ReviewInterface::ID => null,
                    'featuredReviewsCount' => 0,
                    'featuredReviewsLimit' => FeaturedReviewListingDataProvider::REVIEWS_COUNT,
                ],
            ],
            [
                [
                    ReviewInterface::ID => '',
                ],
                [
                    ReviewInterface::ID => '',
                    'featuredReviewsCount' => 0,
                    'featuredReviewsLimit' => FeaturedReviewListingDataProvider::REVIEWS_COUNT,
                ],
            ],
            [
                [
                    ReviewInterface::ID => 1,
                ],
                [
                    ReviewInterface::ID => 1,
                    'featuredReviewsCount' => 0,
                    'featuredReviewsLimit' => FeaturedReviewListingDataProvider::REVIEWS_COUNT,
                ],
            ],

            [
                [
                    ReviewInterface::ID => 1,
                    ReviewInterface::PRODUCT_ID => 1,
                ],
                [
                    ReviewInterface::ID => 1,
                    ReviewInterface::PRODUCT_ID => 1,
                    'featuredReviewsCount' => self::TOTAL_COUNT,
                    'featuredReviewsLimit' => FeaturedReviewListingDataProvider::REVIEWS_COUNT,
                ],
            ],
        ];
    }
}
