<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Test\Unit\Ui\DataProvider\Review\Modifier\Frontend\StaticRenderer;

use Aheadworks\AdvancedReviews\Ui\DataProvider\Review\Modifier\Frontend\StaticRenderer\ProductRecommended;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Aheadworks\AdvancedReviews\Api\Data\ReviewInterface;
use Aheadworks\AdvancedReviews\Model\Source\Review\ProductRecommended as ProductRecommendedSource;

/**
 * Test for \Aheadworks\AdvancedReviews\Ui\DataProvider\Review\Modifier\Frontend\StaticRenderer\ProductRecommended
 */
class ProductRecommendedTest extends TestCase
{
    /**
     * @var ProductRecommended
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
            ProductRecommended::class,
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
                    ReviewInterface::PRODUCT_RECOMMENDED => null,
                ],
                [
                    ReviewInterface::ID => 1,
                    ReviewInterface::PRODUCT_RECOMMENDED => null,
                ],
            ],
            [
                [
                    ReviewInterface::ID => 1,
                    ReviewInterface::PRODUCT_RECOMMENDED => ProductRecommendedSource::NOT_SPECIFIED,
                ],
                [
                    ReviewInterface::ID => 1,
                    ReviewInterface::PRODUCT_RECOMMENDED => ProductRecommendedSource::NOT_SPECIFIED,
                    ReviewInterface::PRODUCT_RECOMMENDED . '_label' => "",
                ],
            ],
            [
                [
                    ReviewInterface::ID => 1,
                    ReviewInterface::PRODUCT_RECOMMENDED => ProductRecommendedSource::NO,
                ],
                [
                    ReviewInterface::ID => 1,
                    ReviewInterface::PRODUCT_RECOMMENDED => ProductRecommendedSource::NO,
                    ReviewInterface::PRODUCT_RECOMMENDED . '_label' => __("I don't recommend this product"),
                ],
            ],
            [
                [
                    ReviewInterface::ID => 1,
                    ReviewInterface::PRODUCT_RECOMMENDED => ProductRecommendedSource::YES,
                ],
                [
                    ReviewInterface::ID => 1,
                    ReviewInterface::PRODUCT_RECOMMENDED => ProductRecommendedSource::YES,
                    ReviewInterface::PRODUCT_RECOMMENDED . '_label' => __("I recommend this product"),
                ],
            ],
        ];
    }
}
