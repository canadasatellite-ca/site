<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Test\Unit\Model\Email\DateTime;

use Aheadworks\AdvancedReviews\Model\Email\DateTime\Resolver;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Aheadworks\AdvancedReviews\Model\DateTime\Formatter as DateTimeFormatter;
use Aheadworks\AdvancedReviews\Model\Config;
use Aheadworks\AdvancedReviews\Model\Source\Email\Type;

/**
 * Test for \Aheadworks\AdvancedReviews\Model\Email\DateTime\Resolver
 */
class ResolverTest extends TestCase
{
    /**
     * @var Resolver
     */
    private $resolver;

    /**
     * @var DateTimeFormatter|\PHPUnit_Framework_MockObject_MockObject
     */
    private $dateTimeFormatterMock;

    /**
     * @var Config|\PHPUnit_Framework_MockObject_MockObject
     */
    private $configMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->dateTimeFormatterMock = $this->createMock(DateTimeFormatter::class);
        $this->configMock = $this->createMock(Config::class);

        $this->resolver = $objectManager->getObject(
            Resolver::class,
            [
                'dateTimeFormatter' => $this->dateTimeFormatterMock,
                'config' => $this->configMock,
            ]
        );
    }

    /**
     * Test getScheduledDateTimeInDbFormat method
     *
     * @param int $emailType
     * @param int $sendAfterDays
     * @param string $currentDate
     * @param string $scheduledDate
     * @param string $result
     * @dataProvider getScheduledDateTimeInDbFormatDataProvider
     */
    public function testGetScheduledDateTimeInDbFormat(
        $emailType,
        $sendAfterDays,
        $currentDate,
        $scheduledDate,
        $result
    ) {
        $storeId = 1;

        $this->dateTimeFormatterMock->expects($this->any())
            ->method('getDateTimeInDbFormat')
            ->withConsecutive([null], [$sendAfterDays . ' days'])
            ->willReturnOnConsecutiveCalls($currentDate, $scheduledDate);

        $this->configMock->expects($this->once())
            ->method('getSendReminderAfterDays')
            ->with($storeId)
            ->willReturn($sendAfterDays);

        $this->assertEquals($result, $this->resolver->getScheduledDateTimeInDbFormat($emailType, $storeId));
    }

    /**
     * @return array
     */
    public function getScheduledDateTimeInDbFormatDataProvider()
    {
        return [
            [
                'emailType' => Type::ADMIN_NEW_REVIEW,
                'sendAfterDays' => 15,
                'currentDate' => '2019-04-01 16:13:00',
                'scheduledDate' => '',
                'result' => '2019-04-01 16:13:00',
            ],
            [
                'emailType' => Type::SUBSCRIBER_REVIEW_APPROVED,
                'sendAfterDays' => 15,
                'currentDate' => '2019-04-01 16:13:00',
                'scheduledDate' => '',
                'result' => '2019-04-01 16:13:00',
            ],
            [
                'emailType' => Type::SUBSCRIBER_NEW_COMMENT,
                'sendAfterDays' => 15,
                'currentDate' => '2019-04-01 16:13:00',
                'scheduledDate' => '',
                'result' => '2019-04-01 16:13:00',
            ],
            [
                'emailType' => Type::SUBSCRIBER_REVIEW_REMINDER,
                'sendAfterDays' => '',
                'currentDate' => '2019-04-01 16:13:00',
                'scheduledDate' => '',
                'result' => '2019-04-01 16:13:00',
            ],
            [
                'emailType' => Type::SUBSCRIBER_REVIEW_REMINDER,
                'sendAfterDays' => 'abc',
                'currentDate' => '2019-04-01 16:13:00',
                'scheduledDate' => '',
                'result' => '2019-04-01 16:13:00',
            ],
            [
                'emailType' => Type::SUBSCRIBER_REVIEW_REMINDER,
                'sendAfterDays' => 10,
                'currentDate' => '2019-04-01 16:13:00',
                'scheduledDate' => '2019-04-11 16:13:00',
                'result' => '2019-04-11 16:13:00',
            ],
            [
                'emailType' => Type::SUBSCRIBER_REVIEW_REMINDER,
                'sendAfterDays' => '10',
                'currentDate' => '2019-04-01 16:13:00',
                'scheduledDate' => '2019-04-11 16:13:00',
                'result' => '2019-04-11 16:13:00',
            ],
            [
                'emailType' => Type::ADMIN_REVIEW_ABUSE_REPORT,
                'sendAfterDays' => 15,
                'currentDate' => '2019-04-01 16:13:00',
                'scheduledDate' => '',
                'result' => '2019-04-01 16:13:00',
            ],
            [
                'emailType' => Type::ADMIN_COMMENT_ABUSE_REPORT,
                'sendAfterDays' => 15,
                'currentDate' => '2019-04-01 16:13:00',
                'scheduledDate' => '',
                'result' => '2019-04-01 16:13:00',
            ],
            [
                'emailType' => Type::ADMIN_CRITICAL_REVIEW_ALERT,
                'sendAfterDays' => 15,
                'currentDate' => '2019-04-01 16:13:00',
                'scheduledDate' => '',
                'result' => '2019-04-01 16:13:00',
            ],
        ];
    }

    /**
     * Test getCurrentDate method
     */
    public function testGetCurrentDate()
    {
        $currentDate = '2019-04-01 16:13:00';

        $this->dateTimeFormatterMock->expects($this->once())
            ->method('getDateTimeInDbFormat')
            ->with(null)
            ->willReturn($currentDate);

        $this->assertTrue(is_string($this->resolver->getCurrentDate()));
    }

    /**
     * Test getDeadlineForProcessedEmails method
     */
    public function testGetDeadlineForProcessedEmails()
    {
        $date = '2019-04-01 16:13:00';

        $this->dateTimeFormatterMock->expects($this->once())
            ->method('getDateTimeInDbFormat')
            ->with('-30 days')
            ->willReturn($date);

        $this->assertTrue(is_string($this->resolver->getDeadlineForProcessedEmails()));
    }
}
