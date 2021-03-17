<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Test\Unit\Model\Email\Subscriber;

use Aheadworks\AdvancedReviews\Model\Email\Subscriber\Checker;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\TestCase;
use Aheadworks\AdvancedReviews\Api\Data\Email\SubscriberInterface;
use Aheadworks\AdvancedReviews\Model\Email\Subscriber\Checker\Pool as CheckerPool;
use Aheadworks\AdvancedReviews\Model\Source\Email\Type as EmailTypeSource;
use Aheadworks\AdvancedReviews\Model\Email\Subscriber\Checker\CheckerInterface;

/**
 * Test for \Aheadworks\AdvancedReviews\Model\Email\Subscriber\Checker
 */
class CheckerTest extends TestCase
{
    const TEST_CORRECT_EMAIL_TYPE = EmailTypeSource::ADMIN_NEW_REVIEW;
    const TEST_INCORRECT_EMAIL_TYPE = -1;

    /**
     * @var Checker
     */
    private $model;

    /**
     * @var CheckerPool|\PHPUnit_Framework_MockObject_MockObject
     */
    private $checkerPoolMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    protected function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->checkerPoolMock = $this->createMock(CheckerPool::class);

        $this->model = $objectManager->getObject(
            Checker::class,
            [
                'checkerPool' => $this->checkerPoolMock,
            ]
        );
    }

    /**
     * Testing of isNeedToSendEmail method
     *
     * @param SubscriberInterface $subscriber
     * @param int $emailType
     * @param bool $result
     * @dataProvider isNeedToSendEmailDataProvider
     */
    public function testIsNeedToSendEmail($subscriber, $emailType, $result)
    {
        $checkerMock = $this->createMock(CheckerInterface::class);
        $checkerMock->expects($this->any())
            ->method('isNeedToSendEmail')
            ->with($subscriber)
            ->willReturn($result);

        $this->checkerPoolMock->expects($this->once())
            ->method('getCheckerByEmailType')
            ->willReturnMap(
                [
                    [
                        self::TEST_CORRECT_EMAIL_TYPE,
                        $checkerMock
                    ],
                    [
                        self::TEST_INCORRECT_EMAIL_TYPE,
                        null
                    ],
                ]
            );

        $this->assertEquals(
            $result,
            $this->model->isNeedToSendEmail(
                $subscriber,
                $emailType
            )
        );
    }

    /**
     * Data provider for isNeedToSendEmail
     *
     * @return array
     */
    public function isNeedToSendEmailDataProvider()
    {
        $subscriber = $this->createMock(SubscriberInterface::class);
        return [
            [
                'subscriber' => $subscriber,
                'emailType' => self::TEST_CORRECT_EMAIL_TYPE,
                'result' => true
            ],
            [
                'subscriber' => $subscriber,
                'emailType' => self::TEST_CORRECT_EMAIL_TYPE,
                'result' => false
            ],
            [
                'subscriber' => $subscriber,
                'emailType' => self::TEST_INCORRECT_EMAIL_TYPE,
                'result' => false
            ],
        ];
    }
}
