<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Test\Unit\Model\Review\Rating;

use Aheadworks\AdvancedReviews\Model\Review\Rating\Resolver;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\TestCase;
use Aheadworks\AdvancedReviews\Model\Source\Review\RatingValue;

/**
 * Class ResolverTest
 *
 * @package Aheadworks\AdvancedReviews\Test\Unit\Model\Review\Rating
 */
class ResolverTest extends TestCase
{
    /**
     * @var Resolver
     */
    private $model;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    protected function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->model = $objectManager->getObject(
            Resolver::class
        );
    }

    /**
     * Testing of getRatingAbsoluteValue method
     *
     * @param int $ratingPercent
     * @param int $precision
     * @param float $ratingAbsoluteValue
     * @dataProvider testGetRatingAbsoluteValueDataProvider
     */
    public function testGetRatingAbsoluteValue($ratingPercent, $precision, $ratingAbsoluteValue)
    {
        $this->assertEquals(
            $ratingAbsoluteValue,
            $this->model->getRatingAbsoluteValue(
                $ratingPercent,
                $precision
            ),
            '',
            0.1
        );
    }

    /**
     * Data provider for getRatingAbsoluteValue
     *
     * @return array
     */
    public function testGetRatingAbsoluteValueDataProvider()
    {
        return [
            [
                50,
                1,
                2.5
            ],
            [
                50,
                0,
                3.0
            ],
            [
                90,
                1,
                4.5
            ],
            [
                60,
                1,
                3.0
            ],
            [
                60,
                0,
                3.0
            ],
        ];
    }

    /**
     * Testing of getRatingTitle method
     *
     * @param int $ratingPercent
     * @param string $ratingTitle
     * @dataProvider testGetRatingTitleDataProvider
     */
    public function testGetRatingTitle($ratingPercent, $ratingTitle)
    {
        $this->assertEquals(
            $ratingTitle,
            $this->model->getRatingTitle(
                $ratingPercent
            )
        );
    }

    /**
     * Data provider for getRatingAbsoluteValue
     *
     * @return array
     */
    public function testGetRatingTitleDataProvider()
    {
        return [
            [
                50,
                "2.5 out of " . RatingValue::VALUES_COUNT . " stars",
            ],
            [
                90,
                "4.5 out of " . RatingValue::VALUES_COUNT . " stars",
            ],
            [
                60,
                "3 out of " . RatingValue::VALUES_COUNT . " stars",
            ],
        ];
    }

    /**
     * Testing of getRatingMaximumAbsoluteValue method
     */
    public function testGetRatingMaximumAbsoluteValue()
    {
        $this->assertTrue(is_int($this->model->getRatingMaximumAbsoluteValue()));
    }

    /**
     * Testing of getRatingMinimumAbsoluteValue method
     */
    public function testGetRatingMinimumAbsoluteValue()
    {
        $this->assertTrue(is_int($this->model->getRatingMinimumAbsoluteValue()));
    }
}
