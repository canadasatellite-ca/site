<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Test\Unit\Ui\DataProvider\Review\Modifier\Frontend;

use Aheadworks\AdvancedReviews\Ui\DataProvider\Review\Modifier\Frontend\IsVerifiedBuyer;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Aheadworks\AdvancedReviews\Api\Data\ReviewInterface;
use Aheadworks\AdvancedReviews\Model\Source\Review\IsVerifiedBuyer as IsVerifiedBuyerSource;

/**
 * Test for \Aheadworks\AdvancedReviews\Ui\DataProvider\Review\Modifier\Frontend\IsVerifiedBuyer
 */
class IsVerifiedBuyerTest extends TestCase
{
    /**
     * @var IsVerifiedBuyer
     */
    private $modifier;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->modifier = $objectManager->getObject(
            IsVerifiedBuyer::class,
            []
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
                    ReviewInterface::IS_VERIFIED_BUYER => null,
                ],
                [
                    ReviewInterface::ID => 1,
                    ReviewInterface::IS_VERIFIED_BUYER => null,
                ],
            ],
            [
                [
                    ReviewInterface::ID => 1,
                    ReviewInterface::IS_VERIFIED_BUYER => IsVerifiedBuyerSource::NO,
                ],
                [
                    ReviewInterface::ID => 1,
                    ReviewInterface::IS_VERIFIED_BUYER => "",
                ],
            ],
            [
                [
                    ReviewInterface::ID => 1,
                    ReviewInterface::IS_VERIFIED_BUYER => IsVerifiedBuyerSource::YES,
                ],
                [
                    ReviewInterface::ID => 1,
                    ReviewInterface::IS_VERIFIED_BUYER => __("Verified Buyer"),
                ],
            ],
        ];
    }
}
