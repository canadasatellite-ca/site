<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Test\Unit\Model\Email\QueueItem\Validator\Type;

use Aheadworks\AdvancedReviews\Model\Email\QueueItem\Validator\Type\Subscriber;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\TestCase;
use Aheadworks\AdvancedReviews\Api\Data\QueueItemInterface;
use Aheadworks\AdvancedReviews\Model\Email\Subscriber\Resolver as SubscriberResolver;
use Aheadworks\AdvancedReviews\Model\Email\Subscriber\Checker as SubscriberChecker;
use Aheadworks\AdvancedReviews\Model\Source\Email\Type as EmailTypeSource;
use Aheadworks\AdvancedReviews\Api\Data\Email\SubscriberInterface;

/**
 * Test for \Aheadworks\AdvancedReviews\Model\Email\QueueItem\Validator\Type\Subscriber
 */
class SubscriberTest extends TestCase
{
    /**
     * @var Subscriber
     */
    private $validator;

    /**
     * @var SubscriberResolver|\PHPUnit_Framework_MockObject_MockObject
     */
    private $subscriberResolverMock;

    /**
     * @var SubscriberChecker|\PHPUnit_Framework_MockObject_MockObject
     */
    private $subscriberCheckerMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->subscriberResolverMock = $this->createMock(SubscriberResolver::class);
        $this->subscriberCheckerMock = $this->createMock(SubscriberChecker::class);

        $this->validator = $objectManager->getObject(
            Subscriber::class,
            [
                'subscriberResolver' => $this->subscriberResolverMock,
                'subscriberChecker' => $this->subscriberCheckerMock,
            ]
        );
    }

    /**
     * Testing of isValid method
     *
     * @param QueueItemInterface $queueItem
     * @param SubscriberInterface|null $subscriber
     * @param bool $result
     * @dataProvider isValidDataProvider
     */
    public function testIsValid($queueItem, $subscriber, $result)
    {
        $this->subscriberResolverMock->expects($this->any())
            ->method('getByEmailQueueItem')
            ->with($queueItem)
            ->willReturn($subscriber);

        $this->subscriberCheckerMock->expects($this->any())
            ->method('isNeedToSendEmail')
            ->with($subscriber, EmailTypeSource::ADMIN_NEW_REVIEW)
            ->willReturn($result);

        $this->assertEquals($result, $this->validator->isValid($queueItem));
    }

    /**
     * Data provider for isValid
     *
     * @return array
     */
    public function isValidDataProvider()
    {
        $queueItemMock = $this->createMock(QueueItemInterface::class);
        $queueItemMock->expects($this->any())
            ->method('getType')
            ->willReturn(EmailTypeSource::ADMIN_NEW_REVIEW);

        $subscriberMock = $this->createMock(SubscriberInterface::class);

        return [
            [
                'queueItem' => $queueItemMock,
                'subscriber' => null,
                'result' => false,
            ],
            [
                'queueItem' => $queueItemMock,
                'subscriber' => $subscriberMock,
                'result' => true,
            ],
            [
                'queueItem' => $queueItemMock,
                'subscriber' => $subscriberMock,
                'result' => false,
            ],
        ];
    }
}
