<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Test\Unit\Ui\DataProvider\Review\Modifier\Frontend;

use Aheadworks\AdvancedReviews\Ui\DataProvider\Review\Modifier\Frontend\Attachments;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Aheadworks\AdvancedReviews\Api\Data\ReviewInterface;
use Aheadworks\AdvancedReviews\Model\ResourceModel\Review\Collection;

/**
 * Test for \Aheadworks\AdvancedReviews\Ui\DataProvider\Review\Modifier\Frontend\Attachments
 */
class AttachmentsTest extends TestCase
{
    /**
     * @var Attachments
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
            Attachments::class,
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
                ],
                [
                    ReviewInterface::ID => 1,
                    ReviewInterface::ATTACHMENTS => []
                ],
            ],
            [
                [
                    ReviewInterface::ID => 1,
                    ReviewInterface::ATTACHMENTS => []
                ],
                [
                    ReviewInterface::ID => 1,
                    ReviewInterface::ATTACHMENTS => []
                ],
            ],
            [
                [
                    ReviewInterface::ID => 1,
                    ReviewInterface::ATTACHMENTS => [
                        [
                            'name' => 'attachment 1 name',
                        ],
                        [
                            'name' => 'attachment 2 name',
                        ],
                    ]
                ],
                [
                    ReviewInterface::ID => 1,
                    ReviewInterface::ATTACHMENTS => [
                        [
                            'name' => 'attachment 1 name',
                            'image_title' => __("A real photo") . " (1)",
                        ],
                        [
                            'name' => 'attachment 2 name',
                            'image_title' => __("A real photo") . " (2)",
                        ],
                    ]
                ],
            ],
            [
                [
                    ReviewInterface::ID => 1,
                    Collection::PRODUCT_NAME_COLUMN_NAME . '_label' => "product name",
                    ReviewInterface::ATTACHMENTS => [
                        [
                            'name' => 'attachment 1 name',
                        ],
                        [
                            'name' => 'attachment 2 name',
                        ],
                    ]
                ],
                [
                    ReviewInterface::ID => 1,
                    Collection::PRODUCT_NAME_COLUMN_NAME . '_label' => "product name",
                    ReviewInterface::ATTACHMENTS => [
                        [
                            'name' => 'attachment 1 name',
                            'image_title' => __("A real photo") . __(" of product name") . " (1)",
                        ],
                        [
                            'name' => 'attachment 2 name',
                            'image_title' => __("A real photo") . __(" of product name") . " (2)",
                        ],
                    ]
                ],
            ],
            [
                [
                    ReviewInterface::ID => 1,
                    ReviewInterface::NICKNAME => "author nickname",
                    ReviewInterface::ATTACHMENTS => [
                        [
                            'name' => 'attachment 1 name',
                        ],
                        [
                            'name' => 'attachment 2 name',
                        ],
                    ]
                ],
                [
                    ReviewInterface::ID => 1,
                    ReviewInterface::NICKNAME => "author nickname",
                    ReviewInterface::ATTACHMENTS => [
                        [
                            'name' => 'attachment 1 name',
                            'image_title' => __("A real photo") . __(" by author nickname") . " (1)",
                        ],
                        [
                            'name' => 'attachment 2 name',
                            'image_title' => __("A real photo") . __(" by author nickname") . " (2)",
                        ],
                    ]
                ],
            ],
            [
                [
                    ReviewInterface::ID => 1,
                    ReviewInterface::NICKNAME => "author nickname",
                    Collection::PRODUCT_NAME_COLUMN_NAME . '_label' => "product name",
                    ReviewInterface::ATTACHMENTS => [
                        [
                            'name' => 'attachment 1 name',
                        ],
                        [
                            'name' => 'attachment 2 name',
                        ],
                    ]
                ],
                [
                    ReviewInterface::ID => 1,
                    ReviewInterface::NICKNAME => "author nickname",
                    Collection::PRODUCT_NAME_COLUMN_NAME . '_label' => "product name",
                    ReviewInterface::ATTACHMENTS => [
                        [
                            'name' => 'attachment 1 name',
                            'image_title' => __("A real photo")
                                . __(" of product name")
                                . __(" by author nickname")
                                . " (1)",
                        ],
                        [
                            'name' => 'attachment 2 name',
                            'image_title' => __("A real photo")
                                . __(" of product name")
                                . __(" by author nickname")
                                . " (2)",
                        ],
                    ]
                ],
            ],
        ];
    }
}
