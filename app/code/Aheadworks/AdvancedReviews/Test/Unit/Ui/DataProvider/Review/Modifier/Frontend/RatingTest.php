<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Test\Unit\Ui\DataProvider\Review\Modifier\Frontend;

use Aheadworks\AdvancedReviews\Ui\DataProvider\Review\Modifier\Frontend\Rating;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Aheadworks\AdvancedReviews\Api\Data\ReviewInterface;
use Aheadworks\AdvancedReviews\Model\Review\Rating\Resolver as RatingResolver;

/**
 * Test for \Aheadworks\AdvancedReviews\Ui\DataProvider\Review\Modifier\Frontend\Rating
 */
class RatingTest extends TestCase
{
    /**
     * @var Rating
     */
    private $modifier;

    /**
     * @var RatingResolver|\PHPUnit_Framework_MockObject_MockObject
     */
    private $ratingResolverMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->ratingResolverMock = $this->createMock(RatingResolver::class);

        $this->modifier = $objectManager->getObject(
            Rating::class,
            [
                'ratingResolver' => $this->ratingResolverMock,
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
        $this->ratingResolverMock->expects($this->any())
            ->method('getRatingTitle')
            ->willReturn('rating title string');
        $this->ratingResolverMock->expects($this->any())
            ->method('getRatingAbsoluteValue')
            ->willReturn(4.0);

        $this->assertSame($result, $this->modifier->modifyData($data));
    }

    /**
     * @return array
     */
    public function modifyDataDataProvider()
    {
        return [
            [
                [],
                []
            ],
            [
                [
                    ReviewInterface::ID => null,
                ],
                [
                    ReviewInterface::ID => null,
                ],
            ],
            [
                [
                    ReviewInterface::ID => '',
                ],
                [
                    ReviewInterface::ID => '',
                ],
            ],
            [
                [
                    ReviewInterface::ID => 1,
                    ReviewInterface::RATING => null,
                ],
                [
                    ReviewInterface::ID => 1,
                    ReviewInterface::RATING => null,
                ],
            ],
            [
                [
                    ReviewInterface::ID => 1,
                    ReviewInterface::RATING => 80,
                ],
                [
                    ReviewInterface::ID => 1,
                    ReviewInterface::RATING => 80,
                    ReviewInterface::RATING . '_title' => 'rating title string',
                    ReviewInterface::RATING . '_absolute_value' => 4.0,
                ],
            ],
        ];
    }
}
