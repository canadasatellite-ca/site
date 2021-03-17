<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Test\Unit\ViewModel\StaticRenderer\Reviews;

use Aheadworks\AdvancedReviews\ViewModel\StaticRenderer\Reviews\Listing;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Aheadworks\AdvancedReviews\Api\Data\ReviewInterface;
use Aheadworks\AdvancedReviews\Api\Data\CommentInterface;
use Magento\Framework\View\Element\UiComponent\DataProvider\DataProviderInterface;
use Aheadworks\AdvancedReviews\Model\Review\Rating\Resolver as ReviewRatingResolver;
use Aheadworks\AdvancedReviews\Model\ResourceModel\Review\Collection;

/**
 * Test for \Aheadworks\AdvancedReviews\ViewModel\StaticRenderer\Reviews\Listing
 */
class ListingTest extends TestCase
{
    /**
     * @var Listing
     */
    private $viewModel;

    /**
     * @var DataProviderInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $reviewDataProviderMock;

    /**
     * @var ReviewRatingResolver|\PHPUnit_Framework_MockObject_MockObject
     */
    private $reviewRatingResolverMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->reviewDataProviderMock = $this->createMock(DataProviderInterface::class);
        $this->reviewRatingResolverMock = $this->createMock(ReviewRatingResolver::class);

