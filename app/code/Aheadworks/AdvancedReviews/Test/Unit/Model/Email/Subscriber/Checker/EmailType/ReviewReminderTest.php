<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Test\Unit\Model\Email\Subscriber\Checker\EmailType;

use Aheadworks\AdvancedReviews\Model\Email\Subscriber\Checker\EmailType\ReviewReminder;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\TestCase;
use Aheadworks\AdvancedReviews\Api\Data\Email\SubscriberInterface;

/**
 * Test for \Aheadworks\AdvancedReviews\Model\Email\Subscriber\Checker\EmailType\ReviewReminder
 */
class ReviewReminderTest extends TestCase
{
    /**
     * @var ReviewReminder
     */
    private $checker;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->checker = $objectManager->getObject(ReviewReminder::class, []);
    }

    /**
     * Test isNeedToSendEmail method
     *
     * @param bool $flag
     * @dataProvider isNeedToSendEmailDataProvider
     */
    public function testIsNeedToSendEmail($flag)
    {
        $subscriber = $this->createMock(SubscriberInterface::class);
        $subscriber->expects($this->once())
            ->method('getIsReviewReminderEmailEnabled')
            ->willReturn($flag);
        $this->assertEquals($flag, $this->checker->isNeedToSendEmail($subscriber));
    }

    /**
     * @return array
     */
    public function isNeedToSendEmailDataProvider()
    {
        return [
            [
                'flag' => true,
            ],
            [
                'flag' => false,
            ],
        ];
    }
}
