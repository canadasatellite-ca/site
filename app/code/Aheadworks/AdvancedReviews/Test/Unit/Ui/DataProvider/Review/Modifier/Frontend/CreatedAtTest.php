<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Test\Unit\Ui\DataProvider\Review\Modifier\Frontend;

use Aheadworks\AdvancedReviews\Ui\DataProvider\Review\Modifier\Frontend\CreatedAt;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Aheadworks\AdvancedReviews\Api\Data\ReviewInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Aheadworks\AdvancedReviews\Model\DateTime\Formatter as DateTimeFormatter;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Framework\Stdlib\DateTime as StdlibDateTime;

/**
 * Test for \Aheadworks\AdvancedReviews\Ui\DataProvider\Review\Modifier\Frontend\CreatedAt
 */
class CreatedAtTest extends TestCase
{
    /**
     * @var CreatedAt
     */
    private $modifier;

    /**
     * @var DateTimeFormatter|\PHPUnit_Framework_MockObject_MockObject
     */
    private $dateTimeFormatterMock;

    /**
     * @var StoreManagerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $storeManagerMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->dateTimeFormatterMock = $this->createMock(DateTimeFormatter::class);
        $this->storeManagerMock = $this->createMock(StoreManagerInterface::class);

        $this->modifier = $objectManager->getObject(
            CreatedAt::class,
            [
                'dateTimeFormatter' => $this->dateTimeFormatterMock,
                'storeManager' => $this->storeManagerMock,
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
        $currentStoreId = 1;
        $dateFormat = \IntlDateFormatter::MEDIUM;

        $this->setProperty('dateFormat', $dateFormat);

        $storeMock = $this->createMock(StoreInterface::class);
        $storeMock->expects($this->any())
            ->method('getId')
            ->willReturn($currentStoreId);
        $this->storeManagerMock->expects($this->any())
            ->method('getStore')
            ->with(true)
            ->willReturn($storeMock);

        $this->dateTimeFormatterMock->expects($this->any())
            ->method('getLocalizedDate')
            ->willReturnMap(
                [
                    [
                        "",
                        $currentStoreId,
                        $dateFormat,
                        'current_localized_date',
                    ],
                    [
                        'date_time_in_db_format',
                        $currentStoreId,
                        $dateFormat,
                        'localized_date',
                    ]
                ]
            );

        $this->dateTimeFormatterMock->expects($this->any())
            ->method('getLocalizedDateTime')
            ->willReturnMap(
                [
                    [
                        "",
                        $currentStoreId,
                        StdlibDateTime::DATE_INTERNAL_FORMAT,
                        'current_localized_date_in_iso_format',
                    ],
                    [
                        'date_time_in_db_format',
                        $currentStoreId,
                        StdlibDateTime::DATE_INTERNAL_FORMAT,
                        'localized_date_in_iso_format',
                    ]
                ]
            );

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
                    ReviewInterface::CREATED_AT => null,
                ],
                [
                    ReviewInterface::ID => 1,
                    ReviewInterface::CREATED_AT => null,
                ],
            ],
            [
                [
                    ReviewInterface::ID => 1,
                    ReviewInterface::CREATED_AT => '',
                ],
                [
                    ReviewInterface::ID => 1,
                    ReviewInterface::CREATED_AT => 'current_localized_date',
                    ReviewInterface::CREATED_AT . '_in_iso_format' => 'current_localized_date_in_iso_format',
                ],
            ],
            [
                [
                    ReviewInterface::ID => 1,
                    ReviewInterface::CREATED_AT => 'date_time_in_db_format',
                ],
                [
                    ReviewInterface::ID => 1,
                    ReviewInterface::CREATED_AT => 'localized_date',
                    ReviewInterface::CREATED_AT . '_in_iso_format' => 'localized_date_in_iso_format',
                ],
            ],
        ];
    }

    /**
     * Test modifyData method when no current store detected
     *
     * @param array $data
     * @param array $result
     * @dataProvider modifyDataDataProvider
     */
    public function testModifyDataNoCurrentStore($data, $result)
    {
        $currentStoreId = null;
        $dateFormat = \IntlDateFormatter::MEDIUM;

        $this->setProperty('dateFormat', $dateFormat);

        $this->storeManagerMock->expects($this->any())
            ->method('getStore')
            ->with(true)
            ->willThrowException(new NoSuchEntityException());

        $this->dateTimeFormatterMock->expects($this->any())
            ->method('getLocalizedDate')
            ->willReturnMap(
                [
                    [
                        "",
                        $currentStoreId,
                        $dateFormat,
                        'current_localized_date',
                    ],
                    [
                        'date_time_in_db_format',
                        $currentStoreId,
                        $dateFormat,
                        'localized_date',
                    ]
                ]
            );

        $this->dateTimeFormatterMock->expects($this->any())
            ->method('getLocalizedDateTime')
            ->willReturnMap(
                [
                    [
                        "",
                        $currentStoreId,
                        StdlibDateTime::DATE_INTERNAL_FORMAT,
                        'current_localized_date_in_iso_format',
                    ],
                    [
                        'date_time_in_db_format',
                        $currentStoreId,
                        StdlibDateTime::DATE_INTERNAL_FORMAT,
                        'localized_date_in_iso_format',
                    ]
                ]
            );

        $this->assertSame($result, $this->modifier->modifyData($data));
    }

    /**
     * Set property
     *
     * @param string $propertyName
     * @param mixed $value
     * @return mixed
     * @throws \ReflectionException
     */
    private function setProperty($propertyName, $value)
    {
        $class = new \ReflectionClass($this->modifier);
        $property = $class->getProperty($propertyName);
        $property->setAccessible(true);
        $property->setValue($this->modifier, $value);

        return $this;
    }
}