        $this->viewModel = $objectManager->getObject(
            Listing::class,
            [
                'reviewDataProvider' => $this->reviewDataProviderMock,
                'reviewRatingResolver' => $this->reviewRatingResolverMock,
            ]
        );
    }

    /**
     * Test for getSortableColumnsHeaders method
     */
    public function testGetBlockIdentities()
    {
        $this->assertTrue(is_array($this->viewModel->getSortableColumnsHeaders()));
    }

    /**
     * Test for getReviewRatingMaximumAbsoluteValue method
     */
    public function testGetReviewRatingMaximumAbsoluteValues()
    {
        $ratingMaximumAbsoluteValue = 5;
        $this->reviewRatingResolverMock->expects($this->once())
            ->method('getRatingMaximumAbsoluteValue')
            ->willReturn($ratingMaximumAbsoluteValue);
        $this->assertTrue(is_int($this->viewModel->getReviewRatingMaximumAbsoluteValue()));
    }

    /**
     * Test for getReviewRatingMinimumAbsoluteValue method
     */
    public function testGetReviewRatingMinimumAbsoluteValue()
    {
        $ratingMinimumAbsoluteValue = 1;
        $this->reviewRatingResolverMock->expects($this->once())
            ->method('getRatingMinimumAbsoluteValue')
            ->willReturn($ratingMinimumAbsoluteValue);
        $this->assertTrue(is_int($this->viewModel->getReviewRatingMinimumAbsoluteValue()));
    }

    /**
     * Test for getReviewsData method
     *
     * @param array $data
     * @param array $result
     * @dataProvider getReviewsDataDataProvider
     */
    public function testGetReviewsData($data, $result)
    {
        $this->reviewDataProviderMock->expects($this->once())
            ->method('addOrder')
            ->with(ReviewInterface::CREATED_AT, 'desc')
            ->willReturn(null);
        $this->reviewDataProviderMock->expects($this->once())
            ->method('getData')
            ->willReturn($data);
        $this->assertEquals($result, $this->viewModel->getReviewsData());
    }

    /**
     * @return array
     */
    public function getReviewsDataDataProvider()
    {
        return [
            [
                'data' => null,
                'result' => [],
            ],
            [
                'data' => '',
                'result' => [],
            ],
            [
                'data' => [],
                'result' => [],
            ],
            [
                'data' => [
                    'totalRecords' => 0,
                ],
                'result' => [],
            ],
            [
                'data' => [
                    'totalRecords' => 0,
                    'items' => null,
                ],
                'result' => [],
            ],
            [
                'data' => [
                    'totalRecords' => 0,
                    'items' => '',
                ],
                'result' => [],
            ],
            [
                'data' => [
                    'totalRecords' => 0,
                    'items' => [],
                ],
                'result' => [],
            ],
            [
                'data' => [
                    'totalRecords' => 0,
                    'items' => [
                        [
                            ReviewInterface::ID => 1,
                        ]
                    ],
                ],
                'result' => [
                    [
                        ReviewInterface::ID => 1,
                    ]
                ],
            ],
        ];
    }

    /**
     * Test for getReviewNickname method
     *
     * @param array $reviewsDataRow
     * @param string $result
     * @dataProvider getReviewNicknameDataProvider
     */
    public function testGetReviewNickname($reviewsDataRow, $result)
    {
        $this->assertEquals($result, $this->viewModel->getReviewNickname($reviewsDataRow));
    }

    /**
     * @return array
     */
    public function getReviewNicknameDataProvider()
    {
        return [
            [
                'reviewsDataRow' => [],
                'result' => '',
            ],
            [
                'reviewsDataRow' => [
                    ReviewInterface::NICKNAME => null,
                ],
                'result' => '',
            ],
            [
                'reviewsDataRow' => [
                    ReviewInterface::NICKNAME => '',
                ],
                'result' => '',
            ],
            [
                'reviewsDataRow' => [
                    ReviewInterface::NICKNAME => 'test nickname',
                ],
                'result' => 'test nickname',
            ],
        ];
    }

    /**
     * Test for getReviewContent method
     *
     * @param array $reviewsDataRow
     * @param string $result
     * @dataProvider getReviewContentDataProvider
     */
    public function testGetReviewContent($reviewsDataRow, $result)
    {
        $this->assertEquals($result, $this->viewModel->getReviewContent($reviewsDataRow));
    }

    /**
     * @return array
     */
    public function getReviewContentDataProvider()
    {
        return [
            [
                'reviewsDataRow' => [],
                'result' => '',
            ],
            [
                'reviewsDataRow' => [
                    ReviewInterface::CONTENT => null,
                ],
                'result' => '',
            ],
            [
                'reviewsDataRow' => [
                    ReviewInterface::CONTENT => '',
                ],
                'result' => '',
            ],
            [
                'reviewsDataRow' => [
                    ReviewInterface::CONTENT => 'test content',
                ],
                'result' => 'test content',
            ],
        ];
    }

    /**
     * Test for getReviewAdvantages method
     *
     * @param array $reviewsDataRow
     * @param string $result
     * @dataProvider getReviewAdvantagesDataProvider
     */
    public function testGetReviewAdvantages($reviewsDataRow, $result)
    {
        $this->assertEquals($result, $this->viewModel->getReviewAdvantages($reviewsDataRow));
    }

    /**
     * @return array
     */
    public function getReviewAdvantagesDataProvider()
    {
        return [
            [
                'reviewsDataRow' => [],
                'result' => '',
            ],
            [
                'reviewsDataRow' => [
                    ReviewInterface::PROS => null,
                ],
                'result' => '',
            ],
            [
                'reviewsDataRow' => [
                    ReviewInterface::PROS => '',
                ],
                'result' => '',
            ],
            [
                'reviewsDataRow' => [
                    ReviewInterface::PROS => 'test content',
                ],
                'result' => 'test content',
            ],
            [
                'reviewsDataRow' => [
                    ReviewInterface::PROS => '     ',
                ],
                'result' => '',
            ],
        ];
    }

    /**
     * Test for getReviewDisadvantages method
     *
     * @param array $reviewsDataRow
     * @param string $result
     * @dataProvider getReviewDisadvantagesDataProvider
     */
    public function testGetReviewDisadvantages($reviewsDataRow, $result)
    {
        $this->assertEquals($result, $this->viewModel->getReviewDisadvantages($reviewsDataRow));
    }

    /**
     * @return array
     */
    public function getReviewDisadvantagesDataProvider()
    {
        return [
            [
                'reviewsDataRow' => [],
                'result' => '',
            ],
            [
                'reviewsDataRow' => [
                    ReviewInterface::CONS => null,
                ],
                'result' => '',
            ],
            [
                'reviewsDataRow' => [
                    ReviewInterface::CONS => '',
                ],
                'result' => '',
            ],
            [
                'reviewsDataRow' => [
                    ReviewInterface::CONS => 'test content',
                ],
                'result' => 'test content',
            ],
            [
                'reviewsDataRow' => [
                    ReviewInterface::CONS => '     ',
                ],
                'result' => '',
            ],
        ];
    }

    /**
     * Test for getReviewSummary method
     *
     * @param array $reviewsDataRow
     * @param string $result
     * @dataProvider getReviewSummaryDataProvider
     */
    public function testGetReviewSummary($reviewsDataRow, $result)
    {
        $this->assertEquals($result, $this->viewModel->getReviewSummary($reviewsDataRow));
    }

    /**
     * @return array
     */
    public function getReviewSummaryDataProvider()
    {
        return [
            [
                'reviewsDataRow' => [],
                'result' => '',
            ],
            [
                'reviewsDataRow' => [
                    ReviewInterface::SUMMARY => null,
                ],
                'result' => '',
            ],
            [
                'reviewsDataRow' => [
                    ReviewInterface::SUMMARY => '',
                ],
                'result' => '',
            ],
            [
                'reviewsDataRow' => [
                    ReviewInterface::SUMMARY => 'test summary',
                ],
                'result' => 'test summary',
            ],
        ];
    }

    /**
     * Test for getReviewVerifiedBuyerLabel method
     *
     * @param array $reviewsDataRow
     * @param string $result
     * @dataProvider getReviewVerifiedBuyerLabelDataProvider
     */
    public function testGetReviewVerifiedBuyerLabel($reviewsDataRow, $result)
    {
        $this->assertEquals($result, $this->viewModel->getReviewVerifiedBuyerLabel($reviewsDataRow));
    }

    /**
     * @return array
     */
    public function getReviewVerifiedBuyerLabelDataProvider()
    {
        return [
            [
                'reviewsDataRow' => [],
                'result' => '',
            ],
            [
                'reviewsDataRow' => [
                    ReviewInterface::IS_VERIFIED_BUYER => null,
                ],
                'result' => '',
            ],
            [
                'reviewsDataRow' => [
                    ReviewInterface::IS_VERIFIED_BUYER => '',
                ],
                'result' => '',
            ],
            [
                'reviewsDataRow' => [
                    ReviewInterface::IS_VERIFIED_BUYER => 'test label',
                ],
                'result' => 'test label',
            ],
        ];
    }

    /**
     * Test for getReviewProductRecommendedLabel method
     *
     * @param array $reviewsDataRow
     * @param string $result
     * @dataProvider getReviewProductRecommendedLabelDataProvider
     */
    public function testGetReviewProductRecommendedLabel($reviewsDataRow, $result)
    {
        $this->assertEquals($result, $this->viewModel->getReviewProductRecommendedLabel($reviewsDataRow));
    }

    /**
     * @return array
     */
    public function getReviewProductRecommendedLabelDataProvider()
    {
        return [
            [
                'reviewsDataRow' => [],
                'result' => '',
            ],
            [
                'reviewsDataRow' => [
                    ReviewInterface::PRODUCT_RECOMMENDED . '_label' => null,
                ],
                'result' => '',
            ],
            [
                'reviewsDataRow' => [
                    ReviewInterface::PRODUCT_RECOMMENDED . '_label' => '',
                ],
                'result' => '',
            ],
            [
                'reviewsDataRow' => [
                    ReviewInterface::PRODUCT_RECOMMENDED . '_label' => 'test label',
                ],
                'result' => 'test label',
            ],
        ];
    }

    /**
     * Test for getReviewRatingAbsoluteValue method
     *
     * @param array $reviewsDataRow
     * @param string $result
     * @dataProvider getReviewRatingAbsoluteValueDataProvider
     */
    public function testGetReviewRatingAbsoluteValue($reviewsDataRow, $result)
    {
        $this->assertEquals($result, $this->viewModel->getReviewRatingAbsoluteValue($reviewsDataRow));
    }

    /**
     * @return array
     */
    public function getReviewRatingAbsoluteValueDataProvider()
    {
        return [
            [
                'reviewsDataRow' => [],
                'result' => '',
            ],
            [
                'reviewsDataRow' => [
                    ReviewInterface::RATING . '_absolute_value' => null,
                ],
                'result' => '',
            ],
            [
                'reviewsDataRow' => [
                    ReviewInterface::RATING . '_absolute_value' => '',
                ],
                'result' => '',
            ],
            [
                'reviewsDataRow' => [
                    ReviewInterface::RATING . '_absolute_value' => '80',
                ],
                'result' => '80',
            ],
        ];
    }

    /**
     * Test for getReviewProductUrl method
     *
     * @param array $reviewsDataRow
     * @param string $result
     * @dataProvider getReviewProductUrlDataProvider
     */
    public function testGetReviewProductUrl($reviewsDataRow, $result)
    {
        $this->assertEquals($result, $this->viewModel->getReviewProductUrl($reviewsDataRow));
    }

    /**
     * @return array
     */
    public function getReviewProductUrlDataProvider()
    {
        return [
            [
                'reviewsDataRow' => [],
                'result' => '',
            ],
            [
                'reviewsDataRow' => [
                    Collection::PRODUCT_NAME_COLUMN_NAME . '_url' => null,
                ],
                'result' => '',
            ],
            [
                'reviewsDataRow' => [
                    Collection::PRODUCT_NAME_COLUMN_NAME . '_url' => '',
                ],
                'result' => '',
            ],
            [
                'reviewsDataRow' => [
                    Collection::PRODUCT_NAME_COLUMN_NAME . '_url' => 'http://magento.local/product-url',
                ],
                'result' => 'http://magento.local/product-url',
            ],
        ];
    }

    /**
     * Test for getReviewProductLabel method
     *
     * @param array $reviewsDataRow
     * @param string $result
     * @dataProvider getReviewProductLabelDataProvider
     */
    public function testGetReviewProductLabel($reviewsDataRow, $result)
    {
        $this->assertEquals($result, $this->viewModel->getReviewProductLabel($reviewsDataRow));
    }

    /**
     * @return array
     */
    public function getReviewProductLabelDataProvider()
    {
        return [
            [
                'reviewsDataRow' => [],
                'result' => '',
            ],
            [
                'reviewsDataRow' => [
                    Collection::PRODUCT_NAME_COLUMN_NAME . '_label' => null,
                ],
                'result' => '',
            ],
            [
                'reviewsDataRow' => [
                    Collection::PRODUCT_NAME_COLUMN_NAME . '_label' => '',
                ],
                'result' => '',
            ],
            [
                'reviewsDataRow' => [
                    Collection::PRODUCT_NAME_COLUMN_NAME . '_label' => 'test product label',
                ],
                'result' => 'test product label',
            ],
        ];
    }

    /**
     * Test for getReviewCreatedAt method
     *
     * @param array $reviewsDataRow
     * @param string $result
     * @dataProvider getReviewCreatedAtDataProvider
     */
    public function testGetReviewCreatedAt($reviewsDataRow, $result)
    {
        $this->assertEquals($result, $this->viewModel->getReviewCreatedAt($reviewsDataRow));
    }

    /**
     * @return array
     */
    public function getReviewCreatedAtDataProvider()
    {
        return [
            [
                'reviewsDataRow' => [],
                'result' => '',
            ],
            [
                'reviewsDataRow' => [
                    ReviewInterface::CREATED_AT => null,
                ],
                'result' => '',
            ],
            [
                'reviewsDataRow' => [
                    ReviewInterface::CREATED_AT => '',
                ],
                'result' => '',
            ],
            [
                'reviewsDataRow' => [
                    ReviewInterface::CREATED_AT => 'localized formatted date',
                ],
                'result' => 'localized formatted date',
            ],
        ];
    }

    /**
     * Test for getReviewCreatedAtInIsoFormat method
     *
     * @param array $reviewsDataRow
     * @param string $result
     * @dataProvider getReviewCreatedAtInIsoFormatDataProvider
     */
    public function testGetReviewCreatedAtInIsoFormat($reviewsDataRow, $result)
    {
        $this->assertEquals($result, $this->viewModel->getReviewCreatedAtInIsoFormat($reviewsDataRow));
    }

    /**
     * @return array
     */
    public function getReviewCreatedAtInIsoFormatDataProvider()
    {
        return [
            [
                'reviewsDataRow' => [],
                'result' => '',
            ],
            [
                'reviewsDataRow' => [
                    ReviewInterface::CREATED_AT . '_in_iso_format' => null,
                ],
                'result' => '',
            ],
            [
                'reviewsDataRow' => [
                    ReviewInterface::CREATED_AT . '_in_iso_format' => '',
                ],
                'result' => '',
            ],
            [
                'reviewsDataRow' => [
                    ReviewInterface::CREATED_AT . '_in_iso_format' => 'localized_date_in_iso_format',
                ],
                'result' => 'localized_date_in_iso_format',
            ],
        ];
    }

    /**
     * Test for getReviewVotesPositive method
     *
     * @param array $reviewsDataRow
     * @param string $result
     * @dataProvider getReviewVotesPositiveDataProvider
     */
    public function testGetReviewVotesPositive($reviewsDataRow, $result)
    {
        $this->assertEquals($result, $this->viewModel->getReviewVotesPositive($reviewsDataRow));
    }

    /**
     * @return array
     */
    public function getReviewVotesPositiveDataProvider()
    {
        return [
            [
                'reviewsDataRow' => [],
                'result' => '',
            ],
            [
                'reviewsDataRow' => [
                    ReviewInterface::VOTES_POSITIVE => null,
                ],
                'result' => '',
            ],
            [
                'reviewsDataRow' => [
                    ReviewInterface::VOTES_POSITIVE => '',
                ],
                'result' => '',
            ],
            [
                'reviewsDataRow' => [
                    ReviewInterface::VOTES_POSITIVE => '10',
                ],
                'result' => '10',
            ],
        ];
    }

    /**
     * Test for getReviewVotesNegative method
     *
     * @param array $reviewsDataRow
     * @param string $result
     * @dataProvider getReviewVotesNegativeDataProvider
     */
    public function testGetReviewVotesNegative($reviewsDataRow, $result)
    {
        $this->assertEquals($result, $this->viewModel->getReviewVotesNegative($reviewsDataRow));
    }

    /**
     * @return array
     */
    public function getReviewVotesNegativeDataProvider()
    {
        return [
            [
                'reviewsDataRow' => [],
                'result' => '',
            ],
            [
                'reviewsDataRow' => [
                    ReviewInterface::VOTES_NEGATIVE => null,
                ],
                'result' => '',
            ],
            [
                'reviewsDataRow' => [
                    ReviewInterface::VOTES_NEGATIVE => '',
                ],
                'result' => '',
            ],
            [
                'reviewsDataRow' => [
                    ReviewInterface::VOTES_NEGATIVE => '19',
                ],
                'result' => '19',
            ],
        ];
    }

    /**
     * Test for getReviewAttachments method
     *
     * @param array $reviewsDataRow
     * @param array $result
     * @dataProvider getReviewAttachmentsDataProvider
     */
    public function testGetReviewAttachments($reviewsDataRow, $result)
    {
        $this->assertSame($result, $this->viewModel->getReviewAttachments($reviewsDataRow));
    }

    /**
     * @return array
     */
    public function getReviewAttachmentsDataProvider()
    {
        return [
            [
                'reviewsDataRow' => [],
                'result' => [],
            ],
            [
                'reviewsDataRow' => [
                    ReviewInterface::ATTACHMENTS => null,
                ],
                'result' => [],
            ],
            [
                'reviewsDataRow' => [
                    ReviewInterface::ATTACHMENTS => '',
                ],
                'result' => [],
            ],
            [
                'reviewsDataRow' => [
                    ReviewInterface::ATTACHMENTS => '19',
                ],
                'result' => [],
            ],
            [
                'reviewsDataRow' => [
                    ReviewInterface::ATTACHMENTS => [],
                ],
                'result' => [],
            ],
            [
                'reviewsDataRow' => [
                    ReviewInterface::ATTACHMENTS => [
                        [
                            'name' => 'attachment name',
                        ]
                    ],
                ],
                'result' => [
                    [
                        'name' => 'attachment name',
                    ]
                ],
            ],
        ];
    }

    /**
     * Test for getReviewAttachmentUrl method
     *
     * @param array $reviewAttachmentData
     * @param string $result
     * @dataProvider getReviewAttachmentUrlDataProvider
     */
    public function testGetReviewAttachmentUrl($reviewAttachmentData, $result)
    {
        $this->assertEquals($result, $this->viewModel->getReviewAttachmentUrl($reviewAttachmentData));
    }

    /**
     * @return array
     */
    public function getReviewAttachmentUrlDataProvider()
    {
        return [
            [
                'reviewAttachmentData' => [],
                'result' => '',
            ],
            [
                'reviewAttachmentData' => [
                    'url' => null,
                ],
                'result' => '',
            ],
            [
                'reviewAttachmentData' => [
                    'url' => '',
                ],
                'result' => '',
            ],
            [
                'reviewAttachmentData' => [
                    'url' => 'http://magento.local/attachment-url',
                ],
                'result' => 'http://magento.local/attachment-url',
            ],
        ];
    }

    /**
     * Test for getReviewAttachmentTitle method
     *
     * @param array $reviewAttachmentData
     * @param string $result
     * @dataProvider getReviewAttachmentTitleDataProvider
     */
    public function testGetReviewAttachmentTitle($reviewAttachmentData, $result)
    {
        $this->assertEquals($result, $this->viewModel->getReviewAttachmentTitle($reviewAttachmentData));
    }

    /**
     * @return array
     */
    public function getReviewAttachmentTitleDataProvider()
    {
        return [
            [
                'reviewAttachmentData' => [],
                'result' => '',
            ],
            [
                'reviewAttachmentData' => [
                    'type' => null,
                ],
                'result' => '',
            ],
            [
                'reviewAttachmentData' => [
                    'type' => '',
                ],
                'result' => '',
            ],
            [
                'reviewAttachmentData' => [
                    'type' => 'attachment type',
                ],
                'result' => '',
            ],
            [
                'reviewAttachmentData' => [
                    'type' => 'image/png',
                ],
                'result' => '',
            ],
            [
                'reviewAttachmentData' => [
                    'type' => 'video/quicktime',
                ],
                'result' => '',
            ],
            [
                'reviewAttachmentData' => [
                    'type' => 'audio/mpeg',
                ],
                'result' => '',
            ],
            [
                'reviewAttachmentData' => [
                    'type' => 'image/jpeg',
                ],
                'result' => '',
            ],
            [
                'reviewAttachmentData' => [
                    'type' => 'image/jpeg',
                    'image_title' => null,
                ],
                'result' => '',
            ],
            [
                'reviewAttachmentData' => [
                    'type' => 'image/jpeg',
                    'image_title' => '',
                ],
                'result' => '',
            ],
            [
                'reviewAttachmentData' => [
                    'type' => 'image/jpeg',
                    'image_title' => 'test image title',
                ],
                'result' => 'test image title',
            ],
        ];
    }

    /**
     * Test for isReviewAttachmentImage method
     *
     * @param array $reviewAttachmentData
     * @param bool $result
     * @dataProvider isReviewAttachmentImageDataProvider
     */
    public function testIsReviewAttachmentImage($reviewAttachmentData, $result)
    {
        $this->assertEquals($result, $this->viewModel->isReviewAttachmentImage($reviewAttachmentData));
    }

    /**
     * @return array
     */
    public function isReviewAttachmentImageDataProvider()
    {
        return [
            [
                'reviewAttachmentData' => [],
                'result' => false,
            ],
            [
                'reviewAttachmentData' => [
                    'type' => null,
                ],
                'result' => false,
            ],
            [
                'reviewAttachmentData' => [
                    'type' => '',
                ],
                'result' => false,
            ],
            [
                'reviewAttachmentData' => [
                    'type' => 'attachment type',
                ],
                'result' => false,
            ],
            [
                'reviewAttachmentData' => [
                    'type' => 'image/png',
                ],
                'result' => true,
            ],
            [
                'reviewAttachmentData' => [
                    'type' => 'image/jpeg',
                ],
                'result' => true,
            ],
            [
                'reviewAttachmentData' => [
                    'type' => 'video/quicktime',
                ],
                'result' => false,
            ],
            [
                'reviewAttachmentData' => [
                    'type' => 'audio/mpeg',
                ],
                'result' => false,
            ],
        ];
    }

    /**
     * Test for getReviewComments method
     *
     * @param array $reviewsDataRow
     * @param array $result
     * @dataProvider getReviewCommentsDataProvider
     */
    public function testGetReviewComments($reviewsDataRow, $result)
    {
        $this->assertSame($result, $this->viewModel->getReviewComments($reviewsDataRow));
    }

    /**
     * @return array
     */
    public function getReviewCommentsDataProvider()
    {
        return [
            [
                'reviewsDataRow' => [],
                'result' => [],
            ],
            [
                'reviewsDataRow' => [
                    ReviewInterface::COMMENTS => null,
                ],
                'result' => [],
            ],
            [
                'reviewsDataRow' => [
                    ReviewInterface::COMMENTS => '',
                ],
                'result' => [],
            ],
            [
                'reviewsDataRow' => [
                    ReviewInterface::COMMENTS => '19',
                ],
                'result' => [],
            ],
            [
                'reviewsDataRow' => [
                    ReviewInterface::COMMENTS => [],
                ],
                'result' => [],
            ],
            [
                'reviewsDataRow' => [
                    ReviewInterface::COMMENTS => [
                        [
                            CommentInterface::CONTENT => 'comment content',
                        ]
                    ],
                ],
                'result' => [
                    [
                        CommentInterface::CONTENT => 'comment content',
                    ]
                ],
            ],
        ];
    }

    /**
     * Test for getReviewCommentNickname method
     *
     * @param array $reviewCommentData
     * @param string $result
     * @dataProvider getReviewCommentNicknameDataProvider
     */
    public function testGetReviewCommentNickname($reviewCommentData, $result)
    {
        $this->assertEquals($result, $this->viewModel->getReviewCommentNickname($reviewCommentData));
    }

    /**
     * @return array
     */
    public function getReviewCommentNicknameDataProvider()
    {
        return [
            [
                'reviewCommentData' => [],
                'result' => '',
            ],
            [
                'reviewCommentData' => [
                    CommentInterface::NICKNAME => null,
                ],
                'result' => '',
            ],
            [
                'reviewCommentData' => [
                    CommentInterface::NICKNAME => '',
                ],
                'result' => '',
            ],
            [
                'reviewCommentData' => [
                    CommentInterface::NICKNAME => 'test nickname',
                ],
                'result' => 'test nickname',
            ],
        ];
    }

    /**
     * Test for getReviewCommentContent method
     *
     * @param array $reviewCommentData
     * @param string $result
     * @dataProvider getReviewCommentContentDataProvider
     */
    public function testGetReviewCommentContent($reviewCommentData, $result)
    {
        $this->assertEquals($result, $this->viewModel->getReviewCommentContent($reviewCommentData));
    }

    /**
     * @return array
     */
    public function getReviewCommentContentDataProvider()
    {
        return [
            [
                'reviewCommentData' => [],
                'result' => '',
            ],
            [
                'reviewCommentData' => [
                    CommentInterface::CONTENT => null,
                ],
                'result' => '',
            ],
            [
                'reviewCommentData' => [
                    CommentInterface::CONTENT => '',
                ],
                'result' => '',
            ],
            [
                'reviewCommentData' => [
                    CommentInterface::CONTENT => 'test content',
                ],
                'result' => 'test content',
            ],
        ];
    }

    /**
     * Test for getReviewCommentCreatedAt method
     *
     * @param array $reviewCommentData
     * @param string $result
     * @dataProvider getReviewCommentCreatedAtDataProvider
     */
    public function testGetReviewCommentCreatedAt($reviewCommentData, $result)
    {
        $this->assertEquals($result, $this->viewModel->getReviewCommentCreatedAt($reviewCommentData));
    }

    /**
     * @return array
     */
    public function getReviewCommentCreatedAtDataProvider()
    {
        return [
            [
                'reviewCommentData' => [],
                'result' => '',
            ],
            [
                'reviewCommentData' => [
                    CommentInterface::CREATED_AT => null,
                ],
                'result' => '',
            ],
            [
                'reviewCommentData' => [
                    CommentInterface::CREATED_AT => '',
                ],
                'result' => '',
            ],
            [
                'reviewCommentData' => [
                    CommentInterface::CREATED_AT => 'localized formatted date',
                ],
                'result' => 'localized formatted date',
            ],
        ];
    }
}
