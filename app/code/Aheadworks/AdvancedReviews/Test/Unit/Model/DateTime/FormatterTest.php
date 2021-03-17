<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Test\Unit\Model\DateTime;

use Aheadworks\AdvancedReviews\Model\DateTime\Formatter;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Framework\Stdlib\DateTime\DateTimeFormatterInterface;

/**
 * Test for \Aheadworks\AdvancedReviews\Model\DateTime\Formatter
 */
class FormatterTest extends TestCase
{
    /**
     * @var Formatter
     */
    private $formatter;

    /**
     * @var DateTime|\PHPUnit_Framework_MockObject_MockObject
     */
    private $dateTimeMock;

    /**
     * @var TimezoneInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $localeDateMock;

    /**
     * @var DateTimeFormatterInterface|\PHPUnit_Framework_MockObject_MockObject
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

        $this->dateTimeMock = $this->createMock(DateTime::class);
        $this->localeDateMock = $this->createMock(TimezoneInterface::class);
        $this->dateTimeFormatterMock = $this->createMock(DateTimeFormatterInterface::class);

        $this->formatter = $objectManager->getObject(
            Formatter::class,
            [
                'dateTime' => $this->dateTimeMock,
                'localeDate' => $this->localeDateMock,
                'dateTimeFormatter' => $this->dateTimeFormatterMock,
            ]
        );
    }

    /**
     * Test for getLocalizedDate method
     *
     * @param string|null $date
     * @param int|null $storeId
     * @param string|int|array|null $dateFormat
     * @param string $result
     * @dataProvider getLocalizedDateDataProvider
     */
    public function testGetLocalizedDate($date, $storeId, $dateFormat, $result)
    {
        $dateTimeMock = $this->createMock(\DateTime::class);
        $this->localeDateMock->expects($this->once())
            ->method('scopeDate')
            ->with($storeId, $date, true)
            ->willReturn($dateTimeMock);

        $this->dateTimeFormatterMock->expects($this->once())
            ->method('formatObject')
            ->with($dateTimeMock, [$dateFormat, \IntlDateFormatter::NONE])
            ->willReturn($result);

        $this->assertEquals($result, $this->formatter->getLocalizedDate($date, $storeId, $dateFormat));
    }

    /**
     * @return array
     */
    public function getLocalizedDateDataProvider()
    {
        return [
            [
                'date' => null,
                'storeId' => null,
                'dateFormat' => \IntlDateFormatter::MEDIUM,
                'result' => 'current localized date time',
            ],
            [
                'date' => '2019-04-01 07:24:00',
                'storeId' => null,
                'dateFormat' => \IntlDateFormatter::MEDIUM,
                'result' => '1 Apr 2019',
            ],
            [
                'date' => '2019-04-01 07:24:00',
                'storeId' => null,
                'dateFormat' => \IntlDateFormatter::SHORT,
                'result' => '01/04/2019',
            ],
        ];
    }

    /**
     * Test for getLocalizedDateTime method
     *
     * @param string|null $date
     * @param int|null $storeId
     * @param string|int|array|null $format
     * @param string $result
     * @dataProvider getLocalizedDateTimeDataProvider
     */
    public function testGetLocalizedDateTime($date, $storeId, $format, $result)
    {
        $dateTimeMock = $this->createMock(\DateTime::class);
        $this->localeDateMock->expects($this->once())
            ->method('scopeDate')
            ->with($storeId, $date, true)
            ->willReturn($dateTimeMock);
        $this->dateTimeFormatterMock->expects($this->once())
            ->method('formatObject')
            ->with($dateTimeMock, $format)
            ->willReturn($result);

        $this->assertEquals($result, $this->formatter->getLocalizedDateTime($date, $storeId, $format));
    }

    /**
     * @return array
     */
    public function getLocalizedDateTimeDataProvider()
    {
        return [
            [
                'date' => null,
                'storeId' => null,
                'format' => null,
                'result' => 'current localized date time',
            ],
            [
                'date' => '2019-04-01 07:24:00',
                'storeId' => null,
                'format' => 'yyyy-MM-dd',
                'result' => '2019-04-01',
            ],
            [
                'date' => '2019-04-01 07:24:00',
                'storeId' => null,
                'format' => [
                    \IntlDateFormatter::MEDIUM,
                    \IntlDateFormatter::MEDIUM,
                ],
                'result' => 'Apr 1, 2019 7:24',
            ],
        ];
    }

    /**
     * Test for getDateTimeInDbFormat method
     *
     * @param string|null $date
     * @param string $result
     * @dataProvider getDateTimeInDbFormatDataProvider
     */
    public function testGetDateTimeInDbFormat($date, $result)
    {
        $this->dateTimeMock->expects($this->any())
            ->method('gmtDate')
            ->willReturn($result);

        $this->assertEquals($result, $this->formatter->getDateTimeInDbFormat($date));
    }

    /**
     * @return array
     */
    public function getDateTimeInDbFormatDataProvider()
    {
        return [
            [
                'date' => null,
                'result' => 'current date time',
            ],
            [
                'date' => '04/01/2019 4:13pm',
                'result' => '2019-04-01 16:13:00',
            ],
        ];
    }
}
