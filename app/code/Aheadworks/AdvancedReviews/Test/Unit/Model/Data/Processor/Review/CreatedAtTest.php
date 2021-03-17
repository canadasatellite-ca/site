<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Test\Unit\Model\Data\Processor\Review;

use Aheadworks\AdvancedReviews\Model\Data\Processor\Review\CreatedAt;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Aheadworks\AdvancedReviews\Api\Data\ReviewInterface;
use Aheadworks\AdvancedReviews\Model\DateTime\Formatter as DateTimeFormatter;

/**
 * Test for \Aheadworks\AdvancedReviews\Model\Data\Processor\Review\CustomerId
 */
class CreatedAtTest extends TestCase
{
    /**
     * @var CreatedAt
     */
    private $processor;

    /**
     * @var DateTimeFormatter|\PHPUnit_Framework_MockObject_MockObject
     */
    private $dateTimeFormatterMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->dateTimeFormatterMock = $this->createMock(DateTimeFormatter::class);

        $this->dateTimeFormatterMock->expects($this->any())
            ->method('getDateTimeInDbFormat')
            ->willReturnMap(
                [
                    [
                        null,
                        'current_datetime_in_db_format',
                    ],
                    [
                        'datetime_1',
                        'datetime_1_in_db_format',
                    ],
                    [
                        'datetime_2',
                        '1datetime_2_in_db_format',
                    ],
                ]
            );

        $this->processor = $objectManager->getObject(
            CreatedAt::class,
            [
                'dateTimeFormatter' => $this->dateTimeFormatterMock
            ]
        );
    }

    /**
     * Test for process method
     *
     * @param array $data
     * @param array $result
     * @dataProvider processDataProvider
     */
    public function testProcess($data, $result)
    {
        $this->assertEquals($result, $this->processor->process($data));
    }

    /**
     * @return array
     */
    public function processDataProvider()
    {
        return [
            [
                'data' => [],
                'result' => [
                    ReviewInterface::CREATED_AT => 'current_datetime_in_db_format',
                ],
            ],
            [
                'data' => [
                    ReviewInterface::ID => 1,
                ],
                'result' => [
                    ReviewInterface::ID => 1,
                    ReviewInterface::CREATED_AT => 'current_datetime_in_db_format',
                ],
            ],
            [
                'data' => [
                    ReviewInterface::ID => 1,
                    ReviewInterface::CREATED_AT => 'datetime_1',
                ],
                'result' => [
                    ReviewInterface::ID => 1,
                    ReviewInterface::CREATED_AT => 'datetime_1_in_db_format',
                ],
            ],
        ];
    }
}
