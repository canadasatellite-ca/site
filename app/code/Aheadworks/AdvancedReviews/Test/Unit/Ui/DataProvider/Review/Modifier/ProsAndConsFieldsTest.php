<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Test\Unit\Ui\DataProvider\Review\Modifier;

use Aheadworks\AdvancedReviews\Ui\DataProvider\Review\Modifier\ProsAndConsFields;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Aheadworks\AdvancedReviews\Model\Config;
use Aheadworks\AdvancedReviews\Model\Store\Resolver as StoreResolver;
use Aheadworks\AdvancedReviews\Api\Data\ReviewInterface;

/**
 * Test for \Aheadworks\AdvancedReviews\Ui\DataProvider\Review\Modifier\ProsAndConsFields
 */
class ProsAndConsFieldsTest extends TestCase
{
    /**
     * @var ProsAndConsFields
     */
    private $modifier;

    /**
     * @var Config|\PHPUnit_Framework_MockObject_MockObject
     */
    private $configMock;

    /**
     * @var StoreResolver|\PHPUnit_Framework_MockObject_MockObject
     */
    private $storeResolverMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->configMock = $this->createMock(Config::class);
        $this->storeResolverMock = $this->createMock(StoreResolver::class);

        $this->modifier = $objectManager->getObject(
            ProsAndConsFields::class,
            [
                'config' => $this->configMock,
                'storeResolver' => $this->storeResolverMock,
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
        $this->storeResolverMock->expects($this->once())
            ->method('getWebsiteIdByStoreId')
            ->willReturnMap(
                [
                    [
                        null,
                        null,
                    ],
                    [
                        1,
                        1,
                    ],
                ]
            );

        $this->configMock->expects($this->once())
            ->method('areProsAndConsEnabled')
            ->willReturnMap(
                [
                    [
                        null,
                        true,
                    ],
                    [
                        1,
                        false,
                    ],
                ]
            );

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
                    'areProsConsEnabledForReviewStore' => true,
                ],
            ],
            [
                [
                    ReviewInterface::ID => null,
                ],
                [
                    ReviewInterface::ID => null,
                    'areProsConsEnabledForReviewStore' => true,
                ],
            ],
            [
                [
                    ReviewInterface::ID => '',
                ],
                [
                    ReviewInterface::ID => '',
                    'areProsConsEnabledForReviewStore' => true,
                ],
            ],
            [
                [
                    ReviewInterface::ID => 1,
                ],
                [
                    ReviewInterface::ID => 1,
                    'areProsConsEnabledForReviewStore' => true,
                ],
            ],

            [
                [
                    ReviewInterface::ID => 1,
                    ReviewInterface::STORE_ID => 1,
                ],
                [
                    ReviewInterface::ID => 1,
                    ReviewInterface::STORE_ID => 1,
                    'areProsConsEnabledForReviewStore' => false,
                ],
            ],
        ];
    }
}
